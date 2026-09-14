@extends('layouts.admin-master')
@section('title') Edit Employee Job Experience @endsection
@section('content')
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Edit Employee Job Experience Information</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active"> Employee Job Experience</li>
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
      <form class="form-horizontal" id="registration" action="{{ route('update-job-experience.info') }}" method="post">
        @csrf
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-8">
                        <h3 class="card-title card_top_title"><i class="fab fa-gg-circle"></i> Employee Job Experience</h3>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="card-body card_form" style="padding-top: 0;">
              <input type="hidden" name="id" value="{{ $edit->ejex_id }}">
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

              <div class="form-group custom_form_group{{ $errors->has('ejex_title') ? ' has-error' : '' }}">
                  <label class="control-label d-block" style="text-align: left;">Job Title:<span class="req_star">*</span></label>
                  <div>
                    <input type="text" class="form-control" placeholder="Input Job Name" id="ejex_title" name="ejex_title" value="{{ $edit->ejex_title }}" required >
                    @if ($errors->has('ejex_title'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('ejex_title') }}</strong>
                        </span>
                    @endif
                  </div>
              </div>

              <div class="form-group custom_form_group{{ $errors->has('company_name') ? ' has-error' : '' }}">
                  <label class="control-label d-block" style="text-align: left;"> Company Name:<span class="req_star">*</span></label>
                  <div>
                    <input type="text" class="form-control" placeholder="Input Company Name" id="company_name" name="company_name" value="{{ $edit->company_name }}" required >
                    @if ($errors->has('company_name'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('company_name') }}</strong>
                        </span>
                    @endif
                  </div>
              </div>

              <div class="form-group custom_form_group{{ $errors->has('designation') ? ' has-error' : '' }}">
                  <label class="control-label d-block" style="text-align: left;"> Designation:<span class="req_star">*</span></label>
                  <div>
                    <input type="text" class="form-control" placeholder="Input Designation" id="designation" name="designation" value="{{ $edit->designation }}" required >
                    @if ($errors->has('designation'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('designation') }}</strong>
                        </span>
                    @endif
                  </div>
              </div>

              <div class="form-group custom_form_group{{ $errors->has('responsibity') ? ' has-error' : '' }}">
                  <label class="control-label d-block" style="text-align: left;"> Responsibity:<span class="req_star">*</span></label>
                  <div>
                    <textarea class="form-control" placeholder="Input Responsibity" id="responsibity" name="responsibity" required> {{old('responsibity')}}</textarea>
                    @if ($errors->has('responsibity'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('responsibity') }}</strong>
                        </span>
                    @endif
                  </div>
              </div>

              <div class="form-group custom_form_group{{ $errors->has('starting_date') ? ' has-error' : '' }}">
                  <label class="control-label d-block" style="text-align: left;"> Starting Date:<span class="req_star">*</span></label>
                  <div>
                    <input type="date" class="form-control" id="starting_date" name="starting_date" value="{{ $edit->starting_date }}" required >
                    @if ($errors->has('starting_date'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('starting_date') }}</strong>
                        </span>
                    @endif
                  </div>
              </div>

              <div class="form-group custom_form_group{{ $errors->has('end_date') ? ' has-error' : '' }}">
                  <label class="control-label d-block" style="text-align: left;"> End Date:<span class="req_star">*</span></label>
                  <div>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $edit->end_date }}" required min="{{ Carbon\Carbon::now()->addDays(1)->format('Y-m-d') }}">
                    @if ($errors->has('end_date'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('end_date') }}</strong>
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
