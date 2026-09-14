export const EmployeeSearchAPI ="/admin/employee/search/with/multitype-parameter/employee-details";
export const DailyActivitiesListAPI = "/admin/hrmanagement/daily-activity/list/";
export const DailyActivitiesUpdateAPI = "/admin/hrmanagement/daily-activity/update";

// Attendance Part
export const AttendanceProcessAPI = '/admin/payroll/api/attendance/'
export const AttendanceSummaryReportProcessAPI = '/admin/payroll/api/attendance/summary-report'

export const AttendanceIN_OUT = '/admin/payroll/attendance/in-out'
export const AttendanceBio = '/admin/payroll/attendance/attendence-bio'
export const OTSheetStore = '/admin/payroll/attendance/ot-file/store'
export const OTSheetUpdate = '/admin/payroll/attendance/ot-file/update/'
export const OTSheetSearch = '/admin/payroll/api/attendance/search-ot-file'
export const OTSheetDelete = '/admin/payroll/api/attendance/delete-ot-file'

//employee bounus
 export const EmployeeBonusStoreAPI = '/admin/payroll/salary/store/new/employee-bonus';
 export const EmployeeBonusListAPI = '/admin/payroll/salary/employee-bonus/records';
 export const EmployeeBonusDeleteAPI = '/admin/payroll/salary/delete/employee-bonus';
 export const EmployeeBonusReportAPI = '/admin/payroll/salary/process/employee-bonus/details/report';

// Salary Part
 export const EmployeeSalaryPaidListAPI = '/admin/payroll/salary/paid-list/';
 
 export const EmployeeSalaryPaymentUndoStatusAPI = '/admin/payroll/salary/payment/unpaid/status';
 export const EmployeeSalaryPaymentPaidStatusAPI = '/admin/payroll/salary/payment/paid/status';

//salary panding

 export const EmployeeSalaryPeindingListAPI = '/admin/payroll/salary/pending-list';
 export const EmployeeSalaryPeindingGetRecordAPI = '/admin/payroll/salary/pending-salary-record-history';
 export const EmployeeSalaryPeindingUpdateAPI = '/admin/payroll/salary/pending-salary-update';
 export const EmployeeSalaryPeindingDeleteAPI = '/admin/payroll/salary/pending-salary-delete';


// export const EmployeeSalaryListAPI = '/admin/payroll/employee/salary/list/';
// export const EmployeeSalaryGenerateAPI = '/admin/payroll/employee/salary/generate/';
export const WPSSalaryPreviewUsingExcelfileAPI = '/admin/payroll/salary/wps-salary-show-using-excel';

export const LeaveApplicationSalaryPendingList = "/admin/payroll/salary/salary_closing-applications";


export const PartialSalaryStoreAPI = '/admin/payroll/salary/partial-salary';
// export const PartialSalaryUpdateAPI = '/admin/payroll/salary/partial-salary/update/';
export const PartialSalarySearchAPI = '/admin/payroll/salary/partial-salary/search';
// now excel downloading from searching result, next time when used from server then use this api
export const PartialSalaryDownloadAPI = '/admin/payroll/salary/partial-salary/download';
export const PartialSalaryDeleteAPI = '/admin/payroll/salary/partial-salary/:id';

// Payslip upload\
//export const PayslipUploadMainPage = '/admin/payroll/salary/payslip-upload';
export const PayslipUploadAPI = '/admin/payroll/salary/salary-sheet/store';
export const PayslipSearchAPI = '/admin/payroll/salary/salary-sheets/search';
export const PayslipUpdateAPI = '/admin/payroll/salary/salary-sheet/update/:id';
export const PayslipDeleteAPI = '/admin/payroll/salary/salary-sheet';


