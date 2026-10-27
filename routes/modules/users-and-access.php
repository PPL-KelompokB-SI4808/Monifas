<?php

use App\Http\Controllers\UsersAndAccess\Roles\RoleController;
use App\Http\Controllers\UsersAndAccess\Users\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])->prefix('dashboard/users-and-access')->name('users-and-access.')->group(function () {
    // Users Management Routes
    Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
    Route::patch('users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');
    Route::resource('users', UserController::class)->except(['show']);

    // Roles Management Routes
    Route::resource('roles', RoleController::class)->except(['show']);
});
