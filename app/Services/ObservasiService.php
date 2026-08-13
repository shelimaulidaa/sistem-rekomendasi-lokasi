<?php

namespace App\Services;

use App\Models\ObservasiLokasi;
use App\Models\DokumentasiLokasi;
use App\Models\Penilaian;
use App\Models\DetailPenilaian;
use App\Models\Kriteria;
use App\Models\HasilPerhitungan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ObservasiService
{
    /**
     * Menyimpan data observasi, mengompres foto, dan membuat penilaian TOPSIS secara transaksional.
     */
    public function storeObservation(array $data, array $photos, int $userId): ObservasiLokasi
    {
        return DB::transaction(function () use ($data, $photos, $userId) {
            // 1. Hitung Skor Aksesibilitas dan Kelayakan
            $akses_score = $this->calculateAksesibilitas($data);
            $layak_score = $this->calculateKelayakan($data);

            // 2. Normalisasi dan Simpan Observasi
            $data['user_id'] = $userId;
            $data = $this->normalizeBooleanFields($data);

            $observasi = ObservasiLokasi::create($data);

            // Sinkronkan kompetitor kustom jika ada
            $this->syncCompetitors($observasi, $data);

            // 3. Olah & Simpan Foto
            $this->processAndSavePhotos($photos, $observasi->id);

            // 4. Buat Penilaian & DetailPenilaian
            $this->generatePenilaian($observasi, $data, $akses_score, $layak_score);

            return $observasi;
        });
    }

    /**
     * Menyinkronkan catatan Penilaian dan DetailPenilaian langsung dari model ObservasiLokasi.
     */
    public function syncPenilaianForObservasi(ObservasiLokasi $observasi, array $data = []): Penilaian
    {
        $penilaian = Penilaian::firstOrCreate(
            ['observasi_lokasi_id' => $observasi->id],
            ['user_id' => $observasi->user_id ?? 1, 'tanggal_penilaian' => now()]
        );
        $penilaian->load('detailPenilaians');

        $aksesScore = isset($data['akses_roda4']) || isset($data['dekat_fasilitas']) 
            ? $this->calculateAksesibilitas($data) 
            : $observasi->aksesibilitas_score;

        $layakScore = isset($data['luas_mencukupi']) || isset($data['bangunan_layak']) 
            ? $this->calculateKelayakan($data) 
            : $observasi->kelayakan_score;

        $existingValues = [];
        foreach ($penilaian->detailPenilaians as $dp) {
            $existingValues[$dp->kriteria_id] = $dp->nilai;
        }

        $mergedData = array_merge([
            'harga_sewa' => $observasi->harga_sewa,
            'jumlah_kompetitor' => $observasi->jumlah_kompetitor,
            'jarak_rph' => $observasi->jarak_rph,
            'kriteria_values' => $existingValues,
        ], $data);

        if (isset($data['kriteria_values']) && is_array($data['kriteria_values'])) {
            $mergedData['kriteria_values'] = $data['kriteria_values'] + $existingValues;
        }

        $penilaian->setRelation('observasiLokasi', $observasi);

        DetailPenilaian::where('penilaian_id', $penilaian->penilaian_id)->delete();
        $this->generatePenilaianFromHeader($penilaian, $mergedData, $aksesScore, $layakScore, $observasi->periode_id);

        return $penilaian;
    }

    /**
     * Memperbarui observasi yang ada dan nilainya secara transaksional.
     */
    public function updateObservation(ObservasiLokasi $observasi, array $data, array $photos, array $deletePhotoIds = []): ObservasiLokasi
    {
        return DB::transaction(function () use ($observasi, $data, $photos, $deletePhotoIds) {
            $rawInputData = $data;

            // 1. Normalisasi dan Perbarui Observasi
            $data = $this->normalizeBooleanFields($data);
            $observasi->update($data);

            // Sinkronkan kompetitor kustom jika ada
            $this->syncCompetitors($observasi, $data);

            // 3. Hapus foto yang diminta
            if (!empty($deletePhotoIds)) {
                $this->deletePhotoFiles($observasi->id, $deletePhotoIds);
            }

            // 4. Olah & Simpan Foto Baru
            $this->processAndSavePhotos($photos, $observasi->id);

            // 5. Perbarui Penilaian & DetailPenilaian
            $observasi->refresh();
            $this->syncPenilaianForObservasi($observasi, $rawInputData);

            return $observasi;
        });
    }

    /**
     * Menghapus observasi secara permanen beserta foto, penilaian, dan data terkait.
     */
    public function deleteObservation(ObservasiLokasi $observasi): void
    {
        DB::transaction(function () use ($observasi) {
            // 1. Hapus file foto dari penyimpanan dan database
            foreach ($observasi->dokumentasiLokasis as $doc) {
                if ($doc->foto_path && Storage::disk('public')->exists($doc->foto_path)) {
                    Storage::disk('public')->delete($doc->foto_path);
                }
                $doc->delete();
            }

            // 2. Hapus Penilaian, DetailPenilaian, dan HasilPerhitungan
            foreach ($observasi->penilaians as $penilaian) {
                DetailPenilaian::where('penilaian_id', $penilaian->penilaian_id)->delete();
                HasilPerhitungan::where('penilaian_id', $penilaian->penilaian_id)->delete();
                $penilaian->delete();
            }

            // 3. Hapus permanen catatan ObservasiLokasi
            $observasi->forceDelete();
        });
    }

    private function normalizeBooleanFields(array $data): array
    {
        $map = [
            'akses_jalan_utama' => ['akses_jalan_utama', 'akses_roda4'],
            'akses_kendaraan_operasional' => ['akses_kendaraan_operasional', 'dekat_fasilitas'],
            'kondisi_jalan_baik' => ['kondisi_jalan_baik', 'jalan_bagus'],
            'mudah_ditemukan_google_maps' => ['mudah_ditemukan_google_maps', 'mudah_ditemukan'],
            'mudah_dijangkau_pelanggan' => ['mudah_dijangkau_pelanggan', 'mudah_dijangkau'],
            'luas_bangunan_mencukupi' => ['luas_bangunan_mencukupi', 'luas_mencukupi'],
            'kondisi_bangunan_baik' => ['kondisi_bangunan_baik', 'bangunan_layak'],
            'ventilasi_sirkulasi_memadai' => ['ventilasi_sirkulasi_memadai', 'ventilasi_baik'],
            'air_listrik_tersedia' => ['air_listrik_tersedia', 'air_listrik_memadai'],
            'area_parkir_memadai' => ['area_parkir_memadai', 'parkir_memadai'],
        ];

        foreach ($map as $newCol => $possibleKeys) {
            $val = false;
            foreach ($possibleKeys as $key) {
                if (isset($data[$key]) && filter_var($data[$key], FILTER_VALIDATE_BOOLEAN)) {
                    $val = true;
                    break;
                }
            }
            $data[$newCol] = $val;
        }

        $oldKeys = ['akses_roda4', 'jalan_bagus', 'dekat_fasilitas', 'mudah_ditemukan', 'mudah_dijangkau', 'bangunan_layak', 'ventilasi_baik', 'air_listrik_memadai', 'luas_mencukupi', 'parkir_memadai'];
        foreach ($oldKeys as $oldKey) {
            unset($data[$oldKey]);
        }

        return $data;
    }

    private function deletePhotoFiles(int $observasiId, array $deletePhotoIds): void
    {
        $photosToDelete = DokumentasiLokasi::whereIn('foto_id', $deletePhotoIds)
            ->where('observasi_lokasi_id', $observasiId)
            ->get();

        foreach ($photosToDelete as $doc) {
            if (Storage::disk('public')->exists($doc->foto_path)) {
                Storage::disk('public')->delete($doc->foto_path);
            }
            $doc->delete();
        }
    }

    private function syncCompetitors(ObservasiLokasi $observasi, array $data): void
    {
        if (!isset($data['competitors_data'])) {
            return;
        }

        $competitorsArray = is_array($data['competitors_data']) 
            ? $data['competitors_data'] 
            : json_decode($data['competitors_data'], true);

        if (!is_array($competitorsArray)) {
            return;
        }

        $existingCatatan = $observasi->catatan ?? '';
        $cleanCatatan = trim(preg_replace('/<!--COMPETITORS_DATA:.*?-->/s', '', $existingCatatan));

        $competitorsJson = json_encode(array_values($competitorsArray));
        $newCatatan = $cleanCatatan . ($cleanCatatan !== '' ? "\n" : "") . '<!--COMPETITORS_DATA:' . $competitorsJson . '-->';

        $observasi->update([
            'jumlah_kompetitor' => count($competitorsArray),
            'catatan' => $newCatatan,
        ]);
    }

    private function processAndSavePhotos(array $photos, int $observasiId): void
    {
        if (empty($photos)) return;

        foreach ($photos as $photo) {
            $extension = $photo->getClientOriginalExtension() ?: 'jpg';
            $filename = 'obs_' . $observasiId . '_' . uniqid() . '.' . $extension;
            $path = $photo->storeAs('dokumentasi_lokasi', $filename, 'public');

            DokumentasiLokasi::create([
                'observasi_lokasi_id' => $observasiId,
                'foto_path' => $path,
            ]);
        }
    }

    private function generatePenilaian(ObservasiLokasi $observasi, array $data, int $aksesScore, int $layakScore): void
    {
        $penilaian = Penilaian::create([
            'observasi_lokasi_id' => $observasi->id,
            'user_id' => $observasi->user_id,
            'tanggal_penilaian' => now(),
        ]);

        $this->generatePenilaianFromHeader($penilaian, $data, $aksesScore, $layakScore);
    }

    private function generatePenilaianFromHeader(Penilaian $penilaian, array $data, int $aksesScore, int $layakScore, ?int $periodeId = null): void
    {
        if (!$periodeId) {
            $periodeId = $penilaian->observasiLokasi?->periode_id;
        }
        $kriteriaList = Kriteria::query()
            ->when($periodeId, function ($query, $pId) {
                return $query->where('periode_id', $pId);
            }, function ($query) {
                return $query->whereNull('periode_id');
            })
            ->get();
        $details = [];
        
        foreach ($kriteriaList as $kriteria) {
            $nilai = null;

            if (isset($data['kriteria_values'][$kriteria->kriteria_id]) && $data['kriteria_values'][$kriteria->kriteria_id] !== '' && $data['kriteria_values'][$kriteria->kriteria_id] !== null) {
                $nilai = (float) $data['kriteria_values'][$kriteria->kriteria_id];
            } else {
                $nilai = match ($kriteria->kunci_observasi) {
                    'biaya_sewa' => isset($data['harga_sewa']) ? (float)$data['harga_sewa'] : 0,
                    'jumlah_kompetitor' => isset($data['jumlah_kompetitor']) ? (float)$data['jumlah_kompetitor'] : 0,
                    'jarak_rph' => isset($data['jarak_rph']) ? (float)$data['jarak_rph'] : 0,
                    'aksesibilitas' => (float)$aksesScore,
                    'kelayakan_bangunan' => (float)$layakScore,
                    default => null,
                };
            }

            if ($nilai !== null) {
                $details[] = [
                    'penilaian_id' => $penilaian->penilaian_id,
                    'kriteria_id' => $kriteria->kriteria_id,
                    'nilai' => $nilai,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        if (!empty($details)) {
            DetailPenilaian::insert($details);
        }
    }

    private function calculateAksesibilitas(array $data): int
    {
        $trues = 0;
        if (!empty($data['akses_roda4'])) $trues++;
        if (!empty($data['jalan_bagus'])) $trues++;
        if (!empty($data['dekat_fasilitas'])) $trues++;
        if (!empty($data['mudah_ditemukan'])) $trues++;
        if (!empty($data['mudah_dijangkau'])) $trues++;

        return max(1, $trues);
    }

    private function calculateKelayakan(array $data): int
    {
        $trues = 0;
        if (!empty($data['luas_mencukupi'])) $trues++;
        if (!empty($data['bangunan_layak'])) $trues++;
        if (!empty($data['ventilasi_baik'])) $trues++;
        if (!empty($data['air_listrik_memadai'])) $trues++;
        if (!empty($data['parkir_memadai'])) $trues++;

        return max(1, $trues);
    }
}
