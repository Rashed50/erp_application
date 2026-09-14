<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\{BranchOffice};


class InventorySupplier extends Model
{
    use HasFactory;

    protected $table = "inventory_suppliers";

    protected $primaryKey = 'isupp_auto_id';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'isupp_auto_id', 
        'isupp_name', 
        'isupp_email', 
        'isupp_vat_number', 
        'isupp_contact_address', 
        'branch_office_id', 
        'isupp_create_by_id', 
        'isupp_update_by_id',
        'isupp_status', 
        'created_at', 
        'updated_at'
    ];

    public function Branch()
    {
        return $this->belongsTo(BranchOffice::class, 'branch_office_id', 'braoff_auto_id');
    }
}
