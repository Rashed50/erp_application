@extends('layouts.admin-master')
@section('title') Vechicle Fine @endsection
@section('content')
<div class="row bread_part">
  <div class="col-sm-12 bread_col">
    <h4 class="pull-left page-title bread_title">Search Vechicle Fine Information</h4>
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
      <div class="card-body card_form" style="padding-top:0;">
        <div class="row">
          <div class="col-md-2"></div>
          <div class="col-md-6">
            <div class="form-group custom_form_group">
              <div>
                <input type="text" class="form-control vechicle-fine-input" name="veh_plate_number" id="veh_plate_number" placeholder="Search Vechicle Plate Number">
              </div>
            </div>
          </div>
          <div class="col-md-2">
            <div class="">
              <button type="submit" class="btn btn-primary waves-effect" onClick="searchVehicleDetails()">Search</button>
            </div>
          </div>
          <div class="col-md-2"></div>
        </div>
      </div>

      <div class="row" id="showVehicleFineDetails" class="d-noe">
        <div class="col-md-2"></div>
        <div class="col-md-8">
          <div class="card card-outline-info">
            <div class="card-body">

              @if(Session::has('success'))
              <div class="alert alert-success alertsuccess" role="alert" style="margin-left: -20px">
                <strong>Successfully!</strong> Added New Vehicle Fine.
              </div>
              @endif
              @if(Session::has('error'))
              <div class="alert alert-warning alerterror" role="alert" style="margin-left: -20px">
                <strong>Opps!</strong> please try again.
              </div>
              @endif

              @if(Session::has('delete_success'))
              <div class="alert alert-success alertsuccess" role="alert" style="margin-left: -20px">
                <strong>Successfully!</strong> Delete Vehicle Fine.
              </div>
              @endif
              @if(Session::has('delete_error'))
              <div class="alert alert-warning alerterror" role="alert" style="margin-left: -20px">
                <strong>Opps!</strong> please try again.
              </div>
              @endif

              <form action="{{ route('vehicle_fine_insert_form') }}" method="POST">
                @csrf
                <table class="table table-bordered table-striped table-hover custom_table">
                  <tr>
                    <td>Driver Employee ID:</td>
                    <td>
                      <span id="show_vechicle_employee_id" class="vehicle"></span>
                      <span id="show_vechicle_employee_auto_id" class="vehicle"></span>

                    </td>
                    <td>Driver Name:</td>
                    <td><span id="show_vechicle_employee_name" class="vehicle"></span></td>
                  </tr>
                  <tr>
                    <td>Akama Number:</td>
                    <td><span id="show_vechicle_akama_no" class="vehicle"></span></td>
                    <td>Mobile Number:</td>
                    <td><span id="show_vechicle_mobile_no" class="vehicle"></span></td>
                  </tr>
                  <tr>
                    <td>Model Number:</td>
                    <td><span id="show_vechicle_model_number" class="vehicle"></span> </td>
                    <td>Plate Number:</td>
                    <td><span id="show_vechicle_plate_number" class="vehicle"></span></td>
                  </tr>
                </table>

                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group custom_form_group">
                      <div>
                        <input type="hidden" class="form-control" id="emp_auto_id" name="emp_auto_id">
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group custom_form_group">
                      <div>
                        <input type="hidden" class="form-control" id="veh_id" name="veh_id" value="{{ old('veh_id') }}">
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group custom_form_group">
                      <label class="control-label d-block" style="text-align: left;">Penalty Amount:<span class="req_star">*</span></label>
                      <div>
                        <input type="text" class="form-control" name="amount" value="{{ old('amount') }}" placeholder="Enter Penalty Amount">
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group custom_form_group">
                      <label class="control-label d-block" style="text-align: left;">Date:<span class="req_star">*</span></label>
                      <div>
                        <input type="text" class="form-control" name="date" value="{{ date('Y-m-d') }}">
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group custom_form_group">
                      <label class="control-label d-block" style="text-align: left;">Remarks:</label>
                      <div>
                        <input type="text" class="form-control" name="remarks" value="{{ old('remarks') }}" placeholder="Enter Remarks">
                      </div>
                    </div>
                  </div>
                  <div class="text-center">
                    <button type="submit" class="btn btn-primary waves-effect">SAVE</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
        <div class="col-md-2"></div>
      </div>

      <div class="row">
        <div class="col-md-12">
          <div class="table-responsive">
            <table id="alltableinfo" class="table table-bordered custom_table">
              <thead>
                <tr>
                  <th>Driver Employee ID</th>
                  <th>Driver Name</th>
                  <th>Vehicle Name</th>
                  <th>Plate Number</th>
                  <th>Date</th>
                  <th>Amount</th>
                  <th>Remarks</th>
                  <th>Manage</th>
                </tr>
              </thead>
              <tbody>
                @foreach($vehicleFineRecord as $aRecord)
                <tr>
                  <td> {{ $aRecord->employee_id }} </td>
                  <td> {{ $aRecord->employee_name }} </td>
                  <td> {{ $aRecord->veh_name }} </td>
                  <td> {{ $aRecord->veh_plate_number }} </td>
                  <td> {{ $aRecord->date }} </td>
                  <td> {{ $aRecord->amount }} </td>
                  <td> {{ $aRecord->remarks }} </td>
                  <td>
                    <!-- <a href="{{ route('vehicle_fine_edit_form',$aRecord->vfr_auto_id) }}" title="edit"><i class="fa fa-pencil-square fa-lg edit_icon"></i></a> -->

                    <a href="{{ route('vehicle_fine_delete_form',$aRecord->vfr_auto_id) }}" title="delete" title="delete data"><i class="fa fa-trash fa-lg delete_icon"></i></a>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
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
  
    function searchVehicleDetails() {
     
    var value = $("input[id='veh_plate_number']").val();
  
      $.ajax({
        type: 'POST',
        url: "{{ url('/admin/vehicle/search') }}",
         data: {
          value: value,
        },
        dataType: 'json',
        success: function(data) { 

          if (data.status_code != 200) {
            alert(data.status_code);
          } else {
            var vehicle = data.data;         
            $("span[id='show_vechicle_employee_id']").text(vehicle.employee_id);
            // $("span[id='show_vechicle_employee_auto_id']").text(vehicle.emp_auto_id);
            $("span[id='show_vechicle_employee_name']").text(vehicle.employee_name);
            $("span[id='show_vechicle_akama_no']").text(vehicle.akama_no);
            $("span[id='show_vechicle_mobile_no']").text(vehicle.mobile_no);
            $("span[id='show_vechicle_model_number']").text(vehicle.veh_model_number);
            $("span[id='show_vechicle_plate_number']").text(vehicle.veh_plate_number);
            $("input[id='veh_id']").val(vehicle.veh_id);
            $("input[id='emp_auto_id']").val(vehicle.emp_auto_id);

          }
        },
        error:function(response){
          alert(response);
        }


      });
    }

 </script>

@endsection