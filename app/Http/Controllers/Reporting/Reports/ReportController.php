<?php

namespace App\Http\Controllers\Reporting\Reports;

use App\Enums\ReportStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Reporting\Reports\StoreReportRequest;
use App\Http\Requests\Reporting\Reports\UpdateReportRequest;
use App\Http\Requests\Reporting\Reports\VerifyReportRequest;
use App\Models\Building;
use App\Models\DamageCategory;
use App\Models\Facility;
use App\Models\Location;
use App\Models\Report;
use App\Models\ReportPriority;
use App\Models\Room;
use App\Services\Reporting\Reports\ReportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(
        protected ReportService $reportService
    ) {}

    /**
     * Display a listing of reports.
     */
    public function index(Request $request): View
    {
        $reports = $this->reportService->getPaginatedList($request->all(), 10);
        $statuses = ReportStatus::cases();
        $priorities = ReportPriority::orderedByUrgency()->get();
        $locations = Location::query()->where('is_active', true)->orderBy('name')->get();
        $damageCategories = DamageCategory::query()->where('is_active', true)->orderBy('name')->get();

        return view('pages.dashboard.reporting.reports.index', compact(
            'reports',
            'statuses',
            'priorities',
            'locations',
            'damageCategories'
        ));
    }

    /**
     * Show the form for creating a new report.
     */
    public function create(): View
    {
        $locations = Location::query()->where('is_active', true)->orderBy('name')->get();
        $buildings = Building::query()->where('is_active', true)->orderBy('name')->get();
        $rooms = Room::query()->where('is_active', true)->orderBy('name')->get();
        $facilities = Facility::query()->where('is_active', true)->orderBy('name')->get();
        $damageCategories = DamageCategory::query()->where('is_active', true)->orderBy('name')->get();
        $priorities = ReportPriority::query()->where('is_active', true)->orderedByUrgency()->get();

        return view('pages.dashboard.reporting.reports.create', compact(
            'locations',
            'buildings',
            'rooms',
            'facilities',
            'damageCategories',
            'priorities'
        ));
    }

    /**
     * Store a newly created report in storage.
     */
    public function store(StoreReportRequest $request): RedirectResponse
    {
        $dto = $request->toDto();
        $user = $request->user();

        if ($user === null) {
            abort(401);
        }

        $report = $this->reportService->store($dto, $user);

        return redirect()
            ->route('reporting.reports.show', $report)
            ->with('success', "Laporan kerusakan #{$report->ticket_number} berhasil dibuat dan menunggu verifikasi supervisor.");
    }

    /**
     * Display the specified report.
     */
    public function show(Report $report): View
    {
        $report->loadMissing([
            'reporter',
            'location',
            'building',
            'room',
            'facility',
            'damageCategory',
            'priority',
            'verifiedBy',
            'repairs',
        ]);

        $priorities = ReportPriority::query()->where('is_active', true)->orderedByUrgency()->get();

        return view('pages.dashboard.reporting.reports.show', compact('report', 'priorities'));
    }

    /**
     * Show the form for editing the specified report.
     */
    public function edit(Report $report, Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user === null || ($report->reporter_id !== $user->id && ! $user->hasRole(UserRole::Admin))) {
            abort(403, 'Anda tidak memiliki hak untuk mengedit laporan ini.');
        }

        if ($report->status !== ReportStatus::Pending) {
            return redirect()
                ->route('reporting.reports.show', $report)
                ->with('error', 'Laporan yang sudah diverifikasi atau diproses tidak dapat diubah.');
        }

        $locations = Location::query()->where('is_active', true)->orderBy('name')->get();
        $buildings = Building::query()->where('is_active', true)->orderBy('name')->get();
        $rooms = Room::query()->where('is_active', true)->orderBy('name')->get();
        $facilities = Facility::query()->where('is_active', true)->orderBy('name')->get();
        $damageCategories = DamageCategory::query()->where('is_active', true)->orderBy('name')->get();
        $priorities = ReportPriority::query()->where('is_active', true)->orderedByUrgency()->get();

        return view('pages.dashboard.reporting.reports.edit', compact(
            'report',
            'locations',
            'buildings',
            'rooms',
            'facilities',
            'damageCategories',
            'priorities'
        ));
    }

    /**
     * Update the specified report in storage.
     */
    public function update(UpdateReportRequest $request, Report $report): RedirectResponse
    {
        $dto = $request->toDto();
        $this->reportService->update($report, $dto);

        return redirect()
            ->route('reporting.reports.show', $report)
            ->with('success', "Laporan kerusakan #{$report->ticket_number} berhasil diperbarui.");
    }

    /**
     * Remove the specified report from storage.
     */
    public function destroy(Report $report, Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user === null || ($report->reporter_id !== $user->id && ! $user->hasRole(UserRole::Admin))) {
            abort(403, 'Anda tidak memiliki hak untuk menghapus laporan ini.');
        }

        if ($report->status !== ReportStatus::Pending) {
            return redirect()
                ->route('reporting.reports.show', $report)
                ->with('error', 'Hanya laporan dengan status Menunggu Verifikasi (Pending) yang dapat dihapus.');
        }

        $this->reportService->delete($report);

        return redirect()
            ->route('reporting.reports.index')
            ->with('success', "Laporan kerusakan #{$report->ticket_number} berhasil dihapus.");
    }

    /**
     * Supervisor verification handler (Approve or Reject).
     */
    public function verify(VerifyReportRequest $request, Report $report): RedirectResponse
    {
        $user = $request->user();

        if ($user === null) {
            abort(401);
        }

        $action = (string) $request->input('action');
        $rejectionReason = $request->rejectionReason();
        $adjustedPriorityId = $request->has('priority_id') && $request->input('priority_id') !== ''
            ? (int) $request->input('priority_id')
            : null;

        $this->reportService->handleVerification(
            $report,
            $user,
            $action,
            $rejectionReason,
            $adjustedPriorityId
        );

        $message = $action === 'reject'
            ? "Laporan kerusakan #{$report->ticket_number} telah ditolak."
            : "Laporan kerusakan #{$report->ticket_number} berhasil diverifikasi.";

        return redirect()
            ->route('reporting.reports.show', $report)
            ->with('success', $message);
    }
}
