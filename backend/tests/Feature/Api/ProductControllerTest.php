<?php

use App\Models\Product;
use App\Models\User;

describe('index', function () {
    it('returns 401 when no token is provided', function () {
        $this->getJson('/api/products')->assertStatus(401);
    });

    it('returns 403 for a user without the products.view permission', function () {
        $this->actingAs(User::factory()->create(), 'sanctum')
            ->getJson('/api/products')
            ->assertStatus(403);
    });

    it('returns a paginated list of products', function () {
        Product::factory()->count(3)->create();

        $this->actingAs(adminUser(), 'sanctum')
            ->getJson('/api/products')
            ->assertOk()
            ->assertJsonCount(3, 'data.products')
            ->assertJsonPath('data.meta.total', 3);
    });

    it('filters products by name or code', function () {
        Product::factory()->create(['name' => 'Cement Bag', 'code' => 'P-11111']);
        Product::factory()->create(['name' => 'Steel Rod', 'code' => 'P-22222']);

        $this->actingAs(adminUser(), 'sanctum')
            ->getJson('/api/products?search=Cement')
            ->assertOk()
            ->assertJsonCount(1, 'data.products');

        $this->actingAs(adminUser(), 'sanctum')
            ->getJson('/api/products?search=22222')
            ->assertOk()
            ->assertJsonCount(1, 'data.products');
    });

    it('filters products by active status', function () {
        Product::factory()->create();
        Product::factory()->inactive()->create();

        $this->actingAs(adminUser(), 'sanctum')
            ->getJson('/api/products?active_status=0')
            ->assertOk()
            ->assertJsonCount(1, 'data.products');
    });
});

describe('store', function () {
    it('requires a name', function () {
        $this->actingAs(adminUser(), 'sanctum')
            ->postJson('/api/products', [])
            ->assertStatus(422);
    });

    it('creates a product with a generated code', function () {
        $actor = adminUser();

        $response = $this->actingAs($actor, 'sanctum')
            ->postJson('/api/products', ['name' => 'Cement Bag'])
            ->assertStatus(201)
            ->assertJsonPath('data.name', 'Cement Bag')
            ->assertJsonPath('data.active_status', true);

        expect($response->json('data.code'))->toStartWith('P-');
        $this->assertDatabaseHas('products', ['name' => 'Cement Bag', 'created_by' => $actor->id]);
    });

    it('rejects a code that is already taken', function () {
        Product::factory()->create(['code' => 'P-12345']);

        $this->actingAs(adminUser(), 'sanctum')
            ->postJson('/api/products', ['name' => 'Cement Bag', 'code' => 'P-12345'])
            ->assertStatus(422);
    });
});

describe('update', function () {
    it('updates the product', function () {
        $product = Product::factory()->create(['name' => 'Old Name']);

        $this->actingAs(adminUser(), 'sanctum')
            ->putJson("/api/products/{$product->id}", ['name' => 'New Name', 'code' => $product->code])
            ->assertOk()
            ->assertJsonPath('data.name', 'New Name');

        expect($product->refresh()->name)->toBe('New Name');
    });
});

describe('destroy', function () {
    it('soft deletes the product and records who deleted it', function () {
        $actor = adminUser();
        $product = Product::factory()->create();

        $this->actingAs($actor, 'sanctum')
            ->deleteJson("/api/products/{$product->id}")
            ->assertOk();

        $this->assertSoftDeleted('products', ['id' => $product->id, 'deleted_by' => $actor->id]);
    });
});
