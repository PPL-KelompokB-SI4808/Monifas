<?php

use App\Http\Controllers\Facility\Buildings\BuildingController;
use App\Http\Controllers\Facility\Facilities\FacilityController;
use App\Http\Controllers\Facility\RoomController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('dashboard')->name('facility.')->group(function () {
    Route::resource('buildings', BuildingController::class);
    Route::resource('rooms', RoomController::class);
    Route::resource('facilities', FacilityController::class);
});
