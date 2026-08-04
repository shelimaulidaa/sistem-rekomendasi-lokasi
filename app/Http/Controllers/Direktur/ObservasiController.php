<?php

namespace App\Http\Controllers\Direktur;

use App\Http\Controllers\Controller;
use App\Models\ObservasiLokasi;
use App\Models\HasilPerhitungan;
use App\Models\Penilaian;

use App\Models\Batch;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ObservasiController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $selectedBatchId = $request->input('batch_id');

        // Only get batches that HAVE ALREADY BEEN CALCULATED
        $calculatedBatchIds = HasilPerhitungan::getCalculatedPeriodeIds();
        $batches = Batch::whereIn('id', $calculatedBatchIds)->orderBy('created_at', 'desc')->get();

        $activeBatchId = $selectedBatchId;
        if (!$activeBatchId && $batches->isNotEmpty()) {
            $activeBatchId = $batches->firstWhere('status', Batch::STATUS_SELESAI)?->id ?? $batches->first()->id;
        }

        $observasisQuery = ObservasiLokasi::with(['user', 'penilaians', 'hasilPerhitungan'])
            ->whereInPeriode($calculatedBatchIds);

        if ($activeBatchId) {
            $observasisQuery->wherePeriode($activeBatchId);
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
        $chosenBatch = $batches->firstWhere('id', $activeBatchId);

        return view('direktur.observasi.index', compact(
            'observasis',
            'batches',
            'activeBatchId',
            'chosenBatch',
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

        $periodeId = $observasi->periode_id ?? $observasi->batch_id;
        $kriterias = \App\Models\Kriteria::where('periode_id', $periodeId)
            ->orderBy('urutan')
            ->get();

        $ref = $request->query('ref');
        $referer = $request->header('referer');

        if ($ref === 'hasil' || (empty($ref) && $referer && str_contains($referer, '/hasil'))) {
            $backUrl = route('direktur.observasi.index', ['batch_id' => $periodeId]);
        } elseif ($ref === 'dashboard' || (empty($ref) && $referer && str_contains($referer, '/dashboard'))) {
            $backUrl = route('direktur.dashboard');
        } else {
            $backUrl = route('direktur.observasi.index', ['batch_id' => $periodeId]);
        }

        return view('direktur.observasi.show', compact('observasi', 'hasilTopsis', 'spatialData', 'kriterias', 'backUrl'));
    }

    public function exportPdf($id)
    {
        $observasi = ObservasiLokasi::with([
            'user', 
            'batch', 
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

        $periodeId = $observasi->periode_id ?? $observasi->batch_id;
        $kriterias = \App\Models\Kriteria::where('periode_id', $periodeId)
            ->orderBy('urutan')
            ->get();

        $pdf = Pdf::loadView('manajer.observasi.pdf', compact('observasi', 'spatialData', 'hasilTopsis', 'kriterias'));
        $filename = 'Detail_Observasi_' . str_replace([' ', '/', '\\'], '_', $observasi->nama_pemilik) . '.pdf';
        return $pdf->download($filename);
    }

}
