<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncomeExpenseTransaction extends Model
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
            'amount' => 'decimal:2',
        ];
    }

    /**
     * The income/expense category side of this double entry.
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(IncomeExpenseAccount::class, 'income_expense_account_id');
    }

    /**
     * The asset (cash/bank) side of this double entry.
     */
    public function paymentAccount(): BelongsTo
    {
        return $this->belongsTo(IncomeExpenseAccount::class, 'payment_account_id');
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
