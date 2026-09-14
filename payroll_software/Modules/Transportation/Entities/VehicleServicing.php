<?php

namespace Modules\Transportation\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\{User, Vehicle, EmployeeInfo};

class VehicleServicing extends Model
{
    use HasFactory;

    protected $table = 'vehicle_servicing';
    protected $primaryKey = 'veh_ser_auto_id';
    public $timestamps = false;

    protected $fillable = [
        'veh_auto_id',
        'grand_total_amount',
        'discount',
        'payable_amount',
        'payment_method',
        'servicing_status',
        'start_date',
        'end_date',
        'service_by',
        'invoice_file',
        'invoice_no',
        'remarks',
        'current_mileage',
        'approved_by',
        'created_by',
        'updated_by'
    ];

    public function vehicle()
    {
        // return $this->belongsTo(Vehicle::class, 'veh_auto_id', 'veh_id');
        return $this->belongsTo(Vehicle::class, 'veh_auto_id', 'veh_id')
            ->select([
                'veh_id',
                'company_id',
                'veh_name',
                'veh_plate_number',
                'veh_model_number'
            ]);
    }

    public function serviceBy()
    {
        // return $this->belongsTo(EmployeeInfo::class, 'service_by', 'emp_auto_id');
        return $this->belongsTo(EmployeeInfo::class, 'service_by', 'employee_id')
            ->select([
                'emp_auto_id',
                'employee_id',
                'employee_name',
                'passfort_no',
                'akama_no',
                'mobile_no'
            ]);
    }


    public function details()
    {
        // return $this->hasMany(VehicleServicingDetails::class, 'veh_ser_auto_id', 'veh_ser_auto_id');
        return $this->hasMany(VehicleServicingDetails::class, 'veh_ser_auto_id', 'veh_ser_auto_id')
            ->with(['serviceName' => function($query) {
                $query->select(['ser_nam_auto_id', 'service_name']);
            }]);
    }

    public function approvedBy()
    {
        // return $this->belongsTo(User::class, 'approved_by', 'id');
        return $this->belongsTo(User::class, 'approved_by', 'id')
            ->select([
                'id',
                'name',
                'phone_number',
                'email'
            ]);
    }

    public function createdBy()
    {
        // return $this->belongsTo(User::class, 'created_by', 'id');
        return $this->belongsTo(User::class, 'created_by', 'id')
            ->select([
                'id',
                'name',
                'phone_number',
                'email'
            ]);
    }

    public function updatedBy()
    {
        // return $this->belongsTo(User::class, 'updated_by', 'id');
        return $this->belongsTo(User::class, 'updated_by', 'id')
            ->select([
                'id',
                'name',
                'phone_number',
                'email'
            ]);
    }
}



