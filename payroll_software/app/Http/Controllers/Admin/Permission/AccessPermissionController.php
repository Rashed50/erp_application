<?php

namespace App\Http\Controllers\Admin\Permission;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AccessPermission;

class AccessPermissionController extends Controller{
    

    public function checkAccessPermissionWithDate($month, $year){
       return $permission = AccessPermission::where('month',$month)->where('year',$year)->where('is_Lock',false)->first();
    }
}
