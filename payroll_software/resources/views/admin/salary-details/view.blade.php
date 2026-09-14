@extends('layouts.admin-master')
@section('title') View Salary Details @endsection
@section('content')
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">View Salary Details</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li><a href="{{ route('salary-details') }}">Salary Details</a></li>
            <li class="active">View Salary Details</li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-8">
                        <h3 class="card-title card_top_title"><i class="fab fa-gg-circle"></i> View Salary Details</h3>
                    </div>
                    <div class="col-md-4 text-right">
                        <a href="{{ route('salary-details') }}" class="btn btn-md btn-primary waves-effect card_top_button"><i class="fa fa-th mr-2"></i>Salary Details List</a>
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
                                <td>Basic Amount</td>
                                <td>:</td>
                                <td>{{$view->basic_amount}} SR</td>
                            </tr>
                            <tr>
                                <td>House Rent</td>
                                <td>:</td>
                                <td>{{$view->house_rent}} SR</td>
                            </tr>
                            <tr>
                                <td>Hourly Rent</td>
                                <td>:</td>
                                <td>{{$view->hourly_rent}} SR</td>
                            </tr>
                            <tr>
                                <td>Moblie Allowance</td>
                                <td>:</td>
                                <td>{{$view->mobile_allowance}} SR</td>
                            </tr>
                            <tr>
                                <td>Medical Allowance</td>
                                <td>:</td>
                                <td>{{$view->medical_allowance}} SR</td>
                            </tr>
                            <tr>
                                <td>Local Travel Allowance</td>
                                <td>:</td>
                                <td>{{$view->local_travel_allowance}} SR</td>
                            </tr>
                            <tr>
                                <td>Conveyance Allowance</td>
                                <td>:</td>
                                <td>{{$view->conveyance_allowance}} SR</td>
                            </tr>
                            <tr>
                                <td>Increment No</td>
                                <td>:</td>
                                <td>{{$view->increment_no}}</td>
                            </tr>
                            <tr>
                                <td>Increment Amount</td>
                                <td>:</td>
                                <td>{{$view->increment_amount}} SR</td>
                            </tr>
                            <tr>
                                <td>Others 1 </td>
                                <td>:</td>
                                <td>@if($view->others1 == null) No @else {{$view->others1}} SR @endif</td>
                            </tr>
                            <tr>
                                <td>Others 2 </td>
                                <td>:</td>
                                <td>@if($view->others2 == null) No @else {{$view->others2}} SR @endif</td>
                            </tr>
                            <tr>
                                <td>Others 3 </td>
                                <td>:</td>
                                <td>@if($view->others3 == null) No @else {{$view->others3}} SR @endif</td>
                            </tr>
                            <tr>
                                <td>Others 4 </td>
                                <td>:</td>
                                <td>@if($view->others4 == null) No @else {{$view->others4}} SR @endif</td>
                            </tr>
                            <tr>
                                <td>Created Time </td>
                                <td>:</td>
                                <td>{{ Carbon\Carbon::parse($view->created_at)->format('D, d F Y') }}</td>
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
