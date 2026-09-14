<?php
// app/Imports/PartialSalaryImport.php

namespace  Modules\Payroll\Imports;

use Modules\Payroll\Entities\PartialSalary;
use App\Models\{EmployeeInfo,MonthlyWorkHistory};
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PartialSalaryImport implements ToCollection, WithHeadingRow, SkipsOnFailure
{
    use SkipsFailures;

    private $projectId;
    private $month;
    private $year;
    private $paidAt;
    private $employees;
    private $errors = [];
    private $successCount = 0;

    public function __construct($projectId, $month, $year, $paidAt)
    {
        $this->projectId = $projectId;
        $this->month = $month;
        $this->year = $year;
        $this->paidAt = $paidAt;
        $this->employees = EmployeeInfo::pluck('emp_auto_id', 'employee_id')->toArray();
       // $this->employees = EmployeeInfo::select('emp_auto_id','employee_name', 'employee_id','akama_no')->get();
    }



    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $rowIndex = $index + 2; // +2 because of 0-index and heading row

            // $result = $employees->firstWhere('employee_id', $employeeId);

            // Validate employee code exists
            if (!isset($this->employees[$row['employee_id']])) {
                $this->errors[] = "Row {$rowIndex}: Employee ID '{$row['employee_id']}' not found";
                continue;
            }

            // Validate amount
            if (!isset($row['amount']) || !is_numeric($row['amount']) || $row['amount'] < 0) {
                $this->errors[] = "Row {$rowIndex}: Invalid amount '{$row['amount']}'";
                continue;
            }

            $emp_auto_id = $this->employees[$row['employee_id']];
            $arecord = MonthlyWorkHistory::where('emp_id',  $emp_auto_id)
                    ->where('month_id', $this->month)
                    ->where('year_id', $this->year)
                    ->select('total_work_day', 'paid_leave', 'total_hours', 'overtime','work_project_id')
                    ->first();
            if($arecord ==null){
                 $this->errors[] = "Row {$rowIndex}:  Word Record Not Found";
            }

            // Create record
            try {
                PartialSalary::create([
                    'emp_auto_id' =>   $emp_auto_id,
                    'month' => $this->month,
                    'year' => $this->year,
                    'amount' => $row['amount'],
                    'paid_at' => $this->paidAt,
                    'project_id' => $arecord->work_project_id,
                    'inserted_by' => Auth::id()
                ]);
                $this->successCount++;
            } catch (\Exception $e) {
                $this->errors[] = "Row {$rowIndex}: Database error - " . $e->getMessage();
            }
        }
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getSuccessCount()
    {
        return $this->successCount;
    }
}
