<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class FinalRankingSheet implements FromArray, WithHeadings, WithTitle
{
    protected $results;

    public function __construct(array $results)
    {
        $this->results = $results;
    }

    public function array(): array
    {
        $data = [];
        foreach ($this->results as $res) {
            // Determine status
            $status = 'Dipertimbangkan';
            if ($res['ranking'] == 1) {
                $status = 'Sangat Direkomendasikan';
            } elseif ($res['ranking'] <= 3) {
                $status = 'Direkomendasikan';
            }

            $data[] = [
                $res['ranking'],
                $res['nama_pemilik'],
                round($res['preference_score'], 4),
                $status,
                now()->format('Y-m-d H:i:s'),
            ];
        }
        return $data;
    }

    public function headings(): array
    {
        return [
            'Ranking',
            'Nama Pemilik',
            'Nilai Preferensi (V)',
            'Status Rekomendasi',
            'Timestamp Perhitungan',
        ];
    }

    public function title(): string
    {
        return 'Final Ranking';
    }
}
