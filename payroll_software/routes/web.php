<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Fontend\FontendController;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Admin\EmpCategoryController;
use App\Http\Controllers\Admin\EmployeeInfoController;
use App\Http\Controllers\Admin\DeductionController;
use App\Http\Controllers\Admin\SalaryDetailsController;
use App\Http\Controllers\Admin\DistrictController;
use App\Http\Controllers\Admin\DivisionController;
use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\Admin\AgencyInfo\AgencyInfoController;
use App\Http\Controllers\Admin\ProjectInfoController;
use App\Http\Controllers\Admin\BannerInfoController;
use App\Http\Controllers\Admin\CompanyProfileController;
use App\Http\Controllers\Admin\Expense\DailyExpenseController;
use App\Http\Controllers\Admin\CostTypeController;
use App\Http\Controllers\Admin\DailyWorkHistoryController;
use App\Http\Controllers\Admin\SubCompanyInfoController;
use App\Http\Controllers\Admin\MonthlyWorkHistoryController;
use App\Http\Controllers\Admin\AdvancePayController;
use App\Http\Controllers\Admin\EmpContactPersonController;
use App\Http\Controllers\Admin\EmpJobExperienceController;
use App\Http\Controllers\Admin\SallaryGenerateController;
use App\Http\Controllers\Admin\IncomeSourceController;
use App\Http\Controllers\Admin\OfficeBuildingController;
use App\Http\Controllers\Admin\SponsorController;
use App\Http\Controllers\Admin\ExcelExportController;
use App\Http\Controllers\Admin\EmpActivity\EmpActivityController;
use App\Http\Controllers\Admin\Inventory_Module\ItemSetup\ItemCategoryController;
use App\Http\Controllers\Admin\Inventory_Module\ItemSetup\ItemSubCategoryController;
use App\Http\Controllers\Admin\Inventory_Module\ItemSetup\ItemBrandController;
use App\Http\Controllers\Admin\Inventory_Module\ItemSetup\ItemDetailsController;
use App\Http\Controllers\Admin\Inventory_Module\ItemSetup\ItemNameController;
use App\Http\Controllers\Admin\Inventory_Module\InventorySubStoreContoller;
use App\Http\Controllers\Admin\RequirementTools\OrderComponentController;
use App\Http\Controllers\Admin\Promosion\PromosionController;
use App\Http\Controllers\Admin\Leave\LeaveApplicationController;
use App\Http\Controllers\Admin\Report\IqamaAdvacnePayController;
use App\Http\Controllers\Admin\Bill_Voucher\BillVoucherController;
use App\Http\Controllers\Admin\Report\IncomeReportController;
use App\Http\Controllers\Admin\Report\InventoryReportController;
use App\Http\Controllers\Admin\ProjectImage\ProjectImgUploadController;
use App\Http\Controllers\Admin\Expire\IqamaExpireController;
use App\Http\Controllers\Admin\Expire\PassportExpireController;
use App\Http\Controllers\Admin\InOut\EmployeeInOutController;
use App\Http\Controllers\Admin\Vehicle\VehicleController;
use App\Http\Controllers\Admin\LogBook\LogBookController;
use App\Http\Controllers\Admin\CPF\ContributionController;
use App\Http\Controllers\Admin\AnualFee\AnualFeeDetailsController;
use App\Http\Controllers\Admin\Authentication\RoleController;
use App\Http\Controllers\Admin\Authentication\UserController;
use App\Http\Controllers\Admin\BdOfficePayment\BdOfficePaymentController;
use App\Http\Controllers\Admin\MainContractorController;
use App\Http\Controllers\Admin\Permission\SalaryProcessPermissionController;
use App\Http\Controllers\Admin\UserProjectAssign\UserProjectAssignController;
use App\Http\Controllers\Admin\Report\EmployeeReportController;
use App\Http\Controllers\Admin\Report\SalaryReportController;
use App\Http\Controllers\Admin\Emp_tuv\EmployeeTUVController;
use App\Http\Controllers\Admin\Inventory_Module\Item_Distribution\EmpItemReceivedController;
use App\Http\Controllers\Admin\CostControl\ActivityDetailsController;
use App\Http\Controllers\Admin\Emp_Bonus\EmployeeBonusController;
use App\Http\Controllers\Admin\FiscalYear\FiscalYearController;
use App\Http\Controllers\Admin\Supplier\SupplierInfoController;
use App\Http\Controllers\Admin\CateringService\EmpMonthlyCateringController;
use App\Http\Controllers\Admin\Employee_Bank\EmployeeBankDetailsController;
// accounting
use App\Http\Controllers\Admin\AccountsModule\ChartOfAccountController;
use App\Http\Controllers\Admin\AccountsModule\JournalInfoController;
use App\Http\Controllers\Admin\AccountsModule\ChartofaccountPurchaseController;
// form download
use App\Http\Controllers\Admin\Download\FormDownloadController;
use App\Http\Controllers\Admin\Vehicle\VehicleAttendanceController;




/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
*/

/* cache clear route */

Route::get('/clear-cache', function () {
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('optimize:clear');
    return 'All cache cleared!';
});

//URL::forceScheme('https');
// Route::get('/', [FontendController::class, 'index']);
//Route::get('/login', [FontendController::class, 'index']);
Auth::routes();


Route::group(['prefix' => 'admin', 'middleware' => ['admin', 'auth']], function () {

    // file preview url
    Route::get('api/get-file-preview-url/{file_location}', [AdminController::class, 'getFilePreviewUrl'])->name('get.file.preview.url');
    /* ============================== Qr Code ============================== */

    Route::get('create-ebill-voucher', [BillVoucherController::class, 'BillVoucherFormLoad'])->name('bill-voucher.create');
    Route::get('get/e-voucher/add-to-cart', [BillVoucherController::class, 'getCartInfo'])->name('get.evoucher-add.to.cart.data');
    Route::post('e-voucher/remove-to-cart', [BillVoucherController::class, 'removeCartInfo'])->name('evoucher-remove.to.cart');
    Route::post('e-voucher/process/add-to-cart', [BillVoucherController::class, 'addToCart'])->name('evoucher-item-add.tocart');
    Route::post('sub-company/e-voucher/process', [BillVoucherController::class, 'eVoucherProccess'])->name('e.voucher.process');
    Route::get('submited/bill/voucher', [BillVoucherController::class, 'submitedBillVoucherUi'])->name('submited-bill-voucher-ui');
    Route::get('submited/bill/voucher-regenerate/{id}', [BillVoucherController::class, 'showQRCodeBillVourcher'])->name('submited-bill-voucher-regenerate');
    Route::get('sub-company/e-voucher/process/print-preview', [BillVoucherController::class, 'eVoucherProccessForPrintPreview'])->name('e.voucher.process-print.preview');


    // Bill voucher edit
    Route::post('qr/voucher/updated/request', [BillVoucherController::class, 'updatedEVoucherAndCreateNewQRCode'])->name('updated.e.voucher.process');
    Route::get('voucher-qr-code.invoice-update-form', [BillVoucherController::class, 'loadBillVoucherFormLoadForInvoiceUpdate'])->name('qrcode.bill.voucher.update.ui');
    Route::post('e-voucher-information/search-by/invoice-no', [BillVoucherController::class, 'getAllEVoucherInformationForUpdateByInvoiceNoAjaxRequest'])->name('bill_voucher.information_details.by_invoice_no');
    Route::post('qr/voucher/updated/invoice-status', [BillVoucherController::class, 'updatedQRCodeInvoiceStatus'])->name('updated.e.voucher.invoice.status');
    Route::post('qr/voucher/report', [BillVoucherController::class, 'processQrCodeInvoiceReport'])->name('qr.invoice.report');
    Route::post('qr/voucher/record-delete', [BillVoucherController::class, 'deleteInvoiceRecordByInvoiceNumber'])->name('qrcode.invoice.record.delete');

    // Only QRCODE Create
    Route::get('company/bill-voucher/qrcode/create', [BillVoucherController::class, 'qrcodeCreateUI'])->name('create.qrcode.only');
    Route::post('company/voucher/diplay-qrcode', [BillVoucherController::class, 'generateQRCode'])->name('display-generated-qrcode-only');
    Route::get('invoice/salary/statement/summary', [BillVoucherController::class, 'loadInvoiceAndSalaryStatementProcessUI'])->name('invoice.and.salary.statement.summary');
    Route::post('invoice/salary/statement/summary/byproject', [BillVoucherController::class, 'getAProjectSalaryInfoAndInvoiceStatementSummary'])->name('aproject.total_salary.andinvoice.summary');
    Route::post('invoice/salary/statement/summary/report-priview', [BillVoucherController::class, 'processAProjectSalaryInfoAndInvoiceStatementSummary'])->name('aproject.salary.andinvoic.statement.report');

    // INVOICE SUMMARY SECTION
    Route::get('invoice/summary/create', [BillVoucherController::class, 'loadInvoiceSummaryFormUI'])->name('invoice.summary.create.ui');
    Route::post('invoice/summary/store', [BillVoucherController::class, 'saveNewInvoiceSummaryInformation'])->name('invoice.summary.store.request');
    Route::get('invoice/summary/edit/', [BillVoucherController::class, 'getInvoiceSummaryForEdit'])->name('invoice.summary.search.by.invoicesummaryid');
    Route::delete('invoice/summary/delete/', [BillVoucherController::class, 'deleteInvoiceSummaryRecord'])->name('invoice.summary.delete.by.invoicesummaryid');
    Route::get('invoice/summary/searching', [BillVoucherController::class, 'searchInvoiceSummary'])->name('invoice.summary.search');
    Route::post('invoice/summary/report-process', [BillVoucherController::class, 'processInvoiceSummaryReport'])->name('invoice.summary.report.process');





    Route::get('dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    /* ======================== User Role Permision ======================== */
    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);


    // add New Permission
    Route::post('user/user/permission/permission-create-request', [RoleController::class, 'createNewPermission'])->name('user.permission.newpermission');

    Route::get('user/profile', [UserController::class, 'profile'])->name('user.user-profile');
    Route::post('user/profile-update', [UserController::class, 'profileInformationUpdate'])->name('user.user-profile-update');
    Route::get('user/login-activity-history', [UserController::class, 'processAndShowLoginUserOrEmployeeActivityHistory'])->name('login.user.employee.activity.history');

    // Salary Process Permission
    Route::get('salary/processing-permissions', [SalaryProcessPermissionController::class, 'salaryProcessPermissionListUi'])->name('salary-process-permission-ui');
    Route::post('salary/processing-permission/update/or/insert', [SalaryProcessPermissionController::class, 'salaryProcessingPermissionInsert'])->name('salary.process.permission.update.or.insert');


    /* ======================== User Role Permision ======================== */

    /* ========== Company Profile ========== */
    Route::get('company/profile', [CompanyProfileController::class, 'profile'])->name('company-profiles');
    /* ========== Banner Information ========== */
    Route::get('banner/information', [BannerInfoController::class, 'index'])->name('banner-info');
    Route::get('banner/information/add', [BannerInfoController::class, 'add'])->name('add-banner-info');
    Route::get('banner/information/edit/{ban_id}', [BannerInfoController::class, 'edit'])->name('edit-banner-info');
    Route::get('banner/information/delete/{ban_id}', [BannerInfoController::class, 'delete'])->name('delete-banner-info');
    Route::post('banner/information/insert', [BannerInfoController::class, 'insert'])->name('insert-banner-info');
    Route::post('banner/information/update', [BannerInfoController::class, 'update'])->name('update-banner-info');
    Route::post('company/profile/update', [CompanyProfileController::class, 'updateProfile'])->name('update-profile');


    /* ======================== Company Bank Informations ======================== */
    Route::get('company/bank/informations', [CompanyProfileController::class, 'bankInformationsUI'])->name('bank-infos');
    Route::post('company/bank/informations/add', [CompanyProfileController::class, 'bankInformationInsertRequest'])->name('bank-infos.insert');
    Route::get('company/bank/informations/{bankID}/edit', [CompanyProfileController::class, 'editBankInformations'])->name('bank-infos.edit');
    Route::post('company/bank/informations/update', [CompanyProfileController::class, 'bankInformationUpdateRequest'])->name('bank-infos.update');
    // sub contractor wise bank info ajax request
    Route::get('company/sub-contractor-wise/bank-info/{subContractorID}', [CompanyProfileController::class, 'getSubContractorWiseBankInfoForAjaxCall']);


    /* ========== Sponsor Information ========== */
    Route::get('sponser/add', [SponsorController::class, 'index'])->name('add-sponser');
    Route::get('sponser/{spons_id}/edit', [SponsorController::class, 'edit'])->name('edit.sponser');
    Route::get('sponser/{spons_id}/delete', [SponsorController::class, 'delete'])->name('delete.sponser');
    Route::post('sponser/insert', [SponsorController::class, 'insert'])->name('insert-new.sponser');
    Route::post('sponser/update', [SponsorController::class, 'update'])->name('update.sponser');

    /* ========== Project Information ========== */
    Route::get('project/information', [ProjectInfoController::class, 'index'])->name('project-info');
    // Route::get('project/information/add', [ProjectInfoController::class, 'add'])->name('add-project-info');
    Route::post('project/information/insert', [ProjectInfoController::class, 'insert'])->name('insert-project-info');
    Route::get('project/information/edit/{proj_id}', [ProjectInfoController::class, 'edit'])->name('project-info-edit');
    Route::post('project/information/update', [ProjectInfoController::class, 'update'])->name('update-project-info');
    Route::get('project/information/view/{proj_id}', [ProjectInfoController::class, 'view'])->name('project-info-view');
    Route::get('projects/information/report', [ProjectInfoController::class, 'generateProjectReport'])->name('project.information.report');
    // delete permission now off
    // Add Project Plot
    Route::post('project/plot/info/insert', [ProjectInfoController::class, 'storeProjectNewPlotInformation'])->name('project.new.plot.insert.request');
    Route::get('project/plot/info/get/{id?}', [ProjectInfoController::class, 'getPlotInformationByProjectId'])->name('project.plot.search.request');

    /* ========== Project Incharge ========== */
    Route::get('project/incharge/add', [ProjectInfoController::class, 'addProjectInchage'])->name('add-project.incharge');
    Route::post('project/incharge/insert', [ProjectInfoController::class, 'InsertProjectInchage'])->name('insert-project.incharge');
    Route::post('find-employee/for/project/incharge', [ProjectInfoController::class, 'findEmployee'])->name('findEmployeeForIncharge');
    Route::post('check/valid/employee', [ProjectInfoController::class, 'validEmployee'])->name('check.valid-emp-id');

    /* ========== Project Image Upload ========== */
    Route::get('project/image/upload', [ProjectInfoController::class, 'loadProjectImageUPloadUI'])->name('project-image-upload');
    Route::get('project/image/remove/{id}', [ProjectInfoController::class, 'deleteUploadedProjectImageInformation']);
    Route::post('project/image/insert/upload', [ProjectInfoController::class, 'loadProjectImageUPloadRequest'])->name('upload-project-muliple-image');
    Route::post('search/project-image', [ProjectInfoController::class, 'searchProjectImage'])->name('search-project-image');


    /* ========== User Project Access Information ========== */
    Route::get('user/project-access/informations', [UserProjectAssignController::class, 'userProjectAccessInformationUI'])->name('user-project-access-permission');
    Route::post('user/project-access/information/insert', [UserProjectAssignController::class, 'userProjAccessInfoInsertRequest'])->name('insert-user-project-access-info');
    Route::get('user/project-access/information-not-allowed/{proj_access_id}', [UserProjectAssignController::class, 'userProjectAccessInformationAsNotAllowed'])->name('user-project-access-deActive');
    Route::get('user/project-access/information-allowed/{proj_access_id}', [UserProjectAssignController::class, 'userProjectAccessInformationAsAllowed'])->name('user-project-access-active');
    // Project Accees Employee Details Ajax Request
    Route::post('user/project-access/info-details/for-ajax-call', [UserProjectAssignController::class, 'employeeProjectAccessInfoDetailsWithAjaxRequest'])->name('user.searching-for-project-access-details-info');
    // Timekeeper Attendnace INOUT Permission in Days
    Route::post('attendance/inout/permission', [UserProjectAssignController::class, 'saveAndUpdateTimekeeperAttendanceINOUTPermission'])->name('attendance.inout.permission');

    /* ========== Company Main Contractor Information ========== */
    Route::get('company/main-contractor/informations', [MainContractorController::class, 'index'])->name('main-contrator-info');
    Route::post('company/main-contractor/insert', [MainContractorController::class, 'insertMainContractorInformations'])->name('insert-main-contractor-info');
    Route::post('company/main-contractor/update', [MainContractorController::class, 'updateMainContractorInformations'])->name('update-main-contractor-info');

    /* ========== Sub Company Information ========== */
    Route::get('sub-company/create', [SubCompanyInfoController::class, 'create'])->name('sub-comp-info');
    Route::get('sub-company/edit/{sb_comp_id}', [SubCompanyInfoController::class, 'edit'])->name('edit-info');
    Route::get('sub-company/view/{sb_comp_id}', [SubCompanyInfoController::class, 'view'])->name('view-info');
    Route::get('sub-company/delete/{sb_comp_id}', [SubCompanyInfoController::class, 'delete'])->name('delete-info');
    Route::post('sub-company/insert', [SubCompanyInfoController::class, 'insert'])->name('insert-sub-company');
    Route::post('sub-company/update', [SubCompanyInfoController::class, 'update'])->name('update-sub-company');
    /* ========== Employee Category ========== */
    Route::get('employee/designation', [EmpCategoryController::class, 'index'])->name('emp-category');
    Route::get('employee/designation/edit/{catg_id}', [EmpCategoryController::class, 'edit'])->name('edit-employee-category');
    Route::get('employee/designation/delete/{catg_id}', [EmpCategoryController::class, 'delete'])->name('delete-employee-category');
    Route::post('employee/designation/insert', [EmpCategoryController::class, 'insert'])->name('insert-category');
    Route::post('employee/designation/update', [EmpCategoryController::class, 'update'])->name('update-category');

    /* ========== Employee Job Experience ========== */
    // closed on 4.5.26
    // Route::get('employee/job/experience/info', [EmpJobExperienceController::class, 'index'])->name('emp-job-experience');
    // Route::get('employee/job/experience/{ejex_id}/info/edit', [EmpJobExperienceController::class, 'edit'])->name('edit.emp-job-experience');
    // Route::post('employee/job/experience/info/insert', [EmpJobExperienceController::class, 'insert'])->name('insert-job-experience.info');
    // Route::post('employee/job/experience/info/update', [EmpJobExperienceController::class, 'update'])->name('update-job-experience.info');


    /* ========== Employee contact info ========== */
    //closed on 4.5.26
    //  Route::get('employee/contact/person/info', [EmpContactPersonController::class, 'index'])->name('emp-contact-person');
    // Route::get('employee/contact/person/{ecp_id}/info/edit', [EmpContactPersonController::class, 'edit'])->name('edit-employee.contact-person');
    // Route::post('employee/contact/person/info/insert', [EmpContactPersonController::class, 'insert'])->name('insert-contact-person.info');
    // Route::post('employee/contact/person/info/update', [EmpContactPersonController::class, 'update'])->name('update-contact-person.info');

    /* ========== Employee involve ========== */
    // closed on 4.5.26
    // Route::get('release/employee/list', [EmployeeInfoController::class, 'releaseList'])->name('releaseListemployee-list');
    // Route::get('pre-release/employee/list', [EmployeeInfoController::class, 'preReleaseList'])->name('pre.releaseListemployee-list');

    Route::post('employee/list/search', [EmployeeInfoController::class, 'searchEmployeeByProjectSponerAndEmpID'])->name('employee-list-search');

    Route::get('search/employee/details', [EmployeeInfoController::class, 'loadSearchingUIForAnEmployeeDetailsByMultiTypeParameter'])->name('search-employee');
    Route::post('employee/search/with/multitype-parameter/employee-details', [EmployeeInfoController::class, 'searchingEmployeeByEmployeeMultitypeParameter'])->name('employee.searching.searching-with-multitype.parameter');
    Route::post('employee/search/with/multitype-parameter/active-employee-details', [EmployeeInfoController::class, 'searchingActiveEmployeeByEmployeeMultitypeParameter'])->name('active.employee.searching.searching-with-multitype.parameter');

    Route::get('search/employee/status', [EmployeeInfoController::class, 'searchEmpStatus'])->name('search-employee.status');
    // closed on 04.5.26 Route::get('search/employee/for/update/info', [EmployeeInfoController::class, 'searchEmpForUpdate'])->name('search-employee.update.info');

    Route::get('employee/transfer/transfer-form', [EmployeeInfoController::class, 'multipleEmployeeTransferForm'])->name('employee.transfer-to-new-project');
    Route::post('employee/transfer/transfer-process', [EmployeeInfoController::class, 'multipleEmployeeTransferFormSubmit'])->name('employee.transfer-form-submit');


    Route::get('search/employee/summary/details', [EmployeeInfoController::class, 'searchEmpSummary'])->name('search-employee.summary');
    // closed on 4.5.26 , Route::get('employee/list', [EmployeeInfoController::class, 'index'])->name('employee-list');
    Route::get('employee/add', [EmployeeInfoController::class, 'add'])->name('add-employee');
    Route::get('employee/edit/{emp_auto_id}', [EmployeeInfoController::class, 'edit']);
    Route::post('employee/information/update', [EmployeeInfoController::class, 'updateEmployeeInformationData'])->name('employee-information-update');
    Route::post('employee/insert', [EmployeeInfoController::class, 'insert'])->name('employee-insert');
    Route::get('employee/job-approve', [EmployeeInfoController::class, 'loadNewEmployeeApprovalPendingListWithUI'])->name('employee.new.employee.job.approval.ui');
    Route::delete('/employee/delete/{id}', [EmployeeInfoController::class, 'destroy'])->name('employee.destroy'); // before approval delete salary details and employee info table


    Route::post('employee/job-status-approved', [EmployeeInfoController::class, 'approvalOfNewInsertedEmployees'])->name('new-employee-job-status-approved');
    Route::get('employee/add-salary/detalis/{insert}', [EmployeeInfoController::class, 'addSalaryDetails'])->name('add-salary-info');
    Route::get('pre-release/employee/status/{id}', [EmployeeInfoController::class, 'preReleaseUpdateStatus'])->name('pre.releaseListemployee-status');


    /* ajax request */

    /* =============== Check Employee Id, Iqama and Passport Number For Ignore Duplicate =============== */
    Route::post('check/employee/unique-id', [EmployeeInfoController::class, 'checkEmployeeUniqueInformationBeforeAddNewEmployee'])->name('checked-employee.id');
    Route::get('search/new-employee/unique-emp-id', [EmployeeInfoController::class, 'searchNextNewEmployeeUniqueID'])->name('search.new.employee.unique.employee.id');

    // rashed
    Route::post('search-employee/for/update', [EmployeeInfoController::class, 'findEmployeeForUpdate'])->name('search.employee-for-update');
    Route::get('division-get/ajax/{country_id}', [EmployeeInfoController::class, 'getDivision']);
    Route::get('/employee/category/ajax/{emp_type_id}', [EmployeeInfoController::class, 'getEmpCategory']);
    /* ajax request */


    Route::post('employee/all/information/update', [EmployeeInfoController::class, 'updateEmployeeAllInformation'])->name('employee-all-information-update');

    Route::post('employee/image/update', [EmployeeInfoController::class, 'updateEmployeeUploadedFileImage'])->name('employee-image.update');
    // update emp job status and activity only
    Route::post('search/employee/status/update', [EmployeeInfoController::class, 'updateAnEmployeeJobStatus'])->name('search.employee.status.update');
    Route::post('search/employee/project/update', [EmployeeInfoController::class, 'udpateEmployeeWorkingProject'])->name('search.employee.project.update');
    // update emp sponsor name
    Route::post('search/employee/sponsor/update', [EmployeeInfoController::class, 'udpateEmployeeSponsorInformations'])->name('employee.sponsor.info.update');
    Route::post('search/employee/designation/update', [EmployeeInfoController::class, 'updateAnEmployeeDesignationAndMultipleTradeExpertness'])->name('search.employee.disignation.update');
    Route::post('search/employee/agency/emp-agency-update', [EmployeeInfoController::class, 'updateEmployeeAgencyInformation'])->name('search.employee.agency.emp-agency-update');
    Route::post('search/employee/company/emp-company-update', [EmployeeInfoController::class, 'updateEmployeeCompanyInformation'])->name('search.employee.company.update');
    Route::post('search/employee/emp-iqama-update', [EmployeeInfoController::class, 'updateEmployeeIqamaInformations'])->name('search.employee.iqama.update');
    Route::post('search/employee/emp-accomodation-update', [EmployeeInfoController::class, 'updateEmployeeAccomodationInformations'])->name('search.employee.accomodation-info.update');
    // Employee Performance Rating
    Route::post('search/employee/emp-work-rating/update', [EmployeeInfoController::class, 'updateEmployeeWorkRatingInformations'])->name('search.employee.update.work-rating-info');
    Route::post('/search/employee/activity-remarks/update', [EmployeeInfoController::class, 'updateEmployeeActivityRemarks'])->name('employee.activity.remarks.update');

    /* ============================= Employee Summary ============================= */

    /* ========== Employee Working Shift Update ========== */

    Route::get('employee/work/shift-status/update-ui', [EmployeeInfoController::class, 'loadEmployeeWorkingShiftStatusUpdateUI'])->name('employee.shift-status-update-ui');
    // Multiple Employee WOrking Shift Update Request
    Route::post('/employee/shift-status/update-request', [EmployeeInfoController::class, 'employeeShiftStatusUpdateRequest'])->name('employee.shift-status-update');
    Route::post('employee/list/project-wise/for-working-shift-status', [EmployeeInfoController::class, 'getProjectWiseActiveEmployeeListForEmployeeShiftStatusAjaxRequest'])->name('employee.list.project.wise.forworking.shift.status');
    // Single Employee WOrk Status Update
    Route::post('employee/work-shifting/change-request', [EmployeeInfoController::class, 'updateEmployeeWorkShiftingDataChangeRequest'])->name('update-employee-work-shifting');

    /* ------------------ Employee Bank Details ---------------------------------------  */

    Route::post('employee/bank/insert-new-bank', [EmployeeBankDetailsController::class, 'storeNewBankName'])->name('employee.bank.insert.new.bank');
    Route::get('employee/bank-details/user-interface', [EmployeeBankDetailsController::class, 'loadEmployeeBankDetailsView'])->name('employee.bank.details.ui.load');
    Route::post('employee/bank-details/insert-new-record', [EmployeeBankDetailsController::class, 'storeEmployeeBankDetailsInformation'])->name('employee.bank.details.insert.request');
    // get bank info for update
    Route::get('employee/bank-details/bank-info', [EmployeeBankDetailsController::class, 'searchAnEmployeeBankInformation'])->name('employee.bank.details.searching');
    // update employee salary payment method
    Route::post('employee/salary/payment-method/update', [EmployeeBankDetailsController::class, 'updateEmployeeSalaryPaymentMethod'])->name('employee.salary.payment.method.update');

    // Employee List those have bank information
    Route::get('employee/emp-bank/emp-list-with-bank-info', [EmployeeBankDetailsController::class, 'showListOfEmployeeWithBankInformationReport'])->name('employee.bank.list.of.employees.report');


    /* ============================= EmployeeInfoController Report related works shifted into EmployeeReportController ============================= */

    Route::get('employee/salary/summary', [EmployeeReportController::class, 'loadEmployeeSalaryReportProcessUI'])->name('employee-salary.summary');
    Route::post('empId-wise/employee/salary/summary', [EmployeeReportController::class, 'createAnEmployeeSalarySummaryReport'])->name('employee-salary.summary-process');
    // 2 HR Report
    Route::post('company/employee/list/summary/report', [EmployeeReportController::class, 'processAndShowProjectwiseEmployeeSummaryReport'])->name('hr.employee.summary.report');


    // Employee List Form Salary Month
    Route::post('salary-month/project/wise/employee/list/process', [EmployeeReportController::class, 'showEmployeeListReportWithSalaryMonthAndProjectWiseEmployeeReport'])->name('salary-month.project-wise.employee.process');
    Route::post('project/wise/employee/list/process', [EmployeeReportController::class, 'projectWiseEmployeeListProcess'])->name('project-wise.employee.process');

    // HR Related EMployee Report process form
    Route::get('employee/hr/report/process-form', [EmployeeReportController::class, 'loadHRRelatedEmployeeReportForm'])->name('hr.employee.report.process.form');

    //1 Multiple EmpID base Employee Details Report
    Route::post('company/employee/multiple/employee/id/report', [EmployeeReportController::class, 'ProcessAndShowMultipleIDBaseEmployeeDetailsReport'])->name('employee.details.multiple.employee.id.report');

    // 3 Employee Designation Head wise report
    Route::post('hr/employee/report/designation-head/wise', [EmployeeReportController::class, 'showEmployeeDesignationWiseEmployeeReport'])->name('hr.employee.designation.head.wise.report');
    // 3.2 employee last activity report using duration
    Route::post('employee/hr/last-activity-details', [EmployeeReportController::class, 'processAndShowEmployeeLastActivityDetailsReport'])->name('hr.employee.last.activity.report');



    // 4 HR Employee List with File Download
    Route::post('employee/hr/report/process', [EmployeeReportController::class, 'processHRRelatedEmployeeReport'])->name('hr.employee.report.process');
    // 4 HR Download Iqama and passport File
    Route::get('employee/iqama-file-download/{id}', [EmployeeReportController::class, 'downloadEmployeeIqamaFile'])->name('employee.iqama.file.download.request');
    Route::get('employee/passport-file-download/{id}', [EmployeeReportController::class, 'downloadEmployeePassportFile'])->name('employee.passport.file.download.request');

    Route::post('employee/hr/company-villa-wise/employees-report/process', [EmployeeReportController::class, 'processHRRelatedCompanyVillaWiseEmployeeReport'])->name('hr.villa_wise_employees.report');
    Route::post('project/employee-type/wise/list/process', [EmployeeReportController::class, 'projectAndEmployeTypeWiseEmployeeListReportRequest'])->name('employe.list.projec.and.type.wise.process');
    // 5. Employee Report by Iqama Expiration
    Route::post('project/wise/employee/list/byiqamaexpire/process', [EmployeeReportController::class, 'projectWiseEmployeeListWithIqamaExpiredateProcess'])->name('employee-list-projectwise-by-iqama-expire-date');
    // Date Wise New Employee Insert Report
    Route::post('company/new-employee/insert-list/date-to-date', [EmployeeReportController::class, 'getAllNewEmployeeInsertListDetailsInfoByDateToDateReport'])->name('employee.list.new-emp-insert-details.by-date-to-date');
    // 9. employee type and sponsor base report
    Route::post('employee/hr/report/employee-type-wise', [EmployeeReportController::class, 'showProjectWiseEmployeeTypeHRReport'])->name('hr.employee.typewise.report.process');
    // 10 sponsor base emp summary report
    Route::post('employee/hr/summary/report/sponsor-emp-summary-report', [EmployeeReportController::class, 'processAndDisplaySponsorBasaeEmployeeSummaryReport'])->name('hr.employee.sponsor.base.emp.summary.report');

    // Employee TUV Informations Report
    Route::post('employee/hr/employee-tuv/certificate-informations-report/process', [EmployeeReportController::class, 'getAllEmployeeTUVCertificateInformationsReport'])->name('hr.report.employee_tuv_infos');

    // Single Employee Details Report From Employee Search
    Route::get('/anemployee-details-info/print-preview', [EmployeeReportController::class, 'getAnEmployeeDetailsInformationsForPrintPreview'])->name('anemployee.details_info.print.privew');
    // An Employee Activities Details Report
    Route::get('/anemployee/activities/details-report/{empid}', [EmployeeReportController::class, 'getAnEmployeeActivitiesDetailsInformation'])->name('anemployee.activities.details.report');
    //8 Vacation/Final Exit Employees Report
    Route::post('employee/report/activity-report', [EmployeeReportController::class, 'showEmployeeActivityReport'])->name('employee.activity.report');

    // Salary Report Section
    Route::get('salary/report', [SalaryReportController::class, 'loadSalaryReportGenerationForm'])->name('salary.report.generation.form');
    Route::post('salary/report/prevacation', [SalaryReportController::class, 'processAndShowEmployeeIDBaseEmployeeReport'])->name('anemployee.salary.report');
    Route::post('salary/report/closing-report', [SalaryReportController::class, 'processAndShowSalaryClosingReport'])->name('salary.closing.report');
    // 7 Designation Head Salary Report
    Route::post('salary/report/project-cost-controll', [SalaryReportController::class, 'processProjectBaseSalaryReportForCostControll'])->name('salary.report.project.cost.controll');
    // Create AProject Current Emp Salary summary multiple month
    Route::post('salary/report/project/current-working-emp', [SalaryReportController::class, 'processAProjectSponsorsMultiMonthWorkingEmployeeSalaryReport'])->name('salary.report.aproject.sponsor.multi.month');

    // Salary Hold  Employees Salary, Office Staff Salary Report
    Route::post('emp-salary/report/print/preview', [SalaryReportController::class, 'showEmployeeSalarySheetReportPrintPreviewByReportType'])->name('employee.salary.sheet.print_preview.bysalary_type');


    Route::get('salary/reports/process-and-show', [SalaryReportController::class, 'salaryReportsProcessAndShow'])->name('salary.reports.process.and.show');

    /* ============================= EmployeeInfoController Report related works shifted into EmployeeReportController ============================= */


    /* ============================= Project Wise Employee List ============================= */
    Route::get('project/wise/employee/list', [EmployeeInfoController::class, 'projectWiseEmployeeList'])->name('project-wise.employee');


    /* ============================= Employee Activitie Related Work ============================= */
    Route::get('employee/activity/new-activity-form', [EmpActivityController::class, 'loadEmployeeNewActivityInsertForm'])->name('employee.new.activity.insert.form');
    Route::post('employee/activity/new-activity-insert', [EmpActivityController::class, 'employeeNewActivityInsertRequest'])->name('employee.new.activity.insert.request');
    Route::post('employee/activity/new-activity-with-salary-status', [EmpActivityController::class, 'employeeNewActivityWithSalaryStatusUpdateRequest'])->name('employee.activity.salary.status.update.request');


    /* ============================= sponer Wise Employee List ============================= */
    Route::post('sponser/report/process', [SponsorController::class, 'reportProcess'])->name('report-sponser-process');


    /* ============================= Trade Wise Employee List ============================= */

    Route::get('trade/wise/employee/list', [EmployeeInfoController::class, 'tradeWiseEmployeeList'])->name('trade-wise.employee');
    // Route::post('trade/wise/employee/list/process', [EmployeeInfoController::class, 'tradeWiseEmployeeListProcess'])->name('trade-wise.employee.process');



    /* ============================= sponser Wise Employee List ============================= */
    Route::get('sponser/wise/employee/list', [EmployeeInfoController::class, 'sponserWiseEmployeeList'])->name('sponser-wise.employee');
    // Route::post('sponser/wise/employee/list/process', [EmployeeInfoController::class, 'sponserWiseEmployeeListProcess'])->name('sponser-wise.employee.process');
    Route::post('sponser/report/process', [SponsorController::class, 'reportProcess'])->name('report-sponser-process');

    /* ============================= Employe Type (basic Salalry) Employee List ============================= */
    //  Route::get('employee-type/wise/employee/list', [EmployeeInfoController::class, 'getEmployeeTypeWiseReportUIRequest'])->name('employee-type-wise.employee');
    //  Route::post('employee-type/wise/employee/list/process', [EmployeeInfoController::class, 'sponserWiseEmployeeListProcess'])->name('employee-type-wise.employee-list.process');



    /* ========== Employee in-out ========== */
    Route::get('employee/work/entry/in-form', [EmployeeInOutController::class, 'index'])->name('employee-in.time');
    // Project wise Emp List for employee attendance Entry IN
    // getProjectWiseEmployeeListRequest
    Route::post('employee/work/project-wise-emp-list', [EmployeeInOutController::class, 'getListOfEmployeeWorkingInProjectForAttendanceIN'])->name('employee.list.ajax.request.for.attendance.in');

    // edit multi project work record
    Route::get('edit/employee/multiple/in/out/{recordId}', [EmployeeInOutController::class, 'editAnEmployeeMultiProjectWorkRecord'])->name('edit.employee.multiple.project.in-out');
    // delete multiple project work record
    Route::get('delete/employee/multi-project/work/record/{empwh_auto_id}', [EmployeeInOutController::class, 'deleteAnEmployeeMultiProjectWorkRecordRequest'])->name('delete.employee.multiple.project.record.request');

    Route::get('employee/out/time', [EmployeeInOutController::class, 'loadAttendanceOutForm'])->name('employee-out.time');
    // no need now


    // will be delete Route::get('employee/in-out/time/delete/{id}', [EmployeeInOutController::class, 'delete'])->name('emp.attendence.delete');
    Route::get('employee/monthly-work/record/searchui', [EmployeeInOutController::class, 'employeeMultipleProjectWorkRecordSearchUI'])->name('employee.month.work.record.searchui');
    Route::post('employee/monthly-work/record/search', [EmployeeInOutController::class, 'searchEmployeeMultipleProjectWorkRecord'])->name('employee.month.work.record.search');
    Route::post('multiproject-work-record/edit', [EmployeeInOutController::class, 'getAnEmployeeMultiProjectWorkRecord'])->name('edit-multiproject-work-record');

    // Employee Attendance IN OUT record update form
    Route::get('employee/attendance/in-out/edit-ui', [EmployeeInOutController::class, 'employeeAttendanceInOutEditUI'])->name('employee-attendance-in-out-edit-form');
    // Employee Attendance IN OUT record update ajax request
    Route::post('employee/attendance/in-out/update', [EmployeeInOutController::class, 'employeeAttendanceInOutUpdate'])->name('employee-attendance-in-out-update-request');
    // searching attendance IN OUT record by ajax request
    Route::get('employee/attendance/in-out/search', [EmployeeInOutController::class, 'employeeAttendanceInOutRecordSearch'])->name('employee-attendance-in-out-search-request');
    // delete attendance IN OUT record by ajax request
    Route::post('employee/attendance/in-out/delete', [EmployeeInOutController::class, 'employeeAttendanceInOutRecordDelete'])->name('employee-attendance-in-out-delete-request');
    // employee  attendance IN OUT process form
    Route::get('employee/attendance/in-out/process/ui', [EmployeeInOutController::class, 'employeeAttendanceProcessForm'])->name('employee-attendance-process-form');
    // Employee attendance IN OUT  process request
    Route::get('employee/attendance/in-out/process-request', [EmployeeInOutController::class, 'employeeAttendanceProcess'])->name('employee-attendance-process-request');
    //  multiple emp attn searching ajax request
    Route::post('employee/attendance/update/mult-employee', [EmployeeInOutController::class, 'searchMultipleEmpAttendanceRecordForUpdate'])->name('multi.employee.attendance.record.search.ajaxrequest');
    Route::post('employee/attendance/in-out/multi-emp-update', [EmployeeInOutController::class, 'multipleEmployeeAttendanceUpdateRequest'])->name('multi.employee.attendance.inout-update-request');
    // monthly  attendance work record approval by project manager
    Route::get('employee/monthly-attend/work-record/approval-ui', [EmployeeInOutController::class, 'loadMonthlyWorkRecordApprovalUI'])->name('monthly.attendance.approval.ui');
    Route::get('employee/monthly-attend/work-record/approval-request', [EmployeeInOutController::class, 'approveOfMonthlyWorkRecords'])->name('monthly.attendance.approval.request2');
    Route::get('employee/monthly-attend/work-record/search', [EmployeeInOutController::class, 'searchMonthlyWorkRecordForApprovalAJaxRequest'])->name('monthly.attendance.work.record.search');



    // Attendance IN/OUT Report UI
    Route::get('employee/in-out/time/report/generat', [EmployeeInOutController::class, 'employeeAttendanceReportProcessingUI'])->name('employee-entry-out-report');
    // Single Emp Attendance Report
    Route::post('employee/in-out/time/single-emp/report', [EmployeeInOutController::class, 'getAnEmployeeDayByDateMonthlyAttendanceReport'])->name('employee-attendance-single-empl-attendance-report');
    // 2 Employee Monthly working hours Summary or Date to Date Attendance Details report
    Route::post('employee/in-out/time/report/process', [EmployeeInOutController::class, 'employeeAttendanceMonthlyReportProcessAndShow'])->name('employee-entry-out-report-process');
    // attendance report 3.1
    Route::post('employee/attendance/process/daily-attendance-summary', [EmployeeInOutController::class, 'showEmployeeDailyAttendanceSumarryReport'])->name('employee.daily.attendance.summary.process.request');
    //  attendance report 3.2
    Route::post('employee/attendance/process/monthly-attendance-summary', [EmployeeInOutController::class, 'showEmployeeMonthlyAttendanceSumarryReport'])->name('employee.monthly.attendance.summary.process.request');
    Route::post('employee/attendance/daily-manpower-summary-project-wise', [EmployeeInOutController::class, 'showEmpDailyAttendanceManpowerSumarryReport'])->name('employee.daily.attendance.manpower.summary.request');
    // 7 Daily Absent report
    Route::post('employee/attendance/daily-absent-report', [EmployeeInOutController::class, 'processAndShowAbsenceEmployeeDetailsOrAttendanceRecordsDetails'])->name('employee.daily.absent.manpower.report.request');
    // 8 Monthly absent report
    Route::post('employee/attendance/montly-absent-report', [EmployeeInOutController::class, 'showMonthlyAbsentManpowerReportDetails'])->name('employee.monthly.absent.report.request');
    // inout excel download
    Route::post('employee/in-out/report/excel-download', [EmployeeInOutController::class, 'downloadEmployeeAttendanceRecordAsExcel'])->name('employee.in.out.record.excel.download');

    // Projectwise Total Hours Summary
    Route::post('employee/attendance/work-hours/summary', [EmployeeInOutController::class, 'getProjectwiseTotalWorkHoursSummaryReport'])->name('emp.attendance.total.work.hours.summary.report');


    /* ============== Ajax Request ============== */
    Route::post('/employee/working/entry/time', [EmployeeInOutController::class, 'employeeAttendanceTimeINInsertRequest'])->name('employee-entry-time-insert');
    // single emp monthly work record search
    Route::post('/employee/multiple/working/entry/time', [EmployeeInOutController::class, 'insertAnEmployeeMultipleProjectWorkRecord'])->name('employee-multiple-time-insert');
    // multipleInsert previous method
    Route::post('/employee/multiple/working/update/time', [EmployeeInOutController::class, 'EmployeeMultiprojectMonthlyWorkRecordUpdate'])->name('employee-multiple-time-update');
    // update multi project work record
    Route::post('update-employee-work-record-url', [EmployeeInOutController::class, 'updateAnEmployeeMultipleProjectWorkRecordRequest'])->name('update-employee-work-record-submit');


    // Route::post('/employee/multiple/working/record-searching', [EmployeeInOutController::class, 'searchAnEmployeeWorkRecordByMonth'])->name('emp.mult.project.month.work.record.search');

    // update monthly work record
    //  Route::post('month-work/record-search/', [EmployeeInOutController::class, ''])->name('record-search');


    //processEntryList
    Route::post('employee/entry/time/list/process', [EmployeeInOutController::class, 'getAttendanceINAllEmployeeListForAttendaceOutAjaxRequest'])->name('show-employee-out.time');
    Route::post('employee/entry/out/time/list/process', [EmployeeInOutController::class, 'outTimeProcessEntryList'])->name('show-employee-entry.out.time');
    Route::post('employee/project/wise/in/out/time/list/process', [EmployeeInOutController::class, 'projectWiseInOutList'])->name('project.wise-employee-in-out.time');
    // Single Employee Attendance OUT not used now may be delete
    //   Route::post('employee/out/time/update', [EmployeeInOutController::class, 'outTimeInsert'])->name('employee-entry-time-update');
    // Multiple Employee Attendance OUT
    Route::post('employee/attendance/out/multiple-employee', [EmployeeInOutController::class, 'multipleEmployeeAttendanceOutRequest'])->name('employee.attendance.out.multiple.emp');
    /* ============== Ajax Request ============== */

    /* ==================== Employee Iqama Expire ==================== */
    Route::get('employee/iqama-expired', [IqamaExpireController::class, 'index'])->name('iqama-expired');
    Route::get('employee/passport-expired', [PassportExpireController::class, 'index'])->name('passport-expired');
    /* ============== Ajax Request ============== */
    Route::post('/employee/iqama-expired/date', [IqamaExpireController::class, 'expiredDate'])->name('iqama-expired-find');
    /* ============== Ajax Request ============== */




    /* ==================== Leave Application part transfer to HRManagement vue module so will be delete from here 28.6.26==================== */
    Route::get('leave/application/new-form', [LeaveApplicationController::class, 'index'])->name('leave.application.form'); // employee-leave-work
    Route::post('leave/application/new-form', [LeaveApplicationController::class, 'insert'])->name('leave.application.submit.request');
    Route::get('leave/application/pending-application', [LeaveApplicationController::class, 'getLeaveApplicationPendingList'])->name('leave.application.pending.list');
    Route::get('leave/application-form/print-preview', [LeaveApplicationController::class, 'processEmployeeLeaveApplicationFormByRequestedParameter'])->name('process-print-leave-application');
    /* ============== Ajax Request ============== */
    Route::get('leave/application/details', [LeaveApplicationController::class, 'getALeaveApplicationRecord'])->name("a.leave.application.details");
    Route::post('leave/application/update', [LeaveApplicationController::class, 'updateALeaveApplicationRecord'])->name("leave.application.details.update");
    Route::get('leave/application/application-rejection/{id}', [LeaveApplicationController::class, 'rejectALeaveApplication']);

    /* ==================== Report Expenditure ==================== */
    Route::get('company/expense/report/form', [DailyExpenseController::class, 'getDailyExpenseReportForm'])->name('report-expenditure');
    Route::post('company/expenses/report/process', [DailyExpenseController::class, 'processDaileyExpenseReport'])->name('report-expenditure-process');


    /* ==================== Report Founding Source ==================== */
    Route::get('report/founding-source', [IncomeReportController::class, 'index'])->name('report-income');
    Route::post('report/income/process', [IncomeReportController::class, 'process'])->name('report-income-process');




    /* ==================== Employee Promosion ==================== */
    Route::get('employee/promosion', [PromosionController::class, 'index'])->name('employee-promosion');
    /* ==== ajax request ==== */
    Route::post('search-employee/for/promosion', [PromosionController::class, 'findEmployee']);

    Route::post('search-employee/for/details', [PromosionController::class, 'findEmployeeDetails'])->name('search.employee-details');
    /* ==== empl Id, Iqama and Passport Wise Employee Info Details ==== */
    Route::post('search-employee/for/adjustment', [PromosionController::class, 'findEmployeeadjustment'])->name('search.employee-adjustment');
    Route::post('search-employee/for/status', [PromosionController::class, 'findEmployeeStatus'])->name('search.employee-status');
    /* ========= Employee Salary Currection ========= */
    // Route::post('search-employee/for/salary/currection', [PromosionController::class, 'findEmployeeForSalaryCurrection'])->name('search.employee-salary-for.currection');
    /* ==== ajax request ==== */
    Route::post('employee/promosion/submit', [PromosionController::class, 'insertPromosion'])->name('employee-promosion.submit');
    Route::get('employee/promoted/employee/searching', [PromosionController::class, 'getPromotedEmployeesWaitingForApprovalRecords'])->name('promoted.employees.waiting.forapproval.records');
    Route::post('employee/promoted/employee/approval-request', [PromosionController::class, 'approvalRequestOfPromotedEmployees'])->name('promoted.employees.approval.request');
    Route::post('employee/promoted/employee/paper-upload-request', [PromosionController::class, 'promotedEmployeePromotionPaperUploadRequest'])->name('promoted.employees.paper.upload.request');
    // Delete Promotion Record
    Route::get('employee/promoted/record/delete', [PromosionController::class, 'deleteAPromotionRecordInsertedRecord'])->name('promotion.record.delete');

    /* ========= Employee (From Date to Today) Date Promotion Report ========= */
    Route::GET('employee/promotion/details/date-to-date/report', [PromosionController::class, 'showEmployeePromotionDetailsDateToDateReport'])->name('employee.promotion.details.date.to.date.report');
    // upload work record from excel file
    Route::post('employee/promotions/import-file', [PromosionController::class, 'importEmployeePromotionsFromExcel'])->name('import.employee.promotions.excel');
    Route::post('employee/promotions/import-file-submitted', [PromosionController::class, 'saveEmployeePromotionRecordsImportFromExcel'])->name('submit.employee.promotions.imported.excel');




    /* ==================== Income Source ==================== */
    Route::get('company/income/source', [IncomeSourceController::class, 'index'])->name('income-source');
    Route::get('company/income/list', [IncomeSourceController::class, 'list'])->name('income-list');
    Route::get('company/income/{inc_id}/edit', [IncomeSourceController::class, 'edit'])->name('edit.income');
    Route::get('company/income/{inc_id}/approve', [IncomeSourceController::class, 'approve'])->name('income-approve');
    Route::get('company/income/{inc_id}/remove', [IncomeSourceController::class, 'delete'])->name('remove.income');
    Route::post('company/income/source/insert', [IncomeSourceController::class, 'insert'])->name('insert.income-source');
    Route::post('company/income/source/update', [IncomeSourceController::class, 'update'])->name('update.income-source');
    Route::post('company/income/source/update', [IncomeSourceController::class, 'update'])->name('update.income-source');
    Route::get('income-expense/report', [IncomeSourceController::class, 'incomeExpenseReportUI'])->name('income-expense-report-ui');
    Route::post('income-expense/report/form/submit', [IncomeSourceController::class, 'incomeExpenseReportProcess'])->name('income-expense-report-process-form-submit');

    /* ========== Comapny Daily Cost Type ========== */
    Route::get('cost/type', [CostTypeController::class, 'create'])->name('cost-type');
    Route::get('cost/type/{cost_type_id}/edit', [CostTypeController::class, 'edit'])->name('edit-cost-type');
    Route::post('cost/type/insert', [CostTypeController::class, 'insert'])->name('insert-cost-type');
    Route::post('cost/type/update', [CostTypeController::class, 'update'])->name('update-cost-type');


    /* ========== Comapny Daily Expense History ========== */
    Route::get('company/daily-expense/create', [DailyExpenseController::class, 'dailyNewExpenseCreateForm'])->name('company.daily.new.expesne.form');
    Route::get('company/daily-expense/edit/{cost_id}', [DailyExpenseController::class, 'dailyNewExpenseEditForm'])->name('company.daily.expesne.edit.form');
    Route::get('company/daily-expense/delete/{cost_id}', [DailyExpenseController::class, 'dailyNewExpenseDeleteRequest'])->name('company.daily.expesne.delete.request');

    Route::post('company/daily-expense/insert-request', [DailyExpenseController::class, 'dailyNewExpenseInsertRequest'])->name('company.daily.new.expesne.insert.request');
    Route::post('company/daily-expense/update-request', [DailyExpenseController::class, 'dailyExpenseUpdateRequest'])->name('company.daily.new.expesne.update.request');
    Route::get('company/daily-expense/expense-list', [DailyExpenseController::class, 'dailyExpensesApprovalForm'])->name('company.daily.expesne.approval-pending.list');
    Route::get('company/daily-expense/approval-request/{id}', [DailyExpenseController::class, 'dailyExpenseApprovalRequest'])->name('company.daily.new.expesne.approval.request');


    // Petty Cash Controll
    Route::GET('daily-transaction/form', [DailyExpenseController::class, 'dailyPettyCashTransactionForm'])->name('company.daily.transaction.form');
    Route::POST('daily-transaction/store', [DailyExpenseController::class, 'storeDailyTransactionNewExpense'])->name('company.daily.transaction.expesne.store');
    Route::GET('daily-transaction/debit-record/edit', [DailyExpenseController::class, 'searchDailyDebitCreditTransactionInvoiceForEditing'])->name('company.daily.transaction.edit');
    Route::POST('daily-transaction/cash-receive', [DailyExpenseController::class, 'storeDailyTransactionCashReceive'])->name('company.daily.transaction.cash.receive');
    // cash receive report
    Route::POST('bank-transaction/cash-receive', [DailyExpenseController::class, 'bankTransactionCashReceive'])->name('company.daily.transaction.bank');
    //  Route::GET('company/daily-transaction/credit-record/edit', [DailyExpenseController::class, 'searchDailyCachInvoiceForEditing'])->name('company.daily.transaction.credit.edit');
    Route::post('daily-transaction/delete', [DailyExpenseController::class, 'deleteDailyTransactionRecord'])->name('company.daily.transaction.delete');
    Route::GET('daily-transaction/searching', [DailyExpenseController::class, 'searchDailyTransactionRecords'])->name('company.daily.transaction.searching');
    Route::POST('daily-transaction/transaction-report', [DailyExpenseController::class, 'processAndShowDailyTransactionReport'])->name('company.daily.transaction.report');

    Route::POST('daily-transaction/invoice-date-by-date', [DailyExpenseController::class, 'processAndShowDrInvoiceDateToDateTransSummaryReport'])->name('company.datebydate.trans.summary.report');
    Route::POST('daily-transaction/expense-date-by-date-report', [DailyExpenseController::class, 'processAndShowDateToDateDrInvoiceHeadbaseSummaryReport'])->name('daily.transaction.datebydate.summary.report');


    /* ==========Employee Iqama Renewal  ========== */
    // Iqama Anual Cost Insert Form
    Route::get('update/iqama-anual/fee', [AnualFeeDetailsController::class, 'index'])->name('anual-fee.details');
    // Iqama Anual Cost update FORM
    Route::get('edit/iqama-anual/fee/{IqamaRenewId}', [AnualFeeDetailsController::class, 'edit'])->name('edit-iqamarenewal-fee');
    // Iqama Anual Cost Delete before approval
    Route::get('delete/iqama-anual/fee/{IqamaRenewId}', [AnualFeeDetailsController::class, 'deleteAnEmployeeIqamaAnualExpenseBeforeApproval'])->name('anemp.iqama.renewal.expense.delete.before.approval');

    // Iqama Anual Cost Insert request submit
    Route::post('insert/iqama-anual/fee/for-employee', [AnualFeeDetailsController::class, 'insert'])->name('insert-iqamarenewal-fee');
    // Iqama Anual Cost update request
    Route::post('update/iqama-anual/fee/for-employee', [AnualFeeDetailsController::class, 'update'])->name('update-iqamarenewal-fee');
    Route::post('employee/iqama-renewal/expense/search', [AnualFeeDetailsController::class, 'searchAnEmployeeIqamaRenewalExpenseRecordsAJAXRequest'])->name('iqama.renewal.expense.record.search.ajax');
    Route::get('employee/iqama-renewal/expense/search-ui', [AnualFeeDetailsController::class, 'getIqamaExpenseRecordSearchingUI'])->name('iqama.renewal.expense.record.search.ui');
    // Iqama Pending Approval Request Shown at approval menu
    Route::get('pending/iqama-anual/fees-show', [AnualFeeDetailsController::class, 'getAllPendingIqamaExpenseRecordForApproved'])->name('show-pending-iqamarenewal-fee');
    Route::get('/iqama-anual/fee-approved/for-employee_id/{id}', [AnualFeeDetailsController::class, 'approveOfIqamaRenewalExpenseRecord'])->name('pending-iqamarenewal-fee-approved');
    Route::get('/iqama-anual/fee-details/view-for-employee_id/{id}', [AnualFeeDetailsController::class, 'getPendingIqamaExpenseRecordForSingleEmployee'])->name('pending-iqamarenewal-fee-view');
    Route::get('iqama-renewal/expense/approval/pending/search/', [AnualFeeDetailsController::class, 'searchAnEmployeeIqamaRenewalExpenseApprovalPendingRecordsAJAXRequest'])->name('anemp.iqama.renewal.expense.aproval.pending.records');
    // multiple approval
    Route::post('/iqama-anual/fee-approved/multi-employee_id', [AnualFeeDetailsController::class, ''])->name('pending-iqamarenewal-fee-multiemp.approved');


    // iqama expire date update
    Route::POST('employee/iqama-renewal/update/upload-preview-request', [AnualFeeDetailsController::class, 'uploadIqamaRenewalExpenseExcelFileWithPreview'])->name('iqama.renewal.expense.upload.preview.request');
    Route::GET('employee/iqama-renewal/update/upload-update-request', [AnualFeeDetailsController::class, 'storeIqamaRenewalExpenseExcelImportedRecordsInMainTable'])->name('iqama.renewal.expense.upload.update.request');

    // Report (Date wise)
    Route::get('employee/renewal/fees-report-process/ui', [AnualFeeDetailsController::class, 'loadEmployeeRenewalExpenseReportProcessForm'])->name('employee.renewal.expense.report.process.form');
    Route::post('pending/iqama-anual/fees-report-process', [AnualFeeDetailsController::class, 'processEmployeeIqamaRenewalExpenseDateByDateReport'])->name('emp.iqama.renewal.report.date.to.date');
    //  Route::get('project/wise/iqama/renewal/report', [AnualFeeDetailsController::class, 'projectWiseIqamaRenewal'])->name('project.wise-employee-iqama-renewal');
    Route::post('report/project/wise-employee/iqama/renewal/process', [AnualFeeDetailsController::class, 'projectAndSponsorWiseIqamaReport'])->name('project-wise-iqwama-renewal-process');


    /* ==================== Advance Payment ==================== */

    // will be delete soon
    // Route::get('employee/advance/payment', [AdvancePayController::class, 'index'])->name('addvance.payment');
    // Route::get('employee/advance/payment/edit/{adv_pay_id}', [AdvancePayController::class, 'edit'])->name('edit-advance.pay');
    // Route::get('employee/advance/payment/delete/{adv_pay_id}', [AdvancePayController::class, 'delete'])->name('delete-advance.pay');
    // Route::post('employee/advance/payment/update', [AdvancePayController::class, 'updateEmployeeAdvanceInformation1'])->name('update-advance.pay');
    // Route::post('employee/advance/payment/insert', [AdvancePayController::class, 'insert'])->name('insert-advance.pay');
    // Route::post('employee/advance/payment/multiple-emp-advance/insert', [AdvancePayController::class, 'multipleEmployeeAdvanceInsertRequest'])->name('multiple.emp.advance.insert.request');
    // searching employee for multiple emp advance insert form
    // Route::post('employee/advance/payment/project-wise-emp-list', [AdvancePayController::class, 'getEmployeeListForMultipleEmployeeAdvancePayment'])->name('employee.advance.employee.list.foradvance.ajax.request');

    // all above route use now using vue js system

    // Advacne Processing UI & Request
    Route::get('employee/advance/processing-ui', [AdvancePayController::class, 'employeeAdvanceProcessingUI'])->name('addvance.processing.ui');
    Route::post('employee/advance/processing-submit', [AdvancePayController::class, 'employeeAdvanceProcessingRequest'])->name('addvance.processing.request');
    Route::post('employee/advance/list-search', [AdvancePayController::class, 'employeeAdvanceListSearch'])->name('employee.advance.list.search');

    Route::get('employee/advance/report-process-ui', [AdvancePayController::class, 'loadEmployeeAdvanceEntryReportProcessForm'])->name('addvance.report.process.form');
    // Route::post('employee/advance/advance-report', [AdvancePayController::class, 'employeeAdvanceSummaryReport'])->name('addvance.report.process');
    Route::post('employee/advance/advance-report-process', [AdvancePayController::class, 'employeeAdvanceSummaryReportProcess'])->name('emp.addvance.report.process');
    // Single Employee Advance Records Report From Employee Advance Report
    Route::get('single-employee-advance-record/report/process', [AdvancePayController::class, 'singleEmployeeAdvanceListSearchForReport'])->name('single.employee.advance.records.report');
    Route::post('employee/advance/report/advance-inserted-user', [AdvancePayController::class, 'processEmployeeAdvanceInsertedByUserReport'])->name('employee.advance.inserted.byuser.report');
    // Advance Paper Upload
    Route::post('advance/paper/upload-request', [AdvancePayController::class, 'uploadAdvancePaperToServer'])->name('advance.paper.upload.request');
    Route::post('advance/paper/upload-for-muultiple-emp', [AdvancePayController::class, 'searchAdvanceInsertedEmployeesForUploadAdvancePaper'])->name('advance.paper.upload.formult.employee');


    /* ==================== Advance Adjust ==================== */
    Route::get('employee/advance/adjust/payment', [AdvancePayController::class, 'employeeMonthlyPaymentSetting'])->name('advance-pay.adjust'); // return advance setting UI
    // update emp advance payment setting
    Route::post('employee/advance/adjust/payment/update', [AdvancePayController::class, 'updateAdvanceInstallAmount'])->name('update.advance-installAmount');

    Route::get('employee/search/for/cash/depoosit', [AdvancePayController::class, 'emplpyeeCashDepositFormRequest'])->name('employee.search.with.cash-payment');
    Route::get('employee/search/for/cash/depoosit-with-allrecords', [AdvancePayController::class, 'emplpyeeCashDepositFormRequestWithAllRecords'])->name('employee.cash.receive.allrecords.with.cash-payment');
    Route::post('employee/advance/payment/cash-receive', [AdvancePayController::class, 'advancePaymentReceivedFromEmployee'])->name('employee-advance-payment-cash-receive');
    Route::get('employee/advance/payment/receive-delete/{id}', [AdvancePayController::class, 'deleteCashDepositAdvancePayment'])->name('delete-employee-advance-payment-cash-receive');
    Route::get('employee/cash-payment/record-search/{id}', [AdvancePayController::class, 'searchARecordOfCashReceivedFromEmployee'])->name('search-employee-cash-payment-record');

    /* ========== Ajax Request ========== */
    // Employee Searching for Advance Install Amount Setting
    Route::post('find-employee/for/advance/adjust/edit', [AdvancePayController::class, 'findanEmployeeWithAdvanceSetting'])->name('findEmployeeForLoan');



    /* ==================== Salary Generat ==================== */
    Route::get('employee/monthly/salary', [SallaryGenerateController::class, 'create'])->name('salary-processing-for-month');



    Route::get('employee/monthly/salary/generat', [SallaryGenerateController::class, 'index'])->name('salary-generat');
    // 0 AJAX CALL FOR SALARY GENERATE
    Route::post('employee/monthly/salary/store/salary-process', [SallaryGenerateController::class, 'employeeSalaryProcessAJAXRequest'])->name('employee-salary-process-request');
    // 1 Processing Salary for an Employee
    Route::post('single-mployee/monthly/salary/report/', [SallaryGenerateController::class, 'singleEmployeeMonthWiseSalaryReport'])->name('single-employee-salary-generat');
    //2 Projet & Employee Status base Salary Report
    Route::post('project/employee-status/monthly/salary/report/', [SallaryGenerateController::class, 'projectEmployeeStatusMonthWiseSalaryReport'])->name('project-month-empStatus.wise-salary');
    // 3 Projet & Employee type base Salary Report
    Route::post('project/employee/monthly/salary/report/', [SallaryGenerateController::class, 'projectEmployeeMonthWiseSalaryReport'])->name('project-month-empType.wise-salary');

    // 3.1
    Route::post('salary/employee/monthly/salary/report/', [SallaryGenerateController::class, 'showSalaryReportProjectAndEmpTradeBase'])->name('salary.report.project.and.trade');

    //4 Salary Report By Sponsor
    Route::post('sponsor-wise/monthly/salary/report/', [SallaryGenerateController::class, 'sponserWiseMonthlySalaryReport'])->name('sponser-month.wise-salary-report');
    // 5 Salary Report For Saudi By Sponsor
    Route::post('sponser-wise/monthly/salary/saudi/report/', [SallaryGenerateController::class, 'getSponserWiseMonthlySalaryReportForSaudi'])->name('sponser.salary.month.saudi.salary.report');
    // 6 All Employees Salary Report
    Route::post('all/employee/monthly/salary/generat/without-project/employee-type', [SallaryGenerateController::class, 'monthWiseSalary'])->name('all-emp.salary-without-project-emp-type');

    // 7 Monthly Paid & Unpaid Salary Summary
    Route::post('month/year-wise/salary/summary/', [SallaryGenerateController::class, 'monthAndYearWiseSalarySummary'])->name('year-month.wise-salary-summary');
    // 8 Project wise Total Working Hours and Total Salary
    Route::post('salary/report/project-wise-toalsalary-totalworking-hours', [SallaryGenerateController::class, 'projectwiseTotalWorkingHoursAndTotalSalary'])->name('project-wise-Total.Working.Hours-And-Total.Salary');
    // 9 Basic & Hourly Employees Salary Summary
    Route::post('salary/report/project-wise-basic/hourly-salary-summary', [SallaryGenerateController::class, 'projectwiseBasicAndHourlyEmployeeSalarySummary'])->name('project-wise.basic.hourly.emp.salary.sumamry');
    //10 Multiple Emplyees ID Salary
    Route::post('month/year-wise/salary/multipleid/', [SallaryGenerateController::class, 'multipleEmployeeIdBaseSalaryProcess'])->name('multiple-empidbase-salary-process');
    // 11 Sponsor employees salary summary report
    Route::post('salary/report/sponsor-wise/salary-summary-report', [SallaryGenerateController::class, 'processSponsorSalarySummaryReport'])->name('sponsor.wise.salary.sumamry.report');
    // 12 Salary Paid By Bank
    Route::post('salary/report/paid-by-bank/employees-salary', [SallaryGenerateController::class, 'processSalaryPaidByBankEmployeesListSalaryReport'])->name('salary.report.paid.by.bank');
    // 13 Salary Update as Paid by Date and Who did Report
    Route::post('salary/report/salary-paid/by-date', [SallaryGenerateController::class, 'processSalaryAsPaidByByDateAndWhoDidItSalaryReport'])->name('salary.report.paid.by.date.who.did.it');


    // Salary Pending
    Route::get('employee/salary/pending', [SallaryGenerateController::class, 'loadSalaryPendingEmployeeListWithUI'])->name('salary-pending');
    Route::post('employee/salary/pending/list', [SallaryGenerateController::class, 'SalaryPendingList'])->name('salary-pending.list');
    Route::post('employee/salary/records-search-by-salary-history-id', [SallaryGenerateController::class, 'getAnEmployeeSalaryRecordBySalaryHistoryAutoId'])->name('get.amemployee.unpaid.salary.record.byslh.autoid');
    Route::post('employee/salary/record-update-request', [SallaryGenerateController::class, 'updateAnEmployeeSalaryRecordBySalaryHistoryAutoId'])->name('employee.salary.update.request');
    Route::delete('employee/salary/delete/{id}', [SallaryGenerateController::class, 'deleteAnEmployeeUnpaidSalaryRecord'])->name('employee.unpaid.salary.record.delete');


    // Salary Paid
    Route::get('employee/salary/paid', [SallaryGenerateController::class, 'Salarypaid'])->name('salary-paid');
    Route::post('employee/salary/paid/list', [SallaryGenerateController::class, 'SalarypaidList'])->name('salary-paid.list');

    // salary sheet transfer to module so may be delete soon 14.5.26
    // Route::get('employee/salary/sheet', [SallaryGenerateController::class, 'loadSalarySheetUploadUI'])->name('salary-sheet');
    // Route::get('employee/salary/sheet/delete/{id}', [SallaryGenerateController::class, 'deleteUploadedSalarySheet'])->name('salary-sheet.delete');
    // Route::post('employee/salary/sheet', [SallaryGenerateController::class, 'uploadEmpSalarySheet'])->name('salary-sheet.store');
    // Route::post('employee/salary/sheet/update', [SallaryGenerateController::class, 'updateSalarySheet'])->name('salary-sheet.update');
    // Route::post('employee/salary/sheet/search', [SallaryGenerateController::class, 'searchSalarySheets'])->name('salary-sheet.search');
    // Route::get('employee/salary/sheet/all', [SallaryGenerateController::class, 'getAllSalarySheets'])->name('salary-sheet.all');

    // Employee Mobile Bill upload   may be mobile bill also transfer
    Route::POST('/employee/mobile-bill/information/manage', [SallaryGenerateController::class, 'manageEmployeeMobileBillRelatedInformation'])->name('employee.mobile.bill.upload');
    Route::GET('/employee/mobile-bill/paper/search', [SallaryGenerateController::class, 'searchMobileBillPaymnetPaperInformation'])->name('search.employee.mobile.bill');


    // Salary Payment

    Route::post('employee/salary/payment', [SallaryGenerateController::class, 'SalaryPayment'])->name('payment.salary');
    Route::post('employee/salary/payment/undo', [SallaryGenerateController::class, 'SalaryPaymentToUnPay'])->name('payment.salary.undo');



    Route::post('employee/monthly/salary/add/history', [SallaryGenerateController::class, 'addSalaryHistory'])->name('add-salary-history');
    /* add employee salary in salary history */
    Route::post('single-employee/monthly/salary/add/history', [SallaryGenerateController::class, 'addSignleEmployeeSalaryHistory'])->name('add-salary-history.single');
    Route::get('pdf', [SallaryGenerateController::class, 'Pdf']);

    Route::get('employee/monthly/salary/generat/for/pdf', [SallaryGenerateController::class, 'downloadPdf'])->name('download.pdf-salary');

    // Employee Bonus
    Route::get('employee/salary/bonus/ui', [EmployeeBonusController::class, 'index'])->name('employee.salary.bonus');
    Route::get('employee/salary/bonus/search', [EmployeeBonusController::class, 'getAnEmployeeBonusSalaryRecords'])->name('employee.salary.bonus.search');
    Route::post('employee/salary/bonus/new-insert', [EmployeeBonusController::class, 'storeNewEmployeeBonusSalaryInformation'])->name('employee.salary.bonus.insert.request');
    Route::delete('employee/salary/bonus/delete/{id}', [EmployeeBonusController::class, 'deleteAnEmployeeBonusSalaryRecordByBonusId'])->name('employee.salary.bonus.delete.request');
    // from bonus page
    Route::get('employee/salary/bonus/sheet-print/{id}', [EmployeeBonusController::class, 'createBonusSalarySheet'])->name('employee.salary.bonus.paper.create');
    // from work record page
    Route::get('employee-multi-project-work-history-report-print', [EmployeeInOutController::class, 'createMultiProjectWorkHistorySheet'])->name('employee.multi.project.work.history.report.create');
    // Salary Details History Report from work record page
    Route::get('employee-salary-details-history-report-print', [EmployeeInOutController::class, 'createSalaryDetailsHistoryReport'])->name('employee.salary.details.history.report.create');
    // Employee Bonus Report from salary report page
    Route::post('/employee/bonus-report', [EmployeeBonusController::class, 'processEmployeeBonusDetailsReport'])->name('employee.bonus.details.report');

    // ==================== Month Work History ====================
    Route::get('add/month/work/history', [DailyWorkHistoryController::class, 'index'])->name('add-daily-work');
    Route::get('delete/work/history/{id}', [DailyWorkHistoryController::class, 'deleteDailyWorkHistory'])->name('delete-daily-work-history');
    Route::post('add/month/work/history/store', [DailyWorkHistoryController::class, 'store'])->name('store.monthly-work-history');
    Route::get('month/work/history/for-report', [DailyWorkHistoryController::class, 'getEmployeMonthlyWorkHistoryRecordReportUI'])->name('monthwork-reportprocess');
    Route::post('month/work/history/for-report/process', [DailyWorkHistoryController::class, 'getEmployeMonthlyWorkHistoryProcess'])->name('project-wise-employe.month-work.record.report');
    Route::post('employee/month/work/status/summary-report', [DailyWorkHistoryController::class, 'processAllEmployeMonthlyWorkStatus'])->name('all-employee-work-status-summary');
    Route::post('daily-work-history/employee-notin-work-record', [DailyWorkHistoryController::class, 'processEmployeNotPresentInMonthlyWorkHistory'])->name('work-history-employee-notin-work-record');
    Route::get('edit/month/work/history/{id}', [DailyWorkHistoryController::class, 'edit'])->name('edit.month-work');
    Route::get('delete/month/{day_work_id}/work/history', [DailyWorkHistoryController::class, 'delete'])->name('delete.daily-work');

    // Multiple Employee Monthly Records now not used 22.9.25
    // Route::get('month/work/record/insert-ui', [DailyWorkHistoryController::class, 'getMultipleEmpMonthlyRecordInsertForm'])->name('multiple.emp.monthly.records.form');
    // now not used 22.9.25
    // Route::post('multi-employee/month-work/project-wise-emp-list', [DailyWorkHistoryController::class, 'projectWiseEmployeeListRequestForMultipleEmpWorkRecordInsert'])->name('employee.month.work-project-wise-employees-request');
    // Route::post('multi-employee/month-work/insert', [DailyWorkHistoryController::class, 'multiEmployeMonthRecordInsertFormSubmit'])->name('multi-employe-month-record-insert-form-submit');
    // upload work record from excel file
    Route::post('add/month/work/history/import-file', [DailyWorkHistoryController::class, 'importEmployeeMonthlyWorkRecordsFromExcel'])->name('import.monthly.work.history.excell');
    Route::post('add/month/work/history/import-file-submitted', [DailyWorkHistoryController::class, 'submitEmployeeMonthlyWorkRecordsImportFromExcel'])->name('submit.monthly.work.history.imported.excell');


    /* ======== ajax request ======== */
    Route::post('find/employee-id', [DailyWorkHistoryController::class, 'autocomplete']);
    Route::post('find/employee/{id?}', [EmployeeInfoController::class, 'searchingEmployeeAjaxRequestForAutoCompleteTextInput']);

    Route::post('find/indirectemployee-id', [DailyWorkHistoryController::class, 'conditionAutocomplete']);
    Route::post('find/employee/type-id', [DailyWorkHistoryController::class, 'findEmployeeTypeId']);
    Route::post('find/direct/employee-id', [DailyWorkHistoryController::class, 'findDirectEmployee']);

    /* ======== ajax request ======== */
    Route::post('insert/month/work/history', [DailyWorkHistoryController::class, 'insert'])->name('insert-month-work');
    Route::post('update/month/work/history', [DailyWorkHistoryController::class, 'update'])->name('update-month-work');

    // update monthly word records
    Route::post('employee/monthly-work/record/search1', [DailyWorkHistoryController::class, 'searchAnEmployeeMonthWorkRecordDetails'])->name('employee.month.work.record.search1');


    /* salary details controller */
    // closed on 4.5.26 Route::get('employee/salary-details', [SalaryDetailsController::class, 'index'])->name('salary-details');
    Route::post('employee/salary-details-list/search', [SalaryDetailsController::class, 'searchEmployeeSalaryDetailsByEmpID'])->name('employee-salary-details-list-search');

    // employee status update
    Route::get('employee/directman/status/update/{emp_auto_id}', [SalaryDetailsController::class, 'directManStatusUpdate'])->name('directman-status-update');



    Route::get('employee/salary/single/edit/{sdetails_id}', [SalaryDetailsController::class, 'edit'])->name('salary-single-edit');
    Route::get('employee/salary/single/details/{sdetails_id}', [SalaryDetailsController::class, 'view'])->name('salary-single-details');
    Route::get('employee/salary/single/delete/{sdetails_id}', [SalaryDetailsController::class, 'delete'])->name('salary-single-delete');
    Route::post('employee/salary-details/insert', [SalaryDetailsController::class, 'insert'])->name('salary-detalis-insert');
    Route::post('employee/salary-details/update', [SalaryDetailsController::class, 'update'])->name('salary-detalis-update');
    /* update salary details info from job approval ui */
    Route::post('employee/job-approve/salary-edit', [SalaryDetailsController::class, 'updateEmployeeSalaryDetailsAtTimeOfEmployeeApproval'])->name('employee.salary.details.update.at-approval.time');

    /* ========== Employee CPF Contribution ========== */
    Route::get('employee/CPF/contribution', [SalaryDetailsController::class, 'setContributionAmount'])->name('cpf-Contribution.set');

    Route::post('employee/CPF/contribution/amount', [SalaryDetailsController::class, 'updateContributionAmount'])->name('update-contribution.amount');


    /* ========== Ajax Call ========== */
    Route::post('employee-information/for/CPF/contribution', [SalaryDetailsController::class, 'empInfoForContribution'])->name('emp-information-for.set-contribution');

    /* ========== Employee Contribution ========== */
    Route::get('employee/contribution-report', [ContributionController::class, 'EmployeeContribution'])->name('CPF-contribution-report');
    Route::post('employee/contribution-report/genarate', [ContributionController::class, 'EmployeeContributionReport'])->name('CPF-contribution-report-generat');
    /* ========== Employee Contribution ========== */



    /* ========== Deduction ========== */
    // Route::get('employee/deduction', [DeductionController::class, 'index'])->name('employee-deduction');
    // Route::get('employee/deduction/add', [DeductionController::class, 'add'])->name('add-employee-deduction');

    /* ========== Address Division ========== */
    Route::get('country/add', [CountryController::class, 'add'])->name('add-country');
    Route::post('country/insert', [CountryController::class, 'insert'])->name('insert-country');


    /* ========== Address Division ========== */
    Route::get('division/add', [DivisionController::class, 'add'])->name('add-division');
    Route::get('division/edit/{division_id}', [DivisionController::class, 'edit'])->name('edit-division');
    Route::post('division/insert', [DivisionController::class, 'insert'])->name('insert-division');
    Route::post('division/update', [DivisionController::class, 'update'])->name('update-division');
    /* ========== Ajax Request ========== */
    Route::post('check/division-name', [DivisionController::class, 'validDivision'])->name('check-division.name');
    /* ========== Ajax Request ========== */

    /* ========== Address District ========== */
    Route::get('district/add', [DistrictController::class, 'add'])->name('add-district');
    Route::get('district/edit/{district_id}', [DistrictController::class, 'edit'])->name('edit-district');
    Route::post('district/insert', [DistrictController::class, 'insert'])->name('insert-district');
    Route::post('district/update', [DistrictController::class, 'update'])->name('update-district');
    /* ajax request */
    Route::get('division/ajax/{country_id}', [DistrictController::class, 'getDivision']);
    Route::get('/district/ajax/{division_id}', [DistrictController::class, 'getDistrict']);
    /* ajax request */


    /* ==================== Vechicle Controller (Vehicle related Infos) ==================== */

    Route::post('/vehicle/save-owner', [VehicleController::class, 'saveVehOwner'])->name('vehicle.saveOwner');
    Route::post('/vehicle/save-type', [VehicleController::class, 'saveVehType'])->name('vehicle.saveType');


    Route::get('vechicle/add', [VehicleController::class, 'index'])->name('add-new.vehicle');
    Route::post('vechicle/insert', [VehicleController::class, 'insert'])->name('insert-new.vechicle');
    Route::get('vechicle/delete/{veh_id}', [VehicleController::class, 'delete'])->name('delete-vechicle');
    // Route::post('vechicle/update', [VehicleController::class, 'update'])->name('update-vechicle');

    Route::get('vechicle/edit/{veh_id}', [VehicleController::class, 'edit'])->name('edit-vechicle');
    Route::post('vechicle/info-update', [VehicleController::class, 'updateVehicleRelatedInfo'])->name('update-vechicle-related-info');
    Route::post('vechicle/image-update', [VehicleController::class, 'updateVehicleRelatedImages'])->name('update-vechicle-related-image');

    /* ==================== Vechicle Controller (Vehicle related Infos) ==================== */
    Route::get('driver/add', [VehicleController::class, 'driverInfosAddUI'])->name('driver-info-add-ui');
    Route::post('driver/insert', [VehicleController::class, 'driverInfoInsertRequest'])->name('insert-driver-info');
    Route::get('driver-info/edit/{dri_id}', [VehicleController::class, 'driverInfoEditUI'])->name('driver-info-edit');
    Route::post('/driver/info-update', [VehicleController::class, 'driverInfoUpdateRequest'])->name('update-driver-info');
    Route::post('/driver/image-update', [VehicleController::class, 'driverImageUpdateRequest'])->name('update-driver-images');
    Route::get('driver/info-delete/{dri_id}', [VehicleController::class, 'updateDriverActiveInactiveStatus'])->name('driver-info-deactive');

    /* ==================== Vechicle Controller (Driver Vehicles related Infos) ==================== */
    Route::get('driver-vehicle/info-add', [VehicleController::class, 'driverVehicleInfosAddUI'])->name('driver-vehicle-info-add-ui');
    Route::post('driver-vehicle/insert', [VehicleController::class, 'driverVehicleInfoInsertRequest'])->name('insert-driver-vehicle-info');
    Route::get('driver-vehicle-info/edit/{driv_veh_auto_id}', [VehicleController::class, 'driverVehicleInfoEditUI'])->name('driver-vehicle-info-edit');
    Route::post('driver-vehicle/info-update', [VehicleController::class, 'driverVehicleInfoUpdateRequest'])->name('update-driver-vehicle-info');
    Route::get('driver-vehicle/info-delete/{driv_veh_auto_id}', [VehicleController::class, 'driverVehicleInfoDeActiveWithReleasedDate'])->name('driver-vehicle-info-deactive');
    Route::post('/driver-details/info-search', [VehicleController::class, 'getDriverDetailsInformationByEmployeeInfo'])->name('driver-info-searchBy-employee-info');

    // upload video photos
    Route::post('driver-vehicle/photo-video-upload', [VehicleController::class, 'uploadDriverVehiclePhotosVideo'])->name('upload-driver-vehicle-photos.video');


    /* ==================== Vechicle Fine Controller ==================== */
    Route::get('vechicle/fine', [VehicleController::class, 'vehicleFineFormWithRecords'])->name('add.vehicle.fine');
    Route::post('vehicle/search', [VehicleController::class, 'searchVehicleInformation'])->name('search_vehicle_information');
    Route::post('vehicle/fine/submit', [VehicleController::class, 'insertVehicleFineFormSubmit'])->name('vehicle_fine_insert_form');
    Route::get('vehicle/fine/edit/{id}', [VehicleController::class, 'editVehicleFineFormSubmit'])->name('vehicle_fine_edit_form');
    Route::post('vehicle/fine/update', [VehicleController::class, 'updateVehicleFineFormSubmit'])->name('vehicle_fine_update_form');
    Route::get('vehicle/fine/delete/{id}', [VehicleController::class, 'deleteVehicleFineForm'])->name('vehicle_fine_delete_form');

    /* Companies Vehicle Related Reports  */
    Route::get('vehicles/details/report/searching', [VehicleController::class, 'getVechileDetailsInformationReport'])->name('vehicles.details.report.seach');
    Route::get('vehicles/report-ui', [VehicleController::class, 'VehicleReportUI'])->name('vehicles.record.report');
    Route::post('vehicle/all-vehicles', [VehicleController::class, 'allActiveCompanyVehiclesReport'])->name('active-vehicle-report');
    Route::post('vehicle-wise/driver-infos', [VehicleController::class, 'vehicleNameWiseDriverAllInformationsCollect'])->name('vehicle-wise-driver-infos');
    Route::post('company-wise/all-vehicles/report-process', [VehicleController::class, 'vehicleTypeAndCompanyNameWiseAllActiveVehiclesReportProcess'])->name('vehicle.type-company.name-wise-report');
    Route::post('company/vehicle/report/process', [VehicleController::class, 'processProjectWiseVehicleDetailsReport'])->name('project-wise.vehicle.report');
    Route::get('company/vehicle/report/all-records', [VehicleController::class, 'processAllVehicleRecordsWithDriverDetailsReport']);






    /* =================== vehicle attendance  ==================== */
    Route::get('others/vehicle/attendance', [VehicleAttendanceController::class, 'index'])->name('vehicle.attendance.index');
    Route::post('others/vehicle/attendance', [VehicleAttendanceController::class, 'store'])->name('vehicle.attendance.store');
    Route::get('others/vehicle/attendance/delete/{id}', [VehicleAttendanceController::class, 'deleteARecord'])->name('vehicle.attendance.edit');
    Route::post('others/vehicle/attendance/update', [VehicleAttendanceController::class, 'update'])->name('vehicle.attendance.update');
    Route::get('others/vehicle/attendance/report', [VehicleAttendanceController::class, 'report'])->name('vehicle.attendance.report');



    /* ==================== LogBook Controller ==================== */
    Route::get('new/log-book/add', [LogBookController::class, 'index'])->name('add-new.LogBook');
    Route::get('new/log-book/edit/{lgb_id}', [LogBookController::class, 'edit'])->name('edit-new.LogBook');
    Route::get('new/log-book/delete/{lgb_id}', [LogBookController::class, 'delete'])->name('delete-new.LogBook');
    Route::post('new/log-book/insert', [LogBookController::class, 'insert'])->name('insert-new.LogBook');
    Route::post('new/log-book/update', [LogBookController::class, 'update'])->name('update-new.LogBook');
    Route::get('log-book/report', [LogBookController::class, 'report'])->name('log-book.report');
    Route::post('log-book/report/generat', [LogBookController::class, 'reportGenerate'])->name('log-book.report-generat');

    /* ==================== Office Building Controller ==================== */
    Route::get('rent/new-building/add', [OfficeBuildingController::class, 'index'])->name('rent.new-building');
    Route::get('rent/{ofb_id}/new-building/delete', [OfficeBuildingController::class, 'delete'])->name('rent.new-building.delete');
    Route::get('rent/{ofb_id}/new-building/edit', [OfficeBuildingController::class, 'edit'])->name('rent.new-building.edit');
    Route::post('rent/new-building/insert', [OfficeBuildingController::class, 'insert'])->name('rent.new-building.insert');
    Route::post('rent/new-building/update', [OfficeBuildingController::class, 'update'])->name('rent.new-building.update');

    /* ==================== Office Building Controller ==================== */

    /* ==================== Agencry Controller ==================== */
    Route::get('agency/add-new-agencry', [AgencyInfoController::class, 'newAgencyAddForm'])->name('agency.add-agencry.form');
    Route::post('agency/add-new-agencry', [AgencyInfoController::class, 'AgencyInformationInsertionUISubmit'])->name('agency.add-agencry.form.submit');


    /* ==================== BdOfficePayemnt  ==================== */
    Route::get('/office-payment', [BdOfficePaymentController::class, 'employeePaymentFromBdOfficeCreateForm'])->name('employee.payment.create.from-bdoffice-payment');
    Route::post('/office-payment/store', [BdOfficePaymentController::class, 'employeePaymentFromBdOfficeInsertRequest'])->name('employee.payment.from-bdoffice.create.insert-request');
    Route::get('/office-payment/edit/{id}', [BdOfficePaymentController::class, 'bdOfficeEmployeePaymentSetupUpdateForm'])->name('employee.payment.from-bdoffice.edit-from');
    Route::post('/office-payment/update', [BdOfficePaymentController::class, 'bdOfficeEmployeePaymentSetupUpdateRequest'])->name('employee.payment.info.update.request.for-bdoffice');

    Route::get('/office-payment/bdoffice-report-ui', [BdOfficePaymentController::class, 'getReportUIForPaymentFromBdOffice'])->name('employee.payment.from-bd-office.report.ui');
    Route::post('/office-payment/bdoffice-report-display', [BdOfficePaymentController::class, 'showApprovedEmployeesReportPaymentFromBdOffice'])->name('employee.payment.from-bd-office.report');
    Route::get('bdoffice/payment/details/employee/{id?}', [BdOfficePaymentController::class, 'showBdOfficePaymetDetailsForAnEmployee'])->name('employee.payment.from-bd-office.emp.payment-details');
    Route::post('bdoffice/payment/details/date-to-date', [BdOfficePaymentController::class, 'showBdOfficePaymetDetailsDateToDate'])->name('employee.payment.from-bd-offic.details.date.to.date');
    Route::post('bdoffice/payment/repor/emp-summary', [BdOfficePaymentController::class, 'showAnEmployeeBdOfficePaymetSummary'])->name('employee.payment.from-bd-offic.report.emp.summary');



    // Access From BD Office
    Route::get('/bd-office/payment-pending-employee-list', [BdOfficePaymentController::class, 'getBbOfficePaymentPendingEmployeeList'])->name('employee.payment.bdoffice-payment-pending');
    Route::get('/bd-office/payment-pending/{id}-employee-details', [BdOfficePaymentController::class, 'getBbOfficePaymentPendingEmployeeDetails'])->name('bdoffice-payment-pending.employee-details');
    Route::post('/bd-office-payment/update-request', [BdOfficePaymentController::class, 'employeePaymentFromBdOfficeUpdateRequest'])->name('employee.payment.from-bdoffice.update-request');

    // Fiscal Year
    Route::get('employee/fiscal-year/ui', [FiscalYearController::class, 'index'])->name('employee.fiscal.year.ui');
    Route::get('employee/fiscal-year/search', [FiscalYearController::class, 'searchAnEmployeeOpenClosingAllRecords'])->name('employee.open.close.fiscal.year.search');
    Route::get('employee/fiscal-year/last-close-fical-year', [FiscalYearController::class, 'searchAnEmployeeLastClosingFiscalRecord'])->name('get.last.close.fiscal.year');

    // close running fiscal year or open new fiscal year  closeAnEmployeeSalaryFiscalYear
    Route::post('employee/salary/closing', [FiscalYearController::class, 'updateAnEmployeeSalaryFiscalYear'])->name('employee.salary.fiscal.year.update');
    // searching employee fiscal year for closing
    Route::post('employee/salary/fiscal-year/search', [EmployeeReportController::class, 'searchAnEmployeeSalaryFiscalYear'])->name('employee.salary.fiscal.year.search');
    // getting data for edit
    Route::get('employee/fiscal-year/search-for-edit', [FiscalYearController::class, 'getAnEmployeeFiscalYearRecordByFiscalYearAutoIdForUpdate'])->name('employee.salary.fiscal.year.arecord');
    // delete a record
    Route::delete('employee/salary/fiscal-year/delete', [FiscalYearController::class, 'deleteAnEmployeeFiscalYear'])->name('employee.salary.fiscal.year.delete');

    /* ==================== EXPORT AS EXCELL OR CSV FORMAT ==================== */
    // now not used Route::get('export/eployees-excell', [ExcelExportController::class, 'EmployeeslistExcellExport'])->name('export.eployees-list-excell');


    /*
    ==========================================================================
    ============================= All AJAX REQUEST ===========================
    ==========================================================================
    */
    Route::get('searching/employee-list/by/project-wise', [EmployeeInfoController::class, 'getProjectWiseEmployeeListForEmployeeTransferAJAXRequest'])->name('search.project.wise.employee.list.for.transfer');


    /*
     ==========================================================================
     ============================= Inventory Module Start =====================
     ==========================================================================
    */

    // ========== Item Category  ==========
    Route::get('inventory/category', [ItemCategoryController::class, 'index'])->name('inventory-category');
    Route::get('inventory/{icatg_id}/category/edit', [ItemCategoryController::class, 'edit'])->name('inventory-category-edit');
    Route::post('inventory/category/insert', [ItemCategoryController::class, 'insert'])->name('insert.item-type-category');
    Route::post('inventory/category/update', [ItemCategoryController::class, 'update'])->name('update.item-type-category');
    Route::get('inventory/{icatg_id}/category/in-active', [ItemCategoryController::class, 'categItemInActive'])->name('inventory-category.inActive');

    // ========== Item Sub Category  ==========
    Route::get('inventory/sub-category', [ItemSubCategoryController::class, 'index'])->name('inventory-sub-category');
    Route::get('inventory/{iscatg_id}/sub-category/edit', [ItemSubCategoryController::class, 'edit'])->name('inventory-sub-category.edit');
    Route::get('inventory/{iscatg_id}/sub-category/in-active', [ItemSubCategoryController::class, 'subCategItemInActive'])->name('inventory-sub-category.inActive');
    Route::post('inventory/sub-category/insert', [ItemSubCategoryController::class, 'insert'])->name('insert.item-type-sub-category');
    Route::post('inventory/sub-category/update', [ItemSubCategoryController::class, 'update'])->name('update.item-type-sub-category');

    // ========== Item Name  ==========
    Route::get('inventory/item-name', [ItemNameController::class, 'index'])->name('inventory-item-name');
    Route::post('inventory/item-name/insert', [ItemNameController::class, 'insert'])->name('insert.item-type-item-name');
    Route::get('inventory/{item_id}/item-name/edit', [ItemNameController::class, 'edit'])->name('inventory-item-name.edit');
    Route::post('inventory/item-name/update', [ItemNameController::class, 'update'])->name('update.item-type-item-name');
    Route::get('inventory/{item_id}/item-name/in-active', [ItemNameController::class, 'inActiveItemDetails'])->name('inventory-item-name.inActive');
    //========== Item Name Search By Item Code ==========
    Route::post('inventory/item-details/info-by-itemCode', [ItemNameController::class, 'findItemNameDetailsByItemCodeBrandAndItemDetails'])->name('item-details-info-by-itemCode');
    Route::get('inventory/item-details/list-report-show', [ItemNameController::class, 'searchItemNameWithCodeByItemCategorySubCatAndItemName'])->name('search.itemcode.list.report');


    // ========== Item Compnay Name  ==========
    Route::post('inventory/item-company/insert', [ItemBrandController::class, 'insertNewItemCompanyName'])->name('insert.item-type.item-company-name');
    // ========== Item Brand Name  ==========
    Route::post('inventory/item-brand/insert', [ItemBrandController::class, 'insert'])->name('insert.item-type-brand-name');


    // ========== Item Quantity Add in Stock  ==========
    Route::post('inventory-items/add-to-cart', [ItemDetailsController::class, 'addToCartInventoryItemsInformation'])->name('item-infos-add-to-cart');
    Route::post('/inventory-items/purchase/with-cart-items', [ItemDetailsController::class, 'purchaseInventoryItemsWithCartsItems'])->name('inventory-items-purchase');
    Route::get('/inventory-items/purchase-invoice/{purchase_record_id}', [ItemDetailsController::class, 'showInventoryPurchaseInvoice'])->name('inventory-purchase-invoice-show');
    Route::post('/inventory-item/cart-item/remove', [ItemDetailsController::class, 'removeInventoryCartItem'])->name('inventory-cart-item-remove');
    Route::get('/inventory/cart-items-load', [ItemDetailsController::class, 'loadInventoryCartItems'])->name('inventory-cart-items');
    Route::get('inventory/item-details', [ItemDetailsController::class, 'index'])->name('inventory-item-details-name');
    Route::post('inventory/item-details/insert', [ItemDetailsController::class, 'insert'])->name('insert.item-type-item-details');
    Route::get('inventory/{item_id}/item-details/edit', [ItemDetailsController::class, 'edit'])->name('inventory-item-details-name.edit');
    Route::post('inventory/item-details/update', [ItemDetailsController::class, 'update'])->name('update.item-type-item-details');

    // ==== Inventory Item Setup All Ajax Request ====
    Route::get('item/category/ajax/{itype_id}', [ItemSubCategoryController::class, 'findCategory']);
    Route::get('item/sub-category/ajax/{icatg_id}', [ItemSubCategoryController::class, 'findSubCategory']);
    Route::get('item-name/ajax/{iscatg_id}', [ItemNameController::class, 'findSubCatgWiseItemName']);
    Route::get('/item-brand/ajax/{iscatg_id}', [ItemBrandController::class, 'findSubCatgWiseItemBrandName']);

    /* ==== ajax request ==== *//* ========== Order metarial & Tools ========== */
    Route::get('order/metarial-tools/', [OrderComponentController::class, 'index'])->name('order-metarial-tools');

    /* ==== ajax request for Item Purchage in  Cart ==== */
    Route::post('metarial-tools/cart/store', [OrderComponentController::class, 'addToCart']);
    Route::get('metarial-tools/list/view', [OrderComponentController::class, 'metarialListView']);
    Route::post('metarial-tools/order/complete', [OrderComponentController::class, 'orderComplete'])->name('metarial.tools-order-confirm');
    Route::get('/metarial-tools/single-list/remove/{rowId}', [OrderComponentController::class, 'metarialSingleListRemove']);


    // ========== Inventory Store  ==========
    Route::get('inventory/item-sub-store', [InventorySubStoreContoller::class, 'index'])->name('inventory-sub-store-infos');
    Route::post('inventory/item-sub-store/insert', [InventorySubStoreContoller::class, 'insert'])->name('insert.inventory-sub-store-info');
    Route::post('inventory/item-sub-store/update', [InventorySubStoreContoller::class, 'update'])->name('update.inventory-sub-store-info');


    // ============ Inventory Item Distribution =============
    Route::get('inventory/item-received-by-emp/form', [EmpItemReceivedController::class, 'index'])->name('emp.received.item.info.insert.form');
    Route::post('inventory/item/received-by-emp/insert-request', [EmpItemReceivedController::class, 'storeEmployeeItemReceivedInformation'])->name('emp.received.item.info.insert.request');
    Route::POST('inventory/item/received-by-emp/search-request', [EmpItemReceivedController::class, 'searchEmployeeReceivedItemDetails'])->name('emp.received.item.info.search.ajaxrequest');
    Route::get('inventory/item/received/emp-search-for-paper-upload', [EmpItemReceivedController::class, 'searchItemReceivedEmployeeForUploadPaper'])->name('item.received.emp.search.forpaper.upload');
    Route::post('inventory/item/received/paper-upload', [EmpItemReceivedController::class, 'uploadEmployeeItemReceivedPaper'])->name('emp.item.received.paper.upload');
    Route::delete('inventory/item/received-item-delete/{id}', [EmpItemReceivedController::class, 'deleteEmployeeReceivedItemRecord'])->name('emp.received.item.delete.ajaxrequest');

    // Inventory related report
    Route::GET('inventory/report/process-ui', [InventoryReportController::class, 'index'])->name('invenotry.report.process.ui');
    Route::POST('inventory/report/item-received-by-emp-report', [InventoryReportController::class, 'showEmployeeInventoryItemReceivedReport'])->name('employee.item.received.report');
    Route::POST('inventory/report/item-stock', [InventoryReportController::class, 'showInventoryItemStockReport'])->name('inventory.item.stock.report');


    // old route tools-metarial-report

    /* ==================== Report Purchase ==================== */
    // Route::get('report/tools-&-metarials/purchase', [ItemPurchaseController::class, 'index'])->name('tools-metarial-report');
    // Route::post('report/tools-&-metarials/purchase/process', [ItemPurchaseController::class, 'process'])->name('tools-metarials-process');
    //  Route::get('report/item/stock-amount', [ItemPurchaseController::class, 'itemStock'])->name('items-report');
    // Route::post('report/item/stock-amount/process', [ItemPurchaseController::class, 'itemStockProcess'])->name('item.stock-process');

    /* ============================= Employee TUV Certification Works ============================= */
    Route::get('employee/tuv/information/insert-form', [EmployeeTUVController::class, 'loadEmployeeTUVInsertForm'])->name('employee.tuv.information.insert.form');
    Route::post('employee/tuv/information/insert-request', [EmployeeTUVController::class, 'insertEmployeeTUVInformationsRequest'])->name('employee.tuv.information.insert.request');

    /*
     ==========================================================================
     ============================= Cost Control Module =======================
     ==========================================================================
    */
    Route::GET('cost-control/activity/insert/index', [ActivityDetailsController::class, 'index'])->name('costcontrol.activity.insert.ui');
    Route::POST('cost-control/activity/element/insert-request', [ActivityDetailsController::class, 'storeActivityElementRequest'])->name('costcontrol.activity.element.insert.request');
    Route::POST('cost-control/activity/name/insert-request', [ActivityDetailsController::class, 'storeActivityNameRequest'])->name('costcontrol.activity.name.insert.request');
    Route::GET('cost-control/activity/details/insert-ui', [ActivityDetailsController::class, 'loadActivityDetailsInsertUserInterface'])->name('costcontrol.activity.details.insert.ui');
    Route::POST('cost-control/activity/details/insert-request', [ActivityDetailsController::class, 'storeNewActivityDetailsInformation'])->name('costcontrol.activity.details.insert.request');
    Route::GET('cost-control/activity/details/delete-request/{id}', [ActivityDetailsController::class, 'deleteAnActivityDetailsRecord'])->name('costcontrol.activity.details.delete.request');
    Route::GET('cost-control/activity/details/report-ui', [ActivityDetailsController::class, 'loadCostControllReportProcessingUI'])->name('costcontrol.activity.details.report.ui');

    Route::POST('cost-control/activity/details/report/multiple-emp-id', [ActivityDetailsController::class, 'multipleEmployeeWiseActivityDetailsReport'])->name('costcontrol.activity.details.report.multiple.emp.id');
    Route::POST('cost-control/activity/details/report/project-plot-activity', [ActivityDetailsController::class, 'getProjectPlotActivityElementsWiseActivityDetailsReport'])->name('costcontrol.activity.details.report.project.plot.element');


    /*
     ==========================================================================
     ========================== Catering Service Module =======================
     ==========================================================================
    */
    Route::GET('catering/monthly-form', [EmpMonthlyCateringController::class, 'loadMonthlyCateringServiceUI'])->name('catering.monthly.information');
    Route::POST('catering/month/record/store', [EmpMonthlyCateringController::class, 'storeMonthlyCateringServiceRecord'])->name('catering.monthly.record.store.request');
    Route::GET('catering/month/record/searching', [EmpMonthlyCateringController::class, 'searchAnEmployeeCateringRecord'])->name('anemployee.catering.record.search');
    //  Route::GET('catering/month/record/delete11/{id}',[EmpMonthlyCateringController::class,'deleteAnEmployeeCateringRecord'])->name('anemployee.catering.record.delete');
    Route::DELETE('catering/month/record/delete', [EmpMonthlyCateringController::class, 'deleteAnEmployeeCateringRecord'])->name('anemployee.catering.record.delete');

    Route::POST('catering/month/record/upload-preview', [EmpMonthlyCateringController::class, 'importEmployeeMonthlyWorkRecordsFromExcel'])->name('catering.monthly.record.import.preview.request');
    Route::POST('catering/month/record/upload-submit', [EmpMonthlyCateringController::class, 'submitEmployeeMonthlyCateringRecordsImportFromExcel'])->name('catering.monthly.record.import.submit.request');
    Route::GET('catering/service/report', [EmpMonthlyCateringController::class, 'processAndShowCateringReport'])->name('catering.service.report');


    /*
     =================================================================================
     ========================== Company Chart Of Account Module =======================
     ==================================================================================
    */
    Route::get('/company/chart-of-account/records', [ChartOfAccountController::class, 'index'])->name('company.chart.of.account');
    // Route::post('/company/chart-of-account/records-store', [ChartOfAccountController::class, 'storeChartOfAccountInfos'])->name('company.chart.of.account.info.store');
    // Route::get('/company/chart-of-account/records-load', [ChartOfAccountController::class, 'getAllChartOfAccountInfos'])->name('company.chart.of.account.info.load');
    // Route::get('/company/chart-of-account/close/{accountId}', [ChartOfAccountController::class, 'closeSingleChartOfAccount'])->name('company.chart.of.account.close');
    // Route::get('/company/chart-of-account/record-edit/{chartOfAcctID}', [ChartOfAccountController::class, 'getInformationForChartOfAccountEdit'])->name('company.chart.of.account.edit');
    // Route::post('/company/chart-of-account/records-update', [ChartOfAccountController::class, 'updateChartOfAccountInfos'])->name('company.chart.of.account.info.update');

    // Route::get('/company/chart-of-account/search-by-account-type/{account_type_Id}', [ChartOfAccountController::class, 'searchChartOfAccountByAccountType'])->name('chart.of.account.search.by.account.type');

    /*
     =================================================================================
     ========================== Journal Info Module =======================
     ==================================================================================
    */
    Route::get('/account/journal-informations', [JournalInfoController::class, 'index'])->name('account.journal.info');
    // Route::get('/account/journal-information/details', [JournalInfoController::class, 'getAllAccountJournalInfos'])->name('account.journal.info.list');
    // Route::get('/account/chart-of-account/records-by/journal-type/{journalTypeId}', [JournalInfoController::class, 'getChartOfAccountInfosByJournalTypeId'])->name('chart.of.account.info.by.journalId');
    // Route::post('/account/journal-information/store', [JournalInfoController::class, 'storeJournalInformation'])->name('account.journal.info.store');
    // Route::get('/account/journal-information/record-edit/{jourInfoId}', [JournalInfoController::class, 'getAccountJournalInformationForEdit'])->name('account.journal.info.edit');
    // Route::post('/account/journal-information/update', [JournalInfoController::class, 'updateJournalInformation'])->name('account.journal.info.update');
    // Route::get('/account/journal-information/de-active/{jourInfoID}', [JournalInfoController::class, 'deActiveJournalInfo'])->name('account.journal.info.deActive');


    /*
     =================================================================================
     ========================== Chart of Account Purchase Invoice =====================
     ==================================================================================
    */
    Route::get('/account/purchase-new-invoice', [ChartofaccountPurchaseController::class, 'index'])->name('chartofaccount.purchase.invoice.new');
    Route::post('/account/purchase-invoice-addtocart', [ChartofaccountPurchaseController::class, 'addToCartInventoryItemsInChartOfAccountPurchaseInvoice'])->name('chartofaccount.purchase-invoice-add-to-cart');
    Route::post('/account/purchase-invoice-store', [ChartofaccountPurchaseController::class, 'storePurchaseInvoiceInformation'])->name('chartofaccount.purchase.invoice.store');


    /*
     =================================================================================
     ========================== Inventory Supplier Info ==============================
     ==================================================================================
    */
    Route::get('/inventory/supplier-info/ui', [SupplierInfoController::class, 'showSupplierInformationUI'])->name('inventory.supplier-info');
    Route::post('/inventory/supplier-info/store', [SupplierInfoController::class, 'storeInventorySupplierInfo'])->name('insert.inventory.supplier-info');
    Route::get('/inventory/supplier-info', [SupplierInfoController::class, 'loadInventorySupplierInformation'])->name('load.inventory.supplier-info');
    Route::get('/inventory/supplier-info/status-update/{supplierAutoID}', [SupplierInfoController::class, 'changeInventorySupplierStatus'])->name('change.supplier-info.status');

    // Item Supplier
    // Route::post('supplier/new/insert-request',[SupplierInfoController::class,'storeNewSupplierInformation'])->name('supplier.insert.request');



    // form generate and download
    Route::get('form/download/generate', [FormDownloadController::class, 'loadFormGenerateAndDownloadUI'])->name('form.download.generate');
    Route::post('form/download/create-advance-papers', [FormDownloadController::class, 'createEmployeeAdvancePaper'])->name('emp.advance.papers.create.request');
    /* ============================== Download FORM ============================== */
    // Route::get('download/form/various-form', [EmployeeReportController::class, "downloadSystemGeneratedVarousForm"])->name('form.download.system.generate.form');
    Route::get('form/download/increment-form', [FormDownloadController::class, "createEmpIncrementForm"])->name('form.download.emp.increment');
});


 
