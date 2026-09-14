<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// Route::middleware('auth:api')->get('/dashboard', function (Request $request) {
//     return $request->user();
// });

//? Controller Import
use Modules\Dashboard\Http\Controllers\LiveDashboardController; 

Route::get('/dashboard/live-data', [LiveDashboardController::class,'LiveDashboardData'])->name('dashboard.live.data');