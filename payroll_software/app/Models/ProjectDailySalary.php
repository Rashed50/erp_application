<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectDailySalary extends Model
{
    use HasFactory;

    protected $table = 'project_daily_salaries';

    protected $fillable = [
        'project_id',
        'day',
        'month',
        'year',
        'total_employees',
        'total_salary',
    ];

    /**
     * Get the project associated with this daily salary record.
     */
    public function project()
    {
        return $this->belongsTo(ProjectInfo::class, 'project_id', 'proj_id');
    }
}
