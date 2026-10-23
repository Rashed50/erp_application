<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/optimize-clear', function () {
    Artisan::call('optimize:clear');

    return response()->json([
        'success' => true,
        'message' => 'Laravel optimize cache cleared successfully!',
        'output' => Artisan::output(),
    ]);
});