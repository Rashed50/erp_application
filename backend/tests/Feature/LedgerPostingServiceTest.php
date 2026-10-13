<?php

use App\Models\AccountTransaction;
use App\Models\ChartOfAccount;
use App\Models\Supplier;
use App\Services\LedgerPostingService;
use App\Services\SupplierService;
use Database\Seeders\ChartOfAccountSeeder;

beforeEach(function () {
    $this->seed(ChartOfAccountSeeder::class);

    $this->cash = ChartOfAccount::query()->where('account_number', '1010')->first();
    $this->bank = ChartOfAccount::query()->where('account_number', '1020')->first();
    $this->ledger = app(LedgerPostingService::class);
});

it('records a header with its debit and credit lines', function () {
    $entry = $this->ledger->post(null, ['tr_no' => 'TR-1', 'date' => '2026-03-01', 'general_particular' => 'Deposit'], [
        ['account_id' => $this->bank->id, 'debit' => 250],
        ['account_id' => $this->cash->id, 'credit' => 250],
    ]);

    expect((float) $entry->total_amount)->toBe(250.0)
        ->and($entry->details)->toHaveCount(2)
        ->and($this->bank->balance)->toBe(250.0)
        ->and($this->cash->balance)->toBe(-250.0);
});

it('refuses an entry whose debits and credits differ', function () {
    $this->ledger->post(null, ['date' => '2026-03-01'], [
        ['account_id' => $this->bank->id, 'debit' => 250],
        ['account_id' => $this->cash->id, 'credit' => 200],
    ]);
})->throws(LogicException::class);

it('skips zero-amount lines', function () {
    $entry = $this->ledger->post(null, ['date' => '2026-03-01'], [
        ['account_id' => $this->bank->id, 'debit' => 100],
        ['account_id' => $this->cash->id, 'credit' => 100],
        ['account_id' => $this->cash->id, 'debit' => 0],
    ]);

    expect($entry->details)->toHaveCount(2);
});

it('removes every entry a record posted', function () {
    $supplier = Supplier::factory()->create();
    $lines = [['account_id' => $this->bank->id, 'debit' => 10], ['account_id' => $this->cash->id, 'credit' => 10]];
    $this->ledger->post($supplier, ['date' => '2026-03-01'], $lines);
    $this->ledger->post($supplier, ['date' => '2026-03-02'], $lines);

    $this->ledger->reverse($supplier);

    expect(AccountTransaction::count())->toBe(0)
        ->and($this->bank->balance)->toBe(0.0);
});

it('posts a supplier opening balance against the Assets group account', function () {
    $supplier = app(SupplierService::class)->create(['name' => 'Opening Supplier', 'opening_balance' => 700]);

    $entry = $this->ledger->entryFor($supplier);
    $assets = ChartOfAccount::query()->where('account_number', '1000')->first();
    $payable = ChartOfAccount::query()->where('account_number', '2010')->first();

    expect($entry->general_particular)->toBe('initialize supplier account')
        ->and($assets->balance)->toBe(700.0)
        ->and($payable->balance)->toBe(700.0);
});
