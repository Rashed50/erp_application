<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyCost extends Model
{
    use HasFactory;
    protected $primaryKey = 'cost_id';

    public function subCompany(){
        return $this->belongsTo('App\Models\SubCompanyInfo','sub_comp_id','sb_comp_id');
    }

    public function expenseHead(){
      return $this->belongsTo('App\Models\CostType','cost_type_id','cost_type_id');
    }

    public function project(){
      return $this->belongsTo('App\Models\ProjectInfo','project_id','proj_id');
    }

    public function employee(){
      return $this->belongsTo('App\Models\EmployeeInfo','employee_id','emp_auto_id');
    }

    public function user(){
      return $this->belongsTo('App\Models\User','entered_id','id');
    }
}
