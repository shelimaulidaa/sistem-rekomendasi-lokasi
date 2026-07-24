<?php

namespace App\Services;

use App\Models\Kriteria;
use App\Models\Penilaian;
use App\Models\HasilPerhitungan;
use App\Models\Periode;
use Illuminate\Support\Facades\DB;

class TopsisService
{
    /**
     * Executes the TOPSIS calculation process and stores the results.
     * 
     * @param int|null $batchId
     * @return array The calculation steps for display
     */
    public function calculate($batchId = null): array
    {
        $kriterias = Kriteria::orderBy('urutan')->get();

        // Sync all active observations for the batch to ensure DetailPenilaian is up to date
        $observasiService = app(ObservasiService::class);
        $observasis = \App\Models\ObservasiLokasi::whereNull('deleted_at')
            ->when($batchId, fn($q) => $q->wherePeriode($batchId))
            ->get();

        foreach ($observasis as $obs) {
            $observasiService->syncPenilaianForObservasi($obs);
        }

        // Get penilaians that have an active (non-deleted) lokasi and belong to the batch
        $penilaians = Penilaian::with(['observasiLokasi', 'detailPenilaians'])
            ->whereHas('observasiLokasi', function ($query) use ($batchId) {
                $query->whereNull('deleted_at');
                if ($batchId) {
                    $query->wherePeriode($batchId);
                }
            })
            ->get();

        if ($kriterias->isEmpty() || $penilaians->isEmpty()) {
            throw new \Exception("Cannot calculate TOPSIS without criteria and alternatives.");
        }

        // 1. Check Completeness and Integrity
        $this->validateMatrixIntegrity($penilaians, $kriterias->count());

        // 2. Normalize Criteria Weights (so they sum to 1)
        $normalizedWeights = $this->normalizeWeights($kriterias);

        // 3. Build Decision Matrix (x_ij)
        [$matrix, $criteriaSums] = $this->buildDecisionMatrix($penilaians, $kriterias);

        // 4. Normalize Decision Matrix & Weight it (v_ij) and find Ideals
        [$normalizedMatrix, $weightedMatrix, $idealPositive, $idealNegative] = $this->calculateIdealsAndWeightedMatrix(
            $penilaians, $kriterias, $matrix, $criteriaSums, $normalizedWeights
        );

        // 5. Calculate Distances and Preference Scores
        $results = $this->calculatePreferenceScores(
            $penilaians, $kriterias, $weightedMatrix, $idealPositive, $idealNegative
        );

        // Sort by preference score descending to rank
        usort($results, fn($a, $b) => $b['preference_score'] <=> $a['preference_score']);

        // 6. Persist to database inside a transaction using bulk insert
        $this->persistResults($results, $batchId);

        // Return steps for view display
        return [
            'kriterias' => $kriterias,
            'matrix' => $matrix,
            'normalizedMatrix' => $normalizedMatrix,
            'weightedMatrix' => $weightedMatrix,
            'idealPositive' => $idealPositive,
            'idealNegative' => $idealNegative,
            'results' => $results,
        ];
    }

    /**
     * Calculates and returns the complete step-by-step TOPSIS calculation matrices without saving to DB.
     */
    public function getTopsisSteps($batchId): ?array
    {
        $kriterias = Kriteria::orderBy('urutan')->get();
        if ($kriterias->isEmpty()) return null;

        $penilaians = Penilaian::with(['observasiLokasi', 'detailPenilaians'])
            ->whereHas('observasiLokasi', function ($query) use ($batchId) {
                $query->whereNull('deleted_at');
                if ($batchId) {
                    $query->wherePeriode($batchId);
                }
            })
            ->get();

        if ($penilaians->isEmpty()) return null;

        $totalKriteria = $kriterias->count();
        foreach ($penilaians as $penilaian) {
            if ($penilaian->detailPenilaians->count() < $totalKriteria) {
                return null;
            }
        }

        $normalizedWeights = $this->normalizeWeights($kriterias);
        [$matrix, $criteriaSums] = $this->buildDecisionMatrix($penilaians, $kriterias);
        [$normalizedMatrix, $weightedMatrix, $idealPositive, $idealNegative] = $this->calculateIdealsAndWeightedMatrix(
            $penilaians, $kriterias, $matrix, $criteriaSums, $normalizedWeights
        );

        $distanceResults = [];
        foreach ($penilaians as $penilaian) {
            $dPlusSum = 0;
            $dMinusSum = 0;
            foreach ($kriterias as $criteria) {
                $y = $weightedMatrix[$penilaian->penilaian_id][$criteria->kriteria_id];
                $dPlusSum += pow($y - $idealPositive[$criteria->kriteria_id], 2);
                $dMinusSum += pow($y - $idealNegative[$criteria->kriteria_id], 2);
            }
            $dPlus = round(sqrt($dPlusSum), 6);
            $dMinus = round(sqrt($dMinusSum), 6);
            $pref = ($dPlus + $dMinus) > 0 ? round($dMinus / ($dPlus + $dMinus), 6) : 0;

            $distanceResults[$penilaian->penilaian_id] = [
                'nama_pemilik' => $penilaian->observasiLokasi->nama_pemilik,
                'alamat_lengkap' => $penilaian->observasiLokasi->alamat_lengkap,
                'd_plus' => $dPlus,
                'd_minus' => $dMinus,
                'v' => $pref
            ];
        }

        return [
            'kriterias' => $kriterias,
            'penilaians' => $penilaians,
            'matrix' => $matrix,
            'normalizedMatrix' => $normalizedMatrix,
            'weightedMatrix' => $weightedMatrix,
            'idealPositive' => $idealPositive,
            'idealNegative' => $idealNegative,
            'distanceResults' => $distanceResults
        ];
    }

    private function validateMatrixIntegrity($penilaians, int $totalKriteria): void
    {
        foreach ($penilaians as $penilaian) {
            if ($penilaian->detailPenilaians->count() < $totalKriteria) {
                throw new \Exception("Data matriks belum lengkap untuk lokasi milik: " . $penilaian->observasiLokasi->nama_pemilik);
            }
            if ($penilaian->detailPenilaians->count() > $totalKriteria) {
                throw new \Exception("Terdeteksi duplikasi data penilaian untuk lokasi milik: " . $penilaian->observasiLokasi->nama_pemilik);
            }
        }
    }

    private function normalizeWeights($kriterias): array
    {
        $totalWeight = $kriterias->sum('bobot');
        if ($totalWeight == 0) {
            throw new \Exception("Total bobot kriteria tidak boleh nol.");
        }

        $normalizedWeights = [];
        foreach ($kriterias as $criteria) {
            $normalizedWeights[$criteria->kriteria_id] = $criteria->bobot / $totalWeight;
        }

        return $normalizedWeights;
    }

    private function buildDecisionMatrix($penilaians, $kriterias): array
    {
        $matrix = [];
        $criteriaSums = [];

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
                $criteriaSums[$criteria->kriteria_id] += pow($score, 2);
            }
        }

        return [$matrix, $criteriaSums];
    }

    private function calculateIdealsAndWeightedMatrix($penilaians, $kriterias, array $matrix, array $criteriaSums, array $normalizedWeights): array
    {
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
                
                $normalizedScore = $denominator > 0 ? round($score / $denominator, 6) : 0;
                $normalizedMatrix[$penilaian->penilaian_id][$criteria->kriteria_id] = $normalizedScore;
                
                $weightedScore = round($normalizedScore * $normalizedWeights[$criteria->kriteria_id], 6);
                $weightedMatrix[$penilaian->penilaian_id][$criteria->kriteria_id] = $weightedScore;

                if (strtolower($criteria->atribut) === 'benefit') {
                    if ($weightedScore > $idealPositive[$criteria->kriteria_id]) $idealPositive[$criteria->kriteria_id] = $weightedScore;
                    if ($weightedScore < $idealNegative[$criteria->kriteria_id]) $idealNegative[$criteria->kriteria_id] = $weightedScore;
                } else {
                    if ($weightedScore < $idealPositive[$criteria->kriteria_id]) $idealPositive[$criteria->kriteria_id] = $weightedScore;
                    if ($weightedScore > $idealNegative[$criteria->kriteria_id]) $idealNegative[$criteria->kriteria_id] = $weightedScore;
                }
            }
        }

        return [$normalizedMatrix, $weightedMatrix, $idealPositive, $idealNegative];
    }

    private function calculatePreferenceScores($penilaians, $kriterias, array $weightedMatrix, array $idealPositive, array $idealNegative): array
    {
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
            $preferenceScore = ($dPlus + $dMinus) > 0 ? round($dMinus / ($dPlus + $dMinus), 6) : 0;

            $results[] = [
                'penilaian_id' => $penilaian->penilaian_id,
                'nama_pemilik' => $penilaian->observasiLokasi->nama_pemilik,
                'preference_score' => $preferenceScore,
                'd_plus' => $dPlus,
                'd_minus' => $dMinus,
            ];
        }

        return $results;
    }

    private function persistResults(array &$results, ?int $batchId): void
    {
        HasilPerhitungan::wherePeriode($batchId)->delete();

        DB::transaction(function () use (&$results, $batchId) {
            $now = now();
            $insertData = [];
            $rank = 1;
            $fk = \Illuminate\Support\Facades\Schema::hasColumn('hasil_perhitungan', 'periode_id') ? 'periode_id' : 'batch_id';

            foreach ($results as &$res) {
                $insertData[] = [
                    $fk => $batchId,
                    'penilaian_id' => $res['penilaian_id'],
                    'nilai_preferensi' => $res['preference_score'],
                    'ranking' => $rank,
                    'tanggal_hitung' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                $res['ranking'] = $rank;
                $rank++;
            }

            if (!empty($insertData)) {
                HasilPerhitungan::insert($insertData);
            }

            if ($batchId) {
                Periode::where('id', $batchId)->update(['status' => Periode::STATUS_SELESAI]);
            }
        });
    }
}
