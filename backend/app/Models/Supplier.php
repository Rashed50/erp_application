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

class Supplier extends Model
{
    use Approvable, HasFactory, RecordsDeleter, SoftDeletes;

    /**
     * The active ledger entries' net movement of a suppliers row, in SQL.
     */
    public const MOVEMENT_SQL = '(select coalesce(sum(supplier_transactions.credit - supplier_transactions.debit), 0) from supplier_transactions where supplier_transactions.supplier_id = suppliers.id and supplier_transactions.status = 1)';

    protected $guarded = [];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'opening_balance' => 'decimal:2',
            'active_status' => 'boolean',
        ];
    }

    /**
     * What is owed to the supplier: the opening balance plus every active
     * ledger entry's credit less its debit. From withCurrentBalance() when
     * the query added it, else queried.
     */
    protected function currentBalance(): Attribute
    {
        return Attribute::get(fn (mixed $value) => round((float) ($value
            ?? (float) $this->opening_balance + (float) $this->transactions()->where('status', true)->sum(DB::raw('credit - debit'))), 2));
    }

    public function scopeWithCurrentBalance(Builder $query): Builder
    {
        if (is_null($query->getQuery()->columns)) {
            $query->select('suppliers.*');
        }

        return $query->selectRaw('suppliers.opening_balance + '.self::MOVEMENT_SQL.' as current_balance');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(SupplierTransaction::class);
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
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
     * A supplier can only be removed once it has no ledger or purchase history,
     * otherwise deleting it would silently erase accounting records.
     */
    public function canBeDeleted(): bool
    {
        return ! $this->transactions()->exists() && ! $this->purchases()->exists();
    }
}
