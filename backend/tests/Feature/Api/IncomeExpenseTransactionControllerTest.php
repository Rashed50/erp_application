<?php

use App\Models\IncomeExpenseAccount;
use App\Models\IncomeExpenseTransaction;

describe('index', function () {
    it('returns 401 when no token is provided', function () {
        $this->getJson('/api/income-expenses')->assertStatus(401);
    });

    it('returns a paginated list of transactions', function () {
        $actor = adminUser();
        IncomeExpenseTransaction::factory()->count(3)->create();

        $response = $this->actingAs($actor, 'sanctum')->getJson('/api/income-expenses');

        $response->assertOk()->assertJsonCount(3, 'data.transactions');
    });

    it('filters transactions by type', function () {
        $actor = adminUser();
        IncomeExpenseTransaction::factory()->create();
        IncomeExpenseTransaction::factory()->income()->create();

        $response = $this->actingAs($actor, 'sanctum')->getJson('/api/income-expenses?type=income');

        $response->assertOk()->assertJsonCount(1, 'data.transactions');
    });
});

describe('store', function () {
    it('requires type, both accounts, amount, and transaction_date', function () {
        $actor = adminUser();

        $this->actingAs($actor, 'sanctum')
            ->postJson('/api/income-expenses', [])
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['type', 'income_expense_account_id', 'payment_account_id', 'amount', 'transaction_date']]);
    });

    it('rejects a category account whose type does not match the entry type', function () {
        $actor = adminUser();
        $incomeAccount = IncomeExpenseAccount::factory()->income()->create();
        $cash = IncomeExpenseAccount::factory()->asset()->create();

        $this->actingAs($actor, 'sanctum')
            ->postJson('/api/income-expenses', [
                'type' => 'expense',
                'income_expense_account_id' => $incomeAccount->id,
                'payment_account_id' => $cash->id,
                'amount' => 100,
                'transaction_date' => now()->toDateString(),
            ])
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['income_expense_account_id']]);
    });

    it('rejects a payment account that is not an asset account', function () {
        $actor = adminUser();
        $expenseAccount = IncomeExpenseAccount::factory()->expense()->create();
        $anotherExpenseAccount = IncomeExpenseAccount::factory()->expense()->create();

        $this->actingAs($actor, 'sanctum')
            ->postJson('/api/income-expenses', [
                'type' => 'expense',
                'income_expense_account_id' => $expenseAccount->id,
                'payment_account_id' => $anotherExpenseAccount->id,
                'amount' => 100,
                'transaction_date' => now()->toDateString(),
            ])
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['payment_account_id']]);
    });

    it('records a valid expense entry', function () {
        $actor = adminUser();
        $rent = IncomeExpenseAccount::factory()->expense()->create(['name' => 'Office Rent']);
        $cash = IncomeExpenseAccount::factory()->asset()->create(['name' => 'Cash in Hand']);

        $response = $this->actingAs($actor, 'sanctum')
            ->postJson('/api/income-expenses', [
                'type' => 'expense',
                'income_expense_account_id' => $rent->id,
                'payment_account_id' => $cash->id,
                'amount' => 5000,
                'transaction_date' => now()->toDateString(),
                'description' => 'September rent',
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.type', 'expense')
            ->assertJsonPath('data.account_name', 'Office Rent')
            ->assertJsonPath('data.payment_account_name', 'Cash in Hand')
            ->assertJsonPath('data.amount', 5000);

        $this->assertDatabaseHas('income_expense_transactions', [
            'income_expense_account_id' => $rent->id,
            'payment_account_id' => $cash->id,
            'amount' => 5000,
        ]);
    });

    it('records a valid income entry', function () {
        $actor = adminUser();
        $commission = IncomeExpenseAccount::factory()->income()->create();
        $bank = IncomeExpenseAccount::factory()->asset()->create();

        $this->actingAs($actor, 'sanctum')
            ->postJson('/api/income-expenses', [
                'type' => 'income',
                'income_expense_account_id' => $commission->id,
                'payment_account_id' => $bank->id,
                'amount' => 1500,
                'transaction_date' => now()->toDateString(),
            ])
            ->assertStatus(201)
            ->assertJsonPath('data.type', 'income');
    });
});

describe('destroy', function () {
    it('deletes a transaction', function () {
        $actor = adminUser();
        $transaction = IncomeExpenseTransaction::factory()->create();

        $this->actingAs($actor, 'sanctum')
            ->deleteJson("/api/income-expenses/{$transaction->id}")
            ->assertOk();

        $this->assertDatabaseMissing('income_expense_transactions', ['id' => $transaction->id]);
    });
});
