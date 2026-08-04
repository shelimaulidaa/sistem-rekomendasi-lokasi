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
            $table->string('nama_pemilik')->nullable()->change();
            $table->string('nomor_telepon_pemilik')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('observasi_lokasi', function (Blueprint $table) {
            $table->string('nama_pemilik')->nullable(false)->change();
            $table->string('nomor_telepon_pemilik')->nullable(false)->change();
        });
    }
};
