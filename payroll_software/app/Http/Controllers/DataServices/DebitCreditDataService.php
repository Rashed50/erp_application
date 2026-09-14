<?php

namespace App\Http\Controllers\DataServices;

use App\Models\AccountsModule\Transactions;
use App\Models\AccountsModule\DebitCredit;
use App\Models\AccountsModule\CrVoucher;
use App\Models\AccountsModule\DrVoucher;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


 class DebitCreditDataService{

 private function createNewTransaction($amount,$date,$transaction_type){

        return Transactions::insertGetId([
            'TranAmount'=>$amount,
            'TranDate'=> $date,
            'TranTypeId'=>$transaction_type,

        ]);

    }

    public function insertNewDebitCreditTransaction($debit_account,$credit_account, $amount,$transaction_id){


       $dr_cr_id[0] =  DebitCredit::insertGetId([
            'Amount'=> $amount,
            'TranId'=> $transaction_id,
            'ChartOfAcctId'=>$debit_account,
            'DrCrTypeId'=>1,  // debit
        ]);

        $dr_cr_id[1]  = DebitCredit::insertGetId([
          'Amount'=> $amount,
          'TranId'=> $transaction_id,
          'ChartOfAcctId'=>$credit_account,
          'DrCrTypeId'=>2,  // credit
      ]);
      return true;

    }

     // do not open this method, account setting change for expense and income transaction
    // public function settingChange(){
    //      $transactions = Transactions::where('TranTypeId', 1)->get(); // expense transaction
    //      foreach($transactions as $transaction){
    //         $debit_credit_records = DebitCredit::where('TranId',$transaction->TranId)->get();
    //         foreach($debit_credit_records as $record){
    //              if($record->DrCrTypeId == 1){ // debit
    //               DebitCredit::where('DebiCredId',$record->DebiCredId )->update([
    //                     'ChartOfAcctId'=>215
    //               ]);
    //              }else{ // credit
    //               DebitCredit::where('DebiCredId',$record->DebiCredId )->update([
    //                   'ChartOfAcctId'=>205
    //              ]);
    //              }
    //         }
    //      }

    //      $transactions = Transactions::where('TranTypeId', 2)->get(); // income
    //      foreach($transactions as $transaction){
    //         $debit_credit_records = DebitCredit::where('TranId',$transaction->TranId)->get();
    //         foreach($debit_credit_records as $record){
    //              if($record->DrCrTypeId == 1){ // debit
    //               DebitCredit::where('DebiCredId',$record->DebiCredId )->update([
    //                     'ChartOfAcctId'=>205
    //               ]);
    //              }else{ // credit
    //               DebitCredit::where('DebiCredId',$record->DebiCredId )->update([
    //                   'ChartOfAcctId'=>210
    //              ]);
    //              }
    //         }
    //      }
    // }

    public function updateDebitCreditDrVourcherTransaction( $amount,$transaction_id,$date){

        Transactions::where('TranId',$transaction_id)->update([
          'TranAmount'=>$amount,
          'TranDate'=> $date,
        ]);
         DebitCredit::where('TranId',$transaction_id)->update([
           'Amount'=> $amount,
         ]);
        return true;
    }

    public function deleteTransactionRecord($transaction_id){

      Transactions::where('TranId',$transaction_id)->delete();
      DebitCredit::where('TranId',$transaction_id)->delete();
      return true;

   }

   // all expense invoice
    public function insertDailyExpenseInvoiceInformation($debit_by,$amount,$expense_type_id,$date,$remarks,$pay_type,$inserted_by,$invoice_path,$project_id,$credit_account_id = 205){
        // To increase an Expense on "Purchase Expense Account-215" , you DEBIT it.
        // To Decrease Asset Account "Hand Cash Account-205", you CREDIT it.

        // To increase an Expense or Asset account, you Debit it. To decrease an Asset account, you Credit it.
        $transaction_id = $this->createNewTransaction($amount,$date,1);  // Expense
        if($transaction_id>0){
            $isSuccess = $this->insertNewDebitCreditTransaction(215, $credit_account_id, $amount, $transaction_id);
            if($isSuccess){
              return  DrVoucher::insertGetId([
                  'TransactionId'=>$transaction_id,
                  'DrTypeId'=> $expense_type_id ,
                  'ExpenseDate'=>$date,
                  'Amount'=>$amount,
                  'DebitedTold'=>215,
                  'CreditedFromId'=>$credit_account_id,
                  'debit_by' =>$debit_by,
                  'Remarks'=>$remarks,
                  'CreateById'=>$inserted_by,
                  'PaymentType' => $pay_type,
                  'created_at'=>Carbon::now(),
                  "invoice_path"=>$invoice_path,
                  "project_id" => $project_id
              ]);
            }
        }
        return false;
    }

    public function updateDailyExpenseInvoiceInformation($dr_vou_auto_id,$transaction_id,$debit_by,$amount,$expense_type_id,$date,$remarks,$pay_type,$updated_by,$project_id,$credit_account_id){

        $this->updateDebitCreditDrVourcherTransaction($amount,$transaction_id,$date);   
        return  DrVoucher::where('dr_vou_auto_id',$dr_vou_auto_id)->update([
                'DrTypeId'=> $expense_type_id ,
                'ExpenseDate'=>$date,
                'Amount'=>$amount,
                'debit_by' =>$debit_by,
                'Remarks'=>$remarks,                   
                'PaymentType' => $pay_type,
                'updated_at'=>Carbon::now(),
                "project_id" => $project_id,
                "CreditedFromId" => $credit_account_id
            ]);
                
    }

     public function updateDailyExpenseInvoiceFilePath($dr_vou_auto_id,$invoice_path,$invoice_paper_type = 1,$updated_by){
        
            
        if((int)$invoice_paper_type == 1){
            return  DrVoucher::where('dr_vou_auto_id',$dr_vou_auto_id)->update([                     
                    'invoice_path'=> $invoice_path,                   
                ]);

        }else {
             // second file assume payment slip
            return  DrVoucher::where('dr_vou_auto_id',$dr_vou_auto_id)->update([                     
                    'invoice_path2'=> $invoice_path,                   
                ]);
        }        
    }

  public function searchDailyExpenseRecordsByDate($date,$employee_id = null,$ledger_accounts_ids=[205] ){

             $query = DB::table('dr_vouchers')
                ->select("employee_infos.employee_id","employee_infos.employee_name","employee_infos.emp_auto_id","users.name","dr_vouchers.*","cost_types.*")
                ->leftjoin('users', 'users.id', '=', 'dr_vouchers.CreateById')
                ->leftjoin('employee_infos', 'employee_infos.emp_auto_id', '=', 'dr_vouchers.debit_by')
                ->leftjoin('cost_types', 'dr_vouchers.DrTypeId', '=', 'cost_types.cost_type_id');
              //  ->whereIn('dr_vouchers.CreditedFromId', $ledger_accounts_ids);

            if ($employee_id) {
                // If employee_id provided, filter by employee
                $query->where('employee_infos.employee_id', $employee_id);
            } else {
                // If no employee_id, filter by date
                  $query->whereDate("ExpenseDate", $date);
            }

            return $query->orderBy('dr_vouchers.created_at', 'DESC')->get();


  }

    public function searchDailyExpenseRecordsForListViewByInsertedDate($from_date,$to_date,$employee_id = null,$search_by_inserted_date = false,$ledger_accounts_ids=[205] ){

         $query = DB::table('dr_vouchers')
                ->select("employee_infos.employee_id","employee_infos.employee_name","employee_infos.emp_auto_id","users.name","dr_vouchers.*","cost_types.*")
                ->leftjoin('users', 'users.id', '=', 'dr_vouchers.CreateById')
                ->leftjoin('employee_infos', 'employee_infos.emp_auto_id', '=', 'dr_vouchers.debit_by')
                ->leftjoin('cost_types', 'dr_vouchers.DrTypeId', '=', 'cost_types.cost_type_id');
               // ->whereIn('dr_vouchers.CreditedFromId', $ledger_accounts_ids);

            if ($employee_id) {
                // If employee_id provided, filter by employee
                $query->where('employee_infos.employee_id', $employee_id);
            } else {
                // If no employee_id, filter by date
                  if($search_by_inserted_date){
                    $query->whereDate('dr_vouchers.created_at', '>=', $from_date)
                            ->whereDate('dr_vouchers.created_at', '<=', $to_date);
                  }else{
                    $query->whereBetween("ExpenseDate", [$from_date, $to_date]);
                  }
                 // $query->whereDate("dr_vouchers.created_at", $date);
            }

            return $query->orderBy('dr_vouchers.created_at', 'DESC')->get();
  }



  public function searchDailyExpenseRecordsByAutoId($DrVoucId){
      return  DrVoucher::select('dr_vou_auto_id','ExpenseDate','Amount','Remarks','PaymentType','TransactionId','DrTypeId','DebitedTold','CreditedFromId','invoice_path','dr_vouchers.project_id','employee_infos.employee_id','DrTypeId')
              ->where('dr_vou_auto_id',$DrVoucId)
              ->leftjoin('employee_infos', 'employee_infos.emp_auto_id', '=', 'dr_vouchers.debit_by')
              ->first();
  }
  public function deleteDailyExpenseRecordsByAutoId($CrVoucId,$transactionId ){
       // $this->searchDailyExpenseRecordsByAutoId($CrVoucId );
       $this->deleteTransactionRecord($transactionId);
       return DrVoucher::where('dr_vou_auto_id',$CrVoucId)->delete();
  }

/*
============================================================================================================
    The Golden Rules (Quick Refresher)
    Debit (Dr): Increases Assets or Expenses. Decreases Liability, Equity, or Income.
    Credit (Cr): Decreases Assets or Expenses. Increases Liability, Equity, or Income.
================================================================================================================
*/


    // all revenue invoice
    public function insertDailyTransactionCashReceive($amount,$receipt_number,$pay_method,$date,$inserted_by,$remarks,$bank_id,$cr_invoice_path){

        // To increase an Asset on "Hand Cash Account-205" , you DEBIT it.
        // To increase an Income "Sales Revenue Account-210", you CREDIT it.
      $transaction_id = $this->createNewTransaction($amount,$date,2); // 2 = Income
      if($transaction_id>0){

          $isSuccess = $this->insertNewDebitCreditTransaction(205,210,$amount,$transaction_id);

          if($isSuccess){
            return  CrVoucher::insertGetId([
                'TransactionId'=>$transaction_id,
                'DrTypeId'=> 1 ,
                'receipt_number'=>$receipt_number,
                'ReceivedDate'=>$date,
                'Amount'=>$amount,
                'DebitedTold'=>205,
                'CreditedFromId'=>210,
                'Remarks'=>$remarks,
                'CreateById'=>$inserted_by,
                'ReceiveMethod' => $pay_method,
                 'BankId' => $bank_id,
                'created_at'=>Carbon::now(),
                'cr_invoice_path'=>$cr_invoice_path
            ]);
          }
      } // dr_vouchers
      return false;
  }


    public function searchDailyCachReceiveRecordsByDate($date,$ledger_accounts_ids=[205] ){

         return  CrVoucher::select("users.name as created_by_name","bank_details.bank_name","cr_vouchers.*")
               ->where('ReceivedDate',$date)
              ->leftjoin('users', 'users.id', '=', 'cr_vouchers.CreateById')
              ->leftjoin('bank_details', 'cr_vouchers.BankId' , '=',  'bank_details.id')
              ->whereIn('cr_vouchers.DebitedTold', $ledger_accounts_ids)
              ->get();

    }

    public function searchDailyCachReceiveRecordsForListViewByInsertedDate($from_date,$to_date,$search_by_inserted_date = false,$ledger_accounts_ids=[205]){

           $query = DB::table('cr_vouchers')->select("users.name as created_by_name","bank_details.bank_name","cr_vouchers.*")
                ->whereIn('cr_vouchers.DebitedTold', $ledger_accounts_ids)
                ->leftjoin('users', 'users.id', '=', 'cr_vouchers.CreateById')
                ->leftjoin('bank_details', 'cr_vouchers.BankId' , '=',  'bank_details.id');
                //  filter by inserted or received date
                if($search_by_inserted_date){
                $query->whereDate('cr_vouchers.created_at', '>=', $from_date)
                        ->whereDate('cr_vouchers.created_at', '<=', $to_date);
                }else{
                $query->whereBetween("ReceivedDate", [$from_date, $to_date]);
                }
            return $query->orderBy('cr_vouchers.created_at', 'DESC')->get();
    }

    


    public function searchDailyCashReceivedRecordByAutoId($DrVoucId ){
      return  CrVoucher::where('cr_vou_auto_id',$DrVoucId )->first();
    }


    public function calculateDailyTransactionCurrentBalanceOfDate($date,$ledger_accounts_ids=[205] ){
          $total_cash_in = CrVoucher::whereBetween('ReceivedDate',["2024-01-01",$date])->whereIn('DebitedTold', $ledger_accounts_ids)->get()->sum('Amount');
          $total_cash_out = DrVoucher::whereBetween('ExpenseDate',["2024-01-01",$date])->whereIn('CreditedFromId', $ledger_accounts_ids)->get()->sum('Amount');
          return $total_cash_in - $total_cash_out;
    }

    public function deleteDailyCashReceiveRecordByAutoId($DrVoucId,$transactionId ){
      // $this->searchDailyExpenseRecordsByAutoId($CrVoucId );
       $this->deleteTransactionRecord($transactionId);
      return CrVoucher::where('cr_vou_auto_id',$DrVoucId)->delete();

    }

    public function getDrInvoiceDateByDateTransactionSummaryReport($from_date,$to_date,$ledger_accounts_ids=[205] ){

        return DB::table('dr_vouchers as dr')
            ->select('dr.ExpenseDate', DB::raw('SUM(dr.Amount) as Total_Amount'))
            /* Use whereIn for the array of IDs */
            ->whereIn('dr.CreditedFromId', $ledger_accounts_ids)
            /* Filter by date range */
            ->whereBetween('dr.ExpenseDate', [$from_date, $to_date])
            /* Grouping and Ordering */
            ->groupBy('dr.ExpenseDate')
            ->orderBy('dr.ExpenseDate', 'asc')
            ->get();


    }

    // public function getDrInvoiceDateByDateTransactionSummaryReport1($from_date,$to_date,$ledger_accounts_ids=[205] ){
    //    // $credit_acc_ids = json_encode($ledger_accounts_ids);
    //    return DB::select("CALL getDrInvoiceDateByDateTransactionSummary1(?,?,?)",array($from_date,$to_date,json_encode($ledger_accounts_ids)));
    // }


    public function getCrInvoiceDateByDateTransactionReport($from_date,$to_date,$ledger_accounts_ids=[205] ){

            return DB::table('cr_vouchers as cr')
                /* Joining with Bank Details */
                ->leftJoin('bank_details as bd', 'cr.BankId', '=', 'bd.id')
                /* Joining with Users to get the creator's name */
                ->leftJoin('users as us', 'cr.CreateById', '=', 'us.id')
                /* Selecting specific columns */
                ->select(
                    'cr.cr_vou_auto_id',
                    'cr.TransactionId',
                    'cr.receipt_number',
                    'cr.ReceivedDate',
                    'cr.Amount',
                    'cr.Remarks',
                    'cr.ReceiveMethod',
                    'cr.BankId',
                    'us.name',
                    'bd.bank_name',
                    'bd.account_no'
                )
                ->whereIn('cr.DebitedTold', $ledger_accounts_ids)
                /* Date filtering */
                ->whereBetween('cr.ReceivedDate', [$from_date, $to_date])
                /* Ordering */
                ->orderBy('cr.ReceivedDate', 'asc')
                ->get();
       //  return DB::select("CALL getCrInvoiceDateByDateTransactionReport1(?,?)",array($from_date,$to_date));
    }

    public function getCrInvoiceDateByDateTransactionSummaryReport($from_date,$to_date,$ledger_accounts_ids=[205] ){
        return DB::table('cr_vouchers as cr')
        ->select(
            'cr.ReceivedDate',
            DB::raw('SUM(cr.Amount) as Total_Amount')
        )
        ->whereIn('cr.DebitedTold', $ledger_accounts_ids)
        ->whereBetween('cr.ReceivedDate', [$from_date, $to_date])
        ->groupBy('cr.ReceivedDate')
        ->orderBy('cr.ReceivedDate', 'asc')
        ->get();

     // return DB::select("CALL getCrInvoiceDateByDateTransactionSummary1(?,?)",array($from_date,$to_date));
    }

    public function getDrInvoiceDateByDateTransactionReportByExpenseBy($expense_by,$from_date,$to_date,$ledger_accounts_ids=[205] ){

            return DB::table('dr_vouchers as dr')
            /* Joining with Employee Info */
            ->leftJoin('employee_infos as ei', 'dr.debit_by', '=', 'ei.emp_auto_id')
            /* Joining with Cost Types */
            ->leftJoin('cost_types as ct', 'dr.DrTypeId', '=', 'ct.cost_type_id')
            /* Joining with Users */
            ->leftJoin('users as us', 'dr.CreateById', '=', 'us.id')
            /* Joining with Project Infos */
            ->leftJoin('project_infos as ps', 'dr.project_id', '=', 'ps.proj_id')
            /* Selecting specific columns */
            ->select(
                'dr.dr_vou_auto_id',
                'dr.TransactionId',
                'dr.ExpenseDate',
                'dr.Amount',
                'dr.Remarks',
                'dr.PaymentType',
                'ei.employee_id',
                'ei.employee_name',
                'ct.cost_type_name',
                'us.name',
                'ps.proj_name'
            )
             ->whereIn('dr.CreditedFromId', $ledger_accounts_ids)
            /* Filtering by specific employee and date range */
            ->where('ei.employee_id', '=', $expense_by)
            ->whereBetween('dr.ExpenseDate', [$from_date, $to_date])
            /* Sorting */
            ->orderBy('dr.ExpenseDate', 'asc')
            ->get();

      //return DB::select("CALL getDrInvoiceDateByDateTransactionReportByEmployeeId1(?,?,?)",array($from_date,$to_date,$expense_by));
    }

    public function getDrInvoiceDateByDateTransactionReportByExpenseType($expense_type_id,$from_date,$to_date,$ledger_accounts_ids=[205] ){
        if(is_null($expense_type_id)){
            $expense_type_id = 0;
        }

        return DB::table('dr_vouchers as dr')
            ->leftJoin('employee_infos as ei', 'dr.debit_by', '=', 'ei.emp_auto_id')
            ->leftJoin('cost_types as ct', 'dr.DrTypeId', '=', 'ct.cost_type_id')
            ->leftJoin('users as us', 'dr.CreateById', '=', 'us.id')
            ->select(
                'dr.dr_vou_auto_id',
                'dr.TransactionId',
                'dr.ExpenseDate',
                'dr.Amount',
                'dr.Remarks',
                'dr.PaymentType',
                'ei.employee_id',
                'ei.employee_name',
                'ct.cost_type_name',
                'us.name'
            )
            ->whereIn('dr.CreditedFromId', $ledger_accounts_ids)
            /* The 'when' method handles your IF/ELSE logic:
            If $expense_type_id > 0, it adds the extra 'where' clause.
            If not, it ignores it.
            */
            ->when($expense_type_id > 0, function ($query) use ($expense_type_id) {
                return $query->where('dr.DrTypeId', $expense_type_id);
            })
            ->whereBetween('dr.ExpenseDate', [$from_date, $to_date])
            ->orderBy('dr.ExpenseDate', 'asc')
            ->get();
       // return DB::select("CALL getDrInvoiceDateByDateTransactionReportByExpenseTypeId(?,?,?)",array($expense_type_id,$from_date,$to_date));
    }

    public function getDrInvoiceDateToDateAllHeadBaseSummaryReport($from_date,$to_date,$ledger_accounts_ids=[205] ){
        return DB::table('dr_vouchers as dr')
            ->leftJoin('cost_types as ct', 'dr.DrTypeId', '=', 'ct.cost_type_id')
            ->select(
                'ct.cost_type_name',
                DB::raw('SUM(dr.Amount) as total_amount')
            )
            ->whereIn('dr.CreditedFromId', $ledger_accounts_ids)
            ->whereBetween('dr.ExpenseDate', [$from_date, $to_date])
            ->groupBy('ct.cost_type_name')
            /* Sorting by the sum (highest spending first) */
            ->orderBy('total_amount', 'desc')
            ->get();

        //return DB::select("CALL getDrInvoiceExpenseHeadBaseDateToDateSummaryReport(?,?)",array($from_date,$to_date));
    }
    public function getDrInvoiceDateToDateExpenseHeadBaseMonthByMonthSummaryReport($expense_type_id, $from_date,$to_date,$ledger_accounts_ids=[205] ){
        return DB::table('dr_vouchers as dr')
            ->leftJoin('cost_types as ct', 'dr.DrTypeId', '=', 'ct.cost_type_id')
            ->selectRaw("
                MONTH(dr.ExpenseDate) as month_id,
                YEAR(dr.ExpenseDate) as year,
                ct.cost_type_name,
                SUM(dr.Amount) as total_amount
            ")
            ->whereIn('dr.CreditedFromId', $ledger_accounts_ids)
            ->whereBetween('dr.ExpenseDate', [$from_date, $to_date])
            ->where('ct.cost_type_id', $expense_type_id)
            /* Grouping by month and year to get the trend */
            ->groupBy(DB::raw('MONTH(dr.ExpenseDate)'), DB::raw('YEAR(dr.ExpenseDate)'), 'ct.cost_type_name')
            ->orderBy('year', 'asc')
            ->orderBy('month_id', 'asc')
            ->get();

      //  return DB::select("CALL getDrInvoiceExpenseHeadBaseMonthByMonthSummaryReport(?,?,?)",array($expense_type_id, $from_date,$to_date));
    }


    // Only Bank Received CR Vourcher
    public function crVoucherCashReceiveFromBankReport($bank_id_list, $from_date, $to_date,$ledger_accounts_ids=[205] ){

                return  CrVoucher::select("users.name","bank_details.bank_name","cr_vouchers.*")
                ->whereBetween('ReceivedDate',[$from_date,$to_date])
               ->whereIn("cr_vouchers.BankId",$bank_id_list)
               ->whereIn('cr_vouchers.DebitedTold', $ledger_accounts_ids)
                ->whereNotNull("cr_vouchers.BankId")
               ->leftjoin('users', 'users.id', '=', 'cr_vouchers.CreateById')
               ->leftjoin('bank_details', 'cr_vouchers.BankId' , '=',  'bank_details.id')
               ->orderBy('ReceivedDate','ASC')
               ->get();
    }
    // Only Handcash CR Vourcher
    public function crVoucherCashReceiveByHandCashReport($from_date, $to_date,$ledger_accounts_ids=[205] ){
           return  CrVoucher::select("users.name","bank_details.bank_name","cr_vouchers.*")
           ->whereBetween('ReceivedDate',[$from_date,$to_date])
          ->where("cr_vouchers.BankId",null)
          ->whereIn('cr_vouchers.DebitedTold', $ledger_accounts_ids)
          ->leftjoin('users', 'users.id', '=', 'cr_vouchers.CreateById')
          ->leftjoin('bank_details', 'cr_vouchers.BankId' , '=',  'bank_details.id')
          ->orderBy('ReceivedDate','ASC')
          ->get();

    }

       // bank and cash CR Vourcher
    public function crVoucherCashAndBankReceiveReport($from_date, $to_date,$ledger_accounts_ids=[205] ){
        return  CrVoucher::select("users.name","bank_details.bank_name","cr_vouchers.*")
        ->whereBetween('ReceivedDate',[$from_date,$to_date])
       ->leftjoin('users', 'users.id', '=', 'cr_vouchers.CreateById')
       ->leftjoin('bank_details', 'cr_vouchers.BankId' , '=',  'bank_details.id')
        ->orderBy('ReceivedDate','ASC')
       ->get();

    }

     public function calculateTotalCashReceivedAndExpenseForAMonth($month, $year,$ledger_accounts_ids=[205] ){

        $total_cash_in = CrVoucher::whereMonth('ReceivedDate',$month)->whereYear('ReceivedDate',$year)->whereIn('DebitedTold', $ledger_accounts_ids)->get()->sum('Amount');
        $total_cash_out = DrVoucher::whereMonth('ExpenseDate',$month)->whereYear('ExpenseDate',$year)->whereIn('CreditedFromId', $ledger_accounts_ids)->get()->sum('Amount');

         return [$total_cash_in, $total_cash_out];

    }



 }
