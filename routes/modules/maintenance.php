<?php

use App\Http\Controllers\Maintenance\Officers\OfficerController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:supervisor'])
    ->prefix('dashboard/maintenance')
    ->name('maintenance.')
    ->group(function () {
        Route::patch('officers/{officer}/toggle-availability', [OfficerController::class, 'toggleAvailability'])
            ->name('officers.toggle-availability');

        Route::resource('officers', OfficerController::class)->except(['show']);
    });
