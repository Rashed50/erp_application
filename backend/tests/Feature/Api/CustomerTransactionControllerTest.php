<?php

use App\Models\ChartOfAccount;
use App\Models\Customer;
use App\Models\CustomerTransaction;
use Database\Seeders\ChartOfAccountSeeder;

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
    it('rejects an invoice, which must come from a sale', function () {
        $customer = Customer::factory()->create();

        $this->actingAs(adminUser(), 'sanctum')
            ->postJson("/api/customers/{$customer->id}/transactions", [
                'transaction_type' => 'Invoice',
                'debit' => 100,
                'transaction_date' => now()->toDateString(),
            ])
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['transaction_type']]);

        expect($customer->transactions()->count())->toBe(0);
    });

    it('rejects a transaction with both debit and credit set', function () {
        $actor = adminUser();
        $customer = Customer::factory()->create();

        $this->actingAs($actor, 'sanctum')
            ->postJson("/api/customers/{$customer->id}/transactions", [
                'transaction_type' => 'Adjustment',
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
                'transaction_type' => 'Adjustment',
                'transaction_date' => now()->toDateString(),
            ])
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['debit']]);
    });

    it('records a debit transaction and increases the customer balance', function () {
        $actor = adminUser();
        $customer = Customer::factory()->create(['opening_balance' => 100]);

        $response = $this->actingAs($actor, 'sanctum')
            ->postJson("/api/customers/{$customer->id}/transactions", [
                'transaction_type' => 'Adjustment',
                'invoice_no' => 'INV-1001',
                'debit' => 400,
                'transaction_date' => now()->toDateString(),
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.debit', 400)
            // The `status` column defaults to true at the DB level, but that
            // default isn't visible on the in-memory model unless it's set
            // explicitly — this pins the immediate API response, not just a
            // re-fetch, to the correct value.
            ->assertJsonPath('data.status', true);

        expect($customer->fresh()->current_balance)->toEqual('500.00');
    });

    it('records a credit transaction and decreases the customer balance', function () {
        $actor = adminUser();
        $customer = Customer::factory()->create(['opening_balance' => 500]);

        $this->actingAs($actor, 'sanctum')
            ->postJson("/api/customers/{$customer->id}/transactions", [
                'transaction_type' => 'Adjustment',
                'credit' => 200,
                'transaction_date' => now()->toDateString(),
            ])
            ->assertStatus(201);

        expect($customer->fresh()->current_balance)->toEqual('300.00');
    });
});

describe('payment received', function () {
    beforeEach(function () {
        $this->seed(ChartOfAccountSeeder::class);

        $this->cash = ChartOfAccount::query()->where('account_number', '1010')->first();
        $this->receivable = ChartOfAccount::query()->where('account_number', '1030')->first();
        $this->revenue = ChartOfAccount::query()->where('account_number', '4010')->first();
    });

    it('debits the cash account and credits Accounts Receivable', function () {
        $customer = Customer::factory()->create(['opening_balance' => 500]);

        $this->actingAs(adminUser(), 'sanctum')
            ->postJson("/api/customers/{$customer->id}/transactions", [
                'transaction_type' => 'Payment Received',
                'credit' => 200,
                'payment_account_id' => $this->cash->id,
                'transaction_date' => now()->toDateString(),
            ])
            ->assertStatus(201);

        $payment = $customer->transactions()->sole();

        expect($customer->fresh()->current_balance)->toEqual('300.00')
            ->and((float) $this->cash->fresh()->balance)->toBe(200.0)
            ->and((float) $this->receivable->fresh()->balance)->toBe(-200.0)
            ->and($payment->payment_account_id)->toBe($this->cash->id)
            ->and($payment->accountTransaction->source->is($payment))->toBeTrue();
    });

    it('reverses the cash posting when the payment is reversed', function () {
        $actor = adminUser();
        $customer = Customer::factory()->create();

        $paymentId = $this->actingAs($actor, 'sanctum')
            ->postJson("/api/customers/{$customer->id}/transactions", [
                'transaction_type' => 'Payment Received',
                'credit' => 200,
                'payment_account_id' => $this->cash->id,
                'transaction_date' => now()->toDateString(),
            ])
            ->json('data.id');

        $this->actingAs($actor, 'sanctum')
            ->deleteJson("/api/customers/{$customer->id}/transactions/{$paymentId}")
            ->assertOk();

        expect((float) $this->cash->fresh()->balance)->toBe(0.0)
            ->and((float) $this->receivable->fresh()->balance)->toBe(0.0);
    });

    it('requires a payment account', function () {
        $customer = Customer::factory()->create();

        $this->actingAs(adminUser(), 'sanctum')
            ->postJson("/api/customers/{$customer->id}/transactions", [
                'transaction_type' => 'Payment Received',
                'credit' => 200,
                'transaction_date' => now()->toDateString(),
            ])
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['payment_account_id']]);
    });

    it('requires an open cash/bank payment account', function () {
        $customer = Customer::factory()->create();

        $this->actingAs(adminUser(), 'sanctum')
            ->postJson("/api/customers/{$customer->id}/transactions", [
                'transaction_type' => 'Payment Received',
                'credit' => 200,
                'payment_account_id' => $this->revenue->id,
                'transaction_date' => now()->toDateString(),
            ])
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['payment_account_id']]);
    });

    it('rejects a payment received as a debit', function () {
        $customer = Customer::factory()->create();

        $this->actingAs(adminUser(), 'sanctum')
            ->postJson("/api/customers/{$customer->id}/transactions", [
                'transaction_type' => 'Payment Received',
                'debit' => 200,
                'payment_account_id' => $this->cash->id,
                'transaction_date' => now()->toDateString(),
            ])
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['debit']]);
    });

    it('rejects a payment account on other transaction types', function () {
        $customer = Customer::factory()->create();

        $this->actingAs(adminUser(), 'sanctum')
            ->postJson("/api/customers/{$customer->id}/transactions", [
                'transaction_type' => 'Adjustment',
                'debit' => 200,
                'payment_account_id' => $this->cash->id,
                'transaction_date' => now()->toDateString(),
            ])
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['payment_account_id']]);
    });
});

describe('destroy (reverse)', function () {
    it('reverses a transaction and restores the customer balance', function () {
        $actor = adminUser();
        $customer = Customer::factory()->create(['opening_balance' => 100]);
        $transaction = CustomerTransaction::factory()->for($customer)->create(['debit' => 400, 'credit' => 0]);
        expect($customer->fresh()->current_balance)->toEqual(500.0);

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
