<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehType extends Model
{
    use HasFactory;
     protected $primaryKey = 'veh_typ_id';

    protected $fillable = ['veh_type_title'];
}
