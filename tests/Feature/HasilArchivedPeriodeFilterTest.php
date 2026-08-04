<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Periode;
use App\Models\ObservasiLokasi;
use App\Models\Penilaian;
use App\Models\HasilPerhitungan;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HasilArchivedPeriodeFilterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::create(['name' => 'manajer']);
        Role::create(['name' => 'direktur']);
        \Spatie\Permission\Models\Permission::create(['name' => 'view hasil']);
        $role = Role::findByName('manajer');
        $role->givePermissionTo('view hasil');
    }

    public function test_archived_periode_is_hidden_and_showing_again_when_set_back_to_selesai(): void
    {
        $manajer = User::factory()->create();
        $manajer->assignRole('manajer');

        $direktur = User::factory()->create();
        $direktur->assignRole('direktur');

        // Create a period with Selesai status and calculation results
        $periode = Periode::create([
            'nama_batch' => 'Periode Test Filter Archived',
            'status' => Periode::STATUS_SELESAI,
        ]);

        $observasi = ObservasiLokasi::create([
            'batch_id' => $periode->id,
            'user_id' => $manajer->id,
            'nama_pemilik' => 'Pemilik Test Filter Archived',
            'nomor_telepon_pemilik' => '08123456789',
            'alamat_lengkap' => 'Jl. Filter Test No. 123',
            'provinsi' => 'Jawa Barat',
            'kabupaten_kota' => 'Kota Bandung',
            'kecamatan' => 'Coblong',
            'harga_sewa' => 10000000,
            'jarak_rph' => 2.5,
            'jumlah_kompetitor' => 1,
            'jenis_bangunan' => 'Rumah',
            'kondisi_bangunan' => 'Baik',
            'luas_tanah' => 100,
            'luas_bangunan' => 80,
            'jumlah_lantai' => 1,
            'jumlah_ruangan' => 2,
            'jumlah_wc' => 1,
            'sumber_air' => 'PDAM',
            'daya_listrik' => '2200 VA',
            'area_parkir' => 'Cukup',
            'lebar_jalan' => '6 meter',
            'ventilasi' => 'Baik',
            'sirkulasi' => 'Lancar',
            'kelayakan_score' => 5,
            'aksesibilitas_score' => 5,
            'tanggal_observasi' => now(),
        ]);

        $penilaian = Penilaian::create([
            'observasi_lokasi_id' => $observasi->id,
            'user_id' => $manajer->id,
            'tanggal_penilaian' => now(),
        ]);

        $hasil = HasilPerhitungan::create([
            'penilaian_id' => $penilaian->penilaian_id,
            'nilai_preferensi' => 0.8765,
            'ranking' => 1,
            'tanggal_hitung' => now(),
        ]);

        // 1. Initially (status = Selesai), it should be displayed for Manajer & Direktur
        $responseManajer = $this->actingAs($manajer)->get(route('manajer.hasil.index'));
        $responseManajer->assertStatus(200);
        $responseManajer->assertSee('Pemilik Test Filter Archived');

        $responseDirektur = $this->actingAs($direktur)->get(route('direktur.rekomendasi.index'));
        $responseDirektur->assertStatus(200);
        $responseDirektur->assertSee('Pemilik Test Filter Archived');

        // 2. Change status to Diarsipkan
        $periode->update(['status' => Periode::STATUS_DIARSIPKAN]);

        // Data should be hidden now
        $responseManajer2 = $this->actingAs($manajer)->get(route('manajer.hasil.index'));
        $responseManajer2->assertStatus(200);
        $responseManajer2->assertDontSee('Pemilik Test Filter Archived');

        $responseDirektur2 = $this->actingAs($direktur)->get(route('direktur.rekomendasi.index'));
        $responseDirektur2->assertStatus(200);
        $responseDirektur2->assertDontSee('Pemilik Test Filter Archived');

        // 3. Change status back to Selesai
        $periode->update(['status' => Periode::STATUS_SELESAI]);

        // Data should reappear without recalculating (HasilPerhitungan record is intact)
        $responseManajer3 = $this->actingAs($manajer)->get(route('manajer.hasil.index'));
        $responseManajer3->assertStatus(200);
        $responseManajer3->assertSee('Pemilik Test Filter Archived');

        $responseDirektur3 = $this->actingAs($direktur)->get(route('direktur.rekomendasi.index'));
        $responseDirektur3->assertStatus(200);
        $responseDirektur3->assertSee('Pemilik Test Filter Archived');
    }
}
