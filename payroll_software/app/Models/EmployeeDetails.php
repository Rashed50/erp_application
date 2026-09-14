<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models;

class EmployeeDetails extends Model
{
  use HasFactory;

  protected $table = 'employee_details';
  protected $primaryKey = 'ed_auto_id';

  protected $fillable = [
    'ed_auto_id',
    'emp_auto_id',
    'dept_id',
    'last_education_id',
    'religion_id',
    'expertness_rating',
    'country_phone_no',
    'agc_info_auto_id',
    'gender',

    'is_married',
    'blood_group',
    'present_address',
    'ref_employee_id',
    'remarks',

    'educational_papers',
    'blood_group_paper',
    'bg_paper',
    'emp_act_remarks',
    'gosi_number',
  ];

  public function agency()
  {
    return $this->belongsTo(AgencyInfo::class, 'agc_info_auto_id', 'agc_info_auto_id');
  }
}
