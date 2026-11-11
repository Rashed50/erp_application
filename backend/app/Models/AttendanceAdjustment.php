<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceAdjustment extends Model
{
    protected $guarded = [];
    protected $casts = ['old_values' => 'array', 'requested_values' => 'array', 'reviewed_at' => 'datetime'];
}
