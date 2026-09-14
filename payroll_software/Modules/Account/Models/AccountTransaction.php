<?php

namespace Modules\Account\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class AccountTransaction extends Model
{
    use HasFactory;
    // public $timestamps = false;

    protected $table = 'account_transaction';

    protected $fillable = [
        'tr_no',
        'date',
        'total_amount',
        'general_particular',
        'sales_id',
        'purchase_invoice_id',
        'approve_by'
    ];

    protected $casts = [
        'date' => 'date',
        'sales_id' => 'integer',
        'approve_by' => 'integer',
    ];


    // Define any relationships,
    public function salesRecord()
    {
        return $this->belongsTo(ChartofaccSalesRecord::class, 'sales_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'approve_by');
    }

    public function transactionDetails()
    {
        return $this->hasMany(AccountTransactionDetails::class, 'trd_id');
    }
}
