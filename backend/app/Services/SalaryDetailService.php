<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\SalaryDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * Salary configuration revisions. A salary change is recorded as a new
 * revision with its own effective date; revisions already used by a
 * generated salary are never edited or deleted.
 */
class SalaryDetailService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function create(Employee $employee, array $data): SalaryDetail
    {
        return $employee->salaryDetails()->create([
            ...$data,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(SalaryDetail $salaryDetail, array $data): SalaryDetail
    {
        $this->ensureUnused($salaryDetail);

        $salaryDetail->fill([...$data, 'updated_by' => Auth::id()]);
        $salaryDetail->save();

        return $salaryDetail;
    }

    public function delete(SalaryDetail $salaryDetail): void
    {
        $this->ensureUnused($salaryDetail);

        $salaryDetail->delete();
    }

    private function ensureUnused(SalaryDetail $salaryDetail): void
    {
        if ($salaryDetail->isUsedByPayroll()) {
            throw ValidationException::withMessages([
                'effective_date' => 'This salary configuration was used to generate salaries. Add a new revision with a later effective date instead.',
            ]);
        }
    }
}
