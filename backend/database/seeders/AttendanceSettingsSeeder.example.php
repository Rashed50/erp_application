<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttendanceSettingsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('attendance_settings')->updateOrInsert(
            ['name' => 'default'],
            [
                'standard_work_hours' => 8.00,
                'overtime_threshold_hours' => 8.00,
                'default_break_minutes' => 60,
                'default_shift_start' => '09:00:00',
                'default_shift_end' => '18:00:00',
                'deduct_break_from_worked_hours' => true,
                'overtime_requires_approval' => true,
                'allow_manual_attendance' => true,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }
}
