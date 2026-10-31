<?php

namespace App\Http\Controllers\Facility\Buildings;

use App\DTOs\Facility\BuildingData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Facility\Buildings\StoreBuildingRequest;
use App\Http\Requests\Facility\Buildings\UpdateBuildingRequest;
use App\Models\AreaZone;
use App\Models\Building;
use App\Models\Location;
use App\Services\Facility\Buildings\BuildingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BuildingController extends Controller
{
    public function __construct(protected BuildingService $buildingService) {}

    public function index(): View
    {
        $buildings = $this->buildingService->getPaginatedList(request()->all());
        $locations = Location::all();

        return view('pages.dashboard.facility.buildings.index', compact('buildings', 'locations'));
    }

    public function create(): View
    {
        $locations = Location::all();
        $areaZones = AreaZone::all();

        return view('pages.dashboard.facility.buildings.create', compact('locations', 'areaZones'));
    }

    public function store(StoreBuildingRequest $request): RedirectResponse
    {
        $dto = BuildingData::fromRequest($request);
        $this->buildingService->store($dto);

        return redirect()->route('facility.buildings.index')->with('success', 'Gedung berhasil ditambahkan.');
    }

    public function show(Building $building): View
    {
        return view('pages.dashboard.facility.buildings.show', compact('building'));
    }

    public function edit(Building $building): View
    {
        $locations = Location::all();
        $areaZones = AreaZone::all();

        return view('pages.dashboard.facility.buildings.edit', compact('building', 'locations', 'areaZones'));
    }

    public function update(UpdateBuildingRequest $request, Building $building): RedirectResponse
    {
        $dto = BuildingData::fromRequest($request);
        $this->buildingService->update($building, $dto);

        return redirect()->route('facility.buildings.index')->with('success', 'Gedung berhasil diperbarui.');
    }

    public function destroy(Building $building): RedirectResponse
    {
        $this->buildingService->delete($building);

        return redirect()->route('facility.buildings.index')->with('success', 'Gedung berhasil dihapus atau dinonaktifkan.');
    }
}
