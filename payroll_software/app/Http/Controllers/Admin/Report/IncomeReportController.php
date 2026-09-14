<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\IncomeSource;
use App\Models\CompanyProfile;

class IncomeReportController extends Controller{
  public function index(){
    return view('admin.report.income.index');
  }

  public function process(Request $request){
    $start_date = $request->start_date;
    $end_date = $request->end_date;

    $incomeReport = IncomeSource::whereBetween('submitted_date', [$start_date, $end_date])->get();
    $company = CompanyProfile::where('comp_id',1)->first();
    // dd($income_report);

    if($incomeReport){
      $total_amount = IncomeSource::whereBetween('submitted_date', [$start_date, $end_date])->sum('total_amount');

      $vat_amont = IncomeSource::whereBetween('submitted_date', [$start_date, $end_date])->sum('vat');

      $debit_amont = IncomeSource::whereBetween('submitted_date', [$start_date, $end_date])->sum('debit_amount');

      $debit_amont = IncomeSource::whereBetween('submitted_date', [$start_date, $end_date])->sum('debit_amount');

      $pending_amount = IncomeSource::whereBetween('submitted_date', [$start_date, $end_date])->where('invoice_status',0)->sum('net_amount');

      $released_amount = IncomeSource::whereBetween('submitted_date', [$start_date, $end_date])->where('invoice_status',1)->sum('net_amount');

      // dd($total_amount);

      return view('admin.report.income.report-sheet',compact('incomeReport','start_date','end_date','company','total_amount','vat_amont','debit_amont','pending_amount','released_amount'));
    }else{
      echo "nai";
    }

  }

  public function downloadPdf(){

  }

}
