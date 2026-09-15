<?php

namespace App\Services;

use App\Models\Customer;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class CustomerService
{
    /**
     * @param  bool|null  $active  Filter by active status, or null for all customers.
     */
    public function paginate(int $perPage = 15, ?string $search = null, ?bool $active = null): LengthAwarePaginator
    {
        return Customer::query()
            ->when($search, fn ($query) => $query->search($search))
            ->when(! is_null($active), fn ($query) => $query->where('active_status', $active))
            ->latest()
            ->paginate($perPage);
    }

    public function find(Customer $customer): Customer
    {
        return $customer->load('transactions');
    }

    /**
     * @param  array{name: string, email?: ?string, phone?: ?string, address?: ?string, vat_no?: ?string, payment_term?: ?int, contact_person?: ?string, contact_person_phone?: ?string, contact_person_email?: ?string, country?: ?string, opening_date?: ?string, opening_balance?: ?float, active_status?: ?bool, branch_office_id?: ?int}  $data
     */
    public function create(array $data): Customer
    {
        $openingBalance = $data['opening_balance'] ?? 0;

        return Customer::create([
            ...$data,
            // The running balance starts out equal to the opening balance; it only
            // moves afterwards through ledger transactions.
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
    public function update(Customer $customer, array $data): Customer
    {
        $customer->fill([...$data, 'updated_by' => Auth::id()]);
        $customer->save();

        return $customer;
    }

    public function delete(Customer $customer): void
    {
        $customer->delete();
    }
}
