<?php

namespace App\Models\Inventory;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubStoreInfo extends Model
{
    use HasFactory;
    protected $primaryKey = 'emp_id';

    public function user(){
        return $this->belongsTo(User::class, 'create_by_id', 'id');
      }
}
