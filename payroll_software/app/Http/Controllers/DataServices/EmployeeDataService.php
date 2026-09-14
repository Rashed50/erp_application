<?php

namespace App\Http\Controllers\DataServices;

use App\Http\Controllers\Controller;
use App\Http\Requests\NewEmpFormRequest;
use App\Models\EmployeeInfo;
use App\Models\SalaryDetails;
use App\Models\EmployeeDetails;
use App\Models\MonthlyWorkHistory;
use App\Models\EmpExpertTrade;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Admin\Helper\UploadDownloadController;
use Carbon\Carbon;

use Illuminate\Support\Facades\Log;

class EmployeeDataService //extends Controller
{


    public function generateEmployeeId()
    {
        $all = EmployeeInfo::count();

        for ($i = 2500; $i <= $all; $i++) {
            if ($this->checkThisEmployeeIdIsAlreadyAssigned($i) == null) {
                break;
            }
        }
        return $i;
    }

    // Next New  Inserting Employee Unique Employee ID
    public function searchNewEmployeeUniqueEmployeeID($new_emp_type)
    {
        if ((int)$new_emp_type == 1) {
            // company sponsor
            return $this->generateEmployeeId();
        }
        // other sponsor
        for ($i = 13000; $i <= 20000; $i++) {
            if ($this->checkThisEmployeeIdIsAlreadyAssigned($i) == null) {
                break;
            }
        }
        return $i;
    }

    public function checkThisEmployeeIdIsAlreadyAssigned($employee_id)
    {
        return EmployeeInfo::where('employee_id', $employee_id)->first();
    }

    public function checkThisValueIsExistInServerDatabase($value, $db_colum)
    {

        return (EmployeeInfo::where($db_colum, $value)->count() > 0 ? true : false);
    }

    public function checkThisEmployeSalaryIsHourlyByEmployeeAutoId($emp_auto_id)
    {
        return (EmployeeInfo::select("hourly_employee")->where('emp_auto_id', $emp_auto_id)->first())->hourly_employee;
    }


    public function insertNewEmployee($request)
    {
        if (
            $this->checkThisEmployeeIdIsAlreadyAssigned($request->emp_id) ||
            $this->checkThisValueIsExistInServerDatabase($request->akama_no, 'akama_no')  ||
            $this->checkThisValueIsExistInServerDatabase($request->passport_no, 'passfort_no')
        ) {
            return -1;
        }





        return  $emp_auto_id = EmployeeInfo::insertGetId([
            'employee_id' => $request->employee_id,
            'employee_name' => $request->emp_name,
            'akama_no' => $request->akama_no,
            'akama_expire_date' => $request->akama_expire,
            'passfort_no' => $request->passfort_no,
            'passfort_expire_date' => $request->passfort_expire_date,
            'sponsor_id' => $request->sponsor_id,
            'mobile_no' => $request->mobile_no,
            'country_id' => $request->country_id,
            'division_id' => $request->division_id,
            'district_id' => $request->district_id,
            'post_code' => $request->post_code,
            'details' => $request->details,
            'present_address' => $request->present_address, //
            'emp_type_id' => $request->emp_type_id,
            'designation_id' =>  $request->designation_id,
            'hourly_employee' => $request->hourly_employee,

            'project_id' => $request->project_id,
            'department_id' => $request->department_id, //
            'date_of_birth' => $request->date_of_birth,
            'phone_no' => $request->phone_no,
            'email' => $request->email,
            'maritus_status' => 1, //  $request->maritus_status,//
            'gender' => 1, //  $request->gender,//
            'religion' => $request->religion, //
            'joining_date' => $request->joining_date,
            'confirmation_date' => $request->confirmation_date,
            'appointment_date' => $request->appointment_date,
            'agc_info_auto_id' => $request->agency_id, //
            'company_id' => $request->company_id,
            'accomd_ofb_id' => $request->accomd_ofb_id,
            'entry_date' => Carbon::now(),
            'entered_id' => Auth::user()->id,
            'created_at' => Carbon::now(),
            'branch_office_id' => Auth::user()->branch_office_id,
            'created_at' => Carbon::now(),
        ]);
    }

    /*
     ==========================================================================
     ============================= Count Method ============================
     ==========================================================================
    */

    public function countTotalEmployees($jobstatus)
    {
        if ($jobstatus < 0 || $jobstatus == null) {
            return  EmployeeInfo::count();
        } else
            return  EmployeeInfo::where('job_status', $jobstatus)->count();
    }

    public function countTotalNumberOfEmployeesInABranchOffice($jobstatus, $branch_office_id)
    {
        if ($jobstatus < 0 || is_null($jobstatus)) {
            return  EmployeeInfo::where('branch_office_id', $branch_office_id)->count();
        } else
            return  EmployeeInfo::where('branch_office_id', $branch_office_id)->where('job_status', $jobstatus)->count();
    }

    public function countTotalNumberOfActiveEmployeesInABranchOfficeByCompanyAndOtherSponsorForDashboard($branch_office_id)
    {
        return  EmployeeInfo::select(
            'sponsors.owner_name',
            DB::raw("COUNT(employee_infos.emp_auto_id) as total_emp")
        )
            ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
            ->where('employee_infos.branch_office_id', $branch_office_id)
            ->where('employee_infos.job_status', 1)
            ->groupBy("sponsors.owner_name")
            ->get();
    }



    // Tradewise  Attendance Summary Report
    public function countTradewiseTotalActiveEmployeeInProject($project_id_list, $working_shift)
    {

        return EmployeeInfo::select(
            'designation_id',
            'employee_categories.catg_name',
            DB::raw("COUNT(emp_auto_id) as total_emp_in_project")
        )
            ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
            ->groupBy("designation_id")
            ->groupBy('employee_categories.catg_name')
            ->where('job_status', 1)
            ->where('isNightShift', $working_shift)
            ->whereIn('project_id', $project_id_list)
            ->orderBy('employee_categories.rank_code', 'ASC')
            ->get();
    }

    public function getProjectsEmployeeSummaryBySponsorReport($project_id_list)
    {

        return EmployeeInfo::select(
            'sponsors.spons_name',
            DB::raw("COUNT(employee_infos.emp_auto_id) as total_emp")
        )
            ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
            ->groupBy("employee_infos.sponsor_id")
            ->groupBy('sponsors.spons_name')
            ->where('job_status', 1)
            ->whereIn('project_id', $project_id_list)
            ->orderBy('total_emp', 'DESC')
            ->get();
    }

    public function countProjectWiseTotalEmployees($project_id_list, $emp_status_id)
    {

        if ($emp_status_id == null || $emp_status_id <= 0) {
            return  $total_records = EmployeeInfo::select(
                'project_id',
                DB::raw("COUNT(emp_auto_id) as total_emp")
            )->groupBy("project_id")->get();
        } else {
            return  $total_records = EmployeeInfo::select(
                'project_id',
                DB::raw("COUNT(emp_auto_id) as total_emp")
            )->groupBy("project_id")->where('job_status', $emp_status_id)->whereIn('project_id', $project_id_list)->get();
        }
    }

    public function countTradeWiseEmployees($emp_status_id)
    {
        if ($emp_status_id == null || $emp_status_id <= 0) {
            return  $total_records = EmployeeInfo::select(
                'designation_id',
                DB::raw("COUNT(emp_auto_id) as total_emp")
            )
                ->leftjoin('employee_categories', 'employee_categories.catg_id', '=', 'employee_infos.designation_id')
                ->groupBy("designation_id")->get();
        } else {

            return  $total_records = EmployeeInfo::select(
                //'employee_categories.catg_name',
                'designation_id',
                DB::raw("COUNT(emp_auto_id) as total_emp")
            )
                ->leftjoin('employee_categories', 'employee_categories.catg_id', '=', 'employee_infos.designation_id')
                ->groupBy("designation_id")->where('job_status', $emp_status_id)->orderBy('employee_categories.rank_code', 'ASC')->get();
        }
    }

    public function countTotalEmployeesInAProject($project_id, $jobstatus)
    {
        if ($jobstatus < 0 || $jobstatus == null) {
            return  EmployeeInfo::where('project_id', $project_id)->count();
        } else
            return  EmployeeInfo::where('project_id', $project_id)->where('job_status', $jobstatus)->count();
    }

    public function countTotalNumberOfActiveEmployeesInAProjectOfSponsors($project_id, $sponsor_ids)
    {
        return  EmployeeInfo::where('project_id', $project_id)->whereIn('sponsor_id', $sponsor_ids)->where('job_status', 1)->count();
    }

    public function countTotalEmployeesInAProjectByCategoryTradeId($project_id, $working_shift, $category_id)
    {
        if (is_null($working_shift)) {
            return  EmployeeInfo::where('project_id', $project_id)->where('designation_id', $category_id)->where('job_status', 1)->count();
        } else
            return  EmployeeInfo::where('project_id', $project_id)->where('designation_id', $category_id)->where('isNightShift', $working_shift)->where('job_status', 1)->count();
    }

    public function countTotalActiveEmployeesInAProject($project_id, $working_shift)
    {
        return  EmployeeInfo::where('project_id', $project_id)->where('job_status', 1)->where('isNightShift', $working_shift)->count();
    }
    // will be delete 19.7.24
    // public function countNightShiftWorkingActiveEmployeesInAProject($project_id)
    // {
    //       return  EmployeeInfo::where('project_id',$project_id)->where('job_status', 1)->where('isNightShift', 1)->count();
    // }
    // public function countDayShiftWorkingActiveEmployeesInAProject($project_id)
    // {
    //       return  EmployeeInfo::where('project_id',$project_id)->where('job_status', 1)->where('isNightShift', 0)->count();
    // }

    public function countNightShiftWorkingActiveEmployeesInAProject($project_id, $branch_office_id)
    {
        return  EmployeeInfo::where('project_id', $project_id)->where('job_status', 1)
            ->where("employee_infos.branch_office_id", $branch_office_id)
            ->where('isNightShift', 1)->count();
    }
    public function countDayShiftWorkingActiveEmployeesInAProject($project_id, $branch_office_id)
    {
        return  EmployeeInfo::where('project_id', $project_id)->where('job_status', 1)
            ->where("employee_infos.branch_office_id", $branch_office_id)
            ->where('isNightShift', 0)->count();
    }

    public function countIqamaExpiredTotalEmployeesProjectWise($proj_id, $iqama_expire_date)
    {
        if ($proj_id == null) {
            return EmployeeInfo::whereDate('akama_expire_date', '>=', $iqama_expire_date)
                ->where('job_status', 1)
                ->orderBy('employee_id', 'ASC')
                ->count();
        } else {
            return EmployeeInfo::whereDate('akama_expire_date', '>=', $iqama_expire_date)
                ->where('project_id', $proj_id)
                ->where('job_status', 1)
                ->orderBy('employee_id', 'ASC')
                ->count();
        }
    }

    /*
     ==========================================================================
     ============================= Employee List ============================
     ==========================================================================
    */




    public function searchingAnEmployeeInfoByMultitypeParameter($searchingValue, $searching_db_column, $branch_office_id = 1)
    {
        return EmployeeInfo::leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
            ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
            ->leftjoin('divisions', 'employee_infos.division_id', '=', 'divisions.division_id')
            ->leftjoin('districts', 'employee_infos.district_id', '=', 'districts.district_id')
            ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
            ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
            ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
            ->leftjoin('employee_details', 'employee_infos.emp_auto_id', '=', 'employee_details.emp_auto_id')
            ->leftjoin('departments', 'employee_details.dept_id', '=', 'departments.dep_id')
            ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
            ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
            ->leftjoin('office_buildings', 'employee_infos.accomd_ofb_id', '=', 'office_buildings.ofb_id')
            // ->leftjoin('users', 'employee_infos.update_by', '=', 'users.id') // never join with users table because of searching employee  emp_auto_id replace very critical  problems
            ->leftjoin('agency_infos', 'employee_details.agc_info_auto_id', '=', 'agency_infos.agc_info_auto_id')
            ->where('employee_infos.' . $searching_db_column, $searchingValue)
            ->where("employee_infos.branch_office_id", $branch_office_id)
            ->get();
    }


    public function searchingAnActiveEmployeeInfoByMultitypeParameter($searchingValue, $searching_db_column)
    {
        return EmployeeInfo::with('agency')->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
            ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
            ->leftjoin('divisions', 'employee_infos.division_id', '=', 'divisions.division_id')
            ->leftjoin('districts', 'employee_infos.district_id', '=', 'districts.district_id')
            ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
            ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
            ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
            ->leftjoin('employee_details', 'employee_infos.emp_auto_id', '=', 'employee_details.emp_auto_id')
            ->leftjoin('departments', 'employee_details.dept_id', '=', 'departments.dep_id')
            ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
            ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
            ->leftjoin('office_buildings', 'employee_infos.accomd_ofb_id', '=', 'office_buildings.ofb_id')
            ->leftjoin('agency_infos', 'employee_details.agc_info_auto_id', '=', 'agency_infos.agc_info_auto_id')
            // ->leftjoin('users', 'employee_infos.update_by', '=', 'users.id') // never join with users table because of searching employee  emp_auto_id replace very critical  problems
            // ->where('job_status', 1)
            ->where($searching_db_column, $searchingValue)->get();
    }


    // Emp. Master Searching multiple colum searching
    public function searchingEmployeeInfoByAnyColumnValueMatching($searchingValue, $branch_office_id = 1)
    {
        return EmployeeInfo::leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
            ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
            ->leftjoin('divisions', 'employee_infos.division_id', '=', 'divisions.division_id')
            ->leftjoin('districts', 'employee_infos.district_id', '=', 'districts.district_id')
            ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
            ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
            ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
            ->leftjoin('employee_details', 'employee_infos.emp_auto_id', '=', 'employee_details.emp_auto_id')
            ->leftjoin('departments', 'employee_details.dept_id', '=', 'departments.dep_id')
            ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
            ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
            ->leftjoin('office_buildings', 'employee_infos.accomd_ofb_id', '=', 'office_buildings.ofb_id')
            // ->leftjoin('users', 'employee_infos.update_by', '=', 'users.id') // never join with users table because of searching employee  emp_auto_id replace very critical  problems
            ->leftjoin('agency_infos', 'employee_details.agc_info_auto_id', '=', 'agency_infos.agc_info_auto_id')
            ->where("employee_id", "LIKE", "%{$searchingValue}%")
            ->orWhere("employee_name", "LIKE", "%{$searchingValue}%")
            ->orWhere("passfort_no", "LIKE", "%{$searchingValue}%")
            ->orWhere("akama_no", "LIKE", "%{$searchingValue}%")
            ->where("employee_infos.branch_office_id", $branch_office_id)
            ->get();
    }

    // use employee searching with most important information
    public function searchAnEmployeeWithImportantInformationAsShowByMultitypeParameter($searchingValue, $searching_db_column, $user_branch_office_id)
    {
        return EmployeeInfo::select(
            'employee_infos.emp_auto_id',
            'employee_id',
            'employee_name',
            'passfort_no',
            'passfort_expire_date',
            'akama_no',
            'akama_photo',
            'akama_expire_date',
            'spons_name',
            'proj_name',
            'mobile_no',
            'employee_infos.email',
            'country_name',
            'joining_date',
            'hourly_employee',
            'catg_name',
            'name', // employee_type_name
            'title', // emp_job status
            'agc_title', // agency name
            'ofb_name',
            'basic_amount',
            'hourly_rent',
            'basic_hours',
            'food_allowance',
            'mobile_allowance'
        )
            ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
            ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
            ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
            ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
            ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
            ->leftjoin('employee_details', 'employee_infos.emp_auto_id', '=', 'employee_details.emp_auto_id')
            ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
            ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
            ->leftjoin('office_buildings', 'employee_infos.accomd_ofb_id', '=', 'office_buildings.ofb_id')
            ->leftjoin('agency_infos', 'employee_details.agc_info_auto_id', '=', 'agency_infos.agc_info_auto_id')
            // ->leftjoin('users', 'employee_infos.update_by', '=', 'users.id') // never join with users table because of emp_auto_id replace problems
            ->where('employee_infos.' . $searching_db_column, $searchingValue)
            ->where("employee_infos.branch_office_id", $user_branch_office_id)
            ->first();
    }


    public function getListOfEmployeeEmpAutoIdAsArrayByEmployeeIdList($employee_id_list)
    {

        return   DB::table('employee_infos')->whereIn('employee_id', $employee_id_list)->get()->pluck('emp_auto_id');
    }

    public function getListOfEmployeeEmpAutoIdThoseJoinedAfterADateByMultipleProjectId($project_id_list, $date)
    {

        if (is_null($project_id_list)) {
            return   DB::table('employee_infos')->where('job_status', 1)
                ->where('joining_date', '<', $date)
                ->get()->pluck('emp_auto_id');
        } else {
            return   DB::table('employee_infos')->where('job_status', 1)
                ->where('joining_date', '<', $date)
                ->whereIn('project_id', $project_id_list)->get()->pluck('emp_auto_id');
        }
    }


    public function getAnEmployeeInfoByEmpAutoId($emp_auto_id)
    {
        return   EmployeeInfo::where('emp_auto_id', $emp_auto_id)->first();
    }

    //     public function deleteAnEmployeeWithServiceDetailsByAutoIdBeforeApproval($emp_auto_id)
    // {
    //     try {
    //         // DB::table('salary_details')->where('emp_id', $emp_auto_id)->delete();
    //         SalaryDetails::where('emp_id', $emp_auto_id)->delete();

    //         $deleted = EmployeeInfo::where('emp_auto_id', $emp_auto_id)->delete();

    //         if ($deleted) {
    //             return true;
    //         }

    //         return false;

    //     }
    //     catch (\Exception $e) {
    //         Log::error("Employee Delete Error: " . $e->getMessage());
    //         return false;
    //     }
    // }


    public function deleteAnEmployeeWithServiceDetailsByAutoIdBeforeApproval($emp_auto_id)
    {

        $isdetele = EmployeeInfo::where('emp_auto_id', $emp_auto_id)->where('job_status', 0)->delete();
        if ($isdetele) {
            SalaryDetails::where('emp_id', $emp_auto_id)->delete();
            return true;
        } else {
            false;
        }
    }



    public function getAnEmployeeInformationWithAllReferenceTableByEmpAutoId($emp_auto_id)
    {
        return   EmployeeInfo::where('employee_infos.emp_auto_id', $emp_auto_id)
            ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
            ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
            ->leftjoin('divisions', 'employee_infos.division_id', '=', 'divisions.division_id')
            ->leftjoin('districts', 'employee_infos.district_id', '=', 'districts.district_id')
            ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
            ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
            ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
            ->leftjoin('employee_details', 'employee_infos.emp_auto_id', '=', 'employee_details.emp_auto_id')
            ->leftjoin('departments', 'employee_details.dept_id', '=', 'departments.dep_id')
            ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
            ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
            ->leftjoin('office_buildings', 'employee_infos.accomd_ofb_id', '=', 'office_buildings.ofb_id')
            ->leftjoin('agency_infos', 'employee_details.agc_info_auto_id', '=', 'agency_infos.agc_info_auto_id')
            // ->leftjoin('users', 'employee_infos.update_by', '=', 'users.id') // never join with users table because of searching employee  emp_auto_id replace very critical  problems
            ->first();
    }

    public function getAnEmployeeInformationWithAllReferenceTableByEmployeeID($employee_id)
    {
        return   EmployeeInfo::where('employee_infos.employee_id', $employee_id)
            ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
            ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
            ->leftjoin('divisions', 'employee_infos.division_id', '=', 'divisions.division_id')
            ->leftjoin('districts', 'employee_infos.district_id', '=', 'districts.district_id')
            ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
            ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
            ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
            ->leftjoin('employee_details', 'employee_infos.emp_auto_id', '=', 'employee_details.emp_auto_id')
            ->leftjoin('departments', 'employee_details.dept_id', '=', 'departments.dep_id')
            ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
            ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
            ->leftjoin('office_buildings', 'employee_infos.accomd_ofb_id', '=', 'office_buildings.ofb_id')
            ->leftjoin('agency_infos', 'employee_details.agc_info_auto_id', '=', 'agency_infos.agc_info_auto_id')
            // ->leftjoin('users', 'employee_infos.update_by', '=', 'users.id') // never join with users table because of searching employee  emp_auto_id replace very critical  problems
            ->first();
    }

    public function getAnEmployeeInfoByEmpId($employee_id)
    {
        return EmployeeInfo::where('employee_id', $employee_id)->first();
    }

    public function getAnEmployeeInfoTableDataByEmployeeIdAndBranchOfficeId($employee_id, $branch_office_id)
    {
        return EmployeeInfo::where('employee_id', $employee_id)
            ->where("employee_infos.branch_office_id", $branch_office_id)
            ->first();
    }

    public function getAnEmployeeInfoTableDataByEmployeeIqamaNumberAndBranchOfficeId($akama_no, $branch_office_id)
    {
        return EmployeeInfo::where('akama_no', $akama_no)
            ->where("employee_infos.branch_office_id", $branch_office_id)
            ->first();
    }

    public function searchingAnEmployeeIsExistInSystemByMultitypeParameter($searchingValue, $searching_db_column)
    {
        return EmployeeInfo::where($searching_db_column, $searchingValue)->first();
    }

    public function getSalaryActiveEmployeeInfoByEmpolyeeIDForTimeSheetUpload($employee_id)
    {
        return EmployeeInfo::select('emp_auto_id', 'employee_name', 'akama_no', 'hourly_employee', 'job_status', 'sponsors.spons_id', 'sponsors.spons_name', 'employee_categories.catg_id', 'employee_categories.catg_name')
            ->where('employee_id', $employee_id)
            ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
            ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
            ->first();

        // return EmployeeInfo::select('emp_auto_id','employee_name','akama_no','hourly_employee','job_status')->where('employee_id', $employee_id)
        //       // ->where('salary_status', 1)
        //         ->first();
    }

    public function getAnEmployeeInfoByEmpolyeeIDForCateringServiceRecordUpload($employee_id)
    {
        return EmployeeInfo::select('emp_auto_id', 'employee_name', 'akama_no', 'hourly_employee', 'job_status')->where('employee_id', $employee_id)
            ->first();
    }

    public function getAnEmployeeInfoWithSalaryDetailsForPromotionExcelFileUpload($employee_id)
    {
        return   EmployeeInfo::where('employee_infos.employee_id', $employee_id)
            ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
            ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
            ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
            ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
            ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
            ->orderBy('employee_id')
            ->first();
    }

    // May be not used now this method
    public function getListOfEmployeesWithSalaryDetailsByMultipleEmployeeId($allEmplId)
    {
        return  EmployeeInfo::whereIn('employee_infos.employee_id', $allEmplId)
            ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
            // ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
            // ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
            // ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
            // ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
            ->orderBy('employee_id')
            ->get();
    }

    public function getEmployeeByEmpIdWithLikeQuery($empId, $job_status)
    {
        return EmployeeInfo::where("employee_id", "LIKE", "%{$empId}%")->where('job_status', $job_status)->get();
    }
    public function getEmployeeByEmpIdAndEmpTypeWithLikeQuery($empId, $job_status, $emp_type_id)
    {
        return EmployeeInfo::where("employee_id", "LIKE", "%{$empId}%")->where('job_status', $job_status)->where('emp_type_id', $emp_type_id)->get();
    }

    public function getAnEmployeeInfoByEmpIqamaNo($iqamaNo)
    {
        return  EmployeeInfo::where('akama_no', $iqamaNo)->first();
    }



    public function getAnEmployeeInfoByEmpPassportNo($passportNo)
    {
        return  EmployeeInfo::where('passfort_no', $passportNo)->first();
    }

    public function getAnyJobStatusEmployeeWithSalaryDetailsByEmpIqamaNo($iqamaNo)
    {
        return EmployeeInfo::with('salarydetails')->where('akama_no', $iqamaNo)->first();
    }

    public function getAnEmployeeInfoByPhoneNumber($phoneNumber)
    {
        return  EmployeeInfo::where('mobile_no', $phoneNumber)->first();
    }
    public function getAnEmployeeInfoByEmail($email)
    {
        $emp =  EmployeeInfo::where('email', $email)->first();
        if ($emp == null) {
            return  new EmployeeInfo();
        }
        return $emp;
    }

    public function getAnEmployeeInfoWithSalaryDetailsByEmpAutoId($emp_auto_id)
    {
        return   EmployeeInfo::with('salarydetails')->where('job_status', 1)->where('emp_auto_id', $emp_auto_id)->first();
    }

    public function getAnEmployeeInfoWithSalaryDetailsByEmpAutoIdForEditEmployeeInformation($emp_auto_id)
    {
        return   EmployeeInfo::with('salarydetails')->where('emp_auto_id', $emp_auto_id)->first();
    }

    public function getAnEmployeeInfoWithSalaryDetailsByEmpId($employee_id)
    {
        return EmployeeInfo::with('salarydetails')->where('job_status', 1)->where('employee_id', $employee_id)->first();
    }

    public function getAnEmployeeInfoWithSalaryDetailsForUpdateSalaryAndAllInformation($employee_id, $iqama_no)
    {
        if ($employee_id)
            return EmployeeInfo::with('salarydetails')->whereIn('job_status', [1, 3, 5])->where('employee_id', $employee_id)->first();
        else
            return EmployeeInfo::with('salarydetails')->whereIn('job_status', [1, 3, 5])->where('akama_no', $iqama_no)->first();
    }

    public function getAnyJobStatusEmployeeWithSalaryDetailsByEmpId($employee_id)
    {
        return EmployeeInfo::with('salarydetails')->where('employee_id', $employee_id)->first();
    }

    public function getAnEmployeeInfoWithSalaryDetailsJoinQueryByEmpId($employee_id, $emp_job_status = null)
    {

        if ($emp_job_status == null) {
            return EmployeeInfo::where('employee_infos.employee_id', $employee_id)
                ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->first();
        } else {
            return EmployeeInfo::where('job_status', $emp_job_status)->where('employee_infos.employee_id', $employee_id)
                ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->first();
        }
    }


    public function getAnEmployeeInfoWithSalaryDetailsInformationByEmployeeID($employee_id, $branch_office_id)
    {

        return EmployeeInfo::where('employee_infos.employee_id', $employee_id)
            ->where("employee_infos.branch_office_id", $branch_office_id)
            ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
            ->first();
    }

    public function getEmployeesInfoWithSalaryDetailsByProjectId($projId, $emp_job_status)
    {

        return EmployeeInfo::where('job_status', $emp_job_status)->where('employee_infos.project_id', $projId)
            ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
            ->orderBy('employee_id')
            ->get();
    }

    // public function getEmployeesInfoWithSalaryDetailsByProjectIdForEmployeeTransfer($projId, $emp_job_status)
    // {
    //     return EmployeeInfo::select('employee_infos.emp_auto_id','employee_infos.employee_name','employee_infos.employee_id','employee_infos.akama_no','employee_infos.isNightShift','salary_details.basic_amount','salary_details.basic_hours','salary_details.hourly_rent')
    //         ->where('job_status', $emp_job_status)->where('employee_infos.project_id', $projId)
    //         ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
    //         ->orderBy('employee_id')
    //         ->get();
    // }

    public function getListOfEmployeeInformationByMultipleEmpIDsAndProjectIDsForDailyAttendanceEmpListReport($mutiple_employee_ids, $project_ids, $working_shift)
    {

        if (count($mutiple_employee_ids) == 1 && $mutiple_employee_ids[0] == null) {


            return   EmployeeInfo::whereIn('employee_infos.project_id', $project_ids)
                ->whereIn('employee_infos.isNightShift', $working_shift)
                ->where('employee_infos.job_status', 1)
                ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->orderBy('employee_categories.rank_code', 'ASC')
                ->get();



        } else {

            return   EmployeeInfo::whereIn('employee_infos.employee_id', $mutiple_employee_ids)
                ->whereIn('employee_infos.project_id', $project_ids)
                ->whereIn('employee_infos.isNightShift', $working_shift)
                ->where('employee_infos.job_status', 1)
                ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->orderBy('employee_categories.rank_code', 'ASC')
                ->get();
            // same order by as user inputted emp id

        }
    }

    public function getEmployeesInfoByMultipleEmployeeIDForEmployeeTransfer($multiple_emp_ids, $branch_office_id)
    {
        return EmployeeInfo::select(
            'employee_infos.emp_auto_id',
            'employee_infos.employee_name',
            'employee_infos.employee_id',
            'employee_infos.akama_no',
            'employee_infos.isNightShift',
            'employee_infos.job_status',
            'project_infos.proj_name',
            'employee_infos.hourly_employee',
            'employee_categories.catg_name'
        )
            ->whereIn('employee_infos.employee_id', $multiple_emp_ids)
            ->where("employee_infos.branch_office_id", $branch_office_id)
            ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
            ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
            ->orderBy('employee_id')
            ->get();
    }

    public function getEmployeesInfoByProjectIdForEmployeeTransfer($projId, $emp_job_status, $branch_office_id)
    {
        return EmployeeInfo::select(
            'employee_infos.emp_auto_id',
            'employee_infos.employee_name',
            'employee_infos.employee_id',
            'employee_infos.akama_no',
            'employee_infos.isNightShift',
            'employee_infos.job_status',
            'project_infos.proj_name',
            'employee_infos.hourly_employee',
            'employee_categories.catg_name'
        )
            ->where('job_status', $emp_job_status)->where('employee_infos.project_id', $projId)
            ->where("employee_infos.branch_office_id", $branch_office_id)
            ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
            ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
            ->orderBy('employee_id')
            ->get();
    }



    public function getEmployeeInfoWithSalaryDetailsByProjectAndJobStatusForShiftUpdate($projId, $emp_job_status, $branch_office_id, $pagination)
    {
        return EmployeeInfo::select(
            'employee_infos.emp_auto_id',
            'employee_infos.employee_name',
            'employee_infos.employee_id',
            'employee_infos.akama_no',
            'employee_infos.isNightShift',
            'salary_details.basic_amount',
            'salary_details.hourly_rent',
            'employee_infos.hourly_employee'
        )
            ->where('job_status', $emp_job_status)->where('employee_infos.project_id', $projId)
            ->where("employee_infos.branch_office_id", $branch_office_id)
            ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
            ->orderBy('employee_id')
            ->skip($pagination)->take(400)
            ->get();
    }

    public function getEmployeesInfoWithSalaryDetailForMultipleEmployeeAdvancePayment($project_id, $accommodation_building_id, $branch_office_id)
    {
        if ($project_id != null && $accommodation_building_id != null) {
            return EmployeeInfo::select('employee_infos.emp_auto_id', 'employee_infos.employee_name', 'employee_infos.employee_id', 'employee_infos.akama_no', 'employee_infos.isNightShift', 'employee_infos.hourly_employee', 'employee_infos.project_id', 'employee_categories.catg_name', 'salary_details.basic_amount', 'salary_details.hourly_rent')
                ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->where('job_status', 1)->where('employee_infos.project_id', $project_id)->where('employee_infos.accomd_ofb_id', $accommodation_building_id)
                ->where("employee_infos.branch_office_id", $branch_office_id)
                ->orderBy('employee_id')
                ->get();
        } elseif ($project_id != null && $accommodation_building_id == null) {

            return EmployeeInfo::select('employee_infos.emp_auto_id', 'employee_infos.employee_name', 'employee_infos.employee_id', 'employee_infos.akama_no', 'employee_infos.isNightShift', 'employee_infos.hourly_employee', 'employee_infos.project_id', 'employee_categories.catg_name', 'salary_details.basic_amount', 'salary_details.hourly_rent')
                ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->where('job_status', 1)->where('employee_infos.project_id', $project_id)
                ->where("employee_infos.branch_office_id", $branch_office_id)
                ->orderBy('employee_id')
                ->get();
        } elseif ($project_id == null && $accommodation_building_id != null) {
            return EmployeeInfo::select('employee_infos.emp_auto_id', 'employee_infos.employee_name', 'employee_infos.employee_id', 'employee_infos.akama_no', 'employee_infos.isNightShift', 'employee_infos.hourly_employee', 'employee_infos.project_id', 'employee_categories.catg_name', 'salary_details.basic_amount', 'salary_details.hourly_rent')
                ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->where('job_status', 1)
                ->where('employee_infos.accomd_ofb_id', $accommodation_building_id)
                ->where("employee_infos.branch_office_id", $branch_office_id)
                ->orderBy('employee_id')
                ->get();
        } else {
            return EmployeeInfo::select('employee_infos.emp_auto_id', 'employee_infos.employee_name', 'employee_infos.employee_id', 'employee_infos.akama_no', 'employee_infos.isNightShift', 'employee_infos.hourly_employee', 'employee_infos.project_id', 'employee_categories.catg_name', 'salary_details.basic_amount', 'salary_details.hourly_rent')
                ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->orderBy('employee_id')
                ->get();
        }
    }

    public function getEmployeesInfoWithSalaryDetailForEmployeeAdvanceByMultipleEmpId($multiple_employee_id)
    {
        return EmployeeInfo::select('employee_infos.emp_auto_id', 'employee_infos.employee_name', 'employee_infos.employee_id', 'employee_infos.akama_no', 'employee_infos.isNightShift', 'employee_infos.project_id', 'salary_details.basic_amount', 'salary_details.hourly_rent')
            ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
            //    ->where('job_status', 1)
            ->whereIn('employee_id', $multiple_employee_id)
            ->orderBy('employee_id')
            ->get();
    }

    // currently use this method for employee advance payment purpose
    public function searchListOfEmployeesInfoWithSalaryDetailForEmployeeAdvanceByMultipleEmpId($multiple_employee_id, $user_branch_office_id)
    {

        return EmployeeInfo::select('employee_infos.emp_auto_id', 'employee_infos.employee_name', 'employee_infos.employee_id', 'employee_infos.akama_no', 'employee_infos.isNightShift', 'employee_infos.hourly_employee', 'employee_infos.project_id', 'employee_categories.catg_name', 'salary_details.basic_amount', 'salary_details.hourly_rent')
            ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
            ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
            ->where('employee_infos.branch_office_id', $user_branch_office_id)
            ->whereIn('employee_id', $multiple_employee_id)
            //  ->orderBy('employee_id')
            ->get();
    }


    // Adance Processing Purpose Empl List
    public function getEmployeesInfoWithSalaryDetailThoseAreExistInThisListForAdvanceProcess($emp_id_list)
    {
        return EmployeeInfo::leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
            ->whereIn('employee_infos.emp_auto_id', $emp_id_list)
            ->get();
    }

    // Adance paper Create
    public function getMultipleEmpIDWiseInformationWithSalaryDetailForEmployeeAdvancePaper($emp_id_list)
    {

        return EmployeeInfo::leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
            ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
            ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
            ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
            ->leftjoin('office_buildings', 'employee_infos.accomd_ofb_id', '=', 'office_buildings.ofb_id')
            // ->where('job_status', 1)
            ->whereIn('employee_id', $emp_id_list)
            ->orderBy('employee_id')
            ->get();
    }

    // Adance paper Create
    public function getEmployeesInfoWithSalaryDetailForEmployeeAdvancePaper($project_id, $accommodation_building_id)
    {
        if ($project_id != null && $accommodation_building_id != null) {
            return EmployeeInfo::
                //select('employee_infos.emp_auto_id','employee_infos.employee_name','employee_infos.employee_id','employee_infos.akama_no','employee_infos.isNightShift','salary_details.basic_amount','salary_details.hourly_rent')
                leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
                // ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->leftjoin('office_buildings', 'employee_infos.accomd_ofb_id', '=', 'office_buildings.ofb_id')
                ->where('job_status', 1)->where('employee_infos.project_id', $project_id)->where('employee_infos.accomd_ofb_id', $accommodation_building_id)
                ->orderBy('employee_id')
                ->get();
        } elseif ($project_id != null && $accommodation_building_id == null) {

            return EmployeeInfo::
                //select('employee_infos.emp_auto_id','employee_infos.employee_name','employee_infos.employee_id','employee_infos.akama_no','employee_infos.isNightShift','salary_details.basic_amount','salary_details.hourly_rent')
                leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
                //->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->leftjoin('office_buildings', 'employee_infos.accomd_ofb_id', '=', 'office_buildings.ofb_id')
                ->where('job_status', 1)->where('employee_infos.project_id', $project_id)
                ->orderBy('employee_id')
                ->get();
        } elseif ($project_id == null && $accommodation_building_id != null) {
            return EmployeeInfo::leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
                //  ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->leftjoin('office_buildings', 'employee_infos.accomd_ofb_id', '=', 'office_buildings.ofb_id')
                ->where('job_status', 1)->where('employee_infos.accomd_ofb_id', $accommodation_building_id)
                ->orderBy('employee_id')
                ->get();
        } else {
            return EmployeeInfo::leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->leftjoin('office_buildings', 'employee_infos.accomd_ofb_id', '=', 'office_buildings.ofb_id')
                ->orderBy('employee_id')
                ->get();
        }
    }


    // iqama and other advance process purpose
    public function getListOfHourlyEmployeesByProjectAndSponsorForIqamaDeductionSetup($project_ids, $sponsor_ids, $month, $year)
    {

        return EmployeeInfo::select(
            'employee_infos.emp_auto_id',
            'employee_infos.employee_id',
            'employee_infos.employee_name',
            'employee_infos.akama_no',
            'employee_infos.hourly_employee',
            'salary_details.iqama_adv_inst_amount',
            'salary_details.other_adv_inst_amount'
        )
            ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
            ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
            ->leftjoin('monthly_work_histories', 'employee_infos.emp_auto_id', '=', 'monthly_work_histories.emp_id')
            ->whereIn('monthly_work_histories.work_project_id', $project_ids) // get emp according to worked records
            ->where('monthly_work_histories.month_id', $month)
            ->where('monthly_work_histories.year_id', $year)
            ->where('employee_infos.hourly_employee', 1)
            ->where('sponsors.owner_name', 'Asloob') // Only asloob sponsor hourly emp
            ->orderBy('employee_infos.employee_id')
            ->get();
    }

    // public function getListOfBasicSalaryEmployeesByProjectAndSponsorForIqamaAndOtherDeductionSetup($project_ids, $sponsor_ids, $month, $year)
    // {
    //     return EmployeeInfo::select(
    //         'employee_infos.emp_auto_id',
    //         'employee_infos.employee_id',
    //         'employee_infos.employee_name',
    //         'employee_infos.akama_no',
    //         'employee_infos.hourly_employee',
    //         'salary_details.iqama_adv_inst_amount',
    //         'salary_details.other_adv_inst_amount'
    //     )
    //         ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
    //         ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
    //         ->leftjoin('monthly_work_histories', 'employee_infos.emp_auto_id', '=', 'monthly_work_histories.emp_id')
    //         ->whereIn('monthly_work_histories.work_project_id', $project_ids) // get emp according to worked records
    //         ->where('monthly_work_histories.month_id', $month)
    //         ->where('monthly_work_histories.year_id', $year)
    //         ->where('employee_infos.hourly_employee', NULL)
    //         ->orderBy('employee_infos.employee_id')
    //         ->get();
    // }

     public function getListOfEmployeesThoseHaveWorkRecordOfThisMonthForAdvanceProcess($month, $year)
    {
            return EmployeeInfo::select(
                'employee_infos.emp_auto_id',
                'employee_infos.employee_id',
                'employee_infos.employee_name',
                'employee_infos.akama_no',
                'employee_infos.hourly_employee',
                'salary_details.iqama_adv_inst_amount',
                'salary_details.other_adv_inst_amount'
            )
            ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
            ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
            ->leftjoin('monthly_work_histories', 'employee_infos.emp_auto_id', '=', 'monthly_work_histories.emp_id')
            ->where('monthly_work_histories.month_id', $month)
            ->where('monthly_work_histories.year_id', $year)
            ->orderBy('employee_infos.employee_id')
            ->get();
    }

    public function getAllActiveAsloobSponsorHourlyEmployeesForIqamaAndAdvanceProcess()
    {

        return  EmployeeInfo::select(
            'employee_infos.emp_auto_id',
            'employee_infos.employee_id',
            'employee_infos.employee_name',
            'employee_infos.akama_no',
            'employee_infos.hourly_employee',
            'salary_details.iqama_adv_inst_amount',
            'salary_details.other_adv_inst_amount'
        )
            ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
            ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
            ->where('employee_infos.hourly_employee', 1)
            ->where('employee_infos.job_status', 1)
            ->where('sponsors.owner_name', 'Asloob') // Only asloob sponsor hourly emp
            ->orderBy('employee_infos.employee_id')
            ->get();
    }

    public function getAllActiveAsloobSponsorBasicSalaryEmployeesForIqamaAndAdvanceProcess()
    {

        return  EmployeeInfo::select(
            'employee_infos.emp_auto_id',
            'employee_infos.employee_id',
            'employee_infos.employee_name',
            'employee_infos.akama_no',
            'employee_infos.hourly_employee',
            'salary_details.iqama_adv_inst_amount',
            'salary_details.other_adv_inst_amount'
        )
            ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
            ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
            ->whereNot('employee_infos.hourly_employee', 1) // all basic emp
            ->where('employee_infos.job_status', 1)
            ->where('sponsors.owner_name', 'Asloob') // Only asloob sponsor hourly emp
            ->orderBy('employee_infos.employee_id')
            ->get();
    }

    public function getListOfEmployeesForIqamaAndOtherDeductionSetup($is_hourly, $month, $year)
    {
        $emp_ids =   MonthlyWorkHistory::where('month_id', $month)
            ->where('year_id', $year)
            ->pluck('emp_id')->toArray();

        if ((int)$is_hourly) {

            return EmployeeInfo::select(
                'employee_infos.emp_auto_id',
                'employee_infos.employee_id',
                'employee_infos.employee_name',
                'employee_infos.akama_no',
                'employee_infos.hourly_employee',
                'salary_details.iqama_adv_inst_amount',
                'salary_details.other_adv_inst_amount'
            )
                ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->whereIn('employee_infos.emp_auto_id', $emp_ids)
                ->where('employee_infos.hourly_employee', 1)
                ->where('sponsors.owner_name', 'Asloob') // Only asloob sponsor hourly emp
                ->orderBy('employee_infos.employee_id')
                ->get();
        } else {

            return EmployeeInfo::select(
                'employee_infos.emp_auto_id',
                'employee_infos.employee_id',
                'employee_infos.employee_name',
                'employee_infos.akama_no',
                'employee_infos.hourly_employee',
                'salary_details.iqama_adv_inst_amount',
                'salary_details.other_adv_inst_amount'
            )
                ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->whereIn('employee_infos.emp_auto_id', $emp_ids)
                ->where('employee_infos.hourly_employee', NULL)
                ->where('sponsors.owner_name', 'Asloob') // Only asloob sponsor hourly emp
                ->orderBy('employee_infos.employee_id')
                ->get();
        }
    }


    public function getListOfSubconSponsorEmployeesForIqamaAndOtherDeductionSetup($month, $year)
    {
        $emp_ids =   MonthlyWorkHistory::where('month_id', $month)
            ->where('year_id', $year)
            ->pluck('emp_id')->toArray();

        return EmployeeInfo::select(
            'employee_infos.emp_auto_id',
            'employee_infos.employee_id',
            'employee_infos.employee_name',
            'employee_infos.akama_no',
            'employee_infos.hourly_employee',
            'salary_details.iqama_adv_inst_amount',
            'salary_details.other_adv_inst_amount'
        )
            ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
            ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
            ->whereIn('employee_infos.emp_auto_id', $emp_ids)
            // ->where('employee_infos.hourly_employee',1)
            ->where('sponsors.owner_name', 'Subcon') // Only subcon sponsor  emp
            ->orderBy('employee_infos.employee_id')
            ->get();
    }




    // Advance Summary Report Purpose

    public function getEmployeesInfoWithSalaryDetailForAdvanceProcessByMultipleEmployeeIDs($emp_auto_id_list)
    {
        return EmployeeInfo::select(
            'employee_infos.emp_auto_id',
            'employee_infos.employee_id',
            'employee_infos.employee_name',
            'employee_infos.akama_no',
            'employee_infos.hourly_employee',
            'salary_details.basic_amount',
            'salary_details.iqama_adv_inst_amount',
            'salary_details.other_adv_inst_amount',
            'sponsors.spons_name',
            'employee_categories.catg_name'
        )
            ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
            ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
            ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
            ->whereIn('employee_infos.employee_id', $emp_auto_id_list)
            ->get();
    }

    public function getListOfHourlyEmployeesThoseAreNowWorkingInThisProjectForAdvanceSummaryReport($project_ids, $sponsor_ids, $month, $year)
    {
        return EmployeeInfo::select(
            'employee_infos.emp_auto_id',
            'employee_infos.employee_id',
            'employee_infos.employee_name',
            'employee_infos.akama_no',
            'employee_infos.hourly_employee',
            'salary_details.basic_amount',
            'salary_details.iqama_adv_inst_amount',
            'salary_details.other_adv_inst_amount',
            'sponsors.spons_name',
            'employee_categories.catg_name'
        )
            ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
            ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
            ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
            ->leftjoin('monthly_work_histories', 'employee_infos.emp_auto_id', '=', 'monthly_work_histories.emp_id')
            ->whereIn('employee_infos.project_id', $project_ids)
            ->where('monthly_work_histories.month_id', $month)
            ->where('monthly_work_histories.year_id', $year)
            ->where('employee_infos.hourly_employee', 1)
            ->where('sponsors.owner_name', 'Asloob') // Only asloob sponsor hourly emp
            ->orderBy('employee_infos.employee_id')
            ->get();
    }

    public function getListOfBasicEmployeesThoseAreWorkingNowInThisProjectForAdvanceSummaryReport($project_ids, $sponsor_ids, $month, $year)
    {

        return EmployeeInfo::select(
            'employee_infos.emp_auto_id',
            'employee_infos.employee_id',
            'employee_infos.employee_name',
            'employee_infos.akama_no',
            'employee_infos.hourly_employee',
            'salary_details.basic_amount',
            'salary_details.iqama_adv_inst_amount',
            'salary_details.other_adv_inst_amount',
            'sponsors.spons_name',
            'employee_categories.catg_name'
        )
            ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
            ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
            ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
            ->leftjoin('monthly_work_histories', 'employee_infos.emp_auto_id', '=', 'monthly_work_histories.emp_id')
            ->whereIn('employee_infos.project_id', $project_ids)
            ->where('monthly_work_histories.month_id', $month)
            ->where('monthly_work_histories.year_id', $year)
            ->where('employee_infos.hourly_employee', NULL)
            ->orderBy('employee_infos.employee_id')
            ->get();
    }




    public function getEmployeeLisForDailyAttendanceINByProjectId($projId, $emp_job_status, $alreadyAttendedEmpLst, $isNightShift)
    {
        return EmployeeInfo::select('employee_infos.emp_auto_id', 'employee_infos.employee_id', 'employee_infos.employee_name', 'employee_infos.akama_no', 'employee_infos.isNightShift', 'employee_infos.hourly_employee', 'employee_categories.catg_name')
            ->where('job_status', $emp_job_status)->where('employee_infos.project_id', $projId)
            ->where('employee_infos.isNightShift', $isNightShift)
            ->whereNotIn('employee_infos.emp_auto_id', $alreadyAttendedEmpLst)
            ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
            ->orderBy('employee_id')
            ->get();
    }

    public function searchEmployeesForDailyAttendanceINByMultEmpId($emp_ids, $projId, $alreadyAttendedEmpLst, $isNightShift)
    {
        return EmployeeInfo::select(
            'employee_infos.emp_auto_id',
            'employee_infos.employee_id',
            'employee_infos.employee_name',
            'employee_infos.akama_no',
            'employee_infos.isNightShift',
            'employee_infos.hourly_employee',
            'employee_categories.catg_name'
        )
            ->where('job_status', 1)
            ->where('employee_infos.project_id', $projId)
            ->whereIn('employee_infos.employee_id', $emp_ids)
            ->where('employee_infos.isNightShift', $isNightShift)
            ->whereNotIn('employee_infos.emp_auto_id', $alreadyAttendedEmpLst)
            ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
            ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
            ->orderBy('employee_id')
            ->get();
    }

    public function getAllEmployeesInformationByProjectId($project_id, $job_status)
    {
        return   EmployeeInfo::where('project_id', $project_id)->where('job_status', $job_status)->orderBy('employee_id', 'ASC')->get();
    }


    public function getEmployeesInformationRecordsByProjectId($project_id)
    {
        return   EmployeeInfo::where('project_id', $project_id)
            ->orderBy('employee_id', 'ASC')->get();
    }

    public function getAllEmployeesIdAsArrayInTheProject($project_id)
    {
        $list =   EmployeeInfo::select('employee_id')->where('project_id', $project_id)
            //->where('job_status', $job_status)
            ->orderBy('employee_id', 'ASC')->get();
        $employee_ids = [];
        $c = 0;
        foreach ($list as $l) {
            $employee_ids[$c++] = $l->employee_id;
        }
        return $employee_ids;
    }


    public function getAllEmployeeInfoWithSalaryDetailsThoseAreNotInMonthlyWorkRecords($projId, $emp_job_status, $year, $month)
    {

        $list = MonthlyWorkHistory::select('emp_id')
            ->where('year_id', $year)
            ->where('month_id', $month)->get();

        $allEmp = EmployeeInfo::where('job_status', $emp_job_status)->where('employee_infos.project_id', $projId)
            ->whereNotIn('employee_infos.emp_auto_id', $list)
            ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
            ->orderBy('employee_id')->limit(150)
            ->get();
        return $allEmp;
    }



    public function getAllEmployeesInformation($pageLimit, $job_status)
    {
        if ($pageLimit <= 0 || $pageLimit == null) {
            return $all =  EmployeeInfo::where('job_status', $job_status)->orderBy('emp_auto_id', 'DESC')->get();
        } else {
            return $all =  EmployeeInfo::where('job_status', $job_status)->orderBy('emp_auto_id', 'DESC')->paginate($pageLimit);
        }
    }


    public function getAllEmployeesForFindingEmployeeThoseAreNotReceivedSalary()
    {

        return   EmployeeInfo::select(
            'employee_infos.emp_auto_id',
            'employee_infos.employee_id',
            'employee_infos.employee_name',
            'employee_infos.akama_no',
            'sponsors.spons_name',
            'employee_categories.catg_name'
        )
            ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
            ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
            ->orderBy('employee_id', 'ASC')->get();
    }

    // this is not used now may be 7.7.2024
    public function getAllEmployeesInformationWithSalaryDetailsForApproval($pageLimit, $job_status)
    {
        if ($pageLimit <= 0 || $pageLimit == null) {
            return $all =  EmployeeInfo::where('job_status', $job_status)
                // ->leftjoin('employee_details', 'employee_infos.emp_auto_id', '=', 'employee_details.emp_auto_id')
                ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->orderBy('emp_auto_id', 'DESC')->get();
        } else {
            return $all =  EmployeeInfo::where('job_status', $job_status)
                ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                //  ->leftjoin('employee_details', 'employee_infos.emp_auto_id', '=', 'employee_details.emp_auto_id')
                ->orderBy('emp_auto_id', 'DESC')->paginate($pageLimit);
        }
    }

    // New Employees those waiting for approval
    public function getListOfNewEmployeesThoseAreWaitingForApproval($pageLimit, $job_status, $branch_office_id)
    {
        if ($pageLimit <= 0 || $pageLimit == null) {
            return $all =  EmployeeInfo::where('job_status', $job_status)->where('branch_office_id', $branch_office_id)
                ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->orderBy('emp_auto_id', 'DESC')->get();
        } else {
            return $all =  EmployeeInfo::where('job_status', $job_status)->where('branch_office_id', $branch_office_id)
                ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->orderBy('emp_auto_id', 'DESC')->paginate($pageLimit);
        }
    }

    public function getListOfNewEmployeesThoseAreWaitingForApprovalForVueComponent($pageLimit, $job_status, $branch_office_id)
    {
        if ($pageLimit <= 0 || $pageLimit == null) {
            return $all =  EmployeeInfo::with(['category', 'sponser', 'country', 'employeeType'])->where('job_status', $job_status)->where('branch_office_id', $branch_office_id)
                ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->orderBy('emp_auto_id', 'DESC')->get();
        } else {
            return $all =  EmployeeInfo::where('job_status', $job_status)->where('branch_office_id', $branch_office_id)
                ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->orderBy('emp_auto_id', 'DESC')->paginate($pageLimit);
        }
    }

    public function getEmployeeInformationForDropdownList()
    {
        return EmployeeInfo::select(
            'emp_auto_id',
            'employee_id',
            'employee_name'
        )->orderBy('employee_id')
            ->get();
    }



    public function getEmployeeListByEmpCategoryId($catg_id)
    {
        return EmployeeInfo::where('designation_id', $catg_id)->get();
    }

    public function getEmployeeListByCategoryIdEmpTypeId($catg_id, $empTypeId)
    {
        if ($catg_id == -1 && $empTypeId >= 1) {
            return  EmployeeInfo::where('emp_type_id', $empTypeId)->orderBy('employee_id', 'ASC')->get();
        } else if ($catg_id >= 1 && $empTypeId == -1) {
            return EmployeeInfo::where('designation_id', $catg_id)->orderBy('employee_id', 'ASC')->get();
        } else if ($catg_id >= 1 && $empTypeId >= 1) {
            return EmployeeInfo::where('designation_id', $catg_id)->where('emp_type_id', $empTypeId)->orderBy('employee_id', 'ASC')->get();
        }
    }

    public function getEmployeeListByCategoryIdEmpTypeAndHourlyEmp($catg_id, $empTypeId, $isHourly)
    {
        if ($catg_id == -1 && $empTypeId == -1) {
            return  EmployeeInfo::where('hourly_employee', $isHourly)->orderBy('employee_id', 'ASC')->get();
        } else if ($catg_id == -1 && $empTypeId >= 1) {
            return  EmployeeInfo::where('emp_type_id', $empTypeId)
                ->where('hourly_employee', $isHourly)->orderBy('employee_id', 'ASC')->get();
        } else if ($catg_id >= 1 && $empTypeId >= 1) {
            return EmployeeInfo::where('designation_id', $catg_id)->where('emp_type_id', $empTypeId)
                ->where('hourly_employee', $isHourly)->orderBy('employee_id', 'ASC')->get();
        }
    }


    public function getEmployeeListWithProjectAndSponsor($projId, $sponseId)
    {

        if ($projId > 0 && $sponseId > 0) {

            return $projAndSponsWiseEmp = EmployeeInfo::with('project', 'sponsor')->where('sponsor_id', $sponseId)->where('project_id', $projId)->get();
        } elseif ($projId > 0) {
            return  $projAndSponsWiseEmp = EmployeeInfo::with('project', 'sponsor')->where('project_id', $projId)->get();
        } elseif ($sponseId > 0) {
            return $projAndSponsWiseEmp = EmployeeInfo::with('project', 'sponsor')->where('sponsor_id', $sponseId)->get();
        } else {
            return  $projAndSponsWiseEmp = EmployeeInfo::all();
        }
    }

    // Employee Monthly Date to Date Attendance Details report
    public function getEmployeeListForAttendanceReportWithProjectAndSponsorAndJobStatus($projId, $sponseId, $working_shift)
    {

        if ($projId != null && $sponseId != null && $working_shift != null) {
            //111
            return  EmployeeInfo::select('emp_auto_id', 'employee_id', 'employee_name', 'akama_no', 'hourly_employee', 'catg_name')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->where('job_status', 1)->where('isNightShift', $working_shift)->where('project_id', $projId)->where('sponsor_id', $sponseId)
                ->orderBy('employee_id')->get();
        } elseif ($projId != null && $sponseId != null && $working_shift == null) {
            // 110
            return   EmployeeInfo::select('emp_auto_id', 'employee_id', 'employee_name', 'akama_no', 'hourly_employee', 'catg_name')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->where('job_status', 1)->where('project_id', $projId)->where('sponsor_id', $sponseId)->orderBy('employee_id')->get();
        } elseif ($projId != null && $sponseId == null && $working_shift != null) {
            //101
            return   EmployeeInfo::select('emp_auto_id', 'employee_id', 'employee_name', 'akama_no', 'hourly_employee', 'catg_name')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->where('job_status', 1)->where('project_id', $projId)->where('isNightShift', $working_shift)->orderBy('employee_id')->get();
        } elseif ($projId != null && $sponseId == null && $working_shift == null) {
            //100
            return   EmployeeInfo::select('emp_auto_id', 'employee_id', 'employee_name', 'akama_no', 'hourly_employee', 'catg_name')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->where('job_status', 1)->where('project_id', $projId)->orderBy('employee_id')->get();
        } elseif ($projId == null && $sponseId != null && $working_shift != null) {
            //011
            return   EmployeeInfo::select('emp_auto_id', 'employee_id', 'employee_name', 'akama_no', 'hourly_employee', 'catg_name')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->where('job_status', 1)->where('sponsor_id', $sponseId)->where('isNightShift', $working_shift)->orderBy('employee_id')->get();
        } elseif ($projId == null && $sponseId != null && $working_shift == null) {
            // 010
            return   EmployeeInfo::select('emp_auto_id', 'employee_id', 'employee_name', 'akama_no', 'hourly_employee', 'catg_name')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->where('job_status', 1)->where('sponsor_id', $sponseId)->orderBy('employee_id')->get();
        } else {
            //000
            return   EmployeeInfo::select('emp_auto_id', 'employee_id', 'employee_name', 'akama_no', 'hourly_employee', 'catg_name')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->where('job_status', 1)->orderBy('employee_id')->get();
        }
    }

    // Employee Monthly Date to Date Attendance Record download as excel
    public function getLisOfEmployeeForTheProjectForAttendanceRecordDownload($projId, $working_shift)
    {

        if ($projId != null && $working_shift != null) {
            //111
            return  EmployeeInfo::select('emp_auto_id', 'employee_id', 'employee_name', 'akama_no', 'hourly_employee', 'catg_name')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                //->where('job_status', 1)
                ->where('isNightShift', $working_shift)->where('project_id', $projId)
                ->orderBy('employee_id')->get();
        } elseif ($projId != null && $working_shift == null) {
            // 110
            return   EmployeeInfo::select('emp_auto_id', 'employee_id', 'employee_name', 'akama_no', 'hourly_employee', 'catg_name')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                // ->where('job_status', 1)
                ->where('project_id', $projId)->orderBy('employee_id')->get();
        } elseif ($projId == null && $working_shift != null) {
            //101
            return   EmployeeInfo::select('emp_auto_id', 'employee_id', 'employee_name', 'akama_no', 'hourly_employee', 'catg_name')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                //->where('job_status', 1)
                ->where('isNightShift', $working_shift)->orderBy('employee_id')->get();
        } else {
            //000
            return   EmployeeInfo::select('emp_auto_id', 'employee_id', 'employee_name', 'akama_no', 'hourly_employee', 'catg_name')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                // ->where('job_status', 1)
                ->orderBy('employee_id')->get();
        }
    }


    public function getMultipleEmployeeInfoByMultipleEmployeeIdForAttendanceReport($allEmplId)
    {
        return  EmployeeInfo::whereIn('employee_infos.employee_id', $allEmplId)
            ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
            ->get();
    }


    public function getEmployeesInformationWithAllReferenceTable($employee_id)
    {
        if ($employee_id == null || $employee_id <= 0) {
            return EmployeeInfo::leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
                ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->orderBy('employee_id')
                ->get();
        } else {
            return EmployeeInfo::leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
                ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->orderBy('employee_id')
                ->where('employee_id', $employee_id)
                ->first();
        }
    }

    public function getEmployeeInformationWithCountryDivDistEmpTypeEmpDepartCateProject($emp_auto_id)
    {
        if ($emp_auto_id == null || $emp_auto_id <= 0) {
            return EmployeeInfo::with('country', 'division', 'district', 'employeeType', 'project', 'category', 'department', 'sponsor', 'status', 'agency')->get();
        } else {
            return EmployeeInfo::with('country', 'division', 'district', 'employeeType', 'project', 'category', 'department', 'sponsor', 'status', 'agency')
                ->where('emp_auto_id', $emp_auto_id)->first();
        }
    }



    /*
     ==========================================================================
     ============================= Employee Update ============================
     ==========================================================================
    */

    public function updateEmployeeAllInformation($request)
    {
        // dd(100,$request->all());
        return  EmployeeInfo::where('emp_auto_id', $request->id)->update([
            'employee_name' => $request->emp_name,
            'akama_no' => $request->akama_no,
            'akama_expire_date' => $request->akama_expire,
            'passfort_no' => $request->passfort_no,
            'passfort_expire_date' => $request->passport_expire_date,
            'sponsor_id' => $request->sponsor_id,
            'mobile_no' => $request->mobile_no,
            'country_id' => $request->country_id,
            'division_id' => $request->division_id,
            'district_id' => $request->district_id,
            'post_code' => $request->post_code,
            'details' => $request->details,
            'present_address' => $request->present_address,
            'agc_info_auto_id' => $request->agency_id,
            'emp_type_id' => $request->emp_type_id,
            // 'project_id' => $request->projectStatus,
            // 'job_status' => $request->EmpStatus_id,
            'designation_id' => $request->designation_id,
            'department_id' => $request->department_id,
            'date_of_birth' => $request->date_of_birth,
            'phone_no' => $request->phone_no,
            'email' => $request->email,
            'maritus_status' => $request->maritus_status,
            'gender' => $request->gender,
            'religion' => $request->religion,
            'joining_date' => $request->joining_date,
            'confirmation_date' => $request->confirmation_date,
            'appointment_date' => $request->appointment_date,
            'entry_date' => Carbon::now(),
            'update_by' => Auth::user()->id,
            'updated_at' => Carbon::now(),
        ]);
    }


    public function updateAnEmployeeAllInformationWithSalaryDetails($anEmployee, $update_by)
    {


        EmployeeInfo::where('emp_auto_id', $anEmployee->emp_auto_id)->update([
            'employee_name' => $anEmployee->emp_name,
            // 'akama_no' => $anEmployee->akama_no,
            // 'akama_expire_date' => $anEmployee->akama_expire,
            // 'passfort_no' => $anEmployee->passfort_no,
            // 'passfort_expire_date' => $anEmployee->passport_expire_date,
            //  'sponsor_id' => $anEmployee->sponsor_id,
            'mobile_no' => $anEmployee->mobile_no,
            //  'country_id' => $anEmployee->country_id,
            //  'division_id' => $anEmployee->division_id,
            //  'district_id' => $anEmployee->district_id,
            //  'post_code' => $anEmployee->post_code,
            //  'details' => $anEmployee->details,
            //  'present_address' => $anEmployee->present_address,
            'emp_type_id' => $anEmployee->emp_type_id,
            //  'project_id' => $anEmployee->project_id,
            'designation_id' => $anEmployee->designation_id,
            //  'department_id' => $anEmployee->department_id,
            //   'date_of_birth' => $anEmployee->date_of_birth,
            'phone_no' => $anEmployee->phone_no,
            'email' => $anEmployee->email,
            //  'maritus_status' => $anEmployee->maritus_status,
            //  'gender' => $anEmployee->gender,
            //  'religion' => $anEmployee->religion,
            //   'joining_date' => $anEmployee->joining_date,
            //  'confirmation_date' => $anEmployee->confirmation_date,
            //  'appointment_date' => $anEmployee->appointment_date,
            'update_by' => $update_by,
            'updated_at' => Carbon::now(),
        ]);

        $anEmployee->id = $anEmployee->emp_auto_id;
        return true; // $this->updateEmployeeSalaryAllInformation($anEmployee,$update_by);

    }


    public function updateAnEmployeeAllBasicInformationFromEmpEdit($anEmployee, $update_by)
    {


        EmployeeInfo::where('emp_auto_id', $anEmployee->emp_auto_id)->update([
            'employee_name' => $anEmployee->emp_name,
            'akama_no' => $anEmployee->akama_no,
            'akama_expire_date' => $anEmployee->akama_expire,
            'passfort_no' => $anEmployee->passfort_no,
            'passfort_expire_date' => $anEmployee->passport_expire_date,
            'sponsor_id' => $anEmployee->sponsor_id,
            'mobile_no' => $anEmployee->mobile_no,
            'country_id' => $anEmployee->country_id,
            'division_id' => $anEmployee->division_id,
            'district_id' => $anEmployee->district_id,
            'post_code' => $anEmployee->post_code,
            'details' => $anEmployee->details,
            // 'present_address' => $anEmployee->present_address,
            //  'emp_type_id' => $anEmployee->emp_type_id,
            //  'project_id' => $anEmployee->project_id,
            // 'designation_id' => $anEmployee->designation_id,
            'department_id' => $anEmployee->department_id,
            'date_of_birth' => $anEmployee->date_of_birth,
            'phone_no' => $anEmployee->phone_no,
            'email' => $anEmployee->email,
            'joining_date' => $anEmployee->joining_date,
            'confirmation_date' => $anEmployee->confirmation_date,
            'appointment_date' => $anEmployee->appointment_date,
            'update_by' => $update_by,
            'updated_at' => Carbon::now(),
        ]);
        return EmployeeDetails::where('emp_auto_id', $anEmployee->emp_auto_id)->update([

            'dept_id' => $anEmployee->department_id,
            // 'last_education_id' => $last_education_id,
            'religion_id' => $anEmployee->religion,
            // 'expertness_rating' => $anEmployee->expertness_rating,
            'country_phone_no' => $anEmployee->country_phone_no,
            'agc_info_auto_id' => $anEmployee->agency_id,
            // 'gender' => $anEmployee->gender,
            'is_married' => $anEmployee->maritus_status,
            'blood_group' => $anEmployee->blood_group,
            'present_address' => $anEmployee->present_address,
            //'ref_employee_id' => $anEmployee->ref_employee_id,
            'remarks' => $anEmployee->remarks ?? "",
            'updated_at' => Carbon::now(),
            'updated_by' => $update_by,
        ]);
    }

    public function updateEmployeeUploadedFileDbPath($emp_auto_id, $filePath, $dbColoumName)
    {
        if ($filePath == '' || $filePath == null)
            return false;
        return  EmployeeInfo::where('emp_auto_id', $emp_auto_id)->update([
            $dbColoumName => $filePath,
            'updated_at' => Carbon::now(),
        ]);
    }

    public function updateEmployeeJobStatus($emp_auto_id, $job_status)
    {
        return EmployeeInfo::where('emp_auto_id', $emp_auto_id)->update([
            'job_status' => $job_status,
            'updated_at' => Carbon::now(),
        ]);
    }

    public function updateAnEmployeeSalaryStatus($emp_auto_id, $salary_status, $update_by_id)
    {
        return EmployeeInfo::where('emp_auto_id', $emp_auto_id)->update([
            'salary_status' => $salary_status,
            'update_by' => $update_by_id,
            'updated_at' => Carbon::now(),
        ]);
    }

    public function approvalOfInsertedNewEmployee($emp_auto_id, $job_status, $approvedBy)
    {

        return EmployeeInfo::where('emp_auto_id', $emp_auto_id)->update([
            'job_status' => $job_status,
            'emp_approved_by' => $approvedBy,
            'updated_at' => Carbon::now(),
        ]);
    }

    public function updateEmployeeAssignedProject($emp_auto_id, $project_id)
    {
        return EmployeeInfo::where('emp_auto_id', $emp_auto_id)->update([
            'project_id' => $project_id,
            'isNightShift' => 0,
        ]);
    }


    public function updateEmployeeDesignationStatus($emp_auto_id, $emplDesig_id)
    {
        return EmployeeInfo::where('emp_auto_id', $emp_auto_id)->update([
            'designation_id' => $emplDesig_id,
            'updated_at' => Carbon::now(),
        ]);
    }



    public function updateEmployeeCompanyInfo($emp_auto_id, $company_id)
    {
        return EmployeeInfo::where('emp_auto_id', $emp_auto_id)->update([
            'company_id' => $company_id,
            'updated_at' => Carbon::now(),
        ]);
    }
    public function updateAnEmployeeIqamaNumberAndExpiredateFromStatusOption($emp_auto_id, $iqamaNo, $iqamaExpDate)
    {
        return EmployeeInfo::where('emp_auto_id', $emp_auto_id)->update([
            'akama_no' => $iqamaNo,
            'akama_expire_date' => $iqamaExpDate
        ]);
    }

    public function updateEmployeeIqamaReletedInfo($emp_auto_id, $iqamaNo, $iqamaExpDate)
    {
        return EmployeeInfo::where('emp_auto_id', $emp_auto_id)->update([
            'akama_no' => $iqamaNo,
            'akama_expire_date' => $iqamaExpDate,
        ]);
    }

    public function updateEmployeePassportInformation($emp_auto_id, $passport_no)
    {
        return EmployeeInfo::where('emp_auto_id', $emp_auto_id)->update([
            'passfort_no' => $passport_no,
        ]);
    }
    public function updateAnEmployeeEmailAddress($emp_auto_id, $email)
    {

        return EmployeeInfo::where('emp_auto_id', $emp_auto_id)->update([
            'email' => $email,
        ]);
    }

    public function updateAnEmployeeMobileAccomdOfbID($emp_auto_id, $emp_mobile_no,  $emp_phone_no, $accomd_ofb_id)
    {
        return EmployeeInfo::where('emp_auto_id', $emp_auto_id)->update([
            'mobile_no' => $emp_mobile_no,
            'phone_no' => $emp_phone_no,
            'accomd_ofb_id' => $accomd_ofb_id,
        ]);
    }

    public function updateAnEmployeeMobileNumber($emp_auto_id, $emp_mobile_no,  $emp_phone_no)
    {

        if (is_null($emp_mobile_no) == false && is_null($emp_phone_no) == false) {
            return EmployeeInfo::where('emp_auto_id', $emp_auto_id)->update([
                'mobile_no' => $emp_mobile_no,
                'phone_no' => $emp_phone_no
            ]);
        } else if (is_null($emp_mobile_no) == false) {
            return EmployeeInfo::where('emp_auto_id', $emp_auto_id)->update([
                'mobile_no' => $emp_mobile_no,
            ]);
        } else if (is_null($emp_phone_no) == false) {
            return EmployeeInfo::where('emp_auto_id', $emp_auto_id)->update([
                'phone_no' => $emp_phone_no
            ]);
        }
    }

    public function updateAnEmployeeAccommodationVilla($emp_auto_id, $accomd_ofb_id)
    {
        return EmployeeInfo::where('emp_auto_id', $emp_auto_id)->update([
            'accomd_ofb_id' => $accomd_ofb_id,
        ]);
    }


    public function updateAnEmployeeWorkRatingInfo($emp_auto_id, $empWorkActivityRating)
    {
        return EmployeeInfo::where('emp_auto_id', $emp_auto_id)->update([
            'emp_rating' => $empWorkActivityRating,
        ]);
    }


    public function updateEmployeeHourlyEmployeeStatus($emp_auto_id, $hourly_employee, $updated_by)
    {
        return EmployeeInfo::where('emp_auto_id', $emp_auto_id)->update([
            'hourly_employee' => $hourly_employee,
            'updated_at' => Carbon::now(),
            'update_by' => $updated_by
        ]);
    }
    public function updateEmployeeInfoEmployeeTypeAndIsHourlyEmployee($emp_auto_id, $emp_type_id, $hourly_employee, $updated_by)
    {
        return EmployeeInfo::where('emp_auto_id', $emp_auto_id)->update([
            'emp_type_id' => $emp_type_id,
            'hourly_employee' => $hourly_employee,
            'updated_at' => Carbon::now(),
            'update_by' => $updated_by
        ]);
    }
    public function updateAnEmployeeIqamaExpireDate($emp_auto_id, $iqama_expire_date)
    {
        return EmployeeInfo::where('emp_auto_id', $emp_auto_id)->update([
            'akama_expire_date' => $iqama_expire_date,
        ]);
    }

    public function updateAnEmployeePassportExpireDate($emp_auto_id, $passport_expire_date)
    {
        return EmployeeInfo::where('emp_auto_id', $emp_auto_id)->update([
            'passfort_expire_date' => $passport_expire_date,
        ]);
    }

    // Employee Working Shift Status Update
    public function updateEmployeeWorkingShiftStatusByEmployeeAutoId($emp_auto_id, $working_shift)
    {
        return  EmployeeInfo::where('emp_auto_id', $emp_auto_id)->update([
            'isNightShift' => $working_shift,
            'updated_at' => Carbon::now(),
        ]);
    }

    // Employee Sponsor Update
    public function updateEmployeeSponsorInfo($emp_auto_id, $sponsor_id)
    {
        return  EmployeeInfo::where('emp_auto_id', $emp_auto_id)->update([
            'sponsor_id' => $sponsor_id,
        ]);
    }

    /*
     ==========================================================================
     ============================= Employee Report ============================
     ==========================================================================
    */
    public function exportEmployeeInformation()
    {
        return EmployeeInfo::select(
            'employee_id',
            'employee_name',
            'passfort_no',
            'passfort_expire_date',
            'akama_no',
            'akama_expire_date',
            'spons_name',
            'proj_name',
            'mobile_no',
            'email',
            'date_of_birth',
            'country_name',
            'hourly_employee',
            'catg_name',
            'name',
            'title',
            'basic_amount',
            'hourly_rent',
            'basic_hours',
            'food_allowance',
            'mobile_allowance'

        )
            ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
            ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
            ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
            ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
            ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
            ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
            ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
            ->orderBy('employee_id')
            ->get();
    }

    public function exportEmployeeHRSectionInformation($projectIdList, $sponserId, $jobStatus, $empCategory, $empTypeId)
    {

        if ($sponserId == null && $empCategory == null) {
            return EmployeeInfo::select(
                'employee_id',
                'employee_name',
                'passfort_no',
                'passfort_expire_date',
                'akama_no',
                'akama_expire_date',
                'spons_name',
                'proj_name',
                'mobile_no',
                'email',
                'date_of_birth',
                'country_name',
                'hourly_employee',
                'catg_name',
                'name',
                'title'
            )
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
                ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                // ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->whereIn('employee_infos.project_id', $projectIdList)
                ->where('employee_infos.job_status', $jobStatus)
                ->orderBy('employee_id')
                ->get();
        } else //if ($sponserId == null && $empCategory != null)
        {

            return EmployeeInfo::select(
                'employee_id',
                'employee_name',
                'passfort_no',
                'passfort_expire_date',
                'akama_no',
                'akama_expire_date',
                'spons_name',
                'proj_name',
                'mobile_no',
                'email',
                'date_of_birth',
                'country_name',
                'hourly_employee',
                'catg_name',
                'name',
                'title'
            )
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
                ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                // ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->whereIn('employee_infos.project_id', $projectIdList)
                ->where('employee_infos.job_status', $jobStatus)
                ->orderBy('employee_id')
                ->get();
        }
    }


    public function exportEmployeeInformationDetailsHRReport($project_ids, $sponser_ids, $trade_ids, $jobStatus, $working_shift)
    {
        if ($sponser_ids == null & $trade_ids == null) {
            return EmployeeInfo::select(
                'employee_id',
                'employee_name',
                'passfort_no',
                'akama_no',
                'akama_expire_date',
                'mobile_no',
                'phone_no',
                'details',
                'district_name',
                'division_name',
                'country_name',
                'agency_infos.agc_title as agency_name',
                'spons_name',
                'proj_name',
                'catg_name',
                'employee_types.name as emp_type_name',
                'hourly_employee',
                'job_statuses.title as job_title',
                'isNightShift'
            )
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
                ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
                ->leftjoin('divisions', 'employee_infos.division_id', '=', 'divisions.division_id')
                ->leftjoin('districts', 'employee_infos.district_id', '=', 'districts.district_id')
                ->leftjoin('employee_details', 'employee_infos.emp_auto_id', '=', 'employee_details.emp_auto_id')
                ->leftjoin('agency_infos', 'employee_details.agc_info_auto_id', '=', 'agency_infos.agc_info_auto_id')
                ->whereIn('employee_infos.project_id', $project_ids)
                ->where('employee_infos.job_status', $jobStatus)
                ->orderBy('employee_id')
                ->get();
        } else  if ($sponser_ids == null & $trade_ids != null) {
            return EmployeeInfo::select(
                'employee_id',
                'employee_name',
                'passfort_no',
                'akama_no',
                'akama_expire_date',
                'mobile_no',
                'phone_no',
                'details',
                'district_name',
                'division_name',
                'country_name',
                'agency_infos.agc_title as agency_name',
                'spons_name',
                'proj_name',
                'catg_name',
                'employee_types.name as emp_type_name',
                'hourly_employee',
                'job_statuses.title as job_title',
                'isNightShift'
            )
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
                ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
                ->leftjoin('divisions', 'employee_infos.division_id', '=', 'divisions.division_id')
                ->leftjoin('districts', 'employee_infos.district_id', '=', 'districts.district_id')
                ->leftjoin('employee_details', 'employee_infos.emp_auto_id', '=', 'employee_details.emp_auto_id')
                ->leftjoin('agency_infos', 'employee_details.agc_info_auto_id', '=', 'agency_infos.agc_info_auto_id')
                ->whereIn('employee_infos.project_id', $project_ids)
                ->whereIn('employee_infos.designation_id', $trade_ids)
                ->where('employee_infos.job_status', $jobStatus)
                ->orderBy('employee_id')
                ->get();
        } else  if ($sponser_ids != null & $trade_ids == null) {
            return EmployeeInfo::select(
                'employee_id',
                'employee_name',
                'passfort_no',
                'akama_no',
                'akama_expire_date',
                'mobile_no',
                'phone_no',
                'details',
                'district_name',
                'division_name',
                'country_name',
                'agency_infos.agc_title as agency_name',
                'spons_name',
                'proj_name',
                'catg_name',
                'name as emp_type_name',
                'hourly_employee',
                'job_statuses.title as job_title',
                'isNightShift'
            )
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
                ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
                ->leftjoin('divisions', 'employee_infos.division_id', '=', 'divisions.division_id')
                ->leftjoin('districts', 'employee_infos.district_id', '=', 'districts.district_id')
                ->leftjoin('employee_details', 'employee_infos.emp_auto_id', '=', 'employee_details.emp_auto_id')
                ->leftjoin('agency_infos', 'employee_details.agc_info_auto_id', '=', 'agency_infos.agc_info_auto_id')
                ->whereIn('employee_infos.project_id', $project_ids)
                ->whereIn('employee_infos.sponsor_id', $sponser_ids)
                ->where('employee_infos.job_status', $jobStatus)
                ->orderBy('employee_id')
                ->get();
        } else  if ($sponser_ids != null & $trade_ids != null) {
            return EmployeeInfo::select(
                'employee_id',
                'employee_name',
                'passfort_no',
                'akama_no',
                'akama_expire_date',
                'mobile_no',
                'phone_no',
                'details',
                'district_name',
                'division_name',
                'country_name',
                'agency_infos.agc_title as agency_name',
                'spons_name',
                'proj_name',
                'catg_name',
                'name as emp_type_name',
                'hourly_employee',
                'job_statuses.title as job_title',
                'isNightShift'
            )
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
                ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
                ->leftjoin('divisions', 'employee_infos.division_id', '=', 'divisions.division_id')
                ->leftjoin('districts', 'employee_infos.district_id', '=', 'districts.district_id')
                ->leftjoin('employee_details', 'employee_infos.emp_auto_id', '=', 'employee_details.emp_auto_id')
                ->leftjoin('agency_infos', 'employee_details.agc_info_auto_id', '=', 'agency_infos.agc_info_auto_id')
                ->whereIn('employee_infos.project_id', $project_ids)
                ->whereIn('employee_infos.designation_id', $trade_ids)
                ->whereIn('employee_infos.sponsor_id', $sponser_ids)
                ->where('employee_infos.job_status', $jobStatus)
                ->orderBy('employee_id')
                ->get();
        }
    }


    /*
     ==========================================================================
     ======================Employee Related Report Generation  ================
     ==========================================================================
    */

    public function exportEmployeeInformationByProjectSponserJobStatusEmpTradeEmpType($projectId, $sponserId, $jobStatus, $empCategory, $empTypeId)
    {
        if ($projectId == null && $sponserId == null && $jobStatus == null) {
            return EmployeeInfo::select(
                'employee_id',
                'employee_name',
                'passfort_no',
                'passfort_expire_date',
                'akama_no',
                'akama_expire_date',
                'spons_name',
                'proj_name',
                'mobile_no',
                'email',
                'date_of_birth',
                'country_name',
                'hourly_employee',
                'catg_name',
                'name',
                'title',
                'basic_amount',
                'hourly_rent',
                'basic_hours',
                'food_allowance',
                'mobile_allowance'
            )
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
                ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->orderBy('employee_id')
                ->get();
        } else if ($projectId == null && $sponserId == null && $jobStatus != null) {
            return EmployeeInfo::select(
                'employee_id',
                'employee_name',
                'passfort_no',
                'passfort_expire_date',
                'akama_no',
                'akama_expire_date',
                'spons_name',
                'proj_name',
                'mobile_no',
                'email',
                'date_of_birth',
                'country_name',
                'hourly_employee',
                'catg_name',
                'name',
                'title',
                'basic_amount',
                'hourly_rent',
                'basic_hours',
                'food_allowance',
                'mobile_allowance'
            )
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
                ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->orderBy('employee_id')
                ->where('employee_infos.job_status', $jobStatus)
                ->get();
        } else if ($projectId == null && $sponserId != null && $jobStatus == null) {
            return EmployeeInfo::select(
                'employee_id',
                'employee_name',
                'passfort_no',
                'passfort_expire_date',
                'akama_no',
                'akama_expire_date',
                'spons_name',
                'proj_name',
                'mobile_no',
                'email',
                'date_of_birth',
                'country_name',
                'hourly_employee',
                'catg_name',
                'name',
                'title',
                'basic_amount',
                'hourly_rent',
                'basic_hours',
                'food_allowance',
                'mobile_allowance'
            )
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
                ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->orderBy('employee_id')
                ->where('employee_infos.sponsor_id', $sponserId)
                ->get();
        } else if ($projectId == null && $sponserId != null && $jobStatus != null) {
            return EmployeeInfo::select(
                'employee_id',
                'employee_name',
                'passfort_no',
                'passfort_expire_date',
                'akama_no',
                'akama_expire_date',
                'spons_name',
                'proj_name',
                'mobile_no',
                'email',
                'date_of_birth',
                'country_name',
                'hourly_employee',
                'catg_name',
                'name',
                'title',
                'basic_amount',
                'hourly_rent',
                'basic_hours',
                'food_allowance',
                'mobile_allowance'
            )
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
                ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->orderBy('employee_id')
                ->where('employee_infos.job_status', $jobStatus)
                ->where('employee_infos.sponsor_id', $sponserId)
                ->get();
        } else if ($projectId != null && $sponserId == null && $jobStatus == null) {
            return EmployeeInfo::select(
                'employee_id',
                'employee_name',
                'passfort_no',
                'passfort_expire_date',
                'akama_no',
                'akama_expire_date',
                'spons_name',
                'proj_name',
                'mobile_no',
                'email',
                'date_of_birth',
                'country_name',
                'hourly_employee',
                'catg_name',
                'name',
                'title',
                'basic_amount',
                'hourly_rent',
                'basic_hours',
                'food_allowance',
                'mobile_allowance'
            )
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
                ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->orderBy('employee_id')
                ->where('employee_infos.project_id', $projectId)
                ->get();
        } else if ($projectId != null && $sponserId == null && $jobStatus != null) {
            return EmployeeInfo::select(
                'employee_id',
                'employee_name',
                'passfort_no',
                'passfort_expire_date',
                'akama_no',
                'akama_expire_date',
                'spons_name',
                'proj_name',
                'mobile_no',
                'email',
                'date_of_birth',
                'country_name',
                'hourly_employee',
                'catg_name',
                'name',
                'title',
                'basic_amount',
                'hourly_rent',
                'basic_hours',
                'food_allowance',
                'mobile_allowance'
            )
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
                ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->orderBy('employee_id')
                ->where('employee_infos.job_status', $jobStatus)
                ->where('employee_infos.project_id', $projectId)
                ->get();
        } else if ($projectId != null && $sponserId != null && $jobStatus == null) {
            return EmployeeInfo::select(
                'employee_id',
                'employee_name',
                'passfort_no',
                'passfort_expire_date',
                'akama_no',
                'akama_expire_date',
                'spons_name',
                'proj_name',
                'mobile_no',
                'email',
                'date_of_birth',
                'country_name',
                'hourly_employee',
                'catg_name',
                'name',
                'title',
                'basic_amount',
                'hourly_rent',
                'basic_hours',
                'food_allowance',
                'mobile_allowance'
            )
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
                ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->orderBy('employee_id')
                ->where('employee_infos.project_id', $projectId)
                ->where('employee_infos.sponsor_id', $sponserId)
                ->get();
        } else if ($projectId != null && $sponserId != null && $jobStatus != null) {

            dd($projectId);
            return EmployeeInfo::select(
                'employee_id',
                'employee_name',
                'passfort_no',
                'passfort_expire_date',
                'akama_no',
                'akama_expire_date',
                'spons_name',
                'proj_name',
                'mobile_no',
                'email',
                'date_of_birth',
                'country_name',
                'hourly_employee',
                'catg_name',
                'name',
                'title',
                'basic_amount',
                'hourly_rent',
                'basic_hours',
                'food_allowance',
                'mobile_allowance'
            )
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
                ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->orderBy('employee_id')
                ->whereIn('employee_infos.project_id', $projectId)
                ->where('employee_infos.job_status', $jobStatus)
                ->whereIn('employee_infos.sponsor_id', $sponserId)
                ->get();
        }
    }



    public function exportEmployeeInformationByProjectSponserEmpTradeSalaryMonthAndYear(
        $projectId,
        $sponserId,
        $jobStatus,
        $empCategory,
        $EmpType,
        $month,
        $year
    ) {
        if ($projectId == null && $sponserId == null && $empCategory == null  && $EmpType == null) {
            return EmployeeInfo::select(
                'employee_id',
                'employee_name',
                'passfort_no',
                'passfort_expire_date',
                'akama_no',
                'akama_expire_date',
                'spons_name',
                'proj_name',
                'mobile_no',
                'email',
                'date_of_birth',
                'country_name',
                'hourly_employee',
                'catg_name',
                'name',
                'title',
                'basic_amount',
                'hourly_rent',
                'basic_hours',
                'food_allowance',
                'mobile_allowance'
            )
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
                ->leftjoin('project_infos', 'salary_histories.project_id', '=', 'project_infos.proj_id')
                ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->orderBy('employee_id')->where('slh_month', $month)->where('slh_year', $year)
                ->get();
        } else if ($projectId == null && $sponserId == null && $empCategory == null  && $EmpType != null) {
            return EmployeeInfo::select(
                'employee_id',
                'employee_name',
                'passfort_no',
                'passfort_expire_date',
                'akama_no',
                'akama_expire_date',
                'spons_name',
                'proj_name',
                'mobile_no',
                'email',
                'date_of_birth',
                'country_name',
                'hourly_employee',
                'catg_name',
                'name',
                'title',
                'basic_amount',
                'hourly_rent',
                'basic_hours',
                'food_allowance',
                'mobile_allowance'
            )
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
                ->leftjoin('project_infos', 'salary_histories.project_id', '=', 'project_infos.proj_id')
                ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->orderBy('employee_id')->where('slh_month', $month)->where('slh_year', $year)
                ->where('employee_types.employee_types', $EmpType)
                ->get();
        } else if ($projectId == null && $sponserId == null && $empCategory != null  && $EmpType == null) {
            return EmployeeInfo::select(
                'employee_id',
                'employee_name',
                'passfort_no',
                'passfort_expire_date',
                'akama_no',
                'akama_expire_date',
                'spons_name',
                'proj_name',
                'mobile_no',
                'email',
                'date_of_birth',
                'country_name',
                'hourly_employee',
                'catg_name',
                'name',
                'title',
                'basic_amount',
                'hourly_rent',
                'basic_hours',
                'food_allowance',
                'mobile_allowance'
            )
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
                ->leftjoin('project_infos', 'salary_histories.project_id', '=', 'project_infos.proj_id')
                ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->orderBy('employee_id')->where('slh_month', $month)->where('slh_year', $year)
                ->where('employee_categories.catg_id', $empCategory)
                ->get();
        } else if ($projectId == null && $sponserId == null && $empCategory != null  && $EmpType != null) {
            return EmployeeInfo::select(
                'employee_id',
                'employee_name',
                'passfort_no',
                'passfort_expire_date',
                'akama_no',
                'akama_expire_date',
                'spons_name',
                'proj_name',
                'mobile_no',
                'email',
                'date_of_birth',
                'country_name',
                'hourly_employee',
                'catg_name',
                'name',
                'title',
                'basic_amount',
                'hourly_rent',
                'basic_hours',
                'food_allowance',
                'mobile_allowance'
            )
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
                ->leftjoin('project_infos', 'salary_histories.project_id', '=', 'project_infos.proj_id')
                ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->orderBy('employee_id')->where('slh_month', $month)->where('slh_year', $year)
                ->where('employee_categories.catg_id', $empCategory)
                ->where('employee_types.employee_types', $EmpType)
                ->get();
        } else if ($projectId == null && $sponserId != null && $empCategory == null  && $EmpType == null) {
            return EmployeeInfo::select(
                'employee_id',
                'employee_name',
                'passfort_no',
                'passfort_expire_date',
                'akama_no',
                'akama_expire_date',
                'spons_name',
                'proj_name',
                'mobile_no',
                'email',
                'date_of_birth',
                'country_name',
                'hourly_employee',
                'catg_name',
                'name',
                'title',
                'basic_amount',
                'hourly_rent',
                'basic_hours',
                'food_allowance',
                'mobile_allowance'
            )
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
                ->leftjoin('project_infos', 'salary_histories.project_id', '=', 'project_infos.proj_id')
                ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->orderBy('employee_id')->where('slh_month', $month)->where('slh_year', $year)
                ->where('sponsors.spons_id', $sponserId)
                ->get();
        } else if ($projectId != null && $sponserId == null) {
            return EmployeeInfo::select(
                'employee_id',
                'employee_name',
                'passfort_no',
                'passfort_expire_date',
                'akama_no',
                'akama_expire_date',
                'spons_name',
                'proj_name',
                'mobile_no',
                'email',
                'date_of_birth',
                'country_name',
                'hourly_employee',
                'catg_name',
                'name',
                'title',
                'basic_amount',
                'hourly_rent',
                'basic_hours',
                'food_allowance',
                'mobile_allowance'
            )
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
                ->leftjoin('project_infos', 'salary_histories.project_id', '=', 'project_infos.proj_id')
                ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->orderBy('employee_id')->where('slh_month', $month)->where('slh_year', $year)
                //->where('sponsors.spons_id', $sponserId)
                ->where('salary_histories.project_id', $projectId)
                ->get();
        } else if ($projectId != null && $sponserId != null) {
            return EmployeeInfo::select(
                'employee_id',
                'employee_name',
                'passfort_no',
                'passfort_expire_date',
                'akama_no',
                'akama_expire_date',
                'spons_name',
                'proj_name',
                'mobile_no',
                'email',
                'date_of_birth',
                'country_name',
                'hourly_employee',
                'catg_name',
                'name',
                'title',
                'basic_amount',
                'hourly_rent',
                'basic_hours',
                'food_allowance',
                'mobile_allowance'
            )
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
                ->leftjoin('project_infos', 'salary_histories.project_id', '=', 'project_infos.proj_id')
                ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->orderBy('employee_id')->where('slh_month', $month)->where('slh_year', $year)
                ->where('sponsors.spons_id', $sponserId)
                ->where('salary_histories.project_id', $projectId)
                ->get();
        } else {

            return EmployeeInfo::select(
                'employee_id',
                'employee_name',
                'passfort_no',
                'passfort_expire_date',
                'akama_no',
                'akama_expire_date',
                'spons_name',
                'proj_name',
                'mobile_no',
                'email',
                'date_of_birth',
                'country_name',
                'hourly_employee',
                'catg_name',
                'name',
                'title',
                'basic_amount',
                'hourly_rent',
                'basic_hours',
                'food_allowance',
                'mobile_allowance'
            )
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
                ->leftjoin('project_infos', 'salary_histories.project_id', '=', 'project_infos.proj_id')
                ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->orderBy('employee_id')->where('slh_month', $month)->where('slh_year', $year)
                // ->where('sponsors.spons_id', $sponserId)
                //  ->where('salary_histories.project_id', $projectId)
                ->get();
        }
    }



    public function getEmployeeInfoWithSalaryDetailsReportByProjectSponorTradeAndJobStatus($proj_id, $spons_id, $catg_id, $job_status)
    {

        if ($proj_id ==  null &&  $spons_id ==  null && $catg_id == null && $job_status == null) {
            // 0000

            $employee = EmployeeInfo::with('project', 'status')
                ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->orderBy('employee_id', 'ASC')
                ->get();
        } else if ($proj_id ==  null &&  $spons_id ==  null && $catg_id == null && $job_status != null) {
            //dd('0001');
            $employee = EmployeeInfo::with('project', 'status')
                ->where('job_status', $job_status)
                ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->orderBy('employee_id', 'ASC')
                ->get();
        } else if ($proj_id ==  null &&  $spons_id ==  null && $catg_id != null && $job_status == null) {
            // dd('0010');
            $employee = EmployeeInfo::with('project', 'status')
                ->where('designation_id', $catg_id)
                ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->orderBy('employee_id', 'ASC')
                ->get();
        } else if ($proj_id ==  null &&  $spons_id ==  null && $catg_id != null && $job_status != null) {
            // dd('0011');
            $employee = EmployeeInfo::with('project', 'status')
                ->where('job_status', $job_status)
                ->where('designation_id', $catg_id)
                ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->orderBy('employee_id', 'ASC')
                ->get();
        } else if ($proj_id ==  null &&  $spons_id !=  null && $catg_id == null && $job_status == null) {
            // dd('0100');

            $employee = EmployeeInfo::with('project', 'status')
                ->where('sponsor_id', $spons_id)
                ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->orderBy('employee_id', 'ASC')
                ->get();
        } else if ($proj_id ==  null &&  $spons_id !=  null && $catg_id == null && $job_status != null) {
            // dd('0101');
            $employee = EmployeeInfo::with('project', 'status')
                ->where('sponsor_id', $spons_id)
                ->where('job_status', $job_status)
                ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->orderBy('employee_id', 'ASC')
                ->get();
        } else if ($proj_id ==  null &&  $spons_id !=  null && $catg_id != null && $job_status == null) {
            // dd('0110');
            $employee = EmployeeInfo::with('project', 'status')
                ->where('sponsor_id', $spons_id)
                ->where('designation_id', $catg_id)
                ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->orderBy('employee_id', 'ASC')
                ->get();
        } else if ($proj_id ==  null &&  $spons_id !=  null && $catg_id != null && $job_status != null) {
            //dd('0111');
            $employee = EmployeeInfo::with('project', 'status')
                ->where('job_status', $job_status)
                ->where('sponsor_id', $spons_id)
                ->where('designation_id', $catg_id)
                ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->orderBy('employee_id', 'ASC')
                ->get();
        } else if ($proj_id !=  null &&  $spons_id ==  null && $catg_id == null && $job_status == null) {
            // dd('1000');
            $employee = EmployeeInfo::with('project', 'status')
                ->where('project_id', $proj_id)
                ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->orderBy('employee_id', 'ASC')
                ->get();
        } else if ($proj_id !=  null &&  $spons_id ==  null && $catg_id == null && $job_status != null) {
            // dd('1001');
            $employee = EmployeeInfo::with('project', 'status')
                ->where('project_id', $proj_id)
                ->where('job_status', $job_status)
                ->orderBy('employee_id', 'ASC')
                ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->get();
        } else if ($proj_id !=  null &&  $spons_id ==  null && $catg_id != null && $job_status == null) {
            // dd('1010');
            $employee = EmployeeInfo::with('project', 'status')
                ->where('project_id', $proj_id)
                ->where('designation_id', $catg_id)
                ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->orderBy('employee_id', 'ASC')
                ->get();
        } else if ($proj_id !=  null &&  $spons_id ==  null && $catg_id != null && $job_status != null) {
            // dd('1011');
            $employee = EmployeeInfo::with('project', 'status')
                ->where('project_id', $proj_id)
                ->where('job_status', $job_status)
                ->where('designation_id', $catg_id)
                ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->orderBy('employee_id', 'ASC')
                ->get();
        } else if ($proj_id !=  null &&  $spons_id !=  null && $catg_id == null && $job_status == null) {
            // dd('1100');
            $employee = EmployeeInfo::with('project', 'status')
                ->where('project_id', $proj_id)
                ->where('sponsor_id', $spons_id)
                ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->orderBy('employee_id', 'ASC')
                ->get();
        } else if ($proj_id !=  null &&  $spons_id !=  null && $catg_id == null && $job_status != null) {
            // dd('1101');
            $employee = EmployeeInfo::with('project', 'status')
                ->where('project_id', $proj_id)
                ->where('sponsor_id', $spons_id)
                ->where('job_status', $job_status)
                ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->orderBy('employee_id', 'ASC')
                ->get();
        } else if ($proj_id !=  null &&  $spons_id !=  null && $catg_id != null && $job_status == null) {
            //  dd('1110');

            $employee = EmployeeInfo::with('project', 'status')
                ->where('project_id', $proj_id)
                ->where('sponsor_id', $spons_id)
                ->where('designation_id', $catg_id)
                ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->orderBy('employee_id', 'ASC')
                ->get();
        } else if ($proj_id !=  null &&  $spons_id !=  null && $catg_id != null && $job_status != null) {
            //dd('1111');
            $employee = EmployeeInfo::with('project', 'status')
                ->where('project_id', $proj_id)
                ->where('job_status', $job_status)
                ->where('sponsor_id', $spons_id)
                ->where('designation_id', $catg_id)
                ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->orderBy('employee_id', 'ASC')
                ->get();
        }

        return $employee;
    }


    // HR Related Report
    public function getHREmployeesReporttByProjectSponorTradeAndJobStatus($proj_id_list, $spons_id_list, $catg_id_list, $job_status, $branch_office_id)
    {

        return  EmployeeInfo::select(
            'employee_infos.employee_id',
            'employee_infos.employee_name',
            'employee_infos.akama_no',
            'employee_infos.mobile_no',
            'employee_infos.phone_no',
            'employee_infos.akama_photo',
            'employee_infos.pasfort_photo',
            'countries.country_name',
            'project_infos.proj_name',
            'employee_categories.catg_name',
            'sponsors.spons_name'
        )
            ->where('employee_infos.branch_office_id', $branch_office_id)
            ->where('job_status', $job_status)
            ->whereIn('designation_id', $catg_id_list)
            ->whereIn('sponsor_id', $spons_id_list)
            ->whereIn('project_infos.proj_id', $proj_id_list)
            ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
            ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
            ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
            ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
            ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
            ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
            ->orderBy('project_infos.proj_id', 'ASC')
            ->get();
    }

    // HR Report 4 for employee info and iqama and passport download link
    public function getHREmployeesReporttByMultipleProjectSponorTradeAndJobStatus($proj_id_list, $spons_id_list, $catg_id_list, $job_status_id, $branch_office_id)
    {

        return  EmployeeInfo::select(
            'employee_infos.employee_id',
            'employee_infos.employee_name',
            'employee_infos.akama_no',
            'employee_infos.mobile_no',
            'employee_infos.phone_no',
            'employee_infos.akama_photo',
            'employee_infos.pasfort_photo',
            'countries.country_name',
            'project_infos.proj_name',
            'employee_categories.catg_name',
            'sponsors.spons_name'
        )
            ->where('employee_infos.branch_office_id', $branch_office_id)
            ->whereIn('job_status', $job_status_id)
            ->whereIn('designation_id', $catg_id_list)
            ->whereIn('sponsor_id', $spons_id_list)
            ->whereIn('project_infos.proj_id', $proj_id_list)
            ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
            ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
            ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
            ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
            ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
            ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
            ->orderBy('project_infos.proj_id', 'ASC')
            ->get();
    }

    public function getHREmployeesReporttByMultipleEmployeeIDsToExportAsExcel($employee_ids)
    {
        return EmployeeInfo::select(
            'employee_id',
            'employee_name',
            'passfort_no',
            'akama_no',
            'akama_expire_date',
            'mobile_no',
            'phone_no',
            'details',
            'district_name',
            'division_name',
            'country_name',
            'agency_infos.agc_title as agency_name',
            'spons_name',
            'proj_name',
            'catg_name',
            'name as emp_type_name',
            'hourly_employee',
            'job_statuses.title as job_title',
            'isNightShift'
        )
            ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
            ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
            ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
            ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
            ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
            ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
            ->leftjoin('divisions', 'employee_infos.division_id', '=', 'divisions.division_id')
            ->leftjoin('districts', 'employee_infos.district_id', '=', 'districts.district_id')
            ->leftjoin('employee_details', 'employee_infos.emp_auto_id', '=', 'employee_details.emp_auto_id')
            ->leftjoin('agency_infos', 'employee_details.agc_info_auto_id', '=', 'agency_infos.agc_info_auto_id')
            ->whereIn('employee_infos.employee_id', $employee_ids)
            ->orderBy('employee_id')
            ->get();
    }
    public function getHREmployeesReporttByMultipleProjectSponorTradeAndJobStatusToExportAsExcel($project_ids, $sponser_ids, $trade_ids, $jobStatus_ids, $working_shift)
    {
        return EmployeeInfo::select(
            'employee_id',
            'employee_name',
            'passfort_no',
            'akama_no',
            'akama_expire_date',
            'mobile_no',
            'phone_no',
            'details',
            'district_name',
            'division_name',
            'country_name',
            'agency_infos.agc_title as agency_name',
            'spons_name',
            'proj_name',
            'catg_name',
            'name as emp_type_name',
            'hourly_employee',
            'job_statuses.title as job_title',
            'isNightShift',
            'email'
        )
            ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
            ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
            ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
            ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
            ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
            ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
            ->leftjoin('divisions', 'employee_infos.division_id', '=', 'divisions.division_id')
            ->leftjoin('districts', 'employee_infos.district_id', '=', 'districts.district_id')
            ->leftjoin('employee_details', 'employee_infos.emp_auto_id', '=', 'employee_details.emp_auto_id')
            ->leftjoin('agency_infos', 'employee_details.agc_info_auto_id', '=', 'agency_infos.agc_info_auto_id')
            ->whereIn('employee_infos.project_id', $project_ids)
            ->whereIn('employee_infos.designation_id', $trade_ids)
            ->whereIn('employee_infos.sponsor_id', $sponser_ids)
            ->wherein('employee_infos.job_status', $jobStatus_ids)
            ->orderBy('employee_id')
            ->get();
    }


    public function getHREmployeeReportByVillaNameProjectAndTrade($accomd_ofb_id, $proj_id, $catg_id)
    {


        if ($proj_id ==  null && $catg_id == null) {
            return  $employee = EmployeeInfo::select(
                'employee_infos.employee_id',
                'employee_infos.employee_name',
                'employee_infos.akama_no',
                'employee_infos.akama_expire_date',
                'employee_infos.phone_no',
                'employee_infos.mobile_no',
                'office_buildings.ofb_name',
                'countries.country_name',
                'project_infos.proj_name',
                'employee_categories.catg_name',
                'sponsors.spons_name'
            )
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('office_buildings', 'employee_infos.accomd_ofb_id', '=', 'office_buildings.ofb_id')
                ->whereIn('office_buildings.ofb_id', $accomd_ofb_id)
                ->orderBy('office_buildings.ofb_id', 'ASC')
                ->get();
        } else if ($proj_id ==  null && $catg_id != null) {

            return  $employee = EmployeeInfo::select(
                'employee_infos.employee_id',
                'employee_infos.employee_name',
                'employee_infos.akama_no',
                'employee_infos.akama_expire_date',
                'employee_infos.phone_no',
                'employee_infos.mobile_no',
                'office_buildings.ofb_name',
                'countries.country_name',
                'project_infos.proj_name',
                'employee_categories.catg_name',
                'sponsors.spons_name'
            )
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('office_buildings', 'employee_infos.accomd_ofb_id', '=', 'office_buildings.ofb_id')
                ->whereIn('office_buildings.ofb_id', $accomd_ofb_id)
                ->whereIn('employee_categories.catg_id', $catg_id)
                ->orderBy('office_buildings.ofb_id', 'ASC')
                ->get();
        } else if ($proj_id !=  null && $catg_id == null) {

            return  $employee = EmployeeInfo::select(
                'employee_infos.employee_id',
                'employee_infos.employee_name',
                'employee_infos.akama_no',
                'employee_infos.akama_expire_date',
                'employee_infos.phone_no',
                'employee_infos.mobile_no',
                'office_buildings.ofb_name',
                'countries.country_name',
                'project_infos.proj_name',
                'employee_categories.catg_name',
                'sponsors.spons_name'
            )
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('office_buildings', 'employee_infos.accomd_ofb_id', '=', 'office_buildings.ofb_id')
                ->whereIn('office_buildings.ofb_id', $accomd_ofb_id)
                ->whereIn('project_infos.proj_id', $proj_id)
                ->orderBy('office_buildings.ofb_id', 'ASC')
                ->get();
        } else {

            return  $employee = EmployeeInfo::select(
                'employee_infos.employee_id',
                'employee_infos.employee_name',
                'employee_infos.akama_no',
                'employee_infos.akama_expire_date',
                'employee_infos.phone_no',
                'employee_infos.mobile_no',
                'office_buildings.ofb_name',
                'countries.country_name',
                'project_infos.proj_name',
                'employee_categories.catg_name',
                'sponsors.spons_name'
            )
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('office_buildings', 'employee_infos.accomd_ofb_id', '=', 'office_buildings.ofb_id')
                ->whereIn('office_buildings.ofb_id', $accomd_ofb_id)
                ->whereIn('project_infos.proj_id', $proj_id)
                ->whereIn('employee_categories.catg_id', $catg_id)
                ->orderBy('office_buildings.ofb_id', 'ASC')
                ->get();
        }
    }

    // may be it will be delete 25.7.24
    public function getAnEmployeeActivitiesReport($employee_id, $project_id = 0)
    {
        return $res = DB::select('CALL getEmployeeActivityRecords(?,?)', array($employee_id, $project_id));
    }

    public function getAnEmployeeActivitiesReportByEmployeeId($employee_id, $branch_office_id)
    {
        return $res = DB::select('CALL getAnEmployeeActivitiesRecordsByEmployeeId(?,?)', array($employee_id, $branch_office_id));
    }


    /*
     ==========================================================================
     =============================  Employee List Report Ui ===================
     ==========================================================================
    */

    // EMployee List By Project and Employee Type
    public function getEmployeeInfoWithSalaryDetailsReportByProjectEmpTypeAndHourlyEmp($proj_id, $emp_type_id, $isHourly)
    {

        // Direct & Indirect Basic Salary Employee List
        if ($emp_type_id == 3) {

            if ($proj_id == null) {

                return  EmployeeInfo::with('project', 'status')
                    ->where('job_status', 1)
                    ->whereNull('hourly_employee')
                    ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                    ->orderBy('employee_id', 'ASC')
                    ->get();
            } else {

                return  EmployeeInfo::with('project', 'status')
                    ->where('job_status', 1)
                    ->whereNull('hourly_employee')
                    ->where('project_id', $proj_id)
                    ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                    ->orderBy('employee_id', 'ASC')
                    ->get();
            }
        }

        if ($proj_id == null && $emp_type_id == null) {
            return  $employee = EmployeeInfo::with('project', 'status')
                ->where('job_status', 1)
                ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->orderBy('employee_id', 'ASC')
                ->get();
        } else if ($proj_id == null && $emp_type_id != null) {

            return  $employee = EmployeeInfo::with('project', 'status')->where('hourly_employee', $isHourly)
                ->where('emp_type_id', $emp_type_id)
                ->where('job_status', 1)
                ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->orderBy('employee_id', 'ASC')
                ->get();
        } else if ($proj_id != null && $emp_type_id == null) {

            return  $employee = EmployeeInfo::with('project', 'status')
                ->where('project_id', $proj_id)
                ->where('job_status', 1)
                ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->orderBy('employee_id', 'ASC')
                ->get();
        } else {
            return  $employee = EmployeeInfo::with('project', 'status')
                ->where('hourly_employee', $isHourly)
                ->where('job_status', 1)
                ->where('emp_type_id', $emp_type_id)
                ->where('project_id', $proj_id)
                ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->orderBy('employee_id', 'ASC')
                ->get();
        }
    }

    // HR Report for multiple emp ID
    public function getMultipleEmployeeDetailsInformationForHRReportByMultipleEmpID($employee_id_list, $branch_office_id)
    {

        return  EmployeeInfo::whereIn('employee_infos.employee_id', $employee_id_list)
            ->where('employee_infos.branch_office_id', $branch_office_id)
            ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
            ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
            ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
            ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
            ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
            ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
            ->get();
    }

    public function getEmployeeDetailsWitFileDownloadReportByMultipleEmpID($employee_id_list, $branch_office_id)
    {

        return  EmployeeInfo::whereIn('employee_infos.employee_id', $employee_id_list)->where('employee_infos.branch_office_id', $branch_office_id)
            ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
            ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
            ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
            ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
            ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
            ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
            ->where('employee_infos.job_status', 1)
            ->get();
    }




    public function getProjectWiseEmployeeListByIqamaExpireDate($proj_ids, $sponsor_ids, $todays, $iqama_expire_date, $job_status)
    {

        return EmployeeInfo::with('project', 'status')
            ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
            ->whereBetween('akama_expire_date', [$todays, $iqama_expire_date])
            ->whereIn('project_id', $proj_ids)
            ->whereIn('sponsor_id', $sponsor_ids)
            ->where('job_status', $job_status)
            ->orderBy('akama_expire_date', 'ASC')
            ->get();
    }



    public function getEmployeeListThoseAreNotUpdateIqamaNumber($proj_ids, $sponsor_ids, $job_status)
    {
        $iqama = "0000";
        return   EmployeeInfo::with('project', 'status')
            ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
            ->where("akama_no", "LIKE", "%{$iqama}%")
            ->whereIn("project_id", $proj_ids)
            ->whereIn('sponsor_id', $sponsor_ids)
            ->where('job_status', $job_status)
            ->orderBy('employee_id', 'ASC')
            ->get();
    }

    public function getEmployeeListThoseAreNotUploadedIqamaFile($proj_ids, $sponsor_ids, $job_status)
    {

        return   EmployeeInfo::with('project', 'status')
            ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
            ->where("akama_photo", null)
            ->whereIn("project_id", $proj_ids)
            ->whereIn('sponsor_id', $sponsor_ids)
            ->where('job_status', $job_status)
            ->orderBy('employee_id', 'ASC')
            ->get();
    }


    public function getEmployeeSummaryReportWithTradeWise($project_id, $job_staus)
    {

        if (is_null($project_id)) {

            return EmployeeInfo::select(
                'designation_id',
                'employee_categories.catg_name',
                DB::raw("COUNT(emp_auto_id) as total_emp")
            )
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->groupBy("designation_id")
                ->groupBy('employee_categories.catg_name')
                ->where('job_status', $job_staus)
                ->orderBy('total_emp', 'DESC')
                ->get();
        } else {
            return EmployeeInfo::select(
                'designation_id',
                'employee_categories.catg_name',
                DB::raw("COUNT(emp_auto_id) as total_emp")
            )
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->groupBy("designation_id")
                ->groupBy('employee_categories.catg_name')
                ->where('job_status', $job_staus)->where('project_id', $project_id)
                ->orderBy('total_emp', 'DESC')
                ->get();
        }
    }



    public function getTradeWiseEmployeeSummaryReportOfAbranchOffice($project_id_list, $job_staus, $branch_office_id)
    {
        return EmployeeInfo::select(
            'designation_id',
            'employee_categories.catg_name',
            DB::raw("COUNT(emp_auto_id) as total_emp")
        )
            ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
            ->groupBy("designation_id")
            ->groupBy('employee_categories.catg_name')
            ->where('job_status', $job_staus)->whereIn('project_id', $project_id_list)
            ->where('branch_office_id', $branch_office_id)
            ->orderBy('total_emp', 'DESC')
            ->get();
    }



    public function searchASponsorTotalHumanResourceSummaryReport($sponsor_id, $project_id)
    {
        return EmployeeInfo::select(

            'employee_infos.job_status',
            'job_statuses.title as Job_Title',
            DB::raw("COUNT(emp_auto_id) as total_emp")
        )
            ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
            ->groupBy('job_statuses.title')
            ->groupBy('employee_infos.job_status')
            ->where('project_id', $project_id)
            ->where('employee_infos.sponsor_id', $sponsor_id)
            ->orderBy('employee_infos.job_status', 'ASC')
            ->get();
    }

    public function getSponsorWiseTotalEmployeeSummaryReport($sponsor_id)
    {
        return  $dd = collect(DB::select('call getSponsorBaseEmployeeSummary1(?)', array($sponsor_id)));
    }

    public function getListOFEmployeesThoseHaveBankInformationReport($project_id, $branch_office_id)
    {
        return   DB::select('call getListOfEmployeeThoseHaveBankInformationReport(?,?)', array($project_id, $branch_office_id));
    }

    public function getDesignationHeadBaseEmployeeSummaryReport($project_ids, $designation_heads, $job_stauses)
    {

        if (is_null($project_ids) && is_null($designation_heads) && is_null($job_stauses)) {
            // 000
            return EmployeeInfo::select(
                'designation__heads.des_head_name',
                DB::raw("COUNT(employee_infos.emp_auto_id) as total_emp")
            )
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->leftjoin('designation__heads', 'employee_categories.dh_auto_id', '=', 'designation__heads.dh_auto_id')
                ->groupBy("employee_categories.dh_auto_id")
                ->groupBy("designation__heads.des_head_name")
                ->orderBy('designation__heads.rank_code', 'DESC')
                ->get();
        } else if (is_null($project_ids) && is_null($designation_heads) && !is_null($job_stauses)) {
            // 001
            return EmployeeInfo::select(
                'designation__heads.des_head_name',
                DB::raw("COUNT(employee_infos.emp_auto_id) as total_emp")
            )
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->leftjoin('designation__heads', 'employee_categories.dh_auto_id', '=', 'designation__heads.dh_auto_id')
                ->whereIn('employee_infos.job_status', $job_stauses)
                ->groupBy("employee_categories.dh_auto_id")
                ->groupBy("designation__heads.des_head_name")
                ->orderBy('designation__heads.rank_code', 'DESC')
                ->get();
        } else if (is_null($project_ids) && !is_null($designation_heads) && is_null($job_stauses)) {
            // 010
            return EmployeeInfo::select(
                'designation__heads.des_head_name',
                DB::raw("COUNT(employee_infos.emp_auto_id) as total_emp")
            )
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->leftjoin('designation__heads', 'employee_categories.dh_auto_id', '=', 'designation__heads.dh_auto_id')
                ->whereIn('employee_categories.dh_auto_id', $designation_heads)
                ->groupBy("employee_categories.dh_auto_id")
                ->groupBy("designation__heads.des_head_name")
                ->orderBy('designation__heads.rank_code', 'DESC')
                ->get();
        } else if (is_null($project_ids) && !is_null($designation_heads) && !is_null($job_stauses)) {
            // 011
            return EmployeeInfo::select(
                'designation__heads.des_head_name',
                DB::raw("COUNT(employee_infos.emp_auto_id) as total_emp")
            )
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->leftjoin('designation__heads', 'employee_categories.dh_auto_id', '=', 'designation__heads.dh_auto_id')
                ->whereIn('employee_infos.job_status', $job_stauses)
                ->whereIn('employee_categories.dh_auto_id', $designation_heads)
                ->groupBy("employee_categories.dh_auto_id")
                ->groupBy("designation__heads.des_head_name")
                ->orderBy('designation__heads.rank_code', 'DESC')
                ->get();
        } else if (!is_null($project_ids) && is_null($designation_heads) && is_null($job_stauses)) {
            // 100
            return EmployeeInfo::select(
                'designation__heads.des_head_name',
                DB::raw("COUNT(employee_infos.emp_auto_id) as total_emp")
            )
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->leftjoin('designation__heads', 'employee_categories.dh_auto_id', '=', 'designation__heads.dh_auto_id')
                ->whereIn('employee_infos.project_id', $project_ids)
                ->groupBy("employee_categories.dh_auto_id")
                ->groupBy("designation__heads.des_head_name")
                ->orderBy('designation__heads.rank_code', 'DESC')
                ->get();
        } else if (!is_null($project_ids) && is_null($designation_heads) && !is_null($job_stauses)) {
            // 101
            return EmployeeInfo::select(
                'designation__heads.des_head_name',
                DB::raw("COUNT(employee_infos.emp_auto_id) as total_emp")
            )
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->leftjoin('designation__heads', 'employee_categories.dh_auto_id', '=', 'designation__heads.dh_auto_id')
                ->whereIn('employee_infos.project_id', $project_ids)
                ->whereIn('employee_infos.job_status', $job_stauses)
                ->groupBy("employee_categories.dh_auto_id")
                ->groupBy("designation__heads.des_head_name")
                ->orderBy('designation__heads.rank_code', 'DESC')
                ->get();
        } else if (!is_null($project_ids) && !is_null($designation_heads) && is_null($job_stauses)) {
            // 110
            return EmployeeInfo::select(
                'designation__heads.des_head_name',
                DB::raw("COUNT(employee_infos.emp_auto_id) as total_emp")
            )
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->leftjoin('designation__heads', 'employee_categories.dh_auto_id', '=', 'designation__heads.dh_auto_id')
                ->whereIn('employee_infos.project_id', $project_ids)
                ->whereIn('employee_infos.job_status', $job_stauses)
                ->groupBy("employee_categories.dh_auto_id")
                ->groupBy("designation__heads.des_head_name")
                ->orderBy('designation__heads.rank_code', 'DESC')
                ->get();
        } else {
            // 111
            return EmployeeInfo::select(
                'designation__heads.des_head_name',
                DB::raw("COUNT(employee_infos.emp_auto_id) as total_emp")
            )
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->leftjoin('designation__heads', 'employee_categories.dh_auto_id', '=', 'designation__heads.dh_auto_id')
                ->whereIn('employee_infos.project_id', $project_ids)
                ->whereIn('employee_infos.job_status', $job_stauses)
                ->whereIn('employee_categories.dh_auto_id', $designation_heads)
                ->groupBy("employee_categories.dh_auto_id")
                ->groupBy("designation__heads.des_head_name")
                ->orderBy('designation__heads.rank_code', 'DESC')
                ->get();
        }
    }

    public function getDesignationHeadBaseEmployeeListReport($project_ids, $designation_heads, $job_stauses)
    {


        if (is_null($project_ids) && is_null($designation_heads) && is_null($job_stauses)) {
            // 000
            return EmployeeInfo::leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                // ->where('employee_infos.job_status', 1)
                ->get();
        } else if (is_null($project_ids) && is_null($designation_heads) && !is_null($job_stauses)) {
            // 001
            return EmployeeInfo::leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->whereIn('employee_infos.job_status', $job_stauses)
                ->get();
        } else if (is_null($project_ids) && !is_null($designation_heads) && is_null($job_stauses)) {
            // 010
            return EmployeeInfo::leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->whereIn('employee_categories.dh_auto_id', $designation_heads)
                ->get();
        } else if (is_null($project_ids) && !is_null($designation_heads) && !is_null($job_stauses)) {
            // 011
            // 010
            return EmployeeInfo::leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->whereIn('employee_categories.dh_auto_id', $designation_heads)
                ->whereIn('employee_infos.job_status', $job_stauses)
                ->get();
        } else if (!is_null($project_ids) && is_null($designation_heads) && is_null($job_stauses)) {
            // 100
            return EmployeeInfo::leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->whereIn('employee_infos.project_id', $project_ids)
                ->get();
        } else if (!is_null($project_ids) && is_null($designation_heads) && !is_null($job_stauses)) {
            // 101
            return EmployeeInfo::leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->whereIn('employee_infos.project_id', $project_ids)
                ->whereIn('employee_infos.job_status', $job_stauses)
                ->get();
        } else if (!is_null($project_ids) && !is_null($designation_heads) && is_null($job_stauses)) {
            // 110
            return EmployeeInfo::leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->whereIn('employee_infos.project_id', $project_ids)
                ->whereIn('employee_categories.dh_auto_id', $designation_heads)
                ->get();
        } else {
            // 111
            return EmployeeInfo::leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->whereIn('employee_infos.project_id', $project_ids)
                ->whereIn('employee_infos.job_status', $job_stauses)
                ->whereIn('employee_categories.dh_auto_id', $designation_heads)
                ->get();
        }
    }

    public function getTotalNoOfEmployeeByProjectAndTradeWise($project_id, $trade_id, $job_staus)
    {
        return   EmployeeInfo::where('designation_id', (int) $trade_id)->where('job_status', (int) $job_staus)->where('project_id', (int) $project_id)->count();
    }


    public function getAllNewEmployeeInsertListByDateToDateReport($from_date, $today, $emp_type, $is_hourly, $sponsor_id_list, $branch_office_id)
    {


        if (is_null($emp_type) && is_null($is_hourly) && is_null($sponsor_id_list)) {
            // All Emp

            return EmployeeInfo::select(
                'employee_id',
                'employee_name',
                'passfort_no',
                'akama_no',
                'joining_date',
                'hourly_employee',
                'employee_infos.created_at',
                'users.name',
                'project_infos.proj_name',
                'employee_categories.catg_name',
                'sponsors.spons_name',
                'salary_details.basic_amount',
                'salary_details.basic_amount',
                'salary_details.food_allowance',
                'salary_details.hourly_rent'
            )
                ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->leftjoin('users', 'users.id', '=', 'employee_infos.entered_id')
                ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->whereBetween('employee_infos.created_at', [$from_date, $today])
                ->where('employee_infos.branch_office_id', $branch_office_id)
                ->get();
        } else  if (is_null($emp_type) && is_null($is_hourly)) {
            // All Emp but selected sponsor
            return EmployeeInfo::select(
                'employee_id',
                'employee_name',
                'passfort_no',
                'akama_no',
                'joining_date',
                'hourly_employee',
                'employee_infos.created_at',
                'users.name',
                'project_infos.proj_name',
                'employee_categories.catg_name',
                'sponsors.spons_name',
                'salary_details.basic_amount',
                'salary_details.basic_amount',
                'salary_details.food_allowance',
                'salary_details.hourly_rent'
            )
                ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->leftjoin('users', 'users.id', '=', 'employee_infos.entered_id')
                ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->whereBetween('employee_infos.created_at', [$from_date, $today])
                ->whereIn('employee_infos.sponsor_id', $sponsor_id_list)
                ->where('employee_infos.branch_office_id', $branch_office_id)
                ->get();
        } else if ($emp_type == 3) {
            //All(Direct & Indirect) Basic Emp
            if (is_null($sponsor_id_list)) {

                return EmployeeInfo::select(
                    'employee_id',
                    'employee_name',
                    'passfort_no',
                    'akama_no',
                    'joining_date',
                    'hourly_employee',
                    'employee_infos.created_at',
                    'users.name',
                    'project_infos.proj_name',
                    'employee_categories.catg_name',
                    'sponsors.spons_name',
                    'salary_details.basic_amount',
                    'salary_details.basic_amount',
                    'salary_details.food_allowance',
                    'salary_details.hourly_rent'
                )
                    ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                    ->leftjoin('users', 'users.id', '=', 'employee_infos.entered_id')
                    ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
                    ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                    ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                    ->where('employee_infos.hourly_employee', $is_hourly)
                    ->whereBetween('employee_infos.created_at', [$from_date, $today])
                    ->where('employee_infos.branch_office_id', $branch_office_id)
                    ->get();
            } else {
                return EmployeeInfo::select(
                    'employee_id',
                    'employee_name',
                    'passfort_no',
                    'akama_no',
                    'joining_date',
                    'hourly_employee',
                    'employee_infos.created_at',
                    'users.name',
                    'project_infos.proj_name',
                    'employee_categories.catg_name',
                    'sponsors.spons_name',
                    'salary_details.basic_amount',
                    'salary_details.basic_amount',
                    'salary_details.food_allowance',
                    'salary_details.hourly_rent'
                )
                    ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                    ->leftjoin('users', 'users.id', '=', 'employee_infos.entered_id')
                    ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
                    ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                    ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                    ->where('employee_infos.hourly_employee', $is_hourly)
                    ->whereBetween('employee_infos.created_at', [$from_date, $today])
                    ->whereIn('employee_infos.sponsor_id', $sponsor_id_list)
                    ->where('employee_infos.branch_office_id', $branch_office_id)
                    ->get();
            }
        } else {
            // dd(200,$emp_type);
            if (is_null($sponsor_id_list)) {

                return EmployeeInfo::select(
                    'employee_id',
                    'employee_name',
                    'passfort_no',
                    'akama_no',
                    'joining_date',
                    'hourly_employee',
                    'employee_infos.created_at',
                    'users.name',
                    'project_infos.proj_name',
                    'employee_categories.catg_name',
                    'sponsors.spons_name',
                    'salary_details.basic_amount',
                    'salary_details.basic_amount',
                    'salary_details.food_allowance',
                    'salary_details.hourly_rent'
                )
                    ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                    ->leftjoin('users', 'users.id', '=', 'employee_infos.entered_id')
                    ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
                    ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                    ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                    ->where('employee_infos.emp_type_id', $emp_type)->where('employee_infos.hourly_employee', $is_hourly)
                    ->whereBetween('employee_infos.created_at', [$from_date, $today])
                    ->where('employee_infos.branch_office_id', $branch_office_id)
                    ->get();
            } else {
                return EmployeeInfo::select(
                    'employee_id',
                    'employee_name',
                    'passfort_no',
                    'akama_no',
                    'joining_date',
                    'hourly_employee',
                    'employee_infos.created_at',
                    'users.name',
                    'project_infos.proj_name',
                    'employee_categories.catg_name',
                    'sponsors.spons_name',
                    'salary_details.basic_amount',
                    'salary_details.basic_amount',
                    'salary_details.food_allowance',
                    'salary_details.hourly_rent'
                )
                    ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                    ->leftjoin('users', 'users.id', '=', 'employee_infos.entered_id')
                    ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
                    ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                    ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                    ->where('employee_infos.emp_type_id', $emp_type)->where('employee_infos.hourly_employee', $is_hourly)
                    ->whereBetween('employee_infos.created_at', [$from_date, $today])
                    ->whereIn('employee_infos.sponsor_id', $sponsor_id_list)
                    ->where('employee_infos.branch_office_id', $branch_office_id)
                    ->get();
            }
        }
    }

    public function getAllNewEmployeeInsertedSummaryReportBySponsorAndByDateToDate($from_date, $today, $sponsor_id_list, $branch_office_id)
    {

        return EmployeeInfo::select(
            'sponsor_id',
            'spons_name',
            DB::raw("count(employee_infos.employee_id) as total_emp")
        )
            ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
            ->whereBetween('employee_infos.joining_date', [$from_date, $today])
            ->whereIn('employee_infos.sponsor_id', $sponsor_id_list)
            ->where('employee_infos.branch_office_id', $branch_office_id)
            ->groupBy('sponsor_id')
            ->groupBy('spons_name')
            ->get();
    }

    public function searchEmployeesForCurrentDeductionSettingEmployeesReport($employee_ids, $project_ids, $sponsor_ids, $employee_type_id, $branch_office_id)
    {


         return EmployeeInfo::select(
                'employee_infos.emp_auto_id',
                'employee_infos.employee_id',
                'employee_infos.employee_name',
                'employee_infos.akama_no',
                'employee_infos.hourly_employee',
                'salary_details.iqama_adv_inst_amount',
                'salary_details.other_adv_inst_amount'
            )
            ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
            ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
            ->leftjoin('monthly_work_histories', 'employee_infos.emp_auto_id', '=', 'monthly_work_histories.emp_id')
            ->where('monthly_work_histories.month_id', $month)
            ->where('monthly_work_histories.year_id', $year)
            ->orderBy('employee_infos.employee_id')
            ->get();

        $query = EmployeeInfo::leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
            ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
            ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
            ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
            // ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
            ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
            ->select(
                'employee_infos.*',
                'employee_categories.catg_name',
                'sponsors.spons_name',
                'project_infos.proj_name',
                'salary_details.*',
                // 'job_statuses.*'
            );
        if (count($employee_ids) != 0) {
            $query->whereIn('employee_infos.employee_id', $employee_ids);
        } else {
            $query->whereIn('employee_infos.project_id', $project_ids)
                ->whereIn('employee_infos.sponsor_id', $sponsor_ids)
                ->where('employee_infos.job_status', 1)
                ->where('employee_infos.branch_office_id', $branch_office_id);
        }

        if ($employee_type_id == 0) {
            // Hourly
            $query->where('employee_infos.hourly_employee', '=', 1);
        } elseif ($employee_type_id == 1) {
            // Direct Basic
            $query->where('employee_infos.hourly_employee', '=', NULL);
            $query->where('employee_infos.emp_type_id', '=', 1);
        } elseif ($employee_type_id == 2) {
            // Indirect
            $query->where('employee_infos.emp_type_id', '=', 2);
        } elseif ($employee_type_id == 3) {
            // Indirect and Direct Basic
            $query->where('employee_infos.hourly_employee', '=', NULL);
            $query->whereIn('employee_infos.emp_type_id', [1, 2]);
        }

        $query->orderBy('employee_id', 'ASC');
        return $query->get();


        // $perPage = $request->per_page ?? 1000;
        // $records = $query->get();
        // return $records->items();
    }


    // Basic Salary Direct and Basic Salary Hourly and Indirect Employee Report
    public function getListOfEmployeesDetailsOfAbranchOfficeByProjectsSponsorsAndEmployeTypeHRReport($project_id_list, $sponsor_id_list, $emp_type_list, $is_hourly, $branch_office_id)
    {

        if (is_null($emp_type_list)) {

            return EmployeeInfo::leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->whereIn('employee_infos.project_id', $project_id_list)
                ->whereIn('employee_infos.sponsor_id', $sponsor_id_list)
                ->where('employee_infos.job_status', 1)
                ->where('employee_infos.branch_office_id', $branch_office_id)
                ->orderBy('employee_id')
                ->get();
        } else {
            return EmployeeInfo::leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->whereIn('employee_infos.project_id', $project_id_list)
                ->whereIn('employee_infos.sponsor_id', $sponsor_id_list)
                ->where('employee_infos.hourly_employee', $is_hourly)
                ->whereIn('employee_infos.emp_type_id', $emp_type_list)
                ->where('employee_infos.job_status', 1)
                ->where('employee_infos.branch_office_id', $branch_office_id)
                ->orderBy('employee_id')
                ->get();
        }
    }

    // Total Employee Summary HR Report
    function getProjectAndSponsorWiseTotalEmployeeSummary($project_id_list, $sponsor_id_list)
    {

        if (is_null($project_id_list) && is_null($sponsor_id_list)) {
            return  $abc = EmployeeInfo::select(
                DB::raw("count(employee_infos.employee_id) as total_emp")
            )
                ->where('employee_infos.job_status', 1)
                ->first();
        } else if (is_null($project_id_list) && !is_null($sponsor_id_list)) {
            return  $abc = EmployeeInfo::select(
                DB::raw("count(employee_infos.employee_id) as total_emp")
            )
                ->whereIn('employee_infos.sponsor_id', $sponsor_id_list)
                ->where('employee_infos.job_status', 1)
                ->first();
        } else if (!is_null($project_id_list) && is_null($sponsor_id_list)) {
            return  $abc = EmployeeInfo::select(
                DB::raw("count(employee_infos.employee_id) as total_emp")
            )
                ->whereIn('employee_infos.project_id', $project_id_list)
                ->where('employee_infos.job_status', 1)
                ->first();
        } else {
            return  $abc = EmployeeInfo::select(
                DB::raw("count(employee_infos.employee_id) as total_emp")
            )
                ->whereIn('employee_infos.project_id', $project_id_list)
                ->whereIn('employee_infos.sponsor_id', $sponsor_id_list)
                ->where('employee_infos.job_status', 1)
                ->first();
        }
    }
    // Total Employee Summary HR Report
    function getProjectAndSponsorWiseTotalBasicOrHourlyEmployeeEmployeeSummary($project_id_list, $sponsor_id_list, $emp_type_list, $is_hourly)
    {

        if (is_null($project_id_list) && is_null($sponsor_id_list)) {
            return  $abc = EmployeeInfo::select(
                DB::raw("count(employee_infos.employee_id) as total_emp")
            )
                ->where('employee_infos.hourly_employee', $is_hourly)
                ->whereIn('employee_infos.emp_type_id', $emp_type_list)
                ->where('employee_infos.job_status', 1)
                ->first();
        } else if (is_null($project_id_list) && !is_null($sponsor_id_list)) {
            return  $abc = EmployeeInfo::select(
                DB::raw("count(employee_infos.employee_id) as total_emp")
            )
                ->whereIn('employee_infos.sponsor_id', $sponsor_id_list)
                ->where('employee_infos.hourly_employee', $is_hourly)
                ->whereIn('employee_infos.emp_type_id', $emp_type_list)
                ->where('employee_infos.job_status', 1)
                ->first();
        } else if (!is_null($project_id_list) && is_null($sponsor_id_list)) {
            return  $abc = EmployeeInfo::select(
                DB::raw("count(employee_infos.employee_id) as total_emp")
            )
                ->whereIn('employee_infos.project_id', $project_id_list)
                ->where('employee_infos.hourly_employee', $is_hourly)
                ->whereIn('employee_infos.emp_type_id', $emp_type_list)
                ->where('employee_infos.job_status', 1)
                ->first();
        } else {
            return  $abc = EmployeeInfo::select(
                DB::raw("count(employee_infos.employee_id) as total_emp")
            )
                ->whereIn('employee_infos.project_id', $project_id_list)
                ->whereIn('employee_infos.sponsor_id', $sponsor_id_list)
                ->where('employee_infos.hourly_employee', $is_hourly)
                ->whereIn('employee_infos.emp_type_id', $emp_type_list)
                ->where('employee_infos.job_status', 1)
                ->first();
        }
    }

    public function getAnEmployeesInfoWithSalaryDetailForEmployeePromotionDetailsReport($multiple_employee_id, $branch_office_id)
    {
        //   return EmployeeInfo::select('employee_infos.emp_auto_id', 'employee_infos.employee_name', 'employee_infos.employee_id', 'employee_infos.akama_no', 'employee_infos.isNightShift','employee_infos.hourly_employee',
        //     'employee_infos.joining_date','project_infos.proj_name','countries.country_name','employee_categories.catg_name',
        //   "salary_details.basic_amount","salary_details.house_rent","salary_details.hourly_rent","salary_details.mobile_allowance","salary_details.medical_allowance","salary_details.local_travel_allowance"
        //   ,"salary_details.conveyance_allowance","salary_details.others1","salary_details.food_allowance","salary_details.saudi_tax")
        //       ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
        //       ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
        //       ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
        //       ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
        //       // ->where('job_status', 1)
        //       ->whereIn('employee_id',$multiple_employee_id)
        //       ->where('employee_infos.branch_office_id',$branch_office_id)
        //       ->orderBy('employee_id')
        //       ->get();

        return EmployeeInfo::select(
            'employee_infos.emp_auto_id',
            'employee_infos.employee_name',
            'employee_infos.employee_id',
            'employee_infos.akama_no',
            'employee_infos.isNightShift',
            'employee_infos.hourly_employee',
            'employee_infos.joining_date',
            'project_infos.proj_name',
            'countries.country_name',
            'employee_categories.catg_name',
            'sponsors.spons_name',
            "salary_details.basic_amount",
            "salary_details.house_rent",
            "salary_details.hourly_rent",
            "salary_details.mobile_allowance",
            "salary_details.medical_allowance",
            "salary_details.local_travel_allowance",
            "salary_details.conveyance_allowance",
            "salary_details.others1",
            "salary_details.food_allowance",
            "salary_details.saudi_tax"
        )
            ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
            ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
            ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
            ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
            ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
            //->where('job_status', 1)
            ->whereIn('employee_id', $multiple_employee_id)
            ->where('employee_infos.branch_office_id', $branch_office_id)
            ->orderBy('employee_id')
            ->get();
    }

    public function getListOfEmployeesInfoWithSalaryDetailForEmployeePromotionDetailsReport($project_ids, $des_head_ids, $branch_office_id)
    {
        return EmployeeInfo::select(
            'employee_infos.emp_auto_id',
            'employee_infos.employee_name',
            'employee_infos.employee_id',
            'employee_infos.akama_no',
            'employee_infos.isNightShift',
            'employee_infos.hourly_employee',
            'employee_infos.joining_date',
            'project_infos.proj_name',
            'countries.country_name',
            'employee_categories.catg_name',
            "salary_details.basic_amount",
            "salary_details.house_rent",
            "salary_details.hourly_rent",
            "salary_details.mobile_allowance",
            "salary_details.medical_allowance",
            "salary_details.local_travel_allowance",
            "salary_details.conveyance_allowance",
            "salary_details.others1",
            "salary_details.food_allowance",
            "salary_details.saudi_tax"
        )
            ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
            ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
            ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
            ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
            ->where('job_status', 1)
            ->whereIn('employee_infos.project_id', $project_ids)
            ->whereIn('employee_categories.dh_auto_id', $des_head_ids)
            ->where('employee_infos.branch_office_id', $branch_office_id)
            ->orderBy('employee_id')
            ->get();
    }

    /*
     ==========================================================================
     ============================= Employee Expert Trade Information ==========
     ==========================================================================
    */
    public function insertAnEmpMultipleTradeExpertnessInformation($emp_auto_id, $designation_id, $insert_by)
    {
        if ($this->checkThisEmployeeAlreadyExpertInThisTrade($emp_auto_id, $designation_id) == false) {
            return EmpExpertTrade::insertGetId([
                'emp_auto_id' => $emp_auto_id,
                'catg_trade_id' => $designation_id,
                'insert_by' => $insert_by,
                'created_at' => Carbon::now(),
            ]);
        }
        return 0;
    }

    private function checkThisEmployeeAlreadyExpertInThisTrade($emp_auto_id, $designation_id)
    {
        return EmpExpertTrade::where('emp_auto_id', $emp_auto_id)->where('catg_trade_id', $designation_id)->count() > 0 ? true : false;
    }




    /*
     ==========================================================================
     ============================= Employee Details Information ===============
     ==========================================================================
    */

    public function insertEmployeeDetailsInformation(
        $emp_auto_id,
        $dept_id,
        $last_education_id,
        $religion_id,
        $expertness_rating,
        $country_phone_no,
        $agc_info_auto_id,
        $gender,
        $is_married,
        $blood_group,
        $present_address,
        $ref_employee_id,
        $remarks
    ) {
        return EmployeeDetails::insertGetId([
            'emp_auto_id' => $emp_auto_id,
            'dept_id' => $dept_id,
            'last_education_id' => $last_education_id,
            'religion_id' => $religion_id,
            'expertness_rating' => $expertness_rating,
            'country_phone_no' => $country_phone_no,
            'agc_info_auto_id' => $agc_info_auto_id,
            'gender' => $gender,
            'is_married' => $is_married,
            'blood_group' => $blood_group,
            'present_address' => $present_address,
            'ref_employee_id' => $ref_employee_id,
            'remarks' => $remarks,
            'created_at' => Carbon::now(),
        ]);
    }

    public function updateAnEmployeeDetailsTableAllInformation(
        $emp_auto_id,
        $dept_id,
        $last_education_id,
        $religion_id,
        $expertness_rating,
        $country_phone_no,
        $agc_info_auto_id,
        $gender,
        $is_married,
        $blood_group,
        $present_address,
        $ref_employee_id,
        $remarks,
        $updated_by
    ) {
        return EmployeeDetails::where('emp_auto_id', $emp_auto_id)->update([
            'dept_id' => $dept_id,
            'last_education_id' => $last_education_id,
            'religion_id' => $religion_id,
            // 'expertness_rating' => $expertness_rating,
            'country_phone_no' => $country_phone_no,
            'agc_info_auto_id' => $agc_info_auto_id,
            'gender' => $gender,
            'is_married' => $is_married,
            'blood_group' => $blood_group,
            'present_address' => $present_address,
            'ref_employee_id' => $ref_employee_id,
            'remarks' => $remarks,
            'updated_at' => Carbon::now(),
            'updated_by' => $updated_by,
        ]);
    }


    public function updateAnEmployeeLeaveContractDuration($emp_auto_id, $leave_contract)
    {
        return EmployeeDetails::where('emp_auto_id', $emp_auto_id)->update([
            'leave_contract' => $leave_contract,
        ]);
    }

    public function updateAnEmployeeGOSINumber($emp_auto_id, $gosi_number)
    {
        return EmployeeDetails::where('emp_auto_id', $emp_auto_id)->update([
            'gosi_number' => $gosi_number,
        ]);
    }

    public function updateAnEmployeeAdministationActivityRemarks($emp_auto_id, $emp_act_remarks)
    {
        return EmployeeDetails::where('emp_auto_id', $emp_auto_id)->update([
            'emp_act_remarks' => $emp_act_remarks,
        ]);
    }



    public function updateAnEmployeeHomeCountryContactNumber($emp_auto_id, $country_phone_no)
    {

        if (is_null($country_phone_no) == false) {
            return EmployeeDetails::where('emp_auto_id', $emp_auto_id)->update([
                'country_phone_no' => $country_phone_no
            ]);
        }
    }
    public function updateAnEmployeeReferencePersonInformation($emp_auto_id, $ref_person_info, $ref_contact_no, $remarks)
    {
        return EmployeeDetails::where('emp_auto_id', $emp_auto_id)->update([
            'ref_employee_id' => $ref_person_info,
            'ref_contact_no' => $ref_contact_no,
            'remarks' => $remarks,
        ]);
    }

    public function updateEmployeeAgencyInfo($emp_auto_id, $agency_id)
    {
        if (is_null($agency_id) == false) {

            EmployeeDetails::where('emp_auto_id', $emp_auto_id)->update([
                'agc_info_auto_id' => $agency_id,
                'updated_at' => Carbon::now(),
            ]);
        }

        return  EmployeeInfo::where('emp_auto_id', $emp_auto_id)->update([
            'agc_info_auto_id' => $agency_id,
            'updated_at' => Carbon::now(),
        ]);
    }

    public function updateEmployeeEducationalDocumentsPath($emp_auto_id, $filePath, $dbColoumName)
    {
        if ($filePath == '' || $filePath == null)
            return false;
        return  EmployeeDetails::where('emp_auto_id', $emp_auto_id)->update([
            $dbColoumName => $filePath,
            'updated_at' => Carbon::now(),
        ]);
    }

    public function updateAnEmployeeBloodGroupInfo($emp_auto_id, $blood_group, $bg_paper_path)
    {
        if (is_null($blood_group) == false && is_null($bg_paper_path) == false) {
            return EmployeeDetails::where('emp_auto_id', $emp_auto_id)->update([
                'blood_group' => $blood_group,
                'blood_group_paper' => $bg_paper_path
            ]);
        } else if (is_null($blood_group) == false) {
            return EmployeeDetails::where('emp_auto_id', $emp_auto_id)->update([
                'blood_group' => $blood_group
            ]);
        } else if (is_null($bg_paper_path) == false) {
            return EmployeeDetails::where('emp_auto_id', $emp_auto_id)->update([
                'blood_group_paper' => $bg_paper_path
            ]);
        }
        return false;
    }

    public function updateEmployeeAsStaff($emp_auto_id, $staff_employee)
    {
        if ($staff_employee == '' || $staff_employee == null)
            $staff_employee = 0;
        return  EmployeeDetails::where('emp_auto_id', $emp_auto_id)->update([
            'staff_employee' => $staff_employee,
            'updated_at' => Carbon::now(),
        ]);
    }

    public function updateEmployeeTypeIdAndIsEmployeeStaffAndHourlyEmployee($emp_auto_id, $staff_employee, $employee_type_id, $hourly_employee, $update_by_id)
    {

        EmployeeDetails::where('emp_auto_id', $emp_auto_id)->update([
            'staff_employee' => $staff_employee,
            'updated_at' => Carbon::now(),
        ]);
        EmployeeInfo::where('emp_auto_id', $emp_auto_id)->update([
            'emp_type_id' => $employee_type_id,
            'hourly_employee' => $hourly_employee,
            'update_by' => $update_by_id,
            'updated_at' => Carbon::now(),
        ]);
        return true;
    }

    // public function insertPreviousEmpRecords(){

    //     $records = EmployeeInfo::get();
    //     foreach($records as $ar){
    //       // dd($ar);
    //         $this->insertEmployeeDetailsInformation($ar->emp_auto_id,2,1,1,$ar->emp_rating,$ar->phone_no,
    //         $ar->agc_info_auto_id,($ar->gender == 1 ? 'M':'F') ,$ar->maritus_status,$ar->present_address,"","");
    //     }
    // }




    /*
     ==========================================================================
     ============================= Employee Salary Information ================
     ==========================================================================
    */


    public function getAnEmployeeSalaryDetailsByEmpAutoId($emp_auto_id)
    {
        return $salary = SalaryDetails::where('emp_id', $emp_auto_id)->first();
    }

    public function getAnBasicEmployeeTotalSalaryAmountByEmpAutoId($emp_auto_id)
    {
        $record = SalaryDetails::where('emp_id', $emp_auto_id)->first();

        if ($record == null) {
            return 0;
        }
        return ($record->basic_amount + $record->house_rent + $record->mobile_allowance + $record->medical_allowance
            + $record->local_travel_allowance + $record->conveyance_allowance + $record->food_allowance + $record->others1);
    }

    public function getAnEmployeeIqamaAdvanceAndOtherAdvanceInstallAmountByEmpAutoId($emp_auto_id)
    {
        return SalaryDetails::select('iqama_adv_inst_amount', 'other_adv_inst_amount')->where('emp_id', $emp_auto_id)->first();
    }

    public function getAnEmployeeSalaryDetailsWithIncrementNoByEmpAutoId($emp_auto_id)
    {
        return   SalaryDetails::where('emp_id', $emp_auto_id)->select('increment_no')->first();
    }

    // may be not used now 15.7.24
    // public function getEmployeeSalaryDetailsListWithPagination1($limit = null)
    // {
    //     if ($limit == null || $limit <= 0) {
    //         return $all = SalaryDetails::orderBy('sdetails_id', 'DESC')->get();
    //     } else {
    //         return $all = SalaryDetails::orderBy('sdetails_id', 'DESC')->paginate($limit);
    //     }
    // }

    public function getEmployeesSalaryDetailsForListViewWithPagination($user_branch_office_id, $limit = 1)
    {
        if ($limit == null || $limit <= 0) {
            return SalaryDetails::select('emp_auto_id', 'employee_id', 'employee_name', 'hourly_employee', 'emp_type_id', 'sdetails_id', 'basic_amount', 'basic_hours', 'hourly_rent', 'house_rent', 'mobile_allowance', 'medical_allowance', 'local_travel_allowance', 'conveyance_allowance', 'food_allowance', 'cpf_contribution', 'increment_amount', 'saudi_tax', 'others1')
                ->leftjoin('employee_infos', 'salary_details.emp_id', '=', 'employee_infos.emp_auto_id')
                ->where('employee_infos.branch_office_id', $user_branch_office_id)
                ->orderBy('sdetails_id', 'DESC')->get();
        } else {
            return SalaryDetails::select('emp_auto_id', 'employee_id', 'employee_name', 'hourly_employee', 'emp_type_id', 'sdetails_id', 'basic_amount', 'basic_hours', 'hourly_rent', 'house_rent', 'mobile_allowance', 'medical_allowance', 'local_travel_allowance', 'conveyance_allowance', 'food_allowance', 'cpf_contribution', 'increment_amount', 'saudi_tax', 'others1')
                ->leftjoin('employee_infos', 'salary_details.emp_id', '=', 'employee_infos.emp_auto_id')
                ->where('employee_infos.branch_office_id', $user_branch_office_id)
                ->orderBy('sdetails_id', 'DESC')->paginate($limit);
        }
    }

    public function getAnEmployeeSalaryDetails($sdetails_id)
    {
        return  SalaryDetails::where('sdetails_id', $sdetails_id)
            ->leftjoin('employee_infos', 'salary_details.emp_id', '=', 'employee_infos.emp_auto_id')
            ->select('employee_infos.emp_type_id', 'salary_details.*')->first();
    }
    public function getAnEmployeeSalaryDetailsByEmployeeID($employee_id, $user_branch_office_id)
    {
        return  SalaryDetails::where('employee_infos.employee_id', $employee_id)
            ->where('employee_infos.branch_office_id', $user_branch_office_id)
            ->leftjoin('employee_infos', 'salary_details.emp_id', '=', 'employee_infos.emp_auto_id')
            ->orderBy('sdetails_id', 'DESC')->first();
    }
    public function getAnEmployeeSalaryDetailsByEmployeeIqamaNo($employee_id)
    {
        return  SalaryDetails::where('employee_infos.akama_no', $employee_id)
            ->leftjoin('employee_infos', 'salary_details.emp_id', '=', 'employee_infos.emp_auto_id')
            ->orderBy('sdetails_id', 'DESC')->first();
    }
    public function updateEmployeeSalaryAllInformation($request, $update_by_id)
    {
        return  $update =  SalaryDetails::where('emp_id', $request->emp_auto_id)->update([

            'basic_amount' => $request->basic_amount,
            'basic_hours' => $request->basic_hours,
            'hourly_rent' => $request->hourly_rent,
            'house_rent' => $request->house_rent  == null ? 0 : $request->house_rent,
            'mobile_allowance' => $request->mobile_allowance,
            'medical_allowance' => $request->medical_allowance == null ? 0 : $request->medical_allowance,
            'local_travel_allowance' => $request->local_travel_allowance == null ? 0 : $request->local_travel_allowance,
            'conveyance_allowance' => $request->conveyance_allowance == null ? 0 : $request->conveyance_allowance,
            'food_allowance' => $request->food_allowance,
            'cpf_contribution' => $request->contribution_amoun == null ? 0 : $request->contribution_amoun,
            'increment_amount' => $request->increment_amount == null ? 0 : $request->increment_amount,
            'saudi_tax' => $request->saudi_tax,
            'others1' => $request->others  == null ? 0 : $request->others,
            'payment_method' => $request->payment_method,
            'update_by' => $update_by_id,
            'updated_at' => Carbon::now(),

        ]);
    }
    public function updateEmployeeSalaryDetailsByEmployeAutoId($emp_auto_id, $basic_amount, $basic_hours, $houreRate, $house_rent, $mobile_allowance, $local_travel_allowance, $conveyance_allowance, $food_allowance, $others1)
    {

        return SalaryDetails::where('emp_id', $emp_auto_id)->update([

            'basic_amount' => $basic_amount,
            'basic_hours' => $basic_hours,
            'hourly_rent' => $houreRate,
            'house_rent' => $house_rent == null ? 0 : $house_rent,
            'mobile_allowance' => $mobile_allowance == null ? 0 : $mobile_allowance,
            'local_travel_allowance' => $local_travel_allowance == null ? 0 : $local_travel_allowance,
            'conveyance_allowance' => $conveyance_allowance == null ? 0 : $conveyance_allowance,
            'food_allowance' => $food_allowance == null ? 0 : $food_allowance,
            'others1' => $others1 == null ? 0 : $others1,
            'created_at' => Carbon::now(),
        ]);
    }

    public function updateEmployeeSalaryDetailsBySalaryDetailsAutoId($sdetails_id, $basic_amount, $basic_hours, $houreRate, $house_rent, $mobile_allowance, $local_travel_allowance, $conveyance_allowance, $food_allowance, $others1, $update_by_id)
    {

        return SalaryDetails::where('sdetails_id', $sdetails_id)->update([

            'basic_amount' => $basic_amount,
            'basic_hours' => $basic_hours,
            'hourly_rent' => $houreRate,
            'house_rent' => $house_rent == null ? 0 : $house_rent,
            'mobile_allowance' => $mobile_allowance == null ? 0 : $mobile_allowance,
            'local_travel_allowance' => $local_travel_allowance == null ? 0 : $local_travel_allowance,
            'conveyance_allowance' => $conveyance_allowance == null ? 0 : $conveyance_allowance,
            'food_allowance' => $food_allowance == null ? 0 : $food_allowance,
            'others1' => $others1 == null ? 0 : $others1,
            'update_by' => $update_by_id,
            'updated_at' => Carbon::now(),
        ]);
    }

    /* ================= Update Salary Details From New Employee Approval UI ================= */
    public function updateEmployeeSalaryDetailsBeforeJobApproval(
        $emp_auto_id,
        $basic_amount,
        $basic_hours,
        $houreRate,
        $food_allowance,
        $mobile_allowance,
        $medical_allowance,
        $saudi_tax,
        $update_by_id
    ) {
        return SalaryDetails::where('emp_id', $emp_auto_id)->update([
            'basic_amount' => $basic_amount,
            'basic_hours' => $basic_hours,
            'hourly_rent' => $houreRate,
            'food_allowance' => $food_allowance,
            'mobile_allowance' => $mobile_allowance,
            'medical_allowance' => $medical_allowance,
            'saudi_tax' => $saudi_tax,
            'update_by' => Auth::user()->id,
            'update_by' => $update_by_id,
            'updated_at' => Carbon::now(),
        ]);
    }



    public function updateEmployeeSalaryDetailsInformationAsPromotionByEmpAutoId(
        $emp_auto_id,
        $basic_amount,
        $houreRate,
        $house_rent,
        $mobile_allowance,
        $local_travel_allowance,
        $conveyance_allowance,
        $medical_allowance,
        $increment,
        $increment_amount,
        $others1,
        $food_allowance
    ) {

        return SalaryDetails::where('emp_id', $emp_auto_id)->update([
            'basic_amount' => $basic_amount,
            'hourly_rent' => $houreRate,
            'house_rent' => $house_rent,
            'mobile_allowance' => $mobile_allowance,
            'local_travel_allowance' => $local_travel_allowance,
            'conveyance_allowance' => $conveyance_allowance,
            'medical_allowance' => $medical_allowance,
            'increment_no' => $increment,
            'increment_amount' => $increment_amount,
            'others1' => $others1,
            'food_allowance' => $food_allowance,
            'created_at' => Carbon::now(),
        ]);
    }



    public function updateEmployeeSalaryDetailsContributionAndSaudiTax($emp_auto_id, $amount, $saudi_tax)
    {
        return SalaryDetails::where('emp_id', $emp_auto_id)->update([
            'cpf_contribution' => $amount,
            'saudi_tax' => $saudi_tax,
            'updated_at' => Carbon::now(),
        ]);
    }

    public function updateEmployeeDesignation($emp_auto_id, $designation_id)
    {
        return EmployeeInfo::where('emp_auto_id', $emp_auto_id)->update([
            'designation_id' => $designation_id,
            'updated_at' => Carbon::now(),
        ]);
    }

    public function updateEmployeeAdvaceInstallAmount($emp_auto_id, $amount, $purposeId)
    {

        if ($purposeId == 1) {
            $update = SalaryDetails::where('emp_id', $emp_auto_id)->update([
                'iqama_adv_inst_amount' => $amount,
                'updated_at' => Carbon::now(),
            ]);
        } else
            $update = SalaryDetails::where('emp_id', $emp_auto_id)->update([
                'other_adv_inst_amount' => $amount,
                'updated_at' => Carbon::now(),
            ]);
        return $update;
    }

    // Update Both Iqama and Other Advance Deduction Amount
    public function updateAnEmployeeIqamaAndOtherAdvaceInstallAmount($emp_auto_id, $iqama_amount, $other_amount)
    {

        return  DB::table('salary_details') // Ensure 'salary_details' is your actual table name
            ->where('emp_id', $emp_auto_id)
            ->update([
                'iqama_adv_inst_amount' => $iqama_amount,
                'other_adv_inst_amount' => $other_amount,
                'updated_at' => Carbon::now(),
            ]);
    }

    public function updateEmployeeIqamaAdvaceInstallAmount($emp_auto_id, $amount)
    {
        return SalaryDetails::where('emp_id', $emp_auto_id)->update([
            'iqama_adv_inst_amount' => $amount,
            'updated_at' => Carbon::now()
        ]);
    }

    public function updateEmployeeOtherAdvaceInstallAmount($emp_auto_id, $amount)
    {
        return SalaryDetails::where('emp_id', (int) $emp_auto_id)->update([
            'other_adv_inst_amount' => $amount,
            'updated_at' => Carbon::now(),
        ]);
    }

    public function updateMultipleEmployeesFoodAmountByMultipleEmpID($emp_auto_id_list, $amount)
    {
        return SalaryDetails::whereIn('emp_id', $emp_auto_id_list)
            ->update([
                'food_allowance' => $amount,
                'updated_at' => Carbon::now(),
            ]);
    }

    public function updateAnEmployeeSalaryPaymentMethodByEmployeeAutoId($emp_auto_id, $payment_method)
    {

        return SalaryDetails::where('emp_id', $emp_auto_id)->update([
            'payment_method' => $payment_method,
            'updated_at' => Carbon::now(),
        ]);
    }



    public function addEmployeeSalaryDetails(
        $emp_auto_id,
        $basic_amount,
        $basic_hours,
        $hourly_rate,
        $house_rent,
        $mobile_allowance,
        $medical_allowance,
        $local_travel_allowance,
        $conveyance_allowance,
        $food_allowance,
        $others1
    ) {
        return SalaryDetails::insert([
            'emp_id' => $emp_auto_id,
            'basic_amount' => $basic_amount,
            'basic_hours' => $basic_hours,
            'hourly_rent' => $hourly_rate,
            'house_rent' => $house_rent,
            'mobile_allowance' => $mobile_allowance,
            'medical_allowance' => $medical_allowance,
            'local_travel_allowance' => $local_travel_allowance,
            'conveyance_allowance' => $conveyance_allowance,
            'food_allowance' => $food_allowance,
            'others1' => $others1,
            'created_at' => Carbon::now(),
        ]);
    }


    public function deleteAnEmployeeSalaryDetails($details_id)
    {
        return SalaryDetails::where('sdetails_id', $details_id)->delete();
    }

    // this is salary update history record
    public function insertAnEmployeeSalaryUpdateHistory(
        $emp_auto_id,
        $basic_amount,
        $basic_hours,
        $house_allowance,
        $hourly_rate,
        $mobile_allowance,
        $medical_allowance,
        $travel_allowance,
        $conveyance_allowance,
        $food_allowance,
        $cpf_contribution,
        $increment_amount,
        $saudi_tax,
        $others1,
        $hourly_employee,
        $payment_method,
        $inserted_by
    ) {
        $now = Carbon::now();

        return DB::table('salary_details_hisotries')->insert([
            'emp_auto_id'            => $emp_auto_id, // Added this since it's a new row
            'basic_amount'           => $basic_amount,
            'basic_hours'            => $basic_hours,
            'hourly_rate'            => $hourly_rate,
            'house_allowance'        => $house_allowance ?? 0,
            'mobile_allowance'       => $mobile_allowance,
            'medical_allowance'      => $medical_allowance ?? 0,
            'travel_allowance'       => $travel_allowance ?? 0,
            'conveyance_allowance'   => $conveyance_allowance ?? 0,
            'food_allowance'         => $food_allowance,
            'cpf_contribution'       => $cpf_contribution ?? 0,
            'increment_amount'       => $increment_amount ?? 0,
            'saudi_tax'              => $saudi_tax,
            'others1'                => $others1 ?? 0,
            'hourly_employee'         => $hourly_employee,
            'payment_method'         => $payment_method,
            'inserted_by'              => $inserted_by, // You might want to rename this to 'created_by' if the column exists
            'created_at'             => $now,
            'updated_at'             => $now,
        ]);
    }
}
