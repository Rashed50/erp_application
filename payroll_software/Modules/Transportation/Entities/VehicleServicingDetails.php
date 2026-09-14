<?php

namespace Modules\Transportation\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VehicleServicingDetails extends Model
{
    use HasFactory;
    
    protected $table      = 'vehicle_servicing_details';
    protected $primaryKey = 'vehser_det_auto_id';
    public $timestamps    = false;

    protected $fillable = [
        'veh_ser_auto_id',
        'ser_nam_auto_id',
        'qty',
        'unit_rate',
        'total_amount',
        'service_type',
        'remarks'
    ];

    public function serviceName()
    {
        return $this->belongsTo(VehicleServicingName::class, 'ser_nam_auto_id', 'ser_nam_auto_id');
    }
    
    protected static function newFactory()
    {
        return \Modules\Transportation\Database\factories\VehicleServicingDetailsFactory::new();
    }
}
