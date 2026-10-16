<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DailyAttendance extends Model
{
    protected $guarded = [];
    protected $casts = [
        'attendance_date' => 'date', 'first_punch_at' => 'datetime', 'last_punch_at' => 'datetime',
        'gross_hours' => 'float', 'worked_hours' => 'float', 'standard_work_hours' => 'float',
        'overtime_threshold_hours' => 'float', 'overtime_hours' => 'float', 'approved_overtime_hours' => 'float',
        'payable_day_fraction' => 'float', 'is_late' => 'boolean', 'is_early_leave' => 'boolean',
        'approved_at' => 'datetime', 'locked_at' => 'datetime',
    ];
    public function employee(): BelongsTo { return $this->belongsTo(Employee::class, 'employee_auto_id', 'id'); }
    public function shift(): BelongsTo { return $this->belongsTo(AttendanceShift::class, 'attendance_shift_id'); }
    public function adjustments(): HasMany { return $this->hasMany(AttendanceAdjustment::class, 'daily_attendance_id'); }
}
