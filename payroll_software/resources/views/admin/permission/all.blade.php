@extends('layouts.admin-master')
@section('title') Create Role @endsection
@section('internal-css')
  <link href="{{ asset('contents/admin') }}/datatables/css/jquery.dataTables.min.css" rel="stylesheet">
@endsection
@section('content')
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title"> User Role Permision</h4>
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
      <form class="form-horizontal" action="{{ route('permission.store') }}" method="post">
        @csrf
        <div class="card">
            <div class="card-header"></div>
            <div class="card-body card_form" style="padding-top: 0;">

              <div class="row">
                <div class="col-md-4">
                  <div class="form-group row custom_form_group{{ $errors->has('role_id') ? ' has-error' : '' }}" style="margin-bottom: 0">
                      <label class="control-label col-md-4">Role Name:<span class="req_star">*</span></label>
                      <div class="col-md-8">
                        <select class="form-control" name="role_id">
                          <option value="">Select Role</option>
                          @foreach ($all as $data)
                            <option value="{{ $data->role_auto_id }}">{{ $data->role_name }}</option>
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
                      <div class="card-header">
                        <input type="button" name="Check_All" id="Check_All" value="Check All"
                          onClick="CheckAll(document.myform.check_list)">

                          <input type="button" name="Un_CheckAll" id="Un_CheckAll" value="Uncheck All"
                          onClick="UnCheckAll(document.myform.check_list)">
                      </div>
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
                                              <input type="checkbox" name="permission[adminSetting][view]" value="1">
                                          </td>
                                          <td>X</td>
                                          <td>X</td>
                                          <td>X</td>
                                      </tr>

                                      <tr>
                                          <td>Employee</td>
                                          <td>X</td>
                                          <td>
                                              <input type="checkbox" name="permission[employeeMenu][view]" value="1">
                                          </td>
                                          <td>X</td>
                                          <td>X</td>
                                          <td>X</td>
                                      </tr>

                                      <tr>
                                          <td>User</td>
                                          <td>
                                              <input type="checkbox" name="permission[user][add]" value="1">
                                          </td>
                                          <td>
                                              <input type="checkbox" name="permission[user][view]" value="1">
                                          </td>
                                          <td>
                                              <input type="checkbox" name="permission[user][edit]" value="1">
                                          </td>
                                          <td>
                                              <input type="checkbox" name="permission[user][delete]" value="1">
                                          </td>
                                          <td>
                                              <input type="checkbox" name="permission[user][list]" value="1">
                                          </td>
                                      </tr>

                                      <tr>
                                          <td>Role</td>
                                          <td>
                                              <input type="checkbox" name="permission[role][add]" value="1">
                                          </td>
                                          <td>
                                              <input type="checkbox" name="permission[role][view]" value="1">
                                          </td>
                                          <td>
                                              <input type="checkbox" name="permission[role][edit]" value="1">
                                          </td>
                                          <td>
                                              <input type="checkbox" name="permission[role][delete]" value="1">
                                          </td>
                                          <td>
                                              <input type="checkbox" name="permission[role][list]" value="1">
                                          </td>
                                      </tr>
                                      <tr>
                                          <td>Role Permision</td>
                                          <td>
                                              <input type="checkbox" name="permission[rolePermission][add]" value="1">
                                          </td>
                                          <td>
                                              <input type="checkbox" name="permission[rolePermission][view]" value="1">
                                          </td>
                                          <td>
                                              <input type="checkbox" name="permission[rolePermission][edit]" value="1">
                                          </td>
                                          <td>
                                              <input type="checkbox" name="permission[rolePermission][delete]" value="1">
                                          </td>
                                          <td>
                                              <input type="checkbox" name="permission[rolePermission][list]" value="1">
                                          </td>
                                      </tr>

                                      <tr>
                                          <td>Company Profile</td>
                                          <td>
                                              <input type="checkbox" name="permission[company][add]" value="1">
                                          </td>
                                          <td> X </td>
                                          <td>
                                              <input type="checkbox" name="permission[company][edit]" value="1">
                                          </td>
                                          <td> X </td>
                                          <td>
                                              <input type="checkbox" name="permission[company][list]" value="1">
                                          </td>
                                      </tr>

                                      <tr>
                                          <td>Sub Company </td>
                                          <td>
                                              <input type="checkbox" name="permission[subcompany][add]" value="1">
                                          </td>
                                          <td>
                                              <input type="checkbox" name="permission[subcompany][view]" value="1">
                                          </td>
                                          <td>
                                              <input type="checkbox" name="permission[subcompany][edit]" value="1">
                                          </td>
                                          <td>
                                              <input type="checkbox" name="permission[subcompany][delete]" value="1">
                                          </td>
                                          <td>
                                              <input type="checkbox" name="permission[subcompany][list]" value="1">
                                          </td>
                                      </tr>

                                      <tr>
                                          <td>E voucher </td>
                                          <td>
                                              <input type="checkbox" name="permission[eVoucher][add]" value="1">
                                          </td>
                                          <td>X</td>
                                          <td>X</td>
                                          <td>X</td>
                                          <td>X</td>
                                      </tr>

                                      <tr>
                                          <td>Banner</td>
                                          <td>
                                              <input type="checkbox" name="permission[banner][add]" value="1">
                                          </td>
                                          <td>
                                              <input type="checkbox" name="permission[banner][view]" value="1">
                                          </td>
                                          <td>
                                              <input type="checkbox" name="permission[banner][edit]" value="1">
                                          </td>
                                          <td>
                                              <input type="checkbox" name="permission[banner][delete]" value="1">
                                          </td>
                                          <td>
                                              <input type="checkbox" name="permission[banner][list]" value="1">
                                          </td>
                                      </tr>

                                      <tr>
                                          <td>Country</td>
                                          <td>
                                              <input type="checkbox" name="permission[country][add]" value="1">
                                          </td>
                                          <td>
                                              <input type="checkbox" name="permission[country][view]" value="1">
                                          </td>
                                          <td>
                                              <input type="checkbox" name="permission[country][edit]" value="1">
                                          </td>
                                          <td>
                                              <input type="checkbox" name="permission[country][delete]" value="1">
                                          </td>
                                          <td>
                                              <input type="checkbox" name="permission[country][list]" value="1">
                                          </td>
                                      </tr>

                                      <tr>
                                          <td>Division</td>
                                          <td>
                                              <input type="checkbox" name="permission[division][add]" value="1">
                                          </td>
                                          <td>
                                              <input type="checkbox" name="permission[division][view]" value="1">
                                          </td>
                                          <td>
                                              <input type="checkbox" name="permission[division][edit]" value="1">
                                          </td>
                                          <td>
                                              <input type="checkbox" name="permission[division][delete]" value="1">
                                          </td>
                                          <td>
                                              <input type="checkbox" name="permission[division][list]" value="1">
                                          </td>
                                      </tr>


                                      <tr>
                                          <td>District</td>
                                          <td>
                                              <input type="checkbox" name="permission[district][add]" value="1">
                                          </td>
                                          <td>
                                              <input type="checkbox" name="permission[district][view]" value="1">
                                          </td>
                                          <td>
                                              <input type="checkbox" name="permission[district][edit]" value="1">
                                          </td>
                                          <td>
                                              <input type="checkbox" name="permission[district][delete]" value="1">
                                          </td>
                                          <td>
                                              <input type="checkbox" name="permission[district][list]" value="1">
                                          </td>
                                      </tr>

                                      <tr>
                                          <td>Designation</td>
                                          <td>
                                              <input type="checkbox" name="permission[designation][add]" value="1">
                                          </td>
                                          <td>X</td>
                                          <td>
                                              <input type="checkbox" name="permission[designation][edit]" value="1">
                                          </td>
                                          <td>X</td>
                                          <td>
                                              <input type="checkbox" name="permission[designation][list]" value="1">
                                          </td>
                                      </tr>

                                      <tr>
                                          <td>Sponser</td>
                                          <td>
                                              <input type="checkbox" name="permission[sponser][add]" value="1">
                                          </td>
                                          <td>X</td>
                                          <td>
                                              <input type="checkbox" name="permission[sponser][edit]" value="1">
                                          </td>
                                          <td>
                                              <input type="checkbox" name="permission[sponser][delete]" value="1">
                                          </td>
                                          <td>
                                              <input type="checkbox" name="permission[sponser][list]" value="1">
                                          </td>
                                      </tr>

                                      <tr>
                                          <td>Qr Code</td>
                                          <td>
                                              <input type="checkbox" name="permission[qrCode][add]" value="1">
                                          </td>
                                          <td>X</td>
                                          <td>X</td>
                                          <td>#</td>
                                          <td>#</td>
                                      </tr>

                                      {{-- end admin setting --}}
                                      {{-- start project info --}}
                                      <tr>
                                          <td>Project Info</td>
                                          <td>
                                              <input type="checkbox" name="permission[projectInfo][add]" value="1">
                                          </td>
                                          <td>
                                              <input type="checkbox" name="permission[projectInfo][view]" value="1">
                                          </td>
                                          <td>
                                              <input type="checkbox" name="permission[projectInfo][edit]" value="1">
                                          </td>
                                          <td>X</td>
                                          <td>
                                              <input type="checkbox" name="permission[projectInfo][list]" value="1">
                                          </td>
                                      </tr>

                                      <tr>
                                          <td>Project In Charge</td>
                                          <td>
                                              <input type="checkbox" name="permission[projectInCharge][add]" value="1">
                                          </td>
                                          <td>X</td>
                                          <td>X</td>
                                          <td>X</td>
                                          <td>
                                              <input type="checkbox" name="permission[projectInCharge][list]" value="1">
                                          </td>
                                      </tr>

                                      <tr>
                                          <td>Project Image</td>
                                          <td>
                                              <input type="checkbox" name="permission[projectImage][add]" value="1">
                                          </td>
                                          <td>X</td>
                                          <td>X</td>
                                          <td>
                                              <input type="checkbox" name="permission[projectImage][delete]" value="1">
                                          </td>
                                          <td>
                                              <input type="checkbox" name="permission[projectImage][list]" value="1">
                                          </td>
                                      </tr>
                                      {{-- end project info --}}

                                      {{-- Approval --}}
                                      <tr>
                                          <td>Income Approval</td>
                                          <td>#</td>
                                          <td>#</td>
                                          <td>
                                              <input type="checkbox" name="permission[incomeApprove][edit]" value="1">
                                          </td>
                                          <td>#</td>
                                          <td>
                                              <input type="checkbox" name="permission[incomeApprove][list]" value="1">
                                          </td>
                                      </tr>
                                      <tr>
                                          <td>Expenditure Approval</td>
                                          <td>#</td>
                                          <td>#</td>
                                          <td>
                                              <input type="checkbox" name="permission[expenditureApprove][edit]" value="1">
                                          </td>
                                          <td>#</td>
                                          <td>
                                              <input type="checkbox" name="permission[expenditureApprove][list]" value="1">
                                          </td>
                                      </tr>
                                      <tr>
                                          <td>Job Approval</td>
                                          <td>#</td>
                                          <td>#</td>
                                          <td>
                                              <input type="checkbox" name="permission[jobApprove][edit]" value="1">
                                          </td>
                                          <td>#</td>
                                          <td>
                                              <input type="checkbox" name="permission[jobApprove][list]" value="1">
                                          </td>
                                      </tr>
                                      <tr>
                                          <td>Leave Approval</td>
                                          <td>X</td>
                                          <td>X</td>
                                          <td>
                                              <input type="checkbox" name="permission[leaveApprove][edit]" value="1">
                                          </td>
                                          <td>
                                              <input type="checkbox" name="permission[leaveApprove][delete]" value="1">
                                          </td>
                                          <td>
                                              <input type="checkbox" name="permission[leaveApprove][list]" value="1">
                                          </td>
                                      </tr>
                                      {{-- Approval --}}

                                      {{-- Employee --}}
                                      <tr>
                                          <td>Employee Info</td>
                                          <td>
                                              <input type="checkbox" name="permission[employeeInfo][add]" value="1">
                                          </td>
                                          <td>
                                              <input type="checkbox" name="permission[employeeInfo][view]" value="1">
                                          </td>
                                          <td>
                                              <input type="checkbox" name="permission[employeeInfo][edit]" value="1">
                                          </td>
                                          <td>X</td>
                                          <td>
                                              <input type="checkbox" name="permission[employeeInfo][list]" value="1">
                                          </td>
                                      </tr>
                                      <tr>
                                          <td>Salary Info</td>
                                          <td>
                                              <input type="checkbox" name="permission[salaryInfo][add]" value="1">
                                          </td>
                                          <td>
                                              <input type="checkbox" name="permission[salaryInfo][view]" value="1">
                                          </td>
                                          <td>
                                              <input type="checkbox" name="permission[salaryInfo][edit]" value="1">
                                          </td>
                                          <td>X</td>
                                          <td>
                                              <input type="checkbox" name="permission[salaryInfo][list]" value="1">
                                          </td>
                                      </tr>
                                      {{-- salary pending --}}
                                      <tr>
                                          <td>Salary Pending</td>
                                          <td>X</td>
                                          <td>
                                              <input type="checkbox" name="permission[salaryPending][view]" value="1">
                                          </td>
                                          <td>X</td>
                                          <td>X</td>
                                          <td>X</td>
                                      </tr>
                                      {{-- salary Payment --}}
                                      <tr>
                                          <td>Salary Payment</td>
                                          <td>X</td>
                                          <td>
                                              <input type="checkbox" name="permission[salaryPayment][view]" value="1">
                                          </td>
                                          <td>X</td>
                                          <td>X</td>
                                          <td>X</td>
                                      </tr>
                                      {{-- salary Correction --}}
                                      <tr>
                                          <td>Salary Correction</td>
                                          <td>X</td>
                                          <td>
                                              <input type="checkbox" name="permission[salaryCorrection][view]" value="1">
                                          </td>
                                          <td>X</td>
                                          <td>X</td>
                                          <td>X</td>
                                      </tr>
                                      {{-- cpf contribute --}}
                                      <tr>
                                          <td>CPF Contribute</td>
                                          <td>
                                              <input type="checkbox" name="permission[contribute][add]" value="1">
                                          </td>
                                          <td>X</td>
                                          <td>X</td>
                                          <td>X</td>
                                          <td>X</td>
                                      </tr>
                                      {{-- cpf contribute --}}
                                      <tr>
                                          <td>Anual Fee</td>
                                          <td>
                                              <input type="checkbox" name="permission[annualFee][add]" value="1">
                                          </td>
                                          <td>
                                            <input type="checkbox" name="permission[annualFee][view]" value="1">
                                          </td>
                                          <td>
                                            <input type="checkbox" name="permission[annualFee][edit]" value="1">
                                          </td>
                                          <td>X</td>
                                          <td>X</td>
                                      </tr>
                                      {{-- cpf contribute --}}
                                      <tr>
                                          <td>Set Iqama Cost</td>
                                          <td>
                                              <input type="checkbox" name="permission[annualFee][add]" value="1">
                                          </td>
                                          <td>X</td>
                                          <td>X</td>
                                          <td>X</td>
                                          <td>X</td>
                                      </tr>
                                      {{-- Advance Payment --}}
                                      <tr>
                                          <td>Advance Payment</td>
                                          <td>
                                              <input type="checkbox" name="permission[advancePay][add]" value="1">
                                          </td>
                                          <td>
                                            <input type="checkbox" name="permission[advancePay][view]" value="1">
                                          </td>
                                          <td>
                                            <input type="checkbox" name="permission[advancePay][edit]" value="1">
                                          </td>
                                          <td>
                                            <input type="checkbox" name="permission[advancePay][delete]" value="1">
                                          </td>
                                          <td>
                                            <input type="checkbox" name="permission[advancePay][list]" value="1">
                                          </td>
                                      </tr>
                                      {{-- employee summary adjustment --}}
                                      <tr>
                                          <td> Employee Summary Adjustment </td>
                                          <td>
                                              <input type="checkbox" name="permission[employeeSummaryAdjustment][add]" value="1">
                                          </td>
                                          <td>
                                            <input type="checkbox" name="permission[employeeSummaryAdjustment][view]" value="1">
                                          </td>
                                          <td>
                                            <input type="checkbox" name="permission[employeeSummaryAdjustment][edit]" value="1">
                                          </td>
                                          <td>
                                            <input type="checkbox" name="permission[employeeSummaryAdjustment][delete]" value="1">
                                          </td>
                                          <td>
                                            <input type="checkbox" name="permission[employeeSummaryAdjustment][list]" value="1">
                                          </td>
                                      </tr>


                                      <tr>
                                          <td>Employee Details</td>
                                          <td>X</td>
                                          <td>
                                              <input type="checkbox" name="permission[employeeDetails][view]" value="1">
                                          </td>
                                          <td>X</td>
                                          <td>X</td>
                                          <td>X</td>
                                      </tr>
                                      <tr>
                                          <td>Employee Leave</td>
                                          <td>
                                              <input type="checkbox" name="permission[employeeLeaveAdd][add]" value="1">
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
                                              <input type="checkbox" name="permission[advanceAdjust][edit]" value="1">
                                          </td>
                                          <td>X</td>
                                          <td>
                                              <input type="checkbox" name="permission[advanceAdjust][list]" value="1">
                                          </td>
                                      </tr>
                                      <tr>
                                          <td>Employee Contact Person</td>
                                          <td>
                                              <input type="checkbox" name="permission[employeeContactPerson][add]" value="1">
                                          </td>
                                          <td>X</td>
                                          <td>
                                              <input type="checkbox" name="permission[employeeContactPerson][edit]" value="1">
                                          </td>
                                          <td>X</td>
                                          <td>
                                              <input type="checkbox" name="permission[employeeContactPerson][list]" value="1">
                                          </td>
                                      </tr>
                                      <tr>
                                          <td>Employee Job Experience</td>
                                          <td>
                                              <input type="checkbox" name="permission[employeeJobExperience][add]" value="1">
                                          </td>
                                          <td>X</td>
                                          <td>
                                              <input type="checkbox" name="permission[employeeJobExperience][edit]" value="1">
                                          </td>
                                          <td>X</td>
                                          <td>
                                              <input type="checkbox" name="permission[employeeJobExperience][list]" value="1">
                                          </td>
                                      </tr>
                                      <tr>
                                          <td>Iqama Expire List</td>
                                          <td>X</td>
                                          <td>X</td>
                                          <td>X</td>
                                          <td>X</td>
                                          <td>
                                              <input type="checkbox" name="permission[iqamaExpire][list]" value="1">
                                          </td>
                                      </tr>
                                      <tr>
                                          <td>Passport Expire List</td>
                                          <td>X</td>
                                          <td>X</td>
                                          <td>X</td>
                                          <td>X</td>
                                          <td>
                                              <input type="checkbox" name="permission[passportExpire][list]" value="1">
                                          </td>
                                      </tr>
                                      <tr>
                                          <td>Employee Promosion</td>
                                          <td>X</td>
                                          <td>X</td>
                                          <td>
                                              <input type="checkbox" name="permission[employeePromosion][edit]" value="1">
                                          </td>
                                          <td>#</td>
                                          <td>
                                              <input type="checkbox" name="permission[employeePromosion][list]" value="1">
                                          </td>
                                      </tr>
                                      {{-- Employee --}}
                                      {{-- Work History --}}
                                      <tr>
                                          <td>Month Work</td>
                                          <td>
                                              <input type="checkbox" name="permission[monthWork][add]" value="1">
                                          </td>
                                          <td>#</td>
                                          <td>
                                              <input type="checkbox" name="permission[monthWork][edit]" value="1">
                                          </td>
                                          <td>#</td>
                                          <td>
                                              <input type="checkbox" name="permission[monthWork][list]" value="1">
                                          </td>
                                      </tr>
                                      <tr>
                                          <td>Daily Work In Time</td>
                                          <td>
                                              <input type="checkbox" name="permission[dailyWorkInTime][add]" value="1">
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
                                              <input type="checkbox" name="permission[dailyWorkOutTime][edit]" value="1">
                                          </td>
                                          <td>#</td>
                                          <td>
                                              <input type="checkbox" name="permission[dailyWorkOutTime][list]" value="1">
                                          </td>
                                      </tr>
                                      {{-- Work History --}}
                                      {{-- Report --}}
                                      <tr>
                                          <td>All Report</td>
                                          <td>#</td>
                                          <td>
                                              <input type="checkbox" name="permission[allReport][view]" value="1">
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
                                              <input type="checkbox" name="permission[salaryGenerate][add]" value="1">
                                          </td>
                                          <td>
                                              <input type="checkbox" name="permission[salaryGenerate][view]" value="1">
                                          </td>
                                          <td>#</td>
                                          <td>#</td>
                                          <td>#</td>
                                      </tr>
                                      <tr>
                                          <td>Income & Costing</td>
                                          <td>
                                              <input type="checkbox" name="permission[incomeCosting][add]" value="1">
                                          </td>
                                          <td>#</td>
                                          <td>
                                              <input type="checkbox" name="permission[incomeCosting][edit]" value="1">
                                          </td>
                                          <td>
                                              <input type="checkbox" name="permission[incomeCosting][delete]" value="1">
                                          </td>
                                          <td>
                                              <input type="checkbox" name="permission[incomeCosting][list]" value="1">
                                          </td>
                                      </tr>
                                      {{-- Metarial Tools --}}
                                      <tr>
                                          <td>Metarials & Tools</td>
                                          <td>
                                              <input type="checkbox" name="permission[metarialsTools][add]" value="1">
                                          </td>
                                          <td>#</td>
                                          <td>
                                              <input type="checkbox" name="permission[metarialsTools][edit]" value="1">
                                          </td>
                                          <td>
                                              <input type="checkbox" name="permission[metarialsTools][delete]" value="1">
                                          </td>
                                          <td>
                                              <input type="checkbox" name="permission[metarialsTools][list]" value="1">
                                          </td>
                                      </tr>
                                      {{-- Vehicle & Building --}}
                                      <tr>
                                          <td>Vehicle Building</td>
                                          <td>
                                              <input type="checkbox" name="permission[vichleBuilding][add]" value="1">
                                          </td>
                                          <td>#</td>
                                          <td>
                                              <input type="checkbox" name="permission[vichleBuilding][edit]" value="1">
                                          </td>
                                          <td>
                                              <input type="checkbox" name="permission[vichleBuilding][delete]" value="1">
                                          </td>
                                          <td>
                                              <input type="checkbox" name="permission[vichleBuilding][list]" value="1">
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
                <button type="submit" class="btn btn-primary waves-effect">SAVE</button>
            </div>
        </div>
      </form>
    </div>
</div>

<!-- division list -->
<div class="row">
    <div class="col-md-1"></div>
    <div class="col-lg-10">
      <div class="card">
          <div class="card-header">
              <div class="row">
                  <div class="col-md-8">
                      <h3 class="card-title card_top_title"><i class="fab fa-gg-circle"></i> User Role</h3>
                  </div>
                  <div class="clearfix"></div>
              </div>
          </div>
          <div class="card-body">
              <div class="row">
                  <div class="col-12">
                      <div class="table-responsive">
                          <table id="alltableinfo" class="table table-bordered custom_table mb-0">
                              <thead>
                                  <tr>
                                      <th>SL No</th>
                                      <th>Role Name</th>
                                      <th>Manage</th>
                                  </tr>
                              </thead>
                              <tbody>
                                @foreach ($getAll as $item)
                                  <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->role->role_name }}</td>
                                    <td>
                                      <a href="{{ route('permission.edit',$item->perm_id) }}" title="edit"><i class="fa fa-pencil-square fa-lg edit_icon"></i></a>
                                    </td>
                                  </tr>
                                @endforeach
                              </tbody>
                          </table>
                      </div>
                  </div>
              </div>
          </div>
      </div>
    </div>
    <div class="col-md-1"></div>
</div>
@section('script')
  <script src="{{ asset('contents/admin') }}/datatables/js/jquery.dataTables.min.js"></script>
  <script src="{{ asset('contents/admin') }}/datatables/js/datatables.init.js"></script>
@endsection


<script type="text/javascript">
    $(document).on('click', '#Check_All',function(){
      $("input[value=1]").prop('checked',true);
    });
    $(document).on('click', '#Un_CheckAll',function(){
      $("input[value=1]").prop('checked',false);
    });
</script>

@endsection
