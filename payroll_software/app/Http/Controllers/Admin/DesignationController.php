<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Designation;
use Carbon\Carbon;

class DesignationController extends Controller{
  /*
    // permission
    function __construct()
    {
         $this->middleware('permission:designation-list|designation-create', ['only' => ['index']]);
    }
    // permission
    public function getAllDesignation(){
      return $all = Designation::get();
    }

    public function index(){
      $all = $this->getAllDesignation();
      return view('admin.employee-designation.index',compact('all'));
    }

    public function designationList(){
      $all = $this->getAllDesignation();
      return json_encode($all);
    }

    public function insert(Request $request){
      $this->validate($request,[
          'desig_name' => 'required',
          'desig_code' => 'required',
      ],[

      ]);

      $desig = new Designation();
      $desig->desig_name = $request->desig_name;
      $desig->desig_code = $request->desig_code;
      $desig->created_at = Carbon::now();
      $desig->save();
      if($desig->save() ){
        return response()->json(['success' => 'Successfully Added New Designation']);
      }else{
        return response()->json(['error' => 'Opps Added Failed New Designation']);
      }

    }

    
    public function getDelete(Request $req){
        $id = $req->input('id');
        $delete = Designation::where('id',$id)->delete();
        if($delete){
          return 1;
        }else{
          return 0;
        }
    }

   */
}
