<?php

namespace Database\Factories;

use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Purchase>
 */
class PurchaseFactory extends Factory
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
            'supplier_id' => Supplier::factory(),
            'purchase_type' => 'product',
            'invoice_number' => fake()->unique()->bothify('PINV-#####'),
            'description' => fake()->sentence(),
            'issue_date' => fake()->date(),
            'purchase_date' => fake()->date(),
            'total_amount' => $totalAmount,
            'discount_amount' => $discount,
            'vat_amount' => $vat,
            'net_total' => $totalAmount - $discount + $vat,
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
