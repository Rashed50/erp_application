<?php

namespace App\Services;

use App\Models\IncomeExpenseAccount;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class IncomeExpenseAccountService
{
    public function paginate(int $perPage = 15, ?string $type = null, ?bool $active = null): LengthAwarePaginator
    {
        return IncomeExpenseAccount::query()
            ->when($type, fn ($query) => $query->ofType($type))
            ->when(! is_null($active), fn ($query) => $query->where('active_status', $active))
            ->orderBy('type')
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function find(IncomeExpenseAccount $account): IncomeExpenseAccount
    {
        return $account;
    }

    /**
     * @param  array{name: string, type: string, active_status?: ?bool}  $data
     */
    public function create(array $data): IncomeExpenseAccount
    {
        return IncomeExpenseAccount::create([
            ...$data,
            'active_status' => $data['active_status'] ?? true,
            'created_by' => Auth::id(),
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(IncomeExpenseAccount $account, array $data): IncomeExpenseAccount
    {
        $account->fill($data);
        $account->save();

        return $account;
    }

    public function delete(IncomeExpenseAccount $account): void
    {
        $account->delete();
    }
}
