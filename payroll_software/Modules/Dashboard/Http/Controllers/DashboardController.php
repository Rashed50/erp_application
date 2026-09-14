<?php

namespace Modules\Dashboard\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $getBanner = collect([(object)['company_logo' => 'images/company_logo.png']]);
        return view('dashboard::index', compact('getBanner'));
    }
}
