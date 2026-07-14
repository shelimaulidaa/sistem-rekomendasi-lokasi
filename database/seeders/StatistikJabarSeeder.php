<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatistikJabarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvPath = base_path('dataset/data_pdrb_umk_penduduk_muslim_jabar.csv');
        if (!file_exists($csvPath)) {
            return;
        }

        $file = fopen($csvPath, 'r');
        $header = fgetcsv($file);

        \App\Models\StatistikJabar::truncate();

        while (($row = fgetcsv($file)) !== false) {
            if (count($row) >= 6) {
                \App\Models\StatistikJabar::create([
                    'provinsi' => $row[0],
                    'kabupaten_kota' => $row[1],
                    'kecamatan' => $row[2],
                    'umk' => $row[3],
                    'pdrb_per_capita' => $row[4],
                    'jumlah_penduduk_muslim' => $row[5],
                ]);
            }
        }
        fclose($file);
    }
}
