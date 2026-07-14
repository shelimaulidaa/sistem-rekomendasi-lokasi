<?php

namespace App\Http\Controllers\Manajer;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreObservasiRequest;
use App\Models\ObservasiLokasi;
use App\Services\ObservasiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ObservasiController extends Controller
{
    public function __construct(
        private ObservasiService $observasiService
    ) {}

    public function index(Request $request)
    {
        $search = $request->input('search');
        $batchId = $request->input('batch_id');

        $batches = \App\Models\Batch::orderBy('created_at', 'desc')->get();

        $observasis = ObservasiLokasi::query()
            ->with(['user', 'penilaians'])
            ->when($search, function ($query, $search) {
                return $query->where('nama_pemilik', 'like', "%{$search}%");
            })
            ->when($batchId, function ($query, $batchId) {
                return $query->where('batch_id', $batchId);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('manajer.observasi.index', compact('observasis', 'search', 'batches', 'batchId'));
    }

    public function create()
    {
        $batches = \App\Models\Batch::orderBy('created_at', 'desc')->get();
        return view('manajer.observasi.create', compact('batches'));
    }

    public function store(StoreObservasiRequest $request)
    {
        \Illuminate\Support\Facades\Log::info('REQUEST DATA: ', $request->all());
        $data = $request->validated();
        $photos = $request->file('photos', []);

        $this->observasiService->storeObservation(
            $data, 
            $photos, 
            Auth::id()
        );

        return redirect()->route('manajer.observasi.index')
            ->with('success', 'Observasi dan Penilaian berhasil disimpan.');
    }

    public function show(ObservasiLokasi $observasi)
    {
        $observasi->load(['user', 'dokumentasiLokasis', 'penilaians.detailPenilaians']);
        return view('manajer.observasi.show', compact('observasi'));
    }

    public function destroy(ObservasiLokasi $observasi)
    {
        // We might need to delete the images from storage if required, 
        // but due to SoftDeletes on ObservasiLokasi, we just soft delete it.
        $observasi->delete();
        return redirect()->route('manajer.observasi.index')->with('success', 'Observasi berhasil dihapus.');
    }
}
