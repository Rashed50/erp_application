<?php

namespace App\Imports; 

use App\Models\IqamaRenewalDetails;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use PhpOffice\PhpWord\Settings;


use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DataServices\EmployeeDataService; 
use App\Http\Controllers\DataServices\EmployeeRelatedDataService; 
use App\Http\Controllers\Admin\Helper\HelperController;
use Carbon\Carbon;
 

class ImportIqamaRenewalExpenseRecord implements ToModel, WithHeadingRow, WithCalculatedFormulas
{
        public $records,$records_not_found;
       
        public function __construct( )
        {
             $this->records = collect();
            $this->records_not_found = collect();                       
            // Remove all data from temp table         
            (new EmployeeRelatedDataService())->removeIqamaExpireUploadedAllDataFromTemporaryTable(); 
        }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {                 
                $model = new IqamaRenewalDetails();
                $model->akama_no = $row['iqama_number'];  
                $model->expire_date =   Date::excelToDateTimeObject($row['expiry_date'])->format('Y-m-d'); 
                $model->jawazat_fee = 0;// $row['jawazat_fee'] == '' ? 0 : (float) $row['jawazat_fee'];  
                $model->maktab_alamal_fee =0;// $row['maktab_alamal_fee'] == '' ? 0 : (float) $row['maktab_alamal_fee'];  
                $model->bd_amount = 0; 
                $model->medical_insurance = $row['medical_insurance'] == '' ? 0 : (float) $row['medical_insurance'];  
                $model->others_fee = 0;// $row['others_fee'] == '' ? 0 : (float) $row['others_fee'];  
                $model->jawazat_penalty = 0;// $row['jawazat_penalty'] == '' ? 0 : (float) $row['jawazat_penalty'];  
                $model->total_amount = round($row['medical_insurance']);// $row['total_amount'] == '' ? 0 : (float) $row['total_amount'];  
                $model->duration = $row['duration'];
                $model->renewal_date = Carbon::now();// $row['renewal_date']; 
                $model->remarks = "";
                $model->reference_emp_id = 5019;// $row['reference_emp_id'];
               
                $model->entered_id = Auth::user()->id;                 
                $anEmp = (new EmployeeDataService())->getAnEmployeeInfoByEmpIqamaNo($model->akama_no); 
                $isOk = true;
                if($anEmp == null){
                    $isOk = false;
                    $model->employee_id = "";
                    $model->employee_name = "Employee Not Found";
                    $model->hourly_employee = ""; 
                    $model->upload_status = "Employee Not Found";
                    $this->records_not_found->push($model);
        
                }else{
        
                    $emp_auto_id  = $anEmp->emp_auto_id;
                    $model->employee_id  = $anEmp->employee_id;
                    $model->employee_name = $anEmp->employee_name;
                    $model->hourly_employee = $anEmp->hourly_employee;
                    $anEmp = (new EmployeeRelatedDataService())->InsertIqamaRenewalExpenseUploadDataInTemporaryTable((int)$emp_auto_id, $model->employee_id, $model->akama_no, $model->expire_date,
                    $model->jawazat_fee,$model->maktab_alamal_fee,$model->bd_amount,$model->medical_insurance,$model->others_fee,$model->jawazat_penalty,$model->total_amount,$model->duration,
                    $model->renewal_date,$model->remarks,$model->reference_emp_id);
                    $model->upload_status = "OK" ;                                                                                                         
                    $this->records->push($model);
                }  
    }
}
