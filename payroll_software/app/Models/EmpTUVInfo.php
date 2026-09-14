<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmpTUVInfo extends Model
{
    use HasFactory;

    public function employee(){
        return $this->belongsTo(EmployeeInfo::class, 'emp_auto_id', 'emp_auto_id');
    }
    public function designation(){
        return $this->belongsTo(EmployeeCategory::class, 'trade_id', 'catg_id');
    }
}
