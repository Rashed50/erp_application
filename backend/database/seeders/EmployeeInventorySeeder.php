<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeeInventorySeeder extends Seeder
{
    public function run(): void
    {
        $employeeId = DB::table('employee_info')->where('employee_code', 'DEMO-EMP-102')->value('id');
        $itemId = DB::table('item_names')->orderBy('item_id')->value('item_id');
        $warehouseId = DB::table('inventory_warehouses')->orderBy('id')->value('id');
        if (!$employeeId || !$itemId || !$warehouseId) {
            $this->command?->warn('Skipping employee inventory: demo employee, item_names or inventory_warehouses not available.');
            return;
        }
        // Demo record only: does not change inventory_stocks or create stock ledger transactions.
        DB::table('employee_inventory')->updateOrInsert(
            ['employee_id' => $employeeId, 'item_id' => $itemId, 'issue_date' => '2026-02-01'],
            [
                'warehouse_id' => $warehouseId, 'quantity' => 1, 'returned_qty' => 0,
                'status' => 'issued', 'condition' => 'good',
                'remarks' => 'Demo issue only; not posted to stock.', 'updated_at' => now(),
            ]
        );
    }
}
