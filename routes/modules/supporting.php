<?php

use App\Http\Controllers\Supporting\Announcements\AnnouncementController;
use App\Http\Controllers\Supporting\Campaigns\CampaignController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])
    ->prefix('dashboard/supporting')
    ->name('supporting.')
    ->group(function () {
        // Announcement Management Routes
        Route::patch('announcements/{announcement}/archive', [AnnouncementController::class, 'archive'])
            ->name('announcements.archive');

        Route::resource('announcements', AnnouncementController::class);

        // Campaign Management Routes
        Route::patch('campaigns/{campaign}/cancel', [CampaignController::class, 'cancel'])
            ->name('campaigns.cancel');

        Route::resource('campaigns', CampaignController::class);
    });
