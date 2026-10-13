<?php

namespace Database\Factories;

use App\Models\AccountTransaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AccountTransaction>
 */
class AccountTransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tr_no' => fake()->bothify('TR-####'),
            'date' => fake()->date(),
            'total_amount' => fake()->randomFloat(2, 100, 5000),
            'general_particular' => fake()->sentence(),
        ];
    }
}
