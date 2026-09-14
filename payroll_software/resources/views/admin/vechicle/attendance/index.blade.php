
@extends('layouts.admin-master')
@section('title')Vehicle Attn. @endsection
@section('content')
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Vechicle Attendance</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active"> Vechicle</li>
        </ol>
    </div>
</div>
<!-- add division -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        @if(Session::has('success'))
        <div class="alert alert-success alertsuccess" role="alert">
            <strong> {{ Session::get('success')}} </strong>
        </div>
        @endif
        @if(Session::has('error'))
        <div class="alert alert-warning alerterror" role="alert">
            <strong> {{ Session::get('error') }} </strong>
        </div>
        @endif
    </div>
    <div class="col-md-2"></div>
</div>
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
            <button class="btn btn-primary float-right" data-toggle="modal" data-target="#addModal"><i class="fa fa-plus"></i> Add New </button> &nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <button class="btn btn-primary float-right" data-toggle="modal" data-target="#reportModal"><i class="fa fa-plus"></i> Report </button>
    </div>
 <div class="col-md-2"></div>


    <h4>Latest Records</h4>
    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>S.N</th>
                <th>Date</th>
                <th>Vehicle</th>
                <th>Dr. Name</th>
                <th>Dr. Iqama</th>
                <th>Phone</th>
                <th>Project</th>
                <th>Remarks</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($records as $r)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $r->atten_date }}</td>
                <td>{{ $r->veh_name }}-{{ $r->veh_plate_number }}</td>
                <td>{{ $r->driver_name }}</td>
                <td>{{ $r->driver_iqama }}</td>
                <td>{{ $r->driver_phone_no }}</td>
                <td>{{ $r->proj_name }}</td>
                <td>{{ $r->remarks }}</td>
                <td>
                    <button class="btn btn-sm btn-info" onclick='openEditModal(@json($r))'><i class="fa fa-pencil-square fa-sm edit_icon"></i></button>

                    {{-- {{ route('attendance.edit', $r->veh_atten_auto_id) }} --}}
                    ||
                    <button class="btn btn-sm btn-info" onclick='deleteARecord(@json($r->veh_atten_auto_id))'><i class="fa fa-trash fa-sm delete_icon"></i></button>

                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
</div>



{{-- record add form using modal  --}}
<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <form action="{{ route('vehicle.attendance.store') }}" method="POST" class="mb-4">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">New Attendance</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body row">

                    <div class="form-group row custom_form_group">
                        <label class="col-md-2 control-label">Vehicle:<span class="req_star">*</span></label>
                        <div  class="col-md-4">
                            <select class="form-select" name="veh_auto_id" required>
                                <option value="">Select Vehicle</option>
                                @foreach ($vehicles as $vehicle)
                                <option value="{{ $vehicle->veh_id }}">{{ $vehicle->veh_plate_number . " - " . $vehicle->veh_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <label class="col-md-2 control-label"> Project:<span class="req_star">*</span></label>
                        <div class="col-md-4">
                            <select class="form-select" id="working_project_id" name="working_project_id" required>
                                <option value="">Select Working Project</option>
                                @foreach($projects as $ap)
                                <option value="{{ $ap->proj_id }}">{{ $ap->proj_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div  class="form-group row custom_form_group">

                        <label class="col-md-2 control-label"> Driver Name:</label>
                        <input type="text" name="driver_name" class="col-md-4 form-control">

                        <label class="col-md-2 control-label"> Iqama No:<span class="req_star">*</span></label>
                        <input type="text" name="driver_iqama" class="col-md-4 form-control" required>
                    </div>

                    <div  class="form-group row custom_form_group">

                        <label class="col-md-2 control-label"> Phone No.:</label>
                        <input type="text" name="driver_phone_no" class="col-md-4 form-control" >

                        <label class="col-md-2 control-label"> Date:<span class="req_star">*</span></label>
                        <input type="date" name="atten_date" class="col-md-4 form-control" value="{{ date('Y-m-d')}}" required>
                    </div>

                    <div  class="form-group row custom_form_group">

                        <label class="col-md-2 control-label"> Remarks:</label>
                        <textarea  rows="2" name="edit_remarks" id="edit_remarks"  class="col-md-10 form-control"></textarea>


                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-primary waves-effect">SAVE</button>
                    </div>

                </div>
            </div>
        </form>

    </div>
</div>


{{-- record update form using modal  --}}
 <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <form method="POST" action="{{ route('vehicle.attendance.update') }}">
        @csrf
        <input type="hidden" name="veh_atten_auto_id" id="edit_id">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Attendance</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body row">
                <input type="hidden" name="veh_atten_auto_id" id="veh_atten_auto_id">
                <div class="form-group row custom_form_group">
                    <label class="col-md-2 control-label">Vehicle:<span class="req_star">*</span></label>
                    <div  class="col-md-4">
                        <select class="form-select" id="edit_vehicle" name="edit_vehicle" required>
                            <option value="">Select Vehicle</option>
                            @foreach ($vehicles as $vehicle)
                            <option value="{{ $vehicle->veh_id }}">{{ $vehicle->veh_plate_number . " - " . $vehicle->veh_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <label class="col-md-2 control-label"> Project:<span class="req_star">*</span></label>
                    <div class="col-md-4">
                        <select class="form-select" id="edit_project" name="edit_project" required>
                            <option value="">Select Working Project</option>
                            @foreach($projects as $ap)
                            <option value="{{ $ap->proj_id }}">{{ $ap->proj_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div  class="form-group row custom_form_group">

                    <label class="col-md-2 control-label"> Driver Name:</label>
                    <input type="text" name="edit_driver" id="edit_driver" class="col-md-4 form-control" >

                    <label class="col-md-2 control-label"> Iqama No:<span class="req_star">*</span></label>
                    <input type="text" name="edit_iqama" id="edit_iqama" class="col-md-4 form-control" required>
                </div>

                <div  class="form-group row custom_form_group">

                    <label class="col-md-2 control-label"> Phone No.:</label>
                    <input type="text" name="edit_phone" id="edit_phone" class="col-md-4 form-control" >
                    <label class="col-md-2 control-label"> Date:<span class="req_star">*</span></label>
                    <input type="date" name="edit_date" id="edit_date" class="col-md-4 form-control" required>

                </div>
                                    <div  class="form-group row custom_form_group">

                        <label class="col-md-2 control-label"> Remarks:</label>

                        <textarea  rows="2" name="edit_remarks" id="edit_remarks"  class="col-md-10 form-control"></textarea>

                    </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Update</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </form>
  </div>
</div>



<script>
    function openEditModal(data) {
        debugger;
        document.getElementById('veh_atten_auto_id').value = data.veh_atten_auto_id;
        document.getElementById('edit_date').value = data.atten_date;
        document.getElementById('edit_project').value = data.working_project_id;
        document.getElementById('edit_vehicle').value = data.veh_auto_id;
        document.getElementById('edit_driver').value = data.driver_name;
        document.getElementById('edit_iqama').value = data.driver_iqama;
        document.getElementById('edit_phone').value = data.driver_phone_no;
        document.getElementById('edit_remarks').value = data.remarks;
        $('#editModal').modal('show');
    }


        function deleteARecord(auto_id){
        //  alert(IqamaRenewRecordAutoId);
            swal({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover this imaginary file!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })

                .then((willDelete) => {
                    if (willDelete) {

                        $.ajax({
                            type: 'GET',
                            url: "{{  url('admin/others/vehicle/attendance/delete') }}/" +auto_id,

                            dataType: 'json',
                            success: function (response) {
                                if(response.status == 200){
                                    showAlertMessage('success', response.message);
                                    window.location.reload();
                                }else {
                                    showAlertMessage('error', response.message);
                                }
                            },
                            error:function(response){
                                showAlertMessage('error', "Operation Failed, Please Try Again");
                            }
                        });


                    }
                });
    }

    // show message
    function showSweetAlertMessage(type,message){
        const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            })
                Toast.fire({
                    type: type,
                    title: message,
                })
    }
</script>

@endsection
