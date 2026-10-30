<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * A journal entry header, as in the payroll_software Account module's
 * `account_transaction` table. Its debit and credit lines always balance.
 */
class AccountTransaction extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    /**
     * The entry's size: the sum of its debit lines (they equal its credits).
     */
    protected function totalAmount(): Attribute
    {
        return Attribute::get(fn () => round((float) ($this->relationLoaded('details') ? $this->details->sum('debit') : $this->details()->sum('debit')), 2));
    }

    public function details(): HasMany
    {
        return $this->hasMany(AccountTransactionDetail::class);
    }

    /**
     * The record whose posting created this entry.
     */
    public function source(): MorphTo
    {
        return $this->morphTo();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
