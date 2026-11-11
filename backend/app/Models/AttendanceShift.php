<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceShift extends Model
{
    protected $guarded = [];
    protected $casts = ['crosses_midnight' => 'boolean', 'is_active' => 'boolean', 'settings' => 'array'];
}
