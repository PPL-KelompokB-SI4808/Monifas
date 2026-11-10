<?php

use App\Http\Controllers\LocationAndCategory\DamageCategories\DamageCategoryController;
use App\Http\Controllers\LocationAndCategory\FacilityCategories\FacilityCategoryController;
use App\Http\Controllers\LocationAndCategory\Locations\LocationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('dashboard/location-and-category')->name('location-and-category.')->group(function () {
    // Locations
    Route::patch('locations/{location}/toggle-status', [LocationController::class, 'toggleStatus'])->name('locations.toggle-status');
    Route::resource('locations', LocationController::class);

    // Facility Categories
    Route::patch('facility-categories/{facility_category}/toggle-status', [FacilityCategoryController::class, 'toggleStatus'])->name('facility-categories.toggle-status');
    Route::resource('facility-categories', FacilityCategoryController::class);

    // Damage Categories
    Route::patch('damage-categories/{damage_category}/toggle-status', [DamageCategoryController::class, 'toggleStatus'])->name('damage-categories.toggle-status');
    Route::resource('damage-categories', DamageCategoryController::class);
});
