<?php

namespace App\Services;

use App\Models\ItemCategory;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class ItemCategoryService
{
    /**
     * @param  bool|null  $status  Filter by active status, or null for all categories.
     */
    public function paginate(int $perPage = 15, ?string $search = null, ?bool $status = null): LengthAwarePaginator
    {
        return ItemCategory::query()
            ->withCount('subCategories')
            ->when($search, fn ($query) => $query->search($search))
            ->when(! is_null($status), fn ($query) => $query->where('status', $status))
            ->latest()
            ->latest('icatg_id')
            ->paginate($perPage);
    }

    /**
     * @param  array{icatg_name: string, icatg_code: string, status?: ?bool}  $data
     */
    public function create(array $data): ItemCategory
    {
        return ItemCategory::create([
            ...$data,
            'status' => $data['status'] ?? true,
            'create_by_id' => Auth::id(),
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(ItemCategory $category, array $data): ItemCategory
    {
        $category->fill([...$data, 'update_by_id' => Auth::id()]);
        $category->save();

        return $category;
    }

    public function setStatus(ItemCategory $category, bool $status): ItemCategory
    {
        return $this->update($category, ['status' => $status]);
    }
}
