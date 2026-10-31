<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeeInventoryReturnsSeeder extends Seeder
{
    public function run(): void
    {
        $employeeId = DB::table('employee_info')->where('employee_code', 'DEMO-EMP-102')->value('id');
        if (!$employeeId) return;
        $issue = DB::table('employee_inventory')->where('employee_id', $employeeId)
            ->where('issue_date', '2026-02-01')->first();
        if (!$issue || !$issue->warehouse_id) {
            $this->command?->warn('Skipping returns: matching inventory issue/warehouse missing.');
            return;
        }
        DB::transaction(function () use ($issue, $employeeId) {
            $exists = DB::table('employee_inventory_returns')
                ->where('employee_inventory_id', $issue->id)
                ->where('return_date', '2026-02-03')->exists();
            if ($exists) return;
            $remaining = (float) $issue->quantity - (float) $issue->returned_qty;
            if ($remaining < 1) return;
            DB::table('employee_inventory_returns')->insert([
                'employee_inventory_id' => $issue->id, 'employee_id' => $employeeId,
                'item_id' => $issue->item_id, 'serial_id' => $issue->serial_id,
                'destination_warehouse_id' => $issue->warehouse_id,
                'quantity' => 1, 'return_date' => '2026-02-03',
                'return_condition' => 'good', 'return_reason' => 'Demo return',
                'remarks' => 'Demo only; not posted to stock.',
                'created_at' => now(), 'updated_at' => now(),
            ]);
            DB::table('employee_inventory')->where('id', $issue->id)->update([
                'returned_qty' => (float) $issue->returned_qty + 1,
                'status' => 'returned', 'updated_at' => now(),
            ]);
        });
    }
}
