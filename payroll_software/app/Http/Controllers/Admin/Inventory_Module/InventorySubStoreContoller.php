<?php

namespace App\Http\Controllers\Admin\Inventory_Module;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DataServices\InventoryItemSetupDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class InventorySubStoreContoller extends Controller
{
    public function index(){
        $all = (new InventoryItemSetupDataService())->getAllInventorySubStoreInfos();
        return view('admin.inventory_module.Store.all', compact('all'));
    }

    public function insert(Request $request){
        $insert = (new InventoryItemSetupDataService())->insertAnInventorySubStoreInfos($request->sub_store_name);

        if( $insert){
            Session::flash('success','Successfully Added New Inventory Sub Store Infos');
            return redirect()->back();
        } else{
            Session::flash('error','Opeation Failed, Please Try Again');
            return redirect()->back();
        }
    }

    public function update(Request $request){
        $update = (new InventoryItemSetupDataService())->updateAnInventorySubStoreInfos($request->sub_store_id, $request->sub_store_code, $request->sub_store_name);

        if( $update){
            Session::flash('success','Successfully Updated New Inventory Sub Store Infos');
            return redirect()->route('inventory-sub-store-infos');
        } else{
            Session::flash('error','Operation Failed, Please Try Again');
            return redirect()->back();
        }
    }

}
