<?php

namespace App\Imports;

use App\Models\MonthlyWorkHistory;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DataServices\EmployeeDataService;
use App\Http\Controllers\DataServices\EmployeeAttendanceDataService;

class ImportMonthlyWorkRecord implements ToModel, WithHeadingRow,WithCalculatedFormulas
{
        public $records,$records_not_found;
        private $project_id,$month,$year,$number_of_days_this_month,$operation_type;


        public function __construct($project_id,$month,$year,$operation_type)
        {
            $this->records = collect();
            $this->records_not_found = collect();
            $this->project_id =  $project_id;
            $this->month = (int) $month;
            $this->year = $year;
            $this->operation_type = $operation_type;
            $this->number_of_days_this_month = cal_days_in_month(CAL_GREGORIAN, $this->month, $this->year);
            // Remove all data form temp table
            (new EmployeeAttendanceDataService())->deleteEmployeeWorkRecordImportedExcellDataFromTable();
        }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {

                $model = new MonthlyWorkHistory();
                $model->emp_id = $row['emp_id']; // emp id
                $model->month_id = $this->month;
                $model->year_id = $this->year;
                $model->project_id =  $this->project_id;
                $model->total_hours =  $row['basic_hours'] == '' ? 0 : (float) $row['basic_hours'];
                $model->overtime =  $row['over_time'] == '' ? 0 : (float) $row['over_time'];
                $model->total_work_day = $row['days'] == '' ? 0 : (int) $row['days'];
                $model->paid_leave = $row['paid_leave'] == '' ? 0 : (int) $row['paid_leave'];
                $model->remarks = $row['remarks'] == '' ? '' :   $row['remarks'];
                $model->entered_id = Auth::user()->id;
                $model->spons_id = "";
                $model->spons_name = "";
                $model->catg_id = "";
                $model->catg_name = "";

                $anEmp = (new EmployeeDataService())->getSalaryActiveEmployeeInfoByEmpolyeeIDForTimeSheetUpload($model->emp_id);

                if($this->operation_type == 1) {
                    // For Preview only, do not insert data into table,
                     if($anEmp) {
                        $model->upload_status = "OK";
                        $model->employee_name = $anEmp->employee_name;
                        $model->spons_id = $anEmp->spons_id;
                        $model->spons_name = $anEmp->spons_name;
                        $model->catg_id = $anEmp->catg_id;
                        $model->catg_name = $anEmp->catg_name;
                        $this->records->push($model);
                    }else{
                        if(is_null($model->emp_id)) {
                            // remove empty row
                        }
                        else {
                            $model->upload_status = "Employee Not Found";
                            $this->records_not_found->push($model);
                        }
                    }

                    return null; // Skip further processing
                }




                $isOk = true;
                if($anEmp == null){
                    $isOk = false;
                    $model->upload_status = "Employee Not Found";
                }else if($model->total_work_day > 30) {
                    $isOk = false;
                    $model->upload_status = "Working Days Greater Than 30";
                }
                else if($model->total_work_day + $model->paid_leave > 30) {
                    $isOk = false;
                    $model->upload_status = "Total Days Greater Than 30";
                }
                else if($model->total_hours > 450 ) {
                    $isOk = false;
                    $model->upload_status = "Working Hours Greater Than 450";
                }
                else if($model->overtime > 180 ) {
                    $isOk = false;
                    $model->upload_status = "Overtime Greater Than 180";
                }else {

                    // Check if employee has a record for a different project
                    $differentProjectRecord = (new EmployeeAttendanceDataService())->getEmployeeExistingProjectForMonth(
                        $anEmp->emp_auto_id,
                        $model->month_id,
                        $model->year_id,
                        $model->project_id
                    );

                    if($differentProjectRecord) {
                        $isOk = false;
                        $model->upload_status = "Same project working record exist";
                    } else {
                        $existingRecord = (new EmployeeAttendanceDataService())->getAnEmployeeMonthlyWorkRecordForTimeSheetUpload($anEmp->emp_auto_id, $model->month_id, $model->year_id);
                        if ($existingRecord) {
                            $existingDays = $existingRecord->total_work_day;// + $existingRecord->paid_leave;
                            $newDays = $model->total_work_day;
                            $totalCombinedDays = $existingDays + $newDays;

                            if ($totalCombinedDays > 30) {
                                $isOk = false;
                                $model->upload_status = "Working Days Exceed 30 (Existing: {$existingDays}, New: {$newDays}, Total: {$totalCombinedDays})";
                            }
                            //  else if ($totalCombinedDays > $this->number_of_days_this_month) {
                            //     $isOk = false;
                            //     $model->upload_status = "Working Days Exceed {$this->number_of_days_this_month} Days of This Month (Existing: {$existingDays}, New: {$newDays}, Total: {$totalCombinedDays})";
                            // }
                        }
                    }
                }
                if(is_null($model->emp_id)) {
                    // remove empty row
                }
                else if($isOk){
                   // insert data into table

                        $model->spons_id = $anEmp->spons_id;
                        $model->spons_name = $anEmp->spons_name;
                        $model->catg_id = $anEmp->catg_id;
                        $model->catg_name = $anEmp->catg_name;

                   $model->upload_status = "OK" ;
                    (new EmployeeAttendanceDataService())->insertEmployeeWorkRecordImportedExcellData(
                        $anEmp->emp_auto_id,$model->month_id,$model->year_id,$model->project_id,$model->total_hours,$model->overtime,$model->total_work_day,$model->entered_id, $model->paid_leave, $model->remarks);
                    $model->emp_auto_id = $anEmp->emp_auto_id;
                    $model->employee_name = $anEmp->employee_name;
                    $model->hourly_employee = $anEmp->hourly_employee;
                    $this->records->push($model);

                }else {
                    $this->records_not_found->push($model);
                }


    }
}
