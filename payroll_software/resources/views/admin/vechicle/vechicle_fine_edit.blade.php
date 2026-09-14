@extends('layouts.admin-master')
@section('title') Vechicle Fine @endsection
@section('content')
<div class="row bread_part">
  <div class="col-sm-12 bread_col">
    <h4 class="pull-left page-title bread_title">Update Vechicle Fine Information</h4>
    <ol class="breadcrumb pull-right">
      <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
      <li class="active">Vechicle Fine</li>
    </ol>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="row">
            <div class="col-md-8">
              
            </div>
            <div class="clearfix"></div>
          </div>
        </div>

        <div class="row" id="showVehicleFineDetails" class="d-noe">
            <div class="col-md-12">
                <div class="card card-outline-info">
                    <div class="card-body">

                        @if(Session::has('success'))
                          <div class="alert alert-success alertsuccess" role="alert" style="margin-left: -20px">
                            <strong>Successfully!</strong> Update Vehicle Fine.
                          </div>
                        @endif
                        @if(Session::has('error'))
                          <div class="alert alert-warning alerterror" role="alert" style="margin-left: -20px">
                            <strong>Opps!</strong> please try again.
                          </div>
                        @endif

                        <form action="{{ route('vehicle_fine_update_form') }}" method="POST">
                            @csrf 

                            <input type="hidden" name="id" value="{{ $fineRecord->vfr_auto_id }}">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group custom_form_group">
                                        <label class="control-label d-block" style="text-align: left;">Employee ID:<span class="req_star">*</span></label>
                                        <div>
                                            <input type="hidden" class="form-control" id="emp_auto_id" name="emp_auto_id" value="{{ $fineRecord->employee_id }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group custom_form_group">
                                        <label class="control-label d-block" style="text-align: left;">Vehicle ID:<span class="req_star">*</span></label>
                                        <div>
                                        <input type="hidden" class="form-control" id="veh_id" name="veh_id" value="{{ $fineRecord->veh_id }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group custom_form_group">
                                        <label class="control-label d-block" style="text-align: left;">Penalty Amount:<span class="req_star">*</span></label>
                                        <div>
                                        <input type="text" class="form-control" name="amount" value="{{ $fineRecord->amount }}" placeholder="Enter Penalty Amount">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group custom_form_group">
                                        <label class="control-label d-block" style="text-align: left;">Date:<span class="req_star">*</span></label>
                                        <div>
                                        <input type="text" class="form-control" name="date" value="{{ $fineRecord->date }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group custom_form_group">
                                        <label class="control-label d-block" style="text-align: left;">Remarks:<span class="req_star">*</span></label>
                                        <div>
                                        <input type="text" class="form-control" name="remarks" value="{{ $fineRecord->remarks }}" placeholder="Enter Remarks">
                                        </div>
                                    </div>
                                </div>
                                <div class="text-center">
                                  <button type="submit" class="btn btn-primary waves-effect">Update</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
      </div>
  </div>
</div>



  <script type="text/javascript">
        $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    /* ================= search Employee Details ================= */

  </script>

@endsection