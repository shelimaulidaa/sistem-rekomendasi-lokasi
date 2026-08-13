<?php

namespace App\Http\Controllers\Manajer;

use App\Http\Controllers\Controller;
use App\Models\HasilPerhitungan;
use App\Models\Periode;
use App\Models\Kriteria;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $calculatedPeriodeIds = HasilPerhitungan::getCalculatedPeriodeIds();
        $periodes = Periode::whereIn('id', $calculatedPeriodeIds)
            ->where('status', Periode::STATUS_SELESAI)
            ->orderBy('created_at', 'desc')
            ->get();

        $activePeriodeId = $request->query('periode_id');
        if ($activePeriodeId && !$periodes->contains('id', $activePeriodeId)) {
            $activePeriodeId = null;
        }

        if (!$activePeriodeId && $periodes->isNotEmpty()) {
            $activePeriodeId = $periodes->first()->id;
        }

        $hasilQuery = HasilPerhitungan::with(['penilaian.observasiLokasi'])
            ->whereHas('penilaian.observasiLokasi.periode', function ($q) {
                $q->where('status', Periode::STATUS_SELESAI);
            });

        if ($activePeriodeId) {
            $hasilQuery->wherePeriode($activePeriodeId);
        } elseif ($periodes->isEmpty()) {
            $hasilQuery->whereRaw('1 = 0');
        }

        $hasil = $hasilQuery->orderBy('ranking', 'asc')->get();

        $lokasiTerbaik = $hasil->first();
        $totalAlternatif = $hasil->count();
        $lastCalculationDate = $hasil->max('tanggal_hitung');
        
        Carbon::setLocale('id');
        $lastCalculation = $lastCalculationDate 
            ? Carbon::parse($lastCalculationDate)->translatedFormat('d F Y - H:i') . ' WIB' 
            : '-';

        return view('manajer.history.index', compact(
            'hasil',
            'lokasiTerbaik',
            'totalAlternatif',
            'lastCalculation',
            'periodes',
            'activePeriodeId'
        ));
    }

    public function exportPdf(Request $request)
    {
        $periodeId = $request->query('periode_id');
        $hasilQuery = HasilPerhitungan::with(['penilaian.observasiLokasi'])
            ->whereHas('penilaian.observasiLokasi.periode', function ($q) {
                $q->where('status', Periode::STATUS_SELESAI);
            });
        
        if ($periodeId) {
            $hasilQuery->wherePeriode($periodeId);
        }

        $hasil = $hasilQuery->orderBy('ranking', 'asc')->get();

        $kriteria = Kriteria::query()
            ->when($periodeId, fn($q) => $q->where('periode_id', $periodeId), fn($q) => $q->whereNull('periode_id'))
            ->orderBy('urutan')
            ->get();
        
        Carbon::setLocale('id');
        $timestamp = Carbon::now()->translatedFormat('d F Y - H:i') . ' WIB';
        
        $periode = Periode::find($periodeId);
        $periodeName = $periode ? $periode->nama_periode : 'Semua Periode';

        $pdf = Pdf::loadView('manajer.history.pdf', compact('hasil', 'kriteria', 'timestamp', 'periodeName'));
        
        return $pdf->download('Laporan_Riwayat_TOPSIS_' . date('Ymd_His') . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        return redirect()->back()->with('error', 'Export Excel belum tersedia.');
    }
}
