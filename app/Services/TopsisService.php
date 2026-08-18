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
            $penilaians, $kriterias, $weightedMatrix, $idealPositive, $idealNegative, $matrix
        );

        // Kriteria diurutkan berdasarkan bobot terbesar untuk tie-breaker ke-2
        $sortedKriterias = $kriterias->sortByDesc('bobot')->values();

        /**
         * Catatan Tie-Breaker pada Pemeringkatan TOPSIS:
         * 1. Mengapa Tie-Breaker diperlukan?
         *    Dalam pemeringkatan TOPSIS, terdapat kemungkinan dua alternatif menghasilkan nilai preferensi (V_i) yang persis sama.
         *    Tanpa tie-breaker yang eksplisit, urutan peringkat akan ditentukan secara implisit dari urutan data di database.
         *    Tie-breaker menjamin pemeringkatan yang deterministik, obyektif, dan adil.
         *
         * 2. Mengapa D+ (Jarak ke Solusi Ideal Positif) terkecil dipilih sebagai pembanding pertama?
         *    Nilai D+ mengukur jarak alternatif terhadap Solusi Ideal Positif (A+). Alternatif dengan nilai D+ yang lebih kecil
         *    berada lebih dekat dengan nilai-nilai kriteria ideal terbaik, sehingga diprioritaskan mendapat peringkat lebih tinggi.
         *
         * 3. Pembanding kedua (jika D+ juga sama):
         *    Menggunakan nilai pada kriteria dengan bobot tertinggi. Alternatif yang unggul pada kriteria utama diprioritaskan.
         */
        usort($results, function ($a, $b) use ($sortedKriterias) {
            // 1. Pembanding Utama: Nilai Preferensi (V_i) terbesar
            if ($a['preference_score'] !== $b['preference_score']) {
                return $b['preference_score'] <=> $a['preference_score'];
            }

            // 2. Tie-Breaker 1: Jarak D+ terkecil (lebih dekat ke Solusi Ideal Positif)
            if ($a['d_plus'] !== $b['d_plus']) {
                return $a['d_plus'] <=> $b['d_plus'];
            }

            // 3. Tie-Breaker 2: Nilai kriteria dengan bobot tertinggi
            foreach ($sortedKriterias as $criteria) {
                $scoreA = $a['matrix_scores'][$criteria->kriteria_id] ?? 0;
                $scoreB = $b['matrix_scores'][$criteria->kriteria_id] ?? 0;

                if ($scoreA != $scoreB) {
                    return $criteria->isBenefit()
                        ? ($scoreB <=> $scoreA)  // Benefit: Nilai lebih tinggi lebih baik
                        : ($scoreA <=> $scoreB); // Cost: Nilai lebih rendah lebih baik
                }
            }

            return 0;
        });

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

        // ---------------------------------------------------------------------
        // TAHAPAN PERHITUNGAN TOPSIS (STEP BY STEP)
        // ---------------------------------------------------------------------

        // Tahap 1: Normalisasi Bobot Kriteria (w_j) agar total bobot = 1 (100%)
        $normalizedWeights = $this->normalizeWeights($kriterias);

        // Tahap 2: Pembentukan Matriks Keputusan (X) & Jumlah Kuadrat per Kriteria
        [$matrix, $criteriaSums] = $this->buildDecisionMatrix($penilaians, $kriterias);

        // Tahap 3, 4, & 5: Normalisasi Matriks (R), Pembobotan Matriks (Y), dan Solusi Ideal (A+ & A-)
        [$normalizedMatrix, $weightedMatrix, $idealPositive, $idealNegative] = $this->calculateIdealsAndWeightedMatrix(
            $penilaians, $kriterias, $matrix, $criteriaSums, $normalizedWeights
        );

        // Tahap 6 & 7: Perhitungan Jarak Euclidean (D+ & D-) serta Nilai Preferensi (V_i)
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

    /**
     * TAHAP 1: Validasi Kelengkapan dan Integritas Matriks Keputusan
     * Memastikan bahwa setiap calon lokasi memiliki jumlah detail penilaian yang sesuai dengan total kriteria.
     */
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

    /**
     * TAHAP 2: Normalisasi Bobot Kriteria (w_j)
     * Mengubah bobot mentah setiap kriteria sehingga total seluruh bobot kriteria bernilai 1 (100%).
     * Rumus: w_j = W_j / sum(W_k)
     */
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

    /**
     * TAHAP 3: Pembentukan Matriks Keputusan (X) & Jumlah Kuadrat Kriteria
     * Menyusun matriks x_ij (nilai lokasi i pada kriteria j) dan menghitung sum(x_ij^2) 
     * sebagai pembagi Euclidean length untuk normalisasi.
     */
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
                
                // Jumlahkan kuadrat nilai kriteria untuk rumus pembagi: sqrt(sum(x_ij^2))
                $criteriaSums[$criteria->kriteria_id] += pow($score, 2);
            }
        }

        return [$matrix, $criteriaSums];
    }

    /**
     * TAHAP 4, 5, & 6: Matriks Ternormalisasi (R), Matriks Terbobot (Y), dan Solusi Ideal (A+ & A-)
     * 
     * 1. Matriks Ternormalisasi (R): r_ij = x_ij / sqrt(sum(x_kj^2))
     * 2. Matriks Terbobot (Y): y_ij = w_j * r_ij
     * 3. Solusi Ideal Positif (A+) dan Solusi Ideal Negatif (A-):
     *    - Kriteria Benefit : A+ = max(y_ij), A- = min(y_ij)
     *    - Kriteria Cost    : A+ = min(y_ij), A- = max(y_ij)
     */
    private function calculateIdealsAndWeightedMatrix($penilaians, $kriterias, array $matrix, array $criteriaSums, array $normalizedWeights): array
    {
        $normalizedMatrix = [];
        $weightedMatrix = [];
        $idealPositive = [];
        $idealNegative = [];

        // Inisialisasi nilai ideal awal
        foreach ($kriterias as $criteria) {
            $idealPositive[$criteria->kriteria_id] = strtolower($criteria->atribut) === 'benefit' ? -INF : INF;
            $idealNegative[$criteria->kriteria_id] = strtolower($criteria->atribut) === 'benefit' ? INF : -INF;
        }

        foreach ($penilaians as $penilaian) {
            foreach ($kriterias as $criteria) {
                $score = $matrix[$penilaian->penilaian_id][$criteria->kriteria_id];
                $denominator = sqrt($criteriaSums[$criteria->kriteria_id]);
                
                // Langkah 4: Normalisasi Matriks Keputusan (r_ij)
                $normalizedScore = $denominator > 0 ? round($score / $denominator, 6) : 0;
                $normalizedMatrix[$penilaian->penilaian_id][$criteria->kriteria_id] = $normalizedScore;
                
                // Langkah 5: Pembobotan Matriks Keputusan (y_ij = w_j * r_ij)
                $weightedScore = round($normalizedScore * $normalizedWeights[$criteria->kriteria_id], 6);
                $weightedMatrix[$penilaian->penilaian_id][$criteria->kriteria_id] = $weightedScore;

                // Langkah 6: Penentuan Solusi Ideal Positif (A+) & Negatif (A-)
                if (strtolower($criteria->atribut) === 'benefit') {
                    if ($weightedScore > $idealPositive[$criteria->kriteria_id]) $idealPositive[$criteria->kriteria_id] = $weightedScore;
                    if ($weightedScore < $idealNegative[$criteria->kriteria_id]) $idealNegative[$criteria->kriteria_id] = $weightedScore;
                } else { // Cost
                    if ($weightedScore < $idealPositive[$criteria->kriteria_id]) $idealPositive[$criteria->kriteria_id] = $weightedScore;
                    if ($weightedScore > $idealNegative[$criteria->kriteria_id]) $idealNegative[$criteria->kriteria_id] = $weightedScore;
                }
            }
        }

        return [$normalizedMatrix, $weightedMatrix, $idealPositive, $idealNegative];
    }

    /**
     * TAHAP 7 & 8: Perhitungan Jarak Solusi Ideal (D+ & D-) dan Nilai Preferensi (V_i)
     * 
     * 1. Jarak Ideal Positif (D_i+) = sqrt(sum((y_ij - A+_j)^2))
     * 2. Jarak Ideal Negatif (D_i-) = sqrt(sum((y_ij - A-_j)^2))
     * 3. Nilai Preferensi (V_i)      = D_i- / (D_i+ + D_i-)
     */
    private function calculatePreferenceScores($penilaians, $kriterias, array $weightedMatrix, array $idealPositive, array $idealNegative, array $matrix = []): array
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

            // Hitung Jarak Euclidean D+ dan D-
            $dPlus = round(sqrt($distancePositive), 6);
            $dMinus = round(sqrt($distanceNegative), 6);
            
            // Hitung Nilai Preferensi V_i
            $preferenceScore = ($dPlus + $dMinus) > 0 ? round($dMinus / ($dPlus + $dMinus), 6) : 0;

            $results[] = [
                'penilaian_id' => $penilaian->penilaian_id,
                'nama_pemilik' => $penilaian->observasiLokasi->nama_pemilik,
                'preference_score' => $preferenceScore,
                'd_plus' => $dPlus,
                'd_minus' => $dMinus,
                'matrix_scores' => $matrix[$penilaian->penilaian_id] ?? [],
            ];
        }

        return $results;
    }

    /**
     * TAHAP 10: Menyimpan Hasil Perhitungan dan Pemeringkatan ke Database
     */
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
