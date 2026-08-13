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

    public function reverseGeocode(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $lat = (float) $request->latitude;
        $lng = (float) $request->longitude;

        try {
            $response = \Illuminate\Support\Facades\Http::withoutVerifying()->withHeaders([
                'User-Agent' => 'SaungAqiqahApp/1.0 (contact@saungaqiqah.com)',
                'Accept-Language' => 'id,en-US;q=0.9,en;q=0.8',
            ])->timeout(5)->get('https://nominatim.openstreetmap.org/reverse', [
                'format' => 'json',
                'lat' => $lat,
                'lon' => $lng,
                'zoom' => 18,
                'addressdetails' => 1,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (!empty($data) && isset($data['address'])) {
                    return response()->json($data);
                }
                \Illuminate\Support\Facades\Log::warning('Nominatim response successful but address empty or missing', ['data' => $data]);
            } else {
                \Illuminate\Support\Facades\Log::warning('Nominatim response not successful', ['status' => $response->status(), 'body' => $response->body()]);
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Nominatim Reverse Geocode exception: ' . $e->getMessage());
        }

        // Alternatif menggunakan API BigDataCloud jika Nominatim gagal atau terblokir
        try {
            $fallbackResponse = \Illuminate\Support\Facades\Http::withoutVerifying()->timeout(5)
                ->get('https://api.bigdatacloud.net/data/reverse-geocode-client', [
                    'latitude' => $lat,
                    'longitude' => $lng,
                    'localityLanguage' => 'id',
                ]);

            if ($fallbackResponse->successful()) {
                $bgData = $fallbackResponse->json();
                $address = [
                    'state' => $bgData['principalSubdivision'] ?? '',
                    'city' => $bgData['city'] ?? '',
                    'district' => $bgData['locality'] ?? '',
                ];
                $displayName = implode(', ', array_filter([$address['district'], $address['city'], $address['state']]));
                return response()->json([
                    'display_name' => $displayName,
                    'address' => $address,
                ]);
            } else {
                \Illuminate\Support\Facades\Log::warning('BigDataCloud response not successful', ['status' => $fallbackResponse->status(), 'body' => $fallbackResponse->body()]);
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('BigDataCloud Reverse Geocode exception: ' . $e->getMessage());
        }

        \Illuminate\Support\Facades\Log::error('Reverse geocode failed on both Nominatim and BigDataCloud. Returning 404.');
        return response()->json(['error' => 'Unable to reverse geocode location'], 404);
    }
}
