<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SeedDummyTopsis extends Command
{
    /**
     * Nama dan signature dari perintah console.
     *
     * @var string
     */
    protected $signature = 'import:dummy';

    /**
     * Deskripsi perintah console.
     *
     * @var string
     */
    protected $description = 'Memuat data dummy untuk pengujian perhitungan TOPSIS';

    /**
     * Menjalankan perintah console.
     */
    public function handle()
    {
        $this->info('Menjalankan seeder data dummy TOPSIS...');
        
        Artisan::call('db:seed', [
            '--class' => 'DummyTopsisSeeder'
        ], $this->output);
        
        $this->info('Data dummy berhasil dimasukkan! Silakan cek perhitungan TOPSIS di aplikasi.');
    }
}
