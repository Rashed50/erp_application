<?php

use App\Models\AccountTransaction;
use App\Models\AccountType;
use App\Models\ChartOfAccount;
use App\Models\IncomeExpenseTransaction;
use App\Services\AccountReportService;
use App\Services\LedgerPostingService;
use Database\Seeders\ChartOfAccountSeeder;

describe('index', function () {
    it('returns 401 when no token is provided', function () {
        $this->getJson('/api/ledger-accounts')->assertStatus(401);
    });

    it('returns a paginated list of accounts', function () {
        $actor = adminUser();
        ChartOfAccount::factory()->count(3)->create();

        $response = $this->actingAs($actor, 'sanctum')->getJson('/api/ledger-accounts');

        $response->assertOk()->assertJsonCount(3, 'data.accounts');
    });

    it('filters accounts by account type', function () {
        $actor = adminUser();
        ChartOfAccount::factory()->asset()->create();
        ChartOfAccount::factory()->expense()->create();

        $response = $this->actingAs($actor, 'sanctum')->getJson('/api/ledger-accounts?account_type_id='.AccountType::ASSET);

        $response->assertOk()->assertJsonCount(1, 'data.accounts');
    });

    it('filters accounts by parent and transaction flag', function () {
        $actor = adminUser();
        $group = ChartOfAccount::factory()->asset()->group()->create();
        ChartOfAccount::factory()->asset()->create(['parent_id' => $group->id]);
        ChartOfAccount::factory()->asset()->create();

        $this->actingAs($actor, 'sanctum')->getJson("/api/ledger-accounts?parent_id={$group->id}")
            ->assertOk()->assertJsonCount(1, 'data.accounts');

        $this->actingAs($actor, 'sanctum')->getJson('/api/ledger-accounts?is_transaction=0')
            ->assertOk()->assertJsonCount(1, 'data.accounts');
    });

    it('filters accounts by closed flag', function () {
        $actor = adminUser();
        ChartOfAccount::factory()->create(['is_closed' => true]);
        ChartOfAccount::factory()->count(2)->create(['is_closed' => false]);

        $this->actingAs($actor, 'sanctum')->getJson('/api/ledger-accounts?is_closed=0')
            ->assertOk()->assertJsonCount(2, 'data.accounts');
    });

    it('searches accounts by account type name', function () {
        $actor = adminUser();
        ChartOfAccount::factory()->asset()->create();
        ChartOfAccount::factory()->expense()->create();

        $this->actingAs($actor, 'sanctum')->getJson('/api/ledger-accounts?search=Expense')
            ->assertOk()
            ->assertJsonCount(1, 'data.accounts')
            ->assertJsonPath('data.accounts.0.account_type', 'Expense');
    });

    it('includes the creator name', function () {
        $actor = adminUser();
        ChartOfAccount::factory()->create(['created_by' => $actor->id]);

        $this->actingAs($actor, 'sanctum')->getJson('/api/ledger-accounts')
            ->assertOk()
            ->assertJsonPath('data.accounts.0.created_by_name', $actor->name);
    });
});

describe('account types', function () {
    it('lists the five account types', function () {
        $actor = adminUser();

        $this->actingAs($actor, 'sanctum')->getJson('/api/account-types')
            ->assertOk()
            ->assertJsonCount(5, 'data.account_types')
            ->assertJsonPath('data.account_types.0.name', 'Asset');
    });
});

describe('store', function () {
    it('requires a name and an account type or parent', function () {
        $actor = adminUser();

        $this->actingAs($actor, 'sanctum')
            ->postJson('/api/ledger-accounts', [])
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['name', 'account_type_id']]);
    });

    it('rejects an unknown account type', function () {
        $actor = adminUser();

        $this->actingAs($actor, 'sanctum')
            ->postJson('/api/ledger-accounts', ['name' => 'Cash', 'account_type_id' => 99])
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['account_type_id']]);
    });

    it('creates a top-level account at level zero', function () {
        $actor = adminUser();

        $response = $this->actingAs($actor, 'sanctum')
            ->postJson('/api/ledger-accounts', ['name' => 'Assets', 'account_number' => '1000', 'account_type_id' => AccountType::ASSET]);
        approvePending();

        $response->assertStatus(201)
            ->assertJsonPath('data.account_type', 'Asset')
            ->assertJsonPath('data.sibling_level', 0)
            ->assertJsonPath('data.is_transaction', false);
        $this->assertDatabaseHas('chart_of_accounts', ['name' => 'Assets', 'created_by' => $actor->id]);
    });

    it('creates a child account that inherits its parent type and level', function () {
        $actor = adminUser();
        $group = ChartOfAccount::factory()->expense()->group()->create();

        $response = $this->actingAs($actor, 'sanctum')
            ->postJson('/api/ledger-accounts', ['name' => 'Rent', 'parent_id' => $group->id, 'is_transaction' => true]);
        approvePending();

        $response->assertStatus(201)
            ->assertJsonPath('data.account_type_id', AccountType::EXPENSE)
            ->assertJsonPath('data.sibling_level', 1)
            ->assertJsonPath('data.parent_name', $group->name);
    });

    it('allows a child under a transaction account', function () {
        $actor = adminUser();
        $leaf = ChartOfAccount::factory()->asset()->create();

        $this->actingAs($actor, 'sanctum')
            ->postJson('/api/ledger-accounts', ['name' => 'Sub', 'parent_id' => $leaf->id])
            ->assertCreated()
            ->assertJsonPath('data.parent_id', $leaf->id);
        approvePending();
    });

    it('rejects a child whose type differs from its parent', function () {
        $actor = adminUser();
        $group = ChartOfAccount::factory()->asset()->group()->create();

        $this->actingAs($actor, 'sanctum')
            ->postJson('/api/ledger-accounts', ['name' => 'Sub', 'parent_id' => $group->id, 'account_type_id' => AccountType::EXPENSE])
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['account_type_id']]);
    });

    it('rejects a duplicate account number', function () {
        $actor = adminUser();
        ChartOfAccount::factory()->create(['account_number' => '1010']);

        $this->actingAs($actor, 'sanctum')
            ->postJson('/api/ledger-accounts', ['name' => 'Dup', 'account_number' => '1010', 'account_type_id' => AccountType::ASSET])
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['account_number']]);
    });
});

describe('store opening balance', function () {
    it('posts an asset opening balance as a journal entry against Owner equity', function () {
        $this->seed(ChartOfAccountSeeder::class);
        $actor = adminUser();

        $response = $this->actingAs($actor, 'sanctum')
            ->postJson('/api/ledger-accounts', ['name' => 'Petty Cash', 'account_type_id' => AccountType::ASSET, 'is_transaction' => true, 'opening_balance' => 500])
            ->assertStatus(201)
            ->assertJsonPath('data.opening_balance', 500)
            ->assertJsonPath('data.approved_at', null);
        approvePending();

        $account = ChartOfAccount::find($response->json('data.id'));
        $entry = AccountTransaction::query()->whereMorphedTo('source', $account)->sole();
        $equity = ChartOfAccount::where('account_number', '3000')->first();

        expect($entry->general_particular)->toBe('Initial Ledger account setup')
            ->and((float) $entry->total_amount)->toBe(500.0)
            ->and($equity->balance)->toBe(500.0);
    });

    it('posts a liability opening balance as a credit to the account and a debit to Owner equity', function () {
        $this->seed(ChartOfAccountSeeder::class);
        $actor = adminUser();

        $response = $this->actingAs($actor, 'sanctum')
            ->postJson('/api/ledger-accounts', ['name' => 'Advance Received from Customer', 'account_type_id' => AccountType::LIABILITY, 'is_transaction' => true, 'opening_balance' => 20000])
            ->assertStatus(201)
            ->assertJsonPath('data.opening_balance', 20000)
            ->assertJsonPath('data.approved_at', null);
        approvePending();

        $account = ChartOfAccount::find($response->json('data.id'));
        $equity = ChartOfAccount::where('account_number', '3000')->first();
        $entry = AccountTransaction::query()->whereMorphedTo('source', $account)->with('details')->sole();

        expect($entry->details)->toHaveCount(2)
            ->and((float) $entry->details->firstWhere('chart_of_account_id', $equity->id)->debit)->toBe(20000.0)
            ->and((float) $entry->details->firstWhere('chart_of_account_id', $account->id)->credit)->toBe(20000.0)
            ->and($equity->balance)->toBe(-20000.0);

        $reports = app(AccountReportService::class);
        $trialBalance = $reports->trialBalance(today()->toDateString(), today()->toDateString(), 'as_of_date');
        $balanceSheet = $reports->balanceSheet();

        expect($trialBalance['total_debit'])->toBe($trialBalance['total_credit'])
            ->and($balanceSheet['total_liabilities'])->toBe(20000.0)
            ->and($balanceSheet['total_equity'])->toBe(-20000.0)
            ->and($balanceSheet['is_balanced'])->toBeTrue();
    });

    it('posts each account type opening balance on its normal side', function (int $accountTypeId, float $expectedEquityBalance) {
        $this->seed(ChartOfAccountSeeder::class);
        $actor = adminUser();

        $response = $this->actingAs($actor, 'sanctum')
            ->postJson('/api/ledger-accounts', ['name' => 'Opening Test', 'account_type_id' => $accountTypeId, 'is_transaction' => true, 'opening_balance' => 750])
            ->assertStatus(201)
            ->assertJsonPath('data.approved_at', null);
        approvePending();

        $entry = AccountTransaction::query()->whereMorphedTo('source', ChartOfAccount::find($response->json('data.id')))->with('details')->sole();

        expect((float) $entry->details->sum('debit'))->toBe((float) $entry->details->sum('credit'))
            ->and(ChartOfAccount::where('account_number', '3000')->first()->balance)->toBe($expectedEquityBalance);
    })->with([
        'asset' => [AccountType::ASSET, 750.0],
        'expense' => [AccountType::EXPENSE, 750.0],
        'liability' => [AccountType::LIABILITY, -750.0],
        'equity' => [AccountType::OWNER_EQUITY, -750.0],
        'revenue' => [AccountType::REVENUE, -750.0],
    ]);

    it('skips the opening entry when Owner equity has not been seeded', function () {
        $actor = adminUser();

        $response = $this->actingAs($actor, 'sanctum')
            ->postJson('/api/ledger-accounts', ['name' => 'Bank Loan', 'account_type_id' => AccountType::LIABILITY, 'is_transaction' => true, 'opening_balance' => 500])
            ->assertStatus(201)
            ->assertJsonPath('data.balance', 0);
        approvePending();

        expect(AccountTransaction::query()->whereMorphedTo('source', ChartOfAccount::find($response->json('data.id')))->exists())->toBeFalse();
    });

    it('rejects a negative opening balance', function () {
        $actor = adminUser();

        $this->actingAs($actor, 'sanctum')
            ->postJson('/api/ledger-accounts', ['name' => 'Cash', 'account_type_id' => AccountType::ASSET, 'opening_balance' => -1])
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['opening_balance']]);
    });
});

describe('next account number', function () {
    it('suggests the next child number under the parent number', function () {
        $actor = adminUser();
        $group = ChartOfAccount::factory()->asset()->group()->create(['account_number' => '1000']);
        ChartOfAccount::factory()->asset()->create(['parent_id' => $group->id, 'account_number' => '1000.1']);

        $this->actingAs($actor, 'sanctum')
            ->getJson("/api/ledger-accounts/{$group->id}/next-account-number")
            ->assertOk()
            ->assertJsonPath('data.account_number', '1000.2');
    });

    it('skips a number that is already taken', function () {
        $actor = adminUser();
        $group = ChartOfAccount::factory()->asset()->group()->create(['account_number' => '2000']);
        ChartOfAccount::factory()->asset()->create(['account_number' => '2000.1']);

        $this->actingAs($actor, 'sanctum')
            ->getJson("/api/ledger-accounts/{$group->id}/next-account-number")
            ->assertOk()
            ->assertJsonPath('data.account_number', '2000.2');
    });
});

describe('update', function () {
    it('does not allow changing the account type', function () {
        $actor = adminUser();
        $account = ChartOfAccount::factory()->asset()->create();

        $this->actingAs($actor, 'sanctum')
            ->putJson("/api/ledger-accounts/{$account->id}", ['account_type_id' => AccountType::EXPENSE])
            ->assertOk();
        approvePending();

        $this->assertDatabaseHas('chart_of_accounts', ['id' => $account->id, 'account_type_id' => AccountType::ASSET]);
    });

    it('does not post a second opening entry when the account is edited', function () {
        $this->seed(ChartOfAccountSeeder::class);
        $actor = adminUser();

        $id = $this->actingAs($actor, 'sanctum')
            ->postJson('/api/ledger-accounts', ['name' => 'Advance Received from Customer', 'account_type_id' => AccountType::LIABILITY, 'is_transaction' => true, 'opening_balance' => 20000, 'opening_date' => '2026-01-01'])
            ->json('data.id');
        approvePending();

        $this->actingAs($actor, 'sanctum')
            ->putJson("/api/ledger-accounts/{$id}", ['name' => 'Customer Advances', 'opening_date' => '2026-02-01', 'opening_balance' => 99999])
            ->assertOk()
            ->assertJsonPath('data.opening_balance', 20000)
            ->assertJsonPath('data.approved_at', null);
        approvePending();

        $entry = AccountTransaction::query()->whereMorphedTo('source', ChartOfAccount::find($id))->sole();

        expect($entry->date->toDateString())->toBe('2026-02-01');
    });

    it('moves an account and re-levels its subtree', function () {
        $actor = adminUser();
        $root = ChartOfAccount::factory()->asset()->group()->create();
        $other = ChartOfAccount::factory()->asset()->group()->create(['parent_id' => $root->id]);
        $moving = ChartOfAccount::factory()->asset()->group()->create();
        $child = ChartOfAccount::factory()->asset()->create(['parent_id' => $moving->id]);

        $this->actingAs($actor, 'sanctum')
            ->putJson("/api/ledger-accounts/{$moving->id}", ['parent_id' => $other->id])
            ->assertOk()
            ->assertJsonPath('data.sibling_level', 2);
        approvePending();

        expect($child->fresh()->sibling_level)->toBe(3);
    });

    it('rejects moving an account beneath its own child', function () {
        $actor = adminUser();
        $parent = ChartOfAccount::factory()->asset()->group()->create();
        $child = ChartOfAccount::factory()->asset()->group()->create(['parent_id' => $parent->id]);

        $this->actingAs($actor, 'sanctum')
            ->putJson("/api/ledger-accounts/{$parent->id}", ['parent_id' => $child->id])
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['parent_id']]);
    });

    it('allows turning an account with children into a transaction account', function () {
        $actor = adminUser();
        $parent = ChartOfAccount::factory()->asset()->group()->create();
        ChartOfAccount::factory()->asset()->create(['parent_id' => $parent->id]);

        $this->actingAs($actor, 'sanctum')
            ->putJson("/api/ledger-accounts/{$parent->id}", ['is_transaction' => true])
            ->assertOk();
        approvePending();
    });

    it('only lets a predefined account be renamed or deactivated', function () {
        $actor = adminUser();
        $account = ChartOfAccount::factory()->asset()->predefined()->create();

        $this->actingAs($actor, 'sanctum')
            ->putJson("/api/ledger-accounts/{$account->id}", ['is_closed' => true])
            ->assertStatus(422);

        $this->actingAs($actor, 'sanctum')
            ->putJson("/api/ledger-accounts/{$account->id}", ['name' => 'Renamed'])
            ->assertOk();
        approvePending();
    });
});

describe('destroy', function () {
    it('deletes an account with no transactions', function () {
        $actor = adminUser();
        $account = ChartOfAccount::factory()->create();

        $this->actingAs($actor, 'sanctum')
            ->deleteJson("/api/ledger-accounts/{$account->id}")
            ->assertOk();

        $this->assertSoftDeleted('chart_of_accounts', ['id' => $account->id]);
    });

    it('rejects deleting an account referenced by a transaction', function () {
        $actor = adminUser();
        $expenseAccount = ChartOfAccount::factory()->expense()->create();
        IncomeExpenseTransaction::factory()->create(['income_expense_account_id' => $expenseAccount->id]);

        $this->actingAs($actor, 'sanctum')
            ->deleteJson("/api/ledger-accounts/{$expenseAccount->id}")
            ->assertStatus(422);

        $this->assertDatabaseHas('chart_of_accounts', ['id' => $expenseAccount->id, 'deleted_at' => null]);
    });

    it('rejects deleting an account that has child accounts', function () {
        $actor = adminUser();
        $parent = ChartOfAccount::factory()->asset()->group()->create();
        ChartOfAccount::factory()->asset()->create(['parent_id' => $parent->id]);

        $this->actingAs($actor, 'sanctum')
            ->deleteJson("/api/ledger-accounts/{$parent->id}")
            ->assertStatus(422);
    });

    it('deletes an account whose only entry is its opening balance and reverses that entry', function () {
        $this->seed(ChartOfAccountSeeder::class);
        $actor = adminUser();

        $id = $this->actingAs($actor, 'sanctum')
            ->postJson('/api/ledger-accounts', ['name' => 'Advance Received from Customer', 'account_type_id' => AccountType::LIABILITY, 'is_transaction' => true, 'opening_balance' => 20000])
            ->json('data.id');
        approvePending();

        $this->actingAs($actor, 'sanctum')
            ->deleteJson("/api/ledger-accounts/{$id}")
            ->assertOk();

        $account = ChartOfAccount::withTrashed()->find($id);
        $entries = AccountTransaction::query()->whereMorphedTo('source', $account)->get();

        $this->assertSoftDeleted('chart_of_accounts', ['id' => $id]);
        expect($entries)->toHaveCount(2)
            ->and($entries->last()->reversal_of_id)->toBe($entries->first()->id)
            ->and($account->balance)->toBe(0.0)
            ->and(ChartOfAccount::where('account_number', '3000')->first()->balance)->toBe(0.0);
    });

    it('rejects deleting an account with journal lines beyond its opening balance', function () {
        $this->seed(ChartOfAccountSeeder::class);
        $actor = adminUser();
        $account = ChartOfAccount::factory()->liability()->create();
        $equity = ChartOfAccount::where('account_number', '3000')->first();

        app(LedgerPostingService::class)->post(null, ['date' => today()->toDateString()], [
            ['account_id' => $equity->id, 'debit' => 100],
            ['account_id' => $account->id, 'credit' => 100],
        ]);

        $this->actingAs($actor, 'sanctum')
            ->deleteJson("/api/ledger-accounts/{$account->id}")
            ->assertStatus(422);
    });

    it('rejects deleting a predefined account', function () {
        $actor = adminUser();
        $account = ChartOfAccount::factory()->predefined()->create();

        $this->actingAs($actor, 'sanctum')
            ->deleteJson("/api/ledger-accounts/{$account->id}")
            ->assertStatus(422);
    });
});

describe('seeder', function () {
    it('seeds the predefined chart idempotently with a parent/child hierarchy', function () {
        $this->seed(ChartOfAccountSeeder::class);
        $this->seed(ChartOfAccountSeeder::class);

        $cash = ChartOfAccount::where('account_number', '1010')->first();

        expect(ChartOfAccount::count())->toBe(31)
            ->and($cash->parent->account_number)->toBe('1000')
            ->and($cash->sibling_level)->toBe(1)
            ->and($cash->is_transaction)->toBeTrue()
            ->and($cash->parent->is_transaction)->toBeFalse();
    });
});
