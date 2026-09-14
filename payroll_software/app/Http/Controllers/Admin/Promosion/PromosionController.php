<?php

namespace App\Http\Controllers\Admin\Promosion;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\EmpCategoryController;
use Illuminate\Http\Request;
use App\Imports\ImportEmployeePromotions;
use App\Http\Controllers\Admin\Helper\HelperController;
use App\Http\Controllers\Admin\Helper\UploadDownloadController;
use App\Http\Controllers\DataServices\EmployeeDataService;
use App\Http\Controllers\DataServices\EmployeeRelatedDataService;
use App\Http\Controllers\DataServices\SalaryProcessDataService;
use App\Http\Controllers\DataServices\CompanyDataService;
use App\Http\Controllers\DataServices\EmployeePromotionDataService;
use App\Http\Controllers\DataServices\AuthenticationDataService;
use App\Http\Controllers\DataServices\ProjectDataService;
use App\Models\EmpJobExperience;
use App\Models\EmpContactPerson;
use App\Models\EmployeeInfo;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;


class PromosionController extends Controller
{
        protected $employee_searching_by_employee_id = 'employee_id';

    function __construct()  {

        $this->middleware('permission:employee-promotion', ['only' =>
        ['index','insertPromosion','approvalRequestOfPromotedEmployees','getPromotedEmployeesWaitingForApprovalRecords',
        'promotedEmployeePromotionPaperUploadRequest','showEmployeePromotionDetailsDateToDateReport']]);
        $this->middleware('permission:emp_promotion_record_delete',['only'=>['deleteAPromotionRecordInsertedRecord']]);

    }


  /*
  |--------------------------------------------------------------------------
  |  DATABASE OPERATION
  |--------------------------------------------------------------------------
  */
  public function findEmployee(Request $request)
  {
    $employee_id = $request->emp_id;
    $emp = (new EmployeeDataService())->getAnEmployeeInfoByEmpId($employee_id);
    $findEmployee =  (new EmployeeDataService())->getEmployeeInformationWithCountryDivDistEmpTypeEmpDepartCateProject($emp->emp_auto_id);
    $find_job_experience = EmpJobExperience::where('emp_id', $emp->emp_auto_id)->get();
    $find_emp_contact_person = EmpContactPerson::where('emp_id', $emp->emp_auto_id)->get();


    return json_encode([
      'find_job_experience' => $find_job_experience,
      'find_emp_contact_person' => $find_emp_contact_person,
      'findEmployee' => $findEmployee,
    ]);
  }


  // Find An Employee Details
  public function findEmployeeDetails(Request $request)
  {
    //dd('calling');
    $employee_id = $request->emp_id;
    $iqamaNo = $request->iqamaNo;

    if ($employee_id != "") {
      $employee = (new EmployeeDataService())->getAnEmployeeInfoByEmpId($employee_id);
    } else {
      $employee = (new EmployeeDataService())->getAnEmployeeInfoByEmpIqamaNo($iqamaNo);
    }

    if ($employee) {
      // dd($employee->emp_auto_id);
      $findEmployee = (new EmployeeDataService())->getEmployeeInformationWithCountryDivDistEmpTypeEmpDepartCateProject($employee->emp_auto_id);

      $salary = (new EmployeeDataService())->getAnEmployeeSalaryDetailsByEmpAutoId($employee->emp_auto_id);

      $designationOBJ = new EmpCategoryController();
      $designation = $designationOBJ->getAllCategory();
      $find_job_experience = EmpJobExperience::where('emp_id', $employee->emp_auto_id)->get();
      $find_emp_contact_person = EmpContactPerson::where('emp_id', $employee->emp_auto_id)->get();

      /* ====== return json ====== */
      return json_encode([
        'find_job_experience' => $find_job_experience,
        'find_emp_contact_person' => $find_emp_contact_person,
        'findEmployee' =>  $findEmployee,
        'salary' => $salary,
        'designation' => $designation,
      ]);
    } else {
      return json_encode([
        'status' => "error",
      ]);
    }
  }


  // Find An Employee advance adjustment
  public function findEmployeeadjustment(Request $request)
  {
    $employee_id = $request->emp_id;
    // $year = $request->year;
    $year = date('Y', strtotime(Carbon::now()));
    $iqamaNo = $request->iqamaNo;

    if ($employee_id != "") {
      $employee = (new EmployeeDataService())->getAnEmployeeInfoByEmpId($employee_id);
    } else {
      $employee = (new EmployeeDataService())->getAnEmployeeInfoByEmpIqamaNo($iqamaNo);
    }

    if ($employee) {
      /* ========== Employee Salary info ========== */

      $findEmployee = (new EmployeeDataService())->getEmployeeInformationWithCountryDivDistEmpTypeEmpDepartCateProject($employee->emp_auto_id);
      $salary = (new EmployeeDataService())->getAnEmployeeSalaryDetailsByEmpAutoId($employee->emp_auto_id);
      $designation =   (new EmployeeRelatedDataService())->getEmpAllCategoryInfoForDropdown();
      $find_job_experience = EmpJobExperience::where('emp_id', $employee->emp_auto_id)->get();
      $find_emp_contact_person = EmpContactPerson::where('emp_id', $employee->emp_auto_id)->get();

      /* ====== return json ====== */

      return json_encode([
        'find_job_experience' => $find_job_experience,
        'find_emp_contact_person' => $find_emp_contact_person,
        'findEmployee' =>  $findEmployee,
        'salary' => $salary,
        'designation' => $designation,
      ]);
    } else {
      return json_encode([
        'status' => "error",
      ]);
    }
  }




  // Find An Employee for update employee Project and job Status
  public function findEmployeeStatus(Request $request)
  {
    $employee_id = $request->emp_id;
    $iqamaNo = $request->iqamaNo;

    if ($employee_id != "") {
      $employee = (new EmployeeDataService())->getAnEmployeeInfoByEmpId($employee_id);
    } else {
      $employee = (new EmployeeDataService())->getAnEmployeeInfoByEmpIqamaNo($iqamaNo);
    }
    if ($employee) {
      $getAllProject = (new EmployeeRelatedDataService())->getAllProjectInfoForDropdown();

      $employeeStatusOBJ = new HelperController();
      $allEmployeeStatus = $employeeStatusOBJ->getEmployeeStatus();


      $findEmployee = (new EmployeeDataService())->getEmployeeInformationWithCountryDivDistEmpTypeEmpDepartCateProject($employee->emp_auto_id);
      $salary = (new EmployeeDataService())->getAnEmployeeSalaryDetailsByEmpAutoId($employee->emp_auto_id);

      $designation =  (new EmployeeRelatedDataService())->getEmpAllCategoryInfoForDropdown();
      $agencies = (new CompanyDataService())->getAllAgencies();

      $find_job_experience = EmpJobExperience::where('emp_id', $employee->emp_auto_id)->get();
      $find_emp_contact_person = EmpContactPerson::where('emp_id', $employee->emp_auto_id)->get();

      /* ====== return json ====== */
      return json_encode([
        'getAllProject' => $getAllProject,
        'allEmployeeStatus' => $allEmployeeStatus,
        'find_job_experience' => $find_job_experience,
        'find_emp_contact_person' => $find_emp_contact_person,
        'findEmployee' =>  $findEmployee,
        'salary' => $salary,
        'designation' => $designation,
        'agencies' => $agencies
      ]);
    } else {
      return json_encode([
        'status' => "error",
      ]);
    }
  }


  /* ++++++++++++++++ Insert Promoted Salary Info ++++++++++++++++ */
  public function insertPromosion(Request $request)
  {

       try{
              $this->validate($request, [
                'emp_id' =>'required',
                'prom_date' => 'required',
                'promotion_by' => 'required',
                'hourly_rent' => 'required',
            ], []);


            $emp_auto_id = $request->emp_id;
            $increment_amount = 0;
            $anEmployee = (new EmployeeDataService())->getAnEmployeeInfoWithSalaryDetailsByEmpAutoId($emp_auto_id);

              if($anEmployee == null){
                Session::flash('error', 'Employee Not Found, Please Refresh Page and Try Again');
                return Redirect()->back();
              }



              if($anEmployee->hourly_employee){
                $increment_amount =  $request->hourly_rent - $anEmployee->salarydetails->hourly_rent;
              }else {
                $total_amount =  $request->basic_amount + $request->house_rent + $request->mobile_allowance + $request->food_allowance + $request->medical_allowance +
                $request->local_travel_allowance + $request->conveyance_allowance + $request->others1;
                $increment_amount =  $total_amount - (new EmployeeDataService())->getAnBasicEmployeeTotalSalaryAmountByEmpAutoId($emp_auto_id);
              }
            $prom_apprv_documents = "";
            if ($request->hasFile('prom_approve_documents')) {
                $file = $request->file('prom_approve_documents');
                $prom_apprv_documents = (new UploadDownloadController())->uploadEmployeePromotionApprovedPhoto($file, null);
            }


            $insertPromosion = (new EmployeePromotionDataService())->insertAnEmployeePromotionDetailsInfo($emp_auto_id, $request->designation_id, $request->emplyoeeDesignation,
            $request->basic_amount, $request->hourly_rent, $request->mobile_allowance, $request->food_allowance, $request->medical_allowance,$request->house_rent, $request->local_travel_allowance,
            $request->conveyance_allowance, $request->others1, $request->promotion_by, $request->prom_date, $request->prom_remarks,$prom_apprv_documents,$increment_amount);


            if ($insertPromosion) {

                // login user activities record
              $salary_amount = $request->basic_amount > 0 ? $request->basic_amount : $request->hourly_rent;
              (new AuthenticationDataService())->InsertLoginUserActivity(13,1, Auth::user()->id,$emp_auto_id,$salary_amount);

              Session::flash('success', 'Employee Promotion Successfully Completed');
              return Redirect()->back();
            } else {
              Session::flash('error', 'Something went wrong, Please try again');
              return Redirect()->back();
            }

       }catch(Exception $ex){
          Session::flash('error', 'System Exception Occured');
          return Redirect()->back();
       }

  }


    public function deleteAPromotionRecordInsertedRecord(Request $request){
        try{
              $arecord =  (new EmployeePromotionDataService())->getAnEmployeePromotionRecordInfoByPromotionAutoId($request->promotion_auto_id);
              if($arecord){
                if($arecord->approval_status){
                    return response()->json(['status'=>405,'success'=>false,'message'=>"Already Approved, Deletion Not Allowed",'error'=>'error']); // 405 Mehtod not allowed
                }else{
                    $isDeleted =  (new EmployeePromotionDataService())->deleteAPromotionRecordThatApprovelPendingByPromotionAutoId($request->promotion_auto_id);
                    if($isDeleted){
                         // login user activities record
                  (new AuthenticationDataService())->InsertLoginUserActivity(28,3, Auth::user()->id,$arecord->emp_id,null);

                      return response()->json(['status'=>200,'success'=>true,'message'=>"Successfully Deleted" ]);
                    } else{
                      return response()->json(['status'=>405,'success'=>false,'message'=>"delete Operation Failed",'error'=>'error']);
                    }
                }
              }else{
                    return response()->json(['status'=>405,'success'=>false,'message'=>"Record Not Found",'error'=>'error']);
              }
        }catch(Exception $ex){
              return response()->json(['status'=>405,'success'=>false,'message'=>"System Operation Failed",'error'=>'error']);
        }

    }

  public function approvalRequestOfPromotedEmployees(Request $request){

      $emp_prom_ids = $request->emp_prom_auto_id;
      $counter = 0;
      foreach ($emp_prom_ids as $prom_id){
          if($request->has('promoted_emp_checkbox-'.$prom_id)){

              $promotion_record =  (new EmployeePromotionDataService())->getAnEmployeePromotionRecordInfoByPromotionAutoId($prom_id);
              $anEmployee = (new EmployeeDataService())->getAnEmployeeInformationWithAllReferenceTableByEmpAutoId($promotion_record->emp_id);
              $update = (new EmployeePromotionDataService())->approveAnEmployeePromotionRecord($prom_id,Carbon::now(),Auth::user()->id);
              if($update && $promotion_record){

                  (new EmployeeDataService())->updateEmployeeSalaryDetailsInformationAsPromotionByEmpAutoId(
                  $promotion_record->emp_id, $promotion_record->basic_amount,  $promotion_record->hourly_rent,  $promotion_record->house_rent,
                  $promotion_record->mobile_allowance,$promotion_record->local_travel_allowance, $promotion_record->conveyance_allowance, $promotion_record->medical_allowance,
                  $promotion_record->increment_no, $promotion_record->increment_amount,  $promotion_record->others1, $promotion_record->food_allowance
                );

                  (new EmployeeDataService())->insertAnEmployeeSalaryUpdateHistory(
                  $promotion_record->emp_id,  $promotion_record->basic_amount,  $anEmployee != null ?  $anEmployee->basic_hours : 0, // basic hours
                  $promotion_record->house_rent,  $promotion_record->hourly_rent, $promotion_record->mobile_allowance,
                  $promotion_record->medical_allowance,  $promotion_record->local_travel_allowance, $promotion_record->conveyance_allowance,
                  $promotion_record->food_allowance,$anEmployee != null ?  $anEmployee->cpf_contribution : 0, // cpf contribution
                  $promotion_record->increment_amount, $anEmployee != null ?  $anEmployee->saudi_tax : 0, // saudi tax
                  $promotion_record->others1,  $anEmployee != null ? ( $anEmployee->hourly_employee == 1 ? 1:0) : 0,
                  $anEmployee != null ?  $anEmployee->payment_method : 'Cash', Auth::user()->id
                );
              }
              $counter++;
                // no need to change
            // (new EmployeeDataService())->updateEmployeeDesignation($promotion_record->emp_id, $promotion_record->new_designation_id);
             // login user activities record
             $salary_amount = $promotion_record->basic_amount > 0 ? $promotion_record->basic_amount : $promotion_record->hourly_rent;
             (new AuthenticationDataService())->InsertLoginUserActivity(27,2, Auth::user()->id,$promotion_record->emp_id,$salary_amount);


          }
      }

      if ($counter>0) {
        Session::flash('success', 'Successfully Completed');
        return Redirect()->back();
      } else {
        Session::flash('error', 'Something went wrong, Please try again');
        return Redirect()->back();
      }

  }

  public function getPromotedEmployeesWaitingForApprovalRecords(Request $request){
      try{

          $records = (new EmployeePromotionDataService())->getEmployeePromotionApprovalWaitingRecords($request->from_date, $request->to_date,Auth::user()->branch_office_id);
          return response()->json(['status'=>200,'success'=>true,'data'=>$records]);
      }catch(Exception $ex){
        return response()->json(['status'=>405,'success'=>false,'message'=>$ex,'error'=>'error']); // 405 Mehtod not allowed

      }
  }

       /* =============== Multiple EMPLOYEE Promotion Paper Upload=============== */
  public function promotedEmployeePromotionPaperUploadRequest(Request $request){

      try{

            $file_path = null;
            if ($request->hasFile('upload_paper') && $request->has('emp_prom_auto_id')) {
                $file = $request->file('upload_paper');
                $file_path = (new UploadDownloadController())->uploadEmployeePromotionApprovedPhoto($file, null);
            }
          if($file_path){

               $isSuccess = false;
               $counter = 0;
               $id_select_list = array();
               foreach ($request->emp_prom_auto_id as $emp_prom_id) {

                   if ($request->has('promoted_emp_checkbox-' . $emp_prom_id)) {
                   $id_select_list[$counter++] = $emp_prom_id;
                 }
               }
               $isSuccess =  (new EmployeePromotionDataService())->updatePromotedMultipleEmployeePromotionPaper($id_select_list,$file_path,Carbon::now(),Auth::user()->id);
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





     // Show Promoted Employee Report



  public function showEmployeePromotionDetailsDateToDateReport(Request $request){

        if((int)$request->report_type == 1){

            if(!is_null($request->employee_ids)){
                $employee = (new EmployeeDataService)->searchingAnEmployeeIsExistInSystemByMultitypeParameter($request->employee_ids, $this->employee_searching_by_employee_id);
                $records = (new EmployeePromotionDataService())->getAnEmployeePromotionDetailsRecords($employee->employee_id);
            }else {
                $records = (new EmployeePromotionDataService())->getEmployeePromotionDetailsDateToDate(0, $request->from_date, $request->to_date);
            }

            $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
            return view('admin.report.emp_increment.employee_promotion_report', compact('records', 'company'));

        }else if($request->report_type == 2){


            if($request->report_record_type == 1){

                    if($request->employee_ids != null){
                        $multiple_employee_id = explode(",", $request->employee_ids);
                        $multiple_employee_id = array_unique($multiple_employee_id); // remove multiple same empl ID
                        $employee_list = (new EmployeeDataService)->getAnEmployeesInfoWithSalaryDetailForEmployeePromotionDetailsReport($multiple_employee_id,Auth::user()->branch_office_id);

                    }else{

                        if ($request->project_ids == null) {
                            $request->project_ids = (new ProjectDataService())->getAllActiveProjectIDs();
                        }else{
                            $request->project_ids = [ $request->project_ids];
                        }

                        if ($request->designation_heads == null) {
                            $request->designation_heads = (new EmployeeRelatedDataService())->getAllActiveDesignationHeadIDs();
                        }
                        else {
                            $request->designation_heads = [ $request->designation_heads ];
                        }
                        $employee_list = (new EmployeeDataService)->getListOfEmployeesInfoWithSalaryDetailForEmployeePromotionDetailsReport($request->project_ids,$request->designation_heads,Auth::user()->branch_office_id);

                    }

                $counter = 0;
                $employees = array();
                foreach($employee_list as $emp){

                    $emp->joining_salary = (new SalaryProcessDataService())->getAnEmployeeJoiningMonthAllIncludedTotalSalaryAmount($emp->emp_auto_id,$emp->hourly_employee);
                    $records = (new EmployeePromotionDataService())->getAnEmployeePromotedAllRecordsAmountRelatedDetailsInformation($emp->emp_auto_id);
                    if(count($records) >0){
                        $emp->promotion_records = $records;
                    }else {
                        $emp->promotion_records = [];
                    }
                      $employees[$counter++] = $emp;

                }
                $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
                $login_user_name= Auth::user()->name;
                return view('admin.report.emp_increment.promoted_emp_history_report', compact('employees', 'company','login_user_name'));
            }
            else if($request->report_record_type == 2){

                 $multiple_employee_id = explode(",", $request->employee_ids);
                $multiple_employee_id = array_unique($multiple_employee_id); // remove multiple same empl ID
                $employees = (new EmployeeDataService)->getAnEmployeesInfoWithSalaryDetailForEmployeePromotionDetailsReport($multiple_employee_id,Auth::user()->branch_office_id);
                $counter = 0;
                foreach($employees as $emp){
                        $emp->prom_date = null;
                        $emp->increment_amount = null;

                        $precord = (new EmployeePromotionDataService)->getAnEmployeePromotionLastRecords1($emp->emp_auto_id,Auth::user()->branch_office_id);

                        if($precord){
                            $emp->prom_date = $precord->prom_date;
                            $emp->increment_amount = $precord->increment_amount;
                        }
                        $employees[$counter++] = $emp;
                }
                $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
                $login_user_name= Auth::user()->name;
                return view('admin.report.emp_increment.multi_emp_last_increment_report', compact('employees', 'company','login_user_name'));

            }
        }

  }




    // Import Work Record Excel File to Temporary Table
    public function importEmployeePromotionsFromExcel(Request $request)
    {
        if ($request->file) {

            $file = $request->file;
            $extension = $file->getClientOriginalExtension(); //Get extension of uploaded file
            $fileSize = $file->getSize(); //Get size of uploaded file in bytes
            if($this->checkUploadedFileProperties($extension, $fileSize)){
                $document_url = '';
                $import = new ImportEmployeePromotions($document_url);
                Excel::import($import, $request->file('file'));
                return response()->json([
                    'status' => 200,
                    'success'=> true,
                    'records_not_found' => $import->records_not_found,
                    'records_with_errors' => $import->records_with_errors,
                    'records' => $import->records,
                    'message' => $import->records->count() ." Records  Added Successfully"
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
    public function saveEmployeePromotionRecordsImportFromExcel(Request $request){

        try{


                $this->validate($request, [
                    'promotion_date' =>'required',
                   // 'prom_approve_documents' => 'required'
                ], []);

                $result = (new EmployeePromotionDataService())->getAllEmployeesPromotionsImportedExcellDataFromImportedTable();
                $startDate = Carbon::now()->firstOfMonth()->format('Y-m-d');
                $promotion_date = $request->promotion_date;
              DB::beginTransaction();
                $prom_apprv_documents = "";
                if ($request->hasFile('prom_approve_documents')) {
                        $file = $request->file('prom_approve_documents');
                        $prom_apprv_documents = (new UploadDownloadController())->uploadEmployeePromotionApprovedPhoto($file, null);
                    }

                foreach ($result as $apromoted_record){

                    $anEmployee = (new EmployeeDataService())->getAnEmployeeInfoWithSalaryDetailsForPromotionExcelFileUpload($apromoted_record->emp_id);
                        if($anEmployee == null){
                            continue;
                        }
                    $emp_auto_id = $anEmployee->emp_auto_id;
                    $increment_amount = 0;

                    if($anEmployee->hourly_employee){
                        $increment_amount =  $apromoted_record->hourly_rent - $anEmployee->hourly_rent;
                    }else {
                        $total_amount =  $apromoted_record->basic_amount + $apromoted_record->house_rent + $apromoted_record->mobile_allowance + $apromoted_record->food_allowance + $apromoted_record->medical_allowance +
                        $apromoted_record->local_travel_allowance + $apromoted_record->conveyance_allowance + $apromoted_record->others1;

                        $previous_total_amount =  $anEmployee->basic_amount + $anEmployee->house_rent + $anEmployee->mobile_allowance + $anEmployee->food_allowance + $anEmployee->medical_allowance +
                        $anEmployee->local_travel_allowance + $anEmployee->conveyance_allowance + $anEmployee->others1;
                        $increment_amount =  $total_amount -  $previous_total_amount  ;//(new EmployeeDataService())->getAnBasicEmployeeTotalSalaryAmountByEmpAutoId($emp_auto_id);
                    }


                    $insertPromosion = (new EmployeePromotionDataService())->insertAnEmployeePromotionDetailsInfo($emp_auto_id, $apromoted_record->new_designation_id,
                    $apromoted_record->new_designation_id,$apromoted_record->basic_amount, $apromoted_record->hourly_rent, $apromoted_record->mobile_allowance,
                    $apromoted_record->food_allowance, $apromoted_record->medical_allowance,$apromoted_record->house_rent, $apromoted_record->local_travel_allowance,
                    $apromoted_record->conveyance_allowance, $apromoted_record->others1, $apromoted_record->prom_by, $promotion_date,
                    $apromoted_record->prom_remarks,$prom_apprv_documents,$increment_amount);

                    if ($insertPromosion) {
                        // login user activities record
                        $salary_amount = $request->basic_amount > 0 ? $request->basic_amount : $request->hourly_rent;
                        (new AuthenticationDataService())->InsertLoginUserActivity(13,1, Auth::user()->id,$emp_auto_id,$salary_amount);
                    }
                }// end of foreach

                        // remove all records from temporary table
            (new EmployeePromotionDataService())->deleteEmployeePromotionsImportedExcellDataFromTable();
              DB::commit();

            Session::flash('success', 'Updated Successfully');
            return redirect()->back();




        }catch(Exception $ex){
          DB::rollBack();
            (new EmployeePromotionDataService())->deleteEmployeePromotionsImportedExcellDataFromTable();
            Session::flash('error', 'Operation Failed, Please Try Again');
                return redirect()->back();
        }

    }


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
        }
        else {
            return false;
        }
    }





  /*
  |--------------------------------------------------------------------------
  |  BLADE OPERATION
  |--------------------------------------------------------------------------
  */
  public function index()
  {
   
      $projects = (new ProjectDataService())->getAllActiveProjectListForDropdown(Auth::user()->branch_office_id);
      $designation_heads = (new EmployeeRelatedDataService())->getDesignationHeadRecordsForDropdown();
      return view('admin.employee-promosion.all',compact('projects','designation_heads'));
  }


  public function insertSalaryHisotry(){

          $emplist =  (new EmployeeDataService())->getDesignationHeadBaseEmployeeListReport(null,null,null);
          foreach($emplist  as $emp){

            (new EmployeeDataService())->insertAnEmployeeSalaryUpdateHistory(
                    $emp->emp_auto_id,  $emp->basic_amount,    $emp->basic_hours ,
                    $emp->house_rent,  $emp->hourly_rent, $emp->mobile_allowance,
                    $emp->medical_allowance,  $emp->local_travel_allowance, $emp->conveyance_allowance,
                    $emp->food_allowance, $emp->cpf_contribution,
                    $emp->increment_amount,   $emp->saudi_tax,
                    $emp->others1, $emp->hourly_employee == 1 ? 1:0,
                    $emp->payment_method != null ? $emp->payment_method : 'Cash', Auth::user()->id,$emp->created_at
                    );
          }
  }




  /* ======================================================================= */
}
