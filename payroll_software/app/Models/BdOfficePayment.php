<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\BdOfficePaymentStatusEnum;

class BdOfficePayment extends Model
{
    use HasFactory;


    protected $casts = [
      'status' =>  BdOfficePaymentStatusEnum::Class  
    ];

    public function employee(){
        return $this->belongsTo(EmployeeInfo::class,'emp_auto_id','emp_auto_id');
    }
    public function approvedBy(){
      return $this->belongsTo(EmployeeInfo::class,'approved_by','emp_auto_id');
    }
}
