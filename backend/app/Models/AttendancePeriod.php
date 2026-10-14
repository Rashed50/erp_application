<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendancePeriod extends Model
{
    protected $guarded = [];
    protected $casts = ['submitted_at' => 'datetime', 'approved_at' => 'datetime', 'locked_at' => 'datetime'];
}
