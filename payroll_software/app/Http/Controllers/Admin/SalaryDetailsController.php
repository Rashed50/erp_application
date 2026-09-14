<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\DataServices\EmployeeDataService;
use App\Http\Controllers\DataServices\AuthenticationDataService;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class SalaryDetailsController extends Controller
{
    
   function __construct()
  {       
       $this->middleware('permission:salarydetails-list', ['only' => ['index']]);
       $this->middleware('permission:salarydetails-edit', ['only' => ['edit','update','directManStatusUpdate']]);
       $this->middleware('permission:salarydetails-delete', ['only' => ['delete']]);
       
  } 

  public function delete($id)
  {

  //  $delete = (new EmployeeDataService())->deleteAnEmployeeSalaryDetails($id);

    if ($delete) {
      Session::flash('success_soft', 'value');
      return Redirect()->back();
    } else {
      Session::flash('error', 'value');
      return Redirect()->back();
    }
  }

  /* Direct Man power status update */
  public function directManStatusUpdate($empId)
  {
    $findEmployee = (new EmployeeDataService())->getAnEmployeeInfoByEmpAutoId($empId);


    if ($findEmployee == null) {
      Session::flash('error', 'value');
      return Redirect()->back();
    }

    if ($findEmployee->emp_type_id == 1 && $findEmployee->hourly_employee == 1) {

      (new EmployeeDataService())->updateEmployeeHourlyEmployeeStatus($empId, NULL,Auth::user()->id);
    } else {

      (new EmployeeDataService())->updateEmployeeHourlyEmployeeStatus($empId, 1,Auth::user()->id);
    }
      // login user activities record
        (new AuthenticationDataService())->InsertLoginUserActivity(25,2, Auth::user()->id,$findEmployee->emp_auto_id,null);

    return Redirect()->back();
  }
  /* Direct Man power status update */

  /* salary data insert */
  public function insert(Request $req)
  {

    $id = $req->empId;
    $type = $req->employeetype;
    $hourlyRate = $req->hourly_rate;
    $houreRate = '';

    // if ($type == 1) {
    //   if ($hourlyRate == 0) {
    //     $houreRate = ($req->basic_amount / $req->basic_hours);
    //   } else {
    //     $houreRate = $req->hourly_rate;
    //   }
    // }
    // elseif ($type == 1 && $req->hourlyEmployee == 1) {
    //   $houreRate = $req->hourly_rate;
    // } else {
    //   if ($hourlyRate == 0) {
    //     $houreRate = ($req->basic_amount / 300);
    //   } else {
    //     $houreRate = $req->hourly_rate;
    //   }
    // }
    
    
    if ($type == 1 && $req->hourlyEmployee == 1) {
      $houreRate = $req->hourly_rate;
    }
    else if ($type == 1) {
        if ($req->basic_amount  > 0) {
        $houreRate = ($req->basic_amount / $req->basic_hours);
      } else {
        $houreRate = $req->hourly_rate;
      }
    }  else {
        if ($hourlyRate == 0) {
            $houreRate = ($req->basic_amount / 300);
      } else {
        $houreRate = $req->hourly_rate;
      }
    }
    

    $update = (new EmployeeDataService())->updateEmployeeSalaryDetailsByEmployeAutoId(
      $id,
      $req->basic_amount,
      $req->basic_hours,
      $houreRate,
      $req->house_rent,
      $req->mobile_allowance,
      $req->local_travel_allowance,
      $req->conveyance_allowance,
      $req->food_allowance,
      $req->others1
    );

    if ($update) {
      Session::flash('success', 'Successfully Saved New Employee Information');
      return Redirect()->route('add-employee');
    } else {
      Session::flash('error', 'value');
      return Redirect()->back();
    }
  }


 
  /* ================= Update Salary Details At Time of Employee Approval UI  ================= */
  public function updateEmployeeSalaryDetailsAtTimeOfEmployeeApproval(Request $request){

       
        $empId = $request->empId;
        $findEmp = (new EmployeeDataService())->getAnEmployeeInfoByEmpId($empId);
        if($findEmp ==  null){
          Session::flash('error', 'Employee Not Found ');
          return Redirect()->back();
        }
        
        $hourly_emp = NULL;
        if ($request->emp_type_id == 1) {
            if($request->has('hourly_employee')){
              if($request->hourly_employee == 1 ){
                $hourly_emp = 1;
                 $request->basic_amount = 0;
                $request->basic_hours = 0;
              }
            }
        }
        (new EmployeeDataService())->updateEmployeeInfoEmployeeTypeAndIsHourlyEmployee($findEmp->emp_auto_id,$request->emp_type_id, $hourly_emp,Auth::user()->id);
        $updateSalary = (new EmployeeDataService())->updateEmployeeSalaryDetailsBeforeJobApproval(
            $findEmp->emp_auto_id,
            $request->basic_amount,
            $request->basic_hours,
            $request->hourly_rate,
            $request->food_allowance,
            $request->mobile_allowance,
            $request->medical_allowance,
            $request->saudi_tax,
            Auth::user()->id
        );
        if ($updateSalary) {
             // login user activities record
           $salary_amount = $request->basic_amount > 0 ? $request->basic_amount : $request->hourly_rate;
          (new AuthenticationDataService())->InsertLoginUserActivity(24,2, Auth::user()->id,$findEmp->emp_auto_id,$salary_amount);
  
  
          Session::flash('success', 'Successfully Updated');
          return Redirect()->route('employee.new.employee.job.approval.ui');

        } else {
          Session::flash('error', 'Opps! please try again.');
          return Redirect()->back();
        }

  }

  /* ================= Salary Details Update ================= */
  public function update(Request $req)
  {
    /* data insert in database */
    $sdetails_id = $req->id;
    $update = (new EmployeeDataService())->updateEmployeeSalaryDetailsBySalaryDetailsAutoId(
      $sdetails_id,
      $req->basic_amount,
      $req->basic_hours,
      $req->hourly_rate,
      $req->house_rent,
      $req->mobile_allowance,
      $req->local_travel_allowance,
      $req->conveyance_allowance,
      $req->food_allowance,
      $req->others1,
      Auth::user()->id
    );

    if ($update) {
        
         // login user activities record
        $salary_details = (new EmployeeDataService())->getAnEmployeeSalaryDetails($sdetails_id);
        $salary_amount = $req->basic_amount > 0 ? $req->basic_amount : $req->hourly_rate;
        (new AuthenticationDataService())->InsertLoginUserActivity(3,2, Auth::user()->id,$salary_details->emp_id,$salary_amount);
        
       Session::flash('success', 'Successfully Updated');
      return Redirect()->route('salary-details');
    } else {
      Session::flash('error', 'value');
      return Redirect()->back();
    }
  }

  /* Employee Information For CPF Contribution */
  public function empInfoForContribution(Request $request)
  {
    $empId = $request->employeeID;
    $data = (new EmployeeDataService())->getAnEmployeeInfoWithSalaryDetailsByEmpId($empId, 1);

    if ($data) {
      return response()->json(['data' => $data]);
    } else {
      return response()->json(['invalidEmpId' => 'Employee Id Dosen,t Match!']);
    }
  }

  public function updateContributionAmount(Request $request)
  {
    $emp_auto_id = $request->empId;

    $update = (new EmployeeDataService())->updateEmployeeSalaryDetailsContributionAndSaudiTax($emp_auto_id, $request->amount, $request->tax);
    if ($update) {
      Session::flash('success', 'value');
      return Redirect()->back();
    }
  }



  /*
    =============================
    =======BLADE OPEREATION======
    =============================
    */

  public function index()
  {
   // $all = (new EmployeeDataService())->getEmployeesSalaryDetailsForListViewWithPagination(Auth::user()->branch_office_id,1);
    $all = array();
    $allActive = (new EmployeeDataService())->countTotalEmployees(1);
    return view('admin.salary-details.index', compact('all', 'allActive'));
  }

  public function searchEmployeeSalaryDetailsByEmpID(Request $request)
  {


    if ($request->employee_id == null) {
     $all = array();
    } else {
      $anEmp = (new EmployeeDataService())->getAnEmployeeSalaryDetailsByEmployeeID($request->employee_id,Auth::user()->branch_office_id,-1);
      $all = array();
      if ($anEmp != null) {
        $all[0] = $anEmp;
      }
    }
    $allActive = (new EmployeeDataService())->countTotalEmployees(1); // 1 = active employee
    return view('admin.salary-details.index', compact('all', 'allActive'));
  }

  public function edit($id)
  {
    $data = (new EmployeeDataService())->getAnEmployeeSalaryDetails($id);
    return view('admin.salary-details.edit', compact('data'));
  }

  public function view($id)
  {
    $view = $this->getSingleSalary($id);
    return view('admin.salary-details.view', compact('view'));
  }

  public function setContributionAmount()
  {
    return view('admin.salary-details.contribution');
  }


  /* ======================================================================= */
}
