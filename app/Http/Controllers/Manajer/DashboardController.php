<?php

namespace App\Http\Controllers\Manajer;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        private DashboardService $dashboardService
    ) {}

    public function index(Request $request)
    {
        $selectedPeriodeId = $request->input('periode_id') ? (int) $request->input('periode_id') : null;
        $data = $this->dashboardService->getDashboardData($selectedPeriodeId);

        return view('dashboard', $data);
    }
}
