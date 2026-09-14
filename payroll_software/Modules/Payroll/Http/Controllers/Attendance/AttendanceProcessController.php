<?php

namespace Modules\Payroll\Http\Controllers\Attendance;

use App\Http\Controllers\DataServices\{ProjectDataService,EmployeeAttendanceDataService,WPSEmployeeDataService};
use App\Http\Controllers\Admin\Helper\{HelperController};
use Illuminate\Http\Request;
use App\Exports\{WPSEmployeMonthlyWorkRecordAsExcel};
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Pagination\LengthAwarePaginator; // <-- This is the required fix
use Illuminate\Support\Collection; // We need this for the collect() helper
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;


class AttendanceProcessController{


    /*
    ========================================================================
    ============================= UI Methods ===============================
    ========================================================================
    */
    public function index(){

        $projects = (new ProjectDataService())->getAllActiveProjectListForDropdown();
        $data = [
            'projects'=>$projects
        ];
        return view('payroll::pages.attendance.attendance_process',['data' =>[
            'projects'=>$projects,
        ]]);
    }



    public function getWPSEmployeeMonthlyWorkingAttendanceSummaryReport( Request $request){

          try{
                    // return response()->json(['status' =>404, 'success'=>false,'error' => "error",'message'=>'Employee Not Found','data'=>$request->all()]);

                    $month = $request->month;
                    $year = $request->year;
                    $multiple_emp_ids =  $request->employee_ids;
                    $project_ids = $request->project_id;
                    $month_name = (new HelperController())->getMonthName($month);
                    $number_of_days_in_month =(new HelperController())->getNumberOfDaysInMonthAndYear($month, $year);
                    $holidays =(new HelperController())->getAMonthHolidaysAsArray($month, $year);


                    $multiple_emp_ids = explode(",", $multiple_emp_ids);
                  //  $multiple_emp_ids =[100,236,769,5017,8074];// array_unique($multiple_emp_ids); // remove multiple same empl ID
                    $multiple_emp_ids =  array_unique($multiple_emp_ids); // remove multiple same empl ID

                    // 2. Data Collection (Keeping the existing logic)
                    $final_records = []; // Use a temporary array to hold the raw results
                    $counter = 0;
                    if($project_ids != null && $project_ids != ''){
                        // $project_emp_ids = (new EmployeeRelatedDataService())->getAllWPSActiveEmployeeIdsByProjectIdsForAttendanceSummaryReport(explode(",", $project_ids));
                        $multiple_emp_ids = (new WPSEmployeeDataService())->getListOfEmployeeAutoIdThoseSalaryPaidToBankByProjectIds([$project_ids],Auth::user()->branch_office_id);
                      //  return response()->json(['status' =>404, 'success'=>false,'error' => "error",'message'=>'Employee Not Found','data'=>$multiple_emp_ids]);
                    }

                    foreach ($multiple_emp_ids as $employee_id) {
                        $emp = (new WPSEmployeeDataService())->getWPSEmployeeInfoWithBankDetailsForAttenanceSummaryReportByEmployeeId($employee_id, 1);
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
                             $emp->duty_status = '';

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

                        $final_records[$counter++] = $emp;
                    }

                    if($request->operation_type == 2){
                        // If operation_type is 1, we assume it's a download request
                            $collection_records = collect($final_records);
                            return Excel::download(new WPSEmployeMonthlyWorkRecordAsExcel($collection_records), $month_name.'_'.$year.' work_summary.xlsx');

                    } else {
                            // Proceed with pagination and response

                            $final_collection = collect($final_records);
                            // Define pagination variables
                            $per_page = 300; // Set your desired number of items per page
                            $current_page = LengthAwarePaginator::resolveCurrentPage();

                            // Slice the collection to get the items for the current page
                            $currentPageItems = $final_collection->slice(($current_page - 1) * $per_page, $per_page)->values();

                            // Create the LengthAwarePaginator instance
                            $paginated_records = new LengthAwarePaginator(
                                $currentPageItems, // Items for the current page
                                $final_collection->count(), // Total count of items
                                $per_page, // Items per page
                                $current_page, // Current page number
                                ['path' => LengthAwarePaginator::resolveCurrentPath()] // Set path for correct URL generation
                            );

                            // 4. Return the Paginated JSON Response
                            if ($paginated_records->count() > 0) {
                                // Your simplified response (using $paginated_records->items() for data)
                                return response()->json([
                                    'holidays_in_month' => $holidays,
                                    'days_in_month' => $number_of_days_in_month,

                                    'success' => true,
                                    'message' => 'Data retrieved successfully',
                                    'data'    => $paginated_records->items(), // Use items() to get only the data array for the current page
                                    'pagination' => [
                                        'current_page'   => $paginated_records->currentPage(),
                                        'last_page'      => $paginated_records->lastPage(),
                                        'per_page'       => $paginated_records->perPage(),
                                        'total'          => $paginated_records->total(),
                                        'from'           => $paginated_records->firstItem(),
                                        'to'             => $paginated_records->lastItem(),
                                        'prev_page_url'  => $paginated_records->previousPageUrl(),
                                        'next_page_url'  => $paginated_records->nextPageUrl(),
                                    ]
                                ]);
                            }

                            // Handle case where no records are found
                                return response()->json([
                                    'success' => false,
                                    'message' => 'No records found for the given criteria.',
                                    'data' => [],
                                    'pagination' => []
                                ], 404);
                        }

        }catch(Exception $ex){
            return "System Operation Failed , Please Refresh and try Again";
        }

    }


    public function processAttendanceSummaryReport( Request $request){

        try{

          if($request->report_category){
           return $this->getProjectwiseMonthByMonthTotalWorkHoursSummaryReport($request);
          }



        }catch(Exception $ex){
            return "System Operation Failed , Please Refresh and try Again";
        }

    }

    public function getProjectwiseMonthByMonthTotalWorkHoursSummaryReport(Request $request)
   {


        if(!$request->has('project_ids')){
          Session::flash('error','Please Select Project');
          return redirect()->back();
        }
        $fromday = $request->fromdate;
        $today = $request->todate;
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




}

