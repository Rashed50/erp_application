<?php

use App\Models\ItemName;
use App\Models\ItemSubCategory;
use App\Models\User;

function itemNamePayload(ItemSubCategory $subCategory, array $overrides = []): array
{
    return array_merge([
        'icatg_id' => $subCategory->icatg_id,
        'iscatg_id' => $subCategory->iscatg_id,
        'itype_id' => ItemName::TYPE_ASSET,
        'item_name' => 'Office Chair',
        'item_title' => 'Revolving Office Chair',
        'item_code' => 'OC-01',
    ], $overrides);
}

describe('index', function () {
    it('returns 403 for a user without the item-names.view permission', function () {
        $this->actingAs(User::factory()->create(), 'sanctum')
            ->getJson('/api/asset/item-names')
            ->assertStatus(403);
    });

    it('lists items with their category, sub category and type names', function () {
        $item = ItemName::factory()->create();

        $this->actingAs(adminUser(), 'sanctum')
            ->getJson('/api/asset/item-names')
            ->assertOk()
            ->assertJsonCount(1, 'data.item_names')
            ->assertJsonPath('data.item_names.0.icatg_name', $item->category->icatg_name)
            ->assertJsonPath('data.item_names.0.iscatg_name', $item->subCategory->iscatg_name)
            ->assertJsonPath('data.item_names.0.itype_name', 'Asset')
            ->assertJsonCount(2, 'data.types');
    });

    it('filters items by type and status', function () {
        ItemName::factory()->create();
        ItemName::factory()->nonAsset()->create();
        ItemName::factory()->nonAsset()->inactive()->create();

        $this->actingAs(adminUser(), 'sanctum')
            ->getJson('/api/asset/item-names?itype_id=2')
            ->assertOk()
            ->assertJsonCount(2, 'data.item_names');

        $this->actingAs(adminUser(), 'sanctum')
            ->getJson('/api/asset/item-names?itype_id=2&status=1')
            ->assertOk()
            ->assertJsonCount(1, 'data.item_names');
    });
});

describe('store', function () {
    it('validates required fields', function () {
        $this->actingAs(adminUser(), 'sanctum')
            ->postJson('/api/asset/item-names', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['icatg_id', 'iscatg_id', 'itype_id', 'item_name', 'item_title', 'item_code'], 'data');
    });

    it('creates an item and records the creator', function () {
        $actor = adminUser();
        $subCategory = ItemSubCategory::factory()->create();

        $this->actingAs($actor, 'sanctum')
            ->postJson('/api/asset/item-names', itemNamePayload($subCategory))
            ->assertStatus(201)
            ->assertJsonPath('data.item_name', 'Office Chair')
            ->assertJsonPath('data.item_status', true);

        $this->assertDatabaseHas('item_names', [
            'iscatg_id' => $subCategory->iscatg_id,
            'item_code' => 'OC-01',
            'itype_id' => ItemName::TYPE_ASSET,
            'create_by_id' => $actor->id,
        ]);
    });

    it('rejects a sub category that does not belong to the selected category', function () {
        $subCategory = ItemSubCategory::factory()->create();
        $otherSubCategory = ItemSubCategory::factory()->create();

        $this->actingAs(adminUser(), 'sanctum')
            ->postJson('/api/asset/item-names', itemNamePayload($subCategory, ['iscatg_id' => $otherSubCategory->iscatg_id]))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['iscatg_id'], 'data');
    });

    it('rejects an inactive sub category', function () {
        $subCategory = ItemSubCategory::factory()->inactive()->create();

        $this->actingAs(adminUser(), 'sanctum')
            ->postJson('/api/asset/item-names', itemNamePayload($subCategory))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['iscatg_id'], 'data');
    });

    it('rejects an unknown item type', function () {
        $subCategory = ItemSubCategory::factory()->create();

        $this->actingAs(adminUser(), 'sanctum')
            ->postJson('/api/asset/item-names', itemNamePayload($subCategory, ['itype_id' => 3]))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['itype_id'], 'data');
    });

    it('rejects a duplicate item code', function () {
        ItemName::factory()->create(['item_code' => 'OC-01']);
        $subCategory = ItemSubCategory::factory()->create();

        $this->actingAs(adminUser(), 'sanctum')
            ->postJson('/api/asset/item-names', itemNamePayload($subCategory))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['item_code'], 'data');
    });
});

describe('update', function () {
    it('updates the item and records the updater', function () {
        $actor = adminUser();
        $item = ItemName::factory()->create();

        $this->actingAs($actor, 'sanctum')
            ->putJson("/api/asset/item-names/{$item->item_id}", itemNamePayload($item->subCategory, [
                'item_name' => 'Renamed',
                'item_code' => $item->item_code,
                'itype_id' => ItemName::TYPE_NON_ASSET,
            ]))
            ->assertOk()
            ->assertJsonPath('data.item_name', 'Renamed')
            ->assertJsonPath('data.itype_name', 'Non-Asset');

        expect($item->refresh()->update_by_id)->toBe($actor->id);
    });
});

describe('status', function () {
    it('deactivates an item', function () {
        $item = ItemName::factory()->create();

        $this->actingAs(adminUser(), 'sanctum')
            ->patchJson("/api/asset/item-names/{$item->item_id}/status", ['status' => false])
            ->assertOk()
            ->assertJsonPath('data.item_status', false);

        expect($item->refresh()->item_status)->toBeFalse();
    });

    it('requires a status value', function () {
        $item = ItemName::factory()->create();

        $this->actingAs(adminUser(), 'sanctum')
            ->patchJson("/api/asset/item-names/{$item->item_id}/status", [])
            ->assertStatus(422);
    });
});
