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
            'total_amount' => $amount,
            'remarks' => fake()->optional()->sentence(),
        ];
    }
}
