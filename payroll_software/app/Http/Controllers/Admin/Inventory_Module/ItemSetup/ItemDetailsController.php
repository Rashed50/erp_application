<?php

namespace App\Http\Controllers\Admin\Inventory_Module\ItemSetup;

use Illuminate\Http\Request;
use App\Enums\InventoryItemsUnit;
use App\Http\Controllers\Controller;
use App\Http\Controllers\DataServices\InventoryItemSetupDataService;
use App\Http\Controllers\DataServices\CompanyDataService;
use App\Http\Controllers\DataServices\SupplierDataService;
use App\Http\Requests\InvItemDetailsFormRequest;
use App\Http\Requests\InventoryPurchaseRequest;
use App\Http\Requests\InventoryItemDetailsCartRequest;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Gloudemans\Shoppingcart\Facades\Cart;

class ItemDetailsController extends Controller
{
    public function index(){
        
       
        $all = (new InventoryItemSetupDataService())->getItemDetailsInvItemAllRecords();
        $allType = (new InventoryItemSetupDataService())->getInventoryItemRecordsForDropDownList();
        $allBrand = (new InventoryItemSetupDataService())->getAllBrandItemRecordsForDropDownList();
       // $allCompany = (new CompanyDataService())->getSubCompanyListForDropdow//n();
        $allCompany = (new InventoryItemSetupDataService())->getAllItemCompnayRecordsForDropDownList();

        $storeList = (new InventoryItemSetupDataService())->getAllSubStoreRecordsForDropDownList();
        $itemUnitList = [];// InventoryItemsUnit::cases();
        $purchase_by = (new InventoryItemSetupDataService())->getItemPurchaseByAllRecords();
        $purchase_from = (new SupplierDataService())->getAllActiveInventorySupplierForDropdown(Auth::user()->branch_office_id);

        (Cart::instance('itemDetailsCart'))->destroy();
        return view('admin.inventory_module.item-details.all',compact('all', 'allType', 'itemUnitList', 'allBrand', 'allCompany', 'storeList', 'purchase_by', 'purchase_from'));

    }

    public function insert(InvItemDetailsFormRequest $request){
        $insert = (new InventoryItemSetupDataService())->insertNewItemDetailsInformationsWithItemDetailsID($request->itype_id, $request->icatg_id, $request->iscatg_id, $request->item_deta_code, $request->item_brand_id, $request->model_no, $request->quantity, $request->invoice_no, $request->invoice_date, $request->recieved_date, $request->serial_no, $request->item_det_unit, $request->item_company_id, $request->store_id);

        if( $insert > 0 ){
            Session::flash('success','Successfully Added New Items Details Infos');
            return redirect()->back();
        } else{
            Session::flash('error','Opeation Failed, Please Try Again');
            return redirect()->back();
        }
    }

    public function edit($id){
        $allType = (new InventoryItemSetupDataService())->getInventoryItemRecordsForDropDownList();
        $invItems = InventoryItemsUnit::cases();
        $allBrand = (new InventoryItemSetupDataService())->getAllBrandItemRecordsForDropDownList();
        $allCompany = (new InventoryItemSetupDataService())->getAllItemCompnayRecordsForDropDownList();
        $allSubStore = (new InventoryItemSetupDataService())->getAllSubStoreRecordsForDropDownList();
        $edit = (new InventoryItemSetupDataService())->getAnItemDetailsInformationsByItemDetailsAutoId($id);
        return view('admin.inventory_module.item-details.edit', compact('allType', 'edit', 'invItems', 'allBrand', 'allCompany', 'allSubStore'));
    }

    public function update(InvItemDetailsFormRequest $request){
        $update = (new InventoryItemSetupDataService())->updateItemDetailsInformations( $request->item_deta_id, $request->itype_id, $request->icatg_id, $request->iscatg_id, $request->item_deta_code, $request->quantity, $request->model_no, $request->item_brand_id, $request->invoice_no, $request->invoice_date, $request->recieved_date, $request->serial_no, $request->item_det_unit, $request->item_company_id, $request->store_id);

        if( $update){
            Session::flash('success','Successfully Updated Items Details Infos');
            return redirect()->route('inventory-item-details-name');
        } else{
            Session::flash('error','Opeation Failed, Please Try Again');
            return redirect()->back();
        }
    }
    
    
  
    public function addToCartInventoryItemsInformation(InventoryItemDetailsCartRequest $request)
    {
        try {

            // Retrieve the necessary data from the database
             $item_info = (new InventoryItemSetupDataService())->searchAnItemNameInformationsByItemCode($request->item_deta_code);
            // Add the items to the cart

            $cart_items = (new InventoryItemSetupDataService())->addToCartInventoryItemsDetailsInformation(
                $request->itype_id,
                $item_info->itype_name,
                $request->icatg_id,
                $item_info->icatg_name,
                $request->iscatg_id,
                $item_info->iscatg_name,
                $request->item_deta_code,
                $item_info->item_id,
                $item_info->item_deta_name,
                $request->item_brand_id,
                $item_info->item_brand_name ?? '',
                $request->model_no ?? '',
                $request->quantity,
                $request->serial_no ?? ''
            );

            if ($cart_items) {
                $item_details_cart_content = Cart::instance('itemDetailsCart')->content();
                return response()->json(
                    [
                        'success' => true,
                        'cart_items' => $item_details_cart_content,
                        'message' => 'Cart Items Successfully Added',
                    ],
                    200
                );
            }

            // Return an error response if the cart items could not be added
            return response()->json(
                [
                    'success' => false,
                     'message' => 'Failed to add items to the cart',
                ],
                500
            );
        } catch (Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ],
                500
            );
        }
    }

    public function purchaseInventoryItemsWithCartsItems(InventoryPurchaseRequest $request){


        
         try {
             
             DB::beginTransaction();

            $insert_purchase_record_id = (new InventoryItemSetupDataService())->insertInventoryItemsPurchaseRecords(
                $request->store_id,
                $request->invoice_no,
                $request->invoice_date,
                $request->received_date,
                $request->purchase_by,
                $request->purchase_from,
                $request->chalan_no
            );

            $insert_cart_contents = (new InventoryItemSetupDataService())->insertInventoryPurchaseCartContentIntoItemDetails($insert_purchase_record_id);

            DB::commit();

            // Cart::instance('itemDetailsCart')->destroy();
            return response()->json([
                'status' => 200,
                'purchase_record_id' => $insert_purchase_record_id,
                'success' => true,
                'message' => 'Save successfully',
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }

    }

    public function showInventoryPurchaseInvoice($purchase_record_id){
        $inventory_purchase_record = (new InventoryItemSetupDataService())->getInventoryPurchaseRecordDetailsByPurchaseRecordId($purchase_record_id);
        $inventory_item_details = (new InventoryItemSetupDataService())->getInventoryPurchaseItemDetailsRecordByPurchaseRecordId($purchase_record_id);
        $company = (new CompanyDataService())->findCompanryProfile();

        return view('admin.inventory_module.item-details.item_purchase_invoice', compact('company', 'inventory_purchase_record', 'inventory_item_details'));
    }



    public function removeInventoryCartItem(Request $request){
        $rowId = $request->rowId;
        Cart::instance('itemDetailsCart')->remove($rowId);
        return response()->json(
            [
                'success' => true,
                'message' => 'Cart Items Removed Successfully',
            ],
            200
        );
    }


    public function loadInventoryCartItems(){
        $item_details_cart_content = Cart::instance('itemDetailsCart')->content();
        return response()->json(
            [
                'success' => true,
                'cart_items' => $item_details_cart_content,
                'message' => 'Cart Items Loaded',
            ],
            200
        );
    }
















}
