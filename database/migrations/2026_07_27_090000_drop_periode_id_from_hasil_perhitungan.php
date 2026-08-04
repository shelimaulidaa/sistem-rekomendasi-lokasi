<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Menghapus kolom periode_id dari tabel hasil_perhitungan karena redundan.
     * Periode sudah dapat diketahui melalui relasi:
     * HasilPerhitungan → Penilaian → ObservasiLokasi → Periode
     */
    public function up(): void
    {
        Schema::table('hasil_perhitungan', function (Blueprint $table) {
            // Drop the foreign key first, then the column
            $table->dropForeign(['periode_id']);
            $table->dropColumn('periode_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hasil_perhitungan', function (Blueprint $table) {
            $table->unsignedBigInteger('periode_id')->nullable()->after('hasil_id');
            $table->foreign('periode_id')->references('id')->on('periodes')->cascadeOnDelete();
        });

        // Re-populate periode_id from the relation chain: penilaian → observasi_lokasi
        DB::statement("
            UPDATE hasil_perhitungan
            JOIN penilaian ON hasil_perhitungan.penilaian_id = penilaian.penilaian_id
            JOIN observasi_lokasi ON penilaian.observasi_lokasi_id = observasi_lokasi.id
            SET hasil_perhitungan.periode_id = observasi_lokasi.periode_id
        ");
    }
};
