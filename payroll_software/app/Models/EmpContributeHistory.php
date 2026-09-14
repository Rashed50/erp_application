<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmpContributeHistory extends Model
{
    use HasFactory;
    protected $primaryKey = 'EmpContHistId';

    public function employee(){
      return $this->belongsTo('App\Models\EmployeeInfo','emp_auto_id','emp_auto_id');
    }

}
