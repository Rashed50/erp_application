<?php

namespace App\Http\Controllers\Admin\Inventory_Module\ItemSetup;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DataServices\InventoryItemSetupDataService;
use App\Http\Requests\InvItemSubCategoryFormRequest;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class ItemSubCategoryController extends Controller{
  /*
  |--------------------------------------------------------------------------
  |  DATABASE OPERATION
  |--------------------------------------------------------------------------
  */
  public function findCategory($id){
    $category = (new InventoryItemSetupDataService())->getCategoryItemsByItemTypeId($id);
    return json_encode($category);
  }
  public function findSubCategory($id){
    $subcategory = (new InventoryItemSetupDataService())->getAllSubCategoryInfo($id);
    return json_encode($subcategory);
  }

  public function insert(InvItemSubCategoryFormRequest $request){

    $created_by = Auth::user()->id;
    $insert = (new InventoryItemSetupDataService())->insertNewSubCategoryItemDetails($request->itype_id,$request->icatg_id,$request->iscatg_name,'',$created_by);


    if($insert >0 ){
      Session::flash('success','Subcategory Name Successfully Added');
      return redirect()->back();
    } else{
      Session::flash('error','Operation Failed, Please try Again');
      return redirect()->back();
    }
  }

  public function update(InvItemSubCategoryFormRequest $request){
    $update = (new InventoryItemSetupDataService())->updateSubCategoryItemDetails($request->id, $request->itype_id, $request->icatg_id, $request->iscatg_name, $request->iscatg_code);

    /* redirect back */
    if($update){
      Session::flash('success','Subcategory Name Successfully Updated');
      return redirect()->route('inventory-sub-category');
    } else{
      Session::flash('error','value');
      return redirect()->back();
    }
  }
  public function subCategItemInActive($iscatg_id){
    $inActive = (new InventoryItemSetupDataService())->inActiveAnSubCategoryItem($iscatg_id);

    /* redirect back */
    if($inActive){
        Session::flash('success','Subcategory Item Inactivated Successfully');
        return redirect()->route('inventory-sub-category');
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
    $all = (new InventoryItemSetupDataService())->getSubCategoryInvItemAllRecords();
    $allType = (new InventoryItemSetupDataService())->getInventoryItemRecordsForDropDownList();
    return view('admin.inventory_module.subcategory.all',compact('all','allType'));
  }

  public function edit($id){
    $edit = (new InventoryItemSetupDataService())->getAnSubCategoryItem($id);
    $allType = (new InventoryItemSetupDataService())->getInventoryItemRecordsForDropDownList();
    return view('admin.inventory_module.subcategory.edit',compact('edit','allType'));
  }









}
