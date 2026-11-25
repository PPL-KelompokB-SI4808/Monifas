<?php

namespace App\Http\Controllers\MonitoringAndAnalytic\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\MonitoringAndAnalytic\Dashboard\DashboardFilterRequest;
use App\Services\MonitoringAndAnalytic\Dashboard\DashboardService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService
    ) {}

    /**
     * Display the operational monitoring and analytics dashboard.
     */
    public function index(DashboardFilterRequest $request): View
    {
        $filters = $request->validated();
        $dashboardData = $this->dashboardService->getDashboardData($filters);
        $filterOptions = $this->dashboardService->getFilterOptions();

        return view('pages.dashboard.monitoring-and-analytic.dashboard.index', [
            'filters' => $filters,
            'kpi' => $dashboardData['kpi'],
            'trends' => $dashboardData['trends'],
            'technicianPerformance' => $dashboardData['technician_performance'],
            'filterOptions' => $filterOptions,
        ]);
    }
}
