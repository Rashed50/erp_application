<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\CompanyProfileController;
use App\Http\Controllers\DataServices\CompanyDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\BannerInfo;
use Carbon\Carbon;
use Session;
use Image;

class BannerInfoController extends Controller
{

  /*
    ===========================
    ====DATABASE OPERATION=====
    ===========================
  */

  // permission
  function __construct()
  {
    $this->middleware('permission:banner-list', ['only' => ['getAllInfo', 'index']]);
    $this->middleware('permission:banner-create', ['only' => ['add', 'insert']]);
    $this->middleware('permission:banner-edit', ['only' => ['getFindId', 'edit', 'update']]);
  
  }
  // permission
  public function getAllInfo()
  {
    return $all = (new CompanyDataService())->getBannersInformation();
  }

  public function getFindId($id)
  {
    return $find = (new CompanyDataService())->findBannerInformation($id);
  }

  /*
    ===========================
    =======BLADE OPERATION=====
    ===========================
  */
  public function index()
  {
    $all = $this->getAllInfo();
    return view('admin.banner.index', compact('all'));
  }

  public function add()
  {
    $comp = (new CompanyDataService())->findCompanryProfile();
    return view('admin.banner.add', compact('comp'));
  }

  public function edit($id)
  {
    $edit = $this->getFindId($id);
    $comp = (new CompanyDataService())->findCompanryProfile();
    return view('admin.banner.edit', compact('edit', 'comp'));
  }

  public function delete($id)
  {

    $delete = (new CompanyDataService())->deleteBanner($id);

    /* redirect back */
    if ($delete) {
      Session::flash('success_soft', 'value');
      return redirect()->back();
    } else {
      Session::flash('error', 'value');
      return redirect()->back();
    }
  }

  public function insert(Request $req)
  {
    /* form validation */
    $this->validate($req, [
      'ban_title' => 'required',
      'ban_image' => 'required',
      'company_id' => 'required',
    ], []);

    /* making banner image */
    $banner_image = $req->file('ban_image');
    $banner_image_name = 'banner-image' . '-' . time() . '-' . $banner_image->getClientOriginalExtension();
    Image::make($banner_image)->resize(1920, 820)->save('uploads/banner/' . $banner_image_name);

    $insert = (new CompanyDataService())->insertNewBanner($req, $banner_image_name);

    if ($insert) {
      Session::flash('success', 'value');
      return redirect()->back();
    } else {
      Session::flash('error', 'value');
      return redirect()->back();
    }
  }

  public function update(Request $req)
  {
    /* form validation */
    $this->validate($req, [
      'ban_title' => 'required',
      'company_id' => 'required',
    ], []);

    $old_img = $req->old_img;
    $banner_image_name = null;
    $comDSObj = new CompanyDataService();
    if ($req->file('ban_image')) {
      unlink('uploads/banner/' . $old_img);
      /* making banner image */
      $banner_image = $req->file('ban_image');
      $banner_image_name = 'banner-image' . '-' . time() . '-' . $banner_image->getClientOriginalExtension();
      Image::make($banner_image)->resize(917, 1000)->save('uploads/banner/' . $banner_image_name);
    }
    if ($banner_image_name == null) {
      $update = $comDSObj->updateBannerInformation($req->id, $req->ban_title, $req->ban_subtitle, $req->ban_caption, $req->ban_description, $req->company_id);
    } else {
      $update = $comDSObj->updateBannerInformationWithBannerImage($req->id, $req->ban_title, $req->ban_subtitle, $req->ban_caption, $req->ban_description, $req->company_id, $banner_image_name);
    }
    /* redirect back */
    if ($update) {
      Session::flash('success', 'value');
      return redirect()->route('banner-info');
    } else {
      Session::flash('error', 'value');
      return redirect()->back();
    }
  }
}
