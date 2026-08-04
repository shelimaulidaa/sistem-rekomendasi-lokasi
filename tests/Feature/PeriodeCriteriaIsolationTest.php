<?php

namespace Tests\Feature;

use App\Models\ObservasiLokasi;
use App\Models\Periode;
use App\Models\Kriteria;
use App\Models\Penilaian;
use App\Models\DetailPenilaian;
use App\Models\HasilPerhitungan;
use App\Models\User;
use App\Services\TopsisService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PeriodeCriteriaIsolationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'RolePermissionSeeder']);
    }

    public function test_periode_criteria_and_topsis_isolation()
    {
        $manajer = User::where('email', 'manajer@saungaqiqah.com')->first();

        // Periode 1 (Old Period - Status Selesai)
        $periodeOld = Periode::create([
            'nama_periode' => 'Periode Old Calculated',
            'status' => Periode::STATUS_DRAFT,
        ]);

        $kriteriaOld1 = Kriteria::create([
            'periode_id' => $periodeOld->id,
            'kode_kriteria' => 'C1',
            'nama_kriteria' => 'Kriteria Old A',
            'bobot' => 50,
            'atribut' => 'benefit',
            'jenis_input' => 'scoring',
            'urutan' => 1,
        ]);

        $kriteriaOld2 = Kriteria::create([
            'periode_id' => $periodeOld->id,
            'kode_kriteria' => 'C2',
            'nama_kriteria' => 'Kriteria Old B',
            'bobot' => 50,
            'atribut' => 'cost',
            'jenis_input' => 'numeric',
            'urutan' => 2,
        ]);

        $obsOld = ObservasiLokasi::create([
            'periode_id' => $periodeOld->id,
            'user_id' => $manajer->id,
            'nama_pemilik' => 'Lokasi Old',
            'nomor_telepon_pemilik' => '08123456789',
            'alamat_lengkap' => 'Alamat Old',
            'provinsi' => 'Jawa Barat',
            'kabupaten_kota' => 'Bandung',
            'kecamatan' => 'Coblong',
            'jenis_bangunan' => 'Ruko',
            'kondisi_bangunan' => 'Baik',
            'luas_tanah' => 100,
            'luas_bangunan' => 100,
            'jumlah_lantai' => 1,
            'jumlah_ruangan' => 2,
            'jumlah_wc' => 1,
            'sumber_air' => 'PDAM',
            'daya_listrik' => '1300',
            'area_parkir' => 'Cukup',
            'lebar_jalan' => '5m',
            'ventilasi' => 'Baik',
            'sirkulasi' => 'Baik',
            'harga_sewa' => 20000000,
            'jarak_rph' => 2.0,
            'jumlah_kompetitor' => 1,
            'tanggal_observasi' => now()->toDateString(),
            'jam_observasi' => '08:00',
        ]);

        // Save detail penilaian for Old Period
        $penilaianOld = Penilaian::create([
            'observasi_lokasi_id' => $obsOld->id,
            'user_id' => $manajer->id,
            'tanggal_penilaian' => now(),
        ]);

        DetailPenilaian::create([
            'penilaian_id' => $penilaianOld->penilaian_id,
            'kriteria_id' => $kriteriaOld1->kriteria_id,
            'nilai' => 4,
        ]);
        DetailPenilaian::create([
            'penilaian_id' => $penilaianOld->penilaian_id,
            'kriteria_id' => $kriteriaOld2->kriteria_id,
            'nilai' => 15000000,
        ]);

        // Calculate TOPSIS for Old Period
        $topsisService = app(TopsisService::class);
        $topsisService->calculate($periodeOld->id);

        $this->assertEquals(Periode::STATUS_SELESAI, $periodeOld->fresh()->status);
        $oldHasilCount = HasilPerhitungan::wherePeriode($periodeOld->id)->count();
        $this->assertEquals(1, $oldHasilCount);

        // Periode 2 (New Period - Status Draft)
        $periodeNew = Periode::create([
            'nama_periode' => 'Periode New Draft',
            'status' => Periode::STATUS_DRAFT,
        ]);

        // Add a NEW custom criteria to Periode 2
        $kriteriaNew1 = Kriteria::create([
            'periode_id' => $periodeNew->id,
            'kode_kriteria' => 'C1',
            'nama_kriteria' => 'Kriteria New Unique',
            'bobot' => 100,
            'atribut' => 'benefit',
            'jenis_input' => 'scoring',
            'urutan' => 1,
        ]);

        // Verify Old Period criteria query returns ONLY Old Period criteria
        $oldCriteriaQuery = Kriteria::where('periode_id', $periodeOld->id)->pluck('kriteria_id')->toArray();
        $this->assertContains($kriteriaOld1->kriteria_id, $oldCriteriaQuery);
        $this->assertContains($kriteriaOld2->kriteria_id, $oldCriteriaQuery);
        $this->assertNotContains($kriteriaNew1->kriteria_id, $oldCriteriaQuery);

        // Verify New Period criteria query returns ONLY New Period criteria
        $newCriteriaQuery = Kriteria::where('periode_id', $periodeNew->id)->pluck('kriteria_id')->toArray();
        $this->assertContains($kriteriaNew1->kriteria_id, $newCriteriaQuery);
        $this->assertNotContains($kriteriaOld1->kriteria_id, $newCriteriaQuery);

        // Verify Old HasilPerhitungan has not changed
        $currentOldHasilCount = HasilPerhitungan::wherePeriode($periodeOld->id)->count();
        $this->assertEquals($oldHasilCount, $currentOldHasilCount);
    }

    public function test_new_periode_has_empty_criteria_and_zero_bobot()
    {
        $kriteriaService = app(\App\Services\KriteriaService::class);

        // Create an existing period with criteria
        $periodeExisting = Periode::create([
            'nama_periode' => 'Periode Existing',
            'status' => Periode::STATUS_DRAFT,
        ]);
        Kriteria::create([
            'periode_id' => $periodeExisting->id,
            'kode_kriteria' => 'C1',
            'nama_kriteria' => 'Existing Kriteria',
            'bobot' => 100,
            'atribut' => 'benefit',
            'jenis_input' => 'scoring',
            'urutan' => 1,
        ]);

        // Create a new empty period
        $periodeBrandNew = Periode::create([
            'nama_periode' => 'Periode Brand New',
            'status' => Periode::STATUS_DRAFT,
        ]);

        // 1. Verify criteria list for brand new period is empty (does not inherit from existing period)
        $brandNewCriteria = $kriteriaService->getKriteriaByPeriode($periodeBrandNew->id)->get();
        $this->assertCount(0, $brandNewCriteria);

        // 2. Verify total bobot is 0 (not 100%)
        $totalBobot = $kriteriaService->getTotalBobot($periodeBrandNew->id);
        $this->assertEquals(0.0, $totalBobot);
    }

    public function test_inactive_criteria_fields_are_not_required_and_not_calculated_in_topsis()
    {
        $manajer = User::where('email', 'manajer@saungaqiqah.com')->first();

        // Create a period with ONLY C1 (Jarak RPH) and C2 (Jumlah Kompetitor) - NO Biaya Sewa
        $periodeNoBiayaSewa = Periode::create([
            'nama_periode' => 'Periode Tanpa Biaya Sewa',
            'status' => Periode::STATUS_DRAFT,
        ]);

        Kriteria::create([
            'periode_id' => $periodeNoBiayaSewa->id,
            'kode_kriteria' => 'C1',
            'nama_kriteria' => 'Jarak ke RPH',
            'bobot' => 50,
            'atribut' => 'cost',
            'jenis_input' => 'numeric',
            'kunci_observasi' => 'jarak_rph',
            'urutan' => 1,
        ]);

        Kriteria::create([
            'periode_id' => $periodeNoBiayaSewa->id,
            'kode_kriteria' => 'C2',
            'nama_kriteria' => 'Tingkat Pesaing',
            'bobot' => 50,
            'atribut' => 'cost',
            'jenis_input' => 'numeric',
            'kunci_observasi' => 'jumlah_kompetitor',
            'urutan' => 2,
        ]);

        // Post observation for this period without filling harga_sewa (Biaya Sewa)
        $response = $this->actingAs($manajer)->post(route('manajer.observasi.store'), [
            'batch_id' => $periodeNoBiayaSewa->id,
            'nama_pemilik' => 'Lokasi Test Without Biaya Sewa',
            'nomor_telepon_pemilik' => '08123456789',
            'alamat_lengkap' => 'Jl. Test 456',
            'provinsi' => 'JAWA BARAT',
            'kabupaten_kota' => 'KOTA BANDUNG',
            'kecamatan' => 'COBLONG',
            'tanggal_observasi' => now()->format('Y-m-d'),
            'harga_sewa' => null, // Biaya Sewa is NOT a criteria on this period -> must be optional
            'jarak_rph' => 2.5,
            'jumlah_kompetitor' => 1,
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $obs = ObservasiLokasi::where('nama_pemilik', 'Lokasi Test Without Biaya Sewa')->first();
        $this->assertNotNull($obs);
        $this->assertNull($obs->harga_sewa);

        // Run TOPSIS calculation for this period
        $topsisService = app(TopsisService::class);
        $topsisResult = $topsisService->calculate($periodeNoBiayaSewa->id);

        // Verify TOPSIS calculated successfully using ONLY active criteria (2 criteria)
        $this->assertEquals(Periode::STATUS_SELESAI, $periodeNoBiayaSewa->fresh()->status);
        $this->assertCount(2, $topsisResult['kriterias']);
        $this->assertCount(1, $topsisResult['results']);
        $this->assertEquals(1, $topsisResult['results'][0]['ranking']);
    }
}
