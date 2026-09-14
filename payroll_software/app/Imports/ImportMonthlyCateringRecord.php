<?php

namespace App\Imports;
 
use App\Models\CateringMonthlyRecord;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DataServices\EmployeeDataService;
use App\Http\Controllers\DataServices\CateringDataService;
use App\Http\Controllers\Admin\Helper\HelperController;

 

class ImportMonthlyCateringRecord implements ToModel, WithHeadingRow, WithCalculatedFormulas
{
        public $records,$records_not_found;
        private $month,$year;


        public function __construct($month,$year)
        {
            $this->records = collect();
            $this->records_not_found = collect();  
            $this->month = (int) $month;
            $this->year = $year;            
            // Remove all data form temp table         
            (new CateringDataService())->deleteCateringImportedTemporaryTableRecordsForExcelUpload();
        }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {                 
                $model = new CateringMonthlyRecord();
                $model->emp_id = $row['emp_id']; // emp
                $model->month = $this->month;  
                $model->month_name = (new HelperController())->getMonthName($this->month);  
                $model->year = $this->year;  
                $model->total_days =  $row['days'] == '' ? 0 : (float) $row['days'];  
                $model->amount =  $row['amount'];// == '' ? 0 : (float) $row['amount']; 
                $model->entered_id = Auth::user()->id;                 
                $anEmp = (new EmployeeDataService())->getAnEmployeeInfoByEmpolyeeIDForCateringServiceRecordUpload($model->emp_id); 

                $isOk = true;
                if($anEmp == null){
                    $isOk = false;
                    $model->emp_auto_id = "";
                    $model->upload_status = "Employee Not Found";
                    $model->employee_name = "Employee Not Found";
                    $model->hourly_employee = "";

                }else{
                    $model->emp_auto_id = $anEmp->emp_auto_id;
                    $model->employee_name = $anEmp->employee_name;
                    $model->hourly_employee = $anEmp->hourly_employee;
                }
                
                if($model->total_days > 31) {
                    $isOk = false;
                    $model->upload_status = "Working Days Greater Than 31";
                }
                else if($model->amount > 310 ) {
                    $isOk = false;
                    $model->upload_status = "Working Hours Greater Than 450";
                } 
                else if( $anEmp != null && (new CateringDataService())->checkAnEmployeeCateringMonthRecordAlreadyExist($anEmp->emp_auto_id,$model->month,$model->year)) {
                    $isOk = false;
                    $model->upload_status = "Duplicate Record";
                }
                if($isOk){
                   // insert data into table 
                    $model->upload_status = "OK" ;
                    (new CateringDataService())->insertCateringMonthlyImportedRecordInTemporaryTable( $anEmp->emp_auto_id,$model->month,$model->year,$model->total_days,$model->amount,$model->entered_id);                                                                                   //    $emp_auto_id,$month,$year,$days,$amount,$inserted_by
                    $this->records->push($model);
                }else {
                    $this->records_not_found->push($model);
                }                  
    }
}
