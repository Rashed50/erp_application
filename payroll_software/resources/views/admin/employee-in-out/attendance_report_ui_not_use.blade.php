@extends('layouts.admin-master')
@section('title') Employee Entry & Out Report @endsection
@section('content')

<div class="row bread_part">
  <div class="col-sm-12 bread_col">
    <h4 class="pull-left page-title bread_title">Employee Attendence Report</h4>
    <ol class="breadcrumb pull-right">
      <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
      <li class="active">Employee Attendence Report</li>
    </ol>
  </div>
</div>

<div class="row">
  <div class="col-md-2"></div>
  <div class="col-md-8">
    @if(Session::has('data_not_found'))
    <div class="alert alert-warning alerterror" role="alert">
      <strong>Opps!</strong> invalid Employee Id.
    </div>
    @endif
  </div>
  <div class="col-md-2"></div>
</div>


<div class="row">
  <div class="col-md-1"></div>
  <div class="col-md-10">
    <form class="form-horizontal" id="employeeInOutReport" target="_blank" action="{{ route('employee-entry-out-report-process') }}" method="post">
      @csrf
      <div class="card">
        <div class="card-body card_form">

          <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
              <div class="form-group custom_form_group">
                <label class="control-label">Select Project:<span class="req_star">*</span></label>
                <div class="">
                  <select class="form-control" name="project_id" required>
                    <option value="">Select Project</option>
                    @foreach($project as $item)
                    <option value="{{ $item->proj_id }}">{{ $item->proj_name }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="form-group custom_form_group">
                <label class="control-label">Select Sponser:<span class="req_star">*</span></label>
                <div class="">
                  <select class="form-control" name="sponserId" required>
                    <option value="all">All Sponser</option>
                    @foreach($sponser as $item)
                    <option value="{{ $item->spons_id }}">{{ $item->spons_name }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="form-group custom_form_group">
                <label class="control-label">Select Year:<span class="req_star">*</span></label>
                <div class="">
                  <select class="form-control" name="year_id" required>
                    <!-- <option value="">Select Year</option> -->
                    @foreach(range(date('Y'), date('Y')-5) as $y)
                    <option value="{{$y}}" {{$y}}>{{$y}}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="form-group custom_form_group">
                <label class="control-label">Select Month:<span class="req_star">*</span></label>
                <div class="">
                  <select class="form-control" name="month_id">
                    <!-- <option value="">Select Month</option> -->
                    @foreach($month as $item)
                    <option value="{{ $item->month_id }}" {{ $item->month_id == Carbon\Carbon::now()->format('m') ? 'selected' :'' }}>{{ $item->month_name }}</option>

                    @endforeach
                  </select>
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
  <div class="col-md-1"></div>
</div>


<!-- script area -->
<script type="text/javascript">
  /* form validation */
  $(document).ready(function() {
    $("#employeeInOutReport").validate({
      rules: {
        month_id: {
          required: true,
        },
        year_id: {
          required: true,
        },
      },

      messages: {
        month_id: {
          required: "You Must Be Select This Field!",
        },
        year_id: {
          required: "Please Select year!",
        },
      },


    });
  });
</script>

@endsection