<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\KriteriaService;
use App\Models\Kriteria;
use Symfony\Component\HttpFoundation\Response;

class EnsureKriteriaIsManageable
{
    public function handle(Request $request, Closure $next): Response
    {
        $kriteriaService = app(KriteriaService::class);

        // 1. Coba ambil periode_id dari input request
        $periodeId = $request->input('periode_id');

        // 2. Jika tidak ada di input, coba ambil dari parameter route 'kriteria'
        if (!$periodeId && $request->route('kriteria')) {
            $kriteria = $request->route('kriteria');
            if (is_string($kriteria) || is_numeric($kriteria)) {
                $kriteriaModel = Kriteria::find($kriteria);
                $periodeId = $kriteriaModel?->periode_id;
            } elseif ($kriteria instanceof Kriteria) {
                $periodeId = $kriteria->periode_id;
            }
        }

        if (!$kriteriaService->canManageKriteria($periodeId)) {
            $reason = $kriteriaService->getRestrictionReason($periodeId);

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => $reason,
                    'error' => 'Kriteria management disabled for this period.'
                ], Response::HTTP_FORBIDDEN);
            }

            if ($request->isMethod('get')) {
                return redirect()->route('manajer.kriteria.index', ['periode_id' => $periodeId])
                    ->with('error', $reason);
            }

            return redirect()->back()->with('error', $reason);
        }

        return $next($request);
    }
}
