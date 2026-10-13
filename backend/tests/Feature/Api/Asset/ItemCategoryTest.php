<?php

use App\Models\ItemCategory;
use App\Models\User;

describe('index', function () {
    it('returns 401 when no token is provided', function () {
        $this->getJson('/api/asset/item-categories')->assertStatus(401);
    });

    it('returns 403 for a user without the item-categories.view permission', function () {
        $this->actingAs(User::factory()->create(), 'sanctum')
            ->getJson('/api/asset/item-categories')
            ->assertStatus(403);
    });

    it('returns a paginated list of categories', function () {
        ItemCategory::factory()->count(3)->create();

        $this->actingAs(adminUser(), 'sanctum')
            ->getJson('/api/asset/item-categories')
            ->assertOk()
            ->assertJsonCount(3, 'data.item_categories')
            ->assertJsonPath('data.meta.total', 3);
    });

    it('filters categories by search text and status', function () {
        ItemCategory::factory()->create(['icatg_name' => 'Furniture', 'icatg_code' => 'FUR']);
        ItemCategory::factory()->inactive()->create(['icatg_name' => 'Electronics', 'icatg_code' => 'ELE']);

        $this->actingAs(adminUser(), 'sanctum')
            ->getJson('/api/asset/item-categories?search=Furn')
            ->assertOk()
            ->assertJsonCount(1, 'data.item_categories')
            ->assertJsonPath('data.item_categories.0.icatg_code', 'FUR');

        $this->actingAs(adminUser(), 'sanctum')
            ->getJson('/api/asset/item-categories?status=0')
            ->assertOk()
            ->assertJsonCount(1, 'data.item_categories')
            ->assertJsonPath('data.item_categories.0.icatg_code', 'ELE');
    });

    it('lists only active categories as options', function () {
        ItemCategory::factory()->create();
        ItemCategory::factory()->inactive()->create();

        $this->actingAs(adminUser(), 'sanctum')
            ->getJson('/api/asset/item-categories/options')
            ->assertOk()
            ->assertJsonCount(1, 'data');
    });
});

describe('store', function () {
    it('validates required fields', function () {
        $this->actingAs(adminUser(), 'sanctum')
            ->postJson('/api/asset/item-categories', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['icatg_name', 'icatg_code'], 'data');
    });

    it('creates an active category and records the creator', function () {
        $actor = adminUser();

        $this->actingAs($actor, 'sanctum')
            ->postJson('/api/asset/item-categories', ['icatg_name' => 'Furniture', 'icatg_code' => 'FUR'])
            ->assertStatus(201)
            ->assertJsonPath('data.icatg_name', 'Furniture')
            ->assertJsonPath('data.status', true);

        $this->assertDatabaseHas('item_categories', [
            'icatg_name' => 'Furniture',
            'icatg_code' => 'FUR',
            'status' => true,
            'create_by_id' => $actor->id,
        ]);
    });

    it('rejects a duplicate name or code', function () {
        ItemCategory::factory()->create(['icatg_name' => 'Furniture', 'icatg_code' => 'FUR']);

        $this->actingAs(adminUser(), 'sanctum')
            ->postJson('/api/asset/item-categories', ['icatg_name' => 'Furniture', 'icatg_code' => 'FUR'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['icatg_name', 'icatg_code'], 'data');
    });

    it('rejects a code longer than 10 characters', function () {
        $this->actingAs(adminUser(), 'sanctum')
            ->postJson('/api/asset/item-categories', ['icatg_name' => 'Furniture', 'icatg_code' => 'FURNITURE-01'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['icatg_code'], 'data');
    });
});

describe('update', function () {
    it('updates the category and records the updater', function () {
        $actor = adminUser();
        $category = ItemCategory::factory()->create(['icatg_name' => 'Old', 'icatg_code' => 'OLD']);

        $this->actingAs($actor, 'sanctum')
            ->putJson("/api/asset/item-categories/{$category->icatg_id}", ['icatg_name' => 'New', 'icatg_code' => 'OLD'])
            ->assertOk()
            ->assertJsonPath('data.icatg_name', 'New');

        expect($category->refresh())
            ->icatg_name->toBe('New')
            ->update_by_id->toBe($actor->id);
    });
});

describe('status', function () {
    it('deactivates and reactivates a category', function () {
        $category = ItemCategory::factory()->create();

        $this->actingAs(adminUser(), 'sanctum')
            ->patchJson("/api/asset/item-categories/{$category->icatg_id}/status", ['status' => false])
            ->assertOk()
            ->assertJsonPath('data.status', false);

        expect($category->refresh()->status)->toBeFalse();

        $this->actingAs(adminUser(), 'sanctum')
            ->patchJson("/api/asset/item-categories/{$category->icatg_id}/status", ['status' => true])
            ->assertOk()
            ->assertJsonPath('data.status', true);
    });

    it('returns 403 for a user without the item-categories.update permission', function () {
        $category = ItemCategory::factory()->create();

        $this->actingAs(User::factory()->create(), 'sanctum')
            ->patchJson("/api/asset/item-categories/{$category->icatg_id}/status", ['status' => false])
            ->assertStatus(403);
    });
});
