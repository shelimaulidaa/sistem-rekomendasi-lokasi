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
            $table->boolean('mudah_ditemukan')->default(false)->after('dekat_fasilitas');
            $table->boolean('mudah_dijangkau')->default(false)->after('mudah_ditemukan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('observasi_lokasi', function (Blueprint $table) {
            $table->dropColumn(['mudah_ditemukan', 'mudah_dijangkau']);
        });
    }
};
