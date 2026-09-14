<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\CompanyProfileController;
use App\Http\Controllers\Admin\AdvancePayController;
use App\Http\Controllers\Admin\AdvancePayRecordController;
use App\Http\Controllers\Admin\AnualFee\AnualFeeDetailsController;
use App\Http\Controllers\DataServices\CompanyDataService;
use App\Http\Controllers\DataServices\EmployeeAdvanceDataService;
use App\Http\Controllers\DataServices\EmployeeDataService;
use App\Http\Controllers\DataServices\EmployeeRelatedDataService;
use App\Http\Controllers\DataServices\SalaryProcessDataService;

use Illuminate\Http\Request;
use App\Models\EmployeeInfo;
use Illuminate\Support\Facades\Session;

class IqamaAdvacnePayController extends Controller
{

  public function iqamaRenewal()
  {
    $allMonth = (new CompanyDataService())->getAllMonth();
    return view('admin.employee-info.iqama-renewal', compact('allMonth'));
  }



  public function projectWiseIqamaRenewal()
  {
    $projects = (new EmployeeRelatedDataService())->getAllProjectInfoForDropdown();
    $sponsor = (new EmployeeRelatedDataService())->getAllSponserInfoForDropdown();
    return view('admin.employee-info.due-iqama-renewal', compact('projects', 'sponsor'));
  }

  public function iqamaRenewalHistoryProcess(Request $request)
  {
    $findEmployee = EmployeeInfo::where('employee_id', $request->emp_id)->first();


    $companyObj = new CompanyProfileController();
    $company = $companyObj->findCompanry();

    if ($findEmployee) {
      $IqamaAdvance = EmployeeInfo::where('employee_infos.emp_auto_id', $findEmployee->emp_auto_id)
        ->where('advance_pays.adv_pay_purpose', 1)
        ->where('advance_pay_histories.aph_year', $request->year_id)
        ->leftjoin('advance_pays', 'employee_infos.emp_auto_id', '=', 'advance_pays.emp_id')
        ->leftjoin('advance_purposes', 'advance_pays.adv_pay_purpose', '=', 'advance_purposes.id')
        ->leftjoin('advance_pay_histories', 'advance_pays.adv_pay_id', '=', 'advance_pay_histories.adv_pay_id')
        ->get();
      return view('admin.report.iqama.iqama-renewal', compact('findEmployee', 'IqamaAdvance', 'company'));
    } else {
      Session::flash('error', 'value');
      return redirect()->back();
    }
  }


 public function projectAndSponsorWiseIqamaReport(Request $request)
  {

    $companyOBJ = new CompanyProfileController();
    $company = $companyOBJ->findCompanry();

    $projId = $request->proj_id;
    $sponsId = $request->spons_id;
    $year = $request->year;

     $projAndSponsWiseEmp = (new EmployeeDataService())->getEmployeeListWithProjectAndSponsor($projId, $sponsId);
    $projectName = 'All';
    $sponsorName = 'All';

    if ($projId != 0) {
      $projectName = (new EmployeeRelatedDataService())->findAProjectInformation($projId)->proj_name;
    }
    if ($sponsId != 0) {
      $sponsorName = (new EmployeeRelatedDataService())->findASponser($sponsId)->spons_name;
    }

    $totalIqmaAdvAmount = 0;
    $totalPaidAmount = 0;
    $totalDueAmount = 0;
    $counter = 0;
    foreach ($projAndSponsWiseEmp as $anEmployee) {
      //  if($counter == 0)
      //   continue;
     
      // $anEmployeeIqamaAdvanceAmont = (new EmployeeAdvanceDataService())->getAnEmployeeIqamaTotalCost($anEmployee->emp_auto_id);
      // $anEmployeePaidAoumt = (new EmployeeAdvanceDataService())->getAnEmployeeAdvancePaidTotalAmount($anEmployee->emp_auto_id, $year, 1);

      $iqama_renewal_cost_total_amount = (new EmployeeAdvanceDataService())->getAnEmployeeIqamaTotalCost($anEmployee->emp_auto_id);
      // Advance Collection from Employee
        $cashReceiveTotalPaidAmount = (new EmployeeAdvanceDataService())->getAnEmployeeAdvancePaidTotalAmount($anEmployee->emp_auto_id, null, 100); // 100 = cash receive from employee
        $toal_iqama_expense_deduction_from_salary = (new SalaryProcessDataService())->getTotalAmountOfIqamaExpenseDeductionFromSalary($anEmployee->emp_auto_id,null);
        $totalPaidAmount = $toal_iqama_expense_deduction_from_salary + $cashReceiveTotalPaidAmount;

      $anEmployee->iqama_renewal_cost_total_amount = $iqama_renewal_cost_total_amount;
      $anEmployee->iqama_renewal_total_paid_amount = $totalPaidAmount;
      

      $totalIqmaAdvAmount += $iqama_renewal_cost_total_amount;
      $totalPaidAmount += $totalPaidAmount;
      $totalDueAmount += ($iqama_renewal_cost_total_amount - $totalPaidAmount);
      $projAndSponsWiseEmp[$counter++] = $anEmployee; 
    }
    

    return view('admin.report.iqama.proj-spons-iqama', compact('projAndSponsWiseEmp', 'sponsorName', 'projectName',  'company', 'totalIqmaAdvAmount', 'totalPaidAmount', 'totalDueAmount'));
  }

 
}
