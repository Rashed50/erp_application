<?php
use Illuminate\Support\Facades\Route;

//? Controller Import
use Modules\Dashboard\Http\Controllers\DashboardController;
use Modules\Dashboard\Http\Controllers\LiveDashboardController;

// website index/home page
Route::get('/', [DashboardController::class, 'index'])->name('dashboard.home');

// Live Dashboard Routes (auth-protected)
Route::group(['prefix' => 'live-dashboard', 'middleware' => ['auth']], function () {

    // Blade view entry point
    Route::get('/', [LiveDashboardController::class, 'index'])->name('dashboard.live');

    // ── Dedicated per-view API endpoints ─────────────────────────────
    // Each endpoint is independently callable and returns a consistent
    // { success, data, timestamp } envelope.

    Route::get('/attendance-data', [LiveDashboardController::class, 'getAttendanceData'])
        ->name('dashboard.attendance.data');

    Route::get('/salary-data', [LiveDashboardController::class, 'getSalaryData'])
        ->name('dashboard.salary.data');

    Route::get('/comprehensive-data', [LiveDashboardController::class, 'getComprehensiveData'])
        ->name('dashboard.comprehensive.data');
});
