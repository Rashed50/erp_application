<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AccountType extends Model
{
    public const ASSET = 1;

    public const LIABILITY = 2;

    public const OWNER_EQUITY = 3;

    public const REVENUE = 4;

    public const EXPENSE = 5;

    protected $guarded = [];

    public function accounts(): HasMany
    {
        return $this->hasMany(ChartOfAccount::class);
    }

    public function increasesOnDebit(): bool
    {
        return $this->normal_balance === 'debit';
    }
}
