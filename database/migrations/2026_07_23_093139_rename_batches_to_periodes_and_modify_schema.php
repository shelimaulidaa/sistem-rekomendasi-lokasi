<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('batches') && !Schema::hasTable('periodes')) {
            Schema::rename('batches', 'periodes');
        }
        
        if (Schema::hasTable('periodes')) {
            if (!Schema::hasColumn('periodes', 'status')) {
                Schema::table('periodes', function (Blueprint $table) {
                    if (Schema::hasColumn('periodes', 'nama_batch')) {
                        $table->renameColumn('nama_batch', 'nama_periode');
                    }
                    $table->date('tanggal_mulai')->nullable();
                    $table->date('tanggal_selesai')->nullable();
                    $table->string('status', 50)->default('Draft');
                });

                if (Schema::hasColumn('periodes', 'is_active')) {
                    DB::table('periodes')->update([
                        'status' => DB::raw("CASE WHEN is_active = 1 THEN 'Aktif' ELSE 'Selesai' END"),
                        'tanggal_mulai' => DB::raw("DATE(created_at)"),
                        'tanggal_selesai' => DB::raw("CASE WHEN is_active = 1 THEN NULL ELSE DATE(updated_at) END"),
                    ]);

                    Schema::table('periodes', function (Blueprint $table) {
                        $table->dropColumn('is_active');
                    });
                }
            }
        }
    }


    public function down(): void
    {
        Schema::table('periodes', function (Blueprint $table) {
            $table->boolean('is_active')->default(false);
        });

        DB::table('periodes')->update([
            'is_active' => DB::raw("CASE WHEN status = 'Aktif' THEN 1 ELSE 0 END")
        ]);

        Schema::table('periodes', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('tanggal_mulai');
            $table->dropColumn('tanggal_selesai');
            $table->renameColumn('nama_periode', 'nama_batch');
        });

        Schema::rename('periodes', 'batches');
    }
};
