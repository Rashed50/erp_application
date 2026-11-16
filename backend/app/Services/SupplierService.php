<?php

namespace App\Services;

use App\Models\AccountType;
use App\Models\Supplier;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SupplierService
{
    public function __construct(private readonly LedgerPostingService $ledgerPostingService) {}

    /**
     * @param  bool|null  $active  Filter by active status, or null for all suppliers.
     */
    public function paginate(int $perPage = 15, ?string $search = null, ?bool $active = null): LengthAwarePaginator
    {
        return Supplier::query()
            ->with(['creator:id,name', 'approver:id,name'])
            ->withCurrentBalance()
            ->when($search, fn ($query) => $query->search($search))
            ->when(! is_null($active), fn ($query) => $query->where('active_status', $active))
            ->latest()
            ->paginate($perPage);
    }

    public function find(Supplier $supplier): Supplier
    {
        return $supplier->load(['transactions.creator:id,name', 'openingBalanceAccount:id,name,account_number']);
    }

    /**
     * @param  array{name: string, supplier_type?: ?string, opening_balance_account_id?: ?int, email?: ?string, phone?: ?string, address?: ?string, vat_no?: ?string, payment_term?: ?int, contact_person?: ?string, contact_person_phone?: ?string, contact_person_email?: ?string, opening_balance?: ?float, active_status?: ?bool, branch_office_id?: ?int}  $data
     */
    public function create(array $data): Supplier
    {
        return DB::transaction(function () use ($data) {
            $openingBalance = $data['opening_balance'] ?? 0;

            $supplier = Supplier::create([
                ...$data,
                // The running balance starts out equal to the opening balance; it only
                // moves afterwards through ledger transactions and purchases.
                'opening_balance' => $openingBalance,
                'supplier_type' => $data['supplier_type'] ?? Supplier::TYPE_GOODS,
                'active_status' => $data['active_status'] ?? true,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            $this->postOpeningBalance($supplier);

            return $supplier;
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Supplier $supplier, array $data): Supplier
    {
        $supplier->fill([...$data, 'updated_by' => Auth::id()]);
        $supplier->save();

        return $supplier;
    }

    /**
     * The opening balance entry is cancelled by a reversal entry rather
     * than erased, so Accounts Payable no longer counts a deleted supplier
     * while the books keep the history.
     */
    public function delete(Supplier $supplier): void
    {
        DB::transaction(function () use ($supplier) {
            $this->ledgerPostingService->postReversal($supplier, 'reverse deleted supplier account');
            $supplier->delete();
        });
    }

    /**
     * A positive opening balance is posted to the general ledger: credit
     * Accounts Payable and debit the account chosen for it (e.g. an expense
     * account for a service supplier), or, as in the payroll module, the
     * Assets group account when none was chosen. Skipped when those accounts
     * have not been seeded.
     */
    private function postOpeningBalance(Supplier $supplier): void
    {
        $amount = (float) $supplier->opening_balance;
        $debitAccount = $supplier->openingBalanceAccount ?? $this->ledgerPostingService->rootAccount(AccountType::ASSET);
        $payable = $this->ledgerPostingService->predefinedAccount(LedgerPostingService::ACCOUNTS_PAYABLE_NUMBER);

        if ($amount <= 0 || ! $debitAccount || ! $payable) {
            return;
        }

        $this->ledgerPostingService->post($supplier, [
            'date' => today()->toDateString(),
            'general_particular' => 'initialize supplier account',
        ], [
            ['account_id' => $debitAccount->id, 'debit' => $amount, 'particular' => "{$amount} amt dr from {$debitAccount->name}"],
            ['account_id' => $payable->id, 'credit' => $amount, 'particular' => "{$amount} amt cr from {$payable->name}"],
        ]);
    }
}
