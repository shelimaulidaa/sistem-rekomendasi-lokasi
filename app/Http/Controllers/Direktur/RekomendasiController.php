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
        $calculatedPeriodeIds = HasilPerhitungan::getCalculatedPeriodeIds();
        $periodes = Periode::whereIn('id', $calculatedPeriodeIds)
            ->where('status', Periode::STATUS_SELESAI)
            ->orderBy('created_at', 'desc')
            ->get();

        $query = HasilPerhitungan::with(['penilaian.observasiLokasi'])
            ->whereHas('penilaian.observasiLokasi.periode', function ($q) {
                $q->where('status', Periode::STATUS_SELESAI);
            })
            ->orderBy('ranking', 'asc');

        $periodeId = $request->input('periode_id');

        if (!empty($periodeId)) {
            if ($periodes->contains('id', $periodeId)) {
                $query->wherePeriode($periodeId);
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
        
        $activePeriodeId = $periodeId;

        return view('direktur.rekomendasi.index', compact('results', 'lastCalculation', 'periodes', 'activePeriodeId'));
    }

    public function show($id, TopsisService $topsisService)
    {
        $hasil = HasilPerhitungan::with(['penilaian.observasiLokasi', 'penilaian.detailPenilaians'])->findOrFail($id);
        
        // Hitung ulang untuk mendapatkan matriks keputusan untuk transparansi skripsi
        $topsisData = $topsisService->calculate($hasil->penilaian->observasiLokasi->periode_id);
        
        $penilaianId = $hasil->penilaian_id;
        $kriterias = $topsisData['kriterias'];
        
        $rawMatrix = $topsisData['matrix'][$penilaianId] ?? [];
        $normalizedMatrix = $topsisData['normalizedMatrix'][$penilaianId] ?? [];
        $weightedMatrix = $topsisData['weightedMatrix'][$penilaianId] ?? [];

        $observasi = $hasil->penilaian->observasiLokasi;
        $spatialData = $observasi ? $observasi->spatial_data : null;

        // Untuk Grafik Radar
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

        $periodeId = $request->query('periode_id');

        if (!empty($periodeId)) {
            $query->whereHas('penilaian.observasiLokasi', function($q) use ($periodeId) {
                $q->where('periode_id', $periodeId);
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
        $kriterias = Kriteria::query()
            ->when($periodeId, fn($q) => $q->where('periode_id', $periodeId), fn($q) => $q->whereNull('periode_id'))
            ->orderBy('urutan')
            ->get();
        $timestamp = (clone $query)->max('tanggal_hitung') ?? now();

        $pdf = Pdf::loadView('direktur.exports.pdf', compact('results', 'kriterias', 'timestamp'));
        return $pdf->download('Laporan_Rekomendasi_Lokasi_TOPSIS.pdf');
    }

    public function exportExcel(Request $request)
    {
        $periodeId = $request->input('periode_id');
        return Excel::download(new RekomendasiExport($periodeId, $request->status), 'Laporan_Rekomendasi_Lokasi_TOPSIS.xlsx');
    }
}
