<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\EmployeeBonusTypeEnum;

class EmployeeBonus extends Model
{
    use HasFactory;
    protected $table = 'employee_bonus_records';

    protected $fillable = ["emp_auto_id","bonus_type","month","year","amount", "status","created_by","updated_by","remarks"];

    protected $casts = [
        'bonus_type' => EmployeeBonusTypeEnum::class,
    ]; 

}
