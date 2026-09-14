<?php

namespace App\Http\Controllers\Admin\CateringService;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\DataServices\CompanyDataService;
use App\Http\Controllers\DataServices\CateringDataService;
use App\Http\Controllers\DataServices\EmployeeDataService;
use App\Http\Controllers\DataServices\SalaryProcessDataService;
use App\Http\Controllers\DataServices\ProjectDataService;
use App\Imports\ImportMonthlyCateringRecord;
use App\Http\Controllers\Admin\Helper\HelperController;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class EmpMonthlyCateringController extends Controller
{
    public function loadMonthlyCateringServiceUI(){
        
        $projects = (new ProjectDataService())->getAllActiveProjectListForDropdown();
        return view('admin.catering_service.emp_monthly_catering_service_ui',compact('projects'));
     
    }

    public function storeMonthlyCateringServiceRecord(Request $request){

         try
        {    
            if($request->emcr_auto_id){
                // update existng  record
                $records = (new CateringDataService())->searchingAnEmployeeCateringMonthRecordByAutoId($request->emcr_auto_id);
                
                 if((int) $request->month != $records->month ){
                    $anEmployee = (new EmployeeDataService())->getAnEmployeeInfoByEmpId($request->emp_id);
                    if((new CateringDataService())->checkAnEmployeeCateringMonthRecordAlreadyExist($anEmployee->emp_auto_id,$request->month,$request->year)){
                        return  response()->json(['status'=>404, 'success' =>false, 'message'=>"This Month Record Already Exist ", 'error'=> "error"]);
                    } 
                }
                $isUpdate = (new CateringDataService())->updateAnEmployeeMonthlyCateringRecord($request->emcr_auto_id,$request->month,$request->year,$request->total_days,$request->amount,Auth::user()->id,Auth::user()->id,$request->remarks,$request->project);
                if ($isUpdate) {                
                    return  response()->json(['status'=>200, 'success' =>true,'message'=>"Successfully Updated"]);
                } else {
                    return  response()->json(['status'=>404, 'success' =>false, 'message'=>"Update Operation Failed ", 'error'=> "error"]);
                }
            }  
            else {
                // store as new record               
                $anEmployee = (new EmployeeDataService())->getAnEmployeeInfoByEmpId($request->emp_id);
                if ( !$anEmployee) {                
                    return  response()->json(['status'=>404, 'success' =>false, 'message'=>"Employee Not Found ", 'error'=> "error"]);
                }else if((new CateringDataService())->checkAnEmployeeCateringMonthRecordAlreadyExist($anEmployee->emp_auto_id,$request->month,$request->year)){
                    return  response()->json(['status'=>404, 'success' =>false, 'message'=>"This Month Record Already Exist ", 'error'=> "error"]);
                }
                $isSaved = (new CateringDataService())->insertAnEmployeeMonthlyCateringRecord($anEmployee->emp_auto_id,$request->month,$request->year,$request->total_days,$request->amount,Auth::user()->id,Auth::user()->id,$request->remarks,$request->project);
                if ($isSaved) {                
                    return  response()->json(['status'=>200, 'success' =>true,'message'=>"Successfully Saved"]);
                } else {
                    return  response()->json(['status'=>404, 'success' =>false, 'message'=>"Operation Failed ", 'error'=> "error"]);
                }
            }
        }catch(Exception $ex){
            return  response()->json(['status'=>404, 'success' =>false, 'message'=>"System Operation Failed", 'error'=> "error" ]);
        } 

    }

    public function searchAnEmployeeCateringRecord(Request $request){
        try
        {   
            if($request->emcr_auto_id){
                // single record searching by auto id for update modal open
                $records = (new CateringDataService())->searchingAnEmployeeCateringMonthRecordByAutoId($request->emcr_auto_id);
            }else {
                // multiple record searching by employee id
                $records = (new CateringDataService())->searcAnEmployeeCateringRecordForListView($request->emp_id,$request->month,$request->year);
            } 
            return  response()->json(['status'=>200, 'success' =>true, 'data'=>$records]);
        }catch(Exception $ex){
            return  response()->json(['status'=>404, 'success' =>false, 'message'=>"System Operation Failed", 'error'=> "error"]);
        } 
  
    }

    public function deleteAnEmployeeCateringRecord(Request $re){
       try
       {      
            $record = (new CateringDataService())->searchingAnEmployeeCateringMonthRecordByAutoId($re->emcr_auto_id);
            if (!$record) {                 
                return  response()->json(['status'=>404, 'success' =>false, 'message'=>"Record Not Found ", 'error'=> "error"]);
            }else if((new SalaryProcessDataService())->checkAnEmployeeSalaryIsAlreadyPaid($record->emp_auto_id, $record->month,$record->year))
            {
                return  response()->json(['status'=>404, 'success' =>false, 'message'=>"This Month Salary Already Paid ", 'error'=> "error"]);
            }
            $isDeleted =  (new CateringDataService())->deleteAnEmployeeCateringMonthRecordByAutoId($re->emcr_auto_id);
            
            if ($isDeleted) {                 
                return  response()->json(['status'=>200, 'success' =>true,'message'=>"Successfully Deleted"]);
            } else {
                return  response()->json(['status'=>404, 'success' =>false, 'message'=>"Deletion Operation Failed ", 'error'=> "error"]);
            }
            
        }catch(Exception $ex){
            return  response()->json(['status'=>404, 'success' =>false, 'message'=>"System Operation Failed".$ex, 'error'=> "error"]);
        }   
    }


    // Upload Employee Work Records Excel File
    public function checkUploadedFileProperties($extension, $fileSize)
    {
            $valid_extension = array("csv", "xlsx"); //Only want csv and excel files
            $maxFileSize = 5242888; // Uploaded file size limit is 5mb
            if (in_array(strtolower($extension), $valid_extension)) {
                if ($fileSize <= $maxFileSize) {
                    return true;
                } else {
                    return false;
                }
            } else {
            return false;
            }
    }
    // Import Work Record Excel File to Temporary Table
    public function importEmployeeMonthlyWorkRecordsFromExcel(Request $request)
    { 
        
                if ($request->file && $request->month) {

                        $file = $request->file; 
                        $month = $request->month;
                        $year = $request->year;
                        $extension = $file->getClientOriginalExtension(); //Get extension of uploaded file
                        $fileSize = $file->getSize(); //Get size of uploaded file in bytes
                        if($this->checkUploadedFileProperties($extension, $fileSize)){
                                    
                                    $import = new ImportMonthlyCateringRecord($month,$year);
                                    Excel::import($import, $request->file('file'));                               
                                    return response()->json([
                                        'status' => 200,
                                        'success'=> true,
                                        'records_not_found' => $import->records_not_found,
                                        'records' => $import->records,
                                        'message' => $import->records->count() ." Records  Added for Uploading"
                                    ]);
                        }else {
                            
                            return response()->json([
                                'status' => 404,
                                'success'=> false,
                                'message' =>  " Invalid File Format Or Large file size"
                            ]);
                        }

                } else {
                    return response()->json([
                        'status' => 403,
                        'success'=> false,
                        'message' =>  " file not found"
                    ]);
                }
    }
    // Submit Imported Excell Data To Final Table 
    public function submitEmployeeMonthlyCateringRecordsImportFromExcel(Request $request){

        try{   
            
            $project_id = $request->project != '' ? $request->project : null;
            $result = (new CateringDataService())->getCateringImportedTemporaryTableRecordsForFinalUpload();
            DB::beginTransaction();
            foreach ($result as $arecord){ 
                $remarks = $arecord->remarks == null ? "" :  $arecord->remarks;
                if(!(new CateringDataService())->checkAnEmployeeCateringMonthRecordAlreadyExist($arecord->emp_auto_id,$arecord->month,$arecord->year)){
                    (new CateringDataService())->insertAnEmployeeMonthlyCateringRecord($arecord->emp_auto_id,$arecord->month,$arecord->year,$arecord->total_days,$arecord->amount,$arecord->inserted_by,$arecord->inserted_by,$remarks, $project_id);
                }                
            }
            DB::commit();
            // remove all records from table 
            (new CateringDataService())->deleteCateringImportedTemporaryTableRecordsForExcelUpload();
            return response()->json([
                'status' =>  200,
                'success' => true,
                'message' => 'Successfully Uploaded'
            ]);
        }catch(Exception $ex){
            DB::rollBack();
            return response()->json([
                'error' =>  "Data Upload Failed",
                'status' => 500,
                'success' => false,
            ]);
        }

    }


    public function processAndShowCateringReport(Request $request){
       
        try
        {   
         
            if($request->report_employee_id){
                // single record searching by auto id for update modal open
                $records = (new CateringDataService())->getAnEmployeeCateringServiceReport($request->report_employee_id,null,$request->report_year);
                $report_title = "Employee Catering Service Report"; 
            }else {
                 
                 // multiple record searching by month and year
                $project_ids = $request->report_project != '' ? [$request->report_project] : (new ProjectDataService())->getAllActiveProjectIDs();
                $records = (new CateringDataService())->getCateringServiceRecordByProjectMonthAndYearReport($project_ids,$request->report_month,$request->report_year);
                //$records = (new CateringDataService())->getCateringServiceRecordByMonthAndYearReport($request->report_month,$request->report_year);

                $report_title = "Catering Service ".(new HelperController())->getMonthName($request->report_month)." ".$request->report_year; 
            }                  
            $company =  (new CompanyDataService())->findCompanryProfile();
            return view('admin.report.catering_service.catering_service_monthly_report', compact('records', 'company', 'report_title'));    
            
        }catch(Exception $ex){
            
        } 
  
    }

    

    
}
