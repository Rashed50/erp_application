<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database: access control, the location
     * hierarchy, and the predefined chart of accounts the general ledger
     * posts to. Demo records are separate: `php artisan db:seed --class=DemoDataSeeder`.
     */
    public function run(): void
    {
        
        DB::transaction(function () {

            $this->call(PermissionSeeder::class);
            $this->call(RoleSeeder::class);
            $this->call(SuperAdminSeeder::class);
            $this->call(ChartOfAccountSeeder::class);

            $this->call(DivisionSeeder::class);
            $this->call(DistrictSeeder::class);
            $this->call(UpazilaSeeder::class);
            $this->call(UnionSeeder::class);
            $this->call(PourashavaSeeder::class);
            $this->call(DepartmentsSeeder::class);
            $this->call(DesignationsSeeder::class);
            $this->call(ItemUnitSeeder::class);

            // for local machine development, not for production. Demo data is separate.
            // $this->call(CustomerSeeder::class);
            // $this->call(WorkOrderSeeder::class);
            // $this->call(EmployeeSeeder::class);
            // $this->call(EmployeeInfoSeeder::class);
            // $this->call(EmpWorksSeeder::class);
            // $this->call(HrDemoDataSeeder::class);
            // $this->call(CustomerWorkOrderSeeder::class);
            // $this->call(ItemSeeder::class);

        });
    }
}
