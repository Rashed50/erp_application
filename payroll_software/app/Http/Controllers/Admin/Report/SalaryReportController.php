<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\Helper\HelperController;
use App\Http\Controllers\DataServices\EmployeeDataService;
use App\Http\Controllers\DataServices\CompanyDataService;
use App\Http\Controllers\DataServices\EmployeeAdvanceDataService;
use App\Http\Controllers\DataServices\SalaryProcessDataService;
use App\Http\Controllers\DataServices\FiscalYearDataService;
use App\Http\Controllers\DataServices\EmpActivityDataService;
use App\Http\Controllers\DataServices\ProjectDataService;
use App\Http\Controllers\DataServices\CostControlReportDataService;
use App\Http\Controllers\DataServices\EmployeeRelatedDataService;
use App\Http\Controllers\DataServices\EmployeeAttendanceDataService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SalaryReportController extends Controller
{

        protected $searchby_employee_id = 'employee_id';
        protected $searchby_iqama_no = 'akama';

  public function loadSalaryReportGenerationForm(){

      $projects = (new ProjectDataService())->getAllActiveInActiveProjectListForDropdown(Auth::user()->branch_office_id);
      $emp_types = (new EmployeeRelatedDataService())->getEmployeeTypeForDropdown();
      $sponsors = (new EmployeeRelatedDataService())->getAllSponserInfoForDropdown(Auth::user()->branch_office_id);
      $emp_job_status = (new HelperController())->getEmployeeStatus();
      $trades = (new EmployeeRelatedDataService())->getEmpAllCategoryInfoForDropdown();
      $designation_heads = (new EmployeeRelatedDataService())->getDesignationHeadRecordsForDropdown();

      return view('admin.report.salary.salary_report_generation_form',compact('projects','sponsors','emp_job_status','designation_heads','trades','emp_types'));

  }

  public function processAndShowSalaryClosingReport(Request  $request){
    try{

      if($request->report_type == 1){
          // closing report employee list
        $report_records = (new FiscalYearDataService())->salaryClosingEmployeeListDateToDateReport($request->from_date,$request->to_date,Auth::user()->branch_office_id);
        $login_name = Auth::User()->name;
        $report_title = "Salary Closing Employees Report";
         $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
        return view('admin.report.salary.salary_closing_emp_list_report',compact('login_name','company','report_records','report_title'));

      }else if($request->report_type == 2){
            // closing report month by month summary
         $month_year = (new HelperController())->getMonthsInRangeOfDate($request->from_date,$request->to_date);
          $report_records = [] ;
          $counter = 0;
          foreach($month_year as $obj){
            $records = (new FiscalYearDataService())->salaryClosingDateToDateSummaryReport($obj["month"],$obj["year"],Auth::user()->branch_office_id);
            if($records[0]->month_name != null){
              $report_records[$counter++] = $records[0];
            }
          }
          $login_name = Auth::User()->name;
          $report_title = "Salary Closing Month by Month Summary";
           $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
          return view('admin.report.salary.salary_closing_monthly_summary_report',compact('login_name','company','report_records','report_title'));
      }



    }catch(Exception $ex){
        return view("System Operation Error. Please Input Valid Data & Try Again ");
    }


  }


    // 1 salary report menu, multiple employee prevacation salary report
  public function processAndShowEmployeeIDBaseEmployeeReport(Request  $request){
      try{
          // may not used now from type 1 to 3 , transfered to employeereportcontroller
        // if($request->report_type == 1){
        //   // employee details
        //       $company =  (new CompanyDataService())->findCompanryProfile();
        //       $employees = (new EmployeeDataService())->getEmployeeDetailsWitFileDownloadReportByMultipleEmpID($allEmplId);
        //       $project = "-";
        //       $report_title = "Multiple Employee ID";
        //       return view('admin.report.hr_section.multiple_id_employee_details', compact('employees', 'company', 'project', 'report_title'));

        // }else if($request->report_type == 2){
        //     // employee activities details
        //     $company =  (new CompanyDataService())->findCompanryProfile();
        //     $records = (new EmployeeDataService())->getAnEmployeeActivitiesReport($allEmplId[0],0);
        //     $loggedInUser = Auth::user()->name;
        //     return view('admin.report.hr_section.emp_activity_report', compact('records', 'company', 'loggedInUser'));
        // }else if($request->report_type == 3){

        //     // Employee working project history
        //     $employee = (new EmployeeDataService())->getAnyJobStatusEmployeeWithSalaryDetailsByEmpId($allEmplId[0]);
        //     return $this->processAndDisplayAnEmployeeWorkingPrjectHisotry($employee);
        // }else
        if($request->report_type == 4){
            // multiple employee prevacation salary statement
            return $this->prevacationSalaryStatementReport($request);
        }
        else {
             return view("Data Processing Error. Please Input Valid Data & Try Again ");
        }
      }
      catch(Exception $ex){
        return view("System Operation Error. Please Input Valid Data & Try Again ");
      }

  }



  // multiple employee prevacation salary statement
  private function prevacationSalaryStatementReport(Request  $request){


        $allEmplId = explode(",", $request->multiple_employee_Id);
        $employee_id_array = array_unique($allEmplId); // remove multiple same empl ID

        $report_records = [];
        $counter = 0;
        foreach($employee_id_array as $anEmp){
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
           // $employee->toal_CPF_contribution_from_salary =  (new SalaryProcessDataService())->getAnEmployeeTotalAmountContributeToCPFByFiscalYear($employee->emp_auto_id,$fiscal->start_date,$fiscal->end_date);
           // $employee->total_saudi_tax = (new SalaryProcessDataService())->getAnEmployeeTotalAmountSaudiTaxDeductionFromSalaryByFiscalYear($employee->emp_auto_id,$fiscal->start_date,$fiscal->end_date);
            $employee->empl_last_activity = (new EmpActivityDataService())->getAnEmployeeLastActivityComments($employee->emp_auto_id);
            $report_records[$counter++] =     $employee;

        }

        $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
        $login_name = Auth::User()->name;
        $report_title = "Pre-Vacation Salary Summary Report";
        return view('admin.report.salary.multi_emp_prevacation_salary_statement',compact('login_name','company','report_records','report_title'));


  }



  /* ==================== 3= Salary Hold , 4 = Office Staff Employees Salary Report ==================== */
  public function showEmployeeSalarySheetReportPrintPreviewByReportType(Request $request)
  {
       try{
            if($request->salary_report_type == 1){
              //3 Salary Hold Employee Salary Sheet
              return $this->processSalaryHoldEmployeeSalaryReport1($request);
            }elseif($request->salary_report_type == 2){
              //4 Office Staff Employee Salary Sheet
              return $this->processOfficeStaffEmployeeSalarySheetReport($request);
            }

        }catch(Exception $ex){
          return "System Data Processsing Error ".$ex;
       }
  }

 // Salary Hold Employee Salary Sheet
  private function processSalaryHoldEmployeeSalaryReport1(Request $request)
  {

       try{

          $month = $request->month;
          $year = $request->year;
          $project_ids = $request->project_ids;
          $salary_status = $request->has('salary_status') ? $request->salary_status:[0,1];
          //$salary_records = (new SalaryProcessDataService())->getEmployeeSalaryReportThoseAreSalaryHold($project_ids,$month, $year,$salary_status);
           $job_statuses = $request->has('job_status') ? $request->job_status:[1,2,3,4,5,6,7,8];
          $salary_records = (new SalaryProcessDataService())->getEmployeeSalaryReportThoseAreSalaryHold($project_ids,$month, $year,$salary_status,$job_statuses);

          $month_name = (new HelperController())->getMonthName($month);
          $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
          $login_name = Auth::user()->name;
          return view('admin.report.salary.salary_sheet.salary_hold_emp_salary_report', compact('salary_records','month_name','month', 'year', 'company' ,'login_name'));
       }catch(Exception $ex){
          return "System Data Processsing Error ".$ex;
       }
  }

  // Office Staff Employee Salary Sheet
  private function processOfficeStaffEmployeeSalarySheetReport(Request $request)
  {

       try{

          $month = $request->month;
          $year = $request->year;
          $project_ids = $request->project_ids;
          $salary_records = (new SalaryProcessDataService())->getOfficeStaffEmployeeSalaryReport($project_ids,$month, $year);
          $month_name = (new HelperController())->getMonthName($month);
          $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
          $login_name = Auth::user()->name;
          return view('admin.report.salary.salary_sheet.office_staff_salary_sheet_report', compact('salary_records','month_name','month', 'year', 'company' ,'login_name'));
       }catch(Exception $ex){
          return "System Data Processsing Error ".$ex;
       }
  }





// 7 Sponsor Type and Designation Head Salary Report (Cost Control)
    public function processProjectBaseSalaryReportForCostControll(Request $request){

        if((int)$request->report_type == 0){
             //sponsor (Asloob, Subcon) type salary details report
            // 7 details. Project-Wise Salary Allocation Report Designation Head
            return $this->processAProjectEmployeeSalaryDetailsRepportForCostcontolBySponsorType($request);

        }else  if((int)$request->report_type == 1){
           //sponsor (Asloob, Subcon) type salary summary report
            // 7 summary Project-Wise Salary Allocation Report Designation Head
            return $this->processAProjectEmployeeSalarySummaryRepportForCostcontolBySponsorType($request);
        }else if((int)$request->report_type == 2){
            // salary details report
            return $this->processAProjectDesignationHeadSalaryDetailsRepportForCostcontol($request);

        }else  if((int)$request->report_type == 3){
            // salary summary report
            return $this->processAProjectDesignationHeadSalarySummaryRepportForCostcontol($request);
        }
        else  if((int)$request->report_type == 4){
            // Only Project salary summary report cost control
            return $this->processOnlyProjectBaseSalarySummaryReportForCostControl($request);
        }

    }

    // 7,0 Designation Salary details Report (Cost Control)
    private function processAProjectEmployeeSalaryDetailsRepportForCostcontolBySponsorType($request){
        try{

             $month =  (int) $request->month;
            $year = (int) $request->year;
            $project_id_array =  [$request->project_ids];
            $sponsor_id_array = $request->has('sponsor_ids') ? $request->sponsor_ids : null;
            $desig_head_id_array = $request->has('designation_ids') ? $request->designation_ids:[];
            $emp_types = $request->has('emp_type') ? [$request->emp_type] : [1,2];

            if ($request->project_ids == null ) {
                    return "Please Select A Project and Try Again ";
            } else {
                $project_name = (new ProjectDataService())->getProjectNameByProjectId($request->project_ids);
            }
            $sponsor_names = "";
            if ( $sponsor_id_array == null ) {

                $sponsor_id_array = (new EmployeeRelatedDataService())->getAllActiveSponsorIdAsArrayOfABranchOfficeBySponsorTypes(Auth::user()->branch_office_id,['Asloob','Subcon']);
                $sponsor_names = "All Sponsors";
            }else {
                $sponsor_names =  count($sponsor_id_array) == 2 ? "All Sponsors": $sponsor_id_array[0]." Sponsors";
                $sponsor_id_array = (new EmployeeRelatedDataService())->getAllActiveSponsorIdAsArrayOfABranchOfficeBySponsorTypes(Auth::user()->branch_office_id,$sponsor_id_array);
            }
          //
            if(count($desig_head_id_array) == 0){
                // dd($desig_records);
                $desig_head_id_array =  (new EmployeeRelatedDataService())->getAllActiveCategoryIDAsArrayWithRankingSequenceByEmpTypeIds([$request->emp_type],Auth::user()->branch_office_id);

            }

            $salaryReport = (new SalaryProcessDataService())->getMultipleProjectSalaryDetailsForCostControllReportBySponsorTypeAndEmployeeType($project_id_array,$sponsor_id_array, $desig_head_id_array ,$month, $year,1);
            $month_name = (new HelperController())->getMonthName($month);
                        $monthName = (new HelperController())->getMonthName($month);
            $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
            $login_name = Auth::user()->name;
            $salaryYear = $year;
            $project = $project_name;
            $employeeType = '';
            $reportLeftTitle = ["Salary month of ".$monthName." ".$year, $project_name , $sponsor_names ];
            return view('admin.salary-generate.projectwise.employee-salary-details',
                compact('salaryReport','sponsor_names','reportLeftTitle','monthName','month','project','year', 'salaryYear','employeeType', 'company' ,'login_name'));

        }catch(Exception $ex){
            return "System Data Processsing Error ".$ex;
        }
    }

    //7,1 Designation  Salary summary for cost Controll
    private function processAProjectEmployeeSalarySummaryRepportForCostcontolBySponsorType($request){
        try{


            $month =  (int) $request->month;
            $year = (int) $request->year;
            $project_id_array =  [$request->project_ids];
            $sponsor_id_array = $request->has('sponsor_ids') ? $request->sponsor_ids : null;
            $desig_records = $request->has('designation_ids') ? $request->designation_ids:[];
            $emp_types = $request->has('emp_type') ? [$request->emp_type] : [1,2];

            if ($request->project_ids == null ) {
                    return "Please Select A Project and Try Again ";
            } else {
                $project_name = (new ProjectDataService())->getProjectNameByProjectId($request->project_ids);
            }
            $sponsor_names = "";
            if ( $sponsor_id_array == null ) {

                $sponsor_id_array = (new EmployeeRelatedDataService())->getAllActiveSponsorIdAsArrayOfABranchOfficeBySponsorTypes(Auth::user()->branch_office_id,['Asloob','Subcon']);
                $sponsor_names = "All Sponsors";
            }else {
                $sponsor_names =  count($sponsor_id_array) == 2 ? "All Sponsors": $sponsor_id_array[0]." Sponsors";
                $sponsor_id_array = (new EmployeeRelatedDataService())->getAllActiveSponsorIdAsArrayOfABranchOfficeBySponsorTypes(Auth::user()->branch_office_id,$sponsor_id_array);
            }

            if(count($desig_records) == 0){
                $desig_head_records =  (new EmployeeRelatedDataService())->getListOfDesignationRecordsByMultipleAutoIds(null,[$request->emp_type],Auth::user()->branch_office_id);

            }else {

                $desig_head_records =  (new EmployeeRelatedDataService())->getListOfDesignationRecordsByMultipleAutoIds($desig_records,[$request->emp_type],Auth::user()->branch_office_id);
            }

             $summary_records = array();
            $counter = 0;
            foreach($desig_head_records as $adh){
                //$records = (new SalaryProcessDataService())->getMultipleProjectSalarySummaryForCostControllReportBySponsorTypeAndEmployeeType($project_id_array,$sponsor_id_array, $adh->catg_id ,$month, $year);
                $records = (new CostControlReportDataService())->getMultipleProjectSalarySummaryForCostControllReportBySponsorTypeAndEmployeeType($project_id_array,$sponsor_id_array, $adh->catg_id ,$month, $year);

                $adh->basic_total_emp = 0;
                $adh->basic_total_hours = 0;
                $adh->basic_total_salary = 0;
                $adh->basic_total_food_allowance = 0;
                $adh->hourly_total_emp =0;
                $adh->hourly_total_hours = 0;
                $adh->hourly_total_salary = 0;
                $adh->hourly_total_food_allowance = 0;

                if(count($records) == 1){

                    $abc = $records[0];
                    if($abc->total_emp == 0)
                        return;

                    if($abc["hourly_employee"] == null){

                        $adh->basic_total_emp = round($abc->total_emp);
                        $adh->basic_total_hours = round($abc->total_hours);
                        $adh->basic_total_salary = round($abc->total_slh_all_include_amount);
                        $adh->basic_total_food_allowance =  round($abc->food_allowance);

                    }else{

                        $adh->hourly_total_emp = round($abc['total_emp']);
                        $adh->hourly_total_hours = round($abc->total_hours);
                        $adh->hourly_total_salary = round($abc['total_slh_all_include_amount']);
                        $adh->hourly_total_food_allowance =  round($abc['food_allowance']);
                    }
                    $summary_records[$counter++] = $adh;

                }elseif(count($records) == 2){

                    if($records[0]->total_emp == 0 && $records[1]->total_emp == 0 )
                        return;

                    $adh->basic_total_emp = round($records[0]->total_emp);
                    $adh->basic_total_hours = round($records[0]->total_hours);
                    $adh->basic_total_salary = round($records[0]->total_slh_all_include_amount);
                    $adh->basic_total_food_allowance =  round($records[0]->food_allowance);

                    $adh->hourly_total_emp = round($records[1]->total_emp);
                    $adh->hourly_total_hours = round($records[1]->total_hours);
                    $adh->hourly_total_salary = round($records[1]->total_slh_all_include_amount);
                    $adh->hourly_total_food_allowance =  round($records[1]->food_allowance);
                   // dd($records);
                    $summary_records[$counter++] = $adh;

                }
            }
            //dd($summary_records);
            $desig_head_records = $summary_records;
            $month_name = (new HelperController())->getMonthName($month);
            $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
            $login_name = Auth::user()->name;
            return view('admin.report.salary.salary_sheet.project_salary_summary_for_cost_control_report',
            compact('desig_head_records','sponsor_names','project_name','month_name','month', 'year', 'company' ,'login_name'));

        }catch(Exception $ex){
            return "System Data Processsing Error ".$ex;
        }
    }

    // 7,2 Designation Salary details Report (Cost Control)
    private function processAProjectDesignationHeadSalaryDetailsRepportForCostcontol($request){
        try{

            $month =  (int) $request->month;
            $year = (int) $request->year;
            $project_id_array =  [$request->project_ids];
            $sponsor_id_array = $request->has('sponsor_ids') ? $request->sponsor_ids : null;
            $desig_head_id_array = $request->designation_head_ids;

            if ($request->project_ids == null ) {
                return "Please Select A Project and Try Again ";
            } else {
                $project_name = (new ProjectDataService())->getProjectNameByProjectId($request->project_ids);
            }
            $sponsor_names = "";
            if ( $sponsor_id_array == null ) {
                $sponsor_id_array = (new EmployeeRelatedDataService())->getAllActiveSponsorIdAsArrayOfABranchOffice(Auth::user()->branch_office_id);
                $sponsor_names = "All Sponsors";
            }else {
                foreach( $sponsor_id_array as $sp)
                    {
                        $sponsor_names = $sponsor_names.(new EmployeeRelatedDataService())->getASponserNameBySponerId( $sp);
                        $sponsor_names = $sponsor_names.' | ';
                    }
            }
            $salaryReport = (new SalaryProcessDataService())->getMultipleProjectSalaryDetailsCostControllReportByDesignationHead(
                $project_id_array,$sponsor_id_array, $desig_head_id_array ,$month, $year,1);
                $month_name = (new HelperController())->getMonthName($month);

            $monthName = (new HelperController())->getMonthName($month);
            $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
            $login_name = Auth::user()->name;
            $salaryYear = $year;
            $project = $project_name;
            $employeeType = '';
            $reportLeftTitle = ["Salary month of ".$monthName." ".$year, $project_name , $sponsor_names ];
            return view('admin.salary-generate.projectwise.employee-salary-details',
                compact('salaryReport','sponsor_names','reportLeftTitle','monthName','month','project', 'salaryYear','employeeType', 'company' ,'login_name'));



        }catch(Exception $ex){
            return "System Data Processsing Error ".$ex;
        }
    }

    // 7,3 Designation Salary summary Report (Cost Control)
    private function processAProjectDesignationHeadSalarySummaryRepportForCostcontol($request){
        try{
            $month =  (int) $request->month;
            $year = (int) $request->year;
            $project_id_array =  [$request->project_ids];
            $sponsor_id_array = $request->has('sponsor_ids') ? $request->sponsor_ids : null;
            $desig_head_id_array = $request->designation_head_ids;

            if ($request->project_ids == null ) {
            return "Please Select A Project and Try Again ";
            } else {
                $project_name = (new ProjectDataService())->getProjectNameByProjectId($request->project_ids);
            }
            $sponsor_names = "";
            if ( $sponsor_id_array == null ) {
                $sponsor_id_array = (new EmployeeRelatedDataService())->getAllActiveSponsorIdAsArrayOfABranchOffice(Auth::user()->branch_office_id);
                $sponsor_names = "All Sponsors";
            }else {
                foreach( $sponsor_id_array as $sp)
                {
                    $sponsor_names = $sponsor_names.(new EmployeeRelatedDataService())->getASponserNameBySponerId( $sp);
                    $sponsor_names = $sponsor_names.' | ';
                }
            }
            $desig_head_records = (new EmployeeRelatedDataService())->getListOfDesignationHeadRecordsByMultipleAutoIds($desig_head_id_array,Auth::user()->branch_office_id);
            $summary_records = array();
            foreach($desig_head_records as $adh){
                //$records = (new SalaryProcessDataService())->getMultipleProjectSalarySummaryCostControllReportByDesignationHead($project_id_array,$sponsor_id_array, $adh->dh_auto_id ,$month, $year);
                $records = (new CostControlReportDataService())->getMultipleProjectSalarySummaryCostControllReportByDesignationHead($project_id_array,$sponsor_id_array, $adh->dh_auto_id ,$month, $year);

                $adh->basic_total_emp = 0;
                $adh->basic_total_salary = 0;
                $adh->basic_total_food_allowance = 0;
                $adh->hourly_total_emp =0;
                $adh->hourly_total_salary = 0;
                $adh->hourly_total_food_allowance = 0;
                if(count($records) == 1){

                    $abc = $records[0];
                    if($abc["hourly_employee"] == null){

                        $adh->basic_total_emp = round($abc->total_emp);
                        $adh->basic_total_salary = round($abc->total_slh_all_include_amount);
                        $adh->basic_total_food_allowance =  round($abc->food_allowance);

                    }else{
                        $adh->hourly_total_emp = round($abc['total_emp']);
                        $adh->hourly_total_salary = round($abc['total_slh_all_include_amount']);
                        $adh->hourly_total_food_allowance =  round($abc['food_allowance']);
                    }

                }elseif(count($records) == 2){

                    $abc = $records[0];

                    $adh->basic_total_emp = round($abc->total_emp);
                    $adh->basic_total_salary = round($abc->total_slh_all_include_amount);
                    $adh->basic_total_food_allowance =  round($abc->food_allowance);

                    $adh->hourly_total_emp = round($records[1]->total_emp);
                    $adh->hourly_total_salary = round($records[1]->total_slh_all_include_amount);
                    $adh->hourly_total_food_allowance =  round($records[1]->food_allowance);

                }
            }
            $month_name = (new HelperController())->getMonthName($month);
            $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
            $login_name = Auth::user()->name;
            return view('admin.report.salary.salary_sheet.designation_head_summary_costcontrol_report',
            compact('desig_head_records','sponsor_names','project_name','month_name','month', 'year', 'company' ,'login_name'));

        }catch(Exception $ex){
            return "System Data Processsing Error ".$ex;
        }
    }

     // 7,4 Only Project Salary summary Report (Cost Control)
    private function processOnlyProjectBaseSalarySummaryReportForCostControl($request)
    {
        try{
            $project_ids = $request->has('project_ids') ? $request->project_ids : (new ProjectDataService())->getAllActiveProjectIdAsArrayOfABranchOffice(Auth::user()->branch_office_id);
            $month = (int) $request->month;
            $year = (int) $request->year;

            $counter = 0;
            $records = array();
            foreach($project_ids as $pid){
                $ap_records = (new SalaryProcessDataService())->getOnlyThisProjectTotalSalaryAmountForMultipleProjectWorkByProjectMonthAndYear($pid,$month,$year);
                if(count($ap_records) == 0){
                    continue;
                }
                $arecord = $ap_records[0];
                $arecord->project_name = (new ProjectDataService())->getProjectNameByProjectId($pid);
                $arecord->month_name = (new HelperController())->getMonthName($month);
                $arecord->year = $year;
                $records[$counter++] = $arecord;
            }
            $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
            $login_name = Auth::user()->name;
            return view('admin.report.salary.summary.only_projectbase_summary_cost_control_report', compact('login_name','records', 'company'));
        }catch(Exception $ex){
            return "System Data Processsing Error ".$ex;
        }

    }




     // 8 sposnor/sub-contractor related all reports
    public function processAProjectSponsorsMultiMonthWorkingEmployeeSalaryReport(Request $request){


        if((int)$request->report_type == 1){
             // salary details report
             
            return  $this->processMultiSopnsorsSingleMonthProjectBaseSalarySummaryReport($request);
        }else  if((int)$request->report_type == 2){
            // salary summary report
            return $this->processAProjectSponsorsMultiMonthWorkingEmployeeSalarySummaryReport($request);
        }

         else  if((int)$request->report_type == 3){
            // Subcon and Asloob Unpaid Salary Summary Report
            return $this->processSubcontractorAndCompanySponsorUnpaidSalarySummaryReport($request);
        }
        else  if((int)$request->report_type == 4){
            // Subcon and Asloob Unpaid Salary Summary Report
            return $this->processSubcontractorAndCompanySponsorPaidUnpaidSalarySummaryReport($request);
        }
         else  if((int)$request->report_type == 5){
            // 5 single month & project subcontractor as sponsor salary summary cost control report
            return $this->processSingleProjectAndMonthMultiSubcontractorAsSponsorBaseSalarySummaryReport($request);
        }



    }


     // 1 sposnor/sub-contractor: Single Project a Sponsor Multiple MOnth Salary Summary Report
    private function processAProjectSponsorsMultiMonthWorkingEmployeeSalarySummaryReport($request){
        try{

            $months = $request->has('month') ? $request->month : null;
            $year = (int) $request->year;
            $project_id_array = $request->has('project_ids') ? $request->project_ids : null;

            $sponsor_id_array = $request->has('sponsor_ids') ? $request->sponsor_ids : array();

            if ($project_id_array == null  || $months == null ) {
                return "Please Select A Project & Maximum 3 Months and Try Again ";
            }else if(count($months) > 3 ){
                return "More Than 3 Month Not Allowed, Please Select Maximum 3 Months";
            }
            else if(  count($sponsor_id_array) == 0  ){
                return "  Please Select One or More Sponsor Sponsor";
            }
            else if(count($project_id_array) > 1 ||  count($project_id_array) < 1  ){
                return "  Please Select single Project";
            }
            $project_id = (int) $project_id_array[0];
            $project_name = (new ProjectDataService())->getProjectNameByProjectId($project_id);

            $current_employee = (new EmployeeDataService())->countTotalNumberOfActiveEmployeesInAProjectOfSponsors($project_id,$sponsor_id_array);

            $summary_records = array();
            $counter = 0;
            foreach($sponsor_id_array as $sp){
               $asponsor = (new EmployeeRelatedDataService())->findASponser( $sp);
               $records = (new SalaryProcessDataService())->getAProjectCurrentlyWorkingEmployeeSalarySummaryReport([$project_id],$sp ,$months,$year);
               $asponsor->total_employee = (new EmployeeDataService())->countTotalNumberOfActiveEmployeesInAProjectOfSponsors($project_id,[$sp]);
                 if(count($records) > 0) {
                    $asponsor->salary_records = $records;
                    $summary_records[$counter++] = $asponsor;
               }
            }

            $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
            $login_name = Auth::user()->name;
            return view('admin.report.salary.sponsors.aproject_multi_sponsor_monthly_salary_summary', compact('summary_records','project_name','current_employee' ,'months', 'year', 'company' ,'login_name'));

        }catch(Exception $ex){
            return "System Data Processsing Error ".$ex;
        }
    }
   //2 sposnor/sub-contractor: multi sponsor project base salary summary
    private function processMultiSopnsorsSingleMonthProjectBaseSalarySummaryReport($request){
        try{

            $months = $request->has('month') ? $request->month : null;
            $year = (int) $request->year;
            $project_id_array =  $request->has('project_ids') ? $request->project_ids : (new ProjectDataService())->getAllActiveProjectIDOfABranchOfficeAsArray(Auth::user()->branch_office_id);
            $sponsor_id_array = $request->has('sponsor_ids') ? $request->sponsor_ids : array();
            if ( is_null($months) ||  count($months) > 1  ) {
                return "Please Select Only One Month ";
            }
            else if(  count($sponsor_id_array) > 6 ||  count($sponsor_id_array) < 1  ){
                return "  Please Select Maximum 6 Sponsors";
            }

            $sponsors = (new EmployeeRelatedDataService())->getListOfActiveSponserInfoOrderBySponsorIdAndByMultipleId( $sponsor_id_array);

            $summary_records = array();
            $counter = 0;
            $month = (int)$months[0];
            foreach($project_id_array as $p){

                $aproject = (new ProjectDataService())->findAProjectInformationWithSelectedBasicInformation($p);
                $sp_records = array();
                $sp_counter = 0;
                $is_data_found = false;
                foreach($sponsors as $sp){

                    $records = (new SalaryProcessDataService())->getAProjectWorkedEmployeeSalaryForSelectedSponsorsSummaryReport($p, $sp->spons_id ,$month,$year);

                    if($records) {
                        $sp_records[$sp_counter] = $records;
                        $is_data_found = true;
                    }else {
                        $object = new \stdClass();
                        $object->sponsor_id = $sp->spons_id;
                        $object->total_emp = 0;
                            $object->total_hours = 0;
                        $object->total_gross_salary = 0;
                        $sp_records[$sp_counter] =  $object;
                    }
                    $sp_counter += 1;
                }
                if($is_data_found){
                    $aproject->salary_records = $sp_records;
                    $summary_records[$counter++] = $aproject;
                }


            }
          //  dd(@$sponsors, $summary_records[6]);
            $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
            $login_name = Auth::user()->name;
            return view('admin.report.salary.sponsors.amonth_mult_sponsor_project_base_salary_summary', compact('summary_records','sponsors','month', 'year', 'company' ,'login_name'));

        }catch(Exception $ex){
            return "System Data Processsing Error ".$ex;
        }
    }


    //3 sposnor/sub-contractor: Subcontractor and Company Sponsor Unpaid Salary Summary Report
    private function processSubcontractorAndCompanySponsorUnpaidSalarySummaryReport($request){
        try{

            $months = $request->has('month') ? $request->month : array();
            $year = (int) $request->year;
            $project_id_array =  $request->has('project_ids') ? $request->project_ids : (new ProjectDataService())->getAllActiveProjectIDOfABranchOfficeAsArray(Auth::user()->branch_office_id);

            if ( is_null($months) ||  count($months) > 1 ) {
                return "Please Select Single Month";
            }
            $summary_records = array();
            $counter = 0;
            foreach($project_id_array as $p){

                $aproject = (new ProjectDataService())->findAProjectInformationWithSelectedBasicInformation($p);
                $sp_records = array();
                $sp_counter = 0;
                $is_data_found = false;
                  foreach($months as $m){

                    $all_records = (new SalaryProcessDataService())->getAProjectSubcontractorAndCompanySponsorUnpaidSalarySummaryReport($p, $m,$year);

                    $subcon = $all_records[0]; // 0 index subcon-paid unpaid , 1 asloob basic-hourly, 2 paid-unpaid salary summary
                    $basic_hourly = $all_records[1];
                    $paid_unpaid = $all_records[2];

                    $object = new \stdClass();
                    $object->subcon_unpaid_salary = 0;
                    $object->subcon_paid_salary = 0;

                    // $object->subcon_total_emp = 0;
                    // $object->asloob_total_emp = 0;
                    $object->asloob_basic_salary = 0;
                    $object->asloob_hourly_salary = 0;

                    $object->total_salary = 0;
                    $object->paid_salary = 0;
                    $object->unpaid_salary = 0;
                    $object->month = $m;

                    // subcon salary
                    if(count($subcon) == 2) {
                        $is_data_found = true;
                        if($subcon[0]->status == 0 ){
                            $object->subcon_unpaid_salary = $subcon[0]->total_salary;
                            $object->total_salary += $subcon[0]->total_salary ;

                            $object->subcon_paid_salary = $subcon[1]->total_salary;
                            $object->total_salary += $subcon[1]->total_salary ;
                        }else {
                            $object->subcon_paid_salary = $subcon[0]->total_salary;
                            $object->total_salary += $subcon[0]->total_salary ;

                            $object->subcon_unpaid_salary = $subcon[1]->total_salary;
                            $object->total_salary += $subcon[1]->total_salary ;
                        }
                    }else if(count($subcon) == 1) {

                        $is_data_found = true;
                        if($subcon[0]->status == 0 ){
                            $object->subcon_unpaid_salary = $subcon[0]->total_salary;
                            $object->total_salary += $subcon[0]->total_salary ;
                        }else{
                            $object->subcon_paid_salary = $subcon[0]->total_salary;
                            $object->total_salary += $subcon[0]->total_salary ;
                        }
                    }



                    // Asloob Basic and Hourly Salary
                    if(count($basic_hourly) == 2) {

                        $is_data_found = true;
                        if($basic_hourly[0]->hourly_employee == 1){ // hourly
                            $object->asloob_hourly_salary = $basic_hourly[0]->total_salary;
                            $object->total_salary += $basic_hourly[0]->total_salary ;

                            $object->asloob_basic_salary = $basic_hourly[1]->total_salary;
                            $object->total_salary += $basic_hourly[1]->total_salary ;

                        }else {
                            $object->asloob_hourly_salary = $basic_hourly[1]->total_salary;
                            $object->total_salary += $basic_hourly[1]->total_salary ;

                            $object->asloob_basic_salary = $basic_hourly[0]->total_salary;
                            $object->total_salary += $basic_hourly[0]->total_salary ;
                        }
                    }else if(count($basic_hourly) == 1) {

                        $is_data_found = true;
                        if($basic_hourly[0]->hourly_employee == 1){ // hourly
                            $object->asloob_hourly_salary = $basic_hourly[0]->total_salary;
                            $object->total_salary += $basic_hourly[0]->total_salary ;
                        }else {
                            $object->asloob_basic_salary = $basic_hourly[0]->total_salary;
                            $object->total_salary += $basic_hourly[0]->total_salary ;
                        }
                    }

                     // Paid-Unpaid  Salary
                    if(count($paid_unpaid) == 2) {
                        $is_data_found = true;
                        if($paid_unpaid[0]->status == 0 ){ // unpaid
                            $object->unpaid_salary = $paid_unpaid[0]->total_salary;
                            $object->paid_salary = $paid_unpaid[1]->total_salary;

                        }else {
                            $object->unpaid_salary = $paid_unpaid[1]->total_salary;
                            $object->paid_salary = $paid_unpaid[0]->total_salary;
                         }
                    }else if(count($basic_hourly) == 1) {
                        $is_data_found = true;
                        if($paid_unpaid[0]->status == 0 ){ // unpaid
                            $object->unpaid_salary = $paid_unpaid[0]->total_salary;

                        }else {
                            $object->paid_salary = $paid_unpaid[0]->total_salary;
                         }
                    }

                    if( $is_data_found){
                        $sp_records[$sp_counter] = $object;
                        $sp_counter += 1;
                    }

                }

                if($is_data_found){
                    $aproject->salary_records = $sp_records;
                    $summary_records[$counter++] = $aproject;
                }
            }
           // dd(100,$summary_records);
            $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
            $login_name = Auth::user()->name;
            return view('admin.report.salary.sponsors.asloob_subcon_sponsor_salary_summary', compact('summary_records','months', 'year', 'company' ,'login_name'));

        }catch(Exception $ex){
            return "System Data Processsing Error ".$ex;
        }
    }


        //4 sposnor/sub-contractor: Subcontractor and Company Sponsor Paid/Unpaid Salary Summary Report
    private function processSubcontractorAndCompanySponsorPaidUnpaidSalarySummaryReport($request){
        try{

            $months = $request->has('month') ? $request->month : array();
            $year = (int) $request->year;
            $project_id_array =  $request->has('project_ids') ? $request->project_ids : (new ProjectDataService())->getAllActiveProjectIDOfABranchOfficeAsArray(Auth::user()->branch_office_id);

            if ( is_null($months) ||  count($months) > 1  ) {
                return "Please Select Month Single Month";
            }
            $summary_records = array();
            $counter = 0;
            $month = $months[0];

            foreach($project_id_array as $p){

                $is_data_found = false;
                $aproject = (new ProjectDataService())->findAProjectInformationWithSelectedBasicInformation($p);

                    $all_records = (new SalaryProcessDataService())->getAProjectSubcontractorAndCompanySponsorPaidUnpaidSalarySummaryReport($p, $month,$year);
                    $subcon = $all_records[0]; // 0 index subcon-paid unpaid , 1 asloob, 2 total salary summary
                    $asloob = $all_records[1];
                   // $paid_unpaid = $all_records[2];

                    $object = new \stdClass();
                    $aproject->subcon_unpaid_salary = 0;
                    $aproject->subcon_paid_salary = 0;
                    $aproject->subcon_total_emp = 0;

                    $aproject->asloob_total_emp = 0;
                    $aproject->asloob_paid_salary = 0;
                    $aproject->asloob_unpaid_salary = 0;

                    $aproject->total_salary = 0;
                    $aproject->paid_salary = 0;
                    $aproject->unpaid_salary = 0;

                    if(count($subcon) == 2) {
                         $is_data_found = true;

                        if($subcon[0]->status == 0 ){
                            $aproject->subcon_unpaid_salary = $subcon[0]->total_salary;
                            $aproject->subcon_paid_salary = $subcon[1]->total_salary;
                        }else {
                            $aproject->subcon_paid_salary = $subcon[0]->total_salary;
                            $aproject->subcon_unpaid_salary = $subcon[1]->total_salary;
                        }

                        $aproject->subcon_total_emp = $subcon[0]->total_emp + $subcon[1]->total_emp;
                    }else if(count($subcon) == 1) {

                        $is_data_found = true;
                        if($subcon[0]->status == 0 ){
                            $aproject->subcon_unpaid_salary = $subcon[0]->total_salary;
                        }else{
                            $aproject->subcon_paid_salary = $subcon[0]->total_salary;
                        }
                        $aproject->subcon_total_emp = $subcon[0]->total_emp;
                    }



                    // Asloob
                     if(count($asloob) == 2) {
                          $is_data_found = true;

                        if($asloob[0]->status == 0 ){
                            $aproject->asloob_unpaid_salary = $asloob[0]->total_salary;
                            $aproject->asloob_paid_salary = $asloob[1]->total_salary;

                        }else {
                            $aproject->asloob_paid_salary = $asloob[0]->total_salary;
                            $aproject->asloob_unpaid_salary = $asloob[1]->total_salary;
                        }

                        $aproject->asloob_total_emp = $asloob[0]->total_emp + $asloob[1]->total_emp;
                    }else if(count($asloob) == 1) {
                          $is_data_found = true;

                        if($asloob[0]->status == 0 ){
                            $aproject->asloob_unpaid_salary = $asloob[0]->total_salary;
                        }else{
                            $aproject->asloob_paid_salary = $asloob[0]->total_salary;
                        }
                        $aproject->asloob_total_emp = $asloob[0]->total_emp;
                    }

                    if($is_data_found){
                            $summary_records[$counter++] = $aproject;
                    }


            }
            $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
            $login_name = Auth::user()->name;
            return view('admin.report.salary.sponsors.asloob_subcon_paid_upaid_summary', compact('summary_records','month', 'year', 'company' ,'login_name'));

        }catch(Exception $ex){
            return "System Data Processsing Error ".$ex;
        }
    }


        //5 single month & project subcontractor as sponsor salary summary cost control report
    private function processSingleProjectAndMonthMultiSubcontractorAsSponsorBaseSalarySummaryReport($request){
        try{

            $months = $request->has('month') ? $request->month : null;
            $year = (int) $request->year;
            $project_id_array =  $request->has('project_ids') ? $request->project_ids : [];// (new ProjectDataService())->getAllActiveProjectIDOfABranchOfficeAsArray(Auth::user()->branch_office_id);
            $sponsor_id_array = $request->has('sponsor_ids') ? $request->sponsor_ids : (new EmployeeRelatedDataService())->getAllActiveSubcontractorSponsorIdAsArrayOfABranchOffice(Auth::user()->branch_office_id);
            if ( is_null($months) ||  count($months) > 1  ) {
                return "Please Select Only One Month ";
            }
            else if(  count($project_id_array) != 1  ){
                return "  Please Select Only One Project Name";
            }

            $month = (int)$months[0]; // only first month
            $counter = 0;

          foreach($sponsor_id_array as $aspon_id){

                $employee_salary_records = (new SalaryProcessDataService())->searchSponsorBaseSalarySheetReport($project_id_array[0], $aspon_id, $month, $year, 0);
                foreach($employee_salary_records as $anemp_sr){
                    $counter++;
                    $this->processAnEmployeMultipleProjectWorkSalary($anemp_sr, $month, $year);
                }

                $employee_salary_records1 = (new SalaryProcessDataService())->searchSponsorBaseSalarySheetReport($project_id_array[0], $aspon_id, $month, $year, 1);
                foreach($employee_salary_records1 as $anemp_sr1){
                    $counter++;
                    $this->processAnEmployeMultipleProjectWorkSalary($anemp_sr1, $month, $year);
                }


            }

            $summary_records = (new CostControlReportDataService())->getAProjectSingleMonthSubcontorAsSponsorBaseMultiProjectWorkActualCostrollRecport($project_id_array[0], $sponsor_id_array ,$month,$year);
          //  dd($counter, $sponsor_id_array);
            $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
            $project_name = (new ProjectDataService())->findAProjectInformationWithSelectedBasicInformation($project_id_array[0])->proj_name;
            $login_name = Auth::user()->name;
            return view('admin.report.salary.sponsors.aproject_amonth_mult_subcon_as_sponsor_report', compact('summary_records','project_name','month', 'year', 'company' ,'login_name'));

        }catch(Exception $ex){
            return "System Data Processsing Error ".$ex;
        }
    }



    private function processAnEmployeMultipleProjectWorkSalary($anEmployee, $month, $salaryYear){


        $records = (new EmployeeAttendanceDataService())->getAnEmployeeMultiprojectWorkRecordsOnly($anEmployee->emp_auto_id,$month,$salaryYear);
        foreach($records as $arecord){
            $total_work_day = $arecord->total_day;
            $overtime = $arecord->total_overtime;
            $total_hour =  $arecord->total_hour;
           $new_record  = (new SalaryProcessDataService())->calculateAnEmployeeMultipleProjectSalaryFromSalaryHistoryTableForAMonth($anEmployee,$arecord);
       //     $new_record  = (new SalaryProcessDataService())->calculateAnEmployeeMultipleProjectSalaryForAMonth($anEmployee,$arecord);
             (new SalaryProcessDataService())->updateAnEmployeeMultipleProjectWorkSalaryAmountAtTimeOfProcessingCostControlReport($arecord->empwh_auto_id,
             $new_record->total_amount,$new_record->food_amount, $new_record->other_amount,$new_record->ot_amount);
        }

    }







} // End Of Controller Class
