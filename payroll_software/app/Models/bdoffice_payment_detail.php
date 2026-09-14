<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\EmployeeInfo;

class bdoffice_payment_detail extends Model
{
    use HasFactory;
    protected $primaryKey = 'bdpay_auto_id';

    public function employee(){
      return $this->belongsTo(EmployeeInfo::class,'emp_auto_id','emp_auto_id');
    }
}
