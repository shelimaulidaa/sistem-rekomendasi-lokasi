<?php

namespace App\Services;

use App\Models\ObservasiLokasi;
use App\Models\DokumentasiLokasi;
use App\Models\Penilaian;
use App\Models\DetailPenilaian;
use App\Models\Kriteria;
use App\Models\Competitor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ObservasiService
{
    /**
     * Store observation data, compress images, and generate TOPSIS scoring transactionally.
     */
    public function storeObservation(array $data, array $photos, int $userId): ObservasiLokasi
    {
        return DB::transaction(function () use ($data, $photos, $userId) {
            // 1. Calculate Scores
            $akses_score = $this->calculateAksesibilitas($data);
            $layak_score = $this->calculateKelayakan($data);

            // 2. Normalize and Save Observasi
            $data['user_id'] = $userId;
            $data = $this->normalizeBooleanFields($data);

            $observasi = ObservasiLokasi::create($data);

            // Sync custom competitors if provided
            $this->syncCompetitors($observasi, $data);

            // 3. Process & Save Photos
            $this->processAndSavePhotos($photos, $observasi->id);

            // 4. Generate Penilaian & DetailPenilaian
            $this->generatePenilaian($observasi, $data, $akses_score, $layak_score);

            return $observasi;
        });
    }

    /**
     * Synchronize Penilaian and DetailPenilaian records directly from an ObservasiLokasi model.
     */
    public function syncPenilaianForObservasi(ObservasiLokasi $observasi): Penilaian
    {
        $penilaian = Penilaian::firstOrCreate(
            ['observasi_lokasi_id' => $observasi->id],
            ['user_id' => $observasi->user_id ?? 1, 'tanggal_penilaian' => now()]
        );

        $aksesScore = $observasi->aksesibilitas_score;
        $layakScore = $observasi->kelayakan_score;

        $data = [
            'harga_sewa' => $observasi->harga_sewa,
            'jumlah_kompetitor' => $observasi->jumlah_kompetitor,
            'jarak_rph' => $observasi->jarak_rph,
        ];

        DetailPenilaian::where('penilaian_id', $penilaian->penilaian_id)->delete();
        $this->generatePenilaianFromHeader($penilaian, $data, $aksesScore, $layakScore);

        return $penilaian;
    }

    /**
     * Update existing observation and its ratings transactionally.
     */
    public function updateObservation(ObservasiLokasi $observasi, array $data, array $photos, array $deletePhotoIds = []): ObservasiLokasi
    {
        return DB::transaction(function () use ($observasi, $data, $photos, $deletePhotoIds) {
            // 1. Calculate Scores
            $akses_score = $this->calculateAksesibilitas($data);
            $layak_score = $this->calculateKelayakan($data);

            // 2. Normalize and Update Observasi
            $data = $this->normalizeBooleanFields($data);
            $observasi->update($data);

            // Sync custom competitors if provided
            $this->syncCompetitors($observasi, $data);

            // 3. Delete requested photos
            if (!empty($deletePhotoIds)) {
                $this->deletePhotoFiles($observasi->id, $deletePhotoIds);
            }

            // 4. Process & Save New Photos
            $this->processAndSavePhotos($photos, $observasi->id);

            // 5. Update Penilaian & DetailPenilaian
            $observasi->refresh();
            $this->syncPenilaianForObservasi($observasi);

            return $observasi;
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
                if (isset($data[$key])) {
                    $val = filter_var($data[$key], FILTER_VALIDATE_BOOLEAN);
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

    private function generatePenilaianFromHeader(Penilaian $penilaian, array $data, int $aksesScore, int $layakScore): void
    {
        $kriteriaList = Kriteria::all();
        $details = [];
        
        foreach ($kriteriaList as $kriteria) {
            $nilai = match ($kriteria->kunci_observasi) {
                'biaya_sewa' => $data['harga_sewa'] ?? 0,
                'jumlah_kompetitor' => $data['jumlah_kompetitor'] ?? 0,
                'jarak_rph' => $data['jarak_rph'] ?? 0,
                'aksesibilitas' => $aksesScore,
                'kelayakan_bangunan' => $layakScore,
                default => null,
            };

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
