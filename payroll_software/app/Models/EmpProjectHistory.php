<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmpProjectHistory extends Model{
    use HasFactory;
    protected $primaryKey = 'eph_id';

    public function employee(){
      return $this->belongsTo('App\Models\EmployeeInfo','emp_id','emp_auto_id');
    }

    public function project(){
      return $this->belongsTo('App\Models\ProjectInfo','project_id','proj_id');
    }

    public function user(){
      return $this->belongsTo('App\Models\User','create_by_id','id');
    }

    public function designation(){
        return $this->hasMany(EmployeeCategory::class);
    }
}
