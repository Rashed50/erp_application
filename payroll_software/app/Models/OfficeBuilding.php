<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficeBuilding extends Model{
    use HasFactory;
    protected $primaryKey = 'ofb_id';
    protected $guarded = [];

    public function user(){
      return $this->belongsTo('App\Models\User','create_by_id','id');
    }

    // public function employee(){
    //     return $this->belongsTo(EmployeeInfo::class, 'ofb_manage_by_emp_auto_id', 'emp_auto_id');
    // }
}
