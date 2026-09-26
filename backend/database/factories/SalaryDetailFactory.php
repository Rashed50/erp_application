<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\SalaryDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SalaryDetail>
 */
class SalaryDetailFactory extends Factory
{
    /**
     * The example from the payroll spec: 30,000 + 10,000 + 2,000 + 2,000 = 44,000 gross.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'employee_id' => Employee::factory(),
            'effective_date' => '2025-01-01',
            'basic_salary' => 30000,
            'house_rent' => 10000,
            'medical_allowance' => 2000,
            'transport_allowance' => 2000,
            'food_allowance' => 0,
            'other_allowance' => 0,
            'overtime_rate' => 0,
            'other_deduction' => 0,
            'deduction_basis' => 'basic',
            'status' => true,
        ];
    }
}
