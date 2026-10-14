<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceLog extends Model
{
    protected $guarded = [];
    protected $casts = ['event_time' => 'datetime', 'received_at' => 'datetime', 'processed_at' => 'datetime'];
    public function device(): BelongsTo { return $this->belongsTo(AttendanceDevice::class, 'attendance_device_id'); }
}
