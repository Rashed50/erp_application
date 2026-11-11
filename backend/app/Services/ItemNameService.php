<?php

namespace App\Services;

use App\Models\ItemName;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ItemNameService
{
    /**
     * @param  array{search?: ?string, status?: ?bool, icatg_id?: ?int, iscatg_id?: ?int, itype_id?: ?int}  $filters
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return ItemName::query()
            ->with(['category', 'subCategory', 'unit'])
            ->when($filters['search'] ?? null, fn ($query, string $search) => $query->search($search))
            ->when(! is_null($filters['status'] ?? null), fn ($query) => $query->where('item_status', $filters['status']))
            ->when($filters['icatg_id'] ?? null, fn ($query, int $categoryId) => $query->whereRelation('subCategory', 'icatg_id', $categoryId))
            ->when($filters['iscatg_id'] ?? null, fn ($query, int $subCategoryId) => $query->where('iscatg_id', $subCategoryId))
            ->when($filters['itype_id'] ?? null, fn ($query, int $typeId) => $query->where('itype_id', $typeId))
            ->latest()
            ->latest('item_id')
            ->paginate($perPage);
    }

    /**
     * The category (`icatg_id`) only narrows the sub category choice; the
     * item keeps just its sub category, which belongs to that category.
     *
     * @param  array{icatg_id: int, iscatg_id: int, itype_id: int, item_name: string, item_title: string, item_code: string, unit_id?: ?int, track_type?: ?string, minimum_stock?: ?float, item_status?: ?bool}  $data
     */
    public function create(array $data): ItemName
    {
        $item = ItemName::create([
            ...Arr::except($data, 'icatg_id'),
            'track_type' => $this->trackTypeFor($data['itype_id'], $data['track_type'] ?? null),
            'minimum_stock' => $data['minimum_stock'] ?? 0,
            'item_status' => $data['item_status'] ?? true,
            'create_by_id' => Auth::id(),
        ]);

        return $item->load(['category', 'subCategory', 'unit']);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(ItemName $item, array $data): ItemName
    {
        if (array_key_exists('itype_id', $data) || array_key_exists('track_type', $data)) {
            $data['track_type'] = $this->trackTypeFor(
                $data['itype_id'] ?? $item->itype_id,
                $data['track_type'] ?? ($item->isAsset() ? null : $item->track_type),
            );

            // Switching between serial and quantity tracking would orphan the
            // serials (or leave untracked units) already in stock.
            if ($data['track_type'] !== $item->track_type && $item->stocks()->exists()) {
                throw ValidationException::withMessages([
                    'track_type' => __('The tracking type cannot change once the item has stock movements.'),
                ]);
            }
        }

        if (array_key_exists('minimum_stock', $data)) {
            $data['minimum_stock'] ??= 0;
        }

        $item->fill([...Arr::except($data, 'icatg_id'), 'update_by_id' => Auth::id()]);
        $item->save();

        return $item->load(['category', 'subCategory', 'unit']);
    }

    /**
     * Asset items are always tracked unit by unit, so each one can be tagged
     * and handed to an employee; other items default to plain quantities.
     */
    private function trackTypeFor(int $typeId, ?string $trackType): string
    {
        return $typeId === ItemName::TYPE_ASSET ? ItemName::TRACK_SERIAL : ($trackType ?? ItemName::TRACK_QUANTITY);
    }

    public function setStatus(ItemName $item, bool $status): ItemName
    {
        return $this->update($item, ['item_status' => $status]);
    }
}
