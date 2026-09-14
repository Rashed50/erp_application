<?php



namespace App\Http\Controllers\DataServices;

use App\Models\Inventory\emp_item_received_record;
use App\Models\Inventory\ItemDetails;
use App\Models\Inventory\ItemCategory;
use App\Models\Inventory\ItemSubCategory;
use App\Models\Inventory\item_name;
// use App\Models\Inventory\ItemBrand;
use App\Models\InventoryPurchaseRecord;
use App\Models\Inventory\SubStoreInfo;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InventoryItemDistributionDataService{


     public function insertEmployeeReceivedItemInformation($item_auto_id,$emp_auto_id,$received_qty,$model_no,$serial_no,$store_id,$brand_id,$item_unit,$received_date,$insert_by,$received_status){
     

        return emp_item_received_record::insertGetId([

            'emp_auto_id' => $emp_auto_id,
            'item_auto_id' => $item_auto_id,
            'approved_qty' => $received_qty,
            'received_qty' => $received_qty,
            'model_no'=>$model_no,
            'serial_no'=>$serial_no,
            'store_id' => $store_id,
            'brand_id'=>$brand_id,
            'item_unit'=>$item_unit,
            'received_date' => $received_date,
            'approved_date' => $received_date,
            'approved_by' => $insert_by,
            'insert_by' => $insert_by,
            'received_status' => $received_qty,
            'created_at' => Carbon::now(),

        ]);
    }
    
    public function getItemReceivedRecordByItemReceivedAutoId($item_received_auto_id){
        return emp_item_received_record::where('item_received_auto_id',$item_received_auto_id)->get()->first();
    }
    public function deleteReceivedItemRecord($item_received_auto_id  ){
        return emp_item_received_record::where('item_received_auto_id',$item_received_auto_id )->delete();
    }
    
    public function updateItemReceivedPaperLocation($item_received_auto_id,$received_paper,$update_by ){
        return emp_item_received_record::whereIn('item_received_auto_id',$item_received_auto_id )->update([
            'received_paper' => $received_paper,
            'update_by'=>$update_by,          

        ]);
    }
    
    public function getItemReceivedRecords($limit = 20){
        return  DB::select('call getEmployeesItemReceivedRecords1(?)',array($limit));
    }

    public function searchAnEmployeeReceivedItemInfoRecords($emp_auto_id){
        return  DB::select('call getAnEmployeeItemReceivedRecords1(?)',array($emp_auto_id));
    }
    
    public function searchItemRecivedEmployeeForUploadPaper($from_date,$to_date,$inserted_by){
        return  DB::select('call searchItemReceivedEmployeeForUploadReceivedPaper1(?,?,?)',array($inserted_by,$from_date,$to_date));
    }
    
    /*
     ==========================================================================
     ==================== Inventory Item Distribution Report ==================
     ==========================================================================
    */
    public function searchEmployeeReceivedInventoryItemReport($from_date,$to_date,$store_id){
        return  DB::select('call getEmployeeReceivedInvenItemRecordsByStoreId1(?,?,?)',array($from_date,$to_date,$store_id));
    }
    
    
        // Inventory Item Stock Report
    public function processInventoryItemRecords($store_id,$cateory_id,$sub_category_id,$from_date,$to_date){

           return ItemDetails::select('item_details.*','item_names.item_deta_name','item_names.item_deta_code','item_categories.icatg_name','item_sub_categories.iscatg_name','item_brands.item_brand_name'
           ,'inventory_purchase_records.invoice_no','inventory_purchase_records.invoice_date','inventory_purchase_records.received_date','sub_store_infos.sub_store_name')
             ->leftjoin('inventory_purchase_records', 'item_details.inv_purchase_rec_id','=','inventory_purchase_records.id')
            ->leftjoin('sub_store_infos', 'inventory_purchase_records.store_id' , '=', 'sub_store_infos.sub_store_id')
            ->leftjoin('item_names', 'item_details.item_name_auto_id' , '=', 'item_names.item_id')
            ->leftjoin('item_categories', 'item_details.icatg_id', '=', 'item_categories.icatg_id')
            ->leftjoin('item_sub_categories', 'item_details.iscatg_id', '=', 'item_sub_categories.iscatg_id')
            ->leftjoin('item_brands', 'item_details.ibrand_id', '=', 'item_brands.ibrand_id')
            ->when($store_id, function ($query, $store_id) {
                return $query->where('inventory_purchase_records.store_id', $store_id);
            })
           ->when($cateory_id, function ($query, $cateory_id) {
                return $query->where('item_details.icatg_id', $cateory_id);
            })
            ->when($sub_category_id, function ($query, $sub_category_id) {
                return $query->where('item_details.iscatg_id', $sub_category_id);
            })
            ->when($from_date, function ($query, $from_date) {
                return $query->whereDate('inventory_purchase_records.received_date', '>=', $from_date);
            })
            ->when($to_date, function ($query, $to_date) {
                return $query->whereDate('inventory_purchase_records.received_date', '<=', $to_date);
            })
           // ->groupBy("employee_infos.sponsor_id")
            ->get();
            


    }

}