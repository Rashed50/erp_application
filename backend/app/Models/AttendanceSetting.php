<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceSetting extends Model
{
    protected $guarded = [];
    protected $casts = [
        'standard_work_hours' => 'float', 'overtime_threshold_hours' => 'float',
        'default_break_minutes' => 'integer', 'deduct_break_from_worked_hours' => 'boolean',
        'overtime_requires_approval' => 'boolean', 'allow_manual_attendance' => 'boolean',
        'allow_manual_override_of_device_logs' => 'boolean', 'rules' => 'array',
    ];
}
