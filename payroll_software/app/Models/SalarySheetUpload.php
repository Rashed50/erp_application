<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalarySheetUpload extends Model
{
    use HasFactory;

    protected $primaryKey = 'ss_auto_id';
    protected $guarded = [];
}
