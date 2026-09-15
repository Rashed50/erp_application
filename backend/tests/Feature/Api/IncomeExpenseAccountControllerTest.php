<?php

use App\Models\IncomeExpenseAccount;
use App\Models\IncomeExpenseTransaction;

describe('index', function () {
    it('returns 401 when no token is provided', function () {
        $this->getJson('/api/ledger-accounts')->assertStatus(401);
    });

    it('returns a paginated list of accounts', function () {
        $actor = adminUser();
        IncomeExpenseAccount::factory()->count(3)->create();

        $response = $this->actingAs($actor, 'sanctum')->getJson('/api/ledger-accounts');

        $response->assertOk()->assertJsonCount(3, 'data.accounts');
    });

    it('filters accounts by type', function () {
        $actor = adminUser();
        IncomeExpenseAccount::factory()->asset()->create();
        IncomeExpenseAccount::factory()->expense()->create();

        $response = $this->actingAs($actor, 'sanctum')->getJson('/api/ledger-accounts?type=asset');

        $response->assertOk()->assertJsonCount(1, 'data.accounts');
    });
});

describe('store', function () {
    it('requires a name and a valid type', function () {
        $actor = adminUser();

        $this->actingAs($actor, 'sanctum')
            ->postJson('/api/ledger-accounts', [])
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['name', 'type']]);
    });

    it('rejects a type outside asset, income, or expense', function () {
        $actor = adminUser();

        $this->actingAs($actor, 'sanctum')
            ->postJson('/api/ledger-accounts', ['name' => 'Cash', 'type' => 'liability'])
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['type']]);
    });

    it('creates an account', function () {
        $actor = adminUser();

        $response = $this->actingAs($actor, 'sanctum')
            ->postJson('/api/ledger-accounts', ['name' => 'Cash in Hand', 'type' => 'asset']);

        $response->assertStatus(201)->assertJsonPath('data.type', 'asset');
        $this->assertDatabaseHas('income_expense_accounts', ['name' => 'Cash in Hand', 'type' => 'asset']);
    });
});

describe('update', function () {
    it('does not allow changing the type', function () {
        $actor = adminUser();
        $account = IncomeExpenseAccount::factory()->asset()->create();

        $this->actingAs($actor, 'sanctum')
            ->putJson("/api/ledger-accounts/{$account->id}", ['type' => 'expense'])
            ->assertOk();

        $this->assertDatabaseHas('income_expense_accounts', ['id' => $account->id, 'type' => 'asset']);
    });
});

describe('destroy', function () {
    it('deletes an account with no transactions', function () {
        $actor = adminUser();
        $account = IncomeExpenseAccount::factory()->create();

        $this->actingAs($actor, 'sanctum')
            ->deleteJson("/api/ledger-accounts/{$account->id}")
            ->assertOk();

        $this->assertDatabaseMissing('income_expense_accounts', ['id' => $account->id]);
    });

    it('rejects deleting an account referenced by a transaction', function () {
        $actor = adminUser();
        $expenseAccount = IncomeExpenseAccount::factory()->expense()->create();
        IncomeExpenseTransaction::factory()->create(['income_expense_account_id' => $expenseAccount->id]);

        $this->actingAs($actor, 'sanctum')
            ->deleteJson("/api/ledger-accounts/{$expenseAccount->id}")
            ->assertStatus(422);

        $this->assertDatabaseHas('income_expense_accounts', ['id' => $expenseAccount->id]);
    });
});
