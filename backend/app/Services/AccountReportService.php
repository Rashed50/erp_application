<?php

namespace App\Services;

use App\Models\AccountTransactionDetail;
use App\Models\AccountType;
use App\Models\ChartOfAccount;
use App\Models\Customer;
use App\Models\CustomerTransaction;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Supplier;
use App\Models\SupplierTransaction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * The payroll_software Account module's reports: the ledger reports (general
 * ledger, trial balance, profit & loss, balance sheet, cash transactions),
 * computed from the journal lines dated by their entry's date, and the
 * supplier, customer, expense and sales/purchase reports built from the
 * sub-ledgers and documents.
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
     * The payroll module's Supplier Report: selected suppliers' (all when
     * none are selected) ledger entries in a date range, with the balance
     * brought forward and a running due. A credit (purchase) raises what is
     * owed, a debit (payment) lowers it. Reversed entries are left out.
     *
     * @param  array<int, int>  $supplierIds
     * @return array{from_date: ?string, to_date: ?string, previous_balance: float, rows: array<int, array<string, mixed>>, total_amount: float, total_paid: float, total_due: float}
     */
    public function supplierStatement(array $supplierIds, ?string $fromDate, ?string $toDate): array
    {
        $opening = (float) Supplier::query()
            ->when($supplierIds, fn ($query) => $query->whereIn('id', $supplierIds))
            ->sum('opening_balance');

        return $this->partyStatement(
            SupplierTransaction::query()->with('supplier:id,name')->when($supplierIds, fn ($query) => $query->whereIn('supplier_id', $supplierIds)),
            $opening,
            'credit',
            fn (SupplierTransaction $row) => $row->supplier?->name,
            $fromDate,
            $toDate,
        );
    }

    /**
     * The payroll module's supplier outstanding balance report: every active
     * supplier's current balance.
     *
     * @return array{rows: array<int, array<string, mixed>>, total_opening_balance: float, total_current_balance: float}
     */
    public function supplierBalances(): array
    {
        $rows = Supplier::query()
            ->where('active_status', true)
            ->orderBy('name')
            ->get(['id', 'name', 'phone', 'email', 'contact_person', 'opening_balance', 'current_balance'])
            ->map(fn (Supplier $supplier) => [
                'id' => $supplier->id,
                'name' => $supplier->name,
                'phone' => $supplier->phone,
                'email' => $supplier->email,
                'contact_person' => $supplier->contact_person,
                'opening_balance' => (float) $supplier->opening_balance,
                'current_balance' => (float) $supplier->current_balance,
            ]);

        return [
            'rows' => $rows->all(),
            'total_opening_balance' => round($rows->sum('opening_balance'), 2),
            'total_current_balance' => round($rows->sum('current_balance'), 2),
        ];
    }

    /**
     * The payroll module's Sales Report (customer statement): selected
     * customers' (all when none are selected) ledger entries in a date range,
     * with the balance brought forward and a running due. A debit (invoice)
     * raises what the customer owes, a credit (payment) lowers it.
     *
     * @param  array<int, int>  $customerIds
     * @return array{from_date: ?string, to_date: ?string, previous_balance: float, rows: array<int, array<string, mixed>>, total_amount: float, total_paid: float, total_due: float}
     */
    public function customerStatement(array $customerIds, ?string $fromDate, ?string $toDate): array
    {
        $opening = (float) Customer::query()
            ->when($customerIds, fn ($query) => $query->whereIn('id', $customerIds))
            ->sum('opening_balance');

        return $this->partyStatement(
            CustomerTransaction::query()->with('customer:id,name')->when($customerIds, fn ($query) => $query->whereIn('customer_id', $customerIds)),
            $opening,
            'debit',
            fn (CustomerTransaction $row) => $row->customer?->name,
            $fromDate,
            $toDate,
        );
    }

    /**
     * The payroll module's Cash Transaction report: journal lines of the
     * selected accounts (all when none are selected) in a date range.
     *
     * @param  array<int, int>  $accountIds
     * @return array{from_date: ?string, to_date: ?string, rows: array<int, array<string, mixed>>, total_debit: float, total_credit: float}
     */
    public function cashTransactions(array $accountIds, ?string $fromDate, ?string $toDate): array
    {
        $rows = $this->lines()
            ->join('chart_of_accounts', 'chart_of_accounts.id', '=', 'account_transaction_details.chart_of_account_id')
            ->when($accountIds, fn (Builder $query) => $query->whereIn('account_transaction_details.chart_of_account_id', $accountIds))
            ->when($fromDate, fn (Builder $query) => $query->where('account_transactions.date', '>=', $fromDate))
            ->when($toDate, fn (Builder $query) => $query->where('account_transactions.date', '<=', $toDate))
            ->orderBy('account_transactions.date')
            ->orderBy('account_transaction_details.id')
            ->get([
                'account_transaction_details.id',
                'account_transactions.date',
                'account_transactions.tr_no',
                'chart_of_accounts.name as ledger_name',
                'chart_of_accounts.account_number',
                'account_transaction_details.particular',
                'account_transaction_details.debit',
                'account_transaction_details.credit',
            ])
            ->map(fn (AccountTransactionDetail $line) => [
                'id' => $line->id,
                'date' => substr((string) $line->getRawOriginal('date'), 0, 10),
                'tr_no' => $line->tr_no,
                'ledger_name' => $line->ledger_name,
                'account_number' => $line->account_number,
                'particular' => $line->particular,
                'debit' => (float) $line->debit,
                'credit' => (float) $line->credit,
            ]);

        return [
            'from_date' => $fromDate,
            'to_date' => $toDate,
            'rows' => $rows->all(),
            'total_debit' => round($rows->sum('debit'), 2),
            'total_credit' => round($rows->sum('credit'), 2),
        ];
    }

    /**
     * The payroll module's Expense Details report: purchases (bills) whose
     * credit account is one of the selected accounts (all when none are
     * selected) in a purchase-date range, with VAT and net totals.
     *
     * @param  array<int, int>  $accountIds
     * @return array{from_date: ?string, to_date: ?string, rows: array<int, array<string, mixed>>, total_vat: float, total_net_amount: float}
     */
    public function expenseDetails(array $accountIds, ?string $fromDate, ?string $toDate): array
    {
        $rows = Purchase::query()
            ->with(['supplier:id,name', 'creditAccount:id,name', 'debitAccount:id,name'])
            ->when($accountIds, fn ($query) => $query->whereIn('credit_account_id', $accountIds))
            ->when($fromDate, fn ($query) => $query->whereDate('purchase_date', '>=', $fromDate))
            ->when($toDate, fn ($query) => $query->whereDate('purchase_date', '<=', $toDate))
            ->orderBy('purchase_date')
            ->orderBy('id')
            ->get()
            ->map(fn (Purchase $purchase) => [
                'id' => $purchase->id,
                'ledger_name' => $purchase->creditAccount?->name,
                'debit_account_name' => $purchase->debitAccount?->name,
                'supplier_name' => $purchase->supplier?->name,
                'purchase_type' => $purchase->purchase_type,
                'invoice_number' => $purchase->invoice_number,
                'purchase_date' => $purchase->purchase_date?->toDateString(),
                'inserted_date' => $purchase->created_at?->toDateString(),
                'notes' => $purchase->notes,
                'vat_amount' => (float) $purchase->vat_amount,
                'net_total' => (float) $purchase->net_total,
            ]);

        return [
            'from_date' => $fromDate,
            'to_date' => $toDate,
            'rows' => $rows->all(),
            'total_vat' => round($rows->sum('vat_amount'), 2),
            'total_net_amount' => round($rows->sum('net_total'), 2),
        ];
    }

    /**
     * The payroll module's Sales & Purchase Summary: sales (credit, by issue
     * date) and purchases (debit, by purchase date) in one date-ordered list
     * with a running balance of sales less purchases.
     *
     * @return array{from_date: string, to_date: string, rows: array<int, array<string, mixed>>, total_sales: float, total_purchase: float, balance: float}
     */
    public function salesPurchaseSummary(string $fromDate, string $toDate): array
    {
        $sales = Sale::query()
            ->whereBetween('issue_date', [$fromDate, $toDate])
            ->get(['id', 'invoice_number', 'issue_date', 'description', 'net_total'])
            ->map(fn (Sale $sale) => [
                'id' => $sale->id,
                'date' => $sale->issue_date?->toDateString(),
                'type' => 'Sale',
                'reference' => $sale->invoice_number,
                'description' => $sale->description,
                'debit' => 0.0,
                'credit' => (float) $sale->net_total,
            ]);

        $purchases = Purchase::query()
            ->whereBetween('purchase_date', [$fromDate, $toDate])
            ->get(['id', 'invoice_number', 'purchase_date', 'description', 'net_total'])
            ->map(fn (Purchase $purchase) => [
                'id' => $purchase->id,
                'date' => $purchase->purchase_date?->toDateString(),
                'type' => 'Purchase',
                'reference' => $purchase->invoice_number,
                'description' => $purchase->description,
                'debit' => (float) $purchase->net_total,
                'credit' => 0.0,
            ]);

        $balance = 0.0;
        $rows = $sales->concat($purchases)
            ->sortBy([['date', 'asc'], ['type', 'asc'], ['id', 'asc']])
            ->values()
            ->map(function (array $row) use (&$balance) {
                $balance = round($balance + $row['credit'] - $row['debit'], 2);

                return [...$row, 'balance' => $balance];
            });

        return [
            'from_date' => $fromDate,
            'to_date' => $toDate,
            'rows' => $rows->all(),
            'total_sales' => round($sales->sum('credit'), 2),
            'total_purchase' => round($purchases->sum('debit'), 2),
            'balance' => $balance,
        ];
    }

    /**
     * The payroll module's sales report list: sales in an issue-date range,
     * optionally for one customer, with their amount totals.
     *
     * @return array{from_date: ?string, to_date: ?string, rows: array<int, array<string, mixed>>, totals: array<string, float>}
     */
    public function salesRegister(?int $customerId, ?string $fromDate, ?string $toDate): array
    {
        $rows = Sale::query()
            ->with('customer:id,name')
            ->when($customerId, fn ($query) => $query->where('customer_id', $customerId))
            ->when($fromDate, fn ($query) => $query->whereDate('issue_date', '>=', $fromDate))
            ->when($toDate, fn ($query) => $query->whereDate('issue_date', '<=', $toDate))
            ->orderBy('issue_date')
            ->orderBy('id')
            ->get()
            ->map(fn (Sale $sale) => [
                'id' => $sale->id,
                'invoice_number' => $sale->invoice_number,
                'customer_name' => $sale->customer?->name,
                'issue_date' => $sale->issue_date?->toDateString(),
                'due_date' => $sale->due_date?->toDateString(),
                'total_amount' => (float) $sale->total_amount,
                'discount_amount' => (float) $sale->discount_amount,
                'vat_amount' => (float) $sale->vat_amount,
                'net_total' => (float) $sale->net_total,
                'paid_amount' => (float) $sale->paid_amount,
                'due_amount' => round((float) $sale->net_total - (float) $sale->paid_amount, 2),
            ]);

        $totals = [];
        foreach (['total_amount', 'discount_amount', 'vat_amount', 'net_total', 'paid_amount', 'due_amount'] as $column) {
            $totals[$column] = round($rows->sum($column), 2);
        }

        return [
            'from_date' => $fromDate,
            'to_date' => $toDate,
            'rows' => $rows->all(),
            'totals' => $totals,
        ];
    }

    /**
     * A supplier or customer ledger statement. `$increasingSide` is the
     * column (debit or credit) that raises the balance owed; the other one
     * is shown as paid.
     *
     * @param  Builder<SupplierTransaction>|Builder<CustomerTransaction>  $transactions
     * @param  callable(SupplierTransaction|CustomerTransaction): ?string  $partyName
     * @return array{from_date: ?string, to_date: ?string, previous_balance: float, rows: array<int, array<string, mixed>>, total_amount: float, total_paid: float, total_due: float}
     */
    private function partyStatement(Builder $transactions, float $opening, string $increasingSide, callable $partyName, ?string $fromDate, ?string $toDate): array
    {
        $decreasingSide = $increasingSide === 'debit' ? 'credit' : 'debit';
        $transactions->where('status', true);

        $broughtForward = $fromDate
            ? (clone $transactions)->whereDate('transaction_date', '<', $fromDate)
                ->selectRaw("COALESCE(SUM({$increasingSide}), 0) - COALESCE(SUM({$decreasingSide}), 0) as net")
                ->value('net')
            : 0;

        $previousBalance = round($opening + (float) $broughtForward, 2);
        $due = $previousBalance;

        $rows = $transactions
            ->when($fromDate, fn ($query) => $query->whereDate('transaction_date', '>=', $fromDate))
            ->when($toDate, fn ($query) => $query->whereDate('transaction_date', '<=', $toDate))
            ->orderBy('transaction_date')
            ->orderBy('id')
            ->get()
            ->map(function ($row) use (&$due, $increasingSide, $decreasingSide, $partyName) {
                $amount = (float) $row->{$increasingSide};
                $paid = (float) $row->{$decreasingSide};
                $due = round($due + $amount - $paid, 2);

                return [
                    'id' => $row->id,
                    'party_name' => $partyName($row),
                    'invoice_no' => $row->invoice_no,
                    'date' => $row->transaction_date?->toDateString(),
                    'transaction_type' => $row->transaction_type,
                    'notes' => $row->notes,
                    'amount' => $amount,
                    'paid' => $paid,
                    'due' => $due,
                ];
            });

        return [
            'from_date' => $fromDate,
            'to_date' => $toDate,
            'previous_balance' => $previousBalance,
            'rows' => $rows->all(),
            'total_amount' => round($rows->sum('amount'), 2),
            'total_paid' => round($rows->sum('paid'), 2),
            'total_due' => $due,
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
