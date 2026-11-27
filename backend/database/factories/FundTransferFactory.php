<?php

namespace Database\Factories;

use App\Models\ChartOfAccount;
use App\Models\FundTransfer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FundTransfer>
 */
class FundTransferFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $amount = fake()->randomFloat(2, 100, 5000);

        return [
            'credit_account_id' => ChartOfAccount::factory()->asset(),
            'debit_account_id' => ChartOfAccount::factory()->asset(),
            'receipt_no' => fake()->optional()->bothify('RCPT-####'),
            'transfer_date' => fake()->date(),
            'amount' => $amount,
            'bank_charge' => 0,
            'vat' => 0,
            'remarks' => fake()->optional()->sentence(),
            'approved_at' => now(),
        ];
    }

    /**
     * Indicate that the record has not been approved yet.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'approved_by' => null,
            'approved_at' => null,
        ]);
    }
}
