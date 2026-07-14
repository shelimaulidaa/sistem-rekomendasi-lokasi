<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('observasi_lokasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users', 'id')->cascadeOnDelete();
            
            // Location Identity
            $table->string('nama_pemilik');
            $table->string('nomor_telepon_pemilik');
            $table->text('alamat_lengkap');
            $table->string('provinsi');
            $table->string('kabupaten_kota');
            $table->string('kecamatan');
            $table->string('province_id', 10)->nullable();
            $table->string('regency_id', 10)->nullable();
            $table->string('district_id', 10)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            $table->string('jenis_bangunan');
            $table->decimal('luas_tanah', 10, 2);
            $table->decimal('luas_bangunan', 10, 2);
            $table->integer('jumlah_ruangan');
            $table->integer('jumlah_wc');
            $table->boolean('listrik');
            $table->string('sumber_air');
            $table->decimal('harga_sewa', 15, 2);
            $table->decimal('jarak_rph', 8, 2); // dalam KM
            $table->integer('jumlah_kompetitor');
            
            // Boolean indicators
            $table->boolean('akses_roda4');
            $table->boolean('jalan_bagus');
            $table->boolean('dekat_fasilitas');
            $table->boolean('bangunan_layak');
            $table->boolean('ventilasi_baik');
            $table->boolean('air_listrik_memadai');
            
            
            $table->text('catatan')->nullable();
            $table->date('tanggal_observasi');
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['kecamatan', 'kabupaten_kota', 'provinsi']);
            $table->index('tanggal_observasi');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('observasi_lokasi');
    }
};
