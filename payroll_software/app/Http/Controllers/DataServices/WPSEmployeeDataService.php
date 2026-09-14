<?php

namespace App\Http\Controllers\DataServices;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\{EmployeeInfo,EmployeeBankDetails};
use App\Models\SalaryDetails;
// use App\Models\MonthlyWorkHistory;
use App\Models\User;
// use App\Models\EmployeeDetails;
use Carbon\Carbon;


class WPSEmployeeDataService{

    public function getWPSEmployeeInfoWithBankDetailsForAttenanceSummaryReportByEmployeeId($employee_id,$branch_office_id)
    {
        return EmployeeBankDetails::select('ebd_auto_id','employee_infos.employee_id','employee_infos.emp_auto_id','employee_infos.email','acc_holder_name','acc_number','acc_iban','employee_bank_details.is_active','remarks','employee_infos.employee_name',
        'employee_infos.akama_no','employee_infos.mobile_no','employee_infos.hourly_employee','employee_categories.catg_name','bank_names.bn_name','bank_names.bank_code','salary_details.payment_method')
                ->leftjoin('employee_infos', 'employee_bank_details.emp_auto_id', '=', 'employee_infos.emp_auto_id')
                ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->leftjoin('bank_names', 'employee_bank_details.bank_id', '=', 'bank_names.bn_auto_id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->where('employee_infos.employee_id',$employee_id)
                ->where('employee_infos.branch_office_id',$branch_office_id)
                ->where('employee_bank_details.is_active',1)->first();
    }

    public function getListOfEmployeeAutoIdThoseSalaryPaidToBankByProjectIds($project_ids, $branch_office_id)
    {
            return EmployeeInfo::where('salary_details.payment_method', 'Bank')
                        ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                        ->where('employee_infos.job_status', 1)
                        ->whereIn('employee_infos.project_id', $project_ids)
                        ->where('employee_infos.branch_office_id', $branch_office_id)
                        ->pluck('employee_id')
                        ->toArray();
    }

}
