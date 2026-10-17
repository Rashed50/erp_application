<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmpWorksSeeder extends Seeder
{
    public function run(): void
    {
        // Demo data only. Never modify real employee salary/work records.
        $samples = [
            'EMP-07' => ['working_days' => 26, 'present_days' => 24, 'absent_days' => 1, 'paid_leave_days' => 1, 'unpaid_leave_days' => 0, 'overtime_hours' => 8, 'bonus' => 1500, 'other_addition' => 0, 'other_deduction' => 0],
            'EMP-08' => ['working_days' => 26, 'present_days' => 25, 'absent_days' => 0, 'paid_leave_days' => 1, 'unpaid_leave_days' => 0, 'overtime_hours' => 5, 'bonus' => 1000, 'other_addition' => 0, 'other_deduction' => 0],
            'EMP-09' => ['working_days' => 26, 'present_days' => 23, 'absent_days' => 2, 'paid_leave_days' => 1, 'unpaid_leave_days' => 0, 'overtime_hours' => 0, 'bonus' => 0, 'other_addition' => 500, 'other_deduction' => 200],
        ];

        foreach ($samples as $employeeCode => $values) {
            $employeeId = DB::table('employee_info')
                ->where('employee_code', $employeeCode)
                ->whereNull('deleted_at')
                ->value('id');

            if (!$employeeId) {
                $this->command?->warn("Skipping {$employeeCode}: employee not found. Run EmployeeInfoSeeder first.");
                continue;
            }

            // INSERT-ONLY: re-running will not overwrite work records.
            $exists = DB::table('emp_works')
                ->where('employee_id', $employeeId)
                ->where('salary_month', '2026-09-01')
                ->exists();

            if (!$exists) {
                DB::table('emp_works')->insert(array_merge([
                    'employee_id' => $employeeId,
                    'salary_month' => '2026-09-01',
                    'remarks' => 'Demo data for development/testing only',
                    'created_at' => now(),
                    'updated_at' => now(),
                ], $values));
            }
        }
    }
}
