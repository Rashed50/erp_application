@extends('layouts.admin-master')
@section('title') Create Role @endsection
@section('updateRole') active @endsection
  @section('internal-css')
    <link href="{{ asset('contents/admin') }}/datatables/css/jquery.dataTables.min.css" rel="stylesheet">
  @endsection
@section('content')
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title"> Edit User Role Permision</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active"> User Role Permision</li>
        </ol>
    </div>
</div>
<!-- add division -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        @if(Session::has('success_add'))
          <div class="alert alert-success alertsuccess" role="alert">
             <strong>Successfully!</strong> Added New Permision In User Role.
          </div>
        @endif
        @if(Session::has('success_update'))
          <div class="alert alert-success alertsuccess" role="alert">
             <strong>Successfully!</strong> Update Role Permision In User Role.
          </div>
        @endif
        @if(Session::has('success_delete'))
          <div class="alert alert-success alertsuccess" role="alert">
             <strong>Successfully!</strong> Delete Role Permision In User Role.
          </div>
        @endif
        @if(Session::has('success_error'))
          <div class="alert alert-warning alerterror" role="alert">
             <strong>Opps!</strong> please try again.
          </div>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-md-12">
      <form class="form-horizontal" action="{{ route('permission.update',$edit->perm_id) }}" method="post">
        @csrf
        @method('put')
        <div class="card">
            <div class="card-header"></div>
            <div class="card-body card_form" style="padding-top: 0;">
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group row custom_form_group{{ $errors->has('role_id') ? ' has-error' : '' }}" style="margin-bottom: 0">
                      <label class="control-label col-md-4">Role Name:<span class="req_star">*</span></label>
                      <div class="col-md-7">
                        <select class="form-control" name="role_id">
                          <option value="">Select Role</option>
                          @foreach ($all as $data)
                            <option value="{{ $data->role_auto_id }}" {{ $data->role_auto_id == $edit->role_id ? 'selected' : '' }}>{{ $data->role_name }}</option>
                          @endforeach
                        </select>
                        @if ($errors->has('role_id'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('role_id') }}</strong>
                            </span>
                        @endif
                      </div>
                  </div>
                </div>
                {{-- permission --}}
                <div class="col-md-8">
                  <div class="card">
                      <div class="card-body">
                          <div class="table-responsive">
                              <table id="example2" class="display" style="width:100%">
                                  <thead>
                                      <tr>
                                          <th>Permission</th>
                                          <th>Add</th>
                                          <th>View</th>
                                          <th>Edit</th>
                                          <th>Delete</th>
                                          <th>List</th>
                                      </tr>
                                  </thead>
                                  <tbody>
                                    <tr>
                                        <td>Admin Setting</td>
                                        <td>X</td>
                                        <td>
                                            <input type="checkbox" name="permission[adminSetting][view]" value="1" @isset($edit['permission']['adminSetting']['view']) checked @endisset>
                                        </td>
                                        <td>X</td>
                                        <td>X</td>
                                        <td>X</td>
                                    </tr>
                                    <tr>
                                        <td>Employee</td>
                                        <td>X</td>
                                        <td>
                                            <input type="checkbox" name="permission[employeeMenu][view]" value="1" @isset($edit['permission']['employeeMenu']['view']) checked @endisset>
                                        </td>
                                        <td>X</td>
                                        <td>X</td>
                                        <td>X</td>
                                    </tr>
                                    <tr>
                                      <td>User</td>
                                      <td>
                                          <input type="checkbox" name="permission[user][add]" value="1" @isset($edit['permission']['user']['add']) checked @endisset>
                                      </td>
                                      <td>
                                          <input type="checkbox" name="permission[user][view]" value="1" @isset($edit['permission']['user']['view']) checked @endisset>
                                      </td>
                                      <td>
                                          <input type="checkbox" name="permission[user][edit]" value="1" @isset($edit['permission']['user']['edit']) checked @endisset>
                                      </td>
                                      <td>
                                          <input type="checkbox" name="permission[user][delete]" value="1" @isset($edit['permission']['user']['delete']) checked @endisset>
                                      </td>
                                      <td>
                                          <input type="checkbox" name="permission[user][list]" value="1" @isset($edit['permission']['user']['list']) checked @endisset>
                                      </td>
                                  </tr>

                                  <tr>
                                      <td>Role</td>
                                      <td>
                                          <input type="checkbox" name="permission[role][add]" value="1" @isset($edit['permission']['role']['add']) checked @endisset>
                                      </td>
                                      <td>
                                          <input type="checkbox" name="permission[role][view]" value="1" @isset($edit['permission']['role']['view']) checked @endisset>
                                      </td>
                                      <td>
                                          <input type="checkbox" name="permission[role][edit]" value="1" @isset($edit['permission']['role']['edit']) checked @endisset>
                                      </td>
                                      <td>
                                          <input type="checkbox" name="permission[role][delete]" value="1" @isset($edit['permission']['role']['delete']) checked @endisset>
                                      </td>
                                      <td>
                                          <input type="checkbox" name="permission[role][list]" value="1" @isset($edit['permission']['role']['list']) checked @endisset>
                                      </td>
                                  </tr>
                                  <tr>
                                      <td>Role Permision</td>
                                      <td>
                                          <input type="checkbox" name="permission[rolePermission][add]" value="1" @isset($edit['permission']['rolePermission']['add']) checked @endisset>
                                      </td>
                                      <td>
                                          <input type="checkbox" name="permission[rolePermission][view]" value="1" @isset($edit['permission']['rolePermission']['view']) checked @endisset>
                                      </td>
                                      <td>
                                          <input type="checkbox" name="permission[rolePermission][edit]" value="1" @isset($edit['permission']['rolePermission']['edit']) checked @endisset>
                                      </td>
                                      <td>
                                          <input type="checkbox" name="permission[rolePermission][delete]" value="1" @isset($edit['permission']['rolePermission']['delete']) checked @endisset>
                                      </td>
                                      <td>
                                          <input type="checkbox" name="permission[rolePermission][list]" value="1" @isset($edit['permission']['rolePermission']['list']) checked @endisset>
                                      </td>
                                  </tr>

                                  <tr>
                                      <td>Company Profile</td>
                                      <td>
                                          <input type="checkbox" name="permission[company][add]" value="1" @isset($edit['permission']['company']['add']) checked @endisset>
                                      </td>
                                      <td> X </td>
                                      <td>
                                          <input type="checkbox" name="permission[company][edit]" value="1" @isset($edit['permission']['company']['edit']) checked @endisset>
                                      </td>
                                      <td> X </td>
                                      <td>
                                          <input type="checkbox" name="permission[company][list]" value="1" @isset($edit['permission']['company']['list']) checked @endisset>
                                      </td>
                                  </tr>

                                  <tr>
                                      <td>Sub Company </td>
                                      <td>
                                          <input type="checkbox" name="permission[subcompany][add]" value="1" @isset($edit['permission']['subcompany']['add']) checked @endisset>
                                      </td>
                                      <td>
                                          <input type="checkbox" name="permission[subcompany][view]" value="1" @isset($edit['permission']['subcompany']['view']) checked @endisset>
                                      </td>
                                      <td>
                                          <input type="checkbox" name="permission[subcompany][edit]" value="1" @isset($edit['permission']['subcompany']['edit']) checked @endisset>
                                      </td>
                                      <td>
                                          <input type="checkbox" name="permission[subcompany][delete]" value="1" @isset($edit['permission']['subcompany']['delete']) checked @endisset>
                                      </td>
                                      <td>
                                          <input type="checkbox" name="permission[subcompany][list]" value="1" @isset($edit['permission']['subcompany']['list']) checked @endisset>
                                      </td>
                                  </tr>

                                  <tr>
                                      <td>Banner</td>
                                      <td>
                                          <input type="checkbox" name="permission[banner][add]" value="1" @isset($edit['permission']['banner']['add']) checked @endisset>
                                      </td>
                                      <td>
                                          <input type="checkbox" name="permission[banner][view]" value="1" @isset($edit['permission']['banner']['view']) checked @endisset>
                                      </td>
                                      <td>
                                          <input type="checkbox" name="permission[banner][edit]" value="1" @isset($edit['permission']['banner']['edit']) checked @endisset>
                                      </td>
                                      <td>
                                          <input type="checkbox" name="permission[banner][delete]" value="1" @isset($edit['permission']['banner']['delete']) checked @endisset>
                                      </td>
                                      <td>
                                          <input type="checkbox" name="permission[banner][list]" value="1" @isset($edit['permission']['banner']['list']) checked @endisset>
                                      </td>
                                  </tr>

                                  <tr>
                                      <td>Country</td>
                                      <td>
                                          <input type="checkbox" name="permission[country][add]" value="1" @isset($edit['permission']['country']['add']) checked @endisset>
                                      </td>
                                      <td>
                                          <input type="checkbox" name="permission[country][view]" value="1" @isset($edit['permission']['country']['view']) checked @endisset>
                                      </td>
                                      <td>
                                          <input type="checkbox" name="permission[country][edit]" value="1" @isset($edit['permission']['country']['edit']) checked @endisset>
                                      </td>
                                      <td>
                                          <input type="checkbox" name="permission[country][delete]" value="1" @isset($edit['permission']['country']['delete']) checked @endisset>
                                      </td>
                                      <td>
                                          <input type="checkbox" name="permission[country][list]" value="1" @isset($edit['permission']['country']['list']) checked @endisset>
                                      </td>
                                  </tr>

                                  <tr>
                                      <td>Division</td>
                                      <td>
                                          <input type="checkbox" name="permission[division][add]" value="1" @isset($edit['permission']['division']['add']) checked @endisset>
                                      </td>
                                      <td>
                                          <input type="checkbox" name="permission[division][view]" value="1" @isset($edit['permission']['division']['view']) checked @endisset>
                                      </td>
                                      <td>
                                          <input type="checkbox" name="permission[division][edit]" value="1" @isset($edit['permission']['division']['edit']) checked @endisset>
                                      </td>
                                      <td>
                                          <input type="checkbox" name="permission[division][delete]" value="1" @isset($edit['permission']['division']['delete']) checked @endisset>
                                      </td>
                                      <td>
                                          <input type="checkbox" name="permission[division][list]" value="1" @isset($edit['permission']['division']['list']) checked @endisset>
                                      </td>
                                  </tr>


                                  <tr>
                                      <td>District</td>
                                      <td>
                                          <input type="checkbox" name="permission[district][add]" value="1" @isset($edit['permission']['district']['add']) checked @endisset>
                                      </td>
                                      <td>
                                          <input type="checkbox" name="permission[district][view]" value="1" @isset($edit['permission']['district']['view']) checked @endisset>
                                      </td>
                                      <td>
                                          <input type="checkbox" name="permission[district][edit]" value="1" @isset($edit['permission']['district']['edit']) checked @endisset>
                                      </td>
                                      <td>
                                          <input type="checkbox" name="permission[district][delete]" value="1" @isset($edit['permission']['district']['delete']) checked @endisset>
                                      </td>
                                      <td>
                                          <input type="checkbox" name="permission[district][list]" value="1" @isset($edit['permission']['district']['list']) checked @endisset>
                                      </td>
                                  </tr>

                                      <tr>
                                          <td>Designation</td>
                                          <td>
                                              <input type="checkbox" name="permission[designation][add]" value="1" @isset($edit['permission']['designation']['add']) checked @endisset>
                                          </td>
                                          <td>X</td>
                                          <td>
                                              <input type="checkbox" name="permission[designation][edit]" value="1" @isset($edit['permission']['designation']['edit']) checked @endisset>
                                          </td>
                                          <td>X</td>
                                          <td>
                                              <input type="checkbox" name="permission[designation][list]" value="1" @isset($edit['permission']['designation']['list']) checked @endisset>
                                          </td>
                                      </tr>

                                      <tr>
                                          <td>Sponser</td>
                                          <td>
                                              <input type="checkbox" name="permission[sponser][add]" value="1" @isset($edit['permission']['sponser']['add']) checked @endisset>
                                          </td>
                                          <td>X</td>
                                          <td>
                                              <input type="checkbox" name="permission[sponser][edit]" value="1" value="1" @isset($edit['permission']['sponser']['edit']) checked @endisset>
                                          </td>
                                          <td>
                                              <input type="checkbox" name="permission[sponser][delete]" value="1" value="1" @isset($edit['permission']['sponser']['delete']) checked @endisset>
                                          </td>
                                          <td>
                                              <input type="checkbox" name="permission[sponser][list]" value="1" value="1" @isset($edit['permission']['sponser']['list']) checked @endisset>
                                          </td>
                                      </tr>

                                      <tr>
                                          <td>Project Info</td>
                                          <td>
                                              <input type="checkbox" name="permission[projectInfo][add]" value="1" value="1" @isset($edit['permission']['projectInfo']['add']) checked @endisset>
                                          </td>
                                          <td>
                                              <input type="checkbox" name="permission[projectInfo][view]" value="1" @isset($edit['permission']['projectInfo']['view']) checked @endisset>
                                          </td>
                                          <td>
                                              <input type="checkbox" name="permission[projectInfo][edit]" value="1" @isset($edit['permission']['projectInfo']['edit']) checked @endisset>
                                          </td>
                                          <td>X</td>
                                          <td>
                                              <input type="checkbox" name="permission[projectInfo][list]" value="1" @isset($edit['permission']['projectInfo']['list']) checked @endisset>
                                          </td>
                                      </tr>

                                      <tr>
                                          <td>Project In Charge</td>
                                          <td>
                                              <input type="checkbox" name="permission[projectInCharge][add]" value="1" @isset($edit['permission']['projectInCharge']['add']) checked @endisset>
                                          </td>
                                          <td>X</td>
                                          <td>X</td>
                                          <td>X</td>
                                          <td>
                                              <input type="checkbox" name="permission[projectInCharge][list]" value="1" @isset($edit['permission']['projectInCharge']['list']) checked @endisset>
                                          </td>
                                      </tr>

                                      <tr>
                                          <td>Project Image</td>
                                          <td>
                                              <input type="checkbox" name="permission[projectImage][add]" value="1" @isset($edit['permission']['projectImage']['add']) checked @endisset>
                                          </td>
                                          <td>X</td>
                                          <td>X</td>
                                          <td>
                                              <input type="checkbox" name="permission[projectImage][delete]" value="1" @isset($edit['permission']['projectImage']['delete']) checked @endisset>
                                          </td>
                                          <td>
                                              <input type="checkbox" name="permission[projectImage][list]" value="1" @isset($edit['permission']['projectImage']['list']) checked @endisset>
                                          </td>
                                      </tr>

                                      {{-- Approval --}}
                                      <tr>
                                          <td>Income Approval</td>
                                          <td>#</td>
                                          <td>#</td>
                                          <td>
                                              <input type="checkbox" name="permission[incomeApprove][edit]" value="1" @isset($edit['permission']['incomeApprove']['edit']) checked @endisset>
                                          </td>
                                          <td>#</td>
                                          <td>
                                              <input type="checkbox" name="permission[incomeApprove][list]" value="1" @isset($edit['permission']['incomeApprove']['list']) checked @endisset>
                                          </td>
                                      </tr>
                                      <tr>
                                          <td>Expenditure Approval</td>
                                          <td>#</td>
                                          <td>#</td>
                                          <td>
                                              <input type="checkbox" name="permission[expenditureApprove][edit]" value="1" @isset($edit['permission']['expenditureApprove']['edit']) checked @endisset>
                                          </td>
                                          <td>#</td>
                                          <td>
                                              <input type="checkbox" name="permission[expenditureApprove][list]" value="1" @isset($edit['permission']['expenditureApprove']['list']) checked @endisset>
                                          </td>
                                      </tr>
                                      <tr>
                                          <td>Job Approval</td>
                                          <td>#</td>
                                          <td>#</td>
                                          <td>
                                              <input type="checkbox" name="permission[jobApprove][edit]" value="1" @isset($edit['permission']['jobApprove']['edit']) checked @endisset>
                                          </td>
                                          <td>#</td>
                                          <td>
                                              <input type="checkbox" name="permission[jobApprove][list]" value="1" @isset($edit['permission']['jobApprove']['list']) checked @endisset>
                                          </td>
                                      </tr>
                                      <tr>
                                          <td>Leave Approval</td>
                                          <td>X</td>
                                          <td>X</td>
                                          <td>
                                              <input type="checkbox" name="permission[leaveApprove][edit]" value="1" @isset($edit['permission']['leaveApprove']['edit']) checked @endisset>
                                          </td>
                                          <td>
                                              <input type="checkbox" name="permission[leaveApprove][delete]" value="1" @isset($edit['permission']['leaveApprove']['delete']) checked @endisset>
                                          </td>
                                          <td>
                                              <input type="checkbox" name="permission[leaveApprove][list]" value="1" @isset($edit['permission']['leaveApprove']['list']) checked @endisset>
                                          </td>
                                      </tr>
                                      {{-- Approval --}}

                                      {{-- Employee --}}
                                      <tr>
                                          <td>Employee Info</td>
                                          <td>
                                              <input type="checkbox" name="permission[employeeInfo][add]" value="1" @isset($edit['permission']['employeeInfo']['add']) checked @endisset>
                                          </td>
                                          <td>
                                              <input type="checkbox" name="permission[employeeInfo][view]" value="1" @isset($edit['permission']['employeeInfo']['view']) checked @endisset>
                                          </td>
                                          <td>
                                              <input type="checkbox" name="permission[employeeInfo][edit]" value="1" @isset($edit['permission']['employeeInfo']['edit']) checked @endisset>
                                          </td>
                                          <td>X</td>
                                          <td>
                                              <input type="checkbox" name="permission[employeeInfo][list]" value="1" @isset($edit['permission']['employeeInfo']['list']) checked @endisset>
                                          </td>
                                      </tr>
                                      <tr>
                                          <td>Salary Info</td>
                                          <td>
                                              <input type="checkbox" name="permission[salaryInfo][add]" value="1" @isset($edit['permission']['salaryInfo']['add']) checked @endisset>
                                          </td>
                                          <td>
                                              <input type="checkbox" name="permission[salaryInfo][view]" value="1" @isset($edit['permission']['salaryInfo']['view']) checked @endisset>
                                          </td>
                                          <td>
                                              <input type="checkbox" name="permission[salaryInfo][edit]" value="1" @isset($edit['permission']['salaryInfo']['edit']) checked @endisset>
                                          </td>
                                          <td>X</td>
                                          <td>
                                              <input type="checkbox" name="permission[salaryInfo][list]" value="1" @isset($edit['permission']['salaryInfo']['list']) checked @endisset>
                                          </td>
                                      </tr>
                                      <tr>
                                          <td>CPF Contribute</td>
                                          <td>
                                              <input type="checkbox" name="permission[contribute][add]" value="1" @isset($edit['permission']['contribute']['add']) checked @endisset>
                                          </td>
                                          <td>X</td>
                                          <td>X</td>
                                          <td>X</td>
                                          <td>X</td>
                                      </tr>
                                      <tr>
                                          <td>Employee Details</td>
                                          <td>X</td>
                                          <td>
                                              <input type="checkbox" name="permission[employeeDetails][view]" value="1" @isset($edit['permission']['employeeDetails']['view']) checked @endisset>
                                          </td>
                                          <td>X</td>
                                          <td>X</td>
                                          <td>X</td>
                                      </tr>
                                      <tr>
                                          <td>Employee Leave</td>
                                          <td>
                                              <input type="checkbox" name="permission[employeeLeaveAdd][add]" value="1" @isset($edit['permission']['employeeLeaveAdd']['add']) checked @endisset>
                                          </td>
                                          <td>X</td>
                                          <td>X</td>
                                          <td>X</td>
                                          <td>X</td>
                                      </tr>
                                      <tr>
                                          <td>Advance Adjust</td>
                                          <td>X</td>
                                          <td>X</td>
                                          <td>
                                              <input type="checkbox" name="permission[advanceAdjust][edit]" value="1" @isset($edit['permission']['advanceAdjust']['edit']) checked @endisset>
                                          </td>
                                          <td>X</td>
                                          <td>
                                              <input type="checkbox" name="permission[advanceAdjust][list]" value="1" @isset($edit['permission']['advanceAdjust']['list']) checked @endisset>
                                          </td>
                                      </tr>
                                      <tr>
                                          <td>Employee Contact Person</td>
                                          <td>
                                              <input type="checkbox" name="permission[employeeContactPerson][add]" value="1" @isset($edit['permission']['employeeContactPerson']['add']) checked @endisset>
                                          </td>
                                          <td>X</td>
                                          <td>
                                              <input type="checkbox" name="permission[employeeContactPerson][edit]" value="1" @isset($edit['permission']['employeeContactPerson']['edit']) checked @endisset>
                                          </td>
                                          <td>X</td>
                                          <td>
                                              <input type="checkbox" name="permission[employeeContactPerson][list]" value="1" @isset($edit['permission']['employeeContactPerson']['list']) checked @endisset>
                                          </td>
                                      </tr>
                                      <tr>
                                          <td>Employee Job Experience</td>
                                          <td>
                                              <input type="checkbox" name="permission[employeeJobExperience][add]" value="1" @isset($edit['permission']['employeeJobExperience']['add']) checked @endisset>
                                          </td>
                                          <td>X</td>
                                          <td>
                                              <input type="checkbox" name="permission[employeeJobExperience][edit]" value="1" @isset($edit['permission']['employeeJobExperience']['edit']) checked @endisset>
                                          </td>
                                          <td>X</td>
                                          <td>
                                              <input type="checkbox" name="permission[employeeJobExperience][list]" value="1" @isset($edit['permission']['employeeJobExperience']['list']) checked @endisset>
                                          </td>
                                      </tr>
                                      <tr>
                                          <td>Iqama Expire List</td>
                                          <td>X</td>
                                          <td>X</td>
                                          <td>X</td>
                                          <td>X</td>
                                          <td>
                                              <input type="checkbox" name="permission[iqamaExpire][list]" value="1" @isset($edit['permission']['iqamaExpire']['list']) checked @endisset>
                                          </td>
                                      </tr>
                                      <tr>
                                          <td>Passport Expire List</td>
                                          <td>X</td>
                                          <td>X</td>
                                          <td>X</td>
                                          <td>X</td>
                                          <td>
                                              <input type="checkbox" name="permission[passportExpire][list]" value="1" @isset($edit['permission']['passportExpire']['list']) checked @endisset>
                                          </td>
                                      </tr>
                                      <tr>
                                          <td>Employee Promosion</td>
                                          <td>X</td>
                                          <td>X</td>
                                          <td>
                                              <input type="checkbox" name="permission[employeePromosion][edit]" value="1" @isset($edit['permission']['employeePromosion']['edit']) checked @endisset>
                                          </td>
                                          <td>#</td>
                                          <td>
                                              <input type="checkbox" name="permission[employeePromosion][list]" value="1" @isset($edit['permission']['employeePromosion']['list']) checked @endisset>
                                          </td>
                                      </tr>
                                      {{-- Employee --}}
                                      {{-- Work History --}}
                                      <tr>
                                          <td>Month Work</td>
                                          <td>
                                              <input type="checkbox" name="permission[monthWork][add]" value="1" @isset($edit['permission']['monthWork']['add']) checked @endisset>
                                          </td>
                                          <td>#</td>
                                          <td>
                                              <input type="checkbox" name="permission[monthWork][edit]" value="1" @isset($edit['permission']['monthWork']['edit']) checked @endisset>
                                          </td>
                                          <td>#</td>
                                          <td>
                                              <input type="checkbox" name="permission[monthWork][list]" value="1" @isset($edit['permission']['monthWork']['list']) checked @endisset>
                                          </td>
                                      </tr>
                                      <tr>
                                          <td>Daily Work In Time</td>
                                          <td>
                                              <input type="checkbox" name="permission[dailyWorkInTime][add]" value="1" @isset($edit['permission']['dailyWorkInTime']['add']) checked @endisset>
                                          </td>
                                          <td>#</td>
                                          <td>#</td>
                                          <td>#</td>
                                          <td>#</td>
                                      </tr>
                                      <tr>
                                          <td>Daily Work Out Time</td>
                                          <td>#</td>
                                          <td>#</td>
                                          <td>
                                              <input type="checkbox" name="permission[dailyWorkOutTime][edit]" value="1" @isset($edit['permission']['dailyWorkOutTime']['edit']) checked @endisset>
                                          </td>
                                          <td>#</td>
                                          <td>
                                              <input type="checkbox" name="permission[dailyWorkOutTime][list]" value="1" @isset($edit['permission']['dailyWorkOutTime']['list']) checked @endisset>
                                          </td>
                                      </tr>
                                      {{-- Work History --}}
                                      {{-- Report --}}
                                      <tr>
                                          <td>All Report</td>
                                          <td>#</td>
                                          <td>
                                              <input type="checkbox" name="permission[allReport][view]" value="1" @isset($edit['permission']['allReport']['view']) checked @endisset>
                                          </td>
                                          <td>#</td>
                                          <td>#</td>
                                          <td>#</td>
                                      </tr>
                                      {{-- Report --}}
                                      {{-- salary --}}
                                      <tr>
                                          <td>Salary Generate</td>
                                          <td>
                                              <input type="checkbox" name="permission[salaryGenerate][add]" value="1" @isset($edit['permission']['salaryGenerate']['add']) checked @endisset>
                                          </td>
                                          <td>
                                              <input type="checkbox" name="permission[salaryGenerate][view]" value="1" @isset($edit['permission']['salaryGenerate']['view']) checked @endisset>
                                          </td>
                                          <td>#</td>
                                          <td>#</td>
                                          <td>#</td>
                                      </tr>
                                      <tr>
                                          <td>Income & Costing</td>
                                          <td>
                                              <input type="checkbox" name="permission[incomeCosting][add]" value="1" @isset($edit['permission']['incomeCosting']['add']) checked @endisset>
                                          </td>
                                          <td>#</td>
                                          <td>
                                              <input type="checkbox" name="permission[incomeCosting][edit]" value="1" @isset($edit['permission']['incomeCosting']['edit']) checked @endisset>
                                          </td>
                                          <td>
                                              <input type="checkbox" name="permission[incomeCosting][delete]" value="1" @isset($edit['permission']['incomeCosting']['delete']) checked @endisset>
                                          </td>
                                          <td>
                                              <input type="checkbox" name="permission[incomeCosting][list]" value="1" @isset($edit['permission']['incomeCosting']['list']) checked @endisset>
                                          </td>
                                      </tr>
                                      {{-- Metarial Tools --}}
                                      <tr>
                                          <td>Metarials & Tools</td>
                                          <td>
                                              <input type="checkbox" name="permission[metarialsTools][add]" value="1" @isset($edit['permission']['metarialsTools']['add']) checked @endisset>
                                          </td>
                                          <td>#</td>
                                          <td>
                                              <input type="checkbox" name="permission[metarialsTools][edit]" value="1" @isset($edit['permission']['metarialsTools']['edit']) checked @endisset>
                                          </td>
                                          <td>
                                              <input type="checkbox" name="permission[metarialsTools][delete]" value="1" @isset($edit['permission']['metarialsTools']['delete']) checked @endisset>
                                          </td>
                                          <td>
                                              <input type="checkbox" name="permission[metarialsTools][list]" value="1" @isset($edit['permission']['metarialsTools']['list']) checked @endisset>
                                          </td>
                                      </tr>
                                      {{-- Vehicle & Building --}}
                                      <tr>
                                          <td>Vehicle Building</td>
                                          <td>
                                              <input type="checkbox" name="permission[vichleBuilding][add]" value="1" @isset($edit['permission']['vichleBuilding']['add']) checked @endisset>
                                          </td>
                                          <td>#</td>
                                          <td>
                                              <input type="checkbox" name="permission[vichleBuilding][edit]" value="1" @isset($edit['permission']['vichleBuilding']['edit']) checked @endisset>
                                          </td>
                                          <td>
                                              <input type="checkbox" name="permission[vichleBuilding][delete]" value="1" @isset($edit['permission']['vichleBuilding']['delete']) checked @endisset>
                                          </td>
                                          <td>
                                              <input type="checkbox" name="permission[vichleBuilding][list]" value="1" @isset($edit['permission']['vichleBuilding']['list']) checked @endisset>
                                          </td>
                                      </tr>

                                  </tbody>
                              </table>
                          </div>
                      </div>
                  </div>
                </div>
                {{-- permission --}}
              </div>


            </div>
            <div class="card-footer card_footer_button text-center">
                <button type="submit" class="btn btn-primary waves-effect">UPDATE</button>
            </div>
        </div>
      </form>
    </div>
</div>
@section('script')
  <script src="{{ asset('contents/admin') }}/datatables/js/jquery.dataTables.min.js"></script>
  <script src="{{ asset('contents/admin') }}/datatables/js/datatables.init.js"></script>
@endsection


<script>
<!--
<!-- Begin
function CheckAll(chk)
{
for (i = 0; i < chk.length; i++)
chk[i].checked = true ;
}

function UnCheckAll(chk)
{
for (i = 0; i < chk.length; i++)
chk[i].checked = false ;
}
//  End -->
</script>

@endsection
