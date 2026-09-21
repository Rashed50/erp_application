<?php

namespace Database\Factories;

use App\Models\AccountType;
use App\Models\ChartOfAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ChartOfAccount>
 */
class ChartOfAccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'account_type_id' => fake()->randomElement([AccountType::ASSET, AccountType::REVENUE, AccountType::EXPENSE]),
            'name' => fake()->unique()->words(2, true),
            'account_number' => null,
            'sibling_level' => 0,
            'balance' => 0,
            'opening_date' => fake()->date(),
            'active_status' => true,
            'is_transaction' => true,
            'is_predefined' => false,
            'is_closed' => false,
        ];
    }

    public function ofType(int $accountTypeId): static
    {
        return $this->state(fn (array $attributes) => ['account_type_id' => $accountTypeId]);
    }

    public function asset(): static
    {
        return $this->ofType(AccountType::ASSET);
    }

    public function liability(): static
    {
        return $this->ofType(AccountType::LIABILITY);
    }

    public function equity(): static
    {
        return $this->ofType(AccountType::OWNER_EQUITY);
    }

    public function revenue(): static
    {
        return $this->ofType(AccountType::REVENUE);
    }

    public function expense(): static
    {
        return $this->ofType(AccountType::EXPENSE);
    }

    /**
     * A group header that only rolls up its children.
     */
    public function group(): static
    {
        return $this->state(fn (array $attributes) => ['is_transaction' => false]);
    }

    public function predefined(): static
    {
        return $this->state(fn (array $attributes) => ['is_predefined' => true]);
    }

    public function closed(): static
    {
        return $this->state(fn (array $attributes) => ['is_closed' => true]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => ['active_status' => false]);
    }
}
