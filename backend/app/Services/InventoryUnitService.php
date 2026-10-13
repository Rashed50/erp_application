<?php

namespace App\Services;

use App\Models\InventoryUnit;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class InventoryUnitService
{
    /**
     * @param  array{search?: ?string, status?: ?bool}  $filters
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return InventoryUnit::query()
            ->when($filters['search'] ?? null, fn ($query, string $search) => $query->search($search))
            ->when(! is_null($filters['status'] ?? null), fn ($query) => $query->where('status', $filters['status']))
            ->orderBy('name')
            ->paginate($perPage);
    }

    /**
     * @param  array{name: string, short_name: string, status?: ?bool}  $data
     */
    public function create(array $data): InventoryUnit
    {
        return InventoryUnit::create([
            ...$data,
            'status' => $data['status'] ?? true,
            'created_by' => Auth::id(),
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(InventoryUnit $unit, array $data): InventoryUnit
    {
        $unit->fill([...$data, 'updated_by' => Auth::id()]);
        $unit->save();

        return $unit;
    }
}
