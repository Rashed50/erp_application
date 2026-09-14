<?php

namespace Modules\HrManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Modules\HrManagement\Entities\DailyActivityType;

class DailyActivity extends Model
{

    protected $table = 'daily_activities';
    protected $primaryKey = 'da_auto_id';

    protected $fillable = [
        'da_subject',
        'da_details',
        'da_type_id',
        'da_for_emp_id',
        'da_created_by',
        'da_responsible_emp',
        'da_status',
        'da_progress',
        'da_status_remarks',
        'da_attached_file',
    ];

    protected $casts = [
        'da_progress' => 'integer'
    ];

    // Define relationships if needed
    public function type()
    {
        return $this->belongsTo(DailyActivityType::class, 'da_type_id');
    }

    // public function employee()
    // {
    //     return $this->belongsTo(Employee::class, 'da_for_emp_id');
    // }

    // public function creator()
    // {
    //     return $this->belongsTo(User::class, 'da_created_by');
    // }

    // public function responsible()
    // {
    //     return $this->belongsTo(Employee::class, 'da_responsible_emp');
    // }
}