<?php

use App\Http\Controllers\LocationAndCategory\Locations\LocationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('dashboard/location-and-category')->name('location-and-category.')->group(function () {
    Route::patch('locations/{location}/toggle-status', [LocationController::class, 'toggleStatus'])->name('locations.toggle-status');
    Route::resource('locations', LocationController::class);
});
