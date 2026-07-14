import re

with open('app/Services/SpatialAnalysisService.php', 'r') as f:
    content = f.read()

# Modify analyzeLocation to return lists
new_analyze = """    public function analyzeLocation(float $lat, float $lng): array
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
    }"""

content = re.sub(r"public function analyzeLocation.*?return \[.*?\];\n    \}", new_analyze, content, flags=re.DOTALL)

# Replace countCompetitors with getCompetitors
new_competitors = """    private function getCompetitors(float $lat, float $lng, float $radius): array
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
    }"""

content = re.sub(r"private function countCompetitors.*?return .*?;\n    \}", new_competitors, content, flags=re.DOTALL)

# Replace findNearestRPH with getNearestRPH
new_rph = """    private function getNearestRPH(float $lat, float $lng, int $limit = 5): array
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
    }"""

content = re.sub(r"private function findNearestRPH.*?return .*?;\n    \}", new_rph, content, flags=re.DOTALL)

with open('app/Services/SpatialAnalysisService.php', 'w') as f:
    f.write(content)

print("Updated SpatialAnalysisService")
