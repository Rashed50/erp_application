<?php

namespace App\Http\Controllers\Admin\Employee_Bank;

use App\Http\Controllers\Controller; 
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DataServices\{EmployeeRelatedDataService,ProjectDataService}; 
use App\Http\Controllers\DataServices\EmployeeDataService;
use App\Http\Controllers\DataServices\CompanyDataService;
use App\Http\Controllers\DataServices\AuthenticationDataService;
use App\Http\Controllers\Admin\Helper\HelperController;
use App\Enums\SalaryPaymentMethodEnum;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Session;


class EmployeeBankDetailsController extends Controller
{

    function __construct()  {       
      $this->middleware('permission:emp_bank_info_add', ['only' => ['storeNewBankName', 'loadEmployeeBankDetailsView','storeEmployeeBankDetailsInformation']]);
      $this->middleware('permission:emp_bank_info_update', ['only' => ['updateEmployeeBankDetailsInformation']]);
      $this->middleware('permission:emp_bank_info_update', ['only' => ['searchEmployeeBankInformation','updateAnEmployeeBankDetailsInformation']]);
       $this->middleware('permission:emp_bank_info_report', ['only' => ['showListOfEmployeeWithBankInformationReport']]);
    } 
    
    
    public function storeNewBankName(Request $request){
        try{
             
            if((int)$request->bank_auto_id){
                (new EmployeeRelatedDataService())->updateBankNameByAutoId($request->bank_auto_id,$request->bn_name);

            }elseif(is_null($request->bn_name) == false){
                (new EmployeeRelatedDataService())->insertNewBankName($request->bn_name,Auth::user()->branch_office_id);
            }
            
            $bank_names = (new EmployeeRelatedDataService())->getListOfBankNameOfABranchForDropdown(Auth::user()->branch_office_id);
            return response()->json(['success' => true, 'status' => 200, 'message' => "Successfully Saved", 'data' => $bank_names]);

        }catch(Exception $ex){
            return response()->json(['success' => false, 'status' => 403,'error'=>'error', 'message' => 'Operation Failed']); 
        }       
    }
    
    public function loadEmployeeBankDetailsView(){
        try{ 
            $bank_names = (new EmployeeRelatedDataService())->getListOfBankNameOfABranchForDropdown(Auth::user()->branch_office_id);              
            return view('admin.employee-info.bank_details.index',compact('bank_names'));
        }catch(Exception $ex){
            return "Operation Failed, Please Reload The Page";
        }
    }
 
 
    
   public function storeEmployeeBankDetailsInformation(Request $request){
    
     try{ 
            
            if($request->ebd_auto_id){
                // update bank information
                 return $this->updateAnEmployeeBankDetailsInformation($request);
            }
            else{

                $arecord = (new EmployeeRelatedDataService())->getAnEmployeeBankInformationRecordByEmployeeAutoId($request->emp_auto_id);
                if($arecord){

                    if($arecord->acc_number == $request->account_number ){
                            return response()->json(['success' => false, 'status' => 403,'error'=>'error', 'message' => 'Account Number Already Added']); 
                    }else {
                            (new EmployeeRelatedDataService())->inactiveAnEmployeePreviousBankAccount($arecord->ebd_auto_id,"New Bank Info Added",Auth::user()->id,carbon::now());
                            $isSuccess = (new EmployeeRelatedDataService())->insertEmployeeBankDetailsInformation($request->emp_auto_id,$request->acc_holder_name,
                            $request->account_number,$request->acc_iban,$request->bank_name,$request->acc_remarks,Auth::user()->id,carbon::now());
                            if($isSuccess){ 
                                
                                // login user activities record
                                (new AuthenticationDataService())->InsertLoginUserActivity(14,2, Auth::user()->id, $request->emp_auto_id,null);
                                
                                return response()->json(['success' => true, 'status' => 200, 'message' => 'Successfully Completed']);
                            }else{ 
                                return  response()->json(['success' => false, 'status' => 403,'error'=>'error', 'message' => 'Data Error, Operation Failed ']);
                            } 
                    } 
                }
                else{
                    $isSuccess = (new EmployeeRelatedDataService())->insertEmployeeBankDetailsInformation($request->emp_auto_id,$request->acc_holder_name,
                    $request->account_number,$request->acc_iban,$request->bank_name,$request->acc_remarks,Auth::user()->id,carbon::now());
                    if($isSuccess){ 
                         // login user activities record
                        (new AuthenticationDataService())->InsertLoginUserActivity(14,1, Auth::user()->id, $request->emp_auto_id,null);
                       
                        return response()->json(['success' => true, 'status' => 200, 'message' => 'Successfully Completed']);
                    }else{ 
                        return response()->json(['success' => false, 'status' => 403,'error'=>'error', 'message' => 'Data Error, Operation Failed ']);
                    }              
                }
            }          
        }catch(Exception $ex){ 
          return response()->json(['success' => false, 'status' => 403,'error'=>'error', 'message' => 'Operation Failed ']);            
        } 
 
   }

   // update an employee bank details
    private function updateAnEmployeeBankDetailsInformation(Request $request){
        try{  

            $isSuccess = (new EmployeeRelatedDataService())->updateEmployeeBankDetailsInformation($request->ebd_auto_id,$request->acc_holder_name,
            $request->account_number,$request->acc_iban,$request->bank_name,$request->acc_remarks,Auth::user()->id,carbon::now());
            if($isSuccess){ 
                
                 // login user activities record
                (new AuthenticationDataService())->InsertLoginUserActivity(14,2, Auth::user()->id, $request->emp_auto_id,null);
                       
                return response()->json(['success' => true, 'status' => 200, 'message' => 'Successfully Completed']);
            }else{ 
                return response()->json(['success' => false, 'status' => 403,'error'=>'error', 'message' => 'Data Error, Operation Failed ']);              
            } 
        }catch(Exception $ex){ 
            return response()->json(['success' => false, 'status' => 403,'error'=>'error', 'message' => 'Operation Failed ']); 
        } 
    }
    
        // searching employee bank details
    public function searchAnEmployeeBankInformation(Request $request){
        try{  

            $arecord = (new EmployeeRelatedDataService())->getAnEmployeeDetailsAndBankInformationRecordByEmployeeId($request->employee_id,Auth::user()->branch_office_id);
            if($arecord){
                return response()->json(['success' => true, 'status' => 200, 'data' => $arecord]);
            }else {
                return response()->json(['success' => false, 'status' => 403,'error'=>'error', 'message' => 'Employee Bank Information not Found']); 
            }    
         
        }catch(Exception $ex){ 
            return response()->json(['success' => false, 'status' => 403,'error'=>'error', 'message' => 'System Operation Failed ']); 
        } 
    }

          //  Employee List with bank information
      public function showListOfEmployeeWithBankInformationReport(Request $request){
             
          try{
              
              
            $permitted_project_ids = (new ProjectDataService())->getLoginUserAccessPermissionProjectIDsArray(Auth::user()->id);
            $employees = [];
            
            foreach($permitted_project_ids as $pid){
               
                $ap_emp = (new EmployeeDataService())->getListOFEmployeesThoseHaveBankInformationReport($pid,Auth::user()->branch_office_id);
                if(count($ap_emp))
                    $employees = array_merge($employees,$ap_emp);
            }
            
            $report_title = "Employees Bank Information";
            $company = (new CompanyDataService())->findCompanryProfile();
            $login_user = Auth::user()->name;
            return view('admin.employee-info.bank_details.emp_list_bank_details', compact('employees','company', 'report_title','login_user'));

        }catch(Exception $ex){
            return ('Operation Failed, Please Try Again');

        }
    
      }
      // Employee Salary Payment Method Update
      public function updateEmployeeSalaryPaymentMethod(Request $request){
            // update employee salary payment methond
            $employee = (new EmployeeRelatedDataService())->getAnEmployeeDetailsAndBankInformationRecordByEmployeeId($request->employee_id,Auth::user()->branch_office_id);
     
            if($employee == null){
                return response()->json(["status" =>403, "success" => false, 'error' => 'error','message'=>  "Employee Information not Found"]);
            }
            else if($employee->acc_number == null && $request->payment_method == SalaryPaymentMethodEnum::Bank){ 
                return response()->json(["status" =>403, "success" => false, 'error' => 'error','message'=>  "Bank Information not Found"]);
            }
            else {
                $is_success = (new EmployeeDataService())->updateAnEmployeeSalaryPaymentMethodByEmployeeAutoId($employee->emp_auto_id,$request->payment_method);
                if($is_success){
                    // login user activities record
                    (new AuthenticationDataService())->InsertLoginUserActivity(15,2,Auth::user()->id,$employee->emp_auto_id,null);
                    return response()->json(["status" =>200, "success" => true, 'message'=>  "Successfully Updated"]);
                }else {
                    return response()->json(["status" =>403, "success" => false, 'error' => 'error','message'=>  "Update Operation Failed"]);
                }               
            }
      }
  
 
}
