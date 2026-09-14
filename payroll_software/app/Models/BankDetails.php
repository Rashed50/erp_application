<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankDetails extends Model
{
    use HasFactory;

    public function subcompany(){
        return $this->belongsTo(SubCompanyInfo::class, 'sub_company_id', 'sb_comp_id');
    }
}
