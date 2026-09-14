@extends('layouts.admin-master')
@section('title') Edit Employee Contact Person @endsection
@section('content')
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Edit Employee Contact Person</h4>
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
        @if(Session::has('error'))
          <div class="alert alert-warning alerterror" role="alert">
             <strong>Opps!</strong> please try again.
          </div>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
      <form class="form-horizontal" id="registration" action="{{ route('update-contact-person.info') }}" method="post">
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
              <input type="text" name="id" value="{{ $edit->ecp_id }}">
              <div class="form-group custom_form_group{{ $errors->has('emp_id') ? ' has-error' : '' }}">
                  <label class="control-label d-block" style="text-align: left;">Employee ID:</label>
                  <div>
                    <input type="text" class="form-control typeahead" placeholder="Input Employee ID" name="emp_id" id="emp_id_search" onkeyup="empSearch()" onfocus="showResult()" onblur="hideResult()" value="{{ $edit->employee->employee_id }}">
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
                    <input type="text" class="form-control" placeholder="Input Employee Contact Person Name" id="ecp_name" name="ecp_name" value="{{ $edit->ecp_name }}" required >
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
                    <input type="text" class="form-control" placeholder="Input Mobile Number" id="ecp_mobile1" name="ecp_mobile1" value="{{ $edit->ecp_mobile1 }}" required >
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
                    <input type="text" class="form-control" placeholder="Input Mobile Number" id="ecp_mobile2" name="ecp_mobile2" value="{{ $edit->ecp_mobile2 }}">
                  </div>
              </div>

              <div class="form-group custom_form_group">
                  <label class="control-label d-block" style="text-align: left;"> Email:</label>
                  <div>
                    <input type="email" class="form-control" placeholder="Input Email Address" id="ecp_email" name="ecp_email" value="{{ $edit->ecp_email }}">
                  </div>
              </div>

              <div class="form-group custom_form_group{{ $errors->has('ecp_relationship') ? ' has-error' : '' }}">
                  <label class="control-label d-block" style="text-align: left;"> Relationship:<span class="req_star">*</span></label>
                  <div>

                    <input type="text" class="form-control" placeholder="Input Relationship" name="ecp_relationship" value="{{ $edit->ecp_relationship }}" required>
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
                    <textarea name="details" class="form-control" placeholder="Input Address Details" required>{{ $edit->details }}</textarea>
                    @if ($errors->has('details'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('details') }}</strong>
                        </span>
                    @endif
                  </div>
              </div>
            </div>
            <div class="card-footer card_footer_button text-center">
                <button type="submit" class="btn btn-primary waves-effect">UPDATE</button>
            </div>
        </div>
      </form>
    </div>
    <div class="col-md-2"></div>
</div>
@endsection
