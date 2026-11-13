<?php

namespace App\Http\Controllers\Reporting\ReportPriorities;

use App\Enums\PriorityLevel;
use App\Http\Controllers\Controller;
use App\Http\Requests\Reporting\ReportPriorities\StoreReportPriorityRequest;
use App\Http\Requests\Reporting\ReportPriorities\UpdateReportPriorityRequest;
use App\Models\ReportPriority;
use App\Services\Reporting\ReportPriorities\ReportPriorityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportPriorityController extends Controller
{
    public function __construct(
        protected ReportPriorityService $priorityService
    ) {}

    /**
     * Display a listing of report priorities.
     */
    public function index(Request $request): View
    {
        $priorities = $this->priorityService->getPaginatedList($request->all(), 10);
        $summaryPriorities = ReportPriority::orderedByUrgency()->get();
        $levels = PriorityLevel::cases();

        return view('pages.dashboard.reporting.report-priorities.index', compact('priorities', 'summaryPriorities', 'levels'));
    }

    /**
     * Show the form for creating a new report priority.
     */
    public function create(): View
    {
        $levels = PriorityLevel::cases();
        $badgeVariants = [
            'danger' => 'Merah (Danger/Darurat)',
            'warning' => 'Kuning (Warning/Tinggi)',
            'info' => 'Biru (Info/Sedang)',
            'secondary' => 'Abu-abu (Secondary/Rendah)',
            'emerald' => 'Hijau (Success/Normal)',
        ];

        return view('pages.dashboard.reporting.report-priorities.create', compact('levels', 'badgeVariants'));
    }

    /**
     * Store a newly created report priority in storage.
     */
    public function store(StoreReportPriorityRequest $request): RedirectResponse
    {
        $dto = $request->toDto();
        $this->priorityService->store($dto);

        return redirect()
            ->route('reporting.report-priorities.index')
            ->with('success', 'Master prioritas laporan berhasil ditambahkan.');
    }

    /**
     * Display the specified report priority.
     */
    public function show(ReportPriority $reportPriority): View
    {
        return view('pages.dashboard.reporting.report-priorities.show', compact('reportPriority'));
    }

    /**
     * Show the form for editing the specified report priority.
     */
    public function edit(ReportPriority $reportPriority): View
    {
        $levels = PriorityLevel::cases();
        $badgeVariants = [
            'danger' => 'Merah (Danger/Darurat)',
            'warning' => 'Kuning (Warning/Tinggi)',
            'info' => 'Biru (Info/Sedang)',
            'secondary' => 'Abu-abu (Secondary/Rendah)',
            'emerald' => 'Hijau (Success/Normal)',
        ];

        return view('pages.dashboard.reporting.report-priorities.edit', compact('reportPriority', 'levels', 'badgeVariants'));
    }

    /**
     * Update the specified report priority in storage.
     */
    public function update(UpdateReportPriorityRequest $request, ReportPriority $reportPriority): RedirectResponse
    {
        $dto = $request->toDto();
        $this->priorityService->update($reportPriority, $dto);

        return redirect()
            ->route('reporting.report-priorities.index')
            ->with('success', 'Data prioritas laporan berhasil diperbarui.');
    }

    /**
     * Remove the specified report priority from storage.
     */
    public function destroy(ReportPriority $reportPriority): RedirectResponse
    {
        $this->priorityService->delete($reportPriority);

        return redirect()
            ->route('reporting.report-priorities.index')
            ->with('success', 'Prioritas laporan berhasil dihapus.');
    }

    /**
     * Toggle the active status of a report priority.
     */
    public function toggleStatus(ReportPriority $reportPriority): RedirectResponse
    {
        $this->priorityService->toggleStatus($reportPriority);

        return back()->with('success', 'Status aktif prioritas berhasil diperbarui.');
    }

    /**
     * Synchronize and automatically reorder all priorities according to standard urgency:
     * Emergency (1) -> High (2) -> Medium (3) -> Low (4).
     */
    public function syncOrder(): RedirectResponse
    {
        $this->priorityService->syncStandardUrgencyOrder();

        return redirect()
            ->route('reporting.report-priorities.index')
            ->with('success', 'Urutan prioritas laporan berhasil diselaraskan otomatis (Darurat -> Tinggi -> Sedang -> Rendah).');
    }
}
