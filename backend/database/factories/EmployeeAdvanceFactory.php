<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\EmployeeAdvance;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EmployeeAdvance>
 */
class EmployeeAdvanceFactory extends Factory
{
    /**
     * A 12,000 advance recovered in 3 installments from January 2026.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'employee_id' => Employee::factory(),
            'advance_date' => '2025-12-20',
            'amount' => 12000,
            'installment_count' => 3,
            'installment_amount' => 4000,
            'deduction_start_month' => '2026-01-01',
            'purpose' => fake()->randomElement(['Medical', 'Family need', 'House rent']),
            'recovered_amount' => 0,
            'status' => EmployeeAdvance::STATUS_RUNNING,
        ];
    }
}
