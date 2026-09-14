<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MonthlyWorkHistory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;


class MonthlyWorkHistoryController extends Controller
{

  public function findIdWiseMonthlyWorkHistory($emp_id, $month, $year)
  {
    return $find = MonthlyWorkHistory::where('status', 1)->where('emp_id', $emp_id)->where('month_id', $month)->where('year_id', $year)->firstOrFail();
  }


  public function monthliWorkHistRecrdUpdate($auto_id, $month, $year, $work_hours, $overtime, $total_work_day)
  {
    $entered_id = Auth::user()->id;
    return $update = MonthlyWorkHistory::where('status', 1)->where('month_work_id', $auto_id)->update([
      // 'emp_id' => $employee_id,
      'month_id' => $month,
      'year_id' => $year,
      'total_hours' => $work_hours,
      'overtime' => $overtime,
      'total_work_day' => $total_work_day,
      'entered_id' => $entered_id,
      'updated_at' => Carbon::now(),
    ]);
  }

  public function deleteMonthlyWorkRecord($emp_auto_id, $month, $year)
  {
    return  MonthlyWorkHistory::where('status', 1)
      ->where('emp_id', $emp_auto_id)
      ->where('month_id', $month)
      ->where('year_id', $year)
      ->delete();
  }




  // public function create(){
  //   $monthObj = new MonthController();
  //   $month = $monthObj->getAll();
  //   return view('admin.month-salary.all',compact('month'));
  // }
  //
  // public function apply(Request $request){
  //   /* form validation */
  //   $this->validate($request,[
  //     'emp_id' => 'required',
  //     'month' => 'required',
  //   ],[
  //
  //   ]);
  //   /* calculation */
  //   $emp_id = $request->emp_id;
  //   $month = $request->month;
  //
  //   $findemployee = EmployeeInfo::where('employee_id',$emp_id)->first();
  //   $salary = SalaryDetails::where('emp_id',$findemployee->emp_auto_id)->first();
  //   $totalWorkingdays = DailyWorkHistory::where('emp_id',$findemployee->emp_auto_id)->where('month',$month)->count('emp_id');
  //
  //   $year = Carbon::now()->format('Y');
  //   $salaryDate = Carbon::now();
  //
  //
  //   if($findemployee->emp_type_id == 1 ){ // permanent employee
  //     $totalWorkingHours = DailyWorkHistory::where('emp_id',$findemployee->emp_auto_id)->where('month',$month)->sum('work_hours');
  //
  //     $total = ($salary->basic_amount + $salary->house_rent + $salary->mobile_allowance + $salary->medical_allowance + $salary->local_travel_allowance + $salary->conveyance_allowance + $salary->increment_amount + $salary->others1 + $salary->others2 +$salary->others3 + $salary->others4 );
  //
  //     return view('admin.month-salary.salary-generat',compact('total','findemployee','salary','totalWorkingdays','totalWorkingHours','month','year','salaryDate'));
  //
  //   }else{
  //      $totalWorkingHours = DailyWorkHistory::where('emp_id',$findemployee->emp_auto_id)->where('month',$month)->sum('work_hours');
  //
  //      $total =  ( $salary->hourly_rent * $totalWorkingHours) + ($salary->basic_amount + $salary->house_rent + $salary->mobile_allowance + $salary->medical_allowance + $salary->local_travel_allowance + $salary->conveyance_allowance + $salary->increment_amount + $salary->others1 + $salary->others2 + $salary->others3 + $salary->others4 );
  //
  //      return view('admin.month-salary.salary-generat',compact('total','findemployee','salary','totalWorkingdays','totalWorkingHours','month','year','salaryDate'));
  //   }
  //
  // }
  //
  // public function salaryHistory(Request $request){
  //   $slHistory =  new SalaryHistory();
  //   $slHistory->emp_auto_id = $request->emp_auto_id;
  //   $slHistory->emp_id = $request->emp_id;
  //   $slHistory->slh_total_salary = $request->totalSalary;
  //   $slHistory->slh_total_hours = $request->totalHours;
  //   $slHistory->slh_total_working_days = $request->totalDays;
  //   $slHistory->slh_month = $request->month;
  //   $slHistory->slh_year = $request->year;
  //   $slHistory->slh_salary_date = $request->salaryDate;
  //   $slHistory->save();
  //   return response()->json(['success' => 'Successfully added']);
  // }
  //
  // public function download(){
  //   // $pdf = PDF::loadView('admin.month-salary.salary-generat2');
  //   // return $pdf->download('invoice.pdf');
  //
  // }
  //


}
