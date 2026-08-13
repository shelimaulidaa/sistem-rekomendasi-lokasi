<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Periode;
use App\Models\Kriteria;
use App\Models\ObservasiLokasi;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PeriodeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::create(['name' => 'manajer']);
    }

    public function test_create_periode_sets_status_draft(): void
    {
        $user = User::factory()->create();
        $user->assignRole('manajer');

        $response = $this->actingAs($user)->post(route('manajer.periode.store'), [
            'nama_periode' => 'Periode Baru 2026',
        ]);

        $response->assertRedirect(route('manajer.periode.index'));
        $this->assertDatabaseHas('periodes', [
            'nama_periode' => 'Periode Baru 2026',
            'status' => Periode::STATUS_DRAFT,
        ]);
    }

    public function test_can_update_periode_status_via_edit(): void
    {
        $user = User::factory()->create();
        $user->assignRole('manajer');

        $periode = Periode::create([
            'nama_periode' => 'Periode Untuk Diarsipkan',
            'status' => Periode::STATUS_SELESAI,
        ]);

        $response = $this->actingAs($user)->put(route('manajer.periode.update', $periode), [
            'nama_periode' => 'Periode Untuk Diarsipkan',
            'status' => Periode::STATUS_DIARSIPKAN,
        ]);

        $response->assertRedirect(route('manajer.periode.index'));
        $this->assertDatabaseHas('periodes', [
            'id' => $periode->id,
            'status' => Periode::STATUS_DIARSIPKAN,
        ]);
    }

    public function test_cannot_delete_periode_with_observasi_lokasis(): void
    {
        $user = User::factory()->create();
        $user->assignRole('manajer');

        $periode = Periode::create([
            'nama_periode' => 'Periode Test Dengan Data',
            'status' => Periode::STATUS_DRAFT,
        ]);

        ObservasiLokasi::create([
            'periode_id' => $periode->id,
            'user_id' => $user->id,
            'nama_pemilik' => 'Bapak Test',
            'nomor_telepon_pemilik' => '08123456789',
            'alamat_lengkap' => 'Jl. Test No. 1, Bandung',
            'provinsi' => 'Jawa Barat',
            'kabupaten_kota' => 'Kota Bandung',
            'kecamatan' => 'Coblong',
            'harga_sewa' => 10000000,
            'jarak_rph' => 2.5,
            'jumlah_kompetitor' => 1,
            'jenis_bangunan' => 'Rumah',
            'kondisi_bangunan' => 'Sangat Baik',
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

        $response = $this->actingAs($user)->delete(route('manajer.periode.destroy', $periode));

        $response->assertRedirect(route('manajer.periode.index'));
        $response->assertSessionHas('error', 'Periode tidak dapat dihapus karena sudah memiliki data observasi lokasi.');
        $this->assertDatabaseHas('periodes', [
            'id' => $periode->id,
            'deleted_at' => null,
        ]);
    }

    public function test_can_delete_empty_periode(): void
    {
        $user = User::factory()->create();
        $user->assignRole('manajer');

        $emptyPeriode = Periode::create([
            'nama_periode' => 'Periode Kosong Untuk Dihapus',
            'status' => Periode::STATUS_DRAFT,
        ]);

        Kriteria::create([
            'periode_id' => $emptyPeriode->id,
            'kode_kriteria' => 'C1',
            'nama_kriteria' => 'Kriteria Test',
            'bobot' => 100,
            'atribut' => 'benefit',
            'jenis_input' => 'angka',
            'urutan' => 1,
        ]);

        $response = $this->actingAs($user)->delete(route('manajer.periode.destroy', $emptyPeriode));

        $response->assertRedirect(route('manajer.periode.index'));
        $response->assertSessionHas('success', "Periode Periode Kosong Untuk Dihapus berhasil dihapus.");
        $this->assertDatabaseMissing('periodes', ['id' => $emptyPeriode->id]);
        $this->assertDatabaseMissing('kriteria', ['periode_id' => $emptyPeriode->id]);
    }

    public function test_cannot_change_status_from_selesai_to_draft_if_calculated(): void
    {
        $user = User::factory()->create();
        $user->assignRole('manajer');

        $periode = Periode::create([
            'nama_periode' => 'Periode Selesai Berhitung',
            'status' => Periode::STATUS_SELESAI,
        ]);

        $observasi = ObservasiLokasi::create([
            'periode_id' => $periode->id,
            'user_id' => $user->id,
            'nama_pemilik' => 'Bapak Test',
            'nomor_telepon_pemilik' => '08123456789',
            'alamat_lengkap' => 'Jl. Test No. 1, Bandung',
            'provinsi' => 'Jawa Barat',
            'kabupaten_kota' => 'Kota Bandung',
            'kecamatan' => 'Coblong',
            'harga_sewa' => 10000000,
            'jarak_rph' => 2.5,
            'jumlah_kompetitor' => 1,
            'jenis_bangunan' => 'Rumah',
            'kondisi_bangunan' => 'Sangat Baik',
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

        $penilaian = \App\Models\Penilaian::create([
            'observasi_lokasi_id' => $observasi->id,
            'user_id' => $user->id,
            'tanggal_penilaian' => now(),
            'total_skor' => 85.5,
        ]);

        \App\Models\HasilPerhitungan::create([
            'penilaian_id' => $penilaian->penilaian_id,
            'nilai_preferensi' => 0.855,
            'ranking' => 1,
            'tanggal_hitung' => now(),
        ]);

        $response = $this->actingAs($user)->put(route('manajer.periode.update', $periode), [
            'nama_periode' => 'Periode Selesai Berhitung',
            'status' => Periode::STATUS_DRAFT,
        ]);

        $response->assertSessionHas('error', 'Periode yang telah selesai dilakukan perhitungan tidak dapat diubah kembali ke status Draft.');
        $this->assertDatabaseHas('periodes', [
            'id' => $periode->id,
            'status' => Periode::STATUS_SELESAI,
        ]);
    }
}
