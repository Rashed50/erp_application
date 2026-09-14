<?php

namespace Modules\Dashboard\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Dashboard\Services\LiveDashboardService;

class LiveDashboardController extends Controller
{
    protected LiveDashboardService $liveDashboardService;

    public function __construct(LiveDashboardService $liveDashboardService)
    {
        $this->liveDashboardService = $liveDashboardService;
    }

    /**
     * Render the live dashboard Blade view.
     */
    public function index()
    {
        $getBanner = collect([(object)['company_logo' => 'images/company_logo.png']]);
        return view('dashboard::livedashboardcontainer', compact('getBanner'));
    }

    // ─────────────────────────────────────────────────────────────────
    // Dedicated per-view API endpoints
    // Each returns: { success: bool, data: {}, timestamp: ISO-8601 }
    // ─────────────────────────────────────────────────────────────────

    /**
     * GET /live-dashboard/attendance-data
     *
     * Returns today's present-employee summary for all running projects.
     */
    public function getAttendanceData(Request $request): JsonResponse
    {
        $raw = $this->liveDashboardService->getComprehensiveDashboardData(null, 'attendance');

        return response()->json([
            'success'   => true,
            'data'      => $raw['live_data'] ?? [],
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * GET /live-dashboard/salary-data
     *
     * Returns current-month daily salary data for all running projects.
     */
    public function getSalaryData(Request $request): JsonResponse
    {
        $raw = $this->liveDashboardService->getComprehensiveDashboardData(null, 'salary');

        return response()->json([
            'success'   => true,
            'data'      => $raw['live_data'] ?? [],
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * GET /live-dashboard/comprehensive-data
     *
     * Returns previous-3-months subcontractor, employee, and chart data.
     *
     * @param Request $request  Optional: ?selected_date=YYYY-MM-DD
     */
    public function getComprehensiveData(Request $request): JsonResponse
    {
        $selectedDate = $request->input('selected_date');
        $raw          = $this->liveDashboardService->getComprehensiveDashboardData($selectedDate, 'comprehensive');

        return response()->json([
            'success'   => true,
            'data'      => [
                'subcontractor_data' => $raw['subcontractor_data'] ?? [],
                'employee_data'      => $raw['employee_data']      ?? [],
                'salary_data'        => $raw['salary_data']        ?? [],
                'man_hours'          => $raw['man_hours']           ?? [],
            ],
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
