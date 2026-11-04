<?php

namespace App\Http\Controllers\Repair\materials;

use App\Http\Controllers\Controller;
use App\Http\Requests\Repair\materials\StoreRepairMaterialRequest;
use App\Http\Requests\Repair\materials\UpdateRepairMaterialRequest;
use App\Models\Repair;
use App\Models\RepairMaterial;
use App\Services\Repair\materials\RepairMaterialService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RepairMaterialController extends Controller
{
    public function __construct(
        protected RepairMaterialService $repairMaterialService
    ) {}

    /**
     * Menampilkan seluruh material untuk satu perbaikan.
     */
    public function index(Repair $repair): View
    {
        $materials = $this->repairMaterialService->getByRepair($repair);

        $totalMaterial = $this->repairMaterialService
            ->calculateMaterialTotal($repair);

        return view('pages.dashboard.repair.materials.index', compact(
            'repair',
            'materials',
            'totalMaterial'
        ));
    }

    /**
     * Menampilkan halaman tambah material.
     */
    public function create(Repair $repair): View
    {
        return view('pages.dashboard.repair.materials.create', compact(
            'repair'
        ));
    }

    /**
     * Menyimpan material baru.
     */
    public function store(
        StoreRepairMaterialRequest $request
    ): RedirectResponse {
        $data = $request->validated();
        $repairId = $data['repair_id'];

        $this->repairMaterialService->store($data);

        return redirect()
            ->route('repair.materials.index', $repairId)
            ->with('success', 'Material berhasil ditambahkan.');
    }

    /**
     * Menampilkan halaman edit material.
     */
    public function edit(RepairMaterial $material): View
    {
        return view('pages.dashboard.repair.materials.edit', compact(
            'material'
        ));
    }

    /**
     * Memperbarui material.
     */
    public function update(
        UpdateRepairMaterialRequest $request,
        RepairMaterial $material
    ): RedirectResponse {
        $this->repairMaterialService->update(
            $material,
            $request->validated()
        );

        return redirect()
            ->route('repair.materials.index', $material->repair_id)
            ->with('success', 'Material berhasil diperbarui.');
    }

    /**
     * Menghapus material.
     */
    public function destroy(RepairMaterial $material): RedirectResponse
    {
        $repairId = $material->repair_id;

        $this->repairMaterialService->delete($material);

        return redirect()
            ->route('repair.materials.index', $repairId)
            ->with('success', 'Material berhasil dihapus.');
    }
}
