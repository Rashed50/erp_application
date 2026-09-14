<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\CompanyProfileController;
use App\Http\Controllers\Admin\EmployeeMultiProjectWorkHistoryController;
use App\Http\Controllers\Admin\Helper\HelperController;
use App\Http\Controllers\Admin\InOut\EmployeeInOutController;
use App\Http\Controllers\Admin\Permission\SalaryProcessPermissionController;
use App\Http\Controllers\Admin\Services\EmployeeService;
use App\Http\Controllers\DataServices\EmployeeAttendanceDataService;
use App\Http\Controllers\DataServices\EmployeeDataService;
use App\Http\Controllers\Admin\Services\EmployeeWorkService;
use App\Http\Controllers\DataServices\CompanyDataService;
use App\Http\Controllers\DataServices\ProjectDataService;
use App\Http\Controllers\DataServices\SalaryProcessDataService;
use App\Http\Controllers\DataServices\EmployeeRelatedDataService;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ImportMonthlyWorkRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\MonthlyWorkHistory;
use App\Enums\EmployeeJobStatusEnum;
use App\Models\EmployeeInfo;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;


class DailyWorkHistoryController extends Controller
{




     function __construct(){
        $this->middleware('permission:month-work-history',['only' => ['index','store',
        'getMultipleEmpMonthlyRecordInsertForm','insertMultipleEmployeeMonthlyRecord','employeeMultipleProjectWorkRecordSearchUI','multiProjectInOut']]);

         $this->middleware('permission:month-work-report',['only' => ['getEmployeMonthlyWorkHistoryRecordReportUI']]);

    }


  /*
  |--------------------------------------------------------------------------
  |  DATABASE OPERATION
  |--------------------------------------------------------------------------
  */
  public function getAll()
  {
    // $currentMonth =  Carbon::now()->format('m');
    return $all = MonthlyWorkHistory::where('status', 1)->orderBy('month_work_id', 'desc')->take(100)->get();
  }
  public function getMonthlyWorkRecords($month, $year)
  {

    $previousMonth = $month;
    if ($month == 1) {
      $previousMonth = 12;
    } else {
      $previousMonth = $month - 1;
    }

    return $all = MonthlyWorkHistory::where('month_id', $month)->orWhere('month_id', $previousMonth)->where('status', 1)->orderBy('month_work_id', 'desc')->take(20)->get();
  }

  public function getfindId($id)
  {
    return $find = MonthlyWorkHistory::with('employee')->where('status', 1)->where('month_work_id', $id)->firstOrFail();
  }

  public function deleteIdWiseMonthlyWorkHistory($month_work_id)
  {
    return $find = MonthlyWorkHistory::where('status', 1)->where('month_work_id', $month_work_id)->delete();
  }


  public function store(Request $request)
  {

    $hasAccessPermission = (new SalaryProcessPermissionController())->checkSalaryProcessPermission($request->month, $request->year);

    if ($hasAccessPermission) {
      if ($request->total_work_day == null || $request->work_hours == null || $request->total_work_day == 0) {
        Session::flash('error_null_0', 'value');
        return Redirect()->back();
      }

      $month = $request->month;
      $year = $request->year;
      $emp_auto_id = $request->emp_id;
      $project_id = $request->project_id;

       if ( (new EmployeeAttendanceDataService())->getAnEmployeeMonthlyWorkRecord($emp_auto_id, $month, $year) != null) {
          Session::flash('duplicate_data_error', 'This Month Work Record Exist');
          return Redirect()->back();

       }
      else {

        $totalHourTime =  $request->work_hours == null ? 0 : $request->work_hours;
        $startDate = Carbon::now();
        $endDate = Carbon::now()->addDay($request->total_work_day);
         (new EmployeeAttendanceDataService())->saveAnEmployeeMonthlyWorkRecrd(
            $emp_auto_id,
            $month,
            $year,
            $totalHourTime,
            $request->overtime,
            $request->total_work_day,
            $project_id
          );
        $empInOutConObj = new EmployeeInOutController();
        $empInOutConObj->insrtEmpMultiProjectWorkRecord($startDate, $endDate, $emp_auto_id, $project_id, $month, $year, $request->total_work_day, $totalHourTime, $request->overtime);

        Session::flash('success', 'Successfully Saved Data');
        return Redirect()->back();
      }
    } else {
      Session::flash('error_date', 'Permission Date over!');
      return redirect()->back();
    }
  }


  public function insertMultipleEmployeeMonthlyRecord(
    $emp_auto_id,
    $year,
    $month,
    $total_work_hours,
    $over_time,
    $total_work_day,
    $project_id
  ) {
    if ((new EmployeeAttendanceDataService())->getAnEmployeeMonthlyWorkRecord($emp_auto_id, $month,$year) != null) {
      return;
    }

    $startDate = Carbon::now();
    $endDate = Carbon::now()->addDay($total_work_day);

    (new EmployeeAttendanceDataService())->saveAnEmployeeMonthlyWorkRecrd(
      $emp_auto_id,
      $month,
      $year,
      $total_work_hours,
      $over_time,
      $total_work_day,
      $project_id
    );
    $empInOutConObj = new EmployeeInOutController();
    $empInOutConObj->insrtEmpMultiProjectWorkRecord(
      $startDate,
      $endDate,
      $emp_auto_id,
      $project_id,
      $month,
      $year,
      $total_work_day,
      $total_work_hours,
      $over_time
    );
  }

// AJAX Request Response
  public function projectWiseEmployeeListRequestForMultipleEmpWorkRecordInsert(Request $request)
  {

    $project_id = $request->project_id;
    $year = $request->year;
    $month = $request->month;
    $emplist = (new EmployeeDataService())->getAllEmployeeInfoWithSalaryDetailsThoseAreNotInMonthlyWorkRecords($project_id, 1, $year, $month);
    if (count($emplist) > 0) {
      return response()->json(["entryList" => $emplist]);
    } else {
      return response()->json(['error' => "Data Not Found!"]);
    }
  }

  public function getMultipleEmpMonthlyRecordInsertForm()
  {
    $projects = (new ProjectDataService())->getAllActiveProjectListForDropdown(Auth::user()->branch_office_id);
     $months = (new CompanyDataService())->getAllMonth();
    return view('admin.month-work.multiple-employe-montly-record-add', compact('projects', 'months'));
  }


  public function update(Request $request)
  {

    $this->validate($request, [
      'emp_id' => 'required',
      'total_work_day' => 'required|integer',
    ], []);

    if ($request->total_work_day == null || $request->work_hours == null || $request->total_work_day == 0 || $request->work_hours == 0) {
      Session::flash('error_null_0', 'value');
      return Redirect()->back();
    }


    $monhWorkConObj = new MonthlyWorkHistoryController();
    $update = $monhWorkConObj->monthliWorkHistRecrdUpdate($request->id, $request->month, $request->year, $request->work_hours, $request->overtime, $request->total_work_day);

    if ($update) {
      Session::flash('success_update', 'value');
      return Redirect()->route('add-daily-work');
    } else {
      Session::flash('error', 'value');
      return Redirect()->back();
    }
  }

  /*
  |--------------------------------------------------------------------------
  |  AJAX OPERATION
  |--------------------------------------------------------------------------
  */

  public function autocomplete(Request $request)
  {
    $data = (new EmployeeDataService())->getEmployeeByEmpIdWithLikeQuery($request->empId, 1);
    return view('admin.month-work.search', compact('data'));
  }

  public function conditionAutocomplete(Request $request)
  {
    $data = (new EmployeeDataService())->getEmployeeByEmpIdAndEmpTypeWithLikeQuery($request->empId, 1, 2);
    return view('admin.month-work.search', compact('data'));
  }

  public function findDirectEmployee(Request $request)
  {
    $data = (new EmployeeDataService())->getEmployeeByEmpIdAndEmpTypeWithLikeQuery($request->empId, 1, 1);
     return view('admin.month-work.search2', compact('data'));
  }

  public function findEmployeeTypeId(Request $request)
  {
    $findType = EmployeeInfo::where('emp_type_id', $request->emp_type_id)->where('job_status', 1)->first();
    return json_encode($findType);
  }


  public function deleteDailyWorkHistory($id)
  {


    $empMonthWorkHistory = $this->getfindId($id);
    $emp_id = $empMonthWorkHistory->emp_id;
    $month_id = $empMonthWorkHistory->month_id;
    $year_id = $empMonthWorkHistory->year_id;

    $month_work_id = $empMonthWorkHistory->month_work_id;

    $DeleteEmpMonthWorkHistory = $this->deleteIdWiseMonthlyWorkHistory($month_work_id);

    $empMultiProjConObj = new EmployeeMultiProjectWorkHistoryController();
    $deleteEmpMultiProject =  $empMultiProjConObj->deleteMonthAndYearWiseAnEmpMultiprojectWorkHistory($emp_id, $month_id, $year_id);

    return response()->json();
  }





  /*
  |--------------------------------------------------------------------------
  |  BLADE OPERATION
  |--------------------------------------------------------------------------
  */

    public function index()
  {

    $emp_type_id = (new EmployeeRelatedDataService())->getEmployeeTypeForDropdown();
    $currentMonth =  Carbon::now()->format('m');
    $currentYear =  Carbon::now()->format('y');
    $all = $this->getMonthlyWorkRecords((int)$currentMonth, (int)$currentYear);
    $projects = (new ProjectDataService())->getAllActiveProjectListForDropdown(Auth::user()->branch_office_id);
    $month = (new CompanyDataService())->getAllMonth();
    return view('admin.month-work.create', compact('all', 'emp_type_id', 'month', 'currentMonth', 'projects'));
  }


  public function edit($id)
  {

    $month = (new CompanyDataService())->getAllMonth();
    $currentMonth =  Carbon::now()->format('m');
    $edit = $this->getfindId($id);
    return view('admin.month-work.edit', compact('edit', 'currentMonth', 'month'));
  }


  // Blade File For Monthly Work Hisotry Processing Report
//   public function getEmployeMonthlyWorkHistory()
//   {
//     $projects = (new EmployeeRelatedDataService())->getAllProjectInfoForDropdown();
//     $month = (new CompanyDataService())->getAllMonth();
//     $sponser = (new EmployeeRelatedDataService())->getAllSponserInfoForDropdown();
//     $emplyoyeeStatus = (new HelperController())->getEmployeeStatus();

//     return view('admin.month-work.monthwork-reportprocess', compact('projects', 'month', 'sponser', 'emplyoyeeStatus'));
//   }



  // EMPLOYEE THOSE ARE NOT IN WORK RECROD REPORT

//   public function processEmployeNotPresentInMonthlyWorkHistory(Request $request)
//   {

//     $company = (new CompanyProfileController())->findCompanry();
//     $month = $request->month;
//     $monthName = (new HelperController())->getMonthName($month);
//     $projectId = $request->proj_id;
//     $sponserId = $request->SponsId;
//     $emp_status_id = $request->emp_status_id;
//     $year = Carbon::now()->format('Y');
//     $projectName = (new EmployeeRelatedDataService())->findAProjectInformation($projectId);


//     $list =  (new EmployeeMultiProjectWorkHistoryController())->getListOfEmployeeAutoIdExistInMultiProectWorkRecord($month, $year);
//     if ($emp_status_id == 0 && $projectId == 0 && $sponserId == 0) {

//       $employee = EmployeeInfo::leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
//         ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
//         ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
//         ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
//         ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
//         ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
//         ->leftjoin('monthly_work_histories', 'employee_infos.emp_auto_id', '=', 'monthly_work_histories.emp_id')
//         ->whereNotIn('employee_infos.emp_auto_id', $list)
//         ->get();

//     } else if ($emp_status_id == 0 && $projectId == 0 && $sponserId > 0) {

//       $employee = EmployeeInfo::where("employee_infos.sponsor_id", $sponserId)
//         ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
//         ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
//         ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
//         ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
//         ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
//         ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
//         ->leftjoin('monthly_work_histories', 'employee_infos.emp_auto_id', '=', 'monthly_work_histories.emp_id')
//         ->whereNotIn('employee_infos.emp_auto_id', $list)
//         ->get();
//     } else if ($emp_status_id == 0 && $projectId > 0 && $sponserId == 0) {
//       $employee = EmployeeInfo::where("employee_infos.project_id", $projectId)
//         ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
//         ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
//         ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
//         ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
//         ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
//         ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
//         ->leftjoin('monthly_work_histories', 'employee_infos.emp_auto_id', '=', 'monthly_work_histories.emp_id')
//         ->whereNotIn('employee_infos.emp_auto_id', $list)
//         ->get();
//     } else if ($emp_status_id == 0 && $projectId > 0 && $sponserId > 0) {
//       $employee = EmployeeInfo::where("employee_infos.sponsor_id", $sponserId)
//         ->where("employee_infos.project_id", $projectId)
//         ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
//         ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
//         ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
//         ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
//         ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
//         ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
//         ->leftjoin('monthly_work_histories', 'employee_infos.emp_auto_id', '=', 'monthly_work_histories.emp_id')
//         ->whereNotIn('employee_infos.emp_auto_id', $list)
//         ->get();
//     } else if ($emp_status_id > 0 && $projectId == 0 && $sponserId == 0) {

//       $employee = EmployeeInfo::where('employee_infos.job_status', $emp_status_id)
//         ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
//         ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
//         ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
//         ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
//         ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
//         ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
//         ->leftjoin('monthly_work_histories', 'employee_infos.emp_auto_id', '=', 'monthly_work_histories.emp_id')
//         ->whereNotIn('employee_infos.emp_auto_id', $list)
//         ->get();
//     } else if ($emp_status_id > 0 && $projectId == 0 && $sponserId > 0) {

//       $employee = EmployeeInfo::where('employee_infos.job_status', $emp_status_id)
//         ->where("employee_infos.sponsor_id", $sponserId)
//         ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
//         ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
//         ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
//         ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
//         ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
//         ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
//         ->leftjoin('monthly_work_histories', 'employee_infos.emp_auto_id', '=', 'monthly_work_histories.emp_id')
//         ->whereNotIn('employee_infos.emp_auto_id', $list)
//         ->get();
//     } else if ($emp_status_id > 0 && $projectId > 0 && $sponserId == 0) {

//       $employee = EmployeeInfo::where('employee_infos.job_status', $emp_status_id)
//         ->where("employee_infos.project_id", $projectId)
//         ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
//         ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
//         ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
//         ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
//         ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
//         ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
//         ->leftjoin('monthly_work_histories', 'employee_infos.emp_auto_id', '=', 'monthly_work_histories.emp_id')
//         ->whereNotIn('employee_infos.emp_auto_id', $list)
//         ->get();
//     } else if ($emp_status_id > 0 && $projectId > 0 && $sponserId > 0) {
//       $employee = EmployeeInfo::where('employee_infos.job_status', $emp_status_id)
//         ->where("employee_infos.sponsor_id", $sponserId)
//         ->where("employee_infos.project_id", $projectId)
//         ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
//         ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
//         ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
//         ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
//         ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
//         ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
//         ->leftjoin('monthly_work_histories', 'employee_infos.emp_auto_id', '=', 'monthly_work_histories.emp_id')
//         ->whereNotIn('employee_infos.emp_auto_id', $list)
//         ->get();
//     }
//     if (count($employee) > 0) {
//       return view('admin.month-work.report.report-employee-not-in-work-record', compact('employee', 'company', 'monthName'));
//     } else {
//       return 'Record Not Found ';
//     }
//   }


//   public function processAllEmployeMonthlyWorkStatus(Request $request)
//   {
//     $year = (new HelperController())->getYear();
//     $project = "All";
//     $sponser = 'All';
//     $month = (new HelperController())->getMonthName($request->month);
//     $company = (new CompanyProfileController())->findCompanry();
//     $totalActiveEmployee = (new  EmployeeDataService())->countTotalEmployees(1);
//     $totalEmployee = (new EmployeeDataService())->countTotalEmployees(0);
//     $totalWokringEmp = (new EmployeeWorkService())->getTotalWorkingEmployees($request->month, $year);
//     return view('admin.month-work.report.employee-work-status-summary', compact('company', 'project', 'month', 'year', 'totalActiveEmployee', 'totalEmployee', 'totalWokringEmp'));
//   }




  /* +++++++++++++++ Project Wise Month Work History +++++++++++++++ */

//   public function displayEmployeMonthlyWorkHistoryReport()
//   {

//     $emp_type_id = (new EmployeeRelatedDataService())->getAllEmployeeType();

//     $all = $this->getAll();

//     $month = (new CompanyDataService())->getAllMonth();

//     $currentMonth =  Carbon::now()->format('m');
//     return view('admin.month-work.report-employe-monthwork', compact('all', 'emp_type_id', 'month', 'currentMonth'));
//   }



  // Udpate EMployee Monthly WOrk Record Details From Multiproject Work Menu

  public function searchAnEmployeeMonthWorkRecordDetails(Request $request)
  {


    $empInfo = (new EmployeeDataService())->getAnEmployeeInfoWithSalaryDetailsByEmpId($request->emp_id);
    $months = (new CompanyDataService())->getAllMonth();
    $projects = (new ProjectDataService())->getAllActiveProjectListForDropdown(Auth::user()->branch_office_id);
     if($empInfo){
        $monthWorkRecord = (new EmployeeAttendanceDataService())->getAnEmployeeMonthlyWorkRecord($empInfo->emp_auto_id,$request->month,$request->year);

        if ($monthWorkRecord == null) {
          return response()->json(["status" =>"error","error" => "Employee Work Record Not Found"]);
        }
        return response()->json(["monthWorkRecord" => $monthWorkRecord,'emp'=>$empInfo,"month"=>$months,"projects"=>$projects, "error" => null]);
     }
    return response()->json(["status" =>"error", "error" => "Work Record Not Found"]);

  }


    // Udpate Employee WOrk Record

    public function updateAnEmployeeMultipleProjectWorkRecordRequest(Request $request){


        $salary_is_paid = (new SalaryProcessDataService())->checkAnEmployeeSalaryIsAlreadyPaid( $request->modal_emp_auto_id, $request->modal_month, $request->modal_year);

        if($salary_is_paid){
            Session::flash('error', 'This Month Salary Already Paid , Update Not Possible');
            return Redirect()->back();
        }else if ($request->modal_total_hour == null || $request->modal_total_hour == "") {
            Session::flash('error', 'Data Erro, Updat Operation Failed');
            return Redirect()->back();
        } else  if ($request->modal_total_overtime == null || $request->modal_total_overtime == "") {
            Session::flash('error', 'Data Erro, Updat Operation Failed');
            return Redirect()->back();
        } else if ($request->modal_total_day == null || $request->modal_total_day == "") {
            Session::flash('error', 'Data Erro, Updat Operation Failed');
            return Redirect()->back();
        }

            $update =  (new EmployeeAttendanceDataService())->updateAnEmployeeMultipleProjectWorkRecordWithAllColum(
            $request->modal_empwh_auto_id, $request->modal_project_name, $request->modal_month, $request->modal_year, $request->modal_total_day,
            $request->modal_total_hour, $request->modal_total_overtime,$request->modal_start_date,$request->modal_end_date,Auth::user()->id);

            (new EmployeeAttendanceDataService())->calculateAnEmployeeMontlyTotalWorkFromMultiProjectWorkAndUpdateMonthlyRecord(
                $request->modal_emp_auto_id,
                $request->modal_month,
                $request->modal_year,
                $request->modal_project_name,
                Auth::user()->id
            );
            Session::flash('success', 'Successfully Updated');
            return Redirect()->back();

    }




    /* ================================================================
         Upload Employee Monthly Work Record Excel File
       ================================================================
    */

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

// Import Work Record Excel File to Temporary Table
public function importEmployeeMonthlyWorkRecordsFromExcel(Request $request)
{
            if ($request->file && $request->month) {

                    $file = $request->file;
                    $project_id = $request->proj_name;
                    $month = $request->month;
                    $year = $request->year;
                    $operation_type = $request->operation_type;
                    $extension = $file->getClientOriginalExtension(); //Get extension of uploaded file
                    $fileSize = $file->getSize(); //Get size of uploaded file in bytes
                    if($this->checkUploadedFileProperties($extension, $fileSize)){

                                $import = new ImportMonthlyWorkRecord($project_id,$month,$year, $operation_type);
                                Excel::import($import, $request->file('file'));
                                return response()->json([
                                    'status' => 200,
                                    'success'=> true,
                                    'records_not_found' => $import->records_not_found,
                                    'records' => $import->records,
                                    'message' => $import->records->count() ." records successfully uploaded"
                                ]);
                    }else {

                        return response()->json([
                            'status' => 404,
                            'success'=> false,
                            'message' =>  " Invalid File Format Or Large file size"
                        ]);
                    }

            } else {
                return response()->json([
                    'status' => 403,
                    'success'=> false,
                    'message' =>  " file not found"
                ]);
            }
}
// Submit Imported Excell Data To Final Table
public function submitEmployeeMonthlyWorkRecordsImportFromExcel(Request $request){

    try{


        $result = (new EmployeeAttendanceDataService())->getEmployeeWorkRecordImportedExcellDataFromTable();
        $startDate = Carbon::now()->firstOfMonth()->format('Y-m-d');
      //  DB::beginTransaction();
        foreach ($result as $arecord){
            $endDate = Carbon::parse($startDate)->addDay(10); // error in latest php version 8.4, $arecord->working_days
            $isExist = (new EmployeeAttendanceDataService())->checkAnEmployeeThisProjectWorkRecordsIsExist($arecord->emp_auto_id,$arecord->month_id,$arecord->year_id,$arecord->project_id);
            if($isExist == false){
                 (new EmployeeAttendanceDataService())->insertAnEmployeeMonthlyWorkRecordByUploadExcelFile($arecord->emp_auto_id,$arecord->month_id,$arecord->year_id,$arecord->project_id,
            $arecord->basic_hours,$arecord->over_time,$arecord->working_days,$startDate,$endDate,Auth::user()->branch_office_id,$arecord->paid_leave,$arecord->remarks);

            }

        }
        // remove all records from temporary table
        (new EmployeeAttendanceDataService())->deleteEmployeeWorkRecordImportedExcellDataFromTable();
        //  DB::commit();

        return response()->json([
            'status' =>  200,
            'success' => true,
            'message' => 'Successfully Uploaded'
        ]);
    }catch(Exception $ex){

      //  DB::rollBack();
        (new EmployeeAttendanceDataService())->deleteEmployeeWorkRecordImportedExcellDataFromTable();
        return response()->json([
            'error' =>  "Data Upload Failed",
            'status' => 500,
            'success' => false,
        ]);
    }
}





    /* ================================================================
         Monthly Work Records Report
       ================================================================
    */
    // User Interface for Monthly Work Record Report Processing
    public function getEmployeMonthlyWorkHistoryRecordReportUI()
    {
        $projects = (new ProjectDataService())->getAllActiveProjectListForDropdown(Auth::user()->branch_office_id);
        $month = (new CompanyDataService())->getAllMonth();
        $sponser = (new EmployeeRelatedDataService())->getAllSponserInfoForDropdown(Auth::user()->branch_office_id);
        $emplyoyeeStatus = EmployeeJobStatusEnum::cases();
      //  $emplyoyeeStatus = (new HelperController())->getEmployeeStatus();
      //  dd($emplyoyeeStatus);
        return view('admin.month-work.report.month_work_report_process_ui', compact('projects', 'month', 'sponser', 'emplyoyeeStatus'));
    }

    /* +++++++++++++++ Project and sponser wise month and year base work records report +++++++++++++++ */

     public function getEmployeMonthlyWorkHistoryProcess(Request $request)
    {

        try{


            $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);

            $year = (int) $request->year;
            $month = (int) $request->month;
            $project_ids =   $request->proj_id ;
            
            if(is_null($project_ids))
            {
                $project_ids = (new ProjectDataService())->getAllActiveProjectIDOfABranchOfficeAsArray(Auth::user()->branch_office_id);
                $report_title[0]  = "All Running Project";
            }
            $sponsor_ids =  $request->has('SponsId') ?   $request->SponsId : (new EmployeeRelatedDataService())->getAllActiveSponsorIdAsArrayOfABranchOffice(Auth::user()->branch_office_id);

            $report_title[0]  = "Multiple Project ";
            $report_title[1]  = (new HelperController())->getMonthName($month);
            $report_title[2]  = $year;

            if($request->data_source == 1){
                // monthly work record report

               $records =  (new EmployeeAttendanceDataService())->getMonthlyWorkedRecordsWithEmployeeDetailsReport($project_ids,$sponsor_ids,$month,$year);

               //  dd( $request->all());
                // $records =  (new EmployeeAttendanceDataService())->processDuplicateWorkRecordsCleanupForMonthlyWorkRecord($project_ids[0],$month,$year);
                // dd($records);

                return view('admin.month-work.report.emp_month_work_report', compact('records', 'company', 'report_title'));
            }
            else if($request->data_source == 2){
                // work record from multi project records
                 $work_records =  (new EmployeeAttendanceDataService())->getEmployeesMultiProectWorkRecordsByProjectIdSponsorIdMonthYear($project_ids,$sponsor_ids,$month,$year);

                // $work_records =  (new EmployeeAttendanceDataService())->processForDuplicateWorkRecordsCleanup($project_ids[0],$month,$year);
                // dd( $work_records);

                return view('admin.month-work.report.emp_multi_project_work_record_report', compact('work_records', 'company', 'report_title'));

            }
            else if($request->data_source == 3){
                    // multiproject work records from attendance inout
                $inout_summary_records =  (new EmployeeAttendanceDataService())->getEmployeesAttendanceInOutSummaryOfAMonthByProjectId($project_ids,$month,$year);
                return view('admin.month-work.report.emp_attendance_inout_summary_report', compact('inout_summary_records', 'company', 'report_title'));

            }else if($request->data_source == 4){

                 $records =  (new EmployeeAttendanceDataService())->getSalaryPaidByBankEmployeeMonthlyWorkRecordsReport($project_ids,$sponsor_ids,$month,$year);

                return view('admin.month-work.report.bank_paid_emp_month_work_report', compact('records', 'company', 'report_title'));


            }
             else if($request->data_source == 5){

                // month by month work summary report
                $month_years = (new HelperController())->getMonthsInRangeOfDate($request->from_date,$request->to_date);
                //$project_ids = $request->project_ids;
                $project_ids = $request->has('project_ids') ? $request->project_ids : (new ProjectDataService())->getAllActiveProjectIDOfABranchOfficeAsArray(Auth::user()->branch_office_id);

                $final_records = [];
                $c = 0;
                foreach($project_ids as $pid){
                    $project = (new ProjectDataService())->findAProjectInformation($pid);
                    $records = [];
                    $counter =0;
                    foreach($month_years as $my){
                        $records[$counter++] =  (new EmployeeAttendanceDataService())->getAProjectActualTotalWorkingHoursForWorkingStatementReport($pid,$my['month'],$my['year']);
                    }
                    $project->records = $records;
                    $final_records[$c++] = $project;

                }
               // dd($final_list);

                $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
                $login_name=Auth::user()->name;
                return view('admin.month-work.report.month_by_month_work_summary', compact('final_records', 'company', 'month_years','login_name'));

            }
            else if($request->data_source == 7){
                // work record update history report
                $employee_ids = $request->employee_ids;
                 
                 $employee_ids = explode(",", $request->employee_ids);
                 $employee_ids = array_unique($employee_ids); // remove multiple same empl ID


                $records =  (new EmployeeAttendanceDataService())->getMultiEmployeesMultiProjectWorkRecordUpdateHistoryForReport($employee_ids,$project_ids,$sponsor_ids,$month,$year);
                $report_title = "";
                $login_name = Auth::user()->name;
                return view('admin.month-work.report.work_records_update_history', compact('records', 'company', 'report_title','month','year','login_name'));

 
            }


        }catch(Exception $ex){
            return "Data Validation Error";
        }


    }
  public function getEmployeMonthlyWorkHistoryProcess2(Request $request)
    {
        try{

            $company = (new CompanyDataService())->findCompanryProfile();
            $year = (int) $request->year;
            $month = (int) $request->month;
            $project_id = (int) $request->proj_id;
            $sponsor_id = (int) $request->SponsId;

            $report_title[0]  = (new ProjectDataService())->getProjectNameByProjectId($project_id);
            $report_title[1]  = (new HelperController())->getMonthName($month);
            $report_title[2]  = $year;

            if($request->data_source == 1){
                // monthly work record report
                $records =  (new EmployeeAttendanceDataService())->getEmployeeMonthlyWorkRecordsReport($project_id,$sponsor_id,$month,$year);
                return view('admin.month-work.report.emp_month_work_report', compact('records', 'company', 'report_title'));
            }
            else if($request->data_source == 2){
                // work record from multi project records
                $sponsor_id =  $sponsor_id == 0 ? null: $sponsor_id;
                $work_records =  (new EmployeeAttendanceDataService())->getEmployeesMultiProectWorkRecordsByProjectIdSponsorIdMonthYear($project_id,$sponsor_id,$month,$year);
                return view('admin.month-work.report.emp_multi_project_work_record_report', compact('work_records', 'company', 'report_title'));

            }
            else if($request->data_source == 3){
                    // multiproject work records from attendance inout
                $emp_ids = (new EmployeeDataService())->getAllEmployeesIdAsArrayInTheProject($project_id);
                $inout_summary_records =  (new EmployeeAttendanceDataService())->getEmployeesAttendanceInOutSummaryOfAMonthByProjectId($project_id,$month,$year);
                return view('admin.month-work.report.emp_attendance_inout_summary_report', compact('inout_summary_records', 'company', 'report_title'));

            }

        }catch(Exception $ex){
            return "Data Validation Error";
        }

    }




    // EMPLOYEE THOSE ARE NOT IN WORK RECROD REPORT
    public function processEmployeNotPresentInMonthlyWorkHistory(Request $request)
    {

        $company = (new CompanyDataService())->findCompanryProfile();
        $month = $request->month;

        $project_id = $request->proj_id;
        $sponsor_id = $request->SponsId;
        $emp_status_id = $request->emp_status_id;
        $year = $request->year;

        $report_title[0]  = (new ProjectDataService())->getProjectNameByProjectId($project_id);
        $report_title[1]  = (new HelperController())->getMonthName($month);
        $report_title[2]  = $year;
        $records =  (new EmployeeAttendanceDataService())->getEmployeesThoseAreNotInMonthlyWorkRecords($project_id,$sponsor_id,$emp_status_id,$month,$year);

        return view('admin.month-work.report.emps_those_not_in_work_record', compact('records', 'company', 'report_title'));

    }


    public function processAllEmployeMonthlyWorkStatus(Request $request)
    {
        $year = (new HelperController())->getYear();
        $project = "All";
        $sponser = 'All';
        $month = (new HelperController())->getMonthName($request->month);
        $company = (new CompanyDataService())->findCompanryProfile();
        $totalActiveEmployee = (new  EmployeeDataService())->countTotalEmployees(1);
        $totalEmployee = (new EmployeeDataService())->countTotalEmployees(0);
        $totalWokringEmp = (new EmployeeAttendanceDataService())->countTotalWorkingEmployees($request->month, $year);

        return view('admin.month-work.report.employee-work-status-summary', compact('company', 'project', 'month', 'year', 'totalActiveEmployee', 'totalEmployee', 'totalWokringEmp'));
    }


    /* +++++++++++++++ Project Wise Month Work History +++++++++++++++ */

    public function displayEmployeMonthlyWorkHistoryReport()
    {

        $emp_type_id = (new EmployeeRelatedDataService())->getEmployeeTypeForDropdown();
        $all = $this->getAll();
        $month = (new CompanyDataService())->getAllMonth();
        $currentMonth =  Carbon::now()->format('m');
        return view('admin.month-work.report-employe-monthwork', compact('all', 'emp_type_id', 'month', 'currentMonth'));
    }




}
