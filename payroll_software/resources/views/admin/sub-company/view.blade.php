@extends('layouts.admin-master')
@section('title') View Company Information @endsection
@section('content')
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">View {{ $view->sb_comp_name }} information</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li><a href="{{ route('sub-comp-info') }}">Company Information List</a></li>
            <li class="active">View</li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-8">
                        <h3 class="card-title card_top_title"><i class="fab fa-gg-circle"></i> View {{ $view->sb_comp_name }} Information</h3>
                    </div>
                    <div class="col-md-4 text-right">
                        <a href="{{ route('sub-comp-info') }}" class="btn btn-md btn-primary waves-effect card_top_button"><i class="fa fa-th"></i> All Company List</a>
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
                                <td>Main Company Name</td>
                                <td>:</td>
                                <td>{{ $view->company->comp_name_en }}</td>
                            </tr>
                            <tr>
                                <td>Company Name (English)</td>
                                <td>:</td>
                                <td>{{$view->sb_comp_name}}</td>
                            </tr>
                            <tr>
                                <td>Company Name (Arabic)</td>
                                <td>:</td>
                                <td>{{$view->sb_comp_name_arb}}</td>
                            </tr>
                            <tr>
                                <td>Mobile No</td>
                                <td>:</td>
                                <td>{{$view->sb_comp_mobile1}}</td>
                            </tr>
                            <tr>
                                <td>Vat No</td>
                                <td>:</td>
                                <td>{{$view->sb_vat_no}}</td>
                            </tr>
                            <tr>
                                <td>Email Address</td>
                                <td>:</td>
                                <td>{{$view->sb_comp_email1}}</td>
                            </tr>
                            <tr>
                                <td>Email Address 2</td>
                                <td>:</td>
                                <td>{{$view->sb_comp_email2}}</td>
                            </tr>
                            <tr>
                                <td>Phone No </td>
                                <td>:</td>
                                <td>{{$view->sb_comp_phone1}}</td>
                            </tr>
                            <tr>
                                <td>Phone No 2</td>
                                <td>:</td>
                                <td>{{$view->sb_comp_phone2}}</td>
                            </tr>
                            <tr>
                                <td>Address</td>
                                <td>:</td>
                                <td>{{$view->sb_comp_address}}</td>
                            </tr>
                            <tr>
                                <td>Contact Parson Details</td>
                                <td>:</td>
                                <td>{{$view->sb_comp_contact_parson_details}}</td>
                            </tr>
                            <tr>
                                <td>Entered By</td>
                                <td>:</td>
                                <td>{{$view->user->name}}</td>
                            </tr>
                            <tr>
                                <td>Entered Time</td>
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
