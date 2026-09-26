<?php

namespace App\Http\Requests\Concerns;

use App\Models\Employee;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Validation\Validator;

/**
 * Shared rules for a monthly work record, used by both the store and the
 * update request.
 */
trait ValidatesWorkRecord
{
    /**
     * @return array<string, array<mixed>>
     */
    protected function workRules(): array
    {
        $days = ['required', 'numeric', 'min:0', 'max:31', 'multiple_of:0.5'];
        $amount = ['required', 'numeric', 'min:0', 'max:9999999999'];

        return [
            'working_days' => $days,
            'present_days' => $days,
            'absent_days' => $days,
            'paid_leave_days' => $days,
            'unpaid_leave_days' => $days,
            'overtime_hours' => ['required', 'numeric', 'min:0', 'max:744'],
            'bonus' => $amount,
            'other_addition' => $amount,
            'other_deduction' => $amount,
            'remarks' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Present + absent + leave days may not exceed the working days, working
     * days may not exceed the month's length, and the employee must have been
     * employed during the month.
     */
    protected function validateWorkRecord(Validator $validator, ?Employee $employee, ?CarbonImmutable $month): void
    {
        if ($validator->errors()->isNotEmpty() || ! $employee || ! $month) {
            return;
        }

        $workingDays = (float) $this->input('working_days');
        $recordedDays = (float) $this->input('present_days') + (float) $this->input('absent_days')
            + (float) $this->input('paid_leave_days') + (float) $this->input('unpaid_leave_days');

        if ($workingDays > $month->daysInMonth) {
            $validator->errors()->add('working_days', "Working days cannot exceed the {$month->daysInMonth} days in {$month->format('F Y')}.");
        }

        if ($recordedDays > $workingDays) {
            $validator->errors()->add('present_days', "Present + absent + leave days ({$recordedDays}) cannot exceed the working days ({$workingDays}).");
        }

        if (! $employee->isEmployedDuring($month)) {
            $validator->errors()->add('salary_month', "{$employee->name} was not employed during {$month->format('F Y')}.");
        }
    }
}
