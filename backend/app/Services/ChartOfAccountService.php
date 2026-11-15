<?php

namespace App\Services;

use App\Models\AccountType;
use App\Models\ChartOfAccount;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChartOfAccountService
{
    public function __construct(private readonly LedgerPostingService $ledgerPostingService) {}

    /**
     * @param  bool|null  $transactionOnly  Restrict to (non-)transaction accounts, or null for all.
     * @param  bool|null  $closed  Restrict to closed or open accounts, or null for all.
     */
    public function paginate(
        int $perPage = 15,
        ?string $search = null,
        ?int $accountTypeId = null,
        ?int $parentId = null,
        ?bool $active = null,
        ?bool $transactionOnly = null,
        ?bool $closed = null,
    ): LengthAwarePaginator {
        return ChartOfAccount::query()
            ->with(['accountType:id,name,normal_balance', 'parent:id,name', 'creator:id,name', 'approver:id,name'])
            ->withLedgerTotals()
            ->when($search, fn ($query) => $query->search($search))
            ->when($accountTypeId, fn ($query) => $query->ofType($accountTypeId))
            ->when($parentId, fn ($query) => $query->where('parent_id', $parentId))
            ->when(! is_null($active), fn ($query) => $query->where('active_status', $active))
            ->when(! is_null($transactionOnly), fn ($query) => $query->where('is_transaction', $transactionOnly))
            ->when(! is_null($closed), fn ($query) => $query->where('is_closed', $closed))
            ->orderBy('account_type_id')
            ->orderBy('account_number')
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function find(ChartOfAccount $account): ChartOfAccount
    {
        return $account->load(['accountType:id,name,normal_balance', 'parent:id,name']);
    }

    /**
     * Create an account. A positive opening balance is posted as a balanced
     * journal entry against the Owner's Equity group account (see
     * postOpeningBalance()).
     *
     * @param  array{name: string, account_number?: ?string, account_type_id?: ?int, parent_id?: ?int, opening_date?: ?string, opening_balance?: ?float, is_transaction?: ?bool, active_status?: ?bool}  $data
     */
    public function create(array $data): ChartOfAccount
    {
        return DB::transaction(function () use ($data) {
            $parent = isset($data['parent_id']) ? ChartOfAccount::find($data['parent_id']) : null;

            $account = ChartOfAccount::create([
                ...$data,
                'account_type_id' => $parent?->account_type_id ?? $data['account_type_id'],
                'opening_date' => $data['opening_date'] ?? today()->toDateString(),
                'opening_balance' => $data['opening_balance'] ?? 0,
                'is_transaction' => $data['is_transaction'] ?? false,
                'active_status' => $data['active_status'] ?? true,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            $this->postOpeningBalance($account);

            return $this->find($account);
        });
    }

    /**
     * The opening balance itself cannot be edited, so editing never posts a
     * second opening entry; a changed opening date moves the existing entry.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(ChartOfAccount $account, array $data): ChartOfAccount
    {
        return DB::transaction(function () use ($account, $data) {
            $account->fill([...$data, 'updated_by' => Auth::id()]);
            $openingDateChanged = $account->isDirty('opening_date');
            $account->save();

            if ($openingDateChanged && $entry = $this->ledgerPostingService->entryFor($account)) {
                $entry->update(['date' => $account->opening_date->toDateString()]);
            }

            return $this->find($account);
        });
    }

    /**
     * Suggests the next child account number under a parent, e.g. `1000.3`
     * for the third child of account `1000`, skipping numbers already taken.
     */
    public function nextChildAccountNumber(ChartOfAccount $parent): string
    {
        $sequence = ChartOfAccount::withTrashed()->where('parent_id', $parent->id)->count() + 1;

        do {
            $accountNumber = $parent->account_number ? "{$parent->account_number}.{$sequence}" : (string) $sequence;
            $sequence++;
        } while (ChartOfAccount::withTrashed()->where('account_number', $accountNumber)->exists());

        return $accountNumber;
    }

    /**
     * An account whose only journal lines are its own opening balance can
     * be deleted; that entry is cancelled by a reversal entry so equity and
     * the trial balance stay correct.
     */
    public function delete(ChartOfAccount $account): void
    {
        DB::transaction(function () use ($account) {
            $this->ledgerPostingService->postReversal($account, 'reverse deleted Ledger account');
            $account->delete();
        });
    }

    /**
     * Post the opening balance on the account's normal side against the
     * Owner's Equity group account, which serves as opening balance equity:
     *
     * - assets and expenses (normal debit): debit the account, credit equity
     * - liabilities, equity and revenue (normal credit): debit equity, credit the account
     *
     * Skipped when the Owner's Equity group account has not been seeded, as
     * the payroll module skips it when the account is missing, or when the
     * new account is that group account itself.
     */
    private function postOpeningBalance(ChartOfAccount $account): void
    {
        $amount = (float) $account->opening_balance;
        $equity = $this->ledgerPostingService->rootAccount(AccountType::OWNER_EQUITY);

        if ($amount <= 0 || ! $equity || $equity->is($account)) {
            return;
        }

        $accountLine = ['account_id' => $account->id];
        $equityLine = ['account_id' => $equity->id];

        if (AccountType::find($account->account_type_id)->increasesOnDebit()) {
            $accountLine += ['debit' => $amount, 'particular' => "{$amount} amt dr from {$account->name}"];
            $equityLine += ['credit' => $amount, 'particular' => "{$amount} amt cr from {$equity->name}"];
        } else {
            $accountLine += ['credit' => $amount, 'particular' => "{$amount} amt cr from {$account->name}"];
            $equityLine += ['debit' => $amount, 'particular' => "{$amount} amt dr from {$equity->name}"];
        }

        $this->ledgerPostingService->post($account, [
            'date' => $account->opening_date->toDateString(),
            'general_particular' => 'Initial Ledger account setup',
        ], [$accountLine, $equityLine]);
    }
}
