<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ImportSpatialDataset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:dataset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import spatial datasets for competitors and slaughterhouses into the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Spatial Dataset Import...');

        $this->importCompetitors();
        $this->info('');
        $this->importSlaughterhouses();

        $this->info('Import process completed successfully.');
    }

    private function importCompetitors()
    {
        $path = base_path(config('spatial.dataset_competitors', 'dataset/data_pesaing_aqiqah_jabar.csv'));
        $this->info("Importing Competitors from: {$path}");
        
        if (!file_exists($path)) {
            $this->error("Competitors dataset not found.");
            return;
        }

        $stats = ['imported' => 0, 'updated' => 0, 'skipped' => 0, 'invalid' => 0];
        
        $file = fopen($path, 'r');
        $header = fgetcsv($file); // nama_pesaing,kabupaten_kota,latitude,longitude,rating_bintang
        
        $totalLines = $this->countLines($path);
        $bar = $this->output->createProgressBar($totalLines);
        $bar->start();
        
        while (($row = fgetcsv($file)) !== false) {
            $bar->advance();

            // Basic validation
            if (count($row) < 4 || empty($row[0]) || empty($row[2]) || empty($row[3])) {
                $stats['invalid']++;
                continue;
            }

            try {
                $competitor = \App\Models\Competitor::updateOrCreate(
                    [
                        'nama' => $row[0],
                        'latitude' => (float) $row[2],
                        'longitude' => (float) $row[3],
                    ],
                    [
                        'kabupaten_kota' => $row[1] ?? null,
                        'rating' => isset($row[4]) && is_numeric($row[4]) ? (float) $row[4] : null,
                    ]
                );

                if ($competitor->wasRecentlyCreated) {
                    $stats['imported']++;
                } else {
                    $stats['updated']++;
                }
            } catch (\Exception $e) {
                $stats['invalid']++;
            }
        }
        $bar->finish();
        fclose($file);

        $this->info("\nCompetitors Import Summary:");
        $this->table(['Imported', 'Updated', 'Skipped', 'Invalid'], [[$stats['imported'], $stats['updated'], $stats['skipped'], $stats['invalid']]]);
    }

    private function importSlaughterhouses()
    {
        $path = base_path(config('spatial.dataset_rph', 'dataset/data_rph_jabar.csv'));
        $this->info("Importing Slaughterhouses from: {$path}");
        
        if (!file_exists($path)) {
            $this->error("Slaughterhouses dataset not found.");
            return;
        }

        $stats = ['imported' => 0, 'updated' => 0, 'skipped' => 0, 'invalid' => 0];
        
        $file = fopen($path, 'r');
        $header = fgetcsv($file); // nama_rph,alamat,kabupaten_kota,no_telp,latitude,longitude,kategori
        
        $totalLines = $this->countLines($path);
        $bar = $this->output->createProgressBar($totalLines);
        $bar->start();
        
        while (($row = fgetcsv($file)) !== false) {
            $bar->advance();

            // Basic validation
            if (count($row) < 6 || empty($row[0]) || empty($row[4]) || empty($row[5])) {
                $stats['invalid']++;
                continue;
            }

            try {
                $rph = \App\Models\Slaughterhouse::updateOrCreate(
                    [
                        'nama' => $row[0],
                        'latitude' => (float) $row[4],
                        'longitude' => (float) $row[5],
                    ],
                    [
                        'alamat' => $row[1] ?? null,
                        'kabupaten_kota' => $row[2] ?? null,
                    ]
                );

                if ($rph->wasRecentlyCreated) {
                    $stats['imported']++;
                } else {
                    $stats['updated']++;
                }
            } catch (\Exception $e) {
                $stats['invalid']++;
            }
        }
        $bar->finish();
        fclose($file);

        $this->info("\nSlaughterhouses Import Summary:");
        $this->table(['Imported', 'Updated', 'Skipped', 'Invalid'], [[$stats['imported'], $stats['updated'], $stats['skipped'], $stats['invalid']]]);
    }

    private function countLines($path)
    {
        $lines = 0;
        $handle = fopen($path, "r");
        while(!feof($handle)){
            $line = fgets($handle);
            if ($line !== false) $lines++;
        }
        fclose($handle);
        return max(1, $lines - 1); // Exclude header
    }
}
