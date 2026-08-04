<?php

namespace Tests\Feature;

use App\Models\Kriteria;
use App\Models\Periode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KriteriaMultiPeriodeUniqueTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'RolePermissionSeeder']);
    }

    public function test_multiple_periodes_can_have_kriteria_with_same_urutan_and_kode()
    {
        $manajer = User::where('email', 'manajer@saungaqiqah.com')->first();

        $periode1 = Periode::create([
            'nama_periode' => 'Periode 1',
            'status' => Periode::STATUS_DRAFT,
        ]);

        $periode2 = Periode::create([
            'nama_periode' => 'Periode 2',
            'status' => Periode::STATUS_DRAFT,
        ]);

        // Create C1 with urutan=1 in Periode 1
        $res1 = $this->actingAs($manajer)->post(route('manajer.kriteria.store'), [
            'periode_id' => $periode1->id,
            'kode_kriteria' => 'C1',
            'nama_kriteria' => 'Aksesibilitas P1',
            'bobot' => 20.0,
            'atribut' => 'benefit',
            'jenis_input' => 'scoring',
        ]);
        $res1->assertRedirect(route('manajer.kriteria.index', ['periode_id' => $periode1->id]));

        // Create C1 with urutan=1 in Periode 2 (Should succeed!)
        $res2 = $this->actingAs($manajer)->post(route('manajer.kriteria.store'), [
            'periode_id' => $periode2->id,
            'kode_kriteria' => 'C1',
            'nama_kriteria' => 'Aksesibilitas P2',
            'bobot' => 30.0,
            'atribut' => 'benefit',
            'jenis_input' => 'scoring',
        ]);
        $res2->assertRedirect(route('manajer.kriteria.index', ['periode_id' => $periode2->id]));

        $this->assertDatabaseHas('kriteria', [
            'periode_id' => $periode1->id,
            'kode_kriteria' => 'C1',
            'urutan' => 1,
        ]);

        $this->assertDatabaseHas('kriteria', [
            'periode_id' => $periode2->id,
            'kode_kriteria' => 'C1',
            'urutan' => 1,
        ]);
    }
}
