@extends('layouts.admin-master')
@section('title') Driver Vehicle @endsection
@section('content')
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Driver Vehicle Information Edit</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active"> Driver Vehicle Information Edit</li>
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
    <div class="col-md-12">
        <form class="form-horizontal" id="driverForm-validation" action="{{ route('update-driver-vehicle-info') }}"
            method="post" enctype="multipart/form-data">
            @csrf
            <div class="card">
                <div class="card-header">
                    <div class="row">

                    </div>
                </div>
                <div class="card-body card_form" style="padding-top: 0;">
                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-8">

                            <input type="hidden" name="driv_veh_auto_id" value="{{ $edit->driv_veh_auto_id }}">
                            <div class="form-group row custom_form_group">
                                <label class="col-sm-4 control-label">Driver Name:<span class="req_star">*</span></label>
                                <div  class="col-sm-5">
                                    <select class="form-control" name="driv_id" required>
                                        <option value="">Select Driver Name</option>
                                        @foreach ($drivers as $driver)
                                        <option value="{{ $driver->dri_auto_id }}" {{ $driver->dri_auto_id == $edit->driv_auto_id ?
                                            'selected':'' }}>{{ $driver->dri_name }}</option>

                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row custom_form_group">
                                <label class="col-sm-4 control-label">Vehicle Name:<span
                                        class="req_star">*</span></label>
                                <div  class="col-sm-5">
                                    <select class="form-control" name="veh_id" required>
                                        <option value="">Select Vehicle Name</option>
                                        @foreach ($vehicles as $vehicle)
                                        <option value="{{ $vehicle->veh_id }}" {{ $vehicle->veh_id == $edit->veh_auto_id ?
                                            'selected':'' }}>{{ $vehicle->veh_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row custom_form_group">
                                <label class="col-sm-4 control-label">Assigned Date:</label>
                                <div class="col-sm-5">
                                    <input type="date" name="assigned_date" class="form-control"
                                        min="{{ date('Y-m-d') }}" value="{{ $edit->assign_date }}" required>
                                </div>
                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn btn-primary waves-effect">UPDATE</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2"></div>
                </div>
            </div>
    </div>
    </form>
</div>
</div>



@endsection
