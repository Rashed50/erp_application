<?php

namespace App\Services;

use App\Models\Department;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class DepartmentService
{
    /**
     * @param  bool|null  $status  Filter by active status, or null for all departments.
     */
    public function paginate(int $perPage = 15, ?string $search = null, ?bool $status = null): LengthAwarePaginator
    {
        return Department::query()
            ->withCount('employees')
            ->when($search, fn ($query) => $query->search($search))
            ->when(! is_null($status), fn ($query) => $query->where('status', $status))
            ->orderBy('name')
            ->paginate($perPage);
    }

    /**
     * @param  array{name: string, description?: ?string, status?: ?bool}  $data
     */
    public function create(array $data): Department
    {
        return Department::create([
            ...$data,
            'status' => $data['status'] ?? true,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Department $department, array $data): Department
    {
        $department->fill([...$data, 'updated_by' => Auth::id()]);
        $department->save();

        return $department;
    }

    public function setStatus(Department $department, bool $status): Department
    {
        return $this->update($department, ['status' => $status]);
    }
}
