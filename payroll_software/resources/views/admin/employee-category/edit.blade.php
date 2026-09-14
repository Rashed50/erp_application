@extends('layouts.admin-master')
@section('title') Edit Employee Category @endsection
@section('content')

<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Update Employee Trade</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li><a href="{{ route('emp-category') }}">Employee Trade</a></li>
            <li class="active">Update Trade</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        @if(Session::has('error_update'))
          <div class="alert alert-warning alerterror" role="alert">
             <strong>Opps!</strong> please try again.
          </div>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-6">
      <form class="form-horizontal" id="registration" action="{{ route('update-category') }}" method="post">
        @csrf
        <div class="card">
            <div class="card-header">
            </div>
            <div class="card-body card_form" style="padding-top: 0;">

              <div class="form-group custom_form_group row{{ $errors->has('emp_type_id') ? ' has-error' : '' }}">
                  <label class="control-label col-md-4" style="text-align: left;">Employee Type:<span class="req_star">*</span></label>
                  <div class="col-md-8">
                    <input type="hidden" name="id" value="{{ $edit->catg_id }}">
                    <select class="form-select" name="emp_type_id" required>
                      <option value="">Select Employee Type</option>
                      @foreach($getType as $type)
                      <option value="{{ $type->id }}" {{ $edit->emp_type_id == $type->id ? 'selected':'' }}>{{ $type->name }}</option>
                      @endforeach
                    </select>
                    @if ($errors->has('catg_name'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('catg_name') }}</strong>
                        </span>
                    @endif
                  </div>
              </div>
              
               <div class="form-group custom_form_group row{{ $errors->has('emp_trade_head') ? ' has-error' : '' }}">
                <label class="control-label col-md-4" style="text-align: left;">Designation Head:<span class="req_star">*</span></label>
                <div class="col-md-8">
                  <select class="form-select" name="emp_trade_head" required>
                    <option value="">Select Designation Head</option>
                    @foreach($trade_heads as $arecord)
                    <option value="{{ $arecord->dh_auto_id }}" {{ $edit->dh_auto_id == $arecord->dh_auto_id ? 'selected':'' }}>{{ $arecord->des_head_name }}</option>
                    @endforeach
                  </select>

                  @if ($errors->has('emp_trade_head'))
                      <span class="invalid-feedback" role="alert">
                          <strong>{{ $errors->first('emp_trade_head') }}</strong>
                      </span>
                  @endif
                </div>
              </div>

              <div class="form-group custom_form_group row{{ $errors->has('catg_name') ? ' has-error' : '' }}">
                  <label class="control-label col-md-4" style="text-align: left;">Trade Name:<span class="req_star">*</span></label>
                  <div class="col-md-8">

                    <input type="text" placeholder="Input Designation Name" class="form-control" id="catg_name" name="catg_name" value="{{ $edit->catg_name }}" required>
                    @if ($errors->has('catg_name'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('catg_name') }}</strong>
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
    <div class="col-md-3"></div>
</div>

@endsection
