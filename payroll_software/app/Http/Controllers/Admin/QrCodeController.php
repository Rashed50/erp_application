<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class QrCodeController extends Controller
{
    public function index(){
      return view('admin.qrcode.index');
    }

    public function add(){
      
    }
}
