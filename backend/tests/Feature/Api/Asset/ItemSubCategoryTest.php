<?php

use App\Models\ItemCategory;
use App\Models\ItemSubCategory;
use App\Models\User;

describe('index', function () {
    it('returns 403 for a user without the item-sub-categories.view permission', function () {
        $this->actingAs(User::factory()->create(), 'sanctum')
            ->getJson('/api/asset/item-sub-categories')
            ->assertStatus(403);
    });

    it('lists sub categories with their category name', function () {
        $category = ItemCategory::factory()->create(['icatg_name' => 'Furniture']);
        ItemSubCategory::factory()->for($category, 'category')->create();

        $this->actingAs(adminUser(), 'sanctum')
            ->getJson('/api/asset/item-sub-categories')
            ->assertOk()
            ->assertJsonCount(1, 'data.item_sub_categories')
            ->assertJsonPath('data.item_sub_categories.0.icatg_name', 'Furniture');
    });

    it('filters sub categories by category', function () {
        $category = ItemCategory::factory()->create();
        ItemSubCategory::factory()->for($category, 'category')->count(2)->create();
        ItemSubCategory::factory()->create();

        $this->actingAs(adminUser(), 'sanctum')
            ->getJson("/api/asset/item-sub-categories?icatg_id={$category->icatg_id}")
            ->assertOk()
            ->assertJsonCount(2, 'data.item_sub_categories');
    });

    it('lists only active sub categories of a category as options', function () {
        $category = ItemCategory::factory()->create();
        ItemSubCategory::factory()->for($category, 'category')->create();
        ItemSubCategory::factory()->for($category, 'category')->inactive()->create();
        ItemSubCategory::factory()->create();

        $this->actingAs(adminUser(), 'sanctum')
            ->getJson("/api/asset/item-sub-categories/options?icatg_id={$category->icatg_id}")
            ->assertOk()
            ->assertJsonCount(1, 'data');
    });
});

describe('store', function () {
    it('creates a sub category under an active category', function () {
        $actor = adminUser();
        $category = ItemCategory::factory()->create();

        $this->actingAs($actor, 'sanctum')
            ->postJson('/api/asset/item-sub-categories', [
                'icatg_id' => $category->icatg_id,
                'iscatg_name' => 'Chair',
                'iscatg_code' => 'CHR',
            ])
            ->assertStatus(201)
            ->assertJsonPath('data.iscatg_name', 'Chair')
            ->assertJsonPath('data.icatg_name', $category->icatg_name);

        $this->assertDatabaseHas('item_sub_categories', [
            'icatg_id' => $category->icatg_id,
            'iscatg_name' => 'Chair',
            'status' => true,
            'create_by_id' => $actor->id,
        ]);
    });

    it('rejects an inactive category', function () {
        $category = ItemCategory::factory()->inactive()->create();

        $this->actingAs(adminUser(), 'sanctum')
            ->postJson('/api/asset/item-sub-categories', [
                'icatg_id' => $category->icatg_id,
                'iscatg_name' => 'Chair',
                'iscatg_code' => 'CHR',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['icatg_id'], 'data');
    });

    it('allows the same name under a different category but not the same one', function () {
        $existing = ItemSubCategory::factory()->create(['iscatg_name' => 'Chair']);
        $otherCategory = ItemCategory::factory()->create();

        $this->actingAs(adminUser(), 'sanctum')
            ->postJson('/api/asset/item-sub-categories', [
                'icatg_id' => $existing->icatg_id,
                'iscatg_name' => 'Chair',
                'iscatg_code' => 'CHR1',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['iscatg_name'], 'data');

        $this->actingAs(adminUser(), 'sanctum')
            ->postJson('/api/asset/item-sub-categories', [
                'icatg_id' => $otherCategory->icatg_id,
                'iscatg_name' => 'Chair',
                'iscatg_code' => 'CHR2',
            ])
            ->assertStatus(201);
    });
});

describe('update', function () {
    it('updates the sub category', function () {
        $subCategory = ItemSubCategory::factory()->create(['iscatg_name' => 'Old']);

        $this->actingAs(adminUser(), 'sanctum')
            ->putJson("/api/asset/item-sub-categories/{$subCategory->iscatg_id}", [
                'icatg_id' => $subCategory->icatg_id,
                'iscatg_name' => 'New',
                'iscatg_code' => $subCategory->iscatg_code,
            ])
            ->assertOk()
            ->assertJsonPath('data.iscatg_name', 'New');
    });

    it('keeps its current category even after that category was deactivated', function () {
        $category = ItemCategory::factory()->inactive()->create();
        $subCategory = ItemSubCategory::factory()->for($category, 'category')->create();

        $this->actingAs(adminUser(), 'sanctum')
            ->putJson("/api/asset/item-sub-categories/{$subCategory->iscatg_id}", [
                'icatg_id' => $category->icatg_id,
                'iscatg_name' => 'Renamed',
                'iscatg_code' => $subCategory->iscatg_code,
            ])
            ->assertOk();
    });
});

describe('status', function () {
    it('deactivates a sub category', function () {
        $subCategory = ItemSubCategory::factory()->create();

        $this->actingAs(adminUser(), 'sanctum')
            ->patchJson("/api/asset/item-sub-categories/{$subCategory->iscatg_id}/status", ['status' => false])
            ->assertOk()
            ->assertJsonPath('data.status', false);

        expect($subCategory->refresh()->status)->toBeFalse();
    });
});
