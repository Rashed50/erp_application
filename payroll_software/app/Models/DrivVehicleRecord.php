<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DrivVehicleRecord extends Model
{
    use HasFactory;
    protected $table = 'driv_vehicle_records';
    protected $primaryKey = 'driv_veh_auto_id';

    protected $fillable = [
      'driv_auto_id', 
      'veh_auto_id', 
      'project_id', 
      'assign_date', 
      'release_date', 
      'insert_by', 
      'update_by', 
      'status'
    ];

    public function Driver(){
        return $this->belongsTo(DriverInfo::class, 'driv_auto_id', 'dri_auto_id');
      }

    public function Vehicle(){
        return $this->belongsTo(Vehicle::class, 'veh_auto_id', 'veh_id');
      }
}
