<?php

namespace Modules\Account\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\{Validator, Log, DB};

//? Modals Import
use App\Models\AccountsModule\ChartOfAccounts;
use App\Models\InventorySupplier;
use Modules\Account\Models\{AccountTransaction, AccountTransactionDetails, SupplierTransactions};
use Modules\Account\Services\{SuppliersService,GeneralLedgerService};


class PaymentController extends Controller
{

        protected SuppliersService $supplierService;
        protected GeneralLedgerService $generalLedgerService;
       // private Collection $project_records;
    public function __construct()
    {
        $this->supplierService = new SuppliersService();
        $this->generalLedgerService = new GeneralLedgerService();

        // $this->project_records = (new ProjectDataService())->getAllActiveProjectListForDropdown(1);
    }

    public function billPaymentView()
    {
        // $data['cr_accounts'] = ChartOfAccounts::select(
        //         'chart_of_acct_name',
        //         'chart_of_acct_number',
        //         'chart_of_acct_id'
        //     )->where('chart_of_acct_name', 'like', '%Sales Revenue%')->get();


        //! load only liabiltiy type account
         $data['liability_accounts'] =  $this->generalLedgerService->getChartOfAccountsMotherAccountlistWithOutHeaderAccountForDropdown(2);

        //! load only asset type account
        $data['cr_accounts'] =  $this->generalLedgerService->getChartOfAccountsMotherAccountlistWithOutHeaderAccountForDropdown(1);
        $data['suppliers'] = $this->supplierService->getActiveSupplierInformationForDropdown();

        return view('account::pages.payment.bill_payment', [
            'data' => $data
        ]);
    }

    public function billPaymentSave(Request $request)
    {
        Log::info('Bill Payment Data:'.json_encode($request->all()));

      // return  response()->json(['data'=>$request->all()], 422);


        $validator = Validator::make($request->all(), [
            'supplier_id'    => 'required|exists:suplier_subsidiary_ledger,supplier_id',
            'invoice_number' => 'nullable|string|max:255|unique:suplier_transactions,invoice_no',
            'payment_date'   => 'required|date',
            'bill_amount'    => 'required|numeric',
            'bank_charge'    => 'required|numeric', #? Deffault set 0
            'total'          => 'required|numeric',
            'files'          => 'nullable|array',
            'files.*'        => 'file|max:20480',
            'remarks'        => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errorssss' => $validator->errors(),'data'=>$request->all()], 422);
        }


        // Begin transaction to ensure atomicity
        DB::beginTransaction();
         try {

            $tr = new AccountTransaction();
            $tr->date = $request->payment_date;
            $tr->tr_no = $request->invoice_number;
            //$tr->purchase_invoice_id = null; // we dont have info
            $tr->approve_by = auth()->user()->id;
            $tr->total_amount = $request->total;
            $tr->general_particular = "Bill Payment";
            $tr->save();

            // Credit
            $tr_cr = new AccountTransactionDetails();
            $tr_cr->trd_id = $tr->id;
            $tr_cr->account_no = $request->credit_account_id;
            $tr_cr->credit = $request->total; // including bank charge
            $tr_cr->particular = "bill payment";// "{$request->total}  cr from {$request->credit->account_id}";
            $tr_cr->save();

            // Debit
            $dr_account_info = $this->generalLedgerService->getGeneralLedgerAccountPayableAccountRecord(2);
            $chart_account_auto_id = $dr_account_info == null ? 10 : $dr_account_info->chart_of_acct_id   ;
            $tr_de = new AccountTransactionDetails();
            $tr_de->trd_id = $tr->id;
            $tr_de->account_no =  $chart_account_auto_id; // Accounts Payable (Control) = 10
            $tr_de->debit = $request->bill_amount;  // without bank charge  $request->bank_charge
            $tr_de->particular = "bill payment";// "{$request->total} dr from { $dr_account_id}";
            $tr_de->save();

            // bank charge will be as expense account
            if($request->bank_charge > 0){
                $tr_de = new AccountTransactionDetails();
                $tr_de->trd_id = $tr->id;
                $tr_de->account_no = 24 ; // purchase expense account id
                $tr_de->debit =$request->bank_charge; // bank charge  as expense jurnal entry
                $tr_de->particular = "bill payment bank charge";// "{$request->total} dr from { $dr_account_id}";
                $tr_de->save();
            }

                $this->supplierService->saveSupplierPaymentSubsidiaryLedgerTransaction(
                       $request->supplier_id ,$tr->id,$request->payment_date,$request->invoice_number,$request->bill_amount,$request->remarks,
                       auth()->user()->id,auth()->user()->branch_office_id
                   );


          // Commit the transaction
            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Bill payment successfully saved',
                'data'    => $request->all(),
            ], 201);
        } catch (\Exception $e) {
            // Rollback the transaction on error
            DB::rollBack();

            Log::error($e->getMessage());

            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }


    }




    public function fundTransferView()
    {
        //! load only liabiltiy type account  load only asset type account as debit account
        $data['dr_accounts'] =   $this->generalLedgerService->getChartOfAccountsMotherAccountlistWithOutHeaderAccountForDropdown(1);
        //! load only asset type account as credit account
        $data['cr_accounts'] =   $this->generalLedgerService->getChartOfAccountsMotherAccountlistWithOutHeaderAccountForDropdown(1);

        return view('account::pages.payment.fund_transfer', [
            'data' => $data
        ]);
    }


    public function fundTransferSave(Request $request)
    {
        Log::info('Fund Transfer Data:' . json_encode($request->all()));
        // return response()->json([
        //         'status'  => 'success',
        //         'message' => 'Bill payment successfully saved',
        //         'data'    => $request->all(),
        //     ], 201);


        $validator = Validator::make($request->all(), [
            'invoice_number' => 'nullable|string|max:255',
            'payment_date'   => 'required|date',
            'credit_account_id'    => 'required|numeric',
            'debit_account_id'    => 'required|numeric',
            'bill_amount'    => 'required|numeric',
            'bank_charge'    => 'required|numeric', #? Deffault set 0
            'vat'    => 'required|numeric',
            'total'          => 'required|numeric',
            'files'          => 'nullable|array',
            'files.*'        => 'file|max:20480',
            'remarks'        => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errorssss' => $validator->errors(),'data'=>$request->all()], 422);
        }

          // Begin transaction to ensure atomicity
        DB::beginTransaction();
        try {

            $tr = new AccountTransaction();
            $tr->date = $request->payment_date;
            $tr->tr_no = $request->invoice_number;
            $tr->approve_by = auth()->user()->id;
            $tr->total_amount = $request->total;
            $tr->general_particular = "Fund Transfer";
            $tr->save();

            // Credit
            $tr_cr = new AccountTransactionDetails();
            $tr_cr->trd_id = $tr->id;
            $tr_cr->account_no = $request->credit_account_id;  // source account credit
            $tr_cr->credit = $request->total;
            $tr_cr->particular =  "F.T Sender"; // Fund Transfer Sender
            $tr_cr->save();

            // Debit
            $tr_de = new AccountTransactionDetails();
            $tr_de->trd_id = $tr->id;
            $tr_de->account_no = $request->debit_account_id; // receiver account debit
            $tr_de->debit = $request->bill_amount;  // only receiving amount
            $tr_de->particular = "F.T Receiver"; // Fund Transfer Receiver
            $tr_de->save();

              // bank charge will be as expense account
            if($request->bank_charge > 0){
                $tr_de = new AccountTransactionDetails();
                $tr_de->trd_id = $tr->id;
                $tr_de->account_no = 24 ; // purchase expense account id
                $tr_de->debit =$request->bank_charge+$request->vat; // bank charge+vat  as expense journal entry
                $tr_de->particular = "F.T bank charge";// "{$request->total} dr from { $dr_account_id}";
                $tr_de->save();
            }

            // Commit the transaction
            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Bill payment successfully saved',
                'data'    => $request->all(),
            ], 201);
        } catch (\Exception $e) {
            // Rollback the transaction on error
            DB::rollBack();
            Log::error($e->getMessage());

            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }



    }
}
