<?php

namespace App\Models;

use App\Models\Concerns\Approvable;
use App\Models\Concerns\RecordsDeleter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FundTransfer extends Model
{
    use Approvable, HasFactory, RecordsDeleter, SoftDeletes;

    protected $guarded = [];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'transfer_date' => 'date',
            'amount' => 'decimal:2',
            'bank_charge' => 'decimal:2',
            'vat' => 'decimal:2',
            'total_amount' => 'decimal:2',
        ];
    }

    /**
     * The sender account the money leaves from.
     */
    public function creditAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'credit_account_id');
    }

    /**
     * The receiver account the money arrives in.
     */
    public function debitAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'debit_account_id');
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
     * Bank charge plus VAT, posted together to the Bank Charges expense account.
     */
    public function charges(): float
    {
        return (float) $this->bank_charge + (float) $this->vat;
    }
}
