<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Services\TopsisService;

class RekomendasiExport implements WithMultipleSheets
{
    protected $topsisData;

    public function __construct($periodeId = null, $status = null)
    {
        $service = app(TopsisService::class);
        $this->topsisData = $service->calculate($periodeId);
    }

    public function sheets(): array
    {
        return [
            new FinalRankingSheet($this->topsisData['results']),
            new DecisionMatrixSheet($this->topsisData['matrix'], $this->topsisData['kriterias'], $this->topsisData['results']),
            new NormalizedMatrixSheet($this->topsisData['normalizedMatrix'], $this->topsisData['kriterias'], $this->topsisData['results']),
        ];
    }
}
