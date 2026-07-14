<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('observasi_lokasi', function (Blueprint $table) {
            // Nullable just in case existing data doesn't have it, but for new data it's required.
            $table->foreignId('batch_id')->nullable()->constrained('batches')->nullOnDelete();
        });

        Schema::table('hasil_perhitungan', function (Blueprint $table) {
            // Add batch_id to calculation results
            $table->foreignId('batch_id')->nullable()->constrained('batches')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('observasi_lokasi', function (Blueprint $table) {
            $table->dropForeign(['batch_id']);
            $table->dropColumn('batch_id');
        });

        Schema::table('hasil_perhitungan', function (Blueprint $table) {
            $table->dropForeign(['batch_id']);
            $table->dropColumn('batch_id');
        });
    }
};
