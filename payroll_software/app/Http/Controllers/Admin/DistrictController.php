<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Division;
use App\Models\District;
use App\Http\Controllers\DataServices\EmployeeRelatedDataService;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class DistrictController extends Controller
{
  /*
  ======================================
  ==========DATABASE OPERATION==========
  ======================================
  */
  // permission
  function __construct()
  {
    $this->middleware('permission:district-list', ['only' => ['getAllDistrict', 'add']]);
    $this->middleware('permission:district-create', ['only' => ['getAllDistrict', 'add', 'insert']]);
    $this->middleware('permission:district-create', ['only' => ['getAllDistrict', 'edit', 'update']]);
  }
  // permission

  public function getAllDistrict()
  {
    return (new EmployeeRelatedDataService())->getDistricts(null);
  }

  public function getDivision($country_id)
  {
    $div = (new EmployeeRelatedDataService())->getDivisionByCountryId($country_id);
    return json_encode($div);
  }

  public function getDistrict($division_id)
  {
    $district = (new EmployeeRelatedDataService())->getDistrictByDivisionId($division_id);
    return json_encode($district);
  }

  public function insert(Request $req)
  {
    /* data validation */
    $this->validate($req, [
      'country_id' => 'required',
      'division_id' => 'required',
      'district_name' => 'required',
    ], [
      'country_id.required' => 'please select country',
      'division_id.required' => 'please select division',
      'district_name.required' => 'please select district',
    ]);
    /* data insert in database */

    $district = (new EmployeeRelatedDataService())->checkDistrictIsExist($req->district_name, $req->division_id, $req->country_id);

    if ($district) {
      Session::flash('already_exit', 'value');
      return Redirect()->back();
    } else {
      $insert = (new EmployeeRelatedDataService())->insertNewDistrict($req->district_name, $req->division_id, $req->country_id);

      /* redirect back */
      if ($insert) {
        Session::flash('success_add', 'value');
        return Redirect()->route('add-district');
      } else {
        Session::flash('success_error', 'value');
        return Redirect()->route('add-district');
      }
      /* ____________ */
    }
  }

  public function update(Request $req)
  {
    /* data validation */
    $this->validate($req, [
      'country_id' => 'required',
      'division_id' => 'required',
      'district_name' => 'required',
    ], [
      'country_id.required' => 'please select country',
      'division_id.required' => 'please select division',
      'district_name.required' => 'please select district',
    ]);
    /* data insert in database */
    $id = $req->id;
    $district = (new EmployeeRelatedDataService())->checkDistrictIsExist($req->district_name, $req->division_id, $req->country_id);

    if ($district) {
      Session::flash('already_exit', 'value');
      return Redirect()->back();
    } else {
      $update = (new EmployeeRelatedDataService())->updateDistrict($id, $req->district_name, $req->division_id, $req->country_id);
      /* redirect back */
      if ($update) {
        Session::flash('success_update', 'value');
        return Redirect()->route('add-district');
      } else {
        Session::flash('error_update', 'value');
        return Redirect()->back();
      }
    }
  }


  /*
  ======================================
  ==========BLADE OPERATION=============
  ======================================
  */

  public function add()
  {


    $allCountry =  (new EmployeeRelatedDataService())->getAllCountry();
    $allDivision = (new EmployeeRelatedDataService())->getDivisions(null);

    $allDistrict = $this->getAllDistrict();
    return view('admin.address.district.add', compact('allCountry', 'allDivision', 'allDistrict'));
  }

  public function edit($id)
  {

    $allCountry =  (new EmployeeRelatedDataService())->getAllCountry();
    $allDivision = (new EmployeeRelatedDataService())->getDivisions(null);
    $edit = (new EmployeeRelatedDataService())->getDistricts($id);
    return view('admin.address.district.edit', compact('allCountry', 'allDivision', 'edit'));
  }
}
