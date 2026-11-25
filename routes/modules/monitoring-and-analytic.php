<?php

use App\Http\Controllers\MonitoringAndAnalytic\Dashboard\DashboardController;
use App\Http\Controllers\MonitoringAndAnalytic\MaintenanceReport\MaintenanceReportController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])
    ->prefix('dashboard/monitoring-and-analytic')
    ->name('monitoring-and-analytic.')
    ->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])
            ->name('dashboard.index');

        Route::get('maintenance-report', [MaintenanceReportController::class, 'index'])
            ->name('maintenance-report.index');
        Route::get('maintenance-report/export', [MaintenanceReportController::class, 'export'])
            ->name('maintenance-report.export');
    });
