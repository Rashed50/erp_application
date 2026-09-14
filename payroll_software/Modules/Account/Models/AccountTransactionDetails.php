<?php

namespace Modules\Account\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountTransactionDetails extends Model
{
    use HasFactory;

    protected $table = 'account_transaction_details';

    protected $fillable = [
        'trd_id',
        'account_no',
        'debit',
        'credit',
        'particular'
    ];

    // Relationships
    public function transaction()
    {
        return $this->belongsTo(AccountTransaction::class, 'trd_id');
    }
}
