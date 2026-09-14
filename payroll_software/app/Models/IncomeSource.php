<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncomeSource extends Model
{
    use HasFactory;

    protected $primaryKey = 'inc_id';

    public function user(){
      return $this->belongsTo('App\Models\User','create_by_id','id');
    }

    public function project(){
      return $this->belongsTo('App\Models\ProjectInfo','project_id','proj_id');
    }

    public function employee(){
      return $this->belongsTo('App\Models\EmployeeInfo','submitted_by_id','emp_auto_id');
    }

    public function income(){
      return $this->belongsTo('App\Models\IncomeType','founding_source','income_type_id');
    }
}
