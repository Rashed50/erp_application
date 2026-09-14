<?php

namespace Modules\HrManagement\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use App\Http\Controllers\DataServices\EmployeeRelatedDataService;
use App\Http\Controllers\DataServices\EmployeeDataService;
use App\Http\Controllers\DataServices\AuthenticationDataService;
use App\Http\Controllers\DataServices\SalaryProcessDataService;
use App\Http\Controllers\DataServices\FiscalYearDataService;
use App\Http\Controllers\Admin\Helper\HelperController;
use App\Enums\EmployeeJobStatusEnum;
use App\Enums\EmpSalaryStatusEnum;
use App\Enums\EmployeeActivityTypeEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;


class EmployeeActivityController extends Controller
{

    function __construct()
    {

        $this->middleware('permission:employee_job_status_change_activity', ['only' => ['loadEmployeeNewActivityInsertForm', 'employeeNewActivityInsertRequest']]);
        $this->middleware('permission:employee_salary_status_update', ['only' => ['employeeNewActivityWithSalaryStatusUpdateRequest']]);
    }


    public function index()
    {
        return view("hrmanagement::pages.employee_activity.emp_activity");
    }




    // Employee Job Status Update
    public function employeeNewActivityInsertRequest(Request $request)
    {

         return response()->json(['status' => 200, 'success' => true, 'data' => $request->all()]);

        $emp = (new EmployeeDataService())->getAnEmployeeInfoByEmpAutoId($request->emp_auto_id);
        if ($request->activity_type == "" || $request->job_status == "") {
            return response()->json(['status' => 404, 'success' => false, 'data' => 0, 'error' => 'Please Select Activity Type & Job Status']);
        } else if ($emp ==  null) {
            return response()->json(['status' => 404, 'success' => false, 'data' => 0, 'error' => 'Employee Not Found']);
        } else if ((int) $emp->job_status ==  0) {
            return response()->json(['status' => 404, 'success' => false, 'data' => 0, 'error' => 'Employee is not Approved Yet']);
        }

        if ($emp->job_status  != (int) $request->job_status) {
            (new EmployeeDataService())->updateEmployeeJobStatus($request->emp_auto_id, $request->job_status);
        } else {
            return response()->json(['status' => 404, 'success' => false, 'data' => 0, 'error' => 'your Selected Status and Employee Current Status are Same']);
        }


        if ($request->activity_type == 10 || (int) $request->job_status == 6 || (int) $request->job_status == 3) {
            // // 6 = Runaway , 10 = salary activity
            $salary_status = 20; // EmpSalaryStatusEnum::Salary_Hold;
            (new EmployeeDataService())->updateAnEmployeeSalaryStatus($request->emp_auto_id, $salary_status, Auth::user()->id);
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
        $this->openAnEmployeeSalaryFiscalYear($request->emp_auto_id, $request->activity_date);

        if ($insert) {

            (new AuthenticationDataService())->InsertLoginUserActivity(35, 2, Auth::user()->id, $request->emp_auto_id, null);

            return response()->json(['status' => 200, 'success' => true, 'data' => 0, 'error' => 'Successfuly Saved']);
        } else {
            return response()->json(['status' => 404, 'success' => false, 'data' => 0, 'error' => 'System Error']);
        }
    }


    private function openAnEmployeeSalaryFiscalYear($emp_auto_id, $activity_date)
    {

        if (!(new FiscalYearDataService())->checkAnEmployeeRunningFiscalYearIsAlreadyExist($emp_auto_id) && (new FiscalYearDataService())->checkAnEmployeeFiscalYearIsAlreadyExist($emp_auto_id)) {
            $start_month = (new HelperController())->getMonthFromDateValue($activity_date);
            $start_year = (new HelperController())->getYearFromDateValue($activity_date);
            $fiscal_record = (new FiscalYearDataService())->setAnEmployeeFiscalYearDuration($emp_auto_id, $start_month, $start_year, $activity_date, 0, Auth::user()->id);
        }
    }


    public function employeeNewActivityWithSalaryStatusUpdateRequest(Request $request)
    {

        // return response()->json(['status' => 200, 'success' => true, 'data' => $request->all()]);

        $emp = (new EmployeeDataService())->getAnEmployeeInfoByEmpAutoId($request->emp_auto_id);
        if ($request->activity_type == "") {


            return response()->json(['status' => 404, 'success' => false, 'data' => 0, 'error' => 'Please Select Activity Type & Job Status']);
        } else if ($emp ==  null) {

            return response()->json(['status' => 404, 'success' => false, 'data' => 0, 'error' => 'Employee  not found ']);
        }

        if ($request->salary_status) {
            (new EmployeeDataService())->updateAnEmployeeSalaryStatus($request->emp_auto_id, $request->salary_status, Auth::user()->id);
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

            (new AuthenticationDataService())->InsertLoginUserActivity(35, 2, Auth::user()->id, $request->emp_auto_id, null);

            return response()->json(['status' => 200, 'success' => true, 'data' => 0, 'error' => 'Successfuly Saved']);
        } else {

            return response()->json(['status' => 404, 'success' => false, 'data' => 0, 'error' => 'Operation Failed, Please try again']);
        }
    }
}
