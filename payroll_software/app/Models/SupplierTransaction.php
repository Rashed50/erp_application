<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierTransaction extends Model
{
    use HasFactory;

    protected $table = 'suplier_transactions';

    protected $primaryKey = 'suptran_id';

    protected $fillable = [
        'supplier_id',
        'invoice_no',
        'transaction_date',
        'transaction_type',
        'debit',
        'credit',
        // add other columns as needed
    ];

    public function supplier()
    {
        return $this->belongsTo(InventorySupplier::class, 'supplier_id', 'isupp_auto_id');
    }
}
