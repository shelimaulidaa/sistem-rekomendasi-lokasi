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
     * Menjalankan proses perhitungan TOPSIS dan menyimpan hasilnya ke database.
     * 
     * @param int|null $periodeId
     * @return array Langkah-langkah perhitungan untuk ditampilkan pada view
     */
    public function calculate($periodeId = null): array
    {
        $kriterias = Kriteria::query()
            ->when($periodeId, fn($q) => $q->where('periode_id', $periodeId), fn($q) => $q->whereNull('periode_id'))
            ->orderBy('urutan')
            ->get();

        // Sinkronkan semua observasi aktif untuk periode agar DetailPenilaian selalu terbaru
        $observasiService = app(ObservasiService::class);
        $observasis = \App\Models\ObservasiLokasi::whereNull('deleted_at')
            ->when($periodeId, fn($q) => $q->wherePeriode($periodeId))
            ->get();

        foreach ($observasis as $obs) {
            $observasiService->syncPenilaianForObservasi($obs);
        }

        // Ambil penilaian yang memiliki lokasi aktif dan sesuai periode
        $penilaians = Penilaian::with(['observasiLokasi', 'detailPenilaians'])
            ->whereHas('observasiLokasi', function ($query) use ($periodeId) {
                $query->whereNull('deleted_at');
                if ($periodeId) {
                    $query->wherePeriode($periodeId);
                }
            })
            ->get();

        if ($kriterias->isEmpty() || $penilaians->isEmpty()) {
            throw new \Exception("Perhitungan TOPSIS tidak dapat dilakukan tanpa kriteria dan alternatif.");
        }

        // 1. Periksa Kelengkapan dan Integritas Matriks
        $this->validateMatrixIntegrity($penilaians, $kriterias->count());

        // 2. Normalisasi Bobot Kriteria (agar total bobot = 1)
        $normalizedWeights = $this->normalizeWeights($kriterias);

        // 3. Buat Matriks Keputusan (x_ij)
        [$matrix, $criteriaSums] = $this->buildDecisionMatrix($penilaians, $kriterias);

        // 4. Normalisasi Matriks Keputusan & Pembobotan (v_ij) serta cari Solusi Ideal
        [$normalizedMatrix, $weightedMatrix, $idealPositive, $idealNegative] = $this->calculateIdealsAndWeightedMatrix(
            $penilaians, $kriterias, $matrix, $criteriaSums, $normalizedWeights
        );

        // 5. Hitung Jarak Solusi Ideal dan Nilai Preferensi
        $results = $this->calculatePreferenceScores(
            $penilaians, $kriterias, $weightedMatrix, $idealPositive, $idealNegative
        );

        // Urutkan berdasarkan nilai preferensi secara menurun untuk menentukan peringkat
        usort($results, fn($a, $b) => $b['preference_score'] <=> $a['preference_score']);

        // 6. Simpan hasil perhitungan ke database dalam transaksi masal
        $this->persistResults($results, $periodeId);

        // Kembalikan tahapan perhitungan untuk ditampilkan pada tampilan view
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
     * Menghitung dan mengembalikan matriks langkah-langkah TOPSIS secara lengkap tanpa menyimpan ke DB.
     */
    public function getTopsisSteps($periodeId): ?array
    {
        $kriterias = Kriteria::query()
            ->when($periodeId, fn($q) => $q->where('periode_id', $periodeId), fn($q) => $q->whereNull('periode_id'))
            ->orderBy('urutan')
            ->get();

        if ($kriterias->isEmpty()) return null;

        $penilaians = Penilaian::with(['observasiLokasi', 'detailPenilaians'])
            ->whereHas('observasiLokasi', function ($query) use ($periodeId) {
                $query->whereNull('deleted_at');
                if ($periodeId) {
                    $query->wherePeriode($periodeId);
                }
            })
            ->get();

        if ($penilaians->isEmpty()) return null;

        $totalKriteria = $kriterias->count();
        foreach ($penilaians as $penilaian) {
            if ($penilaian->detailPenilaians->count() !== $totalKriteria) {
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
        if ($totalKriteria === 0) {
            throw new \Exception("Perhitungan TOPSIS gagal: Jumlah kriteria untuk periode ini adalah 0.");
        }

        foreach ($penilaians as $penilaian) {
            $actualCount = $penilaian->detailPenilaians->count();
            if ($actualCount !== $totalKriteria) {
                throw new \Exception("Data matriks tidak konsisten pada lokasi milik '{$penilaian->observasiLokasi->nama_pemilik}'. Diharapkan {$totalKriteria} kriteria, tetapi ditemukan {$actualCount} detail penilaian.");
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

    private function persistResults(array &$results, ?int $periodeId): void
    {
        HasilPerhitungan::wherePeriode($periodeId)->delete();

        DB::transaction(function () use (&$results, $periodeId) {
            $now = now();
            $insertData = [];
            $rank = 1;

            foreach ($results as &$res) {
                $insertData[] = [
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

            if ($periodeId) {
                Periode::where('id', $periodeId)->update(['status' => Periode::STATUS_SELESAI]);
            }
        });
    }
}
