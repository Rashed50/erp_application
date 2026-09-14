<?php

use Illuminate\Support\Facades\Route;
use Modules\Tender\Http\Controllers\TenderController;

Route::prefix('admin/tender')->name('admin.tender.')->middleware('admin', 'auth')->group(function () {

    Route::prefix('tender')->group(function () {
        Route::get('create/', [TenderController::class, 'create'])->name('tender.create');
        Route::get('tender-pdf/', [TenderController::class, 'tender_pdf'])->name('tender.tender_pdf');
    });
});
