<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Periode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ObservasiNumericValidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_nomor_telepon_pemilik_must_be_numeric()
    {
        $user = User::factory()->create();
        $user->assignRole('manajer');

        $periode = Periode::create([
            'nama_periode' => 'Periode Test Numeric',
            'status' => Periode::STATUS_DRAFT,
        ]);

        $response = $this->actingAs($user)->post(route('manajer.observasi.store'), [
            'periode_id' => $periode->id,
            'nama_pemilik' => 'Pemilik Test',
            'nomor_telepon_pemilik' => '08123abc456', // Invalid non-numeric string
            'alamat_lengkap' => 'Jl. Test',
            'provinsi' => 'JAWA BARAT',
            'kabupaten_kota' => 'KOTA BANDUNG',
            'kecamatan' => 'PANYILEUKAN',
            'tanggal_observasi' => now()->format('Y-m-d'),
            'luas_tanah' => 100,
            'luas_bangunan' => 80,
            'jumlah_lantai' => 1,
            'jumlah_ruangan' => 2,
            'jumlah_wc' => 1,
            'sumber_air' => 'PDAM',
            'daya_listrik' => '1300 VA',
            'area_parkir' => 'Mobil',
            'lebar_jalan' => '5 Meter',
            'ventilasi' => 'Baik',
            'sirkulasi' => 'Baik',
            'harga_sewa' => 10000000,
            'jumlah_kompetitor' => 1,
            'jarak_rph' => 3,
        ]);

        $response->assertSessionHasErrors(['nomor_telepon_pemilik']);
    }

    public function test_luas_bangunan_and_luas_tanah_must_be_numeric()
    {
        $user = User::factory()->create();
        $user->assignRole('manajer');

        $periode = Periode::create([
            'nama_periode' => 'Periode Test Numeric 2',
            'status' => Periode::STATUS_DRAFT,
        ]);

        $response = $this->actingAs($user)->post(route('manajer.observasi.store'), [
            'periode_id' => $periode->id,
            'nama_pemilik' => 'Pemilik Test 2',
            'nomor_telepon_pemilik' => '08123456789',
            'alamat_lengkap' => 'Jl. Test 2',
            'provinsi' => 'JAWA BARAT',
            'kabupaten_kota' => 'KOTA BANDUNG',
            'kecamatan' => 'PANYILEUKAN',
            'tanggal_observasi' => now()->format('Y-m-d'),
            'luas_tanah' => 'seratus', // Invalid non-numeric
            'luas_bangunan' => 'delapanpuluh', // Invalid non-numeric
            'jumlah_lantai' => 1,
            'jumlah_ruangan' => 2,
            'jumlah_wc' => 1,
            'sumber_air' => 'PDAM',
            'daya_listrik' => '1300 VA',
            'area_parkir' => 'Mobil',
            'lebar_jalan' => '5 Meter',
            'ventilasi' => 'Baik',
            'sirkulasi' => 'Baik',
            'harga_sewa' => 10000000,
            'jumlah_kompetitor' => 1,
            'jarak_rph' => 3,
        ]);

        $response->assertSessionHasErrors(['luas_tanah', 'luas_bangunan']);
    }

    public function test_building_information_fields_are_optional()
    {
        $user = User::where('email', 'manajer@saungaqiqah.com')->first();

        $periode = Periode::create([
            'nama_periode' => 'Periode Test Optional Building',
            'status' => Periode::STATUS_DRAFT,
        ]);

        $response = $this->actingAs($user)->post(route('manajer.observasi.store'), [
            'periode_id' => $periode->id,
            'nama_pemilik' => 'Pemilik Test Optional',
            'nomor_telepon_pemilik' => '08123456789',
            'alamat_lengkap' => 'Jl. Test Optional 123',
            'provinsi' => 'JAWA BARAT',
            'kabupaten_kota' => 'KOTA BANDUNG',
            'kecamatan' => 'PANYILEUKAN',
            'tanggal_observasi' => now()->format('Y-m-d'),
            // Building info fields left null / empty
            'harga_sewa' => null,
            'luas_tanah' => null,
            'luas_bangunan' => null,
            'jumlah_lantai' => null,
            'jumlah_ruangan' => null,
            'jumlah_wc' => null,
            'kondisi_bangunan' => null,
            'sumber_air' => null,
            'daya_listrik' => null,
            'area_parkir' => null,
            'lebar_jalan' => null,
            'ventilasi' => null,
            'sirkulasi' => null,
            // Required TOPSIS inputs
            'jumlah_kompetitor' => 0,
            'jarak_rph' => 5,
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('observasi_lokasi', [
            'nama_pemilik' => 'Pemilik Test Optional',
            'luas_bangunan' => null,
            'kondisi_bangunan' => null,
        ]);
    }
}
