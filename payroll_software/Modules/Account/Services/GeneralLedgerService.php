<?php


namespace Modules\Account\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Modules\Account\Models\{AccountTransaction, AccountTransactionDetails};
use Modules\Account\Models\ChartofaccSalesRecord;
use Modules\Account\Models\ChartofaccSalesRecordDetail;
// use App\Models\BankAccountType;
use App\Models\AccountsModule\ChartOfAccounts;
use App\Models\AccountsModule\ChartofAccountType;
use PhpOption\None;

class GeneralLedgerService{





      /*
     ==========================================================================
     ======================== Account Type Module =============================
     ==========================================================================
    */

    public function getChartOfAccountAllChartofAccountType()
    {
        return ChartofAccountType::select('acct_type_id', 'acct_type_name')->get();
    }
    public function getChartOfAccountAccountTypeByTypeId($id)
    {
        return ChartofAccountType::where('acct_type_id',$id)->first();
    }
    public function getLiabilityAccountTypeId()
    {
        return ChartofAccountType::where('acct_type_name','Liability')->first()->acct_type_id;
    }
     public function getEquityAccountTypeId()
    {
        return ChartofAccountType::where('acct_type_name','Owner Equity')->first()->acct_type_id;
    }

    public function getAssetAccountTypeId()
    {
        return ChartofAccountType::where('acct_type_name','like','Asset%')->first()->acct_type_id;
    }

    public function getChartOfAccountAllBankAccountType()
    {
        return BankAccountType::where('ban_acc_type_status', 1)->select('bank_acct_type_id', 'ban_acc_type_name')->get();
    }


    /*
     ==========================================================================
     ======================== General Ledger Accounts Module ==================
     ==========================================================================
    */

    public function getGeneralLedgerMontherAccountWhichParentIdNull($acct_type_id){
         return ChartOfAccounts::where('acct_type_id', $acct_type_id)->where('parent_id',null)->first();
    }
    public function getGeneralLedgerAccountPayableAccountRecord($acct_type_id){
         return ChartOfAccounts::where('acct_type_id', $acct_type_id)->where('chart_of_acct_name','like','Accounts Payable (Control)%')->first();
    }

    public function getGeneralLedgerReceiableAssetTypeAccountRecord($acct_type_id){

         return ChartOfAccounts::where('acct_type_id', $acct_type_id)->where('chart_of_acct_name','like','Accounts Receivable(Control)%')->first();
    }

    public function getGeneralLedgerOwnerEquityCapitalAccountRecord(){
        //Capital
        $acct_type_id = $this->getEquityAccountTypeId();
        return ChartOfAccounts::where('acct_type_id', $acct_type_id)->where('chart_of_acct_name','like','Capital%')->first();
    }


     public function initialGeneralLedgerAccountBalanceSetup($tr_date,$tr_no,$sales_id,$approved_by,$inital_amount,$notes,$dr_account,$cr_account)
    {

          try {

                // $account_type = $this->getChartOfAccountAccountTypeByTypeId($acct_type_id);
                // if(strtolower($account_type->acct_type_name) == 'asset'){
                //     $dr_account = $new_ledger_account;
                //     $cr_account = $this->getGeneralLedgerMontherAccountWhichParentIdNull(3)->chart_of_acct_id; // 3 = Equity
                // }
                // elseif(strtolower($account_type->acct_type_name) == 'liability'){

                //     $cr_account = $this->getGeneralLedgerMontherAccountWhichParentIdNull(3)->chart_of_acct_id; // 3 = Equity
                // }

                $tr = new AccountTransaction();
                $tr->date = $tr_date;
                $tr->tr_no = $tr_no;
                $tr->sales_id = $sales_id;
                $tr->approve_by = $approved_by;
                $tr->total_amount = $inital_amount;
                $tr->general_particular = $notes;
                $tr->save();

                // Credit
                $tr_cr = new AccountTransactionDetails();
                $tr_cr->trd_id = $tr->id;
                $tr_cr->account_no = $cr_account;
                $tr_cr->credit = $inital_amount;
                $tr_cr->particular = "{$inital_amount} amt cr from {$cr_account}";
                $tr_cr->save();

                // Debit
                $tr_de = new AccountTransactionDetails();
                $tr_de->trd_id = $tr->id;
                $tr_de->account_no = $dr_account;
                $tr_de->debit = $inital_amount;
                $tr_de->particular = "{$inital_amount} amt dr from {$dr_account}";
                $tr_de->save();
                return true;

            } catch (\Exception $e) {
                Log::error('Error in creating transaction:', ['error' => $e->getMessage()]);
                return false;
            }
    }

    public function storeChartOfAccountDetailsInformation($acct_type_id, $chart_of_acct_name, $chart_of_acct_number, $account_id, $acct_balance, $opening_date, $active_status, $created_by)
    {
        $sibling_level = ChartOfAccounts::where('chart_of_acct_id', $account_id)->max('sibling_level');
        $sibling_level = $sibling_level ? $sibling_level + 1 : 1;
        return ChartOfAccounts::insertGetId([
            'acct_type_id' => $acct_type_id,
            'chart_of_acct_name' => $chart_of_acct_name,
            'chart_of_acct_number' => $chart_of_acct_number,
            'parent_id' => $account_id,
            'sibling_level' => $sibling_level,
            'acct_balance' => $acct_balance,
            'opening_date' => $opening_date,
            'active_status' => $active_status ?? 1,
            'created_by_id' => $created_by,
            'created_at' => Carbon::now()
        ]);
    }

    public function getChartOfAccountsMotherAccountlistForDropdown($account_type_id = null)
    {

        if ($account_type_id) {
            return ChartOfAccounts::select('chart_of_acct_id', 'chart_of_acct_name', 'chart_of_acct_number','acct_type_id')
            ->where('is_closed', 0)
           // ->whereNotNull('parent_id')
            ->where('acct_type_id', $account_type_id)->get();
        }
        return ChartOfAccounts::select('chart_of_acct_id', 'chart_of_acct_name', 'chart_of_acct_number','acct_type_id')
            ->where('is_closed', 0)
          //  ->whereNotNull('parent_id')
            ->orderBy('acct_type_id','asc')
            ->get();

    }

    public function getChartOfAccountsMotherAccountlistWithOutHeaderAccountForDropdown($account_type_id = null)
    {

        if ($account_type_id) {
            return ChartOfAccounts::select('chart_of_acct_id', 'chart_of_acct_name', 'chart_of_acct_number','acct_type_id')
            ->where('is_closed', 0)
            ->whereNotNull('parent_id')
            ->where('acct_type_id', $account_type_id)->get();
        }
        return ChartOfAccounts::select('chart_of_acct_id', 'chart_of_acct_name', 'chart_of_acct_number','acct_type_id')
            ->where('is_closed', 0)
            ->whereNotNull('parent_id')
            ->orderBy('acct_type_id','asc')
            ->get();

    }




    public function getAChartOfAccountsMothAccountInformation($chart_of_acct_id)
    {

            $account = ChartOfAccounts::with('parentAccount')
                ->where('chart_of_acct_id', $chart_of_acct_id)
                ->first();

            // Access parent account details
            if ($account->parentAccount) {
                $parentName = $account->parentAccount->name;
                $parentId = $account->parentAccount->account_id;
            }
    }

    public function getAllChartOfAccountInformation()
    {
        return DB::table('chart_of_accounts')
        ->leftJoin('chartof_account_types', 'chartof_account_types.acct_type_id', '=', 'chart_of_accounts.acct_type_id')
        ->select(
            'chartof_account_types.acct_type_name',
            'chart_of_accounts.*'
        )
        ->where('chart_of_accounts.is_closed', 0)
        ->get();
    }

    public function closeSingleChartOfAccountInformation($id)
    {
        return ChartOfAccounts::where('chart_of_acct_id', $id)->update([
            'is_closed' => 1,
        ]);
    }

    public function chartOfAccountInformationById($id){
        return ChartOfAccounts::where('chart_of_acct_id', $id)->first();
    }

    public function getListOfChartOfAccountInformationByAccountTypeId($acct_type_id){
        return ChartOfAccounts::select('chart_of_acct_name','chart_of_acct_number','chart_of_acct_id')->where('acct_type_id', $acct_type_id)->get();
    }
    // public function getChartOfAccountsMotherAccountlistForDropdown(){
    //     return ChartOfAccounts::select('chart_of_acct_name','chart_of_acct_number','chart_of_acct_id')->where('is_closed', 0)->select('chart_of_acct_id', 'chart_of_acct_name', 'chart_of_acct_number')->get();;
    // }

    public function getChartOfAccountsInformationForDropdown(){
        return ChartOfAccounts::select('chart_of_acct_name','chart_of_acct_number','chart_of_acct_id')->where('is_closed', 0)->select('chart_of_acct_id', 'chart_of_acct_name', 'chart_of_acct_number')->get();;
    }


    public function getChartOfAccountsInformationByJournalTypeIdForDropdown($id){
        return ChartOfAccounts::where('is_closed', 0)->select('chart_of_acct_id', 'chart_of_acct_name', 'chart_of_acct_number')->get();
    }

    // public function getAChartOfAccountsAsDebitAccountInPurchaseInvoiceEntryForDropdown($id){
    //     return ChartOfAccounts::where('is_closed', 0)->select('chart_of_acct_id', 'chart_of_acct_name', 'chart_of_acct_number')->get();
    // }


    public function updateChartOfAccountDetailsInformation($chart_of_acct_id, $acct_type_id, $chart_of_acct_name, $chart_of_acct_number, $account_id, $acct_balance, $opening_date, $active_status, $update_by){

        $sibling_level = ChartOfAccounts::where('chart_of_acct_id', $account_id)->max('sibling_level');
        $sibling_level = $sibling_level ? $sibling_level + 1 : 1;
        return ChartOfAccounts::where('chart_of_acct_id', $chart_of_acct_id)->update([
            'acct_type_id' => $acct_type_id,
            'chart_of_acct_name' => $chart_of_acct_name,
            'chart_of_acct_number' => $chart_of_acct_number,
            'parent_id' => $account_id,
            'sibling_level' => $sibling_level,
            'acct_balance' => $acct_balance,
            'opening_date' => $opening_date,
            'active_status' => $active_status == null ? 0 : 1,
            'updated_by_id' => $update_by,
            'updated_at' => Carbon::now()
        ]);
    }

}
