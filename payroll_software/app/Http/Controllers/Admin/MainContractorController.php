<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DataServices\CompanyDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class MainContractorController extends Controller
{
     // Middleware
    function __construct()
    {
        $this->middleware('permission:main_contractor_list', ['only' => ['index', 'insertMainContractorInformations', 'updateMainContractorInformations']]);
    }
    
    public function index(){
        $all = (new CompanyDataService())->getAllMainContractorInfos();
        return view('admin.main-contractor.index', compact('all'));
    }

    public function insertMainContractorInformations(Request $request){
        $insert = (new CompanyDataService())->insertMainContractor($request);
        if($insert) {
          Session::flash('success','Successfully! Added New Main Contrator Infos.');
          return Redirect()->back();
        }else{
          Session::flash('error','Operation Failed. Maybe Same Contractor Already Exist!');
          return Redirect()->back();
        }
    }

    public function updateMainContractorInformations(Request $request){
        $contractorStatus =  $request->lock_checkbox == "on" ? 1: 0;
        $update = (new CompanyDataService())->updateMainContractor($request, $contractorStatus);

        if ($update) {
            Session::flash('success', 'Successfully! Updated Main Contrator Infos.');
            return redirect()->back();
        } else {
            Session::flash('error', 'Operation Failed. Maybe Same Contractor Already Exist!');
            return redirect()->back();
        }
    }
}
