<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeeAdvancesSeeder extends Seeder
{
    public function run(): void
    {
        $employeeId = DB::table('employee_info')->where('employee_code', 'DEMO-EMP-101')->value('id');
        if (!$employeeId) {
            $this->command?->warn('Skipping advances: demo employee missing.');
            return;
        }
        // The SQL schema has no unique advance reference; match by employee/date/purpose.
        DB::table('employee_advances')->updateOrInsert(
            ['employee_id' => $employeeId, 'advance_date' => '2026-01-15', 'purpose' => 'Demo advance'],
            [
                'amount' => 12000, 'installment_count' => 6,
                'installment_amount' => 2000, 'deduction_start_month' => '2026-02-01',
                'recovered_amount' => 0, 'status' => 'Running',
                'remarks' => 'Demo only; not an actual disbursement.', 'updated_at' => now(),
            ]
        );
    }
}
