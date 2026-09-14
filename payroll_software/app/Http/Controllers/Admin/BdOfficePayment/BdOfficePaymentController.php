<?php

namespace App\Http\Controllers\Admin\BdOfficePayment;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DataServices\BdOfficePaymentDataService;
use App\Http\Controllers\Admin\Helper\UploadDownloadController;
use App\Http\Controllers\DataServices\EmployeeDataService;
use App\Http\Controllers\DataServices\CompanyDataService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;


class BdOfficePaymentController extends Controller
{
    
    function __construct(){
        $this->middleware('permission:bd office payment setup', ['only' => ['employeePaymentFromBdOfficeCreateForm']]);
        $this->middleware('permission:bd office payment employee list', ['only' => ['getBbOfficePaymentPendingEmployeeList']]);
     }

    /*
        ========================================================
        ========================Head Office Module==============
        ========================================================
    */
    public function employeePaymentFromBdOfficeCreateForm(){
          
          $all = (new BdOfficePaymentDataService())->getBdOfficePaymentApprovedOrCreatedEmployeeList();
          //dd($all);
          return view('admin.bdOffice-payment.index', compact('all'));
    }

    public function employeePaymentFromBdOfficeInsertRequest(Request $request){
      
          $insert = (new BdOfficePaymentDataService())->insertBdOfficePaymentSetupInfo($request->emp_id, $request->approved_amount,Auth::user()->id);
    
        if ($insert > 0 ) {
          Session::flash('success', 'Successfully Added');
          return Redirect()->back();
        } else {
          Session::flash('error', 'Operation Failed');
          return redirect()->back();
        }
       
    }

    public function bdOfficeEmployeePaymentSetupUpdateForm($id){
            
        $SinglePaymentInfo = (new BdOfficePaymentDataService())->getAEmployeePaymentFromBdOfficeDetails($id);
        return view('admin/bdOffice-payment.edit', compact('SinglePaymentInfo'));
    }

    public function bdOfficeEmployeePaymentSetupUpdateRequest(Request $request){
         
        $update = (new BdOfficePaymentDataService())->updateBdOfficePaymentApprovedAmount($request->bdofpay_auto_id,$request->emp_id,
        $request->approved_amount, Auth::user()->id
          );
         if ($update) {
          Session::flash('success', 'Successfully Updated');
          return Redirect()->route('employee.payment.create.from-bdoffice-payment');
        } else {
          Session::flash('error', 'Update Operation Failed');
          return redirect()->back();
        }
    }

    /*
        ========================================================
        ======================== BD Office Module==============
        ========================================================
    */

    public function getBbOfficePaymentPendingEmployeeList(){
        $all = (new BdOfficePaymentDataService())->getBDOfficePaymentPendingList(100);
       return view('admin.bdOffice-payment.bdoffice-payment-pending-list', compact('all'));
    }

    public function getBbOfficePaymentPendingEmployeeDetails($id){


      $employee = (new BdOfficePaymentDataService())->getAEmployeePaymentFromBdOfficeDetails($id);
      $employee->sar_paid_total_amount = (new BdOfficePaymentDataService())->getAnEmployeeTotalPaymentFromBdOffice($employee->emp_auto_id,$id);
      $employee->sar_unpaid_amount =  $employee->approved_amount - $employee->sar_paid_total_amount;
      return view('admin.bdOffice-payment.bdoffice-payment-update-form', compact('employee'));
    }

    
    public function employeePaymentFromBdOfficeUpdateRequest(Request $request){
       
      //// inserted  from  bd office
      //  dd($request->all());
        // if($request->has('print-checkbox'))
        // {
        //     $company = (new CompanyDataService())->findCompanryProfile();
        //     $employee = (new EmployeeDataService())->getAnEmployeeInfoByEmpAutoId($request->emp_auto_id);
        //     $bdoffice_pay_details = (new BdOfficePaymentDataService())->getAEmployeePaymentFromBdOfficeDetails($request->bdpay_auto_id);
            
        //     $employee->approved_amount = $request->approved_amount;
        //     $employee->sar_amount = $request->sar_amount;
        //     $employee->receiver_name = $request->receiver_name;
        //     $employee->receiver_address = $request->receiver_address;
        //     $employee->receiver_mobile =  $request->receiver_mobile;
        //     $employee->relation_with_emp = $request->relation_with_emp;
        //     $employee->payment_received_date = $request->payment_received_date;
        //     $employee->payment_method = $request->payment_method;
        //     $employee->transaction_details = $request->transaction_details;
        //   return view('admin.bdOffice-payment.bdoffice_payment_slip_with_empl_info', compact('employee','company','bdoffice_pay_details'));
        // }
   
        $payment_slip_path = "";
        if ($request->hasFile('payment_slip')) {
          $payment_slip_path =(new UploadDownloadController())->uploadBDOfficePaymentSlip($request->file('payment_slip'), null);
        }
 
        $update =  (new BdOfficePaymentDataService())->insertFromBdOfficePaymentDetailsInformation(
          $request->bdofpay_auto_id,
          $request->emp_auto_id,
          $request->sar_amount,
          $request->bdt_paid_amount,
          $request->exchange_rate,
          $request->receiver_name,
          $request->receiver_address,
          $request->receiver_mobile,
          $request->relation_with_emp,
          $request->payment_received_date,
          $payment_slip_path,
          1, // 1 = payment completed,
          $request->payment_method,
          $request->transaction_details
        );

        $totalPaidAmount = (new BdOfficePaymentDataService())->getAnEmployeeTotalPaymentFromBdOffice( $request->emp_auto_id,$request->bdofpay_auto_id);
        if( (int)( $totalPaidAmount) >= ((int) $request->approved_amount) ){
          (new BdOfficePaymentDataService())->updateApprovedAmountPaymentCompeletedFromBdOffice($request->bdofpay_auto_id,Auth::user()->id);
        } 
        if ($update) {
          Session::flash('success', 'Successfully Updated');
          return Redirect()->route('employee.payment.bdoffice-payment-pending');
        } else {
          Session::flash('error', 'Operation Failed');
          return redirect()->back();
        }
    }


    /*
        ========================================================
        ======================== Report ========================
        ========================================================
    */
     
   
   public function getReportUIForPaymentFromBdOffice(){
      return view('admin.report.bdoffice.bdoffice_report_ui');
  }
  public function showApprovedEmployeesReportPaymentFromBdOffice(Request $request){

     
       $company = (new CompanyDataService())->findCompanryProfile();
       $employees = (new BdOfficePaymentDataService())->getBdOfficePaymentEmployeeList($request->start_date, $request->end_date,$request->payment_status);
      
      return view('admin.report.bdoffice.payment_approved_emp_list', compact('employees', 'company'));
  }

  public function showBdOfficePaymetDetailsForAnEmployee($employee_id){
        $anEmployee = (new EmployeeDataService())->getAnEmployeeInfoByEmpId($employee_id); 
        if($employee == null)
        {
          Session::flash('error','Employee Not Found ');
            return redirect()->back();
        }
         $company = (new CompanyDataService())->findCompanryProfile();       
         $employees = (new BdOfficePaymentDataService())->getBdOfficePaymentDetailsForAnEmployee($anEmployee->emp_auto_id);
         return view('admin.report.bdoffice.bdoffice_payment_details', compact('employees', 'company'));
  }
  public function showBdOfficePaymetDetailsDateToDate(Request $request){
       
     
      $company = (new CompanyDataService())->findCompanryProfile();    
      $employees = (new BdOfficePaymentDataService())->getBdOfficePaymentDetailsDateToDate($request->start_date, $request->end_date);
      return view('admin.report.bdoffice.bdoffice_payment_details', compact('employees', 'company'));
  }

   public function showAnEmployeeBdOfficePaymetSummary(Request $request){         
      
      $employee = (new EmployeeDataService())->getAnEmployeeInfoByEmpId($request->employee_id); 
      if($employee == null)
      {
        Session::flash('error','Employee Not Found ');
          return redirect()->back();
      } 
      return $this->showAnEmployeePaymentFromBDOfficeDetailsReport($employee);  

    }
    // BD OFfice Payment Summary Report called from employee report controller also
     public function showAnEmployeePaymentFromBDOfficeDetailsReport($employee){

      $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);      
      $approved_records = (new BdOfficePaymentDataService())->getAEmployeeBDOfficePaymentApprovedRecords($employee->emp_auto_id);   
      $payment_records = (new BdOfficePaymentDataService())->getAnEmployeePaymentFromBdOfficeRecordsByEmployeeAutoId($employee->emp_auto_id);
      return view('admin.report.bdoffice.bdoffice_payment_employee_summary', compact('employee','approved_records','payment_records', 'company'));
    }
  

}
