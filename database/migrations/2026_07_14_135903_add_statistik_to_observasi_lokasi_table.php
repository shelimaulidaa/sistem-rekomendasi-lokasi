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
            $table->decimal('umk', 15, 2)->nullable()->after('kecamatan');
            $table->decimal('pdrb', 15, 2)->nullable()->after('umk');
            $table->bigInteger('jumlah_penduduk_muslim')->nullable()->after('pdrb');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('observasi_lokasi', function (Blueprint $table) {
            $table->dropColumn(['umk', 'pdrb', 'jumlah_penduduk_muslim']);
        });
    }
};
