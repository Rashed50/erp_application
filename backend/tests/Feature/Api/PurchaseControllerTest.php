<?php

use App\Models\Purchase;
use App\Models\Supplier;

function purchasePayload(int $supplierId, array $overrides = []): array
{
    return array_merge([
        'supplier_id' => $supplierId,
        'purchase_type' => 'product',
        'invoice_number' => 'PINV-'.fake()->unique()->numerify('#####'),
        'description' => 'Office supplies',
        'issue_date' => now()->toDateString(),
        'purchase_date' => now()->toDateString(),
        'notes' => 'test purchase',
        'items' => [
            ['item_name' => 'Paper', 'qty' => 10, 'unit_price' => 5, 'discount' => 0, 'vat' => 0],
            ['item_name' => 'Pens', 'qty' => 20, 'unit_price' => 2, 'discount' => 5, 'vat' => 3],
        ],
    ], $overrides);
}

describe('index', function () {
    it('returns 401 when no token is provided', function () {
        $this->getJson('/api/purchases')->assertStatus(401);
    });

    it('returns a paginated list of purchases', function () {
        $actor = adminUser();
        Purchase::factory()->count(3)->create();

        $response = $this->actingAs($actor, 'sanctum')->getJson('/api/purchases');

        $response->assertOk()->assertJsonCount(3, 'data.purchases');
    });

    it('filters purchases by supplier', function () {
        $actor = adminUser();
        $supplier = Supplier::factory()->create();
        Purchase::factory()->for($supplier)->create();
        Purchase::factory()->create();

        $response = $this->actingAs($actor, 'sanctum')->getJson("/api/purchases?supplier_id={$supplier->id}");

        $response->assertOk()->assertJsonCount(1, 'data.purchases');
    });
});

describe('store', function () {
    it('requires supplier_id, invoice_number, dates, and items', function () {
        $actor = adminUser();

        $this->actingAs($actor, 'sanctum')
            ->postJson('/api/purchases', [])
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['supplier_id', 'invoice_number', 'issue_date', 'purchase_date', 'items']]);
    });

    it('rejects an invoice_number that is already taken', function () {
        $actor = adminUser();
        $supplier = Supplier::factory()->create();
        $existing = Purchase::factory()->for($supplier)->create();

        $this->actingAs($actor, 'sanctum')
            ->postJson('/api/purchases', purchasePayload($supplier->id, ['invoice_number' => $existing->invoice_number]))
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['invoice_number']]);
    });

    it('computes totals from items instead of trusting client-supplied totals', function () {
        $actor = adminUser();
        $supplier = Supplier::factory()->create();

        // Paper: 10 * 5 = 50 gross. Pens: 20 * 2 = 40 gross, -5 discount, +3 vat = 38 net.
        // total_amount = 90, discount_amount = 5, vat_amount = 3, net_total = 88.
        $response = $this->actingAs($actor, 'sanctum')
            ->postJson('/api/purchases', purchasePayload($supplier->id, [
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

    it('posts a Purchase ledger entry that increases the supplier balance', function () {
        $actor = adminUser();
        $supplier = Supplier::factory()->create(['opening_balance' => 100, 'current_balance' => 100]);

        $this->actingAs($actor, 'sanctum')
            ->postJson('/api/purchases', purchasePayload($supplier->id))
            ->assertStatus(201);

        // net_total is 88 (see totals test above).
        expect($supplier->fresh()->current_balance)->toEqual('188.00');
        $this->assertDatabaseHas('supplier_transactions', [
            'supplier_id' => $supplier->id,
            'transaction_type' => 'Purchase',
            'credit' => 88,
        ]);
    });
});

describe('update', function () {
    it('ignores a supplier_id sent in the update payload', function () {
        $actor = adminUser();
        $supplier = Supplier::factory()->create();
        $otherSupplier = Supplier::factory()->create();
        $purchase = $this->actingAs($actor, 'sanctum')
            ->postJson('/api/purchases', purchasePayload($supplier->id))
            ->json('data');

        $this->actingAs($actor, 'sanctum')
            ->putJson("/api/purchases/{$purchase['id']}", ['supplier_id' => $otherSupplier->id])
            ->assertOk();

        $this->assertDatabaseHas('purchases', ['id' => $purchase['id'], 'supplier_id' => $supplier->id]);
    });

    it('recomputes totals and keeps the linked ledger entry in sync when items change', function () {
        $actor = adminUser();
        $supplier = Supplier::factory()->create(['opening_balance' => 0, 'current_balance' => 0]);
        $purchase = $this->actingAs($actor, 'sanctum')
            ->postJson('/api/purchases', purchasePayload($supplier->id))
            ->json('data');

        expect($supplier->fresh()->current_balance)->toEqual('88.00');

        $response = $this->actingAs($actor, 'sanctum')
            ->putJson("/api/purchases/{$purchase['id']}", [
                'items' => [
                    ['item_name' => 'Paper', 'qty' => 1, 'unit_price' => 100, 'discount' => 0, 'vat' => 0],
                ],
            ]);

        $response->assertOk()
            ->assertJsonPath('data.net_total', 100)
            ->assertJsonCount(1, 'data.items');

        // The ledger entry the purchase posted moves with it, not just the purchase row.
        expect($supplier->fresh()->current_balance)->toEqual('100.00');
    });

    it('rejects reducing the total below what has already been paid', function () {
        $actor = adminUser();
        $supplier = Supplier::factory()->create();
        $purchase = $this->actingAs($actor, 'sanctum')
            ->postJson('/api/purchases', purchasePayload($supplier->id))
            ->json('data');

        // net_total is 88; pay 50 of it.
        $this->actingAs($actor, 'sanctum')
            ->postJson("/api/purchases/{$purchase['id']}/payments", ['amount' => 50, 'payment_date' => now()->toDateString()])
            ->assertStatus(201);

        $this->actingAs($actor, 'sanctum')
            ->putJson("/api/purchases/{$purchase['id']}", [
                'items' => [
                    ['item_name' => 'Paper', 'qty' => 1, 'unit_price' => 10, 'discount' => 0, 'vat' => 0],
                ],
            ])
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['items']]);
    });
});

describe('payments', function () {
    it('rejects a payment that exceeds the due amount', function () {
        $actor = adminUser();
        $supplier = Supplier::factory()->create();
        $purchase = $this->actingAs($actor, 'sanctum')
            ->postJson('/api/purchases', purchasePayload($supplier->id))
            ->json('data');

        // net_total is 88.
        $this->actingAs($actor, 'sanctum')
            ->postJson("/api/purchases/{$purchase['id']}/payments", ['amount' => 200, 'payment_date' => now()->toDateString()])
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['amount']]);
    });

    it('records a partial payment, reduces the due amount, and decreases the supplier balance', function () {
        $actor = adminUser();
        $supplier = Supplier::factory()->create(['opening_balance' => 0, 'current_balance' => 0]);
        $purchase = $this->actingAs($actor, 'sanctum')
            ->postJson('/api/purchases', purchasePayload($supplier->id))
            ->json('data');

        // net_total is 88.
        $response = $this->actingAs($actor, 'sanctum')
            ->postJson("/api/purchases/{$purchase['id']}/payments", [
                'amount' => 30,
                'payment_date' => now()->toDateString(),
                'notes' => 'partial payment',
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.paid_amount', 30)
            ->assertJsonPath('data.due_amount', 58);

        expect($supplier->fresh()->current_balance)->toEqual('58.00');
        $this->assertDatabaseHas('supplier_transactions', [
            'supplier_id' => $supplier->id,
            'transaction_type' => 'Bill Payment',
            'debit' => 30,
        ]);
    });

    it('allows paying off the remaining due amount across multiple partial payments', function () {
        $actor = adminUser();
        $supplier = Supplier::factory()->create();
        $purchase = $this->actingAs($actor, 'sanctum')
            ->postJson('/api/purchases', purchasePayload($supplier->id))
            ->json('data');

        // net_total is 88.
        $this->actingAs($actor, 'sanctum')
            ->postJson("/api/purchases/{$purchase['id']}/payments", ['amount' => 58, 'payment_date' => now()->toDateString()])
            ->assertStatus(201);

        $response = $this->actingAs($actor, 'sanctum')
            ->postJson("/api/purchases/{$purchase['id']}/payments", ['amount' => 30, 'payment_date' => now()->toDateString()]);

        $response->assertStatus(201)
            ->assertJsonPath('data.paid_amount', 88)
            ->assertJsonPath('data.due_amount', 0);
    });
});

describe('destroy', function () {
    it('soft-deletes the purchase and reverses its ledger entry', function () {
        $actor = adminUser();
        $supplier = Supplier::factory()->create(['opening_balance' => 0, 'current_balance' => 0]);
        $purchase = $this->actingAs($actor, 'sanctum')
            ->postJson('/api/purchases', purchasePayload($supplier->id))
            ->json('data');

        expect($supplier->fresh()->current_balance)->toEqual('88.00');

        $this->actingAs($actor, 'sanctum')
            ->deleteJson("/api/purchases/{$purchase['id']}")
            ->assertOk();

        $this->assertSoftDeleted('purchases', ['id' => $purchase['id']]);
        expect($supplier->fresh()->current_balance)->toEqual('0.00');
    });
});
