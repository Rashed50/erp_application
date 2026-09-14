<?php

use Illuminate\Support\Facades\Route;
use Modules\Payroll\Http\Controllers\Salary\SalaryController;
use Modules\Payroll\Http\Controllers\Salary\PayslipUploadController;
use Modules\Payroll\Http\Controllers\Salary\PartialSalaryController;
use Modules\Payroll\Http\Controllers\Attendance\AttendanceProcessController;
use Modules\Payroll\Http\Controllers\Attendance\AttendanceReportController;
use Modules\Payroll\Http\Controllers\Attendance\AttendanceInOutController;
use Modules\Payroll\Http\Controllers\EmployeeBonusController;


Route::prefix('admin/payroll')->name('admin.payroll.')->middleware('admin', 'auth')->group(function () {

    /*
    =============================================================
    ===================== Salary Routes =========================
    =============================================================
    */
    Route::prefix('salary')->group(function () {

        Route::get('salary-update', [SalaryController::class, 'salaryIndex'])->name('salary-update');
        Route::post('paid-list', [SalaryController::class, 'SalarypaidList']);
        Route::post('payment/unpaid/status', [SalaryController::class, 'SalaryPaymentToUnPay']);


        Route::get('employee-bonus', [EmployeeBonusController::class, 'index'])->name('employee-bonus');
        Route::get('employee-bonus/records', [EmployeeBonusController::class, 'getAnEmployeeBonusSalaryRecords']);
        Route::post('store/new/employee-bonus', [EmployeeBonusController::class, 'storeNewEmployeeBonusSalaryInformation']);
        Route::delete('delete/employee-bonus/{id}', [EmployeeBonusController::class, 'deleteAnEmployeeBonusSalaryRecordByBonusId']);
        Route::get('process/employee-bonus/details/report', [EmployeeBonusController::class, 'processEmployeeBonusDetailsReport']);





        Route::post('pending-list', [SalaryController::class, 'SalaryPendingList']);
        Route::post('pending-salary-record-history', [SalaryController::class, 'getAnEmployeeSalaryRecordBySalaryHistoryAutoId']);
        Route::post('pending-salary-update', [SalaryController::class, 'updateAnEmployeeSalaryRecordBySalaryHistoryAutoId']);
        Route::delete('pending-salary-delete/{slh_auto_id}', [SalaryController::class, 'deleteAnEmployeeUnpaidSalaryRecord']);
        Route::post('payment/paid/status', [SalaryController::class, 'SalaryPaymentToPay']);






        Route::get('preview/', [SalaryController::class, 'index'])->name('salary.payroll');
        Route::post('/wps-salary-show-using-excel', [SalaryController::class, 'showWPSSalaryUsingExcelUpload'])->name('wps.salary.show.using.excel');
        Route::get('/salary_closing-applications', [SalaryController::class, 'getLeaveApprovedButSalaryPendingApplications'])->name('salary.leave-approved-applications');




        // Partial Salary Routes - Moved inside salary prefix
        Route::resource('partial-salary', PartialSalaryController::class);
        Route::get('partial-salary-index', [PartialSalaryController::class, 'index'])->name('salary.partial-salary.index');
        Route::post('partial-salary/search', [PartialSalaryController::class, 'search'])->name('partial-salary.search');
        Route::post('partial-salary/download', [PartialSalaryController::class, 'searchAndDownloadPatialSalary'])->name('partial-salary.search.download');
        // Excel upload routes
        Route::post('partial-salary-histories/preview-excel', [PartialSalaryController::class, 'previewExcel'])->name('partial-salary-histories.preview-excel');
        Route::post('partial-salary-histories/import-excel', [PartialSalaryController::class, 'importExcel'])->name('partial-salary-histories.import-excel');
        Route::get('partial-salary-histories/download-sample', [PartialSalaryController::class, 'downloadSampleExcel'])->name('partial-salary-histories.download-sample');

        // Payslip Upload Routes
        Route::get('/payslip-upload', [PayslipUploadController::class, 'index'])->name('payslip-upload.index');
        Route::post('/salary-sheet/store', [PayslipUploadController::class, 'store'])->name('salary-sheet.store');
        Route::get('/salary-sheets/search', [PayslipUploadController::class, 'search'])->name('salary-sheets.search');
        Route::delete('/salary-sheet/{id}', [PayslipUploadController::class, 'destroy'])->name('salary-sheet.destroy');
    });



    Route::prefix('api/salary')->group(function () {
        Route::get('/', [SalaryController::class, 'EmployeeSalaryPaidByBankReportForSendingToBank'])->name('api.salary.payroll');
    });

    // Payslip Email Routes
    Route::prefix('api/payslip')->group(function () {
        Route::post('/send-emails', [SalaryController::class, 'sendPayslipEmails'])->name('api.payslip.send');
        Route::get('/employees', [SalaryController::class, 'getEmployeesForPayslip'])->name('api.payslip.employees');
    });





    /*
    =============================================================
    ===================== Partial Salary Routes =================
    =============================================================
    */

    // Route::prefix('salary')->group(function () {
    //     Route::resource('partial-salary', PartialSalaryHistoryController::class);
    //   //  Route::get('partial-salary', PartialSalaryHistoryController::class)->name('salary.partial-salary');
    //     Route::post('partial-salary-histories/search', [PartialSalaryHistoryController::class, 'search'])->name('partial-salary-histories.search');
    // });


    /*
    =============================================================
    ===================== Attendance Routes =====================
    =============================================================
    */

    Route::prefix('attendance')->group(function () {
        Route::get('/proccess', [AttendanceProcessController::class, 'index'])->name('attendance.process');

        Route::get('/in-out', [AttendanceInOutController::class, 'index'])->name('attendance.in.out');
        Route::get('/attendence-bio', [AttendanceInOutController::class, 'employeeAttendanceBioSearch'])->name('attendance.bio.search');

        // overtime file upload routes
        Route::post('/ot-file/store', [AttendanceInOutController::class, 'storeOvertimeSheet'])->name('overtime.sheet.store');
        Route::put('/ot-file/update/{id}', [AttendanceInOutController::class, 'updateOvertimeSheet'])->name('overtime.sheet.update');
    });
    Route::prefix('api/attendance')->group(function () {
        Route::get('/', [AttendanceProcessController::class, 'getWPSEmployeeMonthlyWorkingAttendanceSummaryReport'])->name('api.attendance.process');
        Route::get('/summary-report', [AttendanceReportController::class, 'processAttendanceSummaryReport'])->name('api.attendance.summary.report');

        Route::get('/search-ot-file', [AttendanceInOutController::class, 'searchOvertimeSheets'])->name('overtime.sheet.search');
        Route::delete('/delete-ot-file/{id}', [AttendanceInOutController::class, 'deleteOvertimeSheet'])->name('overtime.sheet.delete');
    });
});
