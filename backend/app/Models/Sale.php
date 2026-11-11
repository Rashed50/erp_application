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

class Sale extends Model
{
    use Approvable, HasFactory, HasLineItemTotals, RecordsDeleter, SoftDeletes;

    protected $guarded = [];

    protected static function lineItemsTable(): array
    {
        return ['sale_items', 'sale_id'];
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
            'due_date' => 'date',
            'overall_discount' => 'decimal:2',
            'overall_vat' => 'decimal:2',
        ];
    }

    /**
     * Payments received against the invoice: the customer's active
     * "Payment Received" ledger entries carrying its invoice number.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(CustomerTransaction::class, 'invoice_no', 'invoice_number')
            ->where('transaction_type', CustomerTransaction::TYPE_PAYMENT_RECEIVED)
            ->where('status', true);
    }

    public function scopeWithPaidAmount(Builder $query): Builder
    {
        return $query->withSum(['payments as paid_amount' => fn (Builder $query) => $query
            ->whereColumn('customer_transactions.customer_id', 'sales.customer_id')], 'credit');
    }

    /**
     * From withPaidAmount() when the query added it, else queried.
     */
    protected function paidAmount(): Attribute
    {
        return Attribute::get(fn (mixed $value) => round((float) ($value
            ?? $this->payments()->where('customer_id', $this->customer_id)->sum('credit')), 2));
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class)->withTrashed();
    }

    public function ledgerTransaction(): BelongsTo
    {
        return $this->belongsTo(CustomerTransaction::class, 'customer_transaction_id');
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
     * The journal entry this sale posted to the general ledger.
     */
    public function journalEntry(): MorphOne
    {
        return $this->morphOne(AccountTransaction::class, 'source');
    }

    public function items(): HasMany
    {
        // Product lines are named by their product.
        return $this->hasMany(SaleItem::class)->with('product:id,name');
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
