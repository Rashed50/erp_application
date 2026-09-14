<?php

namespace App\Models\AccountsModule;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChartOfAccounts extends Model
{
    use HasFactory;

    protected $table = 'chart_of_accounts';
    protected $primaryKey = 'chart_of_acct_id';

    protected $fillable = [
        'chart_of_acct_id',
        'chart_of_acct_name',
        'chart_of_acct_number',
        'account_id',
        'parent_id',
        'acct_balance',
        'opening_date',
        'active_status',
        'is_transaction',
        'is_predefined',
        'acct_type_id',
        'created_by_id',
        'updated_by_id',
        'is_closed',
        'created_at',
        'updated_at'
    ];


    public function chartofAccountType()
    {
        return $this->belongsTo(ChartofAccountType::class, 'acct_type_id', 'acct_type_id');
    }

    public function createdUser()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    /**
     * Get the parent account
     */
    public function parentAccount()
    {
        return $this->belongsTo(ChartOfAccounts::class, 'parent_id', 'chart_of_acct_id');
    }

    /**
     * Get the sub-accounts
     */
    public function subAccounts(): HasMany
    {
        return $this->hasMany(ChartOfAccounts::class, 'account_id', 'chart_of_acct_id ');
    }

 

}
