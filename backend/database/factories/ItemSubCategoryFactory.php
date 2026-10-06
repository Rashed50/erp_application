<?php

namespace Database\Factories;

use App\Models\ItemCategory;
use App\Models\ItemSubCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ItemSubCategory>
 */
class ItemSubCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'icatg_id' => ItemCategory::factory(),
            'iscatg_name' => fake()->unique()->words(2, true),
            'iscatg_code' => fake()->unique()->numerify('SC-#####'),
            'status' => true,
            'create_by_id' => User::factory(),
        ];
    }

    /**
     * Indicate that the sub category is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => false,
        ]);
    }
}
