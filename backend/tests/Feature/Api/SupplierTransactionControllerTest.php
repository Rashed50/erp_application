<?php

use App\Models\ChartOfAccount;
use App\Models\Supplier;
use App\Models\SupplierTransaction;
use Database\Seeders\ChartOfAccountSeeder;

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
        $supplier = Supplier::factory()->create(['opening_balance' => 100]);

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

    it('rejects a purchase transaction entered as a debit', function () {
        $supplier = Supplier::factory()->create();

        $this->actingAs(adminUser(), 'sanctum')
            ->postJson("/api/suppliers/{$supplier->id}/transactions", [
                'transaction_type' => 'Purchase',
                'debit' => 200,
                'transaction_date' => now()->toDateString(),
            ])
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['credit']]);
    });

    it('records a debit (bill payment) transaction and decreases the supplier balance', function () {
        $actor = adminUser();
        $supplier = Supplier::factory()->create(['opening_balance' => 500]);
        $this->seed(ChartOfAccountSeeder::class);
        $cash = ChartOfAccount::query()->where('account_number', '1010')->firstOrFail();
        $payable = ChartOfAccount::query()->where('account_number', '2010')->firstOrFail();

        $this->actingAs($actor, 'sanctum')
            ->postJson("/api/suppliers/{$supplier->id}/transactions", [
                'transaction_type' => 'Bill Payment',
                'debit' => 200,
                'payment_account_id' => $cash->id,
                'transaction_date' => now()->toDateString(),
            ])
            ->assertStatus(201);

        $transaction = $supplier->transactions()->sole();

        expect($supplier->fresh()->current_balance)->toEqual('300.00')
            ->and((float) $cash->fresh()->balance)->toBe(-200.0)
            ->and((float) $payable->fresh()->balance)->toBe(-200.0)
            ->and($transaction->payment_account_id)->toBe($cash->id)
            ->and($transaction->accountTransaction->source->is($transaction))->toBeTrue();
    });
});

describe('bill payment posting', function () {
    beforeEach(function () {
        $this->seed(ChartOfAccountSeeder::class);
        $this->cash = ChartOfAccount::query()->where('account_number', '1010')->firstOrFail();
        $this->payable = ChartOfAccount::query()->where('account_number', '2010')->firstOrFail();
        $this->revenue = ChartOfAccount::query()->where('account_number', '4010')->firstOrFail();
    });

    it('requires a payment account', function () {
        $supplier = Supplier::factory()->create();

        $this->actingAs(adminUser(), 'sanctum')
            ->postJson("/api/suppliers/{$supplier->id}/transactions", [
                'transaction_type' => 'Bill Payment',
                'debit' => 200,
                'transaction_date' => now()->toDateString(),
            ])
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['payment_account_id']]);
    });

    it('requires an open cash or bank account', function () {
        $supplier = Supplier::factory()->create();

        $this->actingAs(adminUser(), 'sanctum')
            ->postJson("/api/suppliers/{$supplier->id}/transactions", [
                'transaction_type' => 'Bill Payment',
                'debit' => 200,
                'payment_account_id' => $this->revenue->id,
                'transaction_date' => now()->toDateString(),
            ])
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['payment_account_id']]);
    });

    it('rejects a bill payment entered as a credit', function () {
        $supplier = Supplier::factory()->create();

        $this->actingAs(adminUser(), 'sanctum')
            ->postJson("/api/suppliers/{$supplier->id}/transactions", [
                'transaction_type' => 'Bill Payment',
                'credit' => 200,
                'payment_account_id' => $this->cash->id,
                'transaction_date' => now()->toDateString(),
            ])
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['debit']]);
    });

    it('reverses the cash and payable posting when the transaction is reversed', function () {
        $supplier = Supplier::factory()->create();
        $transactionId = $this->actingAs(adminUser(), 'sanctum')
            ->postJson("/api/suppliers/{$supplier->id}/transactions", [
                'transaction_type' => 'Bill Payment',
                'debit' => 200,
                'payment_account_id' => $this->cash->id,
                'transaction_date' => now()->toDateString(),
            ])
            ->json('data.id');

        $this->actingAs(adminUser(), 'sanctum')
            ->deleteJson("/api/suppliers/{$supplier->id}/transactions/{$transactionId}")
            ->assertOk();

        expect((float) $this->cash->fresh()->balance)->toBe(0.0)
            ->and((float) $this->payable->fresh()->balance)->toBe(0.0);
    });

    it('rejects a payment account for a purchase transaction', function () {
        $supplier = Supplier::factory()->create();

        $this->actingAs(adminUser(), 'sanctum')
            ->postJson("/api/suppliers/{$supplier->id}/transactions", [
                'transaction_type' => 'Purchase',
                'credit' => 200,
                'payment_account_id' => $this->cash->id,
                'transaction_date' => now()->toDateString(),
            ])
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['payment_account_id']]);
    });
});

describe('destroy (reverse)', function () {
    it('reverses a transaction and restores the supplier balance', function () {
        $actor = adminUser();
        $supplier = Supplier::factory()->create(['opening_balance' => 100]);
        $transaction = SupplierTransaction::factory()->for($supplier)->create(['debit' => 0, 'credit' => 400]);
        expect($supplier->fresh()->current_balance)->toEqual(500.0);

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
