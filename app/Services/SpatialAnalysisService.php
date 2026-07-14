<?php

namespace App\Services;

use App\Models\Competitor;
use App\Models\Slaughterhouse;
use Illuminate\Support\Facades\DB;

class SpatialAnalysisService
{
    /**
     * Analyze a location for competitors and nearest slaughterhouse
     * 
     * @param float $lat
     * @param float $lng
     * @return array
     */
        public function analyzeLocation(float $lat, float $lng): array
    {
        $radius = config('spatial.competitor_radius', 5);
        $rphList = $this->getNearestRPH($lat, $lng, 5); // get top 5
        $competitorsList = $this->getCompetitors($lat, $lng, $radius);
        
        $nearestRPH = $rphList[0] ?? null;
        
        return [
            'competitor_count' => count($competitorsList),
            'search_radius' => $radius,
            'nearest_rph_id' => $nearestRPH['id'] ?? null,
            'nearest_rph_name' => $nearestRPH['nama'] ?? null,
            'nearest_rph_distance' => $nearestRPH['distance'] ?? null,
            'competitors_list' => $competitorsList,
            'rph_list' => $rphList
        ];
    }

    /**
     * Count competitors within a given radius using Haversine formula
     * 
     * @param float $lat
     * @param float $lng
     * @param float $radius In kilometers
     * @return int
     */
        private function getCompetitors(float $lat, float $lng, float $radius): array
    {
        $haversine = "(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude))))";

        $sql = "SELECT id, nama, alamat, rating, {$haversine} as distance FROM competitors HAVING distance <= ? ORDER BY distance ASC";
        $results = DB::select($sql, [$lat, $lng, $lat, $radius]);
        
        // Format to array and round distance
        $formatted = [];
        foreach ($results as $item) {
            $formatted[] = [
                'id' => $item->id,
                'nama' => $item->nama,
                'alamat' => $item->alamat,
                'rating' => $item->rating,
                'distance' => round($item->distance, 2)
            ];
        }
        return $formatted;
    }

    /**
     * Find the nearest slaughterhouse (RPH) using Haversine formula
     * 
     * @param float $lat
     * @param float $lng
     * @return array|null
     */
        private function getNearestRPH(float $lat, float $lng, int $limit = 5): array
    {
        $haversine = "(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude))))";

        $sql = "SELECT id, nama, alamat, {$haversine} AS distance FROM slaughterhouses ORDER BY distance ASC LIMIT ?";
        $results = DB::select($sql, [$lat, $lng, $lat, $limit]);

        $formatted = [];
        foreach ($results as $item) {
            $formatted[] = [
                'id' => $item->id,
                'nama' => $item->nama,
                'alamat' => $item->alamat,
                'distance' => round($item->distance, 2)
            ];
        }

        return $formatted;
    }
}
