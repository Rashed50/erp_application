<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\EmpSalaryStatusEnum;

//! Imports
use App\Models\EmployeeBankDetails;

class EmployeeInfo extends Model
{
  use HasFactory;


  protected $table = 'employee_infos';
  // protected $primaryKey = 'emp_id';
  protected $primaryKey = 'emp_auto_id';

  // protected $casts = [
  //   'salary_status' =>EmpSalaryStatusEnum::class  
  // ];


  protected $fillable = [
    'emp_auto_id',
    'employee_id',
    'employee_name',
    'passfort_no',
    'passfort_expire_date',

    'akama_no',
    'akama_expire_date',
    'company_id',
    'sponsor_id',
    'mobile_no',

    'country_id',
    'division_id',
    'district_id',
    'post_code',
    'details',
    'present_address',
    'emp_type_id',
    'project_id',

    'designation_id',
    'hourly_employee',
    'department_id',
    'date_of_birth',

    'phone_no',
    'email',
    'maritus_status',
    'gender',
    'religion',
    'joining_date',
    'confirmation_date',
    'appointment_date',

    'job_status',
    'job_location',
    'entry_date',
    'pasfort_photo',
    'profile_photo',
    'akama_photo',
    'medical_report',
    'employee_appoint_latter',
    'entered_id',
    'salary_status',
    'isNightShift',

    'accomd_ofb_id',
  ];

  public function salarydetails()
  {
    return $this->belongsTo(SalaryDetails::class, 'emp_auto_id', 'emp_id');
  }

  public function country()
  {
    return $this->belongsTo('App\Models\Country', 'country_id', 'id');
  }

  public function employeeType()
  {
    return $this->belongsTo('App\Models\EmployeeType', 'emp_type_id', 'id');
  }

  public function type()
  {
    return $this->belongsTo('App\Models\EmployeeType', 'emp_type_id', 'id');
  }

  public function category()
  {
    return $this->belongsTo('App\Models\EmployeeCategory', 'designation_id', 'catg_id');
  }


  public function division()
  {
    return $this->belongsTo('App\Models\Division', 'division_id', 'division_id');
  }

  public function district()
  {
    return $this->belongsTo('App\Models\District', 'district_id', 'district_id');
  }

  public function department()
  {
    return $this->belongsTo('App\Models\Department', 'department_id', 'dep_id');
  }

  public function project()
  {
    return $this->belongsTo(ProjectInfo::class, 'project_id', 'proj_id');
  }

  public function sponsor()
  {
    return $this->belongsTo(Sponsor::class, 'sponsor_id', 'spons_id');
  }

  public function status()
  {
    return $this->belongsTo(JobStatus::class, 'job_status', 'id');
  }

  public function religion()
  {
    return $this->belongsTo('App\Models\Religion', 'religion', 'relig_id');
  }

  public function sponser()
  {
    return $this->belongsTo('App\Models\Sponsor', 'sponsor_id', 'spons_id');
  }

  public function agency()
  {
    return $this->belongsTo(AgencyInfo::class, 'agc_info_auto_id', 'agc_info_auto_id');
  }

  public function advancePayInfo()
  {
    return $this->hasMany(AdvancePay::class, 'emp_id', 'emp_auto_id');
  }

  // Define the relationship with the Ticket model
  public function tickets()
  {
    return $this->hasMany(\Modules\Expense\Models\Ticket::class, 'emp_auto_id', 'emp_auto_id');
  }

  // public function activeBankDetail()
  // {
  //     return $this->hasOne(EmployeeBankDetails::class, 'emp_auto_id', 'emp_auto_id')
  //                 ->where('is_active', 1)
  //                 ->latest('ebd_auto_id'); 
  // }

  // public function bank()
  // {
  //     return $this->hasOneThrough(
  //         BankName::class,
  //         EmployeeBankDetails::class,
  //         'emp_auto_id',  // employee_bank_details এর FK
  //         'bn_auto_id',   // bank_names এর PK
  //         'emp_auto_id',  // employee_infos এর PK
  //         'bank_id'       // employee_bank_details এর bank_id
  //     )->where('employee_bank_details.is_active', 1);
  // }
}
