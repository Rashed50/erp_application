<?php

namespace Modules\Advance\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\Helper\HelperController;
use App\Http\Controllers\DataServices\CompanyDataService;
use App\Http\Controllers\DataServices\AccommodationDataService;
use App\Http\Controllers\DataServices\EmployeeAdvanceDataService;
use App\Http\Controllers\DataServices\EmployeeDataService;
use App\Http\Controllers\DataServices\EmployeeRelatedDataService;
use App\Http\Controllers\DataServices\SalaryProcessDataService;
use App\Http\Controllers\DataServices\AuthenticationDataService;
use App\Http\Controllers\DataServices\ProjectDataService;
use App\Http\Controllers\DataServices\EmployeeAttendanceDataService;
use App\Http\Controllers\DataServices\FiscalYearDataService;
use App\Http\Controllers\Admin\Helper\UploadDownloadController;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use PhpParser\Node\Expr\Cast\Double;
use App\Jobs\AdvanceProcessJob;
use Exception;

class CashRececivedController extends Controller
{


    function __construct()
    {
        // $this->middleware('permission:set-advance', ['only' => ['index']]);
        // $this->middleware('permission:employee_advance_insert', ['only' => ['index', 'insert']]);
        // $this->middleware('permission:mult_employee_advance_insert', ['only' => ['index', 'getEmployeeListForMultipleEmployeeAdvancePayment', 'multipleEmployeeAdvanceInsertRequest']]);
        // $this->middleware('permission:employee_advance_edit', ['only' => ['edit', 'update', 'updateEmployeeAdvanceInformationOld']]);
        // $this->middleware('permission:employee-advance-processing', ['only' => ['employeeAdvanceProcessingUI', 'employeeAdvanceProcessingRequest', 'updateAdvanceInstallAmount']]);
        // $this->middleware('permission:summary-adjuget', ['only' => ['emplpyeeCashDepositFormRequest', 'emplpyeeCashDepositFormRequestWithAllRecords']]);
        // $this->middleware('permission:advance-adjuget', ['only' => ['employeeMonthlyPaymentSetting']]);
        // $this->middleware('permission:emp_advance_paid_report', ['only' => ['loadEmployeeAdvanceEntryReportProcessForm']]);
        // $this->middleware('permission:advance_paper_upload_insert', ['only' => 'loadAdvancePaperUploadForm', 'uploadAdvancePaperToServer']);
        // $this->middleware('permission:advance_paper_upload_search', ['only' => 'searchAdvanceInsertedEmployeesForUploadAdvancePaper']);

        //   $this->middleware('permission:advance_paper_uploaded_delete',['only'=>'deleteAdvanceUploadedPaper']);


        $this->middleware('permission:employee_advance_delete', ['only' => ['delete', 'deleteCashDepositAdvancePayment']]);
    }
    public function advancePaymentReceivedFromEmployee(Request $request)
    {

        $input = $request->all();

        try {

             $userId = Auth::user()->id;
            $this->validate($request, [
                'emp_id' => 'required',
                'pay_amount' => 'required',
                'payment_date' => 'required',
            ], []);


            $message = "Error Occured, Please Input Correct Value'";
            $success = false;

            if ($request->record_id > 0) {

                $file_path = null;
                if ($request->has('attached_file') && $request->file('attached_file')) {
                    $file = $request->file('attached_file');
                    $file_path = (new UploadDownloadController())->uploadCashPaidByEmolyeePaper($file, null);
                }

                $month = (new HelperController())->getMonthFromDateValue($request->payment_date);
                $year = (new HelperController())->getYearFromDateValue($request->payment_date);
                $update = (new EmployeeAdvanceDataService())->updateCashPaidByEmployeeRecord($input['record_id'], $input['pay_amount'], $input['payment_date'], $year, $month, $input['payment_remarks'], $file_path, $userId);
                //     (new AuthenticationDataService())->InsertLoginUserActivity(23, 2, Auth::user()->id, $request->emp_auto_id, null);

                $message =  'Successfully Updated';
                $success = true;
                // iqama expense cach received record update activity log
                (new AuthenticationDataService())->InsertLoginUserActivity(39, 2, Auth::user()->id, $request->emp_id, 'Cash Received Updated '. $input['pay_amount']);
            } else {

                $empInfo = (new EmployeeDataService())->getAnEmployeeInfoWithSalaryDetailsByEmpId($input['emp_id']);
                if ($empInfo == null) {
                    return  response()->json(['status' => 401, 'success' => false, 'message' => "Active Employee Not Found"]);
                }

                $file_path = null;
                if ($request->has('attached_file') && $request->file('attached_file')) {
                    $file = $request->file('attached_file');
                    $file_path = 'ok'; // (new UploadDownloadController())->uploadCashPaidByEmolyeePaper($file, null);
                }
                $month = (new HelperController())->getMonthFromDateValue($request->payment_date);
                $year = (new HelperController())->getYearFromDateValue($request->payment_date);

                // 100 = cash payment
                $insertion = (new EmployeeAdvanceDataService())->insertAdvancePaidRecord($empInfo->emp_auto_id, $request->pay_amount, $year, $month, 100, $userId, $file_path, $request->payment_remarks);
                $message = $insertion > 0 ?  'Successfully Saved' : 'Error Occured, Please Input Correct Value';
                $success = true;
                 // iqama expense cach received record update activity log
                (new AuthenticationDataService())->InsertLoginUserActivity(39, 1, Auth::user()->id, $empInfo->emp_auto_id, "Cash Received ".$request->pay_amount);
            }

            if ($success) {
                return  response()->json(['status' => 200, 'success' => true, 'message' => $message]);
            } else {

                return  response()->json(['status' => 403, 'success' => false, 'message' => "Operation Failed"]);
            }
        } catch (Exception $ex) {
            return  response()->json(['status' => 403, 'success' => false, 'message' => "Operation Failed " . $ex]);
        }
    }


    public function deleteCashDepositAdvancePayment($id)
    {

       try {
            $record = (new EmployeeAdvanceDataService())->searchCashPaymentRecordById($id);
            $delete = (new EmployeeAdvanceDataService())->deleteEmployeeCashPaymentRecord($id);
            if ($delete) {
                // iqama expense cach received record update activity log
                (new AuthenticationDataService())->InsertLoginUserActivity(39, 3, Auth::user()->id, $record->emp_id, "Deleted Amount ". $record->adv_amount);
                return  response()->json(['status' => 200, 'success' => true, 'message' => "Successfully Deleted"]);
            } else {
                return  response()->json(['status' => 401, 'success' => false, 'message' => "Operation Failed"]);
            }
        } catch (Exception $ex) {
            return  response()->json(['status' => 401, 'success' => false, 'message' => "Operation Failed " . $ex]);
        }
    }



    public function searchCashReceivedRecord(Request $request)
    {

        try {

            $records = (new EmployeeAdvanceDataService())->getCachReceivedRecordsForListVIew($request->employee_id, $request->from_date, $request->to_date);

            return response()->json([
                'status'  => 200,
                'success' => true,
                'dd' => $request->all(),
                'data' => $records,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status'  => 500,
                'message' => 'Server Error: ' . $e->getMessage()
            ]);
        }
    }
}
