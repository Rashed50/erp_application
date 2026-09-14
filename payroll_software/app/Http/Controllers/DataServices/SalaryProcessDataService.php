<?php

namespace App\Http\Controllers\DataServices;

use App\Models\EmployeeInfo;
use App\Models\EmployeeFiscalYearDuration;
use App\Models\SalaryHistory;
use App\Models\EmployeeMultiProjectWorkHistory;
use App\Models\EmployeeBonus;
use App\Models\EmpMobileBill;
use App\Models\SalarySheetUpload;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SalaryProcessDataService
{



    /*
     ==========================================================================
     =============== Salary Processing Hisotry ================================
     ==========================================================================
    */

    public function insertSalaryProcessHistory($project_id,$month_id,$year,$process_type,$process_by ){

            DB::table('salary_process_history')->updateOrInsert(
            // Condition array - checks if record exists with ALL these values
            [
                'project_id' => $project_id,
                'month_id' => $month_id,
                'year' => $year,
                'process_type' => $process_type,
            ],
            // Data to insert or update
            [
                'process_by' => $process_by,
                'process_date' => now(),
            ]
        );
        return true;


        // DB::table('salary_process_history')->insert([
        //     'project_id' => $project_id,
        //     'month_id' => $month_id,
        //     'year' => $year,
        //     'process_type'=>$process_type,
        //     'process_by' => $process_by,
        //     'process_date' => now(),

        // ]);


    }

    public function checkSalaryProcessHisotryAlreadyDone($project_id,$month_id,$year,$process_type){
        $data = [
            'project_id'   => $project_id,
            'month_id'     => $month_id,
            'year'         => $year,
            'process_type' => $process_type,
        ];

        $exists = DB::table('salary_process_history')
                    ->where($data) // Checks all 4 columns
                    ->exists();

        if (!$exists) {
           return false;
        } else {
            return true;
        }
    }

    public function getSalaryProcessingHistoryReport($month,$year){

        $projects =  DB::table('project_infos')->select('proj_id','proj_name')
                            ->where('working_status', 1)
                            ->where('status',1)->orderBy('proj_name', 'ASC')
                            ->get();
        foreach($projects as $ap){

            $ap->asloob_process = DB::table('salary_process_history')
                        ->where('month_id',$month)
                        ->where('year',$year)
                        ->where('project_id',$ap->proj_id)
                        ->where('process_type',1)
                        ->exists();
            $ap->other_process = DB::table('salary_process_history')
                        ->where('month_id',$month)
                        ->where('year',$year)
                        ->where('project_id',$ap->proj_id)
                        ->where('process_type',2)
                        ->exists();
        }
        return $projects;



        // return   DB::table('salary_process_history')
        //             ->select('project_infos.proj_name','users.name as process_by','salary_process_history.*')
        //             ->leftjoin('project_infos', 'salary_process_history.project_id', '=', 'project_infos.proj_id')
        //             ->leftjoin('users', 'salary_process_history.process_by', '=', 'users.id')
        //             ->where('month_id',$month)
        //             ->where('year',$year)
        //              ->orderBy('project_infos.proj_name', 'ASC')
        //              ->get();
    }

     /*
     ==========================================================================
     =============== Employee Fiscal Year Process Related Methods =============
     ==========================================================================
    */

     public function getAdvanceAndIqamaRenewalDeductionTotalAmountForAdvanceProcess($emp_auto_id,$start_date,$end_date){
          return SalaryHistory::
            where('emp_auto_id', $emp_auto_id)
            ->whereBetween('slh_salary_date', [$start_date,$end_date])
            ->Select(
            DB::raw('SUM(slh_iqama_advance) as iqama_deduction'),
            DB::raw('SUM(slh_other_advance) as other_deduction')
           )
           ->first();
     }

    public function getAnEmployeeSalaryRecordsByFiscalYear($emp_auto_id,$start_date,$end_date)
    {

        return  SalaryHistory::where('emp_auto_id', $emp_auto_id)
                    ->whereBetween('slh_salary_date', [$start_date,$end_date])
                    ->orderBy('slh_year', 'ASC')
                    ->orderBy('slh_month', 'ASC')->get();

    }


    public function getAnEmployeeUnpaidSalaryTotalAmountByFiscalYear($emp_auto_id,$start_date,$end_date)
    {
        return SalaryHistory::where('emp_auto_id', $emp_auto_id)
                ->whereBetween('slh_salary_date', [$start_date,$end_date])
                ->where('Status', 0)
                ->sum('slh_total_salary');
    }

    public function getAnEmployeeTotalAmountOfIqamaExpenseDeductionFromSalaryByFiscalYear($emp_auto_id,$start_date,$end_date)
    {

        return  SalaryHistory::where('emp_auto_id', $emp_auto_id)
        ->whereBetween('slh_salary_date', [$start_date,$end_date])
        ->sum('slh_iqama_advance');

    }
    public function getAnEmployeeTotalAmountOfOtherAdvanceDeductionFromSalaryByFiscalYear($emp_auto_id,$start_date,$end_date)
    {

        return  SalaryHistory::where('emp_auto_id', $emp_auto_id)
        ->whereBetween('slh_salary_date', [$start_date,$end_date])
        ->sum('slh_other_advance');

    }

    public function getAnEmployeeTotalAmountContributeToCPFByFiscalYear($emp_auto_id,$start_date,$end_date)
    {

        return  SalaryHistory::where('emp_auto_id', $emp_auto_id)
        ->whereBetween('slh_salary_date', [$start_date,$end_date])
        ->sum('slh_cpf_contribution');

    }

    public function getAnEmployeeTotalAmountSaudiTaxDeductionFromSalaryByFiscalYear($emp_auto_id,$start_date,$end_date)
    {

        return  SalaryHistory::where('emp_auto_id', $emp_auto_id)
        ->whereBetween('slh_salary_date', [$start_date,$end_date])
        ->sum('slh_saudi_tax');

    }



    /*
     ==========================================================================
     ============= Employee monthly Salary history Single Record ==============
     ==========================================================================
    */

    public function checkAnEmployeeSalaryIsAlreadyPaid($emp_auto_id, $month, $year)
    {
        return SalaryHistory::where('emp_auto_id', $emp_auto_id)->where('slh_month', $month)->where('slh_year', $year)
                              ->where('Status', 1)->count() == 1 ? true : false;
    }

    public function getAnEmployeeSalaryRecordBySalaryHistoryAutoId($slh_auto_id)
    {

        return   SalaryHistory::select('employee_infos.employee_id','employee_infos.emp_auto_id','employee_infos.employee_name','employee_infos.akama_no','employee_infos.salary_status','salary_histories.basic_amount','salary_histories.hourly_rent','sponsors.spons_name','countries.country_name','employee_categories.catg_name','project_infos.proj_name',
        'salary_histories.multProject','salary_histories.slh_auto_id','salary_histories.slh_total_hours','salary_histories.slh_total_working_days','salary_histories.slh_total_overtime','salary_histories.slh_overtime_amount','salary_histories.food_allowance','salary_histories.slh_total_salary','salary_histories.slh_cpf_contribution',
        'salary_histories.slh_iqama_advance','salary_histories.slh_all_include_amount','salary_histories.mobile_allowance','salary_histories.medical_allowance','salary_histories.slh_saudi_tax','salary_histories.slh_other_advance','salary_histories.slh_food_deduction','salary_histories.Status','salary_histories.slh_month','salary_histories.slh_year')
        ->where("salary_histories.slh_auto_id", $slh_auto_id)
        ->leftjoin('employee_infos', 'salary_histories.emp_auto_id', '=', 'employee_infos.emp_auto_id')
        ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
        ->leftjoin('project_infos', 'salary_histories.project_id', '=', 'project_infos.proj_id')
        ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
        ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
        ->first();

    }

    public function getAnEmployeeSalaryRecordBySalaryHistoryAutoIdForEditing($slh_auto_id)
    {

        return   SalaryHistory::select('employee_infos.employee_id','employee_infos.emp_auto_id','employee_infos.employee_name','employee_infos.akama_no','employee_infos.salary_status','salary_histories.basic_amount','salary_histories.hourly_rent','sponsors.spons_name','countries.country_name','employee_categories.catg_name','project_infos.proj_name',
        'salary_histories.*' )
        ->where("salary_histories.slh_auto_id", $slh_auto_id)
        ->leftjoin('employee_infos', 'salary_histories.emp_auto_id', '=', 'employee_infos.emp_auto_id')
        ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
        ->leftjoin('project_infos', 'salary_histories.project_id', '=', 'project_infos.proj_id')
        ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
        ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
        ->first();

    }


    public function getAnEmployeeBankInfoWithSalaryRecordForUpdateSalaryPaidMethodBySalaryHistoryAutoId($slh_auto_id)
    {
        return   SalaryHistory:: where("salary_histories.slh_auto_id", $slh_auto_id)
            ->leftjoin('employee_infos', 'salary_histories.emp_auto_id', '=', 'employee_infos.emp_auto_id')
            ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
            ->leftjoin('employee_bank_details', 'employee_infos.emp_auto_id', '=', 'employee_bank_details.emp_auto_id')
            ->first();
    }

    public function getAnEmployeeSalaryRecord($emp_auto_id, $month, $year)
    {
        return SalaryHistory::where('emp_auto_id', $emp_auto_id)->where('slh_month', $month)->where('slh_year', $year)->first();
    }
    public function getAnEmployeeInfoWithSalaryHistory($emp_auto_id, $month, $year)
    {
        return SalaryHistory::with('employee')->where('emp_auto_id', $emp_auto_id)->where('slh_month', $month)->where('slh_year', $year)->first();
    }

        // for sallary report
     public function getSingleEmployeeSalaryForSalarySheetPrint($emp_auto_id, $month, $year)
    {

             return  EmployeeInfo::select('employee_infos.*','sponsors.spons_name','employee_categories.catg_name','countries.country_name','monthly_work_histories.paid_leave','job_statuses.title','salary_histories.*')
                    ->where('employee_infos.emp_auto_id', $emp_auto_id)
                    ->where('salary_histories.slh_month', $month)
                    ->where('salary_histories.slh_year', $year)
                    ->where('monthly_work_histories.month_id', $month)->where('monthly_work_histories.year_id', $year)
                    ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                    ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                    ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                    ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                    ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
                    ->leftjoin('monthly_work_histories', 'salary_histories.emp_auto_id', '=', 'monthly_work_histories.emp_id')
                    ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
                    ->orderBy('employee_id')
                    ->first();
    }


    public function getAnEmployeeLastMonthSalaryRecord($emp_auto_id)
    {

        return  EmployeeInfo::
             select('employee_infos.employee_id','employee_infos.emp_auto_id','employee_infos.employee_name','employee_infos.akama_no','employee_infos.salary_status','salary_histories.basic_amount','salary_histories.hourly_rent','sponsors.spons_name','countries.country_name','employee_categories.catg_name','project_infos.proj_name',
             'salary_histories.multProject','salary_histories.slh_total_hours','salary_histories.slh_total_working_days','salary_histories.slh_all_include_amount','salary_histories.slh_total_overtime','salary_histories.slh_overtime_amount','salary_histories.food_allowance','salary_histories.slh_total_salary','salary_histories.slh_cpf_contribution',
             'salary_histories.slh_iqama_advance','salary_histories.slh_saudi_tax','salary_histories.slh_other_advance','salary_histories.slh_food_deduction','salary_histories.house_rent','salary_histories.mobile_allowance','salary_histories.medical_allowance',
            'salary_histories.local_travel_allowance','salary_histories.conveyance_allowance','salary_histories.others','salary_histories.slh_bonus_amount','salary_histories.Status','salary_histories.slh_month','salary_histories.slh_year')

        ->where("salary_histories.emp_auto_id", $emp_auto_id)
       ->leftjoin('salary_histories', 'salary_histories.emp_auto_id', '=', 'employee_infos.emp_auto_id')
       ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
       ->leftjoin('project_infos', 'salary_histories.project_id', '=', 'project_infos.proj_id')
       ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
       ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
       ->orderBy('salary_histories.slh_auto_id', 'DESC')
       ->first();


    }

    public function deleteAnEmployeeUnpaidSalaryRecordBySalaryHistoryAutoId($slh_auto_id){
        return SalaryHistory::where('slh_auto_id',$slh_auto_id)->where('Status',0)->delete();
    }


    /*
     ==========================================================================
     ====================== Employee Salary Multiple Record ===================
     ==========================================================================
    */

    public function getAnEmployeeSalaryHistorySummary($employeeId, $year)
    {
        return  SalaryHistory::where('emp_auto_id', $employeeId)->where('slh_year', $year)->orderBy('slh_month', 'ASC')->get();
    }



    public function getAnEmployeeSalaryRecordsByPaidUnpaidStatus($emp_auto_id, $year,$salary_status)
    {

        if(!is_null($year) &&  !is_null($salary_status)){
            return  SalaryHistory::where('emp_auto_id', $emp_auto_id)->where('slh_year', $year)->where('Status', $salary_status)->orderBy('slh_month', 'ASC')->get();
        }else if(!is_null($year) && is_null($salary_status)){
            return  SalaryHistory::where('emp_auto_id', $emp_auto_id)->where('slh_year', $year)->orderBy('slh_month', 'ASC')->get();
        }
        else if(is_null($year) && !is_null($salary_status)){
            return  SalaryHistory::where('emp_auto_id', $emp_auto_id)->where('Status', $salary_status)->orderBy('slh_year','ASC')->orderBy('slh_month', 'ASC')->get();
        }
        else {
            return  SalaryHistory::where('emp_auto_id', $emp_auto_id)->orderBy('slh_year','ASC')->orderBy('slh_month', 'ASC')->get();
           }

    }

        // Employee Details Information With Monthly Working Record
    public function getEmployeeDetailsWithMonthlyWorkRecord($month, $year, $empId, $project_id)
    {
        if ($empId == 0 || $empId == null || $empId == '') {

            return  $allEmpSalary =  EmployeeInfo::
                 where('monthly_work_histories.month_id', $month)
                ->where('monthly_work_histories.year_id', $year)
                ->where('monthly_work_histories.work_project_id', $project_id)
                ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->leftjoin('monthly_work_histories', 'employee_infos.emp_auto_id', '=', 'monthly_work_histories.emp_id')
                ->get();//->all();
        } else {
            return  $allEmpSalary =  EmployeeInfo::
                 where("employee_infos.employee_id", $empId)
                ->where('monthly_work_histories.month_id', $month)
                ->where('monthly_work_histories.year_id', $year)
               ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->leftjoin('monthly_work_histories', 'employee_infos.emp_auto_id', '=', 'monthly_work_histories.emp_id')
                ->first();
        }
    }

     // Employee Details Information With Monthly Working Record for Salary Processing
    //  public function getAnEmployeeDetailsWithMonthlyWorkRecordForSalaryProcessing($employee_id,$user_branch_office_id,$month, $year)
    //  {
    //          return    EmployeeInfo::  where("employee_infos.employee_id", $employee_id)
    //              ->where("employee_infos.branch_office_id", $user_branch_office_id)
    //              ->where('monthly_work_histories.month_id', $month)
    //              ->where('monthly_work_histories.year_id', $year)
    //              ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
    //              ->leftjoin('monthly_work_histories', 'employee_infos.emp_auto_id', '=', 'monthly_work_histories.emp_id')
    //              ->first();

    //  }

     // list of employees  Employee Details Information With Monthly Working Record by multiple employee ids for salary processing
     public function getListOfmployeeDetailsWithMonthlyWorkRecordByMultipleEmpIDsForSalaryProcessing($employee_ids,$month, $year,$user_branch_office_id)
     {
             return    EmployeeInfo::  whereIn("employee_infos.employee_id", $employee_ids)
                 ->where("employee_infos.branch_office_id", $user_branch_office_id)
                 ->where('monthly_work_histories.month_id', $month)
                 ->where('monthly_work_histories.year_id', $year)
                 ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                 ->leftjoin('monthly_work_histories', 'employee_infos.emp_auto_id', '=', 'monthly_work_histories.emp_id')
                 ->get();

     }

    // list of employees those salary paid by cash for salary processing
    public function getListOfEmployeesWithWorkRecordsForProcessingSalary($month, $year, $project_ids,$sponsor_ids,$user_branch_office_id)
     {
             return EmployeeInfo:: where('monthly_work_histories.month_id', $month)
                 ->where("employee_infos.branch_office_id", $user_branch_office_id)
                 ->where('monthly_work_histories.year_id', $year)
                 ->whereIn('monthly_work_histories.work_project_id', $project_ids)
                 ->whereIn('employee_infos.sponsor_id',$sponsor_ids)
                 ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                 ->leftjoin('monthly_work_histories', 'employee_infos.emp_auto_id', '=', 'monthly_work_histories.emp_id')
                // ->where('salary_details.payment_method', 'Cash')  // only cash paid emp salary process
                 ->get();

     }
    // list of employees those salary paid by Bank for salary processing
    public function getListOfEmployeesWithWorkRecordsThoseSalaryPaytoBankForProcessingSalary($month, $year,$sponsor_ids,$user_branch_office_id)
     {
             return EmployeeInfo:: where('monthly_work_histories.month_id', $month)
                 ->where("employee_infos.branch_office_id", $user_branch_office_id)
                 ->where('monthly_work_histories.year_id', $year)
                // ->whereIn('employee_infos.sponsor_id',$sponsor_ids)
                 ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                 ->leftjoin('monthly_work_histories', 'employee_infos.emp_auto_id', '=', 'monthly_work_histories.emp_id')
                 ->where('salary_details.payment_method', 'Bank')
                 ->get();

     }




    public function searchAnEmployeesInfoWithInAFiscalYearPaidSalaryRecordsForListView($employee_id, $start_date, $end_date, $branch_office_id, $salary_month,$salary_year)
     {
             return SalaryHistory::select('employee_infos.employee_id','employee_infos.employee_name','employee_infos.akama_no','employee_infos.hourly_employee','sponsors.spons_name','employee_categories.catg_name',
             'project_infos.proj_name','salary_histories.slh_auto_id','salary_histories.slh_total_salary','salary_histories.slh_month','salary_histories.slh_year','salary_histories.slh_salary_date','salary_histories.updated_at','salary_histories.slh_paid_method',
             'users.name as paid_by_name')
                 ->where('salary_histories.Status', 1) // salary status paid
                 ->where('salary_histories.branch_office_id', $branch_office_id)
                 ->leftjoin('employee_infos', 'salary_histories.emp_auto_id', '=', 'employee_infos.emp_auto_id')
                 ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                 ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                 ->leftjoin('project_infos', 'salary_histories.project_id', '=', 'project_infos.proj_id')
                 ->leftjoin('users', 'salary_histories.updated_by', '=', 'users.id')
                 ->whereBetween('slh_salary_date', [$start_date, $end_date])
                 ->where('employee_infos.employee_id',$employee_id)
                 ->where('salary_histories.slh_month', $salary_month)
                 ->where('salary_histories.slh_year', $salary_year)
                 ->orderBy('slh_auto_id', 'DESC')
                 ->get();
     }

    public function searchAnEmployeeInAFiscalYearPaidSalaryAllRecordsForListView($employee_id, $start_date, $end_date, $branch_office_id)
     {
             return SalaryHistory::select('employee_infos.employee_id','employee_infos.employee_name','employee_infos.akama_no','employee_infos.hourly_employee','sponsors.spons_name','employee_categories.catg_name',
             'project_infos.proj_name','salary_histories.slh_auto_id','salary_histories.slh_total_salary','salary_histories.slh_month','salary_histories.slh_year','salary_histories.slh_salary_date','salary_histories.updated_at','salary_histories.slh_paid_method',
             'users.name as paid_by_name')
                 ->where('salary_histories.Status', 1) // salary status paid
                 ->where('salary_histories.branch_office_id', $branch_office_id)
                 ->leftjoin('employee_infos', 'salary_histories.emp_auto_id', '=', 'employee_infos.emp_auto_id')
                 ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                 ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                 ->leftjoin('project_infos', 'salary_histories.project_id', '=', 'project_infos.proj_id')
                 ->leftjoin('users', 'salary_histories.updated_by', '=', 'users.id')
                 ->whereBetween('slh_salary_date', [$start_date, $end_date])
                 ->where('employee_infos.employee_id',$employee_id)
                 ->orderBy('slh_auto_id', 'DESC')
                 ->get();
     }

      // Salary Pending Or Paid Emp list for Paid Unpaid


    // public function searchListOfEmployeesThoseSalaryAlreadyPaidForListViewByProjectAndSponsor_old($SponsId, $proj_id, $fromMonth, $toMonth, $fromYear,$toYear,$branch_office_id)
    // {
    //     if ($SponsId != 0 && $proj_id != 0) {

    //         return SalaryHistory::with('employee.category', 'month', 'employee.sponsor',  'employee.type', 'employee.project')
    //             ->where('salary_histories.Status', 1) // salary status paid
    //             ->where('salary_histories.branch_office_id', $branch_office_id)
    //             ->where('sponsor_id', $SponsId)
    //             ->where('salary_histories.project_id', $proj_id)
    //             ->leftjoin('employee_infos', 'salary_histories.emp_auto_id', '=', 'employee_infos.emp_auto_id')
    //             ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
    //             ->leftjoin('project_infos', 'salary_histories.project_id', '=', 'project_infos.proj_id')
    //             ->whereIn('slh_month', [$fromMonth, $toMonth])
    //             ->whereIn('slh_year', [$fromYear, $toYear])
    //             ->orderBy('employee_id', 'ASC')
    //             ->get();
    //     }
    //     else  if ($SponsId != 0 && $proj_id == 0) {
    //         return  SalaryHistory::with('employee.category', 'month', 'employee.sponsor',  'employee.type', 'employee.project')
    //         ->where('salary_histories.Status', 1) // salary status paid
    //         ->where('salary_histories.branch_office_id', $branch_office_id)
    //         ->where('sponsor_id', $SponsId)
    //         ->leftjoin('employee_infos', 'salary_histories.emp_auto_id', '=', 'employee_infos.emp_auto_id')
    //         ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
    //         ->leftjoin('project_infos', 'salary_histories.project_id', '=', 'project_infos.proj_id')
    //         ->whereIn('slh_month', [$fromMonth, $toMonth])
    //         ->whereIn('slh_year', [$fromYear, $toYear])
    //         ->orderBy('employee_id', 'ASC')
    //         ->get();
    //     }
    //     else  if ($SponsId == 0 && $proj_id != 0) {
    //         return  SalaryHistory::with('employee.category', 'month', 'employee.sponsor',  'employee.type', 'employee.project')
    //             ->where('salary_histories.Status', 1) // salary status paid
    //             ->where('salary_histories.branch_office_id', $branch_office_id)
    //             ->where('salary_histories.project_id', $proj_id)
    //             ->leftjoin('employee_infos', 'salary_histories.emp_auto_id', '=', 'employee_infos.emp_auto_id')
    //             ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
    //             ->leftjoin('project_infos', 'salary_histories.project_id', '=', 'project_infos.proj_id')
    //             ->whereIn('slh_month', [$fromMonth, $toMonth])
    //             ->whereIn('slh_year', [$fromYear, $toYear])
    //             ->orderBy('employee_id', 'ASC')
    //             ->get();
    //     }
    //     else {
    //         return SalaryHistory::with('employee.category', 'month', 'employee.sponsor',  'employee.type', 'employee.project')
    //             ->where('salary_histories.Status', 1) // salary status paid
    //             ->where('salary_histories.branch_office_id', $branch_office_id)
    //             ->leftjoin('employee_infos', 'salary_histories.emp_auto_id', '=', 'employee_infos.emp_auto_id')
    //             ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
    //             ->leftjoin('project_infos', 'salary_histories.project_id', '=', 'project_infos.proj_id')
    //             ->whereIn('slh_month', [$fromMonth, $toMonth])
    //             ->whereIn('slh_year', [$fromYear, $toYear])
    //             ->orderBy('employee_id', 'ASC')
    //             ->get();
    //     }
    // }

    public function searchListOfEmployeesThoseSalaryAlreadyPaidForListViewByProjectAndSponsor($SponsId, $proj_id, $fromMonth, $toMonth, $fromYear,$toYear,$branch_office_id)
    {
        if ($SponsId != 0 && $proj_id != 0) {


             return SalaryHistory::select('employee_infos.employee_id','employee_infos.employee_name','employee_infos.akama_no','employee_infos.hourly_employee','sponsors.spons_name','employee_categories.catg_name',
             'project_infos.proj_name','salary_histories.slh_auto_id','salary_histories.slh_total_salary','salary_histories.slh_month','salary_histories.slh_year','salary_histories.slh_salary_date','salary_histories.updated_at','salary_histories.slh_paid_method',
             'users.name as paid_by_name')
                 ->where('salary_histories.Status', 1) // salary status paid
                 ->where('salary_histories.branch_office_id', $branch_office_id)
                 ->where('sponsor_id', $SponsId)
                 ->where('salary_histories.project_id', $proj_id)
                 ->leftjoin('employee_infos', 'salary_histories.emp_auto_id', '=', 'employee_infos.emp_auto_id')
                 ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                 ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                 ->leftjoin('project_infos', 'salary_histories.project_id', '=', 'project_infos.proj_id')
                 ->leftjoin('users', 'salary_histories.updated_by', '=', 'users.id')
                 ->whereIn('slh_month', [$fromMonth, $toMonth])
                ->whereIn('slh_year', [$fromYear, $toYear])
                ->orderBy('employee_id', 'ASC')
                ->get();

        }
        else  if ($SponsId != 0 && $proj_id == 0) {


             return SalaryHistory::select('employee_infos.employee_id','employee_infos.employee_name','employee_infos.akama_no','employee_infos.hourly_employee','sponsors.spons_name','employee_categories.catg_name',
             'project_infos.proj_name','salary_histories.slh_auto_id','salary_histories.slh_total_salary','salary_histories.slh_month','salary_histories.slh_year','salary_histories.slh_salary_date','salary_histories.updated_at','salary_histories.slh_paid_method',
             'users.name as paid_by_name')
                 ->where('salary_histories.Status', 1) // salary status paid
                 ->where('salary_histories.branch_office_id', $branch_office_id)
                 ->where('sponsor_id', $SponsId)
                 ->leftjoin('employee_infos', 'salary_histories.emp_auto_id', '=', 'employee_infos.emp_auto_id')
                 ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                 ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                 ->leftjoin('project_infos', 'salary_histories.project_id', '=', 'project_infos.proj_id')
                 ->leftjoin('users', 'salary_histories.updated_by', '=', 'users.id')
                 ->whereIn('slh_month', [$fromMonth, $toMonth])
                ->whereIn('slh_year', [$fromYear, $toYear])
                ->orderBy('employee_id', 'ASC')
                ->get();
        }
        else  if ($SponsId == 0 && $proj_id != 0) {


             return SalaryHistory::select('employee_infos.employee_id','employee_infos.employee_name','employee_infos.akama_no','employee_infos.hourly_employee','sponsors.spons_name','employee_categories.catg_name',
             'project_infos.proj_name','salary_histories.slh_auto_id','salary_histories.slh_total_salary','salary_histories.slh_month','salary_histories.slh_year','salary_histories.slh_salary_date','salary_histories.updated_at','salary_histories.slh_paid_method',
             'users.name as paid_by_name')
                 ->where('salary_histories.Status', 1) // salary status paid
                 ->where('salary_histories.branch_office_id', $branch_office_id)
                  ->where('salary_histories.project_id', $proj_id)
                 ->leftjoin('employee_infos', 'salary_histories.emp_auto_id', '=', 'employee_infos.emp_auto_id')
                 ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                 ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                 ->leftjoin('project_infos', 'salary_histories.project_id', '=', 'project_infos.proj_id')
                 ->leftjoin('users', 'salary_histories.updated_by', '=', 'users.id')
                 ->whereIn('slh_month', [$fromMonth, $toMonth])
                ->whereIn('slh_year', [$fromYear, $toYear])
                ->orderBy('employee_id', 'ASC')
                ->get();

        }
        else {


             return SalaryHistory::select('employee_infos.employee_id','employee_infos.employee_name','employee_infos.akama_no','employee_infos.hourly_employee','sponsors.spons_name','employee_categories.catg_name',
             'project_infos.proj_name','salary_histories.slh_auto_id','salary_histories.slh_total_salary','salary_histories.slh_month','salary_histories.slh_year','salary_histories.slh_salary_date','salary_histories.updated_at','salary_histories.slh_paid_method',
             'users.name as paid_by_name')
                 ->where('salary_histories.Status', 1) // salary status paid
                 ->where('salary_histories.branch_office_id', $branch_office_id)
                 ->leftjoin('employee_infos', 'salary_histories.emp_auto_id', '=', 'employee_infos.emp_auto_id')
                 ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                 ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                 ->leftjoin('project_infos', 'salary_histories.project_id', '=', 'project_infos.proj_id')
                 ->leftjoin('users', 'salary_histories.updated_by', '=', 'users.id')
                 ->whereIn('slh_month', [$fromMonth, $toMonth])
                ->whereIn('slh_year', [$fromYear, $toYear])
                ->orderBy('employee_id', 'ASC')
                ->get();
        }
    }


    public function getAnEmployeeSalaryRecordWithJoinQuery($employee_id, $status)
    {

        return $pendingSalary = SalaryHistory::with('employee.category', 'month', 'employee.sponsor',  'employee.type')
            ->where('salary_histories.Status', $status)
            ->where('employee_infos.employee_id', $employee_id)
            ->leftjoin('employee_infos', 'salary_histories.emp_auto_id', '=', 'employee_infos.emp_auto_id')
            ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
            ->leftjoin('project_infos', 'salary_histories.project_id', '=', 'project_infos.proj_id')
            ->orderBy('employee_id', 'ASC')
            ->get();
    }

   public function searchAnEmployeeUnPaidSalaryRecordsWithEmloyeeInfoForListView($employee_ids,$branch_office_id)
    {
       return   SalaryHistory::with('employee.category', 'month', 'employee.sponsor',  'employee.type')
       ->where('salary_histories.Status', 0) // salary status = 0 = pending
       ->whereIn('employee_infos.employee_id', $employee_ids)
       ->where('salary_histories.branch_office_id',$branch_office_id)
       ->leftjoin('employee_infos', 'salary_histories.emp_auto_id', '=', 'employee_infos.emp_auto_id')
       ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
       ->leftjoin('project_infos', 'salary_histories.project_id', '=', 'project_infos.proj_id')
       ->orderBy('employee_id', 'ASC')
       ->orderBy('salary_histories.slh_month', 'ASC')
       ->orderBy('salary_histories.slh_year', 'ASC')
       ->get();

    }

    public function searchMultiEmployeeUnPaidSalaryRecordsForAMonthWithEmloyeeInfoForListView($employee_ids,$month,$year,$branch_office_id)
    {
        return   SalaryHistory::with('employee.category', 'month', 'employee.sponsor',  'employee.type')
        ->where('salary_histories.Status', 0) // salary status = 0 = pending
        ->where('salary_histories.slh_month', $month)
        ->where('salary_histories.slh_year', $year)
        ->whereIn('employee_infos.employee_id', $employee_ids)
        ->where('salary_histories.branch_office_id',$branch_office_id)
        ->leftjoin('employee_infos', 'salary_histories.emp_auto_id', '=', 'employee_infos.emp_auto_id')
        ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
        ->leftjoin('project_infos', 'salary_histories.project_id', '=', 'project_infos.proj_id')
        ->orderBy('employee_id', 'ASC')
        ->orderBy('salary_histories.slh_month', 'ASC')
       ->orderBy('salary_histories.slh_year', 'ASC')
        ->get();

    }

    // Salary Pending Or Paid Emp list for Paid Unpaid
    // public function searchListOfEmployeesThoseSalaryRecordsStatusIsUnpaidForListViewByProjectAndSponsor($sponsor_id_array, $proj_id, $emp_type,$is_hourly,$month, $year,$branch_office_id)
    // {


    //     if($emp_type >= 1 ){  // -1 means not selected emp type

    //         if($proj_id != 0){

    //              // project base employee type unpaid salary

    //              return $pendingSalary = SalaryHistory::with('employee.category', 'month', 'employee.sponsor',  'employee.type', 'employee.project')
    //             ->where('salary_histories.Status', 0) // salary status unpaid
    //             ->where('salary_histories.branch_office_id', $branch_office_id)
    //             ->where('employee_infos.emp_type_id', $emp_type)
    //             ->where('employee_infos.hourly_employee', $is_hourly)
    //             ->where('salary_histories.project_id', $proj_id)
    //             ->leftjoin('employee_infos', 'salary_histories.emp_auto_id', '=', 'employee_infos.emp_auto_id')
    //             ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
    //             ->leftjoin('project_infos', 'salary_histories.project_id', '=', 'project_infos.proj_id')
    //             ->where('slh_month', $month)
    //             ->where('slh_year', $year)
    //             ->orderBy('employee_id', 'ASC')
    //             ->get();
    //         }

    //         return $pendingSalary = SalaryHistory::with('employee.category', 'month', 'employee.sponsor',  'employee.type', 'employee.project')
    //             ->where('salary_histories.Status', 0) // salary status unpaid
    //             ->where('salary_histories.branch_office_id', $branch_office_id)
    //             ->where('employee_infos.emp_type_id', $emp_type)
    //             ->where('employee_infos.hourly_employee', $is_hourly)
    //             ->leftjoin('employee_infos', 'salary_histories.emp_auto_id', '=', 'employee_infos.emp_auto_id')
    //             ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
    //             ->leftjoin('project_infos', 'salary_histories.project_id', '=', 'project_infos.proj_id')
    //             ->where('slh_month', $month)
    //             ->where('slh_year', $year)
    //             ->orderBy('employee_id', 'ASC')
    //             ->get();
    //     }

    //     else if ( $proj_id != 0) {

    //         return $pendingSalary = SalaryHistory::with('employee.category', 'month', 'employee.sponsor',  'employee.type', 'employee.project')
    //             ->where('salary_histories.Status', 0) // salary status unpaid
    //             ->where('salary_histories.branch_office_id', $branch_office_id)
    //             ->whereIn('sponsor_id', $sponsor_id_array)
    //             ->where('salary_histories.project_id', $proj_id)
    //             ->leftjoin('employee_infos', 'salary_histories.emp_auto_id', '=', 'employee_infos.emp_auto_id')
    //             ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
    //             ->leftjoin('project_infos', 'salary_histories.project_id', '=', 'project_infos.proj_id')
    //             ->where('slh_month', $month)
    //             ->where('slh_year', $year)
    //             ->orderBy('employee_id', 'ASC')
    //             ->get();
    //     }
    //     else {
    //         return $pendingSalary = SalaryHistory::with('employee.category', 'month', 'employee.sponsor',  'employee.type', 'employee.project')
    //         ->where('salary_histories.Status', 0) // salary status unpaid
    //         ->where('salary_histories.branch_office_id', $branch_office_id)
    //             ->leftjoin('employee_infos', 'salary_histories.emp_auto_id', '=', 'employee_infos.emp_auto_id')
    //             ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
    //             ->leftjoin('project_infos', 'salary_histories.project_id', '=', 'project_infos.proj_id')
    //             ->where('slh_month', $month)
    //             ->where('slh_year', $year)
    //             ->whereIn('sponsor_id', $sponsor_id_array)
    //             ->orderBy('employee_id', 'ASC')
    //             ->get();
    //     }
    // }

    public function searchListOfEmployeesThoseSalaryRecordsStatusIsUnpaidForListViewByProjectAndSponsor($sponsor_id_array, $proj_ids, $emp_type,$month, $year,$branch_office_id)
    {
        if($emp_type == -1){
            // all emp type selected
             return SalaryHistory::with('employee.category', 'month', 'employee.sponsor',  'employee.type', 'employee.project')
                ->where('salary_histories.Status', 0) // salary status unpaid
                ->where('salary_histories.branch_office_id', $branch_office_id)
                ->whereIn('sponsor_id', $sponsor_id_array)
                ->whereIn('salary_histories.project_id', $proj_ids)
                ->leftjoin('employee_infos', 'salary_histories.emp_auto_id', '=', 'employee_infos.emp_auto_id')
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('project_infos', 'salary_histories.project_id', '=', 'project_infos.proj_id')
                ->where('slh_month', $month)
                ->where('slh_year', $year)
                ->orderBy('employee_id', 'ASC')
                ->get();

        }else if($emp_type == 0){
            // direct basic emp selected
             return SalaryHistory::with('employee.category', 'month', 'employee.sponsor',  'employee.type', 'employee.project')
                ->where('salary_histories.Status', 0) // salary status unpaid
                ->where('salary_histories.branch_office_id', $branch_office_id)
                ->whereIn('sponsor_id', $sponsor_id_array)
                ->whereIn('salary_histories.project_id', $proj_ids)
                 ->where('employee_infos.emp_type_id', 1)
                ->where('employee_infos.hourly_employee', NULL)
                ->leftjoin('employee_infos', 'salary_histories.emp_auto_id', '=', 'employee_infos.emp_auto_id')
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('project_infos', 'salary_histories.project_id', '=', 'project_infos.proj_id')
                ->where('slh_month', $month)
                ->where('slh_year', $year)
                ->orderBy('employee_id', 'ASC')
                ->get();

        }
        else if($emp_type == 1){
            // hourly emp selected
             return SalaryHistory::with('employee.category', 'month', 'employee.sponsor',  'employee.type', 'employee.project')
                ->where('salary_histories.Status', 0) // salary status unpaid
                ->where('salary_histories.branch_office_id', $branch_office_id)
                ->whereIn('sponsor_id', $sponsor_id_array)
                ->whereIn('salary_histories.project_id', $proj_ids)
                ->where('employee_infos.hourly_employee', 1)
                ->leftjoin('employee_infos', 'salary_histories.emp_auto_id', '=', 'employee_infos.emp_auto_id')
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('project_infos', 'salary_histories.project_id', '=', 'project_infos.proj_id')
                ->where('slh_month', $month)
                ->where('slh_year', $year)
                ->orderBy('employee_id', 'ASC')
                ->get();

        }else {
              // indrect emp selected
             return SalaryHistory::with('employee.category', 'month', 'employee.sponsor',  'employee.type', 'employee.project')
                ->where('salary_histories.Status', 0) // salary status unpaid
                ->where('salary_histories.branch_office_id', $branch_office_id)
                ->whereIn('sponsor_id', $sponsor_id_array)
                ->whereIn('salary_histories.project_id', $proj_ids)
                ->where('employee_infos.emp_type_id', 2)
                ->leftjoin('employee_infos', 'salary_histories.emp_auto_id', '=', 'employee_infos.emp_auto_id')
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('project_infos', 'salary_histories.project_id', '=', 'project_infos.proj_id')
                ->where('slh_month', $month)
                ->where('slh_year', $year)
                ->orderBy('employee_id', 'ASC')
                ->get();

        }

    }



    public function getEmployeeSalaryHistory($projectId, $sponserId, $month, $salaryYear, $salaryStatus)
    {

        if ($projectId  <= 0 && $sponserId <= 0) {
            return $salaryReport = EmployeeInfo::where('salary_histories.slh_month', $month)
                ->where('salary_histories.slh_year', $salaryYear)
                ->where('salary_histories.Status', $salaryStatus)
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
                ->leftjoin('project_infos', 'salary_histories.project_id', '=', 'project_infos.proj_id')
                ->orderBy('employee_id')
                ->get();
        } else if ($projectId  <= 0 && $sponserId > 0) {
            return $salaryReport = EmployeeInfo::where("employee_infos.sponsor_id", $sponserId)
                ->where('salary_histories.slh_month', $month)
                ->where('salary_histories.slh_year', $salaryYear)
                ->where('salary_histories.Status', $salaryStatus)
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
                ->leftjoin('project_infos', 'salary_histories.project_id', '=', 'project_infos.proj_id')
                ->orderBy('employee_id')
                ->get();
        } else if ($projectId  > 0 && $sponserId <= 0) {
            return $salaryReport = EmployeeInfo::where('salary_histories.project_id', $projectId)
                ->where('salary_histories.slh_month', $month)
                ->where('salary_histories.slh_year', $salaryYear)
                ->where('salary_histories.Status', $salaryStatus)
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
                ->leftjoin('project_infos', 'salary_histories.project_id', '=', 'project_infos.proj_id')
                ->orderBy('employee_id')
                ->get();
        } else if ($projectId  > 0 && $sponserId > 0) {
            return $salaryReport = EmployeeInfo::where("employee_infos.sponsor_id", $sponserId)
                ->where('salary_histories.project_id', $projectId)
                ->where('salary_histories.slh_month', $month)
                ->where('salary_histories.slh_year', $salaryYear)
                ->where('salary_histories.Status', $salaryStatus)
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
                 ->leftjoin('project_infos', 'salary_histories.project_id', '=', 'project_infos.proj_id')
                ->orderBy('employee_id')
                ->get();
        }
    }

    public function searchSponsorBaseSalarySheetReport($projectId, $sponserId, $month, $salaryYear, $salaryStatus)
    {

        if ($projectId  <= 0 && $sponserId <= 0) {

            return    EmployeeInfo::where('salary_histories.slh_month', $month)
                ->where('salary_histories.slh_year', $salaryYear)
                ->where('salary_histories.Status', $salaryStatus)
                 ->where('monthly_work_histories.month_id', $month)->where('monthly_work_histories.year_id', $salaryYear)
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                    ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
                  ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
                  ->leftjoin('monthly_work_histories', 'employee_infos.emp_auto_id', '=', 'monthly_work_histories.emp_id')
                ->leftjoin('project_infos', 'salary_histories.project_id', '=', 'project_infos.proj_id')
                ->orderBy('employee_id')
                ->get();

        } else if ($projectId  <= 0 && $sponserId > 0) {

            return  $salaryReport = EmployeeInfo::where("employee_infos.sponsor_id", $sponserId)
                ->where('salary_histories.slh_month', $month)
                ->where('salary_histories.slh_year', $salaryYear)
                ->where('salary_histories.Status', $salaryStatus)
                ->where('monthly_work_histories.month_id', $month)->where('monthly_work_histories.year_id', $salaryYear)
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                                ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
                  ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
                  ->leftjoin('monthly_work_histories', 'employee_infos.emp_auto_id', '=', 'monthly_work_histories.emp_id')

                ->leftjoin('project_infos', 'salary_histories.project_id', '=', 'project_infos.proj_id')
                ->orderBy('employee_id')
                ->get();

        } else if ($projectId  > 0 && $sponserId <= 0) {
            return $salaryReport = EmployeeInfo::where('salary_histories.project_id', $projectId)
                ->where('salary_histories.slh_month', $month)
                ->where('salary_histories.slh_year', $salaryYear)
                ->where('salary_histories.Status', $salaryStatus)
                ->where('monthly_work_histories.month_id', $month)->where('monthly_work_histories.year_id', $salaryYear)
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                                ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
                  ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
                  ->leftjoin('monthly_work_histories', 'employee_infos.emp_auto_id', '=', 'monthly_work_histories.emp_id')

                ->leftjoin('project_infos', 'salary_histories.project_id', '=', 'project_infos.proj_id')
                ->orderBy('employee_id')
                ->get();
        } else if ($projectId  > 0 && $sponserId > 0) {
            return $salaryReport = EmployeeInfo::where("employee_infos.sponsor_id", $sponserId)
                ->where('salary_histories.project_id', $projectId)
                ->where('salary_histories.slh_month', $month)
                ->where('salary_histories.slh_year', $salaryYear)
                ->where('salary_histories.Status', $salaryStatus)
                ->where('monthly_work_histories.month_id', $month)->where('monthly_work_histories.year_id', $salaryYear)
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
                  ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
                  ->leftjoin('monthly_work_histories', 'employee_infos.emp_auto_id', '=', 'monthly_work_histories.emp_id')
                ->leftjoin('project_infos', 'salary_histories.project_id', '=', 'project_infos.proj_id')
                ->orderBy('employee_id')
                ->get();
        }
    }

     public function searchSponsorBaseSalarySheetReportByMultipleProjectIDAndSponsorID($projectIds, $sponserIds, $month, $salaryYear, $salary_status_list)
    {


            return $salaryReport = EmployeeInfo::whereIn("employee_infos.sponsor_id", $sponserIds)
                ->whereIn('salary_histories.project_id', $projectIds)
                ->where('salary_histories.slh_month', $month)
                ->where('salary_histories.slh_year', $salaryYear)
                ->whereIn('salary_histories.Status', $salary_status_list)
                ->where('monthly_work_histories.month_id', $month)->where('monthly_work_histories.year_id', $salaryYear)
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
                  ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
                  ->leftjoin('monthly_work_histories', 'employee_infos.emp_auto_id', '=', 'monthly_work_histories.emp_id')
                ->leftjoin('project_infos', 'salary_histories.project_id', '=', 'project_infos.proj_id')
                ->orderBy('employee_id')
                ->get();

    }

    public function getSponsorwiseEmployeeSalaryReportForSaudi($sponserIdList, $month, $salaryYear)
    {
            return  $salaryReport = EmployeeInfo::whereIn("employee_infos.sponsor_id", $sponserIdList)
                ->where('salary_histories.slh_month', $month)
                ->where('salary_histories.slh_year', $salaryYear)
                 ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
                ->orderBy('employee_id')
                ->get();


    }

    public function getEmployeeSalaryProjectAndTrade($projectId, $trades, $month, $year, $salary_status)
    {

         return  EmployeeInfo::select('employee_infos.employee_id','employee_infos.emp_auto_id','employee_infos.employee_name','employee_infos.akama_no','employee_infos.salary_status','sponsors.spons_name','countries.country_name',
                'monthly_work_histories.paid_leave','job_statuses.title','employee_categories.catg_name','project_infos.proj_name','salary_histories.*')
                ->where("employee_infos.job_status", 1)
                ->whereIn('employee_infos.designation_id', $trades)
                ->whereIn("employee_infos.project_id", $projectId)
                ->where("salary_histories.slh_month", $month)
                ->where("salary_histories.slh_year", $year)
                 ->whereIn("salary_histories.Status", $salary_status)
                 ->where('monthly_work_histories.month_id', $month)->where('monthly_work_histories.year_id', $year)
                ->leftjoin('salary_histories', 'salary_histories.emp_auto_id', '=', 'employee_infos.emp_auto_id')
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->leftjoin('monthly_work_histories', 'salary_histories.emp_auto_id', '=', 'monthly_work_histories.emp_id')
                ->orderBy('employee_infos.employee_id', 'ASC')
                ->get();

    }

    public function getEmployeeSalaryHistoryWithProjectAndEmployeeType($projectId, $empType, $isHourlyEmp, $month, $salaryYear, $salaryStatus)
    {

        if ($projectId  > 0 && $empType > 0) {
            return $salaryReport = EmployeeInfo::where("salary_histories.project_id", $projectId)
                ->where('employee_infos.emp_type_id', $empType)
                ->where('salary_histories.slh_month', $month)
                ->where('salary_histories.slh_year', $salaryYear)
                ->where('salary_histories.Status', $salaryStatus)
                ->where('employee_infos.hourly_employee', $isHourlyEmp)
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
                ->orderBy('employee_id')
                ->get();


        } else if ($empType > 0) {
            return $salaryReport = EmployeeInfo::where('employee_infos.emp_type_id', $empType)
                ->where('salary_histories.slh_month', $month)
                ->where('salary_histories.slh_year', $salaryYear)
                ->where('salary_histories.Status', $salaryStatus)
                ->where('employee_infos.hourly_employee', $isHourlyEmp)
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
                ->orderBy('employee_id')
                ->get();
        }
    }

    public function getAllHourlyEmployeesSalaryInforForSalarySheetPrintByProjectSalaryStatus($projectIds, $month, $salaryYear, $salaryStatus)
  {

                 return EmployeeInfo::select('employee_infos.*','sponsors.spons_name','employee_categories.catg_name','countries.country_name','monthly_work_histories.paid_leave','job_statuses.title','salary_histories.*')
                    ->where('salary_histories.slh_month', $month)
                    ->where('salary_histories.slh_year', $salaryYear)
                    ->where('monthly_work_histories.month_id', $month)->where('monthly_work_histories.year_id', $salaryYear)
                    ->whereIn('salary_histories.Status', $salaryStatus)
                     ->where('employee_infos.hourly_employee', 1) // all hourly employees
                    ->whereIn('salary_histories.project_id', $projectIds)
                    ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                    ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                    ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                    ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                    ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
                     ->leftjoin('monthly_work_histories', 'salary_histories.emp_auto_id', '=', 'monthly_work_histories.emp_id')
                    ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
                    ->orderBy('employee_id')
                    ->get();

  }


public function getAllDirectEmployeesSalaryInforForSalarySheetPrintByProjectSalaryStatus($projectId, $month, $salaryYear, $salaryStatus)
  {

            if ($projectId  == null)
            {
                 return   EmployeeInfo::select('employee_infos.*','sponsors.spons_name','employee_categories.catg_name','countries.country_name','monthly_work_histories.paid_leave','job_statuses.title','salary_histories.*')
                  //  ->where('employee_infos.hourly_employee', $hourly_employee)
                    ->where('salary_histories.slh_month', $month)
                    ->where('salary_histories.slh_year', $salaryYear)
                    ->where('monthly_work_histories.month_id', $month)->where('monthly_work_histories.year_id', $salaryYear)
                    ->whereIn('salary_histories.Status', $salaryStatus)
                    ->where('employee_infos.emp_type_id', 1) // all direct employees
                    ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                    ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                    ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                    ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                    ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
                     ->leftjoin('monthly_work_histories', 'salary_histories.emp_auto_id', '=', 'monthly_work_histories.emp_id')
                    ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
                    ->orderBy('employee_id')
                    ->get();
            }
            else{
                 return $salaryReport = EmployeeInfo::select('employee_infos.*','sponsors.spons_name','employee_categories.catg_name','countries.country_name','monthly_work_histories.paid_leave','job_statuses.title','salary_histories.*')
                    ->where('salary_histories.slh_month', $month)
                    ->where('salary_histories.slh_year', $salaryYear)
                    ->where('monthly_work_histories.month_id', $month)->where('monthly_work_histories.year_id', $salaryYear)
                    ->whereIn('salary_histories.Status', $salaryStatus)
                    ->where('employee_infos.emp_type_id', 1) // all direct employees
                    ->where('salary_histories.project_id', $projectId)
                    ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                    ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                    ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                    ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                    ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
                     ->leftjoin('monthly_work_histories', 'salary_histories.emp_auto_id', '=', 'monthly_work_histories.emp_id')
                    ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
                    ->orderBy('employee_id')
                    ->get();
            }
    }

   // Basic Salary Direct and Basic Salary Hourly and Indirect Employee Salary Report
  public function getEmployeeSalaryHistoryWithProjectAndEmployeeTypeBasicAndHourlyEmployee($projectId, $empType, $hourly_employee, $month, $salaryYear, $salaryStatus)
  {

             if ($projectId  != null && $empType != null)
            {
                if($empType == 3){
                    return  EmployeeInfo::select('employee_infos.*','sponsors.spons_name','employee_categories.catg_name','countries.country_name','monthly_work_histories.paid_leave','job_statuses.title','salary_histories.*')
                    ->where('employee_infos.hourly_employee', null)
                    ->where('salary_histories.slh_month', $month)
                    ->where('salary_histories.slh_year', $salaryYear)
                    ->whereIn('salary_histories.Status', $salaryStatus)
                    ->where('salary_histories.project_id', $projectId)
                    ->where('monthly_work_histories.month_id', $month)->where('monthly_work_histories.year_id', $salaryYear)
                    ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                    ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                    ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                    ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                    ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
                    ->leftjoin('monthly_work_histories', 'salary_histories.emp_auto_id', '=', 'monthly_work_histories.emp_id')
                    ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
                    ->orderBy('employee_id')
                    ->get();
                }
                 return   EmployeeInfo::select('employee_infos.*','sponsors.spons_name','employee_categories.catg_name','countries.country_name','monthly_work_histories.paid_leave','job_statuses.title','salary_histories.*')
                 ->where('employee_infos.hourly_employee', $hourly_employee)
                    ->where('salary_histories.slh_month', $month)
                    ->where('salary_histories.slh_year', $salaryYear)
                    ->where('monthly_work_histories.month_id', $month)->where('monthly_work_histories.year_id', $salaryYear)
                    ->whereIn('salary_histories.Status', $salaryStatus)
                    ->where('employee_infos.emp_type_id', $empType)
                    ->where('salary_histories.project_id', $projectId)
                    ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                    ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                    ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                    ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                    ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
                     ->leftjoin('monthly_work_histories', 'salary_histories.emp_auto_id', '=', 'monthly_work_histories.emp_id')
                    ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
                    ->orderBy('employee_id')
                    ->get();
            }
            else if ($projectId  != null && $empType == null){
                 return $salaryReport = EmployeeInfo::select('employee_infos.*','sponsors.spons_name','employee_categories.catg_name','countries.country_name','monthly_work_histories.paid_leave','job_statuses.title','salary_histories.*')
                    ->where('salary_histories.slh_month', $month)
                    ->where('salary_histories.slh_year', $salaryYear)
                    ->where('monthly_work_histories.month_id', $month)->where('monthly_work_histories.year_id', $salaryYear)
                    ->whereIn('salary_histories.Status', $salaryStatus)
                    ->where('salary_histories.project_id', $projectId)
                    ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                    ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                    ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                    ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                    ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
                     ->leftjoin('monthly_work_histories', 'salary_histories.emp_auto_id', '=', 'monthly_work_histories.emp_id')
                    ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
                    ->orderBy('employee_id')
                    ->get();
            }
            else if ($projectId  == null && $empType != null)
            {
                    if($empType == 3){
                        return  EmployeeInfo::select('employee_infos.*','sponsors.spons_name','employee_categories.catg_name','countries.country_name','monthly_work_histories.paid_leave','job_statuses.title','salary_histories.*')
                        ->where('employee_infos.hourly_employee', null)
                        ->where('salary_histories.slh_month', $month)
                        ->where('salary_histories.slh_year', $salaryYear)
                        ->where('monthly_work_histories.month_id', $month)->where('monthly_work_histories.year_id', $salaryYear)
                        ->whereIn('salary_histories.Status', $salaryStatus)
                        ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                        ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                        ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                        ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                        ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
                         ->leftjoin('monthly_work_histories', 'salary_histories.emp_auto_id', '=', 'monthly_work_histories.emp_id')
                        ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
                        ->orderBy('employee_id')
                        ->get();
                    }
                    return   EmployeeInfo::select('employee_infos.*','sponsors.spons_name','employee_categories.catg_name','countries.country_name','monthly_work_histories.paid_leave','job_statuses.title','salary_histories.*')
                        ->where('employee_infos.hourly_employee', $hourly_employee)
                        ->where('salary_histories.slh_month', $month)
                        ->where('salary_histories.slh_year', $salaryYear)
                        ->where('monthly_work_histories.month_id', $month)->where('monthly_work_histories.year_id', $salaryYear)
                        ->whereIn('salary_histories.Status', $salaryStatus)
                        ->where('employee_infos.emp_type_id', $empType)
                        ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                        ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                        ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                        ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                        ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
                         ->leftjoin('monthly_work_histories', 'salary_histories.emp_auto_id', '=', 'monthly_work_histories.emp_id')
                        ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
                        ->orderBy('employee_id')
                        ->get();
            }
            else   {
                 return EmployeeInfo::select('employee_infos.*','sponsors.spons_name','employee_categories.catg_name','countries.country_name','monthly_work_histories.paid_leave','job_statuses.title','salary_histories.*')
                    ->where('salary_histories.slh_month', $month)
                    ->where('salary_histories.slh_year', $salaryYear)
                    ->where('monthly_work_histories.month_id', $month)->where('monthly_work_histories.year_id', $salaryYear)
                    ->whereIn('salary_histories.Status', $salaryStatus)
                    ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                    ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                    ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                    ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                    ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
                    ->leftjoin('monthly_work_histories', 'salary_histories.emp_auto_id', '=', 'monthly_work_histories.emp_id')
                    ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
                    ->orderBy('employee_id')
                    ->get();
            }


  }



    public function getEmployeeSalaryHistoryWithProjectAndEmployeeJobStatus($projectId, $month, $salaryYear, $salaryStatus)
    {

        if(is_null($salaryStatus)){
             return   $salaryReport = EmployeeInfo::where("salary_histories.project_id", $projectId)
            ->where('salary_histories.slh_month', $month)
            ->where('salary_histories.slh_year', $salaryYear)
            ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
            ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
            ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
            ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
            ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
            ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
            ->orderBy('employee_id')
            ->get();
        }else {

        return   $salaryReport = EmployeeInfo::where("salary_histories.project_id", $projectId)
            ->where('salary_histories.slh_month', $month)
            ->where('salary_histories.slh_year', $salaryYear)
            ->where('salary_histories.Status', $salaryStatus)
            ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
            ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
            ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
            ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
            ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
            ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
            ->orderBy('employee_id')
            ->get();
        }
    }


    // public function getEmployeeSalaryReportThoseAreSalaryHold($project_ids, $month, $year,$status)
    // {

    //     if(is_null($project_ids)  ){
    //         return   EmployeeInfo::where('salary_histories.slh_month', $month)
    //             ->where('salary_histories.slh_year', $year)
    //             ->whereIn('salary_histories.Status', $status)
    //             ->where('employee_infos.salary_status','>=', 2)  // 1 = active salary otherwise hold salary
    //             ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
    //             ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
    //             ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
    //             ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
    //             ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
    //             ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
    //             ->orderBy('employee_id')
    //             ->get();

    //     }else {
    //         return   EmployeeInfo::where('salary_histories.slh_month', $month)
    //             ->where('salary_histories.slh_year', $year)
    //             ->whereIn('salary_histories.Status', $status)
    //             ->whereIn('salary_histories.project_id', $project_ids)
    //             ->where('employee_infos.salary_status','>=', 2)
    //             ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
    //             ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
    //             ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
    //             ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
    //             ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
    //             ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
    //             ->orderBy('employee_id')
    //             ->get();
    //     }



    // }

    public function getEmployeeSalaryReportThoseAreSalaryHold($project_ids, $month, $year,$salary_status,$job_statuses)
    {

        if(is_null($project_ids)  ){
            return   EmployeeInfo::where('salary_histories.slh_month', $month)
                ->where('salary_histories.slh_year', $year)
                ->whereIn('salary_histories.Status', $salary_status)
                ->whereIn('employee_infos.job_status', $job_statuses)
                ->where('employee_infos.salary_status','>=', 2)  // 1 = active salary otherwise hold salary
                ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
                ->orderBy('employee_id')
                ->get();


        }else {
            return   EmployeeInfo::where('salary_histories.slh_month', $month)
                ->where('salary_histories.slh_year', $year)
                ->whereIn('salary_histories.Status', $status)
                ->whereIn('employee_infos.job_status', $job_statuses)
                ->whereIn('salary_histories.project_id', $project_ids)
                ->where('employee_infos.salary_status','>=', 2)
                ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
                ->orderBy('employee_id')
                ->get();
        }



    }


    public function getOfficeStaffEmployeeSalaryReport($project_ids, $month, $year)
    {

        if(is_null($project_ids)  ){
            return   EmployeeInfo::where('salary_histories.slh_month', $month)
                ->where('salary_histories.slh_year', $year)
                ->where('employee_details.staff_employee',1)  // 1 = active salary otherwise hold salary
                ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->leftjoin('employee_details', 'employee_infos.emp_auto_id', '=', 'employee_details.emp_auto_id')
                ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
                ->orderBy('employee_id')
                ->get();

        }else {
            return   EmployeeInfo::where('salary_histories.slh_month', $month)
                ->where('salary_histories.slh_year', $year)
                ->whereIn('salary_histories.project_id', $project_ids)
                ->where('employee_details.staff_employee',1)  // 1 = active salary otherwise hold salary
                ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->leftjoin('employee_details', 'employee_infos.emp_auto_id', '=', 'employee_details.emp_auto_id')
                ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
                ->orderBy('employee_id')
                ->get();

        }
    }



    public function getAnEmployeeDetailsWithSalaryRecordForMultiProjectSalaryProcess($emp_auto_id, $month, $salaryYear)
    {

        return  EmployeeInfo::where("employee_infos.emp_auto_id", $emp_auto_id)
            ->where('salary_histories.slh_month', $month)
            ->where('salary_histories.slh_year', $salaryYear)
            ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
            ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
            ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
            ->first();
    }



    public function getMultipleEmployeeIdBaseSalaryHistory($allEmplId, $month, $salaryYear, $salaryStatus,$branch_office_id)
    {

        if(is_null($salaryStatus)){

             return  EmployeeInfo::select('employee_infos.*','sponsors.spons_name','employee_categories.catg_name','countries.country_name','monthly_work_histories.paid_leave','job_statuses.title','salary_histories.*')
                    ->where('salary_histories.slh_month', $month)
                     ->where('salary_histories.slh_year', $salaryYear)
                    ->whereIn('employee_infos.employee_id', $allEmplId)
                    ->where('salary_histories.branch_office_id',$branch_office_id)
                    ->where('monthly_work_histories.month_id', $month)->where('monthly_work_histories.year_id', $salaryYear)
                    ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                    ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                    ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                    ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                    ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
                    ->leftjoin('monthly_work_histories', 'salary_histories.emp_auto_id', '=', 'monthly_work_histories.emp_id')
                    ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
                    ->orderBy('employee_id')
                    ->get();
        }else {
              return  EmployeeInfo::select('employee_infos.*','sponsors.spons_name','employee_categories.catg_name','countries.country_name','monthly_work_histories.paid_leave','job_statuses.title','salary_histories.*')
                    ->where('salary_histories.slh_month', $month)
                    ->where('salary_histories.slh_year', $salaryYear)
                    ->where('salary_histories.Status', $salaryStatus)
                    ->whereIn('employee_infos.employee_id', $allEmplId)
                    ->where('salary_histories.branch_office_id',$branch_office_id)
                    ->where('monthly_work_histories.month_id', $month)->where('monthly_work_histories.year_id', $salaryYear)
                    ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                    ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                    ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                    ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                    ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
                    ->leftjoin('monthly_work_histories', 'salary_histories.emp_auto_id', '=', 'monthly_work_histories.emp_id')
                    ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
                    ->orderBy('employee_id')
                    ->get();
            }
    }

       public function getEmployeeWorkingSalaryDetailsThoseAreNotActiveNowByProjectAndSponsor($project_ids, $month, $salaryYear, $salaryStatus,$emp_job_status,$branch_office_id)
    {


            return  EmployeeInfo::
                whereIn('salary_histories.project_id',$project_ids)
            ->where('salary_histories.slh_month', $month)
            ->where('salary_histories.slh_year', $salaryYear)
            ->whereIn('employee_infos.job_status',$emp_job_status) // 1 = active salary otherwise hold salary
            ->whereIn('salary_histories.Status', $salaryStatus)
            ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
            ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
            ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
            ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
            ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
            ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
            ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
            ->orderBy('employee_id', 'ASC')
            ->orderBy('salary_histories.project_id', 'ASC')
            ->get();

        //      return  EmployeeInfo::select(
        //         'employee_infos.hourly_employee',
        //         DB::raw("COUNT(salary_histories.slh_auto_id) as total_emp"),
        //         DB::raw("SUM(salary_histories.slh_total_salary) as total_salary"),
        //         DB::raw("SUM(salary_histories.slh_saudi_tax) as total_saudi_tax"),
        //         DB::raw("SUM(salary_histories.slh_iqama_advance) as total_iqama_adv"),
        //         DB::raw("SUM(salary_histories.slh_other_advance) as total_other_adv"),
        //     )
        //      ->whereNot("employee_infos.job_status", 1) // all except active emp
        //     ->where("employee_infos.project_id", $project_id)
        //     ->where("salary_histories.slh_month", $month)
        //     ->where("salary_histories.slh_year", $year)
        //    ->where("monthly_work_histories.month_id", $month)
        //    ->where("monthly_work_histories.year_id", $year)
        //    ->where('monthly_work_histories.work_project_id', $project_id)
        //     ->whereIn("salary_histories.Status", $salary_status_ids)
        //     ->leftjoin('monthly_work_histories', 'employee_infos.emp_auto_id', '=', 'monthly_work_histories.emp_id')
        //     ->leftjoin('salary_histories', 'salary_histories.emp_auto_id', '=', 'employee_infos.emp_auto_id')
        //     ->groupBy('employee_infos.hourly_employee')
        //     ->orderBy('employee_infos.hourly_employee', 'ASC')
        //     ->get();


    }

    // // Cost Controll Salary summary Report
    // public function getMultipleProjectSalarySummaryCostControllReportByDesignationHead_old($project_id_list, $sponsor_id_array,$desig_head_id, $month, $salaryYear)
    // {
    //         return SalaryHistory::select(
    //             'employee_infos.hourly_employee',
    //             DB::raw("COUNT(slh_auto_id) as total_emp"),
    //             DB::raw("SUM(slh_all_include_amount) as total_slh_all_include_amount"),
    //             DB::raw("SUM(food_allowance) as food_allowance")
    //         )
    //         ->leftjoin('employee_infos', 'salary_histories.emp_auto_id' , '=', 'employee_infos.emp_auto_id')
    //         ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
    //         ->groupBy("employee_infos.hourly_employee")
    //         ->where('slh_month', $month)->where('slh_year', $salaryYear)
    //         ->whereIn('salary_histories.project_id', $project_id_list)
    //         ->whereIn('employee_infos.sponsor_id', $sponsor_id_array)
    //         ->where('employee_categories.dh_auto_id', $desig_head_id)
    //         ->get();

    // }

    // public function getMultipleProjectSalarySummaryCostControllReportByDesignationHead($project_id_list, $sponsor_id_array,$catg_id, $month, $salaryYear)
    // {
    //         return SalaryHistory::select(
    //             'employee_infos.hourly_employee',
    //             DB::raw("COUNT(slh_auto_id) as total_emp"),
    //             DB::raw("SUM(slh_all_include_amount) as total_slh_all_include_amount"),
    //             DB::raw("SUM(food_allowance) as food_allowance")
    //         )
    //         ->leftjoin('employee_infos', 'salary_histories.emp_auto_id' , '=', 'employee_infos.emp_auto_id')
    //         ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
    //         ->groupBy("employee_infos.hourly_employee")
    //         ->where('slh_month', $month)->where('slh_year', $salaryYear)
    //         ->whereIn('salary_histories.project_id', $project_id_list)
    //         ->whereIn('employee_infos.sponsor_id', $sponsor_id_array)
    //         ->where('employee_categories.catg_id', $catg_id)
    //         ->get();

    // }

    // // Cost Controll Salary details Report
    // public function getMultipleProjectSalaryDetailsForCostControllReportBySponsorTypeAndEmployeeType($project_id_list, $sponsor_id_array,$desig_head_ids, $month, $salaryYear)
    // {

    //     return  EmployeeInfo::where('salary_histories.slh_month', $month)
    //         ->where('salary_histories.slh_year', $salaryYear)
    //         ->whereIn('salary_histories.project_id', $project_id_list)
    //         ->whereIn('employee_infos.sponsor_id', $sponsor_id_array)
    //         ->whereIn('employee_categories.catg_id', $desig_head_ids)
    //         ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
    //         ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
    //         ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
    //         ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
    //         ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
    //         ->orderBy('employee_id')
    //         ->get();

    // }

    // public function getMultipleProjectSalaryDetailsCostControllReportByDesignationHead($project_id_list, $sponsor_id_array,$desig_head_ids, $month, $salaryYear,$salary_status)
    // {
    //         return  EmployeeInfo::where('salary_histories.slh_month', $month)
    //         ->where('salary_histories.slh_year', $salaryYear)
    //         ->whereIn('salary_histories.project_id', $project_id_list)
    //         ->whereIn('employee_infos.sponsor_id', $sponsor_id_array)
    //         ->whereIn('employee_categories.dh_auto_id', $desig_head_ids)
    //         ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
    //         ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
    //         ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
    //         ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
    //         ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
    //         ->orderBy('employee_id')
    //         ->get();
    // }

         //1 Cost Controll Salary details Report
    public function getMultipleProjectSalaryDetailsForCostControllReportBySponsorTypeAndEmployeeType($project_id_list, $sponsor_id_array,$desig_head_ids, $month, $salaryYear)
    {

        return  EmployeeInfo::where('salary_histories.slh_month', $month)
            ->where('salary_histories.slh_year', $salaryYear)
            ->whereIn('salary_histories.project_id', $project_id_list)
            ->whereIn('employee_infos.sponsor_id', $sponsor_id_array)
            ->whereIn('employee_categories.catg_id', $desig_head_ids)
            ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
            ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
            ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
            ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
            ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
            ->orderBy('employee_id')
            ->get();

    }

    //2 Cost Controll Salary summary Report
    public function getMultipleProjectSalarySummaryForCostControllReportBySponsorTypeAndEmployeeType($project_id_list, $sponsor_id_array,$catg_id, $month, $salaryYear)
    {
            return SalaryHistory::select(
                'employee_infos.hourly_employee',
                DB::raw("COUNT(slh_auto_id) as total_emp"),
                DB::raw("SUM(slh_total_hours) as total_hours"),
                DB::raw("SUM(slh_all_include_amount) as total_slh_all_include_amount"),
                DB::raw("SUM(food_allowance) as food_allowance")
                )
            ->leftjoin('employee_infos', 'salary_histories.emp_auto_id' , '=', 'employee_infos.emp_auto_id')
            ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
            ->groupBy("employee_infos.hourly_employee")
            ->where('slh_month', $month)->where('slh_year', $salaryYear)
            ->whereIn('salary_histories.project_id', $project_id_list)
            ->whereIn('employee_infos.sponsor_id', $sponsor_id_array)
            ->where('employee_categories.catg_id', $catg_id)
            ->get();

    }

    //3 Cost Controll Salary details Report
        public function getMultipleProjectSalaryDetailsCostControllReportByDesignationHead($project_id_list, $sponsor_id_array,$desig_head_ids, $month, $salaryYear,$salary_status)
    {
            return  EmployeeInfo::where('salary_histories.slh_month', $month)
            ->where('salary_histories.slh_year', $salaryYear)
            ->whereIn('salary_histories.project_id', $project_id_list)
            ->whereIn('employee_infos.sponsor_id', $sponsor_id_array)
            ->whereIn('employee_categories.dh_auto_id', $desig_head_ids)
            ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
            ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
            ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
            ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
            ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
            ->orderBy('employee_id')
            ->get();
    }
    //4 Cost Controll Salary details Report
    public function getMultipleProjectSalarySummaryCostControllReportByDesignationHead($project_id_list, $sponsor_id_array,$desig_head_id, $month, $salaryYear)
    {
            return SalaryHistory::select(
                'employee_infos.hourly_employee',
                DB::raw("COUNT(slh_auto_id) as total_emp"),
                DB::raw("SUM(slh_all_include_amount) as total_slh_all_include_amount"),
                DB::raw("SUM(food_allowance) as food_allowance"),
            )
            ->leftjoin('employee_infos', 'salary_histories.emp_auto_id' , '=', 'employee_infos.emp_auto_id')
            ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
            ->groupBy("employee_infos.hourly_employee")
            ->where('slh_month', $month)->where('slh_year', $salaryYear)
            ->whereIn('salary_histories.project_id', $project_id_list)
            ->whereIn('employee_infos.sponsor_id', $sponsor_id_array)
            ->where('employee_categories.dh_auto_id', $desig_head_id)
            ->get();

    }


       // sub-contractor salary: Single Project a Sponsor Multiple MOnth Salary Summary Report
    public function getAProjectCurrentlyWorkingEmployeeSalarySummaryReport($project_id_list, $sponsor_id, $months, $salaryYear)
    {

             return SalaryHistory::select(
                'salary_histories.slh_month',
                DB::raw("COUNT(slh_auto_id) as total_emp"),
                DB::raw("SUM(slh_total_salary) as total_gross_salary")
             )
            ->leftjoin('employee_infos', 'salary_histories.emp_auto_id' , '=', 'employee_infos.emp_auto_id')
             ->whereIn('slh_month', $months)->where('slh_year', $salaryYear)
            ->whereIn('salary_histories.project_id', $project_id_list)
            ->where('employee_infos.sponsor_id', $sponsor_id)
            ->groupBy("salary_histories.slh_month")
            ->orderBy('salary_histories.slh_month')
            ->get();

    }

         // sub-contractor salary: Single Project single Sponsor   Salary amount and no of emp  Summary Report
    public function getAProjectWorkedEmployeeSalaryForSelectedSponsorsSummaryReport($project_id, $sponsor_id, $month, $salaryYear)
    {

            return SalaryHistory::select(
                'employee_infos.sponsor_id',
                DB::raw("COUNT(slh_auto_id) as total_emp"),
                DB::raw("SUM(slh_total_hours) as total_hours"),
                DB::raw("SUM(slh_total_salary) as total_gross_salary")
             )
            ->leftjoin('employee_infos', 'salary_histories.emp_auto_id' , '=', 'employee_infos.emp_auto_id')
            ->where('slh_month', $month)->where('slh_year', $salaryYear)
            ->where('salary_histories.project_id', $project_id)
            ->where('employee_infos.sponsor_id', $sponsor_id)
            ->groupBy("employee_infos.sponsor_id")
            ->first();


    }

     // subcon and asloob sponsor unpaid salary summary
    public function getAProjectSubcontractorAndCompanySponsorUnpaidSalarySummaryReport($project_id, $month, $salaryYear)
    {
        // $subcon = SalaryHistory::select(
        //         'sponsors.owner_name',
        //         DB::raw("COUNT(slh_auto_id) as total_emp"),
        //         DB::raw("SUM(slh_total_salary) as total_salary")
        //      )
        //     ->leftjoin('employee_infos', 'salary_histories.emp_auto_id' , '=', 'employee_infos.emp_auto_id')
        //     ->leftjoin('sponsors', 'employee_infos.sponsor_id' , '=', 'sponsors.spons_id')
        //     ->where('slh_month', $month)->where('slh_year', $salaryYear)
        //     ->where('salary_histories.project_id', $project_id)
        //     ->groupBy("sponsors.owner_name")
        //     ->orderBy('sponsors.owner_name','ASC')
        //     ->get();

         $subcon = SalaryHistory::select(
            'salary_histories.status',
            DB::raw("COUNT(slh_auto_id) as total_emp"),
            DB::raw("SUM(slh_total_salary) as total_salary")
         )
        ->leftjoin('employee_infos', 'salary_histories.emp_auto_id' , '=', 'employee_infos.emp_auto_id')
        ->leftjoin('sponsors', 'employee_infos.sponsor_id' , '=', 'sponsors.spons_id')
        ->where('slh_month', $month)->where('slh_year', $salaryYear)
        ->where('salary_histories.project_id', $project_id)
        ->where('sponsors.owner_name', 'Subcon')
       // ->where('employee_infos.sponsor_id', $sponsor_id)
        ->groupBy("salary_histories.status")
        ->orderBy('salary_histories.status','ASC')
        ->get();

            $basic_hourly = SalaryHistory::select(
                'employee_infos.hourly_employee',
                DB::raw("COUNT(slh_auto_id) as total_emp"),
                DB::raw("SUM(slh_total_salary) as total_salary")
             )
            ->leftjoin('employee_infos', 'salary_histories.emp_auto_id' , '=', 'employee_infos.emp_auto_id')
            ->leftjoin('sponsors', 'employee_infos.sponsor_id' , '=', 'sponsors.spons_id')
            ->where('slh_month', $month)->where('slh_year', $salaryYear)
            ->where('salary_histories.project_id', $project_id)
            ->where('sponsors.owner_name','Asloob')
            ->groupBy("employee_infos.hourly_employee")
            ->orderBy('employee_infos.hourly_employee','ASC')
            ->get();

            $paid_unpaid = SalaryHistory::select(
                'salary_histories.status',
                DB::raw("COUNT(slh_auto_id) as total_emp"),
                DB::raw("SUM(slh_total_salary) as total_salary")
             )
            ->where('slh_month', $month)->where('slh_year', $salaryYear)
            ->where('salary_histories.project_id', $project_id)
            ->groupBy("salary_histories.status")
            ->orderBy("salary_histories.status","ASC")
            ->get();

            return [$subcon,$basic_hourly,$paid_unpaid];
    }


    // subcon and asloob  paid and unpaid salary summary
    public function getAProjectSubcontractorAndCompanySponsorPaidUnpaidSalarySummaryReport($project_id, $month, $salaryYear)
    {


                $subcon = SalaryHistory::select(
                    'salary_histories.status',
                    DB::raw("COUNT(slh_auto_id) as total_emp"),
                    DB::raw("SUM(slh_total_salary) as total_salary"),
                )
                ->leftjoin('employee_infos', 'salary_histories.emp_auto_id' , '=', 'employee_infos.emp_auto_id')
                ->leftjoin('sponsors', 'employee_infos.sponsor_id' , '=', 'sponsors.spons_id')
                ->where('slh_month', $month)->where('slh_year', $salaryYear)
                ->where('salary_histories.project_id', $project_id)
                ->where('sponsors.owner_name', 'Subcon')
            // ->where('employee_infos.sponsor_id', $sponsor_id)
                ->groupBy("salary_histories.status")
                ->orderBy('salary_histories.status','ASC')
                ->get();


                $asloob = SalaryHistory::select(
                    'salary_histories.status',
                    DB::raw("COUNT(slh_auto_id) as total_emp"),
                    DB::raw("SUM(slh_total_salary) as total_salary"),
                )
                ->leftjoin('employee_infos', 'salary_histories.emp_auto_id' , '=', 'employee_infos.emp_auto_id')
                ->leftjoin('sponsors', 'employee_infos.sponsor_id' , '=', 'sponsors.spons_id')
                ->where('slh_month', $month)->where('slh_year', $salaryYear)
                ->where('salary_histories.project_id', $project_id)
                ->where('sponsors.owner_name', 'Asloob')
            // ->where('employee_infos.sponsor_id', $sponsor_id)
                ->groupBy("salary_histories.status")
                ->orderBy('salary_histories.status','ASC')
                ->get();

            // $total_salary = SalaryHistory::select(
            //     'salary_histories.status',
            //     DB::raw("COUNT(slh_auto_id) as total_emp"),
            //     DB::raw("SUM(slh_total_salary) as total_salary"), //slh_all_include_amount
            //  )
            // ->where('slh_month', $month)->where('slh_year', $salaryYear)
            // ->where('salary_histories.project_id', $project_id)
            // ->groupBy("salary_histories.status")
            // ->orderBy("salary_histories.status","ASC")
            // ->get();
             return [$subcon,$asloob];
    }




    public function getSalaryStatusAsPaidByDateAndWhoUpdatedSummaryReport($project_id_list, $month, $year,$is_hourly_emp,$emp_type_ids, $updated_by_ids,$updated_date)
    {
      //  dd($project_id_list, $month, $year,$is_hourly_emp,$emp_type_ids, $updated_by,$updated_date);

      if(count($emp_type_ids) == 0){

             return  SalaryHistory::select(
                DB::raw('DATE(salary_histories.updated_at) as updated_date'),
                'salary_histories.updated_by',
                'users.name as updated_by_name',
                DB::raw("COUNT(slh_auto_id) as total_emp"),
                DB::raw("SUM(slh_total_salary) as total_salary"),
             )
            ->leftjoin('employee_infos', 'salary_histories.emp_auto_id' , '=', 'employee_infos.emp_auto_id')
            ->leftjoin('users', 'salary_histories.updated_by' , '=', 'users.id')
            ->where('slh_month', $month)->where('slh_year', $year)
            ->whereIn('salary_histories.project_id', $project_id_list)
           ->whereIn('salary_histories.updated_by', $updated_by_ids)
           ->where('salary_histories.Status', 1) // paid status
           ->whereDate('salary_histories.updated_at', $updated_date)
           ->groupByRaw('DATE(salary_histories.updated_at)')
           ->groupBy("salary_histories.updated_by")
           ->groupBy("updated_by_name")
           ->get();

      }else {

            return  SalaryHistory::select(
                DB::raw('DATE(salary_histories.updated_at) as updated_date'),
                'salary_histories.updated_by',
                'users.name as updated_by_name',
                DB::raw("COUNT(slh_auto_id) as total_emp"),
                DB::raw("SUM(slh_total_salary) as total_salary"),
             )
            ->leftjoin('employee_infos', 'salary_histories.emp_auto_id' , '=', 'employee_infos.emp_auto_id')
            ->leftjoin('users', 'salary_histories.updated_by' , '=', 'users.id')
            ->where('slh_month', $month)->where('slh_year', $year)
            ->whereIn('salary_histories.project_id', $project_id_list)
            ->where('employee_infos.hourly_employee', $is_hourly_emp)
            ->whereIn('employee_infos.employee_type', $emp_type_ids)
            ->whereIn('salary_histories.updated_by', $updated_by_ids)
            ->whereDate('salary_histories.updated_at', $updated_date)
            ->where('salary_histories.Status', 1) // paid status
            ->groupByRaw('DATE(salary_histories.updated_at)')
            ->groupBy("salary_histories.updated_by")
            ->groupBy("updated_by_name")
            ->get();

      }
    }

    public function getSalaryStatusAsPaidByDateAndWhoUpdatedEmployeeListReport($project_id_list, $month, $year,$is_hourly_emp,$emp_type_ids, $updated_by_ids,$updated_date)
    {

      //  if(count($emp_type_ids) == 0){
            /// all emp type ids
            return   EmployeeInfo::select('employee_infos.*','sponsors.spons_name','employee_categories.catg_name','countries.country_name','monthly_work_histories.paid_leave','job_statuses.title','salary_histories.*')
                    //->where('employee_infos.hourly_employee', $hourly_employee)
                    ->where('salary_histories.slh_month', $month)
                    ->where('salary_histories.slh_year', $year)
                    ->where('monthly_work_histories.month_id', $month)->where('monthly_work_histories.year_id', $year)
                    ->where('salary_histories.Status', 1) // paid status
                    ->whereIn('salary_histories.updated_by', $updated_by_ids)
                    ->whereDate('salary_histories.updated_at', $updated_date)
                    ->whereIn('salary_histories.project_id', $project_id_list)
                    ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                    ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                    ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                    ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                    ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
                     ->leftjoin('monthly_work_histories', 'salary_histories.emp_auto_id', '=', 'monthly_work_histories.emp_id')
                    ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
                    ->orderBy('employee_id')
                    ->get();

       // }

    }






     /*
     ==========================================================================
     ================ Employee Salary Related Summation =======================
     ==========================================================================
    */


    public function getAnEmployeeJoiningMonthAllIncludedTotalSalaryAmount($emp_auto_id,$is_hourly_emp)
    {
       $arecord = DB::select('CALL getAnEmployeeJoiningSalaryAllIncludedByEmpAutoId(?)',array($emp_auto_id));
       return count($arecord) > 0 ? ($arecord[0]->basic_amount == 0 ? $arecord[0]->hourly_rent : $arecord[0]->total_salary) : 0;
     //  return count($arecord) > 0 ? ($is_hourly_emp ? $arecord[0]->hourly_rent : $arecord[0]->total_salary) : 0;
    }

    public function getAnEmployeeSalaryTotalAmount($employeeId, $year)
    {
        return  SalaryHistory::where('emp_auto_id', $employeeId)->where('slh_year', $year)->sum('slh_total_salary');
    }
    public function getAnEmployeeMonltySalaryTotalAmount($emp_auto_id, $month, $salaryYear)
    {
        $record =  SalaryHistory::where('slh_month', $month)->where('emp_auto_id', $emp_auto_id)->where('slh_year', $salaryYear)->sum('slh_total_salary');
        if ($record == null) {
            return 0;
        } else {
            $record->slh_total_salary;
        }
    }
    public function getAnEmployeeMonltySalaryIqamaAmount($emp_auto_id, $month, $salaryYear)
    {
         return SalaryHistory::where('slh_month', $month)->where('emp_auto_id', $emp_auto_id)->where('slh_year', $salaryYear)->sum('slh_iqama_advance');

    }

    public function getAnEmployeeTotalUnPaidSalary($employeeId, $year)
    {
        if ($year == null || $year == '') {
            return SalaryHistory::where('emp_auto_id', $employeeId)
                ->where('Status', 0)
                ->sum('slh_total_salary');
        } else {
            return SalaryHistory::where('emp_auto_id', $employeeId)
                ->where('slh_year', $year)
                ->where('Status', 0)
                ->sum('slh_total_salary');
        }
    }


     public function getAnEmployeeTotalAmountOfSautiTaxDeductionFromSalaryTotalAdvance($employeeId,$year)
    {
        if( is_null($year) == true){
            return   SalaryHistory::where('emp_auto_id', $employeeId)
            ->sum('slh_saudi_tax');
        }else {
        return  SalaryHistory::where('emp_auto_id', $employeeId)
                            ->where('slh_year', $year)
                            ->sum('slh_saudi_tax');
        }
    }

    public function TotalIqamaRenewal($employeeId, $year)
    {
        return $employeeTotalSalarySummary = SalaryHistory::where('emp_auto_id', $employeeId)->where('slh_year', $year)->sum('slh_iqama_advance');
    }

      public function getTotalAmountOfIqamaExpenseDeductionFromSalary($employeeId,$salary_status)
    {
        if( is_null($salary_status) == false){
            return   SalaryHistory::where('emp_auto_id', $employeeId)->where('salary_histories.Status', $salary_status)->sum('slh_iqama_advance');
        }else {
         return   SalaryHistory::where('emp_auto_id', $employeeId)->sum('slh_iqama_advance');
        }
    }

    public function getTotalAmountOfIqamaRenewalDeductionFromSalaryBySponsorId($sponsor_id,$month,$year)
    {

        if( is_null($sponsor_id)){
            return   SalaryHistory::where('slh_month', $month)->where('slh_year', $year)
            ->leftjoin('employee_infos', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
             ->sum('slh_iqama_advance');

        }else {
            return   SalaryHistory::where('slh_month', $month)->where('slh_year', $year)
             ->where('employee_infos.sponsor_id', $sponsor_id)
            ->leftjoin('employee_infos', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
             ->sum('slh_iqama_advance');
         }
    }

    // public function TotalContribution($employeeId, $year)
    // {
    //     return $employeeTotalSalarySummary = SalaryHistory::where('emp_auto_id', $employeeId)->where('slh_year', $year)->sum('slh_cpf_contribution');
    // }

    public function getTotalAmountOfCPFContributionFromSalary($employeeId,$year)
    {
         if(is_null($year)){
             return  SalaryHistory::where('emp_auto_id', $employeeId)->sum('slh_cpf_contribution');
         }else {
            return  SalaryHistory::where('emp_auto_id', $employeeId)->where('slh_year', $salaryYear)->sum('slh_cpf_contribution');
         }

    }

    public function TotalHours($employeeId, $year)
    {
        return $employeeTotalSalarySummary = SalaryHistory::where('emp_auto_id', $employeeId)->where('slh_year', $year)->sum('slh_total_hours');
    }

    public function slh_saudi_tax($employeeId, $year)
    {
        return $employeeTotalSalarySummary = SalaryHistory::where('emp_auto_id', $employeeId)->where('slh_year', $year)->sum('slh_saudi_tax');
    }

    public function getSalarySaudiTaxTotalAmount($month, $salaryYear, $salaryStatus)
    {
        return  $totalSaudiTax = SalaryHistory::where('slh_month', $month)->where('slh_year', $salaryYear)->where('salary_histories.Status', $salaryStatus)->sum('slh_saudi_tax');
    }

    // Month, Yearly Paid and Unpaid Salary Total Amount

    public function getSalaryTotalAmount($project_id, $month, $salaryYear, $salaryStatus)
    {
        if ($project_id <= 0) {
            return $allSalaryAmount = SalaryHistory::where('slh_month', $month)->where('slh_year', $salaryYear)->where('salary_histories.Status', $salaryStatus)->sum('slh_total_salary');
        } else {
            return $allSalaryAmount = SalaryHistory::where('slh_month', $month)->where('slh_year', $salaryYear)
                ->where('salary_histories.Status', $salaryStatus)->where('project_id', $project_id)->sum('slh_total_salary');
        }
    }


    public function getSalaryIqamaAdvanceTotalAmount($month, $salaryYear, $salaryStatus)
    {
        return  $iqamaAmount = SalaryHistory::where('slh_month', $month)->where('slh_year', $salaryYear)->where('salary_histories.Status', $salaryStatus)->sum('slh_iqama_advance');
    }

    public function getTotalAmountOfAdvanceDeductionFromSalary($salaryYear,$month){
        return $amount = SalaryHistory::
         where('slh_month', $month)->where('slh_year', $salaryYear)
         //->where('salary_histories.Status', $salaryStatus)
         ->sum('slh_other_advance');
     }

     public function getTotalAmountOfAdvanceDeductionFromSalaryByYearToYear($fromYear){
        return SalaryHistory::where('slh_year',$fromYear)->sum('slh_other_advance');
     }

    public function getSalaryMonthTotalHours($project_id, $month, $salaryYear, $salaryStatus)
    {
        if ($project_id <= 0) {

            return     $totalHours = SalaryHistory::where('slh_month', $month)->where('slh_year', $salaryYear)
                ->where('salary_histories.Status', $salaryStatus)->sum('slh_total_hours');
        } else {
            return     $totalHours = SalaryHistory::where('slh_month', $month)->where('slh_year', $salaryYear)
                ->where('salary_histories.Status', $salaryStatus)->where('project_id', $project_id)->sum('slh_total_hours');
        }
    }

 public function getMonthlyTotalHoursByProject($project_id, $month, $salaryYear)
    {
        if ($project_id <= 0) {

            return     $totalHours = SalaryHistory::where('slh_month', $month)->where('slh_year', $salaryYear)
                        ->sum('slh_total_hours');
        } else {
            return     $totalHours = SalaryHistory::where('slh_month', $month)->where('slh_year', $salaryYear)
                        ->where('project_id', $project_id)->sum('slh_total_hours');
        }
    }

    public function getMonthlyTotalOverTimeHoursByProject($project_id, $month, $salaryYear)
    {
        if ($project_id <= 0) {
            return  SalaryHistory::where('slh_month', $month)->where('slh_year', $salaryYear)->sum('slh_total_overtime');
        } else {
            return   SalaryHistory::where('slh_month', $month)->where('slh_year', $salaryYear)
                ->where('project_id', $project_id)->sum('slh_total_overtime');
        }
    }


    public function getSalaryMonthTotalOvertimeHours($project_id, $month, $salaryYear, $salaryStatus)
    {
        if ($project_id <= 0) {
            return  SalaryHistory::where('slh_month', $month)->where('slh_year', $salaryYear)->where('salary_histories.Status', $salaryStatus)->sum('slh_total_overtime');
        } else {
            return   SalaryHistory::where('slh_month', $month)->where('slh_year', $salaryYear)
                ->where('salary_histories.Status', $salaryStatus)->where('project_id', $project_id)->sum('slh_total_overtime');
        }
    }
    public function getSalaryMonthTotalOvertimeAmount($month, $salaryYear, $salaryStatus)
    {
        return SalaryHistory::where('slh_month', $month)->where('slh_year', $salaryYear)->where('salary_histories.Status', $salaryStatus)->sum('slh_overtime_amount');
    }
    public function getSalaryMonthFoodAllowanceTotalAmount($month, $salaryYear, $salaryStatus)
    {
        return   SalaryHistory::where('slh_month', $month)->where('slh_year', $salaryYear)->where('salary_histories.Status', $salaryStatus)->sum('food_allowance');
    }
    public function getSalaryMonthEmployeeCPFTotalAmount($month, $salaryYear, $salaryStatus)
    {
        return     $totalContribution = SalaryHistory::where('slh_month', $month)->where('slh_year', $salaryYear)->where('salary_histories.Status', $salaryStatus)->sum('slh_company_contribution');
    }

    public function getSalaryMonthOtherAdvanceTotalAmount($month, $salaryYear, $salaryStatus)
    {
        return  $totalOtherAdvance = SalaryHistory::where('slh_month', $month)->where('slh_year', $salaryYear)->where('salary_histories.Status', $salaryStatus)->sum('slh_other_advance');
    }


     public function getTotalAmountOfOtherAdvanceDeductionFromSalary($employeeId,$salary_status)
    {
        if( is_null($salary_status) == false){
            return   SalaryHistory::where('emp_auto_id', $employeeId)->where('salary_histories.Status', $salary_status)->sum('slh_other_advance');
        }else {
         return   SalaryHistory::where('emp_auto_id', $employeeId)->sum('slh_other_advance');
        }
    }



    /*
     ==========================================================================
     ================ Employee Salary History Count Section ===================
     ==========================================================================
    */

    public function countMonthlySalryTotalEmployees($project_id, $month, $salaryYear)
    {
        if (is_null($project_id)) {
            return  SalaryHistory::where('slh_month', $month)->where('slh_year', $salaryYear)
                 ->count('salary_histories.emp_auto_id');
        } else {
            return   SalaryHistory::where('slh_month', $month)->where('slh_year', $salaryYear)
                 ->where('project_id', $project_id)->count('salary_histories.emp_auto_id');
        }
    }
    public function countMonthlySalryTotalBasicSalaryOrHourlyEmployes($project_id, $month, $salaryYear,$is_hourly_employee)
    {
        if (is_null($project_id)) {
            return  SalaryHistory::where('slh_month', $month)->where('slh_year', $salaryYear)
                    ->leftjoin('employee_infos', 'salary_histories.emp_auto_id', '=', 'employee_infos.emp_auto_id')
                    ->where('employee_infos.hourly_employee',$is_hourly_employee)
                    ->count('salary_histories.emp_auto_id');
        } else {
            return  SalaryHistory::where('slh_month', $month)->where('slh_year', $salaryYear)
            ->leftjoin('employee_infos', 'salary_histories.emp_auto_id', '=', 'employee_infos.emp_auto_id')
            ->where('employee_infos.hourly_employee',$is_hourly_employee)
            ->where('salary_histories.project_id', $project_id)
            ->count('salary_histories.emp_auto_id');

        }
    }

    public function countMonthlySalryPaidTotalEmployes($project_id, $month, $salaryYear)
    {
        if (is_null($project_id)) {
            return  SalaryHistory::where('slh_month', $month)->where('slh_year', $salaryYear)
                    ->where('salary_histories.Status', 1)
                    ->count('salary_histories.emp_auto_id');
        } else {
            return  SalaryHistory::where('slh_month', $month)->where('slh_year', $salaryYear)
            ->where('Status', 1)
            ->where('project_id', $project_id)
            ->count('emp_auto_id');
        }
    }
    public function countMonthlySalryUnPaidTotalEmployes($project_id, $month, $salaryYear)
    {
        if (is_null($project_id)) {
            return  SalaryHistory::where('slh_month', $month)->where('slh_year', $salaryYear)
                    ->where('salary_histories.Status', 0)
                    ->count('salary_histories.emp_auto_id');
        } else {
            return  SalaryHistory::where('slh_month', $month)->where('slh_year', $salaryYear)
            ->where('Status', 0)
            ->where('project_id', $project_id)
            ->count('emp_auto_id');
        }
    }



     /*
     ==========================================================================
     ================ Salary Sheet Upload Related =============================
     ==========================================================================
    */
    public function getAllSalarySheet()
    {
        return $allSheet =  SalarySheetUpload::orderBy('ss_auto_id', 'DESC')->get();
    }

     /*
     ==========================================================================
     ================ Udpate Operation ========================================
     ==========================================================================
    */
    public function updateAnEmployeeMonthlySalaryRecord($salaryHistoryAutoId, $anEmployee, $month, $salaryYear)
    {

        if ($salaryHistoryAutoId > 0) {
            return  $insert = SalaryHistory::where('slh_auto_id', $salaryHistoryAutoId)->update([
                'emp_auto_id' => $anEmployee->emp_auto_id,
                'basic_amount' => $anEmployee->basic_amount,
                'basic_hours' => $anEmployee->basic_hours,
                'house_rent' => $anEmployee->house_rent,
                'hourly_rent' => $anEmployee->hourly_rent,
                'mobile_allowance' => $anEmployee->mobile_allowance,
                'medical_allowance' => $anEmployee->medical_allowance,
                'local_travel_allowance' => $anEmployee->local_travel_allowance,
                'conveyance_allowance' => $anEmployee->conveyance_allowance,
                'food_allowance' =>  $anEmployee->food_allowance,
                'others' => $anEmployee->others1,
                'slh_total_overtime' => $anEmployee->overtime,
                'slh_overtime_amount' =>  $anEmployee->slh_overtime_amount,
                /* New field in Salary History */
                'slh_total_salary' => $anEmployee->gross_salary,
                'slh_total_hours' => $anEmployee->total_hours,
                'slh_total_working_days' => $anEmployee->total_work_day,
                'slh_month' => $month,
                'slh_year' => $salaryYear,
                'slh_cpf_contribution' => $anEmployee->cpf_contribution,
                'slh_saudi_tax' => $anEmployee->saudi_tax,
                'slh_company_contribution' => 0,
                'slh_iqama_advance' => $anEmployee->iqama_adv_inst_amount,
                'slh_other_advance' => $anEmployee->other_adv_inst_amount,
                'slh_bonus_amount' => $anEmployee->bonus_amount,
                'slh_food_deduction' => $anEmployee->slh_food_deduction,
                'partial_paid_amount' =>$anEmployee->partial_paid_amount,
                'slh_all_include_amount' => $anEmployee->slh_all_include_amount,
                'project_id' => $anEmployee->work_project_id,
                'multProject' => $anEmployee->work_multi_project,
                'slh_salary_date' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),

            ]);
        } else {
            return $insert = SalaryHistory::insertGetId([
                'emp_auto_id' => $anEmployee->emp_auto_id,
                'basic_amount' => $anEmployee->basic_amount,
                'basic_hours' => $anEmployee->basic_hours,
                'house_rent' => $anEmployee->house_rent,
                'hourly_rent' => $anEmployee->hourly_rent,
                'mobile_allowance' => $anEmployee->mobile_allowance,
                'medical_allowance' => $anEmployee->medical_allowance,
                'local_travel_allowance' => $anEmployee->local_travel_allowance,
                'conveyance_allowance' => $anEmployee->conveyance_allowance,
                'food_allowance' => $anEmployee->food_allowance,
                'others' => $anEmployee->others1,
                'slh_total_overtime' => $anEmployee->overtime,
                'slh_overtime_amount' => $anEmployee->slh_overtime_amount,
                'slh_total_salary' => $anEmployee->gross_salary,
                'slh_total_hours' => $anEmployee->total_hours,
                'slh_total_working_days' => $anEmployee->total_work_day,
                'slh_month' => $month,
                'slh_year' => $salaryYear,
                'slh_cpf_contribution' => $anEmployee->cpf_contribution,
                'slh_saudi_tax' => $anEmployee->saudi_tax,
                'slh_company_contribution' => 0,
                'slh_iqama_advance' => $anEmployee->iqama_adv_inst_amount,
                'slh_other_advance' => $anEmployee->other_adv_inst_amount,
                'slh_bonus_amount' => $anEmployee->bonus_amount,
                'slh_food_deduction' => $anEmployee->slh_food_deduction,
                'partial_paid_amount' =>$anEmployee->partial_paid_amount,
                'slh_all_include_amount' => $anEmployee->slh_all_include_amount,
                'slh_salary_date' => Carbon::now(),
                'Status' => 0,
                'project_id' => $anEmployee->work_project_id,
                'multProject' => $anEmployee->work_multi_project,
                'branch_office_id' => $anEmployee->branch_office_id,
                'created_at' => Carbon::now(),
            ]);
        }
    }


    public function updateAnEmployeeMonthlySalaryRecordUpdateSalaryStatusAsPaidBySalaryHistoryAutoId($slh_auto_id,$emp_auto_id,$slh_all_include_amount,$new_food_amount,
    $saudi_tax,$new_other_advance,$new_iqama_advance,$new_receivable_total_salary,$updated_by,$salary_paid__status,$catering_amount)
    {
            return  SalaryHistory::where('slh_auto_id', (int)$slh_auto_id)->where('emp_auto_id',(int) $emp_auto_id)->update([
                'food_allowance' =>  $new_food_amount,
                'slh_total_salary' => $new_receivable_total_salary,
                'slh_saudi_tax' => $saudi_tax,
                'slh_iqama_advance' => $new_iqama_advance,
                'slh_other_advance' => $new_other_advance,
                'slh_food_deduction' => $catering_amount,
                'slh_all_include_amount' => $slh_all_include_amount,
                'Status' => (int) $salary_paid__status,
                'updated_at' => Carbon::now(),
                'updated_by' => $updated_by

            ]);
    }


    public function calculateFoodAllowance($totalWorkingDay, $monthlyFoodAllowance)
    {

        if ($monthlyFoodAllowance > 0) {

            if($totalWorkingDay >= 1 && $totalWorkingDay <= 30)
              return (($monthlyFoodAllowance / 30) * $totalWorkingDay);
            else if($totalWorkingDay > 30)
              return 300;
            else
            return 0;

            /*
            if ($totalWorkingDay >= 24) {
                return $monthlyFoodAllowance;
            } elseif ($totalWorkingDay >= 18) {
                return (($monthlyFoodAllowance / 30) * ($totalWorkingDay + 3));
            } elseif ($totalWorkingDay >= 12) {
                return (($monthlyFoodAllowance / 30) * ($totalWorkingDay + 2));
            } elseif ($totalWorkingDay >= 6) {
                return (($monthlyFoodAllowance / 30) * ($totalWorkingDay + 1));
            } else {
                return (($monthlyFoodAllowance / 30) * $totalWorkingDay);
            } */

        } else
            return 0;
    }
    public function calculateOvertimeHoursAndAmount($anEmployee)
    {
        $over_amount = 0;
        $total_amount = 0;

        if ($anEmployee->hourly_employee == true) { // direct emp hourly
            $over_amount = ($anEmployee->overtime * $anEmployee->hourly_rent);
            $total_amount = ($anEmployee->total_hours * $anEmployee->hourly_rent) + $over_amount;
        } elseif ($anEmployee->emp_type_id == 1 ){ // $anEmployee->basic_hours > 0 && $anEmployee->basic_amount > 0) { // direct emp basic
            $over_amount = ($anEmployee->overtime * ($anEmployee->hourly_rent * 1.5));
            $total_amount = (($anEmployee->basic_amount / 30) * $anEmployee->total_work_day)  + $over_amount;
        } elseif ( $anEmployee->emp_type_id == 2 ){ //$anEmployee->basic_amount > 0) {  // indirect emp
            $anEmployee->allOthers = ($anEmployee->house_rent + $anEmployee->conveyance_allowance + $anEmployee->others1);
            $over_amount = ($anEmployee->overtime * $anEmployee->hourly_rent);
            $total_amount = (($anEmployee->basic_amount / 30)) * ($anEmployee->total_work_day) + $over_amount;
        }

        $anEmployee->slh_overtime_amount =  $over_amount;
        $anEmployee->tem_total_amount =  $total_amount;
        return $anEmployee;
    }


    public function calculateAnEmployeeMultipleProjectSalaryForAMonth($anEmployee,$arecord)
    {
         $arecord->ot_amount = 0;
         $arecord->other_amount = 0;
         $arecord->food_amount = 0;

         if ($anEmployee->hourly_employee == true ) {

            $arecord->ot_amount = round( $arecord->total_overtime * $anEmployee->hourly_rent,2);
            $arecord->total_amount = round( ($arecord->total_hour * $anEmployee->hourly_rent) + $arecord->ot_amount,2);

        } elseif ( $anEmployee->emp_type_id == 1 ) {
              // direct basic salary
            $other_amount = ($anEmployee->house_rent + $anEmployee->conveyance_allowance + $anEmployee->mobile_allowance  + $anEmployee->medical_allowance + $anEmployee->others1 + $anEmployee->local_travel_allowance + $anEmployee->bonus_amount);
              if($other_amount > 0 && $anEmployee->total_work_day > 0){
                $arecord->other_amount = round((($other_amount / $anEmployee->total_work_day) * $arecord->total_day),2);
              }
            $arecord->ot_amount = round( ($arecord->total_overtime * ($anEmployee->hourly_rent * 1.5)),2);
            $arecord->total_amount = round( (($anEmployee->basic_amount / 30) * $arecord->total_day)  + $arecord->ot_amount + $arecord->other_amount,2);


        } elseif ( $anEmployee->emp_type_id == 2) {

          // indirect basic salary
          $other_amount = ($anEmployee->house_rent + $anEmployee->conveyance_allowance + $anEmployee->mobile_allowance  + $anEmployee->medical_allowance + $anEmployee->others1 + $anEmployee->local_travel_allowance + $anEmployee->bonus_amount);
          if($other_amount > 0 && $anEmployee->total_work_day > 0){
            $arecord->other_amount = round((($other_amount / $anEmployee->total_work_day) * $arecord->total_day),2);
          }
            $arecord->ot_amount = round( ($arecord->total_overtime * $anEmployee->hourly_rent),2);
            $arecord->total_amount = round( (($anEmployee->basic_amount / 30)) * ($arecord->total_day) + $arecord->ot_amount + $arecord->other_amount,2);
        }

            if( ((float)$anEmployee->food_allowance) > 0 && ((int)$anEmployee->total_work_day) > 0) {
              $arecord->food_amount = round((($anEmployee->food_allowance / $anEmployee->total_work_day) * $arecord->total_day),2);
            }
         $arecord->total_amount += $arecord->food_amount;
         return $arecord;

        // $arecord->ot_amount = 0;
        // $arecord->other_amount = 0;

        // if ($anEmployee->hourly_employee == true ) {

        //     $arecord->ot_amount = $arecord->total_overtime * $anEmployee->hourly_rent;
        //     $arecord->total_amount = ($arecord->total_hour * $anEmployee->hourly_rent) + $arecord->ot_amount;

        // } elseif ( $anEmployee->emp_type_id == 1 ) {
        //       // direct basic salary
        //     $other_amount = ($anEmployee->house_rent + $anEmployee->conveyance_allowance + $anEmployee->mobile_allowance  + $anEmployee->medical_allowance + $anEmployee->others1 + $anEmployee->local_travel_allowance + $anEmployee->bonus_amount);
        //       if($other_amount > 0 && $anEmployee->total_work_day > 0){
        //         $arecord->other_amount = round((($other_amount / $anEmployee->total_work_day) * $arecord->total_day),2);
        //       }
        //     $arecord->ot_amount = ($arecord->total_overtime * ($anEmployee->hourly_rent * 1.5));
        //     $arecord->total_amount = (($anEmployee->basic_amount / 30) * $arecord->total_day)  + $arecord->ot_amount + $arecord->other_amount;


        // } elseif ( $anEmployee->emp_type_id == 2) {

        //   // indirect basic salary
        //   $other_amount = ($anEmployee->house_rent + $anEmployee->conveyance_allowance + $anEmployee->mobile_allowance  + $anEmployee->medical_allowance + $anEmployee->others1 + $anEmployee->local_travel_allowance + $anEmployee->bonus_amount);
        //   if($other_amount > 0 && $anEmployee->total_work_day > 0){
        //     $arecord->other_amount = round((($other_amount / $anEmployee->total_work_day) * $arecord->total_day),2);
        //   }
        //     $arecord->ot_amount = ($arecord->total_overtime * $anEmployee->hourly_rent);
        //     $arecord->total_amount = (($anEmployee->basic_amount / 30)) * ($arecord->total_day) + $arecord->ot_amount + $arecord->other_amount;
        // }
        //  return $arecord;

    }

    // this is called from subcontractor report from salary report menu
    public function calculateAnEmployeeMultipleProjectSalaryFromSalaryHistoryTableForAMonth($anEmployee,$arecord)
    {

          $arecord->ot_amount = 0;
        $arecord->other_amount = 0;
        $arecord->food_amount = 0;

        if ($anEmployee->hourly_employee == true ) {

            $arecord->ot_amount =round( $arecord->total_overtime * $anEmployee->hourly_rent,2);
            $arecord->total_amount =round( ($arecord->total_hour * $anEmployee->hourly_rent) + $arecord->ot_amount,2);

        } elseif ( $anEmployee->emp_type_id == 1 ) {
            // direct basic emp
             $other_amount = ($anEmployee->house_rent + $anEmployee->conveyance_allowance + $anEmployee->mobile_allowance  + $anEmployee->medical_allowance + $anEmployee->others + $anEmployee->local_travel_allowance + $anEmployee->slh_bonus_amount);

            if($other_amount > 0 && $anEmployee->slh_total_working_days > 0){
                $arecord->other_amount = round((($other_amount / $anEmployee->slh_total_working_days) * $arecord->total_day),2);
            }

            $arecord->ot_amount = round( ($arecord->total_overtime * ($anEmployee->hourly_rent * 1.5)),2);
            $arecord->total_amount =round( (($anEmployee->basic_amount / 30) * $arecord->total_day)  + $arecord->ot_amount + $arecord->other_amount, 2);

        } elseif ( $anEmployee->emp_type_id == 2) {


            $other_amount = ($anEmployee->house_rent + $anEmployee->conveyance_allowance + $anEmployee->mobile_allowance  + $anEmployee->medical_allowance + $anEmployee->others + $anEmployee->local_travel_allowance + $anEmployee->slh_bonus_amount);

            if($other_amount > 0 && $anEmployee->slh_total_working_days > 0){
                $arecord->other_amount = round((($other_amount / $anEmployee->slh_total_working_days) * $arecord->total_day),2);
            }
            $arecord->ot_amount = round(($arecord->total_overtime * $anEmployee->hourly_rent),2);
            $arecord->total_amount =round( (($anEmployee->basic_amount / 30)) * ($arecord->total_day) + $arecord->ot_amount + $arecord->other_amount,2);
        }
         if( ((float)$anEmployee->food_allowance) > 0 && ((int)$anEmployee->slh_total_working_days) > 0) {
              $arecord->food_amount = round((($anEmployee->food_allowance / $anEmployee->slh_total_working_days) * $arecord->total_day),2);
         }

         $arecord->total_amount += $arecord->food_amount;
         return $arecord;


        // $arecord->ot_amount = 0;
        // $arecord->other_amount = 0;
        // $arecord->food_amount = 0;

        // if ($anEmployee->hourly_employee == true ) {

        //     $arecord->ot_amount = $arecord->total_overtime * $anEmployee->hourly_rent;
        //     $arecord->total_amount = ($arecord->total_hour * $anEmployee->hourly_rent) + $arecord->ot_amount;

        // } elseif ( $anEmployee->emp_type_id == 1 ) {
        //     // direct basic emp
        //      $other_amount = ($anEmployee->house_rent + $anEmployee->conveyance_allowance + $anEmployee->mobile_allowance  + $anEmployee->medical_allowance + $anEmployee->others + $anEmployee->local_travel_allowance + $anEmployee->slh_bonus_amount);

        //     if($other_amount > 0 && $anEmployee->slh_total_working_days > 0){
        //         $arecord->other_amount = round((($other_amount / $anEmployee->slh_total_working_days) * $arecord->total_day),2);
        //     }

        //     $arecord->ot_amount = ($arecord->total_overtime * ($anEmployee->hourly_rent * 1.5));
        //     $arecord->total_amount = (($anEmployee->basic_amount / 30) * $arecord->total_day)  + $arecord->ot_amount + $arecord->other_amount;

        // } elseif ( $anEmployee->emp_type_id == 2) {


        //     $other_amount = ($anEmployee->house_rent + $anEmployee->conveyance_allowance + $anEmployee->mobile_allowance  + $anEmployee->medical_allowance + $anEmployee->others + $anEmployee->local_travel_allowance + $anEmployee->slh_bonus_amount);

        //     if($other_amount > 0 && $anEmployee->slh_total_working_days > 0){
        //         $arecord->other_amount = round((($other_amount / $anEmployee->slh_total_working_days) * $arecord->total_day),2);
        //     }
        //     $arecord->ot_amount = ($arecord->total_overtime * $anEmployee->hourly_rent);
        //     $arecord->total_amount = (($anEmployee->basic_amount / 30)) * ($arecord->total_day) + $arecord->ot_amount + $arecord->other_amount;
        // }
        //  if( ((float)$anEmployee->food_allowance) > 0 && ((int)$anEmployee->slh_total_working_days) > 0) {
        //       $arecord->food_amount = round((($anEmployee->food_allowance / $anEmployee->slh_total_working_days) * $arecord->total_day),2);
        //  }

        //  $arecord->total_amount += $arecord->food_amount;
        //  return $arecord;

    }



    public function calculateAProjectPaidUnPaidSalarySummaryForReport($projectId, $month, $salaryYear)
    {

        return SalaryHistory::select(
            'Status',
            DB::raw("COUNT(slh_auto_id) as total_emp"),
            DB::raw("SUM(slh_total_salary) as total_salary"),
            DB::raw("SUM(slh_saudi_tax) as total_saudi_tax"),
            DB::raw("SUM(slh_iqama_advance) as total_iqama_adv"),
            DB::raw("SUM(slh_other_advance) as total_other_adv"),
            DB::raw("SUM(slh_cpf_contribution) as total_contribution")
        )->groupBy("Status")->orderBy('Status', 'ASC')->where('slh_month', $month)->where('slh_year', $salaryYear)
            ->where('salary_histories.project_id', $projectId)->get();

    }


    public function calculateAProjectAllIncludedSalaryTotalAmountForAMonthAndYear($projectId, $month, $salaryYear)
    {
            return SalaryHistory::select(
                DB::raw("COUNT(slh_auto_id) as total_emp"),
                DB::raw("SUM(slh_total_salary) as total_salary"),
                DB::raw("SUM(slh_saudi_tax) as total_saudi_tax"),
                DB::raw("SUM(slh_iqama_advance) as total_iqama_adv"),
                DB::raw("SUM(slh_other_advance) as total_other_adv"),
                DB::raw("SUM(slh_food_deduction) as slh_food_deduction"),
                DB::raw("SUM(slh_cpf_contribution) as total_contribution"),
                DB::raw("SUM(slh_all_include_amount) as all_included_total_amount")
            )->where('slh_month', $month)->where('slh_year', $salaryYear)
            ->where('salary_histories.project_id', $projectId)->first();


    }
    public function calculateAProjectFromJanToDecAllIncludedSalaryYearlyStatment($projectId, $salaryYear)
    {
       return   DB::select('call aproject_from_jan_to_dec_yearly_all_included_total_salary(?,?)',array($salaryYear,$projectId));
    }

    public function getSalarytSheetWiseTotalSalarySummaryByMultipleProjectMonthAndYear($project_id_list, $month, $salaryYear)
    {
        if ($project_id_list == null) {
            return  SalaryHistory::select(
                'status',
                DB::raw("COUNT(slh_auto_id) as total_emp"),
                DB::raw("SUM(slh_total_salary) as total_salary"),
                DB::raw("SUM(slh_saudi_tax) as total_saudi_tax"),
                DB::raw("SUM(slh_iqama_advance) as total_iqama_adv"),
                DB::raw("SUM(slh_other_advance) as total_other_adv"),
                DB::raw("SUM(slh_cpf_contribution) as total_contribution")
            )->groupBy("Status")->where('slh_month', $month)->where('slh_year', $salaryYear)->get();
        } else {
            return SalaryHistory::select(
                'status',
                DB::raw("COUNT(slh_auto_id) as total_emp"),
                DB::raw("SUM(slh_total_salary) as total_salary"),
                DB::raw("SUM(slh_saudi_tax) as total_saudi_tax"),
                DB::raw("SUM(slh_iqama_advance) as total_iqama_adv"),
                DB::raw("SUM(slh_other_advance) as total_other_adv"),
                DB::raw("SUM(slh_cpf_contribution) as total_contribution")
            )->groupBy("Status")->where('slh_month', $month)->where('slh_year', $salaryYear)
                ->whereIn('salary_histories.project_id', $project_id_list)->get();
        }
    }

   public function getSalarytSheetWiseTotalSalaryWithDeductionSummaryByMultipleProjectMonthAndYear($project_id_list, $month, $salaryYear)
    {
        if ($project_id_list == null) {
            return  SalaryHistory::select(
                'project_id',
                DB::raw("COUNT(slh_auto_id) as total_emp"),
                DB::raw("SUM(slh_total_salary) as total_salary"),
                DB::raw("SUM(slh_saudi_tax) as total_saudi_tax"),
                DB::raw("SUM(slh_iqama_advance) as total_iqama_advance"),
                DB::raw("SUM(slh_other_advance) as total_other_advance"),
                DB::raw("SUM(slh_cpf_contribution) as total_contribution")
            )
            ->groupBy("project_id")->where('slh_month', $month)->where('slh_year', $salaryYear)->get();
       }
        else {
                return  SalaryHistory::select(
                      'project_id',
                      DB::raw("COUNT(slh_auto_id) as total_emp"),
                      DB::raw("SUM(slh_total_salary) as total_salary"),
                      DB::raw("SUM(slh_saudi_tax) as total_saudi_tax"),
                      DB::raw("SUM(slh_iqama_advance) as total_iqama_advance"),
                      DB::raw("SUM(slh_other_advance) as total_other_advance"),
                      DB::raw("SUM(slh_cpf_contribution) as total_contribution")
                  )
                  ->whereIn('project_id', $project_id_list)->groupBy("project_id")->where('slh_month', $month)->where('slh_year', $salaryYear)->get();
        }
    }


    public function updateEmployeeSalaryStatusAsPaidAndPaymentMethod($slh_auto_id,$slh_paid_method,$updated_by)
    {

        return SalaryHistory::where('slh_auto_id', $slh_auto_id)->update([
            'Status' => 1,
            'slh_paid_method' => $slh_paid_method,
            'updated_at' => Carbon::now(),
            'updated_by' =>$updated_by
        ]);
    }

    public function updateEmployeeSalaryStatusAsPaid($slh_auto_id,$updated_by)
    {
        return SalaryHistory::where('slh_auto_id', $slh_auto_id)->update([
            'Status' => 1,
            'updated_at' => Carbon::now(),
            'updated_by' =>$updated_by
        ]);
    }

    public function updateEmployeeSalaryStatusAsCashPaid($slh_auto_id,$updated_by)
    {
        return SalaryHistory::where('slh_auto_id', $slh_auto_id)->update([
            'Status' => 1,
            'slh_paid_method' => NULL, // cash paid
            'updated_at' => Carbon::now(),
            'updated_by' =>$updated_by
        ]);
    }

    public function updateEmployeeSalaryStatusAsUnPaid($slh_auto_id,$updated_by)
    {
        return SalaryHistory::where('slh_auto_id', $slh_auto_id)->update([
            'Status' => 0,
            'updated_at' => Carbon::now(),
            'updated_by' => $updated_by
        ]);
    }



    /*
     =====================================================================================
     ======== Employee Multiple Project Salary Calculation update in a month =============
     =====================================================================================
    */
    public function updateEmployeeMultipleProjectWorkSalaryAmount($empwh_auto_id, $total_amount, $food_amount, $other_amount,$ot_amount,$update_by)
    {
        return EmployeeMultiProjectWorkHistory::where('empwh_auto_id', $empwh_auto_id)->update([
            'total_amount' => $total_amount,
            'food_amount' =>  $food_amount,
            'other_amount' => $other_amount,
            'ot_amount' => $ot_amount,
            'updated_at' => Carbon::now(),
          //  'update_by_id' => $update_by // off because inserted_by data is updated , we need to create inserted_by column then we can do update
        ]);
    }


    public function updateAnEmployeeMultipleProjectWorkSalaryAmountAtTimeOfProcessingCostControlReport($empwh_auto_id, $total_amount, $food_amount, $other_amount,$ot_amount)
    {
           // total_amount = basic amount + ot_amount + other_amount + food_amount;
        return EmployeeMultiProjectWorkHistory::where('empwh_auto_id', $empwh_auto_id)->update([
            'total_amount' => $total_amount,
            'food_amount' =>  $food_amount,
            'other_amount' => $other_amount,
            'ot_amount' => $ot_amount

        ]);
    }
    // Actual Salary Expense Report for the project only
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


     /*
     =====================================================================================
     ======================= Employee Bonus System Section ==============================
     =====================================================================================
    */
    public function checkThisEmployeeBonusRecordIsExist($emp_auto_id,$month,$year){
        return EmployeeBonus::where('emp_auto_id',$emp_auto_id)->where('month',$month)->where('year',$year)->count() > 0 ? true:false;
    }
    public function insertEmployeeBonusRecord($emp_auto_id,$bonus_amount,$bonus_type,$month,$year,$remarks,$created_by){
        if($this->checkThisEmployeeBonusRecordIsExist($emp_auto_id,$month,$year)){
            return -1;
        }
        return EmployeeBonus::insertGetId([
            "emp_auto_id" => $emp_auto_id,
            "bonus_type" => $bonus_type,
            "amount" => $bonus_amount,
            "month" => $month,
            "year" => $year,
            "remarks" => $remarks,
            "created_by" =>$created_by,
            "updated_by" =>$created_by,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }

    public function getAnEmployeeBonusRecordByBonusAudoId($bonus_auto_id){
        return EmployeeBonus::where('bonus_auto_id',$bonus_auto_id)->first();
    }
    public function getAnEmployeeBonusRecordsWithEmployeeDetails($emp_auto_id,$month,$year){
        return EmployeeBonus::where('employee_bonus_records.emp_auto_id',$emp_auto_id)
                         ->leftjoin('employee_infos', 'employee_bonus_records.emp_auto_id', '=', 'employee_infos.emp_auto_id')
                         ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                         ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                         ->get();
    }



    public function getAnEmployeeBonusAmountByEmpAutoIdAndMonthYear($emp_auto_id,$month,$year){
        return EmployeeBonus::where('emp_auto_id',$emp_auto_id)
                              ->where('month',$month)->where('year',$year)->sum('amount');
    }
    public function getAnEmployeeBonusRecordsByEmpAutoIdAndMonthYear($emp_auto_id,$month,$year){
        return EmployeeBonus::where('emp_auto_id',$emp_auto_id)
                              ->where('month',$month)->where('year',$year)->get();
    }

    public function getAnEmployeeAllBonusRecordByEmpAutoId($emp_auto_id){
        return EmployeeBonus::where('emp_auto_id',$emp_auto_id)->get();
    }
    public function getAnEmployeeBonusRecordByFiscalYear($emp_auto_id, $start_date,$end_date){
        return EmployeeBonus::where('emp_auto_id',$emp_auto_id)->whereBetween('created_at',[$emp_auto_id, $start_date,$end_date])->get();
    }
    public function getAnEmployeeTotalBonusAmountByEmpAutoIdAndMonthYear($emp_auto_id,$month,$year){
        return EmployeeBonus::where('emp_auto_id',$emp_auto_id)->sum('amount');
    }

    public function deleteAnEmployeeBonusRecordByBonusAutoId($bonus_auto_id){
        return EmployeeBonus::where('bonus_auto_id',$bonus_auto_id)->delete();
    }


    // called from salary report page
    public function processEmployeesBonusdetailsReport($employee_id,$bonus_type, $from_date,$to_date,$branch_office_id){

        return EmployeeBonus::whereBetween('employee_bonus_records.created_at', [$from_date, $to_date])
                    ->leftjoin('employee_infos', 'employee_bonus_records.emp_auto_id', '=', 'employee_infos.emp_auto_id')
                    ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                    ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                    ->orderBy('employee_infos.employee_id', 'ASC')
                    ->where('employee_infos.branch_office_id',$branch_office_id)
                    ->orderBy('employee_bonus_records.created_at','DESC')
                    ->get();
    }
    //called from bonus entry page
    public function getEmployeeBonusRecordsWithEmployeeDetailsByDateToDate($start_date,$end_date){

    $date1 = Carbon::parse($start_date);
    $date2 = Carbon::parse($end_date);

    if ($date1->isSameMonth($date2)) {
        // Both are, for example, October 2023
          return EmployeeBonus::where('employee_bonus_records.month', $date1->month)
                         ->where('employee_bonus_records.month', $date1->year)
                         ->leftjoin('employee_infos', 'employee_bonus_records.emp_auto_id', '=', 'employee_infos.emp_auto_id')
                         ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                         ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                         ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                         ->orderBy('employee_bonus_records.month','DESC')
                         ->orderBy('employee_bonus_records.year','DESC')
                         ->get() ;
    }

        $start_date = Carbon::parse($start_date)->startOfDay();
        $end_date = Carbon::parse($end_date)->endOfDay();
        return EmployeeBonus::whereBetween('employee_bonus_records.created_at', [$start_date, $end_date])
                         ->leftjoin('employee_infos', 'employee_bonus_records.emp_auto_id', '=', 'employee_infos.emp_auto_id')
                         ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                         ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                         ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                         ->orderBy('employee_bonus_records.month','DESC')
                         ->orderBy('employee_bonus_records.year','DESC')
                         ->get() ;

    }





     /*
     =====================================================================================
     ======================= Employee Salary Report Section ==============================
     =====================================================================================
    */

     public function getAllTypesOfEmployeesSalaryReportThoseAreWorkingThisProject($project_id,$month,$year,$salary_status)
    {


         if(is_null($salary_status)){
             return  EmployeeInfo::select('employee_infos.employee_id','employee_infos.emp_auto_id','employee_infos.employee_name','employee_infos.akama_no','employee_infos.salary_status','sponsors.spons_name','countries.country_name',
                'monthly_work_histories.paid_leave','job_statuses.title','employee_categories.catg_name','project_infos.proj_name','salary_histories.*')
                ->where("employee_infos.job_status", 1)
                ->where("employee_infos.project_id", $project_id)
                ->where("salary_histories.slh_month", $month)
                ->where("salary_histories.slh_year", $year)
                 ->where('monthly_work_histories.month_id', $month)->where('monthly_work_histories.year_id', $year)
                ->leftjoin('salary_histories', 'salary_histories.emp_auto_id', '=', 'employee_infos.emp_auto_id')
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->leftjoin('monthly_work_histories', 'salary_histories.emp_auto_id', '=', 'monthly_work_histories.emp_id')
                ->orderBy('employee_infos.employee_id', 'ASC')
                ->get();


        }else {

              return  EmployeeInfo::select('employee_infos.employee_id','employee_infos.emp_auto_id','employee_infos.employee_name','employee_infos.akama_no','employee_infos.salary_status','sponsors.spons_name','countries.country_name',
                    'monthly_work_histories.paid_leave','job_statuses.title','employee_categories.catg_name','project_infos.proj_name','salary_histories.*')
                    ->where("employee_infos.job_status", 1)
                    ->where("employee_infos.project_id", $project_id)
                    ->where("salary_histories.slh_month", $month)
                    ->where("salary_histories.slh_year", $year)
                    ->where("salary_histories.Status", $salary_status)
                    ->where('monthly_work_histories.month_id', $month)->where('monthly_work_histories.year_id', $year)
                    ->leftjoin('salary_histories', 'salary_histories.emp_auto_id', '=', 'employee_infos.emp_auto_id')
                    ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                    ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
                    ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                    ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
                    ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                    ->leftjoin('monthly_work_histories', 'salary_histories.emp_auto_id', '=', 'monthly_work_histories.emp_id')
                    ->orderBy('employee_infos.employee_id', 'ASC')
                    ->get();

        }

    }

    public function getEmployeesSalaryReportThoseAreWorkingThisProject($project_id,$month,$year,$salary_status,$isHourlyEmployee)
    {


          if(is_null($salary_status)){

             return  EmployeeInfo::select('employee_infos.employee_id','employee_infos.emp_auto_id','employee_infos.employee_name','employee_infos.akama_no','employee_infos.salary_status','sponsors.spons_name','countries.country_name',
                'monthly_work_histories.paid_leave','job_statuses.title','employee_categories.catg_name','project_infos.proj_name','salary_histories.*')
            ->where("employee_infos.job_status", 1) // only active employee salary will show
            ->where("employee_infos.project_id", $project_id)
            ->where("employee_infos.hourly_employee", $isHourlyEmployee)
            ->where("salary_histories.slh_month", $month)
            ->where("salary_histories.slh_year", $year)
               ->where('monthly_work_histories.month_id', $month)->where('monthly_work_histories.year_id', $year)
            ->leftjoin('salary_histories', 'salary_histories.emp_auto_id', '=', 'employee_infos.emp_auto_id')
            ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
            ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
            ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
               ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
            ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
             ->leftjoin('monthly_work_histories', 'salary_histories.emp_auto_id', '=', 'monthly_work_histories.emp_id')
            ->orderBy('employee_infos.employee_id', 'ASC')
            ->get();


        }else {
              return  EmployeeInfo::select('employee_infos.employee_id','employee_infos.emp_auto_id','employee_infos.employee_name','employee_infos.akama_no','employee_infos.salary_status','sponsors.spons_name','countries.country_name',
                'monthly_work_histories.paid_leave','job_statuses.title','employee_categories.catg_name','project_infos.proj_name','salary_histories.*')
            ->where("employee_infos.job_status", 1)
            ->where("employee_infos.project_id", $project_id)
            ->where("employee_infos.hourly_employee", $isHourlyEmployee)
            ->where("salary_histories.slh_month", $month)
            ->where("salary_histories.slh_year", $year)
            ->where("salary_histories.Status", $salary_status)
               ->where('monthly_work_histories.month_id', $month)->where('monthly_work_histories.year_id', $year)
            ->leftjoin('salary_histories', 'salary_histories.emp_auto_id', '=', 'employee_infos.emp_auto_id')
            ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
            ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
            ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
               ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
            ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
             ->leftjoin('monthly_work_histories', 'salary_histories.emp_auto_id', '=', 'monthly_work_histories.emp_id')
            ->orderBy('employee_infos.employee_id', 'ASC')
            ->get();

        }

    }


   public function getProjectBaseSalarySummaryAmountReportBaseOnCurrentlyWorkingEmployees($project_id,$month,$year,$salary_status_ids){

            return  EmployeeInfo::select(
                'employee_infos.hourly_employee',
                DB::raw("COUNT(salary_histories.slh_auto_id) as total_emp"),
                DB::raw("SUM(salary_histories.slh_total_salary) as total_salary"),
                DB::raw("SUM(salary_histories.slh_saudi_tax) as total_saudi_tax"),
                DB::raw("SUM(salary_histories.slh_iqama_advance) as total_iqama_adv"),
                DB::raw("SUM(salary_histories.slh_other_advance) as total_other_adv"),
            )
            ->where("employee_infos.job_status", 1)
            ->where("employee_infos.project_id", $project_id)
            ->where("salary_histories.slh_month", $month)
            ->where("salary_histories.slh_year", $year)
            ->whereIn("salary_histories.Status", $salary_status_ids)
            ->groupBy('employee_infos.hourly_employee')
            ->leftjoin('salary_histories', 'salary_histories.emp_auto_id', '=', 'employee_infos.emp_auto_id')
            ->orderBy('employee_infos.hourly_employee', 'ASC')
            ->get();
    }

    public function getProjectBaseSalarySummaryAmountReportBaseOnCurrentlyNotWorkingEmployees($project_id,$month,$year,$salary_status_ids){



            return  EmployeeInfo::select(
                'employee_infos.hourly_employee',
                DB::raw("COUNT(salary_histories.slh_auto_id) as total_emp"),
                DB::raw("SUM(salary_histories.slh_total_salary) as total_salary"),
                DB::raw("SUM(salary_histories.slh_saudi_tax) as total_saudi_tax"),
                DB::raw("SUM(salary_histories.slh_iqama_advance) as total_iqama_adv"),
                DB::raw("SUM(salary_histories.slh_other_advance) as total_other_adv"),
            )
             ->whereNot("employee_infos.job_status", 1) // all except active emp
            ->where("employee_infos.project_id", $project_id)
            ->where("salary_histories.slh_month", $month)
            ->where("salary_histories.slh_year", $year)
           ->where("monthly_work_histories.month_id", $month)
           ->where("monthly_work_histories.year_id", $year)
            ->whereIn("salary_histories.Status", $salary_status_ids)
            ->leftjoin('monthly_work_histories', 'employee_infos.emp_auto_id', '=', 'monthly_work_histories.emp_id')
            ->leftjoin('salary_histories', 'salary_histories.emp_auto_id', '=', 'employee_infos.emp_auto_id')
            ->groupBy('employee_infos.hourly_employee')
            ->orderBy('employee_infos.hourly_employee', 'ASC')
            ->get();
    }

    public function findAnEmployeeThoseAreNotReceivedSalary($emp_id,$month,$year){

       return SalaryHistory::select('slh_month','slh_year','salary_histories.Status','slh_total_salary','project_infos.proj_name')->where('emp_auto_id',$emp_id)
                            ->whereBetween("salary_histories.slh_month", [1,$month])
                            ->whereBetween("salary_histories.slh_year", [2022,$year])
                            ->where("salary_histories.Status", 0)
                            ->leftjoin('project_infos', 'salary_histories.project_id', '=', 'project_infos.proj_id')
                            ->get();

    }

    public function getProjectBaseBasicAndHourlyEmployeeSalarySummaryProject($project_id_list,$from_month,$to_month,$from_year,$to_year){
        return  $reports = DB::select('call getBasicAndHourlyEmpSalarySummary1(?,?,?,?,?)',array($project_id_list,$from_month,$to_month,$from_year,$to_year));
    }

    public function getAProjectDirectBasicAndHourlyEmployeesSalarySummaryReport($project_id,$month,$year){
        return  DB::select('call getAProjectDirectBasicAndHourlEmployeesSalaryInfoSummaryReport(?,?,?)',array($project_id,$month,$year));
    }

    public function getAProjectDirectBasicAndHourlyEmployeesSalarySummaryWithOutCateringServiceReport($project_id,$month,$year){
        return  DB::select('call getAProjectDirectEmpSalarySummaryWithOutCateringServiceReport(?,?,?)',array($project_id,$month,$year));
    }


    public function getAProjectInirectEmployeesSalarySummaryWithOutCateringServiceReport($project_id,$month,$year){

        return  DB::select('call getAProjectIndirectEmpSalaryWithOutCateringServiceSummaryReport(?,?,?)',array($project_id,$month,$year));
    }

    public function getAProjectInirectEmployeesSalarySummaryReport($project_id,$month,$year){
        return  DB::select('call getAProjectIndirectEmployeesSalaryInfoSummaryReport(?,?,?)',array($project_id,$month,$year));
    }

    public function getAProjectDirectHourslyEmployeesSalarySummaryReport($project_id,$month,$year){
            return  DB::select('call getAProjectDirectHourlyEmployeesSalaryInfoSummaryReport(?,?,?)',array($project_id,$month,$year));
    }

    // aproject actual salary expense report
    public function getAProjectActualSalaryExpenseOfDirectEmployeesSalarySummaryReport($project_id,$month,$year){
        return  DB::select('call getAProjectDirectEmpActualSalaryExpSummaryReport(?,?,?)',array($project_id,$month,$year));
    }
    public function getAProjectActualSalaryExpenseOfInDirectEmployeesSalarySummaryReport($project_id,$month,$year){
        return  DB::select('call getAProjectIndirectEmpActualSalaryExpSummaryReport(?,?,?)',array($project_id,$month,$year));
    }

    // Employee Working Project record base salary inform
    public function getOnlyThisProjectWorkingEmployeesRecordFromMultiProjectTable($month,$year,$project_id){
        return    DB::select('call getOnlyThisProjectWorkingEmployeeSalaryInformation1(?,?,?)',array($month,$year,$project_id));
    }


    public function getProjectBaseBasicAndHourlyEmployeeSalarySummaryReport($project_id,$from_month,$to_month,$from_year,$to_year){
         return  $dbrecord = DB::select('call getBasicAndHourlyEmpSalaryWithDeductionSummaryByProjectID(?,?,?,?,?)',array($project_id,$from_month,$to_month,$from_year,$to_year));
           $salary = new SalaryHistory();
           if(count($dbrecord) == 2){

            $abc =(array)  $dbrecord[0];
            $salary->total_basic_emp = $abc['total_emp'];
            $salary->total_basic_salary = $abc['total_salary'];

            $abc =(array)  $dbrecord[1];
            $salary->total_hourly_emp = $abc['total_emp'];
            $salary->total_hourly_salary = $abc['total_salary'];

           }
          elseif(count($dbrecord) == 1){

                $abc =(array)  $dbrecord[0];
                if( $dbrecord[0]->hourly_employee == 1){

                    $salary->total_hourly_emp = $abc['total_emp'];
                    $salary->total_hourly_salary = $abc['total_salary'];

                }else {
                    $salary->total_basic_emp = $abc['total_emp'];
                    $salary->total_basic_salary = $abc['total_salary'];
                }
          }
          return $salary;
    }

    public function getAProjectBasicAndHourlyEmployeeSalarySummaryReportByProjectId($project_id,$month,$year){


                $dbrecord = DB::table('salary_histories as sh')
                ->leftJoin('employee_infos as ei', 'sh.emp_auto_id', '=', 'ei.emp_auto_id')
                ->select(
                    'ei.hourly_employee as salary_type',
                    DB::raw('SUM(sh.slh_total_hours + sh.slh_total_overtime) as total_hours'),
                    DB::raw('COUNT(sh.emp_auto_id) as total_emp'),
                    DB::raw('SUM(sh.slh_total_salary) as total_salary'),
                    DB::raw('SUM(sh.slh_iqama_advance) as total_iqama_deduction'),
                    DB::raw('SUM(sh.slh_other_advance + sh.slh_saudi_tax + sh.slh_food_deduction+partial_paid_amount) as total_other_deduction'),
                    DB::raw('SUM(sh.slh_saudi_tax) as total_saudi_deduction'),
                    DB::raw('SUM(sh.slh_food_deduction) as total_food_deduction'),
                    DB::raw('SUM(sh.slh_all_include_amount) as all_incl_total_salary')
                )
                ->where('sh.slh_month', $month)
                ->where('sh.slh_year', $year)
                ->where('sh.project_id', $project_id)
                ->groupBy('ei.hourly_employee')
                ->orderBy('ei.hourly_employee')
                ->get();


       // $dbrecord = DB::select('call getAProjecBasicAndHourlyEmpSalaryAndDeductionSummaryRerport1(?,?,?)',array($project_id,$month,$year));
        $salary = new SalaryHistory();
        if(count($dbrecord) == 2){

         $abc =(array)  $dbrecord[0];
         $salary->basic_emp = $abc['total_emp'];
         $salary->basic_salary = $abc['total_salary'];
         $salary->basic_iqama_deduction = $abc['total_iqama_deduction'];
         $salary->basic_other_deduction = $abc['total_other_deduction'];
         $salary->all_incl_total_basic_salary = $abc['all_incl_total_salary'];
         $salary->basic_hours = $abc['total_hours'];



         $abc =(array)  $dbrecord[1];
         $salary->hourly_emp = $abc['total_emp'];
         $salary->hourly_salary = $abc['total_salary'];
         $salary->hourly_iqama_deduction = $abc['total_iqama_deduction'];
         $salary->hourly_other_deduction = $abc['total_other_deduction'];
         $salary->all_incl_total_hourly_salary = $abc['all_incl_total_salary'];
         $salary->hourly_hours = $abc['total_hours'];

        }
       elseif(count($dbrecord) == 1){

             $abc =(array)  $dbrecord[0];
             if( $dbrecord[0]->salary_type == 1){

                 $salary->hourly_emp = $abc['total_emp'];
                 $salary->hourly_salary = $abc['total_salary'];
                 $salary->hourly_iqama_deduction = $abc['total_iqama_deduction'];
                 $salary->hourly_other_deduction = $abc['total_other_deduction'];
                 $salary->all_incl_total_hourly_salary = $abc['all_incl_total_salary'];
                 $salary->hourly_hours = $abc['total_hours'];


             }else {
                 $salary->basic_emp = $abc['total_emp'];
                 $salary->basic_salary = $abc['total_salary'];
                 $salary->basic_iqama_deduction = $abc['total_iqama_deduction'];
                 $salary->basic_other_deduction = $abc['total_other_deduction'];
                 $salary->all_incl_total_basic_salary = $abc['all_incl_total_salary'];
                 $salary->basic_hours = $abc['total_hours'];

             }
       }
       return $salary;

    }

    public function getAMonthSalarySummaryUsingMultipleSponsorIdForAsloobAndSubconSalarySummaryReport($sponsor_ids,$project_ids,$month,$year){

       //  dd($sponsor_ids,$project_ids,$month,$year);
        $dbrecord = DB::table('salary_histories as sh')
                ->leftJoin('employee_infos as ei', 'sh.emp_auto_id', '=', 'ei.emp_auto_id')
                ->select(                    
                     'sh.Status as paid_status',
                    DB::raw('COUNT(sh.emp_auto_id) as total_emp'),
                    DB::raw('SUM(sh.slh_total_salary) as total_net_salary'),
                    DB::raw('SUM(sh.slh_iqama_advance+sh.slh_other_advance + sh.slh_saudi_tax + sh.slh_food_deduction+partial_paid_amount) as total_deduction'),
                    DB::raw('SUM(sh.slh_all_include_amount) as all_incl_total_salary')
                )
                ->where('sh.slh_month', $month)
                ->where('sh.slh_year', $year)
                ->whereIn('ei.sponsor_id', $sponsor_ids)
                ->groupBy('sh.Status')
                ->orderBy('sh.Status')
                ->get();

        $salary = new SalaryHistory();
        $salary->total_unpaid_emp = 0;
        $salary->total_unpaid_net_salary = 0; 
        $salary->all_incl_total_unpaid_salary = 0; 

        $salary->total_paid_emp = 0;
        $salary->total_paid_net_salary = 0; 
        $salary->all_incl_total_paid_salary = 0;

        if(count($dbrecord) == 2){

                $abc =(array)  $dbrecord[0];
                $salary->total_unpaid_emp = $abc['total_emp'];
                $salary->total_unpaid_net_salary = $abc['total_net_salary']; 
                $salary->all_incl_total_unpaid_salary = $abc['all_incl_total_salary']; 

                $abc =(array)  $dbrecord[1];
                $salary->total_paid_emp = $abc['total_emp'];
                $salary->total_paid_net_salary = $abc['total_net_salary']; 
                $salary->all_incl_total_paid_salary = $abc['all_incl_total_salary']; 


                

        }
       elseif(count($dbrecord) == 1){

             $abc =(array)  $dbrecord[0];
             if( $dbrecord[0]->paid_status == 0){

                $salary->total_unpaid_emp = $abc['total_emp'];
                $salary->total_unpaid_net_salary = $abc['total_net_salary']; 
                $salary->all_incl_total_unpaid_salary = $abc['all_incl_total_salary'];


             }else {
                $salary->total_paid_emp = $abc['total_emp'];
                $salary->total_paid_net_salary = $abc['total_net_salary']; 
                $salary->all_incl_total_paid_salary = $abc['all_incl_total_salary']; 

             }
       }
       return $salary;

    }

   // Employee Working Project record base salary inform
    // public function getOnlyThisProjectWorkingEmployeesRecordFromMultiProjectTable($month,$year,$project_id,$emp_type,$hourly_employee){
    //     return    DB::select('call getOnlyThisProjectWorkingEmployeeSalaryInformation(?,?,?,?,?)',array($month,$year,$project_id,$emp_type,$hourly_employee));
    // }


    public function calculateAProjectTotalPaidUnpaidSalaryInAMonth($project_id, $salary_month, $salary_year)
    {

        $result = DB::select('call calculateAProjectBasicAndHourlySalaryPaidEmployeesReport1(?,?,?)',array($project_id,$salary_month,$salary_year));
        $salary = new SalaryHistory();
        if(count($result) == 2){
            if($result[0]->salary_type == 1){

                $salary->hourly_paid_emp =   $result[0]->total_emp;
                $salary->hourly_paid_amount = $result[0]->total_salary;

                $salary->basic_paid_emp =   $result[1]->total_emp;
                $salary->basic_paid_amount = $result[1]->total_salary;
            }
            else {
                $salary->hourly_paid_emp =   $result[1]->total_emp;
                $salary->hourly_paid_amount = $result[1]->total_salary;

                $salary->basic_paid_emp =   $result[0]->total_emp;
                $salary->basic_paid_amount = $result[0]->total_salary;;
            }
        }else if(count($result) == 1){

            if($result[0]->salary_type == 1){
                $salary->hourly_paid_emp =   $result[0]->total_emp;
                $salary->hourly_paid_amount = $result[0]->total_salary;
            }
            else {
                $salary->basic_paid_emp =   $result[0]->total_emp;
                $salary->basic_paid_amount = $result[0]->total_salary;;
            }
        }
        return $salary;
    }

    public function calculateAProjectBasicAndHourlyEmpUnpaidSalarySummaryInAMonth($project_id, $salary_month, $salary_year)
    {

        $result = DB::select('call calculateAProjectBasicAndHourlyUnpaidSalaryReport(?,?,?)',array($project_id,$salary_month,$salary_year));
        //   dd($result);

        $salary = new SalaryHistory();
        if(count($result) == 2){
            if($result[0]->salary_type == 1){

                $salary->hourly_unpaid_emp =   $result[0]->total_emp;
                $salary->hourly_unpaid_amount = $result[0]->total_salary;

                $salary->basic_unpaid_emp =   $result[1]->total_emp;
                $salary->basic_unpaid_amount = $result[1]->total_salary;
            }
            else {
                $salary->hourly_unpaid_emp =   $result[1]->total_emp;
                $salary->hourly_unpaid_amount = $result[1]->total_salary;

                $salary->basic_unpaid_emp =   $result[0]->total_emp;
                $salary->basic_unpaid_amount = $result[0]->total_salary;;
            }
        }else if(count($result) == 1){

            if($result[0]->salary_type == 1){
                $salary->hourly_unpaid_emp =   $result[0]->total_emp;
                $salary->hourly_unpaid_amount = $result[0]->total_salary;
            }
            else {
                $salary->basic_unpaid_emp =   $result[0]->total_emp;
                $salary->basic_unpaid_amount = $result[0]->total_salary;;
            }
        }
         return $salary;

    }

    public function getASponsorMonthByMonthSalarySummaryInAProjectByMonthYearReport($sponsor_id,$project_id,$month,$year){
        return    DB::select('call getASponsorMonthByMonthSalarySummaryInAProjectByMonthYearReport(?,?,?,?)',array($sponsor_id,$month,$year,$project_id));
    }

    public function getASponsorYearlySalarySummaryMonthByMonthReport($sponsor_id,$month,$year){
        return    DB::select('call getASponsorYearlySalarySummaryMonthByMonthReport1(?,?,?)',array($sponsor_id,$month,$year));
    }

    public function getASponsorSingleMonthSalarySummaryProjecBaseDetailsReport($sponsor_id,$month,$year){
        return    DB::select('call getASponsorSingleMonthSalarySummaryProjectBaseDetailsReport1(?,?,?)',array($sponsor_id,$month,$year));
    }

    public function processASponsorSingleMonthSalaryAsPerSalarySheetProjectBaseReport($sponsor_id,$month,$year){
       return    DB::select('call getASponsorSingleMonthSalarySummaryAsPerSalarySheetReport(?,?,?)',array($sponsor_id,$month,$year));
    }

    public function salaryPaidByBankEmployeesSalaryReport($project_ids, $month,$year,$branch_office_id){


             return   EmployeeInfo::where('salary_histories.slh_month', $month)
            ->where('salary_histories.slh_year', $year)
            ->where('monthly_work_histories.month_id', $month)->where('monthly_work_histories.year_id', $year)
            ->whereNotNull('salary_histories.slh_paid_method')
            ->whereIn('salary_histories.project_id', $project_ids)  // now  parameter value not provide from caller
            ->where('salary_histories.branch_office_id',$branch_office_id)
            ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
            ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
            ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
            ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
            ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
            ->leftjoin('monthly_work_histories', 'salary_histories.emp_auto_id', '=', 'monthly_work_histories.emp_id')
              ->orderBy('employee_id','ASC')
            ->get();
    }

        public function EmployeeSalaryPaidByBankReportForSendingToBank($project_ids, $month,$year,$branch_office_id){

        return   EmployeeInfo::select('employee_infos.employee_id','employee_infos.akama_no','employee_bank_details.acc_iban as account_no',
            'employee_infos.employee_name','bank_names.bank_code','employee_categories.catg_name as designation','project_infos.proj_name','salary_histories.*'
            )
            ->where('salary_histories.slh_month', $month)
            ->where('salary_histories.slh_year', $year)
            //->whereNotNull('salary_histories.slh_paid_method')
            ->where('employee_bank_details.is_active', 1) // only active bank account
            ->whereIn('salary_histories.project_id', $project_ids)
            ->where('salary_histories.branch_office_id',$branch_office_id)
            ->leftjoin('employee_bank_details', 'employee_infos.emp_auto_id', '=', 'employee_bank_details.emp_auto_id')
            ->leftjoin('bank_names', 'employee_bank_details.bank_id', '=', 'bank_names.bn_auto_id')
            ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
            ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
             ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
             ->orderBy('project_infos.proj_name','ASC')
              ->orderBy('employee_id','ASC')
            ->get();
    }

        public function EmployeeSalaryPaidByBankReportForSendingToBankByProjectAndBankIDs($project_ids, $month,$year,$bank_ids){

        return   EmployeeInfo::select('employee_infos.employee_id','employee_infos.akama_no','employee_bank_details.acc_iban as account_no',
            'employee_infos.employee_name','bank_names.bank_code','employee_categories.catg_name as designation','project_infos.proj_name','salary_histories.*'
            )
            ->where('salary_histories.slh_month', $month)
            ->where('salary_histories.slh_year', $year)
            ->whereNotNull('salary_histories.slh_paid_method')
            ->where('employee_bank_details.is_active', 1) // only active bank account
            ->whereIn('salary_histories.project_id', $project_ids)
           // ->whereIn('bank_names.bn_auto_id', $bank_ids)
            ->where('salary_histories.branch_office_id',1)
            ->leftjoin('employee_bank_details', 'employee_infos.emp_auto_id', '=', 'employee_bank_details.emp_auto_id')
            ->leftjoin('bank_names', 'employee_bank_details.bank_id', '=', 'bank_names.bn_auto_id')
            ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
            ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
            ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
            ->orderBy('project_infos.proj_name','ASC')
            ->orderBy('employee_id','ASC')
            ->get();
    }


        public function searchListOfEmployeesThoseNotReceivedSalaryAfterADateForInActiveEmployeeReport($date,$all_emp_ids){

            $subQuery = SalaryHistory::select('emp_auto_id')
            ->where('slh_salary_date', '<', $date);
            // Main query: select IDs not in subquery
        return  SalaryHistory::select('emp_auto_id')->whereNotIn('emp_auto_id', $subQuery)
                                ->whereIn('emp_auto_id',$all_emp_ids)
                                ->distinct()
                                ->pluck('emp_auto_id');
    }

    public function getAnEmployeeDetailsWithSalaryReceivedDateForInactivityEmployeeReport($emp_auto_id){
        return   EmployeeInfo::where('salary_histories.emp_auto_id', $emp_auto_id)
               // ->where('salary_histories.slh_salary_date','<',$date)
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
                ->orderBy('salary_histories.slh_auto_id', 'DESC')
              // ->orderBy('employee_infos.employee_id', 'ASC')
                ->first();
    }

     public function processAMonthUnpaidTotalEmployeesAndNetSalaryAmountReport($month,$year){

        return DB::table('salary_histories')
            ->where('slh_year', $year)
            ->where('Status', 0)
            ->select('slh_month', DB::raw('count(*) as total_unpaid'), DB::raw('SUM(slh_total_salary) as total_unpaid_salary'))
            ->groupBy('slh_month')
            ->get();


    }

     public function getListOfProjectsHavingEmployeeSalaryInAYearForReport($year){

        return  EmployeeMultiProjectWorkHistory::
                 leftjoin('project_infos', 'emp_multi_proj_work_hist.project_id', '=', 'project_infos.proj_id')
                ->select('project_infos.proj_name','project_infos.proj_id')
                ->where('emp_multi_proj_work_hist.year', $year)
                ->distinct()
                ->orderBy('project_infos.proj_name','ASC')
                ->get();

    }
    public function getAYearEveryMonthTotalSalarySummaryReport($year,$project_id){


        return EmployeeMultiProjectWorkHistory::select(
                'month',
                DB::raw("COUNT(DISTINCT(emp_id)) as total_emp"),
                DB::raw("SUM(total_amount) as total_salary"),
              //  DB::raw("SUM(total_hour) as total_hour"),
               // DB::raw("SUM(total_overtime) as total_overtime"),
            )
             ->where('project_id', $project_id)
             ->where('year', $year)
            ->groupBy('month')
            ->orderBy('month','ASC')
            ->get();
    }





       /*
     ==========================================================================
     ================ uploadEmployeeSalarySheet ===============================
     ==========================================================================
    */
    public function getUploadedSalarySheetInformation(){
      return  SalarySheetUpload::   orderBy('year','DESC')
                                    ->get();
    }

    public function getAnUploadedSalarySheetInformation($salary_uploaded_info_auto_id){
        return  SalarySheetUpload::where('ss_auto_id',$salary_uploaded_info_auto_id)->first();
      }

    public function deleteAnUploadedSalarySheetInformation($salary_uploaded_info_auto_id){
        return  SalarySheetUpload::where('ss_auto_id',$salary_uploaded_info_auto_id)->delete();
      }


    public function insertUploadedSalaryShetInformation($no_of_emp,$salary_date,$month,$year,$uploaded_by,$remarks,$file_name,$file_path){

        return SalarySheetUpload::insertGetId([

            'no_of_emp' => $no_of_emp,
            'salary_date' => $salary_date,
            'month' => $month,
            'year' => $year,
            'uploaded_by' => $uploaded_by,
            'remarks' => $remarks,
            'file_name' => $file_name,
            'file_path' => $file_path,
            'created_at' => Carbon::now(),
        ]);

    }







       /*
     ==========================================================================
     ================ Employee Mobile Bill Information ===============================
     ==========================================================================
    */

    public function storeEmployeeMobileBillInformation($bill_month, $bill_year, $bill_project_id,$bill_payment_paper,$insert_by){
        return EmpMobileBill::insertGetId([
            'month' => $bill_month,
            'year' => $bill_year,
            'project_id' => $bill_project_id,
            'bill_payment_paper' => $bill_payment_paper,
            'create_by_id' => $insert_by,
            'created_at' => Carbon::now(),
        ]);
    }

    public function searchEmployeeMobileBillRecordsForListViewByYearMonthProject($month, $year,$project_ids,$branch_office_id){
        return EmpMobileBill::select('project_infos.proj_name','users.name as created_by','emp_mobile_bills.*')
                            ->where('year', $year)->whereIn('month', $month)->whereIn('project_id',$project_ids)
                            ->where('project_infos.branch_office_id',$branch_office_id)
                            ->leftjoin('project_infos', 'emp_mobile_bills.project_id', '=', 'project_infos.proj_id')
                            ->leftjoin('users', 'emp_mobile_bills.create_by_id', '=', 'users.id')
                            ->get();
    }

    /*
     ==========================================================================
     =============== Salary Details Update History Report =====================
     ==========================================================================
    */

    public function getSalaryDetailsRecordsWithEmployeeDetailsByDateToDate($start_date, $end_date, $employee_id = null) {
        // Parse dates explicitly from YYYY-MM-DD format
        try {
            $start_date = Carbon::createFromFormat('Y-m-d', $start_date)->startOfDay();
            $end_date = Carbon::createFromFormat('Y-m-d', $end_date)->endOfDay();
        } catch (\Exception $e) {
            // Fallback to general parsing if format fails
            $start_date = Carbon::parse($start_date)->startOfDay();
            $end_date = Carbon::parse($end_date)->endOfDay();
        }

        // Fetch records with all employee details and relationships from salary_details_hisotries table
        $query = DB::table('salary_details_hisotries')
            ->select(
                'salary_details_hisotries.sdh_auto_id',
                'salary_details_hisotries.emp_auto_id',
                'salary_details_hisotries.basic_amount',
                'salary_details_hisotries.basic_hours',
                'salary_details_hisotries.hourly_rate',
                'salary_details_hisotries.food_allowance',
                'salary_details_hisotries.hourly_employee',
                'salary_details_hisotries.payment_method',
                'salary_details_hisotries.inserted_by',
                'employee_infos.employee_id',
                'employee_infos.employee_name',
                'employee_infos.akama_no',
                'employee_categories.catg_name',
                'sponsors.spons_name',
                'users.name as updated_by_name',
                DB::raw('MONTH(salary_details_hisotries.created_at) as salary_month'),
                DB::raw('YEAR(salary_details_hisotries.created_at) as salary_year')
            )
            ->leftjoin('employee_infos', 'salary_details_hisotries.emp_auto_id', '=', 'employee_infos.emp_auto_id')
            ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
            ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
            ->leftjoin('users', 'salary_details_hisotries.inserted_by', '=', 'users.id')
            ->whereBetween('salary_details_hisotries.created_at', [$start_date, $end_date]);

        // Filter by employee_id if provided and not empty
        if (!empty($employee_id)) {
            $query->where('employee_infos.employee_id', $employee_id);
            $query->distinct()->orderBy('salary_details_hisotries.created_at', 'DESC')
                        ->orderBy('employee_infos.employee_id', 'ASC')
                        ->get();
        }else {
            return $query->distinct()->orderBy('salary_details_hisotries.created_at', 'DESC')
                        ->orderBy('employee_infos.employee_id', 'ASC')
                        ->get();
        }



    }


}
