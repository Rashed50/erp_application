<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverInfo extends Model
{
    use HasFactory;
    protected $table = 'driver_infos';
    protected $primaryKey = 'dri_auto_id';

    protected $fillable = [
        'dri_emp_id', 
        'dri_emp_type', 
        'dri_license_type_id', 
        'dri_name', 
        'dri_address', 
        'dri_iqama_no', 
        'dri_iqama_certificate', 
        'dri_license_certificate', 
        'dri_ins_certificate', 
        'insert_by', 
        'status'
    ];
}
