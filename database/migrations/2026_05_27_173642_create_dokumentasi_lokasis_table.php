<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dokumentasi_lokasi', function (Blueprint $table) {
            $table->id('foto_id');
            $table->foreignId('observasi_lokasi_id')->constrained('observasi_lokasi', 'id')->cascadeOnDelete();
            $table->string('foto_path');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dokumentasi_lokasi');
    }
};
