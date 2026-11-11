<?php

namespace App\Models;

use App\Models\Concerns\Approvable;
use App\Models\Concerns\HasLineItemTotals;
use App\Models\Concerns\RecordsDeleter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
{
    use Approvable, HasFactory, HasLineItemTotals, RecordsDeleter, SoftDeletes;

    protected $guarded = [];

    protected static function lineItemsTable(): array
    {
        return ['purchase_items', 'purchase_id'];
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
            'purchase_date' => 'date',
            'overall_discount' => 'decimal:2',
            'overall_vat' => 'decimal:2',
        ];
    }

    /**
     * Bill payments made against the purchase.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(SupplierPayment::class);
    }

    public function scopeWithPaidAmount(Builder $query): Builder
    {
        return $query->withSum('payments as paid_amount', 'bill_amount');
    }

    /**
     * From withPaidAmount() when the query added it, else queried.
     */
    protected function paidAmount(): Attribute
    {
        return Attribute::get(fn (mixed $value) => round((float) ($value ?? $this->payments()->sum('bill_amount')), 2));
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class)->withTrashed();
    }

    /**
     * The employee who made the purchase (not the user who entered it).
     */
    public function purchasedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'purchased_by_id')->withTrashed();
    }

    public function ledgerTransaction(): BelongsTo
    {
        return $this->belongsTo(SupplierTransaction::class, 'supplier_transaction_id');
    }

    public function debitAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'debit_account_id');
    }

    public function creditAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'credit_account_id');
    }

    /**
     * The journal entry this purchase posted to the general ledger.
     */
    public function journalEntry(): MorphOne
    {
        return $this->morphOne(AccountTransaction::class, 'source');
    }

    public function items(): HasMany
    {
        // Product lines are named by their product.
        return $this->hasMany(PurchaseItem::class)->with('product:id,name');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getDueAmountAttribute(): float
    {
        return (float) $this->net_total - (float) $this->paid_amount;
    }
}
