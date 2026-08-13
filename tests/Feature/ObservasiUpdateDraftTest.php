<?php

namespace Tests\Feature;

use App\Models\ObservasiLokasi;
use App\Models\Periode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ObservasiUpdateDraftTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'RolePermissionSeeder']);
    }

    public function test_can_update_observasi_in_draft_periode()
    {
        $manajer = User::where('email', 'manajer@saungaqiqah.com')->first();

        $draftPeriode = Periode::create([
            'nama_periode' => 'Draft Period Obs Update',
            'status' => Periode::STATUS_DRAFT,
        ]);

        $observasi = ObservasiLokasi::create([
            'periode_id' => $draftPeriode->id,
            'user_id' => $manajer->id,
            'nama_pemilik' => 'Pak Budi Original',
            'nomor_telepon_pemilik' => '08123456789',
            'alamat_lengkap' => 'Jl Sukajadi No 10',
            'provinsi' => 'Jawa Barat',
            'kabupaten_kota' => 'Bandung',
            'kecamatan' => 'Sukajadi',
            'jenis_bangunan' => 'Ruko',
            'kondisi_bangunan' => 'Baik',
            'luas_tanah' => 150,
            'luas_bangunan' => 120,
            'jumlah_lantai' => 2,
            'jumlah_ruangan' => 5,
            'jumlah_wc' => 2,
            'sumber_air' => 'PDAM',
            'daya_listrik' => '3500',
            'area_parkir' => 'Luas',
            'lebar_jalan' => '8m',
            'ventilasi' => 'Bagus',
            'sirkulasi' => 'Bagus',
            'harga_sewa' => 60000000,
            'jarak_rph' => 4.5,
            'jumlah_kompetitor' => 1,
            'tanggal_observasi' => now()->toDateString(),
            'jam_observasi' => '09:00',
        ]);

        $response = $this->actingAs($manajer)->put(route('manajer.observasi.update', $observasi), [
            'periode_id' => $draftPeriode->id,
            'nama_pemilik' => 'Pak Budi Updated Name',
            'nomor_telepon_pemilik' => '08123456789',
            'alamat_lengkap' => 'Jl Sukajadi No 10',
            'provinsi' => 'Jawa Barat',
            'kabupaten_kota' => 'Bandung',
            'kecamatan' => 'Sukajadi',
            'jenis_bangunan' => 'Ruko',
            'kondisi_bangunan' => 'Sangat Baik',
            'luas_tanah' => 150,
            'luas_bangunan' => 120,
            'jumlah_lantai' => 2,
            'jumlah_ruangan' => 5,
            'jumlah_wc' => 2,
            'sumber_air' => 'PDAM',
            'daya_listrik' => '3500',
            'area_parkir' => 'Luas',
            'lebar_jalan' => '8m',
            'ventilasi' => 'Bagus',
            'sirkulasi' => 'Bagus',
            'harga_sewa' => 55000000,
            'jarak_rph' => 4.5,
            'jumlah_kompetitor' => 1,
            'tanggal_observasi' => now()->toDateString(),
            'jam_observasi' => '09:00',
        ]);

        $response->assertRedirect(route('manajer.observasi.index', ['periode_id' => $draftPeriode->id]));
        $this->assertDatabaseHas('observasi_lokasi', [
            'id' => $observasi->id,
            'nama_pemilik' => 'Pak Budi Updated Name',
        ]);
    }

    public function test_can_update_observasi_with_empty_building_information()
    {
        $manajer = User::where('email', 'manajer@saungaqiqah.com')->first();

        $draftPeriode = Periode::create([
            'nama_periode' => 'Draft Period Empty Building Info Update',
            'status' => Periode::STATUS_DRAFT,
        ]);

        $observasi = ObservasiLokasi::create([
            'periode_id' => $draftPeriode->id,
            'user_id' => $manajer->id,
            'nama_pemilik' => 'Pemilik Initially Filled',
            'nomor_telepon_pemilik' => '08123456789',
            'alamat_lengkap' => 'Jl. Test 100',
            'provinsi' => 'Jawa Barat',
            'kabupaten_kota' => 'Bandung',
            'kecamatan' => 'Sukajadi',
            'jenis_bangunan' => 'Ruko',
            'kondisi_bangunan' => 'Baik',
            'luas_tanah' => 150,
            'luas_bangunan' => 120,
            'jumlah_lantai' => 2,
            'jumlah_ruangan' => 5,
            'jumlah_wc' => 2,
            'sumber_air' => 'PDAM',
            'daya_listrik' => '3500 VA',
            'area_parkir' => 'Mobil',
            'lebar_jalan' => '5 Meter',
            'ventilasi' => 'Baik',
            'sirkulasi' => 'Baik',
            'harga_sewa' => 50000000,
            'jarak_rph' => 4.5,
            'jumlah_kompetitor' => 1,
            'tanggal_observasi' => now()->toDateString(),
        ]);

        // Pembaruan observasi dengan mengosongkan seluruh bidang Informasi Bangunan
        $response = $this->actingAs($manajer)->put(route('manajer.observasi.update', $observasi), [
            'periode_id' => $draftPeriode->id,
            'nama_pemilik' => 'Pemilik Updated Empty Building Info',
            'nomor_telepon_pemilik' => '08123456789',
            'alamat_lengkap' => 'Jl. Test 100',
            'provinsi' => 'Jawa Barat',
            'kabupaten_kota' => 'Bandung',
            'kecamatan' => 'Sukajadi',
            'tanggal_observasi' => now()->toDateString(),
            // Empty / null building info fields
            'jenis_bangunan' => null,
            'kondisi_bangunan' => null,
            'luas_tanah' => null,
            'luas_bangunan' => null,
            'jumlah_lantai' => null,
            'jumlah_ruangan' => null,
            'jumlah_wc' => null,
            'sumber_air' => null,
            'daya_listrik' => null,
            'area_parkir' => null,
            'lebar_jalan' => null,
            'ventilasi' => null,
            'sirkulasi' => null,
            'harga_sewa' => null,
            // Retain required inputs
            'jarak_rph' => 4.5,
            'jumlah_kompetitor' => 1,
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('manajer.observasi.index', ['periode_id' => $draftPeriode->id]));

        $this->assertDatabaseHas('observasi_lokasi', [
            'id' => $observasi->id,
            'nama_pemilik' => 'Pemilik Updated Empty Building Info',
            'luas_bangunan' => null,
            'kondisi_bangunan' => null,
            'sumber_air' => null,
        ]);
    }
}
