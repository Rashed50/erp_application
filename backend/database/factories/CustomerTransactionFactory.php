<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\CustomerTransaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CustomerTransaction>
 */
class CustomerTransactionFactory extends Factory
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
            'transaction_type' => 'Invoice',
            'invoice_no' => fake()->unique()->bothify('INV-####'),
            'debit' => fake()->randomFloat(2, 100, 5000),
            'credit' => 0,
            'transaction_date' => fake()->date(),
            'notes' => fake()->sentence(),
            'status' => true,
        ];
    }

    /**
     * Indicate that this is a payment-received (credit) entry.
     */
    public function payment(): static
    {
        return $this->state(fn (array $attributes) => [
            'transaction_type' => 'Payment Received',
            'debit' => 0,
            'credit' => fake()->randomFloat(2, 100, 5000),
        ]);
    }
}
