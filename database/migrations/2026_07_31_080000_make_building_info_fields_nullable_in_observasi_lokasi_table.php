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
            $table->decimal('harga_sewa', 15, 2)->nullable()->change();
            $table->decimal('luas_bangunan', 10, 2)->nullable()->change();
            $table->decimal('luas_tanah', 10, 2)->nullable()->change();
            $table->integer('jumlah_lantai')->nullable()->change();
            $table->integer('jumlah_ruangan')->nullable()->change();
            $table->integer('jumlah_wc')->nullable()->change();
            $table->string('sumber_air')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('observasi_lokasi', function (Blueprint $table) {
            $table->decimal('harga_sewa', 15, 2)->nullable(false)->change();
            $table->decimal('luas_bangunan', 10, 2)->nullable(false)->change();
            $table->decimal('luas_tanah', 10, 2)->nullable(false)->change();
            $table->integer('jumlah_lantai')->nullable(false)->change();
            $table->integer('jumlah_ruangan')->nullable(false)->change();
            $table->integer('jumlah_wc')->nullable(false)->change();
            $table->string('sumber_air')->nullable(false)->change();
        });
    }
};
