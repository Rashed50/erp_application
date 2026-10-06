<?php

namespace App\Services;

use App\Models\Designation;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class DesignationService
{
    /**
     * @param  bool|null  $status  Filter by active status, or null for all designations.
     */
    public function paginate(int $perPage = 15, ?string $search = null, ?bool $status = null): LengthAwarePaginator
    {
        return Designation::query()
            ->withCount('employees')
            ->when($search, fn ($query) => $query->search($search))
            ->when(! is_null($status), fn ($query) => $query->where('status', $status))
            ->orderBy('name')
            ->paginate($perPage);
    }

    /**
     * @param  array{name: string, description?: ?string, status?: ?bool}  $data
     */
    public function create(array $data): Designation
    {
        return Designation::create([
            ...$data,
            'status' => $data['status'] ?? true,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Designation $designation, array $data): Designation
    {
        $designation->fill([...$data, 'updated_by' => Auth::id()]);
        $designation->save();

        return $designation;
    }

    public function setStatus(Designation $designation, bool $status): Designation
    {
        return $this->update($designation, ['status' => $status]);
    }
}
