<?php

namespace App\Http\Controllers\Fontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\BannerInfoController;
//use App\Http\Controllers\Admin\ProjectInfoController;
use App\Http\Controllers\DataServices\EmployeeRelatedDataService;
use App\Http\Controllers\DataServices\ProjectDataService;

class FontendController extends Controller
{

//  PUSH Protocol Version 3.1.2,  last updated 2020 zkteco device communication protocol
// https://www.scribd.com/document/604031919/Security-PUSH-Communication-Protocol-20200325-002#google_vignette

// command send to device
// https://www.scribd.com/document/54798939/Communication-Protocol-Manual-CMD

// old version 2.4.1
// https://studylib.net/doc/27718586/ilide.info-attendance-push-communication-protocol-2020032...?utm_source=chatgpt.com

  public function index1()
  {

  //  return "System Update Operation is running , Please wait...";
    $banner = new BannerInfoController();
    $getBanner = $banner->getAllInfo();
   // $proj = (new ProjectDataService())->getAllProjectInformation();
    return view('website.index', compact('getBanner'));
  }

    public function index()
  {


    // $laborImages = collect();
    // $files = glob(public_path('images/work/*.{jpg,jpeg,png,webp}'), GLOB_BRACE);
    // foreach ($files as $file) {
    //   $laborImages->push('images/work/' . basename($file));
    // }

    return view('website.index2');
  }

  public function projectDetails($proj_id)
  {

    $proj = (new EmployeeRelatedDataService())->findAProjectInformation($proj_id);
    $muliple = (new EmployeeRelatedDataService())->getProjectMultipleImage($proj_id);


    return view('website.project-details', compact('proj', 'muliple'));
  }
}
