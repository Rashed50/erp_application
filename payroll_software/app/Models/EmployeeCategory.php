<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeCategory extends Model
{
    use HasFactory;
    protected $primaryKey = 'catg_id';

    public function empType(){
      return $this->belongsTo('App\Models\EmployeeType','emp_type_id','id');
    }
}
