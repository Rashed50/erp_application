<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentsSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Administration', 'Human Resources', 'Finance', 'Operations','Production','Workshop', 'Store & Inventory', 'Sales'] as $name) {
            DB::table('departments')->updateOrInsert(
                ['name' => $name],
                ['description' => $name . ' department', 'status' => 1, 'updated_at' => now()]
            );
        }
    }
}
