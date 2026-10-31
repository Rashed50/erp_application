<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One add, edit or delete of a salary revision. `changes` maps each changed
 * field to its old and new value: {"basic_salary": {"old": 30000, "new": 35000}}.
 */
class SalaryDetailLog extends Model
{
    public const ACTION_CREATED = 'Created';

    public const ACTION_UPDATED = 'Updated';

    public const ACTION_DELETED = 'Deleted';

    public const UPDATED_AT = null;

    protected $guarded = [];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'changes' => 'array',
        ];
    }

    public function salaryDetail(): BelongsTo
    {
        return $this->belongsTo(SalaryDetail::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
