<?php

use App\Http\Controllers\Reporting\ReportPriorities\ReportPriorityController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('dashboard/reporting')->name('reporting.')->group(function () {
    // Report Priorities (PBI-RPT-02)
    Route::post('report-priorities/sync-order', [ReportPriorityController::class, 'syncOrder'])->name('report-priorities.sync-order');
    Route::patch('report-priorities/{report_priority}/toggle-status', [ReportPriorityController::class, 'toggleStatus'])->name('report-priorities.toggle-status');
    Route::resource('report-priorities', ReportPriorityController::class);
});
