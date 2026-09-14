<?php

namespace Modules\Advance\Http\Controllers\Employee;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpParser\Node\Expr\Cast\Double;
use App\Jobs\AdvanceProcessJob;
use App\Http\Controllers\DataServices\EmployeeAttendanceDataService;

use Illuminate\Foundation\Validation\ValidatesRequests;

use App\Http\Controllers\DataServices\{
    EmployeeDataService,
    EmployeeAdvanceDataService,
    ProjectDataService,
    AccommodationDataService,
    EmployeeRelatedDataService,
    CompanyDataService,
    FiscalYearDataService,
    SalaryProcessDataService
};
use App\Http\Controllers\Admin\Helper\UploadDownloadController;


// insert user activity
// (new AuthenticationDataService())->InsertLoginUserActivity(45, 2, Auth::user()->id, $insert->emp_auto_id, null);


class EmployeeAdvanceController extends Controller
{
    use ValidatesRequests;

    function __construct()
    {
        $this->middleware('permission:set-advance', ['only' => ['index']]);
        $this->middleware('permission:employee_advance_insert', ['only' => ['index']]);
        $this->middleware('permission:employee-advance-processing', ['only' => ['employeeAdvanceProcessingRequest']]);
        $this->middleware('permission:mult_employee_advance_insert', ['only' => ['index', 'getEmployeeListForMultipleEmployeeAdvancePayment', 'multipleEmployeeAdvanceInsertRequest']]);
    }


    public function index()
    {
        $all = (new EmployeeAdvanceDataService())->getEmployeeAdvanceAllRecords(10);
        $purpose = (new EmployeeAdvanceDataService())->getAdvancePurposeAll();
        $projects = (new ProjectDataService())->getAllActiveProjectListForDropdown(Auth::user()->branch_office_id);
        $accomdOfficeBuilding = (new AccommodationDataService())->getAllActiveOfficeBuildingNameIdAndCityForDropdownList();
        return view('advance::pages.employee.empAdvance', [
            'data' => [
                'all' => $all,
                'purpose' => $purpose,
                'projects' => $projects,
                'accomdOfficeBuilding' => $accomdOfficeBuilding,
            ]
        ]);
    }



    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('advance::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('advance::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('advance::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }


    // //Employee Iqama Renewal

    // public function iqamarenewal()
    // {
    //     return view("advance::pages.iqama_renewal.iqama_renewal");
    // }



    // public function searchAnEmployeeIqamaRenewalExpenseRecords(Request $request)
    // {

    //     // return response()->json(['success' => true, 'status' => 200, "data" => $request->all()]);


    //     try {

    //         $find_employee = (new EmployeeDataService())->searchAnEmployeeWithImportantInformationAsShowByMultitypeParameter($request->searchValue, $request->searchType, Auth::user()->branch_office_id);

    //         if ($find_employee) {
    //             $records = (new EmployeeAdvanceDataService())->searchAnEmployeeIqamaAnnualExpenseRecords($find_employee->emp_auto_id);
    //             return  response()->json(['status' => 200, 'success' => true, 'employee' => $find_employee, 'data' => $records]);
    //         } else {
    //             return  response()->json(['status' => 403, 'success' => false, 'error' => 'error', 'message' => 'Employee Not Found']);
    //         }
    //     } catch (Exception $ex) {
    //         return  response()->json(['status' => 403, 'success' => false, 'error' => 'error', 'message' => 'System Exception, Please Reload and Try Again']);
    //     }
    // }



    // public function searchAnEmployeeIqamaRenewalExpenseApprovalPendingRecordsAJAXRequest(Request $request)
    // {

    //     try {
    //         $find_employee = (new EmployeeDataService())->searchAnEmployeeWithImportantInformationAsShowByMultitypeParameter($request->searchValue, $request->searchType, Auth::user()->branch_office_id);
    //         if ($find_employee) {
    //             $records = (new EmployeeAdvanceDataService())->getAnEmployeeIqamaAnnualExpenseApprovalPendingRecords($find_employee->emp_auto_id);
    //         } else {
    //             $records =  (new EmployeeAdvanceDataService())->getIqamaRenewalExpenseApprovalPendingAllRecords(Auth::user()->branch_office_id); // 100 records

    //         }
    //         return  response()->json(['status' => 200, 'success' => true, 'data' => $records]);
    //     } catch (Exception $ex) {
    //         return  response()->json(['status' => 404, 'success' => false, 'message' => "System Operation Failed", 'error' => "error"]);
    //     }
    // }


    // // Import Work Record Excel File to Temporary Table
    // public function uploadIqamaRenewalExpenseExcelFileWithPreview(Request $request)
    // {
    //     try {
    //         if ($request->file) {

    //             $file = $request->file;
    //             $upload_file_type = $request->upload_type_ddl;
    //             $extension = $file->getClientOriginalExtension(); //Get extension of uploaded file
    //             $fileSize = $file->getSize(); //Get size of uploaded file in bytes
    //             if ((new HelperController())->checkUploadedFileFormatAndUploadFileSize($extension, $fileSize)) {
    //                 if ($upload_file_type == 1) {
    //                     // iqama expire date update
    //                     $import = new ImportEmployeeIqamaExpire();
    //                 } else if ($upload_file_type == 2) {
    //                     // renewal expense record
    //                     $import = new ImportIqamaRenewalExpenseRecord();
    //                 } else  if ($upload_file_type == 3) {
    //                     $import = new ImportEmployeeSponsor();
    //                 }

    //                 Excel::import($import, $request->file('file'));
    //                 return response()->json([
    //                     'status' => 200,
    //                     'success' => true,
    //                     'records_not_found' => $import->records_not_found,
    //                     'records' => $import->records,
    //                     'message' => $import->records->count() . " Records  Added for Uploading"
    //                 ]);
    //             } else {
    //                 return response()->json([
    //                     'status' => 404,
    //                     'success' => false,
    //                     'message' =>  "Invalid File Format"
    //                 ]);
    //             }
    //         } else {
    //             return response()->json([
    //                 'status' => 403,
    //                 'success' => false,
    //                 'message' =>  "Please Upload an Excel File"
    //             ]);
    //         }
    //     } catch (Exception $ex) {
    //         return response()->json([
    //             'error' => $request->all(),
    //             'status' => 500,
    //             'success' => false,
    //             'message' =>  $ex . " Upload Failed, Please check Excel Header Name & Data",
    //         ]);
    //     }
    // }


    // // Submit Imported Excell Data To Final Table
    // public function storeIqamaRenewalExpenseExcelImportedRecordsInMainTable(Request $request)
    // {
    //     try {

    //         $result = (new EmployeeRelatedDataService())->getIqamaExpireUploadedAllDataFromTemporaryTable();
    //         $isSave = null;
    //         // DB::beginTransaction();

    //         if ($request->upload_file_type == 1) {
    //             foreach ($result as $arecord) {
    //                 $is_hourly_emp = (new EmployeeDataService())->checkThisEmployeSalaryIsHourlyByEmployeeAutoId($arecord->emp_auto_id);
    //                 (new EmployeeDataService())->updateEmployeeIqamaReletedInfo($arecord->emp_auto_id, $arecord->iqama_no, $arecord->expire_date);
    //             }
    //         } else  if ($request->upload_file_type == 2) {

    //             foreach ($result as $arecord) {
    //                 $is_hourly_emp = (new EmployeeDataService())->checkThisEmployeSalaryIsHourlyByEmployeeAutoId($arecord->emp_auto_id);
    //                 (new EmployeeAdvanceDataService())->saveEmployeeIqamaRenewalExpenseByFileUpload(
    //                     $arecord->emp_auto_id,
    //                     $arecord->jawazat_fee,
    //                     $arecord->maktab_alamal_fee,
    //                     $arecord->bd_amount,
    //                     $arecord->medical_insurance,
    //                     $arecord->others_fee,
    //                     (new HelperController())->getYearFromDateValue($request->renewal_date),
    //                     $arecord->jawazat_penalty,
    //                     $arecord->duration,
    //                     $arecord->renewal_date,
    //                     $arecord->remarks,
    //                     $arecord->total_amount,
    //                     "",
    //                     $arecord->renewal_date,
    //                     $arecord->reference_emp_id,
    //                     1,
    //                     $is_hourly_emp == 1 ? 1 : 2,
    //                     2,
    //                     Auth::user()->branch_office_id
    //                 );
    //                 // basic employee paid by company = 2 and hourly emp self =  1
    //             }
    //         }




    //         // DB::commit();
    //         // remove all records from table
    //         (new EmployeeRelatedDataService())->removeIqamaExpireUploadedAllDataFromTemporaryTable();
    //         return response()->json([
    //             'status' =>  200,
    //             'success' => true,
    //             'message' => 'Successfully Updated'
    //         ]);
    //     } catch (Exception $ex) {
    //         // DB::rollBack();
    //         return response()->json([
    //             'error' =>  "error",
    //             'status' => 500,
    //             'success' => false,
    //             'message' => 'System Operation Failed ' . $ex,
    //         ]);
    //     }
    // }


    // // Employee Advance Setting Json Reponse
    // public function findanEmployeeWithAdvanceSetting(Request $request)
    // {
    //     return response()->json(['status' => 200, 'success' => true,  'dd' => $request->all()]);
    //     // $anEmployee  = (new EmployeeDataService())->getAnEmployeeInfoWithSalaryDetailsJoinQueryByEmpId($request->emp_id,null);

    //     $anEmployee  = (new EmployeeDataService())->getAnEmployeeInfoWithSalaryDetailsInformationByEmployeeID($request->emp_id, Auth::user()->branch_office_id);
    //     if ($anEmployee) {
    //         $anEmployee  = (new EmployeeAdvanceDataService())->processAnEmployeeAdvance($anEmployee);
    //         $salaryDetatils = (new EmployeeDataService())->getAnEmployeeSalaryDetailsByEmpAutoId($anEmployee->emp_auto_id);

    //         return response()->json([
    //             'status' => 200,
    //             'success' => true,
    //             'totalIqama' => $anEmployee->iqama_renewal_cost_total_amount,
    //             'totalPaidIqama' => $anEmployee->iqama_renewal_total_paid_Amount,
    //             'totalOthers' => $anEmployee->other_advance_total_amount,
    //             'totalPaidOthers' => $anEmployee->other_advance_total_paid_Amount,
    //             'empAutoId' => $anEmployee->emp_auto_id,
    //             'salaryDetatils' => $salaryDetatils,
    //             'employee' => [
    //                 'employee_name' => $anEmployee->employee_name,
    //                 'employee_id' => $anEmployee->employee_id,
    //                 'akama_no' => $anEmployee->akama_no,
    //                 'hourly_employee' => $anEmployee->hourly_employee,
    //                 'passfort_no' => $anEmployee->passfort_no
    //             ],
    //         ]);
    //     } else
    //         // return response()->json(['status' => 'error']);
    //         return response()->json(['status' => 404, 'success' => false, 'error' => 'error', 'message' => 'Employee Not Found']);


    //     //   $anEmployee  = (new EmployeeDataService())->searchingAnEmployeeInfoByMultitypeParameter((int)$request->emp_id,'employee_id',Auth::user()->branch_office_id);
    //     //       return response()->json(['status' =>404, 'success' => false, 'error'=>'error', 'message' => 'Employee Not Found' ,'dd'=> $anEmployee,'pp'=>$request->all()]);
    //     //   if (count($anEmployee) >= 2) {
    //     //      //   return json_encode(['success' => false, 'status' => 404, 'error' => 'error','message'=>'Multiple Employee Not found']);
    //     //          return response()->json(['status' =>404, 'success' => false, 'error'=>'error', 'message' => 'Employee Not Found']);

    //     //     }else{

    //     //         $anEmployee  =  $anEmployee[0];
    //     //         $anEmployee  = (new EmployeeAdvanceDataService())->processAnEmployeeAdvanceForFiscalYear($anEmployee);
    //     //         $salaryDetatils = (new EmployeeDataService())->getAnEmployeeSalaryDetailsByEmpAutoId($anEmployee->emp_auto_id);

    //     //         return response()->json([
    //     //             'status' =>200, 'success' => true,
    //     //             'totalIqama' => $anEmployee->iqama_renewal_cost_total_amount,
    //     //             'totalPaidIqama' => $anEmployee->iqama_renewal_total_paid_Amount,
    //     //             'totalOthers' => $anEmployee->other_advance_total_amount,
    //     //             'totalPaidOthers' => $anEmployee->other_advance_total_paid_Amount,
    //     //             'empAutoId' => $anEmployee->emp_auto_id,
    //     //             'salaryDetatils' => $salaryDetatils,
    //     //             'employee' => ['employee_name'=> $anEmployee->employee_name,'employee_id'=> $anEmployee->employee_id,
    //     //             'akama_no'=> $anEmployee->akama_no,'hourly_employee'=> $anEmployee->hourly_employee,'passfort_no'=>$anEmployee->passfort_no],
    //     //         ]);

    //     //     }


    // }

    // public function updateAdvanceInstallAmount(Request $request)
    // {

    //     return response()->json(['success' => true, 'status' => 200, "data" => $request->all()]);

    //     if ((int) $request->operation_type == 2) {
    //         // 2 =  Employee Food Setting

    //         $emp_auto_ids = (new EmployeeAttendanceDataService())->getEmployeesAutoIdsThoseAreInMonthlyWorkRecord($request->project_id, $request->month, $request->year);
    //         $update_status = (new EmployeeDataService())->updateMultipleEmployeesFoodAmountByMultipleEmpID($emp_auto_ids, $request->amount);
    //         return response()->json(['success' => true, 'status' => 200, "data" => $request->all()]);

    //         Session::flash('success',  'Successfully Update Employee Food Amount');
    //         return redirect()->back();
    //     } else if ((int) $request->operation_type == 1) {

    //         // 1 = single emp advance setting
    //         $nextPayIqama = (float) $request->nextPayIqama;
    //         $nextPayOthers = (float) $request->nextPayOthers;
    //         if ($nextPayOthers == "") {
    //             $nextPayOthers = 0;
    //         } elseif ($nextPayOthers == "" && $nextPayIqama == "") {
    //             $nextPayIqama = 0;
    //             $nextPayOthers = 0;
    //         } elseif ($nextPayIqama == "") {
    //             $nextPayIqama = 0;
    //         } else {
    //             $nextPayIqama = (float) $request->nextPayIqama;
    //             $nextPayOthers =  (float) $request->nextPayOthers;
    //         }
    //         // Here id means Employee Auto Id
    //         $update1 = (new EmployeeDataService())->updateEmployeeIqamaAdvaceInstallAmount($request->id, $nextPayIqama);
    //         $update = (new EmployeeDataService())->updateEmployeeOtherAdvaceInstallAmount($request->id, $nextPayOthers);

    //         if ($update || $update1) {
    //             return response()->json(['success' => true, 'status' => 200, "data" => $request->all()]);

    //             Session::flash('success', 'Successfully Update Advance Setting');
    //             return redirect()->back();
    //         } else {

    //             return response()->json(['success' => true, 'status' => 200, "data" => $request->all()]);

    //             Session::flash('error', 'Update Operation Failed, Please Try Again!');
    //             return redirect()->back();
    //         }
    //     } else {
    //         return response()->json(['success' => true, 'status' => 200, "data" => $request->all()]);

    //         return redirect()->back();
    //     }
    // }



    /*
    ===========================================================================
    ========================= API SERVICE =====================================
    ===========================================================================
    */

    //  Employee search For multiple emp Advance Payment Ajax Request
    public function getEmployeeListForMultipleEmployeeAdvancePayment(Request $request)
    {
         // We have to check employee fiscal year date and advace insert date before showing the list of employees for multiple employee advance payment
       // return response()->json(['success' => false, 'message' => 'This service is temporarily unavailable!','dd'=>$request->all()]);
        $project_id = $request->project_id;
        $buildingId = $request->acomdOfbId;
        $multi_emp_id = $request->multi_emp_id;

        // Log::info("Request Data: ". $project_id);
        // Log::info("Project ID = " . $buildingId);
        // Log::info("Employee ID = " . $multi_emp_id);

        $emplist = array();
        if (is_null($multi_emp_id) == false) {

            $allEmplId = explode(",", $multi_emp_id);
            $allEmplId = array_unique($allEmplId); // remove multiple same empl ID
            $emplist = (new EmployeeDataService())->searchListOfEmployeesInfoWithSalaryDetailForEmployeeAdvanceByMultipleEmpId($allEmplId, Auth::user()->branch_office_id);

        } else {
            $emplist = (new EmployeeDataService())->getEmployeesInfoWithSalaryDetailForMultipleEmployeeAdvancePayment($project_id, $buildingId, Auth::user()->branch_office_id);
        }

        foreach ($emplist as $anEmployee) {
            $anEmployee->fiscal = (new FiscalYearDataService())->getAnEmployeeRunningFiscalYearRecord($anEmployee->emp_auto_id);
            if((new  FiscalYearDataService())->checkThisOperationIsAllowInTheRunningFiscalYear($anEmployee->emp_auto_id,$request->adv_date) == false){
                $anEmployee->operation_message = "Fiscal Year is Closed for the Advance Date";
                $anEmployee->operation_lock = true;
            }else{
                $anEmployee->operation_lock = false;
            }
        }

        return response()->json(['status' => 200, 'success' => true, "data" => $emplist]);
       // Log::info("Employee List =" . $emplist);

        if (count($emplist) > 0) {

        } else {
            return response()->json(['status' => 404, 'success' => false, 'error' => "Data Not Found!"]);
        }
    }


    public function multipleEmployeeAdvanceInsertRequest(Request $request)
    {

     // We have to check employee fiscal year date and advace insert date before showing insert multiple employee advance
     //   return response()->json(['success' => false, 'message' => 'This service is temporarily unavailable!']);
        // Validate the request data
        $this->validate($request, [
            'adv_purpose_id' => 'required',
            'adv_date' => 'required|date',
            'adv_remarks' => 'required|string',
            'advance_paper' => 'nullable|file|mimes:pdf,jpg,png'
        ]);

        $creator = Auth::user()->id;
        $emp_auto_id_list = $request->input('emp_auto_id', []);
        $counter = 0;

        // Handle file upload
        $file_path = null;
        if ($request->hasFile('advance_paper')) {
            $file = $request->file('advance_paper');
            $file_path =   (new UploadDownloadController())->uploadAdvancePaper($file, null);
        }

        // Process each employee's advance data
        foreach ($emp_auto_id_list as $index => $aemp_id) {
            if ($request->has('adv_checkbox-' . $aemp_id) && $request->filled('adv_amount-' . $aemp_id)) {
                $adv_amount = $request->input('adv_amount-' . $aemp_id);
                $employee = (new EmployeeDataService())->getAnEmployeeIqamaAdvanceAndOtherAdvanceInstallAmountByEmpAutoId($aemp_id);
                $other_adv_inst_amount = $adv_amount + $employee->other_adv_inst_amount;
                $project_id = $request->input('project_id_' . $aemp_id, null);

                // Insert employee advance data
                (new EmployeeAdvanceDataService())->insertEmployeeAdvance(
                    $aemp_id,
                    $request->adv_purpose_id,
                    $adv_amount,
                    1, // Assuming 1 is a status or type
                    $request->adv_remarks,
                    $request->adv_date,
                    $creator,
                    $file_path,
                    $project_id
                );

                // Update employee advance installment amount
                (new EmployeeDataService())->updateEmployeeAdvaceInstallAmount($aemp_id, $other_adv_inst_amount, 2); // Assuming 2 is a status or type for update
                $counter++;
            }
        }

        // Return response based on the operation result
        if ($counter > 0) {
            return response()->json(['success' => true, 'message' => 'Successfully Saved ' . $counter . ' Employees Advance Payment']);
        } else {
            if ($file_path) {
                (new UploadDownloadController())->deleteUploadedAdvancePaper($file_path);
            }
            return response()->json(['success' => false, 'message' => 'Operation Failed, Please Try Again!']);
        }
    }


    // Advance Report
    public function processAdvanceReport(Request $request)
    {

        //  return response()->json(['status' => 200, 'success' => true, "empList" => $request->all()]);

        if ($request->report_type == 1) {
            return $this->getProjectwiseEmployeeDeductionAndOutstandingWithPreviousFiscalBalance($request);
            // return $this->getProjectWiseEmployeeAdvanceSummaryReport($request);
        } else if ($request->report_type == 2) {
            return $this->getProjectwiseEmployeeDeductionAndOutstandingWithPreviousFiscalBalance($request);
        } else if ($request->report_type == 3) {
            return $this->showEmployeesCurrentDeductionSettingAmount($request);
        } else if ($request->report_type == 4) {
            return $this->showMultipleEmployeesAdvanceTakenAndPaidSummaryReport($request);
        }
        // Log the request data
        return "Please Input Valid Information and Try Again";
    }

    // Employee Advance Taken and Paid Report , Report_type   3
    public function getProjectWiseEmployeeAdvanceSummaryReport($request)
    {

        try {
            $empList = array();

            if ($request->employee_ids != "") {
                $allEmplId = explode(",", $request->employee_ids);
                $allEmplId = array_unique($allEmplId);
                $empList = (new EmployeeDataService())->searchListOfEmployeesInfoWithSalaryDetailForEmployeeAdvanceByMultipleEmpId($allEmplId, 1);
            } else {

                $project_ids = $request->has('project_id') ? [$request->project_id] : (new ProjectDataService())->getAllActiveProjectIDs();
                $sponsor_ids =  (new EmployeeRelatedDataService())->getAllActiveSponsorIdAsArray();

                if ($request->employee_type == 0) {
                    $empList = (new EmployeeDataService())->getListOfHourlyEmployeesThoseAreNowWorkingInThisProjectForAdvanceSummaryReport($project_ids, $sponsor_ids, (int)$request->month, (int)$request->year);
                } else {
                    $empList = (new EmployeeDataService())->getListOfBasicEmployeesThoseAreWorkingNowInThisProjectForAdvanceSummaryReport($project_ids, $sponsor_ids, (int)$request->month, (int)$request->year);
                }
            }

            if (count($empList) <= 0) {
                return "Employee Not Found";
            }

            $counter = 0;
            foreach ($empList as $anEmployee) {

                $fiscal = (new FiscalYearDataService())->getAnEmployeeRunningFiscalYearRecord($anEmployee->emp_auto_id);
                $anEmployee->fiscal_year = $fiscal;
                // Advance Collection from employee
                $anEmployee->iqama_deduction = (new SalaryProcessDataService())->getAnEmployeeTotalAmountOfIqamaExpenseDeductionFromSalaryByFiscalYear($anEmployee->emp_auto_id, $fiscal->start_date, $fiscal->end_date);
                $anEmployee->cach_received =  (new EmployeeAdvanceDataService())->getAnEmployeeAdvancePaidByCachTotalAmountByFiscalYear($anEmployee->emp_auto_id, $fiscal->start_date, $fiscal->end_date);
                // Advance Given to Employee
                $anEmployee->iqama_renewal = (new EmployeeAdvanceDataService())->getAnEmployeeIqamaRenewalTotalExpenseAmountByFiscalYear($anEmployee->emp_auto_id, $fiscal->start_date, $fiscal->end_date);
                // $anEmployee->iqama_renew_balance_amount =  $iqama_renewal_cost_total_amount   - ($toal_iqama_expense_deduction_from_salary + $cashReceiveTotalPaidAmount);
                $anEmployee->this_month_iqama_deduction = 0;
                $anEmployee->this_month_other_deduction = 0;
                // Other Advance Calculation
                // Advance Given to Employee
                $anEmployee->other_advance = (new EmployeeAdvanceDataService())->getAnEmployeeOthersAdvanceGivenTotalAmountByFiscalYear($anEmployee->emp_auto_id, $fiscal->start_date, $fiscal->end_date);
                // Advance Collection from Employee
                $anEmployee->other_advace_deduction = (new SalaryProcessDataService())->getAnEmployeeTotalAmountOfOtherAdvanceDeductionFromSalaryByFiscalYear($anEmployee->emp_auto_id, $fiscal->start_date, $fiscal->end_date);
                //  $other_advance_balance_amount =  $otherAdvanceTotalAmount -  $total_other_advace_deduction_from_salary ;
                $empList[$counter++] = $anEmployee;
            }
            $records = $empList;
            //  dd($records[0]);
            $company = (new CompanyDataService())->findCompanryProfile();
            $projectName =  (new ProjectDataService())->getProjectNameByProjectId($request->project_id);
            $login_name = Auth::user()->name;
            $current_datetime = date('Y-m-d H:i:');
            $pdf = Pdf::loadView('advance::pages.reports.advance_summary_report', compact('records', 'company', 'projectName', 'current_datetime'));
            return $pdf->stream('advance_summary.pdf');
        } catch (Exception $ex) {
            return "System Exception " . $ex;
        }
    }

    public function getProjectwiseEmployeeDeductionAndOutstandingWithPreviousFiscalBalance($request)
    {

        try {
            // dd($request->all());
            $empList = array();
            $projectName = "";
            if ($request->employee_ids != "") {
                // 1048,1301
                $allEmplId = explode(",", $request->employee_ids);
                $allEmplId = array_unique($allEmplId);
                $empList = (new EmployeeDataService())->getEmployeesInfoWithSalaryDetailForAdvanceProcessByMultipleEmployeeIDs($allEmplId);
                $projectName = "";
            } else {

                $project_ids = $request->has('project_id') ? [$request->project_id] : (new ProjectDataService())->getAllActiveProjectIDs();
                $sponsor_ids =  (new EmployeeRelatedDataService())->getAllActiveSponsorIdAsArray();
                $projectName =  (new ProjectDataService())->getProjectNameByProjectId($request->project_id);
                if ($request->employee_type == 0) {
                    $empList = (new EmployeeDataService())->getListOfHourlyEmployeesThoseAreNowWorkingInThisProjectForAdvanceSummaryReport($project_ids, $sponsor_ids, (int)$request->month, (int)$request->year);
                } else {
                    $empList = (new EmployeeDataService())->getListOfBasicEmployeesThoseAreWorkingNowInThisProjectForAdvanceSummaryReport($project_ids, $sponsor_ids, (int)$request->month, (int)$request->year);
                }
            }


            //  return $empList;
            if (count($empList) <= 0) {
                return 'Employee Not Found';
            }

            $counter = 0;
            foreach ($empList as $anEmployee) {

                $fiscal = (new FiscalYearDataService())->getAnEmployeeRunningFiscalYearRecord($anEmployee->emp_auto_id);
                $last_closing_fy = (new FiscalYearDataService())->getAnEmployeeLastClosingFiscalYearRecord($anEmployee->emp_auto_id);
                $anEmployee->fiscal_year = $last_closing_fy;
                // Advance Collection from employee
                $anEmployee->iqama_deduction = (new SalaryProcessDataService())->getAnEmployeeTotalAmountOfIqamaExpenseDeductionFromSalaryByFiscalYear($anEmployee->emp_auto_id, $fiscal->start_date, $fiscal->end_date);
                $anEmployee->cach_received =  (new EmployeeAdvanceDataService())->getAnEmployeeAdvancePaidByCachTotalAmountByFiscalYear($anEmployee->emp_auto_id, $fiscal->start_date, $fiscal->end_date);

                // Iqama renewal expense for Employee
                $anEmployee->iqama_renewal = (new EmployeeAdvanceDataService())->getAnEmployeeIqamaRenewalTotalExpenseAmountByFiscalYear($anEmployee->emp_auto_id, $fiscal->start_date, $fiscal->end_date);
                // $anEmployee->iqama_renew_balance_amount =  $iqama_renewal_cost_total_amount   - ($toal_iqama_expense_deduction_from_salary + $cashReceiveTotalPaidAmount);


                // Advance Given to Employee
                $anEmployee->other_advance = (new EmployeeAdvanceDataService())->getAnEmployeeOthersAdvanceGivenTotalAmountByFiscalYear($anEmployee->emp_auto_id, $fiscal->start_date, $fiscal->end_date);
                // Advance Collection from Employee
                $anEmployee->other_advace_deduction = (new SalaryProcessDataService())->getAnEmployeeTotalAmountOfOtherAdvanceDeductionFromSalaryByFiscalYear($anEmployee->emp_auto_id, $fiscal->start_date, $fiscal->end_date);
                //  $other_advance_balance_amount =  $otherAdvanceTotalAmount -  $total_other_advace_deduction_from_salary ;
                $empList[$counter++] = $anEmployee;


                $anEmployee->iqama_balance = $last_closing_fy->balance_amount +  $anEmployee->iqama_renewal - ($anEmployee->iqama_deduction  +   $anEmployee->cach_received);
                $anEmployee->advance_balance =  $anEmployee->other_advance - $anEmployee->other_advace_deduction; //  $otherAdvanceTotalAmount  - $total_other_advace_deduction_from_salary;
                $final_balance = round($anEmployee->iqama_balance  + $anEmployee->advance_balance);

                if ($final_balance <= 0) {
                    $is_payable = true;
                    $final_balance = $final_balance * (-1); // payable
                } else {
                    $is_payable = false; // receivable
                }
                $anEmployee->final_balance = $final_balance;
                $anEmployee->is_payable = $is_payable;
            }
            $records = $empList;
            //   dd($records[0]);
            $company = (new CompanyDataService())->findCompanryProfile();

            $login_name = Auth::user()->name;
            $current_datetime = date('Y-m-d H:i:');
            // return view('advance::pages.reports.project_base_adv_overall_sum_report', compact('records', 'company', 'projectName','current_datetime','login_name'));
            return view('advance::pages.reports.project_base_adv_overall_sum_report', compact('records', 'company', 'projectName', 'current_datetime', 'login_name'));

            // ini_set('memory_limit', '512M');
            // $pdf = Pdf::loadView('advance::pages.reports.project_base_adv_overall_sum_report', compact('records', 'company', 'projectName','current_datetime','login_name'));
            // return $pdf->stream('advance_overall_summary.pdf');

        } catch (Exception $ex) {

            return "System Exception " . $ex;
        }

        //  /// old system


        //   $projectWiseEmp = (new EmployeeDataService())->getEmployeesInformationRecordsByProjectId($projectId);
        //   if ($projectWiseEmp == NULL) {
        //       Session::flash('no_record', 'Opps! No Records Found With This Informations');
        //       return redirect()->back();
        //   }
        //   $counter = 0;
        //   foreach ($projectWiseEmp as $anEmployee) {
        //       $projectWiseEmp[$counter++] = (new EmployeeAdvanceDataService())->getAnEmployeeAdvanceSummaryReport($anEmployee);
        //   }
        //   $company = (new CompanyDataService())->findCompanryProfile();
        //   $projectName = (new ProjectDataService())->getProjectNameByProjectId($projectId);
        //   return view('admin.report.iqama.emp_advance_summary_report', compact('projectWiseEmp', 'company', 'projectName'));

    }


    // Employees Current Deduction Setting Report
    public function showEmployeesCurrentDeductionSettingAmount($request)
    {

        try {
            $report_title = ['All'];
            $employees = array();
            $allEmplId = array();
            if ($request->employee_ids != "") {
                $allEmplId = explode(",", $request->employee_ids);
                $allEmplId = array_unique($allEmplId);
                $report_title = ['Multiple Employes'];
            }
            $project_id_list = $request->has('project_id') ? [(int)$request->project_id] : (new ProjectDataService())->getAllActiveProjectIDs();
            $sponsor_ids =  (new EmployeeRelatedDataService())->getAllActiveSponsorIdAsArray();

            $empType = $request->employee_type;
            $report_type = $request->report_type;

            if ($project_id_list == null) {
                $project_id_list = (new ProjectDataService())->getAllActiveProjectIDOfABranchOfficeAsArray(Auth::user()->branch_office_id);
            }


            if ($empType == 0) {  // Basic Salary direct Employee
                $report_title[0] = "Hourly Employees";
            } else if ($empType == 1) { //  direct Employee Basic

                $report_title[0] = "Direct Employee(Basic Salary)";
            } else if ($empType == 2) { //  Indirect Employee

                $report_title[0] = "Indirect Employees";
            } else if ($empType == 3) {
                $report_title[0] = "Basic Salary(Indirect & Direct) Employees";
            }
            $employees = (new EmployeeDataService())->searchEmployeesForCurrentDeductionSettingEmployeesReport($allEmplId, $project_id_list, $sponsor_ids, $request->employee_type, Auth::user()->branch_office_id);
            $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
            return view('advance::pages.reports.current_deduction_setting_employees', compact('employees', 'company', 'report_title'));
        } catch (Exception $ex) {
            return "System Exception " . $ex;
        }
    }
    // Employee Advance taken and Paid Report , Report_type   4
    public function showMultipleEmployeesAdvanceTakenAndPaidSummaryReport($request)
    {

        try {
            $report_title = ['All'];
            $employees = array();
            $allEmplId = array();
            if ($request->employee_ids != "") {
                $allEmplId = explode(",", $request->employee_ids);
                $allEmplId = array_unique($allEmplId);
                $report_title = ['Multiple Employes'];
            }
            $project_id_list = $request->has('project_id') ? [$request->project_id] : (new ProjectDataService())->getAllActiveProjectIDs();
            $sponsor_ids =  (new EmployeeRelatedDataService())->getAllActiveSponsorIdAsArray();

            $empType = $request->employee_type;
            $report_type = $request->report_type;

            if ($project_id_list == null) {
                $project_id_list = (new ProjectDataService())->getAllActiveProjectIDOfABranchOfficeAsArray(Auth::user()->branch_office_id);
            }
            $employees = (new EmployeeAdvanceDataService())->getMultipleEmployeesAdvanceTakenAndPaidSummaryReport($allEmplId, $project_id_list, $sponsor_ids, $request->employee_type);
            $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
            $login_name = Auth::user()->name;
            return view('advance::pages.reports.emps_other_advance_summary_report', compact('employees', 'company', 'report_title', 'login_name'));
        } catch (Exception $ex) {
            return "System Exception " . $ex;
        }
    }


    // ADVANCE PROCESSING
    public function employeeAdvanceProcessingRequest(Request $request)
    {
        try {
            $empList = array();
            //  return json_encode(['status' => 405, 'error' => $request->all(), 'success' => false]);
            //// '1' = Asloob Sponsor Hourly, '2' = Asloob Sponsor Basic, '3' = Multiple Employees, '4' = Multiple Direct, '5' = Other Sponsor

            // if ($request->processing_mode == 5) {
            //     // subcon sponsor direct assignment iqama and other advance intallment amount
            //     $allEmplId = explode(",", $request->mul_emp_id);
            //     $allEmplId = array_unique($allEmplId);

            //     if (count($empList) == 0) {
            //         $empList = (new EmployeeDataService())->getListOfSubconSponsorEmployeesForIqamaAndOtherDeductionSetup($request->month, $request->year);
            //     } else {
            //         $empList = (new EmployeeDataService())->searchListOfEmployeesInfoWithSalaryDetailForEmployeeAdvanceByMultipleEmpId($allEmplId, 1);
            //     }
            //     foreach ($empList as $anEmployee) {
            //         (new EmployeeDataService())->updateAnEmployeeIqamaAndOtherAdvaceInstallAmount((int)$anEmployee->emp_auto_id, (int)$request->iqama_amount, (int) $request->other_amount);
            //     }
            //     $message = 'Total ' . count($empList) . ' Employees Advance Processing Completed.';
            //     return json_encode(['status' => 200, 'error' => '', 'message' => $message, 'success' => true]);
            // } else

            if ($request->processing_mode == 3) {
                // multiple employees processing for iqama and other advance installment amount

                 $allEmplId = explode(",", $request->mul_emp_id);
                $allEmplId = array_unique($allEmplId);
                $empList = (new EmployeeDataService())->searchListOfEmployeesInfoWithSalaryDetailForEmployeeAdvanceByMultipleEmpId($allEmplId, 1);
                // return json_encode(['status' => 405, 'error' => $empList, 'success' => false]);
                foreach ($empList as $anEmployee) {

                    $fiscal = (new FiscalYearDataService())->getAnEmployeeRunningFiscalYearRecord($anEmployee->emp_auto_id);
                    // Advance Collection from employee
                    $deduction_record = (new SalaryProcessDataService())->getAdvanceAndIqamaRenewalDeductionTotalAmountForAdvanceProcess($anEmployee->emp_auto_id, $fiscal->start_date, $fiscal->end_date);

                    $toal_iqama_expense_deduction_from_salary =  $deduction_record->iqama_deduction; // (new SalaryProcessDataService())->getAnEmployeeTotalAmountOfIqamaExpenseDeductionFromSalaryByFiscalYear($anEmployee->emp_auto_id,$fiscal->start_date,$fiscal->end_date);
                    $cashReceiveTotalPaidAmount =  (new EmployeeAdvanceDataService())->getAnEmployeeAdvancePaidByCachTotalAmountByFiscalYear($anEmployee->emp_auto_id, $fiscal->start_date, $fiscal->end_date);
                    // Advance Given to Employee
                    $iqama_renewal_cost_total_amount = (new EmployeeAdvanceDataService())->getAnEmployeeIqamaRenewalTotalExpenseAmountByFiscalYear($anEmployee->emp_auto_id, $fiscal->start_date, $fiscal->end_date);
                    $iqama_renew_balance_amount =  $iqama_renewal_cost_total_amount   - ($toal_iqama_expense_deduction_from_salary + $cashReceiveTotalPaidAmount);

                    // Other Advance Calculation
                    // Advance Given to Employee
                    $otherAdvanceTotalAmount = (new EmployeeAdvanceDataService())->getAnEmployeeOthersAdvanceGivenTotalAmountByFiscalYear($anEmployee->emp_auto_id, $fiscal->start_date, $fiscal->end_date);
                    // Advance Collection from Employee
                    $total_other_advace_deduction_from_salary = $deduction_record->other_deduction; // (new SalaryProcessDataService())->getAnEmployeeTotalAmountOfOtherAdvanceDeductionFromSalaryByFiscalYear($anEmployee->emp_auto_id,$fiscal->start_date,$fiscal->end_date);
                    $other_advance_balance_amount =  $otherAdvanceTotalAmount -  $total_other_advace_deduction_from_salary;

                        if ($other_advance_balance_amount <= 0) {
                            $anEmployee->other_adv_inst_amount = 0;
                        } else if ($other_advance_balance_amount <= $request->other_amount) {
                            $anEmployee->other_adv_inst_amount = $other_advance_balance_amount;
                        } else if ($other_advance_balance_amount > $request->other_amount) {
                            $anEmployee->other_adv_inst_amount = $request->other_amount;
                        }

                        if (($iqama_renew_balance_amount) <= 0) {
                            $anEmployee->iqama_adv_inst_amount = 0;
                        } else if ($iqama_renew_balance_amount <= $request->iqama_amount) {
                            $anEmployee->iqama_adv_inst_amount = $iqama_renew_balance_amount;
                        } else if ($iqama_renew_balance_amount > $request->iqama_amount) {
                            $anEmployee->iqama_adv_inst_amount = $request->iqama_amount;
                        }
                        (new EmployeeDataService())->updateAnEmployeeIqamaAndOtherAdvaceInstallAmount((int)$anEmployee->emp_auto_id, $anEmployee->iqama_adv_inst_amount, (int) $anEmployee->other_adv_inst_amount);

                }
                $message = 'Total ' . count($empList) . ' Employees Advance Processing Completed ';
                return json_encode(['status' => 200, 'error' => $empList, 'message' => $message, 'success' => true, 'dd' => $request->all()]);
            } else {

                if(Auth::user()->id != 1){
                     return json_encode(['status' => 405, 'error' => "You are not allowed to Process All Employees", 'success' => false]);
                 }

                // $empList = (new EmployeeDataService())->getListOfEmployeesForIqamaAndOtherDeductionSetup((int)$request->hourly_emp_direct_process,(int)$request->month,(int)$request->year);
                AdvanceProcessJob::dispatch($request->iqama_amount, $request->other_amount, (int)$request->hourly_emp_direct_process, (int)$request->month, (int)$request->year, $request->processing_mode);
                $message =  ' Advance Processing will be started soon. Please wait and refresh after few minutes.';
                return json_encode(['status' => 200, 'error' => '', 'message' => $message, 'success' => true]);
            }
        } catch (Exception $ex) {
            return json_encode(['status' => 405, 'error' => "System Exception" . $ex, 'success' => false]);
        }
    }
}
