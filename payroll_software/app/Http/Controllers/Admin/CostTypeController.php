<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CostType;
use Carbon\Carbon;
use Session;

class CostTypeController extends Controller{
  /*
  |==================================================================
  | DATABSE OPEREATION
  |==================================================================
  */
  public function getCostType(){
    return $all = CostType::orderBy('cost_type_id','DESC')->get();
  }

  public function findCostType($id){
    return $edit = CostType::where('cost_type_id',$id)->first();
  }

  public function insert(Request $request){
    $this->validate($request,[
      'cost_type_name' => 'required'
    ],[

    ]);

    $insert = CostType::insert([
      'cost_type_name' => $request->cost_type_name,
      'created_at' => Carbon::now(),
    ]);

    if($insert){
      Session::flash('success','value');
      return Redirect()->back();
    }else{
      Session::flash('error','value');
      return Redirect()->back();
    }

  }

  public function update(Request $request){
    $this->validate($request,[
      'cost_type_name' => 'required'
    ],[

    ]);
    $id = $request->id;
    $update = CostType::where('cost_type_id',$id)->update([
      'cost_type_name' => $request->cost_type_name,
      'updated_at' => Carbon::now(),
    ]);

    if($update){
      Session::flash('success_update','value');
      return Redirect()->route('cost-type');
    }else{
      Session::flash('error','value');
      return Redirect()->back();
    }

  }

  /*
  |==================================================================
  | BLADE OPEREATION
  |==================================================================
  */
  public function create(){
    $all = $this->getCostType();
    return view('admin.cost-type.create',compact('all'));
  }

  public function edit($id){
    $edit = $this->findCostType($id);
    return view('admin.cost-type.edit',compact('edit'));
  }







  /*
  |==================================================================
  | API OPEREATION
  |==================================================================
  */



  /* ===============================================================*/
}
