<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $table = Schema::hasTable('periodes') ? 'periodes' : (Schema::hasTable('batches') ? 'batches' : null);
        if ($table && !Schema::hasColumn($table, 'status')) {
            Schema::table($table, function (Blueprint $t) {
                $t->string('status', 50)->default('Draft');
            });
        }
    }

    public function down(): void
    {
    }

};
