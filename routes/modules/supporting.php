<?php

use App\Http\Controllers\Supporting\Announcements\AnnouncementController;
use App\Http\Controllers\Supporting\Feedback\FeedbackController;
use Illuminate\Support\Facades\Route;

// Announcement Management Routes (Admin only)
Route::middleware(['auth', 'role:admin'])
    ->prefix('dashboard/supporting')
    ->name('supporting.')
    ->group(function () {
        Route::patch('announcements/{announcement}/archive', [AnnouncementController::class, 'archive'])
            ->name('announcements.archive');

        Route::resource('announcements', AnnouncementController::class);
    });

// Feedback Management Routes (All authenticated users for CRUD, Admin/Supervisor for respond)
Route::middleware(['auth'])
    ->prefix('dashboard/supporting')
    ->name('supporting.')
    ->group(function () {
        Route::post('feedback/{feedback}/respond', [FeedbackController::class, 'respond'])
            ->name('feedback.respond');

        Route::resource('feedback', FeedbackController::class);
    });
