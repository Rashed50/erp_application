<?php

namespace Database\Factories;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PurchaseItem>
 */
class PurchaseItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $qty = fake()->randomFloat(2, 1, 10);
        $unitPrice = fake()->randomFloat(2, 10, 500);

        return [
            'purchase_id' => Purchase::factory(),
            'item_name' => fake()->words(2, true),
            'description' => fake()->optional()->sentence(),
            'qty' => $qty,
            'unit_price' => $unitPrice,
            'discount' => 0,
            'vat' => 0,
            'total_amount' => $qty * $unitPrice,
        ];
    }
}
