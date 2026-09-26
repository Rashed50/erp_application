<?php

namespace App\Models;

use App\Models\Concerns\RecordsDeleter;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, RecordsDeleter, SoftDeletes;

    /**
     * @var array<int, string>
     */
    public const STATUSES = ['Active', 'Inactive', 'Resigned', 'Terminated'];

    /**
     * @var array<int, string>
     */
    public const EMPLOYMENT_TYPES = ['Permanent', 'Probation', 'Contract', 'Part-time'];

    /**
     * @var array<int, string>
     */
    public const GENDERS = ['Male', 'Female', 'Other'];

    protected $table = 'employee_info';

    protected $guarded = [];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'joining_date' => 'date',
            'last_working_date' => 'date',
        ];
    }

    public function detail(): HasOne
    {
        return $this->hasOne(EmployeeDetail::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(EmployeeFile::class);
    }

    public function salaryDetails(): HasMany
    {
        return $this->hasMany(SalaryDetail::class);
    }

    public function works(): HasMany
    {
        return $this->hasMany(EmployeeWork::class);
    }

    public function salaryHistories(): HasMany
    {
        return $this->hasMany(SalaryHistory::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * The salary configuration in force for a month: the latest active
     * revision effective on or before the month's last day.
     */
    public function salaryDetailFor(CarbonInterface $month): ?SalaryDetail
    {
        return $this->salaryDetails()
            ->where('status', true)
            ->whereDate('effective_date', '<=', $month->copy()->endOfMonth())
            ->orderByDesc('effective_date')
            ->first();
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function (Builder $query) use ($search) {
            $query->where('employee_code', 'like', "%{$search}%")
                ->orWhere('name', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        });
    }

    /**
     * Employees who belong on a month's payroll: joined on or before the
     * month's end, and either still active or with a last working date inside
     * or after the month.
     */
    public function scopePayrollEligible(Builder $query, CarbonInterface $month): Builder
    {
        $start = $month->copy()->startOfMonth()->toDateString();
        $end = $month->copy()->endOfMonth()->toDateString();

        return $query
            ->whereDate('joining_date', '<=', $end)
            ->where(function (Builder $query) use ($start) {
                $query->where(function (Builder $query) {
                    $query->where('status', 'Active')->whereNull('last_working_date');
                })->orWhereDate('last_working_date', '>=', $start);
            });
    }

    /**
     * Whether the employee was employed for at least one day of the month.
     */
    public function isEmployedDuring(CarbonInterface $month): bool
    {
        return $this->joining_date->lte($month->copy()->endOfMonth())
            && (! $this->last_working_date || $this->last_working_date->gte($month->copy()->startOfMonth()));
    }
}
