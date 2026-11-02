<?php

use App\Http\Controllers\UsersAndAccess\Roles\RoleController;
use App\Http\Controllers\UsersAndAccess\UserAddresses\UserAddressController;
use App\Http\Controllers\UsersAndAccess\Users\NotificationController;
use App\Http\Controllers\UsersAndAccess\Users\UserController;
use Illuminate\Support\Facades\Route;

// Notification Management Routes (accessible to any authenticated user)
Route::middleware(['auth'])->prefix('dashboard/users-and-access')->name('users-and-access.')->group(function () {
    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::post('notifications/clear-all', [NotificationController::class, 'clearAll'])->name('notifications.clear-all');
    Route::patch('notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::delete('notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

    // Route alias for submodul naming convention
    Route::get('users/notifications', [NotificationController::class, 'index'])->name('users.notifications');
});

Route::middleware(['auth', 'role:admin'])->prefix('dashboard/users-and-access')->name('users-and-access.')->group(function () {
    // Users Management Routes
    Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
    Route::patch('users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');
    Route::resource('users', UserController::class)->except(['show']);

    // Roles Management Routes
    Route::resource('roles', RoleController::class)->except(['show']);

    // User Addresses Management Routes
    Route::resource('user-addresses', UserAddressController::class)->except(['show']);
});
