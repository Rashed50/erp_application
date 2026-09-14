<?php

namespace App\Http\Controllers\Admin\InOut;

use App\Http\Controllers\Controller;

use App\Http\Controllers\Admin\CompanyProfileController;
use App\Http\Controllers\Admin\DailyWorkHistoryController;

use App\Http\Controllers\Admin\Helper\HelperController;
use App\Http\Controllers\Admin\EmployeeMultiProjectWorkHistoryController;
use App\Http\Controllers\Admin\MonthlyWorkHistoryController;
use App\Http\Controllers\DataServices\CompanyDataService;
use App\Http\Controllers\DataServices\EmployeeAdvanceDataService;
use App\Http\Controllers\DataServices\{EmployeeAttendanceDataService,WPSEmployeeDataService,AuthenticationDataService};
use App\Http\Controllers\DataServices\EmployeeDataService;
use App\Http\Controllers\DataServices\EmployeeRelatedDataService;
use App\Http\Controllers\DataServices\ProjectDataService;
use App\Http\Controllers\DataServices\SalaryProcessDataService;
use App\Enums\AttendanceTypeEnum;
use Illuminate\Http\Request;
use App\Models\EmployeeInOut;
use App\Models\EmployeeInfo;
use App\Models\MonthlyWorkHistory;
use App\Exports\EmpAttendanceExport;
use App\Exports\{EmpAttnInOutInAProjectExport,WPSEmployeMonthlyWorkRecordAsExcel};
use Maatwebsite\Excel\Facades\Excel;
use App\Models\EmployeeMultiProjectWorkHistory;
use Carbon\Carbon;
use DateTime;
use Exception;
use Session;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Jobs\CalculateDailyProjectSalaryJob;






// ALTER TABLE `employee_in_outs` ADD COLUMN `over_time` float DEFAULT 0;

class EmployeeInOutController extends Controller
{


    function __construct(){

        $this->middleware('permission:attendence-in',['only'=>['index','getListOfEmployeeWorkingInProjectForAttendanceIN']]);  // monthly attendance IN
        $this->middleware('permission:attendence-out',['only'=>['loadAttendanceOutForm','getAttendanceINAllEmployeeListForAttendaceOutAjaxRequest','multipleEmployeeAttendanceOutRequest']]); //  attendance out
        $this->middleware('permission:attendence-edit',['only'=>['employeeAttendanceInOutEditUI','employeeAttendanceInOutUpdate']]);
        $this->middleware('permission:attendance-processing',['only'=>['employeeAttendanceProcessForm','employeeAttendanceProcess']]);
        $this->middleware('permission:attendance-records-approval',['only'=>['loadMonthlyWorkRecordApprovalUI','searchMonthlyWorkRecordForApprovalAJaxRequest','approveOfMonthlyWorkRecords']]);  // monthly attendance approved by project manager
        // $this->middleware('permission:month_work_record_approval_edit',['only'=>['approveOfMonthlyWorkRecords','']]);  // monthly attendance approved by project manager

            // multi project work record
        $this->middleware('permission:employee_working_record_add|employee_working_record_searching',['only'=>['searchEmployeeMultipleProjectWorkRecord','employeeMultipleProjectWorkRecordSearchUI']]); // Monlty work record searching
        $this->middleware('permission:employee_working_record_add',['only' => ['insertAnEmployeeMultipleProjectWorkRecord']]); // new record insert
        $this->middleware('permission:employee_working_record_edit',['only' => ['updateAnEmployeeMultipleProjectWorkRecordRequest']]); // new record insert
        $this->middleware('permission:employee_working_record_delete',['only' => ['deleteAnEmployeeMultiProjectWorkRecordRequest']]); // new record insert
  }



  public function getAnEmployeeMultiProjectWorkRecord(Request $request)
  {


    $EmpMultiProjWorkHisConObj = new EmployeeMultiProjectWorkHistoryController();
    $workRecord = $EmpMultiProjWorkHisConObj->findIdWiseAnEmpMultiprojectWorkHistory($request->id);

    $month = (new CompanyDataService())->getAllMonth();
    $project = (new EmployeeRelatedDataService())->getAllProjectInfoForDropdown();

    $error = "data not Found!";
    return response()->json(['workRecord' => $workRecord, 'month' => $month, 'project' => $project, 'error' => $error]);
  }

  public function insrtEmpMultiProjectWorkRecord($startDate, $endDate, $emp_id, $proj_name, $month, $year, $totalDays, $totalHourTime, $totalOverTime)
  {
    return $insert = EmployeeMultiProjectWorkHistory::insert([
      'emp_id' => $emp_id,
      'project_id' => $proj_name,
      'month' => $month,
      'year' => $year,
      'total_day' => $totalDays,
      'total_hour' => $totalHourTime,
      'total_overtime' => $totalOverTime,
      'start_date' => $startDate,
      'end_date' => $endDate,
      'created_at' => Carbon::now()->toDateTimeString()
    ]);
  }

  /*
    |--------------------------------------------------------------------------
    |  DATABASE OPERATION
    |--------------------------------------------------------------------------
    */
  // Insert an employee multiple project work record from work record search modal option
  public function insertAnEmployeeMultipleProjectWorkRecord_old(Request $request)
  {

    try{


            $project_id = $request->proj_name;
            $startDate = Carbon::parse($request->startDate);
            $endDate =  Carbon::parse($request->endDate);
            $creator = Auth::user()->id;


            $month =  $request->month;
            $year =  $request->year;

            $new_hours =  $request->totalHourTime;
            $new_ot =  $request->totalOverTime;
            $new_days =  $request->total_days;

            //$findEmployee =  (new EmployeeDataService())->getAnEmployeeInfoByEmpId($request->emp_id);
            $findEmployee =  (new EmployeeDataService())->getAnEmployeeInfoTableDataByEmployeeIdAndBranchOfficeId($request->emp_id,Auth::user()->branch_office_id);

                  if ($findEmployee == null) {
                      return response()->json(['status' =>404, 'success' =>false,'message'=>'Employee Not Found Failed', 'error' => 'error']);
                  }
                  if((new SalaryProcessDataService())->checkAnEmployeeSalaryIsAlreadyPaid( $findEmployee->emp_auto_id,$month, $year)){
                    return response()->json(['status' =>404, 'success' =>false,'message'=>'Selected Month Salary Already Paid', 'error' => 'error']);
                  }
                 $record_total =(new EmployeeAttendanceDataService())->calculateAnEmployeeMonthlyTotalWorkDataFromMultiProjectWorkRecordByMonthYear($findEmployee->emp_auto_id, $month, $year);
                 $multiProjWorkRecord = (new EmployeeAttendanceDataService())->getAnEmployeeMultipleProjectWorkRecords($findEmployee->emp_auto_id, $month, $year,$project_id);

                 if ($multiProjWorkRecord) {
                  return response()->json(['status' =>404, 'success' =>false,'message'=>'This Project Work Record Exist', 'error' => 'error']);
                 }else if(($record_total->total_days + $new_days) > 31 ||  ($record_total->total_hours + $new_hours > 350)){
                  return response()->json(['status' =>404, 'success' =>false,'message'=>'Total Working Days or  Hours Invalid', 'error' => 'error']);
                 }

                  (new EmployeeAttendanceDataService())->saveAnEmployeeMultipleProjectWorkRecrd( $findEmployee->emp_auto_id, $month,  $year,$new_hours,$new_days, $project_id,$new_ot );
                  $record_total =(new EmployeeAttendanceDataService())->calculateAnEmployeeMonthlyTotalWorkDataFromMultiProjectWorkRecordByMonthYear($findEmployee->emp_auto_id, $month, $year);
                  $monthlyWorkHist = (new EmployeeAttendanceDataService())->getAnEmployeeMonthlyWorkRecord($findEmployee->emp_auto_id, $month, $year);
                  if ($monthlyWorkHist != null) {
                    (new EmployeeAttendanceDataService())->updateEmployeeMonthlyWorkRecord( $monthlyWorkHist->month_work_id,$record_total->total_hours, $record_total->total_over_time,$record_total->total_days, $project_id);
                  } else {
                    (new EmployeeAttendanceDataService())->saveAnEmployeeMonthlyWorkRecrd($findEmployee->emp_auto_id,$month,$year, $record_total->total_hours, $record_total->total_over_time, $record_total->total_days,$project_id);
                  }

                return response()->json(['status' =>200, 'success' =>true,'message'=> 'Successfully Added Work Record']);

          }catch(Exception $ex){
            return response()->json(['status' =>404, 'success' =>false,'message'=>'System Exception Occured '.$ex, 'error' => 'System Exception Occured']);

      }

  }

  public function insertAnEmployeeMultipleProjectWorkRecord(Request $request)
  {
      try{



          $project_id = $request->proj_name;
          $startDate = Carbon::parse($request->startDate);
          $endDate =  Carbon::parse($request->endDate);
          $creator = Auth::user()->id;
          $month =  $request->month;
          $year =  $request->year;
          $new_hours =  $request->totalHourTime;
          $new_ot =  $request->totalOverTime;
          $new_days =  $request->total_days;
          $paid_leave = $request->paid_leave;

            $findEmployee =  (new EmployeeDataService())->getAnEmployeeInfoTableDataByEmployeeIdAndBranchOfficeId($request->emp_id,Auth::user()->branch_office_id);

            if ($findEmployee == null) {
              return response()->json(['status' =>404, 'success' =>false,'message'=>'Employee Not Found Failed', 'error' => 'error']);
            }
            else if((new SalaryProcessDataService())->checkAnEmployeeSalaryIsAlreadyPaid( $findEmployee->emp_auto_id,$month, $year)){
              return response()->json(['status' =>404, 'success' =>false,'message'=>'This Month Salary Already Paid', 'error' => 'error']);
            }
            $multiProjWorkRecord = (new EmployeeAttendanceDataService())->getAnEmployeeMultipleProjectWorkRecords($findEmployee->emp_auto_id, $month, $year,$project_id);
            $record_total =(new EmployeeAttendanceDataService())->calculateAnEmployeeMonthlyTotalWorkDataFromMultiProjectWorkRecordByMonthYear($findEmployee->emp_auto_id, $month, $year);

            if ($multiProjWorkRecord) {
              return response()->json(['status' =>404, 'success' =>false,'message'=>'This Project Work Record Exist', 'error' => 'error']);
            }else if(($record_total->total_days + $new_days) > 31 ||  ($record_total->total_hours + $new_hours > 350)){
              return response()->json(['status' =>404, 'success' =>false,'message'=>'Total Working Days or  Hours Invalid', 'error' => 'error']);
            }

            (new EmployeeAttendanceDataService())->saveAnEmployeeMultipleProjectWorkRecrd( $findEmployee->emp_auto_id, $month,  $year,$new_hours,$new_days, $project_id,$new_ot ,$paid_leave);
            $record_total =(new EmployeeAttendanceDataService())->calculateAnEmployeeMonthlyTotalWorkDataFromMultiProjectWorkRecordByMonthYear($findEmployee->emp_auto_id, $month, $year);
            $monthlyWorkHist = (new EmployeeAttendanceDataService())->getAnEmployeeMonthlyWorkRecord($findEmployee->emp_auto_id, $month, $year);
            if ($monthlyWorkHist != null) {
              (new EmployeeAttendanceDataService())->updateEmployeeMonthlyWorkRecord( $monthlyWorkHist->month_work_id,$record_total->total_hours, $record_total->total_over_time,$record_total->total_days, $project_id,$record_total->paid_leave);
            } else {
              (new EmployeeAttendanceDataService())->saveAnEmployeeMonthlyWorkRecrd($findEmployee->emp_auto_id,$month,$year, $record_total->total_hours, $record_total->total_over_time, $record_total->total_days,$project_id,$record_total->paid_leave);
            }
            return response()->json(['status' =>200, 'success' =>true,'message'=> 'Successfully Added Work Record','re'=>$record_total]);

      }catch(Exception $ex){
          return response()->json(['status' =>404, 'success' =>false,'message'=>'Exception Occured '.$ex, 'error' => 'System Exception Occured']);
      }
  }

  /* =============== Employee Multiproject Monthly Work Update =============== */
   // multi project work record update using modal
  public function updateAnEmployeeMultipleProjectWorkRecordRequest(Request $request){



      $findEmployee =  (new EmployeeDataService())->getAnEmployeeInfoByEmpAutoId($request->modal_emp_auto_id);

      if ($findEmployee == null) {
             Session::flash('error', 'Employee Not Found Failed');
            return Redirect()->back();
      }
      $salary_is_paid = (new SalaryProcessDataService())->checkAnEmployeeSalaryIsAlreadyPaid( $request->modal_emp_auto_id, $request->modal_month, $request->modal_year);
    //  dd($request->all());
      if($salary_is_paid){
          Session::flash('error', 'This Month Salary Already Paid , Update Not Possible');
          return Redirect()->back();
      }else if ($request->modal_total_hour == null || $request->modal_total_hour == "") {
          Session::flash('error', 'Data Error, Updat Operation Failed');
          return Redirect()->back();
      } else  if ($request->modal_total_overtime == null || $request->modal_total_overtime == "") {
          Session::flash('error', 'Data Error, Updat Operation Failed');
          return Redirect()->back();
      } else if ($request->modal_total_day == null || $request->modal_total_day == "") {
          Session::flash('error', 'Data Error, Updat Operation Failed');
          return Redirect()->back();
      }

       $existing_record = (new EmployeeAttendanceDataService())->findAnEmpMultiProjectWorkRecordByRecordAutoId($request->modal_empwh_auto_id);
       $record_total =(new EmployeeAttendanceDataService())->calculateAnEmployeeMonthlyTotalWorkDataFromMultiProjectWorkRecordByMonthYear($request->modal_emp_auto_id, $request->modal_month, $request->modal_year);
       $update_days = $record_total->total_days + $request->modal_total_day - $existing_record->total_day;
       $update_hours = $record_total->total_hour + $request->modal_total_hour - $existing_record->total_hour;

       if($update_days > 31 ||  $update_hours > 350){
          Session::flash('error', 'Total Working Days or  Hours Invalid');
          return Redirect()->back();
       }

          $storePreviousData =  (new EmployeeAttendanceDataService())->insertAnEmployeeMultipleProjectWorkRecordUpdateHistory(
              $existing_record->emp_id,
              $existing_record->month,
              $existing_record->year,
              $existing_record->project_id,
              $existing_record->total_day,
              $existing_record->total_hour,
              $existing_record->total_overtime,
              $existing_record->total_amount,
              $existing_record->food_amount,
              $existing_record->other_amount,
              $existing_record->ot_amount,
              $existing_record->paid_leave,
              Auth::user()->branch_office_id,
              $existing_record->start_date,
              $existing_record->end_date,
              'update',
              Auth::user()->id
          );

          $update =  (new EmployeeAttendanceDataService())->updateAnEmployeeMultipleProjectWorkRecordWithAllColum(
          $request->modal_empwh_auto_id, $request->modal_project_name, $request->modal_month, $request->modal_year, $request->modal_total_day,
          $request->modal_total_hour, $request->modal_total_overtime,$request->modal_start_date,$request->modal_end_date,Auth::user()->id,$request->modal_paid_leave);

          $record_total =(new EmployeeAttendanceDataService())->calculateAnEmployeeMonthlyTotalWorkDataFromMultiProjectWorkRecordByMonthYear($request->modal_emp_auto_id, $request->modal_month, $request->modal_year);
          $monthlyWorkHist = (new EmployeeAttendanceDataService())->getAnEmployeeMonthlyWorkRecord($request->modal_emp_auto_id, $request->modal_month, $request->modal_year);
          if ($monthlyWorkHist != null) {
            (new EmployeeAttendanceDataService())->updateEmployeeMonthlyWorkRecord( $monthlyWorkHist->month_work_id,$record_total->total_hours, $record_total->total_over_time,$record_total->total_days, $request->modal_project_name,$record_total->paid_leave);
          } else {
            (new EmployeeAttendanceDataService())->saveAnEmployeeMonthlyWorkRecrd($request->modal_emp_auto_id, $request->modal_month, $request->modal_year, $record_total->total_hours, $record_total->total_over_time, $record_total->total_days,$request->modal_project_name,$record_total->paid_leave);
          }

           // work record update activities record
            (new AuthenticationDataService())->InsertLoginUserActivity(31,2, Auth::user()->id, $request->modal_emp_auto_id,$request->modal_total_hour);

          Session::flash('success', 'Successfully Updated');
          return Redirect()->back();

  }


  public function EmployeeMultiprojectMonthlyWorkRecordUpdate(Request $request)
  {

    $emp_auto_id = $request->emp_id;
    $empwh_auto_id = $request->empwh_auto_id;
    $proj_name = $request->proj_name;
    $startDate = Carbon::parse($request->startDate);
    $endDate =  Carbon::parse($request->endDate);
    $creator = Auth::user()->id;

    $fromDate = $request->startDate;

    $totalHourTime = $request->totalHourTime;
    $totalOverTime = $request->totalOverTime;

    $totalDays = $request->total_day;

    $proj_exit = EmployeeMultiProjectWorkHistory::where('empwh_auto_id', $empwh_auto_id)->first();



    $findEmployee = EmployeeInfo::where('emp_auto_id', $proj_exit->emp_id)->first();
    if ($findEmployee) {
      if ($totalDays != 0) {


        $update = EmployeeMultiProjectWorkHistory::where('empwh_auto_id', $empwh_auto_id)->update([
          'total_day' => $totalDays,
          'total_hour' => $totalHourTime,
          'total_overtime' => $totalOverTime,
          'month' => $proj_exit->month,
          'year' => $proj_exit->year,
          'project_id' => $proj_name,
          'start_date' => $startDate,
          'end_date' => $endDate,
          'updated_at' => Carbon::now()->toDateTimeString()
        ]);


        $total_hour = EmployeeMultiProjectWorkHistory::where('emp_id', $proj_exit->emp_id)
          ->where('month', $proj_exit->month)
          ->where('year', $proj_exit->year)
          ->sum('total_hour');

        $total_day = EmployeeMultiProjectWorkHistory::where('emp_id', $proj_exit->emp_id)
          ->where('month', $proj_exit->month)
          ->where('year', $proj_exit->year)
          ->sum('total_day');

        $total_overtime = EmployeeMultiProjectWorkHistory::where('emp_id', $proj_exit->emp_id)
          ->where('month', $proj_exit->month)
          ->where('year', $proj_exit->year)
          ->sum('total_overtime');

        $update = MonthlyWorkHistory::where('emp_id', $findEmployee->emp_auto_id)->where('month_id', $proj_exit->month)
          ->where('year_id', $proj_exit->year)->update([
            'total_hours' => $total_hour,
            'overtime' => $total_overtime,
            'total_work_day' => $total_day,
            'month_id' => $proj_exit->month,
            'year_id' => $proj_exit->year,
             'project_id' => $proj_name,
            'updated_at' => Carbon::now()
          ]);

        return response()->json(['success' => 'Successfully Updated Employee Multi Project Work Recors']);
      } else {
        return response()->json(['error' => 'Days Not Found!']);
      }
    } else {
      return response()->json(['error' => 'Employee Not Found!']);
    }
  }

 //  Employee Daily Attendance IN

 public function employeeAttendanceTimeINInsertRequest(Request $request)
  {
        try{
           // dd($request->all());
            $emp_list = $request->emp_auto_id;
            $creator = Auth::user()->id;
            $catchDate = $request->date;

            if($catchDate == null || $request->entry_in_time == null){
              Session::flash('error', 'Please Input All Required Data');
              return redirect()->back();
            }

            $dayMonthYear = (new HelperController())->getDayMonthAndYearFromDateValue($catchDate);
            $working_day = $dayMonthYear[0] ;
            $month = $dayMonthYear[1] ;
            $year = $dayMonthYear[2] ;

            $project_id = $request->attn_project_id;
            //$in_time = (float) $request->entry_in_time;
            $in_time =  number_format((float)$request->entry_in_time, 1, '.', '');

            //$attendance_status = "aw";

            $attendance_status = $request->attendance_status;
            $attendance_type = $attendance_status == "AW" ? 1:3 ;//AttendanceTypeEnum ::Working->value ? 1:3 ;

            // if ($request->attendance_status == 'tl' || $request->attendance_status == 'sl' || $request->attendance_status == 'hp') {
            //   $attendance_status = $request->attendance_status;
            // } else {
            //   $attendance_status = $request->attendance_status;
            // }


            $isSuccess = false;
            $shift = $request->has('night_shift') ? true : false;

            foreach ($emp_list as $emp_auto_id) {

              if ($request->has('entry_in_checkbox-' . $emp_auto_id)) {
                  $isSuccess = true;
                  //  $attendance_type = $attendance_status == 'aw' ? 1:3 ;
                    (new EmployeeAttendanceDataService())->insertAnEmployeeAttendanceInInformation($emp_auto_id,$project_id,$working_day,$month,$year,$shift
                    ,$in_time,$attendance_type,$attendance_status,$creator,$catchDate,Carbon::now(),Auth::user()->branch_office_id);

              }
            }


            $total_emp = (new EmployeeDataService())->countTotalActiveEmployeesInAProject($project_id,$shift);
            $total_present =  (int) (new EmployeeAttendanceDataService())->countNumberOfEmployeesPresentInTheProject($project_id,$working_day,$month,$year,$shift);
            (new EmployeeAttendanceDataService())->insertDailyAttendanceSummaryRecord($project_id,$shift,$working_day,$month,$year,$total_emp,$total_present, $catchDate,Auth::user()->branch_office_id);


            if($isSuccess){
              Session::flash('success', 'Successfully Added Employee Attendance');
              return redirect()->back();
            }else {
              Session::flash('error', 'Please Select Attendant Employee ');
              return redirect()->back();
            }

        }catch(Exception $ex){
            Session::flash('error', 'System Exception '.$ex);
            return redirect()->back();

        }

  }

  /* =============== Employee Attendence Out Insert By AJAX FOR SINGLE EMPLOYEE But not used now =============== */
  public function outTimeInsert(Request $request)
  {

    $empInOutId = $request->id;
    $out_time = $request->out_time;

    $attend = (new EmployeeAttendanceDataService())->getAnEmployeeAttendanceInOutRecord($empInOutId);
    $daily_work_hours = ($out_time - $attend->emp_io_entry_time);
    if ($daily_work_hours < 0) {
      $daily_work_hours += 24;
    }
    $isSuccess = (new EmployeeAttendanceDataService())->updateEmployeeDailyAttendanceByAttendanceOut($empInOutId, $out_time, $daily_work_hours);
    if ($isSuccess) {
      return response()->json(['success' => 'Successfully Updated Employee Out Time']);
    } else {
      return response()->json(['error' => 'Opps! Please Try Again']);
    }
  }

    /* =============== Employee Attendence Out Insert By AJAX FOR MULTIPLE EMPLOYEE =============== */
   public function multipleEmployeeAttendanceOutRequest(Request $request){


         if(!$request->has('emp_io_id_list')){
          Session::flash('error', 'Operation Failed, Please Try Again.');
          return redirect()->back();
            }

             CalculateDailyProjectSalaryJob::dispatch(Carbon::today());
            CalculateDailyProjectSalaryJob::dispatch(Carbon::yesterday());

        $attendance_inout_id_list = $request->emp_io_id_list;

          $counter = 0;
        foreach ($attendance_inout_id_list as $emp_io_id) {


          if ($request->has('entry_out_checkbox-' . $emp_io_id)) {

            $attend_record = (new EmployeeAttendanceDataService())->getAnEmployeeAttendanceInOutRecord($emp_io_id);
            $out_time = (float)  $request->atten_out_time;   // multiple employee out time in one input field
            $daily_work_hours = ($out_time - $attend_record->emp_io_entry_time);
            $isFriday = (new HelperController())->checkThisDayIsFriday($attend_record->emp_io_year.'-'. $attend_record->emp_io_month.'-'.$attend_record->emp_io_date);



            if ($daily_work_hours < 0) {
              $daily_work_hours += 24;
            }
             (new EmployeeAttendanceDataService())->updateEmployeeDailyAttendanceByAttendanceOut($emp_io_id, $out_time, $daily_work_hours,$isFriday);
             $counter++;
          }

         }

        if($counter){
          Session::flash('success','Successfully Updated');
          return redirect()->back();
        }else {
          Session::flash('error','Some Operation Failed, Try Again ');
          return redirect()->back();
        }


  }


  /* Ajax method */
// Attendance Processing AJAX Request
 public function employeeAttendanceProcess(Request $request)
  {

    // try{


        $month = $request->month_id;
        $year = $request->year;
        $working_shift = $request->working_shift;
        $project_id = $request->project_id;
        if($request->process_type == 1){
          // emp id wise process
          return  $this->processEmployeeAttendanceInOutByEmployeeID($request->employee_id, $month,$year);
        }else if($request->process_type == 2){
          // project wise project
         return $this->processEmployeeAttendanceByProjectName($project_id,$month,$year,$working_shift);
        }
        return json_encode(['success' => false,'status'=>403,'message'=>"Operation Failed, Please Try Again"]);

    // }catch(Exception $ex){
    //     return json_encode(['success' => false,'status'=>403,'message'=>"System Exception Found, Reload and Try Again",'errpr'=>'error','system_error'=>$ex]);
    // }

  }

  public function processEmployeeAttendanceInOutByEmployeeID($multiple_emp_Id,$month,$year){


        $allEmplId = explode(",", $multiple_emp_Id);
        $allEmplId = array_unique($allEmplId); // remove multiple same empl ID

        $emp_list = (new EmployeeDataService())->getEmployeesInfoWithSalaryDetailForEmployeeAdvanceByMultipleEmpId( $allEmplId);

        foreach ($emp_list as $emp) {

            $workInOutRecords =  (new EmployeeAttendanceDataService())->getAnEmployeeAttendanceInOutTotalHoursAndDaysGroupByProjects($emp->emp_auto_id, $month, $year);
            if (count($workInOutRecords) == 0) { continue; }

                $number_of_days_in_month =(new HelperController())->getNumberOfDaysInMonthAndYear($month, $year);
                $holidays =(new HelperController())->getAMonthHolidaysAsArray($month, $year);

               // $arecord = (new EmployeeAttendanceDataService())->getAWPSEmployeeFullMonthTotalWorkingInfoForSalaryProcessingMonthlyWorkRecord($emp->emp_auto_id, $month, $year);
                $month_working_details = (new EmployeeAttendanceDataService())->calculateAnEmployeeMonthlyWorkSummaryUsingAttendanceINOUTRecords($emp->emp_auto_id,
                         $month, $year,$holidays,$number_of_days_in_month);

                $project_wise_working_summary = $month_working_details['project_wise_summary'];

                  foreach ($project_wise_working_summary as $aproject_summary) {


                    $proj_exit = (new EmployeeAttendanceDataService())->getAnEmployeeMultipleProjectWorkRecords($emp->emp_auto_id, $month, $year, $aproject_summary['proj_id']);

                    if ($proj_exit != null) {
                      (new EmployeeAttendanceDataService())->updateEmployeeMultipleProjectWorkRecord($proj_exit->empwh_auto_id,$aproject_summary['total_hours'],$aproject_summary['total_overtime'],$aproject_summary['total_working_days'],$aproject_summary['total_paid_leave']);
                    } else {
                      (new EmployeeAttendanceDataService())->saveAnEmployeeMultipleProjectWorkRecrd($emp->emp_auto_id,$month,$year,
                      $aproject_summary['total_hours'],$aproject_summary['total_working_days'],$aproject_summary['proj_id'], $aproject_summary['total_overtime'],$aproject_summary['total_paid_leave']);
                    }


                  }

                  $last_record = (new EmployeeAttendanceDataService())->getAnEmployeeMonthlyAttendanceLastWorkingRecordInAMonth($emp->emp_auto_id, $month, $year);

                  $last_working_project =  $emp->project_id;

                  if($last_record != null){
                      $last_working_project =  $last_record->proj_id;
                  }

                $record_total =(new EmployeeAttendanceDataService())->calculateAnEmployeeMonthlyTotalWorkDataFromMultiProjectWorkRecordByMonthYear($emp->emp_auto_id, $month, $year);
                $monthlyWorkHist = (new EmployeeAttendanceDataService())->getAnEmployeeMonthlyWorkRecord($emp->emp_auto_id, $month, $year);

                if($monthlyWorkHist != null) {
                    (new EmployeeAttendanceDataService())->updateEmployeeMonthlyWorkRecord( $monthlyWorkHist->month_work_id,$record_total->total_hours, $record_total->total_over_time,$record_total->total_days, $last_working_project,$record_total->paid_leave);
                } else {
                    (new EmployeeAttendanceDataService())->saveAnEmployeeMonthlyWorkRecrd($emp->emp_auto_id,$month,$year, $record_total->total_hours, $record_total->total_over_time, $record_total->total_days,$last_working_project,$record_total->paid_leave);
                }
        }
        return json_encode(['success' => true,'status'=>200,'message'=>"Successfully Completed"]);

        //   $allEmplId = explode(",", $multiple_emp_Id);
        //   $allEmplId = array_unique($allEmplId); // remove multiple same empl ID

        // $emp_list = (new EmployeeDataService())->getEmployeesInfoWithSalaryDetailForEmployeeAdvanceByMultipleEmpId( $allEmplId);
        // foreach ($emp_list as $emp) {


        //     $workInOutRecords =  (new EmployeeAttendanceDataService())->getAnEmployeeAttendanceInOutTotalHoursAndDaysGroupByProjects($emp->emp_auto_id, $month, $year);

        //     if (count($workInOutRecords) > 0) {
        //           $over_time = 0.0;
        //           $total_hours = 0;
        //           $total_days = 0;

        //           foreach ($workInOutRecords as $iorecord) {

        //             $total_hours += $iorecord->total_work_hours;
        //             $over_time += $iorecord->overtime;
        //             $total_days += $iorecord->total_days;

        //             $proj_exit = (new EmployeeAttendanceDataService())->getAnEmployeeMultipleProjectWorkRecords($emp->emp_auto_id, $month, $year, $iorecord->proj_id);
        //             if ($proj_exit != null) {
        //               (new EmployeeAttendanceDataService())->updateEmployeeMultipleProjectWorkRecord($proj_exit->empwh_auto_id,$iorecord->total_work_hours,$iorecord->overtime,$iorecord->total_days);
        //             } else {
        //               (new EmployeeAttendanceDataService())->saveAnEmployeeMultipleProjectWorkRecrd($emp->emp_auto_id,$month,$year,$iorecord->total_work_hours,$iorecord->total_days,$iorecord->proj_id, $iorecord->overtime);
        //             }
        //           }

        //           $last_working_project =  $emp->project_id;
        //           $last_record = (new EmployeeAttendanceDataService())->getAnEmployeeMonthlyAttendanceLastWorkingRecordInAMonth($emp->emp_auto_id, $month, $year);
        //           if($last_record != null){
        //               $last_working_project =  $last_record->proj_id;
        //           }

        //         $record_total =(new EmployeeAttendanceDataService())->calculateAnEmployeeMonthlyTotalWorkDataFromMultiProjectWorkRecordByMonthYear($emp->emp_auto_id, $month, $year);
        //         $monthlyWorkHist = (new EmployeeAttendanceDataService())->getAnEmployeeMonthlyWorkRecord($emp->emp_auto_id, $month, $year);
        //         if ($monthlyWorkHist != null) {
        //           (new EmployeeAttendanceDataService())->updateEmployeeMonthlyWorkRecord( $monthlyWorkHist->month_work_id,$record_total->total_hours, $record_total->total_over_time,$record_total->total_days, $last_working_project);
        //         } else {
        //           (new EmployeeAttendanceDataService())->saveAnEmployeeMonthlyWorkRecrd($emp->emp_auto_id,$month,$year, $record_total->total_hours, $record_total->total_over_time, $record_total->total_days,$last_working_project);
        //         }

        //     }
        // }
        // return json_encode(['success' => true,'status'=>200,'message'=>"Successfully Completed"]);
  }

  public function processEmployeeAttendanceByProjectName($project_id,$month,$year, $working_shift){

       // $aproval_status = (new EmployeeAttendanceDataService())->checkIsMonthlyAttendanceRecordApprovalStatus($request->project_id,$month,$year);
        // if($aproval_status == false){
        //   return json_encode(['success' => false,'status'=>403,'message'=>"Work Record Not Verified",'error'=>'error']);
        //   }

        $emp_list = (new EmployeeAttendanceDataService())->getListOfEmployeesThoseWorkedInThisProjectForExportAttendanceInOut($project_id,$month,$year, $working_shift);

        foreach ($emp_list as $emp) {
          $workInOutRecords =  (new EmployeeAttendanceDataService())->getAnEmployeeAttendanceInOutTotalBasicAndOvertimeByProjectId($emp->emp_auto_id,$project_id, $month, $year);

          if (count($workInOutRecords) > 0) {
              foreach ($workInOutRecords as $iorecord) {
                $proj_exit = (new EmployeeAttendanceDataService())->getAnEmployeeMultipleProjectWorkRecords($emp->emp_auto_id, $month, $year, $iorecord->proj_id);
                if ($proj_exit != null) {
                   (new EmployeeAttendanceDataService())->updateEmployeeMultipleProjectWorkRecord($proj_exit->empwh_auto_id,$iorecord->total_work_hours, $iorecord->overtime,$iorecord->total_days);
                } else {
                  (new EmployeeAttendanceDataService())->saveAnEmployeeMultipleProjectWorkRecrd($emp->emp_auto_id,  $month,$year, $iorecord->total_work_hours, $iorecord->total_days, $iorecord->proj_id, $iorecord->overtime );
                }
              }
              $last_working_project =  $project_id;
              $last_record = (new EmployeeAttendanceDataService())->getAnEmployeeMonthlyAttendanceLastWorkingRecordInAMonth($emp->emp_auto_id, $month, $year);
              if($last_record != null){
                  $last_working_project =  $last_record->proj_id;
              }

            $record_total =(new EmployeeAttendanceDataService())->calculateAnEmployeeMonthlyTotalWorkDataFromMultiProjectWorkRecordByMonthYear($emp->emp_auto_id, $month, $year);
            $monthlyWorkHist = (new EmployeeAttendanceDataService())->getAnEmployeeMonthlyWorkRecord($emp->emp_auto_id, $month, $year);
            if ($monthlyWorkHist != null) {
              (new EmployeeAttendanceDataService())->updateEmployeeMonthlyWorkRecord( $monthlyWorkHist->month_work_id,$record_total->total_hours, $record_total->total_over_time,$record_total->total_days, $last_working_project);
            } else {
              (new EmployeeAttendanceDataService())->saveAnEmployeeMonthlyWorkRecrd($emp->emp_auto_id,$month,$year, $record_total->total_hours, $record_total->total_over_time, $record_total->total_days,$last_working_project);
            }

          }
        }
        return json_encode(['success' => true,'status'=>200,'message'=>"Successfully Completed "]);

  }




  // Attendance Record Searching Ajax Request From Attendance Edit Form
  public function employeeAttendanceInOutRecordSearch(Request $request)
  {

    $employee_id = $request->employee_id;
    $day =  (int)date('d', strtotime($request->date));
    $month = (int) date('m', strtotime($request->date));
    $year = (int) date('Y', strtotime($request->date));

      $attendance_record = (new EmployeeAttendanceDataService())->searchEmployeeAttendanceRecord($request->project_id, $request->employee_id, $day, $month, $year);

    if ($attendance_record) {
      $attendance_record->emp_io_entry_date = Carbon::parse($attendance_record->emp_io_entry_date)->format('Y-m-d');

      return response()->json(['success' => true, 'status' => 200, 'data' => $attendance_record]);
    } else {
      return response()->json(['success' => false, 'status' => 404, 'error' => 'Attendance Record Not Found']);
    }
  }





  public function employeeAttendanceInOutRecordDelete(Request $request)
  {

    // $emp_io_id = $request->emp_io_id;
    // $isSuccess = (new EmployeeAttendanceDataService())->deleteEmployeeDailyAttendanceRecord($emp_io_id);
    // if ($isSuccess) {
    //   return response()->json(['success' => true, 'status' => 200]);
    // } else {
    //   return response()->json(['success' => false, 'status' => 404, 'error' => 'Opps! Please Try Again']);
    // }

     try{

            if($request->operation_type == 1){
                // delete single employee attendance
                $emp_io_id = $request->emp_io_id;
                $isSuccess = (new EmployeeAttendanceDataService())->deleteEmployeeDailyAttendanceRecord($emp_io_id);
            }else if($request->operation_type == 2){

              // delete multiple employee attendance
                $day_month_year = (new HelperController())->getDayMonthAndYearFromDateValue($request->attendance_date);
                $isSuccess = (new EmployeeAttendanceDataService())->deleteEmployeeDailyAttendanceByProjectDateAndWorkingshift(
                    $request->project_id,$day_month_year[0],$day_month_year[1],$day_month_year[2],$request->working_shift);

            }


            if ($isSuccess) {
                return response()->json(['success' => true, 'status' => 200]);
            } else {
                return response()->json(['success' => false, 'status' => 404, 'error' => 'Opps! Please Try Again']);
            }
        }catch(Exception $ex){
            return response()->json(['success' => false, 'status' => 500, 'error' => 'Operation Failed, Try Again']);

        }


  }


  // AJAX Request for update employee attendance record
  public function employeeAttendanceInOutUpdate(Request $request)
  {
     try{

          $emp_io_id  = $request->emp_io_id;
          $emp_io_entry_time = $request->emp_io_entry_time;
          $emp_io_out_time = $request->emp_io_out_time;
          $daily_work_hours = $request->emp_io_out_time - $request->emp_io_entry_time;
          $emp_io_date = $request->emp_io_date;

          $daily_work_hours = $daily_work_hours < 0 ? $daily_work_hours+24 : $daily_work_hours; // nightshit duty if hours <0

          $attend_record = (new EmployeeAttendanceDataService())->getAnEmployeeAttendanceInOutRecord($emp_io_id);
          if($attend_record == null){
            return response()->json(['success' => false, 'status' => 404, 'error' => 'error','message'=>'Attendance Record Not Found ']);
          }
           $is_friday = (new HelperController())->checkThisDayIsFriday($attend_record->emp_io_year.'-'. $attend_record->emp_io_month.'-'.$attend_record->emp_io_date);
           $isSuccess = (new EmployeeAttendanceDataService())->updateEmployeeDailyAttendanceRecord($emp_io_id, $emp_io_entry_time, $emp_io_out_time, $daily_work_hours,$is_friday);

          if ($isSuccess) {
            return response()->json(['success' => true, 'status' => 200,'message'=>'Successfully Updated']);
          } else {
            return response()->json(['success' => false, 'status' => 404, 'error' => 'error','message'=>'Update Operation Failed ']);
          }

     }catch(Exception $ex){
           return response()->json(['success' => false, 'status' => 404, 'error' => 'error','message'=>'System Operation Failed '.$ex]);
     }
  }


  public function outTimeProcessEntryList(Request $request)
  {


    $catchDate = $request->date;
    $date = date('d', strtotime($catchDate));
    $month = date('m', strtotime($catchDate));
    $year = date('Y', strtotime($catchDate));



    $getAll = EmployeeInOut::where('employee_in_outs.emp_io_date', $date)
      ->where('employee_in_outs.emp_io_month', $month)
      ->where('employee_in_outs.emp_io_year', $year)
      ->where('employee_in_outs.emp_io_out_time', 0.0)
      ->leftjoin('employee_infos', 'employee_in_outs.emp_id', '=', 'employee_infos.emp_auto_id')
      ->orderBy('employee_infos.employee_id')
      ->get();

    if ($getAll == true) {
      return response()->json(["entryList" => $getAll]);
    } else {
      return response()->json(['error' => "Data Not Found!"]);
    }
  }




  public function projectWiseInOutList(Request $request)
  {

    $project = (new EmployeeRelatedDataService())->getAllProjectInfoForDropdown();


    $proj_id = $request->proj_name;

    $catchDate = $request->date;
    $date = date('d', strtotime($catchDate));
    $month = date('m', strtotime($catchDate));
    $year = date('Y', strtotime($catchDate));

    $getAll = EmployeeInOut::where('employee_infos.project_id', $proj_id)
      ->where('employee_in_outs.emp_io_date', $date)
      ->where('employee_in_outs.emp_io_month', $month)
      ->where('employee_in_outs.emp_io_year', $year)
      ->leftjoin('employee_infos', 'employee_in_outs.emp_id', '=', 'employee_infos.emp_auto_id')
      ->get();

    if ($getAll) {
      return view('admin.employee-in-out.proj-wise-list', compact('project', 'getAll'));
    } else {
      return view('admin.employee-in-out.proj-wise-list', compact('project'));
    }
  }


// An employee mutliple project working record delete request
  public function deleteAnEmployeeMultiProjectWorkRecordRequest($empwh_auto_id)
    {
      try{

            $updated_by = Auth::user()->id;
            $multyProjWorkRecord =(new EmployeeAttendanceDataService())->findAnEmpMultiProjectWorkRecordByRecordAutoId($empwh_auto_id);
            $monthWorkRecord = (new EmployeeAttendanceDataService())->getAnEmployeeMonthlyWorkRecord($multyProjWorkRecord->emp_id, $multyProjWorkRecord->month, $multyProjWorkRecord->year);

            if(  $multyProjWorkRecord == null ){
                return response()->json(['status' =>404, 'success' =>false,'message'=>'Record Not Found, Please Reload', 'error' => 'error']);
            }
            else if( (new SalaryProcessDataService())->checkAnEmployeeSalaryIsAlreadyPaid( $multyProjWorkRecord->emp_id,$multyProjWorkRecord->month, $multyProjWorkRecord->year)){
                return response()->json(['status' =>404, 'success' =>false,'message'=>'This Month Salary Already Paid', 'error' => 'error']);
            }
            else if($monthWorkRecord == null){
               (new EmployeeAttendanceDataService())->deleteAnEmpMultiprojectWorkRecordByRecordAutoId($empwh_auto_id);
              return response()->json(["status" =>404,"success" =>false, 'error'=>'error',"message" =>  "Operation Failed, Please Try Again"]);
            }

              $storePreviousData =  (new EmployeeAttendanceDataService())->insertAnEmployeeMultipleProjectWorkRecordUpdateHistory(
                  $multyProjWorkRecord->emp_id,
                  $multyProjWorkRecord->month,
                  $multyProjWorkRecord->year,
                  $multyProjWorkRecord->project_id,
                  $multyProjWorkRecord->total_day,
                  $multyProjWorkRecord->total_hour,
                  $multyProjWorkRecord->total_overtime,
                  $multyProjWorkRecord->total_amount,
                  $multyProjWorkRecord->food_amount,
                  $multyProjWorkRecord->other_amount,
                  $multyProjWorkRecord->ot_amount,
                  $multyProjWorkRecord->paid_leave,
                  Auth::user()->branch_office_id,
                  $multyProjWorkRecord->start_date,
                  $multyProjWorkRecord->end_date,
                  'delete',
                  Auth::user()->id
              );

            $noOfProWorkInThisMonth = (new EmployeeAttendanceDataService())->countAnEmployeeWorkingProjectThisMonth($multyProjWorkRecord->emp_id, $multyProjWorkRecord->month, $multyProjWorkRecord->year);

            $isSuccess = (new EmployeeAttendanceDataService())->deleteAnEmpMultiprojectWorkRecordByRecordAutoId($empwh_auto_id);

            if ($noOfProWorkInThisMonth == 1) {
                (new EmployeeAttendanceDataService())->deleteAnEmployeeMonthlyWorkRecordByEmpAutoIdMonthAndYear($multyProjWorkRecord->emp_id, $multyProjWorkRecord->month, $multyProjWorkRecord->year);
                return response()->json(["status" =>200,"success" =>true,"message" =>"Successfully Completed"]);
            } else {

                $all_records = (new EmployeeAttendanceDataService())->getAnEmployeeMultiprojectWorkRecordsOnly($multyProjWorkRecord->emp_id, $multyProjWorkRecord->month, $multyProjWorkRecord->year);

                $salary_project_id = 0;
                $max_working_days = -1;
                foreach($all_records as $arecord){
                        if($max_working_days < $arecord->total_day){
                          $max_working_days = $arecord->total_day;
                          $salary_project_id = $arecord->project_id;
                        }
                  }

                    $record_total = (new EmployeeAttendanceDataService())->calculateAnEmployeeMonthlyTotalWorkDataFromMultiProjectWorkRecordByMonthYear($multyProjWorkRecord->emp_id,$multyProjWorkRecord->month,$multyProjWorkRecord->year);
                    $monthlyWorkHist = (new EmployeeAttendanceDataService())->getAnEmployeeMonthlyWorkRecord($multyProjWorkRecord->emp_id,$multyProjWorkRecord->month,$multyProjWorkRecord->year);
                    if ($monthlyWorkHist != null) {
                        (new EmployeeAttendanceDataService())->updateEmployeeMonthlyWorkRecord( $monthlyWorkHist->month_work_id,$record_total->total_hours, $record_total->total_over_time,$record_total->total_days, $salary_project_id,$record_total->paid_leave );
                    } else {
                        (new EmployeeAttendanceDataService())->saveAnEmployeeMonthlyWorkRecrd($emp_auto_id,$month,$year, $record_total->total_hours, $record_total->total_over_time, $record_total->total_days,$salary_project_id,$record_total->paid_leave);
                    }

              return response()->json(["status" =>200,"success" =>true,"message" =>  "Successfully Completed",'data'=>$multyProjWorkRecord  ]);
            }
          }catch(Exception $ex){
            return response()->json(["status" =>404,"success" =>false, 'error'=>'error',"message" =>  "Operation Failed with System Exception",'data'=>$multyProjWorkRecord ]);
          }

  }


  // Muti Employee Attendance Update EMP List AJAX Request
  public function searchMultipleEmpAttendanceRecordForUpdate(Request $request){



      $proj_id =   $request->proj_id;
      $allEmplId = explode(",", $request->emp_ids);
      $allEmplId = array_unique($allEmplId); // remove multiple same empl ID


      $attendance_date = $request->attendance_date;
      $day = (int)  date('d', strtotime($attendance_date));
      $month = (int)  date('m', strtotime($attendance_date));
      $year = (int)  date('Y', strtotime($attendance_date));

        if( $request->emp_ids != null && count($allEmplId) > 0){
             $records1 = EmployeeInOut::where('proj_id', $proj_id)
                 ->where('employee_in_outs.emp_io_date', $day)
                 ->where('employee_in_outs.emp_io_month', $month)
                 ->where('employee_in_outs.emp_io_year', $year)
                 ->where('employee_in_outs.emp_io_shift', $request->working_shift)
                 ->whereIn('employee_infos.employee_id', $allEmplId)
                ->leftjoin('employee_infos', 'employee_in_outs.emp_id', '=', 'employee_infos.emp_auto_id')
                ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->orderBy('employee_infos.employee_id')
                ->get();

     }else {
         $records1 = EmployeeInOut::where('proj_id', $proj_id)
                 ->where('employee_in_outs.emp_io_date', $day)
                 ->where('employee_in_outs.emp_io_month', $month)
                 ->where('employee_in_outs.emp_io_year', $year)
                 ->where('employee_in_outs.emp_io_shift', $request->working_shift)
                ->leftjoin('employee_infos', 'employee_in_outs.emp_id', '=', 'employee_infos.emp_auto_id')
                ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->orderBy('employee_infos.employee_id')
                ->get();
     }

    if (count($records1) > 0) {
      return response()->json(["status" =>200,"success" =>true,"data" => $records1]);
    } else {
      return  response()->json(["status"=>403,"success" =>true,"error" =>"Record Not Found","data"=>null]);
    }
  }

  // MULTIPLE EMPLOYEE IN OUT ATTENDANCE RECORD UPDATE  REQUEST
  public function multipleEmployeeAttendanceUpdateRequest(Request $request){
      try{

        if(!$request->has('emp_io_id_list')){
          Session::flash('error', 'Operation Failed, Please Try Again.');
          return redirect()->back();
        }
        $attendance_inout_id_list = $request->emp_io_id_list;

        $updated_by = Auth::user()->id;
        $counter = 0;
        $isFriday = (new HelperController())->checkThisDayIsFriday($request->multiple_attn_selected_date);
        foreach ($attendance_inout_id_list as $emp_io_id) {

          if ($request->has('entry_out_checkbox-' . $emp_io_id)) {

            $out_time = (float)  $request->atten_out_time;   // multiple employee out time in one input field
            $in_time = (float)  $request->atten_in_time;   // multiple employee out time in one input field
            $daily_work_hours = ($out_time - $in_time);
            if ($daily_work_hours < 0) {
              $daily_work_hours += 24;
            }
            (new EmployeeAttendanceDataService())->updateAnEmployeeDailyAttendanceRecordFromMultiEmployeeUpdateRequest($emp_io_id, $in_time, $out_time, $daily_work_hours,$updated_by,$isFriday);
            $counter++;
          }

        }
        if($counter){
          Session::flash('success','Successfully Updated');
          return redirect()->back();
        }else {
          Session::flash('error','Some Operation Failed, Try Again ');
          return redirect()->back();
        }

      }catch(Exception $ex){
        Session::flash('error','Some Operation Failed, Try Again ');
        return redirect()->back();
      }

  }



     /*
    |--------------------------------------------------------------------------
    |  Montly Attendance Work Record Approval Section
    |--------------------------------------------------------------------------
    */

   // load Monthly Attendance Work Record Approval ui
    public function loadMonthlyWorkRecordApprovalUI(){


      $user = Auth::user();
      $current_month = (new HelperController())->getCurrentMonthIntValue();
      $year = date('Y');
      if($user->hasRole('Admin')){
        $project_ids = (new ProjectDataService())->getAllActiveProjectIDs();
      }else {
        $project_ids = (new ProjectDataService())->getLoginUserAccessPermissionProjectIDs($user->id);
      }

      $records =  (new EmployeeAttendanceDataService())->getProjectWiseTotalWorkHoursSummaryForMonthlyWorkRecordApproval($project_ids,$current_month ,$year);

      foreach($records as $arecord){


        $saved = (new EmployeeAttendanceDataService())->insertAttendanceApprovalRecord($arecord->proj_id, $current_month, $year,0,Carbon::now());
        $db_record = (new EmployeeAttendanceDataService())->getAttendanceApprovalRecord($arecord->proj_id, $current_month, $year);
        $arecord->atten_appro_auto_id = $db_record->atten_appro_auto_id;
        $arecord->approval_status = $db_record->approval_status;
        $arecord->approved_by_id = $db_record->approved_by_id;

      }
      return view('admin.employee-in-out.montly_atten_work_record_approval', compact('records'));

    }
    // search record
    public function searchMonthlyWorkRecordForApprovalAJaxRequest(Request $request){
      try{

        $user = Auth::user();
        $month =  $request->month;
        $year = $request->year;
        if($user->hasRole('Admin')){
          $project_ids = (new ProjectDataService())->getAllActiveProjectIDs();
        }else {
          $project_ids = (new ProjectDataService())->getLoginUserAccessPermissionProjectIDs($user->id);
        }
        $records =  (new EmployeeAttendanceDataService())->getProjectWiseTotalWorkHoursSummaryForMonthlyWorkRecordApproval($project_ids,$month ,$year);

        foreach($records as $arecord){
          $saved = (new EmployeeAttendanceDataService())->insertAttendanceApprovalRecord($arecord->proj_id, $month, $year,0,Carbon::now());
          $db_record = (new EmployeeAttendanceDataService())->getAttendanceApprovalRecord($arecord->proj_id, $month, $year);
          $arecord->atten_appro_auto_id = $db_record->atten_appro_auto_id;
          $arecord->approval_status = $db_record->approval_status;
          $arecord->approved_by_id = $db_record->approved_by_id;
        }
        return response()->json(["status" =>200,"success" =>true,'data'=>$records]);


      }catch(Exception $ex){
        return response()->json(["status" =>404,"success" =>false,'message'=>'Operation Failed with Exception','error'=>'error','ex'=>$ex]);
      }
    }

    public function approveOfMonthlyWorkRecords(Request $request){
        try{

            $saved = (new EmployeeAttendanceDataService())->updateMonthlyAttendanceWorkRecordsByPendingOrApproved($request->atten_appro_auto_id,$request->approved_status,Auth::user()->id);
            if($saved){
              return response()->json(["status" =>200,"success" =>true,'message'=>'Successfully Completed']);
            }else {
              return response()->json(["status" =>404,"success" =>false,'message'=>'Operation Failed','error'=>'error']);
            }

        }catch(Exception $ex){
          return response()->json(["status" =>404,"success" =>false,'message'=>'Operation Failed with Exception','error'=>'error']);
        }

    }


  /*
    |--------------------------------------------------------------------------
    |  BLADE OPERATION
    |--------------------------------------------------------------------------
    */

 // Employee Attendance IN  UI Form
  public function index()
  {


    $project = (new ProjectDataService())->getLoginUserAssingedProjectForDropdownList(Auth::user()->id);
   // return view('admin.employee-in-out.employee-attendance-in', compact('project'));
    $allow_days = (new EmployeeAttendanceDataService())->getAttendanceINOUTPermissionDaysValueByAutoId(1); // attendance IN
      return view('admin.employee-in-out.employee-attendance-in', compact('project','allow_days'));
  }
   // Employee Attendance OUT UI Form
  public function loadAttendanceOutForm()
  {
    $project = (new ProjectDataService())->getLoginUserAssingedProjectForDropdownList(Auth::user()->id);
    $allow_days = (new EmployeeAttendanceDataService())->getAttendanceINOUTPermissionDaysValueByAutoId(2); // attendance OUT
    return view('admin.employee-in-out.employee-attendance-out', compact('project','allow_days'));
  }

    // Employee Attendance Edit UI Form
  public function employeeAttendanceInOutEditUI()
  {

      $projects = (new ProjectDataService())->getLoginUserAssingedProjectForDropdownList(Auth::user()->id);
      $allow_days_single_emp = (new EmployeeAttendanceDataService())->getAttendanceINOUTPermissionDaysValueByAutoId(3); // Single Employee Attendance Edit
      $allow_days_multi_emp = (new EmployeeAttendanceDataService())->getAttendanceINOUTPermissionDaysValueByAutoId(4); // Multi Employee Attendance Edit
      return view('admin.employee-in-out.employee-attendance-edit', compact('projects','allow_days_single_emp','allow_days_multi_emp'));

  }


  // Load Employee multiple project Attandance form
  public function loadMonltyMultiProjectWorkRecordInsertForm()
  {

     $records = (new EmployeeAttendanceDataService())->getMultiProjectWorkRecords(10);
     $project = (new EmployeeRelatedDataService())->getAllProjectInfoForDropdown();

    return view('admin.month-work.emp_mult_project_word_record_add', compact('project', 'records'));


  }


  public function editAnEmployeeMultiProjectWorkRecord($recordId)
  {
    $EmpMultiProjWorkHisConObj = new EmployeeMultiProjectWorkHistoryController();
    $multyProjInfoAnEmp = $EmpMultiProjWorkHisConObj->findIdWiseAnEmpMultiprojectWorkHistory($recordId);

    $month = (new CompanyDataService())->getAllMonth();

    $project = (new EmployeeRelatedDataService())->getAllProjectInfoForDropdown();

    return view('admin.employee-in-out.edit-multi-project-in-out', compact('month', 'project', 'multyProjInfoAnEmp'));
  }

  public function employeeMultipleProjectWorkRecordSearchUI()
  {

    $months = (new CompanyDataService())->getAllMonth();
    $projects = (new EmployeeRelatedDataService())->getAllProjectInfoForDropdown();
    $currentMonth = Carbon::now()->format('m');
    return view('admin.month-work.search-work-record', compact('months', 'currentMonth','projects'));

  }


  // Employee Multiple Record Searching Ajax Request
//   public function searchEmployeeMultipleProjectWorkRecord1(Request $request)
//   {

//       $empMulProjWorkRecord = (new EmployeeAttendanceDataService())->getAnEmployeeMultiprojectWorkRecordByEmployeeIdForAjaxRespnse($request->emp_id, $request->month, $request->year);
//       if (count($empMulProjWorkRecord) == 0) {
//         return response()->json(['status' =>404,'success'=> false, "error" => "Employee Work Record Not Found"]);
//       }
//       return response()->json(['status' =>200,'success'=> true,"empMulProjWorkRecord" => $empMulProjWorkRecord, "error" => null]);
//   }

   public function searchEmployeeMultipleProjectWorkRecord(Request $request)
  {
        try{
                 $empMulProjWorkRecord = (new EmployeeAttendanceDataService())->searchAnEmployeeMultiprojectWorkRecordsForListViewByEmployeeIdMonthYear($request->emp_id, $request->month, $request->year,Auth::user()->branch_office_id);
                return response()->json(['status' =>200,'success'=> true,"data" => $empMulProjWorkRecord, "error" =>null]);
        }catch(Exception $ex){
            return response()->json(['status' =>404,'success'=> false, "error" =>'error' , 'message' => "System Operation Failed"]);
        }
  }



  public function employeeAttendanceProcessForm()
  {
    $project = (new EmployeeRelatedDataService())->getAllProjectInfoForDropdown();
    $month = (new CompanyDataService())->getAllMonth();
    return view('admin.employee-in-out.employee-attendance-process', compact('project', 'month'));
  }


  public function projectWiseList()
  {

    $project = (new EmployeeRelatedDataService())->getAllProjectInformation();
    return view('admin.employee-in-out.proj-wise-list', compact('project'));
  }

//   public function employeeAttendanceReportProcessingUI()
//   {

//     $project = (new EmployeeRelatedDataService())->getAllProjectInfoForDropdown();
//     $sponser = (new EmployeeRelatedDataService())->getAllSponserInfoForDropdown();
//     $month = (new CompanyDataService())->getAllMonth();
//     return view('admin.employee-in-out.attendance_report_ui', compact('month', 'project', 'sponser'));
//   }





















    /*
    |--------------------------------------------------------------------------
    | EMPLOYEE ATTENDANCE REPORT SECTION
    |--------------------------------------------------------------------------
    */

  public function employeeAttendanceReportProcessingUI()
  {

    $project = (new EmployeeRelatedDataService())->getAllProjectInfoForDropdown();
    $sponser = (new EmployeeRelatedDataService())->getAllSponserInfoForDropdown();
    $month = (new CompanyDataService())->getAllMonth();
    return view('admin.report.employee_attendance.attendance_report_process_ui', compact('month', 'project', 'sponser'));
  }


  // Employee Day & Night shift Attendance Summary report
  public function showEmployeeDailyAttendanceSumarryReport(Request $request){
      $catchDate = $request->date;
      $dayMonthYear = (new HelperController())->getDayMonthAndYearFromDateValue($request->date);

      $day = $dayMonthYear[0];
      $month = $dayMonthYear[1];
      $year = $dayMonthYear[2];
      $project_id_list = $request->project_name_id != null ? $request->project_name_id : [] ;
     $company = (new CompanyDataService())->findCompanryProfile();
      $report_title[0] = $request->date;
    // dd($request->all());
     if($request->working_shift == 0){
      // Day Shift Only
      return "Report Generation Under Processing";

     }else if($request->working_shift == 1){
      // Night Shift
      return "Report Generation Under Processing";
     }
     else if($request->working_shift == 2){
         // Day&Nisht Shift
         $attendance_summary_records = (new EmployeeAttendanceDataService())->getEmployeeDayNightDetailsAttendanceSummaryByProject($day, $month, $year,$project_id_list);

     }else if($request->working_shift == 3){
        // Previousday Nightshift And Selected Date Dayshift
        $attendance_summary_records = (new EmployeeAttendanceDataService())->getPrevioudayNightAndSelectedDateDayShiftAttendanceSummaryByProject($day, $month, $year,$project_id_list);
          return view('admin.report.employee_attendance.previousday_today_attendance_summary_report', compact('attendance_summary_records','report_title', 'company'));
     }else {
      $attendance_summary_records = (new EmployeeAttendanceDataService())->getEmployeeDailyAttendanceSummaryByProject($day, $month, $year,$project_id_list);
     }




     return view('admin.report.employee_attendance.project_wise_daily_attendance_summary', compact('attendance_summary_records','report_title', 'company'));

   }




      // Employee Daily Attendance manpower Summary report
  public function showEmpDailyAttendanceManpowerSumarryReport(Request $request){

        $catchDate = $request->date;
        $dayMonthYear = (new HelperController())->getDayMonthAndYearFromDateValue($request->date);

        $day = $dayMonthYear[0];
        $month = $dayMonthYear[1];
        $year = $dayMonthYear[2];

      $atten_day_manpower_records = (new EmployeeAttendanceDataService())->getEmployeeDailyAttendanceManpowerSummaryByProject($day, $month, $year,$request->project_id,0);
      $atten_night_manpower_records = (new EmployeeAttendanceDataService())->getEmployeeDailyAttendanceManpowerSummaryByProject($day, $month, $year,$request->project_id,1);

      $company = (new CompanyDataService())->findCompanryProfile();

      $report_title[0] = $request->date;
      $project_name = (new EmployeeRelatedDataService())->getProjectNameByProjectId($request->project_id);

      return view('admin.report.employee_attendance.project_wise_daily_attn_manpower_summary', compact('atten_day_manpower_records','atten_night_manpower_records','report_title', 'company','project_name'));

  }

    // report 2
    public function employeeAttendanceMonthlyReportProcessAndShow(Request $request)
   {
        try{
            if($request->monthly_summary){
              // Monthly summary report
              return $this->employeeAttendanceMonthlySummaryReportBaseOnInOutProjectId($request);
           }else {
             // Employee Date to Date Attendance Details report
             return $this->processProjectBaseEmployeeAttendanceDateToDateReport($request->project_id,$request->date,$request->sponserId,$request->working_shift,$request->page_offset);
           }
        }catch(Exception $ex){
            return "System Operation Error ".$ex;
        }
   }


     //2.1 Monthly Attendance Summary IN OUT project ID Wise all employees
  public function employeeAttendanceMonthlySummaryReportBaseOnInOutProjectId(Request $request)
   {

        $catchDate = $request->date;
        $dayMonthYear = (new HelperController())->getDayMonthAndYearFromDateValue($request->date);

        $fromday = 1;
        $today = $dayMonthYear[0];
        $month = $dayMonthYear[1];
        $year = $dayMonthYear[2];
        $project_id = (int) $request->project_id;
        $working_shift = $request->working_shift;

        $monthName = (new HelperController())->getMonthName($month);
        $numberOfDaysInThisMonth = $today    ;// (new HelperController())->getNumberOfDaysInMonthAndYear($month, $year);
        $company = (new CompanyDataService())->findCompanryProfile();
        $projectName = (new EmployeeRelatedDataService())->getProjectNameByProjectId($project_id);
        $sponserName = null;

         $records = (new EmployeeAttendanceDataService())->getAnEmployeeMonthlyAttendanceSummaryDateToDateRecords($project_id,$fromday,$today, $month, $year,$working_shift);
         $prepared_by = Auth::user()->name;
         return view('admin.report.employee_attendance.project_emp_monthly_work_summary',compact('records','year','monthName','company','sponserName','projectName','working_shift','prepared_by'));

   }

     // 2.2 Employee Monthly Date to Date Attendance Details report preview
   private function processProjectBaseEmployeeAttendanceDateToDateReport($project_id,$date,$sponsor_id,$working_shift,$page_offset)
   {

        $dayMonthYear = (new HelperController())->getDayMonthAndYearFromDateValue($date);
        $fromday = 1;
        $day_name_in_month = array();
        $numberOfDaysInThisMonth = (new HelperController())->getDayFromDateValue($date);// $dayMonthYear[0];
        $month = $dayMonthYear[1];
        $year = $dayMonthYear[2];
        $monthName = (new HelperController())->getMonthName($month);
        $day_name_in_month = (new HelperController())->getAllDaysNameInMonth($month,$year);
        $company = (new CompanyDataService())->findCompanryProfile();
        $projectName = (new EmployeeRelatedDataService())->getProjectNameByProjectId($project_id);
        $sponserName = null;
        if ($sponsor_id != null) {
          $sponserName = (new EmployeeRelatedDataService())->getASponserNameBySponerId($sponsor_id);
        }

        $total_emp_list  = (new EmployeeAttendanceDataService())->getListOfEmployeesThoseWorkedInThisProjectForExportAttendanceInOut($project_id,$month,$year,$working_shift);
        $count = 0;
        $attendent_emp_list = array();
        ini_set('max_execution_time', 600);
        $page_limit = $page_offset+ 500;
        if($page_offset >= count($total_emp_list)){
          return "No Data Found";
        }else if(count($total_emp_list) < $page_limit) {
          $page = count($total_emp_list) - $page_offset;
          $page_limit = $page_offset + $page;
        }
         $working_shift_list = $working_shift == "" ? [0,1]:[$working_shift];
        for($i = $page_offset; $i <$page_limit; $i++){
          $emp = $total_emp_list[$i];
         // $Attendence = (new EmployeeAttendanceDataService())->getAnEmployeeMonthlyAttendanceDateToDateRecordsByProjectIdForExportExcell($emp->emp_auto_id,$project_id,$fromday,$numberOfDaysInThisMonth, $month, $year,$working_shift_list);
        //  $Attendence = (new EmployeeAttendanceDataService())->getAnEmployeeMonthlyAttendanceDateToDateRecordsForAttendancePreview($emp->emp_auto_id,$project_id,$fromday,$numberOfDaysInThisMonth, $month, $year,$working_shift);
          $Attendence = (new EmployeeAttendanceDataService())->getAnEmployeeMonthlyAttendanceDateToDateRecords($emp->emp_auto_id,$fromday,$numberOfDaysInThisMonth, $month, $year);
              if(count($Attendence) >0){
                $allAttend = array_fill(0, $numberOfDaysInThisMonth + 2, null);
                foreach($Attendence as $attend) {
                  $allAttend[(int) $attend->emp_io_date] = $attend;
                }
                $emp->attendace_records = $allAttend;
                $emp->total_over_time =  (new EmployeeAttendanceDataService())->countAnEmployeeTotalOverTimeHoursFromDateToDate($emp->emp_auto_id,$fromday,$numberOfDaysInThisMonth, $month, $year);
                $emp->total_daily_work_hours = $emp->total_over_time + (new EmployeeAttendanceDataService())->countAnEmployeeTotalWorkingHoursFromDateToDate($emp->emp_auto_id,$fromday,$numberOfDaysInThisMonth, $month, $year);
                $emp->total_working_days =  count($Attendence);
                $attendent_emp_list[$count] = $emp;
                $count++;
              }
        }

        $daily_total_hours_array = array_fill(1, $numberOfDaysInThisMonth + 1, 0);
        list($totalHolidays,$holidayArray) = (new HelperController())->countTotalHolidayInThisMonth($month,$year);
        $prepared_by = Auth::user()->name;
        return view('admin.report.employee_attendance.project_date_to_date_attend_report_preview', compact('numberOfDaysInThisMonth','day_name_in_month', 'holidayArray', 'sponserName', 'projectName', 'company', 'attendent_emp_list', 'monthName', 'year', 'daily_total_hours_array', 'totalHolidays','working_shift','prepared_by','page_limit','page_offset'));

   }

    //3.2 Attendance Summary Report
  public function showEmployeeMonthlyAttendanceSumarryReport(Request $request){

       $project_id_list = $request->project_name_id  ;
     //  $month = (new HelperController())->getDayMonthAndYearFromDateValue($request->date);
      // $year = $request->year_id;

       $dayMonthYear = (new HelperController())->getDayMonthAndYearFromDateValue($request->date);
       $day = $dayMonthYear[0];
       $month = $dayMonthYear[1];
       $year = $dayMonthYear[2];


      if($request->report_type == 1){

        if($project_id_list == null){
          $project_records = (new ProjectDataService())->getAllActiveProjectListForDropdown();
        }else {
          $project_records = (new ProjectDataService())->getProjectListByMultipleProjectId($project_id_list);
        }
         $company = (new CompanyDataService())->findCompanryProfile();
          $counter = 0;
          foreach($project_records as $pid){

              $day_shift_record = (new EmployeeAttendanceDataService())->getMonthlyAttendanceHoursSummaryReportByProject($month, $year,$pid->proj_id,0);
              if($day_shift_record){
                $day_shift_record->total_emp_worked = (new EmployeeAttendanceDataService())->countTotalNumberOfWorkersWorkedInAMonthInTheProject($pid->proj_id,$month, $year, 0);
                $project_records[$counter]->day_shift_record = $day_shift_record;
              }
              $night_shift_record = (new EmployeeAttendanceDataService())->getMonthlyAttendanceHoursSummaryReportByProject($month, $year,$pid->proj_id,1);
              if($night_shift_record){
                $night_shift_record->total_emp_worked = (new EmployeeAttendanceDataService())->countTotalNumberOfWorkersWorkedInAMonthInTheProject($pid->proj_id,$month, $year, 1);
                $project_records[$counter]->night_shift_record = $night_shift_record;
              }
              $counter++;
          }
          $report_title[0] = (new HelperController())->getMonthName($month);
          $report_title[1] = $year;
          return view('admin.report.employee_attendance.all_project_monthly_hours_summary', compact('project_records','report_title', 'company'));
      }else if($request->report_type == 2){
          // Today Present Hourly and Basic Employee Summary
           return  $this->processAndShowTodayPresentHourlyAndBasicEmployeeSummary($project_id_list,0,$day,$month,$year);
      }


  }


   // Today Present Hourly and Basic Employee Summary
   private function processAndShowTodayPresentHourlyAndBasicEmployeeSummary($project_id_list,$working_shift,$day,$month,$year){

      if($project_id_list == null){
        $project_records = (new ProjectDataService())->getAllActiveProjectListForDropdown();
      }else {
        $project_records = (new ProjectDataService())->getProjectListByMultipleProjectId($project_id_list);
      }

      //  $summary_records = (new EmployeeAttendanceDataService())->getTodayPresentHourlyAndBasicEmployeeSummary($project_id_list[0],20,$month,$year,$working_shift);

     $counter = 0;
     foreach($project_records as $pid){
        $day_shift_record = (new EmployeeAttendanceDataService())->getTodayPresentHourlyAndBasicEmployeeSummary($pid->proj_id,0,$day,$month,$year);
        if(count($day_shift_record) == 2){
          $project_records[$counter]->day_shift_record  =  [
            'total_basic_emp' => $day_shift_record[0]->total_emp,
            'total_hourly_emp' => $day_shift_record[1]->total_emp,];
         }else if(count($day_shift_record) == 1){
            $project_records[$counter]->day_shift_record  =  [
            'total_basic_emp' => $day_shift_record[0]->total_emp,
            'total_hourly_emp' => 0,
          ];

        }else{
          $project_records[$counter]->day_shift_record  = null;
        }
         $night_shift_record = (new EmployeeAttendanceDataService())->getTodayPresentHourlyAndBasicEmployeeSummary($pid->proj_id,1,$day,$month,$year);
         if( $night_shift_record == null){
          $project_records[$counter]->night_shift_record  =  null;
         }
         if(count($night_shift_record) == 2){
            $project_records[$counter]->night_shift_record  =  [
              'total_basic_emp' => $night_shift_record[0]->total_emp,
              'total_hourly_emp' => $night_shift_record[1]->total_emp,];
           }else if(count($night_shift_record) == 1){
              $project_records[$counter]->night_shift_record  =  [
              'total_basic_emp' => $night_shift_record[0]->total_emp,
              'total_hourly_emp' => 0];
          }else {
            $project_records[$counter]->night_shift_record  =  null;
          }
         $counter++;
     }
     $total_active_emps = (new EmployeeDataService())->countTotalEmployees(1);
     $company = (new CompanyDataService())->findCompanryProfile();
     $report_title[0] =  $year.'-'.$month.'-'.$day;
     return view('admin.report.employee_attendance.today_present_basic_hourly_emp_summary', compact('project_records','total_active_emps','report_title', 'company'));



  }

  // 4 Employee Attendance Excel Download
  public function downloadEmployeeAttendanceRecordAsExcel(Request $request)
  {
      try{


        $catchDate = $request->date;
        $dayMonthYear = (new HelperController())->getDayMonthAndYearFromDateValue($request->date);

        $fromday = 1;
        $day_name_in_month = array();
        $numberOfDaysInThisMonth = $dayMonthYear[0];
        $month = $dayMonthYear[1];
        $year = $dayMonthYear[2];
        $project_id = (int) $request->project_id;
        $working_shift = $request->working_shift;

        $monthName = (new HelperController())->getMonthName($month);
        $day_name_in_month = (new HelperController())->getAllDaysNameInMonth($month,$year);
        $company = (new CompanyDataService())->findCompanryProfile();
        $projectName = (new EmployeeRelatedDataService())->getProjectNameByProjectId($project_id);
        $project_color_codes = (new ProjectDataService())->getAllProjectColorCodeArray();
        ini_set('max_execution_time', 1000);

        if($request->report_type ==1){
          // project base report
          $list_of_emp  = (new EmployeeAttendanceDataService())->getListOfEmployeesAttendanceRecordsThoseWorkedInTheProjectForExportAsExcell($project_id,$month,$year,$working_shift);
          $projectName  = is_null($projectName) == true ? 'attendance.xlsx' : $projectName.".xlsx";

          return Excel::download(new EmpAttnInOutInAProjectExport($working_shift,$fromday,$numberOfDaysInThisMonth,$numberOfDaysInThisMonth,$month,$year,$list_of_emp,$project_color_codes),$projectName);

        }else if($request->report_type == 2){
            // employee base report
           // $list_of_emp = (new EmployeeDataService())->getEmployeeListForAttendanceReportWithProjectAndSponsorAndJobStatus($project_id,$request->sponserId,$working_shift);
            $list_of_emp = (new EmployeeDataService())->getLisOfEmployeeForTheProjectForAttendanceRecordDownload($project_id,$request->sponserId,$working_shift);

             return Excel::download(new EmpAttendanceExport(null,null,$working_shift,$fromday,$numberOfDaysInThisMonth,$numberOfDaysInThisMonth,$month,$year,$list_of_emp,$project_color_codes), 'attendance.xlsx');
         }

      }catch(Exception $ex){
        return "System Error Found , Please Try Again";
      }


  }

  // Multiple Employee ID Attendance Report
   public function getAnEmployeeDayByDateMonthlyAttendanceReport(Request $request){

          if($request->report_type == 1){
              // single month multiple employee attendance report
              return $this->getSingleMonthMultEmployeeAttendanceReport($request->employee_id,$request->month_id,$request->year_id);
          }else if($request->report_type == 2){
              // single employee all attendnace report
              return $this->getAnEmployeeAttendanceAllRecords($request->employee_id,$request->year_id);
          }else if($request->report_type == 3){
              // an employee single month attendnace details
            return $this->getAnEmployeeAttendanceDetailsWithTimekeeerInformationReport($request->employee_id,$request->month_id,$request->year_id);
          } else if($request->report_type == 4){
              // WPS EMPLOYEE OVERTIME SHEET PRINT
            return $this->getMultiEmployeeOvertimeDetailsSheetForSignature($request);
          }
         else if($request->report_type == 5){
                // MULTI EMPLOYEE DAILY ATTENDANCE EMPLOYEE LIST
            return $this->getLoginUserPermittedProjectMultiEmployeeDailyAttendanceReport($request);
          }else if($request->report_type == 6){
              // WPS EMPLOYEE ATTENDANCE REPORT  DOWNLOAD
            return $this->getWPSEmployeeMonthlyWorkingAttendanceSummaryReport($request);
          }


   }

          // An Employee Single Month Atten History
   private function getSingleMonthMultEmployeeAttendanceReport($employee_ids,$month,$year){


          $fromday = 1;
          $count = 0;
          $day_name_in_month = array();
          $attendent_emp_list = array();

          $numberOfDaysInThisMonth = (new HelperController())->getNumberOfDaysInMonthAndYear($month,$year);
          $day_name_in_month = (new HelperController())->getAllDaysNameInMonth($month,$year);
          $monthName = (new HelperController())->getMonthName($month);
          $company = (new CompanyDataService())->findCompanryProfile();


          $allEmplId = explode(",", $employee_ids);
          $allEmplId = array_unique($allEmplId); // remove multiple same empl ID

          $emp_auto_id_list = (new EmployeeDataService())->getListOfEmployeeEmpAutoIdAsArrayByEmployeeIdList( $allEmplId);
          $working_proj_list = (new EmployeeAttendanceDataService())->getEmployeeWorkingListOfProjectWithColorCodeByWorkingMonthYear($emp_auto_id_list,$month,$year);


          $directEmp =(new EmployeeDataService())->getMultipleEmployeeInfoByMultipleEmployeeIdForAttendanceReport($allEmplId);
          foreach ($directEmp as $emp) {
                $Attendence = (new EmployeeAttendanceDataService())->getAnEmployeeMonthlyAttendanceDateToDateRecordsWithProjecColorCodeForShowingAllMonthReport($emp->emp_auto_id,$fromday,$numberOfDaysInThisMonth, $month, $year);

                if(count($Attendence) >0){
                    $allAttend = array_fill(0, $numberOfDaysInThisMonth + 2, null);
                    $total_daily_work_hours = 0;
                    $total_over_time = 0;
                    $total_working_days = 0;
                    foreach($Attendence as $attend) {
                      $attendday = (int) $attend->emp_io_date;
                      $allAttend[$attendday] = $attend;
                    }
                    $emp->attendace_records = $allAttend;
                    $emp->last_working_project_name = (new ProjectDataService())->getProjectNameByProjectId($Attendence[(count($Attendence)-1)]->proj_id);
                    $emp->total_over_time =  (new EmployeeAttendanceDataService())->countAnEmployeeTotalOverTimeHoursFromDateToDate($emp->emp_auto_id,$fromday,$numberOfDaysInThisMonth, $month, $year);
                    $emp->total_daily_work_hours =   (new EmployeeAttendanceDataService())->countAnEmployeeTotalWorkingHoursFromDateToDate($emp->emp_auto_id,$fromday,$numberOfDaysInThisMonth, $month, $year);
                    $emp->total_working_days = count($Attendence);
                    $attendent_emp_list[$count] = $emp;
                    $count++;

                }else {
                    $allAttend = array_fill(0, $numberOfDaysInThisMonth + 2, null);
                    $total_daily_work_hours = 0;
                    $total_over_time = 0;
                    $total_working_days = 0;

                    $emp->attendace_records = $allAttend;

                    $emp->last_working_project_name = '';
                    $emp->total_over_time = 0;
                    $emp->total_daily_work_hours = 0;
                    $emp->total_working_days = 0;
                    $attendent_emp_list[$count] = $emp;
                    $count++;
                }
          }

          $daily_total_hours_array = array_fill(1, $numberOfDaysInThisMonth + 1, 0);
          list($totalHolidays,$holidayArray) = (new HelperController())->countTotalHolidayInThisMonth($month,$year);
          $prepared_by = Auth::user()->name;
          $working_shift = '';
          return view('admin.report.employee_attendance.multi_emp_id_datewise_attendance', compact('day_name_in_month','numberOfDaysInThisMonth', 'holidayArray' ,  'company',
          'attendent_emp_list', 'monthName', 'year', 'daily_total_hours_array', 'totalHolidays','working_shift','prepared_by','working_proj_list'));
   }
      // An Employee All Month Atten History
   private function getAnEmployeeAttendanceAllRecords($employee_id,$year){


          $fromday = 1;
          $count = 0;
          $attendent_emp_list = array();
          $emp = (new EmployeeDataService())->searchingAnEmployeeInfoByMultitypeParameter($employee_id,'employee_id');
          if(count($emp) > 1 || count($emp) == 0){
            return 'Employee Not Found ';
          }
          $emp = $emp[0];

       //   $working_proj_list = (new EmployeeAttendanceDataService())->getEmployeeWorkingListOfProjectWithColorCode([$emp->emp_auto_id],date('Y-m-d', strtotime('-1 year')),date('Y-m-d'));
          $working_proj_list = (new EmployeeAttendanceDataService())->getAnEmployeeWorkedListOfProjectWithColorCodeAndTimekeeperInfo([$emp->emp_auto_id],date('Y-m-d', strtotime('-1 year')),date('Y-m-d'));

          $listofmonth_year = (new HelperController())->getMonthsInRangeOfDate(date('Y-m-d', strtotime('-1 year')),date('Y-m-d'));

          foreach($listofmonth_year as $arecord){

                $aemp = new EmployeeInfo();
                $aemp->working_month = (new HelperController())->getMonthName($arecord['month']);
                $aemp->working_year =  $arecord['year'];
                $aemp->number_of_day_this_month = (new HelperController())->getNumberOfDaysInMonthAndYear($arecord['month'],$arecord['year']);
                $Attendence = (new EmployeeAttendanceDataService())->getAnEmployeeMonthlyAttendanceDateToDateRecordsWithProjecColorCodeForShowingAllMonthReport($emp->emp_auto_id,$fromday,31, $arecord['month'],$arecord['year']);
                list($totalHolidays,$holidayArray) = (new HelperController())->countTotalHolidayInThisMonth($arecord['month'],$arecord['year']);
                $aemp->holiday_array = $holidayArray;
                $aemp->total_holiday = $totalHolidays;
                $allAttend = array_fill(0, 33, null);

                if(count($Attendence) >0){
                  foreach($Attendence as $attend) {
                    $allAttend[(int) $attend->emp_io_date] = $attend;
                  }
                  $aemp->attendace_records = $allAttend;
                  $aemp->last_working_project_name = (new ProjectDataService())->getProjectNameByProjectId($Attendence[(count($Attendence)-1)]->proj_id);
                  $aemp->total_over_time =  (new EmployeeAttendanceDataService())->countAnEmployeeTotalOverTimeHoursFromDateToDate($emp->emp_auto_id,$fromday,$aemp->number_of_day_this_month ,$arecord['month'],$arecord['year']);
                  $aemp->total_daily_work_hours =   (new EmployeeAttendanceDataService())->countAnEmployeeTotalWorkingHoursFromDateToDate($emp->emp_auto_id,$fromday,$aemp->number_of_day_this_month ,$arecord['month'],$arecord['year']);
                  $aemp->total_working_days = count($Attendence);
                  $attendent_emp_list[$count] = $aemp;
                  $count++;

                }else {

                  $aemp->attendace_records =   $allAttend;
                  $aemp->last_working_project_name = '';
                  $aemp->total_over_time = 0;
                  $aemp->total_daily_work_hours = 0;
                  $aemp->total_working_days = 0;
                  $attendent_emp_list[$count] = $aemp;
                  $count++;

                }
          }


          $company = (new CompanyDataService())->findCompanryProfile();
          $prepared_by = Auth::user()->name;
          $working_shift = '';
          return view('admin.report.employee_attendance.anemp_attn_all_records', compact('emp','company',
          'attendent_emp_list','totalHolidays','working_shift','prepared_by','working_proj_list'));

   }

     // 3 an employee attendance details
    private function getAnEmployeeAttendanceDetailsWithTimekeeerInformationReport($employee_ids,$month,$year){


          $fromday = 1;
          $count = 0;
          $day_name_in_month = array();
          $attendent_emp_list = array();

          $numberOfDaysInThisMonth = (new HelperController())->getNumberOfDaysInMonthAndYear($month,$year);
          $day_name_in_month = (new HelperController())->getAllDaysNameInMonth($month,$year);
          $monthName = (new HelperController())->getMonthName($month);
          $company = (new CompanyDataService())->findCompanryProfile();


          $allEmplId = explode(",", $employee_ids);
          $allEmplId = array_unique($allEmplId); // remove multiple same empl ID
          if(count($allEmplId) == 0){
            return "Please Input An Employee ID";

          }
          $emp = (new EmployeeDataService())->getAnEmployeeInformationWithAllReferenceTableByEmployeeID( $allEmplId[0]);

          $attendance_records = (new EmployeeAttendanceDataService())->getAnEmployeeMonthlyAttendanceDateToDateRecordsWithTimekeeperInfoReport($emp->emp_auto_id,$fromday,$numberOfDaysInThisMonth, $month, $year);

           $login_name = Auth::user()->name;


          return view('admin.report.employee_attendance.emp_attendance_with_timekeeper', compact('day_name_in_month','numberOfDaysInThisMonth','company',
          'attendance_records','emp', 'monthName', 'year', 'login_name'));
    }

  // 4 MULTIPLE EMPLOYEE OVERTIME SHEET PRINT FOR SIGNATURE
   private function getMultiEmployeeOvertimeDetailsSheetForSignature($request){

        try{
            $month = $request->month_id;
            $year = $request->year_id;
            $multiple_emp_ids = $request->employee_id;
            $project_ids = $request->project_ids;
            $day_month_year =  (new HelperController())->getDayMonthAndYearFromDateValue($request->working_date);
            $working_date = $request->working_date;
            $day = $day_month_year[0];
            $month = $day_month_year[1];
            $year = $day_month_year[2];

            $month_name = (new HelperController())->getMonthName($month);
            $company = (new CompanyDataService())->findCompanryProfile();


            $multiple_emp_ids = explode(",", $multiple_emp_ids);
            $multiple_emp_ids = array_unique($multiple_emp_ids); // remove multiple same empl ID

            $records = (new EmployeeAttendanceDataService())->getListOfEmployeesAttendanceRecordThoseWorkedOvertimeForADate($multiple_emp_ids,$day, $month, $year);

            $project_name ='';

            $login_name = Auth::user()->name;
            return view('admin.report.employee_attendance.single_day_ot_emp_list', compact('records','login_name','working_date','project_name','company','month_name','year'));


        }catch(Exception $ex){
            return "System Operation Failed , Please Refresh and try Again";
        }

    }

     // 5 WPS EMPLOYEE ATTENDANCE PDF DOWNLOAD
    private function getWPSEmployeeMonthlyWorkingAttendanceSummaryReport($request){

             try{

                    $month = $request->month_id;
                    $year = $request->year_id;
                    $multiple_emp_ids = $request->employee_id;
                    $project_ids = $request->project_ids;
                    // dd( $request->all());

                    $month_name = (new HelperController())->getMonthName($month);
                    $company = (new CompanyDataService())->findCompanryProfile();
                    $number_of_days_in_month =(new HelperController())->getNumberOfDaysInMonthAndYear($month, $year);
                    $holidays =(new HelperController())->getAMonthHolidaysAsArray($month, $year);



                    $multiple_emp_ids = explode(",", $multiple_emp_ids);
                    $multiple_emp_ids = array_unique($multiple_emp_ids); // remove multiple same empl ID
                    if(count($project_ids) >0 && $multiple_emp_ids[0] == ''){
                        $multiple_emp_ids = (new WPSEmployeeDataService())->getListOfEmployeeAutoIdThoseSalaryPaidToBankByProjectIds($project_ids,Auth::user()->branch_office_id);
                    }


                      $final_records = []; // Use a temporary array to hold the raw results
                            $counter = 0;

                            foreach ($multiple_emp_ids as $employee_id) {
                                $emp = (new EmployeeRelatedDataService())->getWPSEmployeeInfoWithBankDetailsForAttenanceSummaryReportByEmployeeId($employee_id, 1);
                                if ($emp == null) {
                                    continue;
                                }

                                $arecord = (new EmployeeAttendanceDataService())->getAWPSEmployeeFullMonthTotalWorkingInfoForSalaryProcessingMonthlyWorkRecord($emp->emp_auto_id, $month, $year);
                                $present_absent = (new EmployeeAttendanceDataService())->calculateAnEmployeeNumberOfAbsentDaysForAMonthUsingINOUTRecords($emp->emp_auto_id,
                                 $month, $year,$holidays,$number_of_days_in_month);

                                // Map attendance data onto the employee object
                                if ($arecord->working_days == 0) {
                                    $emp->basic_hours = 0;
                                    $emp->over_time = 0;
                                    $emp->working_days = 0;
                                    $emp->last_working_project = null;
                                    $emp->sick_leave = 0; // Ensure sick_leave is reset if working_days is 0
                                    $emp->absent = $number_of_days_in_month; // Assuming absent should also be reset
                                    $emp->present = 0;

                                } else {
                                    $emp->basic_hours = $arecord->basic_hours;
                                    $emp->over_time = $arecord->over_time;
                                    $emp->working_days = $arecord->working_days;
                                    $emp->last_working_project = $arecord->last_working_project;
                                    $emp->sick_leave = $arecord->sick_leave;
                                    $emp->present = $present_absent['present'];
                                    $emp->absent = $present_absent['absent'];
                                    $emp->duty_status = '';
                                }

                                // Store the merged Eloquent Model (or object)
                                $final_records[$counter++] = $emp;
                            }

                    $project_name ='';
                    $login_name = Auth::user()->name;
                     if($request->report_format == 1){
                        return view('admin.report.employee_attendance.wps_employee_amonth_atten_report', compact('final_records','login_name','project_name','company','month_name','year'));
                    }else if($request->report_format == 2){
                        // download as excel format
                        $collection_records = collect($final_records);
                        return Excel::download(new WPSEmployeMonthlyWorkRecordAsExcel($collection_records), $month_name.'_'.$year.' work_summary.xlsx');
                    }


                }catch(Exception $ex){
                    return "System Operation Failed , Please Refresh and try Again";
                }


   }

//7 Login User Project Multi Employee Today Attendance
   private function getLoginUserPermittedProjectMultiEmployeeDailyAttendanceReport($request){

          try{
                $company = (new CompanyDataService())->findCompanryProfile();
               // dd($company);
                $working_date = $request->working_date;
                $project_name = '';
                $working_shift = $request->working_shift == '2' ? [0,1]:[$request->working_shift]; // 2 means both shift
                $permitted_project_ids = [];
                if($request->has('project_ids')){
                    $permitted_project_ids = $request->project_ids;

                    if(count($permitted_project_ids) == 0){
                        return "No Project Selected , Please Select Only One Project";
                    }else if (count($permitted_project_ids) > 1){
                        return "You can select maximum One project  at a time for this report";
                    }else{
                        $project_name = (new ProjectDataService())->getProjectNameByProjectId($permitted_project_ids[0]);
                     }
                }
                else {

                    // this is for test purpose, not allowed multi project
                    $permitted_project_ids  = (new ProjectDataService())->getLoginUserAccessPermissionProjectIDsArray(Auth::user()->id);
                     $project_name = 'Multiple Projects Employees';
                }

                $day_month_year = (new HelperController())->getDayMonthAndYearFromDateValue($request->working_date);


                $allEmplId = explode(",", $request->employee_id);
                $allEmplId = array_unique($allEmplId); // remove multiple same empl ID
                $all_employees = (new EmployeeDataService())->getListOfEmployeeInformationByMultipleEmpIDsAndProjectIDsForDailyAttendanceEmpListReport( $allEmplId,$permitted_project_ids, $working_shift );

                $employees = array();
                $counter = 0;
                $total_present = 0;
                $total_sick_leave = 0;
                $total_basic_emps = 0;
                $total_hourly_emps = 0;

                 foreach ($all_employees as $emp) {

                        $attend = (new EmployeeAttendanceDataService())->searchAnEmployeeThisDayAtttendanceRecordForAttendanceReport($emp->emp_auto_id,$day_month_year[0],$day_month_year[1],$day_month_year[2], $working_shift,$permitted_project_ids[0]);
                        if($attend){
                            $total_present += 1;
                            $emp->hourly_employee == 1 ? $total_hourly_emps += 1: $total_basic_emps +=1;

                            $emp->is_present = true;
                            $emp->attend_record = $attend;
                             if($attend->attendance_status == 'SL'){
                                $total_sick_leave +=1;
                            }
                        }else{
                            $emp->is_present = false;
                        }
                        $employees[$counter++] = $emp;
                }

                $subcon_sponsor_ids = (new EmployeeRelatedDataService())->getAllActiveSubcontractorSponsorIdAsArrayOfABranchOffice(Auth::user()->branch_office_id);
                $subcon_emp_summary = (new EmployeeAttendanceDataService())->searchTodayAtttendanceEmpSummaryGroupBySubcontractorForThisDayAttendanceReport($subcon_sponsor_ids,$day_month_year[0],$day_month_year[1],$day_month_year[2], $working_shift,$permitted_project_ids[0]);

                $login_name = Auth::user()->name;
                $working_shift = $request->working_shift == '2' ? 'Both Shift': ($request->working_shift == '0' ? 'Day Shift':'Night Shift');


                $data = [
                    'company'                 => $company,
                    'employees'             => $employees,
                    'working_date'              => $working_date,
                    'project_name'            => $project_name,
                    'total_present'            => $total_present,
                    'working_shift'         => $working_shift,
                    'login_name'         => $login_name,
                     'subcon_emp_summary' => $subcon_emp_summary,
                    'total_basic_emps' => $total_basic_emps,
                    'total_hourly_emps' =>$total_hourly_emps,

                ];

                    //  $pdf = Pdf::loadView('admin.report.employee_attendance.today_present_emp_list', $data)
                    //     ->setPaper('a4', 'portrait');

                    // return $pdf->stream('today_attendance_report.pdf');

                return view('admin.report.employee_attendance.today_present_emp_list', compact('employees','subcon_emp_summary','working_date','company','project_name','total_present','total_sick_leave','working_shift','login_name','total_hourly_emps','total_basic_emps'));

         }catch(Exception $ex){
            return "System Operation Failed , Please Refresh and try Again";
        }
   }


     // 3-Projectwise Month By Month Work Hours Summary
    public function getProjectwiseTotalWorkHoursSummaryReport(Request $request)
     {

        $catchDate = $request->date;
        $dayMonthYear = (new HelperController())->getDayMonthAndYearFromDateValue($request->date);

         $fromday = $request->fromdate ;
         $today = $request->todate ;
         $company = (new CompanyDataService())->findCompanryProfile();
         $monthwithYears =  (new HelperController())->getMonthsInRangeOfDate($fromday, $today);

          $monthList = array();
          $yearList = array();
          $counter = 0;
          foreach ($monthwithYears as $my) {
            $monthList[$counter] = $my['month'];
            $yearList[$counter] =  $my['year'];
            $counter++;
          }
         $records = (new EmployeeAttendanceDataService())->getDateToDateTotalWorkHourseSummary($request->project_id, $fromday, $today, $monthList, $yearList);
         $prepared_by = Auth::user()->name;
         return view('admin.report.employee_attendance.month_by_month_total_work_summary', compact('records', 'company','prepared_by'));


   }



  // 7 Daily Absent Manpower Report Project WIse showDailyAbsentManpowerReportDetails
  public function processAndShowAbsenceEmployeeDetailsOrAttendanceRecordsDetails(Request $request){

    try{

        $dayMonthYear = (new HelperController())->getDayMonthAndYearFromDateValue($request->date);
        $day = $dayMonthYear[0];
        $month = $dayMonthYear[1];
        $year = $dayMonthYear[2];

        $day_night_shifts = $request->day_night_shift;

       if($request->report_type == 2) // absent emp details report
        {
          $absent_manpower_records = (new EmployeeAttendanceDataService())->getTodayAbsentEmployeeDetailsReport($request->project_ids,$day, $month, $year,
          $day_night_shifts);
          $company = (new CompanyDataService())->findCompanryProfile();
          $report_title[0] = $request->date;
          $project_name = "All ";
          if($request->project_id == null){
           }else if(count($request->project_id) == 1){
            $project_name = (new EmployeeRelatedDataService())->getProjectNameByProjectId($request->project_id[0]);
           }else {
            $project_name = "Multiple Projects ";
           }
          return view('admin.report.employee_attendance.absent_emp_details', compact('absent_manpower_records','report_title', 'company','project_name','report_title'));

        }else if($request->report_type == 1){
             // Absence emp attendance details
          return $this->processAndShowAbsenceEmployeesDayByDayAttendanceDetailsReport($request->project_ids,$request->sponsor_ids,$day_night_shifts,$day, $month, $year);
        }

    }catch(Exception $ex){
        return "Operation Failed. Reload and Try Again               ".$ex;
    }


  }
   // 7.1  today absent emp attendance date by date report
  private function processAndShowAbsenceEmployeesDayByDayAttendanceDetailsReport($project_ids,$sponsor_ids,$working_shift,$day,$month,$year){

          $numberOfDaysInThisMonth = (new HelperController())->getNumberOfDaysInMonthAndYear($month,$year);
          $fromday = 1;
          $count = 0;
          $day_name_in_month = array();
          $attendent_emp_list = array();
          if(is_null($project_ids)){
             $project_ids = (new ProjectDataService())->getAllActiveProjectIDs();
          }
          if(is_null($sponsor_ids)){
            $sponsor_ids = (new EmployeeRelatedDataService())->getAllActiveSponsorIdAsArray();
          }
          if(is_null($working_shift)){
            $working_shift = [0,1];
          }

          $directEmp  = (new EmployeeAttendanceDataService())->getListOfEmployeesThoseAreNotPresentInAttendanceRecordByProjectSponsorWorkingshiftDayMonthYear( $project_ids,  $sponsor_ids ,$working_shift, $day, $month, $year);
          $working_proj_list = (new EmployeeAttendanceDataService())->getEmployeeWorkingListOfProjectWithColorCodeByProjectSponsorShiftDayMonthYear($project_ids,$sponsor_ids,$working_shift,$day,$month,$year);

          ini_set('max_execution_time', 900);
          foreach ($directEmp as $emp) {

                $Attendence = (new EmployeeAttendanceDataService())->getAnEmployeeMonthlyAttendanceDateToDateRecordsWithProjecColorCodeForShowingAllMonthReport($emp->emp_auto_id,$fromday,$numberOfDaysInThisMonth, $month, $year);

                if(count($Attendence) >0){
                    $allAttend = array_fill(0, $numberOfDaysInThisMonth + 2, null);
                    $total_daily_work_hours = 0;
                    $total_over_time = 0;
                    $total_working_days = 0;
                    foreach($Attendence as $attend) {
                      $attendday = (int) $attend->emp_io_date;
                      $allAttend[$attendday] = $attend;
                    }
                    $emp->attendace_records = $allAttend;

                    $emp->last_working_project_name = (new ProjectDataService())->getProjectNameByProjectId($Attendence[(count($Attendence)-1)]->proj_id);
                    $emp->total_over_time =  (new EmployeeAttendanceDataService())->countAnEmployeeTotalOverTimeHoursFromDateToDate($emp->emp_auto_id,$fromday,$numberOfDaysInThisMonth, $month, $year);
                    $emp->total_daily_work_hours =  (new EmployeeAttendanceDataService())->countAnEmployeeTotalWorkingHoursFromDateToDate($emp->emp_auto_id,$fromday,$numberOfDaysInThisMonth, $month, $year);
                    $emp->total_working_days = count($Attendence);
                    $attendent_emp_list[$count] = $emp;
                    $count++;

                }else {
                    $allAttend = array_fill(0, $numberOfDaysInThisMonth + 2, null);
                    $total_daily_work_hours = 0;
                    $total_over_time = 0;
                    $total_working_days = 0;
                    $emp->attendace_records = $allAttend;
                    $emp->last_working_project_name = '';
                    $emp->total_over_time = 0;
                    $emp->total_daily_work_hours = 0;
                    $emp->total_working_days = 0;
                    $attendent_emp_list[$count] = $emp;
                    $count++;
                }
          }
          $daily_total_hours_array = array_fill(1, $numberOfDaysInThisMonth + 1, 0);
          list($totalHolidays,$holidayArray) = (new HelperController())->countTotalHolidayInThisMonth($month,$year);
          $prepared_by = Auth::user()->name;
          $day_name_in_month = (new HelperController())->getAllDaysNameInMonth($month,$year);
          $monthName = (new HelperController())->getMonthName($month);
          $company = (new CompanyDataService())->findCompanryProfile();
          $working_shift = '';

          return view('admin.report.employee_attendance.multi_emp_id_datewise_attendance', compact('day_name_in_month','numberOfDaysInThisMonth', 'holidayArray' ,  'company',
          'attendent_emp_list', 'monthName', 'year', 'daily_total_hours_array', 'totalHolidays','working_shift','prepared_by','working_proj_list'));
    }



   // 8 Monthly Absent Manpower Report Project WIse
  public function showMonthlyAbsentManpowerReportDetails(Request $request){

    $month =  $request->month_id;
    $year = $request->year_id;

    if($request->report_type == 1){
       // attendance date by date report

      $emp_list = (new EmployeeAttendanceDataService())->getListOfEmployeesThoseAreNotPresentMinimumDaysInAMonthAttendanceReport($request->project_id, $month, $year, $request->working_day);
           $dayofthis_month =  (new HelperController())->getNumberOfDaysInMonthAndYear($month,$year);

      if((new HelperController())->getCurrentMonthIntValue() == (int) $month){
        $dayofthis_month = (new HelperController())->getTodayDayFromCurrentMonth();
      }
     // dd($dayofthis_month);
      return $this->showAbsetEmployeesAttendanceReport($emp_list,1,$dayofthis_month,$month,$year,$request->project_id);

    }else if($request->report_type == 2){
        // Rrregular attendnace Employee  date by date report
      $absent_manpower_records = (new EmployeeAttendanceDataService())->getMonthlyAbsentEmployeeRerpot($request->project_id, $month, $year, $request->working_day,$request->join_date);
      $company = (new CompanyDataService())->findCompanryProfile();
      $report_title[0] = (new HelperController())->getMonthName($month);
      $report_title[1] = $year;
      $report_title[2] = $request->working_day;
      $project_name = "All ";
      if($request->project_id != null){
        $project_name = (new EmployeeRelatedDataService())->getProjectNameByProjectId($request->project_id);
      }

      return view('admin.report.employee_attendance.emp_absent_monthly_report', compact('absent_manpower_records','report_title', 'company','project_name'));


    }


}

  //  Rrregular attendnace Employee  date by date report
  private function showAbsetEmployeesAttendanceReport($emp_list,$fromday,$numberOfDaysInThisMonth, $month, $year,$project_id){

    $day_name_in_month = array();
    $monthName = (new HelperController())->getMonthName($month);
    $day_name_in_month = (new HelperController())->getAllDaysNameInMonth($month,$year);
    $company = (new CompanyDataService())->findCompanryProfile();
    $projectName = (new EmployeeRelatedDataService())->getProjectNameByProjectId($project_id);
    $sponserName = '';


    $count = 0;
    $attendent_emp_list = array();
      for($i = 0; $i < count($emp_list); $i++){
        $emp = $emp_list[$i];
        $Attendence = (new EmployeeAttendanceDataService())->getAnEmployeeMonthlyAttendanceDateToDateRecords($emp->emp_auto_id,$fromday,$numberOfDaysInThisMonth, $month, $year);
            if(count($Attendence) >0){
              $allAttend = array_fill(0, $numberOfDaysInThisMonth + 2, null);
              foreach($Attendence as $attend) {
                $allAttend[(int) $attend->emp_io_date] = $attend;
              }
              $emp->attendace_records = $allAttend;
              $emp->total_over_time =  (new EmployeeAttendanceDataService())->countAnEmployeeTotalOverTimeHoursFromDateToDate($emp->emp_auto_id,$fromday,$numberOfDaysInThisMonth, $month, $year);
              $emp->total_daily_work_hours = $emp->total_over_time + (new EmployeeAttendanceDataService())->countAnEmployeeTotalWorkingHoursFromDateToDate($emp->emp_auto_id,$fromday,$numberOfDaysInThisMonth, $month, $year);
              $emp->total_working_days =  count($Attendence);
              $attendent_emp_list[$count] = $emp;
              $count++;
            }
      }

      $daily_total_hours_array = array_fill(1, $numberOfDaysInThisMonth + 1, 0);
      list($totalHolidays,$holidayArray) = (new HelperController())->countTotalHolidayInThisMonth($month,$year);
      $prepared_by = Auth::user()->name;
      $working_shift = '2';
      $page_limit = $count;
      $page_offset = 0;
      return view('admin.report.employee_attendance.project_date_to_date_attend_report_preview', compact('numberOfDaysInThisMonth','day_name_in_month', 'holidayArray',
       'sponserName', 'projectName', 'company', 'attendent_emp_list', 'monthName', 'year', 'daily_total_hours_array', 'totalHolidays','working_shift','prepared_by','page_limit'
       ,'page_offset'));

  }
























    /*
    |--------------------------------------------------------------------------
    |   AJAX REQUEST METHODS
    |--------------------------------------------------------------------------
    */
  // Search Employee list For Attendance IN
    public function getListOfEmployeeWorkingInProjectForAttendanceIN(Request $request)
      {
        try{
            $daymonthyear = (new HelperController())->getDayMonthAndYearFromDateValue($request->search_date);
            $attendentedEmpLst = (new EmployeeAttendanceDataService())->getTodayAlreadyAttendedEmployeeIdListByDayMonthAndYear($daymonthyear[0],$daymonthyear[1],$daymonthyear[2]);

           // $emplist = (new EmployeeDataService())->getEmployeeLisForDailyAttendanceINByProjectId($request->project_id, 1,$attendentedEmpLst,$request->isNightShift);

             if($request->emp_ids != null){
                $allEmplId = explode(",", $request->emp_ids);
                $allEmplId = array_unique($allEmplId); // remove multiple same empl ID
                $emplist = (new EmployeeDataService())->searchEmployeesForDailyAttendanceINByMultEmpId( $allEmplId,$request->project_id,$attendentedEmpLst,$request->isNightShift);

            }else {
                $emplist = (new EmployeeDataService())->getEmployeeLisForDailyAttendanceINByProjectId($request->project_id, 1,$attendentedEmpLst,$request->isNightShift);
            }


            if (count($emplist) > 0) {
              return response()->json([ 'status' =>200, 'success'=>true, "data" => $emplist]);
            } else {
              return response()->json(['status' =>404, 'success'=>false,'error' => "error",'message'=>'Employee Not Found']);
            }
        }catch(Exception $ex){
          return response()->json(['status' =>404, 'success'=>false,'error' => "error",'message'=>'System Error Found, Reload Page & Try Again']);
        }

    }

    // Searching ALREADY Attendance IN  Records for   Attendance Out
    public function getAttendanceINAllEmployeeListForAttendaceOutAjaxRequest(Request $request)
    {
        try{
              $daymonthyear = (new HelperController())->getDayMonthAndYearFromDateValue($request->date);
              $isNightShift = $request->isNightShift;
            //   $attendentedEmpLst = (new EmployeeAttendanceDataService())->getAttendanceINEmployeeListForAttendanceOutByProjectIdDayMonthYear($request->proj_name,
            //   $daymonthyear[0],$daymonthyear[1],$daymonthyear[2],$request->isNightShift);

            if($request->emp_ids != null){
                $allEmplId = explode(",", $request->emp_ids);
                $allEmplId = array_unique($allEmplId); // remove multiple same empl ID
                $attendentedEmpLst = (new EmployeeAttendanceDataService())->getAttendanceINEmployeeListForAttendanceOutByMultipleEmpIDAndDayMonthYear( $allEmplId,$request->proj_name, $daymonthyear[0],$daymonthyear[1],$daymonthyear[2],$request->isNightShift);

            }else {
              $attendentedEmpLst = (new EmployeeAttendanceDataService())->getAttendanceINEmployeeListForAttendanceOutByProjectIdDayMonthYear($request->proj_name,
              $daymonthyear[0],$daymonthyear[1],$daymonthyear[2],$request->isNightShift);
            }

              if (count($attendentedEmpLst) > 0) {
                return response()->json([ 'status' =>200, 'success'=>true, "data" => $attendentedEmpLst]);
              } else {
                return response()->json(['status' =>404, 'success'=>false,'error' => "error",'message'=>'Employee Not Found']);
              }
        }catch(Exception $ex){
          return response()->json(['status' =>404, 'success'=>false,'error' => "error",'message'=>'System Error Found, Reload Page & Try Again']);
        }
    }

      // Searching Employee Attendance IN  Records for setting Attendance Out
//   public function getAttendanceINAllEmployeeListForAttendaceOutAjaxRequest(Request $request)
//   {
//     $day = (new HelperController())->getDayFromDateValue($request->date);
//     $month = (new HelperController())->getMonthFromDateValue($request->date);
//     $year = (new HelperController())->getYearFromDateValue($request->date);
//     $isNightShift = $request->isNightShift;
//     $attendentedEmpLst = (new EmployeeAttendanceDataService())->getAttendanceINEmployeeListForAttendanceOutByProjectIdDayMonthYear($request->proj_name,$day,$month,$year,$isNightShift);

//     if (count($attendentedEmpLst) >0) {
//       return response()->json(["entryList" => $attendentedEmpLst]);
//     } else {
//       return response()->json(['error' => "Data Not Found!"]);
//     }


//   }



    // from Searching Employee Working (Multi Project) Records page
    public function createMultiProjectWorkHistorySheet(Request $request){
        $records = (new EmployeeAttendanceDataService())->getEmployeeWorkHistoryRecordsWithEmployeeDetailsByDateToDate($request->from_date,$request->to_date,$request->employee_id ?? null);
        $company = (new CompanyDataService())->findCompanryProfile();
       
        $login_name  = Auth::user()->name;

        return view('admin.month-work.emp_multi_project_work_update_history', compact('records', 'company','login_name'));
    }

    // Salary Details History Report
    public function createSalaryDetailsHistoryReport(Request $request){
        $records = (new SalaryProcessDataService())->getSalaryDetailsRecordsWithEmployeeDetailsByDateToDate($request->from_date,$request->to_date,$request->employee_id ?? null);
//        dd($records);
        $company = (new CompanyDataService())->findCompanryProfile();
        $login_name  = Auth::user()->name;

        return view('admin.month-work.emp_salary_details_history_report', compact('records', 'company','login_name'));
    }

}
