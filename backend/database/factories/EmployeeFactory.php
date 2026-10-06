<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Designation;
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
            'department_id' => fn () => Department::firstOrCreate(['name' => fake()->randomElement(['Accounts', 'Sales', 'Operations'])])->id,
            'designation_id' => fn () => Designation::firstOrCreate(['name' => fake()->randomElement(['Officer', 'Executive', 'Manager'])])->id,
            'employment_type' => 'Permanent',
            'status' => 'Active',
        ];
    }

    /**
     * Put the employee in the named department, creating it if needed.
     */
    public function inDepartment(string $name): static
    {
        return $this->state(fn () => ['department_id' => Department::firstOrCreate(['name' => $name])->id]);
    }

    /**
     * Give the employee the named designation, creating it if needed.
     */
    public function withDesignation(string $name): static
    {
        return $this->state(fn () => ['designation_id' => Designation::firstOrCreate(['name' => $name])->id]);
    }

    public function resigned(string $lastWorkingDate): static
    {
        return $this->state(['status' => 'Resigned', 'last_working_date' => $lastWorkingDate]);
    }
}
