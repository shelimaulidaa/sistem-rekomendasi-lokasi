<?php

namespace App\Http\Controllers;

use App\Models\Province;
use App\Models\Regency;
use App\Models\District;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class WilayahController extends Controller
{
    public function provinces(): JsonResponse
    {
        $provinces = Province::orderBy('name', 'asc')->get(['id', 'name']);
        return response()->json($provinces);
    }

    public function regencies($province_id): JsonResponse
    {
        $regencies = Regency::where('province_id', $province_id)
            ->orderBy('name', 'asc')
            ->get(['id', 'name']);
        return response()->json($regencies);
    }

    public function districts($regency_id): JsonResponse
    {
        $districts = District::where('regency_id', $regency_id)
            ->orderBy('name', 'asc')
            ->get(['id', 'name']);
        return response()->json($districts);
    }

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
