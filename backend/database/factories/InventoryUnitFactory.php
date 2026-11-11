<?php

namespace Database\Factories;

use App\Models\InventoryUnit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InventoryUnit>
 */
class InventoryUnitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word().' unit',
            'short_name' => fake()->unique()->lexify('u???'),
            'status' => true,
        ];
    }
}
