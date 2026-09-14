<?php

namespace Modules\Payroll\Http\Controllers\Salary;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use App\Exports\WpsSalaryTableExport;
use App\Jobs\SendPayslipEmailJob;
use App\Jobs\SendSinglePayslipEmailJob;
use Modules\Payroll\Imports\ImportWPSEmployeeForSalaryPreview;
use Modules\Payroll\Exports\WPSSalaryExportAsExcel;
use App\Http\Controllers\Admin\Helper\HelperController;
use App\Http\Controllers\DataServices\{AuthenticationDataService, EmployeeAttendanceDataService};


use App\Exports\{SalaryReport1ExcelExport};
use App\Http\Controllers\Admin\Helper\UploadDownloadController;

//! Models Imports
use App\Models\EmployeeInfo;
use App\Http\Controllers\DataServices\{
    SalaryProcessDataService,
    EmployeeRelatedDataService,
    ProjectDataService,
    CompanyDataService,
    LeaveApplicationDataService,
    EmployeeDataService,
    FiscalYearDataService,
    EmployeeAdvanceDataService,
    EmpActivityDataService
};
use Modules\Payroll\Services\WPSSalaryReportService;

use Illuminate\Support\Facades\Mail;
use App\Mail\{PayslipMail, MyTestMail};

class SalaryController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:salary_report_processing_page', ['only' => ['create']]);

        $this->middleware('permission:salary-processing', ['only' => [
            'employeeSalaryProcessAJAXRequest',
            'singleEmployeeMonthWiseSalaryReport',
            'deleteAnEmployeeUnpaidSalaryRecord',
            'updateAnEmployeeSalaryRecordBySalaryHistoryAutoId'
        ]]);
        $this->middleware('permission:salary-pending', ['only' => [
            'loadSalaryPendingEmployeeListWithUI',
            'SalaryPendingList',
            'SalaryPaymentToUnPay',
            'SalaryPayment',
            'SalarypaidList',
            'Salarypaid',
            'SalarySheet'
        ]]);
    }



    public function salaryIndex()
    {


        $projects   = (new ProjectDataService())->getAllActiveProjectListForDropdown();
        $sponsors = (new EmployeeRelatedDataService())->getAllSponserInfoForDropdown();


        return view(
            'payroll::pages.Payroll.salary_update',
            [
                'data' => [

                    'projects'   => $projects,
                    'sponsors' => $sponsors

                ]
            ]
        );
    }

    public function SalarypaidList(Request $request)
    {

        // return response()->json(['status' => 200, 'success' => true, 'message' => 'test', 'dd' => $request->all()]);

        try {


            $dayMonthYear1 = (new HelperController())->getDayMonthAndYearFromDateValue($request->fromDate);
            $fromMonth = $dayMonthYear1[1]; // 1 index value as month
            $fromYear = $dayMonthYear1[2]; // 2 index value as Year

            $dayMonthYear2 = (new HelperController())->getDayMonthAndYearFromDateValue($request->toDate);
            $toMonth = $dayMonthYear2[1]; // 1 index value as month
            $toYear = $dayMonthYear2[2]; // 2 index value as Year

            $sponser_id = (int) $request->SponsId;
            $proj_id = (int) $request->proj_id;
            if ($request->employee_id) {

                // multiple emp ids
                $allEmplId = explode(",", $request->employee_id);
                $allEmplId = array_unique($allEmplId); // remove multiple same empl ID
                $final_list = collect();

                $list_of_emps = (new EmployeeDataService())->getListOfEmployeesWithSalaryDetailsByMultipleEmployeeId($allEmplId);
                if (count($list_of_emps) == 1) {

                    $fiscal = (new FiscalYearDataService())->getAnEmployeeRunningFiscalYearRecord($list_of_emps[0]->emp_auto_id);
                    $pendingSalary = (new SalaryProcessDataService())->searchAnEmployeeInAFiscalYearPaidSalaryAllRecordsForListView($list_of_emps[0]->employee_id, $fiscal->start_date, $fiscal->end_date, Auth::user()->branch_office_id);
                    $final_list  = $final_list->concat($pendingSalary);
                } else {
                    foreach ($list_of_emps as $an_emp) {
                        $fiscal = (new FiscalYearDataService())->getAnEmployeeRunningFiscalYearRecord($an_emp->emp_auto_id);
                        $pendingSalary = (new SalaryProcessDataService())->searchAnEmployeesInfoWithInAFiscalYearPaidSalaryRecordsForListView($an_emp->employee_id, $fiscal->start_date, $fiscal->end_date, Auth::user()->branch_office_id, $fromMonth, $fromYear);
                        $final_list  = $final_list->concat($pendingSalary);
                    }
                }

                return response()->json(['status' => 200, 'success' => true, 'data' => $final_list]);
            } else {

                if ($sponser_id == '') {
                    return response()->json(['status' => 404, 'success' => false, 'message' => "Select a Sponsor", 'error' => 'Select a Sponsor']);
                }

                $pendingSalary = (new SalaryProcessDataService())->searchListOfEmployeesThoseSalaryAlreadyPaidForListViewByProjectAndSponsor($sponser_id, $proj_id, $fromMonth, $toMonth, $fromYear, $toYear, Auth::user()->branch_office_id);

                return response()->json(['status' => 200, 'success' => true, 'data' => $pendingSalary, 'fromMonth' => $fromMonth, 'toMonth' => $toMonth]);
            }
        } catch (Exception $ex) {
            return response()->json(['status' => 603, 'success' => false, 'message' => 'Server Operation Failed', 'error' => $ex]);
        }
    }


    public function SalaryPaymentToPay(Request $request)
    {
        $slh_auto_id_list = $request->input('slh_auto_ids', []);

        if (empty($slh_auto_id_list)) {
            return response()->json([
                'success' => false,
                'status' => 400,
                'message' => 'No record selected'
            ], 400);
        }

        $salaryService = new SalaryProcessDataService();

        foreach ($slh_auto_id_list as $slh_auto_id) {
            if ($request->has('emp_slh_paid_checkbox-' . $slh_auto_id)) {
                $emp_salary_record = (new SalaryProcessDataService())->getAnEmployeeBankInfoWithSalaryRecordForUpdateSalaryPaidMethodBySalaryHistoryAutoId($slh_auto_id);
                // $emp_salary_record->ebd_auto_id is the employee active bank details auto id
                (new AuthenticationDataService())->InsertLoginUserActivity(24, 2, Auth::user()->id, $emp_salary_record->emp_auto_id, "Salary Updated from UnPaid to paid");

                if ($emp_salary_record->ebd_auto_id != null && $emp_salary_record->payment_method == "Bank") { // SalaryPaymentMethodEnum::Bank->value

                    (new SalaryProcessDataService())->updateEmployeeSalaryStatusAsPaidAndPaymentMethod((int) $slh_auto_id, (int) $emp_salary_record->ebd_auto_id, Auth::user()->id);
                } else {
                    (new SalaryProcessDataService())->updateEmployeeSalaryStatusAsCashPaid((int) $slh_auto_id, Auth::user()->id);
                }
            }
        }

        // foreach ($slh_auto_id_list as $slh_auto_id) {


        //     if ($emp_salary_record && $emp_salary_record->ebd_auto_id != null && $emp_salary_record->payment_method == "Bank") {
        //         $salaryService->updateEmployeeSalaryStatusAsPaidAndPaymentMethod((int) $slh_auto_id, (int) $emp_salary_record->ebd_auto_id, Auth::user()->id);
        //     } else {
        //         $salaryService->updateEmployeeSalaryStatusAsCashPaid((int) $slh_auto_id, Auth::user()->id);
        //     }
        //     // login user activities record
        //     $emp_salary_record = $salaryService->getAnEmployeeBankInfoWithSalaryRecordForUpdateSalaryPaidMethodBySalaryHistoryAutoId($slh_auto_id);
        //  //   (new AuthenticationDataService())->InsertLoginUserActivity(24, 2, Auth::user()->id, $emp_salary_record->emp_auto_id, "Salary Updated from Unpaid to Paid");
        // }

        // (new AuthenticationDataService())->InsertLoginUserActivity(24, 2, Auth::user()->id, $request->emp_auto_id, "Salary Updated from Unpaid to Paid");


        return response()->json([
            'success' => true,
            'status' => 200,
            'message' => 'Successfully Updated'
        ]);
    }



    public function SalaryPaymentToUnPay(Request $request)
    {
        try {
            $slh_auto_ids = $request->slh_auto_ids;
            for ($i = 0; $i < count($slh_auto_ids); $i++) {
                $slh_auto_id = $slh_auto_ids[$i];
                $payment = (new SalaryProcessDataService())->updateEmployeeSalaryStatusAsUnPaid($slh_auto_id, Auth::user()->id);

                $emp_salary_record = (new SalaryProcessDataService())->getAnEmployeeSalaryRecordBySalaryHistoryAutoId($slh_auto_id);
                // login user activities record
                (new AuthenticationDataService())->InsertLoginUserActivity(24, 2, Auth::user()->id, $emp_salary_record->emp_auto_id, "Salary Updated from Paid to Unpaid");
            }
            if ($i > 0) {
                return response()->json(["success" => "Salary Status Updated", 'status' => 200, 'success' => true]);
            } else {
                return response()->json(['error' => "Data Not Found!", 'status' => 404, 'success' => false]);
            }
        } catch (Exception $ex) {
            return response()->json(['success' => false, 'status' => 404, 'error' => 'error', 'message' => 'Server Operation Failed, Please Try Again', 'error' => $ex->getMessage()]);
        }
    }






    // pending sallary search controller
    public function SalaryPendingList(Request $request)
    {



        try {

            $dayMonthYear1 = (new HelperController())->getDayMonthAndYearFromDateValue($request->fromDate);
            $salary_month = $dayMonthYear1[1]; // 1 index value as month
            $salary_year = $dayMonthYear1[2]; // 2 index value as Year


            $emp_type = (int) $request->emp_type; // -1 = not selected ,  0 = Direct Basic Employee, 1 = Hourly Employee, 2 = Indirect Employee


            $sponser_ids =  $request->SponsId != null ? $request->SponsId : (new EmployeeRelatedDataService())->getAllActiveSponsorIdAsArray();
            $project_ids = $request->proj_id != null ? $request->proj_id : (new ProjectDataService())->getAllActiveProjectIDOfABranchOfficeAsArray(1);


            if ($request->employee_id != null) {
                // multiple emp ids
                $allEmplId = explode(",", $request->employee_id);
                $allEmplId = array_unique($allEmplId); // remove multiple same empl ID
                //  return response()->json(['status' => 404, 'success' => true, 'message' => $allEmplId]);

                if (count($allEmplId) == 1) {
                    $pendingSalary =  (new SalaryProcessDataService())->searchAnEmployeeUnPaidSalaryRecordsWithEmloyeeInfoForListView($allEmplId, Auth::user()->branch_office_id);
                } else {
                    $pendingSalary =  (new SalaryProcessDataService())->searchMultiEmployeeUnPaidSalaryRecordsForAMonthWithEmloyeeInfoForListView($allEmplId, $salary_month, $salary_year, Auth::user()->branch_office_id);
                    // return response()->json(['status' => 404, 'success' => true, 'message' => $allEmplId]);
                }
            } else {
                $pendingSalary = (new SalaryProcessDataService())->searchListOfEmployeesThoseSalaryRecordsStatusIsUnpaidForListViewByProjectAndSponsor($sponser_ids, $project_ids, $emp_type, $salary_month, $salary_year, Auth::user()->branch_office_id);
                //$pendingSalary = (new SalaryProcessDataService())->searchListOfEmployeesThoseSalaryRecordsStatusIsUnpaidForListViewByProjectAndSponsor($sponser_ids, $proj_id,$emp_type,$is_hourly, $salary_month,$salary_year,Auth::user()->branch_office_id);
            }
            if ($pendingSalary->count() > 0) {
                return response()->json(['status' => 200, 'success' => true, 'fromMonth' => $salary_month, 'pendingSalary' => $pendingSalary]);
            } else {
                return response()->json(['status' => 404, 'success' => false, 'message' => 'Records Not Found', 'error' => '']);
            }
        } catch (Exception $ex) {
            return response()->json(['status' => 404, 'success' => false, 'message' => 'Server Operation Failed', 'error' => $ex->getMessage()]);
        }
    }

    // Ajax Calling for  An Employee Salary Record Searching By Salary Hisotry Auto Id
    public function getAnEmployeeSalaryRecordBySalaryHistoryAutoId(Request $request)
    {

        $salary_record = (new SalaryProcessDataService())->getAnEmployeeSalaryRecordBySalaryHistoryAutoIdForEditing($request->slh_auto_id);
        return response()->json(['arecord' => $salary_record, 'status' => 200, 'success' => true, 'slh_auto_id' => $request->slh_auto_id]);
    }
    // Salary  Update Fron Pending Salary Emp List  AJAX Request


    // pending sallarye update
    public function updateAnEmployeeSalaryRecordBySalaryHistoryAutoId(Request $request)
    {


        $total_work_day = (int) $request->working_days;
        $total_amount = (float) $request->total_amount;
        $new_food_amount = (float) $request->new_food_amount;
        $saudi_tax = (float) $request->saudi_tax;
        $new_other_advance = (float) $request->new_other_advance;
        $new_iqama_advance = (float) $request->new_iqama_advance;
        $new_receivable_total_salary = $request->new_receivable_total_salary;
        $salary_month = (int) $request->salary_month;
        $salary_year = (int) $request->salary_year;
        $emp_auto_id = (int) $request->emp_auto_id;
        $slh_auto_id = (int) $request->slh_auto_id;
        $slh_all_include_amount = round($total_amount + $new_food_amount);
        $salary_paid__status = (int) $request->salary_paid__status;
        $catering_amount = (int)$request->catering_amount;
        $payment_mehtod = (int)$request->paytment_method;
        // return response()->json(['success' => false,'status' => 404, 'error' =>'error' ,'message'=>'U Try Again','data'=>$request->all()]);

        $update = (new SalaryProcessDataService())->updateAnEmployeeMonthlySalaryRecordUpdateSalaryStatusAsPaidBySalaryHistoryAutoId($slh_auto_id, $emp_auto_id, $slh_all_include_amount, $new_food_amount, $saudi_tax, $new_other_advance, $new_iqama_advance, $new_receivable_total_salary, Auth::user()->id, $salary_paid__status, $catering_amount);

        // update salary paid method
        if ($salary_paid__status == 1 && $update) {
            if ($payment_mehtod == 2) {

                $emp_salary_record = (new SalaryProcessDataService())->getAnEmployeeBankInfoWithSalaryRecordForUpdateSalaryPaidMethodBySalaryHistoryAutoId($slh_auto_id);

                // $emp_salary_record->ebd_auto_id is the employee active bank details auto id
                if ($emp_salary_record->ebd_auto_id != null &&   $emp_salary_record->payment_method  == 'Bank') {
                    (new SalaryProcessDataService())->updateEmployeeSalaryStatusAsPaidAndPaymentMethod((int) $slh_auto_id, (int) $emp_salary_record->ebd_auto_id, Auth::user()->id);
                } else {
                    return response()->json(['success' => false, 'status' => 404, 'error' => 'Please Select Payment Method', 'message' => 'Update Operation Failed, Please Update Salary Payment Method As Bank,']);
                }
            } else {
                (new SalaryProcessDataService())->updateEmployeeSalaryStatusAsCashPaid((int) $slh_auto_id, Auth::user()->id);
            }
        }


        if ($update) {

            $records = (new EmployeeAttendanceDataService())->getAnEmployeeMultiprojectWorkRecordsOnly($emp_auto_id, $salary_month, $salary_year);
            foreach ($records as $arecord) {
                $food_amount = 0;
                if ($new_food_amount > 0 && ((int)$total_work_day) > 0) {
                    $food_amount = round((($new_food_amount / $total_work_day) * $arecord->total_day), 2);
                }
                // multi project food amount calculation and update
                (new SalaryProcessDataService())->updateEmployeeMultipleProjectWorkSalaryAmount(
                    $arecord->empwh_auto_id,
                    ($arecord->total_amount + $food_amount),
                    $food_amount,
                    $arecord->other_amount,
                    $arecord->ot_amount,
                    Auth::user()->id
                );
            }
            // login user activities record
            (new AuthenticationDataService())->InsertLoginUserActivity(24, 2, Auth::user()->id, $emp_auto_id, "Salary Amount Updated From Pending Salary List");

            return response()->json(['success' => true, 'status' => 200, 'message' => 'Successfully Updated']);
        } else {
            return response()->json(['success' => false, 'status' => 404, 'error' => 'error', 'message' => 'Update Operation Failed, Please Try Again']);
        }
    }

    // Delete Employee Unpaid Salary Record
    public function deleteAnEmployeeUnpaidSalaryRecord($slh_auto_id)
    {

        $salary_record = (new SalaryProcessDataService())->getAnEmployeeSalaryRecordBySalaryHistoryAutoId($slh_auto_id);
        if ($salary_record->Status == 0) {

            $isDeleted = (new SalaryProcessDataService())->deleteAnEmployeeUnpaidSalaryRecordBySalaryHistoryAutoId($slh_auto_id);

            // login user activities record
            (new AuthenticationDataService())->InsertLoginUserActivity(24, 3, Auth::user()->id, $salary_record->emp_auto_id, 'Salary Record Deleted From Pending Salary List');

            return response()->json(['success' => true, 'status' => 200, 'message' => "Successfully Completed", 'data' => $slh_auto_id]);
        } else {
            return response()->json(['success' => false, 'status' => 404, 'error' => 'Salary Already Paid, Not Possible to Delete', 'data' => $slh_auto_id]);
        }
    }

    public function index()
    {
        $bank_names = (new EmployeeRelatedDataService())->getListOfBankNameOfABranchForDropdown(Auth::user()->branch_office_id);
        $projects   = (new ProjectDataService())->getAllActiveProjectListForDropdown();
        $leave_reasons = (new LeaveApplicationDataService())->getLeaveReasonRecordsForDropdown();
        $application_status = (new LeaveApplicationDataService())->getLeaveApplicationStatusForDropdown();
        $sponsors = (new EmployeeRelatedDataService())->getAllSponserInfoForDropdown();

        return view('payroll::pages.Payroll.salary', [
            'data' => [
                'bank_names' => $bank_names,
                'projects'   => $projects,
                'sponsors'   => $sponsors,
                'application_status' => $application_status,
                'leave_reasons' => $leave_reasons,
            ]
        ]);
    }

    public function showSalaryProcessNotYetCompletedEmployeesReport(Request $request)
    {
        // {"success":true,"data":{"month":"2","year":"2026","report_format":"1","report_type":"4","export":"pdf"},"message":"API is working fine"}

        //  return (new WPSSalaryReportService())->generateSalaryProcessNotYetCompletedEmployeesReport($request->project, $request->month, $request->year);
        return view('payroll::pages.Payroll.reports.salary_not_processed_emps', [
            'employees' => (new WPSSalaryReportService())->generateSalaryProcessNotYetCompletedEmployeesReport(null, $request->month, $request->year),
            'company' => (new CompanyDataService())->findACompanryProfileAllInformation(),
            'login_name' => Auth::user()->name,
        ]);
    }

    public function EmployeeSalaryPaidByBankReportForSendingToBank(Request $request)
    {

        if ($request->has('report_type') && $request->input('report_type') == 4) {
            return $this->showSalaryProcessNotYetCompletedEmployeesReport($request);
        }

        // return response()->json([
        //     'success' => true,
        //     'data' => $request->all(),
        //     'message' => 'API is working fine',
        // ]);
        $month = (int) $request->input('month'); // 1-12
        $year = (int) $request->input('year');   // 2025

        $project_ids = $request->input('project_id', []);
        if (!is_array($project_ids)) {
            $project_ids = array_filter(explode(',', (string) $project_ids));
        }
        $project_ids = array_values(array_filter($project_ids, fn($v) => $v !== null && $v !== ''));
        if (empty($project_ids)) {
            $project_ids = (new ProjectDataService())->getAllActiveProjectIDs();
        }

        $sponsor_ids = $request->input('sponsor_id', []);

        $sponsor_ids = array_values(array_filter($sponsor_ids, fn($v) => $v !== null && $v !== ''));
        if (empty($sponsor_ids)) {
            $sponsor_ids = (new EmployeeRelatedDataService())->getAllActiveSponsorIdAsArrayOfABranchOfficeBySponsorTypes(Auth::user()->branch_office_id, ['Asloob']);
        }

        $bank_ids = $request->input('bank_id', []);
        if (!is_array($bank_ids)) {
            $bank_ids = array_filter(explode(',', (string) $bank_ids));
        }
        $bank_ids = array_values(array_filter($bank_ids, fn($v) => $v !== null && $v !== ''));

        $search = $request->input('search');

        $export = $request->input('export'); // 'pdf' | 'excel' | null
        $reportFormat = (int) $request->input('report_format', 0); // legacy: 2 = excel


        // Base Query
        $query = EmployeeInfo::query()
            ->select([
                'employee_infos.emp_auto_id',
                'employee_infos.employee_id',
                'employee_infos.employee_name',
                'employee_infos.akama_no',
                'employee_infos.akama_expire_date',
                'employee_bank_details.acc_iban as iban',
                'employee_bank_details.acc_number as account_number',
                'bank_names.bank_code',

                'employee_categories.catg_name as designation',

                'project_infos.proj_name as project_name',

                'departments.dep_name as department_name',

                'salary_histories.slh_total_salary',
                'salary_histories.house_rent',
                'salary_histories.basic_amount',
                'salary_histories.mobile_allowance',
                'salary_histories.food_allowance',
                'salary_histories.mobile_allowance',
                'salary_histories.slh_iqama_advance',
                'salary_histories.slh_other_advance',
                'salary_histories.slh_food_deduction',
                'salary_histories.slh_paid_method',
                'salary_histories.slh_month',
                'salary_histories.slh_year',
            ])
            ->leftJoin('employee_bank_details', function ($join) {
                $join->on('employee_infos.emp_auto_id', '=', 'employee_bank_details.emp_auto_id');
                // ->where('employee_bank_details.is_active', 1);
            })
            ->leftJoin('bank_names', 'employee_bank_details.bank_id', '=', 'bank_names.bn_auto_id')

            ->leftJoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
            ->leftJoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
            ->leftJoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
            ->leftJoin('departments', 'employee_infos.department_id', '=', 'departments.dep_id')
            // ->whereNotNull('salary_histories.slh_paid_method')
            // ->where('salary_histories.slh_paid_method', 'like', '%Bank%')
            ->where('employee_bank_details.is_active', 1)
            ->where('employee_infos.job_status', 1) //only active employee
            ->whereNotNull('employee_bank_details.acc_iban')
            ->whereIn('employee_infos.sponsor_id', $sponsor_ids)
            ->whereIn('salary_histories.project_id', $project_ids)  // user can be select multiple projects,  but for test purpose we are using fixed project ids
            ->where('salary_histories.slh_month', $month)
            ->where('salary_histories.slh_year', $year);

        // Bank filter (optional)
        if (!empty($bank_ids)) {
            $query->whereIn('employee_bank_details.bank_id', $bank_ids);
        }

        //? Filters: Month, Year, Branch Office, Projects, Banks, Employees, Search
        // if ($month) {
        //     $query->where('salary_histories.slh_month', $month);
        // }

        // if ($year) {
        //     $query->where('salary_histories.slh_year', $year);
        // }

        // if ($branch_office_id) {
        //     $query->where('salary_histories.branch_office_id', $branch_office_id);
        // }

        // if (!empty($project_ids)) {
        //     $query->whereIn('salary_histories.project_id', $project_ids);
        // }

        // if (!empty($bank_ids)) {
        //     $query->whereIn('employee_bank_details.bank_id', $bank_ids);
        // }

        // if (!empty($employee_ids)) {
        //     $query->whereIn('employee_infos.emp_auto_id', $employee_ids);
        // }

        //? Search: Employee ID or Name or Akama No
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('employee_infos.employee_id', 'like', "%{$search}%")
                    ->orWhere('employee_infos.employee_name', 'like', "%{$search}%")
                    ->orWhere('employee_infos.akama_no', 'like', "%{$search}%")
                    ->orWhere('employee_bank_details.acc_iban', 'like', "%{$search}%");
            });
        }

        // Sorting
        $query->orderBy('employee_infos.employee_id', 'ASC')
            ->orderBy('project_infos.proj_name', 'ASC');

        // ================== EXPORTS (PDF/EXCEL) ==================
        if ($export === 'excel' || $reportFormat === 2) {
            $rows = $query->get();
            return Excel::download(
                new WpsSalaryTableExport($rows),
                $month . '_' . $year . '_WPS_Salary_Report.xlsx'
            );
        }

        if ($export === 'pdf') {
            $rows = $query->get();

            $data = [
                'rows' => $rows,
                'month' => $month,
                'year' => $year,
            ];
            return view('payroll::pages.Payroll.wps_salary_pdf', $data);

            // $pdf = Pdf::loadView('payroll::pages.Payroll.wps_salary_pdf', [
            //     'rows' => $rows,
            //     'month' => $month,
            //     'year' => $year,
            // ])->setPaper('a4', 'landscape');
            // return $pdf->stream('WPS_Salary_' . $month . '_' . $year . '.pdf');
        }

        // Pagination
        $perPage = $request->input('per_page', 10);
        $data = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Data retrieved successfully',
            'data'    => $data->items(),
            'pagination' => [
                'current_page'   => $data->currentPage(),
                'last_page'      => $data->lastPage(),
                'per_page'       => $data->perPage(),
                'total'          => $data->total(),
                'from'           => $data->firstItem(),
                'to'             => $data->lastItem(),
                'prev_page_url'  => $data->previousPageUrl(),
                'next_page_url'  => $data->nextPageUrl(),
            ]
        ]);
    }

    /**
     * Send payslip emails to selected employees
     * Dispatches a background job to handle large numbers of employees
     */
    public function sendPayslipEmails(Request $request)
    {
        try {
            $request->validate([
                'month' => 'required|integer|min:1|max:12',
                'year' => 'required|integer|min:2000|max:2100',
                // 'project_ids' => 'nullable|array',
                // 'project_ids.*' => 'integer',
                // 'employee_ids' => 'nullable|array',
                // 'employee_ids.*' => 'integer',
            ]);

            $month = $request->input('month');
            $year = $request->input('year');
            $projectIds = $request->input('project_ids', []);
            $employeeIds = $request->input('employee_ids');
            $branchOfficeId = Auth::user()->branch_office_id;
            $login_user_id = Auth::user()->id;
            if (is_null($employeeIds) == false) {
                $employeeIds = explode(",", $employeeIds);
                $employeeIds = array_unique($employeeIds); // remove multiple same empl ID
            } else {
                $employeeIds = $this->getEmployeeIdsByProjects($projectIds, $month, $year);
            }

            if (empty($employeeIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No employees found with salary records for the selected month/year'
                ], 404);
            }

            // Dispatch the job to process in background
            // SendPayslipEmailJob::dispatch($employeeIds, $month, $year, $branchOfficeId);
            foreach ($employeeIds as $employeeId) {
                SendSinglePayslipEmailJob::dispatch($employeeId, $month, $year,  $login_user_id);
            }

            return response()->json([
                'success' => true,
                'message' => count($employeeIds) . ' payslip sending jobs queued successfully.',
                'employee_count' => count($employeeIds)
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error dispatching payslip email job', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your request: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get employee IDs from projects that have salary records for the given month/year
     */
    private function getEmployeeIdsByProjects(array $projectIds, int $month, int $year): array
    {
        return DB::table('salary_histories')
            ->join('employee_infos', 'salary_histories.emp_auto_id', '=', 'employee_infos.emp_auto_id')
            ->whereIn('salary_histories.project_id', $projectIds)
            ->where('salary_histories.slh_month', $month)
            ->where('salary_histories.slh_year', $year)
            ->whereNotNull('employee_infos.email')
            ->where('employee_infos.email', '!=', '')
            ->distinct()
            ->pluck('employee_infos.employee_id')
            ->toArray();
    }

    /**
     * Get employees list for payslip selection (for the frontend dropdown)
     */
    public function getEmployeesForPayslip(Request $request)
    {
        try {
            $month = $request->input('month');
            $year = $request->input('year');
            $projectIds = $request->input('project_ids', []);


            $query = DB::table('employee_infos')
                ->select([
                    'employee_infos.emp_auto_id',
                    'employee_infos.employee_id',
                    'employee_infos.employee_name',
                    DB::raw('COALESCE(employee_infos.email, "") as email'),
                    'project_infos.proj_name as project_name',
                    'salary_histories.slh_total_salary',
                ])
                ->join('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
                ->leftJoin('project_infos', 'salary_histories.project_id', '=', 'project_infos.proj_id')
                ->where('salary_histories.slh_month', $month)
                ->where('salary_histories.slh_year', $year);



            if (!empty($projectIds)) {
                $query->whereIn('salary_histories.project_id', $projectIds);
            }
            $employees = $query->distinct()->orderBy('employee_infos.employee_name')->get();

            return response()->json([
                'success' => true,
                'data' => $employees
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching employees for payslip', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch employees'
            ], 500);
        }
    }









    /*
        =========================================================
        ================ Salary Preview Report ==================
        =========================================================

    */
    public function showWPSSalaryUsingExcelUpload(Request $request)
    {



        $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000|max:2100',
            'project_id' => 'required|integer',
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:2048', // max 2MB
        ]);

        $month = (int) $request->input('month');
        $year = (int) $request->input('year');
        $project_id = (int) $request->input('project_id');
        $operation_type_id = (int) $request->input('operation_type_id', 1);


        $file = $request->file('excel_file');

        // Process the uploaded Excel file and generate the preview data
        $dataService = new SalaryProcessDataService();
        $previewData = []; // $dataService->generateWPSSalaryPreviewFromExcel($file, $month, $year, $projectId);

        $extension = $file->getClientOriginalExtension(); //Get extension of uploaded file
        $fileSize = $file->getSize(); //Get size of uploaded file in bytes
        if (!$this->checkUploadedFileProperties($extension, $fileSize)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid file type or size'
            ], 422);
        }

        $import = new ImportWPSEmployeeForSalaryPreview($month, $year, $project_id);
        Excel::import($import, $file);

        // $salary_records = (new SalaryProcessDataService())->EmployeeSalaryPaidByBankReportForSendingToBank([$project_id],$month,  $year,Auth::user()->branch_office_id);
        //    $excel_file =  Excel::store(new WPSSalaryExportAsExcel( $import->records), '_'.$month.'_'.$year.'_Salary_Report.xlsx');
        //    $file_path = (new UploadDownloadController())->writeWPSSalaryGeneratedExcelFile($excel_file->getContent());

        // 1. Get ONLY the binary content (not a Response object)
        $binaryData = Excel::raw(new WPSSalaryExportAsExcel($import->records), \Maatwebsite\Excel\Excel::XLSX);
        $file_path = '_' . $month . '_' . $year . '_Salary_Report.xlsx';
        // 2. Upload the raw binary to S3
        Storage::disk('s3')->put('temporary/' . $file_path, $binaryData);
        //  $success = Storage::disk('s3')->put($destinationPath, $file);

        return response()->json([
            'success' => true,
            'message' => 'WPS Salary preview generated successfully from Excel upload',
            'data' => $import->records,
            'file_path' => config('app.aws_s3_bucket_url') . 'temporary/' . $file_path,
        ]);
    }


    public function checkUploadedFileProperties($extension, $fileSize)
    {
        $valid_extension = array("csv", "xlsx"); //Only want csv and excel files
        $maxFileSize = 5242888; // Uploaded file size limit is 5mb
        if (in_array(strtolower($extension), $valid_extension)) {
            if ($fileSize <= $maxFileSize) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }


    public function getLeaveApprovedButSalaryPendingApplications(Request $request)
    {

        $records = (new LeaveApplicationDataService())->getLeaveApplicationApprovedButSalaryPendingRecordsForListView($request, Auth::user()->branch_office_id);
        $final_records = [];
        $counter = 0;

        foreach ($records as $employee) {
            // $employee = (new EmployeeDataService())->searchAnEmployeeWithImportantInformationAsShowByMultitypeParameter($anEmp->emp_auto_id,$this->searchby_employee_id,Auth::user()->branch_office_id);

            if ($employee == null || !(new FiscalYearDataService())->checkAnEmployeeRunningFiscalYearIsAlreadyExist($employee->emp_auto_id)) {
                continue;
            }
            $fiscal = (new FiscalYearDataService())->getAnEmployeeRunningFiscalYearRecord($employee->emp_auto_id);
            $employee->closed_fiscal_record = (new FiscalYearDataService())->getAnEmployeeLastClosingFiscalYearRecord($employee->emp_auto_id);
            $employee->total_unpaid_salary_amount = (new SalaryProcessDataService())->getAnEmployeeUnpaidSalaryTotalAmountByFiscalYear($employee->emp_auto_id, $fiscal->start_date, $fiscal->end_date);
            // Advance Collection
            $employee->toal_iqama_expense_deduction_from_salary = (new SalaryProcessDataService())->getAnEmployeeTotalAmountOfIqamaExpenseDeductionFromSalaryByFiscalYear($employee->emp_auto_id, $fiscal->start_date, $fiscal->end_date);
            $employee->total_other_advace_deduction_from_salary = (new SalaryProcessDataService())->getAnEmployeeTotalAmountOfOtherAdvanceDeductionFromSalaryByFiscalYear($employee->emp_auto_id, $fiscal->start_date, $fiscal->end_date);
            $employee->cash_receive_total_paid_amount =  (new EmployeeAdvanceDataService())->getAnEmployeeAdvancePaidByCachTotalAmountByFiscalYear($employee->emp_auto_id, $fiscal->start_date, $fiscal->end_date);

            $total_collection = (int) $employee->toal_iqama_expense_deduction_from_salary + $employee->total_other_advace_deduction_from_salary + $employee->cash_receive_total_paid_amount;
            // Advance Give to Employee
            $employee->iqama_renewal_total_expence = (new EmployeeAdvanceDataService())->getAnEmployeeIqamaRenewalTotalExpenseAmountByFiscalYear($employee->emp_auto_id, $fiscal->start_date, $fiscal->end_date);
            $employee->other_advance_total_Amount = (new EmployeeAdvanceDataService())->getAnEmployeeOthersAdvanceGivenTotalAmountByFiscalYear($employee->emp_auto_id, $fiscal->start_date, $fiscal->end_date);

            $total_given = (int)  $employee->iqama_renewal_total_expence + $employee->other_advance_total_Amount + $employee->closed_fiscal_record->balance_amount;
            // other
            // $employee->toal_CPF_contribution_from_salary =  (new SalaryProcessDataService())->getAnEmployeeTotalAmountContributeToCPFByFiscalYear($employee->emp_auto_id,$fiscal->start_date,$fiscal->end_date);
            // $employee->total_saudi_tax = (new SalaryProcessDataService())->getAnEmployeeTotalAmountSaudiTaxDeductionFromSalaryByFiscalYear($employee->emp_auto_id,$fiscal->start_date,$fiscal->end_date);

            // $bonus_records = (new SalaryProcessDataService())->getAnEmployeeBonusRecordByFiscalYear($employee->emp_auto_id, $fiscal->start_year,$fiscal->end_year);
            $employee->balance_amount = (int) ($total_given - $total_collection + $employee->total_unpaid_salary_amount);
            $employee->advance_amount = 0;
            $employee->ticket_amount = 0;

            $final_records[$counter++] =     $employee;
        }


        return response()->json([
            'data' =>  $final_records,
            'pagination' => [
                'current_page' => 1, // $records->currentPage(),
                'last_page' => 1, // $records->lastPage(),
                'per_page' => 1000, // $records->perPage(),
                'total' => count($records), // $records->total(),
                'from' => 1, // $records->firstItem(),
                'to' => 1, // $records->lastItem()
            ],
            'status' => 200,
            'success' => true,
        ]);
    }

    public function getEmployeeSalaryReport(Request $request)
    {
        try {
            $month = (int) $request->input('month');
            $year = (int) $request->input('year');
            $reportType = (int) $request->input('report_type', 1); // 1=PDF, 2=Excel
            $projectIds = $request->input('project_ids', []);
            if (!is_array($projectIds)) {
                $projectIds = array_filter(explode(',', (string) $projectIds));
            }
            $projectIds = array_values(array_filter($projectIds, fn($v) => $v !== null && $v !== ''));

            if (empty($projectIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No project IDs provided'
                ], 422);
            }

            // Generate the report using the service
            return (new WPSSalaryReportService())->generateEmployeeSalaryReport($projectIds, $month, $year, $reportType);
        } catch (\Exception $e) {
            Log::error('Error generating employee salary report', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate employee salary report'
            ], 500);
        }
    }
}
