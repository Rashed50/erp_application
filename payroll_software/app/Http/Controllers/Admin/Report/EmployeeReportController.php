<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DataServices\EmployeeAdvanceDataService;
use App\Http\Controllers\DataServices\EmployeeDataService;
use App\Http\Controllers\DataServices\SalaryProcessDataService;
use App\Http\Controllers\DataServices\EmpActivityDataService;
use App\Http\Controllers\DataServices\CompanyDataService;
use App\Http\Controllers\DataServices\EmployeeRelatedDataService;
use App\Http\Controllers\DataServices\EmployeeAttendanceDataService;
use App\Http\Controllers\DataServices\ProjectDataService;
use App\Http\Controllers\DataServices\EmployeePromotionDataService;
use App\Http\Controllers\DataServices\AccommodationDataService;
use App\Http\Controllers\DataServices\EmpTUVInfoDataService;
use App\Http\Controllers\DataServices\BdOfficePaymentDataService;
use App\Http\Controllers\DataServices\FiscalYearDataService;
use App\Http\Controllers\Admin\CompanyProfileController;
use App\Http\Controllers\Admin\BdOfficePayment\BdOfficePaymentController;
use App\Http\Controllers\Admin\Helper\HelperController;
use App\Http\Controllers\Admin\ExcelExportController;
use App\Exports\{EmployeesExportBaseOnActivity,EmpListHRReportExport};

use Maatwebsite\Excel\Facades\Excel;
use App\Enums\EmployeeJobStatusEnum;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class EmployeeReportController extends Controller
{
        protected $searchby_employee_id = 'employee_id';
        protected $searchby_iqama_no = 'akama';

    public function downloadSystemGeneratedVarousForm(Request $request){

        $company = (new CompanyDataService())->findCompanryProfile();
        $prepared_by = Auth::user()->name;

        if($request->form_type == 1){

            $employee = (new EmployeeDataService())->searchingAnEmployeeInfoByMultitypeParameter($request->employee_id,'employee_id');
            if(count($employee)  <= 0){
                return "Employee Not Found ";
            }
            $employee = $employee[0];
            $last_increment = (new EmployeePromotionDataService())->getAnEmployeePromotionLastRecords($request->employee_id);

            $employee->last_increment_date = "";
            $employee->last_increment_amount  = 0;

            if($last_increment){
                $employee->last_increment_date = $last_increment->prom_date;
                $employee->last_increment_amount  = $last_increment->increment_amount;
            }

            $declaration_text = "";
            $form_title = "EMPLOYEE PERFORMANCE EVALUATION FORM";
            $employee->increment_duration = $request->duration;
            $employee->new_salary_type  = $request->new_salary_type;
            $effective_date = $request->effective_date;
            $remarks = $request->remarks;
            $amount = $request->amount;
            $in_word = (new HelperController())->numberToWord($amount);

            return view('admin.download_form.increment_form',compact('employee','company','amount','in_word','effective_date','prepared_by','remarks','declaration_text','form_title'));


        }

    }


    /*  =====================================================================
            ========= HR Related Employee Reports Processing FORM  =========
          =====================================================================
      */
    public function loadHRRelatedEmployeeReportForm()
    {

        $projects = (new ProjectDataService())->getAllActiveProjectListForDropdown(Auth::user()->branch_office_id);
        $sponser = (new EmployeeRelatedDataService())->getAllSponserInfoForDropdown(Auth::user()->branch_office_id);
        $category = (new EmployeeRelatedDataService())->getEmpAllCategoryInfoForDropdown();
        $designation_heads = (new EmployeeRelatedDataService())->getDesignationHeadRecordsForDropdown();
        $jobStatus = EmployeeJobStatusEnum::cases();
        $emp_types = (new EmployeeRelatedDataService())->getEmployeeTypeForDropdown();
        $accomdOfficeBuilding = (new AccommodationDataService())->getAllActiveOfficeBuildingNameIdAndCityForDropdownList();

        return view('admin.report.hr_section.hr_report_processing_form', compact('projects', 'sponser', 'jobStatus', 'category', 'emp_types', 'accomdOfficeBuilding','designation_heads'));
    }




        /*  =====================================================================
           ======== Multiple EmpID base Employee Details Report  ========
        =====================================================================
    */
    public function ProcessAndShowMultipleIDBaseEmployeeDetailsReport(Request $request)
    {

       try{
            $allEmplId = explode(",", $request->multiple_employee_Id);
            $allEmplId = array_unique($allEmplId); // remove multiple same empl ID

             $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
            if($request->report_type == 1){
                        // employee details
                        // $employees = (new EmployeeDataService())->getEmployeeDetailsWitFileDownloadReportByMultipleEmpID($allEmplId,Auth::user()->branch_office_id);
                        // $project = "-";
                        // $report_title = "Multiple Employee ID";
                        // return view('admin.report.hr_section.multiple_id_employee_details', compact('employees', 'company', 'project', 'report_title'));
                   # $employees = (new EmployeeDataService())->getMultipleEmployeeDetailsInformationForHRReportByMultipleEmpID($allEmplId,Auth::user()->branch_office_id);
                    $employees = (new EmployeeDataService())->getHREmployeesReporttByMultipleEmployeeIDsToExportAsExcel($allEmplId,Auth::user()->branch_office_id);

                    if($request->report_format == 2){
                        // excel download
                        return Excel::download(new EmpListHRReportExport($employees), 'hr_employee_list.xlsx');

                    }else {
                        // pdf view
                        $current_datetime = now()->format('d M Y h:i A');
                        $pdf = Pdf::loadView('admin.report.hr_section.multi_emp_id_details_pdf', [
                            'company' => $company,
                            'current_datetime' => $current_datetime,
                            'employees' => $employees,
                            'prepared_by' => Auth::user()->name,
                        ])->setPaper('a4','landscape');
                        //! Open PDF in browser
                        return $pdf->stream('expense.pdf');
                    }


            }else if($request->report_type == 2){
                // employee activities details

                $records = (new EmployeeDataService())->getAnEmployeeActivitiesReportByEmployeeId($allEmplId[0],Auth::user()->branch_office_id);
                $loggedInUser = Auth::user()->name;
                return view('admin.report.hr_section.emp_activity_report', compact('records', 'company', 'loggedInUser'));
            }else if($request->report_type == 3){
                   // Employee working project history
                $employee = (new EmployeeDataService())->getAnEmployeeInfoTableDataByEmployeeIdAndBranchOfficeId($allEmplId[0],Auth::user()->branch_office_id);
                if($employee == null){
                        return "Employee Not Found";
                }
                return $this->processAndDisplayAnEmployeeWorkingPrjectHisotry($employee);
            }
            else if($request->report_type == 4){
                    // multiple employee prevacation salary statement
                 return $this->prevacationSalaryStatementReport( $allEmplId);
                }
            else {
                Session::flash('error', 'Employee Not Found');
                return redirect()->back();
            }
       }catch(Exception $ex){
            Session::flash('error', 'Employee Not Found');
            return redirect()->back();
       }


    }
        /*  =====================================================================
         ========= 2 no HR Report Project Wise Employee Summary Report  =====
        =====================================================================
    */

    public function processAndShowProjectwiseEmployeeSummaryReport(Request $request)
    {

        $company =  (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
        $emp_status = (new EmployeeRelatedDataService())->getEmployeeJobStatusTitle(1);
        $project_id_list = $request->project_id_list;
        if ($project_id_list == null) {
            $project_id_list = (new ProjectDataService())->getAllActiveProjectIDOfABranchOfficeAsArray(Auth::user()->branch_office_id);
         }

        $job_status = 1;
        if ($request->report_format == 1) {

            // trade wise total number of employees
            $project_name = "";
            foreach($project_id_list as $aprojectId)
                $project_name =  $project_name."\n* ". (new ProjectDataService())->getProjectNameByProjectId($aprojectId);


          $list = (new EmployeeDataService())->getTradeWiseEmployeeSummaryReportOfAbranchOffice($project_id_list, $job_status,Auth::user()->branch_office_id);
          return view('admin.report.hr_section.all_trade_wise_emp_summary_report', compact('list', 'company', 'emp_status', 'project_name'));

        } else if ($request->report_format == 2) {  // Project wise sumamry

            $list = (new EmployeeDataService())->countProjectWiseTotalEmployees($project_id_list, $job_status);
             return view('admin.report.hr_section.all_projec_wise_emp_summary_report', compact('list', 'company', 'emp_status'));
        }
        else if($request->report_format == 3){

            $report_records = array();
            $trade_counter = 0;
            $update_trade_list = array();
            $trade_list = (new EmployeeRelatedDataService())->getEmpAllCategoryWithRankingSequence();
            foreach($trade_list as $atrade){

               $project_emp = array();
               $counter = 0;
               $trade_wise_total_emp = 0;
               foreach($project_id_list as $aprojectId)    {
                    $aproject =  (new ProjectDataService())->findAProjectInformation($aprojectId);
                    $total_emp = (new EmployeeDataService())->getTotalNoOfEmployeeByProjectAndTradeWise($aprojectId,$atrade->catg_id, 1 );
                  //  dd($total_emp,$aprojectId,$atrade->catg_id,$job_status);
                    $trade_wise_total_emp += $total_emp;
                    $aproject->total_emp = $total_emp;
                    $project_emp[$counter] = $aproject;
                    $counter += 1;
                }
                $atrade->project_info = $project_emp;
                $atrade->trade_wise_total_emp = $trade_wise_total_emp;
                $update_trade_list[$trade_counter] = $atrade;
                $trade_counter += 1;

            }
            $project_list = array();
            $counter = 0;
            foreach($project_id_list as $aprojectId)    {
                   $aproject =  (new ProjectDataService())->findAProjectInformation($aprojectId);
                   $aproject->total_emp = (new EmployeeDataService())->countTotalEmployeesInAProject($aprojectId,$job_status );
                   $project_list[$counter] = $aproject;
                   $counter += 1;
            }
            $reporter_name = Auth::user()->name;
            return view('admin.report.hr_section.total_manpower_pivot_table', compact('project_list','report_records','update_trade_list', 'company','reporter_name'));
        }
        else if ($request->report_format == 4) {
            // Project wise Manpower Summary by Sponsor
             $project_name = "";
            foreach($project_id_list as $aprojectId)
                $project_name =  $project_name."\n* ". (new ProjectDataService())->getProjectNameByProjectId($aprojectId);

           $records = (new EmployeeDataService())->getProjectsEmployeeSummaryBySponsorReport($project_id_list, $job_status);
           return view('admin.report.hr_section.summary.project_manpower_sum_bysponsor_report', compact('records', 'company','project_name'));
       }

    }

    // Download Emp Iqama File
    public function downloadEmployeeIqamaFile($employee_id){

        try {

            $emp = (new EmployeeDataService())->getAnEmployeeInfoByEmpId($employee_id);

            $lst = explode('.', $emp->akama_photo);
            $file_ext = $lst[1];
          //  $file= public_path().'/'.
            $file = $emp->akama_photo;
            $headers = array(
                    'Content-Type: application/'.$file_ext,
                    );
           // dd($file);
            return Response::download($file, $emp->employee_id.'-iqama.'.$file_ext, $headers);
        }catch(Exception $ex){
            return $ex;
        }

    }
    // download emp passport file

    public function downloadEmployeePassportFile($employee_id){

        try {

            $emp = (new EmployeeDataService())->getAnEmployeeInfoByEmpId($employee_id);

            $lst = explode('.', $emp->pasfort_photo);
            $file_ext = $lst[1];
            $file = $emp->pasfort_photo; // server code for file path
            $headers = array(
                    'Content-Type: application/'.$file_ext,
                    );
            return Response::download($file, $emp->employee_id.'-passport.'.$file_ext, $headers);
        }catch(Exception $ex){
            return $ex;
        }

    }

        // 4. HR Related Employee Reports with File Download
    public function processHRRelatedEmployeeReport(Request $request)
    {
       // dd($request->all());
        ini_set('max_execution_time', '600');

        if ($request->has('project_id_list')) {
            $project_id_list = $request->project_id_list;
        }else{
            $project_id_list = (new ProjectDataService())->getAllActiveProjectIDOfABranchOfficeAsArray(Auth::user()->branch_office_id);
        }

         if ($request->has('spons_id')) {
            $sponosor_ids = $request->spons_id;
        }else{
            $sponosor_ids = (new EmployeeRelatedDataService())->getAllActiveSponsorIdAsArrayOfABranchOffice(Auth::user()->branch_office_id);
        }


        if ($request->has('catg_id')) {
            $trade_ids = $request->catg_id;
        }else{
            $trade_ids = (new EmployeeRelatedDataService())->getAllActiveCategoryIDAsArrayWithRankingSequence();
        }

        if ($request->has('job_status')) {
            $job_status_ids = $request->job_status;
        }else{
            $job_status_ids = [1]; // Default to active employees
        }

        $company =  (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
        if ($request->report_format == 2) // excell format
        {

            $employees = (new EmployeeDataService())->getHREmployeesReporttByMultipleProjectSponorTradeAndJobStatusToExportAsExcel(
                $project_id_list, // list of project id
                $sponosor_ids,
                $trade_ids,
                $job_status_ids,$request->working_shift);
            return Excel::download(new EmpListHRReportExport($employees), 'hr_employee_list.xlsx');

        }
        $project = "Project Name : ";
        $employee = (new EmployeeDataService())->getHREmployeesReporttByMultipleProjectSponorTradeAndJobStatus(
            $project_id_list, // list of project id
            $sponosor_ids,
            $trade_ids,
            $job_status_ids,
            Auth::user()->branch_office_id
        );
        $report_title = "";
        return view('admin.report.hr_section.project_wise_emp_list_report', compact('employee', 'company', 'project', 'report_title'));


    }


    // HR Related Company Villa Wise Employee Reports Processing and Show
    public function processHRRelatedCompanyVillaWiseEmployeeReport(Request $request)
    {
        if ($request->accomd_ofb_id == null) {
            Session::flash('error', 'Operation Failed, Please try again');
            return redirect()->back();
        }

        $project = "";
        $company = (new CompanyProfileController())->findCompanry();
        $employee = (new EmployeeDataService())->getHREmployeeReportByVillaNameProjectAndTrade($request->accomd_ofb_id, $request->project_name_id, $request->catg_id);
        if ($employee->count() > 0) {
            return view('admin.report.hr_section.villa_wise_emp_report', compact('employee', 'company', 'project'));
        } else {
            Session::flash('error', 'Please Select Villa Name');
            return redirect()->back();
        }
    }

    // Employee list Project and Employee Type Report
    public function projectAndEmployeTypeWiseEmployeeListReportRequest(Request $request)
    {
        $project = "All Project";
        $project_id = $request->proj_id;

        if ($project_id != null) {
            $project = (new EmployeeRelatedDataService())->getProjectNameByProjectId($project_id);
        }
        $emp_type_id = $request->emp_type_id;

        $isHourly = NULL;

        if ($emp_type_id == null) {
            $report_title = "Employee Type All";
        } else if ($emp_type_id == -1) {
            $report_title = "Direct Employee(Basic Salary)";
            $emp_type_id = 1;
        } else if ($emp_type_id == 1) {
            $report_title = "Direct Employee(H)";
            $isHourly = true;
        } else if ($emp_type_id == 2) {
            $report_title = "Indirect Employee";
        } else if ($emp_type_id == 3) {
            $report_title = "All Basic Salary Employees";
            $isHourly = true;
        }
        $employee = (new EmployeeDataService())->getEmployeeInfoWithSalaryDetailsReportByProjectEmpTypeAndHourlyEmp($project_id, $emp_type_id, $isHourly);

        $company = (new CompanyProfileController())->findCompanry();

        if ($employee->count() > 0) {
            return view('admin.employee-info.project_wise.report', compact('employee', 'company', 'project', 'report_title'));
        } else {
            Session::flash('error', 'Employee Not Found');
            return redirect()->back();
        }
    }

    // 5 HR Report project wise Employee list Iqama Expired Report

    public function projectWiseEmployeeListWithIqamaExpiredateProcess(Request $request)
    {

        $fromDate = Carbon::now();
        $company =  (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);

        $sponsor_id_list = $request->sponsor_id_list;
        if($sponsor_id_list == null){
            $sponsor_id_list = (new EmployeeRelatedDataService())->getAllActiveSponsorIdAsArrayOfABranchOffice(Auth::user()->branch_office_id);
        }

        $project_id_list = $request->project_id_list;
        if($project_id_list == null){
            $project_id_list = (new ProjectDataService())->getAllActiveProjectIDOfABranchOfficeAsArray(Auth::user()->branch_office_id);
        }
         // Duration Wise Employee List
        if ($request->expire_durations == 1) { // Already Expired
            $toDate = $fromDate;
            $fromDate = Carbon::now()->subDays(2000);
            $employees = (new EmployeeDataService())->getProjectWiseEmployeeListByIqamaExpireDate($project_id_list,$sponsor_id_list, $fromDate, $toDate, 1);
        }
         elseif ($request->expire_durations == 2) { // This Month From Today Expired

            $lastDateofMonth = Carbon::now()->endOfMonth()->toDateString();
            $employees = (new EmployeeDataService())->getProjectWiseEmployeeListByIqamaExpireDate($project_id_list,$sponsor_id_list, $fromDate, $lastDateofMonth, 1);
        } elseif ($request->expire_durations == 3) { // Next Month From Today Expired

            $lastDateofMonth = Carbon::now()->addMonths(1)->endOfMonth()->toDateString();
            $employees = (new EmployeeDataService())->getProjectWiseEmployeeListByIqamaExpireDate($project_id_list,$sponsor_id_list, $fromDate, $lastDateofMonth, 1);
        } elseif ($request->expire_durations == 4) { // After 3 Months From Today Expired

            $lastDateofMonth = Carbon::now()->addMonths(3)->endOfMonth()->toDateString();
            $employees = (new EmployeeDataService())->getProjectWiseEmployeeListByIqamaExpireDate($project_id_list,$sponsor_id_list, $fromDate, $lastDateofMonth, 1);
        } elseif ($request->expire_durations == 5) { // After 6 Months From Today Expired

            $lastDateofMonth = Carbon::now()->addMonths(6)->endOfMonth()->toDateString();
            $employees = (new EmployeeDataService())->getProjectWiseEmployeeListByIqamaExpireDate($project_id_list,$sponsor_id_list, $fromDate, $lastDateofMonth, 1);
        } elseif ($request->expire_durations == 6) { // After 6 Months From Today Expired

            $lastDateofMonth = Carbon::now()->addMonths(12)->endOfMonth()->toDateString();
            $employee = (new EmployeeDataService())->getProjectWiseEmployeeListByIqamaExpireDate($project_id_list,$sponsor_id_list, $fromDate, $lastDateofMonth, 1);
        } elseif ($request->expire_durations == 7) {

            $lastDateofMonth = Carbon::now()->addMonths(24)->endOfMonth()->toDateString();
            $employees = (new EmployeeDataService())->getProjectWiseEmployeeListByIqamaExpireDate($project_id_list, $sponsor_id_list,$fromDate, $lastDateofMonth, 1);
        }elseif ($request->expire_durations == 8) {
            // iqama number not updated empl list report
                $employees = (new EmployeeDataService())->getEmployeeListThoseAreNotUpdateIqamaNumber($project_id_list,$sponsor_id_list,1);
        }elseif ($request->expire_durations == 9) {
            // Iqama file not found emp report
            $employees = (new EmployeeDataService())->getEmployeeListThoseAreNotUploadedIqamaFile($project_id_list,$sponsor_id_list,1);

        }elseif ($request->expire_durations == 10) {

            // Iqama Valid Employee List
            $lastDateofMonth = Carbon::now()->addMonths(72)->endOfMonth()->toDateString();
            $employees = (new EmployeeDataService())->getProjectWiseEmployeeListByIqamaExpireDate($project_id_list, $sponsor_id_list,$fromDate, $lastDateofMonth, 1);

        }else {
            Session::flash('error', 'Employee Not Found');
            return redirect()->back();
        }
        $report_title = "";
        return view('admin.report.hr_section.emp_list_iqama_expired', compact('employees', 'company', 'report_title'));
    }


    /*  =====================================================================
        ==================== Employee Salary Summary ====================
        =====================================================================
    */

    //  UI for viewing Emp. Salary Summary and Details Report
    public function loadEmployeeSalaryReportProcessUI()
    {
        return view('admin.report.employee_summary.emp_salary_summary_processing_form');
    }
    //   display employee salary summary report
    public function createAnEmployeeSalarySummaryReport(Request $request)
    {
    //    dd($request->all());
        $employee_id = $request->emp_id;  // employee id or iqama number
        $employee = (new EmployeeDataService())->getAnEmployeeInfoTableDataByEmployeeIdAndBranchOfficeId($employee_id,Auth::user()->branch_office_id);
        if ($employee == null) {
            // search by iqama number
            $employee = (new EmployeeDataService())->getAnEmployeeInfoTableDataByEmployeeIqamaNumberAndBranchOfficeId($employee_id,Auth::user()->branch_office_id);
        }

        if ($employee == null) {
            return 'Employee Not Found';
        }

        if ($request->emp_report_type == 1) {
            // Slary Summary Report
            return $this->processAndDisplayAnEmployeeUnpaidSalarySummaryReport($employee, null);
        } else if ($request->emp_report_type == 2) {
            // Salary running fiscal year deduction details Report
                return $this->processAndDisplayAnEmployeeSalaryFiscalYearClosingSummaryDetailsReport($employee);
         } else if ($request->emp_report_type == 3) {
            // Salary Report for Employee Copy
            return $this->processAndDisplayAnEmployeeSummaryReportForEmployeeCopy($employee, null);
        } else if ($request->emp_report_type == 4) {
            // Working project History
            return $this->processAndDisplayAnEmployeeWorkingPrjectHisotry($employee);
        } else if ($request->emp_report_type == 5) {
            // Advance History
            return $this->processAndDisplayAnEmployeeIqamaAndAdvanceHistoryReport($employee);
        } else if ($request->emp_report_type == 6) {
            // BD OFfice Payment History
            return $this->processAndDisplayAnEmployeePaidFromBDOffice($employee);
        }
         else if ($request->emp_report_type == 7) {
            // Salary Closing Summary
                return $this->processAndDisplayAnEmployeeSalaryAllRecordsSummaryReport($employee, null);
         }
         else if($request->emp_report_type == 8){
          // Salary Paid all records report for employee
          return $this->processAndDisplayAnEmployeeSalaryAllPaidRecordsSummaryReport($employee, null);

        }
         else if($request->emp_report_type == 9){
          // employee closing agreement paper report
          return $this->processAndDisplayAnEmployeeClosingAgreementPaperReport($employee, null);

        }
         else {
            Session::flash('not_found', 'Operation Failed');
            return redirect()->back();
        }
    }
      // 1 Unpaid Salary Summary Report
    private function processAndDisplayAnEmployeeUnpaidSalarySummaryReport($employee)
    {
        $company =  (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
        $unpaid_salary_records = (new SalaryProcessDataService())->getAnEmployeeSalaryRecordsByPaidUnpaidStatus($employee->emp_auto_id, null, 0);
        $totalUnPaidSalaryAmount = (new SalaryProcessDataService())->getAnEmployeeTotalUnPaidSalary($employee->emp_auto_id, null);

        // Advance Collection
        $toal_iqama_expense_deduction_from_salary = (new SalaryProcessDataService())->getTotalAmountOfIqamaExpenseDeductionFromSalary($employee->emp_auto_id, null);
        $total_other_advace_deduction_from_salary = (new SalaryProcessDataService())->getTotalAmountOfOtherAdvanceDeductionFromSalary($employee->emp_auto_id, null);
        $cashReceiveTotalPaidAmount =  (new EmployeeAdvanceDataService())->getAnEmployeeAdvancePaidTotalAmount($employee->emp_auto_id, null, 100);
        // 100 = Cash Received By Employee

        // Advance Give to Employee
        $iqamaExpenseAllRecords = (new EmployeeAdvanceDataService())->getAnEmployeeIqamaAnnualExpenseAllRecords($employee->emp_auto_id);


        $iqamaRenewalTotalExpence = (new EmployeeAdvanceDataService())->getAnEmployeeIqamaTotalCost($employee->emp_auto_id);
        $otherAdvanceTotalAmount = (new EmployeeAdvanceDataService())->getAnEmployeeOthersAdvanceTotalAmount($employee->emp_auto_id, null, 0); // 0 mean other type addvance taken by employee

        $toal_CPF_contribution_from_salary =  (new SalaryProcessDataService())->getTotalAmountOfCPFContributionFromSalary($employee->emp_auto_id, null);
        $totalSaudiTax = (new SalaryProcessDataService())->getAnEmployeeTotalAmountOfSautiTaxDeductionFromSalaryTotalAdvance($employee->emp_auto_id, null);
        $empl_last_activity = (new EmpActivityDataService())->getAnEmployeeLastActivityComments($employee->emp_auto_id);
        $bd_office_paid_total_amount = (new BdOfficePaymentDataService())->getAnEmployeeTotalPaidFromBdOffice($employee->emp_auto_id);
        $login_name = Auth::user()->name;

        return view(
            'admin.report.employee_summary.emp_unpaid_salary_report',
            compact('unpaid_salary_records', 'toal_CPF_contribution_from_salary', 'toal_iqama_expense_deduction_from_salary', 'totalSaudiTax', 'totalUnPaidSalaryAmount', 'iqamaRenewalTotalExpence', 'cashReceiveTotalPaidAmount',
            'total_other_advace_deduction_from_salary', 'otherAdvanceTotalAmount', 'employee', 'company', 'empl_last_activity','bd_office_paid_total_amount','login_name')
        );
    }



    // 2 Employee Salary Running Fiscal Year Report
 private function processAndDisplayAnEmployeeSalaryFiscalYearClosingSummaryDetailsReport($employee)
    {


            $company =  (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
            $fiscal = (new FiscalYearDataService())->getAnEmployeeRunningFiscalYearRecord($employee->emp_auto_id);
            $closed_fiscal_record = (new FiscalYearDataService())->getAnEmployeeLastClosingFiscalYearRecord($employee->emp_auto_id);

            $salary_records = (new SalaryProcessDataService())->getAnEmployeeSalaryRecordsByFiscalYear($employee->emp_auto_id,$fiscal->start_date,$fiscal->end_date);
            $totalUnPaidSalaryAmount = (new SalaryProcessDataService())->getAnEmployeeUnpaidSalaryTotalAmountByFiscalYear($employee->emp_auto_id,$fiscal->start_date,$fiscal->end_date);

            // Advance Collection
            $toal_iqama_expense_deduction_from_salary = (new SalaryProcessDataService())->getAnEmployeeTotalAmountOfIqamaExpenseDeductionFromSalaryByFiscalYear($employee->emp_auto_id,$fiscal->start_date,$fiscal->end_date);
            $total_other_advace_deduction_from_salary = (new SalaryProcessDataService())->getAnEmployeeTotalAmountOfOtherAdvanceDeductionFromSalaryByFiscalYear($employee->emp_auto_id,$fiscal->start_date,$fiscal->end_date);
            $cashReceiveTotalPaidAmount =  (new EmployeeAdvanceDataService())->getAnEmployeeAdvancePaidByCachTotalAmountByFiscalYear($employee->emp_auto_id,$fiscal->start_date,$fiscal->end_date);
            $iqamaExpenseAllRecords = (new EmployeeAdvanceDataService())->getAnEmployeeIqamaExpenseSelfAndCompanyAllRecordsByFiscalYear($employee->emp_auto_id,$fiscal->start_date,$fiscal->end_date);

            // Advance Give to Employee
            $iqamaRenewalTotalExpence = (new EmployeeAdvanceDataService())->getAnEmployeeIqamaRenewalTotalExpenseAmountByFiscalYear($employee->emp_auto_id,$fiscal->start_date,$fiscal->end_date);

            $otherAdvanceTotalAmount = (new EmployeeAdvanceDataService())->getAnEmployeeOthersAdvanceGivenTotalAmountByFiscalYear($employee->emp_auto_id,$fiscal->start_date,$fiscal->end_date);
            $toal_CPF_contribution_from_salary =  (new SalaryProcessDataService())->getAnEmployeeTotalAmountContributeToCPFByFiscalYear($employee->emp_auto_id,$fiscal->start_date,$fiscal->end_date);
            $totalSaudiTax = (new SalaryProcessDataService())->getAnEmployeeTotalAmountSaudiTaxDeductionFromSalaryByFiscalYear($employee->emp_auto_id,$fiscal->start_date,$fiscal->end_date);
            $empl_last_activity = (new EmpActivityDataService())->getAnEmployeeLastActivityComments($employee->emp_auto_id);
            $bonus_records = (new SalaryProcessDataService())->getAnEmployeeBonusRecordByFiscalYear($employee->emp_auto_id, $fiscal->start_year,$fiscal->end_year);

            $closing_balnce = ($iqamaRenewalTotalExpence + $otherAdvanceTotalAmount + $closed_fiscal_record->balance_amount) - ($toal_iqama_expense_deduction_from_salary +
            $total_other_advace_deduction_from_salary+$cashReceiveTotalPaidAmount + $totalUnPaidSalaryAmount );

            $login_name= Auth::user()->name;
            return view('admin.report.employee_summary.salary_summary_runing_fiscal_year', compact('totalUnPaidSalaryAmount', 'toal_iqama_expense_deduction_from_salary',
             'salary_records',  'cashReceiveTotalPaidAmount', 'otherAdvanceTotalAmount', 'total_other_advace_deduction_from_salary', 'toal_CPF_contribution_from_salary',
             'totalSaudiTax', 'company', 'iqamaRenewalTotalExpence', 'employee', 'iqamaExpenseAllRecords', 'empl_last_activity','bonus_records','closed_fiscal_record','login_name'));


    }

   // 3 Salary Report for Employee Copy

    private function processAndDisplayAnEmployeeSummaryReportForEmployeeCopy($employee)
    {

        $unpaid_salary_records = (new SalaryProcessDataService())->getAnEmployeeSalaryRecordsByPaidUnpaidStatus($employee->emp_auto_id, null, 0);
        $company =  (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);

        $fiscal = (new FiscalYearDataService())->getAnEmployeeRunningFiscalYearRecord($employee->emp_auto_id);
        $closed_fiscal_record = (new FiscalYearDataService())->getAnEmployeeLastClosingFiscalYearRecord($employee->emp_auto_id);

        $salary_records = (new SalaryProcessDataService())->getAnEmployeeSalaryRecordsByFiscalYear($employee->emp_auto_id,$fiscal->start_date,$fiscal->end_date);
        $totalUnPaidSalaryAmount = (new SalaryProcessDataService())->getAnEmployeeUnpaidSalaryTotalAmountByFiscalYear($employee->emp_auto_id,$fiscal->start_date,$fiscal->end_date);
        // Advance Collection
        $toal_iqama_expense_deduction_from_salary = (new SalaryProcessDataService())->getAnEmployeeTotalAmountOfIqamaExpenseDeductionFromSalaryByFiscalYear($employee->emp_auto_id,$fiscal->start_date,$fiscal->end_date);
        $total_other_advace_deduction_from_salary = (new SalaryProcessDataService())->getAnEmployeeTotalAmountOfOtherAdvanceDeductionFromSalaryByFiscalYear($employee->emp_auto_id,$fiscal->start_date,$fiscal->end_date);
        $cashReceiveTotalPaidAmount =  (new EmployeeAdvanceDataService())->getAnEmployeeAdvancePaidByCachTotalAmountByFiscalYear($employee->emp_auto_id,$fiscal->start_date,$fiscal->end_date);
        $iqamaExpenseAllRecords = (new EmployeeAdvanceDataService())->getAnEmployeeIqamaExpenseSelfAndCompanyAllRecordsByFiscalYear($employee->emp_auto_id,$fiscal->start_date,$fiscal->end_date);
        // Advance Give to Employee
        $iqamaRenewalTotalExpence = (new EmployeeAdvanceDataService())->getAnEmployeeIqamaRenewalTotalExpenseAmountByFiscalYear($employee->emp_auto_id,$fiscal->start_date,$fiscal->end_date);
        $otherAdvanceTotalAmount = (new EmployeeAdvanceDataService())->getAnEmployeeOthersAdvanceGivenTotalAmountByFiscalYear($employee->emp_auto_id,$fiscal->start_date,$fiscal->end_date);
        $toal_CPF_contribution_from_salary =  (new SalaryProcessDataService())->getAnEmployeeTotalAmountContributeToCPFByFiscalYear($employee->emp_auto_id,$fiscal->start_date,$fiscal->end_date);
        $totalSaudiTax = (new SalaryProcessDataService())->getAnEmployeeTotalAmountSaudiTaxDeductionFromSalaryByFiscalYear($employee->emp_auto_id,$fiscal->start_date,$fiscal->end_date);
        $empl_last_activity = (new EmpActivityDataService())->getAnEmployeeLastActivityComments($employee->emp_auto_id);
        $bonus_records = (new SalaryProcessDataService())->getAnEmployeeBonusRecordByFiscalYear($employee->emp_auto_id, $fiscal->start_year,$fiscal->end_year);
     $login_user = Auth::user()->name;
        return view(
            'admin.report.employee_summary.summary_report_emp_copy',
            compact('totalUnPaidSalaryAmount', 'toal_iqama_expense_deduction_from_salary', 'unpaid_salary_records',  'cashReceiveTotalPaidAmount',
             'otherAdvanceTotalAmount', 'total_other_advace_deduction_from_salary', 'toal_CPF_contribution_from_salary', 'totalSaudiTax', 'company',
             'iqamaRenewalTotalExpence', 'employee', 'iqamaExpenseAllRecords','closed_fiscal_record','login_user')
        );
    }


    // 4 ======== Employee Working Project Hisotry =========
    private function processAndDisplayAnEmployeeWorkingPrjectHisotry($employee_info)
    {
        $company =  (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
        $employeeInfos = (new EmployeeRelatedDataService())->getAnEmployeeWorkingProjectHisotry($employee_info->emp_auto_id);
        return view('admin.report.employee_summary.emp_work_project_history_report', compact('company', 'employeeInfos'));
    }

    // 5 Iqama and  Advance History
    private function processAndDisplayAnEmployeeIqamaAndAdvanceHistoryReport($employee)
    {

         $fiscal = (new FiscalYearDataService())->getAnEmployeeRunningFiscalYearRecord($employee->emp_auto_id);
        $month_years = (new HelperController())->getMonthsInRangeOfDate($fiscal->start_date,$fiscal->end_date);
        $summary_records = array();
        $counter = 0;
        foreach($month_years as $month_year){
            $arecord = new \stdClass();
            $arecord->month = $month_year['month'];
            $arecord->year = $month_year['year'];
            $iq_record = (new EmployeeAdvanceDataService())->getIqamaRenewalExpenseRecordForExpenseSummaryReportByEmployeeAutoId($employee->emp_auto_id,$month_year['month'],$month_year['year']);
            $is_found = false;
            if($iq_record != null){
                $arecord->iqama_expense = $iq_record->total_amount;
                $arecord->iqama_expense_date = $iq_record->renewal_date;
                $arecord->iqama_expense_purpase = $iq_record->payment_purpose_id;
                $arecord->iqama_remarks = $iq_record->remarks;

                $is_found = true;
            }else{
                $arecord->iqama_expense = 0;
                $arecord->iqama_expense_date = null;
                $arecord->iqama_remarks = null;
            }

            $av_record = (new EmployeeAdvanceDataService())->getAnEmployeeAvanceReceivedRecordForAdvanceSummaryReportByEmployeeAutoId($employee->emp_auto_id,$month_year['month'],$month_year['year']);
            if($av_record != null){
                $arecord->other_advance = $av_record->adv_amount;
                $arecord->other_advance_date = $av_record->date;
                 $arecord->other_remarks = $av_record->remarks;
                $is_found = true;
            }else{
                $arecord->other_advance = 0;
                $arecord->other_advance_date = null;
                $arecord->other_remarks = null;
            }
            $asalary = (new SalaryProcessDataService())->getAnEmployeeSalaryRecord($employee->emp_auto_id,$month_year['month'],$month_year['year']);
            if($asalary != null ){
                // now all paid unpaid salary      // && $asalary->Status == 1 only paid salary rules
               $arecord->iqama_paid = $asalary->slh_iqama_advance;
               $arecord->advance_paid = $asalary->slh_other_advance;
               $is_found = true;
            }else{
               $arecord->iqama_paid = 0;
               $arecord->advance_paid = 0;
            }
            if($is_found)
                $results[$counter++] = $arecord;

        }
        $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
        $login_name = Auth::user()->name;
        return view('admin.report.employee_summary.anemp_advance_summary',compact('company','employee','results','login_name'));
    }


    // 6 BdOffice Payment Summary
    private function processAndDisplayAnEmployeePaidFromBDOffice($employee)
    {
        return (new BdOfficePaymentController())->showAnEmployeePaymentFromBDOfficeDetailsReport($employee);
    }


      // 7 Salary Deduction Details Report
    private function processAndDisplayAnEmployeeSalaryAllRecordsSummaryReport($employee)
    {

        $company =  (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
        $salary_records = (new SalaryProcessDataService())->getAnEmployeeSalaryRecordsByPaidUnpaidStatus($employee->emp_auto_id, null, null);
        $totalUnPaidSalaryAmount = (new SalaryProcessDataService())->getAnEmployeeTotalUnPaidSalary($employee->emp_auto_id, null);

        // Advance Collection
        $toal_iqama_expense_deduction_from_salary = (new SalaryProcessDataService())->getTotalAmountOfIqamaExpenseDeductionFromSalary($employee->emp_auto_id, null);
        $total_other_advace_deduction_from_salary = (new SalaryProcessDataService())->getTotalAmountOfOtherAdvanceDeductionFromSalary($employee->emp_auto_id, null);
        $cashReceiveTotalPaidAmount =  (new EmployeeAdvanceDataService())->getAnEmployeeAdvancePaidTotalAmount($employee->emp_auto_id, null, 100);
        // 100 = Cash Received By Employee

        // iqama renewal records for showing to emp
        $iqamaExpenseAllRecords = (new EmployeeAdvanceDataService())->getAnEmployeeIqamaAnnualExpenseSelfAndCompanyAllRecords($employee->emp_auto_id);
        // Advance Give to Employee
        $iqamaRenewalTotalExpence = (new EmployeeAdvanceDataService())->getAnEmployeeIqamaTotalCost($employee->emp_auto_id);
        $otherAdvanceTotalAmount = (new EmployeeAdvanceDataService())->getAnEmployeeOthersAdvanceTotalAmount($employee->emp_auto_id, null, 0); // 0 mean other type addvance taken by employee

        $toal_CPF_contribution_from_salary =  (new SalaryProcessDataService())->getTotalAmountOfCPFContributionFromSalary($employee->emp_auto_id, null);
        $totalSaudiTax = (new SalaryProcessDataService())->getAnEmployeeTotalAmountOfSautiTaxDeductionFromSalaryTotalAdvance($employee->emp_auto_id, null);
        $empl_last_activity = (new EmpActivityDataService())->getAnEmployeeLastActivityComments($employee->emp_auto_id);
        $bonus_records = (new SalaryProcessDataService())->getAnEmployeeAllBonusRecordByEmpAutoId($employee->emp_auto_id);
        $login_name = Auth::user()->name;

        return view('admin.report.employee_summary.salary_summary_all_records', compact('totalUnPaidSalaryAmount', 'toal_iqama_expense_deduction_from_salary', 'salary_records',  'cashReceiveTotalPaidAmount',
        'otherAdvanceTotalAmount', 'total_other_advace_deduction_from_salary', 'toal_CPF_contribution_from_salary', 'totalSaudiTax', 'company', 'iqamaRenewalTotalExpence',
        'employee', 'iqamaExpenseAllRecords', 'empl_last_activity','bonus_records','login_name'));
    }

    // 8 Employee Salary paid Records Details for Employee Copy Report

    private function processAndDisplayAnEmployeeSalaryAllPaidRecordsSummaryReport($employee)
    {
        $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
        $salary_records = (new SalaryProcessDataService())->getAnEmployeeSalaryRecordsByPaidUnpaidStatus($employee->emp_auto_id, null, 1);
        $empl_last_activity = (new EmpActivityDataService())->getAnEmployeeLastActivityComments($employee->emp_auto_id);
        $login_name = Auth::user()->name;
        return view('admin.report.employee_summary.only_paid_salary_summary_report', compact('employee','salary_records', 'empl_last_activity','company','login_name'));
    }


    // 9 Employee closing agreement paper
    private function processAndDisplayAnEmployeeClosingAgreementPaperReport($employee)
    {
        $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
        $login_name = Auth::user()->name;
        return view('admin.report.employee_summary.closing_agreement', compact('employee','company','login_name'));
    }

    // Searching Employee Fiscal Year Last Record (RUnning or CLosing )
    public function searchAnEmployeeSalaryFiscalYear(Request $request){

        //$employee = (new EmployeeDataService())->searchingAnEmployeeInfoByMultitypeParameter($request->employee_searching_value, $request->search_by);
        $employee = (new EmployeeDataService())->searchAnEmployeeWithImportantInformationAsShowByMultitypeParameter($request->employee_searching_value, $request->search_by,Auth::user()->branch_office_id);

        if($employee){

            $company =  (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
            if(!(new FiscalYearDataService())->checkAnEmployeeRunningFiscalYearIsAlreadyExist($employee->emp_auto_id)){
                return response()->json(['success'=>false,'status'=>404,'message'=>'Employee Fiscal Year is already closed ','error'=>'error']);
            }
            $fiscal = (new FiscalYearDataService())->getAnEmployeeRunningFiscalYearRecord($employee->emp_auto_id);


            $closed_fiscal_record = (new FiscalYearDataService())->getAnEmployeeLastClosingFiscalYearRecord($employee->emp_auto_id);

            $totalUnPaidSalaryAmount = (new SalaryProcessDataService())->getAnEmployeeUnpaidSalaryTotalAmountByFiscalYear($employee->emp_auto_id,$fiscal->start_date,$fiscal->end_date);
            // Advance Collection
            $toal_iqama_expense_deduction_from_salary = (new SalaryProcessDataService())->getAnEmployeeTotalAmountOfIqamaExpenseDeductionFromSalaryByFiscalYear($employee->emp_auto_id,$fiscal->start_date,$fiscal->end_date);
            $total_other_advace_deduction_from_salary = (new SalaryProcessDataService())->getAnEmployeeTotalAmountOfOtherAdvanceDeductionFromSalaryByFiscalYear($employee->emp_auto_id,$fiscal->start_date,$fiscal->end_date);
            $cashReceiveTotalPaidAmount =  (new EmployeeAdvanceDataService())->getAnEmployeeAdvancePaidByCachTotalAmountByFiscalYear($employee->emp_auto_id,$fiscal->start_date,$fiscal->end_date);
            // Advance Give to Employee
            $iqamaRenewalTotalExpence = (new EmployeeAdvanceDataService())->getAnEmployeeIqamaRenewalTotalExpenseAmountByFiscalYear($employee->emp_auto_id,$fiscal->start_date,$fiscal->end_date);
            $otherAdvanceTotalAmount = (new EmployeeAdvanceDataService())->getAnEmployeeOthersAdvanceGivenTotalAmountByFiscalYear($employee->emp_auto_id,$fiscal->start_date,$fiscal->end_date);
            $toal_CPF_contribution_from_salary =  (new SalaryProcessDataService())->getAnEmployeeTotalAmountContributeToCPFByFiscalYear($employee->emp_auto_id,$fiscal->start_date,$fiscal->end_date);
            $totalSaudiTax = (new SalaryProcessDataService())->getAnEmployeeTotalAmountSaudiTaxDeductionFromSalaryByFiscalYear($employee->emp_auto_id,$fiscal->start_date,$fiscal->end_date);
            $empl_last_activity = (new EmpActivityDataService())->getAnEmployeeLastActivityComments($employee->emp_auto_id);
            $bonus_records = (new SalaryProcessDataService())->getAnEmployeeBonusRecordByFiscalYear($employee->emp_auto_id, $fiscal->start_year,$fiscal->end_year);

            $fiscal->balance_amount =   ($iqamaRenewalTotalExpence + $otherAdvanceTotalAmount + $closed_fiscal_record->balance_amount) - ($toal_iqama_expense_deduction_from_salary +
                              $total_other_advace_deduction_from_salary+$cashReceiveTotalPaidAmount + $totalUnPaidSalaryAmount);

        return response()->json(['success'=>true,'status'=>200,'message'=>'Successfully Update ','fiscal_record'=>$fiscal,'closing_amount'=> $fiscal->balance_amount,'employee'=>$employee]);
        }else {
            return response()->json(['success'=>false,'status'=>404,'message'=>'Employee Not Found','error'=>'error']);
        }


    }


    // Update Running Salary Closing Or Insert New Salary Fiscal Year
    public function closeAnEmployeeSalaryFiscalYear(Request $request){


          $fiscal_record = (new SalaryProcessDataService())->getAnEmployeeFiscalYearRecordByFiscalYearAutoId($request->fiscal_year_auto_id);
          if($request->operation_type == 1){

                if(! $fiscal_record->closing_status){
                    $last_date_of_month = (new HelperController())->getLastDateFromDateValue($request->closing_date);
                    $fiscal_record->end_date =   $last_date_of_month;
                    $fiscal_record->end_month = (new HelperController())->getMonthFromDateValue($request->closing_date);
                    $fiscal_record->end_year = (new HelperController())->getYearFromDateValue($request->closing_date);
                    $fiscal_record->remarks =  $request->remarks;
                    $fiscal_record->balance_amount =  $request->balance_amount;
                    $update = (new FiscalYearDataService())->updateAnEmployeeFiscalYearClosing($fiscal_record->efcr_auto_id,$fiscal_record->emp_auto_id,$fiscal_record->end_month,
                    $fiscal_record->end_year,$fiscal_record->end_date,$fiscal_record->balance_amount,$fiscal_record->remarks,Auth::user()->id);
                }else {
                    return response()->json(['success'=>true,'status'=>303,'message'=>'This Employee Fiscal Year Is Closed ']);
                }
          }

          if($request->operation_type == 2){

               // if(!(new SalaryProcessDataService())->checkAnEmployeeRunningFiscalYearIsAlreadyExist($request->emp_auto_id)){
                if($fiscal_record->closing_status){
                    $last_date_of_month = (new HelperController())->getLastDateFromDateValue($request->closing_date);
                    $next_date =  (Carbon::createFromFormat('Y-m-d',$last_date_of_month ))->addDay(1)->format('Y-m-d');
                    $start_month = (new HelperController())->getMonthFromDateValue($next_date);
                    $start_year = (new HelperController())->getYearFromDateValue($next_date);
                    $fiscal_record = (new FiscalYearDataService())->setAnEmployeeFiscalYearDuration($request->emp_auto_id,$start_month,$start_year,$next_date,0,Auth::user()->id);
                }else {
                    return response()->json(['success'=>true,'status'=>303,'message'=>'This Employee Fiscal Year Is Running ']);
                }
          }

          return response()->json(['success'=>true,'status'=>200,'message'=>'Successfully Update ','record'=>$fiscal_record]);


    }



    /*  =====================================================================
          ============== Project wise Employee List Report  ==============
        =====================================================================
    */
    public function  showEmployeeListReportWithSalaryMonthAndProjectWiseEmployeeReport(Request $request)
    {


        if ($request->report_format == 2) // excell format
        {
            return (new ExcelExportController())->exportEmployeeInformationByProjectSponserJobStatusEmpTradeEmpType(
                $request->proj_id,
                $request->spons_id,
                $request->catg_id,
                null,
                null
            );
        }


        if ($request->project_id == null) {
            $report_title = "All Project";
        } else {
            $report_title = "" . (new EmployeeRelatedDataService())->getProjectNameByProjectId($request->project_id);
        }
        $company =  (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
        $employee =  (new EmployeeDataService())->exportEmployeeInformationByProjectSponserEmpTradeSalaryMonthAndYear($request->project_id, $request->sponsor_id, null, null, null, $request->month, $request->year);
        return view('admin.employee-info.project_wise.project_wise_all_report', compact('employee', 'company', 'report_title'));
    }

    /*  =====================================================================
            ============== Employee List project wise Report  ==============
          =====================================================================
    */
    public function projectWiseEmployeeListProcess(Request $request)
    {

        $company =  (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);

        if ($request->report_format == 2) // excell format
        {
            return (new ExcelExportController())->exportEmployeeInformationByProjectSponserJobStatusEmpTradeEmpType(
                $request->proj_id,
                $request->spons_id,
                $request->catg_id,
                $request->job_status,
                $request->emp_type_id
            );
        }

        $project = "All Project";
        if ($request->proj_id != null) {
            $project = (new EmployeeRelatedDataService())->getProjectNameByProjectId($request->proj_id);
        }

        // dd($request->all());
        $employee = (new EmployeeDataService())->getEmployeeInfoWithSalaryDetailsReportByProjectSponorTradeAndJobStatus(
            $request->proj_id,
            $request->spons_id,
            $request->catg_id,
            $request->job_status
        );

        $report_title = "";
        if ($employee->count() > 0) {
            return view('admin.employee-info.project_wise.report', compact('employee', 'company', 'project', 'report_title'));
        } else {
            Session::flash('error', 'value');
            return redirect()->back();
        }
    }



 // multiple employee prevacation salary statement
 private function prevacationSalaryStatementReport($employee_id_array){

        $report_records = [];
        $counter = 0;
        foreach($employee_id_array as $anEmp){
           // $employee = (new EmployeeDataService())->searchAnEmployeeWithImportantInformationAsShowByMultitypeParameter($anEmp,'employee_id',Auth::user()->branch_office_id);
            $employee = (new EmployeeDataService())->searchAnEmployeeWithImportantInformationAsShowByMultitypeParameter($anEmp,$this->searchby_employee_id,Auth::user()->branch_office_id);

            if($employee == null || !(new FiscalYearDataService())->checkAnEmployeeRunningFiscalYearIsAlreadyExist($employee->emp_auto_id)){
                continue;
            }
            $fiscal = (new FiscalYearDataService())->getAnEmployeeRunningFiscalYearRecord($employee->emp_auto_id);
            $employee->closed_fiscal_record = (new FiscalYearDataService())->getAnEmployeeLastClosingFiscalYearRecord($employee->emp_auto_id);
            $employee->total_unpaid_salary_amount = (new SalaryProcessDataService())->getAnEmployeeUnpaidSalaryTotalAmountByFiscalYear($employee->emp_auto_id,$fiscal->start_date,$fiscal->end_date);
            // Advance Collection
            $employee->toal_iqama_expense_deduction_from_salary = (new SalaryProcessDataService())->getAnEmployeeTotalAmountOfIqamaExpenseDeductionFromSalaryByFiscalYear($employee->emp_auto_id,$fiscal->start_date,$fiscal->end_date);
            $employee->total_other_advace_deduction_from_salary = (new SalaryProcessDataService())->getAnEmployeeTotalAmountOfOtherAdvanceDeductionFromSalaryByFiscalYear($employee->emp_auto_id,$fiscal->start_date,$fiscal->end_date);
            $employee->cash_receive_total_paid_amount =  (new EmployeeAdvanceDataService())->getAnEmployeeAdvancePaidByCachTotalAmountByFiscalYear($employee->emp_auto_id,$fiscal->start_date,$fiscal->end_date);
            // Advance Give to Employee
            $employee->iqama_renewal_total_expence = (new EmployeeAdvanceDataService())->getAnEmployeeIqamaRenewalTotalExpenseAmountByFiscalYear($employee->emp_auto_id,$fiscal->start_date,$fiscal->end_date);
            $employee->other_advance_total_Amount = (new EmployeeAdvanceDataService())->getAnEmployeeOthersAdvanceGivenTotalAmountByFiscalYear($employee->emp_auto_id,$fiscal->start_date,$fiscal->end_date);
            $employee->toal_CPF_contribution_from_salary =  (new SalaryProcessDataService())->getAnEmployeeTotalAmountContributeToCPFByFiscalYear($employee->emp_auto_id,$fiscal->start_date,$fiscal->end_date);
            $employee->total_saudi_tax = (new SalaryProcessDataService())->getAnEmployeeTotalAmountSaudiTaxDeductionFromSalaryByFiscalYear($employee->emp_auto_id,$fiscal->start_date,$fiscal->end_date);
            $employee->empl_last_activity = (new EmpActivityDataService())->getAnEmployeeLastActivityComments($employee->emp_auto_id);
           // $bonus_records = (new SalaryProcessDataService())->getAnEmployeeBonusRecordByFiscalYear($employee->emp_auto_id, $fiscal->start_year,$fiscal->end_year);
            $employee->all_others_balance_amount =   ($employee->iqama_renewal_total_expence + $employee->other_advance_total_Amount + $employee->closed_fiscal_record->balance_amount) - ($employee->toal_iqama_expense_deduction_from_salary +
                              $employee->total_other_advace_deduction_from_salary+$employee->cash_receive_total_paid_amount );
            $report_records[$counter++] =     $employee;

        }
        $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
        $login_name = Auth::User()->name;
        $report_title = "Pre-Vacation Salary Summary Report";

        return view('admin.report.hr_section.multi_emp_prevacation_salary_statement',compact('login_name','company','report_records','report_title'));


    }

    /*  =====================================================================
           =========== Date Wise New Employee Insert Report  ===========
        =====================================================================
    */


    public function getAllNewEmployeeInsertListDetailsInfoByDateToDateReport(Request $request)
    {

        $from_date = $request->from_date;
        $to_date =    $to_date = date('Y-m-d', strtotime($request->todate . " + 1 day")); // add 1 day for correct data
        $emp_type_id = $request->emp_type_id;
        $report_type = $request->report_type;
        // $sponsor_id_list = $request->has('spons_id') ? $request->spons_id : null;
        $sponsor_id_list = $request->has('spons_id') ? $request->spons_id : (new EmployeeRelatedDataService())->getAllActiveSponsorIdAsArrayOfABranchOffice(Auth::user()->branch_office_id);


        if ($from_date == null || $to_date == null) {
            Session::flash('error', 'Please select new employee inserted date');
            return redirect()->back();
        }else if($report_type == 1){
                //  employee list print
                $is_hourly = null;
                $emp_type = null;
                if ($emp_type_id == -1) {
                    // All Employee
                    $is_hourly = null;
                    $emp_type = null;
                }
                else if ($emp_type_id == 1) {
                    //Direct Employee (Basic Salary)
                    $is_hourly = null;
                    $emp_type = 1;
                } else if ($emp_type_id == 2) {
                    // Indirect Employee
                    $emp_type = 2;
                    $is_hourly = null;
                } else if ($emp_type_id == 3) {
                    // Basic Salary (Direct & Indirect) Employees
                    $is_hourly = null;
                    $emp_type = 3;
                } else if ($emp_type_id == 4) {
                    // Direct Employee (Hourly)
                    $is_hourly = true;
                    $emp_type = 1;
                }
                $employees = (new EmployeeDataService())->getAllNewEmployeeInsertListByDateToDateReport($from_date, $to_date, $emp_type, $is_hourly,   $sponsor_id_list,Auth::user()->branch_office_id);
                $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);

                $report_title = [$from_date, $to_date];
                return view('admin.report.hr_section.date_wise_new_inserted_emp_list_report', compact('employees', 'company', 'report_title'));
        }else if($report_type == 2){
           return "Report Under Processing....";
        }else if($report_type == 3){

            $records = (new EmployeeDataService())->getAllNewEmployeeInsertedSummaryReportBySponsorAndByDateToDate($from_date, $to_date,$sponsor_id_list,Auth::user()->branch_office_id);

            $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);

            $report_title = [$from_date, $to_date];
            return view('admin.report.hr_section.joining_wise_new_emp_summary', compact('records', 'company', 'report_title'));

        }


    }


    // 8 HR Report Vacation/Final Exit Employees Report
    public function showEmployeeActivityReport(Request $request){

       // try{
                $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
                if ($request->report_type == 1) // html view format
                {            //   Employees list Report
                    $report_title = [$request->from_date,$request->to_date];
                    $activity_name = (new EmployeeRelatedDataService())->getEmployeeJobStatusTitle($request->job_status);
                    $records =  (new EmployeeRelatedDataService())->getEmployeeActivityEmployeeDetailsListReport($request->project_name_id,$request->job_status,$request->from_date,$request->to_date,Auth::user()->branch_office_id);
                    return view('admin.report.hr_section.multiemp_activity_details_report', compact('records', 'company','report_title','activity_name'));
                }
                else if($request->report_type == 2) {  // excell format

                    $records =  (new EmployeeRelatedDataService())->getEmployeeActivityEmployeeDetailsListReport(
                        $request->project_name_id,
                        $request->job_status,
                        $request->from_date,$request->to_date,
                        Auth::user()->branch_office_id);
                       //  dd( $records);
                   return Excel::download(new EmployeesExportBaseOnActivity( $records), 'employee_list.xlsx');
                }
                else if($request->report_type == 3){ // summary report
                    $listofmonths = (new HelperController())->getMonthsInRangeOfDate($request->from_date,$request->to_date);
                    $records = [];
                    $counter = 0;
                    foreach($listofmonths as $my){
                        $re =  (new EmployeeRelatedDataService())->getVacationApprovedMonthlyEmployeeSummaryReportByMonthAndYear($my['month'],$request->job_status,$my['year'],Auth::user()->branch_office_id);
                        if($re){
                            $re->job_status = "Vacation";
                            $records[$counter++] = $re;
                        }
                    }
                    $login_user_name = Auth::user()->name;
                    return view('admin.report.hr_section.summary.monthwise_emp_activity_summary', compact('records', 'company','login_user_name' ));
                }
        // }catch(Exception $ex){
        //     return'Operation Failed, System Exception, '+$ex->getMessage();

        // }
    }



     // HR Report 10, Employee Type base list
    public function showProjectWiseEmployeeTypeHRReport(Request $request)
    {


        $project_id_list = $request->project_id_list;
        $spons_id_list = $request->spons_id;

        $empType = $request->emp_type_id;
        $project = 'All';


        if($spons_id_list == null){
            $spons_id_list = (new EmployeeRelatedDataService())->getAllActiveSponsorIdAsArrayOfABranchOffice(Auth::user()->branch_office_id);
        }

        if($project_id_list == null){
            $project_id_list = (new ProjectDataService())->getAllActiveProjectIDOfABranchOfficeAsArray(Auth::user()->branch_office_id);
        }

            $report_title = ['All'];
            $hourly_employee = null;
            $emp_type = null;

            if ($empType == -1) {  // Basic Salary direct Employee
            $hourly_employee = null;
            $emp_type = null;
            $report_title[0] = "Direct & Indirect All" ;

            }else if ($empType == 1) { //  direct Employee Basic
                $hourly_employee = null;
                $report_title[0] = "Direct Employee(Basic Salary)" ;
                $emp_type = [1];
            } else if ($empType == 2) { //  direct Employee Hourly
            $hourly_employee = true;
            $report_title[0] = "Direct Employee-Hourly" ;
            $emp_type = [1];
            }else if ($empType == 3) {   // Indirect Employee
            $report_title[0] = 'Indirect Employee';
            $hourly_employee = NULL;
            $emp_type = [2];
            } else if ($empType == 4) {
            $report_title[0] = "Basic Salary(Indirect & Direct) Employees";
            $hourly_employee = NULL;
            $emp_type = [1,2];
            }

            if($request->report_format == 2){
                // summary report
                return $this->showBasicAndHourlyEmployeeSummary($project_id_list,$spons_id_list,  $emp_type, $hourly_employee);
            }else {
                // employee list report
            //  dd($project_id_list,$spons_id_list,  $emp_type, $hourly_employee,$request->all());
                $employees = (new EmployeeDataService())->getListOfEmployeesDetailsOfAbranchOfficeByProjectsSponsorsAndEmployeTypeHRReport($project_id_list,$spons_id_list,  $emp_type, $hourly_employee,Auth::user()->branch_office_id);
                $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
                return view('admin.report.hr_section.employee_list_emp_type_wise', compact('employees', 'company', 'report_title'));
            }


            $employee = (new EmployeeDataService())->getHREmployeesReporttByMultipleProjectSponorTradeAndJobStatus(
                $project_id_list, // list of project id
                $sponosor_ids,
                $trade_ids,
                $job_status_ids,
                Auth::user()->branch_office_id
            );

    }

     // 10.1 company total employee summary
    private function showBasicAndHourlyEmployeeSummary($project_id_list,$spons_id_list,  $emp_type, $hourly_employee){

        $report_list = array();
        $total_active_emp = (new EmployeeDataService())->getProjectAndSponsorWiseTotalEmployeeSummary(null,null);
        $total_active_emp->title = "Total Employee Including Others";
        $report_list[0] =  $total_active_emp;

        $abc_active_emp = (new EmployeeDataService())->getProjectAndSponsorWiseTotalEmployeeSummary($project_id_list,$spons_id_list);
        $abc_active_emp->title = "ABC Total Employee";
        $report_list[1] =  $abc_active_emp;

        $abc_basic_active_emp = (new EmployeeDataService())->getProjectAndSponsorWiseTotalBasicOrHourlyEmployeeEmployeeSummary($project_id_list,$spons_id_list,[1],null);
        $abc_basic_active_emp->title = "ABC Basic Salary Employee";
        $report_list[2] =  $abc_basic_active_emp;

        $abc_basic_staff_active_emp = (new EmployeeDataService())->getProjectAndSponsorWiseTotalBasicOrHourlyEmployeeEmployeeSummary($project_id_list,$spons_id_list,[2],null);
        $abc_basic_staff_active_emp->title = "ABC Total Staff";
        $report_list[3] =  $abc_basic_staff_active_emp;

        $abc_hourly_active_emp = (new EmployeeDataService())->getProjectAndSponsorWiseTotalBasicOrHourlyEmployeeEmployeeSummary($project_id_list,$spons_id_list,[1],true);
        $abc_hourly_active_emp->title = "ABC Hourly Employee";
        $report_list[4] =  $abc_hourly_active_emp;

      //  dd($report_list,$total_active_emp->total_emp,$abc_active_emp,$abc_basic_active_emp,$abc_basic_staff_active_emp,$abc_hourly_active_emp);
      $company = (new CompanyDataService())->findCompanryProfile();
      return view('admin.report.hr_section.over_all_employee_summary', compact('report_list', 'company'));


    }


     /*  =====================================================================
           =========== Employee TUV Informations Report  ===========
        =====================================================================
    */
    public function getAllEmployeeTUVCertificateInformationsReport(Request $request){


       //  $employee = (new EmpTUVInfoDataService())->getAllEmployeeTUVDetailsInfoForReport();
       // $company = (new CompanyDataService())->findCompanryProfile();

        $employee = (new EmpTUVInfoDataService())->getAllEmployeeTUVDetailsInfoForReport(Auth::user()->branch_office_id);
        $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
        $report_title = "Employee TUV Card Informations.";

        return view('admin.report.hr_section.all_emp_tuv_infos_report', compact('employee', 'company', 'report_title'));

    }

    /*  =====================================================================
           =========== An Employee Details Informations Print Priview  ===========
        =====================================================================
    */
    public function getAnEmployeeDetailsInformationsForPrintPreview(Request $request){

        $searchByDb_Column = $request->searchType;
        $employee_searching_value = $request->searchValue;
        $employeeInfo = (new EmployeeDataService())->searchingAnEmployeeInfoByMultitypeParameter($employee_searching_value, $searchByDb_Column);

        if (count($employeeInfo) > 0 ) {
            $employee = $employeeInfo[0];
            $company = (new CompanyDataService())->findCompanryProfile();
            $loggedInUser = Auth::user()->name;
            return view('admin.employee-info.anemp_details_info_print_preview', compact('employee', 'company', 'loggedInUser'));
        } else {
            Session::flash('error', 'Operation Failed!, Employee Not Found With This Informations!!!');
            return redirect()->back();
        }
    }

     public function getAnEmployeeActivitiesDetailsInformation($employee_id){

           // activity report by employee id,
           $records = (new EmployeeDataService())->getAnEmployeeActivitiesReportByEmployeeId($employee_id,Auth::user()->branch_office_id);

             $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
            $loggedInUser = Auth::user()->name;
            return view('admin.report.hr_section.emp_activity_report', compact('records', 'company', 'loggedInUser'));

    }

       // 3 Designation Head Basis Employees Report
    public function showEmployeeDesignationWiseEmployeeReport(Request $request){

        $company = (new CompanyDataService())->findCompanryProfile();
        $loggedInUser = Auth::user()->name;
        $project_ids = $request->has('project_id_list') ? $request->project_id_list:null;
        $designation_heads = $request->has('designation_head') ? $request->designation_head:null;
        $job_status = $request->has('job_status') ? $request->job_status:null;

        if($request->report_format == 1){
            $report_title = 'Employees Summary (Designation base)';
            $records = (new EmployeeDataService())->getDesignationHeadBaseEmployeeSummaryReport($project_ids,$designation_heads,$job_status);
            return view('admin.report.hr_section.designation_head_emp_summary', compact('records', 'company', 'loggedInUser','report_title'));
        } else {
            $employees = (new EmployeeDataService())->getDesignationHeadBaseEmployeeListReport($project_ids,$designation_heads,$job_status);
            $report_title= 'Employees List (Designation base)';
            return view('admin.report.hr_section.designation_head_emp_list', compact('employees', 'company', 'report_title','loggedInUser'));
        }

    }


       // 3.1 Sponsor base active vacation  runaway emp summary
    public function processAndDisplaySponsorBasaeEmployeeSummaryReport(Request $request){

         $project_ids = $request->has('project_id_list') ? $request->project_id_list:null;
        $job_status = $request->has('job_status') ? $request->job_status:null;
        $sponsor_ids = $request->spons_id;

        if($sponsor_ids == null){
            $sponsor_ids = (new EmployeeRelatedDataService())->getAllActiveSponsorIdAsArrayOfABranchOffice(Auth::user()->branch_office_id);
        }else if(count($sponsor_ids) == 1){
            // only single sponsor all HR summary report
          return  $this->searchASponsorTotalHumanResourceEmployeeSummary($sponsor_ids[0],$project_ids,$job_status);
        }

        // if($project_ids == null){
        //     $project_ids = (new ProjectDataService())->getAllActiveProjectIDOfABranchOfficeAsArray(Auth::user()->branch_office_id);
        // }

        $report_records = array();
        $counter = 0;
        foreach($sponsor_ids as $sid){
            $asponsor = (new EmployeeRelatedDataService())->searchASponserBySponsorId($sid);
            $records = (new EmployeeDataService())->getSponsorWiseTotalEmployeeSummaryReport($sid);

            $asponsor->active_emp = 0;
            $asponsor->vacation_emp = 0;
            $asponsor->runaway_emp = 0;

            if(count($records) == 3){
                $asponsor->active_emp = $records[0]->total_emp; // Active
                $asponsor->vacation_emp = $records[1]->total_emp; // Vacation
                $asponsor->runaway_emp = $records[2]->total_emp; // Run Away
            }else if(count($records) == 2){

                if($records[0]->id == 1){
                    $asponsor->active_emp = $records[0]->total_emp; // Active
                }else if($records[0]->id == 5){
                    $asponsor->vacation_emp = $records[0]->total_emp; // vacatopm
                }else if($records[0]->id == 6){
                    $asponsor->runaway_emp = $records[0]->total_emp; // run_away_emp
                }

                if($records[1]->id == 1){
                    $asponsor->active_emp = $records[1]->total_emp; // Active
                }else if($records[1]->id == 5){
                    $asponsor->vacation_emp = $records[1]->total_emp; // vacatopm
                }else if($records[1]->id == 6){
                    $asponsor->runaway_emp = $records[1]->total_emp; // run_away_emp
                }
            }
            else if(count($records) == 1){

                if($records[0]->id == 1){
                    $asponsor->active_emp = $records[0]->total_emp; // Active
                }else if($records[0]->id == 5){
                    $asponsor->vacation_emp = $records[0]->total_emp; // vacatopm
                }else if($records[0]->id == 6){
                    $asponsor->runaway_emp = $records[0]->total_emp; // run_away_emp
                }
            }

            if($asponsor->active_emp + $asponsor->vacation_emp + $asponsor->runaway_emp > 0){
                //  if a sponsor contains employees then this record will show in report
                $report_records[$counter++] = $asponsor;
            }

        }

        $report_title = "Sponsor Base Employee  Summary";
        $company = (new CompanyDataService())->findCompanryProfile();
        $loggedInUser = Auth::user()->name;
        return view('admin.report.hr_section.summary.sponsor_job_status_emp_summary', compact('report_records','company', 'report_title'));


    }

     // 3.1 only single sponsor all HR summary report
    private function searchASponsorTotalHumanResourceEmployeeSummary($sponsor_id,$project_ids,$job_status){


        if($project_ids == null){
            $project_ids = (new ProjectDataService())->getAllActiveProjectIDOfABranchOfficeAsArray(Auth::user()->branch_office_id);
        }
        $sponsor_name = (new EmployeeRelatedDataService())->getASponserNameBySponerId($sponsor_id);
        $report_records = array();
        $result = 0;
        foreach($project_ids as $pid){
            $aproject = (new EmployeeRelatedDataService())->findAProjectInformation($pid);
            $records = (new EmployeeDataService())->searchASponsorTotalHumanResourceSummaryReport($sponsor_id,$pid );

            if(count( $records) > 0){
                $aproject->active_emp = 0;
                $aproject->vacation_emp = 0;
                $aproject->runaway_emp = 0;
                $aproject->others = 0;
                $counter = 0;
                while($counter < count($records)){
                    if($records[$counter]->job_status == 1){
                        $aproject->active_emp = $records[$counter]->total_emp;
                    }else if($records[$counter]->job_status == 5){
                        $aproject->vacation_emp = $records[$counter]->total_emp;
                    }
                    else if($records[$counter]->job_status == 6){
                        $aproject->runaway_emp = $records[$counter]->total_emp;
                    }else {
                        $aproject->others += $records[$counter]->total_emp;
                    }
                    $counter++;
                }
                $report_records[$result++] = $aproject;

            }
        }
      //  dd($report_records);
      $report_title = "Sponsor Base Employee  Summary";
      $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
      $loggedInUser = Auth::user()->name;
      return view('admin.report.hr_section.summary.asponsor_hr_summary', compact('report_records','company', 'report_title','sponsor_name'));

    }


        // 3.2 Employee Last Activity details usign duration
    public function processAndShowEmployeeLastActivityDetailsReport(Request $request){


        $project_ids = $request->has('project_id_list') ? $request->project_id_list:null;
        $sponsor_ids = $request->spons_id;
        $activity_period =  $request->activity_period;

        $newDate = date('Y-m-d', strtotime(date('Y-m-d') . ' - 90 days'));
        if( $activity_period == 6){
            $newDate = date('Y-m-d', strtotime(date('Y-m-d') . ' - 180 days'));
        }elseif( $activity_period == 9){
            $newDate = date('Y-m-d', strtotime(date('Y-m-d') . ' - 270 days'));
        }else if( $activity_period == 12){
            $newDate = date('Y-m-d', strtotime(date('Y-m-d') . ' - 365 days'));
        }

        $dmy = (new HelperController())->getDayMonthAndYearFromDateValue($newDate);
        $emp_auto_ids = (new EmployeeDataService())->getListOfEmployeeEmpAutoIdThoseJoinedAfterADateByMultipleProjectId(null,$newDate);
        $absent_emp_ids = (new EmployeeAttendanceDataService())->getListOfEmployeesAutoIdThoseHaveNoRecordsAfterADate( $newDate,$emp_auto_ids);

        $salary_not_received_emp_ids= (new SalaryProcessDataService())->searchListOfEmployeesThoseNotReceivedSalaryAfterADateForInActiveEmployeeReport( $newDate,$emp_auto_ids);

        $unique_ids = array_unique(array_merge($salary_not_received_emp_ids->toArray(), $absent_emp_ids->toArray()));
        $report_records = array();
        $counter =0 ;
        foreach($unique_ids as $aid){

            $arecord =  (new SalaryProcessDataService())->getAnEmployeeDetailsWithSalaryReceivedDateForInactivityEmployeeReport( $aid);
              if ($arecord != null  && strtotime($arecord->slh_salary_date) <= strtotime($newDate)) {
                $ar = (new EmployeeAttendanceDataService())->getAnEmployeeLastAttendanceRecordForInactivityReport( $aid);
                $arecord->last_attendance = ($ar == null ? "" : ($ar->emp_io_year.'-'.$ar->emp_io_month.'-'.$ar->emp_io_date));
                $report_records[$counter++] = $arecord;
            }
        }

      //  dd($report_records);
        // if($sponsor_ids == null){
        //     $sponsor_ids = (new EmployeeRelatedDataService())->getAllActiveSponsorIdAsArrayOfABranchOffice(Auth::user()->branch_office_id);
        // }else if(count($sponsor_ids) == 1){
        //     // only single sponsor all HR summary report
        //     return  $this->searchASponsorTotalHumanResourceEmployeeSummary($sponsor_ids[0],$project_ids,$job_status);
        // }

        $report_title = "Employee Attendance and Salary Activities";
        $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
        $loggedInUser = Auth::user()->name;
        return view('admin.report.hr_section.emp_last_activity_baseon_duration_report', compact('report_records','company', 'report_title','loggedInUser','activity_period'));


    }

}
