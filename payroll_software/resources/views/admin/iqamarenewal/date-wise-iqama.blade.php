@extends('layouts.admin-master')
@section('title') Project Wise Employees @endsection
@section('content')
<div class="row bread_part">
  <div class="col-sm-12 bread_col">
    <h4 class="pull-left page-title bread_title"> Employees Iqama Expenses Renewal Report</h4>
    <ol class="breadcrumb pull-right">
      <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
      <li class="active"> Employees Report </li>
    </ol>
  </div>
</div>
<!-- add division -->

<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-7">
        @if(Session::has('success'))
        <div class="alert alert-success alertsuccess" role="alert" style="margin-left: -20px">
            <strong>{{Session::get('success')}}</strong>
        </div>
        @endif
        @if(Session::has('error'))
        <div class="alert alert-warning alerterror" role="alert" style="margin-left: -20px">
            <strong>{{Session::get('error')}}</strong>
        </div>
        @endif
    </div>
    <div class="col-md-2"></div>
</div>

<div class="row">
  <div class="col-md-2"></div>
  <div class="col-md-8">
    <form class="form-horizontal" id="validate_form" target="_blank" action="{{ route('iqamaexpense-report-date-wise-process') }}" method="post">
      @csrf
      <div class="card">
        <div class="card-header"></div><br>
        <div class="card-body card_form" style="padding-top: 0;">

           <div class="form-group row custom_form_group">
                <label class="control-label col-md-3"> Approval Status</label>
                <div class="col-md-6">
                  <select class="form-control" name="approval_status">
                    <option value="">All </option>
                    <option value="2">Pending</option>
                    <option value="3">Approved</option>
              
                  </select>
                </div>
            </div> 

            <div class="form-group row custom_form_group">
                <label class="control-label col-md-3"> Iqama Renewal Expense By</label>
                <div class="col-md-6">
                  <select class="form-control" name="expense_by">
                  <option value="">All </option>
                    <option value="1">Self </option>
                    <option value="2">Company</option>               
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
                  <input type="date" class="form-control" name="end_date" value="<?= date("Y-m-d") ?>"
                  max="{{ Carbon\Carbon::now()->format('Y-m-d') }}">
                </div>
            </div>

          {{-- <div class="form-group row custom_form_group">
            <label class="control-label col-md-3"> Report Format:</label>
            <div class="col-md-6">
              <select class="form-control" name="report_format">
                <option value="1">Print</option>
                <option value="2">Excell</option>
                <option value="3">CSV</option>
               </select>
            </div>
          </div> --}}


        </div>
        <div class="card-footer card_footer_button text-center">
          <button type="submit"  class="btn btn-primary waves-effect">Show Report</button>
        </div>
      </div>
    </form>
  </div>
  <div class="col-md-2"></div>
</div>

@endsection

 
