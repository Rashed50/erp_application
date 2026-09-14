<?php

namespace App\Http\Controllers\Admin\AccountsModule;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChartOfAccountStoreRequest;
use App\Http\Controllers\DataServices\ChartOfAccountDataService;
use App\Http\Controllers\DataServices\EmployeeRelatedDataService;
use Illuminate\Support\Facades\Auth;

class ChartOfAccountController extends Controller
{
    // Middleware
    function __construct()
    {
       // $this->middleware('permission:chart-of-account-manage', ['only' => ['index', 'edit', 'store', 'update']]);
    }


    public function index()
    {
        $account_types = (new ChartOfAccountDataService())->getChartOfAccountAllChartofAccountType();
        $mother_accounts = (new ChartOfAccountDataService())->getChartOfAccountsMotherAccountlistForDropdown();
        return view('admin.accounts_module.chart_of_account.index', compact('account_types','mother_accounts'));
    }

    public function storeChartOfAccountInfos(ChartOfAccountStoreRequest $request){
        $insert_infos = (new ChartOfAccountDataService())->storeChartOfAccountDetailsInformation(
            $request->acct_type_id,
            $request->chart_of_acct_name,
            $request->chart_of_acct_number,
            $request->account_id,
            $request->acct_balance,
            $request->opening_date,
            $request->active_status,
            Auth::user()->id
        );

        if ($insert_infos) {
            return response()->json([
                'success' => true,
                'message' => 'Successfully! Data Inserted.',
            ]);
        }else {
            return response()->json([
                'success' => false,
                'message' => 'Something! Went Wrong.',
            ]);
        }
    }

    public function getAllChartOfAccountInfos(){
        $chart_of_accounts = (new ChartOfAccountDataService())->getAllChartOfAccountInformation();

        return response()->json(
            [
                'success' => true,
                'chart_of_accounts' => $chart_of_accounts,
            ],
        );
    }


    public function closeSingleChartOfAccount($accountId){
        $close_account = (new ChartOfAccountDataService())->closeSingleChartOfAccountInformation($accountId);

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



    public function getInformationForChartOfAccountEdit($accountId){
        $chart_of_account = (new ChartOfAccountDataService())->chartOfAccountInformationById($accountId);

        if ($chart_of_account) {
            return response()->json(
                [
                    'success' => true,
                    'chart_of_account' => $chart_of_account,
                ],
            );
        } else {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Account information not found.'
                ],
            );
        }
    }




    public function searchChartOfAccountByAccountType($account_type_Id){

        try{
            $chart_of_account = (new ChartOfAccountDataService())->getListOfChartOfAccountInformationByAccountTypeId($account_type_Id);
            return response()->json(
                [
                    'status'=> 200,
                    'success' => true,
                    'data' => $chart_of_account,
                    'para'=> $account_type_Id,
                ],
            );
        }catch(Exception $ex){
                return response()->json(
                    [
                        'status'=> 404,
                        'success' => false,
                        'error'=>'error',
                        'message' => 'Account Head not found.'
                    ],
                );
        }

    }



    public function updateChartOfAccountInfos(ChartOfAccountStoreRequest $request){

        $update_infos = (new ChartOfAccountDataService())->updateChartOfAccountDetailsInformation(
            $request->chart_of_acct_id,
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
        }else {
            return response()->json([
                'success' => false,
                'message' => 'Something! Went Wrong.',
            ]);
        }
    }





}
