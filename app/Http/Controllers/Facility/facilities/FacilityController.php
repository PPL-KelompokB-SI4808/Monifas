<?php

namespace App\Http\Controllers\Facility\Facilities;

use App\Http\Controllers\Controller;
use App\Http\Requests\Facility\Facilities\StoreFacilityRequest;
use App\Http\Requests\Facility\Facilities\UpdateFacilityRequest;
use App\Models\Facility;
use App\Models\Building;
use App\Models\Location;
use App\Models\FacilityCategory;
use App\Services\Facility\Facilities\FacilityService;
use Illuminate\Http\Request;

class FacilityController extends Controller
{
    protected $facilityService;

    public function __construct(FacilityService $facilityService)
    {
        $this->facilityService = $facilityService;
    }

    public function index(Request $request)
    {
        $facilities = $this->facilityService->getFilteredFacilities($request);
        $buildings = Building::all();

        return view('modules.facility.facilities.index', compact('facilities', 'buildings'));
    }

    public function create()
    {
        $locations = Location::all();
        $buildings = Building::all();
        $categories = FacilityCategory::all();
        
        return view('modules.facility.facilities.create', compact('locations', 'buildings', 'categories'));
    }

    public function store(StoreFacilityRequest $request)
    {
        $this->facilityService->createFacility($request->validated());

        return redirect()->route('facility.facilities.index')
                         ->with('success', 'Fasilitas berhasil ditambahkan.');
    }

    public function edit(Facility $facility)
    {
        $locations = Location::all();
        $buildings = Building::all();
        $categories = FacilityCategory::all();

        return view('modules.facility.facilities.edit', compact('facility', 'locations', 'buildings', 'categories'));
    }

    public function update(UpdateFacilityRequest $request, Facility $facility)
    {
        $this->facilityService->updateFacility($facility, $request->validated());

        return redirect()->route('facility.facilities.index')
                         ->with('success', 'Fasilitas berhasil diperbarui.');
    }

    public function destroy(Facility $facility)
    {
        $this->facilityService->deleteFacility($facility);

        return redirect()->route('facility.facilities.index')
                         ->with('success', 'Fasilitas berhasil dihapus.');
    }
}