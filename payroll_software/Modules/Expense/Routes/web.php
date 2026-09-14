<?php

use Illuminate\Support\Facades\Route;
use Modules\Expense\Http\Controllers\TicketController;

Route::prefix('admin/expense')->name('admin.expense.')->middleware('admin', 'auth')->group(function () {

    Route::prefix('ticket')->group(function () {
        Route::get('index/', [TicketController::class, 'index'])->name('ticket.index');
        Route::get('create/', [TicketController::class, 'create'])->name('ticket.create');
        Route::post('store/', [TicketController::class, 'store'])->name('ticket.store');
      //  Route::put('update/{ticket}', [TicketController::class, 'update'])->name('ticket.update');
        Route::delete('delete/{id}', [TicketController::class, 'delete'])->name('ticket.delete');
        Route::get('list/',  [TicketController::class, 'list_index'])->name('ticket.list');
        Route::get('list-api/',  [TicketController::class, 'listAPI'])->name('ticket.listAPI');
        Route::patch('tickets/{ticket}', [TicketController::class, 'updateApprovalStatus']);
        Route::get('search-ticket/', [TicketController::class, 'searchTicketAPI']);

        // Report Route
        Route::get('report/', [TicketController::class, 'exportToPdf']);

    });
});
