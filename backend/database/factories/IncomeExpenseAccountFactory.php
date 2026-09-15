<?php

namespace Database\Factories;

use App\Models\IncomeExpenseAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<IncomeExpenseAccount>
 */
class IncomeExpenseAccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'type' => fake()->randomElement(['asset', 'income', 'expense']),
            'active_status' => true,
        ];
    }

    public function asset(): static
    {
        return $this->state(fn (array $attributes) => ['type' => 'asset']);
    }

    public function income(): static
    {
        return $this->state(fn (array $attributes) => ['type' => 'income']);
    }

    public function expense(): static
    {
        return $this->state(fn (array $attributes) => ['type' => 'expense']);
    }
}
