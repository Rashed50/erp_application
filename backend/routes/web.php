<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;


Route::get('/clear-cache', function () {
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('optimize:clear');
    return 'All cache cleared!';
});
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