<?php

use App\Models\ChartOfAccount;
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
        $incomeAccount = ChartOfAccount::factory()->revenue()->create();
        $cash = ChartOfAccount::factory()->asset()->create();

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
        $expenseAccount = ChartOfAccount::factory()->expense()->create();
        $anotherExpenseAccount = ChartOfAccount::factory()->expense()->create();

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
        $rent = ChartOfAccount::factory()->expense()->create(['name' => 'Office Rent']);
        $cash = ChartOfAccount::factory()->asset()->create(['name' => 'Cash in Hand']);

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
        $commission = ChartOfAccount::factory()->revenue()->create();
        $bank = ChartOfAccount::factory()->asset()->create();

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

        $this->assertSoftDeleted('income_expense_transactions', ['id' => $transaction->id]);
    });
});

describe('account balances', function () {
    it('posts an expense by raising the expense account and lowering the asset account', function () {
        $actor = adminUser();
        $rent = ChartOfAccount::factory()->expense()->create();
        $cash = ChartOfAccount::factory()->asset()->create(['balance' => 10000]);

        $this->actingAs($actor, 'sanctum')->postJson('/api/income-expenses', [
            'type' => 'expense',
            'income_expense_account_id' => $rent->id,
            'payment_account_id' => $cash->id,
            'amount' => 2500,
            'transaction_date' => now()->toDateString(),
        ])->assertStatus(201);

        expect((float) $rent->fresh()->balance)->toBe(2500.0)
            ->and((float) $cash->fresh()->balance)->toBe(7500.0);
    });

    it('posts an income by raising both the revenue and the asset account', function () {
        $actor = adminUser();
        $sales = ChartOfAccount::factory()->revenue()->create();
        $bank = ChartOfAccount::factory()->asset()->create();

        $this->actingAs($actor, 'sanctum')->postJson('/api/income-expenses', [
            'type' => 'income',
            'income_expense_account_id' => $sales->id,
            'payment_account_id' => $bank->id,
            'amount' => 1500,
            'transaction_date' => now()->toDateString(),
        ])->assertStatus(201);

        expect((float) $sales->fresh()->balance)->toBe(1500.0)
            ->and((float) $bank->fresh()->balance)->toBe(1500.0);
    });

    it('re-posts balances when an entry amount is edited and reverses them on delete', function () {
        $actor = adminUser();
        $rent = ChartOfAccount::factory()->expense()->create();
        $cash = ChartOfAccount::factory()->asset()->create(['balance' => 10000]);

        $id = $this->actingAs($actor, 'sanctum')->postJson('/api/income-expenses', [
            'type' => 'expense',
            'income_expense_account_id' => $rent->id,
            'payment_account_id' => $cash->id,
            'amount' => 2500,
            'transaction_date' => now()->toDateString(),
        ])->json('data.id');

        $this->actingAs($actor, 'sanctum')->putJson("/api/income-expenses/{$id}", ['amount' => 1000])->assertOk();

        expect((float) $rent->fresh()->balance)->toBe(1000.0)
            ->and((float) $cash->fresh()->balance)->toBe(9000.0);

        $this->actingAs($actor, 'sanctum')->deleteJson("/api/income-expenses/{$id}")->assertOk();

        expect((float) $rent->fresh()->balance)->toBe(0.0)
            ->and((float) $cash->fresh()->balance)->toBe(10000.0);
    });

    it('rejects posting to a group account', function () {
        $actor = adminUser();
        $group = ChartOfAccount::factory()->expense()->group()->create();
        $cash = ChartOfAccount::factory()->asset()->create();

        $this->actingAs($actor, 'sanctum')->postJson('/api/income-expenses', [
            'type' => 'expense',
            'income_expense_account_id' => $group->id,
            'payment_account_id' => $cash->id,
            'amount' => 100,
            'transaction_date' => now()->toDateString(),
        ])->assertStatus(422)->assertJsonStructure(['data' => ['income_expense_account_id']]);
    });

    it('rejects posting to a closed account', function () {
        $actor = adminUser();
        $rent = ChartOfAccount::factory()->expense()->create();
        $cash = ChartOfAccount::factory()->asset()->closed()->create();

        $this->actingAs($actor, 'sanctum')->postJson('/api/income-expenses', [
            'type' => 'expense',
            'income_expense_account_id' => $rent->id,
            'payment_account_id' => $cash->id,
            'amount' => 100,
            'transaction_date' => now()->toDateString(),
        ])->assertStatus(422)->assertJsonStructure(['data' => ['payment_account_id']]);
    });
});
