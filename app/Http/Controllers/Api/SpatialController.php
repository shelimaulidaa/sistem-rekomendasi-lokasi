<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\SpatialAnalysisService;

class SpatialController extends Controller
{
    protected $spatialService;

    public function __construct(SpatialAnalysisService $spatialService)
    {
        $this->spatialService = $spatialService;
    }

    public function analyzeLocation(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $lat = (float) $request->latitude;
        $lng = (float) $request->longitude;

        $analysis = $this->spatialService->analyzeLocation($lat, $lng);

        return response()->json($analysis);
    }
}
