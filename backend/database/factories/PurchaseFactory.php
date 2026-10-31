<?php

namespace Database\Factories;

use App\Models\Purchase;
use App\Models\PurchaseItem;
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
        return [
            'supplier_id' => Supplier::factory(),
            'purchase_type' => 'product',
            'invoice_number' => fake()->unique()->bothify('PINV-#####'),
            'description' => fake()->sentence(),
            'issue_date' => fake()->date(),
            'purchase_date' => fake()->date(),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Totals come from the line items, so every purchase gets one line
     * unless a state gave it its own.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Purchase $model) {
            if ($model->items()->doesntExist()) {
                PurchaseItem::factory()->for($model)->create();
            }
        });
    }

    /**
     * One line making the net total $netTotal, $vat of it VAT.
     */
    public function withTotal(float $netTotal, float $vat = 0): static
    {
        return $this->has(PurchaseItem::factory()->state([
            'qty' => 1,
            'unit_price' => $netTotal - $vat,
            'discount' => 0,
            'vat' => $vat,
        ]), 'items');
    }
}
