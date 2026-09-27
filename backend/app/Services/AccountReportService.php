<?php

namespace App\Services;

use App\Models\AccountTransactionDetail;
use App\Models\AccountType;
use App\Models\ChartOfAccount;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * The payroll_software Account module's ledger reports (general ledger,
 * trial balance, profit & loss, balance sheet), computed from the journal
 * lines. Lines are dated by their journal entry's date.
 */
class AccountReportService
{
    /**
     * One account's lines between two dates, with the balance brought
     * forward and a running balance (debit minus credit, labelled Dr/Cr).
     *
     * @return array{account: array{id: int, name: string, account_number: ?string}, from_date: string, to_date: string, opening_balance: float, opening_balance_type: string, transactions: array<int, array<string, mixed>>, total_debit: float, total_credit: float, closing_balance: float, closing_balance_type: string}
     */
    public function generalLedger(ChartOfAccount $account, string $fromDate, string $toDate): array
    {
        $opening = $this->lines()
            ->where('account_transaction_details.chart_of_account_id', $account->id)
            ->where('account_transactions.date', '<', $fromDate)
            ->selectRaw('COALESCE(SUM(account_transaction_details.debit), 0) - COALESCE(SUM(account_transaction_details.credit), 0) as net')
            ->value('net');

        $balance = round((float) $opening, 2);
        $totalDebit = 0.0;
        $totalCredit = 0.0;

        $transactions = $this->lines()
            ->where('account_transaction_details.chart_of_account_id', $account->id)
            ->whereBetween('account_transactions.date', [$fromDate, $toDate])
            ->orderBy('account_transactions.date')
            ->orderBy('account_transaction_details.id')
            ->get([
                'account_transaction_details.id',
                'account_transaction_details.account_transaction_id',
                'account_transactions.date',
                'account_transactions.tr_no',
                'account_transactions.general_particular',
                'account_transaction_details.particular',
                'account_transaction_details.debit',
                'account_transaction_details.credit',
            ])
            ->map(function (AccountTransactionDetail $line) use (&$balance, &$totalDebit, &$totalCredit) {
                $debit = (float) $line->debit;
                $credit = (float) $line->credit;
                $balance = round($balance + $debit - $credit, 2);
                $totalDebit += $debit;
                $totalCredit += $credit;

                return [
                    'id' => $line->id,
                    'account_transaction_id' => $line->account_transaction_id,
                    'date' => substr((string) $line->getRawOriginal('date'), 0, 10),
                    'tr_no' => $line->tr_no,
                    'general_particular' => $line->general_particular,
                    'particular' => $line->particular,
                    'debit' => $debit,
                    'credit' => $credit,
                    'balance' => abs($balance),
                    'balance_type' => $this->side($balance),
                ];
            })
            ->all();

        return [
            'account' => ['id' => $account->id, 'name' => $account->name, 'account_number' => $account->account_number],
            'from_date' => $fromDate,
            'to_date' => $toDate,
            'opening_balance' => abs((float) $opening),
            'opening_balance_type' => $this->side((float) $opening),
            'transactions' => $transactions,
            'total_debit' => round($totalDebit, 2),
            'total_credit' => round($totalCredit, 2),
            'closing_balance' => abs($balance),
            'closing_balance_type' => $this->side($balance),
        ];
    }

    /**
     * Every open account's balance on its debit or credit side. "as_of_date"
     * sums everything up to `to_date`; "as_of_period" also splits out the
     * opening balance before `from_date` and the movement within the period.
     * As in the payroll module, the totals are of the balances shown in the
     * debit/credit columns (the period columns for "as_of_period").
     *
     * @return array{report_type: string, from_date: string, to_date: string, accounts: array<int, array<string, mixed>>, total_debit: float, total_credit: float}
     */
    public function trialBalance(string $fromDate, string $toDate, string $reportType): array
    {
        $accounts = ChartOfAccount::query()->where('is_closed', false)->orderBy('account_number')->orderBy('id')->get();

        $closing = $this->netByAccount(fn (Builder $query) => $query->where('account_transactions.date', '<=', $toDate));
        $opening = $reportType === 'as_of_period'
            ? $this->netByAccount(fn (Builder $query) => $query->where('account_transactions.date', '<', $fromDate))
            : collect();
        $period = $reportType === 'as_of_period'
            ? $this->netByAccount(fn (Builder $query) => $query->whereBetween('account_transactions.date', [$fromDate, $toDate]))
            : $closing;

        $rows = $accounts->map(function (ChartOfAccount $account) use ($reportType, $opening, $period, $closing) {
            $row = [
                'account_id' => $account->id,
                'account_no' => $account->account_number,
                'account_name' => $account->name,
                ...$this->columns((float) ($period[$account->id] ?? 0)),
            ];

            if ($reportType === 'as_of_period') {
                $row = [
                    ...$row,
                    ...$this->columns((float) ($opening[$account->id] ?? 0), 'opening_'),
                    ...$this->columns((float) ($closing[$account->id] ?? 0), 'closing_'),
                ];
            }

            return $row;
        });

        return [
            'report_type' => $reportType,
            'from_date' => $fromDate,
            'to_date' => $toDate,
            'accounts' => $rows->values()->all(),
            'total_debit' => round($rows->sum('debit'), 2),
            'total_credit' => round($rows->sum('credit'), 2),
        ];
    }

    /**
     * Revenue (credit minus debit) less expenses (debit minus credit).
     *
     * @return array{total_revenue: float, total_expense: float, profit_or_loss: float, result: string}
     */
    public function profitAndLoss(?string $fromDate = null, ?string $toDate = null): array
    {
        $revenue = -$this->netOfType(AccountType::REVENUE, $fromDate, $toDate);
        $expense = $this->netOfType(AccountType::EXPENSE, $fromDate, $toDate);
        $profitOrLoss = round($revenue - $expense, 2);

        $result = match (true) {
            $profitOrLoss > 0 => 'Profit: '.number_format($profitOrLoss, 2),
            $profitOrLoss < 0 => 'Loss: '.number_format(abs($profitOrLoss), 2),
            default => 'Break-Even: No profit, no loss.',
        };

        return [
            'total_revenue' => $revenue,
            'total_expense' => $expense,
            'profit_or_loss' => $profitOrLoss,
            'result' => $result,
        ];
    }

    /**
     * Assets (debit minus credit) against liabilities and owner's equity
     * (credit minus debit), as the payroll module's balance sheet sums them.
     *
     * @return array{total_assets: float, total_liabilities: float, total_equity: float, is_balanced: bool}
     */
    public function balanceSheet(?string $toDate = null): array
    {
        $assets = $this->netOfType(AccountType::ASSET, null, $toDate);
        $liabilities = -$this->netOfType(AccountType::LIABILITY, null, $toDate);
        $equity = -$this->netOfType(AccountType::OWNER_EQUITY, null, $toDate);

        return [
            'total_assets' => $assets,
            'total_liabilities' => $liabilities,
            'total_equity' => $equity,
            'is_balanced' => round($assets, 2) === round($liabilities + $equity, 2),
        ];
    }

    /**
     * Journal lines joined to their entry, so they can be filtered by date.
     */
    private function lines(): Builder
    {
        return AccountTransactionDetail::query()
            ->join('account_transactions', 'account_transactions.id', '=', 'account_transaction_details.account_transaction_id');
    }

    /**
     * Debit minus credit per account for the lines matching `$filter`.
     *
     * @param  callable(Builder): Builder  $filter
     * @return Collection<int, float>
     */
    private function netByAccount(callable $filter): Collection
    {
        return $filter($this->lines())
            ->groupBy('account_transaction_details.chart_of_account_id')
            ->selectRaw('account_transaction_details.chart_of_account_id as account_id, SUM(account_transaction_details.debit) - SUM(account_transaction_details.credit) as net')
            ->pluck('net', 'account_id')
            ->map(fn ($net) => round((float) $net, 2));
    }

    /**
     * Debit minus credit over every account of a type.
     */
    private function netOfType(int $accountTypeId, ?string $fromDate, ?string $toDate): float
    {
        $net = $this->lines()
            ->join('chart_of_accounts', 'chart_of_accounts.id', '=', 'account_transaction_details.chart_of_account_id')
            ->where('chart_of_accounts.account_type_id', $accountTypeId)
            ->when($fromDate, fn (Builder $query) => $query->where('account_transactions.date', '>=', $fromDate))
            ->when($toDate, fn (Builder $query) => $query->where('account_transactions.date', '<=', $toDate))
            ->selectRaw('COALESCE(SUM(account_transaction_details.debit), 0) - COALESCE(SUM(account_transaction_details.credit), 0) as net')
            ->value('net');

        return round((float) $net, 2);
    }

    /**
     * Split a net (debit minus credit) balance into debit and credit columns.
     *
     * @return array<string, float>
     */
    private function columns(float $net, string $prefix = ''): array
    {
        return [
            "{$prefix}debit" => max($net, 0),
            "{$prefix}credit" => $net < 0 ? abs($net) : 0.0,
        ];
    }

    private function side(float $balance): string
    {
        return $balance > 0 ? 'Dr' : 'Cr';
    }
}
