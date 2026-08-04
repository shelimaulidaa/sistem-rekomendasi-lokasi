<?php

namespace Tests\Feature;

use App\Models\Kriteria;
use App\Models\ObservasiLokasi;
use App\Models\Periode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KriteriaLockingMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'RolePermissionSeeder']);
    }

    public function test_direct_postman_request_to_create_kriteria_on_period_with_observations_returns_403()
    {
        $manajer = User::where('email', 'manajer@saungaqiqah.com')->first();

        // Draft period WITH an observation
        $draftPeriode = Periode::create([
            'nama_periode' => 'Draft Period With Obs',
            'status' => Periode::STATUS_DRAFT,
        ]);

        ObservasiLokasi::create([
            'periode_id' => $draftPeriode->id,
            'user_id' => $manajer->id,
            'nama_pemilik' => 'Test Pemilik',
            'nomor_telepon_pemilik' => '08123456789',
            'alamat_lengkap' => 'Jl Test No 123',
            'provinsi' => 'Jawa Barat',
            'kabupaten_kota' => 'Bandung',
            'kecamatan' => 'Coblong',
            'luas_tanah' => 100,
            'luas_bangunan' => 80,
            'jumlah_lantai' => 1,
            'jumlah_ruangan' => 3,
            'jumlah_wc' => 1,
            'sumber_air' => 'PAM',
            'daya_listrik' => '2200',
            'area_parkir' => 'Cukup',
            'lebar_jalan' => '6m',
            'ventilasi' => 'Baik',
            'sirkulasi' => 'Baik',
            'harga_sewa' => 50000000,
            'jarak_rph' => 5.2,
            'jumlah_kompetitor' => 2,
            'tanggal_observasi' => now(),
            'jam_observasi' => '10:00',
        ]);

        // Simulating direct Postman request (Accept: application/json)
        $response = $this->actingAs($manajer)->postJson(route('manajer.kriteria.store'), [
            'periode_id' => $draftPeriode->id,
            'kode_kriteria' => 'C99',
            'nama_kriteria' => 'Bypass Test',
            'bobot' => 10.0,
            'atribut' => 'benefit',
            'jenis_input' => 'scoring',
            'urutan' => 1,
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('kriteria', [
            'kode_kriteria' => 'C99',
        ]);
    }

    public function test_direct_postman_request_to_delete_kriteria_on_period_with_observations_returns_403()
    {
        $manajer = User::where('email', 'manajer@saungaqiqah.com')->first();

        $draftPeriode = Periode::create([
            'nama_periode' => 'Draft Period With Obs',
            'status' => Periode::STATUS_DRAFT,
        ]);

        $kriteria = Kriteria::create([
            'periode_id' => $draftPeriode->id,
            'kode_kriteria' => 'C1',
            'nama_kriteria' => 'Aksesibilitas',
            'bobot' => 20.0,
            'atribut' => 'benefit',
            'jenis_input' => 'scoring',
            'urutan' => 1,
        ]);

        ObservasiLokasi::create([
            'periode_id' => $draftPeriode->id,
            'user_id' => $manajer->id,
            'nama_pemilik' => 'Test Pemilik',
            'nomor_telepon_pemilik' => '08123456789',
            'alamat_lengkap' => 'Jl Test No 123',
            'provinsi' => 'Jawa Barat',
            'kabupaten_kota' => 'Bandung',
            'kecamatan' => 'Coblong',
            'luas_tanah' => 100,
            'luas_bangunan' => 80,
            'jumlah_lantai' => 1,
            'jumlah_ruangan' => 3,
            'jumlah_wc' => 1,
            'sumber_air' => 'PAM',
            'daya_listrik' => '2200',
            'area_parkir' => 'Cukup',
            'lebar_jalan' => '6m',
            'ventilasi' => 'Baik',
            'sirkulasi' => 'Baik',
            'harga_sewa' => 50000000,
            'jarak_rph' => 5.2,
            'jumlah_kompetitor' => 2,
            'tanggal_observasi' => now(),
            'jam_observasi' => '10:00',
        ]);

        // Simulating direct Postman DELETE request
        $response = $this->actingAs($manajer)->deleteJson(route('manajer.kriteria.destroy', $kriteria->kriteria_id));

        $response->assertStatus(403);
        $this->assertDatabaseHas('kriteria', [
            'kriteria_id' => $kriteria->kriteria_id,
            'deleted_at' => null,
        ]);
    }
}
