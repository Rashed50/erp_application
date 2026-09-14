<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\MonthsEnum;

class upload_advance_paper extends Model
{
    use HasFactory;

    protected $casts = [
        'month' => MonthsEnum::class
    ]; 
}
