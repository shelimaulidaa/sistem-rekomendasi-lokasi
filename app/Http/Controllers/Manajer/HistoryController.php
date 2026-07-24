<?php

namespace App\Http\Controllers\Manajer;

use App\Http\Controllers\Controller;
use App\Models\HasilPerhitungan;
use App\Models\Batch;
use App\Models\Kriteria;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $batches = Batch::orderBy('created_at', 'desc')->get();
        $activeBatchId = $request->query('batch_id');

        if (!$activeBatchId && $batches->isNotEmpty()) {
            $activeBatchId = $batches->firstWhere('status', Batch::STATUS_SELESAI)?->id ?? $batches->first()->id;
        }

        $hasilQuery = HasilPerhitungan::with(['penilaian.observasiLokasi']);
        if ($activeBatchId) {
            $hasilQuery->wherePeriode($activeBatchId);
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
            'batches',
            'activeBatchId'
        ));
    }

    public function exportPdf(Request $request)
    {
        $batchId = $request->query('batch_id');
        $hasilQuery = HasilPerhitungan::with(['penilaian.observasiLokasi']);
        
        if ($batchId) {
            $hasilQuery->wherePeriode($batchId);
        }

        $hasil = $hasilQuery->orderBy('ranking', 'asc')->get();

        $kriteria = Kriteria::orderBy('urutan')->get();
        
        Carbon::setLocale('id');
        $timestamp = Carbon::now()->translatedFormat('d F Y - H:i') . ' WIB';
        
        $batch = Batch::find($batchId);
        $batchName = $batch ? $batch->nama_batch : 'Semua Batch';

        // Assuming there's a view manajer.history.pdf
        $pdf = Pdf::loadView('manajer.history.pdf', compact('hasil', 'kriteria', 'timestamp', 'batchName'));
        
        return $pdf->download('Laporan_Riwayat_TOPSIS_' . date('Ymd_His') . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        // For simplicity we will skip Excel implementation or use the existing one if there's a specific class.
        // If there was an export class, we should move it.
        // Let's just return back with a message for now, or if you had an export class, use it.
        return redirect()->back()->with('error', 'Export Excel belum tersedia.');
    }
}
