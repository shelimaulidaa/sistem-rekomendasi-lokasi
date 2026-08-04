<?php

namespace App\Http\Controllers\Direktur;

use App\Http\Controllers\Controller;
use App\Models\HasilPerhitungan;
use App\Models\Periode;
use App\Models\Kriteria;
use App\Services\TopsisService;

use App\Exports\RekomendasiExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class RekomendasiController extends Controller
{
    public function index(Request $request)
    {
        $calculatedBatchIds = HasilPerhitungan::getCalculatedPeriodeIds();
        $batches = Periode::whereIn('id', $calculatedBatchIds)
            ->where('status', Periode::STATUS_SELESAI)
            ->orderBy('created_at', 'desc')
            ->get();

        $query = HasilPerhitungan::with(['penilaian.observasiLokasi'])
            ->whereHas('penilaian.observasiLokasi.periode', function ($q) {
                $q->where('status', Periode::STATUS_SELESAI);
            })
            ->orderBy('ranking', 'asc');

        if ($request->filled('batch_id')) {
            if ($batches->contains('id', $request->batch_id)) {
                $query->wherePeriode($request->batch_id);
            } else {
                $query->whereRaw('1 = 0');
            }
        }


        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('penilaian.observasiLokasi', function($q) use ($search) {
                $q->where('nama_pemilik', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $status = $request->status;
            if ($status === 'sangat_direkomendasikan') {
                $query->where('ranking', 1);
            } elseif ($status === 'direkomendasikan') {
                $query->whereBetween('ranking', [2, 3]);
            } elseif ($status === 'dipertimbangkan') {
                $query->where('ranking', '>', 3);
            }
        }

        $results = $query->paginate(10)->withQueryString();
        $lastCalculation = (clone $query)->max('tanggal_hitung') ?? HasilPerhitungan::whereHas('penilaian.observasiLokasi.periode', fn($q) => $q->where('status', Periode::STATUS_SELESAI))->max('tanggal_hitung');
        $activeBatchId = $request->batch_id;

        return view('direktur.rekomendasi.index', compact('results', 'lastCalculation', 'batches', 'activeBatchId'));
    }

    public function show($id, TopsisService $topsisService)
    {
        $hasil = HasilPerhitungan::with(['penilaian.observasiLokasi', 'penilaian.detailPenilaians'])->findOrFail($id);
        
        // Recalculate to get matrix arrays for thesis transparency
        $topsisData = $topsisService->calculate($hasil->penilaian->observasiLokasi->batch_id);
        
        $penilaianId = $hasil->penilaian_id;
        $kriterias = $topsisData['kriterias'];
        
        $rawMatrix = $topsisData['matrix'][$penilaianId] ?? [];
        $normalizedMatrix = $topsisData['normalizedMatrix'][$penilaianId] ?? [];
        $weightedMatrix = $topsisData['weightedMatrix'][$penilaianId] ?? [];

        $observasi = $hasil->penilaian->observasiLokasi;
        $spatialData = $observasi ? $observasi->spatial_data : null;


        // For Radar Chart
        $chartLabels = [];
        $chartDataRaw = [];
        $chartDataWeighted = [];
        foreach ($kriterias as $k) {
            $chartLabels[] = $k->nama_kriteria;
            $chartDataRaw[] = $rawMatrix[$k->kriteria_id] ?? 0;
            $chartDataWeighted[] = $weightedMatrix[$k->kriteria_id] ?? 0;
        }

        return view('direktur.rekomendasi.show', compact(
            'hasil', 
            'kriterias', 
            'rawMatrix', 
            'normalizedMatrix', 
            'weightedMatrix',
            'chartLabels',
            'chartDataRaw',
            'chartDataWeighted',
            'spatialData'
        ));
    }

    public function exportPdf(Request $request)
    {
        $query = HasilPerhitungan::with('penilaian.observasiLokasi')
            ->whereHas('penilaian.observasiLokasi.periode', function ($q) {
                $q->where('status', Periode::STATUS_SELESAI);
            })
            ->orderBy('ranking', 'asc');

        if ($request->filled('batch_id')) {
            $batchId = $request->batch_id;
            $query->whereHas('penilaian.observasiLokasi', function($q) use ($batchId) {
                $q->where('batch_id', $batchId);
            });
        }

        if ($request->filled('status')) {
            $status = $request->status;
            if ($status === 'sangat_direkomendasikan') {
                $query->where('ranking', 1);
            } elseif ($status === 'direkomendasikan') {
                $query->whereBetween('ranking', [2, 3]);
            } elseif ($status === 'dipertimbangkan') {
                $query->where('ranking', '>', 3);
            }
        }

        $results = $query->get();
        $batchId = $request->query('batch_id');
        $kriterias = Kriteria::query()
            ->when($batchId, fn($q) => $q->where('periode_id', $batchId), fn($q) => $q->whereNull('periode_id'))
            ->orderBy('urutan')
            ->get();
        $timestamp = (clone $query)->max('tanggal_hitung') ?? now();

        $pdf = Pdf::loadView('direktur.exports.pdf', compact('results', 'kriterias', 'timestamp'));
        return $pdf->download('Laporan_Rekomendasi_Lokasi_TOPSIS.pdf');
    }

    public function exportExcel(Request $request)
    {
        // Using Laravel Excel to export multiple sheets
        // We can pass the request parameters to the export class if needed
        return Excel::download(new RekomendasiExport($request->batch_id, $request->status), 'Laporan_Rekomendasi_Lokasi_TOPSIS.xlsx');
    }
}
