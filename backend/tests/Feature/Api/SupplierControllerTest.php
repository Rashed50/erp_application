<?php

use App\Models\Purchase;
use App\Models\Supplier;

describe('index', function () {
    it('returns 401 when no token is provided', function () {
        $this->getJson('/api/suppliers')->assertStatus(401);
    });

    it('returns a paginated list of suppliers', function () {
        $actor = adminUser();
        Supplier::factory()->count(3)->create();

        $response = $this->actingAs($actor, 'sanctum')->getJson('/api/suppliers');

        $response->assertOk()
            ->assertJson(['success' => true, 'code' => 200])
            ->assertJsonCount(3, 'data.suppliers')
            ->assertJsonPath('data.meta.total', 3);
    });

    it('filters suppliers by search term', function () {
        $actor = adminUser();
        Supplier::factory()->create(['name' => 'Acme Supplies']);
        Supplier::factory()->create(['name' => 'Globex Trading']);

        $response = $this->actingAs($actor, 'sanctum')->getJson('/api/suppliers?search=Acme');

        $response->assertOk()->assertJsonCount(1, 'data.suppliers');
    });

    it('filters suppliers by active status', function () {
        $actor = adminUser();
        Supplier::factory()->create(['active_status' => true]);
        Supplier::factory()->inactive()->create();

        $response = $this->actingAs($actor, 'sanctum')->getJson('/api/suppliers?active_status=0');

        $response->assertOk()->assertJsonCount(1, 'data.suppliers');
    });

    it('returns every supplier when the active_status filter is present but empty', function () {
        $actor = adminUser();
        Supplier::factory()->create(['active_status' => true]);
        Supplier::factory()->inactive()->create();

        $response = $this->actingAs($actor, 'sanctum')->getJson('/api/suppliers?active_status=');

        $response->assertOk()->assertJsonCount(2, 'data.suppliers');
    });
});

describe('show', function () {
    it('returns 404 for a supplier that does not exist', function () {
        $actor = adminUser();

        $this->actingAs($actor, 'sanctum')
            ->getJson('/api/suppliers/999')
            ->assertStatus(404);
    });

    it('returns the requested supplier', function () {
        $actor = adminUser();
        $supplier = Supplier::factory()->create();

        $this->actingAs($actor, 'sanctum')
            ->getJson("/api/suppliers/{$supplier->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $supplier->id)
            ->assertJsonPath('data.email', $supplier->email);
    });
});

describe('store', function () {
    it('requires a name', function () {
        $actor = adminUser();

        $this->actingAs($actor, 'sanctum')
            ->postJson('/api/suppliers', [])
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['name']]);
    });

    it('rejects an email that is already taken', function () {
        $actor = adminUser();
        $existing = Supplier::factory()->create();

        $this->actingAs($actor, 'sanctum')
            ->postJson('/api/suppliers', ['name' => 'New Supplier', 'email' => $existing->email])
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['email']]);
    });

    it('creates a supplier with the opening balance mirrored as the current balance', function () {
        $actor = adminUser();

        $response = $this->actingAs($actor, 'sanctum')->postJson('/api/suppliers', [
            'name' => 'New Supplier',
            'email' => 'new-supplier@example.com',
            'opening_balance' => 1500,
        ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true, 'message' => 'Supplier created successfully.'])
            ->assertJsonPath('data.opening_balance', 1500)
            ->assertJsonPath('data.current_balance', 1500)
            ->assertJsonPath('data.active_status', true);

        $this->assertDatabaseHas('suppliers', ['email' => 'new-supplier@example.com', 'created_by' => $actor->id]);
    });
});

describe('update', function () {
    it('rejects an email already taken by another supplier', function () {
        $actor = adminUser();
        $other = Supplier::factory()->create();
        $target = Supplier::factory()->create();

        $this->actingAs($actor, 'sanctum')
            ->putJson("/api/suppliers/{$target->id}", ['email' => $other->email])
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['email']]);
    });

    it('updates the supplier and persists the change', function () {
        $actor = adminUser();
        $target = Supplier::factory()->create();

        $response = $this->actingAs($actor, 'sanctum')
            ->putJson("/api/suppliers/{$target->id}", ['name' => 'Updated Name']);

        $response->assertOk()->assertJsonPath('data.name', 'Updated Name');

        $this->assertDatabaseHas('suppliers', ['id' => $target->id, 'name' => 'Updated Name', 'updated_by' => $actor->id]);
    });

    it('ignores an opening_balance sent in the update payload', function () {
        $actor = adminUser();
        $target = Supplier::factory()->create(['opening_balance' => 100, 'current_balance' => 100]);

        $this->actingAs($actor, 'sanctum')
            ->putJson("/api/suppliers/{$target->id}", ['opening_balance' => 9999])
            ->assertOk();

        $this->assertDatabaseHas('suppliers', ['id' => $target->id, 'opening_balance' => 100]);
    });
});

describe('destroy', function () {
    it('deletes a supplier with no ledger transactions or purchases', function () {
        $actor = adminUser();
        $target = Supplier::factory()->create();

        $this->actingAs($actor, 'sanctum')
            ->deleteJson("/api/suppliers/{$target->id}")
            ->assertOk()
            ->assertJson(['success' => true, 'message' => 'Supplier deleted successfully.']);

        $this->assertDatabaseMissing('suppliers', ['id' => $target->id]);
    });

    it('rejects deleting a supplier that has ledger transactions', function () {
        $actor = adminUser();
        $target = Supplier::factory()->create();
        $target->transactions()->create([
            'transaction_type' => 'Purchase',
            'debit' => 0,
            'credit' => 500,
            'transaction_date' => now(),
        ]);

        $this->actingAs($actor, 'sanctum')
            ->deleteJson("/api/suppliers/{$target->id}")
            ->assertStatus(422)
            ->assertJson(['success' => false]);

        $this->assertDatabaseHas('suppliers', ['id' => $target->id]);
    });

    it('rejects deleting a supplier that has purchases', function () {
        $actor = adminUser();
        $target = Supplier::factory()->create();
        Purchase::factory()->for($target)->create();

        $this->actingAs($actor, 'sanctum')
            ->deleteJson("/api/suppliers/{$target->id}")
            ->assertStatus(422)
            ->assertJson(['success' => false]);

        $this->assertDatabaseHas('suppliers', ['id' => $target->id]);
    });
});
