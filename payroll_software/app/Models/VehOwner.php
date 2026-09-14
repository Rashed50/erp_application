<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehOwner extends Model
{
    use HasFactory;
      protected $primaryKey = 'veh_own_id';

    protected $fillable = ['veh_own_title'];
}
