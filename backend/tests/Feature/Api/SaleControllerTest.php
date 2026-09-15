<?php

use App\Models\Customer;
use App\Models\Sale;

function salePayload(int $customerId, array $overrides = []): array
{
    return array_merge([
        'customer_id' => $customerId,
        'invoice_number' => 'SINV-'.fake()->unique()->numerify('#####'),
        'description' => 'Consulting services',
        'issue_date' => now()->toDateString(),
        'notes' => 'test sale',
        'items' => [
            ['item_name' => 'Consulting', 'qty' => 10, 'unit_price' => 5, 'discount' => 0, 'vat' => 0],
            ['item_name' => 'Support', 'qty' => 20, 'unit_price' => 2, 'discount' => 5, 'vat' => 3],
        ],
    ], $overrides);
}

describe('index', function () {
    it('returns 401 when no token is provided', function () {
        $this->getJson('/api/sales')->assertStatus(401);
    });

    it('returns a paginated list of sales', function () {
        $actor = adminUser();
        Sale::factory()->count(3)->create();

        $response = $this->actingAs($actor, 'sanctum')->getJson('/api/sales');

        $response->assertOk()->assertJsonCount(3, 'data.sales');
    });

    it('filters sales by customer', function () {
        $actor = adminUser();
        $customer = Customer::factory()->create();
        Sale::factory()->for($customer)->create();
        Sale::factory()->create();

        $response = $this->actingAs($actor, 'sanctum')->getJson("/api/sales?customer_id={$customer->id}");

        $response->assertOk()->assertJsonCount(1, 'data.sales');
    });
});

describe('store', function () {
    it('requires customer_id, invoice_number, issue_date, and items', function () {
        $actor = adminUser();

        $this->actingAs($actor, 'sanctum')
            ->postJson('/api/sales', [])
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['customer_id', 'invoice_number', 'issue_date', 'items']]);
    });

    it('rejects an invoice_number that is already taken', function () {
        $actor = adminUser();
        $customer = Customer::factory()->create();
        $existing = Sale::factory()->for($customer)->create();

        $this->actingAs($actor, 'sanctum')
            ->postJson('/api/sales', salePayload($customer->id, ['invoice_number' => $existing->invoice_number]))
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['invoice_number']]);
    });

    it('computes totals from items instead of trusting client-supplied totals', function () {
        $actor = adminUser();
        $customer = Customer::factory()->create();

        // Consulting: 10 * 5 = 50 gross. Support: 20 * 2 = 40 gross, -5 discount, +3 vat = 38 net.
        // total_amount = 90, discount_amount = 5, vat_amount = 3, net_total = 88.
        $response = $this->actingAs($actor, 'sanctum')
            ->postJson('/api/sales', salePayload($customer->id, [
                'total_amount' => 999999,
                'net_total' => 999999,
            ]));

        $response->assertStatus(201)
            ->assertJsonPath('data.total_amount', 90)
            ->assertJsonPath('data.discount_amount', 5)
            ->assertJsonPath('data.vat_amount', 3)
            ->assertJsonPath('data.net_total', 88)
            ->assertJsonCount(2, 'data.items');
    });

    it('posts an Invoice ledger entry that increases the customer balance', function () {
        $actor = adminUser();
        $customer = Customer::factory()->create(['opening_balance' => 100, 'current_balance' => 100]);

        $this->actingAs($actor, 'sanctum')
            ->postJson('/api/sales', salePayload($customer->id))
            ->assertStatus(201);

        // net_total is 88 (see totals test above).
        expect($customer->fresh()->current_balance)->toEqual('188.00');
        $this->assertDatabaseHas('customer_transactions', [
            'customer_id' => $customer->id,
            'transaction_type' => 'Invoice',
            'debit' => 88,
        ]);
    });
});

describe('update', function () {
    it('ignores a customer_id sent in the update payload', function () {
        $actor = adminUser();
        $customer = Customer::factory()->create();
        $otherCustomer = Customer::factory()->create();
        $sale = $this->actingAs($actor, 'sanctum')
            ->postJson('/api/sales', salePayload($customer->id))
            ->json('data');

        $this->actingAs($actor, 'sanctum')
            ->putJson("/api/sales/{$sale['id']}", ['customer_id' => $otherCustomer->id])
            ->assertOk();

        $this->assertDatabaseHas('sales', ['id' => $sale['id'], 'customer_id' => $customer->id]);
    });

    it('recomputes totals and keeps the linked ledger entry in sync when items change', function () {
        $actor = adminUser();
        $customer = Customer::factory()->create(['opening_balance' => 0, 'current_balance' => 0]);
        $sale = $this->actingAs($actor, 'sanctum')
            ->postJson('/api/sales', salePayload($customer->id))
            ->json('data');

        expect($customer->fresh()->current_balance)->toEqual('88.00');

        $response = $this->actingAs($actor, 'sanctum')
            ->putJson("/api/sales/{$sale['id']}", [
                'items' => [
                    ['item_name' => 'Consulting', 'qty' => 1, 'unit_price' => 100, 'discount' => 0, 'vat' => 0],
                ],
            ]);

        $response->assertOk()
            ->assertJsonPath('data.net_total', 100)
            ->assertJsonCount(1, 'data.items');

        // The ledger entry the sale posted moves with it, not just the sale row.
        expect($customer->fresh()->current_balance)->toEqual('100.00');
    });

    it('rejects reducing the total below what has already been paid', function () {
        $actor = adminUser();
        $customer = Customer::factory()->create();
        $sale = $this->actingAs($actor, 'sanctum')
            ->postJson('/api/sales', salePayload($customer->id))
            ->json('data');

        // net_total is 88; pay 50 of it.
        $this->actingAs($actor, 'sanctum')
            ->postJson("/api/sales/{$sale['id']}/payments", ['amount' => 50, 'payment_date' => now()->toDateString()])
            ->assertStatus(201);

        $this->actingAs($actor, 'sanctum')
            ->putJson("/api/sales/{$sale['id']}", [
                'items' => [
                    ['item_name' => 'Consulting', 'qty' => 1, 'unit_price' => 10, 'discount' => 0, 'vat' => 0],
                ],
            ])
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['items']]);
    });
});

describe('destroy', function () {
    it('soft-deletes the sale and reverses its ledger entry', function () {
        $actor = adminUser();
        $customer = Customer::factory()->create(['opening_balance' => 0, 'current_balance' => 0]);
        $sale = $this->actingAs($actor, 'sanctum')
            ->postJson('/api/sales', salePayload($customer->id))
            ->json('data');

        expect($customer->fresh()->current_balance)->toEqual('88.00');

        $this->actingAs($actor, 'sanctum')
            ->deleteJson("/api/sales/{$sale['id']}")
            ->assertOk();

        $this->assertSoftDeleted('sales', ['id' => $sale['id']]);
        expect($customer->fresh()->current_balance)->toEqual('0.00');
    });
});

describe('payments', function () {
    it('rejects a payment that exceeds the due amount', function () {
        $actor = adminUser();
        $customer = Customer::factory()->create();
        $sale = $this->actingAs($actor, 'sanctum')
            ->postJson('/api/sales', salePayload($customer->id))
            ->json('data');

        // net_total is 88.
        $this->actingAs($actor, 'sanctum')
            ->postJson("/api/sales/{$sale['id']}/payments", ['amount' => 200, 'payment_date' => now()->toDateString()])
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['amount']]);
    });

    it('records a partial payment, reduces the due amount, and decreases the customer balance', function () {
        $actor = adminUser();
        $customer = Customer::factory()->create(['opening_balance' => 0, 'current_balance' => 0]);
        $sale = $this->actingAs($actor, 'sanctum')
            ->postJson('/api/sales', salePayload($customer->id))
            ->json('data');

        // net_total is 88.
        $response = $this->actingAs($actor, 'sanctum')
            ->postJson("/api/sales/{$sale['id']}/payments", [
                'amount' => 30,
                'payment_date' => now()->toDateString(),
                'notes' => 'partial payment',
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.paid_amount', 30)
            ->assertJsonPath('data.due_amount', 58);

        expect($customer->fresh()->current_balance)->toEqual('58.00');
        $this->assertDatabaseHas('customer_transactions', [
            'customer_id' => $customer->id,
            'transaction_type' => 'Payment Received',
            'credit' => 30,
        ]);
    });

    it('allows paying off the remaining due amount across multiple partial payments', function () {
        $actor = adminUser();
        $customer = Customer::factory()->create();
        $sale = $this->actingAs($actor, 'sanctum')
            ->postJson('/api/sales', salePayload($customer->id))
            ->json('data');

        // net_total is 88.
        $this->actingAs($actor, 'sanctum')
            ->postJson("/api/sales/{$sale['id']}/payments", ['amount' => 58, 'payment_date' => now()->toDateString()])
            ->assertStatus(201);

        $response = $this->actingAs($actor, 'sanctum')
            ->postJson("/api/sales/{$sale['id']}/payments", ['amount' => 30, 'payment_date' => now()->toDateString()]);

        $response->assertStatus(201)
            ->assertJsonPath('data.paid_amount', 88)
            ->assertJsonPath('data.due_amount', 0);
    });
});
