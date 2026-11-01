<?php

use App\Http\Controllers\Supporting\Announcements\AnnouncementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])
    ->prefix('dashboard/supporting')
    ->name('supporting.')
    ->group(function () {
        // Announcement Management Routes
        Route::patch('announcements/{announcement}/archive', [AnnouncementController::class, 'archive'])
            ->name('announcements.archive');

        Route::resource('announcements', AnnouncementController::class);
    });

