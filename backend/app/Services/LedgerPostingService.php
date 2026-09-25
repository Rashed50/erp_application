<?php

namespace App\Services;

use App\Models\ChartOfAccount;

/**
 * Posts debits and credits to chart of account balances, and resolves the
 * predefined accounts (as seeded by ChartOfAccountSeeder) that automatic
 * postings use.
 */
class LedgerPostingService
{
    public const ACCOUNTS_RECEIVABLE_NUMBER = '1030';

    public const ACCOUNTS_PAYABLE_NUMBER = '2010';

    public const SALES_REVENUE_NUMBER = '4010';

    public const PURCHASE_NUMBER = '5010';

    public const BANK_CHARGES_NUMBER = '5090';

    public function predefinedAccount(string $accountNumber): ?ChartOfAccount
    {
        return ChartOfAccount::query()->where('account_number', $accountNumber)->first();
    }

    public function debit(ChartOfAccount $account, float $amount): void
    {
        $this->move($account, debit: true, amount: $amount);
    }

    public function credit(ChartOfAccount $account, float $amount): void
    {
        $this->move($account, debit: false, amount: $amount);
    }

    /**
     * A debit raises a debit-normal account (asset, expense) and lowers a
     * credit-normal one; a credit does the opposite.
     */
    private function move(ChartOfAccount $account, bool $debit, float $amount): void
    {
        $raises = $debit === $account->accountType->increasesOnDebit();

        $account->increment('balance', $raises ? $amount : -$amount);
    }
}
