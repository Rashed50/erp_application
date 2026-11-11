<?php

namespace Database\Factories;

use App\Models\InventoryWarehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InventoryWarehouse>
 */
class InventoryWarehouseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'warehouse_code' => fake()->unique()->numerify('WH-####'),
            'name' => fake()->city().' Store',
            'warehouse_type' => InventoryWarehouse::TYPE_CENTER,
            'status' => true,
        ];
    }

    /**
     * Indicate that the store is a project store under the given parent.
     */
    public function projectStore(?InventoryWarehouse $parent = null): static
    {
        return $this->state(fn (array $attributes) => [
            'warehouse_type' => InventoryWarehouse::TYPE_PROJECT_STORE,
            'parent_id' => $parent?->id,
        ]);
    }

    /**
     * Indicate that the store is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => false,
        ]);
    }
}
