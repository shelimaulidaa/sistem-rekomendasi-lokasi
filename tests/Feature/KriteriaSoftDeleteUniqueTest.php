<?php

namespace Tests\Feature;

use App\Models\Kriteria;
use App\Models\Periode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KriteriaSoftDeleteUniqueTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'RolePermissionSeeder']);
    }

    public function test_can_create_kriteria_with_deleted_kriteria_code()
    {
        $manajer = User::where('email', 'manajer@saungaqiqah.com')->first();

        $draftPeriode = Periode::create([
            'nama_periode' => 'Draft Soft Delete Test Period',
            'status' => Periode::STATUS_DRAFT,
        ]);

        $kriteria = Kriteria::create([
            'periode_id' => $draftPeriode->id,
            'kode_kriteria' => 'C1',
            'nama_kriteria' => 'Kriteria Lama',
            'bobot' => 20.0,
            'atribut' => 'benefit',
            'jenis_input' => 'scoring',
            'urutan' => 1,
        ]);

        // Hapus kriteria (soft delete)
        $kriteria->delete();

        // Buat kriteria baru dengan kode C1 yang sama pada periode yang sama (harus berhasil)
        $response = $this->actingAs($manajer)->post(route('manajer.kriteria.store'), [
            'periode_id' => $draftPeriode->id,
            'kode_kriteria' => 'C1',
            'nama_kriteria' => 'Kriteria Baru Pengganti',
            'bobot' => 25.0,
            'atribut' => 'benefit',
            'jenis_input' => 'scoring',
        ]);

        $response->assertRedirect(route('manajer.kriteria.index', ['periode_id' => $draftPeriode->id]));
        $this->assertDatabaseHas('kriteria', [
            'periode_id' => $draftPeriode->id,
            'kode_kriteria' => 'C1',
            'nama_kriteria' => 'Kriteria Baru Pengganti',
            'deleted_at' => null,
        ]);
    }
}
