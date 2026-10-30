<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
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

    /**
     * @var array<int, string>
     */
    public const CHANGE_REASONS = ['Joining', 'Promotion', 'Demotion', 'Annual Increment', 'Performance Increment', 'Adjustment', 'Other'];

    /**
     * The amounts compared field by field between revisions.
     *
     * @var array<int, string>
     */
    public const AMOUNT_FIELDS = ['basic_salary', ...self::ALLOWANCES, 'overtime_rate', 'other_deduction'];

    /**
     * Reasons that come with a new designation.
     *
     * @var array<int, string>
     */
    public const DESIGNATION_CHANGE_REASONS = ['Promotion', 'Demotion'];

    public const CHANGE_INITIAL = 'Initial';

    public const CHANGE_INCREMENT = 'Increment';

    public const CHANGE_DECREMENT = 'Decrement';

    public const CHANGE_NONE = 'No Change';

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

    public function designation(): BelongsTo
    {
        return $this->belongsTo(Designation::class);
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
        return isset($this->salary_histories_count)
            ? $this->salary_histories_count > 0
            : $this->salaryHistories()->exists();
    }

    /**
     * Whether a later revision replaced this one. A replaced revision is the
     * employee's salary record of that time and is kept as it was.
     */
    public function isSuperseded(): bool
    {
        if ($this->relationLoaded('nextRevision')) {
            return $this->getRelation('nextRevision') !== null;
        }

        return self::query()
            ->where('employee_id', $this->employee_id)
            ->whereDate('effective_date', '>', $this->effective_date)
            ->exists();
    }

    public function isLocked(): bool
    {
        return $this->isUsedByPayroll() || $this->isSuperseded();
    }

    /**
     * Increment or decrement against the previous active revision, by
     * monthly gross. Null for an inactive revision or when the previous
     * revision is not linked.
     */
    public function changeType(): ?string
    {
        if (! $this->status || ! $this->relationLoaded('previousRevision')) {
            return null;
        }

        $previous = $this->getRelation('previousRevision');

        return match (true) {
            $previous === null => self::CHANGE_INITIAL,
            $this->monthly_gross > $previous->monthly_gross => self::CHANGE_INCREMENT,
            $this->monthly_gross < $previous->monthly_gross => self::CHANGE_DECREMENT,
            default => self::CHANGE_NONE,
        };
    }

    /**
     * Every amount that differs from the previous active revision, such as
     * a house rent raise inside an otherwise unchanged salary.
     *
     * @return array<int, array{field: string, previous: float, current: float, difference: float}>
     */
    public function fieldChanges(): array
    {
        $previous = $this->relationLoaded('previousRevision') ? $this->getRelation('previousRevision') : null;

        if (! $this->status || ! $previous) {
            return [];
        }

        return collect(self::AMOUNT_FIELDS)
            ->map(fn (string $field) => [
                'field' => $field,
                'previous' => (float) $previous->{$field},
                'current' => (float) $this->{$field},
                'difference' => round((float) $this->{$field} - (float) $previous->{$field}, 2),
            ])
            ->filter(fn (array $change) => $change['difference'] != 0)
            ->values()
            ->all();
    }

    /**
     * The revision's values as written to the edit log, with the designation
     * by name so the log still reads right if the designation is renamed or
     * removed later.
     *
     * @return array<string, mixed>
     */
    public function loggableValues(): array
    {
        return [
            'effective_date' => $this->effective_date?->toDateString(),
            ...collect(self::AMOUNT_FIELDS)->mapWithKeys(fn (string $field) => [$field => (float) $this->{$field}])->all(),
            'deduction_basis' => $this->deduction_basis,
            'change_reason' => $this->change_reason,
            'designation' => $this->designation_id ? Designation::query()->whereKey($this->designation_id)->value('name') : null,
            'status' => (bool) $this->status,
            'remarks' => $this->remarks,
        ];
    }

    public function logs(): HasMany
    {
        return $this->hasMany(SalaryDetailLog::class);
    }

    public function changeAmount(): ?float
    {
        $previous = $this->relationLoaded('previousRevision') ? $this->getRelation('previousRevision') : null;

        return $this->status && $previous ? round($this->monthly_gross - $previous->monthly_gross, 2) : null;
    }

    /**
     * Link each of one employee's revisions to the one before it (active
     * revisions only, as only those are paid) and the one after it, so the
     * change type and the lock can be read without more queries.
     *
     * @param  Collection<int, SalaryDetail>  $revisions
     * @return Collection<int, SalaryDetail>
     */
    public static function linkRevisions(Collection $revisions): Collection
    {
        $ordered = $revisions->sortBy(fn (SalaryDetail $revision) => [$revision->effective_date->toDateString(), $revision->id])->values();
        $previousActive = null;

        foreach ($ordered as $index => $revision) {
            $revision->setRelation('nextRevision', $ordered->get($index + 1));

            if ($revision->status) {
                $revision->setRelation('previousRevision', $previousActive);
                $previousActive = $revision;
            }
        }

        return $revisions;
    }

    /**
     * How many times the salary went up and down.
     *
     * @param  Collection<int, SalaryDetail>  $revisions  revisions linked by linkRevisions()
     * @return array{revisions: int, increments: int, decrements: int}
     */
    public static function changeSummary(Collection $revisions): array
    {
        $types = $revisions->map(fn (SalaryDetail $revision) => $revision->changeType());

        return [
            'revisions' => $revisions->count(),
            'increments' => $types->filter(fn (?string $type) => $type === self::CHANGE_INCREMENT)->count(),
            'decrements' => $types->filter(fn (?string $type) => $type === self::CHANGE_DECREMENT)->count(),
        ];
    }
}
