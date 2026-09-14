<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankName extends Model
{
    use HasFactory;
    protected $table = "bank_names";
    protected $primaryKey = 'bn_auto_id';
    protected $guarded = [];

    protected $fillable = [
        'bn_name',
        'is_active',
    ];

    public $timestamps = false;
}
