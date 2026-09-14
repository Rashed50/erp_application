<?php
use Illuminate\Support\Facades\Route;
use Modules\Transportation\Http\Controllers\{VehicleServicingController,TransportationReportController};


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::prefix('transportation')->group(function() {
//     Route::get('/', 'TransportationController@index');
// });


Route::prefix('admin/transportation')->name('admin.transportation.')->middleware('admin', 'auth')->group(function () {

    Route::get('employee-verify/{empoyee_id}', [VehicleServicingController::class, 'searchEmployeeByEmployeeId'])->name('employee.id.verify');
    Route::prefix('vehicle-servicing')->group(function () {

        Route::get('/', [VehicleServicingController::class, 'index'])->name('transportation.index');
        Route::post('store/', [VehicleServicingController::class, 'store'])->name('transportation.servicing.store');
        Route::post('record-delete/{id}', [VehicleServicingController::class, 'deleteVehicleMaintenanceRecord'])->name('transportation.servicing.record.delete');

        Route::get('search/',  [VehicleServicingController::class, 'searchVehicleSearchingRecords'])->name('vehicle.servicing.search');
        Route::post('service-name/', [VehicleServicingController::class, 'storeVehicleServiceName'])->name('service.name.store');
        Route::delete('service-name-delete/{id}', [VehicleServicingController::class, 'deleteVehicleServiceName'])->name('service.name.destroy');

        Route::get('details/{veh_id}', [VehicleServicingController::class, 'getServicingDetails'])->name('vehicle.service.details');
        Route::put('update/{veh_id}', [VehicleServicingController::class, 'updateServicing'])->name('vehicle.service.update');

    });
    Route::prefix('reports')->name('reports.')->group(function(){
        Route::get('/report-form', [TransportationReportController::class, 'reportGenerationForm'])->name('generation.form');
        Route::get('/api/reports-generate', [TransportationReportController::class, 'generateTransportationReport'])->name('generate.report');

    });
    // admin/transportation/reports/api/reports-generate

});
