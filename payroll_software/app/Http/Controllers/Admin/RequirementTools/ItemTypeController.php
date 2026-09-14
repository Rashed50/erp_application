<?php

namespace App\Http\Controllers\Admin\RequirementTools;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ItemType;

class ItemTypeController extends Controller{
    public function getAll(){
      return $all = ItemType::get();
    }
}
