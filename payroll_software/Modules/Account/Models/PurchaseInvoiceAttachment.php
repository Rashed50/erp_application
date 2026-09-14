<?php

namespace Modules\Account\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseInvoiceAttachment extends Model
{
    use HasFactory;

    protected $table = 'purchase_invoice_attachments';
    protected $primaryKey = 'id';

    protected $fillable = [
        'file_path',
        'p_invoice_id',
    ];
}
