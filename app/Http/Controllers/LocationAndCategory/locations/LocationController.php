<?php

namespace App\Http\Controllers\LocationAndCategory\Locations;

use App\Http\Controllers\Controller;
use App\Http\Requests\LocationAndCategory\Locations\StoreLocationRequest;
use App\Http\Requests\LocationAndCategory\Locations\UpdateLocationRequest;
use App\Models\Location;
use App\Services\LocationAndCategory\Locations\LocationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LocationController extends Controller
{
    public function __construct(
        protected LocationService $locationService
    ) {}

    /**
     * Display a listing of locations.
     */
    public function index(Request $request): View
    {
        $locations = $this->locationService->getPaginatedList($request->all(), 10);
        $parents = $this->locationService->getParentCandidates(null, false);
        $regions = $this->locationService->getUniqueRegions();

        return view('pages.dashboard.location-and-category.locations.index', compact('locations', 'parents', 'regions'));
    }

    /**
     * Show the form for creating a new location.
     */
    public function create(): View
    {
        $parents = $this->locationService->getParentCandidates(null, true);
        $regions = $this->locationService->getUniqueRegions();

        return view('pages.dashboard.location-and-category.locations.create', compact('parents', 'regions'));
    }

    /**
     * Store a newly created location in storage.
     */
    public function store(StoreLocationRequest $request): RedirectResponse
    {
        $dto = $request->toDto();
        $this->locationService->store($dto);

        return redirect()
            ->route('location-and-category.locations.index')
            ->with('success', 'Lokasi berhasil ditambahkan.');
    }

    /**
     * Display the specified location.
     */
    public function show(Location $location): View
    {
        $location = $this->locationService->findById($location->id) ?? $location;

        return view('pages.dashboard.location-and-category.locations.show', compact('location'));
    }

    /**
     * Show the form for editing the specified location.
     */
    public function edit(Location $location): View
    {
        $parents = $this->locationService->getParentCandidates($location, true);
        $regions = $this->locationService->getUniqueRegions();

        return view('pages.dashboard.location-and-category.locations.edit', compact('location', 'parents', 'regions'));
    }

    /**
     * Update the specified location in storage.
     */
    public function update(UpdateLocationRequest $request, Location $location): RedirectResponse
    {
        $dto = $request->toDto();
        $this->locationService->update($location, $dto);

        return redirect()
            ->route('location-and-category.locations.index')
            ->with('success', 'Lokasi berhasil diperbarui.');
    }

    /**
     * Remove or safely deactivate the specified location.
     */
    public function destroy(Location $location): RedirectResponse
    {
        $hasDependencies = $location->children()->exists() || $location->buildings()->exists() || $location->facilities()->exists();
        $this->locationService->delete($location);

        $message = $hasDependencies
            ? 'Lokasi dinonaktifkan karena memiliki sub-lokasi, gedung, atau fasilitas terkait.'
            : 'Lokasi berhasil dihapus.';

        return redirect()
            ->route('location-and-category.locations.index')
            ->with('success', $message);
    }

    /**
     * Toggle active status of a location.
     */
    public function toggleStatus(Location $location): RedirectResponse
    {
        $this->locationService->toggleStatus($location);

        return back()->with('success', 'Status keaktifan lokasi berhasil diubah.');
    }
}
