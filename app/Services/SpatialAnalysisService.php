<?php

namespace App\Services;

use App\Models\Competitor;
use App\Models\Slaughterhouse;
use Illuminate\Support\Facades\DB;

class SpatialAnalysisService
{
    public const EARTH_RADIUS_KM = 6371;

    /**
     * Menganalisis lokasi untuk mencari kompetitor dan RPH terdekat.
     * 
     * @param float $lat
     * @param float $lng
     * @return array
     */
    public function analyzeLocation(float $lat, float $lng, ?float $radius = null): array
    {
        $radius = $radius ?? config('spatial.competitor_radius', 5);
        $rphList = $this->getNearestRPH($lat, $lng, 5); // Ambil 5 RPH terdekat
        $competitorsList = $this->getCompetitors($lat, $lng, $radius);
        
        $nearestRPH = $rphList[0] ?? null;
        
        $ratings = array_filter(array_column($competitorsList, 'rating'));
        $avgRating = count($ratings) > 0 ? round(array_sum($ratings) / count($ratings), 2) : null;
        
        return [
            'competitor_count' => count($competitorsList),
            'competitors_avg_rating' => $avgRating,
            'search_radius' => $radius,
            'nearest_rph_id' => $nearestRPH['id'] ?? null,
            'nearest_rph_name' => $nearestRPH['nama'] ?? null,
            'nearest_rph_distance' => $nearestRPH['distance'] ?? null,
            'competitors_list' => $competitorsList,
            'rph_list' => $rphList
        ];
    }

    /**
     * Menghitung kompetitor dalam radius tertentu menggunakan rumus Haversine.
     * 
     * @param float $lat
     * @param float $lng
     * @param float|null $radius Dalam kilometer
     * @return array
     */
    public function getCompetitors(float $lat, float $lng, ?float $radius = null): array
    {
        $radius = $radius ?? config('spatial.competitor_radius', 25);
        $earthRadius = self::EARTH_RADIUS_KM;

        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            $competitors = Competitor::all();
            $formatted = [];
            foreach ($competitors as $item) {
                if ($item->latitude === null || $item->longitude === null) continue;
                $dLat = deg2rad($item->latitude - $lat);
                $dLng = deg2rad($item->longitude - $lng);
                $a = sin($dLat / 2) * sin($dLat / 2) +
                     cos(deg2rad($lat)) * cos(deg2rad($item->latitude)) *
                     sin($dLng / 2) * sin($dLng / 2);
                $a = min(1.0, max(0.0, $a));
                $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
                $distance = $earthRadius * $c;

                if ($distance <= $radius) {
                    $formatted[] = [
                        'id' => $item->id,
                        'nama' => $item->nama,
                        'alamat' => $item->alamat,
                        'rating' => $item->rating,
                        'latitude' => (float)$item->latitude,
                        'longitude' => (float)$item->longitude,
                        'distance' => round($distance, 2)
                    ];
                }
            }
            usort($formatted, fn($a, $b) => $a['distance'] <=> $b['distance']);
            return $formatted;
        }

        $haversine = "({$earthRadius} * acos(LEAST(1.0, GREATEST(-1.0, cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude))))))";
        $sql = "SELECT id, nama, alamat, rating, latitude, longitude, {$haversine} as distance FROM competitors HAVING distance <= ? ORDER BY distance ASC";
        $results = DB::select($sql, [$lat, $lng, $lat, $radius]);
        
        // Format data ke dalam array dan bulatkan jarak
        $formatted = [];
        foreach ($results as $item) {
            $formatted[] = [
                'id' => $item->id,
                'nama' => $item->nama,
                'alamat' => $item->alamat,
                'rating' => $item->rating,
                'latitude' => (float)$item->latitude,
                'longitude' => (float)$item->longitude,
                'distance' => round($item->distance, 2)
            ];
        }
        return $formatted;
    }

    /**
     * Mencari Rumah Potong Hewan (RPH) terdekat menggunakan rumus Haversine.
     * 
     * @param float $lat
     * @param float $lng
     * @return array|null
     */
    private function getNearestRPH(float $lat, float $lng, int $limit = 5): array
    {
        $earthRadius = self::EARTH_RADIUS_KM;
        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            $rphs = Slaughterhouse::all();
            $formatted = [];
            foreach ($rphs as $item) {
                if ($item->latitude === null || $item->longitude === null) continue;
                $dLat = deg2rad($item->latitude - $lat);
                $dLng = deg2rad($item->longitude - $lng);
                $a = sin($dLat / 2) * sin($dLat / 2) +
                     cos(deg2rad($lat)) * cos(deg2rad($item->latitude)) *
                     sin($dLng / 2) * sin($dLng / 2);
                $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
                $distance = $earthRadius * $c;

                $formatted[] = [
                    'id' => $item->id,
                    'nama' => $item->nama,
                    'alamat' => $item->alamat,
                    'latitude' => (float)$item->latitude,
                    'longitude' => (float)$item->longitude,
                    'distance' => round($distance, 2)
                ];
            }
            usort($formatted, fn($a, $b) => $a['distance'] <=> $b['distance']);
            return array_slice($formatted, 0, $limit);
        }

        $haversine = "({$earthRadius} * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude))))";
        $sql = "SELECT id, nama, alamat, latitude, longitude, {$haversine} AS distance FROM slaughterhouses ORDER BY distance ASC LIMIT ?";
        $results = DB::select($sql, [$lat, $lng, $lat, $limit]);

        $formatted = [];
        foreach ($results as $item) {
            $formatted[] = [
                'id' => $item->id,
                'nama' => $item->nama,
                'alamat' => $item->alamat,
                'latitude' => (float)$item->latitude,
                'longitude' => (float)$item->longitude,
                'distance' => round($item->distance, 2)
            ];
        }

        return $formatted;
    }
}
