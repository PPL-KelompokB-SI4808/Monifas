<?php

use App\Http\Controllers\MonitoringAndAnalytic\Dashboard\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])
    ->prefix('dashboard/monitoring-and-analytic')
    ->name('monitoring-and-analytic.')
    ->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])
            ->name('dashboard.index');
    });
