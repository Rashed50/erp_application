@extends('layouts.admin-master')
@section('title') Edit Division @endsection
@section('content')

<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Edit Division</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li><a href="{{ route('add-division') }}">Add Division</a></li>
            <li class="active">Edit</li>
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
        @if(Session::has('already_exit'))
          <div class="alert alert-warning alerterror" role="alert">
             <strong>Opps!</strong> This Division Already Exist.
          </div>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-md-4"></div>
    <div class="col-md-4">
      <form class="form-horizontal" id="registration" action="{{ route('update-division') }}" method="post">
        @csrf
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-8">
                        <h3 class="card-title card_top_title"><i class="fab fa-gg-circle"></i> Division Information</h3>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="card-body card_form" style="padding-top: 0;">
              <div class="form-group custom_form_group{{ $errors->has('country_id') ? ' has-error' : '' }}">
                  <label class="control-label d-block" style="text-align: left;">Country Name:<span class="req_star">*</span></label>
                  <div>
                    <input type="hidden" name="id" value="{{ $edit->division_id }}">
                    <select class="form-control" name="country_id" required>
                        <option value="">Select Country</option>
                        @foreach($countries as $country)
                        <option value="{{ $country->id }}" {{ $country->id == $edit->country->id ? 'selected':'' }}>{{ $country->country_name }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('country_id'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('country_id') }}</strong>
                        </span>
                    @endif
                  </div>
              </div>
              <div class="form-group custom_form_group{{ $errors->has('division_name') ? ' has-error' : '' }}">
                  <label class="control-label d-block" style="text-align: left;">Division Name:<span class="req_star">*</span></label>
                  <div>
                    <input type="text" placeholder="please enter division name" class="form-control" id="division_name" name="division_name" value="{{ $edit->division_name }}" required>
                    @if ($errors->has('division_name'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('division_name') }}</strong>
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
    <div class="col-md-4"></div>
</div>

@endsection
