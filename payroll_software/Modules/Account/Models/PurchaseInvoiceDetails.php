<?php

namespace Modules\Account\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Inventory\item_name;
use Modules\Account\Models\PurchaseInvoice;

class PurchaseInvoiceDetails extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'purchase_invoice_details';
    protected $primaryKey = 'pur_det_id';

    protected $fillable = [
        'purchase_id',
        'item_id', 
        'service_name', 
        'description', 
        'qty',
        'unit_price',
        'discount',
        'vat',
        'total_amount'
    ];


    public function purchase()
    {
        return $this->belongsTo(PurchaseInvoice::class, 'purchase_id');
    }

    public function product()
    {
        return $this->belongsTo(item_name::class, 'item_id');
    }
}
