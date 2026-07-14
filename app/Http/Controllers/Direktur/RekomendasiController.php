<?php

namespace App\Http\Controllers\Direktur;

use App\Http\Controllers\Controller;
use App\Models\HasilPerhitungan;
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
        $query = HasilPerhitungan::with(['penilaian.lokasi'])
            ->orderBy('ranking', 'asc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('penilaian.lokasi', function($q) use ($search) {
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
        $lastCalculation = HasilPerhitungan::max('tanggal_hitung');

        return view('direktur.rekomendasi.index', compact('results', 'lastCalculation'));
    }

    public function show($id, TopsisService $topsisService)
    {
        $hasil = HasilPerhitungan::with(['penilaian.lokasi', 'penilaian.observasiLokasi', 'penilaian.detailPenilaians'])->findOrFail($id);
        
        // Recalculate to get matrix arrays for thesis transparency
        $topsisData = $topsisService->calculate();
        
        $penilaianId = $hasil->penilaian_id;
        $kriterias = $topsisData['kriterias'];
        
        $rawMatrix = $topsisData['matrix'][$penilaianId] ?? [];
        $normalizedMatrix = $topsisData['normalizedMatrix'][$penilaianId] ?? [];
        $weightedMatrix = $topsisData['weightedMatrix'][$penilaianId] ?? [];

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
            'chartDataWeighted'
        ));
    }

    public function exportPdf()
    {
        $results = HasilPerhitungan::with('penilaian.lokasi')->orderBy('ranking', 'asc')->get();
        $kriterias = Kriteria::orderBy('urutan')->get();
        $timestamp = HasilPerhitungan::max('tanggal_hitung') ?? now();

        $pdf = Pdf::loadView('direktur.exports.pdf', compact('results', 'kriterias', 'timestamp'));
        return $pdf->download('Laporan_Rekomendasi_Lokasi_TOPSIS.pdf');
    }

    public function exportExcel()
    {
        // Using Laravel Excel to export multiple sheets
        return Excel::download(new RekomendasiExport, 'Laporan_Rekomendasi_Lokasi_TOPSIS.xlsx');
    }
}
