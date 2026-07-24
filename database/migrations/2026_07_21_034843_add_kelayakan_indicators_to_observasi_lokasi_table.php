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
            $table->boolean('luas_mencukupi')->default(false)->after('air_listrik_memadai');
            $table->boolean('parkir_memadai')->default(false)->after('luas_mencukupi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('observasi_lokasi', function (Blueprint $table) {
            $table->dropColumn(['luas_mencukupi', 'parkir_memadai']);
        });
    }
};
