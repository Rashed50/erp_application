<?php

namespace App\Http\Controllers\DataServices;

use App\Models\bdoffice_payment_detail;
use App\Models\BdOfficePayment;
use App\Enums\BdOfficePaymentStatusEnum;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class BdOfficePaymentDataService
{
 
    public function getBdOfficePaymentApprovedOrCreatedEmployeeList(){

        return BdOfficePayment::
          // where('paid_status',1)
          // orWhere('status',5) // BdOfficePaymentStatusEnum::Approved
          // ->orWhere('status',1) // BdOfficePaymentStatusEnum::Approval_Pending
           //->take($limit)
           get();
    }

    public function getBDOfficePaymentPendingList($limit){
        return BdOfficePayment::where('paid_status',0)
        ->orWhere('status',5) //BdOfficePaymentStatusEnum::Approved
        ->take($limit)->get();
    }

    public function insertBdOfficePaymentSetupInfo($emp_id, $approved_amount,$insert_by){
         
       return BdOfficePayment::insertGetId([            
            'approved_amount' => $approved_amount,
            'emp_auto_id' => $emp_id,
			'approved_amount_tk' => $approved_amount,
            'approved_date' => Carbon::now()->format('Y-m-d'),            
            'approved_by' => $insert_by,
            'created_at' => Carbon::now()->format('Y-m-d'),
            'insert_by' => $insert_by,
            'status'=> 5,// BdOfficePaymentStatusEnum::Approved,
          ]);
    }

    
    public function updateBdOfficePaymentApprovedAmount($bdofpay_auto_id,$emp_auto_id,$approved_amount,$updateBy){
        
      return  $update = BdOfficePayment::where('bdofpay_auto_id', $bdofpay_auto_id)->update([
            'approved_amount' => $approved_amount,
            'update_by' => $updateBy,
            'updated_at' => Carbon::now(),
        ]);
    }

    public function updateApprovedAmountPaymentCompeletedFromBdOffice($bdofpay_auto_id,$updateBy){
        
        return  $update = BdOfficePayment::where('bdofpay_auto_id', $bdofpay_auto_id)->update([
              'paid_status' => 1, // competed
              'status' => 15,// BdOfficePaymentStatusEnum::Completed, // competed
              'update_by' => $updateBy,
              'updated_at' => Carbon::now(),
          ]);
      }

    public function getAEmployeePaymentFromBdOfficeDetails($id){
       return BdOfficePayment::where('bdofpay_auto_id', $id)
          ->leftjoin('employee_infos', 'bd_office_payments.emp_auto_id', '=', 'employee_infos.emp_auto_id')
          ->first();
    }

    public function getAEmployeeBDOfficePaymentApprovedRecords($empl_auto_id){
        return BdOfficePayment::where('emp_auto_id', $empl_auto_id)
                ->get();
     }

    public function insertFromBdOfficePaymentDetailsInformation($bdofpay_auto_id,$emp_auto_id,$sar_amount,$bdt_paid_amount,$exchange_rate,$receiver_name,
    $receiver_address,$receiver_mobile,$relation_with_emp_id,$payment_received_date,$payment_slip_path,
    $payment_status,$payment_method,$transaction_details){
       return bdoffice_payment_detail::insertGetId([  
             'bdofpay_auto_id' => $bdofpay_auto_id, 
             'emp_auto_id' => $emp_auto_id,
             'sar_paid_amount' => $sar_amount,           
             'bdt_paid_amount' => $bdt_paid_amount, 
             'exchange_rate' => $exchange_rate,
             'receiver_name' => $receiver_name, 
             'receiver_address' => $receiver_address, 
             'receiver_mobile' => $receiver_mobile,     
             'relation_with_emp_id' => $relation_with_emp_id,
             'payment_received_date' => $payment_received_date,
             'payment_slip' => $payment_slip_path,
             'payment_status' => $payment_status,
             'payment_method' => $payment_method,
             'transaction_details' => $transaction_details,
             'receiver_name' => $receiver_name,
             'insert_by' => Auth::user()->id,
             'approved_by' => Auth::user()->id,
             'update_by' => Auth::user()->id,
             'updated_at' => Carbon::now(),
         ]);
    }

    public function getAnEmployeeTotalPaymentFromBdOffice($emp_auto_id,$bdofpay_auto_id){
     
      return bdoffice_payment_detail::
                 where('bdofpay_auto_id', $bdofpay_auto_id)
                ->where('emp_auto_id',$emp_auto_id)->sum('sar_paid_amount');
    }
    
     public function getAnEmployeeTotalPaidFromBdOffice($emp_auto_id){
     
        return bdoffice_payment_detail::where('emp_auto_id',$emp_auto_id)->sum('sar_paid_amount');
      }



     /*
        ========================================================
        ======================== Report ========================
        ========================================================
    */

    public function getBdOfficePaymentEmployeeList($startDate,$endDate,$payment_status){
         if($payment_status == null || $payment_status == 0){
            return BdOfficePayment::whereBetween('approved_date',[$startDate,$endDate])->get();
        }else { 
            return BdOfficePayment::where('status', $payment_status)
            ->whereBetween('approved_date',[$startDate,$endDate])
            ->get();
        }
    }

    public function getBdOfficePaymentDetailsForAnEmployee($emp_auto_id){
       
       return bdoffice_payment_detail::
                 where('emp_auto_id',$emp_auto_id)->get();
     }

     public function getBdOfficePaymentDetailsDateToDate($startDate,$endDate){
       
        return bdoffice_payment_detail::         
                whereBetween('updated_at',[$startDate,$endDate])->get();
      }
      
    public function getAnEmployeePaymentFromBdOfficeRecordsByEmployeeAutoId($emp_auto_id){
            return bdoffice_payment_detail::where('emp_auto_id',$emp_auto_id)->get();
      }


}
