<?php

namespace App\Http\Controllers\Admin\Expense;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\CostTypeController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DataServices\ExpenseDataService;
use App\Http\Controllers\DataServices\EmployeeRelatedDataService;
use App\Http\Controllers\DataServices\CompanyDataService;
use App\Http\Controllers\DataServices\EmployeeDataService;
use App\Http\Controllers\DataServices\DebitCreditDataService;
use App\Http\Controllers\Admin\Helper\HelperController;
use App\Http\Controllers\Admin\Helper\UploadDownloadController;
use App\Http\Controllers\DataServices\ProjectDataService;
use Illuminate\Http\Request;
use App\Models\DailyCost;

use Carbon\Carbon;
use Session;
use Image;
use File;

use Illuminate\Support\Facades\Process;
class DailyExpenseController extends Controller{


    /*
    =============================
    =======BLADE OPEREATION======
    =============================
    */
//     public function dailyNewExpenseCreateForm(){



//       $expenseHeads =  (new ExpenseDataService())->getCostTypeAll();
//       $projects = (new EmployeeRelatedDataService())->getAllProjectInfoForDropdown();
//       $subCompanies = (new CompanyDataService())->getSubCompanyListForDropdown();

//       $allexpenses =  (new ExpenseDataService())->getDailyExpenseDetailsList();

//       return view('admin.daily-expense.add',compact('expenseHeads','projects','allexpenses', 'subCompanies'));
//   }

//     public function dailyExpensesApprovalForm(){
//       $all =  (new ExpenseDataService())->getDailyExpenseDetailsList();
//       return view('admin.daily-expense.expenses-approval-pending-list',compact('all'));
//     }

//     public function dailyNewExpenseEditForm($id){

//       $expenseHeads =  (new ExpenseDataService())->getCostTypeAll();
//       $projects = (new EmployeeRelatedDataService())->getAllProjectInfoForDropdown();
//       $subCompanies = (new CompanyDataService())->getSubCompanyListForDropdown();

//       $expensDetails =  (new ExpenseDataService())->getDailyExpenseDetailsById($id);

//       return view('admin.daily-expense.edit',compact('expenseHeads','projects','expensDetails', 'subCompanies'));
//     }


//    public function getDailyExpenseReportForm(){
//       $subCompanies = (new CompanyDataService())->getSubCompanyList();
//       $allProjects = (new EmployeeRelatedDataService())->getAllProjectInformation();
//       $expenseHeads =  (new ExpenseDataService())->getCostTypeAll();

//       return view('admin.report.expenditure.index', compact('subCompanies', 'allProjects', 'expenseHeads'));
//   }


    /*
    ====================================================================
    ===================== BUSINESS LOGIC OPEREATION ====================
    ====================================================================
    */

//     public function dailyNewExpenseInsertRequest(Request $req){


//         $empInfo = (new EmployeeDataService())->getAnEmployeeInfoByEmpId($req->employee_id);
//         if($empInfo){
//            $req->employee_id = $empInfo->emp_auto_id;
//         }
//         else {

//           Session::flash('error','Employee ID Not Found ');
//           return Redirect()->back();
//         }

//         $month = (new HelperController() )->getMonthFromDateValue($req->voucher_date);
//         $year = (new HelperController())->getYearFromDateValue($req->voucher_date);

//         $vouchar_image_path = "";
//         if($req->file('vouchar')){

//           $vouchar_image = $req->file('vouchar');
//           $vouchar_image_path = 'vouchar-image'.'-'.time().'-'.$vouchar_image->getClientOriginalExtension();
//           Image::make($vouchar_image)->resize(300,300)->save('uploads/vouchar/'.$vouchar_image_path);

//         }

//         $entered_id = Auth::user()->id;
//         $insert = (new ExpenseDataService())->saveNewExpenseDetails(
//              $req->sub_comp_name,
//             $req->cost_type_id,
//             $req->project_id,
//             $entered_id,
//             $req->employee_id,
//             $req->voucher_date,
//               $vouchar_image_path,
//              $req->vouchar_no,
//              $req->gross_amount,
//              $req->vat,
//              $req->total_amount,
//               $month,
//               $year,
//               $req->description
//         );



//         if($insert){
//           Session::flash('success','Successfully Added New Expense');
//           return Redirect()->back();
//         }else{
//           Session::flash('error','Opeation Failed');
//           return Redirect()->back();
//         }


//     }

//     public function dailyExpenseUpdateRequest(Request $req){

//         $id = $req->id;
//         $entered_id = Auth::user()->id;
//         $old_img = $req->old_image;

//         $month = (new HelperController() )->getMonthFromDateValue($req->voucher_date);
//         $year = (new HelperController())->getYearFromDateValue($req->voucher_date);


//         $empInfo = (new EmployeeDataService())->getAnEmployeeInfoByEmpId($req->employee_id);

//         if($empInfo){
//            $req->employee_id = $empInfo->emp_auto_id;
//         }
//         else {

//           Session::flash('error','Employee ID Not Found ');
//           return Redirect()->back();
//         }


//         $vouchar_image_path = "";
//         if($req->file('vouchar')){

//           $vouchar_image = $req->file('vouchar');
//           $vouchar_image_path = 'vouchar-image'.'-'.time().'-'.$vouchar_image->getClientOriginalExtension();
//           Image::make($vouchar_image)->resize(300,300)->save('uploads/vouchar/'.$vouchar_image_path);
//          // unlink('uploads/vouchar/'.$old_img);
//         }
//           // update data
//           $update =  (new ExpenseDataService())->updateExpenseDetailsByCostAutoId(
//             $id,
//             $req->sub_comp_name,
//            $req->cost_type_id,
//            $req->project_id,
//            $entered_id,
//            $req->employee_id,
//            $req->voucher_date,
//              $vouchar_image_path,
//             $req->vouchar_no,
//             $req->gross_amount,
//             $req->vat,
//             $req->total_amount,
//              $month,
//              $year,
//              $req->description
//        );

//           if($update){
//             Session::flash('success','Successfully Updated Expense Details');
//             return Redirect()->route('company.daily.new.expesne.form');
//           }else{
//             Session::flash('error','Update Operation Failed');
//             return Redirect()->back();
//           }

//     }

//     public function dailyNewExpenseDeleteRequest($id){
//       $delete = DailyCost::where('status','pending')->where('cost_id',$id)->delete();
//       if($delete){
//         Session::flash('success','Successfully Deleted The Record');
//         return Redirect()->back();
//       }else{
//         Session::flash('error','Delete Operation Failed');
//         return Redirect()->back();
//       }
//     }

//     public function dailyExpenseApprovalRequest($id){

//       $approve =  (new ExpenseDataService())->approvalOfDailyExpenseDetails($id, Auth::user()->id);

//       if($approve){
//         Session::flash('success','The Expense is Approved');
//         return Redirect()->back();
//       }else{
//         Session::flash('error','Approval Failed');
//         return Redirect()->back();
//       }
//     }



//   public function processDaileyExpenseReport(Request $request){


//       $subCompany = $request->subcompany_id;
//       $project = $request->project_id;
//       $expenseHead = $request->expense_head;

//       $from_date = $request->from_date;
//       $to_date = $request->to_date;

//       $company = (new CompanyDataService())->findCompanryProfile();
//       $expend_report = (new ExpenseDataService())->ExpenditureReportDatabaseOperation($from_date, $to_date, $subCompany, $project, $expenseHead);

//       if ($expend_report != null) {
//           return view('admin.report.expenditure.report-sheet',compact('company', 'expend_report','from_date','to_date'));
//       } else {
//           Session::flash('error', 'Information not found');
//           return redirect()->back();
//         }
//   }



    /*
    =============================
    =======DAILY PETTY CASH ======
    =============================
        "Other-Hand Cash Account-200"
        "Rashed-Hand Cash Account-205"
        "Sales Revenue Account-210"
        "Purchase Expense Account-215"


    */

    public $Rashed_Hand_Cash_Account = 205;
    public $Other_Hand_Cash_Account = 200;
    public $Sales_Revenue_Account = 210;
    public $Purchase_Expense_Account = 215;

    public function dailyPettyCashTransactionForm(){

       $bank_list =  (new  CompanyDataService())->getAllActiveBankAccountNameForDropdownlist();
       $expense_types =  (new ExpenseDataService())->getCostTypeHeadForDropdownList();
       $projects = (new ProjectDataService())->getAllActiveProjectListForDropdown(1);
       return view('admin.accounts_module.petty_cash_inout_form',compact('expense_types','bank_list','projects'));
    }

public function fastMerge($files)
{

                // $files = $re->file('dr_invoice_path');
                // $file1 =   public_path('contents/admin/assets/documents/sample1.pdf');
                // $file2 =   public_path('contents/admin/assets/documents/sample2.pdf');
                // $outputFile = public_path('contents/admin/assets/documents/merged_output.pdf');

                $directory = public_path('contents/admin/assets/documents');

                // Automatically create folders if they don't exist
                if (!File::isDirectory($directory)) {
                    File::makeDirectory($directory, 0777, true, true);
                }

                $file1 = $files[0] ;// $directory . '/sample1.pdf';
                $file2 = $files[1] ;// $directory . '/sample2.pdf';
                $outputFile = $directory . '/merged_output.pdf';

                // Verify the input files actually exist before calling PDFtk
                if (!File::exists($file1) || !File::exists($file2)) {
                    return response()->json([
                        'error' => 'Input files missing',
                        'details' => "Check if sample1.pdf and sample2.pdf exist in: {$directory}"
                    ], 400);
                }

                // PDFTK Command structure: pdftk input1.pdf input2.pdf cat output output.pdf
                // Wrapping paths in quotes handles any spaces in folder names gracefully
                // Hardcode the absolute path to your pdftk.exe file directly
                $pdftkPath = '"C:\Program Files (x86)\PDFtk\bin\pdftk.exe"';
                // If installed in 64-bit folder, use: '"C:\Program Files\PDFtk Server\bin\pdftk.exe"'
                $command = "{$pdftkPath} \"{$file1}\" \"{$file2}\" cat output \"{$outputFile}\"";
                // Run the background system execution
                $result = Process::run($command);
                // if you have pdftk in your system PATH, you can simply call it without the full path:
                // $result = Process::run("pdftk {$file1} {$file2} cat output {$outputFile}");

                if ($result->successful()) {
                    return response()->download($outputFile);
                }

                // If it fails, output the exact error message from PDFTK for troubleshooting
                return response()->json([
                    'error' => 'PDF merging failed',
                    'details' => $result->errorOutput(), 'dd'=>$files
                ], 500);


//     $file1 = $files[0];
//     $file2 = $files[1];
//     $output = public_path('merged.pdf');

//     // Runs a native optimized terminal command background process safely
//     $result = Process::run("pdftk {$file1} {$file2} cat output {$output}");

//     if ($result->successful()) {
//         return response()->download($output);
//     }

//     return response()->json(['error' => 'Merging failed'], 500);
 }

// insert and update dr/expense invoice/voucher
  public function storeDailyTransactionNewExpense(Request $re){
        try{


            $emp = (new EmployeeDataService())->getAnEmployeeInfoByEmpId($re->employee_id);    
            $emp_auto_id = null;
            if($emp){
                $emp_auto_id =  $emp->emp_auto_id;
            }

            if($re->dr_vou_auto_id){
                // update existing record
                $record = (new DebitCreditDataService())->searchDailyExpenseRecordsByAutoId($re->dr_vou_auto_id);
                if($record){
                        // dr invoice paper upload                     
                        if ($re->hasFile('dr_invoice_path')) {
                            $file = $re->file('dr_invoice_path');
                            $old_path = $re->upload_paper_type == 1 ? $record->invoice_path : $record->invoice_path2;
                            $invoice_path = (new  UploadDownloadController())->uploadDrInvoiceFile($file,  $old_path);
                            (new DebitCreditDataService())->updateDailyExpenseInvoiceFilePath($record->dr_vou_auto_id, $invoice_path,$re->upload_paper_type, Auth::user()->id);
                        }

                        $isSuccess = (new DebitCreditDataService())->updateDailyExpenseInvoiceInformation($record->dr_vou_auto_id,$record->TransactionId,$emp_auto_id,$re->amount,$re->expense_type,$re->expense_date,
                        $re->remarks,$re->expense_method,Auth::user()->id,$re->project_id,$re->credit_account_id);
                        return response()->json(['success' => true, 'message' => 'Successfully Updated', 'status' => 200]);

                }else{
                    return json_encode(['status'=>403,'success'=>false,'message'=>'Record Not Found','error'=>'error']);
                }
            }else{
                // new expense insert
                $uplodedPath = null;
                // dr invoice paper upload
                if ($re->hasFile('dr_invoice_path')) {
                    $file = $re->file('dr_invoice_path');
                    $uplodedPath =  (new  UploadDownloadController())->uploadDrInvoiceFile($file, null);
                }

                $isSuccess = (new DebitCreditDataService())->insertDailyExpenseInvoiceInformation($emp_auto_id,$re->amount,$re->expense_type,$re->expense_date,$re->remarks,
                $re->expense_method,Auth::user()->id,  $uplodedPath,$re->project_id,$re->credit_account_id);
                return response()->json(['success' => true, 'message' => 'Successfully Completed', 'status' => 200]);
            }
       

      }catch(Exception $ex){
            return json_encode(['error' => 'error', 'message' => $ex, 'success' => false, 'status' => 404]);
      }

  }


  // Credit/Cash Receive Invoice
  public function storeDailyTransactionCashReceive(Request $re){
    try{

           $uplodedPath = null;
            // dr invoice paper upload
            if ($re->hasFile('cr_invoice_path')) {
                $file = $re->file('cr_invoice_path');
                $uplodedPath =  (new  UploadDownloadController())->uploadCrInvoiceFile($file, null);
            }


            $isSuccess =  (new DebitCreditDataService())->insertDailyTransactionCashReceive($re->receive_amount, $re->receipt_number, $re->cash_receive_method, $re->cash_receive_date, Auth::user()->id, $re->cash_remarks, $re->bank_id, $uplodedPath);
            if ($isSuccess) {
                return response()->json(['status' => 200, 'success' => true, 'message' => 'Successfully Completed', 'status' => 200]);
            } else {
                return json_encode(['status' => 403, 'success' => false, 'message' => 'Data Error, Operation Failed ', 'error' => 'error']);
            }
    }catch(Exception $ex){
       return json_encode(['error' => 'error', 'message' => $ex, 'success' => false, 'status' => 404]);
    }

}







  // searching single credit or debit invoice for editing
  public function searchDailyDebitCreditTransactionInvoiceForEditing(Request $re){

    try{

       if($re->record_type == 1){
          $record = (new DebitCreditDataService())->searchDailyExpenseRecordsByAutoId($re->record_auto_id);
       }else if($re->record_type == 2){
         $record = (new DebitCreditDataService())->searchDailyCashReceivedRecordByAutoId($re->record_auto_id);
       }
      return response()->json(['success' => true, 'data' => $record, 'status' => 200]);

    }catch(Exception $ex){
       return json_encode(['error' => 'error', 'message' => $ex, 'success' => false, 'status' => 404]);
    }

  }


  // searching Debit or Credit Invoice records for listview
  public function searchDailyTransactionRecords(Request $re){

      try {

            if ($re->search_type == 1) {
                // expense record
                 $records = (new DebitCreditDataService())->searchDailyExpenseRecordsForListViewByInsertedDate($re->from_date,$re->to_date,$re->employee_id,$re->search_by_inserted_date);

            } else if ($re->search_type == 2) {
                // cash receive records
                 $records = (new DebitCreditDataService())->searchDailyCachReceiveRecordsForListViewByInsertedDate($re->from_date,$re->to_date,$re->search_by_inserted_date);
            }
            return response()->json(['success' => true, 'data' => $records, 'status' => 200,'dd'=>$re->all()]);
        } catch (Exception $ex) {
            return json_encode(['error' => 'error', 'message' => $ex, 'success' => false, 'status' => 404]);
        }




  }

  public function deleteDailyTransactionRecord(Request $re){

    try{

        if((int) $re->operation_type == 1){
            // expense record delete
            $record = (new DebitCreditDataService())->searchDailyExpenseRecordsByAutoId($re->record_auto_id);
            if($record){
              (new DebitCreditDataService())->deleteDailyExpenseRecordsByAutoId($record->dr_vou_auto_id ,$record->TransactionId);
              return response()->json(['success' => true, 'message' => 'Successfully Completed', 'status' => 200,'data'=> $record, 're'=>$re->all() ]);
            }else {
              return json_encode(['error' => 'error', 'message' => "Delete Operation Failed", 'success' => false, 'status' => 404,'data'=> $record,'re'=>$re->all() ]);
            }

        }
        else if((int) $re->operation_type == 2)
        {
            //  cash recieved delete
            $record = (new DebitCreditDataService())->searchDailyCashReceivedRecordByAutoId($re->record_auto_id);

            if($record){
              (new DebitCreditDataService())->deleteDailyCashReceiveRecordByAutoId($record->cr_vou_auto_id ,$record->TransactionId);
              return response()->json(['success' => true, 'message' => 'Successfully Completed', 'status' => 200,'data'=> $record, 're'=>$re->all() ]);
            }else {
              return json_encode(['error' => 'error', 'message' => "Delete Operation Failed", 'success' => false, 'status' => 404,'data'=> $record,'re'=>$re->all() ]);
            }

        }
    }catch(Exception $ex){
       return json_encode(['error' => 'error', 'message' => $ex, 'success' => false, 'status' => 404]);
    }

  }


  /*
  ==========================================================
  ========== DAILY TRANSACTION REPORTS SECTION  ============
  ==========================================================

  */
  public function bankTransactionCashReceive(Request $request)
    {
         try {
                $validated = $request->validate([
                    'date_from' => 'required|date',
                    'date_to'   => 'required|date|after_or_equal:date_from',
                ]);

                if(is_null($request->bank_id[0])){
                    // bank and  cash recive
                // dd($request->all(),1222);
                    $report_result = (new DebitCreditDataService())->crVoucherCashAndBankReceiveReport($request->date_from,$request->date_to);
                }elseif($request->bank_id[0] == -1){
                    // only cash recive
                // dd($request->all(),1222);
                    $report_result = (new DebitCreditDataService())->crVoucherCashReceiveByHandCashReport($request->date_from,$request->date_to);
                }
                else{
                    // Bank Received
                   // dd($request->bank_id,000);
                    $report_result = (new DebitCreditDataService())->crVoucherCashReceiveFromBankReport($request->bank_id, $request->date_from,$request->date_to);

                }
                $company = (new CompanyDataService())->findCompanryProfile();
                return view('admin.report.voucher.bank_report',
                    [
                        'items'   => $report_result,
                        'company' => $company,
                    ]
                );
        } catch (Exception $ex) {
            return 'System Operation Failed with Exception, Please reload page and try again';
        }

    }


  //  Balance report of a date
  public function processAndShowDailyTransactionReport(Request $re){

    try{

        if($re->has('only_selected_date')){

                $expense_records = (new DebitCreditDataService())->searchDailyExpenseRecordsByDate($re->report_date);
                $cash_received_records = (new DebitCreditDataService())->searchDailyCachReceiveRecordsByDate($re->report_date);

                $company = (new CompanyDataService())->findCompanryProfile();
                $report_date = $re->report_date;
                return view('admin.report.accounts_module.only_daily_transaction_report', compact('cash_received_records', 'expense_records', 'report_date', 'company'));


            }else{


                $previous_date  = date('Y-m-d', strtotime('-1 day', strtotime($re->report_date)));
                $previous_balance = (new DebitCreditDataService())->calculateDailyTransactionCurrentBalanceOfDate($previous_date);
                $expense_records = (new DebitCreditDataService())->searchDailyExpenseRecordsByDate($re->report_date);
                $cash_received_records = (new DebitCreditDataService())->searchDailyCachReceiveRecordsByDate($re->report_date);
                 $company = (new CompanyDataService())->findCompanryProfile();
                 $report_date = $re->report_date;
                 return view('admin.report.accounts_module.daily_transaction_report', compact('cash_received_records', 'expense_records', 'previous_balance', 'report_date', 'previous_date', 'company'));

            }




    }catch(Exception $ex){
       return view('System Operation Failed with Exception '.$ex);
    }

  }


    // date to date debit invoice report
  public function processAndShowDrInvoiceDateToDateTransSummaryReport(Request $re){

    try{

           // dd($re->all());

            $from_date = $re->from_date;
            $to_date = $re->to_date;
            $company = (new CompanyDataService())->findCompanryProfile();

            if(is_null($re->report_type)){

              $previous_date  = date('Y-m-d', strtotime('-1 day', strtotime($re->from_date)));
              $previous_balance = (new DebitCreditDataService())->calculateDailyTransactionCurrentBalanceOfDate($previous_date);
              $expense_records = (new DebitCreditDataService())->getDrInvoiceDateByDateTransactionSummaryReport($from_date,$to_date);
              $cash_received_records = (new DebitCreditDataService())->getCrInvoiceDateByDateTransactionReport($from_date,$to_date);
              // dd($cash_received_records);
              return view('admin.report.accounts_module.dr_cr_date_by_date_summary_report',compact('expense_records',"cash_received_records","previous_balance",'previous_date','from_date','to_date','company'));
            }
            else if($re->report_type == 1){
              // Debit/Expense Report

                $records = (new DebitCreditDataService())->getDrInvoiceDateByDateTransactionSummaryReport($from_date,$to_date);
                return view('admin.report.accounts_module.dr_date_by_date_summary_report',compact('records','from_date','to_date','company'));
              }else if($re->report_type == 2){

                // Credit/Invoice Report
                $expense_records = (new DebitCreditDataService())->getCrInvoiceDateByDateTransactionSummaryReport($from_date,$to_date);
                return view('admin.report.accounts_module.cr_date_by_date_summary_report',compact('expense_records','from_date','to_date','company'));
              }
              else if($re->report_type == 5){
              // cash receive and expense details report
               $cash_received_records = (new DebitCreditDataService())->getCrInvoiceDateByDateTransactionReport($from_date,$to_date);
               $expense_records = (new DebitCreditDataService())->getDrInvoiceDateByDateTransactionReportByExpenseType(null,$from_date,$to_date);

              return view('admin.report.accounts_module.receive_expense_date_to_date_details_only',compact('expense_records',"cash_received_records",'from_date','to_date','company'));
             }
            else if($re->report_type == 3){
                  // debit/expense invoice report by emp id
                  if($from_date == $to_date){
                    $from_date = "2024-03-01";
                  }
                  $records = (new DebitCreditDataService())->getDrInvoiceDateByDateTransactionReportByExpenseBy($re->employee_id,$from_date,$to_date);
                  return view('admin.report.accounts_module.dr_transaction_by_emp_report',compact('records','from_date','to_date','company'));
            }else if($re->report_type == 4){

              // debit/expense type base report
              $records = (new DebitCreditDataService())->getDrInvoiceDateByDateTransactionReportByExpenseType($re->expense_type,$from_date,$to_date);
              return view('admin.report.accounts_module.dr_transaction_by_exp_type_report',compact('records','from_date','to_date','company'));
            }
             else if ($re->report_type == 6) {
                  // cash received and expense month by month summary report
                $previous_date  = date('Y-m-d', strtotime('-1 day', strtotime($re->from_date)));
                $previous_balance = (new DebitCreditDataService())->calculateDailyTransactionCurrentBalanceOfDate($previous_date);
                $month_year = (new HelperController())->getMonthsInRangeOfDate($from_date, $to_date);
                $records = array();
                $counter=0;

                foreach($month_year as $my){
                    $ar = (new DebitCreditDataService())->calculateTotalCashReceivedAndExpenseForAMonth((int)$my['month'],(int)$my['year']);
                    $info = [
                        'cash_received' => $ar[0],
                        'expensed' =>  $ar[1],
                        'month' => (new HelperController())->getMonthName((int)$my['month']) ,
                        'year' => $my['year']
                    ];
                    $records[$counter++] =(object) $info;
                }
                return view('admin.report.accounts_module.dr_cr_month_by_month_summary_report', compact('records', 'from_date', 'to_date', 'company','previous_balance','previous_date'));

            }else if($re->report_type ==7){
               // return "Development Under Processing";
                // cash received and expense summary with out previous balance report
                $previous_date  = date('Y-m-d', strtotime('-1 day', strtotime($re->from_date)));
                $previous_balance =0;//  (new DebitCreditDataService())->calculateDailyTransactionCurrentBalanceOfDate($previous_date);
                $expense_records = (new DebitCreditDataService())->getDrInvoiceDateByDateTransactionSummaryReport($from_date, $to_date);
                $cash_received_records = (new DebitCreditDataService())->getCrInvoiceDateByDateTransactionReport($from_date, $to_date);
                // dd($cash_received_records);
                return view('admin.report.accounts_module.receive_expense_date_by_date_summary', compact('expense_records', "cash_received_records", "previous_balance", 'previous_date', 'from_date', 'to_date', 'company'));

            }
    }catch(Exception $ex){
       return view('System Operation Failed with Exception'.$ex);
    }

  }

     // debit invoice expense head date by date summary report
    public function processAndShowDateToDateDrInvoiceHeadbaseSummaryReport(Request $re){

      try{

              $from_date = $re->from_date;
              $to_date = $re->to_date;
              $company = (new CompanyDataService())->findCompanryProfile();
              $login_name = Auth::user()->name;

              if($re->report_type == 1){
                // debit/expense type  date by date details report
                $records = (new DebitCreditDataService())->getDrInvoiceDateByDateTransactionReportByExpenseType($re->expense_type,$from_date,$to_date);
                 return view('admin.report.accounts_module.dr_transaction_by_exp_type_report',compact('records','from_date','to_date','company','login_name'));
              }
              else if($re->report_type == 2){
                // Expense Head All Summary report
                $records = (new DebitCreditDataService())->getDrInvoiceDateToDateAllHeadBaseSummaryReport($from_date,$to_date);
                return view('admin.report.accounts_module.drinvoice_head_base_datetodate_summary_report',compact('records','from_date','to_date','company','login_name'));
              }
               else if($re->report_type == 3){
                // Expense Head base month by month Summary report
                $records = (new DebitCreditDataService())->getDrInvoiceDateToDateExpenseHeadBaseMonthByMonthSummaryReport($re->expense_type,$from_date,$to_date);
                return view('admin.report.accounts_module.drinv_headbase_month_bymonth_summary',compact('records','from_date','to_date','company','login_name'));
              }

      }catch(Exception $ex){
         return view('System Operation Failed with Exception'.$ex);
      }

    }



















}
