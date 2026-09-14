@extends('layouts.admin-master')
@section('title') Report Founding Source @endsection
@section('content')
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Report Founding Source</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active"> Report Founding Source</li>
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
      <form class="form-horizontal" id="registration" target="_blank" action="{{ route('report-income-process') }}" method="post">
        @csrf
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-8">
                        <h3 class="card-title card_top_title"><i class="fab fa-gg-circle"></i> Report Founding Source</h3>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="card-body card_form" style="padding-top: 0;">
              <div class="row">
                  <div class="col-md-2"></div>
                  <div class="col-md-4">
                    <div class="form-group custom_form_group">
                        <label class="control-label d-block" style="text-align: left;">Start Date:</label>
                        <div>
                            <input type="date" class="form-control" name="start_date" value="{{ old('start_date') }}" required>
                        </div>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group custom_form_group">
                        <label class="control-label d-block" style="text-align: left;">End Date:</label>
                        <div>
                            <input type="date" class="form-control" name="end_date" value="{{ old('end_date') }}" required>
                        </div>
                    </div>
                  </div>
                  <div class="col-md-2"></div>
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
