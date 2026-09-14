@extends('layouts.admin-master')
@section('title') Employee Contribution Report Generat @endsection
@section('content')

<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title"> Employee Contribution Report Generat </h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active"> Employee Contribution</li>
        </ol>
    </div>
</div>
<!-- response Message -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        @if(Session::has('success'))
          <div class="alert alert-success alertsuccess" role="alert">
             <strong>Successfully!</strong> Salary Processing Done.
          </div>
        @endif
        @if(Session::has('error'))
          <div class="alert alert-warning alerterror" role="alert">
             <strong>Opps!</strong> Employee Id Dosen,t Match!.
          </div>
        @endif
    </div>
    <div class="col-md-2"></div>
</div>
<!-- Form -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-lg-8">
        <form class="form-horizontal" id="registration" method="post" action="{{ route('CPF-contribution-report-generat') }}" >
          @csrf
          <div class="card">
              <div class="card-header"></div>
              <div class="card-body card_form">
                <div class="form-group row custom_form_group">
                    <label class="col-sm-3 control-label">Employee ID:</label>
                    <div class="col-sm-7">
                      <input type="text" class="form-control typeahead" placeholder="Input Employee ID" name="emp_id" id="emp_id_search" onkeyup="empSearch()" onfocus="showResult()" onblur="hideResult()">
                      <div id="showEmpId"></div>
                      <span id="error_show" class="d-none" style="color: red"></span>
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
                  <button type="submit" class="btn btn-primary waves-effect">Processing</button>
              </div>
          </div>
        </form>
    </div>
    <div class="col-md-2"></div>
</div>
@endsection
