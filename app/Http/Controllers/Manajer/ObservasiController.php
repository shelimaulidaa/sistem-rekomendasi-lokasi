<?php

namespace App\Http\Controllers\Manajer;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreObservasiRequest;
use App\Models\Batch;
use App\Models\HasilPerhitungan;
use App\Models\Kriteria;
use App\Models\ObservasiLokasi;
use App\Services\ObservasiService;
use App\Services\TopsisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;


class ObservasiController extends Controller
{
    public function __construct(
        private ObservasiService $observasiService
    ) {}

    public function index(Request $request)
    {
        $search = $request->input('search');
        $batchId = $request->input('batch_id');

        // Only include batches that have NOT been calculated yet (Uncalculated / Pending batches)
        $fkHp = \Illuminate\Support\Facades\Schema::hasColumn('hasil_perhitungan', 'periode_id') ? 'periode_id' : 'batch_id';
        $calculatedBatchIds = HasilPerhitungan::select($fkHp)->distinct()->pluck($fkHp)->toArray();
        
        $uncalculatedBatches = Batch::whereNotIn('id', $calculatedBatchIds)
            ->orderBy('created_at', 'desc')
            ->get();

        $allCalculated = $uncalculatedBatches->isEmpty();

        $currentBatchId = $batchId;
        if (!$currentBatchId && $uncalculatedBatches->isNotEmpty()) {
            $activeUncalculated = $uncalculatedBatches->firstWhere('status', Batch::STATUS_DRAFT) ?? $uncalculatedBatches->first();
            $currentBatchId = $activeUncalculated?->id;
        }

        $observasis = ObservasiLokasi::query()
            ->with(['user', 'penilaians'])
            ->when($currentBatchId, function ($query, $bId) {
                return $query->wherePeriode($bId);
            })
            ->when(!$currentBatchId, function ($query) use ($calculatedBatchIds) {
                return $query->whereNotInPeriode($calculatedBatchIds);
            })
            ->when($search, function ($query, $search) {
                return $query->where(function($q) use ($search) {
                    $q->where('nama_pemilik', 'like', "%{$search}%")
                      ->orWhere('alamat_lengkap', 'like', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        // Check matrix completeness for calculation on this uncalculated batch via SQL
        $isComplete = false;
        if ($currentBatchId) {
            $hasIncomplete = ObservasiLokasi::wherePeriode($currentBatchId)
                ->where(function ($q) {
                    $q->whereNull('aksesibilitas_score')
                      ->orWhereNull('kelayakan_score')
                      ->orWhereNull('jarak_rph')
                      ->orWhereNull('harga_sewa')
                      ->orWhereNull('jumlah_kompetitor');
                })
                ->exists();
            $hasObservasis = ObservasiLokasi::wherePeriode($currentBatchId)->exists();
            $isComplete = $hasObservasis && !$hasIncomplete && Kriteria::exists();
        }


        $topsisDone = false; // Always false on this page since it shows uncalculated observations

        return view('manajer.observasi.index', [
            'observasis' => $observasis,
            'search' => $search,
            'batches' => $uncalculatedBatches,
            'batchId' => $currentBatchId,
            'topsisDone' => $topsisDone,
            'isComplete' => $isComplete,
            'allCalculated' => $allCalculated,
        ]);
    }

    public function create(Request $request)
    {
        $batchId = $request->query('batch_id');
        
        if (!$batchId) {
            $fkHp = \Illuminate\Support\Facades\Schema::hasColumn('hasil_perhitungan', 'periode_id') ? 'periode_id' : 'batch_id';
            $calculatedBatchIds = HasilPerhitungan::select($fkHp)->distinct()->pluck($fkHp)->toArray();
            $uncalculatedBatch = Batch::whereNotIn('id', $calculatedBatchIds)
                ->where('status', Batch::STATUS_DRAFT)->first() 
                ?? Batch::whereNotIn('id', $calculatedBatchIds)->where('status', '!=', Batch::STATUS_DIARSIPKAN)->first();
            $batchId = $uncalculatedBatch ? $uncalculatedBatch->id : null;
        }

        if (!$batchId) {
            return redirect()->route('manajer.periode.index')
                ->with('error', 'Harap buat periode baru terlebih dahulu sebelum menambahkan observasi lokasi.');
        }

        $chosenBatch = Batch::findOrFail($batchId);
        if ($chosenBatch->isDiarsipkan()) {
            return redirect()->route('manajer.periode.index')
                ->with('error', 'Periode ini sudah diarsipkan dan tidak dapat ditambahkan data observasi baru.');
        }

        $topsisDone = Batch::isBatchCalculated($batchId);
        if ($topsisDone) {
            return redirect()->route('manajer.hasil.index', ['batch_id' => $batchId])
                ->with('error', 'Tidak dapat menambahkan lokasi baru karena proses perhitungan TOPSIS untuk periode ini sudah selesai. Data berada di menu Hasil Observasi.');
        }

        return view('manajer.observasi.create', compact('batchId', 'chosenBatch'));
    }

    public function store(StoreObservasiRequest $request)
    {
        $batchId = $request->input('batch_id');
        $topsisDone = Batch::isBatchCalculated($batchId);
        if ($topsisDone) {
            return redirect()->route('manajer.hasil.index', ['batch_id' => $batchId])
                ->with('error', 'Tidak dapat menambahkan lokasi baru karena proses perhitungan TOPSIS untuk periode ini sudah selesai dilakukan.');
        }

        \Illuminate\Support\Facades\Log::info('REQUEST DATA: ', $request->all());
        $data = $request->validated();
        $photos = $request->file('photos', []);

        $this->observasiService->storeObservation(
            $data, 
            $photos, 
            Auth::id()
        );

        return redirect()->route('manajer.observasi.index', ['batch_id' => $batchId])
            ->with('success', 'Observasi dan Penilaian berhasil disimpan.');
    }

    public function calculate(Request $request, TopsisService $topsisService)
    {
        $batchId = $request->input('batch_id');
        if (!$batchId) {
            return redirect()->back()->with('error', 'Silakan pilih periode yang belum dihitung terlebih dahulu.');
        }

        $batch = Batch::find($batchId);

        try {
            $topsisService->calculate($batchId);

            return redirect()->route('manajer.hasil.index', ['batch_id' => $batchId])
                ->with('success', 'Perhitungan TOPSIS berhasil dilakukan! Data observasi periode "' . ($batch->nama_periode ?? '') . '" secara otomatis telah berpindah ke menu Hasil Observasi.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal melakukan perhitungan: ' . $e->getMessage());
        }
    }


    public function show(ObservasiLokasi $observasi)
    {
        $observasi->load(['user', 'dokumentasiLokasis', 'penilaians.detailPenilaians']);
        
        $spatialData = $observasi->spatial_data;

        $penilaian = $observasi->penilaians->first();
        $hasilTopsis = null;
        if ($penilaian) {
            $hasilTopsis = HasilPerhitungan::where('penilaian_id', $penilaian->penilaian_id)->first();
        }

        return view('manajer.observasi.show', compact('observasi', 'spatialData', 'hasilTopsis'));
    }

    public function edit(ObservasiLokasi $observasi)
    {
        $topsisDone = Batch::isBatchCalculated($observasi->batch_id);
        if ($topsisDone) {
            return redirect()->route('manajer.hasil.index', ['batch_id' => $observasi->batch_id])
                ->with('error', 'Tidak dapat mengedit observasi karena periode ini sudah dihitung dan berada pada Hasil Observasi.');
        }

        $observasi->load(['batch', 'dokumentasiLokasis']);
        $chosenBatch = $observasi->batch;

        $spatialData = $observasi->spatial_data;

        return view('manajer.observasi.edit', compact('observasi', 'chosenBatch', 'spatialData'));
    }


    public function update(StoreObservasiRequest $request, ObservasiLokasi $observasi)
    {
        $batchId = $request->input('batch_id') ?? $observasi->batch_id;
        $topsisDone = Batch::isBatchCalculated($observasi->batch_id);
        if ($topsisDone) {
            return redirect()->route('manajer.hasil.index', ['batch_id' => $batchId])
                ->with('error', 'Tidak dapat mengedit observasi karena periode ini sudah dihitung.');
        }

        $data = $request->validated();
        $photos = $request->file('photos', []);
        $deletePhotoIds = $request->input('delete_photos', []);

        $this->observasiService->updateObservation($observasi, $data, $photos, $deletePhotoIds);

        return redirect()->route('manajer.observasi.index', ['batch_id' => $batchId])
            ->with('success', 'Observasi dan Penilaian berhasil diperbarui.');
    }

    public function destroy(ObservasiLokasi $observasi)
    {
        $batchId = $observasi->batch_id;
        $topsisDone = Batch::isBatchCalculated($batchId);
        if ($topsisDone) {
            return redirect()->route('manajer.hasil.index', ['batch_id' => $batchId])
                ->with('error', 'Tidak dapat menghapus observasi karena periode ini sudah dihitung.');
        }

        $observasi->delete();
        return redirect()->route('manajer.observasi.index', ['batch_id' => $batchId])->with('success', 'Observasi berhasil dihapus.');
    }

    public function exportPdf(ObservasiLokasi $observasi)
    {
        $observasi->load(['user', 'batch']);
        
        $spatialData = $observasi->spatial_data;

        $pdf = Pdf::loadView('manajer.observasi.pdf', compact('observasi', 'spatialData'));

        $filename = 'Detail_Observasi_' . str_replace([' ', '/', '\\'], '_', $observasi->nama_pemilik) . '.pdf';
        return $pdf->download($filename);
    }
}
