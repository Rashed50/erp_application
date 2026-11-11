<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\SalaryDetail;
use App\Models\SalaryDetailLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Salary configuration revisions. A salary change (promotion, demotion,
 * increment...) is recorded as a new revision with its own effective date;
 * revisions already used by a generated salary or replaced by a later one
 * are never edited or deleted, so every earlier salary stays on record.
 *
 * Every add, edit and delete is written to the salary edit log with the old
 * and new value of each changed field, so even a corrected or removed
 * revision can be traced.
 */
class SalaryDetailService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function create(Employee $employee, array $data): SalaryDetail
    {
        return DB::transaction(function () use ($employee, $data) {
            $salaryDetail = $employee->salaryDetails()->create([
                ...$data,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            $this->log($salaryDetail, SalaryDetailLog::ACTION_CREATED, [], $salaryDetail->loggableValues());
            $this->applyDesignation($salaryDetail);

            return $salaryDetail->load('designation:id,name');
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(SalaryDetail $salaryDetail, array $data): SalaryDetail
    {
        $this->ensureUnused($salaryDetail);

        return DB::transaction(function () use ($salaryDetail, $data) {
            $before = $salaryDetail->loggableValues();

            $salaryDetail->fill([...$data, 'updated_by' => Auth::id()]);
            $salaryDetail->save();

            $this->log($salaryDetail, SalaryDetailLog::ACTION_UPDATED, $before, $salaryDetail->loggableValues());
            $this->applyDesignation($salaryDetail);

            return $salaryDetail->load('designation:id,name');
        });
    }

    public function delete(SalaryDetail $salaryDetail): void
    {
        $this->ensureUnused($salaryDetail);

        DB::transaction(function () use ($salaryDetail) {
            $this->log($salaryDetail, SalaryDetailLog::ACTION_DELETED, $salaryDetail->loggableValues(), []);
            $salaryDetail->delete();
        });
    }

    /**
     * A promotion or demotion in the employee's latest revision gives the
     * employee that designation. An older (back-dated) revision only records
     * the designation of its time.
     */
    private function applyDesignation(SalaryDetail $salaryDetail): void
    {
        if (! $salaryDetail->designation_id || $salaryDetail->isSuperseded()) {
            return;
        }

        $salaryDetail->employee()->update([
            'designation_id' => $salaryDetail->designation_id,
            'updated_by' => Auth::id(),
        ]);
    }

    /**
     * Write the fields whose value differs between `$before` and `$after`;
     * an edit that changed nothing is not logged.
     *
     * @param  array<string, mixed>  $before
     * @param  array<string, mixed>  $after
     */
    private function log(SalaryDetail $salaryDetail, string $action, array $before, array $after): void
    {
        $changes = collect(array_keys($before + $after))
            ->mapWithKeys(fn (string $field) => [$field => ['old' => $before[$field] ?? null, 'new' => $after[$field] ?? null]])
            ->reject(fn (array $change) => $change['old'] === $change['new'])
            ->all();

        if (! $changes) {
            return;
        }

        SalaryDetailLog::create([
            'salary_detail_id' => $salaryDetail->id,
            'employee_id' => $salaryDetail->employee_id,
            'action' => $action,
            'changes' => $changes,
            'created_by' => Auth::id(),
        ]);
    }

    private function ensureUnused(SalaryDetail $salaryDetail): void
    {
        if ($salaryDetail->isUsedByPayroll()) {
            throw ValidationException::withMessages([
                'effective_date' => __('This salary configuration was used to generate salaries. Add a new revision with a later effective date instead.'),
            ]);
        }

        if ($salaryDetail->isSuperseded()) {
            throw ValidationException::withMessages([
                'effective_date' => __('A later salary revision exists, so this one is kept as salary history and cannot be changed.'),
            ]);
        }
    }
}
