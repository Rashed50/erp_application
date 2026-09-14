<?php

namespace Modules\Account\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CustomerTransaction extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'customer_transactions';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'custran_id';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * The data type of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'transaction_id',
        'customer_id',
        'transaction_type',
        'invoice_no',
        'debit',
        'credit',
        'transaction_date',
        'notes',
        'created_by',
        'updated_by',
        'branch_office_id',
        'ct_status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'custran_id' => 'integer',
        'transaction_id' => 'integer',
        'customer_id' => 'integer',
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
        'transaction_date' => 'date',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'branch_office_id' => 'integer',
        'ct_status' => 'boolean',
    ];

    /**
     * Default attribute values.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'debit' => 0,
        'credit' => 0,
        'branch_office_id' => 1,
        'ct_status' => true,
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array<int, string>
     */
    protected $dates = [
        'transaction_date',
        'created_at',
        'updated_at',
    ];

    /**
     * Get the customer associated with the transaction.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(CustomerLedger::class, 'customer_id', 'customer_id');
    }

    /**
     * Get the main transaction record.
     */
    public function accountTransaction(): BelongsTo
    {
        return $this->belongsTo(AccountTransaction::class, 'transaction_id', 'id');
    }

    /**
     * Get the branch office for the transaction.
     */
    public function branchOffice(): BelongsTo
    {
        return $this->belongsTo(BranchOffice::class, 'branch_office_id');
    }

    /**
     * Get the creator user.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the updater user.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope a query to only include active transactions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('ct_status', true);
    }

    /**
     * Scope a query to only include inactive transactions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactive($query)
    {
        return $query->where('ct_status', false);
    }

    /**
     * Scope a query to filter by customer.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $customerId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    /**
     * Scope a query to filter by branch office.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $branchOfficeId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByBranch($query, $branchOfficeId)
    {
        return $query->where('branch_office_id', $branchOfficeId);
    }

    /**
     * Scope a query to filter by transaction type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByType($query, $type)
    {
        return $query->where('transaction_type', $type);
    }

    /**
     * Scope a query to filter by transaction date range.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $fromDate
     * @param  string  $toDate
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDateRange($query, $fromDate, $toDate = null)
    {
        if (!$toDate) {
            $toDate = $fromDate;
        }

        return $query->whereBetween('transaction_date', [$fromDate, $toDate]);
    }

    /**
     * Scope a query to filter by invoice number.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $invoiceNo
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByInvoice($query, $invoiceNo)
    {
        return $query->where('invoice_no', $invoiceNo);
    }

    /**
     * Scope a query to include only debit transactions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDebitOnly($query)
    {
        return $query->where('debit', '>', 0);
    }

    /**
     * Scope a query to include only credit transactions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCreditOnly($query)
    {
        return $query->where('credit', '>', 0);
    }

    /**
     * Scope a query to search transactions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('invoice_no', 'like', "%{$search}%")
              ->orWhere('transaction_type', 'like', "%{$search}%")
              ->orWhere('notes', 'like', "%{$search}%")
              ->orWhereHas('customer', function ($customerQuery) use ($search) {
                  $customerQuery->where('customer_name', 'like', "%{$search}%")
                                ->orWhere('customer_email', 'like', "%{$search}%");
              });
        });
    }

    /**
     * Get the transaction amount (positive for credit, negative for debit).
     *
     * @return float
     */
    public function getAmountAttribute(): float
    {
        if ($this->debit > 0) {
            return -$this->debit; // Negative for debit
        }

        return $this->credit; // Positive for credit
    }

    /**
     * Get the absolute amount (always positive).
     *
     * @return float
     */
    public function getAbsoluteAmountAttribute(): float
    {
        return max($this->debit, $this->credit);
    }

    /**
     * Get the transaction type with amount sign.
     *
     * @return string
     */
    public function getTypeWithAmountAttribute(): string
    {
        if ($this->debit > 0) {
            return "Debit: {$this->formatted_debit}";
        }

        return "Credit: {$this->formatted_credit}";
    }

    /**
     * Get formatted debit amount.
     *
     * @return string|null
     */
    public function getFormattedDebitAttribute(): ?string
    {
        return $this->debit > 0 ? number_format($this->debit, 2) : null;
    }

    /**
     * Get formatted credit amount.
     *
     * @return string|null
     */
    public function getFormattedCreditAttribute(): ?string
    {
        return $this->credit > 0 ? number_format($this->credit, 2) : null;
    }

    /**
     * Get the net effect of the transaction.
     *
     * @return string
     */
    public function getNetEffectAttribute(): string
    {
        if ($this->debit > 0) {
            return 'Outflow';
        } elseif ($this->credit > 0) {
            return 'Inflow';
        }

        return 'Neutral';
    }

    /**
     * Check if transaction is a debit.
     *
     * @return bool
     */
    public function getIsDebitAttribute(): bool
    {
        return $this->debit > 0;
    }

    /**
     * Check if transaction is a credit.
     *
     * @return bool
     */
    public function getIsCreditAttribute(): bool
    {
        return $this->credit > 0;
    }

    /**
     * Check if transaction is balanced (both zero).
     *
     * @return bool
     */
    public function getIsBalancedAttribute(): bool
    {
        return $this->debit == 0 && $this->credit == 0;
    }

    /**
     * Update customer balance when transaction is created.
     *
     * @return void
     */
    public function updateCustomerBalance(): void
    {
        if ($this->customer && $this->customer->exists) {
            if ($this->is_debit) {
                $this->customer->updateBalance($this->debit, 'debit');
            } elseif ($this->is_credit) {
                $this->customer->updateBalance($this->credit, 'credit');
            }
        }
    }

    /**
     * Reverse the transaction effect.
     *
     * @return bool
     */
    public function reverse(): bool
    {
        if ($this->customer && $this->customer->exists) {
            // Reverse the balance effect
            if ($this->is_debit) {
                $this->customer->updateBalance($this->debit, 'credit');
            } elseif ($this->is_credit) {
                $this->customer->updateBalance($this->credit, 'debit');
            }

            // Mark transaction as reversed
            $this->ct_status = false;
            $this->notes = ($this->notes ? $this->notes . "\n" : '') . 'Reversed on ' . now()->toDateString();
            return $this->save();
        }

        return false;
    }

    /**
     * Get the running balance up to this transaction.
     *
     * @return float|null
     */
    public function getRunningBalanceAttribute(): ?float
    {
        if (!$this->customer_id) {
            return null;
        }

        // Get all transactions up to this date
        $balance = $this->customer->transactions()
            ->where('transaction_date', '<', $this->transaction_date)
            ->orWhere(function ($query) {
                $query->where('transaction_date', $this->transaction_date)
                      ->where('custran_id', '<=', $this->custran_id);
            })
            ->selectRaw('SUM(credit - debit) as balance')
            ->value('balance');

        return (float) $balance;
    }

    /**
     * Check if transaction can be edited.
     *
     * @return bool
     */
    public function getCanEditAttribute(): bool
    {
        // Example: Only allow editing if transaction is recent (within 7 days)
        return $this->created_at->diffInDays(now()) <= 7 && $this->ct_status;
    }

    /**
     * Check if transaction can be deleted.
     *
     * @return bool
     */
    public function getCanDeleteAttribute(): bool
    {
        // Example: Only allow deletion if transaction is recent and not reversed
        return $this->created_at->diffInDays(now()) <= 2 && $this->ct_status;
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($transaction) {
            // Set created_by if not already set
            if (empty($transaction->created_by) && auth()->check()) {
                $transaction->created_by = auth()->id();
            }

            // Set transaction date to today if not set
            if (empty($transaction->transaction_date)) {
                $transaction->transaction_date = now();
            }

            // Validate debit/credit
            if ($transaction->debit > 0 && $transaction->credit > 0) {
                throw new \Exception('Transaction cannot have both debit and credit amounts.');
            }
        });

        static::created(function ($transaction) {
            // Update customer balance when transaction is created
            $transaction->updateCustomerBalance();
        });

        static::updating(function ($transaction) {
            // Set updated_by if not already set
            if (empty($transaction->updated_by) && auth()->check()) {
                $transaction->updated_by = auth()->id();
            }

            // Prevent updating if transaction is reversed
            if (!$transaction->getOriginal('ct_status') && $transaction->ct_status) {
                throw new \Exception('Cannot update a reversed transaction.');
            }
        });

        static::deleting(function ($transaction) {
            // Prevent deletion if certain conditions are met
            if (!$transaction->can_delete) {
                throw new \Exception('This transaction cannot be deleted.');
            }

            // Reverse the balance effect before deletion
            if ($transaction->is_debit) {
                $transaction->customer->updateBalance($transaction->debit, 'credit');
            } elseif ($transaction->is_credit) {
                $transaction->customer->updateBalance($transaction->credit, 'debit');
            }
        });
    }
}
