<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmpJobExperience extends Model
{
    use HasFactory;
    protected $primaryKey = 'ejex_id';

    public function employee(){
      return $this->belongsTo('App\Models\EmployeeInfo','emp_id','emp_auto_id');
    }

    public function user(){
      return $this->belongsTo('App\Models\User','create_by_id','id');
    }
}
