<?php

namespace App\Services;

use App\Models\EmployeeWork;
use App\Models\SalaryHistory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class EmployeeWorkService
{
    /**
     * @param  array{month?: ?string, employee_id?: ?int, department?: ?string, designation?: ?string, search?: ?string}  $filters
     */
    public function paginate(int $perPage, array $filters): LengthAwarePaginator
    {
        return EmployeeWork::query()
            ->with('employee:id,employee_code,name,department,designation')
            ->when($filters['month'] ?? null, fn (Builder $query, string $month) => $query->whereDate('salary_month', $month.'-01'))
            ->when($filters['employee_id'] ?? null, fn (Builder $query, int $employeeId) => $query->where('employee_id', $employeeId))
            ->when(
                ($filters['department'] ?? null) || ($filters['designation'] ?? null) || ($filters['search'] ?? null),
                fn (Builder $query) => $query->whereHas('employee', fn (Builder $query) => $query
                    ->when($filters['department'] ?? null, fn (Builder $query, string $department) => $query->where('department', $department))
                    ->when($filters['designation'] ?? null, fn (Builder $query, string $designation) => $query->where('designation', $designation))
                    ->when($filters['search'] ?? null, fn (Builder $query, string $search) => $query->search($search)))
            )
            ->orderByDesc('salary_month')
            ->orderBy('employee_id')
            ->paginate($perPage);
    }

    public function findFor(int $employeeId, string $month): ?EmployeeWork
    {
        return EmployeeWork::query()
            ->where('employee_id', $employeeId)
            ->whereDate('salary_month', $month.'-01')
            ->first();
    }

    /**
     * @param  array<string, mixed>  $data  Includes `employee_id` and `salary_month` as Y-m.
     */
    public function create(array $data): EmployeeWork
    {
        return EmployeeWork::create([
            ...$data,
            'salary_month' => $data['salary_month'].'-01',
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ])->load('employee');
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(EmployeeWork $work, array $data): EmployeeWork
    {
        $this->ensureNotLocked($work);

        $work->fill([...$data, 'updated_by' => Auth::id()]);
        $work->save();

        return $work->load('employee');
    }

    public function delete(EmployeeWork $work): void
    {
        $this->ensureNotLocked($work);

        if ($this->hasLiveSalary($work)) {
            throw ValidationException::withMessages([
                'salary_month' => 'A salary has been generated from this work record. Cancel that salary first.',
            ]);
        }

        $work->delete();
    }

    /**
     * A generated (not yet approved) salary exists and should be regenerated
     * after this record changes.
     */
    public function hasLiveSalary(EmployeeWork $work): bool
    {
        return SalaryHistory::query()
            ->live()
            ->where('employee_id', $work->employee_id)
            ->whereDate('salary_month', $work->salary_month)
            ->exists();
    }

    private function ensureNotLocked(EmployeeWork $work): void
    {
        if ($work->isLocked()) {
            throw ValidationException::withMessages([
                'salary_month' => 'The salary for this month is already approved or paid, so its work record can no longer change.',
            ]);
        }
    }
}
