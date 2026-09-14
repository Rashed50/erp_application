<?php


namespace Modules\Payroll\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\DataServices\{CompanyDataService, AuthenticationDataService, SalaryProcessDataService};
use App\Http\Controllers\DataServices\EmployeeDataService;
use App\Http\Controllers\Admin\Helper\HelperController;
use App\Http\Requests\EmpBonusFormRequest;
use Illuminate\Support\Facades\Auth;
use App\Jobs\ProcessProjectSalary;
use Exception;

class EmployeeBonusController extends Controller
{

    function __construct()
    {
        $this->middleware('permission:salary_bonus_add', ['only' => ['index', 'storeNewEmployeeBonusSalaryInformation', 'createBonusSalarySheet']]);
        $this->middleware('permission:salary_bonus_edit', ['only' => ['getAnEmployeeBonusSalaryRecords']]);
        $this->middleware('permission:salary_bonus_delete', ['only' => ['deleteAnEmployeeBonusSalaryRecordByBonusId']]);
    }



    public function index()
    {
        //    dd(1200);
        //    ProcessProjectSalary::dispatch([]);//->delay(now()->addMinutes(5));

        return view(
            'payroll::pages.Payroll.employee_bonus',
        );
    }


    // Ajax request to store new bouns record
    public function storeNewEmployeeBonusSalaryInformation(EmpBonusFormRequest $request)
    {
        try {
            $emp_auto_id = $request->emp_auto_id;
            $month = $request->month;
            $year = $request->year;
            $bonus_amount = $request->bonus_amount;
            $remarks = $request->remarks;
            $bonus_type =  $request->bonus_type;

            $isSaved = (new SalaryProcessDataService())->insertEmployeeBonusRecord($emp_auto_id, $bonus_amount, $bonus_type, $month, $year, $remarks, Auth::user()->id);
            if ($isSaved == -1) {
                return response()->json(['status' => 203, 'success' => false, 'error' => 'This Employee Bonus Record Already Exist']);
            } else if ($isSaved <= 0) {
                return response()->json(['status' => 409, 'success' => false, 'error' => 'Data Missing and Operation Failed']);
            } else {
                $arecord = (new SalaryProcessDataService())->getAnEmployeeBonusRecordByBonusAudoId($isSaved);

                return response()->json(['status' => 200, 'success' => true, 'data' => $arecord]);
            }
        } catch (Exception $ex) {
            return response()->json(['status' => 401, 'success' => false, 'data' => 'something error']);
        }
    }
    public function getAnEmployeeBonusSalaryRecords(Request $request)
    {
        //  return response()->json(['status' => 200, 'success' => true, 'records' => $request->all()]);

        try {

            $emp = (new EmployeeDataService())->searchingAnEmployeeInfoByMultitypeParameter($request->searchValue, $request->searchType, Auth::user()->branch_office_id);
            if (count($emp) == 0) {
                return response()->json(['status' => 404, 'success' => false, 'error' => 'error', 'message' => 'Employee Not Found']);
            } else if (count($emp) > 1) {
                return response()->json(['status' => 404, 'success' => false, 'error' => 'error', 'message' => 'Multiple Employees Found']);
            } else {
                $records = (new SalaryProcessDataService())->getAnEmployeeBonusRecordsWithEmployeeDetails($emp[0]->emp_auto_id, null, null);

                return response()->json(['status' => 200, 'success' => true, 'records' => $records]);
            }
        } catch (Exception $ex) {
            return response()->json(['status' => 404, 'success' => false, 'error' => 'error', 'message' => 'System Operation Failed']);
        }
    }



    public function deleteAnEmployeeBonusSalaryRecordByBonusId($bonus_auto_id)

    {
        //   return response()->json(['status' => 201, 'success' => true, 'records' => $bonus_auto_id]);

        try {

            $arecord = (new SalaryProcessDataService())->getAnEmployeeBonusRecordByBonusAudoId($bonus_auto_id);
            $isPaidSalary = (new SalaryProcessDataService())->checkAnEmployeeSalaryIsAlreadyPaid($arecord->emp_auto_id, $arecord->month, $arecord->year);
            if (!$isPaidSalary) {
                $isDeleted = (new SalaryProcessDataService())->deleteAnEmployeeBonusRecordByBonusAutoId($bonus_auto_id);

                (new AuthenticationDataService())->InsertLoginUserActivity(32, 3, Auth::user()->id, $arecord->emp_auto_id, null);
                return response()->json(['status' => 200, 'success' => true, 'message' => 'Successfully Deleted', 'arecord' => $arecord]);
            } else {
                return response()->json(['status' => 401, 'success' => true, 'message' => 'Selected Month Of Salary Already Paid,Operation Denied', 'arecord' => $arecord]);
            }
        } catch (Exception $ex) {
            return response()->json(['status' => 404, 'success' => false, 'error' => 'error', 'message' => 'System Operation Failed']);
        }
    }


    // ========================== Bonus Report ===============================
    // from salary report page
    public function processEmployeeBonusDetailsReport(Request $request)
    {
        try {
            if ($request->bonus_report_type == 1) {
                $records  = (new SalaryProcessDataService())->processEmployeesBonusdetailsReport(
                    $request->employee_id,
                    $request->bonus_type,
                    $request->from_date,
                    $request->to_date,
                    Auth::user()->branch_office_id
                );
                $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
                $login_name  = Auth::user()->name;
                return view('admin.report.salary.bonus.emp_bonus_details_report', compact('records', 'login_name', 'company'));
            }
        } catch (Exception $ex) {
            return 'Operation Failed, Please Try Again  ' . $ex;
        }
    }

    // from bonus page
    public function createBonusSalarySheet(Request $request, $bonus_auto_id)
    {

        if ($request->has('operation_type')) {
            // report from bonus page
            return $this->getEmployeeBonusReport($request);
        } else {

            $record  = (new SalaryProcessDataService())->getAnEmployeeBonusRecordByBonusAudoId($bonus_auto_id);
            $employee = (new EmployeeDataService())->getAnEmployeeInfoWithSalaryDetailsByEmpAutoId($record->emp_auto_id);
            $month = (new HelperController())->getMonthName($record->month);
            $year = $record->year;
            $company = (new CompanyDataService())->findCompanryProfile();
            if ($employee == null) {
                return 'Employee Not Found, The employee might be inactive now, Please Check Employee Job Status and Try Again';
            }
            return view('admin.salary-generate.single_employee.salary_bonus_sheet', compact('employee', 'record', 'month', 'year', 'company'));
        }
    }


    // bonus report
    public function getEmployeeBonusReport(Request $request)
    {


        $records = (new SalaryProcessDataService())->getEmployeeBonusRecordsWithEmployeeDetailsByDateToDate($request->from_date, $request->to_date);
        // $month = (new HelperController())->getMonthName($record ->month);
        // $year = $record ->year;
        $company = (new CompanyDataService())->findCompanryProfile();
        //dd($records);
        $login_name  = Auth::user()->name;

        return view('admin.emp_bonus.emp_bonus_report', compact('records', 'company', 'login_name'));
    }
}
