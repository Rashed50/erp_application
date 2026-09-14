<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DataServices\EmployeeDataService;
use App\Http\Controllers\DataServices\EmployeeAttendanceDataService;
use App\Http\Controllers\DataServices\AuthenticationDataService;
use App\Http\Controllers\DataServices\ProjectDataService;
use App\Http\Controllers\DataServices\TransportationDataService;
use App\Http\Controllers\Admin\Helper\HelperController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function index()
    {
        $noOfProjects = (new ProjectDataService())->countTotalNumberOfRunningPrjects(Auth::user()->branch_office_id);
        $noOfUsers = (new AuthenticationDataService())->countTotalUsersInABranchOffice(Auth::user()->branch_office_id);
        $emp_record = (new EmployeeDataService())->countTotalNumberOfActiveEmployeesInABranchOfficeByCompanyAndOtherSponsorForDashboard(Auth::user()->branch_office_id); // active employee
        $noOfAsloobEmp = $emp_record[0]->total_emp;
        $noOfOtherEmp = $emp_record[1]->total_emp;
        $noOfEmpl_vacation = (new EmployeeDataService())->countTotalNumberOfEmployeesInABranchOffice(5,Auth::user()->branch_office_id); // vacation employee

        $day = (int) date('d');
        $month = (int) date('m');
        $year = (int)date('Y');

        $attendance_summary = (new EmployeeAttendanceDataService())->getTodayAttendanceSummaryReportInABranchOffice(-1,$day,$month,$year,Auth::user()->branch_office_id);
        $day_month_year = (new HelperController())->getDayMonthAndYearFromDateValue(date('d-m-Y',strtotime("-1 days")));

        $yesterday_nightshift_attend_summary = (new EmployeeAttendanceDataService())->getYesterdayNightshiftAttendanceSummaryReportOfABranchOffice(1,$day_month_year[0],$day_month_year[1],$day_month_year[2],Auth::user()->branch_office_id);
        $yesterday_dayshift_attend_summary = (new EmployeeAttendanceDataService())->getYesterdayDayshiftAttendanceSummaryReportOfABranchOffice(1,$day_month_year[0],$day_month_year[1],$day_month_year[2],Auth::user()->branch_office_id);
        $yesterday_present = (new EmployeeAttendanceDataService())->countTotalNumberOfWorkersPresentInADayOfABranchOffice($day_month_year[0],$day_month_year[1],$day_month_year[2],Auth::user()->branch_office_id);

        $total_vehicles = (new TransportationDataService())->countTotalNumberOfVehicles();
        $project_ids = (new ProjectDataService())->getLoginUserAccessPermissionProjectIDsArray(Auth::user()->id);
        $vehicle_summary = (new TransportationDataService())->getProjectwiseAssignedVehicleSummaryByLoginUserAccessPermision( $project_ids);

        return view('admin.dashboard.index', compact('noOfProjects', 'noOfAsloobEmp','noOfOtherEmp','noOfEmpl_vacation', 'noOfUsers','attendance_summary','yesterday_dayshift_attend_summary','yesterday_nightshift_attend_summary','yesterday_present','total_vehicles','vehicle_summary'));


    }

    public function getFilePreviewUrl($file_location)
    {
        return response()->json(['preview_url' => 'https://abccpayroll.s3.us-east-1.amazonaws.com/'+ $previewUrl]);
    }
}
