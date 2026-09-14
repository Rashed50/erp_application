<?php

namespace App\Http\Controllers\DataServices;

use Carbon\Carbon;
use App\Models\InventoryStock;
use App\Models\SubCompanyInfo;
use App\Enums\InventoryItemsUnit;
use App\Models\Inventory\ItemType;
use Illuminate\Support\Facades\DB;
use App\Models\Inventory\item_name;
use App\Models\Inventory\ItemBrand;
use Illuminate\Support\Facades\Auth;
use App\Models\Inventory\ItemCompany;
use App\Models\Inventory\ItemDetails;
use App\Models\Inventory\ItemCategory;
use App\Models\Inventory\SubStoreInfo;
use App\Models\InventoryPurchaseRecord;
use App\Models\Inventory\ItemSubCategory;
use App\Models\ItemPurchaseBy;
use Gloudemans\Shoppingcart\Facades\Cart;

class InventoryItemSetupDataService
{

    /*
     ==========================================================================
     ============================= Inventory Item Section =====================
     ==========================================================================
    */

    public function getInventoryItemRecordsForDropDownList()
    {
        return ItemType::select('itype_id', 'itype_name')->get();
    }

    public function getInventoryItemAllRecords()
    {
        return ItemType::get();
    }

    /*
     ==========================================================================
     ==================== Inventory Item Category Section =====================
     ==========================================================================
    */

    public function getCategoryItemsForDropDownList()
    {
        return ItemCategory::orderBy('icatg_name', 'ASC')->get();
    }

    // Ajax Calling Data
    public function getCategoryItemsByItemTypeId($itypeId)
    {
        return ItemCategory::where('itype_id', $itypeId)->orderBy('icatg_name', 'ASC')->get();
    }

    public function countCategoryRecords()
    {
        $nore = ItemCategory::count();
        return $nore > 0 ? (100 * 1) + ($nore + 1) : 101;
    }
    public function getCategoryInventoryItemAllRecords()
    {
        return ItemCategory::where('status', 1)->get();
    }
    public function getAllInvCategoryItem()
    {
        return $all = ItemCategory::get();
    }
    public function getCategoryInvItemRecordsForDropDownList()
    {
        return ItemCategory::select('icatg_id', 'icatg_name')->where('status', 1)->get();
    }
    public function findACategoryItemByCategoryId($id)
    {
        return ItemCategory::where('icatg_id', $id)->first();
    }
    public function findAllCategoryItemByItypeTypeId($id)
    {
        $category = ItemCategory::where('itype_id', $id)->where('status', 1)->orderBy('icatg_name', 'ASC')->get();
        return json_encode($category);
    }

    public function inActiveAnCategoryItem($icatg_id)
    {
        return ItemCategory::where('icatg_id', $icatg_id)->update([
            'status' => 0,
        ]);
    }
    public function insertNewItemCategoryInformation($item_type_id, $catetory_name, $insert_by_id)
    {
        $code = $this->countCategoryRecords();
        return ItemCategory::insertGetId([
            'itype_id' => $item_type_id,
            'icatg_name' => $catetory_name,
            'icatg_code' => $code,
            'create_by_id' => $insert_by_id,
            'created_at' => Carbon::now(),
        ]);
    }
    public function updateItemCategoryInformations($icatg_id, $itype_id, $icatg_name, $icatg_code, $entered)
    {
        return ItemCategory::where('icatg_id', $icatg_id)->update([
            'itype_id' => $itype_id,
            'icatg_name' => $icatg_name,
            'icatg_code' => $icatg_code,
            'update_by_id' => $entered,
            'updated_at' => Carbon::now(),
        ]);
    }


    /*
     ==========================================================================
     ==================== Inventory Item Subcategory Section =====================
     ==========================================================================
    */

    public function countSubCategoryRecords()
    {
        $nore = ItemSubCategory::count();
        return $nore > 0 ? (100 * 1) + ($nore + 1) : 101;
    }
    public function getSubCategoryInvItemRecordsForDropDownList()
    {
        return $all = ItemSubCategory::select('iscatg_id', 'iscatg_name')->where('status', 1)->get();
    }
    public function getSubCategoryInvItemAllRecords()
    {
        return $all = ItemSubCategory::where('status', 1)->get();
    }
    public function getAnSubCategoryItem($id)
    {
        return $find = ItemSubCategory::where('iscatg_id', $id)->first();
    }
    public function inActiveAnSubCategoryItem($iscatg_id)
    {
        return ItemSubCategory::where('iscatg_id', $iscatg_id)->update([
            'status' => 0,
        ]);
    }
    public function getAllSubCategoryInfo($id)
    {
        return ItemSubCategory::where('icatg_id', $id)->orderBy('iscatg_name', 'ASC')->get();
    }

    public function insertNewSubCategoryItemDetails($type_id, $category_id, $subcategory_name, $subcategory_code, $created_by)
    {
        $subcategory_code = $this->countSubCategoryRecords();
        return ItemSubCategory::insertGetId([
            'itype_id' => $type_id,
            'icatg_id' => $category_id,
            'iscatg_name' => $subcategory_name,
            'iscatg_code' => $subcategory_code,
            'create_by_id' => $created_by,
            'created_at' => Carbon::now(),
        ]);
    }
    public function updateSubCategoryItemDetails($iscatg_id, $itype_id, $icatg_id, $iscatg_name, $iscatg_code)
    {
        $updated = Auth::user()->id;
        return ItemSubCategory::where('iscatg_id', $iscatg_id)->update([
            'itype_id' => $itype_id,
            'icatg_id' => $icatg_id,
            'iscatg_name' => $iscatg_name,
            'iscatg_code' => $iscatg_code,
            'update_by_id' => $updated,
            'updated_at' => Carbon::now(),
        ]);
    }

    /*
     ==========================================================================
     ==================== Inventory Item Name Section =====================
     ==========================================================================
    */

    public function getAllItemName($id)
    {
        return item_name::where('iscatg_id', $id)->orderBy('item_id', 'ASC')->get();
    }

    public function countItemNamesRecords()
    {
        $nore = item_name::count();
        return $nore > 0 ? (1000 * 1) + ($nore + 1) : 1001;
    }
    public function getInventoryItemNamesAllRecords()
    {
        return $all = item_name::where('item_deta_status', 1)->get();
    }
    public function getInventoryItemNamesAllRecordsForDropDownMenu()
    {
        return  item_name::select('item_id','item_deta_name','item_deta_code')->where('item_deta_status', 1)->get();
    }
    public function findAnItemNames($item_id)
    {
        return item_name::where('item_id', $item_id)->first();
    }
    public function inActiveAnItemNames($item_id)
    {
        return item_name::where('item_id', $item_id)->update([
            'item_deta_status' => 0,
        ]);
    }
    // Ajax Calling All Item Information By Item Code
    // public function getAllItemNameInformationByItemCode($itemCode)
    // {
    //     return item_name::leftjoin('item_types', 'item_names.itype_id', '=', 'item_types.itype_id')
    //         ->leftjoin('item_categories', 'item_names.icatg_id', '=', 'item_categories.icatg_id')
    //         ->leftjoin('item_sub_categories', 'item_names.iscatg_id', '=', 'item_sub_categories.iscatg_id')
    //         ->where('item_deta_code', $itemCode)->get();
    // }

    public function getAllItemNameInformationsByItemCode($itemCode){
        return item_name::
                       leftjoin('item_types', 'item_names.itype_id', '=', 'item_types.itype_id')
                       ->leftjoin('item_categories', 'item_names.icatg_id', '=', 'item_categories.icatg_id')
                       ->leftjoin('item_sub_categories', 'item_names.iscatg_id', '=', 'item_sub_categories.iscatg_id')
                       ->where('item_deta_code', $itemCode)->get();

    }

    public function searchAnItemNameInformationsByItemCode($itemCode)
    {
        return item_name::leftjoin('item_types', 'item_names.itype_id', '=', 'item_types.itype_id')
            ->leftjoin('item_categories', 'item_names.icatg_id', '=', 'item_categories.icatg_id')
            ->leftjoin('item_sub_categories', 'item_names.iscatg_id', '=', 'item_sub_categories.iscatg_id')
            ->where('item_deta_code', $itemCode)->first();
    }

    public function insertNewItemDetailsInformations($itype_id, $icatg_id, $iscatg_id, $item_deta_name)
    {
        $itemDetails_code = $this->countItemNamesRecords();
        $create_by = Auth::user()->id;

        return item_name::insertGetId([
            'itype_id' => $itype_id,
            'icatg_id' => $icatg_id,
            'iscatg_id' => $iscatg_id,
            'item_deta_name' => $item_deta_name,
            'item_deta_code' => $itemDetails_code,
            'create_by_id' => $create_by,
            'created_at' => Carbon::now(),
        ]);
    }
    public function updateAnItemDetailsInformations($item_id, $itype_id, $icatg_id, $iscatg_id, $item_deta_name, $item_deta_code)
    {
        $update_by = Auth::user()->id;

        return item_name::where('item_id', $item_id)->update([
            'itype_id' => $itype_id,
            'icatg_id' => $icatg_id,
            'iscatg_id' => $iscatg_id,
            'item_deta_name' => $item_deta_name,
            'item_deta_code' => $item_deta_code,
            'update_by_id' => $update_by,
            'updated_at' => Carbon::now(),
        ]);
    }


    public function getItemTypeCategorySubCategoryAndItemNameWithCodeByTypeCateSubAndItemName($item_type_id, $cat_id, $sub_cat_id)
    {
        return  DB::select('CALL getItemNameListReportByTypeCateSubCatId(?,?,?)', array($item_type_id, $cat_id, $sub_cat_id));
    }


    /*
     ==========================================================================
     ==================== Inventory Item Company Section =====================
     ==========================================================================
    */
    public function getAllItemCompnayRecordsForDropDownList()
    {
        return ItemCompany::select('item_comp_id', 'item_comp_name')->where('item_comp_status', 1)->orderBy('item_comp_id', 'ASC')->get();
    }
    public function checkThisItemCompanyIsExist($itemCompany)
    {
        $companyName = ItemCompany::where('item_comp_name', $itemCompany)->first();

        if ($companyName == null) {
            return false;
        } else {
            return true;
        }
    }
    public function insertNewItemCompanyName($itemCompany)
    {
        $create_by = Auth::user()->id;
        if ($this->checkThisItemCompanyIsExist($itemCompany) != null) {
            return 0;
        } else {
            return ItemCompany::insertGetID([
                'item_comp_name' => $itemCompany,
                'create_by_id' => $create_by,
                'created_at' => Carbon::now(),
            ]);
        }
    }




    /*
     ==========================================================================
     ==================== Inventory Item Brand Section =====================
     ==========================================================================
    */
    public function getAllBrandItemRecordsForDropDownList()
    {
        return ItemBrand::get(); //select('ibrand_id', 'item_brand_name', 'item_brand_code')->where('item_brand_status', 1)->orderBy('ibrand_id', 'ASC')->get();
    }
    
      public function getAllBrandNameForAItemByItemCodeForDropDownList($item_code)
    {
        return ItemBrand::select('ibrand_id', 'item_brand_name')->where('item_code',$item_code)
        ->where('item_brand_status', 1)->orderBy('item_brand_name', 'ASC')->get();
    }

    public function getAllBrandName($iscatg_id)
    {
        return ItemBrand::where('iscatg_id', $iscatg_id)->orderBy('ibrand_id', 'ASC')->get();
    }

    public function countItemBrandRecords()
    {
        $nore = ItemBrand::count();
        return $nore > 0 ? (100 * 1) + ($nore + 1) : 101;
    }
    //  $itemBrand_code = $this->countItemBrandRecords();
    public function checkThisBrandIsExist($item_idB, $brand_name)
    {
        $brandName = ItemBrand::where('item_brand_name', $brand_name)
            ->where('item_id', $item_idB)
            ->first();

        if ($brandName != null) {
            return true;
        } else {
            return false;
        }
    }
    public function insertNewBrandNameInformations($item_code, $brand_name)
    {
        //  $itemBrand_code = $this->countItemBrandRecords();
        $create_by = Auth::user()->id;

        // if ($this->checkThisBrandIsExist($item_idB, $brand_name) != null) {
        //     return 0;
        // } else {
        return ItemBrand::insert([
            'item_code' => $item_code,
            'item_brand_name' => $brand_name,
            'create_by_id' => $create_by,
            'created_at' => Carbon::now(),
        ]);
        // }
    }

    /*
     ==========================================================================
     ==================== Inventory Item Details Section =====================
     ==========================================================================
    */

    public function getItemDetailsInvItemAllRecords()
    {
        return   ItemDetails::with('itemType')->with('itemCatg')->with('itemSubCatg')->with('itemNameCode')->with('itemBrandName')->orderBy('item_deta_id', 'DESC')->get();
    }

    public function getAnItemDetailsInformationsByItemDetailsAutoId($item_deta_id)
    {
        return ItemDetails::where('item_deta_id', $item_deta_id)->first();
    }

  

    public function addToCartInventoryItemsDetailsInformation($itype_id, $itype_name, $icategory_id,$icategory_name,
     $itsub_category_id, $itsub_category_name,$item_detail_code,$item_name_auto_id, $item_detail_name, $item_brand_id, $item_brand_name,
       $model_no, $quantity, $serial_no)
    {

        return $add = Cart::instance('itemDetailsCart')->add([
            'id' => $itype_id,
            'name' => $itype_name,
            'qty' => $quantity ?? 1,
            'price' => 0,
            'weight' => 1,
            'options' => [
                'item_category_id' => $icategory_id,
                'item_category' => $icategory_name,
                'item_sub_category_id' => $itsub_category_id,
                'item_sub_category' => $itsub_category_name,
                'item_detail_name_code' => $item_detail_code,
                'item_name_auto_id' => $item_name_auto_id,
                'item_detail_name' => $item_detail_name,
                'item_brand_id' => $item_brand_id,
                'item_brand' => $item_brand_name,
                'model_no' => $model_no ?? "",
                'serial_no' => $serial_no ?? "",

            ],
        ]);
    }



    public function insertInventoryPurchaseCartContentIntoItemDetails($purchase_record_id)
    {

        foreach (Cart::instance('itemDetailsCart')->content() as $cart) {
           //  try {
                $item_unit_name = 1;//  InventoryItemsUnit::getValueByName($cart->options->unit);

                $insertedId = ItemDetails::insertGetID([
                    'itype_id' => $cart->id,
                    'quantity' => $cart->qty,
                    'icatg_id' => $cart->options->item_category_id,
                    'iscatg_id' => $cart->options->item_sub_category_id,
                    'item_name_auto_id' => $cart->options->item_name_auto_id,
                // 'item_comp_id' =>  $item_sub_company_name->sb_comp_id,
                    'ibrand_id' => $cart->options->item_brand_id,
                    'inv_purchase_rec_id' => $purchase_record_id,
                    'model_no' => $cart->options->model_no,
                    'serial_no' => $cart->options->serial_no,
                    'item_det_unit' => $item_unit_name,
                    'create_by_id' => Auth::user()->id,
                ]);
            // } catch (\Exception $e) {
            //     $insertResults[] = [
            //         'status' => false,
            //         'error' => $e->getMessage(),
            //     ];
            // }
        }

        // Return the results of the insert operations
        return true;
    }


    public function getInventoryPurchaseRecordDetailsByPurchaseRecordId($purchase_record_id){
        return InventoryPurchaseRecord::where('id', $purchase_record_id)->first();
    }



    public function getInventoryPurchaseItemDetailsRecordByPurchaseRecordId($purchase_record_id){
        return ItemDetails::where('inv_purchase_rec_id', $purchase_record_id)->get();
    }



    public function insertInventoryItemsPurchaseRecords($store_id, $invoice_no, $invoice_date, $received_date, $purchase_by, $purchase_from, $chalan_no)
    {
        return InventoryPurchaseRecord::insertGetId([
            'store_id' => $store_id,
            'invoice_no' => $invoice_no,
            'invoice_date' => $invoice_date,
            'received_date' => $received_date,
            'purchase_by' => $purchase_by,
            'purchase_from' => $purchase_from ?? '',
            'chalan_no' => $chalan_no ?? '',
            'create_by_id' => Auth::user()->id,
            'created_at' => Carbon::now()
        ]);
    }



    /*
     ==========================================================================
     ==================== Inventory Item Stock Section =====================
     ==========================================================================
    */
    public function insertInventoryItemsStockInformations($item_deta_id, $inv_prev_stock, $inv_current_stock, $inv_year, $inv_date, $inv_start_date, $inv_end_date)
    {
        $create_by = Auth::user()->id;
        return InventoryStock::insert([
            'item_deta_id' => $item_deta_id,
            'inv_prev_stock' => $inv_prev_stock,
            'inv_current_stock' => $inv_current_stock,
            'inv_year' => $inv_year,
            'inv_date' => $inv_date,
            'inv_start_date' => $inv_start_date,
            'inv_end_date' => $inv_end_date,
            'create_by_id' => $create_by,
            'created_at' => Carbon::now(),
        ]);
    }


    /*
     ==========================================================================
     ==================== Inventory Item Sub Stock Section =====================
     ==========================================================================
    */
    public function getAllSubStoreRecordsForDropDownList()
    {
        return SubStoreInfo::select('sub_store_id', 'sub_store_name')->where('sub_store_status', 1)->get();
    }
    public function getAllInventorySubStoreInfos()
    {
        return SubStoreInfo::with('user')->orderBy('sub_store_id', 'DESC')->get();
    }
    public function countSubStoreRecords()
    {
        $nore = SubStoreInfo::count();
        return $nore > 0 ? (1000 * 1) + ($nore + 1) : 1001;
    }

    public function checkThisSubStoreNameIsExist($storeName)
    {
        $storeName = SubStoreInfo::where('sub_store_name', $storeName)->first();

        if ($storeName != null) {
            return true;
        } else {
            return false;
        }
    }

    public function insertAnInventorySubStoreInfos($storeName)
    {
        $subStoreCode = $this->countSubStoreRecords();
        $create_by = Auth::user()->id;
        if ($this->checkThisSubStoreNameIsExist($storeName)) {
            return 0;
        } else {
            return SubStoreInfo::insertGetID([
                'sub_store_name' => $storeName,
                'sub_store_code' => $subStoreCode,
                'create_by_id' => $create_by,
                'created_at' => Carbon::now(),
            ]);
        }
    }

    public function updateAnInventorySubStoreInfos($sub_store_id, $sub_store_code, $sub_store_name)
    {
        $update_by = Auth::user()->id;
        if ($this->checkThisSubStoreNameIsExist($sub_store_name)) {
            return 0;
        } else {
            return SubStoreInfo::where('sub_store_id', $sub_store_id)->update([
                'sub_store_name' => $sub_store_name,
                'sub_store_code' => $sub_store_code,
                'update_by_id' => $update_by,
                'updated_at' => Carbon::now(),
            ]);
        }
    }




    // Some demo data inserted now into database, those are fetched by this method
    public function getItemPurchaseByAllRecords(){
        return ItemPurchaseBy::select('id', 'name')->get();
    }




}
