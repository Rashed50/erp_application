<?php

namespace Modules\Payroll\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Models\EmployeeInfo;
use App\Models\ProjectInfo;
use App\Models\User;

class PartialSalary extends Model
{
    protected $table = 'partial_salary_histories';
    protected $primaryKey = 'psh_auto_id';

    protected $fillable = [
        'emp_auto_id',
        'month',
        'year',
        'amount',
        'paid_at',
        'project_id',
        'inserted_by',
        'updated_by'
    ];

    protected $dates = ['paid_at', 'created_at', 'updated_at'];

    public function employee()
    {
        return $this->belongsTo(EmployeeInfo::class, 'emp_auto_id');
    }

    public function project()
    {
        return $this->belongsTo(ProjectInfo::class, 'proj_id');
    }
     

    public function inserter()
    {
        return $this->belongsTo(User::class, 'inserted_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
