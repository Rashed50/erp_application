<?php

namespace App\Imports;

use App\Models\EmployeePromotion;
use App\Http\Controllers\DataServices\EmployeePromotionDataService;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use App\Http\Controllers\DataServices\EmployeeDataService;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Illuminate\Support\Str;

class ImportEmployeePromotions implements ToModel, WithHeadingRow,WithCalculatedFormulas
{

    public $records,$records_not_found,$records_with_errors,$document_url;
    private $employee_promotion;

    public function __construct($document_url){
        $this->records = collect();
        $this->records_not_found = collect();
        $this->records_with_errors = collect();
        $this->document_url = $document_url;
        $this->employee_promotion = new EmployeePromotionDataService();

        // remove all temporary data
        $this->employee_promotion->deleteEmployeePromotionsImportedExcellDataFromTable();


    }

    public function model(array $row)
    {


        try {

            $model = new EmployeePromotion();
            $model->emp_id = $row['employee_id'];
            $model->iqama_number = $row['iqama_number'];
            $model->increment_amount = $row['increment_amount'];
            $model->salary_type = Str::lower($row['salary_type']);  // basic or hourly
            $model->prom_remarks = $row['notes'] ?? '';  // basic or hourly


            $anEmp = (new EmployeeDataService())->getAnEmployeeInfoWithSalaryDetailsForPromotionExcelFileUpload($model->emp_id);

            $isOk = true;
            if($anEmp == null){
                $isOk = false;
                $model->upload_status = "Employee Not Founddd";

                $model->employee_name = "";
                $model->akama_no = "";
                $model->catg_name = "";
                $model->sponsor_name = "";
            }
            // else if( $model->salary_type == 'hourly' && $model->increment_amount > 14 ) {
            //     $isOk = false;
            //     $model->upload_status = "Hourly Rate Greater Than 14";

            //     $model->employee_name = $anEmp->employee_name;
            //     $model->akama_no = $anEmp->akama_no;
            //     $model->catg_name = $anEmp->catg_name;
            //     $model->sponsor_name = $anEmp->spons_name;

            // }
            // else if( $model->salary_type == 'basic' && $model->increment_amount > 2600 ) {
            //     $isOk = false;
            //     $model->upload_status = "Basic Salary Greater Than 1500";

            //     $model->employee_name = $anEmp->employee_name;
            //     $model->akama_no = $anEmp->akama_no;
            //     $model->catg_name = $anEmp->catg_name;
            //     $model->sponsor_name = $anEmp->spons_name;
            // }

            if(is_null($model->emp_id)) {
                // remove empty row
            }
            else if($isOk){
                // insert data into table
                $model->upload_status = "OK" ;
                $model->prom_by = 'Managing Director';
                $model->prom_date = Carbon::now()->format('Y-m-d');
                $model->new_designation_id = $anEmp->designation_id;// $row['designation_id'];
                $model->basic_amount = $model->salary_type == 'basic' ? $model->increment_amount : 0;  // hourly employee basic 0
                $model->house_rent = 0;// $row['house_rent'];
                $model->hourly_rent = $model->salary_type == 'basic' ? number_format($model->increment_amount / 300,2) : $model->increment_amount ; //  $row['hourly_rate'];
                $model->food_allowance =  $anEmp->food_allowance ;// $row['food'];
                $model->medical_allowance = 0;// $row['medical'];
                $model->local_travel_allowance = 0;// $row['travel'];
                $model->conveyance_allowance = 0;// $row['conveyance'];
                $model->others1 = 0 ;// $row['others'];
                $model->mobile_allowance = 0;// $row['mobile_allowance'];
                $model->prom_apprv_documents = '';// $this->document_url;
                $model->entered_id = Auth::user()->id;
                $this->employee_promotion->insertEmployeePromotionsImportedExcellDataFromTable($model);

                $model->employee_name = $anEmp->employee_name;
                $model->akama_no = $anEmp->akama_no;
                $model->catg_name = $anEmp->catg_name;
                $model->sponsor_name = $anEmp->spons_name;

                    $this->records->push($model);

            }else {
                $this->records_not_found->push($model);
            }
         } catch (\Exception $e) {
            $row['errors'] = [$e->getMessage()
            ];
            $this->records_with_errors->push($row);
            return;
        }


    }
}
