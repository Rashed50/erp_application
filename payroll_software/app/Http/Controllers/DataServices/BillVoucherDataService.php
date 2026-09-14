<?php

namespace App\Http\Controllers\DataServices;

use App\Models\InvoiceRecords;
use App\Models\InvoiceRecordDetails;
use App\Models\InvoiceSummary;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BillVoucherDataService
{


     public function saveBillInvoiceRecord(
        $mainContID,
        $subContID,
        $bankInfoId,
        $projectId,
        $submittedEmpId,
        $item_grand_total,
        $invoiceNo,
        $invoiceStatus,
        $submittedDate,
        $invoice_from_date,
        $invoice_to_date,
        $working_month,
        $year,
        $invoice_type,
        $remarks,
        $percent_of_vat,
        $percent_of_retention,
        $total_vat,
        $total_retention,
        $total_with_vat, $contract_no
    ) {
        return  $invoiceRecordId = InvoiceRecords::insertGetId([
            'income_sources_id' => 2,
            'invoice_no' => $invoiceNo,
            'contract_no' =>$contract_no,
            'main_contractor_id' => $mainContID,
            'sub_contractor_id' => $subContID,
            'percent_of_vat' => $percent_of_vat,
            'total_vat' => $total_vat,
            'percent_of_retention' => $percent_of_retention,
            'total_retention' => $total_retention,
            'total_amount' => $total_with_vat,
            'items_grand_total_amount' => $item_grand_total,
            'remarks'  => $remarks,
            'project_id' => $projectId,
            'submitted_date' => $submittedDate,
            'invoice_from_date' => $invoice_from_date,
            'invoice_to_date' => $invoice_to_date,
            'working_month' => $working_month, 
            'year' => $year,
            'invoice_type' => $invoice_type,
            'entered_by_id' => $submittedEmpId,
            'invoice_status_id' => $invoiceStatus,
            'bank_details_id' => $bankInfoId,
            'status' => true,
            'created_at' => Carbon::now(),
        ]);
    }


    // update Invoice Record Infos From invoice Edit UI
    public function updateInvoiceRecordInfosByInvoiceRecordAutoID(
        $invoiceRecordAutoID,
        $mainContID,
        $subContID,
        $bankInfoId,
        $projectId,
        $submittedDate,
        $submittedEmpId,
        $startDate,
        $invoiceStatus,
        $remarks,
        $invoiceNo,
        $item_grand_total,
        $total_vat,
        $percent_of_vat,
        $total_retention,
        $percent_of_retention,
        $total_with_vat,
        $total_with_vatAndExclRetention,$contract_no
    ) {
        return InvoiceRecords::where('invoice_record_auto_id', $invoiceRecordAutoID)->update([
            'income_sources_id' => 2,
            'invoice_no' => $invoiceNo,
            //'contract_no' =>$contract_no,
            'percent_of_vat' => $percent_of_vat,
            'total_vat' => $total_vat,
            'items_grand_total_amount' => $item_grand_total,
            'percent_of_retention' => $percent_of_retention,
            'total_retention' => $total_retention,
            'total_amount' => $total_with_vat,
            'remarks' => $remarks,
            'project_id' => $projectId,
            'bank_details_id' => $bankInfoId,
            'main_contractor_id' => $mainContID,
            'sub_contractor_id' => $subContID,
            'submitted_date' => $submittedDate,
            'entered_by_id' => $submittedEmpId,
            'invoice_status_id' => $invoiceStatus,
            'status' => true,
            'updated_at' => Carbon::now(),
        ]);
    }
    
     public function updateAnInvoicePaymentStatus($invoice_record_auto_id,$invoice_status){
  
        return InvoiceRecords::where('invoice_record_auto_id', $invoice_record_auto_id)->update([          
            'invoice_status_id' => $invoice_status,
            'updated_at' => Carbon::now(),
        ]);
    }
    
    public function deleteAnInvoiceRecordByInvoiceRecordId($invoiceRecordId)
    {
        return InvoiceRecords::where('invoice_record_auto_id', $invoiceRecordId)->delete();
    }
    
    // last fifty records for table
    public function getLastFiftyInvoiceRecords(){
        
        return InvoiceRecords::with('mainContractor', 'subContractor')
                ->leftjoin('project_infos', 'invoice_records.project_id', '=', 'project_infos.proj_id')
                ->leftjoin('users', 'invoice_records.entered_by_id', '=', 'users.id')
                ->orderBy('invoice_record_auto_id', 'DESC')->take(50)->get();
    }
    public function getInvoiceRecordWithProjectDetailsByInvoiceRecordId($invoiceRecordId)
    {
        return InvoiceRecords::with('mainContractor')->with('subContractor')->with('bankInfo')->with('SubmitedBy')->where('invoice_record_auto_id', $invoiceRecordId)
            ->leftjoin('project_infos', 'invoice_records.project_id', '=', 'project_infos.proj_id')
            ->first();
    }
    public function getInvoiceRecordById($invoiceRecordId)
    {
        return InvoiceRecords::where('invoice_record_auto_id', $invoiceRecordId)->first();
    }

    public function getInvoiceRecordDetilaByInvoiceRecordId($invoice_record_id)
    {
        return InvoiceRecordDetails::where('invoice_record_id', $invoice_record_id)->get();
    }
    
    public function checkThisInvoiceRecordAlreadyExistByInvoiceNumber($invoice_no,$project_id)
    {
        return InvoiceRecords::where('invoice_no', $invoice_no)->where('project_id',$project_id)->count() > 0 ? true : false;
    }

     
    public function getInvoiceRecordByInvoiceNumber($invoice_no)
    {

        return InvoiceRecords::with('mainContractor')->with('subContractor')->with('bankInfo')->with('SubmitedBy')->where('invoice_no', $invoice_no)
            ->leftjoin('project_infos', 'invoice_records.project_id', '=', 'project_infos.proj_id')
            ->first();
    }
    public function getAnSingleInvoiceRecordAllDetailsByProjectInoviceMonthYear($project_id,$month,$year)
    {

       return $record = InvoiceRecords::with('mainContractor')->with('subContractor')->with('bankInfo')->with('SubmitedBy')
            ->where('project_id', $project_id)
            ->where('working_month', $month)
            ->where('year', $year)
            ->leftjoin('project_infos', 'invoice_records.project_id', '=', 'project_infos.proj_id')
            ->first();  
            
            
    }


    public function saveNewInvoiceDetailsRecord($carts, $invoiceRecordId)
    {
        foreach ($carts  as $item) {

            $invRecord = new InvoiceRecordDetails();
            $invRecord->invoice_record_id = $invoiceRecordId;
            $invRecord->items_details = $item->name;
            $invRecord->percent_of_retention = 0; // $item->percent_of_retention;
            $invRecord->percent_of_vat = 0; // $item->options->cartVat;
            $invRecord->quantity = $item->qty;
            $invRecord->rate = $item->price;
            $invRecord->total = $item->options->cartTotal;
            $invRecord->area_no = $item->options->areaNo;
            $invRecord->item_no = $item->options->itemNo;
            $invRecord->item_unit = $item->options->qty_unit;
            $invRecord->created_at = Carbon::now();
            $invRecord->save();
        }
    }
    public function saveANewItemDetailsRecord($item, $invoiceRecordId)
    { 
            $invRecord = new InvoiceRecordDetails();
            $invRecord->invoice_record_id = $invoiceRecordId;
            $invRecord->items_details = $item->name;
            $invRecord->percent_of_retention = 0; // $item->percent_of_retention;
            $invRecord->percent_of_vat = 0; // $item->options->cartVat;
            $invRecord->quantity = $item->qty;
            $invRecord->rate = $item->price;
            $invRecord->total = $item->options->cartTotal;
            $invRecord->area_no = $item->options->areaNo;
            $invRecord->item_no = $item->options->itemNo;
            $invRecord->item_unit = $item->options->qty_unit;
            $invRecord->created_at = Carbon::now();
            $invRecord->save();
        
    }

    // update Bill Voucher Informations from Edit UI
    public function updateBillVoucherInvoiceRecordInformations($carts, $invoiceRecordAutoID)
    {
    
        foreach($carts  as $cartItem) {
           
            $invoiceRecordDetailsAutoId =   $cartItem->options->invoiceRecordAutoID;              
            if ($invoiceRecordDetailsAutoId) { 

                InvoiceRecordDetails::where('inv_record_auto_id',$invoiceRecordDetailsAutoId)->update([
                    'items_details' => $cartItem->name,
                    'percent_of_retention' => 0, // $item->percent_of_retention;
                    'percent_of_vat' => 0, // $item->options->cartVat;
                    'quantity' => $cartItem->qty,
                    'rate' => $cartItem->price,
                    'total' => $cartItem->options->cartTotal,
                    'area_no' => $cartItem->options->areaNo,
                    'item_no' => $cartItem->options->itemNo,
                    'item_unit' => $cartItem->options->qty_unit,
                    'updated_at' => Carbon::now(),
                ]);
            }
            else {
                $this->saveANewItemDetailsRecord($cartItem,$invoiceRecordAutoID);
           }
        }
    }
    
    public function deleteInvoiceDetailsRecordByInvoiceRecordId($invoice_record_id){  
        return InvoiceRecordDetails::where('invoice_record_id', $invoice_record_id)->delete();       
    }
    public function deleteInvoiceDetailsRecordThoseAreNotInCart($newCarts, $previousCartDBRecrods){

      
        if($newCarts == null || count($newCarts) == 0){
                return;
        }     
        foreach($previousCartDBRecrods as $adbrecord){

            $checker = 0;
            foreach($newCarts as $acart){
                 $cart_inv_record_details_auto_id  = $acart->options->invoiceRecordAutoID;                  
                if($cart_inv_record_details_auto_id  == $adbrecord->inv_record_auto_id){
                    $checker = 1;                    
                    break;
                }
            }          
            if($checker == 0){
                InvoiceRecordDetails::where('inv_record_auto_id', $adbrecord->inv_record_auto_id)->delete();
            }
        }

    }
 

    // Searching Bill Voucher Details By Voucher No For Voucher Update AJAX Request
    public function getEVoucherInformationsByInvoiceNo($invoiceNo)
    {
        return InvoiceRecords::where('invoice_no', $invoiceNo)->first();
    }
    public function getEVoucherCartInformationsByInvoiceRecordID($invoiceRecordId)
    {
        return InvoiceRecordDetails::where('invoice_record_id', $invoiceRecordId)->get();
    }
    
     public function getInvoiceWithQRCodeReport($company_ids,$project_ids,$invoice_status){

        
          if($invoice_status == null){
    
            return InvoiceRecords:: select('invoice_records.invoice_no','invoice_records.submitted_date','invoice_records.status','invoice_records.total_vat','invoice_records.total_retention'
            ,'invoice_records.total_amount','invoice_records.items_grand_total_amount','main_contractor_infos.en_name','sub_company_infos.sb_comp_name','project_infos.proj_name','users.name as InsertBy')
            ->whereIn('invoice_records.project_id', $project_ids)
            ->whereIn('invoice_records.sub_contractor_id', $company_ids)
            ->leftjoin('main_contractor_infos', 'invoice_records.main_contractor_id', '=', 'main_contractor_infos.mc_auto_id')
            ->leftjoin('sub_company_infos', 'invoice_records.sub_contractor_id', '=', 'sub_company_infos.sb_comp_id')
            ->leftjoin('project_infos', 'invoice_records.project_id', '=', 'project_infos.proj_id')
            ->leftjoin('users', 'invoice_records.entered_by_id', '=', 'users.id')
             ->get();

          }else{
            return InvoiceRecords::select('invoice_records.invoice_no','invoice_records.submitted_date','invoice_records.status','invoice_records.total_vat','invoice_records.total_retention'
            ,'invoice_records.total_amount','main_contractor_infos.en_name','sub_company_infos.sb_comp_name','project_infos.proj_name','users.name')
            ->whereIn('invoice_records.project_id', $project_ids)
            ->whereIn('invoice_records.sub_contractor_id', $company_ids)
            ->where('invoice_records.status', $invoice_status)
            ->leftjoin('main_contractor_infos', 'invoice_records.main_contractor_id', '=', 'main_contractor_infos.mc_auto_id')
            ->leftjoin('sub_company_infos', 'invoice_records.sub_contractor_id', '=', 'sub_company_infos.sb_comp_id')
            ->leftjoin('project_infos', 'invoice_records.project_id', '=', 'project_infos.proj_id')
            ->leftjoin('users', 'invoice_records.entered_by_id', '=', 'users.id')
             ->get();

          } 

       }
    
    
        public function getAProjectInvoiceSummaryAmountRecord($project_id,$month,$year){
             return $result = InvoiceRecords::select(
                DB::raw("COUNT(invoice_record_auto_id ) as count_of_invoice"),
                DB::raw('sum(items_grand_total_amount) as  grand_total_amount'),
                DB::raw('sum(total_retention) as  total_retention'),
                DB::raw('sum(total_vat) as  total_vat'),
                DB::raw('sum(total_amount) as total_receivable_amount')
                )->where('project_id',$project_id)
                ->where('year',$year)
                ->where('working_month',$month)
                ->first();
        
    }
    
    // ====================================================================
    // ======================= INVOICE SUMMARY ============================
    // ====================================================================
        function saveNewInoviceSummaryInformation($project_id,$month,$year,$invoice_amount,$vat_amount,
        $retention_amount,$vat,$retention,$submit_date,$receivable_amount,$remarks,$invoice_file,$invoice_no,$contract_reference,$login_id){

            return InvoiceSummary::insertGetId([
                'invoice_no' => $invoice_no,
                'contract_reference' => $contract_reference,
                'project_id' => $project_id,
                'month' => $month,
                'year' => $year,
                'invoice_amount' => $invoice_amount,
                'vat_amount' => $vat_amount,
                'retention_amount'=>$retention_amount,
                'retention' => $retention,
                'vat' => $vat,
                'receivable_amount' => $receivable_amount,
                'invoice_date' => $submit_date,
                'submit_date' => $submit_date,
                'invoice_status' => 0,
                'remarks' => $remarks,
                'invoice_file'=>$invoice_file,
                'created_by' => $login_id,
                'created_at' => Carbon::now(),
            ]);

    }


    function updateInoviceSummaryInformation($inv_sum_auto_id, $project_id,$month,$year,$invoice_amount,$vat_amount,
        $retention_amount,$vat,$retention,$submit_date,$receivable_amount,$remarks,$inv_status,$invoice_file,$invoice_no,$contract_reference,$login_id){

            return InvoiceSummary::where('inv_sum_auto_id',$inv_sum_auto_id)->update([
                'invoice_no' => $invoice_no,
                'contract_reference' => $contract_reference,
                'project_id' => $project_id,
                'month' => $month,
                'year' => $year,
                'invoice_amount' => $invoice_amount,
                'vat_amount' => $vat_amount,
                'retention_amount'=>$retention_amount,
                'retention' => $retention,
                'vat' => $vat,
                'receivable_amount' => $receivable_amount,
                'invoice_date' => $submit_date,
                'submit_date' => $submit_date,
                'invoice_status' => $inv_status,
                'remarks' => $remarks,
                'invoice_file'=>$invoice_file,
                'updated_by' => $login_id,
                'updated_at' => Carbon::now(),
            ]);

    }

    function checkThisInvoiceNoIsAlreadyExist($invoice_no ){
        return (InvoiceSummary::where('invoice_no',$invoice_no)->count()) > 0 ? true:false;
    }


    function deleteInvoiceSummaryByInvoiceSummaryAutoId($inv_sum_auto_id ){
        return InvoiceSummary::where('inv_sum_auto_id',$inv_sum_auto_id)->delete();  
    }

    function getInvoiceSummaryByInvoiceSummaryAutoId($inv_sum_auto_id ){
        return InvoiceSummary::where('inv_sum_auto_id',$inv_sum_auto_id)
                ->leftjoin('project_infos', 'invoice_summaries.project_id', '=', 'project_infos.proj_id')
                ->leftjoin('months', 'invoice_summaries.month', '=', 'months.month_id')
                ->first();  
    }


    function getInvoiceSummaryByProjectId($project_id){
        return InvoiceSummary::where('project_id',$project_id)
                ->leftjoin('project_infos', 'invoice_summaries.project_id', '=', 'project_infos.proj_id')
                ->leftjoin('months', 'invoice_summaries.month', '=', 'months.month_id')
                ->get();  
    }

    function getInvoiceSummaryByProjectIdAndStatus($project_id,$invoice_status){
        if(is_null($invoice_status)){
            return InvoiceSummary::where('project_id',$project_id)
            ->leftjoin('project_infos', 'invoice_summaries.project_id', '=', 'project_infos.proj_id')
            ->leftjoin('months', 'invoice_summaries.month', '=', 'months.month_id')
            ->get(); 
        }else {
            return InvoiceSummary::where('project_id',$project_id)->where('invoice_status',$invoice_status)
            ->leftjoin('project_infos', 'invoice_summaries.project_id', '=', 'project_infos.proj_id')
            ->leftjoin('months', 'invoice_summaries.month', '=', 'months.month_id')
            ->get(); 
        }
        
    }

    
    
    
    // function saveNewInoviceSummaryInformation($project_id,$month,$year,$invoice_amount,$vat_amount,
    //     $retention_amount,$vat,$retention,$submit_date,$receivable_amount,$remarks,$invoice_file){
 
    //         return InvoiceSummary::insertGetId([
    //             'project_id' => $project_id,
    //             'month' => $month,
    //             'year' => $year,
    //             'invoice_amount' => $invoice_amount,
    //             'vat_amount' => $vat_amount,
    //             'retention_amount'=>$retention_amount,
    //             'retention' => $retention,
    //             'vat' => $vat,
    //             'receivable_amount' => $receivable_amount,  
    //             'invoice_date' => $submit_date,     
    //             'submit_date' => $submit_date,        
    //             'invoice_status' => 0,
    //             'remarks' => $remarks,
    //             'invoice_file'=>$invoice_file,
    //             'created_at' => Carbon::now(),
    //         ]);

    // }
    // function getInvoiceSummaryByProjectId($project_id){
    //     return InvoiceSummary::where('project_id',$project_id)
    //             ->leftjoin('project_infos', 'invoice_summaries.project_id', '=', 'project_infos.proj_id')
    //             ->leftjoin('months', 'invoice_summaries.month', '=', 'months.month_id')
    //             ->get();  
    // }
    
    // function getInvoiceSummaryByProjectIdAndStatus($project_id,$invoice_status){
    //     if(is_null($invoice_status)){
    //         return InvoiceSummary::where('project_id',$project_id)
    //         ->leftjoin('project_infos', 'invoice_summaries.project_id', '=', 'project_infos.proj_id')
    //         ->leftjoin('months', 'invoice_summaries.month', '=', 'months.month_id')
    //         ->get(); 
    //     }else {
    //         return InvoiceSummary::where('project_id',$project_id)->where('invoice_status',$invoice_status)
    //         ->leftjoin('project_infos', 'invoice_summaries.project_id', '=', 'project_infos.proj_id')
    //         ->leftjoin('months', 'invoice_summaries.month', '=', 'months.month_id')
    //         ->get(); 
    //     }
        
    // }
    
    
    
}
