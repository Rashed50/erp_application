<?php

use App\Models\Customer;
use App\Models\CustomerTransaction;

describe('index', function () {
    it('lists transactions for a customer', function () {
        $actor = adminUser();
        $customer = Customer::factory()->create();
        CustomerTransaction::factory()->for($customer)->count(2)->create();

        $response = $this->actingAs($actor, 'sanctum')
            ->getJson("/api/customers/{$customer->id}/transactions");

        $response->assertOk()->assertJsonCount(2, 'data.transactions');
    });
});

describe('store', function () {
    it('rejects a transaction with both debit and credit set', function () {
        $actor = adminUser();
        $customer = Customer::factory()->create();

        $this->actingAs($actor, 'sanctum')
            ->postJson("/api/customers/{$customer->id}/transactions", [
                'transaction_type' => 'Invoice',
                'debit' => 100,
                'credit' => 50,
                'transaction_date' => now()->toDateString(),
            ])
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['debit']]);
    });

    it('rejects a transaction with neither debit nor credit set', function () {
        $actor = adminUser();
        $customer = Customer::factory()->create();

        $this->actingAs($actor, 'sanctum')
            ->postJson("/api/customers/{$customer->id}/transactions", [
                'transaction_type' => 'Invoice',
                'transaction_date' => now()->toDateString(),
            ])
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['debit']]);
    });

    it('records a debit transaction and increases the customer balance', function () {
        $actor = adminUser();
        $customer = Customer::factory()->create(['opening_balance' => 100, 'current_balance' => 100]);

        $response = $this->actingAs($actor, 'sanctum')
            ->postJson("/api/customers/{$customer->id}/transactions", [
                'transaction_type' => 'Invoice',
                'invoice_no' => 'INV-1001',
                'debit' => 400,
                'transaction_date' => now()->toDateString(),
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.debit', 400);

        expect($customer->fresh()->current_balance)->toEqual('500.00');
    });

    it('records a credit transaction and decreases the customer balance', function () {
        $actor = adminUser();
        $customer = Customer::factory()->create(['opening_balance' => 500, 'current_balance' => 500]);

        $this->actingAs($actor, 'sanctum')
            ->postJson("/api/customers/{$customer->id}/transactions", [
                'transaction_type' => 'Payment Received',
                'credit' => 200,
                'transaction_date' => now()->toDateString(),
            ])
            ->assertStatus(201);

        expect($customer->fresh()->current_balance)->toEqual('300.00');
    });
});

describe('destroy (reverse)', function () {
    it('reverses a transaction and restores the customer balance', function () {
        $actor = adminUser();
        $customer = Customer::factory()->create(['opening_balance' => 100, 'current_balance' => 100]);
        $transaction = CustomerTransaction::factory()->for($customer)->create(['debit' => 400, 'credit' => 0]);
        $customer->update(['current_balance' => 500]);

        $response = $this->actingAs($actor, 'sanctum')
            ->deleteJson("/api/customers/{$customer->id}/transactions/{$transaction->id}");

        $response->assertOk()->assertJsonPath('data.status', false);

        expect($customer->fresh()->current_balance)->toEqual('100.00');
        expect($transaction->fresh()->status)->toBeFalse();
    });

    it('rejects reversing an already-reversed transaction', function () {
        $actor = adminUser();
        $customer = Customer::factory()->create();
        $transaction = CustomerTransaction::factory()->for($customer)->create(['status' => false]);

        $this->actingAs($actor, 'sanctum')
            ->deleteJson("/api/customers/{$customer->id}/transactions/{$transaction->id}")
            ->assertStatus(422);
    });

    it('returns 404 when the transaction does not belong to the customer', function () {
        $actor = adminUser();
        $customer = Customer::factory()->create();
        $otherCustomer = Customer::factory()->create();
        $transaction = CustomerTransaction::factory()->for($otherCustomer)->create();

        $this->actingAs($actor, 'sanctum')
            ->deleteJson("/api/customers/{$customer->id}/transactions/{$transaction->id}")
            ->assertStatus(404);
    });
});
