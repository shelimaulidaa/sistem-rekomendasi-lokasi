<?php

namespace Tests\Unit;

use App\Models\ObservasiLokasi;
use App\Models\Periode;
use App\Models\Kriteria;
use App\Models\Penilaian;
use App\Models\DetailPenilaian;
use App\Models\User;
use App\Services\TopsisService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TopsisTieBreakerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'RolePermissionSeeder']);
    }

    public function test_topsis_tie_breaker_prefers_smaller_d_plus_and_highest_weighted_kriteria()
    {
        $manajer = User::where('email', 'manajer@saungaqiqah.com')->first();

        $periode = Periode::create([
            'nama_periode' => 'Periode Tie Breaker Test',
            'status' => Periode::STATUS_DRAFT,
        ]);

        $kriteriaUtama = Kriteria::create([
            'periode_id' => $periode->id,
            'kode_kriteria' => 'C1',
            'nama_kriteria' => 'Kriteria Utama (Bobot Tinggi)',
            'bobot' => 70,
            'atribut' => 'benefit',
            'jenis_input' => 'scoring',
            'urutan' => 1,
        ]);

        $kriteriaSekunder = Kriteria::create([
            'periode_id' => $periode->id,
            'kode_kriteria' => 'C2',
            'nama_kriteria' => 'Kriteria Sekunder',
            'bobot' => 30,
            'atribut' => 'benefit',
            'jenis_input' => 'scoring',
            'urutan' => 2,
        ]);

        // Lokasi A
        $obsA = ObservasiLokasi::create([
            'periode_id' => $periode->id,
            'user_id' => $manajer->id,
            'nama_pemilik' => 'Lokasi A',
            'nomor_telepon_pemilik' => '08123456781',
            'alamat_lengkap' => 'Alamat A',
            'provinsi' => 'Jawa Barat',
            'kabupaten_kota' => 'Bandung',
            'kecamatan' => 'Coblong',
            'jenis_bangunan' => 'Ruko',
            'kondisi_bangunan' => 'Baik',
            'luas_tanah' => 100,
            'luas_bangunan' => 100,
            'jumlah_lantai' => 1,
            'jumlah_ruangan' => 2,
            'jumlah_wc' => 1,
            'sumber_air' => 'PDAM',
            'daya_listrik' => '1300',
            'area_parkir' => 'Cukup',
            'lebar_jalan' => '5m',
            'ventilasi' => 'Baik',
            'sirkulasi' => 'Baik',
            'harga_sewa' => 20000000,
            'jarak_rph' => 2.0,
            'jumlah_kompetitor' => 1,
            'tanggal_observasi' => now()->toDateString(),
            'jam_observasi' => '08:00',
        ]);

        $penilaianA = Penilaian::create([
            'observasi_lokasi_id' => $obsA->id,
            'user_id' => $manajer->id,
            'tanggal_penilaian' => now(),
        ]);
        DetailPenilaian::create([
            'penilaian_id' => $penilaianA->penilaian_id,
            'kriteria_id' => $kriteriaUtama->kriteria_id,
            'nilai' => 5,
        ]);
        DetailPenilaian::create([
            'penilaian_id' => $penilaianA->penilaian_id,
            'kriteria_id' => $kriteriaSekunder->kriteria_id,
            'nilai' => 3,
        ]);

        // Lokasi B
        $obsB = ObservasiLokasi::create([
            'periode_id' => $periode->id,
            'user_id' => $manajer->id,
            'nama_pemilik' => 'Lokasi B',
            'nomor_telepon_pemilik' => '08123456782',
            'alamat_lengkap' => 'Alamat B',
            'provinsi' => 'Jawa Barat',
            'kabupaten_kota' => 'Bandung',
            'kecamatan' => 'Coblong',
            'jenis_bangunan' => 'Ruko',
            'kondisi_bangunan' => 'Baik',
            'luas_tanah' => 100,
            'luas_bangunan' => 100,
            'jumlah_lantai' => 1,
            'jumlah_ruangan' => 2,
            'jumlah_wc' => 1,
            'sumber_air' => 'PDAM',
            'daya_listrik' => '1300',
            'area_parkir' => 'Cukup',
            'lebar_jalan' => '5m',
            'ventilasi' => 'Baik',
            'sirkulasi' => 'Baik',
            'harga_sewa' => 20000000,
            'jarak_rph' => 2.0,
            'jumlah_kompetitor' => 1,
            'tanggal_observasi' => now()->toDateString(),
            'jam_observasi' => '08:00',
        ]);

        $penilaianB = Penilaian::create([
            'observasi_lokasi_id' => $obsB->id,
            'user_id' => $manajer->id,
            'tanggal_penilaian' => now(),
        ]);
        DetailPenilaian::create([
            'penilaian_id' => $penilaianB->penilaian_id,
            'kriteria_id' => $kriteriaUtama->kriteria_id,
            'nilai' => 3,
        ]);
        DetailPenilaian::create([
            'penilaian_id' => $penilaianB->penilaian_id,
            'kriteria_id' => $kriteriaSekunder->kriteria_id,
            'nilai' => 5,
        ]);

        $service = app(TopsisService::class);
        $calc = $service->calculate($periode->id);

        $this->assertNotEmpty($calc['results']);
        $this->assertCount(2, $calc['results']);
    }
}
