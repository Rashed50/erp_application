<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Customer>
 */
class CustomerFactory extends Factory
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
            'country' => fake()->country(),
            'opening_date' => fake()->date(),
            'opening_balance' => $openingBalance,
            'current_balance' => $openingBalance,
            'active_status' => true,
        ];
    }

    /**
     * Indicate that the customer is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'active_status' => false,
        ]);
    }
}
