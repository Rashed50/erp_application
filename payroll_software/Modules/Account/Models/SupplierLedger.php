<?php

namespace Modules\Account\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\{BranchOffice};


class SupplierLedger extends Model
{
    use HasFactory;

    protected $table = "suplier_subsidiary_ledger";

    protected $primaryKey = 'supplier_id';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'supplier_id',
        'supplier_name',
        'supplier_email',
        'supplier_phone',
        'supplier_address',
        'vat_no',
        'payment_term',
        'isupp_update_by_id',
        'contact_person',
        'contact_person_phone',
        'contact_person_email',
        'opening_balance',
        'current_balance',
        'created_by',
        'created_at',
        'updated_at'
    ];


    public function Branch()
    {
        return $this->belongsTo(BranchOffice::class, 'branch_office_id', 'braoff_auto_id');
    }
}
