<?php

namespace App\Services;

use App\Models\Supplier;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class SupplierService
{
    /**
     * @param  bool|null  $active  Filter by active status, or null for all suppliers.
     */
    public function paginate(int $perPage = 15, ?string $search = null, ?bool $active = null): LengthAwarePaginator
    {
        return Supplier::query()
            ->when($search, fn ($query) => $query->search($search))
            ->when(! is_null($active), fn ($query) => $query->where('active_status', $active))
            ->latest()
            ->paginate($perPage);
    }

    public function find(Supplier $supplier): Supplier
    {
        return $supplier->load('transactions');
    }

    /**
     * @param  array{name: string, email?: ?string, phone?: ?string, address?: ?string, vat_no?: ?string, payment_term?: ?int, contact_person?: ?string, contact_person_phone?: ?string, contact_person_email?: ?string, opening_balance?: ?float, active_status?: ?bool, branch_office_id?: ?int}  $data
     */
    public function create(array $data): Supplier
    {
        $openingBalance = $data['opening_balance'] ?? 0;

        return Supplier::create([
            ...$data,
            // The running balance starts out equal to the opening balance; it only
            // moves afterwards through ledger transactions and purchases.
            'opening_balance' => $openingBalance,
            'current_balance' => $openingBalance,
            'active_status' => $data['active_status'] ?? true,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);
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

    public function delete(Supplier $supplier): void
    {
        $supplier->delete();
    }
}
