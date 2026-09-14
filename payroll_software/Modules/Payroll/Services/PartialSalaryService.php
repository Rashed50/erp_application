<?php

namespace Modules\Payroll\Services;


use Illuminate\Support\Facades\DB;
use Modules\Payroll\Entities\PartialSalary;
use Maatwebsite\Excel\Facades\Excel;
// use App\Models\{EmployeeInfo,MonthlyWorkHistory,SalaryHistory};

class PartialSalaryService{

   public function storePartialSalary($data)
   {
       return PartialSalary::create($data);
   }
   public function searchPartialSalaries($filters)
   {

        $query = PartialSalary::with(['employee', 'project']);

        if ($filters->filled('month')) {
            $query->where('month', $filters->month);
        }

        if ($filters->filled('year')) {
            $query->where('year', $filters->year);
        }

        if ($filters->filled('project_id')) {
            $query->where('project_id', $filters->project_id);
        }

        // Filter by employee existence
        if ($filters->filled('employee_id')) {
            $query->whereHas('employee', function($q) use ($filters) {
                $q->where('employee_id', $filters->employee_id);
            });
        }

        return $query->orderBy('year', 'desc')
                        ->orderBy('month', 'desc')
                        ->orderBy('psh_auto_id', 'desc')
                        ->get();

   }

   public function getAnEmployeeSalaryPartialPaidAmountByMonthAndYear($emp_auto_id,$month,$year){
        $re = PartialSalary::where('emp_auto_id',$emp_auto_id)->where('month',$month)->where('year',$year)->first();
        return  $re == null ? 0: $re->amount;
   }

}
