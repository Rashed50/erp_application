<?php

namespace Modules\Payroll\Http\Controllers\Attendance;

use App\Http\Controllers\DataServices\{ProjectDataService,CompanyDataService,EmployeeAttendanceDataService,WPSEmployeeDataService};
use App\Http\Controllers\Admin\Helper\{HelperController};
use Illuminate\Http\Request;
// use App\Exports\{WPSEmployeMonthlyWorkRecordAsExcel};
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Pagination\LengthAwarePaginator; // <-- This is the required fix
use Illuminate\Support\Collection; // We need this for the collect() helper
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;


class AttendanceReportController{


    /*
    ========================================================================
    ============================= UI Methods ===============================
    ========================================================================
    */



    public function processAttendanceSummaryReport( Request $request){




          if($request->report_category == 1){
                return $this->getProjectwiseMonthByMonthTotalWorkHoursSummaryReport($request);
          }





    }

    public function getProjectwiseMonthByMonthTotalWorkHoursSummaryReport(Request $request)
    {
        try{

                if($request->project_ids == null){
                    $projects = (new ProjectDataService())->getAllActiveProjectListForDropdown();
                }else{

                    $pids = explode(",", $request->project_ids);
                    $pids = array_unique($pids); // remove multiple same empl ID
                    $projects = (new ProjectDataService())->getProjectListByMultipleProjectId($pids,Auth::user()->branch_office_id);
                }

                $from_date = $request->from_date;
                $to_date = $request->to_date;
                $company = (new CompanyDataService())->findCompanryProfile();
                $monthwithYears =  (new HelperController())->getMonthsInRangeOfDate($from_date, $to_date);
                $months = array();
                $years = array();
                $counter = 0;
                foreach ($monthwithYears as $my) {
                    $months[$counter] = $my['month'];
                    $years[$counter] =  $my['year'];
                    $counter++;
                }
                $final_list = array();
                $counter = 0;

                foreach($projects as $p){
                        $work_records = array();
                        $c = 0;
                        $is_data_found=false;

                        foreach ($monthwithYears as $my) {
                            $record = (new EmployeeAttendanceDataService())->getAProjectSingleMonthTotalWorkHoursFromAttendanceRecords($p->proj_id,$my['month'],$my['year']);
                            if($record==null){
                                $record = array(
                                    'basic_hours'=> 0,
                                    'over_time'=> 0,
                                    'total_emp'=> 0,
                                    'emp_io_month'=>  $my['month'],
                                    'emp_io_year'=>  $my['year'],
                                );
                                $record = (object) $record;
                            }else{
                                $is_data_found = true;
                            }
                            $work_records[$c++]= $record;
                        }
                        if($is_data_found){
                        $p->records = $work_records;
                        $final_list[$counter++] = $p;
                        }

                }

                // return $final_list;
                $login_name = Auth::user()->name;
                return view('payroll::pages.attendance..reports.multi_project_month_by_month_work_hours', compact('final_list','months','years', 'company','login_name'));

        }catch(Exception $ex){
            return "System Operation Failed , Please Refresh and try Again";
        }


    }




}

