<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('observasi_lokasi', function (Blueprint $table) {
            // Drop old legacy columns
            $table->dropColumn([
                'akses_roda4',
                'jalan_bagus',
                'dekat_fasilitas',
                'bangunan_layak',
                'ventilasi_baik',
                'air_listrik_memadai',
            ]);

            // Add new 10 indicators
            $table->boolean('akses_jalan_utama')->default(false);
            $table->boolean('akses_kendaraan_operasional')->default(false);
            $table->boolean('kondisi_jalan_baik')->default(false);
            $table->boolean('mudah_ditemukan_google_maps')->default(false);
            $table->boolean('mudah_dijangkau_pelanggan')->default(false);

            $table->boolean('luas_bangunan_mencukupi')->default(false);
            $table->boolean('kondisi_bangunan_baik')->default(false);
            $table->boolean('ventilasi_sirkulasi_memadai')->default(false);
            $table->boolean('air_listrik_tersedia')->default(false);
            $table->boolean('area_parkir_memadai')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('observasi_lokasi', function (Blueprint $table) {
            // Drop the 10 new columns
            $table->dropColumn([
                'akses_jalan_utama',
                'akses_kendaraan_operasional',
                'kondisi_jalan_baik',
                'mudah_ditemukan_google_maps',
                'mudah_dijangkau_pelanggan',
                'luas_bangunan_mencukupi',
                'kondisi_bangunan_baik',
                'ventilasi_sirkulasi_memadai',
                'air_listrik_tersedia',
                'area_parkir_memadai',
            ]);

            // Restore legacy columns
            $table->boolean('akses_roda4')->default(false);
            $table->boolean('jalan_bagus')->default(false);
            $table->boolean('dekat_fasilitas')->default(false);
            $table->boolean('bangunan_layak')->default(false);
            $table->boolean('ventilasi_baik')->default(false);
            $table->boolean('air_listrik_memadai')->default(false);
        });
    }
};
