<?php

namespace App\Http\Controllers\Maintenance\Officers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Maintenance\Officers\StoreOfficerRequest;
use App\Http\Requests\Maintenance\Officers\UpdateOfficerRequest;
use App\Models\MaintenanceOfficer;
use App\Services\Maintenance\Officers\OfficerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class OfficerController extends Controller implements HasMiddleware
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
        protected OfficerService $officerService,
    ) {}

    /**
     * Display a listing of maintenance officers.
     */
    public function index(): View
    {
        $officers = $this->officerService->paginateOfficers();

        return view('pages.dashboard.maintenance.officers.index', compact('officers'));
    }

    /**
     * Show the form for creating a maintenance officer.
     */
    public function create(): View
    {
        return view('pages.dashboard.maintenance.officers.create');
    }

    /**
     * Store a newly created maintenance officer.
     */
    public function store(StoreOfficerRequest $request): RedirectResponse
    {
        $this->officerService->createOfficer($request->validated());

        return redirect()
            ->route('maintenance.officers.index')
            ->with('status', 'Petugas pemeliharaan berhasil ditambahkan.');
    }

    /**
     * Show the form for editing a maintenance officer.
     */
    public function edit(MaintenanceOfficer $officer): View
    {
        return view('pages.dashboard.maintenance.officers.edit', compact('officer'));
    }

    /**
     * Update the specified maintenance officer.
     */
    public function update(UpdateOfficerRequest $request, MaintenanceOfficer $officer): RedirectResponse
    {
        $this->officerService->updateOfficer($officer, $request->validated());

        return redirect()
            ->route('maintenance.officers.index')
            ->with('status', 'Data petugas pemeliharaan berhasil diperbarui.');
    }

    /**
     * Remove the specified maintenance officer.
     */
    public function destroy(MaintenanceOfficer $officer): RedirectResponse
    {
        if (! $this->officerService->deleteOfficer($officer)) {
            return redirect()
                ->route('maintenance.officers.index')
                ->withErrors(['error' => 'Petugas pemeliharaan gagal dihapus.']);
        }

        return redirect()
            ->route('maintenance.officers.index')
            ->with('status', 'Petugas pemeliharaan berhasil dihapus.');
    }

    /**
     * Toggle the specified maintenance officer's availability.
     */
    public function toggleAvailability(MaintenanceOfficer $officer): RedirectResponse
    {
        if (! $this->officerService->toggleAvailability($officer)) {
            return back()->withErrors(['error' => 'Status ketersediaan petugas gagal diubah.']);
        }

        return back()->with('status', 'Status ketersediaan petugas berhasil diubah.');
    }
}
