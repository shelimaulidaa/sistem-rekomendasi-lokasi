<?php

namespace App\Http\Controllers\Direktur;

use App\Http\Controllers\Controller;
use App\Models\ObservasiLokasi;
use App\Models\HasilPerhitungan;
use App\Models\Penilaian;
use App\Models\Periode;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ObservasiController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $selectedPeriodeId = $request->input('periode_id');

        // Hanya ambil periode yang SUDAH DIHITUNG
        $calculatedPeriodeIds = HasilPerhitungan::getCalculatedPeriodeIds();
        $periodes = Periode::whereIn('id', $calculatedPeriodeIds)->orderBy('created_at', 'desc')->get();

        $activePeriodeId = $selectedPeriodeId;
        if (!$activePeriodeId && $periodes->isNotEmpty()) {
            $activePeriodeId = $periodes->firstWhere('status', Periode::STATUS_SELESAI)?->id ?? $periodes->first()->id;
        }

        $observasisQuery = ObservasiLokasi::with(['user', 'penilaians', 'hasilPerhitungan'])
            ->whereInPeriode($calculatedPeriodeIds);

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

        return view('direktur.observasi.index', compact(
            'observasis',
            'periodes',
            'activePeriodeId',
            'chosenPeriode',
            'search',
            'lokasiTerbaik'
        ));
    }

    public function show(\Illuminate\Http\Request $request, $id)
    {
        $observasi = ObservasiLokasi::with([
            'user', 
            'dokumentasiLokasis',
            'penilaians.detailPenilaians.kriteria',
            'periode'
        ])->findOrFail($id);

        $penilaian = Penilaian::where('observasi_lokasi_id', $observasi->id)->first();
        $hasilTopsis = null;

        if ($penilaian) {
            $hasilTopsis = HasilPerhitungan::where('penilaian_id', $penilaian->penilaian_id)->first();
        }

        $spatialData = $observasi->spatial_data;

        $periodeId = $observasi->periode_id;
        $kriterias = \App\Models\Kriteria::where('periode_id', $periodeId)
            ->orderBy('urutan')
            ->get();

        $ref = $request->query('ref');
        $referer = $request->header('referer');

        if ($ref === 'hasil' || (empty($ref) && $referer && str_contains($referer, '/hasil'))) {
            $backUrl = route('direktur.observasi.index', ['periode_id' => $periodeId]);
        } elseif ($ref === 'dashboard' || (empty($ref) && $referer && str_contains($referer, '/dashboard'))) {
            $backUrl = route('direktur.dashboard');
        } else {
            $backUrl = route('direktur.observasi.index', ['periode_id' => $periodeId]);
        }

        return view('direktur.observasi.show', compact('observasi', 'hasilTopsis', 'spatialData', 'kriterias', 'backUrl'));
    }

    public function exportPdf($id)
    {
        $observasi = ObservasiLokasi::with([
            'user', 
            'periode', 
            'dokumentasiLokasis', 
            'penilaians.detailPenilaians.kriteria'
        ])->findOrFail($id);

        $spatialData = $observasi->spatial_data;

        $penilaian = Penilaian::where('observasi_lokasi_id', $observasi->id)->first();
        $hasilTopsis = null;

        if ($penilaian) {
            $hasilTopsis = HasilPerhitungan::where('penilaian_id', $penilaian->penilaian_id)->first();
        }

        $periodeId = $observasi->periode_id;
        $kriterias = \App\Models\Kriteria::where('periode_id', $periodeId)
            ->orderBy('urutan')
            ->get();

        $pdf = Pdf::loadView('manajer.observasi.pdf', compact('observasi', 'spatialData', 'hasilTopsis', 'kriterias'));
        $filename = 'Detail_Observasi_' . str_replace([' ', '/', '\\'], '_', $observasi->nama_pemilik) . '.pdf';
        return $pdf->download($filename);
    }
}
