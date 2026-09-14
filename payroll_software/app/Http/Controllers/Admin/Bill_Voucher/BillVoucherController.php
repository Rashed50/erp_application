<?php

namespace App\Http\Controllers\Admin\Bill_Voucher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\DataServices\ProjectDataService;
use App\Http\Controllers\DataServices\EmployeeDataService;
use App\Http\Controllers\DataServices\EmployeeRelatedDataService;
use App\Http\Controllers\DataServices\AuthenticationDataService;
use App\Http\Controllers\DataServices\CompanyDataService;
use App\Http\Controllers\DataServices\BillVoucherDataService;
use App\Http\Controllers\DataServices\SalaryProcessDataService;
use App\Http\Controllers\Admin\Helper\HelperController;
use App\Http\Controllers\Admin\Helper\UploadDownloadController;
use App\Models\InvoiceRecordDetails;
use App\Models\InvoiceRecords;
use Salla\ZATCA\GenerateQrCode;
use Salla\ZATCA\Tags\InvoiceDate;
use Salla\ZATCA\Tags\InvoiceTaxAmount;
use Salla\ZATCA\Tags\InvoiceTotalAmount;
use Salla\ZATCA\Tags\Seller;
use Salla\ZATCA\Tags\TaxNumber;
use Carbon\Carbon;
use Session;
use Auth;
use Cart;


class BillVoucherController extends Controller
{

    // middleware
    function __construct()
    {
        $this->middleware('permission:evoucher-list', ['only' => ['qrcodeCreateUI','generateQRCode']]);  // Only QRCode 
        $this->middleware('permission:invoice_with_qr_code_create',['only' => ['BillVoucherFormLoad','eVoucherProccess']]); // Invoice with QRCode
        $this->middleware('permission:invoice_with_qr_code_update',['only' => ['loadBillVoucherFormLoadForInvoiceUpdate','updatedEVoucherAndCreateNewQRCode']]); // Invoice with QRCode
        $this->middleware('permission:invoice_with_qrcode_status_update', ['only' => ['updatedQRCodeInvoiceStatus']]);  // Update QR Invoice Status
        $this->middleware('permission:invoice_with_qrcode_report', ['only' => ['processQrCodeInvoiceReport']]);  // QRCode Invoice Report
        $this->middleware('permission:invoice_and_salary_summary_statement_create', ['only' => ['loadInvoiceAndSalaryStatementProcessUI','getAProjectSalaryInfoAndInvoiceStatementSummary','processAProjectSalaryInfoAndInvoiceStatementSummary']]);  // QRCode Invoice Report
        
        $this->middleware('permission:invoice_summary_add',['only' =>['loadInvoiceSummaryFormUI','saveNewInvoiceSummaryInformation']]);
        $this->middleware('permission:invoice_summary_edit',['only' =>['searchInvoiceSummary','getInvoiceSummaryForEdit','saveNewInvoiceSummaryInformation']]);
        $this->middleware('permission:invoice_summary_delete',['only' =>['deleteInvoiceSummaryRecord']]);
        $this->middleware('permission:invoice_summary_search',['only' =>['processInvoiceSummaryReport']]);
        

    }
    // middleware

    public function addToCart(Request $request)
    {

        Cart::add([
            'id' => uniqid(),
            'name' => $request->cartDescription,
            'qty' =>  $request->cartQuantity,
            'price' => $request->cartRate,
            'weight' => 1,
            'options' => [
                'qty_unit' => $request->qty_unit,
                'cartVat' => 0, //  $request->cartVat,
                'cartTotal' =>  $request->cartTotal,
                'areaNo' => $request->cartAreaNo,
                'itemNo' => $request->itemNo,
            ]
        ]);
        $carts = Cart::content();

        return response()->json(['success' => 'Successfully Added Cart On Item ' . $request->cartDescription]);
    }


    public function getCartInfo()
    {
        $carts = Cart::content();
        $cartQty = Cart::count();
        $cartTotal = Cart::total();
        return response()->json(array(
            'cartContect' => $carts,
            'cartQty' => $cartQty,
            'cartTotal' => $cartTotal,
        ));
    }


    public function removeCartInfo(Request $request)
    {

        Cart::remove($request->rowId);
        return response()->json(['success' => 'Successfully Remove The Item']);
    }

      //  FORM Submit for Create Bill Invoice With QRCode
    public function eVoucherProccess(Request $request)
    {
        //dd($request->all());
        try{ 
     
                    $todayDate = $request->voucher_date;
            
                    $carts = Cart::content();
                    $cartQty = Cart::count();
                    $cartTotal = Cart::total();
                    
            
                    $mainContID = $request->main_contractor_id;
                    $subContID = $request->sub_contractor_id;
                    $bankInfoId = $request->bank_account_id;
                    $projectId = $request->project_id;
                    $submittedEmpId = $request->employee_id;
                    $item_grand_total = $request->total;
                    $invoiceNo = $request->invoice_no;
                    $contract_no = $request->contract_no;
                    $invoiceNoWithProjectCode =  $projectId."-".$request->invoice_no;
                    $invoiceStatus = $request->invoice_status;
                    $invoice_from_date = $request->invoice_from_date;          
                    $invoice_to_date = $request->invoice_to_date;
                    $working_month = (new HelperController())->getMonthFromDateValue($request->invoice_from_date); 
                    $year =(new HelperController())->getYearFromDateValue($request->invoice_from_date);  
                    $invoice_type = $request->invoice_type; 
                    
                    $startDate = $request->start_date;              // voucher date
                    $submittedDate = $request->submitted_date;
                    $remarks = $request->remarks ?? ' ';
                    $percent_of_vat = $request->vat;                // percent_of_vat
                    $percent_of_retention = $request->retention;   // percent_of_retention
                    $total_vat = $request->vat_total;              // total amount of vat
                    $total_retention = $request->retention_total;  // total amount of retention
            
                    $total_with_vat = $request->total_with_vat;    // Total Amount Included VAT
                    $total_with_vatAndExclRetention = $request->grandTotal;  // Total Amount Included VAT and Exclusive Retention
            
                    $subContractorInfo = (new CompanyDataService())->getAnSubContractorDetailsInfoByID($subContID);
                    $subContEn = $subContractorInfo->sb_comp_name;
                    $subContVatNo = intval($subContractorInfo->sb_vat_no);
                    $qrCoded = $this->createBillVoucherQRCode($subContEn, $subContVatNo, $submittedDate, $total_with_vat, $total_vat);
                     
            
                    if((new BillVoucherDataService())->checkThisInvoiceRecordAlreadyExistByInvoiceNumber($invoiceNoWithProjectCode,$projectId)){
                     
                        // $invoiceRecord = (new BillVoucherDataService())->getInvoiceRecordByInvoiceNumber($invoiceNoWithProjectCode);
                        // $invoiceRecordDetails = (new BillVoucherDataService())->getInvoiceRecordDetilaByInvoiceRecordId($invoiceRecord->invoice_record_auto_id);    
                        // $total_with_vat = round($invoiceRecord->items_grand_total_amount +  $invoiceRecord->total_vat,2) ;
                        
                        // $invoiceRecord->receiveable_amount = $invoiceRecord->total_amount - $invoiceRecord->total_retention;
                        // $invoiceRecord->receiveable_amount_inword = (new HelperController())->numberToWord($invoiceRecord->receiveable_amount);
                        
                        // $qrCoded = $this->createBillVoucherQRCode($subContEn, $subContVatNo, $submittedDate, $total_with_vat, $invoiceRecord->total_vat);
                        //  if($projectId == 29){          
                        //     // Avenue Mall Project Invoice    
                        //      return view('admin.bill_voucher.avenue_mal_invoice', compact('qrCoded', 'invoiceRecord', 'invoiceRecordDetails'));
                        // }else {
                        //     return view('admin.bill_voucher.invoice_with_qr', compact('qrCoded', 'invoiceRecord', 'invoiceRecordDetails'));
                        // }
                        
                        return "Invoice Number Already Exist. Please Change Invoice Number and Try Again ";
                        
                        
                    }else {
                        
                         $qrCoded = $this->createBillVoucherQRCode($subContEn, $subContVatNo, $submittedDate, $total_with_vat, $total_vat);
                         $invoiceRecordId = (new BillVoucherDataService())->saveBillInvoiceRecord(
                            $mainContID,$subContID,$bankInfoId,$projectId,$submittedEmpId,$item_grand_total,$invoiceNoWithProjectCode,$invoiceStatus,$submittedDate,$invoice_from_date,$invoice_to_date,
                            $working_month,$year,$invoice_type,$remarks,$percent_of_vat,$percent_of_retention,$total_vat,$total_retention,$total_with_vat,$contract_no
                        );
                        
                     
                        (new BillVoucherDataService())->saveNewInvoiceDetailsRecord($carts, $invoiceRecordId);
                        $invoiceRecord = (new BillVoucherDataService())->getInvoiceRecordWithProjectDetailsByInvoiceRecordId($invoiceRecordId);
                        $invoiceRecord->invoice_no = $invoiceNo;
                        
                        $invoiceRecord->receiveable_amount = $invoiceRecord->total_amount - $invoiceRecord->total_retention;
                        $invoiceRecord->receiveable_amount_inword = (new HelperController())->numberToWord($invoiceRecord->receiveable_amount);
                        
                        $invoiceRecordDetails = (new BillVoucherDataService())->getInvoiceRecordDetilaByInvoiceRecordId($invoiceRecordId);
                 
                        Cart::destroy(); // reset cart list
                        if($projectId == 29){          
                            // Avenue Mall Project Invoice      
                            return view('admin.bill_voucher.avenue_mal_invoice', compact('qrCoded', 'invoiceRecord', 'invoiceRecordDetails'));
                        }else {
                            return view('admin.bill_voucher.invoice_with_qr', compact('qrCoded', 'invoiceRecord', 'invoiceRecordDetails'));
                        }
                    }
                    
        }catch(Exception $ex){
                return "System Operation Failed. Please Try Again ".$ex;
        }


    }

    public function updatedEVoucherAndCreateNewQRCode(Request $request){

     

        $carts = Cart::content();
        $cartQty = Cart::count();
        $cartTotal = Cart::total();
     

        $invoiceRecordAutoID = $request->invoiceRecordAutoID;
        $mainContID = $request->main_contractor_id;
        $subContID = $request->sub_contractor_id;
        $bankInfoId = $request->bank_account_id;
        $projectId = $request->project_id;
        $submittedDate = $request->submitted_date;
        $submittedEmpId = $request->employee_id;
        $startDate = $request->start_date;              // voucher date
        $invoiceStatus = $request->invoice_status;
        $remarks = $request->remarks ?? ' ';
        $invoiceNo = $request->invoice_no;
        $item_grand_total = $request->total;
        $total_vat = $request->vat_total;              // total amount of vat
        $percent_of_vat = $request->vat;                // percent_of_vat
        $total_retention = $request->retention_total;  // total amount of retention
        $percent_of_retention = $request->retention;   // percent_of_retention
        $total_with_vat = $request->total_with_vat;    // Total Amount Included VAT
        $total_with_vatAndExclRetention = $request->grandTotal;  // Total Amount Included VAT and Exclusive Retention

        $subContractorInfo = (new CompanyDataService())->getAnSubContractorDetailsInfoByID($subContID);
        $subContEn = $subContractorInfo->sb_comp_name;
        $subContVatNo = intval($subContractorInfo->sb_vat_no);
        $qrCoded = $this->createBillVoucherQRCode($subContEn, $subContVatNo, $submittedDate, $total_with_vat, $total_vat);

        $previousCartDBRecrods = (new BillVoucherDataService())->getInvoiceRecordDetilaByInvoiceRecordId($invoiceRecordAutoID);
        $updateInvoiceRecordDetailsInfo = (new BillVoucherDataService())->deleteInvoiceDetailsRecordThoseAreNotInCart($carts, $previousCartDBRecrods);
        (new BillVoucherDataService())->updateBillVoucherInvoiceRecordInformations($carts, $invoiceRecordAutoID);
             $updateInvoiceRecords = (new BillVoucherDataService())->updateInvoiceRecordInfosByInvoiceRecordAutoID($invoiceRecordAutoID,
             $mainContID, $subContID, $bankInfoId, $projectId, $submittedDate, $submittedEmpId, $startDate, $invoiceStatus,
             $remarks, $invoiceNo, $item_grand_total, $total_vat, $percent_of_vat, $total_retention, $percent_of_retention,
             $total_with_vat, $total_with_vatAndExclRetention);
        Cart::destroy(); // reset cart list

        $invoiceRecord = (new BillVoucherDataService())->getInvoiceRecordWithProjectDetailsByInvoiceRecordId($invoiceRecordAutoID);
        $invoiceRecordDetails = (new BillVoucherDataService())->getInvoiceRecordDetilaByInvoiceRecordId($invoiceRecord->invoice_record_auto_id);
        return view('admin.bill_voucher.invoice_with_qr', compact('qrCoded', 'invoiceRecord', 'invoiceRecordDetails'));


    }

    public function deleteInvoiceRecordByInvoiceNumber(Request $request){ 

        $invoiceRecord = (new BillVoucherDataService())->getInvoiceRecordByInvoiceNumber($request->invoice_number);     
        if($invoiceRecord){
            (new BillVoucherDataService())->deleteInvoiceDetailsRecordByInvoiceRecordId($invoiceRecord->invoice_record_auto_id);
            (new BillVoucherDataService())->deleteAnInvoiceRecordByInvoiceRecordId($invoiceRecord->invoice_record_auto_id);
           
            return response()->json(['status'=>200, 'success'=>true,'message'=>"Successfully Deleted",'data'=>$invoiceRecord]);
        }else {
            return response()->json(['status'=>403, 'success'=>false,'message'=>"Record Not Found",'error'=>"error"]);
        }
        
    }

    public function createBillVoucherQRCode($subContrEn, $subVat, $date, $totalWithVat, $totalVat)
    {
        // dd($subContrEn, $subVat, $date, $totalWithVat, $totalVat);
        return  GenerateQrCode::fromArray([
            new Seller($subContrEn), // seller name
            new TaxNumber($subVat), // seller tax number
            new InvoiceDate($date), // invoice date as Zulu ISO8601 @see https://en.wikipedia.org/wiki/ISO_8601
            new InvoiceTotalAmount($totalWithVat), // invoice total amount
            new InvoiceTaxAmount($totalVat) // invoice tax amount
        ])->render();
    }
    
    // Search by invoice number and Print Again invoice
     public function eVoucherProccessForPrintPreview(Request $request){


        $invoiceNo = $request->invoice_no;
        $invoiceRecord = (new BillVoucherDataService())->getInvoiceRecordByInvoiceNumber($invoiceNo);

        if(is_null($invoiceRecord)){
           return "Invoice Record Not Found. Please Check Invoice Number"; 
        }
        
        $invoiceRecord->receiveable_amount = $invoiceRecord->total_amount - $invoiceRecord->total_retention;
        $invoiceRecord->receiveable_amount_inword = (new HelperController())->numberToWord($invoiceRecord->receiveable_amount);
            
        $subContID = $invoiceRecord->sub_contractor_id;
        $submittedDate = $invoiceRecord->submitted_date;
        $total_with_vat = $invoiceRecord->total_amount;
        $total_vat = $invoiceRecord->total_vat;

        $subContractorInfo = (new CompanyDataService())->getAnSubContractorDetailsInfoByID($subContID);
        $subContEn = $subContractorInfo->sb_comp_name;
        $subContVatNo = intval($subContractorInfo->sb_vat_no);
        $qrCoded = $this->createBillVoucherQRCode($subContEn, $subContVatNo, $submittedDate, $total_with_vat, $total_vat);
        
        $invoiceRecordDetails = (new BillVoucherDataService())->getInvoiceRecordDetilaByInvoiceRecordId($invoiceRecord->invoice_record_auto_id);
        //return view('admin.bill_voucher.invoice_with_qr_preview', compact('qrCoded', 'invoiceRecord', 'invoiceRecordDetails'));
        return view('admin.bill_voucher.invoice_with_qr', compact('qrCoded', 'invoiceRecord', 'invoiceRecordDetails'));
    }
    
    // ======================= Submitred Invoice record  Details preview View =====================
    public function showQRCodeBillVourcher($invoiceRecordId)
    {
        $company = (new CompanyDataService())->findCompanryProfile();
        $invoiceRecord = (new BillVoucherDataService())->getInvoiceRecordWithProjectDetailsByInvoiceRecordId($invoiceRecordId);   
        $invoiceRecordDetails =  (new BillVoucherDataService())->getInvoiceRecordDetilaByInvoiceRecordId($invoiceRecord->invoice_record_auto_id);         
        $qrCoded = $this->createBillVoucherQRCode($invoiceRecord->sub_contractor_en, $invoiceRecord->sub_con_vat_no, $invoiceRecord->submitted_date, $invoiceRecord->total_amount, $invoiceRecord->total_vat);

        return view('admin.bill_voucher.invoice_with_qr', compact('company', 'qrCoded', 'invoiceRecord', 'invoiceRecordDetails'));
    }

   
    // AJAX Request For Getting Invoice Details For Update 
  public function getAllEVoucherInformationForUpdateByInvoiceNoAjaxRequest(Request $request)
    { 
        Cart::destroy();
        $billVoucherInfos = (new BillVoucherDataService())->getEVoucherInformationsByInvoiceNo($request->invoiceNo);
        if ($billVoucherInfos != null) {

            $cartInfos = (new BillVoucherDataService())->getEVoucherCartInformationsByInvoiceRecordID($billVoucherInfos->invoice_record_auto_id);
            $bankInfos = (new CompanyDataService())->getBankInfoBySubCompanyId($billVoucherInfos->sub_contractor_id);
            foreach ($cartInfos as $cartItem) {
                Cart::add([
                    'id' => uniqid(),
                    'name' => $cartItem->items_details,
                    'qty' =>  $cartItem->quantity,
                    'price' => $cartItem->rate,
                    'weight' => 1,
                    'options' => [
                        'qty_unit' => $cartItem->item_unit,
                        'cartVat' => 0, //  $request->cartVat,
                        'cartTotal' => $cartItem->total,
                        'areaNo' => $cartItem->area_no,
                        'itemNo' => $cartItem->item_no,
                        'invoiceRecordAutoID' => $cartItem->inv_record_auto_id,
                    ]
                ]);
            }
            $carts = Cart::content();

            return json_encode([
                'status' => 200,
                'success' => true,
                'error' => null,
                'voucherInfo' => $billVoucherInfos,
                'cartInfo' => $carts,
                'bankInfos' => $bankInfos,
            ]);

        } else {
            return json_encode([
                'status' => 404,
                'success' => false,
                'error' => 'error',
                'message' => 'Invoice Information Not Found!',
            ]);
        }
        
    }

    // INovice Payment Status Update Request  
     public function updatedQRCodeInvoiceStatus(Request $request){
    
        $isUpdated = (new BillVoucherDataService())->updateAnInvoicePaymentStatus((int)$request->invoice_record_auto_id,$request->invoice_status);
        if($isUpdated){
            Session::flash('success', 'Successfully Updated');
            return redirect()->back();
        }else{
            Session::flash('error', 'Update Operation Failed, Please Try Again');
            return redirect()->back();
        }
    }

    // ======================= blade ============================

    //   QRCode with Item Details Information User Interface Loading
    public function BillVoucherFormLoad()
    {
        $mainContractors = (new CompanyDataService())->getAllActiveMainContractorForDropdownList();
        $subContractor = (new CompanyDataService())->getSubCompanyListForDropdown();
        $project = (new EmployeeRelatedDataService())->getAllProjectInfoForDropdown();
        $user = (new AuthenticationDataService())->findUserById(Auth::user()->id);
        $bankInfos = (new CompanyDataService())->getAllActiveBankAccountNameForDropdownlist();
        $employee = [$user];
        Cart::destroy(); // reset cart list
        return view('admin.bill_voucher.index', compact('project', 'employee', 'bankInfos', 'mainContractors', 'subContractor'));
    }
    
      //   QRCode with Item Details Information User Interface Load For Update
    public function loadBillVoucherFormLoadForInvoiceUpdate()
    {
        $invoice_records = (new BillVoucherDataService())->getLastFiftyInvoiceRecords();      
        $company_list = (new CompanyDataService())->getSubCompanyListForDropdown();
        $main_contractors = (new CompanyDataService())->getAllActiveMainContractorForDropdownList();
        $projects = (new ProjectDataService())->getAllActiveProjectListForDropdown();
        $users = [(new AuthenticationDataService())->findUserById(Auth::user()->id)];
        $banks = (new CompanyDataService())->getAllActiveBankAccountNameForDropdownlist();

        return view('admin.bill_voucher.edit', compact('invoice_records', 'company_list','main_contractors','projects','users','banks'));
    }


    // Only QRCode Creation User Interface Loading
    public function qrcodeCreateUI()
    {

        $project = (new EmployeeRelatedDataService())->findLoginUserProject();
        $user = (new AuthenticationDataService())->findUserById(Auth::user()->id);
        $employee =  (new EmployeeDataService())->getAnEmployeeInfoByEmail($user->email);
        $employee = [$employee];

        return view('admin.bill_voucher.qrcode_create', compact('project', 'employee'));
    }
    
        // =======================Already Submited Invoice List ============================
    public function submitedBillVoucherUi()
    {
      
        $invoice_records = (new BillVoucherDataService())->getLastFiftyInvoiceRecords();    
        return view('admin.bill_voucher.submitted_invoice_list', compact('invoice_records'));
    }

    // Only QRCode Creation Request with basic information
    public function generateQRCode(Request $request)
    {

        $subContEn = $request->sub_contractor_en;
        $subContArb = $request->sub_contractor_rb;
        $subContVatNo = $request->sub_con_vat_no;
        $percent_of_vat = $request->vat; // percent_of_vat
        $total_vat = $request->vat_total;
        $percent_of_retention = $request->retention; // percent_of_vat
        $total_retention = $request->retention_total;
        $total_with_vat = $request->total_with_vat;

        $qrCoded = $this->createBillVoucherQRCode($subContEn, $subContVatNo, $request->voucher_date, $total_with_vat, $total_vat);
        return view('admin.bill_voucher.qrcode_diplay', compact('qrCoded'));
    }
    
    
        public function loadInvoiceAndSalaryStatementProcessUI(){
        $projects = (new ProjectDataService())->getAllActiveProjectListForDropdown();
        return view('admin.bill_voucher.income_expense.project_cost_calculation',compact('projects'));
    }
    
    public function getAProjectSalaryInfoAndInvoiceStatementSummary(Request $request){
 
            $work_summary = (new SalaryProcessDataService())->getOnlyThisProjectTotalSalaryAmountForMultipleProjectWorkByProjectMonthAndYear($request->project_id,(int)$request->month,$request->year);
            $invoice_record = (new BillVoucherDataService())->getAProjectInvoiceSummaryAmountRecord($request->project_id,(int)$request->month,$request->year);
            return response()->json(['status'=>200,'success'=>true,'data'=>$request->all(),'work_summary'=>$work_summary[0],'invoice_summary'=>$invoice_record]);
    }

    public function processAProjectSalaryInfoAndInvoiceStatementSummary(Request $request){

        //  dd($request->all());

         $invoice_record_details = (new BillVoucherDataService())->getAnSingleInvoiceRecordAllDetailsByProjectInoviceMonthYear($request->select_project_id,(int)$request->select_month,$request->select_year);
         if( (int)$request->count_of_invoice == 1){
            $invoice_record_details = (new BillVoucherDataService())->getAnSingleInvoiceRecordAllDetailsByProjectInoviceMonthYear($request->select_project_id,(int)$request->select_month,$request->select_year);
         }
         $invoice_project_info = [
            'month' => (new HelperController())->getMonthName($request->select_month),
            'year' => $request->select_year,
            'total_employee' => $request->total_employee,
            'total_man_hours' => $request->total_man_hours,
            'total_salary' => $request->total_salary,
            'hourly_cost' => $request->hourly_rate,
            'invoice_grand_total_amount' =>$request->grand_total_amount,
            'invoice_total_vat' =>$request->total_vat,
            'invoice_vat_percent' =>$request->vat,
            'invoice_total_retention' =>$request->total_retention,
            'invoice_retention_percent' =>$request->retention,
            'contract_no' => $request->contract_no,
         ];
         
        //  dd($invoice_record_details);         
        $company = (new CompanyDataService())->findCompanryProfile();
        return view("admin.bill_voucher.income_expense.invoice_statement",compact("company",'invoice_project_info','invoice_record_details'));
    }
 


   // ======================= Invoice Related Reports ============================

     public function processQrCodeInvoiceReport(Request $request){

      $records =  (new BillVoucherDataService())->getInvoiceWithQRCodeReport($request->company_id_list,$request->project_id_list,$request->invoice_status);
      $company = (new CompanyDataService())->findCompanryProfile();
      $report_title = ["Invoice Report"];
      return view('admin.report.invoices.project_invoice_report',compact('company','records','report_title'));
    }
    
    
    
    
     
    
    
    
    
    // ====================================================================
    // ======================= INVOICE SUMMARY ============================
    // ====================================================================
    // public function loadInvoiceSummaryFormUI()
    // { 
    //     $projects = (new ProjectDataService())->getAllActiveProjectListForDropdown();      
    //     return view('admin.bill_voucher.invoice_summary.index', compact('projects'));
    // }

    // public function saveNewInvoiceSummaryInformation(Request $request)
    // { 
         
    //     try{
    //       //  $day_month_year =(new HelperController())->getDayMonthAndYearFromDateValue($request->submit_date);
    //         $file_path = null;  

    //         if ($request->hasFile('invoice_paper')) {               
    //             $file = $request->file('invoice_paper');
    //             $file_path = (new UploadDownloadController())->uploadInvoiceSummaryPaper($file,null);               
    //         }          
    //         $invoice_id = (new BillVoucherDataService())->saveNewInoviceSummaryInformation($request->project_id,$request->month,$request->year,$request->invoice_amount,$request->vat_amount,
    //         $request->retention_amount,$request->vat,$request->retention,$request->submit_date,$request->receivable_amount,$request->remarks,$file_path);
            
    //         if($invoice_id){
    //             Session::flash('success', 'Successfully Saved');
    //             return redirect()->back();
    //         }else{
    //             Session::flash('error', 'Insertion Operation Failed, Please Try Again');
    //             return redirect()->back();
    //         }

    //     }catch(Exception $ex){
    //         Session::flash('error', 'System Exception, Please Try Again');
    //         return redirect()->back();
    //     }


    // }

    // public function searchInvoiceSummary(Request $request)
    // {          
    //     try{        
             
    //         $invoice_records = (new BillVoucherDataService())->getInvoiceSummaryByProjectId($request->project_id);
    //         return response()->json(['status' => 200, 'success' => true,'data' => $invoice_records,'error' => null,'value'=>$request->project_id ]);

    //     }catch(Exception $ex){
    //         return response()->json(['success' => false,'status' => 403, 'error' => 'error','message'=>'System Operation Exception, Please Try Again','data' => null ]);
    //     }
    // }

    // public function processInvoiceSummaryReport(Request $request)
    // {          
    //     try{        
    //         $project_reports = array() ; //$request->project_id_list;
    //         $counter = 0;
    //         foreach($request->project_id_list as $pid){
    //             $project_info = (new ProjectDataService())->findAProjectInformation($pid);
    //             $project_info->invoice_records = (new BillVoucherDataService())->getInvoiceSummaryByProjectIdAndStatus($pid,$request->invoice_status);
    //             // (new BillVoucherDataService())->getInvoiceSummaryByProjectId($pid);
    //             $project_reports[$counter++] = $project_info;
    //         }
    //       $company = (new CompanyDataService())->findCompanryProfile();
    //       return view('admin.report.invoices.invoice_summary_report',compact('company','project_reports')); 
    //     }catch(Exception $ex){
    //         return view($ex);
    //      }
    // }


    public function loadInvoiceSummaryFormUI()
    { 
        $invoice_records = (new BillVoucherDataService())->getInvoiceSummaryByProjectId(0);
        $projects = (new ProjectDataService())->getAllActiveProjectListForDropdown();      
        return view('admin.bill_voucher.invoice_summary.index', compact('projects'));
    }
 
    
    public function saveNewInvoiceSummaryInformation(Request $request)
    {
       
        try{
            $invoice_id = -1;
            $message = 'Successfully Saved';
            $login_id = Auth::user()->id;
            
            if($request->inv_sum_auto_id >0 ){ // if exist mean update operation
                $invoice_record = (new BillVoucherDataService())->getInvoiceSummaryByInvoiceSummaryAutoId($request->inv_sum_auto_id);
                $file_path = null;
                if ($request->hasFile('invoice_paper')) {
                    $file = $request->file('invoice_paper');
                    $file_path = (new UploadDownloadController())->uploadInvoiceSummaryPaper($file, $invoice_record->invoice_file);
                }else {
                    $file_path = $invoice_record->invoice_file;
                }
                if($invoice_record){
                    $invoice_id = (new BillVoucherDataService())->updateInoviceSummaryInformation($request->inv_sum_auto_id,$request->project_id,$request->month,$request->year,$request->invoice_amount,$request->vat_amount,
                    $request->retention_amount,$request->vat,$request->retention,$request->submit_date,$request->receivable_amount,$request->remarks,$request->invoice_status,$file_path,$request->invoice_no,$request->contract_reference, $login_id);
                    $message = 'Successfully Updated';
                }

            }else {

                    if( (new BillVoucherDataService())->checkThisInvoiceNoIsAlreadyExist($request->invoice_no)){
                        // inovice number alrady exist
                        Session::flash('error', 'Invoice Number Alrady Exist, Please Check the Invoice Number');
                        return redirect()->back();
                    }

                    // new invoice information inert
                    $file_path = null;
                    if ($request->hasFile('invoice_paper')) {
                        $file = $request->file('invoice_paper');
                        $file_path = (new UploadDownloadController())->uploadInvoiceSummaryPaper($file,null);
                    }
                    $invoice_id = (new BillVoucherDataService())->saveNewInoviceSummaryInformation($request->project_id,$request->month,$request->year,$request->invoice_amount,$request->vat_amount,
                    $request->retention_amount,$request->vat,$request->retention,$request->submit_date,$request->receivable_amount,$request->remarks,$file_path,$request->invoice_no,$request->contract_reference, $login_id);

                }

            if($invoice_id){
                Session::flash('success',$message);
                return redirect()->back();
            }else{
                Session::flash('error', 'Operation Failed, Please Try Again');
                return redirect()->back();
            }

        }catch(Exception $ex){
            Session::flash('error', 'System Exception, Please Try Again');
            return redirect()->back();
        }
    }

    public function getInvoiceSummaryForEdit(Request $request)
    {          
        try{        
             
            $invoice_records = (new BillVoucherDataService())->getInvoiceSummaryByInvoiceSummaryAutoId($request->inv_sum_auto_id);
            return response()->json(['status' => 200, 'success' => true,'data' => $invoice_records,'error' => null  ]);

        }catch(Exception $ex){
            return response()->json(['success' => false,'status' => 403, 'error' => 'error','message'=>'System Operation Exception, Please Try Again','data' => null ]);
        }
    }


    public function deleteInvoiceSummaryRecord(Request $request)
    {          
        try{        
             
            $isDeleted = (new BillVoucherDataService())->deleteInvoiceSummaryByInvoiceSummaryAutoId($request->inv_sum_auto_id);
            if($isDeleted){
                return response()->json(['status' => 200, 'success' => true,'message' => 'Successfully Deleted','error' => null  ]);
            }else {
                return response()->json(['status' => 404, 'success' => false,'message' => 'Delete Operation Failed, Please Try Again','error' => 'error'  ]);

            }

        }catch(Exception $ex){
            return response()->json(['success' => false,'status' => 403, 'error' => 'error','message'=>'System Operation Exception, Please Try Again','data' => null ]);
        }
    }

    public function searchInvoiceSummary(Request $request)
    {          
        try{        
             
            $invoice_records = (new BillVoucherDataService())->getInvoiceSummaryByProjectId($request->project_id);
            return response()->json(['status' => 200, 'success' => true,'data' => $invoice_records,'error' => null,'value'=>$request->project_id ]);

        }catch(Exception $ex){
            return response()->json(['success' => false,'status' => 403, 'error' => 'error','message'=>'System Operation Exception, Please Try Again','data' => null ]);
        }
    }

    public function processInvoiceSummaryReport(Request $request)
    {          
        try{        
            $project_reports = array(); 
            $counter = 0;
            foreach($request->project_id_list as $pid){
                $project_info = (new ProjectDataService())->findAProjectInformation($pid);
                $project_info->invoice_records = (new BillVoucherDataService())->getInvoiceSummaryByProjectIdAndStatus($pid,$request->invoice_status);
                $project_reports[$counter++] = $project_info;
            }
           $company = (new CompanyDataService())->findCompanryProfile();
           $login_name = Auth::user()->name;
           return view('admin.report.invoices.invoice_summary_report',compact('company','project_reports','login_name')); 
        }catch(Exception $ex){
            return view($ex);
         }
    }

    



    

    
}
