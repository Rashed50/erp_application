<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\CountryController;
use App\Models\Division;
use Carbon\Carbon;
use App\Http\Controllers\DataServices\EmployeeRelatedDataService;
use Session;


class DivisionController extends Controller
{
  /*
  ======================================
  ==========DATABASE OPERATION==========
  ======================================
  */
  // permission
  function __construct()
  {
    $this->middleware('permission:division-list', ['only' => ['add']]);
    $this->middleware('permission:division-create', ['only' => ['add', 'getAllDivision', 'insert']]);
    $this->middleware('permission:division-edit', ['only' => ['edit', 'editDivision', 'update']]);
  }
  // permission
  public function getAllDivision()
  {
    return (new EmployeeRelatedDataService())->getDivisions(null);
  }

  public function editDivision($division_id)
  {
    return (new EmployeeRelatedDataService())->getDivisions($division_id);
  }

  public function validDivision(Request $request)
  {

    $findDivision = (new EmployeeRelatedDataService())->checkDivisionIsExist($request->division_name, $request->country_id);

    if ($findDivision) {
      return response()->json(['status' => 'success']);
    } else {
      return response()->json(['status' => 'error']);
    }
  }


  /*
  ======================================
  ============BLADE OPERATION===========
  ======================================
  */


  public function add()
  {
    $countries =  (new EmployeeRelatedDataService())->getAllCountry();
    $getallDiv =  (new EmployeeRelatedDataService())->getDivisions(null);  // Retrive all divisions
    return view('admin.address.add', compact('countries', 'getallDiv'));
  }

  public function edit($division_id)
  {

    $edit = (new EmployeeRelatedDataService())->getDivisions($division_id);
    $countries =  (new EmployeeRelatedDataService())->getAllCountry();
    return view('admin.address.edit', compact('edit', 'countries'));
  }

  public function insert(Request $req)
  {
    /* form validation */
    $this->validate($req, [
      'country_id' => 'required',
      'division_name' => 'required',
    ], [
      'country_id.required' => 'please select country',
      'division_name.required' => 'please insert division name',
    ]);

    $Division = (new EmployeeRelatedDataService())->checkDivisionIsExist($req->division_name, $req->country_id);

    if ($Division) {
      Session::flash('already_exit', 'value');
      return redirect()->back();
    } else {
      /* insert data in database */
      $insert = (new EmployeeRelatedDataService())->insertNewDivision($req->country_id, $req->division_name);

      if ($insert) {
        Session::flash('success_add', 'value');
        return redirect()->back();
      } else {
        Session::flash('error_add', 'value');
        return redirect()->back();
      }
      /*_____________*/
    }
  }

  public function update(Request $req)
  {
    /* form validation */
    $this->validate($req, [
      'country_id' => 'required',
      'division_name' => 'required',
    ], [
      'country_id.required' => 'please select country',
      'division_name.required' => 'please insert division name',
    ]);

    $division = (new EmployeeRelatedDataService())->checkDivisionIsExist($req->division_name, $req->country_id);

    if ($division) {
      Session::flash('already_exit', 'value');
      return redirect()->back();
    } else {
      $update = (new EmployeeRelatedDataService())->updateDivision($req->id, $req->country_id, $req->division_name);
      if ($update) {
        Session::flash('success_update', 'value');
        return Redirect()->route('add-division');
      } else {
        Session::flash('error_update', 'value');
        return Redirect()->route('add-division');
      }
    }
  }
}
