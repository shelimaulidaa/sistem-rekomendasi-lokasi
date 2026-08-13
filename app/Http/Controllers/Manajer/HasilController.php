<?php

namespace App\Http\Controllers\Manajer;

use App\Http\Controllers\Controller;
use App\Models\HasilPerhitungan;
use App\Models\ObservasiLokasi;
use App\Models\Kriteria;
use App\Models\Periode;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class HasilController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $selectedPeriodeId = $request->input('periode_id');

        // Hanya ambil periode yang SUDAH DIHITUNG DAN BERSTATUS SELESAI
        $calculatedPeriodeIds = HasilPerhitungan::getCalculatedPeriodeIds();
        $periodes = Periode::whereIn('id', $calculatedPeriodeIds)
            ->where('status', Periode::STATUS_SELESAI)
            ->orderBy('created_at', 'desc')
            ->get();

        $activePeriodeId = $selectedPeriodeId;
        if ($activePeriodeId && !$periodes->contains('id', $activePeriodeId)) {
            $activePeriodeId = null;
        }
        if (!$activePeriodeId && $periodes->isNotEmpty()) {
            $activePeriodeId = $periodes->first()->id;
        }

        $selesaiPeriodeIds = $periodes->pluck('id')->toArray();

        $observasisQuery = ObservasiLokasi::with(['user', 'penilaians', 'hasilPerhitungan'])
            ->whereInPeriode($selesaiPeriodeIds);

        if ($activePeriodeId) {
            $observasisQuery->wherePeriode($activePeriodeId);
        }

        if ($search) {
            $observasisQuery->where(function($q) use ($search) {
                $q->where('nama_pemilik', 'like', "%{$search}%")
                  ->orWhere('alamat_lengkap', 'like', "%{$search}%");
            });
        }

        $observasis = $observasisQuery->get()->sortBy(function($obs) {
            return $obs->hasilPerhitungan?->ranking ?? 9999;
        });

        $lokasiTerbaik = $observasis->first();
        $chosenPeriode = $periodes->firstWhere('id', $activePeriodeId);

        return view('manajer.hasil.index', compact(
            'observasis',
            'periodes',
            'activePeriodeId',
            'chosenPeriode',
            'search',
            'lokasiTerbaik'
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
        
        return $pdf->download('Hasil_Observasi_TOPSIS_' . date('Ymd_His') . '.pdf');
    }
}
