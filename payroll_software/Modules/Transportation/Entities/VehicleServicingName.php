<?php

namespace Modules\Transportation\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VehicleServicingName extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'vehicle_servicing_names';
    protected $primaryKey = 'ser_nam_auto_id';

    protected $fillable = [
        'service_name',
        'ser_nam_status'
    ];

    protected static function newFactory()
    {
        return \Modules\Transportation\Database\factories\VehicleServicingNameFactory::new();
    }
}
