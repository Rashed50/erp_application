<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLoginActivity extends Model
{
    use HasFactory;
    protected $table = 'user_login_activities';
    protected $fillable = [
        'ui_form_id',
        'uo_type_id',
        'user_id',
        'emp_auto_id',
        'salary_amount'
    ];
}
