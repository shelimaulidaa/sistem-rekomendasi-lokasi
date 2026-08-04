<?php

namespace App\Http\Controllers\Manajer;

use App\Http\Controllers\Controller;
use App\Models\Batch;
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
        $selectedBatchId = $request->input('batch_id');

        // Only get batches that HAVE ALREADY BEEN CALCULATED AND ARE STATUS SELESAI
        $calculatedBatchIds = HasilPerhitungan::getCalculatedPeriodeIds();
        $batches = Periode::whereIn('id', $calculatedBatchIds)
            ->where('status', Periode::STATUS_SELESAI)
            ->orderBy('created_at', 'desc')
            ->get();

        $activeBatchId = $selectedBatchId;
        if ($activeBatchId && !$batches->contains('id', $activeBatchId)) {
            $activeBatchId = null;
        }
        if (!$activeBatchId && $batches->isNotEmpty()) {
            $activeBatchId = $batches->first()->id;
        }

        $selesaiBatchIds = $batches->pluck('id')->toArray();

        $observasisQuery = ObservasiLokasi::with(['user', 'penilaians', 'hasilPerhitungan'])
            ->whereInPeriode($selesaiBatchIds);

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

        return view('manajer.hasil.index', compact(
            'observasis',
            'batches',
            'activeBatchId',
            'chosenBatch',
            'search',
            'lokasiTerbaik'
        ));
    }

    public function exportPdf(Request $request)
    {
        $batchId = $request->query('batch_id');
        $hasilQuery = HasilPerhitungan::with(['penilaian.observasiLokasi'])
            ->whereHas('penilaian.observasiLokasi.periode', function ($q) {
                $q->where('status', Periode::STATUS_SELESAI);
            });
        
        if ($batchId) {
            $hasilQuery->wherePeriode($batchId);
        }
        $hasil = $hasilQuery->orderBy('ranking', 'asc')->get();

        $kriteria = Kriteria::query()
            ->when($batchId, fn($q) => $q->where('periode_id', $batchId), fn($q) => $q->whereNull('periode_id'))
            ->orderBy('urutan')
            ->get();
        
        Carbon::setLocale('id');
        $timestamp = Carbon::now()->translatedFormat('d F Y - H:i') . ' WIB';
        
        $batch = Batch::find($batchId);
        $batchName = $batch ? $batch->nama_batch : 'Semua Batch';

        $pdf = Pdf::loadView('manajer.history.pdf', compact('hasil', 'kriteria', 'timestamp', 'batchName'));
        
        return $pdf->download('Hasil_Observasi_TOPSIS_' . date('Ymd_His') . '.pdf');
    }
}
