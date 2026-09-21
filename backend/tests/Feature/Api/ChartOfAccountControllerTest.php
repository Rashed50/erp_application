<?php

use App\Models\AccountType;
use App\Models\ChartOfAccount;
use App\Models\IncomeExpenseTransaction;
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
        ChartOfAccount::factory()->asset()->create(['parent_id' => $group->id, 'sibling_level' => 1]);
        ChartOfAccount::factory()->asset()->create();

        $this->actingAs($actor, 'sanctum')->getJson("/api/ledger-accounts?parent_id={$group->id}")
            ->assertOk()->assertJsonCount(1, 'data.accounts');

        $this->actingAs($actor, 'sanctum')->getJson('/api/ledger-accounts?is_transaction=0')
            ->assertOk()->assertJsonCount(1, 'data.accounts');
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

        $response->assertStatus(201)
            ->assertJsonPath('data.account_type_id', AccountType::EXPENSE)
            ->assertJsonPath('data.sibling_level', 1)
            ->assertJsonPath('data.parent_name', $group->name);
    });

    it('rejects a child under a transaction account', function () {
        $actor = adminUser();
        $leaf = ChartOfAccount::factory()->asset()->create();

        $this->actingAs($actor, 'sanctum')
            ->postJson('/api/ledger-accounts', ['name' => 'Sub', 'parent_id' => $leaf->id])
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['parent_id']]);
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

describe('update', function () {
    it('does not allow changing the account type', function () {
        $actor = adminUser();
        $account = ChartOfAccount::factory()->asset()->create();

        $this->actingAs($actor, 'sanctum')
            ->putJson("/api/ledger-accounts/{$account->id}", ['account_type_id' => AccountType::EXPENSE])
            ->assertOk();

        $this->assertDatabaseHas('chart_of_accounts', ['id' => $account->id, 'account_type_id' => AccountType::ASSET]);
    });

    it('moves an account and re-levels its subtree', function () {
        $actor = adminUser();
        $root = ChartOfAccount::factory()->asset()->group()->create();
        $other = ChartOfAccount::factory()->asset()->group()->create(['parent_id' => $root->id, 'sibling_level' => 1]);
        $moving = ChartOfAccount::factory()->asset()->group()->create();
        $child = ChartOfAccount::factory()->asset()->create(['parent_id' => $moving->id, 'sibling_level' => 1]);

        $this->actingAs($actor, 'sanctum')
            ->putJson("/api/ledger-accounts/{$moving->id}", ['parent_id' => $other->id])
            ->assertOk()
            ->assertJsonPath('data.sibling_level', 2);

        expect($child->fresh()->sibling_level)->toBe(3);
    });

    it('rejects moving an account beneath its own child', function () {
        $actor = adminUser();
        $parent = ChartOfAccount::factory()->asset()->group()->create();
        $child = ChartOfAccount::factory()->asset()->group()->create(['parent_id' => $parent->id, 'sibling_level' => 1]);

        $this->actingAs($actor, 'sanctum')
            ->putJson("/api/ledger-accounts/{$parent->id}", ['parent_id' => $child->id])
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['parent_id']]);
    });

    it('rejects turning an account with children into a transaction account', function () {
        $actor = adminUser();
        $parent = ChartOfAccount::factory()->asset()->group()->create();
        ChartOfAccount::factory()->asset()->create(['parent_id' => $parent->id, 'sibling_level' => 1]);

        $this->actingAs($actor, 'sanctum')
            ->putJson("/api/ledger-accounts/{$parent->id}", ['is_transaction' => true])
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['is_transaction']]);
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
        ChartOfAccount::factory()->asset()->create(['parent_id' => $parent->id, 'sibling_level' => 1]);

        $this->actingAs($actor, 'sanctum')
            ->deleteJson("/api/ledger-accounts/{$parent->id}")
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
