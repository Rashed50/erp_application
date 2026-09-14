<?php

namespace App\Http\Controllers\Admin\Inventory_Module;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DataServices\InventoryItemSetupDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class InventoryStockController extends Controller
{
    public function insert(Request $request){
        $insert = (new InventoryItemSetupDataService())->insertInventoryItemsStockInformations(
            $request->item_deta_id,
            $request->inv_prev_stock,
            $request->inv_current_stock,
            $request->inv_year,
            $request->inv_date,
            $request->inv_start_date,
            $request->inv_end_date,
        );

        if( $insert){
            Session::flash('success','Successfully Added New Inventory Stock Infos');
            return redirect()->back();
        } else{
            Session::flash('error','Opeation Failed, Please Try Again');
            return redirect()->back();
        }
    }
}
