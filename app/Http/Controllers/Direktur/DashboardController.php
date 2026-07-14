<?php

namespace App\Http\Controllers\Direktur;

use App\Http\Controllers\Controller;
use App\Models\HasilPerhitungan;
use App\Models\ObservasiLokasi;
use App\Models\Penilaian;

class DashboardController extends Controller
{
    public function index()
    {
        $totalObservasi = ObservasiLokasi::count();
        $totalPenilaian = Penilaian::count();
        $totalPerhitungan = HasilPerhitungan::count();

        $topRanking = HasilPerhitungan::with('penilaian.observasiLokasi')
            ->orderBy('ranking', 'asc')
            ->get();

        $lokasiTerbaik = $topRanking->first();
        $top3 = $topRanking->take(3);

        $lastCalculation = HasilPerhitungan::max('tanggal_hitung');

        // Prepare data for Chart.js (Top 5 Ranking)
        $chartLabels = [];
        $chartData = [];
        foreach ($topRanking->take(5) as $rank) {
            $chartLabels[] = $rank->penilaian->observasiLokasi->nama_pemilik ?? 'Unknown';
            $chartData[] = round($rank->nilai_preferensi, 4);
        }

        return view('direktur.dashboard', compact(
            'totalObservasi',
            'totalPenilaian',
            'totalPerhitungan',
            'lokasiTerbaik',
            'top3',
            'lastCalculation',
            'chartLabels',
            'chartData'
        ));
    }
}
