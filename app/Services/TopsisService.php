<?php

namespace App\Services;

use App\Models\Kriteria;
use App\Models\Penilaian;
use App\Models\HasilPerhitungan;
use Illuminate\Support\Facades\DB;

class TopsisService
{
    /**
     * Executes the TOPSIS calculation process and stores the results.
     * 
     * @return array The calculation steps for display
     */
    public function calculate($batchId): array
    {
        $kriterias = Kriteria::orderBy('urutan')->get();
        // ONLY get penilaians that have an active (non-deleted) lokasi and belong to the batch
        $penilaians = Penilaian::with(['observasiLokasi', 'detailPenilaians'])
            ->whereHas('observasiLokasi', function ($query) use ($batchId) {
                $query->whereNull('deleted_at')
                      ->where('batch_id', $batchId);
            })
            ->get();

        if ($kriterias->isEmpty() || $penilaians->isEmpty()) {
            throw new \Exception("Cannot calculate TOPSIS without criteria and alternatives.");
        }

        // 1. Check Completeness and Integrity
        $totalKriteria = $kriterias->count();
        foreach ($penilaians as $penilaian) {
            if ($penilaian->detailPenilaians->count() < $totalKriteria) {
                throw new \Exception("Data matriks belum lengkap untuk lokasi milik: " . $penilaian->observasiLokasi->nama_pemilik);
            }
            if ($penilaian->detailPenilaians->count() > $totalKriteria) {
                throw new \Exception("Terdeteksi duplikasi data penilaian untuk lokasi milik: " . $penilaian->observasiLokasi->nama_pemilik);
            }
        }

        // 2. Normalize Criteria Weights (so they sum to 1)
        $totalWeight = $kriterias->sum('bobot');
        if ($totalWeight == 0) {
            throw new \Exception("Total bobot kriteria tidak boleh nol.");
        }

        $normalizedWeights = [];
        foreach ($kriterias as $criteria) {
            $normalizedWeights[$criteria->kriteria_id] = $criteria->bobot / $totalWeight;
        }

        // 3. Build Decision Matrix (x_ij)
        $matrix = [];
        $criteriaSums = []; // For denominator of normalized matrix

        // Initialize sums
        foreach ($kriterias as $criteria) {
            $criteriaSums[$criteria->kriteria_id] = 0;
        }

        foreach ($penilaians as $penilaian) {
            foreach ($kriterias as $criteria) {
                $detail = $penilaian->detailPenilaians->where('kriteria_id', $criteria->kriteria_id)->first();
                if ($detail === null || $detail->nilai === null) {
                    throw new \Exception("Nilai kriteria {$criteria->kode_kriteria} kosong atau tidak valid pada lokasi milik {$penilaian->observasiLokasi->nama_pemilik}.");
                }
                $score = (float)$detail->nilai;
                
                $matrix[$penilaian->penilaian_id][$criteria->kriteria_id] = $score;
                
                $sq = pow($score, 2);
                $criteriaSums[$criteria->kriteria_id] += $sq;
            }
        }

        // 4. Normalize Decision Matrix & Weight it (v_ij)
        $normalizedMatrix = [];
        $weightedMatrix = [];
        $idealPositive = [];
        $idealNegative = [];

        foreach ($kriterias as $criteria) {
            $idealPositive[$criteria->kriteria_id] = strtolower($criteria->atribut) === 'benefit' ? -INF : INF;
            $idealNegative[$criteria->kriteria_id] = strtolower($criteria->atribut) === 'benefit' ? INF : -INF;
        }

        foreach ($penilaians as $penilaian) {
            foreach ($kriterias as $criteria) {
                $score = $matrix[$penilaian->penilaian_id][$criteria->kriteria_id];
                $denominator = sqrt($criteriaSums[$criteria->kriteria_id]);
                
                // If all scores for a criteria are 0, prevent division by zero
                $normalizedScore = $denominator > 0 ? round($score / $denominator, 6) : 0;
                $normalizedMatrix[$penilaian->penilaian_id][$criteria->kriteria_id] = $normalizedScore;
                
                $weightedScore = round($normalizedScore * $normalizedWeights[$criteria->kriteria_id], 6);
                $weightedMatrix[$penilaian->penilaian_id][$criteria->kriteria_id] = $weightedScore;

                // Determine Ideals
                if (strtolower($criteria->atribut) === 'benefit') {
                    if ($weightedScore > $idealPositive[$criteria->kriteria_id]) $idealPositive[$criteria->kriteria_id] = $weightedScore;
                    if ($weightedScore < $idealNegative[$criteria->kriteria_id]) $idealNegative[$criteria->kriteria_id] = $weightedScore;
                } else { // cost
                    if ($weightedScore < $idealPositive[$criteria->kriteria_id]) $idealPositive[$criteria->kriteria_id] = $weightedScore;
                    if ($weightedScore > $idealNegative[$criteria->kriteria_id]) $idealNegative[$criteria->kriteria_id] = $weightedScore;
                }
            }
        }

        // 5. Calculate Distances and Preference Scores
        $results = [];
        foreach ($penilaians as $penilaian) {
            $distancePositive = 0;
            $distanceNegative = 0;

            foreach ($kriterias as $criteria) {
                $val = $weightedMatrix[$penilaian->penilaian_id][$criteria->kriteria_id];
                $distancePositive += pow($val - $idealPositive[$criteria->kriteria_id], 2);
                $distanceNegative += pow($val - $idealNegative[$criteria->kriteria_id], 2);
            }

            $dPlus = round(sqrt($distancePositive), 6);
            $dMinus = round(sqrt($distanceNegative), 6);

            // V_i (Preference Score)
            $preferenceScore = ($dPlus + $dMinus) > 0 ? round($dMinus / ($dPlus + $dMinus), 6) : 0;

            $results[] = [
                'penilaian_id' => $penilaian->penilaian_id,
                'nama_lokasi' => $penilaian->observasiLokasi->nama_pemilik,
                'preference_score' => $preferenceScore,
                'd_plus' => $dPlus,
                'd_minus' => $dMinus,
            ];
        }

        // Sort by preference score descending to rank
        usort($results, fn($a, $b) => $b['preference_score'] <=> $a['preference_score']);

        // Only delete results for the current batch
        HasilPerhitungan::where('batch_id', $batchId)->delete();

        // 6. Persist to database inside a transaction
        DB::transaction(function () use (&$results, $batchId) {
            $rank = 1;
            foreach ($results as &$res) {
                HasilPerhitungan::create([
                    'batch_id' => $batchId,
                    'penilaian_id' => $res['penilaian_id'],
                    'nilai_preferensi' => $res['preference_score'],
                    'ranking' => $rank,
                    'tanggal_hitung' => now(),
                ]);
                $res['ranking'] = $rank;
                $rank++;
            }
        });

        // Return steps for view display
        return [
            'kriterias' => $kriterias,
            'matrix' => $matrix,
            'normalizedMatrix' => $normalizedMatrix,
            'weightedMatrix' => $weightedMatrix,
            'idealPositive' => $idealPositive,
            'idealNegative' => $idealNegative,
            'results' => $results
        ];
    }
}
