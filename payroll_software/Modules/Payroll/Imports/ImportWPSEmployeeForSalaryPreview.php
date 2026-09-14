<?php

namespace Modules\Payroll\Imports;

// use App\Models\EmployeePromotion;

use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use App\Http\Controllers\DataServices\{EmployeeDataService,SalaryProcessDataService};
use Modules\Payroll\Services\WPSSalaryReportService;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use stdClass;
use Illuminate\Support\Str;

class ImportWPSEmployeeForSalaryPreview implements ToModel, WithHeadingRow,WithCalculatedFormulas
{

    public $records,$records_not_found,$records_with_errors,$month;
    private $employee_promotion;

    public function __construct($month,$year,$project_id){
        $this->records = collect();
        $this->records_not_found = collect();
        $this->records_with_errors = collect();
        $this->month = $month;
        $this->year = $year;
        $this->project_id = $project_id;

       // $this->employee_promotion = new EmployeePromotionDataService();

        // remove all temporary data
      //  $this->employee_promotion->deleteEmployeePromotionsImportedExcellDataFromTable();


    }

    public function model(array $row)
    {
            $model = new \stdClass();

        try {



            $model->employee_id = $row['emp_id'];
            $model->employee_name = $row['emp_name'] ?? '';
            $model->akama_no =$row['Iqama'] ?? '';
            $model->iban = $row['iban'] ?? '';
            $model->account_no = $row['iban'] ?? '';
            $model->bank_code = $row['bank_code'] ?? '';
            $model->sponsor_name = $row['sponsor_name'] ?? '';
            $model->designation =   '';
            $model->proj_name =   '';

            $model->basic_amount = 0 ;  // hourly employee basic 0
            $model->house_rent = 0;
            $model->other_allowance = 0;
            $model->deduction = 0;
            $model->gross_amount = 0;


            $anEmp = (new WPSSalaryReportService())->getAnEmployeeInfoWithSalaryDetailsForSalaryExcelFileUpload($model->employee_id);


            //  $this->records->push($salary_record);
            //  return;
            $isOk = true;
            $model->upload_status = "Ok";
            if(is_null($model->employee_id) || trim($model->employee_id) == ''){

                $isOk = false;
                $model->upload_status = "Invalid Employee ID";
                // $model->employee_name = "";
                // $model->akama_no = "";
                // $model->catg_name = "";
                // $model->sponsor_name = "";

            }
           else if($anEmp == null){

                $isOk = false;
                $model->upload_status = "Employee Not Found";
            }
            else {

                    // if($model->iban != '' &&  Str::length($model->iban) != 24){
                    //     $model->upload_status = "IBAN Invalid";
                    //     // $model->employee_name = $anEmp->employee_name;
                    //     // $model->akama_no = $anEmp->akama_no;
                    //     // $model->catg_name = $anEmp->designation;
                    //     // $model->sponsor_name = $anEmp->spons_name;

                    // }
                    // else if($anEmp->akama_expire_date != '' &&  Carbon::parse($anEmp->akama_expire_date) < Carbon::now()){
                    //     $model->upload_status = "Iqama Expired";
                    // }else {
                    //     $model->upload_status = "OK" ;
                    // }

                    $salary_record = (new WPSSalaryReportService())->getAnWPSEmployeeSalaryRecordForPreviewSalaryUsingExcelUpload($model->employee_id, $this->month,$this->year,$this->project_id);
                    if($salary_record){

                        $other_allowance =  ($salary_record->slh_overtime_amount ?? 0 + $salary_record->house_rent + $salary_record->conveyance_allowance + $salary_record->mobile_allowance  + $salary_record->medical_allowance + $salary_record->others1 + $salary_record->local_travel_allowance  + $salary_record->slh_bonus_amount);
                        // $anEmployee->gross_salary =  $anEmployee->slh_all_include_amount -  $total_deduct_amount ;
                        $basic_salary = $salary_record->slh_all_include_amount - $other_allowance ; // food allowance is included in basic salary
                        $total_deduct_amount = $salary_record->slh_food_deduction + $salary_record->slh_saudi_tax + $salary_record->slh_iqama_advance + $salary_record->slh_other_advance + $salary_record->slh_cpf_contribution ;

                        $model->basic_amount = $basic_salary;
                        $model->house_rent = $salary_record->house_rent;
                        $model->other_allowance = $other_allowance ;
                        $model->deduction = $total_deduct_amount ;
                        $model->gross_amount = round($basic_salary + $salary_record->house_rent + $other_allowance - $total_deduct_amount ) ;

                       // $model->basic_amount = $model->hourly_employee ?  $salary_record->hourly_rent :$salary_record->basic_amount + $salary_record->food_allowance ;  // hourly employee basic 0
                        // $model->other_allowance = $salary_record->slh_overtime_amount + $salary_record->medical_allowance + $salary_record->mobile_allowance + $salary_record->others+ $salary_record->local_travel_allowance + $salary_record->slh_bonus_amount ;
                        // $model->deduction = $salary_record->slh_iqama_advance + $salary_record->slh_other_advance ;
                        // $model->basic_amount +  $model->house_rent + $model->other_allowance - $model->deduction ;
                    }

            }
            //  $this->records->push($model);
         } catch (\Exception $e) {

            $row['errors'] = [$e->getMessage()];
              $model->error = [$e->getMessage()];
          //   $this->records->push($model);

            $this->records_with_errors->push($row);

        } finally {

              $this->records->push($model);

        }


    }
}

//             "basic_hours": 0,
//             "house_rent": 0,
//             "hourly_rent": 14,
//             "mobile_allowance": 0,
//             "medical_allowance": 0,
//             "local_travel_allowance": 0,
//             "conveyance_allowance": 0,
//             "increment_no": 0,
//             "increment_amount": 0,
//             "others1": 0,
//             "food_allowance": 300,
//             "cpf_contribution": 0,
//             "saudi_tax": 0,
//             "iqama_adv_inst_amount": 1000,
//             "other_adv_inst_amount": 0,
//             "others4": 0,
//             "payment_method": "Cash",

//             "slh_auto_id": 95730,
//             "basic_amount": 2000,
//             "basic_hours": 0,
//             "house_rent": 0,
//             "hourly_rent": 0,
//             "mobile_allowance": 0,
//             "medical_allowance": 0,
//             "local_travel_allowance": 0,
//             "conveyance_allowance": 0,
//             "increment_amount": 0,
//             "food_allowance": 0,
//             "others": 0,
//             "slh_total_overtime": 0,
//             "slh_overtime_amount": 0,
//             "slh_total_salary": 2000,
//             "slh_total_hours": 240,
//             "slh_total_working_days": 30,
//             "slh_month": 2,
//             "slh_year": 2025,
//             "slh_cpf_contribution": 0,
//             "slh_saudi_tax": 0,
//             "slh_company_contribution": 0,
//             "slh_iqama_advance": 0,
//             "slh_other_advance": 0,
//             "slh_bonus_amount": 0,
//             "slh_food_deduction": 0,
//             "slh_salary_date": "2025-05-18",

//   "emp_auto_id": 2897,
//             "employee_id": 6010,
//             "akama_no": "2514227954",
//             "employee_name": "MD RASHEDUL HOQUE",
//             "designation": "Software Developer",
//             "proj_name": "ASLOOB HEAD OFFICE RIYADH",

//             "Status": 1,
//             "project_id": 22,
//             "multProject": 0,
//             "created_at": "2025-05-18T09:01:08.000000Z",
//             "updated_at": "2025-05-18T09:01:28.000000Z",
//             "updated_by": 11,
//             "slh_all_include_amount": 2000,
//             "slh_paid_method": 35,
//             "branch_office_id": 1
