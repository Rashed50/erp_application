<?php

namespace App\Http\Controllers\Admin;

use App\Enums\EmpWorkActivityRatingEnum;
use App\Models\Enum\JobStatusEnum;
use App\Enums\SalaryPaymentMethodEnum;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\CompanyProfileController;
use App\Http\Controllers\Admin\EmpCategoryController;
use App\Http\Controllers\Admin\AnualFee\AnualFeeDetailsController;
use App\Http\Controllers\Admin\Helper\HelperController;
use App\Http\Controllers\DataServices\EmployeeDataService;
use App\Http\Controllers\Admin\Helper\UploadDownloadController;
use App\Http\Controllers\DataServices\AccommodationDataService;
use App\Http\Controllers\DataServices\CompanyDataService;
use App\Http\Controllers\DataServices\EmployeeAdvanceDataService;
use App\Http\Controllers\DataServices\SalaryProcessDataService;
use App\Http\Controllers\DataServices\EmpActivityDataService;
use App\Http\Controllers\DataServices\EmployeeRelatedDataService;
use App\Http\Controllers\DataServices\ProjectDataService;
use App\Http\Controllers\DataServices\AuthenticationDataService;
use App\Http\Requests\NewEmpFormRequest;
use App\Models\EmpContactPerson;
use App\Models\EmpJobExperience;
use Illuminate\Http\Request;
use App\Models\IqamaRenewalDetails;
use App\Models\EmployeeInfo;
use App\Models\Religion;
use App\Models\JobStatus;
use App\Models\EmployeeCategory;
use App\Models\Sponsor;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;





class EmployeeInfoController extends Controller
{


    function __construct()
    {

        $this->middleware('permission:employee-add', ['only' => ['add','insert','addSalaryDetails','']]);
        $this->middleware('permission:employee-list', ['only' => ['index','edit','updateEmployeeInformationData']]);
        $this->middleware('permission:employee-search', ['only' => ['loadSearchingUIForAnEmployeeDetailsByMultiTypeParameter']]); // Only Searching Employee
        $this->middleware('permission:employee-status', ['only' => ['searchEmpStatus']]); // Employee Status Update
        $this->middleware('permission:multiple-employee-transfer', ['only' => ['multipleEmployeeTransferForm','multipleEmployeeTransferFormSubmit']]); // multiple emp tranfer
        $this->middleware('permission:employee_all_information_update', ['only' => ['searchEmpForUpdate','updateEmployeeAllInformation']]); // update all including salary detail
        $this->middleware('permission:job-approve', ['only' => ['loadNewEmployeeApprovalPendingListWithUI','approvalOfNewInsertedEmployees']]);

       // $this->middleware('permission:employee_job_status_change_activity', ['only' => ['loadNewEmployeeApprovalPendingListWithUI','approvalOfNewInsertedEmployees']]);
         // need to check this more before uncomment

        $this->middleware('permission:employee_working_shift_update', ['only' => ['loadEmployeeWorkingShiftStatusUpdateUI','']]); // Employee WOrking Shift Update
        $this->middleware('permission:report-employee-list-project', ['only' => ['projectWiseEmployeeList']]); // Employee List Report Projectwise
        $this->middleware('permission:hr_report_employee_information', ['only' => ['loadHRRelatedEmployeeReportForm']]); // HR Section Report
    }


  public function getEmpCategory($emp_type_id)
  {
       try {

             $getEmpCatg = (new EmployeeRelatedDataService())->getAllActiveCategoriesForDropdownByEmployeeTypeId(null);
            return json_encode($getEmpCatg);
        } catch (\Exception $e) {
            Log::error('Error in getEmpCategory: ' . $e->getMessage());
            return json_encode(['status'=>500,'success'=>false,'error'=>$e->getMessage()]);
        }
  }



 // Ajax Request Form Employee Insert UI checkEmployeeId
    public function checkEmployeeUniqueInformationBeforeAddNewEmployee(Request $request)
    {
        $find_emp = (new EmployeeDataService())->checkThisValueIsExistInServerDatabase($request->value,$request->dbcolum_name);
        if ($find_emp) {
            return response()->json(['status' => 200, 'success' =>true, 'data' => 1,'error' => 'Already Exist this information']);
        } else {
            return response()->json(['status' => 404, 'success' =>false,'data' => 0,'error' => '']);
        }
    }
    // Ajax Request for searching next new Employee unique ID
    public function searchNextNewEmployeeUniqueID(Request $request)
    {
        try{

             $find_next_emp_id = (new EmployeeDataService())->searchNewEmployeeUniqueEmployeeID($request->new_emp_type);
             if ($find_next_emp_id) {
                 return response()->json(['status' => 200, 'success' => true, 'data' => $find_next_emp_id]);
             } else {
                 return response()->json(['status' => 404, 'success' => false, 'data' => 0, 'error' => 'System Error']);
             }


        }catch(Exception $ex){
            return response()->json(['status' => 404, 'success' => false, 'data' => 0, 'error' => 'System Error']);
        }

    }


  /*
  |--------------------------------------------------------------------------
  |  FROM BLADE FILE REQUEST OPERATION
  |--------------------------------------------------------------------------
  */



      // Searching Employee Full Details By Employee Id Or Passport or Iqama  AJAX Request from emp search menu
  public function searchingEmployeeByEmployeeMultitypeParameter(Request $request){

        if($request->search_by == 1){
            // searching by name or passport or iqama or employee id
            $employee = (new EmployeeDataService())->searchingEmployeeInfoByAnyColumnValueMatching($request->employee_searching_value,Auth::user()->branch_office_id);
        }else {
            $employee = (new EmployeeDataService())->searchingAnEmployeeInfoByMultitypeParameter($request->employee_searching_value, $request->search_by,Auth::user()->branch_office_id);
        }

       if (count($employee) > 0) {
            return json_encode([
                'success'  => true,
                'status'=> 200,
                'error' => null,
                'findEmployee' =>  $employee,
            ]);
        } else {
            return json_encode([
                'success'  => false,
                'status'=> 404,
                'error' => 'error',
                'message' => 'Employee Not Found',
            ]);
        }
  }

    // Searching Active Employee Full Details By Employee Id Or Passport or Iqama AJAX Request from emp status menu
  public function searchingActiveEmployeeByEmployeeMultitypeParameter(Request $request){

    $searchByDb_Column = $request->search_by;
    $employee_searching_value = $request->employee_searching_value;
    $employee = (new EmployeeDataService())->searchingAnEmployeeInfoByMultitypeParameter($employee_searching_value, $searchByDb_Column,Auth::user()->branch_office_id);

    //$employee = (new EmployeeDataService())->searchingAnActiveEmployeeInfoByMultitypeParameter( $employee_searching_value,$searchByDb_Column); // will be delete 19.7.24

    $accomdOfficeBuilding = (new AccommodationDataService())->getAllActiveOfficeBuildingNameIdAndCityForDropdownList();
    $getAllProject = (new ProjectDataService())->getLoginUserAssingedProjectForDropdownList(Auth::user()->id);

   // $employeeStatusOBJ = new HelperController();
    $allEmployeeStatus = [];// $employeeStatusOBJ->getEmployeeStatus();
    $designation =   (new EmployeeRelatedDataService())->getEmpAllCategoryInfoForDropdown();
    $agencies = (new CompanyDataService())->getAllAgencies();
    $sponsor = (new EmployeeRelatedDataService())->getAllSponserInfoForDropdown(Auth::user()->branch_office_id);

    if (count($employee) > 0) {
        return json_encode([
          'success'  => true,
           'status'=> 200,
          'error' => null,
          'findEmployee' =>  $employee,
          'empOfficeBuilding' => $accomdOfficeBuilding,
          'getAllProject' => $getAllProject,
          'allEmployeeStatus' => $allEmployeeStatus,
          'designation' => $designation,
          'agencies' => $agencies,
          'sponsors' => $sponsor,
        ]);
      }else {
          return json_encode([
            'success'  => false,
             'status'=> 404,
            'error' => 'error',
            'message' => 'Employee Not Found',
          ]);
      }
  }



  // ============== insert Employee Information in DATABASE ==============


  public function insert(Request $request)
  {
        // dd($request->all());
      // try{


            $this->validate($request, [

              'emp_name' => 'required|string|max:45',
              'akama_no' => 'required|string|max:10|unique:employee_infos',
              'passfort_no' => 'required|string|max:40|unique:employee_infos',
              'mobile_no' => 'required|max:20',
              'akama_expire' => 'required',
              'passfort_expire_date' => 'required',
              'sponsor_id' => 'required',
              'project_id' => 'required',

            ], [
              'emp_name.required' => 'please enter employee name!',
            ]);


        //       $this->validate($request, [

        //     'emp_id' =>'required|int|max:6|unique:employee_infos',
        //     'emp_name' => 'required|string|max:45',
        //     'akama_no' => 'required|string|max:10|unique:employee_infos',
        //     'akama_expire' => 'required',
        //     'passfort_no' => 'required|string|max:40|unique:employee_infos',
        //     'passfort_expire_date' => 'required',
        //     'sponsor_id' => 'required|integer',
        //     'mobile_no' => 'required|max:20',
        //     'designation_id' => 'required|integer',
        //     'country_id' => 'required|integer',
        //     'division_id' => 'required|integer',
        //     'district_id' => 'required|integer',
        //     'emp_type_id' => 'required|integer',
        //     'emp_type_id' => 'required|integer',
        //     'department_id' => 'required|integer',
        //     'project_id' => 'required|integer',
        //     // etra data

        //   ], [
        //     'emp_name.required' => 'please enter all required information!',
        //   ]);



            $request->employee_id = (int)$request->emp_id;
            $request->agency_id = $request->agency;

            $creator = Auth::user()->id;
            $emp_auto_id =  (new EmployeeDataService())->insertNewEmployee($request);

            if ($emp_auto_id > 0) {

              (new EmployeeDataService())->addEmployeeSalaryDetails($emp_auto_id, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
              (new EmployeeDataService())->insertEmployeeDetailsInformation($emp_auto_id,$request->department_id, 0, $request->religion, 0,$request->country_phone_no, $request->agency,
                    $request->gender, $request->maritus_status,$request->blood_group, $request->present_address,  $request->ref_employee_id, $request->remarks);

              if ($request->project_id == '' || $request->project_id == null) {
                $request->project_id = 0;
              }

              (new EmployeeRelatedDataService())->assignEmployeeToNewProject($emp_auto_id, $request->project_id, Carbon::now(),null, $creator,null);

                  // profile photo
                  if ($request->hasFile('profile_photo')) {
                    $file1 = $request->file('profile_photo');
                    $uplodedPath =  (new  UploadDownloadController())->uploadEmployeeProfilePhoto($file1, null);
                    $update = (new EmployeeDataService())->updateEmployeeUploadedFileDbPath($emp_auto_id, $uplodedPath, 'profile_photo');
                  }


                   // Iqama File
                    if ($request->hasFile('akama_photo')) {
                        $file = $request->file('akama_photo');
                        $uplodedPath =  (new  UploadDownloadController())->uploadEmployeeIqamaFile($file, null);
                        $update = (new EmployeeDataService())->updateEmployeeUploadedFileDbPath($emp_auto_id, $uplodedPath, 'akama_photo');
                    }

                  // pasfort_photo upload
                  if ($request->hasFile('pasfort_photo')) {
                    $file2 = $request->file('pasfort_photo');
                    $uplodedPath =  (new  UploadDownloadController())->uploadEmployeePassportFile($file2, null);
                    $update = (new EmployeeDataService())->updateEmployeeUploadedFileDbPath($emp_auto_id, $uplodedPath, 'pasfort_photo');
                  }


                  if ($request->hasFile('covid_certificate')) {
                  $file3 = $request->file('covid_certificate');
                  $uplodedPath =  (new  UploadDownloadController())->uploadEmployeeCOVIDCertificateFile($file3, null);
                  $update = (new EmployeeDataService())->updateEmployeeUploadedFileDbPath($emp_auto_id, $uplodedPath, 'covid_certificate');
                }

                  if ($request->hasFile('appoint_latter')) {

                        $file3 = $request->file('appoint_latter');
                        $uplodedPath =  (new  UploadDownloadController())->uploadEmployeeAppointmentLetterFile($file3, null);
                        $update = (new EmployeeDataService())->updateEmployeeUploadedFileDbPath($emp_auto_id, $uplodedPath, 'employee_appoint_latter');
                  }

                  if ($request->hasFile('educational_papers')) {
                        $file4 = $request->file('educational_papers');
                        $uplodedPath =  (new  UploadDownloadController())->uploadEmployeeEducationalDocuments($file4, null);
                        $update = (new EmployeeDataService())->updateEmployeeEducationalDocumentsPath($emp_auto_id, $uplodedPath, 'educational_papers');
                 }

                return Redirect()->route('add-salary-info', [$emp_auto_id]);
            }else {
              Session::flash('error', 'Employee ID Already Assigned, Please Reload and Try Again');
              return redirect()->back();
            }
    //   }catch(Exception $ex){
    //         Session::flash('error', 'System Operation Failed. Please Reload and Try Again '.$ex);
    //           return redirect()->back();
    //   }


  }




  // === Update Employee Basic & Salary Information ===

    public function updateEmployeeAllInformation(Request $request)
    {

         $update_by = Auth::user()->id;

         if((int)$request->operation_type == 2){
            // update employee salary payment methond
            $employee = (new EmployeeRelatedDataService())->getAnEmployeeDetailsAndBankInformationRecordByEmployeeId($request->employee_id);

             if($employee == null){
                return response()->json(["status" =>403, "success" => false, 'error' => 'error','message'=>  "Data Not Found"]);
            }
            else if($employee->acc_number == null && $request->payment_method == 'Bank'){
                return response()->json(["status" =>403, "success" => false, 'error' => 'error','message'=>  "Bank Information not Found"]);
            }
            else {
                $is_success = (new EmployeeDataService())->updateAnEmployeeSalaryPaymentMethodByEmployeeAutoId($employee->emp_auto_id,$request->payment_method);
                if($is_success){
                    // login user activities record
                    (new AuthenticationDataService())->InsertLoginUserActivity(15,2,$update_by,$employee->emp_auto_id,null);
                    return response()->json(["status" =>200, "success" => true, 'message'=>  "Successfully Updated"]);
                }else {
                    return response()->json(["status" =>403, "success" => false, 'error' => 'error','message'=>  "Update Operation Failed"]);
                }
            }


        }else{
                // upate all information
                $emp_auto_id = $request->id;

                $employee = (new EmployeeDataService())->getAnEmployeeInfoByEmpAutoId($emp_auto_id);
                if(! $employee){
                    Session::flash('error', 'Employee Not Found ');
                    return redirect()->back();
                }
                $request->project_id = (int) $request->projectStatus;
                $request->emp_auto_id = (int) $request->id;
                // dd($request->all());
                $update = (new EmployeeDataService())->updateAnEmployeeAllInformationWithSalaryDetails($request,$update_by);
                if ($employee->project_id != $request->projectStatus) {
                    $insert = (new EmployeeRelatedDataService())->assignEmployeeToNewProject($emp_auto_id, $request->project_id, $request['asign_date'], $request['asign_date'], $update_by,null);
                }
                if ($employee->job_status != $request->EmpStatus_id) {
                    (new EmployeeRelatedDataService())->insertEmployeeJobStatusUpdateRecord($emp_auto_id, Carbon::now(), $request['EmpStatus_id']);
                }

                (new EmployeeDataService())->updateEmployeeAsStaff((int)$emp_auto_id,$request->staff_employee);
                if ($update) {

                    $salary_amount = $request->basic_amount > 0 ? $request->basic_amount : $request->hourly_rent;
                    // login user activities record
                    (new AuthenticationDataService())->InsertLoginUserActivity(2,2, $update_by,$emp_auto_id,$salary_amount);

                    Session::flash('success', 'Successfully Updated');
                    return Redirect()->back();
                } else {
                    Session::flash('error', 'Update Operation Failed. Please Try Again');
                    return redirect()->back();
                }
        }
    }


  // === Update Employee Basic Information without file by edit button  ===
  public function updateEmployeeInformationData(Request $request)
  {

      $update = (new EmployeeDataService())->updateEmployeeAllInformation($request);
      (new EmployeeDataService())->updateAnEmployeeDetailsTableAllInformation($request->id ,$request->department_id,0 ,$request->religion,0 ,
        $request->country_phone_no ,$request->agency_id ,$request->gender ,$request->maritus_status ,$request->blood_group ,$request->present_address ,$request->ref_employee_id, $request->remarks,Auth::user()->id);


    if ($update) {
        // login user activities record
        (new AuthenticationDataService())->InsertLoginUserActivity(4,2,Auth::user()->id, $request->id,null);

      Session::flash('success', 'Successfully Updated');
      return Redirect()->route('employee-list');
    } else {
      Session::flash('error', 'Update Operation Failed, Please try again');
      return redirect()->back();
    }
  }

  // === Upload Employee File/Image ===

   public function updateEmployeeUploadedFileImage(Request $request)
  {

    $emp_auto_id = $request->emp_auto_id;
    $anEmployee = (new EmployeeDataService())->getAnEmployeeInfoWithSalaryDetailsByEmpAutoIdForEditEmployeeInformation($emp_auto_id);
    if(is_null($anEmployee)){
       Session::flash('error', 'Employee Not Found');
      return redirect()->back();
    }

    $update = false;
    // profile photo
    if ($request->hasFile('profile_photo')) {
      $file = $request->file('profile_photo');
      $uplodedPath =  (new  UploadDownloadController())->uploadEmployeeProfilePhoto($file, $anEmployee->profile_photo);
      $update = (new EmployeeDataService())->updateEmployeeUploadedFileDbPath($emp_auto_id, $uplodedPath, 'profile_photo');
    }
    // pasfort_photo upload
    if ($request->hasFile('pasfort_photo')) {
      $file = $request->file('pasfort_photo');
      $uplodedPath =  (new  UploadDownloadController())->uploadEmployeePassportFile($file, $anEmployee->pasfort_photo);
      $update = (new EmployeeDataService())->updateEmployeeUploadedFileDbPath($emp_auto_id, $uplodedPath, 'pasfort_photo');
    }
    // Iqama File
    if ($request->hasFile('akama_photo')) {
      $file = $request->file('akama_photo');
      $uplodedPath =  (new  UploadDownloadController())->uploadEmployeeIqamaFile($file, $anEmployee->akama_photo);
      $update = (new EmployeeDataService())->updateEmployeeUploadedFileDbPath($emp_auto_id, $uplodedPath, 'akama_photo');
    }

     // covid_certificate
     if ($request->hasFile('covid_certificate')) {
      $file = $request->file('covid_certificate');
      $uplodedPath =  (new  UploadDownloadController())->uploadEmployeeCOVIDCertificateFile($file, $anEmployee->covid_certificate);
      $update = (new EmployeeDataService())->updateEmployeeUploadedFileDbPath($emp_auto_id, $uplodedPath, 'covid_certificate');
    }

    // Medical Report
    if ($request->hasFile('medical_report')) {
      $file = $request->file('medical_report');
      $uplodedPath =  (new  UploadDownloadController())->uploadEmployeeMedicalReportFile($file, $anEmployee->medical_report);
      $update = (new EmployeeDataService())->updateEmployeeUploadedFileDbPath($emp_auto_id, $uplodedPath, 'medical_report');
    }


    // appoint letter update
    if ($request->hasFile('appoint_latter')) {
      $file = $request->file('appoint_latter');
      $uplodedPath =  (new  UploadDownloadController())->uploadEmployeeAppointmentLetterFile($file, $anEmployee->employee_appoint_latter);
      $update =  (new EmployeeDataService())->updateEmployeeUploadedFileDbPath($emp_auto_id, $uplodedPath, 'employee_appoint_latter');
    }

    if ($update) {

     // login user activities record
     (new AuthenticationDataService())->InsertLoginUserActivity(23,2, Auth::user()->id,$emp_auto_id,null);


      Session::flash('success_update_image', 'value');
      return Redirect()->route('employee-list');
    } else {
      Session::flash('error', 'value');
      return redirect()->back();
    }
  }


  // === Updated Employee Job Status ===
  public function approvalOfNewInsertedEmployees(Request $request)
  {
       try {
            $emp_auto_ids = $request->emp_auto_ids;
            foreach($emp_auto_ids as $id){

                (new EmployeeDataService())->approvalOfInsertedNewEmployee($id, 1,Auth::user()->id); //  1 = Active Employee
                $emp = (new EmployeeDataService())->getAnEmployeeInformationWithAllReferenceTableByEmpAutoId($id);
                if($emp){
                    (new EmployeeDataService())->insertAnEmployeeSalaryUpdateHistory(
                            $emp->emp_auto_id,  $emp->basic_amount,    $emp->basic_hours ,
                            $emp->house_rent,  $emp->hourly_rent, $emp->mobile_allowance,
                            $emp->medical_allowance,  $emp->local_travel_allowance, $emp->conveyance_allowance,
                            $emp->food_allowance, $emp->cpf_contribution,
                            $emp->increment_amount,   $emp->saudi_tax,
                            $emp->others1, $emp->hourly_employee == 1 ? 1:0,
                            $emp->payment_method != null ? $emp->payment_method : 'Cash', Auth::user()->id,
                        );
                }

            }
            return response()->json(['status' => 200, 'success' => true, 'message' => 'Successfully Approved', 'data' => $emp_auto_ids]);
        } catch (\Exception $e) {
            return response()->json(['status' => 404, 'success' => false, 'message' => 'Update Operation Failed. Please Try Again', 'error' => $e->getMessage()]);
        }
  }


  // display employee salary summary report
  public function createAnEmployeeSalarySummaryReport(Request $request)
  {

    $employee_id = $request->emp_id;

    $employee_id = $request->emp_id;
    $employee = (new EmployeeDataService())->getAnyJobStatusEmployeeWithSalaryDetailsByEmpId($employee_id);
    if ($employee == null) {
      $employee = (new EmployeeDataService())->getAnyJobStatusEmployeeWithSalaryDetailsByEmpIqamaNo($employee_id);
    }


     if($employee == null)
     {
        Session::flash('not_found', 'Employee Not Found');
        return redirect()->back();
     }

     if($request->emp_report_type == 1){
        // Slary Summary Report
        return $this->processAndDisplayAnEmployeeSalarySummaryReport($employee,null);
     }
    else if ($request->emp_report_type == 2) {
        // Salary Details Report
        return $this->processAndDisplayAnEmployeeSalarySummaryDetailsReport($employee,null);
    }else if ($request->emp_report_type == 3) {
       // Salary Report for Employee Copy
        return $this->processAndDisplayAnEmployeeSummaryReportForEmployeeCopy($employee,null);
    }
    else if ($request->emp_report_type == 4) {
           // Working project History
            return $this->processAndDisplayAnEmployeeWorkingPrjectHisotry($employee);
    }
    else  {
      Session::flash('not_found', 'Operation Failed');
      return redirect()->back();
    }
  }

  private function processAndDisplayAnEmployeeSalarySummaryReport($employee,$year){

    $company = (new CompanyProfileController())->findCompanry();
    $unpaid_salary_records =(new SalaryProcessDataService())->getAnEmployeeSalaryRecordsByPaidUnpaidStatus($employee->emp_auto_id,null,0);
    $totalUnPaidSalaryAmount = (new SalaryProcessDataService())->getAnEmployeeTotalUnPaidSalary($employee->emp_auto_id, null);

    // Advance Collection
    $toal_iqama_expense_deduction_from_salary = (new SalaryProcessDataService())->getTotalAmountOfIqamaExpenseDeductionFromSalary($employee->emp_auto_id,null);
    $total_other_advace_deduction_from_salary =(new SalaryProcessDataService())->getTotalAmountOfOtherAdvanceDeductionFromSalary($employee->emp_auto_id,null);
    $cashReceiveTotalPaidAmount =  (new EmployeeAdvanceDataService())->getAnEmployeeAdvancePaidTotalAmount($employee->emp_auto_id, null, 100);
    // 100 = Cash Received By Employee

    // Advance Give to Employee
    $iqamaExpenseAllRecords = (new EmployeeAdvanceDataService())->getAnEmployeeIqamaAnnualExpenseAllRecords($employee->emp_auto_id );

    $iqamaRenewalTotalExpence = (new EmployeeAdvanceDataService())->getAnEmployeeIqamaTotalCost($employee->emp_auto_id);
    $otherAdvanceTotalAmount = (new EmployeeAdvanceDataService())->getAnEmployeeOthersAdvanceTotalAmount($employee->emp_auto_id, null , 0); // 0 mean other type addvance taken by employee

    $toal_CPF_contribution_from_salary =  (new SalaryProcessDataService())->getTotalAmountOfCPFContributionFromSalary($employee->emp_auto_id,null);
    $totalSaudiTax = (new SalaryProcessDataService())->getAnEmployeeTotalAmountOfSautiTaxDeductionFromSalaryTotalAdvance($employee->emp_auto_id,null);
    $empl_last_activity = (new EmpActivityDataService())->getAnEmployeeLastActivityComments($employee->emp_auto_id);

      return view(
        'admin.report.employee_summary.employee_salary_summary',
        compact('unpaid_salary_records','toal_CPF_contribution_from_salary', 'toal_iqama_expense_deduction_from_salary',
        'totalSaudiTax', 'totalUnPaidSalaryAmount', 'iqamaRenewalTotalExpence', 'cashReceiveTotalPaidAmount','total_other_advace_deduction_from_salary',
        'otherAdvanceTotalAmount', 'employee', 'company','empl_last_activity')
      );
  }
  private function processAndDisplayAnEmployeeSalarySummaryDetailsReport($employee,$year)
  {
      $company = (new CompanyProfileController())->findCompanry();
      $salary_records =(new SalaryProcessDataService())->getAnEmployeeSalaryRecordsByPaidUnpaidStatus($employee->emp_auto_id,null,null);
    //  dd($salary_records);
      $totalUnPaidSalaryAmount = (new SalaryProcessDataService())->getAnEmployeeTotalUnPaidSalary($employee->emp_auto_id, null);

      // Advance Collection
      $toal_iqama_expense_deduction_from_salary = (new SalaryProcessDataService())->getTotalAmountOfIqamaExpenseDeductionFromSalary($employee->emp_auto_id,null);
      $total_other_advace_deduction_from_salary =(new SalaryProcessDataService())->getTotalAmountOfOtherAdvanceDeductionFromSalary($employee->emp_auto_id,null);
      $cashReceiveTotalPaidAmount =  (new EmployeeAdvanceDataService())->getAnEmployeeAdvancePaidTotalAmount($employee->emp_auto_id, null, 100);
      // 100 = Cash Received By Employee

      // Advance Give to Employee
      $iqamaExpenseAllRecords = (new EmployeeAdvanceDataService())->getAnEmployeeIqamaAnnualExpenseSelfAndCompanyAllRecords($employee->emp_auto_id );
      // getAnEmployeeIqamaAnnualExpenseAllRecords($employee->emp_auto_id );

      $iqamaRenewalTotalExpence = (new EmployeeAdvanceDataService())->getAnEmployeeIqamaTotalCost($employee->emp_auto_id);
      $otherAdvanceTotalAmount = (new EmployeeAdvanceDataService())->getAnEmployeeOthersAdvanceTotalAmount($employee->emp_auto_id, null , 0); // 0 mean other type addvance taken by employee

      $toal_CPF_contribution_from_salary =  (new SalaryProcessDataService())->getTotalAmountOfCPFContributionFromSalary($employee->emp_auto_id,null);
      $totalSaudiTax = (new SalaryProcessDataService())->getAnEmployeeTotalAmountOfSautiTaxDeductionFromSalaryTotalAdvance($employee->emp_auto_id,null);
      $empl_last_activity = (new EmpActivityDataService())->getAnEmployeeLastActivityComments($employee->emp_auto_id);

      return view('admin.report.employee_summary.emp_summary_details', compact('totalUnPaidSalaryAmount','toal_iqama_expense_deduction_from_salary',
      'salary_records',  'cashReceiveTotalPaidAmount','otherAdvanceTotalAmount','total_other_advace_deduction_from_salary','toal_CPF_contribution_from_salary',
      'totalSaudiTax', 'company','iqamaRenewalTotalExpence', 'employee', 'iqamaExpenseAllRecords','empl_last_activity'));

  }

  private function processAndDisplayAnEmployeeSummaryReportForEmployeeCopy($employee,$year)
  {


      $company = (new CompanyProfileController())->findCompanry();
      $unpaid_salary_records =(new SalaryProcessDataService())->getAnEmployeeSalaryRecordsByPaidUnpaidStatus($employee->emp_auto_id,null,0);
      $totalUnPaidSalaryAmount = (new SalaryProcessDataService())->getAnEmployeeTotalUnPaidSalary($employee->emp_auto_id, null);

      // Advance Collection
      $toal_iqama_expense_deduction_from_salary = (new SalaryProcessDataService())->getTotalAmountOfIqamaExpenseDeductionFromSalary($employee->emp_auto_id,null);
      $total_other_advace_deduction_from_salary =(new SalaryProcessDataService())->getTotalAmountOfOtherAdvanceDeductionFromSalary($employee->emp_auto_id,null);
      $cashReceiveTotalPaidAmount =  (new EmployeeAdvanceDataService())->getAnEmployeeAdvancePaidTotalAmount($employee->emp_auto_id, null, 100);
      // 100 = Cash Received By Employee

      // Advance Give to Employee
      $iqamaExpenseAllRecords = (new EmployeeAdvanceDataService())->getAnEmployeeIqamaAnnualExpenseAllRecords($employee->emp_auto_id );

      $iqamaRenewalTotalExpence = (new EmployeeAdvanceDataService())->getAnEmployeeIqamaTotalCost($employee->emp_auto_id);
      $otherAdvanceTotalAmount = (new EmployeeAdvanceDataService())->getAnEmployeeOthersAdvanceTotalAmount($employee->emp_auto_id, null , 0); // 0 mean other type addvance taken by employee

      $toal_CPF_contribution_from_salary =  (new SalaryProcessDataService())->getTotalAmountOfCPFContributionFromSalary($employee->emp_auto_id,null);
      $totalSaudiTax = (new SalaryProcessDataService())->getAnEmployeeTotalAmountOfSautiTaxDeductionFromSalaryTotalAdvance($employee->emp_auto_id,null);

      return view('admin.report.employee_summary.summary_report_emp_copy', compact('totalUnPaidSalaryAmount','toal_iqama_expense_deduction_from_salary', 'unpaid_salary_records',  'cashReceiveTotalPaidAmount','otherAdvanceTotalAmount','total_other_advace_deduction_from_salary','toal_CPF_contribution_from_salary','totalSaudiTax', 'company','iqamaRenewalTotalExpence', 'employee', 'iqamaExpenseAllRecords'));

  }

  // ======== Employee Working Project Hisotry=========
    private function processAndDisplayAnEmployeeWorkingPrjectHisotry($employee_info)
    {
        $company = (new CompanyDataService())->findCompanryProfile();
        $employeeInfos = (new EmployeeRelatedDataService())->getAnEmployeeWorkingProjectHisotry($employee_info->emp_auto_id );
        return view('admin.report.employee_summary.emp_work_project_history_report', compact('company', 'employeeInfos'));
    }


   // Project wise Employee List Base on Salary Report Request

  public function  showEmployeeListReportWithSalaryMonthAndProjectWiseEmployeeReport(Request $request){

     if ($request->report_format == 2) // excell format
    {
      return (new ExcelExportController())->exportEmployeeInformationByProjectSponserJobStatusEmpTradeEmpType(
        $request->proj_id,
        $request->spons_id,
        $request->catg_id,
        null,
        null
      );
    }

    if ($request->project_id == null) {
      $report_title = "All Project";
    } else {
       $report_title = "" . (new EmployeeRelatedDataService())->getProjectNameByProjectId($request->project_id);
    }
    $company = (new CompanyProfileController())->findCompanry();
    $employee =  (new EmployeeDataService())->exportEmployeeInformationByProjectSponserEmpTradeSalaryMonthAndYear($request->project_id,$request->sponsor_id,null,null,null,$request->month,$request->year);
    return view('admin.employee-info.project_wise.project_wise_all_report', compact('employee', 'company', 'report_title'));

  }

  // project wise employee list report
    public function projectWiseEmployeeListProcess(Request $request)
  {

      $company = (new CompanyProfileController())->findCompanry();

      if ($request->report_format == 2) // excell format
      {

        return (new ExcelExportController())->exportEmployeeInformationByProjectSponserJobStatusEmpTradeEmpType(
          $request->proj_id,
          $request->spons_id,
          $request->catg_id,
          $request->job_status,
          $request->emp_type_id
        );
      }

      $project = "All Project";
      if ($request->proj_id != null) {
        $project = (new EmployeeRelatedDataService())->getProjectNameByProjectId($request->proj_id);
      }

        $employee = (new EmployeeDataService())->getEmployeeInfoWithSalaryDetailsReportByProjectSponorTradeAndJobStatus( $request->proj_id,
        $request->spons_id,
        $request->catg_id,
        $request->job_status);

      $report_title ="";
      if ($employee->count() > 0) {
        return view('admin.employee-info.project_wise.report', compact('employee', 'company', 'project','report_title'));
      } else {
        Session::flash('error', 'value');
        return redirect()->back();
      }



  }

   // HR Related Employee Reports Processing FORM
    public function loadHRRelatedEmployeeReportForm()
    {

        $projects = (new ProjectDataService())->getAllActiveProjectListForDropdown();
         // (new EmployeeRelatedDataService())->getAllProjectInfoForDropdown();
        $sponser = (new EmployeeRelatedDataService())->getAllSponserInfoForDropdown();
        $category = (new EmployeeRelatedDataService())->getEmpAllCategoryInfoForDropdown();
        $jobStatus = [];// JobStatusEnum::cases();
        $emp_types = (new EmployeeRelatedDataService())->getEmployeeTypeForDropdown();
        return view('admin.report.hr_section.hr_report_processing_form', compact('projects', 'sponser', 'category', 'jobStatus', 'emp_types'));
    }

     // HR Related Employee Reports Processing and Show
    public function processHRRelatedEmployeeReport(Request $request)
    {

        $company = (new CompanyProfileController())->findCompanry();

        if ($request->report_format == 2) // excell format
        {
            return (new ExcelExportController())->exportEmployeeInformationByProjectSponserJobStatusEmpTradeEmpType(
                $request->proj_id,
                $request->spons_id,
                $request->catg_id,
                $request->job_status,
                $request->emp_type_id
            );
        }

        $project = "All Project";
        if ($request->project_name_id != null) {
            $project = (new EmployeeRelatedDataService())->getProjectNameByProjectId($request->project_name_id);
        }else {
          $request->project_name_id = [];
        }

        // dd($request->all());
      //  $project_id_list = $request->project_name_id;
        $employee = (new EmployeeDataService())->getHREmployeesReporttByProjectSponorTradeAndJobStatus(
            $request->project_name_id, // list of project id
            $request->spons_id,
            $request->catg_id,
            $request->job_status
        );

        $report_title = "";
        if ($employee->count() > 0) {
            return view('admin.report.hr_section.project_wise_emp_report', compact('employee', 'company', 'project', 'report_title'));
        } else {
            Session::flash('error', 'Please Select Project Name');
            return redirect()->back();
        }
    }



 // Employee list Report base on Project and Employee Type Report
  public function projectAndEmployeTypeWiseEmployeeListReportRequest(Request $request){

        $project = "All Project";
        $project_id = $request->proj_id;

        if ($project_id != null) {
            $project = (new EmployeeRelatedDataService())->getProjectNameByProjectId($project_id);
        }
        $emp_type_id = $request->emp_type_id;

        $isHourly = NULL;

        if ($emp_type_id == null) {
            $report_title = "Employee Type All";

        } else if ($emp_type_id == -1) {
            $report_title = "Direct Employee(Basic Salary)";
            $emp_type_id = 1;
        } else if ($emp_type_id == 1) {
            $report_title = "Direct Employee(H)";
            $isHourly = true;
        } else if ($emp_type_id == 2) {
            $report_title = "Indirect Employee";
        }else if ($emp_type_id == 3) {
            $report_title = "All Basic Salary Employees";
            $isHourly = true;
        }
        $employee = (new EmployeeDataService())->getEmployeeInfoWithSalaryDetailsReportByProjectEmpTypeAndHourlyEmp($project_id, $emp_type_id, $isHourly);

        $company = (new CompanyProfileController())->findCompanry();

        if ($employee->count() > 0) {
            return view('admin.employee-info.project_wise.report', compact('employee', 'company', 'project', 'report_title'));
        } else {
            Session::flash('error', 'Employee Not Found');
            return redirect()->back();
        }

  }


  // project wise Employee list Iqama Expired Report

    public function projectWiseEmployeeListWithIqamaExpiredateProcess(Request $request)
    {
        $fromDate = Carbon::now();
        $company = (new CompanyProfileController())->findCompanry();
        $project = "All Project";
        if ($request->project_id != null) {
            $project = (new EmployeeRelatedDataService())->getProjectNameByProjectId($request->project_id);
            $noOfEmployeesInProject = (new EmployeeDataService())->countTotalEmployeesInAProject($request->project_id, 1);
        } else {
            $noOfEmployeesInProject = (new EmployeeDataService())->countTotalEmployees(1);
        }

        // Duration Wise Employee List
        if ($request->expire_durations == 1) { // Already Expired
            $toDate = $fromDate;
            $fromDate = Carbon::now()->subDays(400);
            $employee = (new EmployeeDataService())->getProjectWiseEmployeeListByIqamaExpireDate($request->project_id, $fromDate, $toDate, 1);
        } elseif ($request->expire_durations == 2) { // This Month From Today Expired

            $lastDateofMonth = Carbon::now()->endOfMonth()->toDateString();
            $employee = (new EmployeeDataService())->getProjectWiseEmployeeListByIqamaExpireDate($request->project_id, $fromDate, $lastDateofMonth, 1);

        } elseif ($request->expire_durations == 3) { // Next Month From Today Expired

            $lastDateofMonth = Carbon::now()->addMonths(1)->endOfMonth()->toDateString();
            $employee = (new EmployeeDataService())->getProjectWiseEmployeeListByIqamaExpireDate($request->project_id, $fromDate, $lastDateofMonth, 1);

        } elseif ($request->expire_durations == 4) { // After 3 Months From Today Expired

            $lastDateofMonth = Carbon::now()->addMonths(3)->endOfMonth()->toDateString();
            $employee = (new EmployeeDataService())->getProjectWiseEmployeeListByIqamaExpireDate($request->project_id, $fromDate, $lastDateofMonth, 1);
        } elseif ($request->expire_durations == 5) { // After 6 Months From Today Expired

            $lastDateofMonth = Carbon::now()->addMonths(6)->endOfMonth()->toDateString();
            $employee = (new EmployeeDataService())->getProjectWiseEmployeeListByIqamaExpireDate($request->project_id, $fromDate, $lastDateofMonth, 1);
        }elseif ($request->expire_durations == 6) { // After 12 Months From Today Expired

            $lastDateofMonth = Carbon::now()->addMonths(12)->endOfMonth()->toDateString();
            $employee = (new EmployeeDataService())->getProjectWiseEmployeeListByIqamaExpireDate($request->project_id, $fromDate, $lastDateofMonth, 1);
        }else {

            // After 24 Months From Today Expired
            $lastDateofMonth = Carbon::now()->addMonths(24)->endOfMonth()->toDateString();
            $employee = (new EmployeeDataService())->getProjectWiseEmployeeListByIqamaExpireDate($request->project_id, $fromDate, $lastDateofMonth, 1);
        }

        $noOfEmployeesIqamaValid = null ;// $noOfEmployeesInProject - count($employee);

        $report_title = "";
        if ($employee->count() > 0) {
            return view('admin.employee-info.project_wise.emp_list_iqama_expired', compact('employee', 'company', 'project', 'report_title', 'noOfEmployeesIqamaValid', 'noOfEmployeesInProject'));
        } else {
            Session::flash('error', 'Employee Not Found');
            return redirect()->back();
        }
    }


        //  Date Wise New Employee Insert Report
    public function getAllNewEmployeeInsertListDetailsInfoByDateToDateReport(Request $request){



         if ($request->from_date == null || $request->today == null) {
            Session::flash('error', 'Please select new employee inserted date');
            return redirect()->back();
        } else {
            $is_hourly = null;
            $emp_type = null;
            if($request->emp_type_id == 1){
                $is_hourly = null;
                $emp_type = 1;
            }
            else if($request->emp_type_id == 2){
                   $emp_type = 2;
                   $is_hourly = null;
            }
            else if($request->emp_type_id == 3){
                $is_hourly = null;
            }
            else if($request->emp_type_id == 4){
                $is_hourly = true;
                $emp_type = 1;
            }
            $employees = (new EmployeeDataService())->getAllNewEmployeeInsertListByDateToDateReport($request->from_date, $request->today,$emp_type,$is_hourly);
            $company = (new CompanyDataService())->findCompanryProfile();
            $report_title = [$request->from_date,$request->today];
            return view('admin.employee-info.date_wise_new_inserted_emp_list_report', compact('employees', 'company','report_title'));

        }


    }

    public function ProcessAndShowMultipleIDBaseEmployeeDetailsReport(Request $request){

        $allEmplId = explode(",", $request->multiple_employee_Id);
        $allEmplId = array_unique($allEmplId); // remove multiple same empl ID

        $employee = (new EmployeeDataService())->getEmployeeDetailsWithSalaryDetailsByMultipleIDReport($allEmplId);

        $company = (new CompanyProfileController())->findCompanry();
        $project ="-";
        $report_title = "Multiple Employee ID";
        if ($employee->count() > 0) {
            return view('admin.employee-info.project_wise.report', compact('employee', 'company', 'project', 'report_title'));
        } else {
            Session::flash('error', 'Employee Not Found');
            return redirect()->back();
        }

   }

   public function tradeWiseEmployeeListProcess(Request $request)
   {

    $catg_id = $request->catg_id;
    $emp_type_id = $request->emp_type_id;

    if ($emp_type_id == -1 && $catg_id == 0) {
      Session::flash('error', 'value');
      return redirect()->back();
    }
    $empTypeName = "All";
    $company = (new CompanyProfileController())->findCompanry();
    $tradeName =  "All";
    $employee = null;

    if ($emp_type_id == -1 && $catg_id >= 1) { // no type but trade
      $empTypeName = "All";
      $employee = (new EmployeeDataService())->getEmployeeListByEmpCategoryId($catg_id);
      $tradeName = EmployeeCategory::where('catg_id', $request->catg_id)->pluck('catg_name');
    } else if ($emp_type_id >= 0 && $catg_id == 0) {  // seleced type but no trade

      if ($emp_type_id == 0) {
        $empTypeName = "Direct Employee(Basic Salary)";
        $employee = (new EmployeeDataService())->getEmployeeListByCategoryIdEmpTypeAndHourlyEmp(-1, 1, null);
        // $employee = EmployeeInfo::where('emp_type_id', 1)->where('hourly_employee', null)->orderBy('employee_id', 'ASC')->get();
      } else if ($emp_type_id == 1) {

        $empTypeName = "Direct Employee(Hourly)";
        $employee = (new EmployeeDataService())->getEmployeeListByCategoryIdEmpTypeAndHourlyEmp(-1, 1, 1);
        // $employee = EmployeeInfo::where('emp_type_id', 1)->where('hourly_employee', 1)->orderBy('employee_id', 'ASC')->get();
      } else {
        $empTypeName = (new EmployeeRelatedDataService())->getAnEmployeeTypeName($request->emp_type_id);
        $employee = EmployeeInfo::where('emp_type_id', $emp_type_id)->orderBy('employee_id', 'ASC')->get();
      }
    } else {  // selected both menu

      $tradeName = EmployeeCategory::where('catg_id', $request->catg_id)->pluck('catg_name');

      if ($emp_type_id == 0) {
        $empTypeName = "Direct Employee(Basic Salary)";
        // $employee = EmployeeInfo::where('designation_id', $catg_id)->where('emp_type_id', 1)->where('hourly_employee', null)->orderBy('employee_id', 'ASC')->get();
        $employee = (new EmployeeDataService())->getEmployeeListByCategoryIdEmpTypeAndHourlyEmp($catg_id, 1, null);
      } else if ($emp_type_id == 1) {

        $empTypeName = "Direct Employee(Hourly)";
        $employee = (new EmployeeDataService())->getEmployeeListByCategoryIdEmpTypeAndHourlyEmp($catg_id, 1, 1);
        // $employee = EmployeeInfo::where('designation_id', $catg_id)->where('emp_type_id', 1)->where('hourly_employee', 1)->orderBy('employee_id', 'ASC')->get();
      } else {
        $empTypeName = (new EmployeeRelatedDataService())->getAnEmployeeTypeName($request->emp_type_id);
        $employee = (new EmployeeDataService())->getEmployeeListByCategoryIdEmpTypeAndHourlyEmp($catg_id, $emp_type_id, null);
        // $employee = EmployeeInfo::where('designation_id', $catg_id)->where('emp_type_id', $emp_type_id)->orderBy('employee_id', 'ASC')->get();
      }
    }

    if ($employee != NULL) {
      return view('admin.employee-info.trade_wise.report', compact('employee', 'company', 'tradeName', 'empTypeName'));
    } else {
      Session::flash('error', 'value');
      return redirect()->back();
    }
  }


  public function sponserWiseEmployeeListProcess(Request $request)
  {
    $company = (new CompanyProfileController())->findCompanry();
    $sponsor = Sponsor::where('spons_id', $request->spons_id)->pluck('spons_name');
    $employee = (new EmployeeDataService())->getEmployeeListWithProjectAndSponsor(0, $request->spons_id);
    // EmployeeInfo::where('sponsor_id', $request->spons_id)->orderBy('employee_id', 'ASC')->get();

    if ($employee != NULL) {
      return view('admin.employee-info.sponser_wise.report', compact('employee', 'company', 'sponsor'));
    } else {
      Session::flash('error', 'value');
      return redirect()->back();
    }
  }


  // Find An Employee for update all information with salary details
    public function findEmployeeForUpdate(Request $request)
    {

            $employee = (new EmployeeDataService())->searchingAnEmployeeInfoByMultitypeParameter($request->searchValue,$request->searchType,Auth::user()->branch_office_id);
            if(count($employee) == 0){
                return response()->json(["status" =>403, "success" => false, 'error' => 'error','message'=> "Employee Not Found"]);
            }
            else if(count($employee) >1){
                return response()->json(["status" =>403, "success" => false, 'error' => 'error','message'=>  "Multiple Employee Found"]);
            }
            $employee =  $employee[0];
            $allCountry =  (new EmployeeRelatedDataService())->getAllCountry();
            $allDistrict = (new DistrictController())->getAllDistrict();
            $allDivision = (new EmployeeRelatedDataService())->getDivisions(null);
            $allEmpType = (new EmployeeRelatedDataService())->getEmployeeTypeForDropdown();
            $allDepartment = (new EmployeeRelatedDataService())->getAllDepartment();
            $allSponsor = (new EmployeeRelatedDataService())->getAllSponserInfoForDropdown(Auth::user()->branch_office_id);
            $getAllProject = (new ProjectDataService())->getAllActiveProjectListForDropdown();
            $allEmployeeStatus = (new HelperController())->getEmployeeStatus();
            $designation = (new EmployeeRelatedDataService())->getEmpAllCategoryInfoForDropdown();
            $find_job_experience = EmpJobExperience::where('emp_id', $employee->emp_auto_id)->get();
            $find_emp_contact_person = EmpContactPerson::where('emp_id', $employee->emp_auto_id)->get();

            return json_encode([
                'success' => true,
                'status' => 200,
                'allCountry' => $allCountry,
                'allDistrict' => $allDistrict,
                'allSponsor' => $allSponsor,
                'allDepartment' => $allDepartment,
                'allEmpType' => $allEmpType,
                'allDivision' => $allDivision,
                'allEmployeeStatus' => $allEmployeeStatus,
                'getAllProject' => $getAllProject,
                'find_job_experience' => $find_job_experience,
                'find_emp_contact_person' => $find_emp_contact_person,
                'findEmployee' => $employee,
                'designation' => $designation,
            ]);

    }


  // ========###### employee list end #####=========

  public function updateAnEmployeeJobStatus(Request $request)
  {
    $emp_auto_id = $request->emp_auto_id;
    $emp_job_status = $request['empStatus'];
    $update = (new EmployeeDataService())->updateEmployeeJobStatus($emp_auto_id, $emp_job_status);
    (new EmployeeRelatedDataService())->insertEmployeeJobStatusUpdateRecord($emp_auto_id, Carbon::now(), $emp_job_status);

    if ($update) {
      Session::flash('success', 'Successfully Updated');
      return redirect()->back();
    } else {
      Session::flash('error', 'Please try again');
      return redirect()->back();
    }

  }


  public function udpateEmployeeWorkingProject(Request $request)
  {

    $emp_auto_id = $request->emp_auto_id;
    $project_id = $request->projectStatus;
    $assign_date = $request->date;

    if($project_id == null){
      Session::flash('error', 'Operation Failed, Please try again');
      return redirect()->back();
    }
    $update = (new EmployeeDataService())->updateEmployeeAssignedProject($emp_auto_id, $project_id);
    if ($update) {
      (new EmployeeRelatedDataService())->assignEmployeeToNewProject($emp_auto_id, $project_id,$assign_date, $assign_date, Auth::user()->id,null);
      Session::flash('success', 'Successfully Updated Project Info');
      return redirect()->back();
    } else {
      Session::flash('error', 'Operation Failed, Please try again');
      return redirect()->back();
    }

  }


    public function udpateEmployeeSponsorInformations(Request $request){

        $emp_auto_id = $request->emp_auto_id;
        $prev_spons_id = $request->emp_prev_sponsor_id;
        $curnt_spons_id = $request->emp_current_sponsor;

        if ($curnt_spons_id == null) {
            Session::flash('error', 'Operation Failed, Please try again');
            return redirect()->back();
        }

        $update = (new EmployeeDataService())->updateEmployeeSponsorInfo($emp_auto_id, $curnt_spons_id);
        if ($update) {
             // login user activities record
            (new AuthenticationDataService())->InsertLoginUserActivity(7,2, Auth::user()->id, $emp_auto_id,null);


            Session::flash('success', 'Successfully Updated Sponsor Info');
            return redirect()->back();
        } else {
            Session::flash('error', 'Operation Failed, Please try again');
            return redirect()->back();
        }
    }


   // Search EMployee by Emp ID and Update Employee Trade/Designation Or Employee Multi Expert Trade/Designation Update
    public function updateAnEmployeeDesignationAndMultipleTradeExpertness(Request $request)
    {
        $emp_auto_id = $request->emp_auto_id;
        $emplDesig_id = $request->emplyoeeDesignation;
        $empMultiDesignations = $request->empMultiExptDesignation;
        $empWorkActivityRating = $request->empWorkActivityRating;

        if( is_null($emp_auto_id) || ($emplDesig_id == null && $empMultiDesignations == null && $empWorkActivityRating == null)){
            Session::flash('error', 'Please Select Employee Trade Name');
            return redirect()->back();
        }else{
            if($emplDesig_id != null){
                (new EmployeeDataService())->updateEmployeeDesignationStatus($emp_auto_id, $emplDesig_id);
            }
            if($empWorkActivityRating != null){
              (new EmployeeDataService())->updateAnEmployeeWorkRatingInfo($request->emp_auto_id, $request->empWorkActivityRating);
            }

            if( $empMultiDesignations != null){
                foreach ($empMultiDesignations as $trade_id) {
                    (new EmployeeDataService())->insertAnEmpMultipleTradeExpertnessInformation($emp_auto_id, $trade_id, Auth::user()->id);
                }
            }

            // login user activities record
            (new AuthenticationDataService())->InsertLoginUserActivity(11,2, Auth::user()->id, $emp_auto_id,null);


            Session::flash('success', 'Successfully Updated');
            return redirect()->back();
        }

    }


    // update employee Agency and Reference Person Information
  public function updateEmployeeAgencyInformation(Request $request){

        $emp_auto_id = $request->emp_auto_id;
        $agency_id = $request->emplyoeeAgency;
        $ref_person_info = $request->ref_employee_id;
        $ref_contact_no = $request->ref_contact_no;
        $remarks = $request->remarks;

        if($agency_id != null &&  $emp_auto_id  != null){
             (new EmployeeDataService())->updateEmployeeAgencyInfo($emp_auto_id, $agency_id);

              // login user activities record
             (new AuthenticationDataService())->InsertLoginUserActivity(8,2, Auth::user()->id, $emp_auto_id,null);

        }
        if($ref_person_info != null &&  $emp_auto_id  != null ){
           (new EmployeeDataService())->updateAnEmployeeReferencePersonInformation($emp_auto_id, $ref_person_info,$ref_contact_no,$remarks);
        }

        // if ($update) {
            Session::flash('success', 'Successfully Updated');
            return redirect()->back();
        // } else {
        //     Session::flash('error', 'Please try again');
        // }
    }

    // Employee Compnay Information Update
    public function updateEmployeeCompanyInformation(Request $request){

        $emp_auto_id = $request->emp_auto_id;
        $company_id = $request->company_id;

        $update = (new EmployeeDataService())->updateEmployeeCompanyInfo($emp_auto_id, $company_id);
        if ($update) {
            Session::flash('success', 'Successfully Updated');
            return redirect()->back();
        } else {
            Session::flash('error', 'Please try again');
        }
    }

     // Update Employee Photo , Blood Group
    private function updateEmployeePhotoAndBloodGroup($request){
        $emp_auto_id = $request->input_emp_auto_id;
        $anEmployee = (new EmployeeDataService())->getAnEmployeeInfoByEmpAutoId($emp_auto_id);

        if ($request->hasFile('profile_photo')) {

            $file = $request->file('profile_photo');
            $uplodedPath = (new  UploadDownloadController())->uploadEmployeeProfilePhoto($file, $anEmployee->profile_photo);

            $update = (new EmployeeDataService())->updateEmployeeUploadedFileDbPath($emp_auto_id, $uplodedPath, 'profile_photo');

           }
        $uplodedPath = "";
        if ($request->hasFile('blood_group_paper')) {

            $file = $request->file('blood_group_paper');
            $uplodedPath = (new  UploadDownloadController())->uploadEmployeeBloodGroupPaper($file, $anEmployee->blood_group_file);

           }
        $update = (new EmployeeDataService())->updateAnEmployeeBloodGroupInfo($emp_auto_id,$request->blood_group, $uplodedPath);
          Session::flash('success', 'Successfully Updated');
          return redirect()->back();
    }


     // Update Employee AJEER file
    private function updateEmployeeAjeerDocument($request){
        $emp_auto_id = $request->input_emp_auto_id;
        $anEmployee = (new EmployeeDataService())->getAnEmployeeInfoByEmpAutoId($emp_auto_id);

        if ($anEmployee && $request->hasFile('ajeer_file')) {

            $file = $request->file('ajeer_file');
            $uplodedPath = (new  UploadDownloadController())->uploadEmployeeAjeerFile($file, $anEmployee->ajeer_file);
            $update = (new EmployeeDataService())->updateEmployeeUploadedFileDbPath($emp_auto_id, $uplodedPath, 'ajeer_file');
            //ALTER TABLE `employee_infos` ADD `ajeer_file` VARCHAR(256) NULL AFTER `branch_office_id`;

        }else {
            Session::flash('error', 'Employee Not Found or Ajeer File Not Uploaded');
            return redirect()->back();
        }

        Session::flash('success', 'Ajeer Document Successfully Uploaded');
        return redirect()->back();
    }

   // Employee Iqama and Passport number and file update
    public function updateEmployeeIqamaInformations(Request $request)
    {
        try{

            if($request->operation_type == 2){
                // blood group, bg paper and photo
                return  $this->updateEmployeePhotoAndBloodGroup($request);
            }
            else if($request->operation_type == 3){
                // ajeer document upload
                return  $this->updateEmployeeAjeerDocument($request);
            }
            else{


                $emp_auto_id = $request->emp_auto_id;
                $iqamaNo = $request->akama_no_up;
                $passport = $request->passport_no_up;
                $isThisIqamaExist = (new EmployeeDataService())->getAnEmployeeInfoByEmpIqamaNo($iqamaNo);
                $isThisPassportExist = (new EmployeeDataService())->getAnEmployeeInfoByEmpPassportNo($passport);

                if ($isThisIqamaExist) {
                    $isThisIqamaExist = true;
                }else {
                    $isThisIqamaExist = false;
                    $anEmployee = (new EmployeeDataService())->getAnEmployeeInfoByEmpAutoId($emp_auto_id);
                    if ($request->hasFile('akama_photo')) {
                        $file = $request->file('akama_photo');
                        $uplodedPath = (new  UploadDownloadController())->uploadEmployeeIqamaFile($file, $anEmployee->akama_photo);
                        $update = (new EmployeeDataService())->updateEmployeeUploadedFileDbPath($emp_auto_id, $uplodedPath, 'akama_photo');
                        Session::flash('success', 'Successfully Updated');
                    }
                    if ($iqamaNo != null) {

                        (new EmployeeDataService())->updateAnEmployeeIqamaNumberAndExpiredateFromStatusOption($emp_auto_id, $iqamaNo, $request->akama_expire);
                         // login user activities record
                        (new AuthenticationDataService())->InsertLoginUserActivity(5,2, Auth::user()->id, $emp_auto_id,null);

                    }
                }
                if ($isThisPassportExist) {
                    $isThisPassportExist = true;
                }else{
                    $isThisPassportExist = false;
                    if ($request->hasFile('passport_file')) {
                        $file1 = $request->file('passport_file');
                        $uplodedPath1 = (new  UploadDownloadController())->uploadEmployeePassportFile($file1, $anEmployee->pasfort_photo);
                        $update = (new EmployeeDataService())->updateEmployeeUploadedFileDbPath($emp_auto_id, $uplodedPath1, 'pasfort_photo');
                        Session::flash('success', 'Successfully Updated');
                    }
                    if ($passport != null) {
                        (new EmployeeDataService())->updateEmployeePassportInformation($emp_auto_id,   $passport);
                          // login user activities record
                        (new AuthenticationDataService())->InsertLoginUserActivity(6,2, Auth::user()->id, $emp_auto_id,null);

                    }
                }
                if($isThisIqamaExist && $isThisPassportExist){
                    Session::flash('error', 'Passport and Iqama Number both are Exist ');
                    return redirect()->back();
                }
                else if($isThisIqamaExist){
                    Session::flash('error', 'Iqama Number Already Exist');
                    return redirect()->back();
                }else if($isThisPassportExist){
                    Session::flash('error', 'Passport Number Already Exist');
                    return redirect()->back();
                }
                Session::flash('success', 'Successfully Updated');
                return redirect()->back();
            }

        }catch(Exception $ex){

            Session::flash('error','System Operation Failed');
            return redirect()->back();
        }

    }


     // Employee Accomodation Information Update
    public function updateEmployeeAccomodationInformations(Request $request){

        if (is_null($request->email) && is_null($request->mobile_no_up) && is_null($request->phone_no_up) &&  is_null($request->country_phone_no)  && is_null($request->emplyoeeAccommodationBuiling)) {
            Session::flash('error', 'Please Input Employee Contact Mobile Information');
            return redirect()->back();
        }
         if (is_null($request->email) == false ){
            (new EmployeeDataService())->updateAnEmployeeEmailAddress($request->emp_auto_id, $request->email);
        }

        if (is_null($request->mobile_no_up) == false  || is_null($request->phone_no_up) == false ){
             (new EmployeeDataService())->updateAnEmployeeMobileNumber($request->emp_auto_id, $request->mobile_no_up, $request->phone_no_up);

        }
        if (is_null($request->country_phone_no) == false ){
            (new EmployeeDataService())->updateAnEmployeeHomeCountryContactNumber($request->emp_auto_id, $request->country_phone_no);

        }

        if (is_null($request->emplyoeeAccommodationBuiling) == false ){
            (new EmployeeDataService())->updateAnEmployeeAccommodationVilla($request->emp_auto_id, $request->emplyoeeAccommodationBuiling);
        }

            Session::flash('success', 'Successfully Updated');
            return redirect()->back();
    }

        // Employee Activity Remarks/Comments Add
    public function updateEmployeeActivityRemarks(Request $request){


        $update = false;
        if ($request->emp_act_remarks != null) {
            $update =  (new EmployeeDataService())->updateAnEmployeeAdministationActivityRemarks(
                $request->emp_auto_id,
                $request->emp_act_remarks
            );
        }
        if ($request->gosi_number != null) {
           $update =  (new EmployeeDataService())->updateAnEmployeeGOSINumber(
                $request->emp_auto_id,
                $request->gosi_number
            );
        }

        if ($update) {
            return response()->json([
                'success' => true,
                'status' => 200,
                'message' => 'Successfully Updated .',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'status' => 404,
                'message' => 'Something Wrong, Please Try Again.',
            ]);
        }
    }

  public function preRelease(Request $request)
  {
    $all = (new EmployeeDataService())->getAllEmployeesInformation(-1, 4);
    // 5 = job status release
    return view('admin.salary-generate.pending-salary', compact('pendingSalary'));
  }

  public function preReleaseUpdateStatus($id)
  {

    $update = (new EmployeeDataService())->updateEmployeeJobStatus($id, 5);
    $all = (new EmployeeDataService())->getAllEmployeesInformation(-1, 3);
    // 4 = prerelease job status

    if ($update) {
      Session::flash('success', 'successfuly update employee status');
      return redirect()->back();
    } else {
      Session::flash('error', 'Please try again');
      return redirect()->back();
    }
  }



    /*
    |--------------------------------------------------------------------------
    | EMPLOYEE WORK SHIFT STATUS PROCESS
    |--------------------------------------------------------------------------
    */





  public function loadEmployeeWorkingShiftStatusUpdateUI(){

    $project =  (new ProjectDataService())->getLoginUserAssingedProjectForDropdownList(Auth::user()->id);

    return view('admin.employee-in-out.employee-work-shift-status', compact('project'));

  }

    // Signle Employee WOrking Shift Status Update Ajax Request
    public function updateEmployeeWorkShiftingDataChangeRequest(Request $request)
    {
        try{
            $isUpdate = false;
            $anEmp = (new EmployeeDataService())->getAnEmployeeInfoTableDataByEmployeeIdAndBranchOfficeId($request->employee_id,Auth::user()->branch_office_id);
            if ($anEmp) {
                $isUpdate = (new EmployeeDataService())->updateEmployeeWorkingShiftStatusByEmployeeAutoId($anEmp->emp_auto_id, $request->working_shift);
                return response()->json(['status' => 200,'success'=>true, 'error' =>null,'message'=> "Successfully Updated"]);
            }else{
                return response()->json(['status' => 404,'success'=>false, 'error' => 'error','message'=> "Employee Not Found"]);
            }

        }catch(Exception $ex){
            return response()->json(['status' => 404,'success'=>false, 'error' => 'error','message'=> "System Operation Failed, Please Reload and try Again"]);
        }
    }

    // Employee Shift Status Update Ui Data Ajax Request
    public function getProjectWiseActiveEmployeeListForEmployeeShiftStatusAjaxRequest(Request $request)
    {
        try{
              $projectActiveEmpList = (new EmployeeDataService())->getEmployeeInfoWithSalaryDetailsByProjectAndJobStatusForShiftUpdate($request->project_id, 1,Auth::user()->branch_office_id,$request->pagination);
              $total_emp_day_shift = (new EmployeeDataService())->countDayShiftWorkingActiveEmployeesInAProject($request->project_id,Auth::user()->branch_office_id);
              $total_emp_night_shift = (new EmployeeDataService())->countNightShiftWorkingActiveEmployeesInAProject($request->project_id,Auth::user()->branch_office_id);

               if (count($projectActiveEmpList) > 0) {
                  return response()->json(["status"=> 200,"success"=>true,"emp_day_shift" =>$total_emp_day_shift,"emp_night_shift" => $total_emp_night_shift,
                "employee_list" => $projectActiveEmpList]);
              } else {
                  return response()->json(["status"=> 204,"success"=>false,'error' => "Data Not Found!"]);
              }
        }catch(Exception $ex){
            return response()->json(['status' => 404,'success'=>false, 'error' => 'Data Not Found!','message'=> "Data Not Found!"]);
        }


    }

  public function employeeShiftStatusUpdateRequest(Request $request){



    if(!$request->has('emp_auto_id')){
      Session::flash('error', 'Operation Failed, Please Try Again.');
      return redirect()->back();
    }
    $allEmpList = $request->emp_auto_id;
    $update = false;
    foreach ($allEmpList as $emp_auto_id) {
        if ( $request->has('emp_work_night_shift-' . $emp_auto_id) ) {
            $update = (new EmployeeDataService())->updateEmployeeWorkingShiftStatusByEmployeeAutoId($emp_auto_id,1); // 1 night shift
           // employeeWorkingShiftUpdateWithNight($emp_auto_id);
        } else {
            $update = (new EmployeeDataService())->updateEmployeeWorkingShiftStatusByEmployeeAutoId($emp_auto_id,0); // 0 day shift
        }
    }
    if ($update) {
        Session::flash('success', 'Successfully Updated Employee Working Shift Status');
        return redirect()->route('employee.shift-status-update-ui');
    } else {
        Session::flash('error', 'Some Operation Failed, Please Try Again.');
        return redirect()->back();
    }

  }



  /*
  |--------------------------------------------------------------------------
  |  BLADE OPERATION
  |--------------------------------------------------------------------------
  */
  public function index()
  {


   $total_active_emp = (new EmployeeDataService())->countTotalNumberOfEmployeesInABranchOffice(1,Auth::user()->branch_office_id); // 1= active employee
    return view('admin.employee-info.index', compact('total_active_emp'));
  }
  public function searchEmployeeByProjectSponerAndEmpID(Request $request)
  {

    if ($request->employee_id == null) {
      return $this->index();
    }
    $projectlist = (new ProjectDataService())->getAllActiveProjectListForDropdown();
     // (new EmployeeRelatedDataService())->getAllProjectInfoForDropdown();
    $sponserList = (new EmployeeRelatedDataService())->getAllSponserInfoForDropdown(Auth::user()->branch_office_id);
    $anEmp = (new EmployeeDataService())->getAnEmployeeInfoWithSalaryDetailsJoinQueryByEmpId($request->employee_id,null);

    $all = array();
    if ($anEmp != null) {
      $all[0] = $anEmp;
    }
    // $all =  (new EmployeeDataService())->getAllEmployeesInformation(10, JobStatusEnum::Active);
    // 2000 = page limit, 1 = active employee
    $allActive = (new EmployeeDataService())->countTotalEmployees(1); // 1 = active employee

    return view('admin.employee-info.index', compact('all', 'projectlist', 'sponserList', 'allActive'));
  }

  public function loadNewEmployeeApprovalPendingListWithUI()
  {

    $all = (new EmployeeDataService())->getListOfNewEmployeesThoseAreWaitingForApproval(30, 0,Auth::user()->branch_office_id);
    //dd($all[0]);
     // -1 = no limit, 0 = approval pending emlpoyee
    $empTypes = (new EmployeeRelatedDataService())->getEmployeeTypeForDropdown();

    return view('admin.employee-info.job-approve', compact('all','empTypes'));
  }




// Unapproved emp delete operation
public function destroy($id)
{
    try{
            $anemp = (new EmployeeDataService())->getAnEmployeeInfoByEmpAutoId($id);
            if ($anemp && $anemp->job_status == 0) {
                $isDeleted = (new EmployeeDataService())->deleteAnEmployeeWithServiceDetailsByAutoIdBeforeApproval($id);
                (new UploadDownloadController())->deleteAnEmplyoeeAllfilesBeforeApproval($anemp);
                return redirect()->back()->with('success', 'Employee  deleted successfully!');
            }
            return redirect()->back()->with('error', 'Failed to delete. Check laravel.log for details.');

    }catch(Exception $ex){
            return redirect()->back()->with('error', 'Failed to delete. Please try Again .'.$ex);
    }

}


  public function preReleaseList()
  {
    // $all = $this->getAllPrereles();
    $all = (new EmployeeDataService())->getAllEmployeesInformation(-1, 3);

    return view('admin.employee-info.pre-release', compact('all'));
  }

  public function releaseList()
  {
    //$all = $this->getAllRelease();
    $all = (new EmployeeDataService())->getAllEmployeesInformation(-1, 0);

    return view('admin.employee-info.release', compact('all'));
  }
 // Multtype parameter Base Employee Searching UI
  public function loadSearchingUIForAnEmployeeDetailsByMultiTypeParameter()
  {
    return view('admin.employee-info.search-emp');
  }

  public function searchEmpForUpdate()
  {

    return view('admin.employee-info.emp-update');
  }

    public function multipleEmployeeTransferForm()
  {
    $projects = (new ProjectDataService())->getAllActiveProjectListForDropdown(Auth::user()->branch_office_id);
    return view("admin.employee-info.employee-transfer", compact("projects"));
  }

  public function multipleEmployeeTransferFormSubmit(Request $request)
  {

    $emp_list = $request->emp_auto_id;
    $creator = Auth::user()->id;
    $asign_date = $request->asign_date;

     if ($request['assigned_project'] == null) {
        Session::flash('error', 'Please Select Project For Transfer  Employees!');
        return redirect()->back();
    } else {

        foreach ($emp_list as $emp_auto_id) {
          if ($request->has('emp_transfer_checkbox-' . $emp_auto_id)) {

            (new EmployeeDataService())->updateEmployeeAssignedProject($emp_auto_id,$request['assigned_project']);
            (new EmployeeRelatedDataService())->assignEmployeeToNewProject($emp_auto_id, $request['assigned_project'], $asign_date, $asign_date, $creator,$request->remarks);
          }
        }
    }

    Session::flash('success', 'Successfully Transfer to new Project');
    return redirect()->back();
  }

 // Load User Interface for Employee Trade , Accommodation, Rating, Project Update
  public function searchEmpStatus()
  {

        $empWorkRating = [];// EmpWorkActivityRatingEnum::cases();
        $designationList =  (new EmployeeRelatedDataService())->getEmpAllCategoryInfoForDropdown();

        return view('admin.employee-info.search-status', compact('empWorkRating', 'designationList'));
  }


  public function getReligion()
  {
    return $all = Religion::get();
  }



  public function add()
  {


    $designationList =  (new EmployeeRelatedDataService())->getEmpAllCategoryInfoForDropdown();
    $countryList =  (new EmployeeRelatedDataService())->getAllCountry();
    $empTypes = (new EmployeeRelatedDataService())->getAllEmployeeType();
    $allDepart = (new EmployeeRelatedDataService())->getAllDepartment();
    $empIdGeneret = (new EmployeeDataService())->generateEmployeeId();
    $relig = $this->getReligion();
    $proj = (new ProjectDataService())->getAllActiveProjectListForDropdown(Auth::user()->branch_office_id);
    $sponsor = (new EmployeeRelatedDataService())->getAllSponserInfoForDropdown(Auth::user()->branch_office_id);
    $agencies = (new EmployeeRelatedDataService())->getAgencyInformationForDropdownList();
    $accomdOfficeBuilding = (new AccommodationDataService())->getAllActiveOfficeBuildingNameIdAndCityForDropdownList();
        // 2000 = page limit, 1 = active employee
     return view('admin.employee-info.add', compact('designationList', 'sponsor', 'proj', 'relig', 'countryList', 'empTypes', 'allDepart', 'empIdGeneret', 'agencies', 'accomdOfficeBuilding'));
  }

  public function addSalaryDetails($emp_auto_id)
  {
    $employee = (new EmployeeDataService())->getAnEmployeeInfoByEmpAutoId($emp_auto_id);
    return view('admin.employee-info.add-salary-info', compact('emp_auto_id', 'employee'));
  }

  public function edit($emp_auto_id)
  {
    Cache::flush();
    cache()->flush();

       //  $edit = (new EmployeeDataService())->getAnEmployeeInfoWithSalaryDetailsByEmpAutoIdForEditEmployeeInformation($emp_auto_id);
         $edit = (new EmployeeDataService())->getAnEmployeeInformationWithAllReferenceTableByEmpAutoId($emp_auto_id);
        if($edit == null){
           return 'Update Permission Denied, You Selected an Inactive Employee';
        }

    $designationList =  (new EmployeeRelatedDataService())->getEmpAllCategoryInfoForDropdown();
    $countryList =  (new EmployeeRelatedDataService())->getAllCountry();
    $empTypes = (new EmployeeRelatedDataService())->getEmployeeTypeForDropdown();
    $allDepart = (new EmployeeRelatedDataService())->getAllDepartment();
    $agencies = (new EmployeeRelatedDataService())->getAgencyInformationForDropdownList();
    $relig = $this->getReligion();
    $proj = (new ProjectDataService())->getAllActiveProjectListForDropdown();
    // (new EmployeeRelatedDataService())->getAllProjectInfoForDropdown();
    $sponsor = (new EmployeeRelatedDataService())->getAllSponserInfoForDropdown(Auth::user()->branch_office_id);



    return view('admin.employee-info.edit', compact('designationList', 'sponsor','agencies', 'edit', 'proj', 'relig', 'countryList', 'empTypes', 'allDepart'));
  }

  public function view($emp_auto_id)
  {
    $view = (new EmployeeDataService())->getAnEmployeeInfoByEmpAutoId($emp_auto_id);

    return view('admin.employee-info.view', compact('view'));
  }

  /* ==================== Employee Salary Summary ==================== */
  //  UI for viewing Emp. Salary Summary and Details Report
  public function EmployeeSummary()
  {
    return view('admin.employee-info.salary-summary');
  }

  /* ==================== Project Wise Employee List Report UI form ==================== */
  public function projectWiseEmployeeList()
  {

    $projects = (new ProjectDataService())->getAllActiveProjectListForDropdown();
    // (new EmployeeRelatedDataService())->getAllProjectInfoForDropdown();

    $sponser = (new EmployeeRelatedDataService())->getAllSponserInfoForDropdown(Auth::user()->branch_office_id);

    $categoryOBJ = new EmpCategoryController();
    $category = $categoryOBJ->getAllCategory();

    $jobStatus = JobStatus::all();

    $emp_types = (new EmployeeRelatedDataService())->getAllEmployeeType();


    return view('admin.employee-info.project_wise.employee_report_processing_form', compact('projects', 'sponser', 'category', 'jobStatus', 'emp_types'));
  }
  /* ==================== Trade Wise Employee List ==================== */
  public function tradeWiseEmployeeList()
  {
    $category = (new EmpCategoryController())->getAllCategory();
    $empTypeList = (new EmployeeRelatedDataService())->getAllEmployeeType();
    return view('admin.employee-info.trade_wise.all', compact('category', 'empTypeList'));
  }

  // UI For Creating EMployee Report
  public function sponserWiseEmployeeList()
  {

    $sponser = (new EmployeeRelatedDataService())->getAllSponserInfoForDropdown(Auth::user()->branch_office_id);
    return view('admin.employee-info.sponser_wise.all', compact('sponser'));
  }



  /*
  |--------------------------------------------------------------------------
  |  JSON Reponse OPERATION
  |--------------------------------------------------------------------------
  */


    // Project wise Employee List For Transfer Ajax Request
    public function getProjectWiseEmployeeListForEmployeeTransferAJAXRequest(Request $request)
    {

        if(!is_null($request->multi_emp_id)){

            $allEmplId = explode(",", $request->multi_emp_id);
            $allEmplId = array_unique($allEmplId); // remove multiple same empl ID
            $employee_list = (new EmployeeDataService())->getEmployeesInfoByMultipleEmployeeIDForEmployeeTransfer($allEmplId,Auth::user()->branch_office_id);
            return response()->json(['success' => true, 'status' => 200, "data" => $employee_list]);

        }elseif ($request->project_id >0) {
            $employee_list = (new EmployeeDataService())->getEmployeesInfoByProjectIdForEmployeeTransfer($request->project_id, 1,Auth::user()->branch_office_id);
            return response()->json(['success' => true, 'status' => 200, "data" => $employee_list]);
        }else {
            return response()->json(['success' => false, 'status' => 404, 'message' => 'Employee Not Found. Please Try Again','error' => 'error']);
        }
    }



  /* ======== end class bracket ======== */
}
