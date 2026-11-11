<?php

namespace App\Models;

use App\Models\Concerns\Approvable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalaryHistory extends Model
{
    use Approvable, HasFactory;

    public const STATUS_GENERATED = 'Generated';

    public const STATUS_APPROVED = 'Approved';

    /**
     * Approved, with part of the net salary paid.
     */
    public const STATUS_PARTIALLY_PAID = 'Partially Paid';

    public const STATUS_PAID = 'Paid';

    public const STATUS_CANCELLED = 'Cancelled';

    /**
     * @var array<int, string>
     */
    public const STATUSES = [self::STATUS_GENERATED, self::STATUS_APPROVED, self::STATUS_PARTIALLY_PAID, self::STATUS_PAID, self::STATUS_CANCELLED];

    /**
     * Statuses whose values can no longer be recalculated.
     *
     * @var array<int, string>
     */
    public const LOCKED_STATUSES = [self::STATUS_APPROVED, self::STATUS_PARTIALLY_PAID, self::STATUS_PAID];

    /**
     * Statuses a payment can be made in.
     *
     * @var array<int, string>
     */
    public const PAYABLE_STATUSES = [self::STATUS_APPROVED, self::STATUS_PARTIALLY_PAID];

    /**
     * The paid total of a salary_history row, for SQL filters and sums.
     */
    public const PAID_SQL = '(select coalesce(sum(salary_payments.amount), 0) from salary_payments where salary_payments.salary_history_id = salary_history.id)';

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
            'other_deduction', 'advance_deduction', 'total_deduction', 'net_salary',
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

    /**
     * Advance installments deducted by this salary.
     */
    public function advanceRecoveries(): HasMany
    {
        return $this->hasMany(EmployeeAdvanceRecovery::class);
    }

    public function generator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function payer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    /**
     * The payments made against the net salary.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(SalaryPayment::class);
    }

    /**
     * The sum of the payments: from withPaidAmount() when the query added
     * it, else from the loaded or queried payments.
     */
    protected function paidAmount(): Attribute
    {
        return Attribute::get(fn (mixed $value) => round((float) ($value
            ?? ($this->relationLoaded('payments') ? $this->payments->sum('amount') : $this->payments()->sum('amount'))), 2));
    }

    public function scopeWithPaidAmount(Builder $query): Builder
    {
        return $query->withSum('payments as paid_amount', 'amount');
    }

    /**
     * Salaries with part of the net salary still unpaid.
     */
    public function scopeUnpaid(Builder $query): Builder
    {
        return $query->whereRaw('salary_history.net_salary > '.self::PAID_SQL);
    }

    /**
     * Net salary not paid yet.
     */
    public function dueAmount(): float
    {
        return round(max(0, (float) $this->net_salary - (float) $this->paid_amount), 2);
    }

    public function scopeLive(Builder $query): Builder
    {
        return $query->where('is_active_record', true);
    }

    public function scopeForMonth(Builder $query, string $month): Builder
    {
        return $query->whereDate('salary_month', $month.'-01');
    }

    public function scopeInDepartment(Builder $query, string $name): Builder
    {
        return $query->whereHas('employee', fn (Builder $query) => $query->inDepartment($name));
    }

    public function scopeWithDesignation(Builder $query, string $name): Builder
    {
        return $query->whereHas('employee', fn (Builder $query) => $query->withDesignation($name));
    }

    public function scopeOrderByEmployeeCode(Builder $query): Builder
    {
        return $query->orderBy(
            Employee::withTrashed()->select('employee_code')->whereColumn('employee_info.id', 'salary_history.employee_id')
        );
    }

    public function isLocked(): bool
    {
        return in_array($this->status, self::LOCKED_STATUSES, true);
    }
}
