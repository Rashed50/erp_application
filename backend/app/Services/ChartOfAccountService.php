<?php

namespace App\Services;

use App\Models\ChartOfAccount;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChartOfAccountService
{
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
            ->with(['accountType:id,name', 'parent:id,name', 'creator:id,name'])
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
        return $account->load(['accountType:id,name', 'parent:id,name']);
    }

    /**
     * @param  array{name: string, account_number?: ?string, account_type_id?: ?int, parent_id?: ?int, opening_date?: ?string, balance?: ?float, is_transaction?: ?bool, active_status?: ?bool}  $data
     */
    public function create(array $data): ChartOfAccount
    {
        $parent = isset($data['parent_id']) ? ChartOfAccount::find($data['parent_id']) : null;

        $account = ChartOfAccount::create([
            ...$data,
            'account_type_id' => $parent?->account_type_id ?? $data['account_type_id'],
            'sibling_level' => $parent ? $parent->sibling_level + 1 : 0,
            'opening_date' => $data['opening_date'] ?? today()->toDateString(),
            'balance' => $data['balance'] ?? 0,
            'is_transaction' => $data['is_transaction'] ?? false,
            'active_status' => $data['active_status'] ?? true,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return $this->find($account);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(ChartOfAccount $account, array $data): ChartOfAccount
    {
        return DB::transaction(function () use ($account, $data) {
            $account->fill([...$data, 'updated_by' => Auth::id()]);

            if (array_key_exists('parent_id', $data)) {
                $parent = $data['parent_id'] ? ChartOfAccount::find($data['parent_id']) : null;
                $account->sibling_level = $parent ? $parent->sibling_level + 1 : 0;
            }

            $levelChanged = $account->isDirty('sibling_level');
            $account->save();

            if ($levelChanged) {
                $this->syncChildLevels($account);
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

    public function delete(ChartOfAccount $account): void
    {
        $account->delete();
    }

    /**
     * Keeps every descendant's sibling_level consistent after its ancestor moved.
     */
    private function syncChildLevels(ChartOfAccount $account): void
    {
        foreach ($account->children as $child) {
            $child->update(['sibling_level' => $account->sibling_level + 1]);
            $this->syncChildLevels($child);
        }
    }
}
