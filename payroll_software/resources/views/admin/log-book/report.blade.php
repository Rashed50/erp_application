@extends('layouts.admin-master')
@section('title') Log Book Report @endsection
@section('content')
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Log Book Report</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active"> Log Book Report</li>
        </ol>
    </div>
</div>
<!-- add division -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        @if(Session::has('success'))
          <div class="alert alert-success alertsuccess" role="alert">
             <strong>Successfully!</strong> Added New Vechicle Information.
          </div>
        @endif
        @if(Session::has('success_update'))
          <div class="alert alert-success alertsuccess" role="alert">
             <strong>Successfully!</strong> Update Vechicle Information.
          </div>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-6">
      <form class="form-horizontal" id="vechicleForm-validation" target="_blank" action="{{ route('log-book.report-generat') }}" method="post">
        @csrf
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-8">
                        <h3 class="card-title card_top_title"><i class="fab fa-gg-circle"></i> Log Book Report</h3>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="card-body card_form" style="padding-top: 0;">

              <div class="form-group custom_form_group">
                  <label class="control-label d-block" style="text-align: left;">Vehicle Name:<span class="req_star">*</span></label>
                  <div>
                      <select class="form-control" name="veh_id">
                          <option value="">Select Vehicle Name</option>
                          @foreach($vehicle as $veh)
                          <option value="{{ $veh->veh_id }}">{{ $veh->veh_name }}</option>
                          @endforeach
                      </select>
                  </div>
              </div>

              <div class="row">
                  <div class="col-md-6">
                    <div class="form-group custom_form_group">
                        <label class="control-label d-block" style="text-align: left;">Start Date:<span class="req_star">*</span></label>
                        <div>
                            <input type="date" class="form-control" name="start_date" value="{{ old('start_date') }}">
                        </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group custom_form_group">
                        <label class="control-label d-block" style="text-align: left;">End Date:<span class="req_star">*</span></label>
                        <div>
                            <input type="date" class="form-control" name="end_date" value="{{ old('end_date') }}">
                        </div>
                    </div>
                  </div>
              </div>


            </div>
            <div class="card-footer card_footer_button text-center">
                <button type="submit" target="_blank" class="btn btn-primary waves-effect">PROCESS</button>
            </div>
        </div>
      </form>
    </div>
    <div class="col-md-3"></div>
</div>

<!-- script area -->
<script type="text/javascript">
/* form validation */
$(document).ready(function(){
  $("#vechicleForm-validation").validate({
    /* form tag off  */
    // submitHandler: function(form) { return false; },
    /* form tag off  */
    rules: {
      veh_id: {
        required : true,
      },
      start_date: {
        required : true,
      },
      end_date: {
        required : true,
      },

    },

    messages: {
      veh_id: {
        required : "You Must Be Select This Field!",
      },
      start_date: {
        required : "You Must Be Select This Field!",
      },
      end_date: {
        required : "You Must Be Select This Field!",
      },
    },
  });
});
</script>
@endsection
