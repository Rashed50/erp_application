<?php

namespace Modules\HrManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DailyActivityType extends Model
{
    protected $table = 'daily_activities_type';
    protected $primaryKey = 'id';

    protected $fillable = [
        'da_type_name',
    ];
    
}
