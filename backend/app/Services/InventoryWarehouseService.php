<?php

namespace App\Services;

use App\Models\InventoryWarehouse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class InventoryWarehouseService
{
    /**
     * @param  array{search?: ?string, status?: ?bool, warehouse_type?: ?string, parent_id?: ?int}  $filters
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return InventoryWarehouse::query()
            ->with(['parent', 'workOrder', 'manager'])
            ->when($filters['search'] ?? null, fn ($query, string $search) => $query->search($search))
            ->when(! is_null($filters['status'] ?? null), fn ($query) => $query->where('status', $filters['status']))
            ->when($filters['warehouse_type'] ?? null, fn ($query, string $type) => $query->where('warehouse_type', $type))
            ->when($filters['parent_id'] ?? null, fn ($query, int $parentId) => $query->where('parent_id', $parentId))
            ->orderBy('warehouse_type')
            ->orderBy('name')
            ->paginate($perPage);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): InventoryWarehouse
    {
        $warehouse = InventoryWarehouse::create([
            ...$data,
            'status' => $data['status'] ?? true,
            'created_by' => Auth::id(),
        ]);

        return $warehouse->load(['parent', 'workOrder', 'manager']);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(InventoryWarehouse $warehouse, array $data): InventoryWarehouse
    {
        $warehouse->fill([...$data, 'updated_by' => Auth::id()]);
        $warehouse->save();

        return $warehouse->load(['parent', 'workOrder', 'manager']);
    }
}
