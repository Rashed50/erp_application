<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\CostTypeController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\DailyCost;
use App\Models\CostType;
use App\Models\ProjectInfo;
use App\Models\EmployeeInfo;
use Carbon\Carbon;
use Session;
use Image;

class DailyCostController extends Controller{
    /*
    =============================
    =====DATABSE OPEREATION======
    =============================
    */

    public function getProjectInfo(){
      return $all = ProjectInfo::get();
    }
    public function getEmployee(){
      return $all = EmployeeInfo::get();
    }
    public function getAll(){
      return $all = DailyCost::get();
    }

    public function getEdit($id){
      return $edit = DailyCost::where('status','pending')->where('cost_id',$id)->firstOrFail();
    }


    /*
    =============================
    =======BLADE OPEREATION======
    =============================
    */
    public function create(){
      $costTypeObj = new CostTypeController();
      $costType = $costTypeObj->getCostType();

      $project = $this->getProjectInfo();
      $employee = $this->getEmployee();
      $all = $this->getAll();
      return view('admin.daily-cost.add',compact('costType','project','employee','all'));
    }

    public function costList(){
      $all = DailyCost::where('status','pending')->get();
      return view('admin.daily-cost.list',compact('all'));
    }

    public function edit($id){
      $costTypeObj = new CostTypeController();
      $costType = $costTypeObj->getCostType;
      $project = $this->getProjectInfo();
      $employee = $this->getEmployee();
      $edit = $this->getEdit($id);

      return view('admin.daily-cost.edit',compact('costType','project','employee','edit'));
    }

    public function insert(Request $req){
        /* form validation */
        $this->validate($req,[
          'cost_type_id' => 'required',
          'project_id' => 'required',
          'employee_id' => 'required',
          'expire_date' => 'required',
          'amount' => 'required',
          'vouchar' => 'required',
        ],[

        ]);

        /* making banner image */
        $vouchar_image = $req->file('vouchar');
        $vouchar_image_name = 'banner-image'.'-'.time().'-'.$vouchar_image->getClientOriginalExtension();
        Image::make($vouchar_image)->resize(300,300)->save('uploads/vouchar/'.$vouchar_image_name);

        /* data insert in database */
        $entered_id = Auth::user()->id;
        $insert = DailyCost::insert([
          'cost_type_id' => $req->cost_type_id,
          'project_id' => $req->project_id,
          'employee_id' => $req->employee_id,
          'vouchar_no' => $req->vouchar_no,
          'expire_date' => $req->expire_date,
          'amount' => $req->amount,
          'vouchar' => $vouchar_image_name,
          'entered_id' => $entered_id,

          'created_at' => Carbon::now(),
        ]);



        if($insert){
          Session::flash('success','value');
          return Redirect()->back();
        }else{
          Session::flash('error','value');
          return Redirect()->back();
        }
    }

    public function update(Request $req){
        /* form validation */
        $this->validate($req,[
          'cost_type_id' => 'required',
          'project_id' => 'required',
          'employee_id' => 'required',
          'vouchar_no' => 'required',
          'expire_date' => 'required',
          'amount' => 'required',
        ],[

        ]);
        /* data insert in database */
        $id = $req->id;
        $entered_id = Auth::user()->id;
        $old_img = $req->old_image;
        // make Condition
        if($req->file('vouchar')){
          unlink('uploads/vouchar/'.$old_img);
          /* making banner image */
          $vouchar_image = $req->file('vouchar');
          $vouchar_image_name = 'banner-image'.'-'.time().'-'.$vouchar_image->getClientOriginalExtension();
          Image::make($vouchar_image)->resize(300,300)->save('uploads/vouchar/'.$vouchar_image_name);
          // update data
          $update = DailyCost::where('cost_id',$id)->update([
            'cost_type_id' => $req->cost_type_id,
            'project_id' => $req->project_id,
            'employee_id' => $req->employee_id,
            'vouchar_no' => $req->vouchar_no,
            'expire_date' => $req->expire_date,
            'amount' => $req->amount,
            'vouchar' => $vouchar_image_name,
            'entered_id' => $entered_id,
            'updated_at' => Carbon::now(),
          ]);
          if($update){
            Session::flash('success_update','value');
            return Redirect()->route('daily-cost');
          }else{
            Session::flash('error','value');
            return Redirect()->back();
          }
        }else {
          $update = DailyCost::where('cost_id',$id)->update([
            'cost_type_id' => $req->cost_type_id,
            'project_id' => $req->project_id,
            'employee_id' => $req->employee_id,
            'vouchar_no' => $req->vouchar_no,
            'expire_date' => $req->expire_date,
            'amount' => $req->amount,
            'entered_id' => $entered_id,
            'updated_at' => Carbon::now(),
          ]);
          if($update){
            Session::flash('success_update','value');
            return Redirect()->route('daily-cost');
          }else{
            Session::flash('error','value');
            return Redirect()->back();
          }
        }
    }

    public function delete($id){
      $delete = DailyCost::where('status','pending')->where('cost_id',$id)->delete();
      if($delete){
        Session::flash('delete','value');
        return Redirect()->back();
      }else{
        Session::flash('error','value');
        return Redirect()->back();
      }
    }

    public function approve($id){
      $approve = DailyCost::where('status','pending')->where('cost_id',$id)->update([
          'entered_id' => Auth::user()->id,
          'status' => 'approve',
          'updated_at' => Carbon::now(),
      ]);
      /* redirect back */
      if($approve){
        Session::flash('approve','value');
        return Redirect()->back();
      }else{
        Session::flash('error','value');
        return Redirect()->back();
      }
    }
}
