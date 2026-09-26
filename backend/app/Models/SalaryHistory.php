<?php

namespace App\Models;

use App\Models\Concerns\Approvable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalaryHistory extends Model
{
    use Approvable, HasFactory;

    public const STATUS_GENERATED = 'Generated';

    public const STATUS_APPROVED = 'Approved';

    public const STATUS_PAID = 'Paid';

    public const STATUS_CANCELLED = 'Cancelled';

    /**
     * @var array<int, string>
     */
    public const STATUSES = [self::STATUS_GENERATED, self::STATUS_APPROVED, self::STATUS_PAID, self::STATUS_CANCELLED];

    /**
     * Statuses whose values can no longer be recalculated.
     *
     * @var array<int, string>
     */
    public const LOCKED_STATUSES = [self::STATUS_APPROVED, self::STATUS_PAID];

    protected $table = 'salary_history';

    protected $guarded = [];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        $money = collect([
            'overtime_rate', 'basic_salary', 'house_rent', 'medical_allowance', 'transport_allowance',
            'food_allowance', 'other_allowance', 'total_allowance', 'overtime_amount', 'bonus',
            'other_addition', 'gross_salary', 'per_day_rate', 'absence_deduction', 'unpaid_leave_deduction',
            'other_deduction', 'total_deduction', 'net_salary',
            'working_days', 'present_days', 'absent_days', 'paid_leave_days', 'unpaid_leave_days', 'overtime_hours',
        ])->mapWithKeys(fn (string $column) => [$column => 'decimal:2'])->all();

        return [
            ...$money,
            'salary_month' => 'date',
            'is_active_record' => 'boolean',
            'generated_at' => 'datetime',
            'paid_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class)->withTrashed();
    }

    public function salaryDetail(): BelongsTo
    {
        return $this->belongsTo(SalaryDetail::class);
    }

    public function work(): BelongsTo
    {
        return $this->belongsTo(EmployeeWork::class, 'emp_work_id');
    }

    public function generator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function payer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    public function scopeLive(Builder $query): Builder
    {
        return $query->where('is_active_record', true);
    }

    public function scopeForMonth(Builder $query, string $month): Builder
    {
        return $query->whereDate('salary_month', $month.'-01');
    }

    public function isLocked(): bool
    {
        return in_array($this->status, self::LOCKED_STATUSES, true);
    }
}
