<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttendanceSettingsSeeder extends Seeder
{
    public function run(): void
    {
        // DB::table('attendance_settings')->updateOrInsert(
        //     ['name' => 'default'],
        //     [
        //         'standard_work_hours' => 8.00,
        //         'overtime_threshold_hours' => 8.00,
        //         'default_break_minutes' => 60,
        //         'default_shift_start' => '09:00:00',
        //         'default_shift_end' => '18:00:00',
        //         'deduct_break_from_worked_hours' => true,
        //         'overtime_requires_approval' => true,
        //         'allow_manual_attendance' => true,
        //         'updated_at' => now(),
        //         'created_at' => now(),
        //     ]
        // );

         // Insert a default global attendance setting
        DB::table('attendance_settings')->insert([
            'name' => 'default',
            'standard_work_hours' => 8.00,
            'overtime_threshold_hours' => 8.00,
            'default_break_minutes' => 60,
            'default_shift_start' => '09:00:00',
            'default_shift_end' => '18:00:00',
            'late_grace_minutes' => 10,
            'early_leave_grace_minutes' => 5,
            'deduct_break_from_worked_hours' => true,
            'overtime_requires_approval' => true,
            'allow_manual_attendance' => true,
            'allow_manual_override_of_device_logs' => false,
            'default_daily_status' => 'present',
            'rules' => json_encode([
                'weekend_days' => ['Saturday', 'Friday'],
                'holiday_policy' => 'paid',
                'grace_period_enabled' => true
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

         // Insert a standard day shift
        DB::table('attendance_shifts')->insert([
            'name' => 'Standard Day Shift',
            'code' => 'DAY_STD',
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
            'break_minutes' => 60,
            'standard_work_hours' => 8.00,
            'overtime_threshold_hours' => 8.00,
            'late_grace_minutes' => 10,
            'early_leave_grace_minutes' => 5,
            'crosses_midnight' => false,
            'is_active' => true,
            'settings' => json_encode([
                'flexible_start' => false,
                'half_day_hours' => 4.00,
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
