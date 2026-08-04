<?php

namespace Database\Seeders;

use App\Models\Kriteria;
use Illuminate\Database\Seeder;

class KriteriaSeeder extends Seeder
{
    public function run(): void
    {
        $kriterias = [
            [
                'kode_kriteria' => 'C1',
                'nama_kriteria' => 'Aksesibilitas',
                'bobot' => 23.00,
                'atribut' => 'benefit',
                'jenis_input' => 'scoring',
                'kunci_observasi' => 'aksesibilitas',
            ],
            [
                'kode_kriteria' => 'C2',
                'nama_kriteria' => 'Kelayakan Bangunan',
                'bobot' => 21.00,
                'atribut' => 'benefit',
                'jenis_input' => 'scoring',
                'kunci_observasi' => 'kelayakan_bangunan',
            ],
            [
                'kode_kriteria' => 'C3',
                'nama_kriteria' => 'Jarak ke RPH',
                'bobot' => 19.00,
                'atribut' => 'cost',
                'jenis_input' => 'numeric',
                'kunci_observasi' => 'jarak_rph',
            ],
            [
                'kode_kriteria' => 'C4',
                'nama_kriteria' => 'Biaya Sewa',
                'bobot' => 19.00,
                'atribut' => 'cost',
                'jenis_input' => 'numeric',
                'kunci_observasi' => 'biaya_sewa',
            ],
            [
                'kode_kriteria' => 'C5',
                'nama_kriteria' => 'Tingkat Pesaing',
                'bobot' => 18.00,
                'atribut' => 'cost',
                'jenis_input' => 'numeric',
                'kunci_observasi' => 'jumlah_kompetitor',
            ]
        ];

        $firstPeriode = \App\Models\Periode::first();
        $periodeId = $firstPeriode?->id;

        $urutan = 1;
        foreach ($kriterias as $kriteria) {
            $kriteria['urutan'] = $urutan++;
            if ($periodeId) {
                $kriteria['periode_id'] = $periodeId;
            }
            Kriteria::create($kriteria);
        }
    }
}
