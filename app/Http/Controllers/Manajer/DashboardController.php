<?php

namespace App\Http\Controllers\Manajer;

use App\Http\Controllers\Controller;
use App\Models\HasilPerhitungan;
use App\Models\Kriteria;
use App\Models\ObservasiLokasi;
use App\Models\Penilaian;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalObservasi = ObservasiLokasi::count();
        $observasiBulanIni = ObservasiLokasi::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->count();
        
        $sudahDiproses = Penilaian::whereHas('hasilPerhitungan')->count();
        $belumDiproses = Penilaian::whereDoesntHave('hasilPerhitungan')->count();
        
        $kriteriaBenefit = Kriteria::where('atribut', 'benefit')->count();
        $kriteriaCost = Kriteria::where('atribut', 'cost')->count();

        $topRanking = HasilPerhitungan::with('penilaian.observasiLokasi')
            ->orderBy('ranking', 'asc')
            ->get();

        $lokasiTerbaik = $topRanking->first();
        $top3 = $topRanking->take(3);
        $lastCalculation = HasilPerhitungan::max('tanggal_hitung');

        $chartLabels = [];
        $chartData = [];
        foreach ($topRanking->take(5) as $rank) {
            $chartLabels[] = $rank->penilaian->observasiLokasi->nama_pemilik ?? 'Unknown';
            $chartData[] = round($rank->nilai_preferensi, 4);
        }

        $totalKriteriaAktif = Kriteria::count();
        $totalHasilPerhitungan = HasilPerhitungan::count();

        $statusMessage = 'Semua data observasi telah diproses oleh TOPSIS';
        $statusType = 'success'; // success, warning, error

        if ($belumDiproses > 0) {
            $statusMessage = "{$belumDiproses} observasi belum diproses oleh TOPSIS.";
            $statusType = 'warning';
        } elseif ($totalObservasi === 0 || $totalKriteriaAktif === 0) {
            $statusMessage = "Data kriteria atau observasi belum lengkap.";
            $statusType = 'error';
        }

        return view('dashboard', compact(
            'totalObservasi',
            'observasiBulanIni',
            'sudahDiproses',
            'belumDiproses',
            'kriteriaBenefit',
            'kriteriaCost',
            'lokasiTerbaik',
            'top3',
            'lastCalculation',
            'chartLabels',
            'chartData',
            'totalKriteriaAktif',
            'totalHasilPerhitungan',
            'statusMessage',
            'statusType'
        ));
    }
}
