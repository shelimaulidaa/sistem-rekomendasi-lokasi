<?php

namespace App\Http\Controllers\Manajer;

use App\Http\Controllers\Controller;
use App\Models\Kriteria;
use App\Models\Penilaian;
use Illuminate\Http\Request;

class PenilaianController extends Controller
{
    public function index(Request $request)
    {
        // Get batches for filter dropdown
        $batches = \App\Models\Batch::orderBy('created_at', 'desc')->get();
        
        $activeBatchId = $request->query('batch_id');
        if (!$activeBatchId && $batches->isNotEmpty()) {
            $activeBatchId = $batches->firstWhere('is_active', true)?->id ?? $batches->first()->id;
        }

        // Get all active criteria for the table header, ordered by 'urutan'
        $kriterias = Kriteria::orderBy('urutan')->get();

        // Get all locations that have a valuation (Penilaian) and belong to the selected batch
        $penilaiansQuery = Penilaian::with(['observasiLokasi', 'detailPenilaians.kriteria'])
            ->whereHas('observasiLokasi', function ($q) use ($activeBatchId) {
                if ($activeBatchId) {
                    $q->where('batch_id', $activeBatchId);
                }
            });

        $penilaians = $penilaiansQuery->get();

        $totalKriteria = $kriterias->count();
        $isComplete = true;

        // Build the matrix data structure
        $matrix = [];
        foreach ($penilaians as $penilaian) {
            $row = [
                'penilaian_id' => $penilaian->penilaian_id,
                'nama_pemilik' => $penilaian->observasiLokasi->nama_pemilik,
                'details' => []
            ];

            // Initialize all criteria slots with null
            foreach ($kriterias as $k) {
                $row['details'][$k->kriteria_id] = null;
            }

            // Fill in the actual details
            $detailsCount = 0;
            foreach ($penilaian->detailPenilaians as $detail) {
                $row['details'][$detail->kriteria_id] = $detail->nilai;
                $detailsCount++;
            }

            // Check completeness for this row
            if ($detailsCount < $totalKriteria) {
                $isComplete = false;
            }

            $matrix[] = $row;
        }

        // If there are no penilaians at all, it's not "complete" enough to calculate
        if ($penilaians->isEmpty() || $totalKriteria === 0) {
            $isComplete = false;
        }

        return view('manajer.penilaian.index', compact('kriterias', 'matrix', 'isComplete', 'batches', 'activeBatchId'));
    }

    public function calculate(Request $request, \App\Services\TopsisService $topsisService)
    {
        $batchId = $request->input('batch_id');
        
        if (!$batchId) {
            return redirect()->back()->with('error', 'Silakan pilih batch terlebih dahulu sebelum menghitung.');
        }

        try {
            $topsisService->calculate($batchId);

            return redirect()->route('manajer.penilaian.index', ['batch_id' => $batchId])
                ->with('success', 'Perhitungan TOPSIS berhasil dilakukan!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal melakukan perhitungan: ' . $e->getMessage());
        }
    }
}
