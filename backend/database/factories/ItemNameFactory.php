<?php

namespace Database\Factories;

use App\Models\ItemName;
use App\Models\ItemSubCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ItemName>
 */
class ItemNameFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'iscatg_id' => ItemSubCategory::factory(),
            // The item's category is always its sub category's category.
            'icatg_id' => fn (array $attributes) => ItemSubCategory::find($attributes['iscatg_id'])->icatg_id,
            'itype_id' => ItemName::TYPE_ASSET,
            'item_name' => fake()->unique()->words(2, true),
            'item_title' => fake()->words(3, true),
            'item_code' => fake()->unique()->numerify('I-#####'),
            'item_status' => true,
            'create_by_id' => User::factory(),
        ];
    }

    /**
     * Indicate that the item is a non-asset (consumable) item.
     */
    public function nonAsset(): static
    {
        return $this->state(fn (array $attributes) => [
            'itype_id' => ItemName::TYPE_NON_ASSET,
        ]);
    }

    /**
     * Indicate that the item is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'item_status' => false,
        ]);
    }
}
