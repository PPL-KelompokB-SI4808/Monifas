<?php

namespace App\Http\Controllers\MonitoringAndAnalytic\MaintenanceReport;

use App\Http\Controllers\Controller;
use App\Http\Requests\MonitoringAndAnalytic\MaintenanceReport\MaintenanceReportFilterRequest;
use App\Services\MonitoringAndAnalytic\MaintenanceReport\MaintenanceReportService;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MaintenanceReportController extends Controller
{
    public function __construct(
        protected MaintenanceReportService $maintenanceReportService
    ) {}

    /**
     * Display the maintenance history, damage frequency, cost, and performance report.
     */
    public function index(MaintenanceReportFilterRequest $request): View
    {
        $filters = $request->validated();
        $reportData = $this->maintenanceReportService->getReportData($filters);
        $filterOptions = $this->maintenanceReportService->getFilterOptions();

        return view('pages.dashboard.monitoring-and-analytic.maintenance-report.index', [
            'filters' => $filters,
            'history' => $reportData['history'],
            'summary' => $reportData['summary'],
            'damageFrequency' => $reportData['damage_frequency'],
            'costAnalysis' => $reportData['cost_analysis'],
            'preventiveVsReactive' => $reportData['preventive_vs_reactive'],
            'filterOptions' => $filterOptions,
        ]);
    }

    /**
     * Export the filtered maintenance report as CSV (Excel-compatible) or printable view.
     */
    public function export(MaintenanceReportFilterRequest $request): StreamedResponse|View
    {
        $filters = $request->validated();

        if (($filters['export'] ?? 'csv') === 'print') {
            $printable = $this->maintenanceReportService->getPrintableData($filters);

            return view('pages.dashboard.monitoring-and-analytic.maintenance-report.print', [
                'filters' => $filters,
                'records' => $printable['records'],
                'summary' => $printable['summary'],
                'damageFrequency' => $printable['damage_frequency'],
                'costAnalysis' => $printable['cost_analysis'],
                'preventiveVsReactive' => $printable['preventive_vs_reactive'],
                'generatedAt' => $printable['generated_at'],
            ]);
        }

        return $this->maintenanceReportService->exportCsv($filters);
    }
}
