<?php

use Illuminate\Support\Facades\Route;


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
