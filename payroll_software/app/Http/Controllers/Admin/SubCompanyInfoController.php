<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\SubCompanyInfo;
use App\Http\Controllers\Admin\CompanyProfileController;
use App\Http\Controllers\DataServices\CompanyDataService;
use Carbon\Carbon;
use Session;

class SubCompanyInfoController extends Controller{
  /*
  =============================
  =====DATABSE OPEREATION======
  =============================
  */


  
  // Middleware
  function __construct()
  {
       $this->middleware('permission:subcompany-list', ['only' => ['create','insert','view','edit','update']]);
       $this->middleware('permission:subcompany-edit', ['only' => ['edit','update']]);
       $this->middleware('permission:subcompany-delete', ['only' => ['delete']]);
  }
  // Middleware
  public function getAll(){
    return $all = (new CompanyDataService())->getSubCompanyList();
  }
  public function getfindId($id){
    return (new CompanyDataService())->getSubCompanyById($id);
  }
  /*
  =============================
  =======BLADE OPEREATION======
  =============================
  */
  public function create(){
    $all = $this->getAll();
    $comp = (new CompanyDataService())->getCompanyProfileInformation();
    return view('admin.sub-company.add',compact('all','comp'));
  }

  public function edit($id){
      $edit = $this->getfindId($id);
      $comp = (new CompanyDataService())->getCompanyProfileInformation();
      return view('admin.sub-company.edit',compact('edit','comp'));
  }

  public function view($id){
      $view = $this->getfindId($id);
      return view('admin.sub-company.view',compact('view'));
  }

  public function delete($id){
      
      $delete = (new CompanyDataService())->deleteASubCompanyById($id);
      if($delete) {
        Session::flash('success','Successfully Deleted');
        return Redirect()->back();
      }else{
         Session::flash('error','Operation Failed');
        return Redirect()->back();
      }
  }

 
  public function insert(Request $req){
    // form validation
    $this->validate($req,[
      'company_id' => 'required',
      'sb_comp_name' => 'required',
      'sb_comp_name_arb' => 'required',
      'sb_comp_mobile1' => 'required',
      'sb_vat_no' => 'required',
      'sb_comp_email1' => 'required',
      'sb_comp_phone1' => 'required',
      'sb_comp_address' => 'required',
    ],[

    ]);

    $insert = (new CompanyDataService())->subCompanyNameInsertRequest(
        $req->sb_comp_name, $req->sb_comp_name_arb, $req->company_id, $req->sb_comp_mobile1, $req->sb_vat_no, $req->sb_comp_email1,
        $req->sb_comp_email2, $req->sb_comp_phone1, $req->sb_comp_phone2, $req->sb_comp_address, $req->sb_details
    );
    if($insert) {
      Session::flash('success','Successfully Added');
      return Redirect()->back();
    }else{
      Session::flash('error','Operation Failed. May be Same Company Name Already Exist');
      return Redirect()->back();
    }
  }

  public function update(Request $req){
    // form validation
    $this->validate($req,[
        'company_id' => 'required',
        'sb_comp_name' => 'required',
        'sb_comp_name_arb' => 'required',
        'sb_comp_mobile1' => 'required',
        'sb_vat_no' => 'required',
        'sb_comp_email1' => 'required',
        'sb_comp_phone1' => 'required',
        'sb_comp_address' => 'required',
    ],[

    ]);

    $update = (new CompanyDataService())->SubCompanyNameUpdateRequest(
        $req->id,$req->sb_comp_name, $req->sb_comp_name_arb, $req->company_id, $req->sb_comp_mobile1, $req->sb_vat_no, $req->sb_comp_email1,
        $req->sb_comp_email2, $req->sb_comp_phone1, $req->sb_comp_phone2, $req->sb_comp_address, $req->sb_details
    );

    if($update) {
      Session::flash('success','Successfully Updated');
      return Redirect()->route('sub-comp-info');
    }else{
      Session::flash('error','Operation Failed');
      return Redirect()->route('sub-comp-info');
    }
  }
  
  
  

}
