<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DesignationsSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Manager', 'HR Officer', 'Accountant', 'Supervisor', 'Store Keeper', 'Sales Executive', 'Office Assistant'] as $name) {
            DB::table('designations')->updateOrInsert(
                ['name' => $name],
                ['description' => $name . ' position', 'status' => 1, 'updated_at' => now()]
            );
        }
    }
}
