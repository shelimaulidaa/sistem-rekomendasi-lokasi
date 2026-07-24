<?php

namespace App\Http\Controllers\Direktur;

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
        $selectedBatchId = $request->input('batch_id') ? (int) $request->input('batch_id') : null;
        $data = $this->dashboardService->getDashboardData($selectedBatchId);

        return view('direktur.dashboard', $data);
    }
}
