<?php

namespace Database\Factories;

use App\Models\Supplier;
use App\Models\SupplierTransaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SupplierTransaction>
 */
class SupplierTransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'supplier_id' => Supplier::factory(),
            'transaction_type' => 'Purchase',
            'invoice_no' => fake()->unique()->bothify('PUR-####'),
            'debit' => 0,
            'credit' => fake()->randomFloat(2, 100, 5000),
            'transaction_date' => fake()->date(),
            'notes' => fake()->sentence(),
            'status' => true,
        ];
    }

    /**
     * Indicate that this is a bill-payment (debit) entry.
     */
    public function payment(): static
    {
        return $this->state(fn (array $attributes) => [
            'transaction_type' => 'Bill Payment',
            'debit' => fake()->randomFloat(2, 100, 5000),
            'credit' => 0,
        ]);
    }
}
