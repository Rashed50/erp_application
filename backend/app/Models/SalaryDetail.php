<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalaryDetail extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    public const DEDUCTION_BASES = ['basic', 'gross'];

    /**
     * @var array<int, string>
     */
    public const ALLOWANCES = ['house_rent', 'medical_allowance', 'transport_allowance', 'food_allowance', 'other_allowance'];

    protected $guarded = [];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'effective_date' => 'date',
            'basic_salary' => 'decimal:2',
            'house_rent' => 'decimal:2',
            'medical_allowance' => 'decimal:2',
            'transport_allowance' => 'decimal:2',
            'food_allowance' => 'decimal:2',
            'other_allowance' => 'decimal:2',
            'overtime_rate' => 'decimal:2',
            'other_deduction' => 'decimal:2',
            'status' => 'boolean',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function salaryHistories(): HasMany
    {
        return $this->hasMany(SalaryHistory::class);
    }

    public function getTotalAllowanceAttribute(): float
    {
        return round(collect(self::ALLOWANCES)->sum(fn (string $column) => (float) $this->{$column}), 2);
    }

    /**
     * Basic + allowances for a full month, before overtime and deductions.
     */
    public function getMonthlyGrossAttribute(): float
    {
        return round((float) $this->basic_salary + $this->total_allowance, 2);
    }

    /**
     * A revision already used by a generated salary stays as it was, so
     * history can always be traced back to it; changes need a new revision.
     */
    public function isUsedByPayroll(): bool
    {
        return $this->salaryHistories()->exists();
    }
}
