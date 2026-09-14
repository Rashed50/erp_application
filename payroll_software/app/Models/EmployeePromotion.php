<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeePromotion extends Model
{
    use HasFactory;
    protected $primaryKey = 'emp_prom_id';
    protected $guarded = [];


    public function employee()
    {
        return $this->belongsTo(EmployeeInfo::class, 'emp_id', 'emp_auto_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'entered_id', 'id');
    }
}
