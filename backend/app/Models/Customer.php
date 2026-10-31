<?php

namespace App\Models;

use App\Models\Concerns\Approvable;
use App\Models\Concerns\RecordsDeleter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Customer extends Model
{
    use Approvable, HasFactory, RecordsDeleter, SoftDeletes;

    /**
     * The active ledger entries' net movement of a customers row, in SQL.
     */
    public const MOVEMENT_SQL = '(select coalesce(sum(customer_transactions.debit - customer_transactions.credit), 0) from customer_transactions where customer_transactions.customer_id = customers.id and customer_transactions.status = 1)';

    protected $guarded = [];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'opening_date' => 'date',
            'opening_balance' => 'decimal:2',
            'active_status' => 'boolean',
        ];
    }

    /**
     * What the customer owes: the opening balance plus every active ledger
     * entry's debit less its credit. From withCurrentBalance() when the
     * query added it, else queried.
     */
    protected function currentBalance(): Attribute
    {
        return Attribute::get(fn (mixed $value) => round((float) ($value
            ?? (float) $this->opening_balance + (float) $this->transactions()->where('status', true)->sum(DB::raw('debit - credit'))), 2));
    }

    public function scopeWithCurrentBalance(Builder $query): Builder
    {
        if (is_null($query->getQuery()->columns)) {
            $query->select('customers.*');
        }

        return $query->selectRaw('customers.opening_balance + '.self::MOVEMENT_SQL.' as current_balance');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(CustomerTransaction::class);
    }

    public function workOrders(): HasMany
    {
        return $this->hasMany(WorkOrder::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active_status', true);
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function (Builder $query) use ($search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('vat_no', 'like', "%{$search}%");
        });
    }

    /**
     * A customer can only be removed once it has no ledger history, otherwise
     * deleting it would silently erase accounting records.
     */
    public function canBeDeleted(): bool
    {
        return ! $this->transactions()->exists();
    }

    /**
     * Soft-deleting a customer would leave its work orders pointing at a
     * customer that no longer shows up anywhere.
     */
    public function hasWorkOrders(): bool
    {
        return $this->workOrders()->exists();
    }
}
