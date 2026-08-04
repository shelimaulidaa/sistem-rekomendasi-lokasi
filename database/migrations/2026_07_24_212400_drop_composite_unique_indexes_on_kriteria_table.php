<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasIndex('kriteria', 'kriteria_periode_id_kode_kriteria_unique')) {
            Schema::table('kriteria', function ($table) {
                $table->dropIndex('kriteria_periode_id_kode_kriteria_unique');
            });
        }

        if (Schema::hasIndex('kriteria', 'kriteria_periode_id_urutan_unique')) {
            Schema::table('kriteria', function ($table) {
                $table->dropIndex('kriteria_periode_id_urutan_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op
    }
};
