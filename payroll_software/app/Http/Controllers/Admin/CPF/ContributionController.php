<?php

namespace App\Http\Controllers\Admin\CPF;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\Helper\HelperController;
use App\Http\Controllers\Admin\CompanyProfileController;
use App\Http\Controllers\DataServices\EmployeeDataService;
use Illuminate\Http\Request;
use App\Models\EmpContributeHistory;
 use Carbon\Carbon;
use Session;

class ContributionController extends Controller{

   public function getEmployeeContribution($emp_auto_id,$month,$salaryYear){
    return EmpContributeHistory::where('emp_auto_id', $emp_auto_id)->where('Month', $month)->where('Year', $salaryYear)->first();
   }
   public function saveEmployeeContribution($emp_auto_id,$cpf_contribution,$month,$salaryYear,$loginUserId){
        EmpContributeHistory::insert([
          'emp_auto_id' => $emp_auto_id,
          'Amount' => $cpf_contribution,
          'Month' => $month,
          'Year' => $salaryYear,
          'CreateById' => $loginUserId,
          'created_at' => Carbon::now(),
        ]);
   }
   public function updateEmployeeContribution($id,$cpf_contribution,$loginUserId){ 
      EmpContributeHistory::where('EmpContHistId', $id)->update([
        'Amount' => $cpf_contribution,
        'CreateById' => $loginUserId,
        'updated_at' => Carbon::now(),
      ]);
}


    public function EmployeeContribution(){
      return view('admin.contribution.index');
    }

    public function EmployeeContributionReport(Request $request){
      // Catch Value
      $empId = $request->emp_id;

      $employee = (new EmployeeDataService())->getAnEmployeeInfoByEmpId($empId);
       //EmployeeInfo::where('employee_id',$empId)->first();
      if($employee){
        // Date Formating
        $start_month = date('m', strtotime($request->start_date));
        $start_year = date('Y', strtotime($request->start_date));
        $end_month = date('m', strtotime($request->end_date));
        $end_year = date('Y', strtotime($request->end_date));


        $helperOBJ = new HelperController();
        $startMonthName = $helperOBJ->getMonthName($start_month);
        $endMonthName = $helperOBJ->getMonthName($end_month);

        $companyOBJ = new CompanyProfileController();
        $company = $companyOBJ->findCompanry();


        $contributionReport = EmpContributeHistory::with('employee')->where('emp_auto_id',$employee->emp_auto_id)->whereBetween('Month',[$start_month, $end_month])->whereBetween('Year',[$start_year, $end_year])->get();

        return view('admin.contribution.report',compact('contributionReport','startMonthName','endMonthName','company'));
      }else{
        Session::flash('error','value');
        return redirect()->back();
      }




    }

}
