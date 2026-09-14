<?php

namespace App\Models\AccountsModule;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrVoucher extends Model
{
    use HasFactory;
    protected $table = "cr_vouchers";
    protected $primaryKey = 'cr_vou_auto_id';
    protected $guarded = [];

    protected $fillable = [
        'CrTypeId',
        'TransactionId',
        'CrTypeId',
        'receipt_number',
        'ReceivedDate',
        'Amount',
        'DebitedTold',
        'CreditedFromId',
        'Remarks',
        'CreateById',
        'ReceiveMethod',
        'BankId',
        'cr_invoice_path',
    ];



    public function bankInfo()
    {
        return $this->belongsTo(\App\Models\BankName::class, 'BankId', 'bn_auto_id');
    }


    public function createdBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'CreateById', 'id');
    }

}
