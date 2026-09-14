<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DataServices\InventoryItemSetupDataService;
use App\Http\Controllers\DataServices\InventoryItemDistributionDataService;
use App\Http\Controllers\DataServices\CompanyDataService;
use Illuminate\Http\Request;
use App\Models\ItemPurchase;
use App\Models\ItemSubCategory;
use App\Models\PurchaseRecord;

class InventoryReportController extends Controller
{
  public function index()
  {
    
    $sub_store = (new InventoryItemSetupDataService())->getAllSubStoreRecordsForDropDownList();
    $item_category = (new InventoryItemSetupDataService())->getCategoryItemsForDropDownList();
    
    return view('admin.report.inventory.index', compact('sub_store','item_category'));
  }

  public function showEmployeeInventoryItemReceivedReport(Request $request){
     
    $records = (new InventoryItemDistributionDataService())->searchEmployeeReceivedInventoryItemReport($request->start_date,$request->end_date,$request->sub_store_id);
    
    $company = (new CompanyDataService())->findCompanryProfile();
    return view('admin.report.inventory.emp_item_received_report', compact('records', 'company'));

  }
  
    public function showInventoryItemStockReport(Request $request){

  
        $records = (new InventoryItemDistributionDataService())->processInventoryItemRecords($request->sub_store_id,$request->icatg_id,$request->iscatg_id,$request->start_date,$request->end_date);
      //  dd($records);
        $company = (new CompanyDataService())->findCompanryProfile();
        return view('admin.report.inventory.item_purchase_report', compact('records', 'company'));
  }

   
  
}
