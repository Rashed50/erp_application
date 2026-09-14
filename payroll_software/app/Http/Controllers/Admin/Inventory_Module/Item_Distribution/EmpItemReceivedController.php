<?php

namespace App\Http\Controllers\Admin\Inventory_Module\Item_Distribution;

use Illuminate\Http\Request;
use App\Http\Requests\EmpItemRecievedFormRequest;
use App\Http\Controllers\Controller;
use App\Http\Controllers\DataServices\InventoryItemSetupDataService;
use App\Http\Controllers\DataServices\InventoryItemDistributionDataService;
use App\Http\Controllers\DataServices\CompanyDataService;
use App\Http\Controllers\DataServices\EmployeeDataService;
use App\Http\Controllers\Admin\Helper\UploadDownloadController;
use App\Enums\InventoryItemsUnit;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Exception;

class EmpItemReceivedController extends Controller
{
     
      function __construct(){
        $this->middleware('permission:inventory_item_distribution',['only'=>['index','storeEmployeeItemReceivedInformation','searchEmployeeReceivedItemDetails','searchItemReceivedEmployeeForUploadPaper','uploadEmployeeItemReceivedPaper']]);
        $this->middleware('permission:emp_received_item_record_delete',['only'=>['deleteEmployeeReceivedItemRecord']]);
       
    }
     
     
    public function index(){
 
        $allType = (new InventoryItemSetupDataService())->getInventoryItemRecordsForDropDownList();
        $allBrand = (new InventoryItemSetupDataService())->getAllBrandItemRecordsForDropDownList();
        $storeList = (new InventoryItemSetupDataService())->getAllSubStoreRecordsForDropDownList();
        $itemUnitList = [] ;// InventoryItemsUnit::cases();

        return view('admin.inventory_module.item_distribution.create',compact( 'allType', 'itemUnitList', 'allBrand', 'storeList'));
    }

    public function storeEmployeeItemReceivedInformation(EmpItemRecievedFormRequest $request){
    
        try{
            
             $employee = (new EmployeeDataService())->getAnEmployeeInfoWithSalaryDetailsByEmpId($request->emp_id);
            if($employee == null){
                Session::flash('error','This Employee Not Found ');
                return redirect()->back(); 
            }
    
           $insertedId =  (new InventoryItemDistributionDataService())->insertEmployeeReceivedItemInformation($request->item_code_auto_id,$employee->emp_auto_id,$request->quantity,
             $request->model_no,$request->serial_no,$request->store_id,$request->item_brand_id,$request->item_det_unit,$request->recieved_date,
           Auth::user()->id,1);
           if($insertedId){
            Session::flash('success','Successfully Save Information');
           }else {
            Session::flash('error','Operation Failed');
           }
           return redirect()->back(); 
        }
        catch(Exception $ex){
             Session::flash('error','Operation Failed');
            return redirect()->back(); 
        }

    }
    
    
    public function deleteEmployeeReceivedItemRecord($item_received_auto_id){

        try{
          
           // $record = (new InventoryItemDistributionDataService())->getItemReceivedRecordByAutoId($item_received_auto_id);
            $isDelete = (new InventoryItemDistributionDataService())->deleteReceivedItemRecord($item_received_auto_id);
 
            if ($isDelete) {
                return json_encode(['success' => true, 'status' => 200, 'message' => 'Successfully Deleted']);
            } else {
                return json_encode(['success' => false, 'status' => 404, 'error' => 'error','message' => 'Delete Operation Failed']);
            }
        }
        catch(Exception $ex){
            return json_encode(['success' => false, 'status' => 505, 'error' => 'error','message' =>'System Exception Occured']);
        }
 
    }

    public function searchEmployeeReceivedItemDetails(Request $request){
          try{
          
            $employee = (new EmployeeDataService())->getAnEmployeeInfoWithSalaryDetailsByEmpId($request->emp_id_or_iqama);
            if($employee == null){
                return json_encode(['success' => false, 'status' => 404, 'error' => 'Employee Not Found By This ID ']);
            }
            $records = (new InventoryItemDistributionDataService())->searchAnEmployeeReceivedItemInfoRecords($employee->emp_auto_id);
            if ($records) {
                return json_encode(['success' => true, 'status' => 200, 'data' => $records]);
            } else {
                return json_encode(['success' => false, 'status' => 404, 'error' => 'Record Not Found']);
            }
        }
        catch(Exception $ex){
            return json_encode(['success' => false, 'status' => 505, 'error' => 'System Exception Occured']);
        }
    }
    
        public function searchItemReceivedEmployeeForUploadPaper(Request $request){

        try{          
             
            $records = (new InventoryItemDistributionDataService())->searchItemRecivedEmployeeForUploadPaper($request->from_date,$request->to_date,Auth::user()->id);
            if ($records) {
                return json_encode(['success' => true, 'status' => 200, 'data' => $records]);
            } else {
                return json_encode(['success' => false, 'status' => 404, 'error' => 'Record Not Found']);
            }
         }
         catch(Exception $ex){
             return json_encode(['success' => false, 'status' => 505, 'error' => 'System Exception Occured','exception'=>$ex]);
         }
       
        
    }

    public function uploadEmployeeItemReceivedPaper(Request $request)
    {
        
        try{ 

             $file_path = null;          
             if ($request->hasFile('upload_paper')) {
                 $file = $request->file('upload_paper');
                 $file_path = (new UploadDownloadController())->uploadItemReceivedPaper($file,null);               
             } 

             if($file_path){
                        
                $isSuccess = false;
                $counter = 0;
                $item_rec_auto_id_select_list = array();
                foreach ($request->item_rec_auto_id_list as $irec_auto_id) {             
                 
                    if ($request->has('paper_checkbox-' . $irec_auto_id)) {            
                    $item_rec_auto_id_select_list[$counter++] = $irec_auto_id;                    
                  }            
                }
                $isSuccess =  (new InventoryItemDistributionDataService())->updateItemReceivedPaperLocation($item_rec_auto_id_select_list,$file_path,Auth::user()->id);
                if ($isSuccess) {
                    Session::flash('success', 'Successfully Uploaded');
                    return redirect()->back();
                } else {
                    Session::flash('error', 'Operation Failed, Please Try Again');
                    return redirect()->back();
                }
                
            }else {
                Session::flash('error', 'Operation Failed, Please Try Again');
                return redirect()->back();
            }
        }catch(Exception $ex){
            Session::flash('error', $ex);
            return redirect()->back();
        }
    }
    
}
