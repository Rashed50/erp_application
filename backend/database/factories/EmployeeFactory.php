<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'employee_code' => fake()->unique()->bothify('EMP-T####'),
            'name' => fake()->name(),
            'gender' => fake()->randomElement(Employee::GENDERS),
            'phone' => fake()->numerify('017########'),
            'email' => fake()->unique()->safeEmail(),
            'joining_date' => '2025-01-01',
            'department' => fake()->randomElement(['Accounts', 'Sales', 'Operations']),
            'designation' => fake()->randomElement(['Officer', 'Executive', 'Manager']),
            'employment_type' => 'Permanent',
            'status' => 'Active',
        ];
    }

    public function resigned(string $lastWorkingDate): static
    {
        return $this->state(['status' => 'Resigned', 'last_working_date' => $lastWorkingDate]);
    }
}
