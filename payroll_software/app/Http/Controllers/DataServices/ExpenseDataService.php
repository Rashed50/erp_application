<?php

namespace App\Http\Controllers\DataServices;

use App\Models\CostType;
use App\Models\DailyCost;
use Carbon\Carbon;


class ExpenseDataService {


    /*
     ==========================================================================
     ============================= Expense Type/Head ============================
     ==========================================================================
    */
    public function checkExpenseTypeTitleIsExist($cost_type_name){

        $cost_type_name = strtolower($cost_type_name);
        return CostType::where( strtolower('cost_type_name'), $cost_type_name)->first();
    }

    public function getCostTypeAll(){
        return $all = CostType::orderBy('cost_type_id','DESC')->get();
      }
      
     public function getCostTypeHeadForDropdownList(){
        return $all = CostType::where('status',1)->orderBy('cost_type_name','ASC')->get();
      }
    
      public function findACostType($id){
        return $edit = CostType::where('cost_type_id',$id)->first();
      }

    public function insertNewCostType($cost_type_name){

        if ($this->checkExpenseTypeTitleIsExist($cost_type_name) != null) {
            return 0;
        } else {
           return CostType::insertGetId([
                'cost_type_name' => $cost_type_name,
                'created_at' => Carbon::now(),
              ]);
        }
        
    }

    public function updateCostType($id,$cost_type_name){

        if ($this->checkExpenseTypeTitleIsExist($cost_type_name) != null) {
            return 0;
        } else {
           return $update = CostType::where('cost_type_id',$id)->update([
            'cost_type_name' => $cost_type_name,
            'updated_at' => Carbon::now(),
          ]);
        }


    }


    /*
     ==========================================================================
     =============================Daily Expense Details =======================
     ==========================================================================
    */

    public function saveNewExpenseDetails(
      $sub_comp_name_id,
      $cost_type_id,
      $project_id,
      $entered_id,
      $employee_auto_id,
      $voucher_date,
      $vouchar_image_path,
      $vouchar_no,
      $gross_amount,
       $vat,
       $total_amount,
       $month,
       $year,
       $description,
    ){
      return DailyCost::insert([
        'sub_comp_id' => $sub_comp_name_id,
        'cost_type_id' => $cost_type_id,
        'project_id' => $project_id,
        'entered_id' => $entered_id,
        'employee_id' => $employee_auto_id,
        'voucher_date' => $voucher_date,
        'vouchar' => $vouchar_image_path,
        'vouchar_no' => $vouchar_no,
        'gross_amount' => $gross_amount,
        'vat' => $vat,
        'total_amount' => $total_amount,
        'month' => $month,
        'year' => $year,
        'description' => $description,
        'created_at' => Carbon::now(),
    ]);
    }

    public function updateExpenseDetailsByCostAutoId(
      $cost_id,
      $sub_comp_name_id,
      $cost_type_id,
      $project_id,
      $entered_id,
      $employee_auto_id,
      $voucher_date,
      $vouchar_image_path,
      $vouchar_no,
      $gross_amount,
       $vat,
       $total_amount,
       $month,
       $year,
       $description,
    ){

     
     return DailyCost::where('cost_id',$cost_id)->update([
        'sub_comp_id' => $sub_comp_name_id,
        'cost_type_id' => $cost_type_id,
        'project_id' => $project_id,
        'entered_id' => $entered_id,
        'employee_id' => $employee_auto_id,
        'voucher_date' => $voucher_date,
        'vouchar' => $vouchar_image_path,
        'vouchar_no' => $vouchar_no,
        'gross_amount' => $gross_amount,
        'vat' => $vat,
        'total_amount' => $total_amount,
        'month' => $month,
        'year' => $year,
        'description' => $description,
        'created_at' => Carbon::now(),
    ]);
    }

    public function getDailyExpenseDetailsList(){
          return DailyCost::where('status','pending')->get();
    }
    
    public function getDailyExpenseDetailsById($id){
      return DailyCost::where('status','pending')->where('cost_id',$id)->firstOrFail();
    }

    public function approvalOfDailyExpenseDetails($cost_id,$approved_by_id){

      return DailyCost::where('status','pending')->where('cost_id',$cost_id)->update([
        'approved_by' => $approved_by_id,
        'status' => 'approved',
        'updated_at' => Carbon::now(),
       ]);
    }
    
    
     /*
     ==========================================================================
     ============================= Expenditure Report Process =================
     ==========================================================================
    */

    public function ExpenditureReportDatabaseOperation($start_date, $end_date, $subCompany, $project, $expenseHead){

        if ($subCompany == null && $project == null && $expenseHead == null) {
            // dd('000');
            return DailyCost::with('employee', 'subCompany', 'project', 'expenseHead')
                    ->where('status','pending')->whereBetween('voucher_date', [$start_date, $end_date])
                    ->orderBy('cost_id', 'ASC')
                    ->get();
        } elseif ($subCompany == null && $project == null && $expenseHead != null) {
            // dd('001');
            return DailyCost::with('employee', 'subCompany', 'project', 'expenseHead')
                    ->where('cost_type_id', $expenseHead)
                    ->where('status','pending')->whereBetween('voucher_date', [$start_date, $end_date])
                    ->orderBy('cost_id', 'ASC')
                    ->get();
        } elseif ($subCompany == null && $project != null && $expenseHead == null) {
            // dd('010');
            return DailyCost::with('employee', 'subCompany', 'project', 'expenseHead')
                    ->where('project_id', $project)
                    ->where('status','pending')->whereBetween('voucher_date', [$start_date, $end_date])
                    ->orderBy('cost_id', 'ASC')
                    ->get();
        } elseif ($subCompany == null && $project != null && $expenseHead != null){
            // dd('011');
            return DailyCost::with('employee', 'subCompany', 'project', 'expenseHead')
                    ->where('project_id', $project)
                    ->where('cost_type_id', $expenseHead)
                    ->where('status','pending')->whereBetween('voucher_date', [$start_date, $end_date])
                    ->orderBy('cost_id', 'ASC')
                    ->get();
        } elseif ($subCompany != null && $project == null && $expenseHead == null) {
            // dd('100');
            return DailyCost::with('employee', 'subCompany', 'project', 'expenseHead')
                    ->where('sub_comp_id', $subCompany)
                    ->where('status','pending')->whereBetween('voucher_date', [$start_date, $end_date])
                    ->orderBy('cost_id', 'ASC')
                    ->get();
        } elseif ($subCompany != null && $project == null && $expenseHead != null){
            // dd('101');
            return DailyCost::with('employee', 'subCompany', 'project', 'expenseHead')
                    ->where('sub_comp_id', $subCompany)
                    ->where('cost_type_id', $expenseHead)
                    ->where('status','pending')->whereBetween('voucher_date', [$start_date, $end_date])
                    ->orderBy('cost_id', 'ASC')
                    ->get();
        } elseif ($subCompany != null && $project != null && $expenseHead == null){
            // dd('110');
            return DailyCost::with('employee', 'subCompany', 'project', 'expenseHead')
                    ->where('sub_comp_id', $subCompany)
                    ->where('project_id', $project)
                    ->where('status','pending')->whereBetween('voucher_date', [$start_date, $end_date])
                    ->orderBy('cost_id', 'ASC')
                    ->get();
        } elseif ($subCompany != null && $project != null && $expenseHead != null){
            // dd('111');
            return DailyCost::with('employee', 'subCompany', 'project', 'expenseHead')
                    ->where('sub_comp_id', $subCompany)
                    ->where('project_id', $project)
                    ->where('cost_type_id', $expenseHead)
                    ->where('status','pending')->whereBetween('voucher_date', [$start_date, $end_date])
                    ->orderBy('cost_id', 'ASC')
                    ->get();
        }else {
            return DailyCost::with('employee', 'subCompany', 'project', 'expenseHead')
            ->where('status','pending')->whereBetween('voucher_date', [$start_date, $end_date])
            ->orderBy('cost_id', 'ASC')
            ->get();
        }
    }
     
}