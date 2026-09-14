<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; 
use App\Http\Controllers\DataServices\EmployeeDataService;
use App\Http\Controllers\DataServices\EmployeeRelatedDataService;
use App\Http\Controllers\DataServices\CompanyDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

 

class SponsorController extends Controller
{
  /*
    |--------------------------------------------------------------------------
    |  DATABASE OPERATION
    |--------------------------------------------------------------------------
    */
  // Permision
  function __construct()
  {
    $this->middleware('permission:sponser-list', ['only' => ['index']]);
    $this->middleware('permission:sponser-create', ['only' => ['index', 'insert']]);
    $this->middleware('permission:sponser-edit', ['only' => ['edit', 'update']]);
    $this->middleware('permission:sponser-delete', ['only' => ['delete']]);
  }
  

  /* Dhaka Insert */
  public function insert(Request $request)
  { 
      $this->validate($request, [
        'spons_name' => 'required',
      ], [
        'spons_name.required' => 'You Must Be Input This Field!',
      ]);
      
      $insert = (new EmployeeRelatedDataService())->insertNewSponerName($request->spons_name);
  
      if ($insert) {
        Session::flash('success', 'Successfully Added');
        return redirect()->back();
      } else {
        Session::flash('error', 'Dulicate Sponsor Name, Please Try Again');
        return redirect()->back();
      }
  }

  public function update(Request $request)
  {
     
    // $this->validate($request, [
    //   'spons_name' => 'required',
    // ], [
    //   'spons_name.required' => 'You Must Be Input This Field!',
      
    // ]); 
    // $update = (new EmployeeRelatedDataService())->updateSponerName($request->id, $request->spons_name);
   
   
    $this->validate($request, [
      'spons_name' => 'required',
      'owner_name' => 'required',
      'active_status' => 'required',
    ], [
      'spons_name.required' => 'You Must Be Input This Field!',
      'owner_name.required' => 'You Must Be Input This Field!',
      'active_status.required' => 'You Must Be Input This Field!',


    ]);
    $update = (new EmployeeRelatedDataService())->updateSponerName($request->id, $request->spons_name, $request->owner_name, $request->active_status);

      if ($update) {
        Session::flash('success', 'Successfully Updated');
        return $this->index();
      } else {
        Session::flash('error', 'Dulicate Sponsor Name, Please Try Again');
        return redirect()->back();
      }

  }

  public function delete($id)
  {
    $updateStatus = (new EmployeeRelatedDataService())->updateSponorAsInactive($id);
 
    if ($updateStatus) {
      Session::flash('delete', 'Successfully Deleted');
      return redirect()->back();
    } else {
      Session::flash('error', 'Delete Operation Failed, Please Try Again');
      return redirect()->back();
    }
  }


  /* ==================== Report Process ==================== */
  public function reportProcess(Request $request)
  {
     
    $company = (new CompanyDataService())->findCompanryProfile();     
    $sponserWiseEmployee = (new EmployeeDataService())->getEmployeeListWithProjectAndSponsor(0,$request->spons_id);
    return view('admin.sponser.report_process', compact('company', 'sponserWiseEmployee'));

  }


  /*
    |--------------------------------------------------------------------------
    |  BLADE OPERATION
    |--------------------------------------------------------------------------
    */
  public function index()
  {
    $all =  (new EmployeeRelatedDataService())->getAllSponsorsInformationForListView(Auth::user()->branch_office_id);
    return view('admin.sponser.all', compact('all'));
  }

  public function edit($id)
  {
     $edit =   (new EmployeeRelatedDataService())->searchASponserBySponsorId($id);
    return view('admin.sponser.edit', compact('edit'));
  } 
  
}
