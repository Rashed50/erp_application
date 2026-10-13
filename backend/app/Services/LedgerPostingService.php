<?php

namespace App\Services;

use App\Models\AccountTransaction;
use App\Models\ChartOfAccount;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use LogicException;

/**
 * Posts journal entries to the general ledger the way the payroll_software
 * Account module does: each posting is an `account_transactions` header plus
 * its debit and credit lines in `account_transaction_details`. Account
 * balances are never stored; they are always summed from those lines.
 *
 * Also resolves the predefined accounts (as seeded by ChartOfAccountSeeder)
 * that automatic postings use.
 */
class LedgerPostingService
{
    public const ACCOUNTS_RECEIVABLE_NUMBER = '1030';

    public const INVENTORY_NUMBER = '1040';

    public const ACCOUNTS_PAYABLE_NUMBER = '2010';

    public const CAPITAL_NUMBER = '3010';

    public const SALES_REVENUE_NUMBER = '4010';

    public const PURCHASE_NUMBER = '5010';

    /**
     * The payroll module debits bank charges (and fund transfer VAT) to its
     * purchase expense account, so they land in Purchase here too.
     */
    public const BANK_CHARGES_NUMBER = self::PURCHASE_NUMBER;

    public function predefinedAccount(string $accountNumber): ?ChartOfAccount
    {
        return ChartOfAccount::query()->where('account_number', $accountNumber)->first();
    }

    /**
     * The top-level group account of a type (e.g. "Assets", "Owner's Equity"),
     * which the payroll module uses as the balancing side of opening balances.
     */
    public function rootAccount(int $accountTypeId): ?ChartOfAccount
    {
        return ChartOfAccount::query()
            ->where('account_type_id', $accountTypeId)
            ->whereNull('parent_id')
            ->orderBy('id')
            ->first();
    }

    /**
     * Record a journal entry. Zero-amount lines are skipped, and the entry is
     * refused unless its debits equal its credits.
     *
     * @param  array{tr_no?: ?string, date: string, general_particular?: ?string, sale_id?: ?int, purchase_id?: ?int}  $header
     * @param  array<int, array{account_id: int, debit?: float, credit?: float, particular?: ?string}>  $lines
     */
    public function post(?Model $source, array $header, array $lines): AccountTransaction
    {
        return DB::transaction(function () use ($source, $header, $lines) {
            $lines = $this->balancedLines($lines);

            $entry = new AccountTransaction([
                ...$header,
                'total_amount' => $this->totalDebit($lines),
                'approved_by' => Auth::id(),
                'created_by' => Auth::id(),
            ]);
            $entry->source()->associate($source);
            $entry->save();

            $entry->details()->createMany($lines);

            return $entry;
        });
    }

    /**
     * Rewrite an existing entry in place, e.g. after the sale it belongs to
     * was edited, so the ledger holds one entry per document rather than a
     * trail of adjustments.
     *
     * @param  array{tr_no?: ?string, date?: string, general_particular?: ?string, sale_id?: ?int, purchase_id?: ?int}  $header
     * @param  array<int, array{account_id: int, debit?: float, credit?: float, particular?: ?string}>  $lines
     */
    public function repost(AccountTransaction $entry, array $header, array $lines): AccountTransaction
    {
        return DB::transaction(function () use ($entry, $header, $lines) {
            $lines = $this->balancedLines($lines);

            $entry->update([...$header, 'total_amount' => $this->totalDebit($lines)]);
            $entry->details()->delete();
            $entry->details()->createMany($lines);

            return $entry;
        });
    }

    /**
     * The journal entry a record posted, if any.
     */
    public function entryFor(Model $source): ?AccountTransaction
    {
        return AccountTransaction::query()->whereMorphedTo('source', $source)->latest('id')->first();
    }

    /**
     * Remove every journal entry a record posted, as the payroll module does
     * when the record itself is deleted.
     */
    public function reverse(Model $source): void
    {
        DB::transaction(function () use ($source) {
            AccountTransaction::query()->whereMorphedTo('source', $source)->each(function (AccountTransaction $entry) {
                $entry->details()->delete();
                $entry->delete();
            });
        });
    }

    /**
     * @param  array<int, array{account_id: int, debit?: float, credit?: float, particular?: ?string}>  $lines
     * @return array<int, array{chart_of_account_id: int, debit: float, credit: float, particular: ?string}>
     */
    private function balancedLines(array $lines): array
    {
        $lines = collect($lines)
            ->map(fn (array $line) => [
                'chart_of_account_id' => $line['account_id'],
                'debit' => round((float) ($line['debit'] ?? 0), 2),
                'credit' => round((float) ($line['credit'] ?? 0), 2),
                'particular' => $line['particular'] ?? null,
            ])
            ->reject(fn (array $line) => $line['debit'] == 0 && $line['credit'] == 0)
            ->values()
            ->all();

        $debit = $this->totalDebit($lines);
        $credit = round(array_sum(array_column($lines, 'credit')), 2);

        if ($lines === [] || $debit !== $credit) {
            throw new LogicException("A journal entry must balance: debits {$debit}, credits {$credit}.");
        }

        return $lines;
    }

    /**
     * @param  array<int, array{debit: float}>  $lines
     */
    private function totalDebit(array $lines): float
    {
        return round(array_sum(array_column($lines, 'debit')), 2);
    }
}
