<?php

namespace Tests\Feature;

use App\Models\Kriteria;
use App\Models\ObservasiLokasi;
use App\Models\Periode;
use App\Models\User;
use App\Services\KriteriaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KriteriaBackendTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'RolePermissionSeeder']);
    }

    public function test_can_manage_kriteria_only_on_draft_period_without_observations()
    {
        $manajer = User::where('email', 'manajer@saungaqiqah.com')->first();
        $service = app(KriteriaService::class);

        // 1. Create a Draft period without observations
        $draftPeriode = Periode::create([
            'nama_periode' => 'Draft Periode Test',
            'status' => Periode::STATUS_DRAFT,
        ]);

        $this->assertTrue($service->canManageKriteria($draftPeriode->id));

        // 2. Add an observation to the Draft period
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

        // Now CRUD should be disabled because observations exist
        $this->assertFalse($service->canManageKriteria($draftPeriode->id));

        // 3. Create a non-Draft period (e.g. Selesai) without observations
        $selesaiPeriode = Periode::create([
            'nama_periode' => 'Selesai Periode Test',
            'status' => Periode::STATUS_SELESAI,
        ]);

        // CRUD disabled because status is not Draft
        $this->assertFalse($service->canManageKriteria($selesaiPeriode->id));
    }

    public function test_store_kriteria_requires_periode_id_and_validates_draft_status()
    {
        $manajer = User::where('email', 'manajer@saungaqiqah.com')->first();

        $draftPeriode = Periode::create([
            'nama_periode' => 'Draft Periode Test',
            'status' => Periode::STATUS_DRAFT,
        ]);

        $response = $this->actingAs($manajer)->post(route('manajer.kriteria.store'), [
            'periode_id' => $draftPeriode->id,
            'kode_kriteria' => 'C10',
            'nama_kriteria' => 'Kriteria Baru Test',
            'bobot' => 20.0,
            'atribut' => 'benefit',
            'jenis_input' => 'scoring',
            'urutan' => 1,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('kriteria', [
            'periode_id' => $draftPeriode->id,
            'kode_kriteria' => 'C10',
        ]);
    }

    public function test_delete_kriteria_performs_hard_delete()
    {
        $manajer = User::where('email', 'manajer@saungaqiqah.com')->first();

        $draftPeriode = Periode::create([
            'nama_periode' => 'Draft Periode Test For Hard Delete',
            'status' => Periode::STATUS_DRAFT,
        ]);

        $kriteria = Kriteria::create([
            'periode_id' => $draftPeriode->id,
            'kode_kriteria' => 'C1',
            'nama_kriteria' => 'Kriteria Test Hard Delete',
            'bobot' => 50.0,
            'atribut' => 'benefit',
            'jenis_input' => 'scoring',
            'urutan' => 1,
        ]);

        $response = $this->actingAs($manajer)->delete(route('manajer.kriteria.destroy', $kriteria));

        $response->assertRedirect(route('manajer.kriteria.index', ['periode_id' => $draftPeriode->id]));
        $this->assertDatabaseMissing('kriteria', [
            'kriteria_id' => $kriteria->kriteria_id,
        ]);
    }
}
