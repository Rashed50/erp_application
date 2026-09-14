@extends('layouts.admin-master')
@section('title') Employee Contact Person @endsection
@section('content')
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Employee Contact Person</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active"> Employee Contact Person</li>
        </ol>
    </div>
</div>
<!-- add division -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        @if(Session::has('success'))
          <div class="alert alert-success alertsuccess" role="alert">
             <strong>Successfully!</strong> Added Employee Contact Person.
          </div>
        @endif
        @if(Session::has('success_update'))
          <div class="alert alert-success alertsuccess" role="alert">
             <strong>Successfully!</strong> Update Employee Contact Person Information.
          </div>
        @endif
        @if(Session::has('duplicate'))
          <div class="alert alert-warning alerterror" role="alert">
             <strong>Opps!</strong> This recode already exist.
          </div>
        @endif
        @if(Session::has('error'))
          <div class="alert alert-warning alerterror" role="alert">
             <strong>Opps!</strong> please try again.
          </div>
        @endif
        @if(Session::has('invalid_employee'))
          <div class="alert alert-warning alerterror" role="alert">
             <strong>Opps!</strong> Invalid Employee Id Please Correct Id & Try Again.
          </div>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
      <form class="form-horizontal" id="registration" action="{{ route('insert-contact-person.info') }}" method="post">
        @csrf
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-8">
                        <h3 class="card-title card_top_title"><i class="fab fa-gg-circle"></i> Employee Contact Person</h3>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="card-body card_form" style="padding-top: 0;">

              <div class="form-group custom_form_group{{ $errors->has('emp_id') ? ' has-error' : '' }}">
                  <label class="control-label d-block" style="text-align: left;">Employee ID:</label>
                  <div>
                    <input type="text" class="form-control typeahead" placeholder="Input Type Employee ID" name="emp_id" id="emp_id_search" onkeyup="empSearch()" onfocus="showResult()" onblur="hideResult()" value="{{ old('emp_id') }}">
                    @if ($errors->has('emp_id'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('emp_id') }}</strong>
                        </span>
                    @endif
                  </div>
                  <div id="showEmpId"></div>
              </div>

              <div class="form-group custom_form_group{{ $errors->has('ecp_name') ? ' has-error' : '' }}">
                  <label class="control-label d-block" style="text-align: left;">Contact Person Name:<span class="req_star">*</span></label>
                  <div>
                    <input type="text" class="form-control" placeholder="Input Contact Person Name" id="ecp_name" name="ecp_name" value="{{old('ecp_name')}}" required >
                    @if ($errors->has('ecp_name'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('ecp_name') }}</strong>
                        </span>
                    @endif
                  </div>
              </div>

              <div class="form-group custom_form_group{{ $errors->has('ecp_mobile1') ? ' has-error' : '' }}">
                  <label class="control-label d-block" style="text-align: left;"> Mobile No:<span class="req_star">*</span></label>
                  <div>
                    <input type="text" class="form-control" placeholder="Input Mobile Number" id="ecp_mobile1" name="ecp_mobile1" value="{{old('ecp_mobile1')}}" required >
                    @if ($errors->has('ecp_mobile1'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('ecp_mobile1') }}</strong>
                        </span>
                    @endif
                  </div>
              </div>

              <div class="form-group custom_form_group">
                  <label class="control-label d-block" style="text-align: left;"> Mobile2 No:</label>
                  <div>
                    <input type="text" class="form-control" placeholder="Input Mobile Number" id="ecp_mobile2" name="ecp_mobile2" value="{{old('ecp_mobile2')}}">
                  </div>
              </div>

              <div class="form-group custom_form_group">
                  <label class="control-label d-block" style="text-align: left;"> Email:</label>
                  <div>
                    <input type="email" class="form-control" placeholder="Input Email Address" id="ecp_email" name="ecp_email" value="{{old('ecp_email')}}">
                  </div>
              </div>

              <div class="form-group custom_form_group{{ $errors->has('ecp_relationship') ? ' has-error' : '' }}">
                  <label class="control-label d-block" style="text-align: left;"> Relationship:<span class="req_star">*</span></label>
                  <div>
                    <input type="text" class="form-control" placeholder="Input Relationship" name="ecp_relationship" value="{{ old('ecp_relationship') }}" required>
                    @if ($errors->has('ecp_relationship'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('ecp_relationship') }}</strong>
                        </span>
                    @endif
                  </div>
              </div>

              <div class="form-group custom_form_group{{ $errors->has('details') ? ' has-error' : '' }}">
                  <label class="control-label d-block" style="text-align: left;"> Address Details:<span class="req_star">*</span></label>
                  <div>
                    <textarea name="details" class="form-control" placeholder="Input Address Details" required>{{ old('details') }}</textarea>
                    @if ($errors->has('details'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('details') }}</strong>
                        </span>
                    @endif
                  </div>
              </div>
            </div>
            <div class="card-footer card_footer_button text-center">
                <button type="submit" class="btn btn-primary waves-effect">SAVE</button>
            </div>
        </div>
      </form>
    </div>
    <div class="col-md-2"></div>
</div>


<div class="row">
    <div class="col-lg-12">
      <div class="card">
          <div class="card-header">
              <div class="row">
                  <div class="col-md-8">
                      <h3 class="card-title card_top_title"><i class="fab fa-gg-circle"></i> Employee Contact Person List</h3>
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
                                      <th>Employee Id</th>
                                      <th>Employee Name</th>
                                      <th>Person Name</th>
                                      <th>Mobile No</th>
                                      <th>Relationship</th>
                                      <th>Manage</th>
                                  </tr>
                              </thead>
                              <tbody>
                                @foreach($all as $item)
                                  <tr>
                                    <td>{{ $item->employee->employee_id }}</td>
                                    <td>{{ $item->employee->employee_name }}</td>
                                    <td>{{ $item->ecp_name }}</td>
                                    <td>{{ $item->ecp_mobile1 }}</td>
                                    <td>{{ $item->ecp_relationship }}</td>
                                    <td>
                                      <a href="{{ route('edit-employee.contact-person',$item->ecp_id) }}" title="edit"><i class="fa fa-pencil-square fa-lg edit_icon"></i></a>
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
</div>

<script type="text/javascript">
function showResult(){
    $("#showEmpId").slideDown();
}
function hideResult(){
    $("#showEmpId").slideUp();
}
</script>
@endsection
