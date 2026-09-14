<?php
  
namespace App\Http\Controllers\Admin\Signature;
  
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\Helper\UploadDownloadController;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
 
  
class EmployeeSignatureController extends Controller
{
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function index()
    {
        return view('admin.employee-info.signature.signature_pad');
    }
  
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function uploadEmployeeSignature(Request $request)
    {
        // $folderPath = public_path('upload/');
        
        // $image_parts = explode(";base64,", $request->signed);
              
        // $image_type_aux = explode("image/", $image_parts[0]);
           
        // $image_type = $image_type_aux[1];
           
        // $image_base64 = base64_decode($image_parts[1]);
    
        // $file = $folderPath . uniqid() . '.'.$image_type;
        if($request->signed == null){
            Session::Flash('error','Operation Failed, Please Try Again');
            return back();
        }
 
        (new UploadDownloadController())->uploadEmployeeSignature($request->signed,null);
        return back()->with('success', 'Successfull Completed');
    }

}