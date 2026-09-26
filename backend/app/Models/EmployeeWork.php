<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeWork extends Model
{
    use HasFactory;

    protected $table = 'emp_works';

    protected $guarded = [];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'salary_month' => 'date',
            'working_days' => 'decimal:2',
            'present_days' => 'decimal:2',
            'absent_days' => 'decimal:2',
            'paid_leave_days' => 'decimal:2',
            'unpaid_leave_days' => 'decimal:2',
            'overtime_hours' => 'decimal:2',
            'bonus' => 'decimal:2',
            'other_addition' => 'decimal:2',
            'other_deduction' => 'decimal:2',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class)->withTrashed();
    }

    /**
     * Once the month's salary is approved or paid, the work record it was
     * calculated from is frozen.
     */
    public function isLocked(): bool
    {
        return SalaryHistory::query()
            ->where('employee_id', $this->employee_id)
            ->whereDate('salary_month', $this->salary_month)
            ->where('is_active_record', true)
            ->whereIn('status', SalaryHistory::LOCKED_STATUSES)
            ->exists();
    }
}
