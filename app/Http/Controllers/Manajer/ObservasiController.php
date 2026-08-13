<?php

namespace App\Http\Controllers\Manajer;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreObservasiRequest;
use App\Http\Requests\UpdateObservasiRequest;
use App\Models\ObservasiLokasi;
use App\Models\Periode;
use App\Models\HasilPerhitungan;
use App\Models\Kriteria;
use App\Services\ObservasiService;
use App\Services\TopsisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class ObservasiController extends Controller
{
    protected $observasiService;

    public function __construct(ObservasiService $observasiService)
    {
        $this->observasiService = $observasiService;
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $periodeId = $request->input('periode_id');

        // Hanya sertakan periode yang BELUM DIHITUNG
        $calculatedPeriodeIds = HasilPerhitungan::getCalculatedPeriodeIds();
        
        $uncalculatedPeriodes = Periode::whereNotIn('id', $calculatedPeriodeIds)
            ->orderBy('created_at', 'desc')
            ->get();

        $allCalculated = $uncalculatedPeriodes->isEmpty();

        $currentPeriodeId = $periodeId;
        if (!$currentPeriodeId && $uncalculatedPeriodes->isNotEmpty()) {
            $activeUncalculated = $uncalculatedPeriodes->firstWhere('status', Periode::STATUS_DRAFT) ?? $uncalculatedPeriodes->first();
            $currentPeriodeId = $activeUncalculated?->id;
        }

        $observasis = ObservasiLokasi::query()
            ->with(['user', 'penilaians'])
            ->when($currentPeriodeId, function ($query, $pId) {
                return $query->wherePeriode($pId);
            })
            ->when(!$currentPeriodeId, function ($query) use ($calculatedPeriodeIds) {
                return $query->whereNotInPeriode($calculatedPeriodeIds);
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

        // Periksa kelengkapan matriks keputusan via SQL
        $isComplete = false;
        if ($currentPeriodeId) {
            $activeKriteriaKeys = Kriteria::where('periode_id', $currentPeriodeId)
                ->whereNotNull('kunci_observasi')
                ->pluck('kunci_observasi')
                ->toArray();

            $hasIncomplete = false;
            if (!empty($activeKriteriaKeys)) {
                $hasIncomplete = ObservasiLokasi::wherePeriode($currentPeriodeId)
                    ->where(function ($q) use ($activeKriteriaKeys) {
                        $conditions = [];
                        if (in_array('jarak_rph', $activeKriteriaKeys)) {
                            $conditions[] = 'jarak_rph';
                        }
                        if (in_array('biaya_sewa', $activeKriteriaKeys)) {
                            $conditions[] = 'harga_sewa';
                        }
                        if (in_array('jumlah_kompetitor', $activeKriteriaKeys)) {
                            $conditions[] = 'jumlah_kompetitor';
                        }
                        if (!empty($conditions)) {
                            foreach ($conditions as $idx => $col) {
                                if ($idx === 0) {
                                    $q->whereNull($col);
                                } else {
                                    $q->orWhereNull($col);
                                }
                            }
                        } else {
                            $q->whereRaw('1 = 0');
                        }
                    })
                    ->exists();
            }

            $hasObservasis = ObservasiLokasi::wherePeriode($currentPeriodeId)->exists();
            $hasKriteria = Kriteria::where('periode_id', $currentPeriodeId)->exists();
            $isComplete = $hasObservasis && !$hasIncomplete && $hasKriteria;
        }

        $topsisDone = false; // Selalu false pada halaman ini karena hanya menampilkan observasi yang belum dihitung

        // Periksa apakah total bobot kriteria = 100% untuk periode ini
        $totalBobot = 0;
        $bobotCukup = false;
        if ($currentPeriodeId) {
            $kriteriaForPeriode = Kriteria::where('periode_id', $currentPeriodeId)->get();
            $totalBobot = $kriteriaForPeriode->sum('bobot');
            $bobotCukup = abs($totalBobot - 100) < 0.01; // float tolerance
        }

        return view('manajer.observasi.index', [
            'observasis' => $observasis,
            'search' => $search,
            'periodes' => $uncalculatedPeriodes,
            'periodeId' => $currentPeriodeId,
            'topsisDone' => $topsisDone,
            'isComplete' => $isComplete,
            'allCalculated' => $allCalculated,
            'bobotCukup' => $bobotCukup,
            'totalBobot' => $totalBobot,
        ]);
    }

    public function create(Request $request)
    {
        $periodeId = $request->query('periode_id');
        
        if (!$periodeId) {
            $calculatedPeriodeIds = HasilPerhitungan::getCalculatedPeriodeIds();
            $uncalculatedPeriode = Periode::whereNotIn('id', $calculatedPeriodeIds)
                ->where('status', Periode::STATUS_DRAFT)->first() 
                ?? Periode::whereNotIn('id', $calculatedPeriodeIds)->where('status', '!=', Periode::STATUS_DIARSIPKAN)->first();
            $periodeId = $uncalculatedPeriode ? $uncalculatedPeriode->id : null;
        }

        if (!$periodeId) {
            return redirect()->route('manajer.periode.index')
                ->with('error', 'Harap buat periode baru terlebih dahulu sebelum menambahkan observasi lokasi.');
        }

        $chosenPeriode = Periode::findOrFail($periodeId);
        if ($chosenPeriode->isDiarsipkan()) {
            return redirect()->route('manajer.periode.index')
                ->with('error', 'Periode ini sudah diarsipkan dan tidak dapat ditambahkan data observasi baru.');
        }

        $topsisDone = Periode::isPeriodeCalculated($periodeId);
        if ($topsisDone) {
            return redirect()->route('manajer.hasil.index', ['periode_id' => $periodeId])
                ->with('error', 'Tidak dapat menambahkan lokasi baru karena proses perhitungan TOPSIS untuk periode ini sudah selesai. Data berada di menu Hasil Observasi.');
        }

        $kriterias = Kriteria::where('periode_id', $periodeId)
            ->orderBy('urutan')
            ->get();

        return view('manajer.observasi.create', compact('periodeId', 'chosenPeriode', 'kriterias'));
    }

    public function store(StoreObservasiRequest $request)
    {
        $periodeId = $request->input('periode_id');
        $topsisDone = Periode::isPeriodeCalculated($periodeId);
        if ($topsisDone) {
            return redirect()->route('manajer.hasil.index', ['periode_id' => $periodeId])
                ->with('error', 'Tidak dapat menambahkan lokasi baru karena proses perhitungan TOPSIS untuk periode ini sudah selesai dilakukan.');
        }

        $data = $request->validated();
        $data['periode_id'] = $periodeId;
        $photos = $request->file('photos', []);

        $this->observasiService->storeObservation(
            $data, 
            $photos, 
            Auth::id()
        );

        return redirect()->route('manajer.observasi.index', ['periode_id' => $periodeId])
            ->with('success', 'Observasi dan Penilaian berhasil disimpan.');
    }

    public function calculate(Request $request, TopsisService $topsisService)
    {
        $periodeId = $request->input('periode_id');
        if (!$periodeId) {
            return redirect()->back()->with('error', 'Silakan pilih periode yang belum dihitung terlebih dahulu.');
        }

        $periode = Periode::find($periodeId);

        try {
            $topsisService->calculate($periodeId);

            return redirect()->route('manajer.hasil.index', ['periode_id' => $periodeId])
                ->with('success', 'Perhitungan berhasil dilakukan. Silakan buka menu Hasil Observasi untuk melihat rekomendasi lokasi terbaik.')
                ->with('calculation_success', true);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal melakukan perhitungan: ' . $e->getMessage());
        }
    }

    public function show(\Illuminate\Http\Request $request, ObservasiLokasi $observasi)
    {
        $observasi->load(['user', 'dokumentasiLokasis', 'penilaians.detailPenilaians.kriteria', 'periode']);
        
        $spatialData = $observasi->spatial_data;

        $penilaian = $observasi->penilaians->first();
        $hasilTopsis = null;
        if ($penilaian) {
            $hasilTopsis = HasilPerhitungan::where('penilaian_id', $penilaian->penilaian_id)->first();
        }

        $periodeId = $observasi->periode_id;
        $kriterias = Kriteria::where('periode_id', $periodeId)
            ->orderBy('urutan')
            ->get();

        $ref = $request->query('ref');
        $referer = $request->header('referer');

        if ($ref === 'hasil' || (empty($ref) && $referer && str_contains($referer, '/hasil'))) {
            $backUrl = route('manajer.hasil.index', ['periode_id' => $periodeId]);
        } elseif ($ref === 'dashboard' || (empty($ref) && $referer && str_contains($referer, '/dashboard'))) {
            $backUrl = route('dashboard');
        } else {
            $backUrl = route('manajer.observasi.index', ['periode_id' => $periodeId]);
        }

        return view('manajer.observasi.show', compact('observasi', 'spatialData', 'hasilTopsis', 'kriterias', 'backUrl'));
    }

    public function edit(ObservasiLokasi $observasi)
    {
        $topsisDone = Periode::isPeriodeCalculated($observasi->periode_id);
        if ($topsisDone) {
            return redirect()->route('manajer.hasil.index', ['periode_id' => $observasi->periode_id])
                ->with('error', 'Tidak dapat mengedit observasi karena periode ini sudah dihitung dan berada pada Hasil Observasi.');
        }

        $observasi->load(['periode', 'dokumentasiLokasis', 'penilaians.detailPenilaians']);
        $chosenPeriode = $observasi->periode;

        $spatialData = $observasi->spatial_data;

        $periodeId = $observasi->periode_id;
        $kriterias = Kriteria::where('periode_id', $periodeId)
            ->orderBy('urutan')
            ->get();

        return view('manajer.observasi.edit', compact('observasi', 'chosenPeriode', 'spatialData', 'kriterias'));
    }

    public function update(UpdateObservasiRequest $request, ObservasiLokasi $observasi)
    {
        $periodeId = $request->input('periode_id') ?? $observasi->periode_id;
        $topsisDone = Periode::isPeriodeCalculated($observasi->periode_id);
        if ($topsisDone) {
            return redirect()->route('manajer.hasil.index', ['periode_id' => $periodeId])
                ->with('error', 'Tidak dapat mengedit observasi karena periode ini sudah dihitung.');
        }

        $data = $request->validated();
        $data['periode_id'] = $periodeId;
        $photos = $request->file('photos', []);
        $deletePhotoIds = $request->input('delete_photos', []);

        $this->observasiService->updateObservation($observasi, $data, $photos, $deletePhotoIds);

        return redirect()->route('manajer.observasi.index', ['periode_id' => $periodeId])
            ->with('success', 'Observasi dan Penilaian berhasil diperbarui.');
    }

    public function destroy(ObservasiLokasi $observasi)
    {
        $periodeId = $observasi->periode_id;
        $topsisDone = Periode::isPeriodeCalculated($periodeId);
        if ($topsisDone) {
            return redirect()->route('manajer.hasil.index', ['periode_id' => $periodeId])
                ->with('error', 'Tidak dapat menghapus observasi karena periode ini sudah dihitung.');
        }

        $this->observasiService->deleteObservation($observasi);

        return redirect()->route('manajer.observasi.index', ['periode_id' => $periodeId])
            ->with('success', 'Observasi berhasil dihapus.');
    }

    public function exportPdf(ObservasiLokasi $observasi)
    {
        $observasi->load(['user', 'periode', 'dokumentasiLokasis', 'penilaians.detailPenilaians.kriteria']);
        
        $spatialData = $observasi->spatial_data;

        $penilaian = $observasi->penilaians->first();
        $hasilTopsis = null;
        if ($penilaian) {
            $hasilTopsis = HasilPerhitungan::where('penilaian_id', $penilaian->penilaian_id)->first();
        }

        $periodeId = $observasi->periode_id;
        $kriterias = Kriteria::where('periode_id', $periodeId)
            ->orderBy('urutan')
            ->get();

        $pdf = Pdf::loadView('manajer.observasi.pdf', compact('observasi', 'spatialData', 'hasilTopsis', 'kriterias'));

        $filename = 'Detail_Observasi_' . str_replace([' ', '/', '\\'], '_', $observasi->nama_pemilik) . '.pdf';
        return $pdf->download($filename);
    }
}
