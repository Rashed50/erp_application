<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class SalaryDetails extends Model
{
    use HasFactory;
    public function employee(){
      return $this->belongsTo('App\Models\EmployeeInfo','emp_id','emp_auto_id');
    }

     
}
