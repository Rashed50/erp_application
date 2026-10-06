<?php

namespace App\Services;

use App\Models\ItemName;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class ItemNameService
{
    /**
     * @param  array{search?: ?string, status?: ?bool, icatg_id?: ?int, iscatg_id?: ?int, itype_id?: ?int}  $filters
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return ItemName::query()
            ->with(['category', 'subCategory'])
            ->when($filters['search'] ?? null, fn ($query, string $search) => $query->search($search))
            ->when(! is_null($filters['status'] ?? null), fn ($query) => $query->where('item_status', $filters['status']))
            ->when($filters['icatg_id'] ?? null, fn ($query, int $categoryId) => $query->where('icatg_id', $categoryId))
            ->when($filters['iscatg_id'] ?? null, fn ($query, int $subCategoryId) => $query->where('iscatg_id', $subCategoryId))
            ->when($filters['itype_id'] ?? null, fn ($query, int $typeId) => $query->where('itype_id', $typeId))
            ->latest()
            ->latest('item_id')
            ->paginate($perPage);
    }

    /**
     * @param  array{icatg_id: int, iscatg_id: int, itype_id: int, item_name: string, item_title: string, item_code: string, item_status?: ?bool}  $data
     */
    public function create(array $data): ItemName
    {
        $item = ItemName::create([
            ...$data,
            'item_status' => $data['item_status'] ?? true,
            'create_by_id' => Auth::id(),
        ]);

        return $item->load(['category', 'subCategory']);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(ItemName $item, array $data): ItemName
    {
        $item->fill([...$data, 'update_by_id' => Auth::id()]);
        $item->save();

        return $item->load(['category', 'subCategory']);
    }

    public function setStatus(ItemName $item, bool $status): ItemName
    {
        return $this->update($item, ['item_status' => $status]);
    }
}
