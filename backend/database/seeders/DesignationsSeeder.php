<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DesignationsSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Chairman','Managing Director','CEO','Manager (Admin)','Manager (HR)','Manager (Finance)', 'Accountant', 'Supervisor'] as $name) {
            DB::table('designations')->updateOrInsert(
                ['name' => $name],
                ['description' => $name, 'status' => 1, 'updated_at' => now()]
            );
        }
    }
}
