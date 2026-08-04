<?php

namespace App\Services;

use App\Models\Periode;
use App\Models\HasilPerhitungan;
use App\Models\Kriteria;
use App\Models\ObservasiLokasi;

class DashboardService
{
    /**
     * Get aggregated data for dashboard view including statistics, chart data, and map location markers.
     * 
     * @param int|null $selectedBatchId
     * @return array
     */
    public function getDashboardData(?int $selectedBatchId = null): array
    {
        $calculatedBatchIds = HasilPerhitungan::getCalculatedPeriodeIds();
        $batches = Periode::whereIn('id', $calculatedBatchIds)
            ->where('status', Periode::STATUS_SELESAI)
            ->orderBy('created_at', 'desc')
            ->get();

        $chosenBatch = null;
        if ($selectedBatchId) {
            $chosenBatch = $batches->firstWhere('id', $selectedBatchId);
        }

        if (!$chosenBatch) {
            $chosenBatch = $batches->first();
        }

        $activeBatchId = $chosenBatch?->id;

        $totalObservasi = $activeBatchId ? ObservasiLokasi::wherePeriode($activeBatchId)->count() : 0;
        $kriteriaQuery = Kriteria::query()->when($activeBatchId, fn($q) => $q->where('periode_id', $activeBatchId), fn($q) => $q->whereNull('periode_id'));

        $totalKriteria = (clone $kriteriaQuery)->count();
        $kriteriaBenefit = (clone $kriteriaQuery)->where('atribut', 'benefit')->count();
        $kriteriaCost = (clone $kriteriaQuery)->where('atribut', 'cost')->count();

        $topRanking = $activeBatchId 
            ? HasilPerhitungan::wherePeriode($activeBatchId)
                ->with('penilaian.observasiLokasi')
                ->orderBy('nilai_preferensi', 'desc')
                ->get()
            : collect();

        $lokasiTerbaik = $topRanking->first();

        // Prepare data for Chart.js (Top 5 Ranking)
        $chartLabels = [];
        $chartData = [];
        foreach ($topRanking->take(5) as $rank) {
            $alamat = $rank->penilaian->observasiLokasi->alamat_lengkap ?? 'Unknown';
            $chartLabels[] = strlen($alamat) > 20 ? substr($alamat, 0, 17) . '...' : $alamat;
            $chartData[] = round($rank->nilai_preferensi, 4);
        }

        // Build Map Locations Data
        $rankingMap = [];
        foreach ($topRanking as $index => $rankItem) {
            $obsId = $rankItem->penilaian->observasi_lokasi_id ?? null;
            if ($obsId) {
                $rankingMap[$obsId] = [
                    'rank' => $index + 1,
                    'nilai_preferensi' => rtrim(rtrim(number_format((float)$rankItem->nilai_preferensi, 4, '.', ''), '0'), '.'),
                    'category' => ($index === 0) ? 'terbaik' : (($index <= 2) ? 'sedang' : 'kurang'),
                ];
            }
        }

        $allObservasis = $activeBatchId
            ? ObservasiLokasi::wherePeriode($activeBatchId)->get()
            : collect();

        $mapLocations = [];

        foreach ($allObservasis as $obs) {
            if (is_numeric($obs->latitude) && is_numeric($obs->longitude) && (float)$obs->latitude != 0 && (float)$obs->longitude != 0) {
                $lat = (float) $obs->latitude;
                $lng = (float) $obs->longitude;

                $rankInfo = $rankingMap[$obs->id] ?? [
                    'rank' => '-',
                    'nilai_preferensi' => '-',
                    'category' => 'unranked',
                ];

                $spatial = $obs->spatial_data;

                $rphName = $spatial['nearest_rph_name'] ?? 'RPH Terdekat';
                $rphJarak = $spatial['nearest_rph_distance'] ?? null;
                $rphData = [
                    'id' => $obs->id,
                    'nama' => $rphName,
                    'jarak' => $rphJarak,
                    'alamat' => $rphJarak ? "Jarak: {$rphJarak} KM" : '-',
                    'lat' => $lat + 0.005,
                    'lng' => $lng + 0.005,
                ];

                $competitorsData = [];
                foreach ($spatial['competitors_list'] as $comp) {
                    $competitorsData[] = [
                        'id' => $comp['id'] ?? null,
                        'nama' => $comp['nama'],
                        'alamat' => $comp['alamat'] ?? ($comp['distance'] ? "Jarak: {$comp['distance']} KM" : '-'),
                        'rating' => isset($comp['rating']) && $comp['rating'] ? (float)$comp['rating'] : null,
                        'distance' => $comp['distance'] ?? null,
                        'lat' => !empty($comp['latitude']) ? (float)$comp['latitude'] : $lat - 0.003,
                        'lng' => !empty($comp['longitude']) ? (float)$comp['longitude'] : $lng + 0.003,
                    ];
                }

                $compCount = $spatial['competitor_count'];

                $mapLocations[] = [
                    'id' => $obs->id,
                    'nama_lokasi' => $obs->alamat_lengkap ?: 'Lokasi Observasi',
                    'nama_pemilik' => $obs->nama_pemilik ?: 'Lokasi Observasi',
                    'alamat' => $obs->alamat_lengkap ?: '-',
                    'lat' => $lat,
                    'lng' => $lng,
                    'rank' => $rankInfo['rank'],
                    'nilai_preferensi' => $rankInfo['nilai_preferensi'],
                    'category' => $rankInfo['category'],
                    'rph' => $rphData,
                    'jumlah_kompetitor' => $compCount,
                    'competitors' => $competitorsData,
                ];
            }
        }

        return [
            'batches' => $batches,
            'chosenBatch' => $chosenBatch,
            'totalObservasi' => $totalObservasi,
            'totalKriteria' => $totalKriteria,
            'kriteriaBenefit' => $kriteriaBenefit,
            'kriteriaCost' => $kriteriaCost,
            'lokasiTerbaik' => $lokasiTerbaik,
            'chartLabels' => $chartLabels,
            'chartData' => $chartData,
            'mapLocations' => $mapLocations,
        ];
    }
}
