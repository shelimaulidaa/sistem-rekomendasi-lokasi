<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class DecisionMatrixSheet implements FromArray, WithHeadings, WithTitle
{
    protected $matrix;
    protected $kriterias;
    protected $results;

    public function __construct(array $matrix, $kriterias, array $results)
    {
        $this->matrix = $matrix;
        $this->kriterias = $kriterias;
        $this->results = $results;
    }

    public function array(): array
    {
        $data = [];
        // Petakan penilaian_id ke nama_pemilik untuk pencarian mudah
        $lokasiMap = [];
        foreach ($this->results as $res) {
            $lokasiMap[$res['penilaian_id']] = $res['nama_pemilik'];
        }

        foreach ($this->matrix as $penilaianId => $scores) {
            $row = [
                $lokasiMap[$penilaianId] ?? 'Unknown Lokasi'
            ];
            foreach ($this->kriterias as $k) {
                $row[] = $scores[$k->kriteria_id] ?? 0;
            }
            $data[] = $row;
        }

        return $data;
    }

    public function headings(): array
    {
        $headings = ['Alternatif (Lokasi)'];
        foreach ($this->kriterias as $k) {
            $headings[] = $k->kode_kriteria . ' - ' . $k->nama_kriteria;
        }
        return $headings;
    }

    public function title(): string
    {
        return 'Decision Matrix (X)';
    }
}
