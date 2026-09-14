<?php

namespace App\Models\AccountsModule;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DrVoucher extends Model
{
    use HasFactory;
    protected $primaryKey = 'dr_vou_auto_id';
    protected $table = "dr_vouchers";
    protected $guarded = [];
    
}
