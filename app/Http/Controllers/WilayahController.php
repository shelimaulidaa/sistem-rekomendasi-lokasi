<?php

namespace App\Http\Controllers;

use App\Models\StatistikJabar;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;


class WilayahController extends Controller
{
    public function jabarStats(Request $request): JsonResponse
    {
        $regencyName = $request->query('regency_name');
        if (!$regencyName) {
            return response()->json(null);
        }

        // Normalize requested name
        $searchName = strtoupper(trim(str_replace(['KABUPATEN ', 'KOTA '], '', $regencyName)));

        // Search in database
        $stats = \App\Models\StatistikJabar::get();
        
        $data = null;
        foreach ($stats as $stat) {
            $rowKabKota = strtoupper(trim(str_replace(['KABUPATEN ', 'KOTA '], '', $stat->kabupaten_kota)));
            if (str_contains($searchName, $rowKabKota) || str_contains($rowKabKota, $searchName)) {
                $data = [
                    'umk' => $stat->umk,
                    'pdrb_per_capita' => $stat->pdrb_per_capita,
                    'jumlah_penduduk_muslim' => $stat->jumlah_penduduk_muslim
                ];
                break;
            }
        }

        return response()->json($data);
    }
}
