<?php

use Illuminate\Support\Facades\Route;
use Modules\HrManagement\Http\Controllers\EmloyeeInformationController;
use Modules\HrManagement\Http\Controllers\DailyActivityController;
use Modules\HrManagement\Http\Controllers\EmployeeActivityController;

use Modules\HrManagement\Http\Controllers\LeaveApplicationController;


Route::prefix('admin/hrmanagement')->name('admin.hrmanagement.')->middleware('admin', 'auth')->group(function () {

    Route::prefix('daily-activity')->group(function () {
        Route::get('/', [DailyActivityController::class, 'index'])->name('daily-activity.hrmanagement');
        Route::post('create/', [DailyActivityController::class, 'store'])->name('daily-activity.store');
        Route::get('list/', [DailyActivityController::class, 'list'])->name('daily-activity.list');
        Route::put('update/{id}', [DailyActivityController::class, 'update'])->name('daily-activity.update');
    });

    Route::prefix('employee')->group(function () {


        Route::get('get-new-employee-id/{id}', [EmloyeeInformationController::class, 'searchNextNewEmployeeUniqueID'])->name('search.new.employee.unique.employee.id');
        Route::post('check/employee/unique-id', [EmloyeeInformationController::class, 'checkEmployeeUniqueInformationBeforeAddNewEmployee'])->name('checked-employee.id');

        Route::get('/update-container', [EmloyeeInformationController::class, 'index'])->name('employee.information.update');
        Route::get('/add-new', [EmloyeeInformationController::class, 'create'])->name('new.employee');


        Route::post('new-insert', [EmloyeeInformationController::class, 'store'])->name('employee-insert');

        Route::get('/search-for-edit-all-info', [EmloyeeInformationController::class, 'searchEmployeeForEditEmployeeInforDetails'])->name('employee.search.for.edit.all.info');
        Route::post('/update-all-information', [EmloyeeInformationController::class, 'updateAnEmployeeAllInforDetails'])->name('employee.updatye.all.info');


        Route::post('/file-update', [EmloyeeInformationController::class, 'updateEmployeeFile'])->name('employee.file.update');
        Route::post('salary-update', [EmloyeeInformationController::class, 'updateEmployeeSalaryInformation'])->name('employee.salary.update');

        Route::post('iqama-passport-update', [EmloyeeInformationController::class, 'updateIqamaAndPassportUpdate']);
        Route::post('working-project-update', [EmloyeeInformationController::class, 'updateWorkingProject']);
        Route::post('trade-designation-update', [EmloyeeInformationController::class, 'updateTradeAndDesignation']);
        Route::post('sponsor-update', [EmloyeeInformationController::class, 'updateSponsor']);
        Route::post('reference-update', [EmloyeeInformationController::class, 'updateEmployeeReference']);
        Route::post('blood-group-update', [EmloyeeInformationController::class, 'updateEmployeeBloodGroup']);
        Route::post('accommodation-update', [EmloyeeInformationController::class, 'updateEmployeeAccommadation']);
        Route::post('ajeerfile-update', [EmloyeeInformationController::class, 'updateEmployeeAjeerDocument']);
        //     employee-all-information-update


        Route::get('/unapproved-employees', [EmloyeeInformationController::class, 'unapprovedList'])->name('unapproved.employees');
        Route::post('/new-employee-job-status-approved', [EmloyeeInformationController::class, 'approvalOfNewInsertedEmployees'])->name('new-employee-job-status-approved');
        Route::post('/employee-salary-update-at-approval', [EmloyeeInformationController::class, 'updateSalaryAtApproval'])->name('employee.salary.details.update.at-approval.time');
        Route::delete('/delete/{id}', [EmloyeeInformationController::class, 'destroy'])->name('employee.destroy');


        // Employee transfer 
        Route::get('/emp-transfer-container', [EmloyeeInformationController::class, 'multipleEmployeeTransferForm'])->name('employee.transfer');
        Route::post('/emp-transfer-submit-form', [EmloyeeInformationController::class, 'multipleEmployeeTransferFormSubmit']);
        Route::get('searching/emp-list/by/project-wise', [EmloyeeInformationController::class, 'getProjectWiseEmployeeListForEmployeeTransfer']);




        // Emploee Actitiy
        Route::get('/emp-activity', [EmployeeActivityController::class, 'index'])->name('employee-activity');


        Route::post('activity/new-activity-insert', [EmployeeActivityController::class, 'employeeNewActivityInsertRequest'])->name('employee.new.activity.insert.request');
        Route::post('new-activity-with-salary-status', [EmployeeActivityController::class, 'employeeNewActivityWithSalaryStatusUpdateRequest'])->name('employee.activity.salary.status.update.request');
    });



    Route::prefix('leave')->group(function () {

        Route::get('application/new-form', [LeaveApplicationController::class, 'index'])->name('leave.application.form'); // employee-leave-work
        Route::post('application/submit', [LeaveApplicationController::class, 'insert'])->name('leave.application.submit.request');
        Route::get('/pending-applications', [LeaveApplicationController::class, 'getLeaveApplications'])->name('leave.applications.list');
        Route::get('/salary_closing-applications', [LeaveApplicationController::class, 'getLeaveApprovedButSalaryPendingApplications'])->name('leave.applications.salary.closing.list');
        // Route::get('leave/application/pending-application', [LeaveApplicationController::class, 'getLeaveApplicationPendingList'])->name('leave.application.pending.list');
        Route::get('/application-form/print-preview', [LeaveApplicationController::class, 'processEmployeeLeaveApplicationFormByRequestedParameter'])->name('process-print-leave-applications');
        // /* ============== Ajax Request ============== */
        // Route::get('leave/application/details', [LeaveApplicationController::class, 'getALeaveApplicationRecord'])->name("a.leave.application.details");
        Route::post('application/update', [LeaveApplicationController::class, 'updateALeaveApplicationRecord'])->name("leave.application.details.update");
        Route::get('application-rejection/{id}', [LeaveApplicationController::class, 'rejectALeaveApplication']);
    });
});
