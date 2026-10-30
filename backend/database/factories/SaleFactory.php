<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Sale;
use App\Models\SaleItem;
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
        return [
            'customer_id' => Customer::factory(),
            'invoice_number' => fake()->unique()->bothify('SINV-#####'),
            'description' => fake()->sentence(),
            'issue_date' => fake()->date(),
            'due_date' => fake()->optional()->date(),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Totals come from the line items, so every sale gets one line
     * unless a state gave it its own.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Sale $model) {
            if ($model->items()->doesntExist()) {
                SaleItem::factory()->for($model)->create();
            }
        });
    }

    /**
     * One line making the net total $netTotal, $vat of it VAT.
     */
    public function withTotal(float $netTotal, float $vat = 0): static
    {
        return $this->has(SaleItem::factory()->state([
            'qty' => 1,
            'unit_price' => $netTotal - $vat,
            'discount' => 0,
            'vat' => $vat,
        ]), 'items');
    }
}
