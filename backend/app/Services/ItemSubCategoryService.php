<?php

namespace App\Services;

use App\Models\ItemSubCategory;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class ItemSubCategoryService
{
    /**
     * @param  bool|null  $status  Filter by active status, or null for all sub categories.
     */
    public function paginate(int $perPage = 15, ?string $search = null, ?bool $status = null, ?int $categoryId = null): LengthAwarePaginator
    {
        return ItemSubCategory::query()
            ->with('category')
            ->withCount('items')
            ->when($search, fn ($query) => $query->search($search))
            ->when(! is_null($status), fn ($query) => $query->where('status', $status))
            ->when($categoryId, fn ($query) => $query->where('icatg_id', $categoryId))
            ->latest()
            ->latest('iscatg_id')
            ->paginate($perPage);
    }

    /**
     * @param  array{icatg_id: int, iscatg_name: string, iscatg_code: string, status?: ?bool}  $data
     */
    public function create(array $data): ItemSubCategory
    {
        $subCategory = ItemSubCategory::create([
            ...$data,
            'status' => $data['status'] ?? true,
            'create_by_id' => Auth::id(),
        ]);

        return $subCategory->load('category');
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(ItemSubCategory $subCategory, array $data): ItemSubCategory
    {
        $subCategory->fill([...$data, 'update_by_id' => Auth::id()]);
        $subCategory->save();

        return $subCategory->load('category');
    }

    public function setStatus(ItemSubCategory $subCategory, bool $status): ItemSubCategory
    {
        return $this->update($subCategory, ['status' => $status]);
    }
}
