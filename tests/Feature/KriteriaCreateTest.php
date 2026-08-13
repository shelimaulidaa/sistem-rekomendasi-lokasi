<?php

namespace Tests\Feature;

use App\Models\Periode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KriteriaCreateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'RolePermissionSeeder']);
    }

    public function test_store_kriteria_automatically_assigns_urutan_when_missing_from_form()
    {
        $this->withoutExceptionHandling();

        $manajer = User::where('email', 'manajer@saungaqiqah.com')->first();

        $draftPeriode = Periode::create([
            'nama_periode' => 'Draft New Period',
            'status' => Periode::STATUS_DRAFT,
        ]);

        $response = $this->actingAs($manajer)->post(route('manajer.kriteria.store'), [
            'periode_id' => $draftPeriode->id,
            'kode_kriteria' => 'C10',
            'nama_kriteria' => 'Test Kriteria Baru',
            'bobot' => 15.0,
            'atribut' => 'benefit',
            'jenis_input' => 'scoring',
        ]);

        $response->assertRedirect(route('manajer.kriteria.index', ['periode_id' => $draftPeriode->id]));
        $this->assertDatabaseHas('kriteria', [
            'periode_id' => $draftPeriode->id,
            'kode_kriteria' => 'C10',
            'urutan' => 1,
        ]);
    }
}
