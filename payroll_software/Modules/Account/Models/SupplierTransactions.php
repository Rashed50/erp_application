<?php

namespace Modules\Account\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Account\Models\SupplierLedger;

class SupplierTransactions extends Model
{
    use HasFactory;//, SoftDeletes;
    protected $table = 'suplier_transactions';
    protected $primaryKey = 'suptran_id';
    protected $fillable = ['suptran_id','transaction_id','supplier_id','transaction_type','invoice_no','debit','credit','transaction_date','notes','created_by'];


    public function supplier()
    {
        return $this->belongsTo(SupplierLedger::class, 'supplier_id', 'supplier_id');
    }
}
