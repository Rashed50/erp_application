<?php

use App\Models\ChartOfAccount;
use App\Models\Customer;
use App\Models\User;
use Database\Seeders\ChartOfAccountSeeder;

beforeEach(function () {
    $this->seed(ChartOfAccountSeeder::class);

    $this->cash = ChartOfAccount::query()->where('account_number', '1010')->first();
    $this->bank = ChartOfAccount::query()->where('account_number', '1020')->first();
    $this->rent = ChartOfAccount::query()->where('account_number', '5030')->first();
    $this->revenue = ChartOfAccount::query()->where('account_number', '4010')->first();
    $this->actor = adminUser();
});

/**
 * Posts an income or expense entry through the API.
 */
function postIncomeExpense(User $actor, string $type, ChartOfAccount $account, ChartOfAccount $paymentAccount, float $amount, string $date): void
{
    test()->actingAs($actor, 'sanctum')->postJson('/api/income-expenses', [
        'type' => $type,
        'income_expense_account_id' => $account->id,
        'payment_account_id' => $paymentAccount->id,
        'amount' => $amount,
        'transaction_date' => $date,
    ])->assertStatus(201);
}

describe('access', function () {
    it('requires the account-reports.view permission', function () {
        $this->actingAs(User::factory()->create(), 'sanctum')
            ->getJson('/api/accounting/reports/balance-sheet')
            ->assertStatus(403);
    });
});

describe('general ledger', function () {
    it('brings the balance forward and keeps a running Dr/Cr balance', function () {
        postIncomeExpense($this->actor, 'income', $this->revenue, $this->cash, 1000, '2026-01-10');
        postIncomeExpense($this->actor, 'expense', $this->rent, $this->cash, 300, '2026-02-05');
        postIncomeExpense($this->actor, 'expense', $this->rent, $this->cash, 900, '2026-02-20');

        $this->actingAs($this->actor, 'sanctum')
            ->getJson("/api/accounting/reports/general-ledger?account_id={$this->cash->id}&from_date=2026-02-01&to_date=2026-02-28")
            ->assertOk()
            ->assertJsonPath('data.opening_balance', 1000)
            ->assertJsonPath('data.opening_balance_type', 'Dr')
            ->assertJsonCount(2, 'data.transactions')
            ->assertJsonPath('data.transactions.0.date', '2026-02-05')
            ->assertJsonPath('data.transactions.0.credit', 300)
            ->assertJsonPath('data.transactions.0.balance', 700)
            ->assertJsonPath('data.transactions.0.balance_type', 'Dr')
            ->assertJsonPath('data.transactions.1.balance', 200)
            ->assertJsonPath('data.transactions.1.balance_type', 'Cr')
            ->assertJsonPath('data.total_credit', 1200)
            ->assertJsonPath('data.closing_balance', 200)
            ->assertJsonPath('data.closing_balance_type', 'Cr');
    });

    it('validates the account and dates', function () {
        $this->actingAs($this->actor, 'sanctum')
            ->getJson('/api/accounting/reports/general-ledger?account_id=999999&from_date=2026-02-10&to_date=2026-02-01')
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['account_id', 'to_date']]);
    });
});

describe('trial balance', function () {
    it('balances as of a date', function () {
        postIncomeExpense($this->actor, 'income', $this->revenue, $this->cash, 1000, '2026-01-10');
        postIncomeExpense($this->actor, 'expense', $this->rent, $this->bank, 400, '2026-01-15');

        $response = $this->actingAs($this->actor, 'sanctum')
            ->getJson('/api/accounting/reports/trial-balance?from_date=2026-01-01&to_date=2026-01-31&report_type=as_of_date')
            ->assertOk()
            ->assertJsonPath('data.total_debit', 1400)
            ->assertJsonPath('data.total_credit', 1400);

        $rows = collect($response->json('data.accounts'))->keyBy('account_no');

        expect($rows['1010']['debit'])->toEqual(1000)
            ->and($rows['1020']['credit'])->toEqual(400)
            ->and($rows['4010']['credit'])->toEqual(1000)
            ->and($rows['5030']['debit'])->toEqual(400);
    });

    it('splits a period into opening, movement and closing balances', function () {
        postIncomeExpense($this->actor, 'income', $this->revenue, $this->cash, 1000, '2026-01-10');
        postIncomeExpense($this->actor, 'expense', $this->rent, $this->cash, 250, '2026-02-10');

        $response = $this->actingAs($this->actor, 'sanctum')
            ->getJson('/api/accounting/reports/trial-balance?from_date=2026-02-01&to_date=2026-02-28&report_type=as_of_period')
            ->assertOk();

        $cash = collect($response->json('data.accounts'))->firstWhere('account_no', '1010');

        expect($cash['opening_debit'])->toEqual(1000)
            ->and($cash['credit'])->toEqual(250)
            ->and($cash['closing_debit'])->toEqual(750);
    });
});

describe('profit and loss', function () {
    it('nets revenue against expenses', function () {
        postIncomeExpense($this->actor, 'income', $this->revenue, $this->cash, 1000, '2026-01-10');
        postIncomeExpense($this->actor, 'expense', $this->rent, $this->cash, 400, '2026-01-15');

        $this->actingAs($this->actor, 'sanctum')
            ->getJson('/api/accounting/reports/profit-loss')
            ->assertOk()
            ->assertJsonPath('data.total_revenue', 1000)
            ->assertJsonPath('data.total_expense', 400)
            ->assertJsonPath('data.profit_or_loss', 600)
            ->assertJsonPath('data.result', 'Profit: 600.00');
    });
});

describe('balance sheet', function () {
    it('balances assets against liabilities and equity', function () {
        $this->actingAs($this->actor, 'sanctum')->postJson('/api/customers', [
            'name' => 'Opening Customer',
            'opening_balance' => 500,
        ])->assertStatus(201);

        $this->actingAs($this->actor, 'sanctum')
            ->getJson('/api/accounting/reports/balance-sheet')
            ->assertOk()
            ->assertJsonPath('data.total_assets', 500)
            ->assertJsonPath('data.total_liabilities', 0)
            ->assertJsonPath('data.total_equity', 500)
            ->assertJsonPath('data.is_balanced', true);

        expect(ChartOfAccount::query()->where('account_number', '1030')->first()->balance)->toBe(500.0)
            ->and(Customer::query()->where('name', 'Opening Customer')->value('current_balance'))->toEqual('500.00');
    });
});
