<?php

namespace App\Http\Controllers\Admin\Inventory_Module;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\Inventory_Module\ItemTypeController;
use Illuminate\Http\Request;
use App\Models\ItemType;

class ItemTypeController extends Controller{
    public function getAll(){
      return $all = ItemType::get();
    }
}
