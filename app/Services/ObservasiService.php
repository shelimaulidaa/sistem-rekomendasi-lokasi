<?php

namespace App\Services;

use App\Models\ObservasiLokasi;
use App\Models\DokumentasiLokasi;
use App\Models\Penilaian;
use App\Models\DetailPenilaian;
use App\Models\Kriteria;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

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

            // 2. Save Observasi
            $data['user_id'] = $userId;
            
            // Explicitly cast booleans just in case string '1'/'0' is passed
            $booleanFields = ['akses_roda4', 'jalan_bagus', 'dekat_fasilitas', 'bangunan_layak', 'ventilasi_baik', 'air_listrik_memadai'];
            foreach ($booleanFields as $field) {
                $data[$field] = isset($data[$field]) ? filter_var($data[$field], FILTER_VALIDATE_BOOLEAN) : false;
            }

            $observasi = ObservasiLokasi::create($data);

            // 3. Process & Save Photos
            $this->processAndSavePhotos($photos, $observasi->id);

            // 4. Generate Penilaian & DetailPenilaian
            $this->generatePenilaian($observasi, $data, $akses_score, $layak_score);

            return $observasi;
        });
    }

    private function processAndSavePhotos(array $photos, int $observasiId)
    {
        if (empty($photos)) return;

        foreach ($photos as $photo) {
            // Generate unique filename
            $extension = $photo->getClientOriginalExtension() ?: 'jpg';
            $filename = 'obs_' . $observasiId . '_' . uniqid() . '.' . $extension;
            
            // Store directly using Laravel Native Storage to prevent local GD corruption
            $path = $photo->storeAs('dokumentasi_lokasi', $filename, 'public');

            // Save to DB
            DokumentasiLokasi::create([
                'observasi_lokasi_id' => $observasiId,
                'foto_path' => $path,
            ]);
        }
    }

    private function generatePenilaian(ObservasiLokasi $observasi, array $data, int $aksesScore, int $layakScore)
    {
        // Fetch all criteria regardless of code, we will match by kunci_observasi
        $kriteriaList = Kriteria::all();

        // Create Header
        $penilaian = Penilaian::create([
            'observasi_lokasi_id' => $observasi->id,
            'user_id' => $observasi->user_id,
            'tanggal_penilaian' => now(),
        ]);

        // Map values dynamically
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

        return match ($trues) {
            3 => 5,
            2 => 3,
            1 => 1,
            default => 0,
        };
    }

    private function calculateKelayakan(array $data): int
    {
        $trues = 0;
        if (!empty($data['bangunan_layak'])) $trues++;
        if (!empty($data['ventilasi_baik'])) $trues++;
        if (!empty($data['air_listrik_memadai'])) $trues++;

        return match ($trues) {
            3 => 5,
            2 => 3,
            1 => 1,
            default => 0,
        };
    }
}
