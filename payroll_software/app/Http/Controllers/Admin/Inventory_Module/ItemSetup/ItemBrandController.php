<?php

namespace App\Http\Controllers\Admin\Inventory_Module\ItemSetup;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DataServices\InventoryItemSetupDataService;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

class ItemBrandController extends Controller
{
    /*
     ==========================================================================
     ============================= Inventory Item Company =====================
     ==========================================================================
    */
    
    public function insertNewItemCompanyName(Request $request){
        $insert = (new InventoryItemSetupDataService())->insertNewItemCompanyName($request->item_comp_name);

        if( $insert){
            Session::flash('success','Successfully Added New Company Name');
            return redirect()->back();
        } else{
            Session::flash('error','Opeation Failed, Please Try Again');
            return redirect()->back();
        }
    }


    /*
     ==========================================================================
     ============================= Inventory Item Brand =====================
     ==========================================================================
    */
    public function findSubCatgWiseItemBrandName($id){
        $brandName = (new InventoryItemSetupDataService())->getAllBrandName($id);
        return json_encode($brandName);
    }
    public function insert(Request $request){
        $insert = (new InventoryItemSetupDataService())->insertNewBrandNameInformations($request->item_idB, $request->brand_name);

        if( $insert){
            Session::flash('success','Successfully Added New Brand Name');
            return redirect()->back();
        } else{
            Session::flash('error','Opeation Failed, Please Try Again');
            return redirect()->back();
        }
    }
}
