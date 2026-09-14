<?php

namespace App\Http\Controllers\Admin\EmpActivity;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DataServices\EmployeeRelatedDataService;
use App\Http\Controllers\DataServices\EmployeeDataService;
use App\Http\Controllers\DataServices\SalaryProcessDataService;
use App\Http\Controllers\DataServices\FiscalYearDataService;
use App\Http\Controllers\Admin\Helper\HelperController;
use Illuminate\Http\Request;
 use App\Enums\EmployeeJobStatusEnum;
 use App\Enums\EmpSalaryStatusEnum;
 use App\Enums\EmployeeActivityTypeEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class EmpActivityController extends Controller
{
    
     function __construct(){
    
        $this->middleware('permission:employee_job_status_change_activity', ['only' => ['loadEmployeeNewActivityInsertForm','employeeNewActivityInsertRequest']]);
        $this->middleware('permission:employee_salary_status_update', ['only' => ['employeeNewActivityWithSalaryStatusUpdateRequest']]);
      }
  
  /* ==================== Employee Activitie Related Works ==================== */
  public function loadEmployeeNewActivityInsertForm(){
      
    $emp_activity = EmployeeActivityTypeEnum::cases();
    $job_status = EmployeeJobStatusEnum::cases();
    $salary_status = EmpSalaryStatusEnum::cases();
    
   // dd(100);
    return view('admin.empactivity.new_activity_insert',compact('emp_activity','job_status','salary_status'));

  }

 // Employee Job Status Update
  public function employeeNewActivityInsertRequest(Request $request){
     
      $emp = (new EmployeeDataService())->getAnEmployeeInfoByEmpAutoId($request->emp_auto_id);
      if($request->activity_type == "" || $request->job_status == ""){
        Session::flash('error', 'Please Select Activity Type & Job Status');
        return redirect()->back();
      }else if($emp ==  null)
      {
        Session::flash('error', 'Employee Not Found');
        return redirect()->back();
      } 
       else if((int) $emp->job_status ==  0)
      {
          Session::flash('error', 'Employee is not Approved Yet');
          return redirect()->back();
      }
      
      
      if($emp->job_status  != (int) $request->job_status){
          
        (new EmployeeDataService())->updateEmployeeJobStatus($request->emp_auto_id,$request->job_status);
      }else {
        Session::flash('error', 'Your Selected Status and Employee Current Status are Same');
        return redirect()->back();
      }
     
        
       if($request->activity_type == 10 || (int) $request->job_status == 6 || (int) $request->job_status == 3){
           // // 6 = Runaway , 10 = salary activity 
        $salary_status = 20;// EmpSalaryStatusEnum::Salary_Hold;
        (new EmployeeDataService())->updateAnEmployeeSalaryStatus($request->emp_auto_id,$salary_status,Auth::user()->id);
      }
      
        $insert = (new EmployeeRelatedDataService())->employeeWiseActivityRecordInsert(
        $request->emp_auto_id,
        $request->activity_remarks,
        $request->activity_description,
        $request->activity_type,
        $request->activity_date,
        (int) $request->job_status,
        Auth::user()->id
      ); 
        // Create an Employee Fiscal Year if running fiscal year not exist
       $this->openAnEmployeeSalaryFiscalYear($request->emp_auto_id,$request->activity_date);

        if ($insert) {
            Session::flash('success', 'Successfuly Saved');
            return redirect()->route('employee.new.activity.insert.form');
          } else {
            Session::flash('error', 'Operation Failed, Please try again');
            return redirect()->back();
      }
  }
  
  private function openAnEmployeeSalaryFiscalYear($emp_auto_id,$activity_date){
           
            if(!(new FiscalYearDataService())->checkAnEmployeeRunningFiscalYearIsAlreadyExist($emp_auto_id) && (new FiscalYearDataService())->checkAnEmployeeFiscalYearIsAlreadyExist($emp_auto_id)){               
                  $start_month = (new HelperController())->getMonthFromDateValue($activity_date);
                  $start_year = (new HelperController())->getYearFromDateValue($activity_date);
                  $fiscal_record = (new FiscalYearDataService())->setAnEmployeeFiscalYearDuration($emp_auto_id,$start_month,$start_year,$activity_date,0,Auth::user()->id);
            }   
  }
  
   // Employee Salary Status Update
  public function employeeNewActivityWithSalaryStatusUpdateRequest(Request $request){
     
    $emp = (new EmployeeDataService())->getAnEmployeeInfoByEmpAutoId($request->emp_auto_id);
    if($request->activity_type == ""){
      Session::flash('error', 'Please Select Activity Type & Job Status');
      return redirect()->back();
    }else if($emp ==  null)
    {
      Session::flash('error', 'Employee Not Found');
      return redirect()->back();
    } 
  
    if($request->salary_status){   
      (new EmployeeDataService())->updateAnEmployeeSalaryStatus($request->emp_auto_id,$request->salary_status,Auth::user()->id);
    }
   
      $insert = (new EmployeeRelatedDataService())->employeeWiseActivityRecordInsert(
      $request->emp_auto_id,
      $request->activity_remarks,
      $request->activity_description,
      $request->activity_type,
      $request->activity_date,
      (int) $request->salary_status,
      Auth::user()->id
    ); 

    if ($insert) {
        Session::flash('success', 'Successfuly Saved');
        return redirect()->route('employee.new.activity.insert.form');
      } else {
        Session::flash('error', 'Operation Failed, Please try again');
        return redirect()->back();
      }
  }



 
}
