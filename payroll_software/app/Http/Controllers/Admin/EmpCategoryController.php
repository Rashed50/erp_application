<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DataServices\EmployeeRelatedDataService;
use Illuminate\Http\Request;
use Session;

class EmpCategoryController extends Controller
{
  /*
    |--------------------------------------------------------------------------
    |  DATABASE OPERATION
    |--------------------------------------------------------------------------
    */
  public function getAllCategory()
  {
    // Emplpoyee Designation is the called Employee Category
    return $all = (new EmployeeRelatedDataService())->getEmployeeAllCategory();
  }

  public function findCategory($id)
  {
    return $edit = (new EmployeeRelatedDataService())->getEmployeeCategory($id);
  }


  /*
    |--------------------------------------------------------------------------
    |  BLADE OPERATION
    |--------------------------------------------------------------------------
    */

  public function index()
  {

     $all = (new EmployeeRelatedDataService())->getEmployeeAllCategory();
    $trade_heads =  (new EmployeeRelatedDataService())->getDesignationHeadRecordsForDropdown();
    $getType = (new EmployeeRelatedDataService())->getEmployeeTypeForDropdown();

    return view('admin.employee-category.add', compact('all', 'getType','trade_heads'));
  }

  public function edit($id)
  {
    $edit = $this->findCategory($id);
    $getType = (new EmployeeRelatedDataService())->getAllEmployeeType();
    $trade_heads =  (new EmployeeRelatedDataService())->getDesignationHeadRecordsForDropdown();
    return view('admin.employee-category.edit', compact('edit', 'getType','trade_heads'));
  }

  public function insert(Request $req)
  {

    /* form validation */
    $this->validate($req, [
      'catg_name' => 'required',
      'emp_type_id' => 'required'
    ], [
      'catg_name.required' => 'please enter category name'
    ]);
    /* data insert */
    $insert = (new EmployeeRelatedDataService())->insertNewCategoryName($req->catg_name, $req->emp_type_id,$req->emp_trade_head);

    if ($insert) {
      Session::flash('success_add', 'value');
      return Redirect()->back();
    } else {
      Session::flash('success_error', 'value');
      return Redirect()->back();
    }
  }

  public function update(Request $req)
  {
    /* form validation */
    $this->validate($req, [
      'catg_name' => 'required',
      'emp_type_id' => 'required',
    ], [
      'catg_name.required' => 'please enter category name'
    ]);
    /* data update */
    $id = $req->id;

    $update = (new EmployeeRelatedDataService())->updateCategoryName($id, $req->catg_name, $req->emp_type_id,$req->emp_trade_head);

    /* Redirect back */
    if ($update) {
      Session::flash('success_update', 'value');
      return Redirect()->route('emp-category');
    } else {
      Session::flash('error_update', 'value');
      return Redirect()->back();
    }
  }

  public function delete($id)
  {
    $delete = (new EmployeeRelatedDataService())->deleteCategory($id);

    /* Redirect back */
    if ($delete) {
      Session::flash('success_delete', 'value');
      return Redirect()->back();
    } else {
      Session::flash('success_error', 'value');
      return Redirect()->back();
    }
  }
}
