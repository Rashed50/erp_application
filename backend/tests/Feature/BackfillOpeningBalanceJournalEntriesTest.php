<?php

use App\Models\AccountTransaction;
use App\Models\ChartOfAccount;
use App\Models\Supplier;
use App\Services\AccountReportService;
use App\Services\LedgerPostingService;
use Database\Seeders\ChartOfAccountSeeder;

beforeEach(function () {
    $this->seed(ChartOfAccountSeeder::class);

    $this->migration = require database_path('migrations/2026_11_15_160142_backfill_opening_balance_journal_entries.php');
    $this->payable = ChartOfAccount::query()->where('account_number', LedgerPostingService::ACCOUNTS_PAYABLE_NUMBER)->first();
    $this->equity = ChartOfAccount::query()->where('account_number', '3000')->first();
});

it('reverses the opening entry of a supplier deleted before deletion reversed it', function () {
    $supplier = Supplier::factory()->create(['opening_balance' => 3000]);
    $assets = ChartOfAccount::query()->where('account_number', '1000')->first();
    app(LedgerPostingService::class)->post($supplier, ['date' => '2026-01-01'], [
        ['account_id' => $assets->id, 'debit' => 3000],
        ['account_id' => $this->payable->id, 'credit' => 3000],
    ]);
    $supplier->delete();

    $this->migration->up();
    $this->migration->up();

    expect(AccountTransaction::query()->whereMorphedTo('source', $supplier)->count())->toBe(2)
        ->and($this->payable->balance)->toBe(0.0)
        ->and($assets->balance)->toBe(0.0);
});

it('posts the missing opening entry of a liability account, once', function () {
    $account = ChartOfAccount::factory()->liability()->create(['opening_balance' => 20000, 'opening_date' => '2026-01-01']);

    $this->migration->up();
    $this->migration->up();

    $entry = AccountTransaction::query()->whereMorphedTo('source', $account)->with('details')->sole();

    expect($entry->date->toDateString())->toBe('2026-01-01')
        ->and((float) $entry->details->firstWhere('chart_of_account_id', $this->equity->id)->debit)->toBe(20000.0)
        ->and($account->balance)->toBe(20000.0)
        ->and(app(AccountReportService::class)->balanceSheet()['is_balanced'])->toBeTrue();
});
