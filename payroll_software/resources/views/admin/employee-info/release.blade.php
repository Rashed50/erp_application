@extends('layouts.admin-master')
@section('title') Release Employee list @endsection
@section('content')

<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Release Employee List</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active">Release Employee List</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-7">
        @if(Session::has('payment'))
        <div class="alert alert-success alertsuccess" role="alert">
            <strong>Successfully!</strong> Payment Employee Salary.
        </div>
        @endif
        @if(Session::has('success_soft'))
        <div class="alert alert-success alertsuccess" role="alert">
            <strong>Successfully!</strong> delete banner information.
        </div>
        @endif
        @if(Session::has('success_publish'))
        <div class="alert alert-success alertsuccess" role="alert">
            <strong>Successfully!</strong> publish banner information.
        </div>
        @endif
        @if(Session::has('success_update_image'))
        <div class="alert alert-success alertsuccess" role="alert">
            <strong>Successfully!</strong> Update New Image.
        </div>
        @endif
        @if(Session::has('error'))
        <div class="alert alert-warning alerterror" role="alert">
            <strong>Opps!</strong> please try again.
        </div>
        @endif
    </div>
    <div class="col-md-2"></div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-8">
                        <h3 class="card-title card_top_title"><i class="fab fa-gg-circle"></i> Release Employee List </h3>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="card-body">

                <!-- <form method="post" action="#">
                    @csrf
                    <div class="row">
                        <div class="col-md-2"></div>

                        <div class="col-md-10">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group row custom_form_group">
                                        <label class="col-sm-3 control-label">From:<span class="req_star">*</span></label>
                                        <div class="col-sm-7">
                                            <input type="text" class="form-control fromDate" id="datepickerFrom" autocomplete="off" name="fromDate" value="{{date('m/Y')}}" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group row custom_form_group">
                                        <label class="col-sm-3 control-label">To:<span class="req_star">*</span></label>
                                        <div class="col-sm-7">
                                            <input type="text" class="form-control toDate" id="datepickerTo" autocomplete="off" name="toDate" value="{{date('m/Y')}}" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" onclick="searchEmpPendingSalaryList()" class="btn btn-success btn-sm">SEARCH</button>
                                </div>
                            </div>
                        </div>

                        <div class="clearfix"></div>
                    </div>
                </form> -->

                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">

                            <table id="alltableinfo" class="table table-bordered custom_table mb-0">
                            <thead>
                                    <tr>
                                        <th>Emp Id</th>
                                        <th>Name</th>
                                        <th>Iqama No</th>
                                        <th>Sponsore</th>
                                        <th>Project</th>
                                        <th>Trade</th>
                                        <th>Country</th>
                                        <th>Status</th>
                                        <!-- <th colspan="2" class="text-center">Manage</th>
                                        <th></th> -->
                                    </tr>
                                </thead>
                                <tbody id="">
                                    @foreach($all as $item)
                                        <tr>
                                            <td>{{ $item->employee_id }}</td>
                                            <td>{{Str::words($item->employee_name,3)}}</td>
                                            <td>{{ $item->akama_no }}</td>
                                            <td>{{ $item->sponsor->spons_name ?? ''}}</td>
                                            <td>{{ $item->project->proj_name ?? ''}}</td>
                                            <td>{{ $item->category->catg_name }}</td>
                                            @if($item->country_id == NULL)
                                                <td>Not Assigned</td>
                                            @else
                                                <td>{{ $item->country->country_name }}</td> <!--country-->
                                            @endif
                                            <td>{{ $item->emp_type_id == NULL ? 'Not Assigned' : $item->employeeType->name }}</td> <!--employeeType-->
                                            <!-- <td> -->
                                                <!-- <a href="{{ url('admin/employee/view/'.$item->emp_auto_id) }}" title="view"><i class="fas fa-eye fa-lg view_icon"></i></a> -->
                                                <!-- <a href="#" title="Release">Release</a> -->
                                                <!-- <a href="{{ url('admin/employee/edit/'.$item->emp_auto_id) }}" title="edit"><i class="fa fa-pencil-square fa-lg edit_icon"></i></a> -->
                                                <!-- <a href="#" title="delete" id="softDelete" data-toggle="modal" data-target="#softDelModal" data-id="#"><i class="fa fa-trash fa-lg delete_icon"></i></a> -->
                                            <!-- </td> -->
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

@endsection