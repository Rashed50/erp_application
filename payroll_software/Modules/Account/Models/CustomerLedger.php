<?php

namespace Modules\Account\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerLedger extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'customer_subsidiary_ledger';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'customer_id';

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
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_address',
        'vat_no',
        'payment_term',
        'contact_person',
        'contact_person_phone',
        'contact_person_email',
        'country',
        'opening_date',
        'opening_balance',
        'current_balance',
        'active_status',
        'created_by',
        'updated_by',
        'branch_office_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'customer_id' => 'integer',
        'payment_term' => 'integer',
        'opening_date' => 'date',
        'current_balance' => 'decimal:2',
        'active_status' => 'boolean',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'branch_office_id' => 'integer',
    ];

    /**
     * Default attribute values.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'payment_term' => 20,
        'current_balance' => 0,
        'active_status' => true,
        'branch_office_id' => 1,
    ];

    /**
     * Get the transactions for the customer.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(CustomerTransaction::class, 'customer_id', 'customer_id');
    }

    /**
     * Get the branch office for the customer.
     */
    public function branchOffice()
    {
        return $this->belongsTo(BranchOffice::class, 'branch_office_id');
    }

    /**
     * Get the creator user.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the updater user.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope a query to only include active customers.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('active_status', true);
    }

    /**
     * Scope a query to only include inactive customers.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactive($query)
    {
        return $query->where('active_status', false);
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
     * Scope a query to search customers.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('customer_name', 'like', "%{$search}%")
              ->orWhere('customer_email', 'like', "%{$search}%")
              ->orWhere('customer_phone', 'like', "%{$search}%")
              ->orWhere('vat_no', 'like', "%{$search}%");
        });
    }

    /**
     * Get the total debit amount from all transactions.
     *
     * @return float
     */
    public function getTotalDebitAttribute(): float
    {
        return $this->transactions()->sum('debit');
    }

    /**
     * Get the total credit amount from all transactions.
     *
     * @return float
     */
    public function getTotalCreditAttribute(): float
    {
        return $this->transactions()->sum('credit');
    }

    /**
     * Get the net balance (credit - debit).
     *
     * @return float
     */
    public function getNetBalanceAttribute(): float
    {
        return $this->total_credit - $this->total_debit;
    }

    /**
     * Check if customer has overdue payments.
     *
     * @return bool
     */
    public function getHasOverdueAttribute(): bool
    {
        // Assuming you have a payment_term field in days
        // You might need to adjust this based on your business logic
        $overdueDate = now()->subDays($this->payment_term);

        return $this->transactions()
            ->where('transaction_type', 'invoice')
            ->whereDate('transaction_date', '<=', $overdueDate)
            ->exists();
    }

    /**
     * Get customer's full address.
     *
     * @return string|null
     */
    public function getFullAddressAttribute(): ?string
    {
        $parts = [];

        if ($this->customer_address) {
            $parts[] = $this->customer_address;
        }

        if ($this->country) {
            $parts[] = $this->country;
        }

        return $parts ? implode(', ', $parts) : null;
    }

    /**
     * Get contact information.
     *
     * @return string|null
     */
    public function getContactInfoAttribute(): ?string
    {
        $contacts = [];

        if ($this->contact_person) {
            $contacts[] = "Contact: {$this->contact_person}";
        }

        if ($this->contact_person_phone) {
            $contacts[] = "Phone: {$this->contact_person_phone}";
        }

        if ($this->contact_person_email) {
            $contacts[] = "Email: {$this->contact_person_email}";
        }

        return $contacts ? implode(', ', $contacts) : null;
    }

    /**
     * Update the current balance.
     *
     * @param  float  $amount
     * @param  string  $type  'debit' or 'credit'
     * @return void
     */
    public function updateBalance(float $amount, string $type = 'credit'): void
    {
        if ($type === 'debit') {
            $this->current_balance -= $amount;
        } else {
            $this->current_balance += $amount;
        }

        $this->save();
    }

    /**
     * Check if customer can be deleted.
     *
     * @return bool
     */
    public function canBeDeleted(): bool
    {
        // Check if customer has any transactions
        return !$this->transactions()->exists();
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($customer) {
            // Set created_by if not already set
            if (empty($customer->created_by) && auth()->check()) {
                $customer->created_by = auth()->id();
            }
        });

        static::updating(function ($customer) {
            // Set updated_by if not already set
            if (empty($customer->updated_by) && auth()->check()) {
                $customer->updated_by = auth()->id();
            }
        });

        static::deleting(function ($customer) {
            // Prevent deletion if customer has transactions
            if ($customer->transactions()->exists()) {
                throw new \Exception('Cannot delete customer with existing transactions.');
            }
        });
    }
}
