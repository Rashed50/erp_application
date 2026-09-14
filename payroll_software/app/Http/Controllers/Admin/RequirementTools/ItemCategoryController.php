<?php

namespace App\Http\Controllers\Admin\RequirementTools;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\RequirementTools\ItemTypeController;
use Illuminate\Http\Request;
use App\Models\ItemCategory;
use Carbon\Carbon;
use Session;
use Auth;

class ItemCategoryController extends Controller{
  /*
  |--------------------------------------------------------------------------
  |  DATABASE OPERATION
  |--------------------------------------------------------------------------
  */
  public function getAll(){
    return $all = ItemCategory::get();
  }

  public function findId($id){
    return $find = ItemCategory::where('icatg_id',$id)->first();
  }

  public function insert(Request $request){
    // form validation
    $this->validate($request,[
      'itype_id' => 'required',
      'icatg_name' => 'required',
    ],[

    ]);
    // insert data ind database
    $entered = Auth::user()->id;
    $insert = ItemCategory::insert([
      'itype_id' => $request->itype_id,
      'icatg_name' => $request->icatg_name,
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
      'icatg_name' => 'required',
    ],[

    ]);
    // insert data ind database
    $id = $request->id;
    $entered = Auth::user()->id;
    $update = ItemCategory::where('icatg_id',$id)->update([
      'itype_id' => $request->itype_id,
      'icatg_name' => $request->icatg_name,
      'create_by_id' => $entered,
      'updated_at' => Carbon::now(),
    ]);
    /* redirect back */
    if($update){
      Session::flash('success_update','value');
      return redirect()->route('metarial-tools-category');
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
    return view('admin.metarials-tools.category.all',compact('all','allType'));
  }

  public function edit($id){
    $edit = $this->findId($id);
    /* ==== call itemType controller ==== */
    $itemType = new ItemTypeController();
    $allType = $itemType->getAll();
    return view('admin.metarials-tools.category.edit',compact('edit','allType'));
  }


}
