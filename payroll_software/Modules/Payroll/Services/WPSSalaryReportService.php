<?php



namespace Modules\Payroll\Services;
use Illuminate\Support\Facades\DB;
use Modules\Payroll\Imports\ImportWPSEmployeeForSalaryPreview;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\{EmployeeInfo,MonthlyWorkHistory,SalaryHistory};


class WPSSalaryReportService
{
    // public function importWPSEmployeeForSalaryPreview($document_url)
    // {
    //     $import = new ImportWPSEmployeeForSalaryPreview($document_url);
    //     Excel::import($import, $document_url);


    //     return $import;
    // }

    public function getAnEmployeeInfoWithSalaryDetailsForSalaryExcelFileUpload($employee_id)
    {
         return   EmployeeInfo::where('employee_infos.employee_id', $employee_id)
                        ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                        ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                        ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
                        ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                        ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                        ->orderBy('employee_id')
                        ->first();
    }

        public function getAnWPSEmployeeSalaryRecordForPreviewSalaryUsingExcelUpload($employee_id, $month,$year,$project_id,$branch_office_id=1){

        return   EmployeeInfo::select('employee_infos.emp_auto_id','employee_infos.employee_id','employee_infos.akama_no',
            'employee_infos.employee_name','employee_categories.catg_name as designation','project_infos.proj_name','salary_histories.*'
            )
            ->where('salary_histories.slh_month', $month)
            ->where('salary_histories.slh_year', $year)
           // ->whereNotNull('salary_histories.slh_paid_method')
           // ->where('employee_bank_details.is_active', 1) // only active bank account
          //  ->where('salary_histories.project_id', $project_id)
            ->where('employee_infos.employee_id', $employee_id)
         //   ->where('salary_histories.branch_office_id',$branch_office_id)
           // ->leftjoin('employee_bank_details', 'employee_infos.emp_auto_id', '=', 'employee_bank_details.emp_auto_id')
           // ->leftjoin('bank_names', 'employee_bank_details.bank_id', '=', 'bank_names.bn_auto_id')
            ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
            ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
            ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
            ->first();
    }


    public function generateSalaryProcessNotYetCompletedEmployeesReport($project_ids, $month, $year)
    {
        // Implement the logic to generate the report based on the provided filters
        // You can use the filters to query the database and retrieve the relevant data
        // Then, you can format the data as needed and return it or save it to a file

        // Example: Fetching employees whose salary process is not yet completed for the given month and year
        // $month = $filters['month'] ?? null;
        // $year = $filters['year'] ?? null;

        $worked_emp_auto_ids = MonthlyWorkHistory::where('month_id', $month)
                                        ->where('year_id', $year)
                                        ->pluck('emp_id')
                                        ->toArray();

        $salary_emp_auto_ids = SalaryHistory::where('slh_month', $month)
                                        ->where('slh_year', $year)
                                        ->pluck('emp_auto_id')
                                        ->toArray();



       return $employees = MonthlyWorkHistory::whereNotIn('monthly_work_histories.emp_id', $salary_emp_auto_ids)
                                ->where('monthly_work_histories.month_id', $month)
                                ->where('monthly_work_histories.year_id', $year)
                       // ->whereIn('work_project_id ', $project_ids)
                        ->leftjoin('employee_infos', 'monthly_work_histories.emp_id', '=', 'employee_infos.emp_auto_id')
                        ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                        ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
                        ->select('employee_infos.employee_id', 'employee_infos.employee_name','employee_infos.akama_no','employee_infos.job_status', 'employee_categories.catg_name as designation',
                         'project_infos.proj_name','monthly_work_histories.*')
                        ->get();


    }
}
