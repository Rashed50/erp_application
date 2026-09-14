<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; 
use App\Http\Controllers\DataServices\CompanyDataService;
use App\Http\Controllers\DataServices\EmployeeDataService;
use App\Http\Controllers\DataServices\EmployeeRelatedDataService;
use App\Http\Controllers\DataServices\AuthenticationDataService;
use App\Http\Controllers\DataServices\ProjectDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CompanyProfileController extends Controller
{

  // Middleware
  function __construct()
  {
    $this->middleware('permission:company-profile', ['only' => ['profile', 'updateProfile']]);
    $this->middleware('permission:company_bank_info_list', ['only' => ['bankInformationsUI', 'bankInformationInsertRequest', 'editBankInformations', 'bankInformationUpdateRequest']]); // Bank Information 

  }
  // Middleware

  public function getAll()
  {
    return   (new CompanyDataService())->getCompanyProfileInformation();
    
  }

  public function findCompanry()
  {
    return $profile = (new CompanyDataService())->findACompanryProfileAllInformation();
  }

  public function profile()
  {

   // $comDSObj = new CompanyDataService();
    $cur = (new CompanyDataService())->getAvailableCurrency();
    $profile =  (new CompanyDataService())->findACompanryProfileAllInformation();
    return view('admin.company-profile.profile', compact('profile', 'cur'));
  }

  public function updateProfile(Request $req)
  {
    // form validation
    $this->validate($req, [
      'comp_name_en' => 'required',
      'comp_name_arb' => 'required',
      'curc_id' => 'required',
      'comp_email1' => 'required',
      'comp_email2' => 'required',
      'comp_phone1' => 'required',
      'comp_phone2' => 'required',
      'comp_mobile1' => 'required',
      'comp_mobile2' => 'required',
      'comp_address' => 'required',
      'comp_mission' => 'required',
      'comp_vission' => 'required',
      'comp_contact_address' => 'required',
      'comp_support_number' => 'required',
      'comp_hotline_number' => 'required',
      'comp_description' => 'required',
    ], []);

    $update = (new CompanyDataService())->insertCompanyInformation($req);

    if ($update) {
      Session::flash('success', 'value');
      return Redirect()->back();
    } else {
      Session::flash('error', 'value');
      return Redirect()->back();
    }
  }
  
  


  /* ======================== Company Bank Informations ======================== */
    public function getSubContractorWiseBankInfoForAjaxCall($subContractorID){
      $bankInfo = (new CompanyDataService())->getBankInfoBySubCompanyId($subContractorID);
      return json_encode($bankInfo);
  }

  public function bankInformationsUI(){
      $all = (new CompanyDataService())->getAllBankInformations();
      $subCompany = (new CompanyDataService())->getSubCompanyListForDropdown();
      return view('admin.bank_information.index', compact('all', 'subCompany'));
  }
  public function bankInformationInsertRequest(Request $request){
      
      $insert = (new CompanyDataService())->insertBankInformations($request);
      if ($insert) {
          Session::flash('success', 'Successfully Added Bank Account Infos.');
          return redirect()->back();
      } else {
          Session::flash('error', 'Operation Failed, Please Try Again With New Account Number!');
          return redirect()->back();
      }
  }
  public function editBankInformations($bankID){
      $edit = (new CompanyDataService())->getSingleBankInformations($bankID);
      $subCompany = (new CompanyDataService())->getSubCompanyListForDropdown();
      return view('admin.bank_information.edit', compact('edit', 'subCompany'));
  }

  public function bankInformationUpdateRequest(Request $request){
      $accnt_status =  $request->lock_checkbox == "on" ? 1: 0;
      $update = (new CompanyDataService())->updateBankInformations($request, $accnt_status);

      if ($update) {
          Session::flash('success', 'Successfully Updated Bank Account Infos.');
          return redirect()->route('bank-infos');
      } else {
          Session::flash('error', 'Operation Failed, Please Try Again with New Account Number!');
          return redirect()->back();
      }
  }
  
  
}
