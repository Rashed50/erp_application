<?php

use App\Models\Customer;

describe('index', function () {
    it('returns 401 when no token is provided', function () {
        $this->getJson('/api/customers')->assertStatus(401);
    });

    it('returns a paginated list of customers', function () {
        $actor = adminUser();
        Customer::factory()->count(3)->create();

        $response = $this->actingAs($actor, 'sanctum')->getJson('/api/customers');

        $response->assertOk()
            ->assertJson(['success' => true, 'code' => 200])
            ->assertJsonCount(3, 'data.customers')
            ->assertJsonPath('data.meta.total', 3);
    });

    it('filters customers by search term', function () {
        $actor = adminUser();
        Customer::factory()->create(['name' => 'Acme Corporation']);
        Customer::factory()->create(['name' => 'Globex Inc']);

        $response = $this->actingAs($actor, 'sanctum')->getJson('/api/customers?search=Acme');

        $response->assertOk()->assertJsonCount(1, 'data.customers');
    });

    it('filters customers by active status', function () {
        $actor = adminUser();
        Customer::factory()->create(['active_status' => true]);
        Customer::factory()->inactive()->create();

        $response = $this->actingAs($actor, 'sanctum')->getJson('/api/customers?active_status=0');

        $response->assertOk()->assertJsonCount(1, 'data.customers');
    });
});

describe('show', function () {
    it('returns 404 for a customer that does not exist', function () {
        $actor = adminUser();

        $this->actingAs($actor, 'sanctum')
            ->getJson('/api/customers/999')
            ->assertStatus(404);
    });

    it('returns the requested customer', function () {
        $actor = adminUser();
        $customer = Customer::factory()->create();

        $this->actingAs($actor, 'sanctum')
            ->getJson("/api/customers/{$customer->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $customer->id)
            ->assertJsonPath('data.email', $customer->email);
    });
});

describe('store', function () {
    it('requires a name', function () {
        $actor = adminUser();

        $this->actingAs($actor, 'sanctum')
            ->postJson('/api/customers', [])
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['name']]);
    });

    it('rejects an email that is already taken', function () {
        $actor = adminUser();
        $existing = Customer::factory()->create();

        $this->actingAs($actor, 'sanctum')
            ->postJson('/api/customers', ['name' => 'New Customer', 'email' => $existing->email])
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['email']]);
    });

    it('creates a customer with the opening balance mirrored as the current balance', function () {
        $actor = adminUser();

        $response = $this->actingAs($actor, 'sanctum')->postJson('/api/customers', [
            'name' => 'New Customer',
            'email' => 'new-customer@example.com',
            'opening_balance' => 1500,
        ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true, 'message' => 'Customer created successfully.'])
            ->assertJsonPath('data.opening_balance', 1500)
            ->assertJsonPath('data.current_balance', 1500)
            ->assertJsonPath('data.active_status', true);

        $this->assertDatabaseHas('customers', ['email' => 'new-customer@example.com', 'created_by' => $actor->id]);
    });
});

describe('update', function () {
    it('rejects an email already taken by another customer', function () {
        $actor = adminUser();
        $other = Customer::factory()->create();
        $target = Customer::factory()->create();

        $this->actingAs($actor, 'sanctum')
            ->putJson("/api/customers/{$target->id}", ['email' => $other->email])
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['email']]);
    });

    it('updates the customer and persists the change', function () {
        $actor = adminUser();
        $target = Customer::factory()->create();

        $response = $this->actingAs($actor, 'sanctum')
            ->putJson("/api/customers/{$target->id}", ['name' => 'Updated Name']);

        $response->assertOk()->assertJsonPath('data.name', 'Updated Name');

        $this->assertDatabaseHas('customers', ['id' => $target->id, 'name' => 'Updated Name', 'updated_by' => $actor->id]);
    });

    it('ignores an opening_balance sent in the update payload', function () {
        $actor = adminUser();
        $target = Customer::factory()->create(['opening_balance' => 100, 'current_balance' => 100]);

        $this->actingAs($actor, 'sanctum')
            ->putJson("/api/customers/{$target->id}", ['opening_balance' => 9999])
            ->assertOk();

        $this->assertDatabaseHas('customers', ['id' => $target->id, 'opening_balance' => 100]);
    });
});

describe('destroy', function () {
    it('deletes a customer with no ledger transactions', function () {
        $actor = adminUser();
        $target = Customer::factory()->create();

        $this->actingAs($actor, 'sanctum')
            ->deleteJson("/api/customers/{$target->id}")
            ->assertOk()
            ->assertJson(['success' => true, 'message' => 'Customer deleted successfully.']);

        $this->assertDatabaseMissing('customers', ['id' => $target->id]);
    });

    it('rejects deleting a customer that has ledger transactions', function () {
        $actor = adminUser();
        $target = Customer::factory()->create();
        $target->transactions()->create([
            'transaction_type' => 'Invoice',
            'debit' => 500,
            'credit' => 0,
            'transaction_date' => now(),
        ]);

        $this->actingAs($actor, 'sanctum')
            ->deleteJson("/api/customers/{$target->id}")
            ->assertStatus(422)
            ->assertJson(['success' => false]);

        $this->assertDatabaseHas('customers', ['id' => $target->id]);
    });
});
