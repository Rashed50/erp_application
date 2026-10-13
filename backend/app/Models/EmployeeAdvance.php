<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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
            'recovered_amount' => 'decimal:2',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class)->withTrashed();
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
        return $query->where('status', self::STATUS_RUNNING);
    }
}
