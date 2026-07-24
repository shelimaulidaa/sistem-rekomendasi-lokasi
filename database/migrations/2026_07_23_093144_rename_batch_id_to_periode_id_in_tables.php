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
            $table->dropForeign(['batch_id']);
            $table->renameColumn('batch_id', 'periode_id');
            $table->foreign('periode_id')->references('id')->on('periodes')->nullOnDelete();
        });

        Schema::table('hasil_perhitungan', function (Blueprint $table) {
            $table->dropForeign(['batch_id']);
            $table->renameColumn('batch_id', 'periode_id');
            $table->foreign('periode_id')->references('id')->on('periodes')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('hasil_perhitungan', function (Blueprint $table) {
            $table->dropForeign(['periode_id']);
            $table->renameColumn('periode_id', 'batch_id');
            $table->foreign('batch_id')->references('id')->on('batches')->cascadeOnDelete();
        });

        Schema::table('observasi_lokasi', function (Blueprint $table) {
            $table->dropForeign(['periode_id']);
            $table->renameColumn('periode_id', 'batch_id');
            $table->foreign('batch_id')->references('id')->on('batches')->nullOnDelete();
        });
    }
};
