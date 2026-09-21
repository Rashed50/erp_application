<?php

use App\Models\Supplier;
use App\Models\SupplierTransaction;

describe('index', function () {
    it('lists transactions for a supplier', function () {
        $actor = adminUser();
        $supplier = Supplier::factory()->create();
        SupplierTransaction::factory()->for($supplier)->count(2)->create();

        $response = $this->actingAs($actor, 'sanctum')
            ->getJson("/api/suppliers/{$supplier->id}/transactions");

        $response->assertOk()->assertJsonCount(2, 'data.transactions');
    });
});

describe('store', function () {
    it('rejects a transaction with both debit and credit set', function () {
        $actor = adminUser();
        $supplier = Supplier::factory()->create();

        $this->actingAs($actor, 'sanctum')
            ->postJson("/api/suppliers/{$supplier->id}/transactions", [
                'transaction_type' => 'Purchase',
                'debit' => 100,
                'credit' => 50,
                'transaction_date' => now()->toDateString(),
            ])
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['debit']]);
    });

    it('rejects a transaction with neither debit nor credit set', function () {
        $actor = adminUser();
        $supplier = Supplier::factory()->create();

        $this->actingAs($actor, 'sanctum')
            ->postJson("/api/suppliers/{$supplier->id}/transactions", [
                'transaction_type' => 'Purchase',
                'transaction_date' => now()->toDateString(),
            ])
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['debit']]);
    });

    it('records a credit (purchase) transaction and increases the supplier balance', function () {
        $actor = adminUser();
        $supplier = Supplier::factory()->create(['opening_balance' => 100, 'current_balance' => 100]);

        $response = $this->actingAs($actor, 'sanctum')
            ->postJson("/api/suppliers/{$supplier->id}/transactions", [
                'transaction_type' => 'Purchase',
                'invoice_no' => 'PUR-1001',
                'credit' => 400,
                'transaction_date' => now()->toDateString(),
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.credit', 400)
            ->assertJsonPath('data.status', true);

        expect($supplier->fresh()->current_balance)->toEqual('500.00');
    });

    it('records a debit (bill payment) transaction and decreases the supplier balance', function () {
        $actor = adminUser();
        $supplier = Supplier::factory()->create(['opening_balance' => 500, 'current_balance' => 500]);

        $this->actingAs($actor, 'sanctum')
            ->postJson("/api/suppliers/{$supplier->id}/transactions", [
                'transaction_type' => 'Bill Payment',
                'debit' => 200,
                'transaction_date' => now()->toDateString(),
            ])
            ->assertStatus(201);

        expect($supplier->fresh()->current_balance)->toEqual('300.00');
    });
});

describe('destroy (reverse)', function () {
    it('reverses a transaction and restores the supplier balance', function () {
        $actor = adminUser();
        $supplier = Supplier::factory()->create(['opening_balance' => 100, 'current_balance' => 100]);
        $transaction = SupplierTransaction::factory()->for($supplier)->create(['debit' => 0, 'credit' => 400]);
        $supplier->update(['current_balance' => 500]);

        $response = $this->actingAs($actor, 'sanctum')
            ->deleteJson("/api/suppliers/{$supplier->id}/transactions/{$transaction->id}");

        $response->assertOk()->assertJsonPath('data.status', false);

        expect($supplier->fresh()->current_balance)->toEqual('100.00');
        expect($transaction->fresh()->status)->toBeFalse();
    });

    it('rejects reversing an already-reversed transaction', function () {
        $actor = adminUser();
        $supplier = Supplier::factory()->create();
        $transaction = SupplierTransaction::factory()->for($supplier)->create(['status' => false]);

        $this->actingAs($actor, 'sanctum')
            ->deleteJson("/api/suppliers/{$supplier->id}/transactions/{$transaction->id}")
            ->assertStatus(422);
    });

    it('returns 404 when the transaction does not belong to the supplier', function () {
        $actor = adminUser();
        $supplier = Supplier::factory()->create();
        $otherSupplier = Supplier::factory()->create();
        $transaction = SupplierTransaction::factory()->for($otherSupplier)->create();

        $this->actingAs($actor, 'sanctum')
            ->deleteJson("/api/suppliers/{$supplier->id}/transactions/{$transaction->id}")
            ->assertStatus(404);
    });
});
