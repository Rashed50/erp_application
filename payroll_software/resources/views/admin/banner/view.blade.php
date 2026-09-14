@extends('layouts.admin-master')
@section('title') View Project @endsection
@section('content')
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">View Project</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li><a href="{{ route('project-info') }}">Project Information</a></li>
            <li class="active">View Project</li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-8">
                        <h3 class="card-title card_top_title"><i class="fab fa-gg-circle"></i> View Project</h3>
                    </div>
                    <div class="col-md-4 text-right">
                        <a href="{{ route('project-info') }}" class="btn btn-md btn-primary waves-effect card_top_button"><i class="fa fa-th mr-2"></i>Project Information</a>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2"></div>
                    <div class="col-md-8">
                        <table class="table table-bordered table-striped table-hover custom_view_table">
                            <tr>
                                <td>Project Name</td>
                                <td>:</td>
                                <td>{{$view->proj_name}}</td>
                            </tr>
                            <tr>
                                <td>Starting Time</td>
                                <td>:</td>
                                <td>{{ Carbon\Carbon::parse($view->starting_date)->format('D, d F Y') }}</td>
                            </tr>
                            <tr>
                                <td>Address</td>
                                <td>:</td>
                                <td>{{$view->address}}</td>
                            </tr>
                            <tr>
                                <td>Project Code</td>
                                <td>:</td>
                                <td>{{$view->proj_code}}</td>
                            </tr>
                            <tr>
                                <td>Project Budget</td>
                                <td>:</td>
                                <td>{{$view->proj_budget}}tk</td>
                            </tr>
                            <tr>
                                <td>Deadline</td>
                                <td>:</td>
                                <td>{{ Carbon\Carbon::parse($view->proj_deadling)->format('D, d F Y') }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-2"></div>
                </div>
            </div>
            <div class="card-footer card_footer_expode">
                <a href="#" class="btn btn-secondary waves-effect">PRINT</a>
                <a href="#" class="btn btn-warning waves-effect">EXCEL</a>
                <a href="#" class="btn btn-success waves-effect">PDF</a>
            </div>
        </div>
    </div>
</div>

@endsection
