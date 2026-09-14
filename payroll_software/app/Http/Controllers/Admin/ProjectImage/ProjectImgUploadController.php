<?php

namespace App\Http\Controllers\Admin\ProjectImage;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DataServices\EmployeeRelatedDataService;
use Illuminate\Http\Request;
use App\Models\ProjectImgUpload;
use Carbon\Carbon;
use Session;
use Image;
use Auth;

class ProjectImgUploadController extends Controller{
    
    
    // All Function Tranfer To ProjectInfoController, so it will be delete soon
  public function index(){

    $getProject = (new EmployeeRelatedDataService())->getAllProjectInfoForDropdown();
    return view('admin.project-info.images-upload.all',compact('getProject'));
  }

  public function searchProjectImage(Request $request){
      $project_id = $request->project_id;

      $findImage = ProjectImgUpload::where('project_id',$project_id)->get();

      return response()->json([ 'findImage' => $findImage]);
  }


  public function removeImage($id){
    $delete = ProjectImgUpload::where('proj_img_id',$id)->delete();
    $old_image = ProjectImgUpload::where('proj_img_id',$id)->first();
    unlink($old_image->photo_path);
  }

  public function upload(Request $request){

    $images = $request->file('project_image');

    foreach ($images as $img) {
        $make_name = 'project_image'.time().uniqid().'.'.$img->getClientOriginalExtension();
        Image::make($img)->resize(450,500)->save('uploads/project/mult-image/'.$make_name);
        $uplodPath = 'uploads/project/mult-image/'.$make_name;

        $uploader = Auth::user()->id;
        $insert =  ProjectImgUpload::insert([
            'project_id' => $request->project_id,
            'photo_path' => $uplodPath,
            'uploaded_by_id' => $uploader,
            'created_at' => Carbon::now(),
        ]);
    }

    /* redirect add page */
    if($insert){
      Session::flash('success','value');
      return redirect()->back();
    } else{
      Session::flash('error','value');
      return redirect()->back();
    }


  }

}
