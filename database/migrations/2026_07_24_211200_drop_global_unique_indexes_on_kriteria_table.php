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
        Schema::table('kriteria', function (Blueprint $table) {
            $table->dropUnique('kriteria_urutan_unique');
            $table->dropUnique('kriteria_kode_kriteria_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op for down migration on unique indexes
    }
};
