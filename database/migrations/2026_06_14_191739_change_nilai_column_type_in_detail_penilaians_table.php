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
        Schema::table('detail_penilaian', function (Blueprint $table) {
            $table->decimal('nilai', 16, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_penilaian', function (Blueprint $table) {
            $table->decimal('nilai', 10, 2)->change();
        });
    }
};
