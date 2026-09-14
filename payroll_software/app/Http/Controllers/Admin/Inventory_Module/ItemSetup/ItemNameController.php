<?php

namespace App\Http\Controllers\Admin\Inventory_Module\ItemSetup;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DataServices\InventoryItemSetupDataService;
use App\Http\Controllers\DataServices\CompanyDataService;
use App\Http\Requests\InvItemDetailsFormRequest;
use App\Http\Requests\InvItemNamesFormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ItemNameController extends Controller
{

    public function findSubCatgWiseItemName($id){
        $itemNames = (new InventoryItemSetupDataService())->getAllItemName($id);
        return json_encode($itemNames);
    }

    public function index(){
        $all = (new InventoryItemSetupDataService())->getInventoryItemNamesAllRecords();
        $allType = (new InventoryItemSetupDataService())->getInventoryItemRecordsForDropDownList();
        return view('admin.inventory_module.item-name.all',compact('all','allType'));
    }
    public function insert(InvItemNamesFormRequest $request){
        $insert = (new InventoryItemSetupDataService())->insertNewItemDetailsInformations($request->itype_id, $request->icatg_id, $request->iscatg_id, $request->item_deta_name);

        if( $insert > 0 ){
            Session::flash('success','Successfully Added New Item Details Name');
            return redirect()->back();
        } else{
            Session::flash('error','Operation Failed, Please Try Again');
            return redirect()->back();
        }
    }

    public function edit($item_id){
        $edit = (new InventoryItemSetupDataService())->findAnItemNames($item_id);
        $allType = (new InventoryItemSetupDataService())->getInventoryItemRecordsForDropDownList();
        $allCetgItem = (new InventoryItemSetupDataService())->getCategoryInvItemRecordsForDropDownList();
        $allSubCetgItem = (new InventoryItemSetupDataService())->getSubCategoryInvItemRecordsForDropDownList();
        return view('admin.inventory_module.item-name.edit', compact('edit', 'allType', 'allCetgItem', 'allSubCetgItem'));
    }

    public function update(InvItemNamesFormRequest $request){
        $update = (new InventoryItemSetupDataService())->updateAnItemDetailsInformations($request->item_id, $request->itype_id, $request->icatg_id, $request->iscatg_id, $request->item_deta_name, $request->item_deta_code);

        if( $update){
            Session::flash('success','Successfully Updated Item Details Informations');
            return redirect()->route('inventory-item-name');
        } else{
            Session::flash('error','Operation Failed, Please Try Again');
            return redirect()->back();
        }
    }

    public function inActiveItemDetails($id){
        $inActive = (new InventoryItemSetupDataService())->inActiveAnItemNames($id);

        /* redirect back */
        if($inActive){
            Session::flash('success','Item Details Inactivated Successfully');
            return redirect()->route('inventory-item-name');
        } else{
            Session::flash('error','value');
            return redirect()->back();
        }
    }
    // Azax Request For Item Name Details Info By Item Code For Brand Insert
    public function findItemNameDetailsByItemCodeBrandAndItemDetails(Request $request){
        $itemInfos = (new InventoryItemSetupDataService())->getAllItemNameInformationsByItemCode($request->itemCode);
        $itemBrands = (new InventoryItemSetupDataService())->getAllBrandNameForAItemByItemCodeForDropDownList($request->itemCode);

      if (count($itemInfos) > 0) {
          return json_encode([
            'success'  => true,
            'error' => null,
            'itemInfos' =>  $itemInfos,
            'item_brands' =>  $itemBrands,
            'total_item' =>  count($itemInfos),
          ]);
        }else {
            return json_encode([
              'success'  => false,
              'error' => 'error',
              'message' => 'Item Not Found',
            ]);
        }
    }
 

 // Inventory Item Name Report 
public function searchItemNameWithCodeByItemCategorySubCatAndItemName(Request $request){
      
    $records = (new InventoryItemSetupDataService())->getItemTypeCategorySubCategoryAndItemNameWithCodeByTypeCateSubAndItemName(
        $request->item_type_id,$request->category_id,$request->subcategory_id
    );
    $company = (new CompanyDataService())->findCompanryProfile();
    $report_title = ["All Item List"];
    return view('admin.report.inventory.item_list_report',compact('records','company','report_title'));
}







}
