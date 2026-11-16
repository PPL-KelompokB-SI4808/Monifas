<?php

namespace App\Http\Controllers\Maintenance\Assignments;

use App\Enums\AssignmentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Maintenance\Assignments\CancelAssignmentRequest;
use App\Http\Requests\Maintenance\Assignments\ReassignAssignmentRequest;
use App\Http\Requests\Maintenance\Assignments\StoreAssignmentRequest;
use App\Models\Assignment;
use App\Models\Report;
use App\Models\User;
use App\Services\Maintenance\Assignments\AssignmentService;
use App\Services\Maintenance\Officers\OfficerService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class AssignmentController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware('role:supervisor'),
        ];
    }

    public function __construct(
        protected AssignmentService $assignmentService,
        protected OfficerService $officerService,
    ) {}

    public function index(Request $request): View
    {
        $assignments = $this->assignmentService->paginateAssignments([
            'status' => $request->query('status'),
            'officer_id' => $request->query('officer_id'),
        ]);
        $officers = $this->officerService->getAllOfficers();
        $statuses = AssignmentStatus::cases();

        return view('pages.dashboard.maintenance.assignments.index', compact('assignments', 'officers', 'statuses'));
    }

    public function create(Report $report): View|RedirectResponse
    {
        if (! $report->status->canBeAssigned()) {
            return redirect()
                ->route('maintenance.assignments.index')
                ->withErrors(['error' => 'Laporan ini belum diverifikasi dan tidak dapat ditugaskan.']);
        }

        $officers = $this->assignmentService->recommendOfficersForReport($report);

        return view('pages.dashboard.maintenance.assignments.create', compact('report', 'officers'));
    }

    public function store(StoreAssignmentRequest $request): RedirectResponse
    {
        $assignedBy = $request->user();
        if (! $assignedBy instanceof User) {
            abort(401);
        }

        $instructionNotes = $request->validated('instruction_notes');

        try {
            $this->assignmentService->assignReport(
                report: $request->integer('report_id'),
                officer: $request->integer('officer_id'),
                assignedBy: $assignedBy,
                instructionNotes: is_string($instructionNotes) ? $instructionNotes : null,
            );
        } catch (Exception $exception) {
            return back()
                ->withInput()
                ->withErrors(['error' => $exception->getMessage()]);
        }

        return redirect()
            ->route('maintenance.assignments.index')
            ->with('status', 'Laporan berhasil ditugaskan kepada petugas.');
    }

    public function show(Assignment $assignment): View
    {
        $assignment->load(['report', 'officer.user', 'assignedBy']);

        return view('pages.dashboard.maintenance.assignments.show', compact('assignment'));
    }

    public function reassign(ReassignAssignmentRequest $request, Assignment $assignment): RedirectResponse
    {
        $reassignedBy = $request->user();
        if (! $reassignedBy instanceof User) {
            abort(401);
        }

        try {
            $this->assignmentService->reassignAssignment(
                assignment: $assignment,
                newOfficer: $request->integer('officer_id'),
                reason: (string) $request->validated('reason'),
                reassignedBy: $reassignedBy,
            );
        } catch (Exception $exception) {
            return back()
                ->withInput()
                ->withErrors(['error' => $exception->getMessage()]);
        }

        return redirect()
            ->route('maintenance.assignments.index')
            ->with('status', 'Penugasan berhasil dialihkan kepada petugas baru.');
    }

    public function cancel(CancelAssignmentRequest $request, Assignment $assignment): RedirectResponse
    {
        $cancelledBy = $request->user();
        if (! $cancelledBy instanceof User) {
            abort(401);
        }

        try {
            $this->assignmentService->cancelAssignment(
                assignment: $assignment,
                reason: (string) $request->validated('reason'),
                cancelledBy: $cancelledBy,
            );
        } catch (Exception $exception) {
            return back()
                ->withInput()
                ->withErrors(['error' => $exception->getMessage()]);
        }

        return redirect()
            ->route('maintenance.assignments.index')
            ->with('status', 'Penugasan berhasil dibatalkan.');
    }
}
