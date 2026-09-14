@extends('layouts.admin-master')
@section('title') Driver Vehicle @endsection
@section('internal-css')
<style media="screen">
    .approve_button {
        background: #2B4049;
        color: #fff;
        font-size: 12px;
        padding: 3px 6px;
        border-radius: 5px;
    }

    .approve_button:hover {
        color: #fff;
    }
</style>
@endsection
@section('content')
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Driver Vehicle Informations</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active"> Driver Vehicle Information Add</li>
        </ol>
    </div>
</div>
<!-- flass Message -->

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

<!-- Employee information searching start -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body card_form" style="margin: 0px;">
                <div class="form-group row custom_form_group{{ $errors->has('emp_information') ? ' has-error' : '' }}">
                    <label class="col-md-4 control-label" for="emp_information">Employee ID: </label>
                    <div class="col-md-6">
                        <input type="text" placeholder="Please Enter Employee ID" class="form-control" id="emp_information" name="emp_information" value="{{ old('emp_id') }}" required autofocus>
                        <span id="driver_not_found_error_show" class="d-none" style="color: red"></span>
                        @if ($errors->has('emp_information'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('emp_information') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-2">
                        <button type="button" onclick="driverInformationSearch()" class="btn btn-primary waves-effect">SEARCH</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Employee information searching end -->

<!-- Employee Searching Data Deatils Show Start -->
<div class="row d-none" id="driver_searching_result_section" style="margin-bottom:20px;">
    <div class="col-md-12">
        <table  class="table table-bordered table-striped table-hover custom_view_table show_employee_details_table">

            <tr>
                <td>
                    <span class="emp">Employee Id:</span>
                    <span id="show_driver_employee_id" class="emp2"></span>
                </td>
                <td>
                    <span class="emp">Vehicle Name:</span>
                    <span id="show_driver_vehicle_name" class="emp2"></span>
                </td>
            </tr>


            <tr>
                <td>
                    <span class="emp">Driver Name:</span>
                    <span id="show_driver_name" class="emp2"></span>
                </td>
                <td>
                    <span class="emp">Model:</span>
                    <span id="show_driver_vehicle_model" class="emp2"></span>
                </td>
            </tr>


            <tr>
                <td>
                    <span class="emp">Iqama No:</span>
                    <span id="show_driver_iqama_no" class="emp2"  style="font-weight:bold;color:red;font-size:18px;"></span>
                </td>
                <td>
                    <span class="emp">Plate:</span>
                    <span id="show_driver_vehicle_plate_no" class="emp2"></span>
                </td>
            </tr>


            <tr>
                <td>
                    <span class="emp">Address:</span>
                    <span id="show_driver_address" class="emp2"></span>
                </td>
                <td>
                    <span class="emp">License No:</span>
                    <span id="show_driver_vehicle_license" class="emp2"></span>

                </td>
            </tr>

            <tr>
                <td>
                    <span class="emp">Assign Date:</span>
                    <span id="show_driver_vehicle_assign_date" class="emp2"></span>
                </td>
                <td>
                    <span class="emp">Project Name:</span>
                    <span id="show_driver_vehicle_project_name" class="emp2"></span>
                </td>
            </tr>
        </table>
    </div>
</div>
<!-- Employee Searching Data Deatils Show End -->


<div class="row">
    <div class="col-md-12">
        <form class="form-horizontal" id="driverForm-validation" action="{{ route('insert-driver-vehicle-info') }}"
            method="post" enctype="multipart/form-data">
            @csrf
            <div class="card">
                <div class="card-header">
                    <div class="row">

                    </div>
                </div>
                <div class="card-body card_form" style="padding-top: 0;">
                    <div class="row">
                        <div class="col-md-2">
                            <button type="button" class="btn btn-primary" data-toggle="modal" id="new_record_button" data-target="#vehicle_photo_upload_modal">Upload Photos</button>
                        </div>
                        <div class="col-md-8">
                            <input type="hidden" name="driv_id">

                            <div class="form-group row custom_form_group">
                                <label class="col-sm-4 control-label">Vehicle:<span class="req_star">*</span></label>
                                <div  class="col-sm-5">
                                    <select class="form-select" name="veh_id" required>
                                        <option value="">Select Vehicle Name</option>
                                        @foreach ($vehicles as $vehicle)
                                        <option value="{{ $vehicle->veh_id }}">{{ $vehicle->veh_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div  class="form-group row custom_form_group">
                                <label class="col-sm-4 control-label"> Working Project:<span class="req_star">*</span></label>
                                <div class="col-sm-5">
                                    <select class="form-select" id="project_id" name="project_id" required>
                                        <option value="">Select Working Project</option>
                                        @foreach($projects as $ap)
                                        <option value="{{ $ap->proj_id }}">{{ $ap->proj_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>


                            <div class="form-group row custom_form_group">
                                <label class="col-sm-4 control-label">Date:</label>
                                <div class="col-sm-5">
                                    <input type="date" name="assigned_date" class="form-control"
                                         value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn btn-primary waves-effect">SAVE</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                    </div>
                </div>
            </div>
        <!--</div>-->
        </form>
    </div>
</div>




<!--  Vehicle Photos/Video Upload Modal -->
<div class="modal fade" id="vehicle_photo_upload_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form id="upload_photo_video"  method="post" action="{{route('upload-driver-vehicle-photos.video')}}"   onsubmit="updatebtn.disabled = true;" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Vehicle Photo/Video Upload<span class="text-danger" id="errorData"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="form-group row custom_form_group{{ $errors->has('employee_id') ? ' has-error' : '' }}">
                        <label class="control-label col-md-3">Driver Name <span
                                class="req_star">*</span></label>
                        <div class="col-md-9">
                            <select class="form-control" name="modal_driv_id" id="modal_driv_id" required>
                                <option value="">Select Driver Name</option>
                                @foreach ($drivers as $driver)
                                <option value="{{ $driver->dri_auto_id }}">{{ $driver->dri_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row custom_form_group">
                        <label class="col-sm-3 control-label">Vehicle :<span
                                class="req_star">*</span></label>
                        <div  class="col-sm-9">
                            <select class="form-control"  id="modal_veh_id" name="modal_veh_id" required>
                                <option value="">Select Vehicle Name</option>
                                @foreach ($vehicles as $vehicle)
                                <option value="{{ $vehicle->veh_id }}">{{ $vehicle->veh_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row custom_form_group">
                        <label class="col-sm-3 control-label">Video Url:</label>
                        <div class="col-sm-9">
                            <input type="text" id="video_url" name="video_url" class="form-control"   >
                        </div>
                    </div>

                    <div class="form-group row custom_form_group">
                        <label class="col-sm-4 control-label">remarks:</label>
                        <div class="col-sm-5">
                            <input type="text" name="remarks" class="form-control" >
                        </div>
                    </div>

                    <div class="form-group row custom_form_group">
                        <label class="col-sm-3 control-label">Upload:</label>
                        <div class="col-sm-4">
                            <div class="input-group">
                                <span class="input-group-btn">
                                    <span class="btn btn-default btn-file btnu_browse">
                                        Browse… <input type="file" name="photo_upload" id="imgInp4">
                                    </span>
                                </span>
                                <input type="text" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="col-sm-6=5">
                            <img id='img-upload4' class="upload_image" />
                        </div>
                    </div>
                </div>
                <div class="card-footer card_footer_button text-center">
                    <button type="submit" id="new_record_save_button" class="btn btn-primary waves-effect"
                         >SAVE</button>
                </div>
            </div>
        </form>
    </div>
</div>


 <!-- vehicle list -->
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <div class="row">

                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table id="dt-vertical-scroll" class="table" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>S.N</th>
                                        <th>Emp. ID</th>
                                        <th>Name</th>
                                        <th>Vehicle</th>
                                        <th>Project Name</th>
                                        <th>Status</th>
                                        <th>Assigned</th>
                                        <th>Released</th>
                                        <th>Manage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($all as $item)
                                    <tr>
                                        <td> {{ $loop->iteration }} </td>
                                        <td> {{ $item->dri_emp_id }} </td>
                                        <td> {{ $item->dri_name }} </td>
                                        <td> {{ $item->veh_plate_number . " - " . $item->veh_brand_name }} </td>
                                        <td> {{ $item->proj_name }} </td>
                                        <td>
                                            @if ($item->status == 1)
                                                <span class="badge badge-pill badge-success">Engaged</span>
                                            @else
                                                <span class="badge badge-pill badge-danger">Released </span>
                                            @endif
                                        </td>
                                        <td> {{ Carbon\Carbon::parse($item->assign_date)->format('D, d F Y') }} </td>
                                        <td>
                                            @if ($item->release_date == null)
                                                <span class="badge badge-pill badge-success"> - - - </span>
                                            @else
                                                {{ Carbon\Carbon::parse($item->release_date)->format('D, d F Y') }}
                                            @endif
                                        </td>
                                        <td>

                                            <a href="{{ route('driver-vehicle-info-edit',$item->driv_veh_auto_id) }}" title="edit"  ><i   class="fa fa-pencil-square fa-lg edit_icon"></i></a>
                                            <a href="{{ route('driver-vehicle-info-deactive',$item->driv_veh_auto_id) }}" title="Released" id="released" ><i class="fa fa-trash fa-lg delete_icon"></i></a>

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
</div>

<script type="text/javascript">


</script>

@endsection
