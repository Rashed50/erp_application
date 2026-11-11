<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\EmployeeWork;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EmployeeWork>
 */
class EmployeeWorkFactory extends Factory
{
    /**
     * A full month with perfect attendance.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'employee_id' => Employee::factory(),
            'salary_month' => '2026-01-01',
            'working_days' => 26,
            'present_days' => 26,
            'absent_days' => 0,
            'paid_leave_days' => 0,
            'unpaid_leave_days' => 0,
            'overtime_hours' => 0,
            'bonus' => 0,
            'other_addition' => 0,
            'other_deduction' => 0,
        ];
    }
}
