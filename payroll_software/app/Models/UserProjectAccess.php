<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProjectInfo;
 

class UserProjectAccess extends Model
{
    use HasFactory;use HasFactory;
    protected $primaryKey = 'emp_proj_acc_auto_id';

    public function project(){
      return $this->belongsTo(ProjectInfo::class, 'proj_id','proj_id');
    }

    public function accessUser(){
        return $this->belongsTo(User::class, 'user_auto_id', 'id');
    }

    public function user(){
      return $this->belongsTo(User::class, 'insert_by','id');
    }
}
