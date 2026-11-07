<?php

namespace App\Http\Controllers\LocationAndCategory\FacilityCategories;

use App\Enums\CategoryGroupType;
use App\Http\Controllers\Controller;
use App\Http\Requests\LocationAndCategory\FacilityCategories\StoreFacilityCategoryRequest;
use App\Http\Requests\LocationAndCategory\FacilityCategories\UpdateFacilityCategoryRequest;
use App\Models\FacilityCategory;
use App\Services\LocationAndCategory\FacilityCategories\FacilityCategoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FacilityCategoryController extends Controller
{
    public function __construct(
        protected FacilityCategoryService $facilityCategoryService
    ) {}

    /**
     * Display a listing of facility categories.
     */
    public function index(Request $request): View
    {
        $categories = $this->facilityCategoryService->getPaginatedList($request->all(), 10);
        $groupTypes = CategoryGroupType::facilityTypes();

        return view('pages.dashboard.location-and-category.facility-categories.index', compact('categories', 'groupTypes'));
    }

    /**
     * Show the form for creating a new facility category.
     */
    public function create(): View
    {
        $defaultCategories = CategoryGroupType::defaultFacilityCategories();

        return view('pages.dashboard.location-and-category.facility-categories.create', compact('defaultCategories'));
    }

    /**
     * Store a newly created facility category in storage.
     */
    public function store(StoreFacilityCategoryRequest $request): RedirectResponse
    {
        $dto = $request->toDto();
        $this->facilityCategoryService->store($dto);

        return redirect()
            ->route('location-and-category.facility-categories.index')
            ->with('success', 'Kategori fasilitas berhasil ditambahkan.');
    }

    /**
     * Display the specified facility category.
     */
    public function show(FacilityCategory $facilityCategory): View
    {
        $category = $this->facilityCategoryService->findById($facilityCategory->id) ?? $facilityCategory;

        return view('pages.dashboard.location-and-category.facility-categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified facility category.
     */
    public function edit(FacilityCategory $facilityCategory): View
    {
        $category = $facilityCategory;
        $defaultCategories = CategoryGroupType::defaultFacilityCategories();

        return view('pages.dashboard.location-and-category.facility-categories.edit', compact('category', 'defaultCategories'));
    }

    /**
     * Update the specified facility category in storage.
     */
    public function update(UpdateFacilityCategoryRequest $request, FacilityCategory $facilityCategory): RedirectResponse
    {
        $dto = $request->toDto();
        $this->facilityCategoryService->update($facilityCategory, $dto);

        return redirect()
            ->route('location-and-category.facility-categories.index')
            ->with('success', 'Kategori fasilitas berhasil diperbarui.');
    }

    /**
     * Remove or safely deactivate the specified facility category (FR-LCT-06).
     */
    public function destroy(FacilityCategory $facilityCategory): RedirectResponse
    {
        $hasDependencies = $this->facilityCategoryService->hasDependencies($facilityCategory);
        $this->facilityCategoryService->delete($facilityCategory);

        $message = $hasDependencies
            ? 'Kategori fasilitas dinonaktifkan via Soft Delete karena terhubung dengan data fasilitas.'
            : 'Kategori fasilitas berhasil dihapus.';

        return redirect()
            ->route('location-and-category.facility-categories.index')
            ->with('success', $message);
    }

    /**
     * Toggle the active status of the specified facility category.
     */
    public function toggleStatus(FacilityCategory $facilityCategory): RedirectResponse
    {
        $this->facilityCategoryService->toggleStatus($facilityCategory);

        return back()->with('success', 'Status kategori fasilitas berhasil diubah.');
    }
}
