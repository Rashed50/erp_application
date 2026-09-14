<?php

namespace App\Http\Controllers\Admin\Inventory_Module\ItemSetup;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DataServices\InventoryItemSetupDataService;
use App\Http\Requests\InvItemCategoryFormRequest;
use Illuminate\Support\Facades\Session;
use Auth;

class ItemCategoryController extends Controller{
  /*
  |--------------------------------------------------------------------------
  |  DATABASE OPERATION
  |--------------------------------------------------------------------------
  */
  public function insert(InvItemCategoryFormRequest $request)
  {
     $insert_by = Auth::user()->id;
     $insert = (new InventoryItemSetupDataService())->insertNewItemCategoryInformation($request->itype_id,$request->icatg_name,$insert_by);

    if( $insert > 0 ){
      Session::flash('success','Successfully Added New Category Name');
      return redirect()->back();
    } else{
      Session::flash('error','Opeation Failed, Please Try Again');
      return redirect()->back();
    }
  }

  public function update(InvItemCategoryFormRequest $request){
    $entered = Auth::user()->id;
    $update = (new InventoryItemSetupDataService())->updateItemCategoryInformations($request->id, $request->itype_id, $request->icatg_name, $request->icatg_code, $entered);
    /* redirect back */
    if($update){
      Session::flash('success','Successfully Updated New Category Name');
      return redirect()->route('inventory-category');
    } else{
      Session::flash('error','value');
      return redirect()->back();
    }
  }

  public function categItemInActive($id){
    $inActive = (new InventoryItemSetupDataService())->inActiveAnCategoryItem($id);

    /* redirect back */
    if($inActive){
        Session::flash('success','Category Item Inactivated Successfully');
        return redirect()->route('inventory-category');
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
    $all = (new InventoryItemSetupDataService())->getAllInvCategoryItem();;
    $allType = (new InventoryItemSetupDataService())->getInventoryItemRecordsForDropDownList();
    return view('admin.inventory_module.category.all',compact('all','allType'));
  }

  public function edit($id){
    $edit = (new InventoryItemSetupDataService())->findACategoryItemByCategoryId($id);
    $allType =  (new InventoryItemSetupDataService())->getInventoryItemRecordsForDropDownList();
    return view('admin.inventory_module.category.edit',compact('edit','allType'));
  }


}
