<?php

namespace Database\Factories;

use App\Models\ChartOfAccount;
use App\Models\IncomeExpenseTransaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<IncomeExpenseTransaction>
 */
class IncomeExpenseTransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => 'expense',
            'income_expense_account_id' => ChartOfAccount::factory()->expense(),
            'payment_account_id' => ChartOfAccount::factory()->asset(),
            'amount' => fake()->randomFloat(2, 100, 5000),
            'transaction_date' => fake()->date(),
            'reference_no' => fake()->optional()->bothify('REF-####'),
            'description' => fake()->sentence(),
        ];
    }

    public function income(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'income',
            'income_expense_account_id' => ChartOfAccount::factory()->revenue(),
        ]);
    }
}
