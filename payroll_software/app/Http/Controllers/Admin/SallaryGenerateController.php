<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\AnualFee\AnualFeeDetailsController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\Permission\SalaryProcessPermissionController;
use App\Http\Controllers\Admin\CompanyProfileController;
use App\Http\Controllers\Admin\CPF\ContributionController;
use App\Http\Controllers\Admin\EmployeeMultiProjectWorkHistoryController;
use App\Http\Controllers\Admin\Helper\HelperController;
use App\Http\Controllers\DataServices\CompanyDataService;
use App\Http\Controllers\DataServices\EmployeeAdvanceDataService;
use App\Http\Controllers\DataServices\EmployeeRelatedDataService;
use App\Http\Controllers\DataServices\SalaryProcessDataService;
use App\Http\Controllers\DataServices\EmployeeDataService;
use App\Http\Controllers\DataServices\EmployeeAttendanceDataService;
use App\Http\Controllers\DataServices\ProjectDataService;
use App\Http\Controllers\DataServices\CateringDataService;
use App\Http\Controllers\DataServices\FiscalYearDataService;
use App\Http\Controllers\DataServices\AuthenticationDataService;
use Modules\Payroll\Services\PartialSalaryService;
use App\Http\Controllers\Admin\Helper\UploadDownloadController;
use App\Exports\{SalaryReport1ExcelExport, WPSSalaryExportAsExcel};
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use App\Models\SalarySheetUpload;
//use App\Enums\SalaryPaymentMethodEnum;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class SallaryGenerateController extends Controller
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


  public function showSalaryProcessHistoryReport($request)
  {

    // return "Report Development Under Processing";

    $month = $request->month;
    $year = $request->year;
    $records =   (new SalaryProcessDataService())->getSalaryProcessingHistoryReport($month, $year);
    // dd($records);
    $month_name = date("F", mktime(0, 0, 0,  $month, 10));

    // Load the view and pass data
    // temporary off for not found error in server
    //   $pdf = Pdf::loadView('payroll::pages.Payroll.reports.salary_process_history', ['records'=>$records,'login_name'=>Auth::user()->name,'month_name'=>$month_name,'year'=>$year]);
    return view('admin.salary-generate.salary_process_history', ['records' => $records, 'login_name' => Auth::user()->name, 'month_name' => $month_name, 'year' => $year]);

    // Set paper size to A4 Landscape (matching your image width)
    $pdf->setPaper('a4', 'portrait');

    // Stream shows it in the browser instead of downloading
    return $pdf->stream('salary_process_history.pdf');
  }
  /* ===================== 1 Project base Salary Processing ===================== */
  public function employeeSalaryProcessAJAXRequest(Request $request)
  {

    try {

      if ($request->operation_type == 2) {
        // salary processing history report
        return $this->showSalaryProcessHistoryReport($request);
      }
      // otherwise salary processing
      $month = $request->month;
      $year = $request->year;
      $project_ids = $request->proj_ids ? $request->proj_ids : [];
      $loginUserId = Auth::user()->id;
      $user_branch_id = Auth::user()->branch_office_id;
      $sponsor_ids = $request->sponsor_ids ? $request->sponsor_ids : [];
      $hasAccessPermission = (new SalaryProcessPermissionController())->checkSalaryProcessPermission($month, $year);

      $salary_process_emp_type = 0;
      if (count($sponsor_ids) == 0) {
        return json_encode(['error' => 'Error', 'message' => 'Please Select Either Asloob or Subcon. Sponsor 1', 'success' => false, 'status' => 404]);
      } else if ((int) $sponsor_ids[0] == 1 && (count($project_ids) == 0 || count($project_ids) > 1)) {
        // multiple projects selected but Asloob sponsor at a time not allowed
        return json_encode(['error' => 'Error', 'message' => 'Please Select Single Project for Aloob Sponsor', 'success' => false, 'status' => 404]);
      }
      // else if(count($project_ids) >= 2 && (int) $sponsor_ids[0] == 1){
      //     // multiple projects selected but Asloob sponsor at a time not allowed
      //     return json_encode(['error' => 'Error', 'message' => 'Please Select One Sponsor at a Time 3', 'success' => false, 'status' => 404]);
      // }
      else if (count($sponsor_ids) >= 2 && (int) $sponsor_ids[0] == 1) {
        // multiple sponsors selected included Asloob, but subcon and asloob at a time not allowed
        // $sponsor_ids =  (new EmployeeRelatedDataService())->getAllActiveSponsorIdAsArray();
        return json_encode(['error' => 'Error', 'message' => 'Please Select Either Asloob or Subcon. Sponsor', 'success' => false, 'status' => 404]);
      } else if ((int) $sponsor_ids[0] == 1) {
        // Only Asloob but all asloob owner sponsor id

        $sponsor_ids =  (new EmployeeRelatedDataService())->getAllActiveSponsorIdAsArrayOfABranchOfficeBySponsorTypes($user_branch_id, ['Asloob']);
        $project_ids[0] = $project_ids[0];
        $salary_process_emp_type = 1; // 1 for asloob sponsor, 2 for other subcon sponsor
      } else if ((int) $sponsor_ids[0] > 1) {

        // Only Other/Subcontractor Sponsor
        // $sponsor_ids =  (new EmployeeRelatedDataService())->getAllActiveSponsorIdAsArrayOfABranchOfficeBySponsorTypes($user_branch_id,['Subcon']);

        if (count($project_ids) == 0) {
          // if selected subcon sponsor but no project selected, consider all projects
          $project_ids = (new ProjectDataService())->getAllActiveProjectIDs();
        }
        $salary_process_emp_type = 2;
      }


      // if((new SalaryProcessDataService())->checkSalaryProcessHisotryAlreadyDone($project_id,$month,$year,$salary_process_emp_type)){
      //     return json_encode(['error' => 'Error', 'message' => 'Selected Project & Sponsor Type Salary Already Processed ', 'success' => false, 'status' => 404]);
      // }

      $allEmployeeMontlySalaryDetails = (new SalaryProcessDataService())->getListOfEmployeesWithWorkRecordsForProcessingSalary($month, $year, $project_ids, $sponsor_ids, $user_branch_id);

      // If No Record Found
      if ($allEmployeeMontlySalaryDetails == NULL) {
        return json_encode(['error' => 'Error', 'message' => 'Record Not Found', 'success' => false, 'status' => 404]);
      }

      // Process Each Employee Salary
      foreach ($allEmployeeMontlySalaryDetails as $anEmployee) {
        $this->processAnEmployeeSalaryCalculation($anEmployee, $month, $year, $loginUserId);
      }
      if ($salary_process_emp_type == 1) {
        // both Asloob and Subcon, but now this feature not allowed
        (new SalaryProcessDataService())->insertSalaryProcessHistory($project_ids[0], $month, $year, 1, $loginUserId); // 1 = Asloob, 2= subcon

      } else {
        // only one either Asloob or Subcon
        foreach ($project_ids as $project_id) {
          (new SalaryProcessDataService())->insertSalaryProcessHistory($project_id, $month, $year,  $salary_process_emp_type, $loginUserId);
        }
      }
      // login user activities record
      (new AuthenticationDataService())->InsertLoginUserActivity(16, 1, $loginUserId, Auth::user()->emp_auto_id, null);
      return json_encode(['success' => true, 'message' => ' Total ' . count($allEmployeeMontlySalaryDetails) . ' Employees Processing Completed', 'dd' => $request->is_wps_employees, 'status' => 200]);
    } catch (Exception $ex) {
      return json_encode(['error' => 'Error', 'message' => $ex->getMessage(), 'success' => false, 'status' => 404]);
    }
  }


  /* ===================== Single Employee Salary Processing & Report ===================== */
  public function singleEmployeeMonthWiseSalaryReport(Request $request)
  {

    try {
      $salaryYear = $request->year;
      $month = $request->month;
      $multiple_emp_ids = explode(",", $request->emp_id);
      $multiple_emp_ids = array_unique($multiple_emp_ids); // remove multiple same empl ID
      // Project Base Salary Processing
      $allEmployeeMontlySalaryDetails = (new SalaryProcessDataService())->getListOfmployeeDetailsWithMonthlyWorkRecordByMultipleEmpIDsForSalaryProcessing($multiple_emp_ids, $month, $salaryYear, Auth::user()->branch_office_id);

      if ($allEmployeeMontlySalaryDetails == NULL) {
        Session::flash('error', 'Employee Information or Working Record Not Found');
        return redirect()->back();
      } else if (!(new SalaryProcessPermissionController())->checkSalaryProcessPermission($month, $salaryYear)) {
        Session::flash('error', 'Salary Processing System Already Locked');
        return redirect()->back();
      }
      $loginUserId = Auth::user()->id;
      foreach ($allEmployeeMontlySalaryDetails as $anEmployee) {
        $this->processAnEmployeeSalaryCalculation($anEmployee, $month, $salaryYear, $loginUserId);
      }


      $salaryStatus = 0; // 0 = Unpaid
      $salaryReport = (new SalaryProcessDataService())->getMultipleEmployeeIdBaseSalaryHistory($multiple_emp_ids, $month, $salaryYear, $salaryStatus, Auth::user()->branch_office_id);
      $employeeType = "";
      $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
      $project = '';
      $monthName = (new HelperController())->getMonthName($month);
      $reportLeftTitle = ['Salary Month of ' . $monthName . ', ' . $salaryYear, '', ''];
      $login_name = Auth::user()->name;
      return view('admin.salary-generate.all-employee.all-employees-salary', compact('login_name', 'salaryReport', 'month', 'monthName', 'salaryYear', 'company'));
    } catch (Exception $ex) {
      Session::flash('error', 'Salary Processing Error: ' . $ex->getMessage());
      return redirect()->back();
    }
  }

  // process an employee salary
  private function processAnEmployeeSalaryCalculation($anEmployee, $month, $salaryYear, $loginUserId)
  {
    try {


      $duplicateSalary  = (new SalaryProcessDataService())->getAnEmployeeSalaryRecord($anEmployee->emp_auto_id, $month, $salaryYear);
      // Salary Already Paid so  Processing is not Possible
      $thisMonthIqamaPaidAmount = 0;
      if ($duplicateSalary != null) {
        if ($duplicateSalary->Status == 1) {
          return $duplicateSalary;
        }
        $thisMonthIqamaPaidAmount = $duplicateSalary->slh_iqama_advance;
      }

      $anEmployee->bonus_amount = (new SalaryProcessDataService())->getAnEmployeeBonusAmountByEmpAutoIdAndMonthYear($anEmployee->emp_auto_id, $month, $salaryYear);

      $totalOthers = ($anEmployee->house_rent + $anEmployee->conveyance_allowance + $anEmployee->mobile_allowance  + $anEmployee->medical_allowance + $anEmployee->others1 + $anEmployee->local_travel_allowance + $anEmployee->bonus_amount);

      $tem_anEmployee  = (new SalaryProcessDataService())->calculateOvertimeHoursAndAmount($anEmployee);
      $anEmployee->slh_overtime_amount = $tem_anEmployee->slh_overtime_amount;

      $anEmployee->food_allowance = (new SalaryProcessDataService())->calculateFoodAllowance($anEmployee->total_work_day, $anEmployee->food_allowance);
      $anEmployee->slh_all_include_amount = $tem_anEmployee->tem_total_amount + $totalOthers + $anEmployee->food_allowance;

      $anEmployee->partial_paid_amount = (new PartialSalaryService())->getAnEmployeeSalaryPartialPaidAmountByMonthAndYear($anEmployee->emp_auto_id, (int) $month, $salaryYear);

      $catering_food = (new CateringDataService())->getAnEmployeeAMonthCateringRecordForSalaryProcessingByEmployeeAutoId($anEmployee->emp_auto_id, (int) $month, $salaryYear);
      $anEmployee->slh_food_deduction = 0;
      if ($catering_food) {
        $anEmployee->slh_food_deduction = $catering_food->amount;
      }

      $total_deduct_amount = $anEmployee->slh_food_deduction + $anEmployee->saudi_tax + $anEmployee->iqama_adv_inst_amount + $anEmployee->other_adv_inst_amount + $anEmployee->cpf_contribution +  $anEmployee->partial_paid_amount;
      $anEmployee->gross_salary =  $anEmployee->slh_all_include_amount -  $total_deduct_amount; //  this is slh_total_salary

      $anEmployee->work_multi_project = (new  EmployeeMultiProjectWorkHistoryController())->getIsAnEmployeeMultiprojectWorkHistory($anEmployee->emp_auto_id, $month, $salaryYear);
      // calculate an employee multiple project working salary for single month
      $this->processAnEmployeMultipleProjectWorkSalary($anEmployee, $month, $salaryYear);


      // ================ End Calculation =================

      if ($duplicateSalary) {
        if ($duplicateSalary->Status == 0) {
          // Update allowed if salary status is not paid yet
          (new SalaryProcessDataService())->updateAnEmployeeMonthlySalaryRecord($duplicateSalary->slh_auto_id, $anEmployee, $month, $salaryYear);
        }
      } else {
        // insert new record salary record
        (new SalaryProcessDataService())->updateAnEmployeeMonthlySalaryRecord(-1, $anEmployee, $month, $salaryYear);
      }


      return $anEmployee;
    } catch (Exception $ex) {
      return null;
      // dd('System Exception Occurred',$anEmployee->employee_id, $ex);
    }
  }
  // process multi project work salary
  private function processAnEmployeMultipleProjectWorkSalary($anEmployee, $month, $salaryYear)
  {

    $records = (new EmployeeAttendanceDataService())->getAnEmployeeMultiprojectWorkRecordsOnly($anEmployee->emp_auto_id, $month, $salaryYear);

    foreach ($records as $arecord) {

      $new_record  = (new SalaryProcessDataService())->calculateAnEmployeeMultipleProjectSalaryForAMonth($anEmployee, $arecord);
      (new SalaryProcessDataService())->updateEmployeeMultipleProjectWorkSalaryAmount(
        $arecord->empwh_auto_id,
        $new_record->total_amount,
        $new_record->food_amount,
        $new_record->other_amount,
        $new_record->ot_amount,
        Auth::user()->id
      );
    }
  }




  /* ==================== 2 Projet & Employee Status base Salary Report ==================== */
  public function projectEmployeeStatusMonthWiseSalaryReport(Request $request)
  {
    //  dd($request->all());
    if ($request->salary_report_type == 1) {
      // all emp. now working this project salary
      return $this->salaryReportThoseAreWorkingNowInthisProject($request->proj_id, $request->month, $request->year, $request->salary_status, [1, NULL]);
    } else if ($request->salary_report_type == 6) {
      // only basic emp. now working this project salary
      return $this->salaryReportThoseAreWorkingNowInthisProject($request->proj_id, $request->month, $request->year, $request->salary_status, [NULL]);
    } else if ($request->salary_report_type == 7) {
      // only hourly emp. now working this project salary
      return $this->salaryReportThoseAreWorkingNowInthisProject($request->proj_id, $request->month, $request->year, $request->salary_status, [1]);
    } else if ($request->salary_report_type == 2) {
      // last month salary
      return $this->projectWiseEployeeLastSalaryHistory($request->proj_id);
    } else if ($request->salary_report_type == 3) {
      // project base paid unpaid salary
      return $this->processAndDisplayProjectwiseEmployeeSalaryReportWithPaidUnpaidStatus($request->proj_id, $request->month, $request->salary_status, $request->year, $request->emp_status_id);
    } else if ($request->salary_report_type == 4 || $request->salary_report_type == 5) {
      // those are not received salary 1 or 2 month
      return $this->processAndDisplayEmployeesThoseAreNotReceivedSalary($request->month, $request->year, $request->salary_report_type);
    } else if ($request->salary_report_type == 8) {
      //Not Active but have salary All Projects
      return $this->salaryReportThoseAreNotActiveNowInthisProject($request->proj_id, $request->month, $request->year, $request->salary_status, $request->salary_report_type);
    } else {
      return "Please Select Report Type and Try Again.";
    }
  }

  // salary_report_type  1 now working this project salary
  private function salaryReportThoseAreWorkingNowInthisProject($project_id, $month, $salaryYear, $salary_status, $salary_type_list)
  {


    $employeeType = "";
    if (count($salary_type_list) == 2) {
      // both basic and hourly employee
      $employeeType = "All";
      $salaryReport =  (new SalaryProcessDataService())->getAllTypesOfEmployeesSalaryReportThoseAreWorkingThisProject($project_id, $month, $salaryYear, $salary_status);
    } else {
      // only hourly or basic emp
      $employeeType = $salary_type_list[0] == 1 ? "Hourly" : "Direct Basic";
      $salaryReport =  (new SalaryProcessDataService())->getEmployeesSalaryReportThoseAreWorkingThisProject($project_id, $month, $salaryYear, $salary_status, $salary_type_list[0]);
    }

    $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
    $project = (new EmployeeRelatedDataService())->getProjectNameByProjectId($project_id);
    $monthName = (new HelperController())->getMonthName($month);
    $reportLeftTitle = ['Salary Month of ' . $monthName . ', ' . $salaryYear, ('Project: ' . $project), ''];
    $login_name = Auth::user()->name;
    return view('admin.salary-generate.projectwise.employee-salary-details', compact('login_name', 'salaryReport', 'reportLeftTitle', 'company', 'monthName', 'salaryYear', 'project', 'employeeType'));
  }

  //  salary_report_type 3  projec base paid unpaid salary
  private function processAndDisplayProjectwiseEmployeeSalaryReportWithPaidUnpaidStatus($projectId, $month, $salary_status_id, $salaryYear, $empStatusId)
  {

    $monthName = (new HelperController())->getMonthName($month);
    $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
    $project = (new EmployeeRelatedDataService())->findAProjectInformation($projectId);
    $project = $project == null ? 'All' : $project->proj_name;

    $salary_status = $salary_status_id == null ? 'All' : ($salary_status_id == 0 ? 'Unpaid' : 'Paid');
    $salaryReport = (new SalaryProcessDataService())->getEmployeeSalaryHistoryWithProjectAndEmployeeJobStatus($projectId, $month, $salaryYear, $salary_status_id);
    $login_name = Auth::user()->name;
    return view('admin.salary-generate.projectwise.statuswise-salary-report', compact('login_name', 'salaryReport', 'monthName', 'salaryYear', 'company', 'project', 'salary_status'));
  }

  //  salary_report_type 2 last month salary
  private function projectWiseEployeeLastSalaryHistory($project_id)
  {

    $employeeList = (new EmployeeDataService())->getAllEmployeesInformationByProjectId($project_id, 1);
    $salaryReport =  array();
    $counter = 0;
    foreach ($employeeList as $emp) {
      $srecord =  (new SalaryProcessDataService())->getAnEmployeeLastMonthSalaryRecord($emp->emp_auto_id);
      if ($srecord) {
        $salaryReport[$counter++] = $srecord;
      }
    }
    $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
    $project = (new EmployeeRelatedDataService())->getProjectNameByProjectId($project_id);
    $reportLeftTitle = ['Employees Last Month Salary Details', ('Project: ' . $project), ''];
    $monthName = '';
    $salaryYear = "";
    $employeeType = "";
    $login_name = Auth::user()->id;
    return view('admin.salary-generate.projectwise.employee-salary-details', compact('login_name', 'salaryReport', 'reportLeftTitle', 'company', 'monthName', 'salaryYear', 'project', 'employeeType'));
  }
  // salary_report_type 4&5 those are not received salary
  private function processAndDisplayEmployeesThoseAreNotReceivedSalary($month, $year, $salary_report_type)
  {

    $no_of_month = $salary_report_type == 4 ? 1 : 2;
    $employeeList = (new EmployeeDataService())->getAllEmployeesForFindingEmployeeThoseAreNotReceivedSalary();
    $report =  array();
    $counter = 0;
    foreach ($employeeList as $emp) {
      $salary_record1 =  (new SalaryProcessDataService())->findAnEmployeeThoseAreNotReceivedSalary($emp->emp_auto_id, $month, $year);
      if ($salary_record1->count() >= $no_of_month) {
        $emp->salary_record = $salary_record1;
        $report[$counter++] = $emp;
      }
    }

    $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
    $reportLeftTitle = ['Employee Those Not Received Salary', 'Project: All', ''];
    $login_name = Auth::user()->name;
    return view('admin.salary-generate.projectwise.salary_unpaid_emp_with_month', compact('report', 'reportLeftTitle', 'company', 'login_name'));
  }
  // not active but have salary this month
  private function salaryReportThoseAreNotActiveNowInthisProject($project_id, $month, $year, $salary_status, $salary_type_list)
  {

    $salary_status = $salary_status == null ? [0, 1] : [$salary_status];
    // $project_id = [$project_id];
    // if($projectId == null){
    $project_id = (new ProjectDataService())->getAllActiveProjectIDOfABranchOfficeAsArray(Auth::user()->branch_office_id);
    //     }

    // dd($salary_status);
    $salaryReport = (new SalaryProcessDataService())->getEmployeeWorkingSalaryDetailsThoseAreNotActiveNowByProjectAndSponsor($project_id, $month, $year, $salary_status, [2, 3, 4, 5, 6, 7, 8, 9], 1);
    $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
    $project_name = (new EmployeeRelatedDataService())->getProjectNameByProjectId($project_id);
    $monthName = (new HelperController())->getMonthName($month);
    $reportLeftTitle = ['Salary Month of ' . $monthName . ', ' . $year, ('Project: ' . $project_name), ''];
    $login_name = Auth::user()->name;
    return view('admin.salary-generate.all-employee.not_active_but_have_salary_this_month', compact('login_name', 'salaryReport', 'reportLeftTitle', 'company'));
  }



  /* ====================3 Employee Type (Basic Salary, Hourly,Direct) salary report ==================== */
  public function projectEmployeeMonthWiseSalaryReport(Request $request)
  {



    try {
      $projectId = $request->proj_id;
      $month = $request->month;
      $salaryYear = $request->year;
      $salaryStatus = $request->salary_status; // 0 = Unpaid 1= Paid
      $empType = $request->emp_type_id;
      $monthName = (new HelperController())->getMonthName($month);
      $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
      $login_name = Auth::user()->name;
      $project = 'All';
      if ($projectId != null) {
        $project = (new EmployeeRelatedDataService())->getProjectNameByProjectId($projectId);
      }

      $employeeType = 'All';
      $hourly_employee = null;
      $emp_type = null;

      if ($empType == -2) {
        $hourly_employee = null;
        $emp_type = 1;
        $employeeType = "Direct(Basic & Hourly) Employee Salary";
      } elseif ($empType == -1) {  // Basic Salary direct Employee
        $hourly_employee = null;
        $emp_type = 1;
        $employeeType = "Direct Employee-Basic Salary";
      } else if ($empType == 1) { // Hourly salary direct Employee
        $hourly_employee = true;
        $employeeType = "Hourly Employee";
        $emp_type = 1;

        // salary status show/hide
        $paid_unpaid_show = $request->paid_unpaid_show == null ? false : true;

        if ($request->proj_id == null) {
          $project_ids = (new ProjectDataService())->getAllActiveProjectIDs($projectId);
        } else {
          $project_ids = [$projectId];
        }
        $salaryReport = (new SalaryProcessDataService())->getAllHourlyEmployeesSalaryInforForSalarySheetPrintByProjectSalaryStatus($project_ids, $month, $salaryYear, $salaryStatus);
        return view('admin.salary-generate.projectwise.projectwise-salary-report', compact('login_name', 'paid_unpaid_show', 'salaryReport', 'monthName', 'salaryYear', 'company', 'project', 'employeeType'));
      } else if ($empType == 2) {   // Indirect Employee
        $employeeType = 'Indirect Employee';
        $hourly_employee = NULL;
        $emp_type = 2;
      } else if ($empType == 3) {
        $employeeType = "Basic Salary(Indirect & Direct) Employees";
        $hourly_employee = NULL;
        $emp_type = 3;
      }


      // only this projectwork salary  report
      if ($request->projec_cost_check) {
        return $this->processAndDisplayOnlyThisProjectWorkingEmployeesSalaryFromMultiProjectRecord($request->month, $request->year, $request->proj_id, $emp_type == null ? 0 : $emp_type, $hourly_employee);
      }

      // salary status show/hinde
      $paid_unpaid_show = $request->paid_unpaid_show == null ? false : true;
      // $salaryReport = (new SalaryProcessDataService())->getEmployeeSalaryHistoryWithProjectAndEmployeeTypeBasicAndHourlyEmployee($projectId, $emp_type, $hourly_employee, $month, $salaryYear, $salaryStatus);

      if ($empType == -2) {
        // All direct employees
        $salaryReport = (new SalaryProcessDataService())->getAllDirectEmployeesSalaryInforForSalarySheetPrintByProjectSalaryStatus($projectId, $month, $salaryYear, $salaryStatus);
      } else {
        $salaryReport = (new SalaryProcessDataService())->getEmployeeSalaryHistoryWithProjectAndEmployeeTypeBasicAndHourlyEmployee($projectId, $emp_type, $hourly_employee, $month, $salaryYear, $salaryStatus);
      }

      return view('admin.salary-generate.projectwise.projectwise-salary-report', compact('login_name', 'paid_unpaid_show', 'salaryReport', 'monthName', 'salaryYear', 'company', 'project', 'employeeType'));
    } catch (Exception $ex) {
      return 'Operation Failed, System Exception: ' . $ex->getMessage();
    }
  }




  // salary report 3.1
  public function showSalaryReportProjectAndEmpTradeBase(Request $request)
  {
    try {
      $projectId = $request->proj_idss;
      $trades = $request->trade_idss;
      $month = (int) $request->month;
      $salaryYear = $request->year;
      $salaryStatus = $request->has('salary_statuss') ? $request->salary_statuss : [0, 1]; // 0 = Unpaid 1= Paid

      $monthName = (new HelperController())->getMonthName($month);
      $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);

      $sponser = "All";
      $project = "All";

      if ($trades == null) {
        $trades = (new EmployeeRelatedDataService())->getAllActiveCategoryIDAsArrayWithRankingSequence();
      }
      if ($projectId == null) {
        $projectId = (new ProjectDataService())->getAllActiveProjectIDOfABranchOfficeAsArray(Auth::user()->branch_office_id);
      }
      $report_title = array();
      $report_title[0]  = 'Salary Report Month of ' . $monthName . ', ' . $salaryYear;
      $report_title[1]  = 'Employee Trade Base Salary Report';
      $report_title[2]  = ' ';
      $salaryReport = (new SalaryProcessDataService())->getEmployeeSalaryProjectAndTrade($projectId, $trades, $month, $salaryYear, $salaryStatus);
      $login_name = Auth::user()->name;
      return view('admin.salary-generate.spnserwise.sponserwise-salary-report', compact('login_name', 'monthName', 'salaryYear', 'project', 'report_title', 'salaryReport', 'company'));
    } catch (Exception $ex) {
      return "System Operation Failed \n " . $ex;
    }
  }


  private function processAndDisplayOnlyThisProjectWorkingEmployeesSalaryFromMultiProjectRecord($month, $year, $project_id)
  {

    $employees = (new SalaryProcessDataService())->getOnlyThisProjectWorkingEmployeesRecordFromMultiProjectTable($month, $year, $project_id);
    $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
    $month = (new HelperController())->getMonthName($month);
    $login_name = Auth::user()->name;

    $project = 'All';
    if ($project_id != null) {
      $project = (new EmployeeRelatedDataService())->getProjectNameByProjectId($project_id);
    }
    return view('admin.salary-generate.projectwise.onlythis_project_work_salary_emp_list', compact('login_name', 'employees', 'project', 'company', 'month', 'year'));
  }



  /* ==================== 4 Sponser Wise Employee Salary Report ==================== */
  public function sponserWiseMonthlySalaryReport(Request $request)
  {

    try {
      $project_ids = $request->has('proj_id') ? $request->proj_id : (new ProjectDataService())->getAllActiveProjectIDOfABranchOfficeAsArray(Auth::user()->branch_office_id);

      $sponser_ids = $request->has('SponsId') ? $request->SponsId : [];
      $month = $request->month;
      $salaryYear = $request->year;
      $salary_status_list = $request->has('salary_status') ?  $request->salary_status : [0, 1];  // 0 = Unpaid 1= Paid

      $monthName = (new HelperController())->getMonthName($month);
      $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);

      $sponser = " ";
      $project = "All";

      if (count($sponser_ids) == 0) {
        $sponser = "All";
        $sponser_ids = (new EmployeeRelatedDataService())->getAllActiveSponsorIdAsArrayOfABranchOffice(Auth::user()->branch_office_id);
      } else if (count($sponser_ids) == 1) {
        $sponser = (new EmployeeRelatedDataService())->getASponserNameBySponerId($sponser_ids[0]);
      } else {
        $sponser = "Multiple";
      }
      if (count($project_ids) == 1) {
        $project = (new EmployeeRelatedDataService())->getProjectNameByProjectId($project_ids[0]);
      }

      $report_title = array();
      $report_title[0]  = 'Salary Report Month of ' . $monthName . ', ' . $salaryYear;
      $report_title[1]  = 'Sponsor: ' . $sponser;
      $report_title[2]  = 'Project: ' . $project;
      $salaryReport = (new SalaryProcessDataService())->searchSponsorBaseSalarySheetReportByMultipleProjectIDAndSponsorID($project_ids, $sponser_ids, $month, $salaryYear, $salary_status_list);
      $login_name = Auth::user()->name;
      return view('admin.salary-generate.spnserwise.sponserwise-salary-report', compact('login_name', 'report_title', 'salaryReport', 'company', 'monthName', 'salaryYear', 'project'));
    } catch (Exception $ex) {

      return "Operation Failed, Please Refresh Page and Try Again";
    }

    // old method
    // try {
    //   $project_ids = $request->has('proj_id') ? $request->proj_id : (new ProjectDataService())->getAllActiveProjectIDOfABranchOfficeAsArray(Auth::user()->branch_office_id);

    //   $sponserId = $request->SponsId;
    //   $month = $request->month;
    //   $salaryYear = $request->year;
    //   $salary_status_list = $request->has('salary_status') ?  $request->salary_status : [0, 1];  // 0 = Unpaid 1= Paid

    //   $monthName = (new HelperController())->getMonthName($month);
    //   $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);

    //   $sponser = "All";
    //   $project = "All";

    //   if ($sponserId > 0) {
    //     $sponser = (new EmployeeRelatedDataService())->getASponserNameBySponerId($sponserId);
    //   }
    //   if (count($project_ids) == 1) {
    //     $project = (new EmployeeRelatedDataService())->getProjectNameByProjectId($project_ids[0]);
    //   }

    //   $report_title = array();
    //   $report_title[0]  = 'Salary Report Month of ' . $monthName . ', ' . $salaryYear;
    //   $report_title[1]  = 'Sponsor: ' . $sponser;
    //   $report_title[2]  = 'Project: ' . $project;
    //   $salaryReport = (new SalaryProcessDataService())->searchSponsorBaseSalarySheetReportByMultipleProjectIDAndSponsorID($project_ids, [$sponserId], $month, $salaryYear, $salary_status_list);
    //   $login_name = Auth::user()->name;
    //   return view('admin.salary-generate.spnserwise.sponserwise-salary-report', compact('login_name', 'report_title', 'salaryReport', 'company', 'monthName', 'salaryYear', 'project'));
    // } catch (Exception $ex) {

    //   return "Operation Failed, Please Refresh Page and Try Again";
    // }


  }


  /* ==================== 5 Report For Saudi By Sponsort ==================== */
  public function getSponserWiseMonthlySalaryReportForSaudi(Request $request)
  {


    $sponsor_id_list = $request->sponsor_id_list;
    $month = $request->month;
    $salaryYear = $request->year;

    $monthName = (new HelperController())->getMonthName($month);
    $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);

    $sponser = "";
    foreach ($sponsor_id_list as $spId) {
      $sponser = $sponser . "\n* " . (new EmployeeRelatedDataService())->getASponserNameBySponerId($spId);
    }
    $report_title = array();
    $report_title[0]  = 'Salary Report Month of ' . $monthName . ', ' . $salaryYear;
    $report_title[1]  = 'Sponor: ' . $sponser;


    $salaryReport = (new SalaryProcessDataService())->getSponsorwiseEmployeeSalaryReportForSaudi($sponsor_id_list, $month, $salaryYear);
    $login_name = Auth::user()->name;
    //  dd($salaryReport[0]);
    return view('admin.salary-generate.spnserwise.sponsor_wise_salary_for_saudi_report', compact('report_title', 'salaryReport', 'company', 'login_name'));
  }

  /* ===================6 Month wise All Employee Salary with PAID/Unpaid =================== */
  public function monthWiseSalary(Request $request)
  {


    if ($request->report_type == 1) {
      // multi project work emp salary report
      return $this->showAMonthUnpaiddSalaryEmployeesReport($request);
    } else if ($request->report_type == 2) {

      // Year by Year unpaid salary month base emps number
      return $this->showYearByYearUnpaidEmployeesSummaryReport($request);
    } else if ($request->report_type == 3) {

      //  Year all project salary samary report
      return $this->showAYearAllMonthProjectBaseSalaryTotalAmountSummaryReport($request);
    } else {
      return "Please Select Report Type and Try Again.";
    }
  }
  // 6.1
  public function showAMonthUnpaiddSalaryEmployeesReport(Request $request)
  {
    $month = $request->month;
    $salaryYear = $request->year;
    $salaryStatus = $request->salary_status; // 0 = Unpaid 1= Paid
    $monthName = (new HelperController())->getMonthName($month);
    $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);


    $salaryReport = (new SalaryProcessDataService())->getEmployeeSalaryHistory(-1, -1, $month, $salaryYear, $salaryStatus);
    $allSalaryAmount = (new SalaryProcessDataService())->getSalaryTotalAmount(-1, $month, $salaryYear, $salaryStatus);
    $iqamaAmount =  (new SalaryProcessDataService())->getSalaryIqamaAdvanceTotalAmount($month, $salaryYear, $salaryStatus);
    $totalHours =  (new SalaryProcessDataService())->getSalaryMonthTotalHours(-1, $month, $salaryYear, $salaryStatus);
    $totalSaudiTax = (new SalaryProcessDataService())->getSalarySaudiTaxTotalAmount($month, $salaryYear, $salaryStatus);
    $totalOverTimeHours = (new SalaryProcessDataService())->getSalaryMonthTotalOvertimeHours(-1, $month, $salaryYear, $salaryStatus);
    $totalOverTimeAmount = (new SalaryProcessDataService())->getSalaryMonthTotalOvertimeAmount($month, $salaryYear, $salaryStatus);
    $totalFoodAllowance = (new SalaryProcessDataService())->getSalaryMonthFoodAllowanceTotalAmount($month, $salaryYear, $salaryStatus);
    $totalContribution = (new SalaryProcessDataService())->getSalaryMonthEmployeeCPFTotalAmount($month, $salaryYear, $salaryStatus);
    $totalOtherAdvance = (new SalaryProcessDataService())->getSalaryMonthOtherAdvanceTotalAmount($month, $salaryYear, $salaryStatus);

    $login_name = Auth::user()->name;

    if ($salaryReport) {
      return view('admin.salary-generate.all-employee.all-employees-salary', compact('login_name', 'salaryReport', 'month', 'monthName', 'salaryYear', 'company', 'allSalaryAmount', 'iqamaAmount', 'totalHours', 'totalSaudiTax', 'totalOverTimeHours', 'totalOverTimeAmount', 'totalFoodAllowance', 'totalContribution', 'totalOtherAdvance'));
    } else {
      Session::flash('salaryRecordNotFound', 'value');
      return redirect()->back();
    }
  }

  // 6.2
  public function showYearByYearUnpaidEmployeesSummaryReport(Request $request)
  {

    $month = $request->month;
    $from_year = (int) $request->year;
    $salaryStatus = 0; // Unpaid
    $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
    $login_name = Auth::user()->name;

    $currentYear = date("Y");
    $records = array();
    $counter = 0;
    for ($from_year = $from_year; $from_year <= $currentYear; $from_year++) {
      $yearObj = new \stdClass();
      $yearObj->year = $from_year;
      $summary = (new SalaryProcessDataService())->processAMonthUnpaidTotalEmployeesAndNetSalaryAmountReport($month, $from_year);
      if ($summary) {
        $yearObj->summary = $summary;
        $records[$counter++] = $yearObj;
      }
    }
    // dd($records[4]);
    $report_title = ['Month by Month Unpaid Salary Summary Report'];
    return view('admin.salary-generate.all-employee.year_by_year_unpaid_salary_summary', compact('login_name', 'records', 'company', 'report_title'));
  }

  // 6.3
  public function showAYearAllMonthProjectBaseSalaryTotalAmountSummaryReport(Request $request)
  {

    $month = $request->month;
    $year = (int) $request->year;
    $salaryStatus = 0; // Unpaid
    $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
    $login_name = Auth::user()->name;

    $projects = (new SalaryProcessDataService())->getListOfProjectsHavingEmployeeSalaryInAYearForReport($year);
    $records = array();
    $counter = 0;
    foreach ($projects as $ap) {
      $ap->summary = (new SalaryProcessDataService())->getAYearEveryMonthTotalSalarySummaryReport($year, $ap->proj_id);
      $records[$counter++] = $ap;
    }

    //  dd($records[2]);
    $report_title = ['Project Base Month by Month Salary Summary Report'];
    return view('admin.salary-generate.all-employee.project_base_month_by_month_salary_report', compact('login_name', 'records', 'company', 'year', 'report_title'));
  }






  /* =================== 7. Project Wise All Employee Salary Paid and Unpaid Summary  =================== */
  public function monthAndYearWiseSalarySummary(Request $request)
  {

    if ($request->report_type == 1) {
      // paid unpaid salary summary
      return $this->processMonthlySalaryAndManpowerSummaryReport($request);
    } else if ($request->report_type == 2) {
      // Only The Project Expenses Report For Cost Controll,
      // Only The Project Expenses Report For Cost Controll, this report transfer to cost control module, but currently show in salary module
      $month = (int) $request->month;
      $salaryYear = (int) $request->year;
      $projectId =  $request->has('proj_id') ? $request->proj_id[0] : 0;
      if ($projectId == 0) {
        Session::flash('error', 'Please Select a Project and Try Again.');
        return redirect()->back();
      }
      return $this->processEmployeeMultiProjectSalaryTemporary($projectId, $month, $salaryYear);
    } else if ($request->report_type == 3) {
      return $this->currentlyWorkingEmployeeSalarySummaryReport($request);
    } else if ($request->report_type == 4) {
      return $this->currentlyNotWorkingEmployeesButHaveSalarySummaryReport($request);
    }
  }

  private function processMonthlySalaryAndManpowerSummaryReport($request)
  {
    // 0 = Unpaid, 1= Paid


    $month = (int) $request->month;
    $year = (int) $request->year;
    $project_ids =  $request->has('proj_id') ? $request->proj_id : (new ProjectDataService())->getAllActiveProjectIDOfABranchOfficeAsArray(Auth::user()->branch_office_id);
    $monthName = (new HelperController())->getMonthName($month);
    $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
    $login_name = Auth::user()->name;

    $records = array();
    $counter = 0;
    foreach ($project_ids as $pid) {
      $aproject = (new EmployeeRelatedDataService())->findAProjectInformation($pid);
      $salaryReport =  (new SalaryProcessDataService())->calculateAProjectPaidUnPaidSalarySummaryForReport($pid, $month, $year);


      $aproject->paid_emp = 0;
      $aproject->paid_salary = 0;
      $aproject->paid_iqama_adv = 0;
      $aproject->paid_other_adv = 0;
      $aproject->paid_saudi_tax = 0;

      $aproject->unpaid_emp = 0;
      $aproject->unpaid_salary = 0;
      $aproject->unpaid_iqama_adv = 0;
      $aproject->unpaid_other_adv = 0;
      $aproject->unpaid_saudi_tax = 0;
      $is_found = false;

      if (count($salaryReport) == 2) {
        $aproject->unpaid_emp = $salaryReport[0]->total_emp;
        $aproject->unpaid_salary = $salaryReport[0]->total_salary;
        $aproject->unpaid_iqama_adv = $salaryReport[0]->total_iqama_adv;
        $aproject->unpaid_other_adv = $salaryReport[0]->total_other_adv;
        $aproject->unpaid_saudi_tax = $salaryReport[0]->total_saudi_tax;

        $aproject->paid_emp = $salaryReport[1]->total_emp;
        $aproject->paid_salary = $salaryReport[1]->total_salary;
        $aproject->paid_iqama_adv = $salaryReport[1]->total_iqama_adv;
        $aproject->paid_other_adv = $salaryReport[1]->total_other_adv;
        $aproject->paid_saudi_tax = $salaryReport[1]->total_saudi_tax;
        $is_found = true;
      } else if (count($salaryReport) == 1) {

        if ($salaryReport[0]->Status == 0) {
          $aproject->unpaid_emp = $salaryReport[0]->total_emp;
          $aproject->unpaid_salary = $salaryReport[0]->total_salary;
          $aproject->unpaid_iqama_adv = $salaryReport[0]->total_iqama_adv;
          $aproject->unpaid_other_adv = $salaryReport[0]->total_other_adv;
          $aproject->unpaid_saudi_tax = $salaryReport[0]->total_saudi_tax;
        } else {
          $aproject->paid_emp = $salaryReport[0]->total_emp;
          $aproject->paid_salary = $salaryReport[0]->total_salary;
          $aproject->paid_iqama_adv = $salaryReport[0]->total_iqama_adv;
          $aproject->paid_other_adv = $salaryReport[0]->total_other_adv;
          $aproject->paid_saudi_tax = $salaryReport[0]->total_saudi_tax;
        }
        $is_found = true;
      }

      if ($is_found) {
        $records[$counter++] = $aproject;
      }
    }

    return view('admin.salary-generate.all-employee.salary-summary', compact('login_name', 'records', 'monthName', 'year', 'company'));
  }

  /* ===================
    Project Wise All Employee Salary Paid and Unpaid Summary ,
    Only The Project Expenses Report For Cost Controll
    =================== */
  private function processEmployeeMultiProjectSalaryTemporary($project_id, $month, $year)
  {
    $emp_auto_id_list =  (new EmployeeAttendanceDataService())->getListOfEmployeeAutoIdWorkInMultiProectByProjectIdMonthYear($project_id, $month, $year);

    $pre_emp_id = null;
    $counter = 0;
    foreach ($emp_auto_id_list as $emp_auto_id) {
      $emp_auto_id = $emp_auto_id->emp_id;
      if ($pre_emp_id == null) {
        $pre_emp_id = $emp_auto_id;
      } else if ($pre_emp_id == $emp_auto_id) {
        continue;
      } else {
        $pre_emp_id = $emp_auto_id;
      }

      $anEmployee = (new SalaryProcessDataService())->getAnEmployeeDetailsWithSalaryRecordForMultiProjectSalaryProcess($emp_auto_id, $month, $year);
      // dd($anEmployee);
      if ($anEmployee != null) {
        $this->processAnEmployeMultipleProjectWorkSalary($anEmployee, $month, $year);
        $counter++;
      }
    }

    $records = (new SalaryProcessDataService())->getOnlyThisProjectTotalSalaryAmountForMultipleProjectWorkByProjectMonthAndYear($project_id, $month, $year);
    $counter = 0;
    // dd($records);
    foreach ($records as $arecord) {

      $arecord->project_name = (new ProjectDataService())->getProjectNameByProjectId($project_id);
      $arecord->month_name = (new HelperController())->getMonthName($month);
      $arecord->year = $year;
      $records[$counter++] = $arecord;
    }
    // dd($records);
    $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
    $login_name = Auth::user()->name;
    return view('admin.salary-generate.projectwise.thisproject_salary_cost_only', compact('login_name', 'records', 'company'));
  }

  // currently working employee salary summary report
  private function currentlyWorkingEmployeeSalarySummaryReport($request)
  {

    $month = (int) $request->month;
    $year = (int) $request->year;
    $salary_status = $request->has('salary_status') ? $request->salary_status : [0, 1];
    $project_ids =  $request->has('proj_id') ? $request->proj_id : (new ProjectDataService())->getAllActiveProjectIDOfABranchOfficeAsArray(Auth::user()->branch_office_id);
    $monthName = (new HelperController())->getMonthName($month);
    $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);

    $records = array();
    $counter = 0;
    foreach ($project_ids as $pid) {
      $aproject = (new EmployeeRelatedDataService())->findAProjectInformation($pid);
      $salaryReport =  (new SalaryProcessDataService())->getProjectBaseSalarySummaryAmountReportBaseOnCurrentlyWorkingEmployees($pid, $month, $year, $salary_status);
      $aproject->basic_emp = 0;
      $aproject->basic_salary = 0;
      $aproject->hourly_emp = 0;
      $aproject->hourly_salary = 0;
      $is_found = false;

      if (count($salaryReport) == 2) {
        $aproject->basic_emp = $salaryReport[0]->total_emp;
        $aproject->basic_salary = $salaryReport[0]->total_salary;
        $aproject->hourly_emp = $salaryReport[1]->total_emp;
        $aproject->hourly_salary = $salaryReport[1]->total_salary;
        $is_found = true;
      } else if (count($salaryReport) == 1) {

        if ($salaryReport[0]->hourly_employee == 1) {
          $aproject->hourly_emp = $salaryReport[0]->total_emp;
          $aproject->hourly_salary = $salaryReport[0]->total_salary;
        } else {
          $aproject->basic_emp = $salaryReport[0]->total_emp;
          $aproject->basic_salary = $salaryReport[0]->total_salary;
        }
        $is_found = true;
      }

      //  dd($salaryReport);
      if ($is_found) {
        $records[$counter++] = $aproject;
      }
    }
    //  dd($records);

    $login_name = Auth::user()->name;
    return view('admin.salary-generate.projectwise.project_base_working_emp_salary_summary', compact('login_name', 'records', 'monthName', 'year', 'company'));
  }

  // currently not working employee but have salary summary report
  private function currentlyNotWorkingEmployeesButHaveSalarySummaryReport($request)
  {

    $month = (int) $request->month;
    $year = (int) $request->year;
    $salary_status = $request->has('salary_status') ? $request->salary_status : [0, 1];
    $project_ids =  $request->has('proj_id') ? $request->proj_id : (new ProjectDataService())->getAllActiveProjectIDOfABranchOfficeAsArray(Auth::user()->branch_office_id);
    $monthName = (new HelperController())->getMonthName($month);
    $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);

    $records = array();
    $counter = 0;
    foreach ($project_ids as $pid) {
      $aproject = (new EmployeeRelatedDataService())->findAProjectInformation($pid);
      $salaryReport =  (new SalaryProcessDataService())->getProjectBaseSalarySummaryAmountReportBaseOnCurrentlyNotWorkingEmployees($pid, $month, $year, $salary_status);
      $aproject->basic_emp = 0;
      $aproject->basic_salary = 0;
      $aproject->hourly_emp = 0;
      $aproject->hourly_salary = 0;
      $is_found = false;

      if (count($salaryReport) == 2) {
        $aproject->basic_emp = $salaryReport[0]->total_emp;
        $aproject->basic_salary = $salaryReport[0]->total_salary;
        $aproject->hourly_emp = $salaryReport[1]->total_emp;
        $aproject->hourly_salary = $salaryReport[1]->total_salary;
        $is_found = true;
      } else if (count($salaryReport) == 1) {

        if ($salaryReport[0]->hourly_employee == 1) {
          $aproject->hourly_emp = $salaryReport[0]->total_emp;
          $aproject->hourly_salary = $salaryReport[0]->total_salary;
        } else {
          $aproject->basic_emp = $salaryReport[0]->total_emp;
          $aproject->basic_salary = $salaryReport[0]->total_salary;
        }
        $is_found = true;
      }

      //  dd($salaryReport);
      if ($is_found) {
        $records[$counter++] = $aproject;
      }
    }
    // dd($records);

    $login_name = Auth::user()->name;
    return view('admin.salary-generate.projectwise.projectbase_inactive_emp_salary_summary', compact('login_name', 'records', 'monthName', 'year', 'company'));
  }



  /* =================== 8 Project,month,year Wise Salary, TOtal OT , Hours, Iqama Adv, Others Adv. Summary  =================== */

  public function projectwiseTotalWorkingHoursAndTotalSalary(Request $request)
  {

    if ($request->report_type == 1) {
      // Monthly Salary base Total Hours and Salary Summary
      return  $this->processProjectwiseMonthlyTotalWorkingHoursAndTotalSalaryReport($request->project_id_list, $request->from_date, $request->to_date);
    } else if ($request->report_type == 2) {
      // Manpower Summary for Monthly Salary
      return  $this->processProjectwiseMonthlySalaryManpowerSummaryReport($request->project_id_list[0], $request->from_date, $request->to_date);
    } else if ($request->report_type == 3) {
      // Only THis Project Salary Summary
      return $this->processOnlyThisProjectMonthlyTotalWorkingHoursAndTotalSalaryReport($request->project_id_list[0], $request->from_date, $request->to_date);
    } else if ($request->report_type == 4) {
      // only single month all project salary and deduction summmary
      return $this->processProjectwiseAMonthlySalaryAndDeductionSummaryReport($request->project_id_list, $request->from_date, $request->to_date);
    } else if ($request->report_type == 5) {
      // From January to December Yearly Salary Statement Month by Month Salary Summary Report
      return $this->processYearlySalaryStatementMonthByMonthSalarySummaryReport($request);
    } else if ($request->report_type == 6) {
      // from custom month to month project wise month by month salary summary report
      return $this->processProjectwiseMonthByMonthSalarySummaryReport($request);
    }
  }
  // report 8.1  Monthly Salary base Total Hours and Salary Summary
  private function processProjectwiseMonthlyTotalWorkingHoursAndTotalSalaryReport($project_id_list, $from_date, $to_date)
  {


    $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
    $project_names['proj_name'] = "All Project";
    $monthwithYears =  (new HelperController())->getMonthsInRangeOfDate($from_date, $to_date);
    $salaryReport = array();
    $counter = 0;
    foreach ($monthwithYears as $my) {

      $summaryRecords = (new SalaryProcessDataService())->getSalarytSheetWiseTotalSalarySummaryByMultipleProjectMonthAndYear($project_id_list, $my['month'], $my['year']);

      $totalUnPaidSalaryAllIncluded = 0;
      $grossTotalUnPaidSalary = 0;
      $total_iqama_advance = 0;
      $total_other_advance = 0;

      $grossTotalPaidSalary = 0;
      $totalPaidSalaryAllIncluded = 0;
      $total_emp = 0;

      if (count($summaryRecords) == 2) {
        $total_emp = (int) $summaryRecords[0]->total_emp + (int) $summaryRecords[1]->total_emp;
        if ($summaryRecords[0]->status == 0) { // unpaid salary
          $unpaidRecord = $summaryRecords[0];
          $paidRecord = $summaryRecords[1];  // paid salary record
        } else {

          $paidRecord = $summaryRecords[0];  // paid salary
          $unpaidRecord = $summaryRecords[1];
        }

        $totalUnPaidSalaryAllIncluded = $unpaidRecord['total_salary'] + $unpaidRecord['total_saudi_tax'] + $unpaidRecord['total_iqama_adv'] + $unpaidRecord['total_other_adv'] + $unpaidRecord['total_contribution'];
        $grossTotalUnPaidSalary = $unpaidRecord['total_salary'];

        $total_iqama_advance += $paidRecord['total_iqama_adv'] + $unpaidRecord['total_iqama_adv'];
        $total_other_advance += $paidRecord['total_other_adv'] +  $unpaidRecord['total_other_adv'] + $paidRecord['total_saudi_tax'] +  $unpaidRecord['total_saudi_tax'];


        $totalPaidSalaryAllIncluded = $paidRecord['total_salary'] + $paidRecord['total_saudi_tax'] + $paidRecord['total_iqama_adv'] + $paidRecord['total_other_adv'] + $paidRecord['total_contribution'];
        $grossTotalPaidSalary = $paidRecord['total_salary'];
      } else  if (count($summaryRecords) == 1) {

        $total_emp = $summaryRecords[0]->total_emp;
        $total_iqama_advance += $summaryRecords[0]->total_iqama_adv;
        $total_other_advance += $summaryRecords[0]->total_other_adv + $summaryRecords[0]->total_saudi_tax;

        if ($summaryRecords[0]->status == 1) { // paid salary record
          $paidRecord = $summaryRecords[0];
          $totalPaidSalaryAllIncluded = $paidRecord['total_salary'] + $paidRecord['total_saudi_tax'] + $paidRecord['total_iqama_adv'] + $paidRecord['total_other_adv'] + $paidRecord['total_contribution'];
          $grossTotalPaidSalary = $paidRecord['total_salary'];
        } else {
          // uppaid salary record
          $unpaidRecord = $summaryRecords[0];
          $totalUnPaidSalaryAllIncluded = $unpaidRecord['total_salary'] + $unpaidRecord['total_saudi_tax'] + $unpaidRecord['total_iqama_adv'] + $unpaidRecord['total_other_adv'] + $unpaidRecord['total_contribution'];
          $grossTotalUnPaidSalary = $unpaidRecord['total_salary'];
        }
      }
      $info = array(
        'year' => $my['year'],
        'month_name' => (new HelperController())->getMonthName($my['month']),
        'month' => $my['month'],
        'total_emp' => $total_emp,
        'total_iqama_advance' => round($total_iqama_advance),
        'total_other_advance' => round($total_other_advance),
        'unpaid_gross_salary' => round($grossTotalUnPaidSalary),
        'paid_gross_salary' => round($grossTotalPaidSalary),
        'total_salary' => round($totalUnPaidSalaryAllIncluded + $totalPaidSalaryAllIncluded),
        'gross_total_salary' => round($grossTotalPaidSalary + $grossTotalUnPaidSalary),
      );
      $salaryReport[$counter++] = $info;
    }
    $login_name = Auth::user()->name;
    return view('admin.salary-generate.projectwise.salarysheet_monthlywise_salary_summary', compact('salaryReport', 'company', 'project_names', 'login_name'));
  }

  // 8.2 Monthly Salary base Total Employee Summary
  private function processProjectwiseMonthlySalaryManpowerSummaryReport($proj_id, $from_date, $to_date)
  {
    try {
      $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
      $projectName = "All Project";
      if ($proj_id > 0) {
        $projectName = (new ProjectDataService())->getProjectNameByProjectId($proj_id);
      } else
        $proj_id = null;

      $monthwithYears =  (new HelperController())->getMonthsInRangeOfDate($from_date, $to_date);
      $salary_report = array();
      $counter = 0;
      foreach ($monthwithYears as $my) {

        $total_emp = (new SalaryProcessDataService())->countMonthlySalryTotalEmployees($proj_id, $my['month'], $my['year']);
        $total_basic_emp =  (new SalaryProcessDataService())->countMonthlySalryTotalBasicSalaryOrHourlyEmployes($proj_id, $my['month'], $my['year'], null);
        $total_hourly_emp =  (new SalaryProcessDataService())->countMonthlySalryTotalBasicSalaryOrHourlyEmployes($proj_id, $my['month'], $my['year'], 1);
        $salary_paid_total_emp =  (new SalaryProcessDataService())->countMonthlySalryUnPaidTotalEmployes($proj_id, $my['month'], $my['year']);
        $salary_unpaid_total_emp =  (new SalaryProcessDataService())->countMonthlySalryPaidTotalEmployes($proj_id, $my['month'], $my['year']);

        $arecord = array(
          'year' => $my['year'],
          'month_name' => (new HelperController())->getMonthName($my['month']),
          'month' => $my['month'],
          'total_emp' => $total_emp,
          'total_basic_emp' => $total_basic_emp,
          'total_hourly_emp' => $total_hourly_emp,
          'salary_paid_total_emp' => $salary_paid_total_emp,
          'salary_unpaid_total_emp' => $salary_unpaid_total_emp,
        );
        $salary_report[$counter++] = $arecord;
      }
      $login_name = Auth::user()->name;
      return view('admin.salary-generate.projectwise.monthly_salary_emp_summary_report', compact('salary_report', 'company', 'projectName', 'login_name'));
    } catch (Exception $ex) {

      return "Operation Failed, System Exception  " . $ex->getMessage();
    }
  }

  // 8.3 Only THis Project Salary Summary
  private function processOnlyThisProjectMonthlyTotalWorkingHoursAndTotalSalaryReport($proj_id, $from_date, $to_date)
  {

    try {

      $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
      $projectName = "All Project";
      if ($proj_id > 0) {
        $projectName = (new ProjectDataService())->getProjectNameByProjectId($proj_id);
      }

      $monthwithYears =  (new HelperController())->getMonthsInRangeOfDate($from_date, $to_date);

      $summary_records = array();
      $counter = 0;
      foreach ($monthwithYears as $my) {

        $arecord = (new SalaryProcessDataService())->getOnlyThisProjectTotalSalaryAmountForMultipleProjectWorkByProjectMonthAndYear($proj_id, $my['month'], $my['year']);
        $arecord = $arecord[0];
        $arecord->year = $my['year'];
        $arecord->month_name = (new HelperController())->getMonthName($my['month']);
        $arecord->month = $my['month'];
        $summary_records[$counter++] = $arecord;
      }

      $login_name = Auth::user()->name;
      return view('admin.salary-generate.projectwise.only_projects_salary_summary', compact('summary_records', 'company', 'projectName', 'login_name'));
    } catch (Exception $ex) {

      return "Operation Failed, System Exception  " . $ex->getMessage();
    }
  }

  // 8.4 only single month all project salary and deduction summmary
  private function processProjectwiseAMonthlySalaryAndDeductionSummaryReport($project_id_list, $from_date, $to_date)
  {


    $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
    $project_list;
    if ($project_id_list == null) {
      $project_id_list = (new ProjectDataService())->getAllActiveProjectIDs();
    }
    $project_list = (new ProjectDataService())->getProjectListByMultipleProjectId($project_id_list);
    $month =  (new HelperController())->getMonthFromDateValue($from_date);
    $year =  (new HelperController())->getYearFromDateValue($from_date);

    $counter = 0;
    foreach ($project_list as $aproject) {
      $summaryRecords = (new SalaryProcessDataService())->getSalarytSheetWiseTotalSalarySummaryByMultipleProjectMonthAndYear([$aproject->proj_id], $month, $year);

      $totalUnPaidSalaryAllIncluded = 0;
      $grossTotalUnPaidSalary = 0;
      $total_iqama_advance = 0;
      $total_other_advance = 0;
      $grossTotalPaidSalary = 0;
      $totalPaidSalaryAllIncluded = 0;
      $total_emp = 0;
      if (count($summaryRecords) == 2) {
        $total_emp = (int) $summaryRecords[0]->total_emp + (int) $summaryRecords[1]->total_emp;

        if ((int)$summaryRecords[0]->status == 0) { // unpaid salary
          $unpaidRecord = $summaryRecords[0];
          $paidRecord = $summaryRecords[1];  // paid salary record
        } else {

          $paidRecord = $summaryRecords[0];  // paid salary
          $unpaidRecord = $summaryRecords[1];
        }
        // dd(1200,$summaryRecords,$paidRecord,$unpaidRecord);
        $totalUnPaidSalaryAllIncluded = $unpaidRecord['total_salary'] + $unpaidRecord['total_saudi_tax'] + $unpaidRecord['total_iqama_adv'] + $unpaidRecord['total_other_adv'] + $unpaidRecord['total_contribution'];
        $grossTotalUnPaidSalary = $unpaidRecord['total_salary'];
        $total_iqama_advance += $paidRecord['total_iqama_adv'] + $unpaidRecord['total_iqama_adv'];
        $total_other_advance += $paidRecord['total_other_adv'] +  $unpaidRecord['total_other_adv'] + $paidRecord['total_saudi_tax'] +  $unpaidRecord['total_saudi_tax'];
        $totalPaidSalaryAllIncluded = $paidRecord['total_salary'] + $paidRecord['total_saudi_tax'] + $paidRecord['total_iqama_adv'] + $paidRecord['total_other_adv'] + $paidRecord['total_contribution'];
        $grossTotalPaidSalary = $paidRecord['total_salary'];
      } else  if (count($summaryRecords) == 1) {

        $total_emp = $summaryRecords[0]->total_emp;
        $total_iqama_advance += $summaryRecords[0]->total_iqama_adv;
        $total_other_advance += $summaryRecords[0]->total_other_adv + $summaryRecords[0]->total_saudi_tax;

        if ($summaryRecords[0]->status == 1) { // paid salary record
          $paidRecord = $summaryRecords[0];
          $totalPaidSalaryAllIncluded = $paidRecord['total_salary'] + $paidRecord['total_saudi_tax'] + $paidRecord['total_iqama_adv'] + $paidRecord['total_other_adv'] + $paidRecord['total_contribution'];
          $grossTotalPaidSalary = $paidRecord['total_salary'];
        } else {
          // uppaid salary record
          $unpaidRecord = $summaryRecords[0];
          $totalUnPaidSalaryAllIncluded = $unpaidRecord['total_salary'] + $unpaidRecord['total_saudi_tax'] + $unpaidRecord['total_iqama_adv'] + $unpaidRecord['total_other_adv'] + $unpaidRecord['total_contribution'];
          $grossTotalUnPaidSalary = $unpaidRecord['total_salary'];
        }
      }
      $aproject->total_emp = $total_emp;
      $aproject->total_iqama_advance = round($total_iqama_advance);
      $aproject->total_other_advance = round($total_other_advance);
      $aproject->unpaid_gross_salary = round($grossTotalUnPaidSalary);
      $aproject->paid_gross_salary = round($grossTotalPaidSalary);
      $aproject->total_salary = round($totalUnPaidSalaryAllIncluded + $totalPaidSalaryAllIncluded);
      $aproject->gross_total_salary = round($grossTotalPaidSalary + $grossTotalUnPaidSalary);

      $project_list[$counter++] = $aproject;
    }

    $month_name = (new HelperController())->getMonthName($month);
    $login_name = Auth::user()->name;
    return view('admin.salary-generate.projectwise.salarysheet_single_month_salary_summary', compact('project_list', 'company', 'month_name', 'year', 'login_name'));
  }


  // 8.5 Yearly Salary Statement Month by Month Salary Summary Report
  private function processYearlySalaryStatementMonthByMonthSalarySummaryReport(Request $request)
  {

    //  dd($request->all());

    $project_list = $request->project_id_list;
    if ($request->has('project_id_list') == false) {

      $project_list = (new ProjectDataService())->getAllActiveInactiveProjectIDs();
    }
    $project_list = (new ProjectDataService())->getActiveInactiveAllProjectsForAnnualSalaryReportByMultipleProjectId($project_list);
    $year =  (new HelperController())->getYearFromDateValue($request->from_date);
    //  (new HelperController())->getMonthsInRangeOfDate($request->from_date,$request->to_date); //[0]["year"]

    $main_counter = 0;
    $final_records = array();
    foreach ($project_list as $pd) {

      $counter = 0;
      $salary_record = array_fill(0, 12, 0);
      $is_data_found = false;
      $records = (new SalaryProcessDataService())->calculateAProjectFromJanToDecAllIncludedSalaryYearlyStatment($pd->proj_id, $year);

      foreach ($records as $r) {

        if ($r->all_included_total_amount > 0) {
          $m = (int)$r->slh_month;
          $salary_record[$m - 1] = $r->all_included_total_amount;
          $is_data_found = true;
        }
      }
      $pd->salary_record = $salary_record;
      if ($is_data_found) {
        $final_records[$main_counter++] = $pd;
      }
    }

    //  dd($project_list);

    if ($request->report_format == 1) {
      // show as print preview
      $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
      $login_name = Auth::user()->name;
      return view('admin.salary-generate.projectwise.yearly_salary_statement', compact('login_name', 'final_records', 'year', 'company'));
    } else if ($request->report_format == 2) {
      // download as excel format
      return Excel::download(new SalaryReport1ExcelExport($project_list, $my_list), 'salary_report1.xlsx');
    }
  }

  // 8.6  from custom month to month project wise month by month salary summary report
  public function processProjectwiseMonthByMonthSalarySummaryReport(Request $request)
  {

    //  dd($request->all());
    $project_list = $request->proj_id;
    if ($request->has('proj_id') == false) {
      $project_list = (new ProjectDataService())->getAllActiveInactiveProjectIDs();
    }
    $project_list = (new ProjectDataService())->getActiveInactiveAllProjectsForAnnualSalaryReportByMultipleProjectId($project_list);
    $my_list =  (new HelperController())->getMonthsInRangeOfDate($request->from_date, $request->to_date);

    $main_counter = 0;
    $final_result = array();
    foreach ($project_list as $pd) {

      $counter = 0;
      $project_salary = array();
      $is_data_found = false;
      foreach ($my_list as $my) {

        $total_slh_record = (new SalaryProcessDataService())->calculateAProjectAllIncludedSalaryTotalAmountForAMonthAndYear(
          $pd->proj_id,
          $my["month"],
          $my["year"]
        );
        $total_deduction_amount = $total_slh_record->total_saudi_tax + $total_slh_record->total_iqama_adv + $total_slh_record->total_other_adv + $total_slh_record->slh_food_deduction;

        if ($total_slh_record->total_salary +  $total_deduction_amount > 0) {
          $is_data_found = true;
        }

        $project_salary[$counter++] = Round(($total_slh_record->total_salary +  $total_deduction_amount), 2);
      }

      if ($is_data_found) {
        $pd->salary_record = $project_salary;
        $final_result[$main_counter++] = $pd;
      }
    }

    $counter = 0;
    foreach ($my_list as $my) {
      $my['month'] = (new HelperController())->getMonthName($my["month"]);
      $my_list[$counter++] = $my;
    }

    $project_list = $final_result;

    if ($request->report_format == 1) {
      // show as print preview
      $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
      $login_name = Auth::user()->name;
      return view('admin.salary-generate.projectwise.all_project_month_by_month_report', compact('login_name', 'project_list', 'my_list', 'company'));
    } else if ($request->report_format == 2) {
      // download as excel format
      return Excel::download(new SalaryReport1ExcelExport($project_list, $my_list), 'salary_report1.xlsx');
    }
  }



  /* =================== 9. Project wise Basic Empl and Hourly Emp Salary Summary =================== */
  public function projectwiseBasicAndHourlyEmployeeSalarySummary(Request $request)
  {



    if ($request->report_type == 1) {
      // Monthly Salary Statement
      return $this->processProjectwiseBasicAndHourlyEmployeeSalarySummary($request);
    } else if ($request->report_type == 2) {
      return $this->processProjectBaseBasicAndHourlySalaryAndNoOfEmployeeSummary($request);
    } else if ($request->report_type == 3) {
      return $this->processMultipleMonthProjectwiseBasicAndHourlyEmployeeSalarySummary($request);
    } else if ($request->report_type == 4) {
      return $this->processProjectBaseBasicAndHourlyAndStaffSalarySummaryrReport($request);
    } else if ($request->report_type == 5) {
      return $this->processProjectBaseBasicAndHourlyAndStaffSalarySummaryrWithOutCateringServiceReport($request);
    } else if ($request->report_type == 6) {
      return $this->processAProjectActualSalaryExpenseReportOfDirectAndIndirectEmployee($request);
    } else if ($request->report_type == 7) {
      // monthly only unpaid salary summary report
      return $this->processProjectwiseBasicAndHourlyEmployeeOnlyUnpaidSalarySummary($request);
    } else if ($request->report_type == 8) {
      return $this->processAsloobAndSubconMultipleMonthSalarySummary($request);
    }
  }



  // 9.1
  public function processProjectwiseBasicAndHourlyEmployeeSalarySummary(Request $request)
  {


    if (! $request->has('proj_id')) {
      $request->proj_id = (new ProjectDataService())->getAllActiveProjectIDs();
    }

    $project_list = (new ProjectDataService())->getProjectListByMultipleProjectId($request->proj_id);
    $month =  (new HelperController())->getMonthFromDateValue($request->from_date);
    $year =  (new HelperController())->getYearFromDateValue($request->from_date);


    $records = array();
    $counter = 0;

    foreach ($project_list as $pd) {
      $basic_hourly_salary = (new SalaryProcessDataService())->getAProjectBasicAndHourlyEmployeeSalarySummaryReportByProjectId(
        $pd->proj_id,
        $month,
        $year
      );

      $project_list[$counter]->basic_emp = $basic_hourly_salary->basic_emp;
      $project_list[$counter]->basic_salary = $basic_hourly_salary->basic_salary;
      $project_list[$counter]->basic_iqama_deduction = $basic_hourly_salary->basic_iqama_deduction;
      $project_list[$counter]->basic_other_deduction = $basic_hourly_salary->basic_other_deduction;
      $project_list[$counter]->all_incl_total_basic_salary = $basic_hourly_salary->all_incl_total_basic_salary;
      $project_list[$counter]->basic_hours = $basic_hourly_salary->basic_hours;

      $project_list[$counter]->hourly_emp =  $basic_hourly_salary->hourly_emp;
      $project_list[$counter]->hourly_salary = $basic_hourly_salary->hourly_salary;
      $project_list[$counter]->hourly_iqama_deduction = $basic_hourly_salary->hourly_iqama_deduction;
      $project_list[$counter]->hourly_other_deduction = $basic_hourly_salary->hourly_other_deduction;
      $project_list[$counter]->all_incl_total_hourly_salary = $basic_hourly_salary->all_incl_total_hourly_salary;
      $project_list[$counter]->hourly_hours = $basic_hourly_salary->hourly_hours;


      $paid_result = (new SalaryProcessDataService())->calculateAProjectTotalPaidUnpaidSalaryInAMonth(
        $pd->proj_id,
        $month,
        $year
      );

      $project_list[$counter]->hourly_paid_emp = $paid_result->hourly_paid_emp;
      $project_list[$counter]->hourly_paid_amount = $paid_result->hourly_paid_amount;
      $project_list[$counter]->basic_paid_emp =  $paid_result->basic_paid_emp;
      $project_list[$counter]->basic_paid_amount = $paid_result->basic_paid_amount;

      $unpaid_result = (new SalaryProcessDataService())->calculateAProjectBasicAndHourlyEmpUnpaidSalarySummaryInAMonth(
        $pd->proj_id,
        $month,
        $year
      );

      $project_list[$counter]->hourly_unpaid_emp = $unpaid_result->hourly_unpaid_emp;
      $project_list[$counter]->hourly_unpaid_amount = $unpaid_result->hourly_unpaid_amount;
      $project_list[$counter]->basic_unpaid_emp =  $unpaid_result->basic_unpaid_emp;
      $project_list[$counter]->basic_unpaid_amount = $unpaid_result->basic_unpaid_amount;

      $counter++;
    }
    $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
    $report_title = "Month of " . (new HelperController())->getMonthName($month) . ", " . $year;
    $login_name = Auth::user()->name;
    return view('admin.salary-generate.projectwise.projec_paidunpaid_basichourly_salary_summary', compact('project_list', 'company', 'report_title', 'login_name'));
  }



  // 9.1.2   monthly only unpaid salary summary report
  public function processProjectwiseBasicAndHourlyEmployeeOnlyUnpaidSalarySummary(Request $request)
  {



    if (! $request->has('proj_id')) {
      $request->proj_id = (new ProjectDataService())->getAllActiveProjectIDs();
    }
    $project_list = (new ProjectDataService())->getProjectListByMultipleProjectId($request->proj_id);
    $month =  (new HelperController())->getMonthFromDateValue($request->from_date);
    $year =  (new HelperController())->getYearFromDateValue($request->from_date);


    $records = array();
    $counter = 0;

    foreach ($project_list as $pd) {
      $basic_hourly_salary = (new SalaryProcessDataService())->getAProjectBasicAndHourlyEmployeeSalarySummaryReportByProjectId(
        $pd->proj_id,
        $month,
        $year
      );

      $project_list[$counter]->basic_emp = $basic_hourly_salary->basic_emp;
      $project_list[$counter]->basic_salary = $basic_hourly_salary->basic_salary;
      $project_list[$counter]->basic_iqama_deduction = $basic_hourly_salary->basic_iqama_deduction;
      $project_list[$counter]->basic_other_deduction = $basic_hourly_salary->basic_other_deduction;
      $project_list[$counter]->all_incl_total_basic_salary = $basic_hourly_salary->all_incl_total_basic_salary;
      $project_list[$counter]->basic_hours = $basic_hourly_salary->basic_hours;

      $project_list[$counter]->hourly_emp =  $basic_hourly_salary->hourly_emp;
      $project_list[$counter]->hourly_salary = $basic_hourly_salary->hourly_salary;
      $project_list[$counter]->hourly_iqama_deduction = $basic_hourly_salary->hourly_iqama_deduction;
      $project_list[$counter]->hourly_other_deduction = $basic_hourly_salary->hourly_other_deduction;
      $project_list[$counter]->all_incl_total_hourly_salary = $basic_hourly_salary->all_incl_total_hourly_salary;
      $project_list[$counter]->hourly_hours = $basic_hourly_salary->hourly_hours;


      $paid_result = (new SalaryProcessDataService())->calculateAProjectTotalPaidUnpaidSalaryInAMonth(
        $pd->proj_id,
        $month,
        $year
      );

      $project_list[$counter]->hourly_paid_emp = $paid_result->hourly_paid_emp;
      $project_list[$counter]->hourly_paid_amount = $paid_result->hourly_paid_amount;
      $project_list[$counter]->basic_paid_emp =  $paid_result->basic_paid_emp;
      $project_list[$counter]->basic_paid_amount = $paid_result->basic_paid_amount;

      $unpaid_result = (new SalaryProcessDataService())->calculateAProjectBasicAndHourlyEmpUnpaidSalarySummaryInAMonth(
        $pd->proj_id,
        $month,
        $year
      );

      $project_list[$counter]->hourly_unpaid_emp = $unpaid_result->hourly_unpaid_emp;
      $project_list[$counter]->hourly_unpaid_amount = $unpaid_result->hourly_unpaid_amount;
      $project_list[$counter]->basic_unpaid_emp =  $unpaid_result->basic_unpaid_emp;
      $project_list[$counter]->basic_unpaid_amount = $unpaid_result->basic_unpaid_amount;

      $counter++;
    }

    $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
    $report_title = "Month of " . (new HelperController())->getMonthName($month) . ", " . $year;
    $login_name = Auth::user()->name;
    return view('admin.salary-generate.projectwise.projects_montly_only_unpaid_salary_summary', compact('login_name', 'project_list', 'company', 'report_title'));
  }


  // 9.2
  public function processProjectBaseBasicAndHourlySalaryAndNoOfEmployeeSummary($request)
  {

    try {

      $proj_id = $request->proj_id;
      if ($request->has('proj_id') == null) {
        $proj_id = (new ProjectDataService())->getAllActiveProjectIDs();
      }

      $from_date = $request->from_date;
      $to_date = $request->to_date;
      $monthwithYears =  (new HelperController())->getMonthsInRangeOfDate($from_date, $to_date);

      $records = array();
      $counter = 0;
      foreach ($proj_id as $pid) {
        $dbrecord = (new SalaryProcessDataService())->getProjectBaseBasicAndHourlyEmployeeSalarySummaryProject(
          $pid,
          $monthwithYears[0]['month'],
          $monthwithYears[0]['month'],
          $monthwithYears[0]['year'],
          $monthwithYears[0]['year']
        );

        $arecord = array();
        if (count($dbrecord) == 2) {

          $abc = (array)  $dbrecord[0];
          $abc['total_basic_salary'] = round($abc['total_salary']);
          $abc['total_basic_emp'] =  round($abc['total_emp']);

          $ab = (array) $dbrecord[1];
          $abc['total_hourly_salary'] = round($ab['total_salary']);
          $abc['total_hourly_emp'] =  round($ab['total_emp']);
          $records[$counter++] = $abc;
        } elseif (count($dbrecord) == 1) {

          $abc = (array)  $dbrecord[0];
          //dd($abc);

          $abc['total_hourly_salary'] = 0;
          $abc['total_basic_salary'] = 0;
          $abc['total_basic_emp'] =  0;
          $abc['total_hourly_emp'] = 0;

          if ($abc['hourly_employee'] == 1) {
            $abc['total_hourly_salary'] = round($abc['total_salary']);
            $abc['total_hourly_emp'] =  round($abc['total_emp']);
          } else {
            $abc['total_basic_salary'] = round($abc['total_salary']);
            $abc['total_basic_emp'] =  round($abc['total_emp']);
          }
          $records[$counter++] = $abc;
        }
      }

      $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
      $report_title = "Month of " . (new HelperController())->getMonthName($monthwithYears[0]['month']) . ", " . $monthwithYears[0]['year'];
      $login_name = Auth::user()->name;
      return view('admin.salary-generate.projectwise.project_basic_hourly_emp_sal_summary', compact('records', 'company', 'report_title', 'login_name'));
    } catch (Exception $ex) {
      return "Operation Failed, System Exception  " . $ex->getMessage();
    }
  }


  // 9.3
  public function processMultipleMonthProjectwiseBasicAndHourlyEmployeeSalarySummary(Request $request)
  {

    // dd($request->all());

    if (! $request->has('proj_id')) {
      $request->proj_id = (new ProjectDataService())->getAllActiveProjectIDs();
    }

    $project_list = (new ProjectDataService())->getProjectListByMultipleProjectId($request->proj_id);
    // dd($project_list);
    $month =  (new HelperController())->getMonthFromDateValue($request->from_date);
    $year =  (new HelperController())->getYearFromDateValue($request->from_date);

    $last_month =  (new HelperController())->getMonthFromDateValue($request->to_date);
    $last_year =  (new HelperController())->getYearFromDateValue($request->to_date);

    if ($last_month < $month && $last_year < $year) {
      return "Invalid Month Range, Please select valid date range";
    }
    $month_counter = 0;
    $final_result = array();
    for ($m = $month; $m <= $last_month; $m++) {
      $my_list[$month_counter++] = array(
        'month' => $m,
        'year' => $year,
      );
    }


    $records = array();
    $counter = 0;

    foreach ($project_list as $pd) {
      $pcounter = 0;
      $presult = array();
      for ($m = $month; $m <= $last_month; $m++) {

        $basic_hourly_salary = (new SalaryProcessDataService())->getAProjectBasicAndHourlyEmployeeSalarySummaryReportByProjectId(
          $pd->proj_id,
          $m,
          $year
        );
        $presult[$pcounter++] = $basic_hourly_salary;


        // $project_list[$counter]->basic_emp = $basic_hourly_salary->basic_emp;
        // $project_list[$counter]->basic_salary = $basic_hourly_salary->basic_salary;
        // $project_list[$counter]->basic_iqama_deduction = $basic_hourly_salary->basic_iqama_deduction;
        // $project_list[$counter]->basic_other_deduction = $basic_hourly_salary->basic_other_deduction;
        // $project_list[$counter]->all_incl_total_basic_salary = $basic_hourly_salary->all_incl_total_basic_salary;
        // $project_list[$counter]->basic_hours = $basic_hourly_salary->basic_hours;

        // $project_list[$counter]->hourly_emp =  $basic_hourly_salary->hourly_emp;
        // $project_list[$counter]->hourly_salary = $basic_hourly_salary->hourly_salary;
        // $project_list[$counter]->hourly_iqama_deduction = $basic_hourly_salary->hourly_iqama_deduction;
        // $project_list[$counter]->hourly_other_deduction = $basic_hourly_salary->hourly_other_deduction;
        // $project_list[$counter]->all_incl_total_hourly_salary = $basic_hourly_salary->all_incl_total_hourly_salary;
        // $project_list[$counter]->hourly_hours = $basic_hourly_salary->hourly_hours;
      }
      $pd->salary_records = $presult;
      $project_list[$counter++] = $pd;
    }



    // dd($project_list);

    $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
    $report_title = "Month of " . (new HelperController())->getMonthName($month) . ", " . $year;
    $login_name = Auth::user()->name;
    return view('admin.salary-generate.projectwise.multimonth_projectdetails_salary_summary', compact('project_list', 'my_list', 'company', 'report_title', 'login_name'));
  }




  // 9.4 Project base Office Staff and Direct Employees Hours and Salary Summary
  public function processProjectBaseBasicAndHourlyAndStaffSalarySummaryrReport($request)
  {

    $project_id_list = $request->proj_id;
    if ($project_id_list == null) {
      $project_id_list = (new ProjectDataService())->getAllActiveProjectIDs();
    }
    $project_list = (new ProjectDataService())->getProjectListByMultipleProjectId($project_id_list);

    $from_date = $request->from_date;
    $month =  (new HelperController())->getMonthFromDateValue($from_date);
    $year =  (new HelperController())->getYearFromDateValue($from_date);
    $counter = 0;

    foreach ($project_list as $ap) {

      $basic_hourly_record = (new SalaryProcessDataService())->getAProjectDirectBasicAndHourlyEmployeesSalarySummaryReport((int)$ap->proj_id, $month, $year);

      if (count($basic_hourly_record) == 1) {

        $abc = (array)  $basic_hourly_record[0];
        if ($abc["hourly_employee"] == null) {
          $ap->total_basic_emp = round($abc['total_emp']);
          $ap->total_basic_salary = round($abc['total_salary']);
          $ap->total_basic_hours =  round($abc['total_hours']);
        } else {
          $ap->total_hourly_emp = round($abc['total_emp']);
          $ap->total_hourly_salary = round($abc['total_salary']);
          $ap->total_hourly_hours =  round($abc['total_hours']);
        }
      } elseif (count($basic_hourly_record) == 2) {

        //   $abc = (array)  $basic_hourly_record[0];
        //   $ap->total_hourly_emp = round($abc['total_emp']);
        //   $ap->total_hourly_salary = round($abc['total_salary']);
        //   $ap->total_hourly_hours =  round($abc['total_hours']);

        //   $abc = (array)  $basic_hourly_record[1];

        //   $ap->total_basic_emp = round($abc['total_emp']);
        //   $ap->total_basic_salary = round($abc['total_salary']);
        //   $ap->total_basic_hours =  round($abc['total_hours']);

        $abc = (array)  $basic_hourly_record[0];
        if ($abc["hourly_employee"] == null) {

          $ap->total_basic_emp = round($abc['total_emp']);
          $ap->total_basic_salary = round($abc['total_salary']);
          $ap->total_basic_hours =  round($abc['total_hours']);

          $abc = (array)  $basic_hourly_record[1];
          $ap->total_hourly_emp = round($abc['total_emp']);
          $ap->total_hourly_salary = round($abc['total_salary']);
          $ap->total_hourly_hours =  round($abc['total_hours']);
        } else {

          $abc = (array)  $basic_hourly_record[0];
          $ap->total_hourly_emp = round($abc['total_emp']);
          $ap->total_hourly_salary = round($abc['total_salary']);
          $ap->total_hourly_hours =  round($abc['total_hours']);

          $abc = (array)  $basic_hourly_record[1];
          $ap->total_basic_emp = round($abc['total_emp']);
          $ap->total_basic_salary = round($abc['total_salary']);
          $ap->total_basic_hours =  round($abc['total_hours']);
        }
      }

      $staff_record = (new SalaryProcessDataService())->getAProjectInirectEmployeesSalarySummaryReport((int)$ap->proj_id, $month, $year);
      if (count($staff_record) == 1) {

        $abc = (array)  $staff_record[0];
        $ap->total_indirect_emp = round($abc['total_emp']);
        $ap->total_indirect_salary = round($abc['total_salary']);
        $ap->total_indirect_hours =  round($abc['total_hours']);
      }


      $project_list[$counter++] = $ap;
    }

    $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
    $login_name = Auth::user()->name;
    $report_title = "Salary Month of " . (new HelperController())->getMonthName($month) . ", " . $year;
    return view('admin.salary-generate.projectwise.office_staff_and_direct_emp_salary_summary', compact('project_list', 'company', 'report_title', 'login_name'));
  }


  // 9.5 Project base Office Staff and Direct Employees Hours and Salary Summary
  public function processProjectBaseBasicAndHourlyAndStaffSalarySummaryrWithOutCateringServiceReport($request)
  {

    $project_id_list = $request->proj_id;
    if ($project_id_list == null) {
      $project_id_list = (new ProjectDataService())->getAllActiveProjectIDs();
    }
    $project_list = (new ProjectDataService())->getProjectListByMultipleProjectId($project_id_list);

    $from_date = $request->from_date;
    $month =  (new HelperController())->getMonthFromDateValue($from_date);
    $year =  (new HelperController())->getYearFromDateValue($from_date);
    $counter = 0;

    foreach ($project_list as $ap) {

      $basic_hourly_record = (new SalaryProcessDataService())->getAProjectDirectBasicAndHourlyEmployeesSalarySummaryWithOutCateringServiceReport((int)$ap->proj_id, $month, $year);
      if (count($basic_hourly_record) == 1) {

        $abc = (array)  $basic_hourly_record[0];
        if ($abc["hourly_employee"] == null) {
          $ap->total_basic_emp = round($abc['total_emp']);
          $ap->total_basic_salary = round($abc['total_salary']);
          $ap->total_basic_hours =  round($abc['total_hours']);
        } else {
          $ap->total_hourly_emp = round($abc['total_emp']);
          $ap->total_hourly_salary = round($abc['total_salary']);
          $ap->total_hourly_hours =  round($abc['total_hours']);
        }
      } elseif (count($basic_hourly_record) == 2) {
        $abc = (array)  $basic_hourly_record[0];
        if ($abc["hourly_employee"] == null) {
          $ap->total_basic_emp = round($abc['total_emp']);
          $ap->total_basic_salary = round($abc['total_salary']);
          $ap->total_basic_hours =  round($abc['total_hours']);

          $abc = (array)  $basic_hourly_record[1];
          $ap->total_hourly_emp = round($abc['total_emp']);
          $ap->total_hourly_salary = round($abc['total_salary']);
          $ap->total_hourly_hours =  round($abc['total_hours']);
        } else {

          $abc = (array)  $basic_hourly_record[0];
          $ap->total_hourly_emp = round($abc['total_emp']);
          $ap->total_hourly_salary = round($abc['total_salary']);
          $ap->total_hourly_hours =  round($abc['total_hours']);

          $abc = (array)  $basic_hourly_record[1];
          $ap->total_basic_emp = round($abc['total_emp']);
          $ap->total_basic_salary = round($abc['total_salary']);
          $ap->total_basic_hours =  round($abc['total_hours']);
        }
      }

      $staff_record = (new SalaryProcessDataService())->getAProjectInirectEmployeesSalarySummaryWithOutCateringServiceReport((int)$ap->proj_id, $month, $year);
      if (count($staff_record) == 1) {

        $abc = (array)  $staff_record[0];
        $ap->total_indirect_emp = round($abc['total_emp']);
        $ap->total_indirect_salary = round($abc['total_salary']);
        $ap->total_indirect_hours =  round($abc['total_hours']);
      }
      $project_list[$counter++] = $ap;
    }

    $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
    $report_title = "Salary Month of " . (new HelperController())->getMonthName($month) . ", " . $year;
    $login_name = Auth::user()->name;
    return view('admin.salary-generate.projectwise.staff_direct_emp_salary_summary_without_catering', compact('project_list', 'company', 'report_title', 'login_name'));
  }


  // 9.6 Project base Office Staff and Direct Employees Hours and Salary Actual Expense for a Project
  public function processAProjectActualSalaryExpenseReportOfDirectAndIndirectEmployee($request)
  {

    $project_id_list = $request->proj_id;
    if ($project_id_list == null) {
      $project_id_list = (new ProjectDataService())->getAllActiveProjectIDs();
    }
    $project_list = (new ProjectDataService())->getProjectListByMultipleProjectId($project_id_list);

    $from_date = $request->from_date;
    $month =  (new HelperController())->getMonthFromDateValue($from_date);
    $year =  (new HelperController())->getYearFromDateValue($from_date);
    $counter = 0;

    foreach ($project_list as $ap) {

      $basic_hourly_record = (new SalaryProcessDataService())->getAProjectActualSalaryExpenseOfDirectEmployeesSalarySummaryReport((int)$ap->proj_id, $month, $year);
      if (count($basic_hourly_record) == 1) {

        $abc = (array)  $basic_hourly_record[0];
        if ($abc["hourly_employee"] == null) {
          $ap->total_basic_emp = round($abc['total_emp']);
          $ap->total_basic_salary = round($abc['total_salary']);
          $ap->total_basic_hours =  round($abc['total_hours']);
        } else {
          $ap->total_hourly_emp = round($abc['total_emp']);
          $ap->total_hourly_salary = round($abc['total_salary']);
          $ap->total_hourly_hours =  round($abc['total_hours']);
        }
      } elseif (count($basic_hourly_record) == 2) {

        $abc = (array)  $basic_hourly_record[0];
        if ($abc["hourly_employee"] == null) {

          $ap->total_basic_emp = round($abc['total_emp']);
          $ap->total_basic_salary = round($abc['total_salary']);
          $ap->total_basic_hours =  round($abc['total_hours']);

          $abc = (array)  $basic_hourly_record[1];
          $ap->total_hourly_emp = round($abc['total_emp']);
          $ap->total_hourly_salary = round($abc['total_salary']);
          $ap->total_hourly_hours =  round($abc['total_hours']);
        } else {

          $abc = (array)  $basic_hourly_record[0];
          $ap->total_hourly_emp = round($abc['total_emp']);
          $ap->total_hourly_salary = round($abc['total_salary']);
          $ap->total_hourly_hours =  round($abc['total_hours']);

          $abc = (array)  $basic_hourly_record[1];
          $ap->total_basic_emp = round($abc['total_emp']);
          $ap->total_basic_salary = round($abc['total_salary']);
          $ap->total_basic_hours =  round($abc['total_hours']);
        }
      }
      $staff_record = (new SalaryProcessDataService())->getAProjectActualSalaryExpenseOfInDirectEmployeesSalarySummaryReport((int)$ap->proj_id, $month, $year);
      if (count($staff_record) == 1) {

        $abc = (array)  $staff_record[0];
        $ap->total_indirect_emp = round($abc['total_emp']);
        $ap->total_indirect_salary = round($abc['total_salary']);
        $ap->total_indirect_hours =  round($abc['total_hours']);
      }
      $project_list[$counter++] = $ap;
    }

    $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
    $report_title = "Salary Month of " . (new HelperController())->getMonthName($month) . ", " . $year;
    $login_name = Auth::user()->name;
    return view('admin.salary-generate.projectwise.aproject_direct_indirect_actual_salary_expense', compact('login_name', 'project_list', 'company', 'report_title'));
  }

    //9.8
  public function processAsloobAndSubconMultipleMonthSalarySummary(Request $request)
  {

        //  dd($request->all());
       
       $project_ids = $request->proj_ids;
       $subcon_sponsor_ids = $request->subcon_sponsor_ids;
       
        if (! $request->has('proj_ids')) {
            $project_ids = (new ProjectDataService())->getAllActiveProjectIDs();
        }
        
        $project_ids = (new ProjectDataService())->getAllActiveProjectIDs();
        if (! $request->has('subcon_sponsor_ids')) {
            $subcon_sponsor_ids = (new EmployeeRelatedDataService())->getAllActiveSponsorIdAsArrayOfABranchOfficeBySponsorTypes(1,['Subcon']);
        }
 
        $asloob_month_year =  (new HelperController())->getMonthsInRangeOfDate($request->asloob_from_date , $request->asloob_to_date);
        $subcon_month_year =  (new HelperController())->getMonthsInRangeOfDate($request->subcon_from_date, $request->subcon_to_date);

        $asloob_sponsor_ids = (new EmployeeRelatedDataService())->getAllActiveSponsorIdAsArrayOfABranchOfficeBySponsorTypes(1,['Asloob']);
      
        $month_counter = 0;
        $asloob_result = array();
        $subcon_result = array();
         
        $records = array();
        $counter = 0;

        foreach ($asloob_month_year as $my) {
            
            $salary = (new SalaryProcessDataService())->getAMonthSalarySummaryUsingMultipleSponsorIdForAsloobAndSubconSalarySummaryReport($asloob_sponsor_ids,$project_ids,$my['month'],$my['year']  );
            $salary->month = $my['month'];
            $salary->year = $my['year'];
            $asloob_result[$counter++] = $salary;          
                          
        }
        $counter = 0;
        foreach ($subcon_month_year as $my) {           
            
           $subsalary = (new SalaryProcessDataService())->getAMonthSalarySummaryUsingMultipleSponsorIdForAsloobAndSubconSalarySummaryReport($subcon_sponsor_ids,$project_ids,$my['month'],$my['year']  );
           $subsalary->month = $my['month'];
           $subsalary->year = $my['year'];
           $subcon_result[$counter++] = $subsalary;            
              
        }
        // dd($subcon_result, $asloob_result);

        $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
        $report_title = "Mutiple Month Asloob And Subcon Salary Summary";
        $login_name = Auth::user()->name;
        return view('admin.salary-generate.projectwise.multimonth_asloob_subcon_salary_summary', compact('asloob_result', 'subcon_result', 'company', 'report_title', 'login_name'));
  }


  /* =================== 10. Sponsor Salary Summary   report =================== */
  public function processSponsorSalarySummaryReport(Request $request)
  {

    //  return $this->processASponsorSingleMonthAsPerSalarySheetProjectBaseSummaryReport($request);


    if ($request->report_type == 1) {
      return $this->processASponsorYearlyMonthByMonthSalarySummaryReport($request);
    } else if ($request->report_type == 2) {
      // Single Month Project Details from Multiproject work records
      return $this->processASponsorSingleMonthSalaryProjectBaseSummaryReport($request);
    } else if ($request->report_type == 3) {
      // Single Month As Per salary sheet report
      return $this->processASponsorSingleMonthAsPerSalarySheetProjectBaseSummaryReport($request);
    }
  }

  //report 10.1 a single sponsor month by month summary
  public function processASponsorYearlyMonthByMonthSalarySummaryReport(Request $request)
  {

    $sponsor_id = $request->sponsor_id;
    $from_date = $request->from_date;
    $to_date = $request->to_date;
    $monthwithYears =  (new HelperController())->getMonthsInRangeOfDate($from_date, $to_date);
    $summary_records = array();
    $report_title = "";
    $counter = 0;
    foreach ($monthwithYears as $my) {
      // $records = (new SalaryProcessDataService())->getASponsorYearlySalarySummaryMonthByMonthReport($sponsor_id,$my['month'],  $my['year']);

      if ($request->has('proj_id')) {
        $records = (new SalaryProcessDataService())->getASponsorMonthByMonthSalarySummaryInAProjectByMonthYearReport($sponsor_id, $request->proj_id[0], $my['month'],  $my['year']);
        $report_title = (new ProjectDataService())->getProjectNameByProjectId($request->proj_id[0]);
      } else {
        $records = (new SalaryProcessDataService())->getASponsorYearlySalarySummaryMonthByMonthReport($sponsor_id, $my['month'],  $my['year']);
      }

      if (count($records) > 0) {
        $summary_records[$counter++] = $records[0];
      }
    }
    $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
    $sponsor_name =   (new EmployeeRelatedDataService())->getASponserNameBySponerId($sponsor_id);

    $login_name = Auth::user()->name;
    return view('admin.salary-generate.spnserwise.asponsor_month_bymonth_salary_summary', compact('summary_records', 'company', 'sponsor_name', 'report_title', 'login_name'));
  }

  // 10.2 sponsor single month with project details summary
  public function processASponsorSingleMonthSalaryProjectBaseSummaryReport(Request $request)
  {

    $sponsor_id = $request->sponsor_id;
    $month =  (new HelperController())->getMonthFromDateValue($request->from_date);
    $year =  (new HelperController())->getYearFromDateValue($request->from_date);
    $select_project_ids = $request->proj_id;

    if ($select_project_ids == null) {
      $select_project_ids = (new ProjectDataService())->getAllActiveProjectIDs();
    }
    $summary_records1 = (new SalaryProcessDataService())->getASponsorSingleMonthSalarySummaryProjecBaseDetailsReport($sponsor_id, $month,  $year);
    $summary_records = array();
    $counter = 0;
    foreach ($summary_records1 as $arecord) {
      if (in_array($arecord->proj_id, $select_project_ids)) {
        $summary_records[$counter++] = $arecord;
      }
    }

    $login_name = Auth::user()->name;
    $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
    $sponsor_name =   (new EmployeeRelatedDataService())->getASponserNameBySponerId($sponsor_id);
    $report_title[0] = "Month of " . (new HelperController())->getMonthName($month) . ", " . $year;
    $report_title[1] = "Monthly Salary Summary As Per Multiple Project Working Records";

    return view('admin.salary-generate.spnserwise.asponsor_single_month_salary_project_details_report', compact('summary_records', 'company', 'sponsor_name', 'report_title', 'login_name'));
  }

  // 10.3 Single Month As Per salary sheet report
  public function processASponsorSingleMonthAsPerSalarySheetProjectBaseSummaryReport(Request $request)
  {

    $sponsor_id = $request->sponsor_id;
    $month =  (new HelperController())->getMonthFromDateValue($request->from_date);
    $year =  (new HelperController())->getYearFromDateValue($request->from_date);
    $select_project_ids = $request->proj_id;

    if ($select_project_ids == null) {
      $select_project_ids = (new ProjectDataService())->getAllActiveProjectIDOfABranchOfficeAsArray(1);
    }
    $summary_records1 = (new SalaryProcessDataService())->processASponsorSingleMonthSalaryAsPerSalarySheetProjectBaseReport($sponsor_id, $month,  $year);
    $summary_records = array();
    $counter = 0;
    foreach ($summary_records1 as $arecord) {
      if (in_array($arecord->proj_id, $select_project_ids)) {
        $summary_records[$counter++] = $arecord;
      }
    }

    $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
    $sponsor_name =   (new EmployeeRelatedDataService())->getASponserNameBySponerId($sponsor_id);
    $report_title[0] = "Month of " . (new HelperController())->getMonthName($month) . ", " . $year;
    $report_title[1] = "Monthly Salary Summary with Project As Per Salary Sheet";
    $login_name = Auth::user()->name;
    return view('admin.salary-generate.spnserwise.asponsor_single_month_salary_project_details_report', compact('login_name', 'summary_records', 'company', 'sponsor_name', 'report_title'));
  }

  /* =================== 11. Multiple Emplyees ID Salary   report =================== */
  public function multipleEmployeeIdBaseSalaryProcess(Request $request)
  {
    try {


      $month = $request->month;
      $salaryYear = $request->year;
      $salaryStatus =  $request->salary_status; // 0 = Unpaid 1= Paid

      $allEmplId = explode(",", $request->multiple_emp_Id);
      $allEmplId = array_unique($allEmplId); // remove multiple same empl ID
      $salaryReport = (new SalaryProcessDataService())->getMultipleEmployeeIdBaseSalaryHistory($allEmplId, $month, $salaryYear, $salaryStatus, Auth::user()->branch_office_id);


      $employeeType = "";
      $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
      $project = '';
      $monthName = (new HelperController())->getMonthName($month);
      $reportLeftTitle = ['Salary Month of ' . $monthName . ', ' . $salaryYear, '', ''];
      $login_name = Auth::user()->name;
      return view('admin.salary-generate.all-employee.all-employees-salary', compact('login_name', 'salaryReport', 'month', 'monthName', 'salaryYear', 'company'));
    } catch (Exception $ex) {
      return "System Operation Failed " . $ex;
    }
  }



  //12 Salary Paid Bank Employees Salary Report
  public function processSalaryPaidByBankEmployeesListSalaryReport(Request $request)
  {

    try {


      $month = $request->month;
      $year = $request->year;
      $project_id_list = $request->project_id_list;
      $project_name = "-";
      if (is_null($project_id_list)) {
        $project_id_list = (new ProjectDataService())->getAllActiveProjectIDOfABranchOfficeAsArray(1);
      } else if (count($project_id_list) == 1) {
        $project_name =  (new ProjectDataService())->getProjectNameByProjectId($project_id_list[0]);
      }


      $month_name = (new HelperController())->getMonthName($month);
      if ($request->report_format == 1) {
        // show as print preview
        $salaryReport = (new SalaryProcessDataService())->salaryPaidByBankEmployeesSalaryReport($project_id_list, $month,  $year, Auth::user()->branch_office_id);
        $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
        $report_left_title = ['WPS-SALARY REPORT', $month_name, $year, $project_name];
        $login_name = Auth::user()->name;

        return view('admin.salary-generate.projectwise.bank_paid_emp_salary_report', compact('login_name', 'salaryReport', 'report_left_title', 'company'));
      } else if ($request->report_format == 2) {
        // download as excel format
        $salary_records = (new SalaryProcessDataService())->EmployeeSalaryPaidByBankReportForSendingToBank($project_id_list, $month,  $year, Auth::user()->branch_office_id);
        //   dd($salary_records);
        return Excel::download(new WPSSalaryExportAsExcel($salary_records), $month_name . '_' . $year . '_Salary_Report.xlsx');
      }
    } catch (Exception $ex) {
      return view("System Operation Failed " . $ex);
    }
  }

  public function processSalaryAsPaidByByDateAndWhoDidItSalaryReport(Request $request)
  {

    try {
      $projectId = $request->proj_id;
      $month = $request->month;
      $year = $request->year;
      $updated_date = $request->updated_date;
      $updated_by_ids = $request->update_by_ids;
      $project_id_list = $request->project_id_list;
      $project_name = "-";
      if (is_null($project_id_list)) {
        $project_id_list = (new ProjectDataService())->getAllActiveProjectIDOfABranchOfficeAsArray(1);
      } else if (count($project_id_list) == 1) {
        $project_name =  (new ProjectDataService())->getProjectNameByProjectId($project_id_list[0]);
      }

      $emp_type_id = $request->emp_type_id;
      $monthName = (new HelperController())->getMonthName($month);
      $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);

      // dd($request->all());
      $project = 'All';
      if ($projectId != null) {
        $project = (new EmployeeRelatedDataService())->getProjectNameByProjectId($projectId);
      }

      $employeeType = 'All';
      $hourly_employee = null;
      $emp_type_ids = []; // default all emp types

      if ($emp_type_id == -1) {  // Basic Salary direct Employee
        $hourly_employee = NULL;
        $emp_type_ids = [1];
        $employeeType = "Direct Employee-Basic Salary";
      } else if ($emp_type_id == 1) { // Hourly salary direct Employee
        $hourly_employee = true;
        $employeeType = "Direct Employee-Hourly";
        $emp_type_ids = [1];
      } else if ($emp_type_id == 2) {   // Indirect Employee
        $employeeType = 'Indirect Employee';
        $hourly_employee = NULL;
        $emp_type_ids = [2];
      } else if ($emp_type_id == 3) {
        $employeeType = "Basic Salary(Indirect & Direct) Employees";
        $hourly_employee = NULL;
        $emp_type_ids = [1, 2];
      }
      //   dd($project_id_list, $month, $year, $hourly_employee, $emp_type_ids,$updated_by_ids,$updated_date);
      //  dd($salaryReport);
      if ($request->report_type == 1) {
        // list show report
        $paid_unpaid_show = true;
        $salaryReport = (new SalaryProcessDataService())->getSalaryStatusAsPaidByDateAndWhoUpdatedEmployeeListReport($project_id_list, $month, $year, $hourly_employee, $emp_type_ids, $updated_by_ids, $updated_date);
        $login_name = Auth::user()->name;
        $salaryYear = $year;
        return view('admin.salary-generate.projectwise.projectwise-salary-report', compact('login_name', 'paid_unpaid_show', 'salaryReport', 'monthName', 'salaryYear', 'company', 'project', 'employeeType'));
      } else if ($request->report_type == 2) {
        // summary report
        $salary_record = (new SalaryProcessDataService())->getSalaryStatusAsPaidByDateAndWhoUpdatedSummaryReport($project_id_list, $month, $year, $hourly_employee, $emp_type_ids, $updated_by_ids, $updated_date);
        //  dd($salaryReport);
        $login_name  = Auth::user()->name;
        return view('admin.salary-generate.salary-paid.salary_paid_by_date_report', compact('login_name', 'salary_record', 'monthName', 'salary_record', 'year', 'company', 'project', 'employeeType', 'updated_date'));
      }
    } catch (Exception $ex) {
      return 'Operation Failed, System Exception: ' . $ex->getMessage();
    }
  }

  /* ==================== 13 Salary Hold Employees Salary Report ==================== */
  public function showEmployeeSalarySheetReportPrintPreviewByReportType(Request $request)
  {
    try {
      if ($request->salary_report_type == 1) {
        // Salary Hold Employee Salary Sheet
        return $this->processSalaryHoldEmployeeSalaryReport($request);
      } elseif ($request->salary_report_type == 2) {
        // Office Staff Employee Salary Sheet
        return $this->processOfficeStaffEmployeeSalarySheetReport($request);
      }
    } catch (Exception $ex) {
      return "System Data Processsing Error " . $ex;
    }
  }


  // Salary Hold Employee Salary Sheet
  public function processSalaryHoldEmployeeSalaryReport(Request $request)
  {
    try {

      $month = $request->month;
      $year = $request->year;
      $project_ids = $request->project_ids;
      $salary_records = (new SalaryProcessDataService())->getEmployeeSalaryReportThoseAreSalaryHold($project_ids, $month, $year);
      $month_name = (new HelperController())->getMonthName($month);
      $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
      $login_name = Auth::user()->name;
      return view('admin.report.salary.salary_sheet.salary_hold_emp_salary_report', compact('salary_records', 'month_name', 'month', 'year', 'company', 'login_name'));
    } catch (Exception $ex) {
      return "System Data Processsing Error " . $ex;
    }
  }

  // Office Staff Employee Salary Sheet

  public function processOfficeStaffEmployeeSalarySheetReport(Request $request)
  {

    try {

      $month = $request->month;
      $year = $request->year;
      $project_ids = $request->project_ids;
      $salary_records = (new SalaryProcessDataService())->getOfficeStaffEmployeeSalaryReport($project_ids, $month, $year);
      $month_name = (new HelperController())->getMonthName($month);
      $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
      $login_name = Auth::user()->name;
      return view('admin.report.salary.salary_sheet.office_staff_salary_sheet_report', compact('salary_records', 'month_name', 'month', 'year', 'company', 'login_name'));
    } catch (Exception $ex) {
      return "System Data Processsing Error " . $ex;
    }
  }






  /*
  |--------------------------------------------------------------------------
  |  BLADE OPERATION
  |--------------------------------------------------------------------------
  */
  public function index()
  {
    return null;
  }

  public function create()
  {
    $currentMonth = Carbon::now()->format('m');
    $projects = (new ProjectDataService())->getAllActiveProjectListForDropdown(Auth::user()->branch_office_id);
    $all_projects = (new ProjectDataService())->getAllActiveInActiveProjectListForDropdown(Auth::user()->branch_office_id);


    $month = (new CompanyDataService())->getAllMonth();
    $emp_types = (new EmployeeRelatedDataService())->getAllEmployeeType();
    $sponser = (new EmployeeRelatedDataService())->getAllSponserInfoForDropdown(Auth::user()->branch_office_id);
    $sponsor_for_salary_process = (new EmployeeRelatedDataService())->getAllSubcontractorsSponsorsForDropdown(Auth::user()->branch_office_id);

    $allEmployeeStatus = (new HelperController())->getEmployeeStatus();
    $trades = (new EmployeeRelatedDataService())->getEmpAllCategoryInfoForDropdown();
    $designation_heads = (new EmployeeRelatedDataService())->getDesignationHeadRecordsForDropdown();
    return view('admin.salary-generate.salary-proccessing', compact('trades', 'allEmployeeStatus', 'month', 'currentMonth', 'all_projects', 'projects', 'emp_types', 'sponser', 'designation_heads', 'sponsor_for_salary_process'));
  }


  // Employee Pending Salary UI Loading
  public function loadSalaryPendingEmployeeListWithUI()
  {
    $projectlist = (new ProjectDataService())->getAllActiveProjectListForDropdown(Auth::user()->branch_office_id);
    $sponserList = (new EmployeeRelatedDataService())->getAllSponserInfoForDropdown(Auth::user()->branch_office_id);

    return view('admin.salary-generate.salary_pending.pending-salary', compact('projectlist', 'sponserList'));
  }
  // Employee Paid Salary UI Loading
  public function Salarypaid()
  {

    $projectlist = (new ProjectDataService())->getAllActiveProjectListForDropdown(Auth::user()->branch_office_id);
    $sponserList = (new EmployeeRelatedDataService())->getAllSponserInfoForDropdown(Auth::user()->branch_office_id);

    return view('admin.salary-generate.salary-paid.paid-salary', compact('projectlist', 'sponserList'));
  }


//     // Employee Salary sheet UI Loading
//   public function loadSalarySheetUploadUI()
//   {
//     $allSheet =    (new SalaryProcessDataService())->getUploadedSalarySheetInformation();
//     $month = (new CompanyDataService())->getAllMonth();

//     $projectlist = (new ProjectDataService())->getAllActiveProjectListForDropdown(Auth::user()->branch_office_id);
//     $sponserList = (new EmployeeRelatedDataService())->getAllSponserInfoForDropdown(Auth::user()->branch_office_id);

//    // return view('admin.salary-generate.salary_related_file_upload', compact('projectlist', 'sponserList', 'month', 'allSheet'));
//     return view('payroll::pages.Payroll.payslip_upload', compact('projectlist', 'sponserList', 'month', 'allSheet'));



//   }

//   /**
//    * Update Salary Sheet Record
//    */
//   public function updateSalarySheet(Request $request)
//   {
//     try {
//       // Validate input
//       $this->validate($request, [
//         'record_id' => 'required|integer',
//         'no_of_emp' => 'nullable|integer',
//         'month' => 'required|integer|between:1,12',
//         'year' => 'required|integer',
//         'salary_date' => 'required|date',
//         'remarks' => 'nullable|string|max:200'
//       ]);

//       // Get the record
//       $recordId = $request->record_id;
//       $salarySheet = SalarySheetUpload::find($recordId);

//       if (!$salarySheet) {
//         return response()->json([
//           'status' => 404,
//           'message' => 'Salary sheet record not found'
//         ], 404);
//       }

//       // Update the record
//       $salarySheet->update([
//         'no_of_emp' => $request->no_of_emp,
//         'month' => $request->month,
//         'year' => $request->year,
//         'salary_date' => $request->salary_date,
//         'remarks' => $request->remarks
//       ]);

//       return response()->json([
//         'status' => 200,
//         'message' => 'Salary sheet updated successfully',
//         'data' => $salarySheet
//       ]);

//     } catch (\Illuminate\Validation\ValidationException $e) {
//       return response()->json([
//         'status' => 422,
//         'message' => 'Validation error',
//         'errors' => $e->errors()
//       ], 422);

//     } catch (Exception $e) {
//       return response()->json([
//         'status' => 500,
//         'message' => 'Error updating salary sheet: ' . $e->getMessage()
//       ], 500);
//     }
//   }

//   /**
//    * Search Salary Sheets with Filters
//    */
//   public function searchSalarySheets(Request $request)
//   {
//     try {
//       // Build query based on filters
//       $query = SalarySheetUpload::query();

//       // Filter by employee ID if provided
//       if ($request->has('employee_id') && !empty($request->employee_id)) {
//         $query->where('no_of_emp', $request->employee_id);
//       }

//       // Filter by month if provided
//       if ($request->has('month') && !empty($request->month)) {
//         $query->where('month', $request->month);
//       }

//       // Filter by year if provided
//       if ($request->has('year') && !empty($request->year)) {
//         $query->where('year', $request->year);
//       }

//       // Filter by salary date if provided
//       if ($request->has('date') && !empty($request->date)) {
//         $query->where('salary_date', $request->date);
//       }

//       // Get results ordered by latest first
//       $results = $query->orderBy('ss_auto_id', 'DESC')->get();

//       if ($results->isEmpty()) {
//         return response()->json([
//           'status' => 404,
//           'message' => 'No salary sheets found matching your criteria',
//           'data' => []
//         ], 200);
//       }

//       return response()->json([
//         'status' => 200,
//         'message' => 'Salary sheets found',
//         'data' => $results
//       ]);

//     } catch (Exception $e) {
//       return response()->json([
//         'status' => 500,
//         'message' => 'Error searching salary sheets: ' . $e->getMessage()
//       ], 500);
//     }
//   }

//   /**
//    * Get All Salary Sheets
//    */
//   public function getAllSalarySheets()
//   {
//     try {
//       $allSheets = SalarySheetUpload::orderBy('ss_auto_id', 'DESC')->get();

//       return response()->json([
//         'status' => 200,
//         'message' => 'All salary sheets retrieved',
//         'data' => $allSheets
//       ]);

//     } catch (Exception $e) {
//       return response()->json([
//         'status' => 500,
//         'message' => 'Error retrieving salary sheets: ' . $e->getMessage()
//       ], 500);
//     }
//   }

  /**
   * ...existing code...
   */

  //  Ajax Calling Request For Load Salary Pending List Date to Date

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

        if (count($allEmplId) == 1) {
          $pendingSalary =  (new SalaryProcessDataService())->searchAnEmployeeUnPaidSalaryRecordsWithEmloyeeInfoForListView($allEmplId, Auth::user()->branch_office_id);
        } else {
          $pendingSalary =  (new SalaryProcessDataService())->searchMultiEmployeeUnPaidSalaryRecordsForAMonthWithEmloyeeInfoForListView($allEmplId, $salary_month, $salary_year, Auth::user()->branch_office_id);
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
      return response()->json(['success' => true, 'status' => 200, 'message' => "Successfully Completed", 'data' => $slh_auto_id]);
    } else {
      return response()->json(['success' => false, 'status' => 404, 'error' => 'Salary Already Paid, Not Possible to Delete', 'data' => $slh_auto_id]);
    }
  }

  // Ajax Calling Request For Load Salary Paid Emp List Date to Date

  // move to payroll module salary controller
  public function SalarypaidList(Request $request)
  {
    try {


      // return response()->json(['status'=>404,'success'=>true, 'message'=>'Employee Not Found','error' =>'error']);
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

        return response()->json(['status' => 200, 'success' => true, 'pendingSalary' => $final_list]);
      } else {

        if ($sponser_id == '') {
          return response()->json(['status' => 404, 'success' => false, 'message' => "Select a Sponsor", 'error' => 'Select a Sponsor']);
        }

        $pendingSalary = (new SalaryProcessDataService())->searchListOfEmployeesThoseSalaryAlreadyPaidForListViewByProjectAndSponsor($sponser_id, $proj_id, $fromMonth, $toMonth, $fromYear, $toYear, Auth::user()->branch_office_id);

        return response()->json(['status' => 200, 'success' => false, 'pendingSalary' => $pendingSalary, 'fromMonth' => $fromMonth, 'toMonth' => $toMonth]);
      }
    } catch (Exception $ex) {
      return response()->json(['status' => 603, 'success' => false, 'message' => 'Server Operation Failed', 'error' => $ex]);
    }
  }



  // Pending Salary  Payment Confirmation
  public function SalaryPayment(Request $request)
  {


    $slh_auto_id_list = $request->slh_auto_id;
    foreach ($slh_auto_id_list as $slh_auto_id) {
      if ($request->has('emp_slh_paid_checkbox-' . $slh_auto_id)) {
        $emp_salary_record = (new SalaryProcessDataService())->getAnEmployeeBankInfoWithSalaryRecordForUpdateSalaryPaidMethodBySalaryHistoryAutoId($slh_auto_id);
        // $emp_salary_record->ebd_auto_id is the employee active bank details auto id

        if ($emp_salary_record->ebd_auto_id != null && $emp_salary_record->payment_method == "Bank") { // SalaryPaymentMethodEnum::Bank->value

          (new SalaryProcessDataService())->updateEmployeeSalaryStatusAsPaidAndPaymentMethod((int) $slh_auto_id, (int) $emp_salary_record->ebd_auto_id, Auth::user()->id);
        } else {
          (new SalaryProcessDataService())->updateEmployeeSalaryStatusAsCashPaid((int) $slh_auto_id, Auth::user()->id);
        }
      }
    }


    Session::flash('success', 'Successfully Updated');
    return redirect()->back();
  }

  public function SalaryPaymentToUnPay(Request $request)
  {
    try {
      $slh_auto_ids = $request->slh_auto_ids;
      for ($i = 0; $i < count($slh_auto_ids); $i++) {
        $slh_auto_id = $slh_auto_ids[$i];
        $payment = (new SalaryProcessDataService())->updateEmployeeSalaryStatusAsUnPaid($slh_auto_id, Auth::user()->id);
        // login user activities record
        //  (new AuthenticationDataService())->InsertLoginUserActivity(24,2, Auth::user()->id, $request->emp_auto_id,null);

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
}
