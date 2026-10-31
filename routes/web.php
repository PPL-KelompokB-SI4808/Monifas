<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Facility\RoomController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('pages.home.index'));

Route::get('/dashboard', fn () => redirect('/dashboard/monitoring-and-analytic/dashboard'))->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

foreach (glob(__DIR__.'/modules/*.php') as $moduleRoute) {
    require $moduleRoute;
}

// Tambahan Route untuk Facility / Rooms (PBI-FAC-02)
Route::prefix('facility')->name('facility.')->group(function () {
    // Route Buildings sebelumnya...
    
    // Route Rooms (PBI-FAC-02)
    Route::resource('rooms', RoomController::class);
});

Route::get('/dashboard/{module}/{submodule}', function (string $module, string $submodule) {
    $viewPath = "pages.dashboard.{$module}.{$submodule}.index";

    if (view()->exists($viewPath)) {
        return view($viewPath);
    }

    abort(404);
})->name('dashboard.submodule');

require __DIR__.'/auth.php';