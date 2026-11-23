<?php

use App\Http\Controllers\Home\PublicReportController;
use App\Http\Controllers\Reporting\ReportPriorities\ReportPriorityController;
use App\Http\Controllers\Reporting\Reports\ReportController;
use Illuminate\Support\Facades\Route;

// Route publik — dapat diakses tanpa login (laporan dari halaman beranda)
Route::prefix('public/reporting')->name('reporting.public.')->group(function () {
    Route::post('reports', [PublicReportController::class, 'store'])->name('store');
    Route::get('buildings', [PublicReportController::class, 'buildingsByLocation'])->name('buildings');
    Route::get('rooms', [PublicReportController::class, 'roomsByBuilding'])->name('rooms');
    Route::get('facilities', [PublicReportController::class, 'facilitiesByRoom'])->name('facilities');
});

Route::middleware(['auth'])->prefix('dashboard/reporting')->name('reporting.')->group(function () {
    // Reports (PBI-RPT-01)
    Route::post('reports/{report}/verify', [ReportController::class, 'verify'])->name('reports.verify');
    Route::resource('reports', ReportController::class);

    // Report Priorities (PBI-RPT-02)
    Route::post('report-priorities/sync-order', [ReportPriorityController::class, 'syncOrder'])->name('report-priorities.sync-order');
    Route::patch('report-priorities/{report_priority}/toggle-status', [ReportPriorityController::class, 'toggleStatus'])->name('report-priorities.toggle-status');
    Route::resource('report-priorities', ReportPriorityController::class);
});
