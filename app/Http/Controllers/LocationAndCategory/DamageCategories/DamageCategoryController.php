<?php

namespace App\Http\Controllers\LocationAndCategory\DamageCategories;

use App\Http\Controllers\Controller;
use App\Http\Requests\LocationAndCategory\DamageCategories\StoreDamageCategoryRequest;
use App\Http\Requests\LocationAndCategory\DamageCategories\UpdateDamageCategoryRequest;
use App\Models\DamageCategory;
use App\Services\LocationAndCategory\DamageCategories\DamageCategoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DamageCategoryController extends Controller
{
    public function __construct(
        protected DamageCategoryService $damageCategoryService
    ) {}

    /**
     * Display a listing of damage categories.
     */
    public function index(Request $request): View
    {
        $categories = $this->damageCategoryService->getPaginatedList($request->all(), 10);
        $severityHints = $this->damageCategoryService->getUniqueSeverityHints();

        return view('pages.dashboard.location-and-category.damage-categories.index', compact('categories', 'severityHints'));
    }

    /**
     * Show the form for creating a new damage category.
     */
    public function create(): View
    {
        return view('pages.dashboard.location-and-category.damage-categories.create');
    }

    /**
     * Store a newly created damage category in storage.
     */
    public function store(StoreDamageCategoryRequest $request): RedirectResponse
    {
        $dto = $request->toDto();
        $this->damageCategoryService->store($dto);

        return redirect()
            ->route('location-and-category.damage-categories.index')
            ->with('success', 'Kategori kerusakan berhasil ditambahkan.');
    }

    /**
     * Display the specified damage category.
     */
    public function show(DamageCategory $damageCategory): View
    {
        $category = $this->damageCategoryService->findById($damageCategory->id) ?? $damageCategory;

        return view('pages.dashboard.location-and-category.damage-categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified damage category.
     */
    public function edit(DamageCategory $damageCategory): View
    {
        $category = $damageCategory;

        return view('pages.dashboard.location-and-category.damage-categories.edit', compact('category'));
    }

    /**
     * Update the specified damage category in storage.
     */
    public function update(UpdateDamageCategoryRequest $request, DamageCategory $damageCategory): RedirectResponse
    {
        $dto = $request->toDto();
        $this->damageCategoryService->update($damageCategory, $dto);

        return redirect()
            ->route('location-and-category.damage-categories.index')
            ->with('success', 'Kategori kerusakan berhasil diperbarui.');
    }

    /**
     * Remove or safely deactivate the specified damage category (FR-LCT-06).
     */
    public function destroy(DamageCategory $damageCategory): RedirectResponse
    {
        $hasDependencies = $this->damageCategoryService->hasDependencies($damageCategory);
        $this->damageCategoryService->delete($damageCategory);

        $message = $hasDependencies
            ? 'Kategori kerusakan dinonaktifkan via Soft Delete karena terhubung dengan data laporan.'
            : 'Kategori kerusakan berhasil dihapus.';

        return redirect()
            ->route('location-and-category.damage-categories.index')
            ->with('success', $message);
    }

    /**
     * Toggle the active status of the specified damage category.
     */
    public function toggleStatus(DamageCategory $damageCategory): RedirectResponse
    {
        $this->damageCategoryService->toggleStatus($damageCategory);

        return back()->with('success', 'Status kategori kerusakan berhasil diubah.');
    }
}
