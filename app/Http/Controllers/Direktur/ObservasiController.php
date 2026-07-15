<?php

namespace App\Http\Controllers\Direktur;

use App\Http\Controllers\Controller;
use App\Models\ObservasiLokasi;
use App\Models\Penilaian;
use App\Models\HasilPerhitungan;
use Illuminate\Http\Request;

class ObservasiController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $batchId = $request->input('batch_id');

        $batches = \App\Models\Batch::orderBy('created_at', 'desc')->get();

        $query = ObservasiLokasi::with(['user', 'penilaians']);

        if ($search) {
            $query->where('nama_pemilik', 'like', "%{$search}%");
        }

        if ($batchId) {
            $query->where('batch_id', $batchId);
        }

        $observasis = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

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

        return view('direktur.observasi.show', compact('observasi', 'hasilTopsis'));
    }
}
