<?php

namespace App\Models;

use App\Models\Concerns\Approvable;
use App\Models\Concerns\RecordsDeleter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;


class IncomeExpenseAccount extends Model
{
    use Approvable, HasFactory, RecordsDeleter, SoftDeletes;

    protected $guarded = [];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'active_status' => 'boolean',
        ];
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
     * Entries where this account is the income/expense category side.
     */
    public function categoryEntries(): HasMany
    {
        return $this->hasMany(IncomeExpenseTransaction::class, 'income_expense_account_id');
    }

    /**
     * Entries where this account is the payment (asset) side.
     */
    public function paymentEntries(): HasMany
    {
        return $this->hasMany(IncomeExpenseTransaction::class, 'payment_account_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active_status', true);
    }

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    /**
     * An account can only be removed once no entry references it from
     * either side, otherwise deleting it would silently erase accounting
     * records.
     */
    public function canBeDeleted(): bool
    {
        return ! $this->categoryEntries()->exists() && ! $this->paymentEntries()->exists();
    }
}
