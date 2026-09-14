<?php

namespace App\Http\Controllers\Admin\AccountsModule;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\DataServices\ChartOfAccountDataService;

class JournalInfoController extends Controller
{
    public function index(){
        $journal_types = (new ChartOfAccountDataService())->getAllJournalTypeInformationForDropDown();

        return view('admin.accounts_module.journal_info.index', compact('journal_types'));
    }

    public function storeJournalInformation(Request $request){
        $request->validate([
            'jour_type_id' => 'required|integer',
            'chart_of_acct_id' => 'required|integer',
            'jour_name' => 'required|string|max:255',
            'jour_code' => 'required|string|max:20|unique:journal_infos,jour_code',
        ]);
        $insert = (new ChartOfAccountDataService())->storeAllRequestedJournalInformation($request->jour_type_id, $request->chart_of_acct_id, $request->jour_name, $request->jour_code);

        if ($insert) {
            return response()->json([
                'success' => true,
                'message' => 'Successfully! Data Inserted.',
            ]);
        }else {
            return response()->json([
                'success' => false,
                'message' => 'Operation Failed. May be Same Journal Info Already Exist',
            ]);
        }
    }


    public function getChartOfAccountInfosByJournalTypeId($journalTypeId){
        $chart_of_accounts = (new ChartOfAccountDataService())->getChartOfAccountsInformationByJournalTypeIdForDropdown($journalTypeId);
        return json_encode($chart_of_accounts);
    }

    public function getAccountJournalInformationForEdit($jourInfoId){
        $jour_Info = (new ChartOfAccountDataService())->getAccountJournalInformationById($jourInfoId);

        if ($jour_Info) {
            return response()->json(
                [
                    'success' => true,
                    'jour_Info' => $jour_Info,
                ],
            );
        } else {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Journal information not found.'
                ],
            );
        }
    }


    public function deActiveJournalInfo($jourInfoID){
        $jour_info_de_active = (new ChartOfAccountDataService())->deActivateRequestedJournalInfo($jourInfoID);

        if ($jour_info_de_active) {
            return response()->json([
                'success' => true,
                'message' => 'Journal Info status updated successfully.'
            ], 200); // HTTP status 200 OK
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Journal Info not found.'
            ], 404); // HTTP status 404 Not Found
        }
    }



    public function updateJournalInformation(Request $request){
        $request->validate([
            'jour_type_id' => 'required|integer',
            'chart_of_acct_id' => 'required|integer',
            'jour_name' => 'required|string|max:255',
            'jour_code' => [ 'required', 'string', 'max:20', Rule::unique('journal_infos', 'jour_code')->ignore($request->jour_id, 'jour_id')
            ],
        ]);


        $update = (new ChartOfAccountDataService())->updateAllRequestedJournalInformation($request->jour_id, $request->jour_type_id, $request->chart_of_acct_id, $request->jour_name, $request->jour_code);

        if ($update) {
            return response()->json([
                'success' => true,
                'message' => 'Successfully! Data Updated.',
            ]);
        }else {
            return response()->json([
                'success' => false,
                'message' => 'Operation Failed. May be Same Journal Info Already Exist',
            ]);
        }
    }




    public function getAllAccountJournalInfos(){
        $journal_infos = (new ChartOfAccountDataService())->getAllJournalInformation();

        return response()->json(
            [
                'success' => true,
                'journal_infos' => $journal_infos,
            ],
        );
    }




}
