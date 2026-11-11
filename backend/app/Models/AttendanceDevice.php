<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AttendanceDevice extends Model
{
    protected $guarded = [];
    protected $casts = ['is_active' => 'boolean', 'metadata' => 'array', 'last_seen_at' => 'datetime'];
    public function logs(): HasMany { return $this->hasMany(AttendanceLog::class); }
}
