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
        if (Schema::hasTable('kriteria')) {
            Schema::table('kriteria', function (Blueprint $table) {
                if (!Schema::hasColumn('kriteria', 'periode_id')) {
                    $table->unsignedBigInteger('periode_id')->nullable()->after('kriteria_id');

                    $targetTable = Schema::hasTable('periodes') ? 'periodes' : (Schema::hasTable('batches') ? 'batches' : null);
                    if ($targetTable) {
                        $table->foreign('periode_id')->references('id')->on($targetTable)->nullOnDelete();
                    }
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('kriteria')) {
            Schema::table('kriteria', function (Blueprint $table) {
                if (Schema::hasColumn('kriteria', 'periode_id')) {
                    // Try dropping foreign key first
                    try {
                        $table->dropForeign(['periode_id']);
                    } catch (\Exception $e) {
                        // Foreign key might not exist in some DB drivers
                    }
                    $table->dropColumn('periode_id');
                }
            });
        }
    }
};
