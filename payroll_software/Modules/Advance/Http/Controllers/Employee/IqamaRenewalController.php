<?php

namespace Modules\Advance\Http\Controllers\Employee;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpParser\Node\Expr\Cast\Double;
use App\Jobs\AdvanceProcessJob;
use App\Http\Controllers\Admin\Helper\HelperController;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\DataServices\EmployeeAttendanceDataService;
use App\Http\Controllers\DataServices\{
    EmployeeDataService,
    EmployeeAdvanceDataService,
    ProjectDataService,
    AccommodationDataService,
    EmployeeRelatedDataService,
    CompanyDataService,
    FiscalYearDataService,
    SalaryProcessDataService,
    AuthenticationDataService
};

class IqamaRenewalController extends Controller
{

    function __construct()
    {

        $this->middleware('permission:employee-anualsfee', ['only' => ['iqamarenewal', 'insert']]);
        //    // $this->middleware('permission:iqama-renewal-expense-search|iqama-renewal-expense-edit',['only'=>['getIqamaExpenseRecordSearchingUI']]);
        //     $this->middleware('permission:iqama_renewal_expense_search|iqama-renewal-expense-edit',['only'=>['getIqamaExpenseRecordSearchingUI','searchAnEmployeeIqamaRenewalExpenseRecordsAJAXRequest']]);
        $this->middleware('permission:iqama-renewal-expense-edit', ['only' => ['update']]);
        $this->middleware('permission:iqama_renewal_expense_delete', ['only' => ['deleteAnEmployeeIqamaAnualExpenseBeforeApproval']]);
        $this->middleware('permission:iqama_renewal_expense_search', ['only' => ['searchAnEmployeeIqamaRenewalExpenseRecordsAJAXRequest', 'searchAnEmployeeIqamaRenewalExpenseApprovalPendingRecordsAJAXRequest']]);
        $this->middleware('permission:iqama-renewal-expense-approval', ['only' => ['approveOfMultiEmployeeeIqamaRenewalExpenseRecord', 'approveOfIqamaRenewalExpenseRecord']]);
        //     $this->middleware('permission:multi_emp_iqama_expiration_date_update_by_excel_upload',['only'=>['uploadIqamaRenewalExpenseExcelFileWithPreview','updateIqamaExpireDateImportedFromExcel']]);

    }

    //Employee Iqama Renewal

    public function iqamarenewal()
    {
        return view("advance::pages.iqama_renewal.iqama_renewal");
    }

    public function insert(Request $request)
    {

        //dd('Contact with Rashed ',$request->all());

        // $this->validate($request, [
        //     'emp_auto_id' => 'required',
        //     'jawazat_fee' => 'required',
        //     'maktab_alamal_fee' => 'required',
        //     'bd_amount' => 'required',
        //     'medical_insurance' => 'required',
        //     'others_fee' => 'required',
        //     'jawazat_penalty' => 'required',
        //     'payment_purpose_id' => 'required',
        // ], []);


        $findEmployee = (new EmployeeDataService())->getAnEmployeeInfoByEmpAutoId($request->emp_auto_id);
        if (!$findEmployee) {
            return response()->json(['success' => true, 'status' => 200, "message" => 'Employee ID Not Found']);
        } else if ((new  FiscalYearDataService())->checkThisOperationIsAllowInTheRunningFiscalYear($request->emp_auto_id, $request->renewal_date) == false) {

            return response()->json(['success' => false, 'status' => 403, "message" => 'Employee Account is Closed. Please Open Account and Try Again']);
        } else {
            $newInsertId = (new EmployeeAdvanceDataService())->saveEmployeeIqamaRenewalExpense(
                $findEmployee->emp_auto_id,
                $request->jawazat_fee,
                $request->maktab_alamal_fee,
                $request->bd_amount,
                $request->medical_insurance,
                $request->others_fee,
                (new HelperController())->getYearFromDateValue($request->renewal_date),
                $request->jawazat_penalty,
                $request->duration,
                $request->renewal_date,
                $request->remarks ?? '',
                $request->total_amount,
                $request->payment_number,
                $request->payment_date,
                $request->reference_emp_id,
                $request->renewal_status,
                $request->expense_paid_by,
                $request->iqama_expire_date,
                $request->payment_purpose_id,
                $findEmployee->branch_office_id

            );
            if ($newInsertId > 0) {


                return response()->json(['success' => true, 'status' => 200, "message" => 'Operation Successfully Completed']);
            } else {
                return response()->json(['success' => false, 'status' => 403, "message" => 'Operation failed']);
            }
        }
    }
    public function update(Request $request)
    {




        $validator = Validator::make($request->all(), [
            'jawazat_fee'         => 'required',
            'maktab_alamal_fee'   => 'required',
            'bd_amount'           => 'required',
            'medical_insurance'   => 'required',
            'others_fee'          => 'required',
            'jawazat_penalty'     => 'required',
            'payment_purpose_id'  => 'required',
            'id'                  => 'required', // iqama renewal table auto id
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 422,
                'success' => false,
                'errors'  => $validator->errors(),
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {

            $data = (new EmployeeAdvanceDataService())->findAnEmployeeIqamaExpenseRecordByRecordId($request->id);

            if ((new FiscalYearDataService())->checkThisOperationIsAllowInTheRunningFiscalYear($data->EmplId, $request->renewal_date) == false) {
                return response()->json([
                    'status'  => 403,
                    'success' => false,
                    'message' => 'Employee Account is Closed. Please Open Account and Try Again',
                ]);
            }

            $update = (new EmployeeAdvanceDataService())->updateEmployeeIqamaRenewalExpenseByRecordId(
                $request->id,
                $request->jawazat_fee,
                $request->maktab_alamal_fee,
                $request->bd_amount,
                $request->medical_insurance,
                $request->others_fee,
                (new HelperController())->getYearFromDateValue($request->renewal_date),
                $request->jawazat_penalty,
                $request->duration,
                $request->renewal_date,
                $request->remarks ?? '',
                $request->total_amount,
                $request->payment_number,
                $request->payment_date,
                $request->reference_emp_id,
                $request->renewal_status,
                $request->expense_paid_by,
                $request->iqama_expire_date,
                $request->payment_purpose_id
            );

            if ($request->has('update_approved_chk')) {
                $iqama_renewal_record = (new EmployeeAdvanceDataService())->findAnEmployeeIqamaExpenseRecordByRecordId($request->id);
                (new EmployeeAdvanceDataService())->approvedPendingIqamaExpenseRecordForSingleEmpl($request->id);


                (new AuthenticationDataService())->InsertLoginUserActivity(34, 2, Auth::user()->id, $data->EmplId, null);

                return response()->json([
                    'status'  => 200,
                    'success' => true,
                    'message' => 'Successfully Updated ',
                    //'dd' => $request->all(),
                    // 'ed' => $iqama_renewal_record
                ]);
                // temporary stop
                // (new EmployeeDataService())->updateAnEmployeeIqamaExpireDate($iqama_renewal_record->EmplId, $request->iqama_expire_date);
            }

            if ($update > 0) {

                (new AuthenticationDataService())->InsertLoginUserActivity(34, 2, Auth::user()->id, $request->emp_auto_id, null);

                return response()->json([
                    'status'  => 200,
                    'success' => true,
                    'message' => 'Update Successfully Completed',
                ]);
            } else {
                return response()->json([
                    'status'  => 401,
                    'success' => false,
                    'message' => 'Update Operation Failed',
                ]);
            }
        } catch (Exception $ex) {
            return response()->json([
                'status'  => 500,
                'success' => false,
                'message' => 'System Operation Failed: ' . $ex->getMessage(),
            ]);
        }
    }

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




    public function searchAnEmployeeIqamaRenewalExpenseApprovalPendingRecordsAJAXRequest(Request $request)
    {

        try {
            $find_employee = (new EmployeeDataService())->searchAnEmployeeWithImportantInformationAsShowByMultitypeParameter($request->searchValue, $request->searchType, Auth::user()->branch_office_id);
            if ($find_employee) {
                $records = (new EmployeeAdvanceDataService())->getAnEmployeeIqamaExpensePendingOrApprovedRecordsByApprovalStatus($find_employee->emp_auto_id, $request->approval_status);
            } else {
                $records =  (new EmployeeAdvanceDataService())->getIqamaRenewalExpenseApprovalPendingAllRecords(Auth::user()->branch_office_id); // 100 records

            }
            return  response()->json(['status' => 200, 'success' => true, 'data' => $records]);
        } catch (Exception $ex) {
            return  response()->json(['status' => 404, 'success' => false, 'message' => "System Operation Failed", 'error' => "error"]);
        }
    }




    // multi employee iqama renewal expense approval


    public function approveOfMultiEmployeeeIqamaRenewalExpenseRecord(Request $request)
    {
        try {

            //  return $request->all();

            $iqama_renewal_auto_id_list = $request->iqama_renewal_auto_id;
            $counter = 0;
            foreach ($iqama_renewal_auto_id_list as $iqama_renewal_auto_id) {
                // if ($request->has('iqama_renewal_checkbox-' . $iqama_renewal_auto_id)) {

                $iqama_renewal_record = (new EmployeeAdvanceDataService())->findAnEmployeeIqamaExpenseRecordByRecordId($iqama_renewal_auto_id);
                $is_allowed = (new  FiscalYearDataService())->checkThisOperationIsAllowInTheRunningFiscalYear($iqama_renewal_record->EmplId, $iqama_renewal_record->renewal_date);
                if ($is_allowed && $iqama_renewal_record) {
                    $counter++;
                    $updateData = (new EmployeeAdvanceDataService())->approvedPendingIqamaExpenseRecordForSingleEmpl($iqama_renewal_auto_id);
                    // temporary stop
                    // if(is_null($iqama_renewal_record->iqama_expire_date)== false){
                    //     (new EmployeeDataService())->updateAnEmployeeIqamaExpireDate($iqama_renewal_record->EmplId, $iqama_renewal_record->iqama_expire_date);
                    // }
                }
                // }
            }

            return response()->json([
                'status'  => 200,
                'message' => $counter . ' Records Successfully Updated'
            ]);
        } catch (Exception $ex) {

            return response()->json([
                'status'  => 422,
                'message' => 'No record selected.'
            ]);
        }
    }


    // delete expences search
    public function deleteAnEmployeeIqamaAnualExpenseBeforeApproval($id)
    {

        // return response()->json(['success' => true, 'status' => 200, "data"]);

        try {
            $iqama_renewal_record = (new EmployeeAdvanceDataService())->findAnEmployeeIqamaExpenseRecordByRecordId($id);
            if ((new  FiscalYearDataService())->checkThisOperationIsAllowInTheRunningFiscalYear($iqama_renewal_record->EmplId, $iqama_renewal_record->renewal_date) == false) {
                return  response()->json(['status' => 404, 'success' => false, 'message' => "Employee Account is Closed. Delete is not Possible", 'error' => "error"]);
            }
            $delete = (new EmployeeAdvanceDataService())->deleteAnEmployeeIqamaExpenseRecordByRecordId($id);
            if ($delete) {
                (new AuthenticationDataService())->InsertLoginUserActivity(34, 3, Auth::user()->id, $iqama_renewal_record->EmplId, null);

                return  response()->json(['status' => 200, 'success' => true, 'message' => "Successfully Deleted"]);
            } else {
                return  response()->json(['status' => 404, 'success' => false, 'message' => "Record Not Found ", 'error' => "error"]);
            }
        } catch (Exception $ex) {
            return  response()->json(['status' => 404, 'success' => false, 'message' => "System Operation Failed", 'error' => "error"]);
        }
    }

    // Import Work Record Excel File to Temporary Table


    // Employee Advance Setting Json Reponse
    public function findanEmployeeWithAdvanceSetting(Request $request)
    {
        return response()->json(['status' => 200, 'success' => true,  'dd' => $request->all()]);
        // $anEmployee  = (new EmployeeDataService())->getAnEmployeeInfoWithSalaryDetailsJoinQueryByEmpId($request->emp_id,null);

        $anEmployee  = (new EmployeeDataService())->getAnEmployeeInfoWithSalaryDetailsInformationByEmployeeID($request->emp_id, Auth::user()->branch_office_id);
        if ($anEmployee) {
            $anEmployee  = (new EmployeeAdvanceDataService())->processAnEmployeeAdvance($anEmployee);
            $salaryDetatils = (new EmployeeDataService())->getAnEmployeeSalaryDetailsByEmpAutoId($anEmployee->emp_auto_id);

            return response()->json([
                'status' => 200,
                'success' => true,
                'totalIqama' => $anEmployee->iqama_renewal_cost_total_amount,
                'totalPaidIqama' => $anEmployee->iqama_renewal_total_paid_Amount,
                'totalOthers' => $anEmployee->other_advance_total_amount,
                'totalPaidOthers' => $anEmployee->other_advance_total_paid_Amount,
                'empAutoId' => $anEmployee->emp_auto_id,
                'salaryDetatils' => $salaryDetatils,
                'employee' => [
                    'employee_name' => $anEmployee->employee_name,
                    'employee_id' => $anEmployee->employee_id,
                    'akama_no' => $anEmployee->akama_no,
                    'hourly_employee' => $anEmployee->hourly_employee,
                    'passfort_no' => $anEmployee->passfort_no
                ],
            ]);
        } else
            // return response()->json(['status' => 'error']);
            return response()->json(['status' => 404, 'success' => false, 'error' => 'error', 'message' => 'Employee Not Found']);


        //   $anEmployee  = (new EmployeeDataService())->searchingAnEmployeeInfoByMultitypeParameter((int)$request->emp_id,'employee_id',Auth::user()->branch_office_id);
        //       return response()->json(['status' =>404, 'success' => false, 'error'=>'error', 'message' => 'Employee Not Found' ,'dd'=> $anEmployee,'pp'=>$request->all()]);
        //   if (count($anEmployee) >= 2) {
        //      //   return json_encode(['success' => false, 'status' => 404, 'error' => 'error','message'=>'Multiple Employee Not found']);
        //          return response()->json(['status' =>404, 'success' => false, 'error'=>'error', 'message' => 'Employee Not Found']);

        //     }else{

        //         $anEmployee  =  $anEmployee[0];
        //         $anEmployee  = (new EmployeeAdvanceDataService())->processAnEmployeeAdvanceForFiscalYear($anEmployee);
        //         $salaryDetatils = (new EmployeeDataService())->getAnEmployeeSalaryDetailsByEmpAutoId($anEmployee->emp_auto_id);

        //         return response()->json([
        //             'status' =>200, 'success' => true,
        //             'totalIqama' => $anEmployee->iqama_renewal_cost_total_amount,
        //             'totalPaidIqama' => $anEmployee->iqama_renewal_total_paid_Amount,
        //             'totalOthers' => $anEmployee->other_advance_total_amount,
        //             'totalPaidOthers' => $anEmployee->other_advance_total_paid_Amount,
        //             'empAutoId' => $anEmployee->emp_auto_id,
        //             'salaryDetatils' => $salaryDetatils,
        //             'employee' => ['employee_name'=> $anEmployee->employee_name,'employee_id'=> $anEmployee->employee_id,
        //             'akama_no'=> $anEmployee->akama_no,'hourly_employee'=> $anEmployee->hourly_employee,'passfort_no'=>$anEmployee->passfort_no],
        //         ]);

        //     }


    }

    public function updateAdvanceInstallAmount(Request $request)
    {

        return response()->json(['success' => true, 'status' => 200, "data" => $request->all()]);

        if ((int) $request->operation_type == 2) {
            // 2 =  Employee Food Setting

            $emp_auto_ids = (new EmployeeAttendanceDataService())->getEmployeesAutoIdsThoseAreInMonthlyWorkRecord($request->project_id, $request->month, $request->year);
            $update_status = (new EmployeeDataService())->updateMultipleEmployeesFoodAmountByMultipleEmpID($emp_auto_ids, $request->amount);
            return response()->json(['success' => true, 'status' => 200, "data" => $request->all()]);

            Session::flash('success',  'Successfully Update Employee Food Amount');
            return redirect()->back();
        } else if ((int) $request->operation_type == 1) {

            // 1 = single emp advance setting
            $nextPayIqama = (float) $request->nextPayIqama;
            $nextPayOthers = (float) $request->nextPayOthers;
            if ($nextPayOthers == "") {
                $nextPayOthers = 0;
            } elseif ($nextPayOthers == "" && $nextPayIqama == "") {
                $nextPayIqama = 0;
                $nextPayOthers = 0;
            } elseif ($nextPayIqama == "") {
                $nextPayIqama = 0;
            } else {
                $nextPayIqama = (float) $request->nextPayIqama;
                $nextPayOthers =  (float) $request->nextPayOthers;
            }
            // Here id means Employee Auto Id
            $update1 = (new EmployeeDataService())->updateEmployeeIqamaAdvaceInstallAmount($request->id, $nextPayIqama);
            $update = (new EmployeeDataService())->updateEmployeeOtherAdvaceInstallAmount($request->id, $nextPayOthers);

            if ($update || $update1) {
                return response()->json(['success' => true, 'status' => 200, "data" => $request->all()]);

                Session::flash('success', 'Successfully Update Advance Setting');
                return redirect()->back();
            } else {

                return response()->json(['success' => true, 'status' => 200, "data" => $request->all()]);

                Session::flash('error', 'Update Operation Failed, Please Try Again!');
                return redirect()->back();
            }
        } else {
            return response()->json(['success' => true, 'status' => 200, "data" => $request->all()]);

            return redirect()->back();
        }
    }
}
