<?php

namespace Database\Factories;

use App\Models\ChartOfAccount;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SupplierPayment>
 */
class SupplierPaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $billAmount = fake()->randomFloat(2, 100, 5000);

        return [
            'supplier_id' => Supplier::factory(),
            'payment_account_id' => ChartOfAccount::factory()->asset(),
            'invoice_no' => fake()->optional()->bothify('INV-####'),
            'payment_date' => fake()->date(),
            'bill_amount' => $billAmount,
            'bank_charge' => 0,
            'total_amount' => $billAmount,
            'remarks' => fake()->optional()->sentence(),
        ];
    }
}
