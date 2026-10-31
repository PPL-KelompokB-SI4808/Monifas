<?php

namespace App\Http\Controllers\Repair\repairs;

use App\Http\Controllers\Controller;
use App\Http\Requests\Repair\Repairs\StoreRepairRequest;
use App\Http\Requests\Repair\Repairs\UpdateRepairRequest;
use App\Models\Repair;
use App\Services\Repair\repairs\RepairService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RepairController extends Controller
{
    public function __construct(
        private readonly RepairService $repairService
    ) {}

    public function index(Request $request): View
    {
        $repairs = $this->repairService->history(
            $request->only([
                'facility_id',
                'officer_id',
                'location_id',
                'started_from',
                'started_until',
            ])
        );

        return view(
            'pages.dashboard.repair.repairs.index',
            compact('repairs')
        );
    }

    public function create(): View
    {
        return view('pages.dashboard.repair.repairs.create');
    }

    public function store(StoreRepairRequest $request): RedirectResponse
    {
        $repair = $this->repairService->create($request->validated());

        return redirect()
            ->route('repair.repairs.show', $repair)
            ->with('success', 'Data perbaikan berhasil ditambahkan.');
    }

    public function show(Repair $repair): View
    {
        $repair->load([
            'assignment',
            'report',
            'facility',
            'officer',
            'materials',
            'cost',
            'feedback',
        ]);

        return view(
            'pages.dashboard.repair.repairs.show',
            compact('repair')
        );
    }

    public function edit(Repair $repair): View
    {
        return view(
            'pages.dashboard.repair.repairs.edit',
            compact('repair')
        );
    }

    public function update(
        UpdateRepairRequest $request,
        Repair $repair
    ): RedirectResponse {
        $this->repairService->update(
            $repair,
            $request->validated()
        );

        return redirect()
            ->route('repair.repairs.show', $repair)
            ->with('success', 'Data perbaikan berhasil diperbarui.');
    }

    public function destroy(Repair $repair): RedirectResponse
    {
        $repair->delete();

        return redirect()
            ->route('repair.repairs.index')
            ->with('success', 'Data perbaikan berhasil dihapus.');
    }
}
