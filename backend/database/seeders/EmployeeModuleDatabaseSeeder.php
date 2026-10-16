<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeeModuleDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            DepartmentsSeeder::class,
            DesignationsSeeder::class,
            EmployeeInfoSeeder::class,
            EmployeeAdvancesSeeder::class,
            EmployeeFilesSeeder::class,
            EmployeeInventorySeeder::class,
            EmployeeInventoryReturnsSeeder::class,
        ]);
    }
}
