<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;
    protected $table = 'vehicles';
    protected $primaryKey = 'veh_id';
    protected $guarded = [];

    protected $fillable = [
      'company_id', 
      'veh_name', 
      'veh_plate_number', 
      'veh_model_number', 
      'veh_brand_name', 
      'veh_insurrance_date', 
      'veh_price', 
      'veh_color', 
      'veh_licence_no', 
      'veh_type_id', 
      'veh_purchase_date', 
      'joining_date', 
      'veh_present_metar', 
      'veh_ins_expire_date', 
      'veh_ins_renew_date', 
      'veh_ins_certificate', 
      'veh_reg_expire_date', 
      'veh_reg_renew_date', 
      'veh_reg_certificate', 
      'veh_photo', 
      'remarks', 
      'create_by_id', 
      'status'
    ];

    
    public function employee(){
      return $this->belongsTo('App\Models\EmployeeInfo','driver_id','emp_auto_id');
    }
    public function user(){
      return $this->belongsTo('App\Models\User','create_by_id','id');
    }

}
