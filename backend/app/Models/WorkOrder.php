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

class WorkOrder extends Model
{
    use Approvable, HasFactory, RecordsDeleter, SoftDeletes;

    /**
     * @var array<int, string>
     */
    public const STATUSES = ['Pending', 'In Progress', 'Completed', 'Cancelled'];

    protected $guarded = [];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
            'deliver_date' => 'date',
            'total_amount' => 'decimal:2',
            'retention_percent' => 'decimal:2',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(CustomerTransaction::class);
    }

    /**
     * Payments received against this work order: active (non-reversed)
     * "Payment Received" entries in the customer ledger.
     */
    public function payments(): HasMany
    {
        return $this->transactions()->where('status', true)->where('transaction_type', 'Payment Received');
    }

    /**
     * Adds `paid_amount` and `payments_count` aggregates for the resource.
     */
    public function scopeWithPaymentTotals(Builder $query): Builder
    {
        return $query->withSum('payments as paid_amount', 'credit')->withCount('payments');
    }

    public function loadPaymentTotals(): static
    {
        return $this->loadSum('payments as paid_amount', 'credit')->loadCount('payments');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function (Builder $query) use ($search) {
            $query->where('work_order_no', 'like', "%{$search}%")
                ->orWhere('work_title', 'like', "%{$search}%");
        });
    }

    public function getRetentionAmountAttribute(): float
    {
        return round((float) $this->total_amount * (float) $this->retention_percent / 100, 2);
    }
}
