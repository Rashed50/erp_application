export const NewEmployeeIDAPI  = '/admin/hrmanagement/employee/get-new-employee-id/';
export const EmpTransferSubmitAPI  = '/admin/hrmanagement/employee/emp-transfer-submit-form/';
export const EmpTransferSearchAPI  = '/admin/hrmanagement/employee/searching/emp-list/by/project-wise';
export const EmployeeCheckUniqueIDAPI ='/admin/hrmanagement/employee/check/employee/unique-id'
export const NewEmployeeInsertAPI  = '/admin/hrmanagement/employee/new-insert';
export const EmployeeSearchAPI ="/admin/employee/search/with/multitype-parameter/employee-details";
export const EmployeeSearchAPIForInfoEdit ="/admin/hrmanagement/employee/search-for-edit-all-info";
export const EmployeeAllInfoUpdateAPI =    '/admin/hrmanagement/employee/update-all-information';
export const FileUploadAPI =    '/admin/hrmanagement/employee/file-update';
export const SalaryUpdateAPI = '/admin/hrmanagement/employee/salary-update';

export const DivisionSearchByAPI = '/admin/division/ajax'
export const DistrictSearchByAPI = '/admin/district/ajax'


export const IqamaAndPassportUpdateAPI = "/admin/hrmanagement/employee/iqama-passport-update";
export const updateWorkingProjectAPI = "/admin/hrmanagement/employee/working-project-update";
export const updateDesignationAPI = "/admin/hrmanagement/employee/trade-designation-update";
export const updateSponserAPI = "/admin/hrmanagement/employee/sponsor-update";
export const ReferenceUpdateAPI = "/admin/hrmanagement/employee/reference-update";
export const BloodGroupUpdateAPI = "/admin/hrmanagement/employee/blood-group-update";
export const AccommodationUpdateAPI = "/admin/hrmanagement/employee/accommodation-update";
export const AjeerDocumentUpdateAPI = "/admin/hrmanagement/employee/ajeerFile-update";
//   Route::post('employee/image/update', [EmployeeInfoController::class, 'updateEmployeeUploadedFileImage'])->name('employee-image.update');
export const DailyActivitiesListAPI = "/admin/hrmanagement/daily-activity/list/";
export const DailyActivitiesUpdateAPI = "/admin/hrmanagement/daily-activity/update";

// emp activity

export const NewActivityInsertAPI = "/admin/hrmanagement/employee/activity/new-activity-insert";
export const SalaryActivityUpdateAPI = "/admin/hrmanagement/employee/new-activity-with-salary-status";



export const UnapprovedEmployeesAPI = "/admin/hrmanagement/employee/unapproved-employees";
export const ApprovalOfNewEmployeesAPI = "/admin/hrmanagement/employee/new-employee-job-status-approved";
export const UnapprovedEmpSalaryUpdateAPI = "/admin/hrmanagement/employee/employee-salary-update-at-approval";
export const UnapprovedEmployeesDeleteAPI = "/admin/hrmanagement/employee/delete";


export const LeaveApplicationSubmitAPI = "/admin/hrmanagement/leave/application/submit";
export const LeaveApplicationPendingList = "/admin/hrmanagement/leave/pending-applications";
export const LeaveApplicationUpdateAPI = "/admin/hrmanagement/leave/application/update";
export const LeaveApplicationRejectAPI = "/admin/hrmanagement/leave/application-rejection";
export const LeaveApplicationSalaryPendingList = "/admin/hrmanagement/leave/salary_closing-applications";
export const LeaveApplicationFormPrintAPI = "/admin/hrmanagement/leave/application-form/print-preview";






//  Route::post('/new-employee-job-status-approved', [EmployeeController::class, 'approveMultiple'])->name('new-employee-job-status-approved');
//         Route::post('/employee-salary-update-at-approval', [EmployeeController::class, 'updateSalaryAtApproval'])->name('employee.salary.details.update.at-approval.time');
//         Route::delete('/employee/{id}', [EmployeeController::class, 'destroy'])->name('employee.destroy');
