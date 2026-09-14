<?php


namespace App\Http\Controllers\DataServices;

use Carbon\Carbon;
use App\Models\EmployeeInfo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\EmployeeMultiProjectWorkHistory;


class CostControlReportDataService
{


    public function getAProjectSingleMonthSubcontorAsSponsorBaseMultiProjectWorkActualCostrollRecport($project_id, $sponsor_ids, $month, $salaryYear)
    {

            return EmployeeMultiProjectWorkHistory::select(
               // 'employee_infos.sponsor_id',
                'sponsors.spons_name',
                 DB::raw("SUM(total_day) as total_day"),
                DB::raw("COUNT(DISTINCT(emp_id)) as total_emp"),
                DB::raw("SUM(total_hour) as total_hour"),
                DB::raw("SUM(total_overtime) as total_overtime"),
                DB::raw("SUM(ot_amount) as total_ot_amount"),
                DB::raw("SUM(total_amount) as total_salary"),
                DB::raw("SUM(food_amount) as food_amount"),
                DB::raw("SUM(other_amount) as other_amount")
             )
            ->leftjoin('employee_infos', 'emp_multi_proj_work_hist.emp_id' , '=', 'employee_infos.emp_auto_id')
            ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
            ->where('emp_multi_proj_work_hist.month', $month)->where('emp_multi_proj_work_hist.year', $salaryYear)
            ->where('emp_multi_proj_work_hist.project_id', $project_id)
            ->whereIn('employee_infos.sponsor_id', $sponsor_ids)
           // ->groupBy( "employee_infos.sponsor_id")
            ->groupBy("sponsors.spons_name")
            ->get();


    }

        public function getOnlyThisProjectTotalSalaryAmountForMultipleProjectWorkByProjectMonthAndYear($projectId, $month, $salaryYear)
    {

            return $total_records = EmployeeMultiProjectWorkHistory::select(
                DB::raw("COUNT(DISTINCT(emp_id)) as total_emp"),
                DB::raw("SUM(total_amount) as total_salary"),
                DB::raw("SUM(total_hour) as total_hour"),
                DB::raw("SUM(total_overtime) as total_overtime")
            )->where('month', $month)->where('year', $salaryYear)
                ->where('project_id', $projectId)->get();

    }
    
            // Project-Wise Salary Allocation Report Designation Head  Salary summary for cost Controll
    public function getMultipleProjectSalarySummaryForCostControllReportBySponsorTypeAndEmployeeType($project_id_list, $sponsor_id_array,$catg_id, $month, $salaryYear)
    {


            return EmployeeMultiProjectWorkHistory::select(
                'employee_infos.hourly_employee',
                DB::raw("COUNT(emp_id) as total_emp"),
                DB::raw("SUM(total_amount) as total_slh_all_include_amount"),
                DB::raw("SUM(food_amount) as food_allowance"),
                DB::raw("SUM(total_hour+total_overtime) as total_hours")

            )
            ->leftjoin('employee_infos', 'emp_multi_proj_work_hist.emp_id' , '=', 'employee_infos.emp_auto_id')
            ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
            ->groupBy("employee_infos.hourly_employee")
            ->where('emp_multi_proj_work_hist.month', $month)->where('emp_multi_proj_work_hist.year', $salaryYear)
            ->whereIn('emp_multi_proj_work_hist.project_id', $project_id_list)
            ->whereIn('employee_infos.sponsor_id', $sponsor_id_array)
            ->where('employee_categories.catg_id', $catg_id)
            ->get();


    }
    
    
    // Designation Head(Engineer,HSE, Mason etc) Salary summary Report (Cost Control)
    public function getMultipleProjectSalarySummaryCostControllReportByDesignationHead($project_id_list, $sponsor_id_array,$desig_head_id, $month, $salaryYear)
    {
            return EmployeeMultiProjectWorkHistory::select(
                'employee_infos.hourly_employee',
                DB::raw("COUNT(emp_multi_proj_work_hist.emp_id) as total_emp"),
                DB::raw("SUM(total_amount) as total_slh_all_include_amount"),
                DB::raw("SUM(emp_multi_proj_work_hist.food_amount) as food_allowance"),
                DB::raw("SUM(total_hour) as total_hours")
            )
            ->leftjoin('employee_infos', 'emp_multi_proj_work_hist.emp_id' , '=', 'employee_infos.emp_auto_id')
            ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
            ->groupBy("employee_infos.hourly_employee")
            ->where('emp_multi_proj_work_hist.month', $month)->where('emp_multi_proj_work_hist.year', $salaryYear)
            ->whereIn('emp_multi_proj_work_hist.project_id', $project_id_list)
            ->whereIn('employee_infos.sponsor_id', $sponsor_id_array)
            ->where('employee_categories.dh_auto_id', $desig_head_id)
            ->get();

    }
    
    
    
    
    
    
    
    
    
    
    
}
