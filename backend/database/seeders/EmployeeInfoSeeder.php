<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeeInfoSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['code' => 'EMP-07', 'name' => 'Demo Employee One', 'department' => 'Operations', 'designation' => 'Supervisor'],
            ['code' => 'EMP-08', 'name' => 'Demo Employee Two', 'department' => 'Store & Inventory', 'designation' => 'Store Keeper'],
            ['code' => 'EMP-09', 'name' => 'Demo Employee Three', 'department' => 'Accounts', 'designation' => 'Accountant'],
        ];
        foreach ($rows as $row) {
            $departmentId = DB::table('departments')->where('name', $row['department'])->value('id');
            $designationId = DB::table('designations')->where('name', $row['designation'])->value('id');
            if (!$departmentId || !$designationId) {
                $this->command?->warn('Skipping ' . $row['code'] . ': department/designation unavailable.');
                continue;
            }
            DB::table('employee_info')->updateOrInsert(
                ['employee_code' => $row['code']],
                [
                    'name' => $row['name'], 'joining_date' => '2026-01-01',
                    'department_id' => $departmentId, 'designation_id' => $designationId,
                    'employment_type' => 'Permanent', 'status' => 'Active',
                    'updated_at' => now(),
                ]
            );
        }
    }
}
