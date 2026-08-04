<?php

namespace Tests\Feature;

use App\Models\ObservasiLokasi;
use App\Models\Periode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ObservasiBackNavigationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'RolePermissionSeeder']);
    }

    public function test_manajer_back_button_from_dashboard()
    {
        $manajer = User::where('email', 'manajer@saungaqiqah.com')->first();
        $periode = Periode::create(['nama_periode' => 'Test Period Back Nav', 'status' => Periode::STATUS_DRAFT]);
        $observasi = ObservasiLokasi::create([
            'periode_id' => $periode->id,
            'user_id' => $manajer->id,
            'nama_pemilik' => 'Pemilik Nav Test',
            'nomor_telepon_pemilik' => '08123456789',
            'alamat_lengkap' => 'Jl Test 123',
            'provinsi' => 'Jawa Barat',
            'kabupaten_kota' => 'Bandung',
            'kecamatan' => 'Sukajadi',
            'tanggal_observasi' => now()->toDateString(),
            'jarak_rph' => 3.5,
            'jumlah_kompetitor' => 2,
        ]);

        $response = $this->actingAs($manajer)->get(route('manajer.observasi.show', [$observasi, 'ref' => 'dashboard']));
        $response->assertStatus(200);
        $response->assertViewHas('backUrl', route('dashboard'));
    }

    public function test_manajer_back_button_from_hasil()
    {
        $manajer = User::where('email', 'manajer@saungaqiqah.com')->first();
        $periode = Periode::create(['nama_periode' => 'Test Period Back Nav 2', 'status' => Periode::STATUS_SELESAI]);
        $observasi = ObservasiLokasi::create([
            'periode_id' => $periode->id,
            'user_id' => $manajer->id,
            'nama_pemilik' => 'Pemilik Hasil Nav Test',
            'nomor_telepon_pemilik' => '08123456789',
            'alamat_lengkap' => 'Jl Test 123',
            'provinsi' => 'Jawa Barat',
            'kabupaten_kota' => 'Bandung',
            'kecamatan' => 'Sukajadi',
            'tanggal_observasi' => now()->toDateString(),
            'jarak_rph' => 3.5,
            'jumlah_kompetitor' => 2,
        ]);

        $response = $this->actingAs($manajer)->get(route('manajer.observasi.show', [$observasi, 'ref' => 'hasil']));
        $response->assertStatus(200);
        $response->assertViewHas('backUrl', route('manajer.hasil.index', ['batch_id' => $periode->id]));
    }

    public function test_manajer_back_button_from_observasi_index()
    {
        $manajer = User::where('email', 'manajer@saungaqiqah.com')->first();
        $periode = Periode::create(['nama_periode' => 'Test Period Back Nav 3', 'status' => Periode::STATUS_DRAFT]);
        $observasi = ObservasiLokasi::create([
            'periode_id' => $periode->id,
            'user_id' => $manajer->id,
            'nama_pemilik' => 'Pemilik Obs Nav Test',
            'nomor_telepon_pemilik' => '08123456789',
            'alamat_lengkap' => 'Jl Test 123',
            'provinsi' => 'Jawa Barat',
            'kabupaten_kota' => 'Bandung',
            'kecamatan' => 'Sukajadi',
            'tanggal_observasi' => now()->toDateString(),
            'jarak_rph' => 3.5,
            'jumlah_kompetitor' => 2,
        ]);

        $response = $this->actingAs($manajer)->get(route('manajer.observasi.show', [$observasi, 'ref' => 'observasi']));
        $response->assertStatus(200);
        $response->assertViewHas('backUrl', route('manajer.observasi.index', ['batch_id' => $periode->id]));
    }
}
