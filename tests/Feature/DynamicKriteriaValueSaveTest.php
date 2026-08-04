<?php

namespace Tests\Feature;

use App\Models\ObservasiLokasi;
use App\Models\Periode;
use App\Models\Kriteria;
use App\Models\Penilaian;
use App\Models\DetailPenilaian;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DynamicKriteriaValueSaveTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'RolePermissionSeeder']);
    }

    public function test_can_save_dynamic_kriteria_values()
    {
        $manajer = User::where('email', 'manajer@saungaqiqah.com')->first();

        $periode = Periode::create([
            'nama_periode' => 'Periode Test Dynamic Values',
            'status' => Periode::STATUS_DRAFT,
        ]);

        // Create a custom Likert scoring criterion
        $scoringKriteria = Kriteria::create([
            'periode_id' => $periode->id,
            'kode_kriteria' => 'C6',
            'nama_kriteria' => 'Tingkat Keamanan',
            'bobot' => 15,
            'atribut' => 'benefit',
            'jenis_input' => 'scoring',
            'urutan' => 6,
        ]);

        // Create a custom numeric criterion
        $numericKriteria = Kriteria::create([
            'periode_id' => $periode->id,
            'kode_kriteria' => 'C7',
            'nama_kriteria' => 'Potensi Pasar',
            'bobot' => 10,
            'atribut' => 'benefit',
            'jenis_input' => 'numeric',
            'urutan' => 7,
        ]);

        $observasi = ObservasiLokasi::create([
            'periode_id' => $periode->id,
            'user_id' => $manajer->id,
            'nama_pemilik' => 'Pak Ahmad Test',
            'nomor_telepon_pemilik' => '08123456789',
            'alamat_lengkap' => 'Jl Asia Afrika No 10',
            'provinsi' => 'Jawa Barat',
            'kabupaten_kota' => 'Kota Bandung',
            'kecamatan' => 'Sumur Bandung',
            'jenis_bangunan' => 'Ruko',
            'kondisi_bangunan' => 'Baik',
            'luas_tanah' => 100,
            'luas_bangunan' => 80,
            'jumlah_lantai' => 2,
            'jumlah_ruangan' => 3,
            'jumlah_wc' => 1,
            'sumber_air' => 'PDAM',
            'daya_listrik' => '2200',
            'area_parkir' => 'Cukup',
            'lebar_jalan' => '6m',
            'ventilasi' => 'Baik',
            'sirkulasi' => 'Baik',
            'harga_sewa' => 50000000,
            'jarak_rph' => 3.2,
            'jumlah_kompetitor' => 2,
            'tanggal_observasi' => now()->toDateString(),
            'jam_observasi' => '10:00',
        ]);

        $response = $this->actingAs($manajer)->put(route('manajer.observasi.update', $observasi), [
            'batch_id' => $periode->id,
            'nama_pemilik' => 'Pak Ahmad Updated',
            'nomor_telepon_pemilik' => '08123456789',
            'alamat_lengkap' => 'Jl Asia Afrika No 10',
            'provinsi' => 'Jawa Barat',
            'kabupaten_kota' => 'Kota Bandung',
            'kecamatan' => 'Sumur Bandung',
            'jenis_bangunan' => 'Ruko',
            'kondisi_bangunan' => 'Baik',
            'luas_tanah' => 100,
            'luas_bangunan' => 80,
            'jumlah_lantai' => 2,
            'jumlah_ruangan' => 3,
            'jumlah_wc' => 1,
            'sumber_air' => 'PDAM',
            'daya_listrik' => '2200',
            'area_parkir' => 'Cukup',
            'lebar_jalan' => '6m',
            'ventilasi' => 'Baik',
            'sirkulasi' => 'Baik',
            'harga_sewa' => 50000000,
            'jarak_rph' => 3.2,
            'jumlah_kompetitor' => 2,
            'tanggal_observasi' => now()->toDateString(),
            'kriteria_values' => [
                $scoringKriteria->kriteria_id => 4, // Likert score 4
                $numericKriteria->kriteria_id => 85.5, // Numeric value 85.5
            ],
        ]);

        $response->assertRedirect(route('manajer.observasi.index', ['batch_id' => $periode->id]));

        $penilaian = Penilaian::where('observasi_lokasi_id', $observasi->id)->first();
        $this->assertNotNull($penilaian);

        $this->assertDatabaseHas('detail_penilaian', [
            'penilaian_id' => $penilaian->penilaian_id,
            'kriteria_id' => $scoringKriteria->kriteria_id,
            'nilai' => 4,
        ]);

        $this->assertDatabaseHas('detail_penilaian', [
            'penilaian_id' => $penilaian->penilaian_id,
            'kriteria_id' => $numericKriteria->kriteria_id,
            'nilai' => 85.5,
        ]);
    }
}
