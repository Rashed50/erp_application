<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SupplierTransaction extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'transaction_date' => 'date',
            'debit' => 'decimal:2',
            'credit' => 'decimal:2',
            'status' => 'boolean',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * The cash/bank account a bill payment recorded on the ledger left from.
     */
    public function paymentAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'payment_account_id');
    }

    /**
     * The supplier payment that posted this entry, if any.
     */
    public function supplierPayment(): HasOne
    {
        return $this->hasOne(SupplierPayment::class, 'supplier_transaction_id');
    }

    /**
     * The journal entry this ledger entry corresponds to.
     */
    public function accountTransaction(): BelongsTo
    {
        return $this->belongsTo(AccountTransaction::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
