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
            $table->integer('jumlah_lantai')->after('luas_bangunan')->default(1);
            $table->string('kondisi_bangunan')->after('jenis_bangunan')->nullable();
            
            // Drop old boolean 'listrik' and add 'daya_listrik'
            if (Schema::hasColumn('observasi_lokasi', 'listrik')) {
                $table->dropColumn('listrik');
            }
            $table->string('daya_listrik')->after('sumber_air')->nullable();
            
            $table->string('area_parkir')->after('daya_listrik')->nullable();
            $table->string('lebar_jalan')->after('area_parkir')->nullable();
            $table->string('ventilasi')->after('lebar_jalan')->nullable();
            $table->string('sirkulasi')->after('ventilasi')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('observasi_lokasi', function (Blueprint $table) {
            $table->dropColumn([
                'jumlah_lantai',
                'kondisi_bangunan',
                'daya_listrik',
                'area_parkir',
                'lebar_jalan',
                'ventilasi',
                'sirkulasi',
            ]);
            $table->boolean('listrik')->default(true);
        });
    }
};
