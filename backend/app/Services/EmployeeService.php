<?php

namespace App\Services;

use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\EmployeeBankDetail;
use App\Models\EmployeeDetail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EmployeeService
{
    /**
     * @param  array{search?: ?string, department?: ?string, designation?: ?string, status?: ?string, employment_type?: ?string}  $filters
     */
    public function paginate(int $perPage, array $filters): LengthAwarePaginator
    {
        return Employee::query()
            ->with(['department', 'designation'])
            ->when($filters['search'] ?? null, fn (Builder $query, string $search) => $query->search($search))
            ->when($filters['department'] ?? null, fn (Builder $query, string $department) => $query->inDepartment($department))
            ->when($filters['designation'] ?? null, fn (Builder $query, string $designation) => $query->withDesignation($designation))
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status))
            ->when($filters['employment_type'] ?? null, fn (Builder $query, string $type) => $query->where('employment_type', $type))
            ->orderBy('employee_code')
            ->paginate($perPage);
    }

    public function find(Employee $employee): Employee
    {
        return $employee->load([
            'department',
            'designation',
            'division',
            'district',
            'upazila',
            'detail.permanentDivision',
            'detail.permanentDistrict',
            'detail.permanentUpazila',
            'bankDetail',
            'files',
            'salaryDetails' => fn ($query) => $query->withCount('salaryHistories')->orderByDesc('effective_date'),
        ]);
    }

    /**
     * Create the employee and its one-to-one detail and bank rows together.
     *
     * @param  array<string, mixed>  $data  Employee columns plus optional `detail` and `bank` arrays.
     */
    public function create(array $data): Employee
    {
        return DB::transaction(function () use ($data) {
            $employee = Employee::create([
                ...Arr::except($data, ['detail', 'bank']),
                'employee_code' => ($data['employee_code'] ?? null) ?: $this->nextEmployeeCode(),
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            $employee->detail()->create(Arr::only($data['detail'] ?? [], EmployeeDetail::FIELDS));

            if (array_filter($data['bank'] ?? [])) {
                $employee->bankDetail()->create(Arr::only($data['bank'], EmployeeBankDetail::FIELDS));
            }

            return $this->find($employee);
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Employee $employee, array $data): Employee
    {
        return DB::transaction(function () use ($employee, $data) {
            $employee->fill([...Arr::except($data, ['detail', 'bank']), 'updated_by' => Auth::id()]);
            $employee->save();

            if (array_key_exists('detail', $data)) {
                $employee->detail()->updateOrCreate([], Arr::only($data['detail'] ?? [], EmployeeDetail::FIELDS));
            }

            if (array_key_exists('bank', $data)) {
                $employee->bankDetail()->updateOrCreate([], Arr::only($data['bank'] ?? [], EmployeeBankDetail::FIELDS));
            }

            return $this->find($employee);
        });
    }

    public function delete(Employee $employee): void
    {
        $employee->delete();
    }

    /**
     * The next sequential code, e.g. EMP-1001, EMP-1002 (deleted employees
     * included so a code is never reused).
     */
    public function nextEmployeeCode(): string
    {
        $highest = Employee::withTrashed()
            ->where('employee_code', 'like', 'EMP-%')
            ->pluck('employee_code')
            ->map(fn (string $code) => (int) substr($code, 4))
            ->max();

        return 'EMP-'.max(1001, $highest + 1);
    }

    /**
     * Every department and designation (inactive ones included, so a
     * filter or an employee that still uses one can show it).
     *
     * @return array{departments: array<int, array{id: int, name: string, status: bool}>, designations: array<int, array{id: int, name: string, status: bool}>}
     */
    public function options(): array
    {
        return [
            'departments' => Department::query()->orderBy('name')->get(['id', 'name', 'status'])->toArray(),
            'designations' => Designation::query()->orderBy('name')->get(['id', 'name', 'status'])->toArray(),
        ];
    }
}
