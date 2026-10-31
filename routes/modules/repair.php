<?php

use App\Http\Controllers\Repair\repairs\RepairController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])
    ->prefix('repair/repairs')
    ->name('repair.repairs.')
    ->group(function () {
        Route::get('/', [RepairController::class, 'index'])
            ->name('index');

        Route::get('/create', [RepairController::class, 'create'])
            ->name('create');

        Route::post('/', [RepairController::class, 'store'])
            ->name('store');

        Route::get('/{repair}', [RepairController::class, 'show'])
            ->name('show');

        Route::get('/{repair}/edit', [RepairController::class, 'edit'])
            ->name('edit');

        Route::put('/{repair}', [RepairController::class, 'update'])
            ->name('update');

        Route::delete('/{repair}', [RepairController::class, 'destroy'])
            ->name('destroy');
    });
