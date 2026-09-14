<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectInfo extends Model
{
  use HasFactory;

  protected $table = "project_infos";
  protected $primaryKey = "proj_id";

  protected $fillable = [
    'proj_id',
    'proj_name',
    'starting_date',
    'proj_Incharge_id',
    'proj_description',
    'address',
    'proj_code',
    'proj_budget',
    'proj_deadling',
    'proj_main_thumb',
    'status',
    'created_at',
    'created_by',
    'updated_at',
    'updated_by',
  ];


  public function status()
  {
    return $this->belongsTo(JobStatus::class, 'job_status', 'id');
  }

  public function employee()
  {
    return $this->belongsTo('App\Models\EmployeeInfo', 'proj_Incharge_id', 'emp_auto_id');
  }

  public function projectWiseEmployee()
  {
    return $this->hasMany('App\Models\EmployeeInfo', 'project_id', 'proj_id');
  }
}
