<?php

use App\Http\Controllers\Maintenance\Assignments\AssignmentController;
use App\Http\Controllers\Maintenance\Officers\OfficerController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:supervisor'])
    ->prefix('dashboard/maintenance')
    ->name('maintenance.')
    ->group(function () {
        Route::get('assignments', [AssignmentController::class, 'index'])
            ->name('assignments.index');
        Route::get('reports/{report}/assignments/create', [AssignmentController::class, 'create'])
            ->name('assignments.create');
        Route::post('assignments', [AssignmentController::class, 'store'])
            ->name('assignments.store');
        Route::get('assignments/{assignment}', [AssignmentController::class, 'show'])
            ->name('assignments.show');
        Route::patch('assignments/{assignment}/reassign', [AssignmentController::class, 'reassign'])
            ->name('assignments.reassign');
        Route::patch('assignments/{assignment}/cancel', [AssignmentController::class, 'cancel'])
            ->name('assignments.cancel');

        Route::patch('officers/{officer}/toggle-availability', [OfficerController::class, 'toggleAvailability'])
            ->name('officers.toggle-availability');

        Route::resource('officers', OfficerController::class)->except(['show']);
    });
