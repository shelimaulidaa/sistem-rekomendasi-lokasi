<?php

namespace Database\Seeders;

use App\Models\DetailPenilaian;
use App\Models\Kriteria;
use App\Models\ObservasiLokasi;
use App\Models\Penilaian;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ObservasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Mulai melakukan seeding data observasi & dummy TOPSIS...');

        // Get the first user to assign as creator (assuming admin exists)
        $user = User::first();
        $userId = $user ? $user->id : 1;

        // Fetch criteria dynamically ordered by 'urutan'
        $kriterias = Kriteria::orderBy('urutan')->get();

        if ($kriterias->count() < 5) {
            $this->command->error('Kriteria kurang dari 5. Pastikan KriteriaSeeder telah dijalankan.');
            return;
        }

        /* 
         * DATASET EXCEL
         */
        $dataset = [
            [
                'nama' => 'Budi Santoso',
                'phone' => '081234567890',
                'scores' => [3, 11000000, 1, 0.37, 3]
            ],
            [
                'nama' => 'Andi Wijaya',
                'phone' => '082345678901',
                'scores' => [5, 11400000, 3, 2.3, 3]
            ],
            [
                'nama' => 'Siti Aminah',
                'phone' => '083456789012',
                'scores' => [5, 23000000, 4, 3.5, 3]
            ],
            [
                'nama' => 'Rahmat Hidayat',
                'phone' => '084567890123',
                'scores' => [5, 8000000, 2, 2.8, 1]
            ],
            [
                'nama' => 'Dewi Lestari',
                'phone' => '085678901234',
                'scores' => [5, 12000000, 2, 1.2, 5]
            ],
            [
                'nama' => 'Ahmad Fauzi',
                'phone' => '086789012345',
                'scores' => [5, 16000000, 2, 1.7, 5]
            ],
            [
                'nama' => 'Nina Safitri',
                'phone' => '087890123456',
                'scores' => [1, 12000000, 1, 4.1, 5]
            ],
        ];

        $batch = \App\Models\Batch::firstOrCreate(
            ['nama_batch' => 'Cabang Cianjur 2026'],
            ['is_active' => true]
        );

        DB::beginTransaction();

        try {
            foreach ($dataset as $data) {
                // 1. Create ObservasiLokasi
                $observasi = ObservasiLokasi::create([
                    'batch_id' => $batch->id,
                    'user_id' => $userId,
                    'nama_pemilik' => $data['nama'],
                    'nomor_telepon_pemilik' => $data['phone'],
                    'alamat_lengkap' => 'Jalan Dummy untuk ' . $data['nama'],
                    'provinsi' => 'Jawa Barat',
                    'kabupaten_kota' => 'Cianjur',
                    'kecamatan' => 'Cianjur',
                    'jenis_bangunan' => 'Ruko',
                    'kondisi_bangunan' => 'Baik',
                    'luas_tanah' => 100,
                    'luas_bangunan' => 100,
                    'jumlah_lantai' => 1,
                    'jumlah_ruangan' => 2,
                    'jumlah_wc' => 1,
                    'sumber_air' => 'PDAM',
                    'daya_listrik' => '2200 VA',
                    'area_parkir' => 'Tersedia',
                    'lebar_jalan' => 'Lebar',
                    'ventilasi' => 'Baik',
                    'sirkulasi' => 'Baik',
                    'harga_sewa' => $data['scores'][1], // C2
                    'jumlah_kompetitor' => $data['scores'][2], // C3
                    'jarak_rph' => $data['scores'][3], // C4
                    'akses_roda4' => true,
                    'jalan_bagus' => true,
                    'dekat_fasilitas' => true,
                    'bangunan_layak' => true,
                    'ventilasi_baik' => true,
                    'air_listrik_memadai' => true,
                    'catatan' => 'Dummy record for testing',
                    'tanggal_observasi' => now(),
                    'jam_observasi' => '10:00:00',
                    'anggota_pendamping' => ['Fulan', 'Fulanah'],
                    'umk' => 2893229,
                    'pdrb' => 30000000,
                    'jumlah_penduduk_muslim' => 1500000,
                    'latitude' => -6.816 + (rand(-100, 100) / 10000),
                    'longitude' => 107.142 + (rand(-100, 100) / 10000),
                ]);

                // 2. Create Penilaian
                $penilaian = Penilaian::create([
                    'observasi_lokasi_id' => $observasi->id,
                    'user_id' => $userId,
                    'tanggal_penilaian' => now(),
                ]);

                // 3. Create Detail Penilaian dynamically
                foreach ($data['scores'] as $index => $score) {
                    $kriteria = $kriterias[$index];

                    DetailPenilaian::create([
                        'penilaian_id' => $penilaian->penilaian_id,
                        'kriteria_id' => $kriteria->kriteria_id,
                        'nilai' => (float)$score, 
                    ]);
                }
            }

            DB::commit();
            $this->command->info('Seeding data observasi & TOPSIS berhasil!');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Gagal melakukan seeding: ' . $e->getMessage());
        }
    }
}
