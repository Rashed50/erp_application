@extends('layouts.admin-master')
@section('title') Project Basic Total Work Hours @endsection
@section('content')

<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title"> Project Basic Total Work Hours</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active"> Project Work Report</li>
        </ol>
    </div>
</div>

<!-- response massage -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        @if(Session::has('success'))
          <div class="alert alert-success alertsuccess" role="alert">
             <strong>Successfully!</strong> Added New Banner.
          </div>
        @endif
        @if(Session::has('error'))
          <div class="alert alert-warning alerterror" role="alert">
             <strong>Opps!</strong> please try again.
          </div>
        @endif
    </div>
    <div class="col-md-2"></div>
</div>


<div class="row">
    <div class="col-md-2"></div>
    <div class="col-lg-8">
        <form class="form-horizontal" id="registration" target="_blank" method="post" action="{{ route('project-wise-total-hours.generat') }}" enctype="multipart/form-data">
          @csrf
          <div class="card">
              <div class="card-header">
              </div>
              <div class="card-body card_form">

                <div class="form-group row custom_form_group{{ $errors->has('proj_id') ? ' has-error' : '' }}">
                    <label class="col-sm-3 control-label">Project Name:<span class="req_star">*</span></label>
                    <div class="col-sm-7">
                      <select class="form-control" name="proj_id">
                        @foreach($project as $proj)
                          <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                        @endforeach
                      </select>
                    </div>
                </div>

                <div class="form-group row custom_form_group{{ $errors->has('start_date') ? ' has-error' : '' }}">
                    <label class="col-sm-3 control-label">Start Date:<span class="req_star">*</span></label>
                    <div class="col-sm-7">
                      <input type="date" class="form-control" name="start_date" value="<?= date("Y-m-d") ?>">
                    </div>
                </div>

                <div class="form-group row custom_form_group{{ $errors->has('end_date') ? ' has-error' : '' }}">
                    <label class="col-sm-3 control-label">End Date:<span class="req_star">*</span></label>
                    <div class="col-sm-7">
                      <input type="date" class="form-control" name="end_date" value="<?= date("Y-m-d") ?>">
                    </div>
                </div>

              </div>
              <div class="card-footer card_footer_button text-center">
                  <button type="submit" class="btn btn-primary waves-effect">PROCESS</button>
              </div>
          </div>
        </form>
    </div>
    <div class="col-md-2"></div>
</div>
@endsection
