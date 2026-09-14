<?php

namespace App\Http\Controllers\Admin\Permission;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use Carbon\Carbon;
use Session;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $all = Role::orderBy('role_auto_id','ASC')->get();
        return view('admin.role.all',compact('all'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        // form validation
        $this->validate($request,[
          'role_name' => 'required|max:100|regex:/^[a-zA-ZÑñ\s]+$/|unique:roles,role_name',
        ],[

        ]);
        $insert = Role::insert([
          'role_name' => $request->role_name,
          'created_at' => Carbon::now(),
        ]);

        if($insert){
          Session::flash('success_add','value');
          return redirect()->back();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
       $role = Role::where('role_auto_id',$id)->first();
       return view('admin.role.edit',compact('role'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
      $request->validate([
          'role_name' => 'required|max:100|regex:/^[a-zA-ZÑñ\s]+$/',
      ]);
      $update = Role::where('role_auto_id',$id)->update([
        'role_name' => $request->role_name,
        'updated_at' => Carbon::now(),
      ]);

      if($update){
        Session::flash('update_add','value');
        return redirect()->route('role-manage.create');
      }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
