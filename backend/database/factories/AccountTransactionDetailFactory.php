<?php

namespace Database\Factories;

use App\Models\AccountTransaction;
use App\Models\AccountTransactionDetail;
use App\Models\ChartOfAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AccountTransactionDetail>
 */
class AccountTransactionDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'account_transaction_id' => AccountTransaction::factory(),
            'chart_of_account_id' => ChartOfAccount::factory(),
            'debit' => fake()->randomFloat(2, 100, 5000),
            'credit' => 0,
            'particular' => fake()->sentence(),
        ];
    }

    /**
     * Indicate that this is a credit line.
     */
    public function credit(): static
    {
        return $this->state(fn (array $attributes) => [
            'credit' => $attributes['debit'],
            'debit' => 0,
        ]);
    }
}
