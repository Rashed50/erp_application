@extends('layouts.admin-master')
@section('title') Project Wise Employees @endsection
@section('content')
<div class="row bread_part">
  <div class="col-sm-12 bread_col">
    <h4 class="pull-left page-title bread_title"> Payment From BD Office Report</h4>
    <ol class="breadcrumb pull-right">
      <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
      
    </ol>
  </div>
</div>

<!-- Session Message -->
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


<!-- Employee ID Wise BD Payment Summary -->
<div class="row">
  <div class="col-md-2"></div>
  <div class="col-md-8">
    <form class="form-horizontal" id="validate_form" target="_blank" action="{{ route('employee.payment.from-bd-offic.report.emp.summary') }}" method="post">
      @csrf
      <div class="card">
        <div class="card-header">BD Office Payment Report For an Employee</div><br>
        <div class="card-body card_form" style="padding-top: 0;"> 

            <div class="form-group row custom_form_group{{ $errors->has('end_date') ? ' has-error' : '' }}">
                <label class="col-sm-3 control-label">Employee ID<span class="req_star">*</span></label>
                <div class="col-sm-7">
                  <input type="number" class="form-control" name="employee_id"  min= "1" required>
                </div>
            </div>

        </div>
        <div class="card-footer card_footer_button text-center">
          <button type="submit" class="btn btn-primary waves-effect">Show Report</button>
        </div>
      </div>
    </form>
  </div>
  <div class="col-md-2"></div>
</div>

<!-- Date to Date BD Payment Summary -->
<div class="row">
  <div class="col-md-2"></div>
  <div class="col-md-8">
    <form class="form-horizontal" id="validate_form" target="_blank" action="{{ route('employee.payment.from-bd-office.report') }}" method="post">
      @csrf
      <div class="card">
        <div class="card-header">Payment Approved Employee List</div><br>
        <div class="card-body card_form" style="padding-top: 0;">

            <div class="form-group row custom_form_group{{ $errors->has('start_date') ? ' has-error' : '' }}">
                <label class="col-sm-3 control-label">Start Date:<span class="req_star">*</span></label>
                <div class="col-sm-7">
                  <input type="date" class="form-control" name="start_date" value="<?= date("Y-m-d") ?>" required>
                </div>
            </div>

            <div class="form-group row custom_form_group{{ $errors->has('end_date') ? ' has-error' : '' }}">
                <label class="col-sm-3 control-label">End Date:<span class="req_star">*</span></label>
                <div class="col-sm-7">
                  <input type="date" class="form-control" name="end_date" value="<?= date("Y-m-d") ?>"
                  max="{{ Carbon\Carbon::now()->format('Y-m-d') }}" required>
                </div>
            </div>

            <div class="form-group row custom_form_group">
            <label class="control-label col-md-3"> Payment Status</label>
            <div class="col-md-6">
              <select class="form-control" name="payment_status" required>
                <option value="0">All</option>
                <!-- <option value="1">Approval Pending</option> -->
                <option value="5">Approved</option>
                <option value="15">Payment Completed</option>
                <!-- <option value="10">Approval Rejected</option> -->
               </select>
            </div>
          </div>


        </div>
        <div class="card-footer card_footer_button text-center">
          <button type="submit" class="btn btn-primary waves-effect">Show Report</button>
        </div>
      </div>
    </form>
  </div>
  <div class="col-md-2"></div>
</div>

<!-- BD Office Payment Details -->
<div class="row">
  <div class="col-md-2"></div>
  <div class="col-md-8">
    <form class="form-horizontal" id="validate_form" target="_blank" action="{{ route('employee.payment.from-bd-offic.details.date.to.date') }}" method="post">
      @csrf
      <div class="card">
        <div class="card-header">Employee Payment Details</div><br>
        <div class="card-body card_form" style="padding-top: 0;">

            <div class="form-group row custom_form_group{{ $errors->has('start_date') ? ' has-error' : '' }}">
                <label class="col-sm-3 control-label">Start Date:<span class="req_star">*</span></label>
                <div class="col-sm-7">
                  <input type="date" class="form-control" name="start_date" value="<?= date("Y-m-d") ?>" required>
                </div>
            </div>

            <div class="form-group row custom_form_group{{ $errors->has('end_date') ? ' has-error' : '' }}">
                <label class="col-sm-3 control-label">End Date:<span class="req_star">*</span></label>
                <div class="col-sm-7">
                  <input type="date" class="form-control" name="end_date" value="<?= date("Y-m-d") ?>"
                  max="{{ Carbon\Carbon::now()->format('Y-m-d') }}" required>
                </div>
            </div> 

        </div>
        <div class="card-footer card_footer_button text-center">
          <button type="submit" class="btn btn-primary waves-effect">Show Report</button>
        </div>
      </div>
    </form>
  </div>
  <div class="col-md-2"></div>
</div>

@endsection


@section('script')
<!-- form validation -->
<script type="text/javascript">
    $(document).ready(function () {
        
    });
</script>
@endsection
