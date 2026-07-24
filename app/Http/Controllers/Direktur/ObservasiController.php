<?php

namespace App\Http\Controllers\Direktur;

use App\Http\Controllers\Controller;
use App\Models\ObservasiLokasi;
use App\Models\HasilPerhitungan;

use App\Models\Batch;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ObservasiController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $batchId = $request->input('batch_id');

        // Only get batches that HAVE ALREADY BEEN CALCULATED
        $fkHp = \Illuminate\Support\Facades\Schema::hasColumn('hasil_perhitungan', 'periode_id') ? 'periode_id' : 'batch_id';
        $calculatedBatchIds = HasilPerhitungan::select($fkHp)->distinct()->pluck($fkHp)->toArray();
        $batches = Batch::whereIn('id', $calculatedBatchIds)->orderBy('created_at', 'desc')->get();

        $query = ObservasiLokasi::with(['user', 'penilaians', 'hasilPerhitungan'])
            ->leftJoin('penilaian', 'observasi_lokasi.id', '=', 'penilaian.observasi_lokasi_id')
            ->leftJoin('hasil_perhitungan', 'penilaian.penilaian_id', '=', 'hasil_perhitungan.penilaian_id')
            ->select('observasi_lokasi.*')
            ->whereInPeriode($calculatedBatchIds);

        if ($search) {
            $query->where('observasi_lokasi.nama_pemilik', 'like', "%{$search}%");
        }

        if ($batchId) {
            $query->where('observasi_lokasi.batch_id', $batchId);
        }

        $observasis = $query->orderBy('hasil_perhitungan.ranking', 'asc')
            ->orderBy('observasi_lokasi.created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('direktur.observasi.index', compact('observasis', 'search', 'batches', 'batchId'));
    }

    public function show($id)
    {
        $observasi = ObservasiLokasi::with([
            'user', 
            'dokumentasiLokasis',
            'penilaians'
        ])->findOrFail($id);

        $penilaian = Penilaian::where('observasi_lokasi_id', $observasi->id)->first();
        $hasilTopsis = null;

        if ($penilaian) {
            $hasilTopsis = HasilPerhitungan::where('penilaian_id', $penilaian->penilaian_id)->first();
        }

        $spatialData = $observasi->spatial_data;

        return view('direktur.observasi.show', compact('observasi', 'hasilTopsis', 'spatialData'));
    }

    public function exportPdf($id)
    {
        $observasi = ObservasiLokasi::with(['user', 'batch'])->findOrFail($id);
        $spatialData = $observasi->spatial_data;
        $pdf = Pdf::loadView('manajer.observasi.pdf', compact('observasi', 'spatialData'));
        $filename = 'Detail_Observasi_' . str_replace([' ', '/', '\\'], '_', $observasi->nama_pemilik) . '.pdf';
        return $pdf->download($filename);
    }

}
