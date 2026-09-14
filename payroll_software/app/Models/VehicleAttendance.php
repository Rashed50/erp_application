<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleAttendance extends Model
{
    protected $primaryKey = 'veh_atten_auto_id';

    protected $fillable = [
        'veh_atten_auto_id','atten_date', 'atten_day', 'atten_month', 'atten_year',
        'working_project_id', 'veh_auto_id', 'driver_name',
        'driver_iqama', 'driver_phone_no','remarks',
    ];
}
