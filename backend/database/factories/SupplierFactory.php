<?php

namespace Database\Factories;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Supplier>
 */
class SupplierFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $openingBalance = fake()->randomFloat(2, 0, 5000);

        return [
            'name' => fake()->company(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'vat_no' => fake()->numerify('VAT-########'),
            'payment_term' => fake()->randomElement([15, 20, 30, 45]),
            'contact_person' => fake()->name(),
            'contact_person_phone' => fake()->phoneNumber(),
            'contact_person_email' => fake()->unique()->safeEmail(),
            'opening_balance' => $openingBalance,
            'current_balance' => $openingBalance,
            'active_status' => true,
        ];
    }

    /**
     * Indicate that the supplier is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'active_status' => false,
        ]);
    }
}
