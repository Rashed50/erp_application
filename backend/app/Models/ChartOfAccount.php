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

class ChartOfAccount extends Model
{
    use Approvable, HasFactory, RecordsDeleter, SoftDeletes;

    protected $guarded = [];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'opening_date' => 'date',
            'balance' => 'decimal:2',
            'active_status' => 'boolean',
            'is_transaction' => 'boolean',
            'is_predefined' => 'boolean',
            'is_closed' => 'boolean',
        ];
    }

    public function accountType(): BelongsTo
    {
        return $this->belongsTo(AccountType::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
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

    public function scopeOfType(Builder $query, int $accountTypeId): Builder
    {
        return $query->where('account_type_id', $accountTypeId);
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function (Builder $query) use ($search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('account_number', 'like', "%{$search}%")
                ->orWhereHas('accountType', fn (Builder $query) => $query->where('name', 'like', "%{$search}%"));
        });
    }

    /**
     * Entries can only be posted to an open, active transaction account;
     * group accounts exist purely to roll their children up.
     */
    public function canReceiveEntries(): bool
    {
        return $this->is_transaction && $this->active_status && ! $this->is_closed;
    }

    /**
     * Every account below this one, used to stop an account being moved
     * underneath its own descendant.
     *
     * @return array<int, int>
     */
    public function descendantIds(): array
    {
        $ids = [];
        $frontier = [$this->id];

        while ($frontier !== []) {
            $frontier = self::query()->whereIn('parent_id', $frontier)->pluck('id')->all();
            $ids = [...$ids, ...$frontier];
        }

        return $ids;
    }

    /**
     * An account can only be removed once it is not predefined, has no child
     * accounts, and no entry references it from either side, otherwise
     * deleting it would silently erase accounting records.
     */
    public function canBeDeleted(): bool
    {
        return ! $this->is_predefined
            && ! $this->children()->exists()
            && ! $this->categoryEntries()->exists()
            && ! $this->paymentEntries()->exists();
    }
}
