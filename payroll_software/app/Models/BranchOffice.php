<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BranchOffice extends Model
{
    use HasFactory;

    protected $fillable = [
        'braoff_auto_id', 
        'branch_code', 
        'branch_name_en', 
        'branch_name_native', 
        'branch_status', 
        'branch_address', 
        'branch_currency_id', 
        'created_by', 
        'updated_by', 
    ];
}
