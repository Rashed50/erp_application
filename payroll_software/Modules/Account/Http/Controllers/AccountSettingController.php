<?php

namespace Modules\Account\Http\Controllers;

use App\Http\Controllers\DataServices\{ChartOfAccountDataService};
use App\Http\Requests\ChartOfAccountStoreRequest;
use Modules\Account\Services\{GeneralLedgerService};
use App\Models\AccountsModule\ChartOfAccounts;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class AccountSettingController extends Controller
{
    public function index()
    {

        // load chart of account create UI
        $account_types = (new ChartOfAccountDataService())->getChartOfAccountAllChartofAccountType();
        $mother_accounts = (new ChartOfAccountDataService())->getChartOfAccountsMotherAccountlistForDropdown();

        return view('account::pages.chart_of_account.index', [
            'data' => [
                'account_types' => $account_types,
                'mother_accounts' => $mother_accounts,
            ]
        ]);
    }

    public function storeChartOfAccountInfos(ChartOfAccountStoreRequest $request)
    {
        // Log::info("Request received");
        // Log::info($request->all());

        $insert_infos = (new ChartOfAccountDataService())->storeChartOfAccountDetailsInformation(
            $request->acct_type_id,
            $request->chart_of_acct_name,
            $request->chart_of_acct_number,
            $request->account_id,
            $request->acct_balance,
            $request->opening_date,
            $request->active_status,
            Auth::user()->id,
        );
        if($request->acct_balance >0){

            $dr_account =   $request->account_id;
            $account_type =  (new GeneralLedgerService())->getChartOfAccountAccountTypeByTypeId( $request->acct_type_id);
            if(strtolower($account_type->acct_type_name) == 'asset'){
                $equity_account_type_id = (new GeneralLedgerService())->getEquityAccountTypeId();
                $cr_account = (new GeneralLedgerService())->getGeneralLedgerMontherAccountWhichParentIdNull($equity_account_type_id)->chart_of_acct_id; // 3 = Equity
                (new GeneralLedgerService())->initialGeneralLedgerAccountBalanceSetup($request->opening_date,"",1000,Auth::user()->id,$request->acct_balance,"Initial Ledger account setup",$dr_account,$cr_account);
            }

        }


        if ($insert_infos) {
            return response()->json([
                'success' => true,
                'message' => 'Successfully! Data Inserted.',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Something! Went Wrong.',
            ]);
        }
    }

    public function listChartOfAccountInfos()
    {
        //dd('dd');
        // load chart of account page

        $chartOfAccounts = (new ChartOfAccountDataService())->getChartOfAccountsMotherAccountlistForDropdown();
        return view('account::pages.chart_of_account.list', compact('chartOfAccounts'));
    }

    public function listChartOfAccountInfosAPI(Request $request)
    {

        $perPage = (int)$request->query('per_page', 10);
        $page = (int)$request->query('page', 1);
        $search = $request->query('search', '');


        $query = ChartOfAccounts::with([
            'chartofAccountType',
            'createdUser',
            'parentAccount',
        ])->join('chartof_account_types', 'chart_of_accounts.acct_type_id', '=', 'chartof_account_types.acct_type_id')
            ->whereNotNull('parent_id')
            ->where('chart_of_accounts.is_closed', false)
            ->orderBy('chart_of_accounts.created_at', 'desc')
            ->orderBy('chart_of_accounts.acct_type_id', 'asc');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('chart_of_acct_name', 'LIKE', "%{$search}%")
                    ->orWhere('chart_of_acct_number', 'LIKE', "%{$search}%")
                   // ->orWhere('chart_of_accounts.chart_of_acct_name', 'LIKE', "%{$search}%") // its not working, but need for parent account name searching
                    ->orWhere('chartof_account_types.acct_type_name', 'LIKE', "%{$search}%");
            });
        }

        $chartOfAccounts = $query->paginate($perPage, ['*'], 'page', $page);
        return response()->json($chartOfAccounts);
    }

    public function closeSingleChartOfAccount($accountId)
    {
        // Log::info("AccountId =".$accountId);
        $close_account = (new ChartOfAccountDataService())->closeSingleChartOfAccountInformation($accountId);
        // Log::info("AccountId =".$close_account);

        if ($close_account) {
            return response()->json([
                'success' => true,
                'message' => 'Account status updated successfully.'
            ], 200); // HTTP status 200 OK
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Account not found.'
            ], 404); // HTTP status 404 Not Found
        }
    }

    public function ChartOfAccountEditGet($id)
    {
        $chart_of_account = (new ChartOfAccountDataService())->chartOfAccountInformationById($id);
        $account_types = (new ChartOfAccountDataService())->getChartOfAccountAllChartofAccountType();
        $mother_accounts = (new ChartOfAccountDataService())->getChartOfAccountsMotherAccountlistForDropdown();
      //  dd($chart_of_account);

        return view('account::pages.chart_of_account.edit', [
            'data' => [
                'chart_of_account' => $chart_of_account,
                'account_types' => $account_types,
                'mother_accounts' => $mother_accounts,
            ]
        ]);
    }

    public function updateChartOfAccountInfos(ChartOfAccountStoreRequest $request, $chart_of_acct_id)
    {


        $recordExists = ChartOfAccounts::where('chart_of_acct_id', $chart_of_acct_id)->exists();
        // Log::info('Record exists: ' . $recordExists);

        if ($recordExists) {
            $update_infos = (new ChartOfAccountDataService())->updateChartOfAccountDetailsInformation(
                $chart_of_acct_id,
                $request->acct_type_id,
                $request->chart_of_acct_name,
                $request->chart_of_acct_number,
                $request->account_id,
                $request->acct_balance,
                $request->opening_date,
                $request->active_status,
                Auth::user()->id,
            );



            if ($update_infos) {
                return response()->json([
                    'success' => true,
                    'message' => 'Successfully! Data Updated.',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Something went wrong!',
                ]);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Record not found.',
            ]);
        }
    }

        public function getUniqueAccountNumberBySelectedLedgerAccount($chart_of_acct_id)
        {
            $unique_account_number = (new ChartOfAccountDataService())->generateUniqueAccountNumberBySelectedLedgerAccount($chart_of_acct_id);
            return response()->json(['unique_account_number' => $unique_account_number]);
        }


}
