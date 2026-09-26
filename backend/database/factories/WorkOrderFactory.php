<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\WorkOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WorkOrder>
 */
class WorkOrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'work_title' => fake()->sentence(3),
            'work_order_no' => fake()->unique()->bothify('WO-#####'),
            'issue_date' => fake()->date(),
            'total_amount' => fake()->randomFloat(2, 1000, 50000),
            'retention_percent' => fake()->randomElement([0, 5, 10]),
            'deliver_date' => null,
            'status' => 'Pending',
        ];
    }
}
