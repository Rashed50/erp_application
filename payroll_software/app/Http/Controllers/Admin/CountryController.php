<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\DataServices\EmployeeRelatedDataService;

use Session;

class CountryController extends Controller
{
  // permission
  function __construct()
  {
    $this->middleware('permission:country-list', ['only' => ['getAllCountry', 'add']]);
    $this->middleware('permission:country-create', ['only' => ['insert']]);
  }
  // permission

  public function getAllCountry()
  {
    return (new EmployeeRelatedDataService())->getAllCountry();
  }

  public function add()
  {

    $allCountries =  (new EmployeeRelatedDataService())->getAllCountry();
    return view('admin.address.country.add', compact('allCountries'));
  }

  public function insert(Request $req)
  {
    /* form validation */
    $this->validate($req, [
      'country_name' => 'required|unique:countries,country_name',
    ], [
      'country_name.required' => 'please insert country name',
    ]);
    $insert = (new EmployeeRelatedDataService())->insertNewCountry($req->country_name);
    if ($insert) {
      Session::flash('success_add', 'value');
      return redirect()->back();
    } else {
      Session::flash('error_add', 'value');
      return redirect()->back();
    }
  }
}
