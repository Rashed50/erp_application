<?php

namespace App\Http\Controllers\Admin\RequirementTools;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\RequirementTools\ItemTypeController;
use Illuminate\Http\Request;
use App\Models\ItemSubCategory;
use App\Models\ItemCategory;
use Carbon\Carbon;
use Session;
use Auth;

class ItemSubCategoryController extends Controller{
  /*
  |--------------------------------------------------------------------------
  |  DATABASE OPERATION
  |--------------------------------------------------------------------------
  */
  public function getAll(){
    return $all = ItemSubCategory::get();
  }

  public function findId($id){
    return $find = ItemSubCategory::where('iscatg_id',$id)->first();
  }

  public function findCategory($id){
    $category = ItemCategory::where('itype_id',$id)->orderBy('icatg_name','ASC')->get();
    return json_encode($category);
  }

  public function findSubCategory($id){
    $subcategory = ItemSubCategory::where('icatg_id',$id)->orderBy('iscatg_name','ASC')->get();
    return json_encode($subcategory);
  }

  public function insert(Request $request){
    // form validation
    $this->validate($request,[
      'itype_id' => 'required',
      'icatg_id' => 'required',
      'iscatg_name' => 'required',
      'stock_amount' => 'required',
    ],[

    ]);
    // insert data ind database
    $entered = Auth::user()->id;
    $insert = ItemSubCategory::insert([
      'itype_id' => $request->itype_id,
      'icatg_id' => $request->icatg_id,
      'iscatg_name' => $request->iscatg_name,
      'stock_amount' => $request->stock_amount,
      'create_by_id' => $entered,
      'created_at' => Carbon::now(),
    ]);
    /* redirect back */
    if($insert){
      Session::flash('success','value');
      return redirect()->back();
    } else{
      Session::flash('error','value');
      return redirect()->back();
    }

  }

  public function update(Request $request){
    // form validation
    $this->validate($request,[
      'itype_id' => 'required',
      'icatg_id' => 'required',
      'iscatg_name' => 'required',
      'stock_amount' => 'required',
    ],[

    ]);
    // insert data ind database
    $id = $request->id;
    $entered = Auth::user()->id;
    $update = ItemSubCategory::where('iscatg_id',$id)->update([
      'itype_id' => $request->itype_id,
      'icatg_id' => $request->icatg_id,
      'iscatg_name' => $request->iscatg_name,
      'stock_amount' => $request->stock_amount,
      'create_by_id' => $entered,
      'updated_at' => Carbon::now(),
    ]);
    /* redirect back */
    if($update){
      Session::flash('success_update','value');
      return redirect()->route('metarial-tools-sub-category');
    } else{
      Session::flash('error','value');
      return redirect()->back();
    }

  }








  /*
  |--------------------------------------------------------------------------
  |  API OPERATION
  |--------------------------------------------------------------------------
  */








  /*
  |--------------------------------------------------------------------------
  |  BLADE OPERATION
  |--------------------------------------------------------------------------
  */
  public function index(){
    $all = $this->getAll();
    /* ==== call itemType controller ==== */
    $itemType = new ItemTypeController();
    $allType = $itemType->getAll();
    return view('admin.metarials-tools.subcategory.all',compact('all','allType'));
  }

  public function edit($id){
    $edit = $this->findId($id);
    /* ==== call itemType controller ==== */
    $itemType = new ItemTypeController();
    $allType = $itemType->getAll();
    return view('admin.metarials-tools.subcategory.edit',compact('edit','allType'));
  }
}
