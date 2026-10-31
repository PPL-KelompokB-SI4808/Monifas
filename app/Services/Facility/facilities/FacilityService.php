<?php

namespace App\Services\Facility\Facilities;

use App\Models\Facility;
use Illuminate\Http\Request;

class FacilityService
{
    public function getFilteredFacilities(Request $request)
    {
        return Facility::with(['location', 'building', 'room', 'category'])
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%");
            })
            ->when($request->condition, function ($query, $condition) {
                $query->where('condition', $condition);
            })
            ->when($request->building_id, function ($query, $buildingId) {
                $query->where('building_id', $buildingId);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();
    }

    public function createFacility(array $data): Facility
    {
        return Facility::create($data);
    }

    public function updateFacility(Facility $facility, array $data): bool
    {
        return $facility->update($data);
    }

    public function deleteFacility(Facility $facility): ?bool
    {
        return $facility->delete();
    }
}
