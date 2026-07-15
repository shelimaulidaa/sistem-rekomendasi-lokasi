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
            $table->json('anggota_pendamping')->nullable()->after('tanggal_observasi');
            $table->time('jam_observasi')->nullable()->after('tanggal_observasi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('observasi_lokasi', function (Blueprint $table) {
            $table->dropColumn(['anggota_pendamping', 'jam_observasi']);
        });
    }
};
