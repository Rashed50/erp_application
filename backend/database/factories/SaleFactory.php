<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Sale;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Sale>
 */
class SaleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $totalAmount = fake()->randomFloat(2, 500, 5000);
        $vat = fake()->randomFloat(2, 0, 100);
        $discount = fake()->randomFloat(2, 0, 50);

        return [
            'customer_id' => Customer::factory(),
            'invoice_number' => fake()->unique()->bothify('SINV-#####'),
            'description' => fake()->sentence(),
            'issue_date' => fake()->date(),
            'due_date' => fake()->optional()->date(),
            'total_amount' => $totalAmount,
            'discount_amount' => $discount,
            'vat_amount' => $vat,
            'net_total' => $totalAmount - $discount + $vat,
            'paid_amount' => 0,
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
