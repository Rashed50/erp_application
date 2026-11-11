<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmployeeAdvance extends Model
{
    use HasFactory;

    public const STATUS_RUNNING = 'Running';

    public const STATUS_COMPLETED = 'Completed';

    /**
     * @var array<int, string>
     */
    public const STATUSES = [self::STATUS_RUNNING, self::STATUS_COMPLETED];

    /**
     * The recovered total of an employee_advances row, for SQL filters and sums.
     */
    public const RECOVERED_SQL = '(select coalesce(sum(employee_advance_recoveries.amount), 0) from employee_advance_recoveries where employee_advance_recoveries.employee_advance_id = employee_advances.id)';

    protected $guarded = [];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'advance_date' => 'date',
            'deduction_start_month' => 'date',
            'amount' => 'decimal:2',
            'installment_amount' => 'decimal:2',
        ];
    }

    /**
     * The sum of the recoveries: from withRecoveredAmount() when the query
     * added it, else from the loaded or queried recoveries.
     */
    protected function recoveredAmount(): Attribute
    {
        return Attribute::get(fn (mixed $value) => round((float) ($value
            ?? ($this->relationLoaded('recoveries') ? $this->recoveries->sum('amount') : $this->recoveries()->sum('amount'))), 2));
    }

    /**
     * Running until the whole amount is recovered.
     */
    protected function status(): Attribute
    {
        return Attribute::get(fn () => $this->outstandingAmount() > 0 ? self::STATUS_RUNNING : self::STATUS_COMPLETED);
    }

    public function scopeWithRecoveredAmount(Builder $query): Builder
    {
        return $query->withSum('recoveries as recovered_amount', 'amount');
    }

    public function scopeInStatus(Builder $query, string $status): Builder
    {
        return $status === self::STATUS_COMPLETED
            ? $query->whereRaw('employee_advances.amount <= '.self::RECOVERED_SQL)
            : $query->running();
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class)->withTrashed();
    }

    /**
     * The cash/bank account the advance was paid from.
     */
    public function paymentAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'payment_account_id');
    }

    public function recoveries(): HasMany
    {
        return $this->hasMany(EmployeeAdvanceRecovery::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function outstandingAmount(): float
    {
        return round(max(0, (float) $this->amount - (float) $this->recovered_amount), 2);
    }

    public function scopeRunning(Builder $query): Builder
    {
        return $query->whereRaw('employee_advances.amount > '.self::RECOVERED_SQL);
    }
}
