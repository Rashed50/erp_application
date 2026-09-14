@extends('layouts.admin-master')
@section('title') Employee view @endsection
@section('content')
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">{{ $view->employee_name }} view information</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
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
                        <h3 class="card-title card_top_title"><i class="fab fa-gg-circle"></i> View {{ $view->employee_name }} Information</h3>
                    </div>
                    <div class="col-md-4 text-right">
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
                                <td>Employee Id No</td>
                                <td>:</td>
                                <td>{{$view->employee_id}}</td>
                            </tr>
                            <tr>
                                <td>Name</td>
                                <td>:</td>
                                <td>{{$view->employee_name}}</td>
                            </tr>
                            <tr>
                                <td>Akama No</td>
                                <td>:</td>
                                <td>{{$view->akama_no}}</td>
                            </tr>
                            <tr>
                                <td>Akama Expire Date</td>
                                <td>:</td>
                                <td>{{ Carbon\Carbon::parse($view->akama_expire)->format('D, d F Y') }}</td>
                            </tr>

                            <tr>
                                <td>Present Address</td>
                                <td>:</td>
                                <td>{{$view->present_address}}</td>
                            </tr>
                            <tr>
                                <td>Parmanent Address</td>
                                <td>:</td>
                                <td>{{$view->country->country_name}}, {{ $view->division->division_name}}, {{ $view->district->district_name}}, <span>post code : {{ $view->post_code }}</span> </td>
                            </tr>
                            <tr>
                                <td>Parmanent Address Details</td>
                                <td>:</td>
                                <td>{{ $view->details }}</td>
                            </tr>
                            <tr>
                                <td>Employee Type </td>
                                <td>:</td>
                                <td>{{ $view->employeeType->name }}</td>
                            </tr>
                            <tr>
                                <td>Date of Birth</td>
                                <td>:</td>
                                <td>{{ Carbon\Carbon::parse($view->date_of_birth)->format('D, d F Y') }}</td>
                            </tr>
                            <tr>
                                <td>Moblie Number</td>
                                <td>:</td>
                                <td>
                                  @if($view->mobile_no == NULL) No Number ... @else {{ $view->mobile_no }} @endif
                                </td>
                            </tr>
                            <tr>
                                <td>Phone Number</td>
                                <td>:</td>
                                <td>{{ $view->phone_no }}</td>
                            </tr>
                            <tr>
                                <td>Email Address</td>
                                <td>:</td>
                                <td>{{ $view->email }}</td>
                            </tr>
                            <tr>
                                <td>Maritus Status</td>
                                <td>:</td>
                                <td>
                                  @if($view->maritus_status == 1)
                                    Unmarid
                                  @else
                                    Marid
                                  @endif
                                </td>
                            </tr>
                            <tr>
                                <td>Gender</td>
                                <td>:</td>
                                <td>@if($view->gender == 'm') Male @else Female @endif</td>
                            </tr>
                            <tr>
                                <td>Religion</td>
                                <td>:</td>
                                <td>
                                  @if($view->religion == 1)
                                      Muslim
                                  @elseif($view->religion == 2)
                                    Christianity
                                  @else
                                    Hinduism
                                  @endif
                                </td>
                            </tr>
                            <tr>
                                <td>Joining Date</td>
                                <td>:</td>
                                <td>{{ Carbon\Carbon::parse($view->joining_date)->format('D, d F Y') }}</td>
                            </tr>
                            <tr>
                                <td>Confirmation Date</td>
                                <td>:</td>
                                <td>{{ Carbon\Carbon::parse($view->confirmation_date)->format('D, d F Y') }}</td>
                            </tr>
                            <tr>
                                <td>Appointment Date</td>
                                <td>:</td>
                                <td>{{ Carbon\Carbon::parse($view->appointment_date)->format('D, d F Y') }}</td>
                            </tr>
                            <tr>
                                <td>Job Status</td>
                                <td>:</td>
                                <td>@if($view->job_status == 1) Active @else Inactive @endif </td>
                            </tr>
                            <tr>
                                <td>Job Location</td>
                                <td>:</td>
                                <td>{{ $view->job_location }}</td>
                            </tr>
                            <tr>
                                <td>Employee Insert Date</td>
                                <td>:</td>
                                <td>{{ Carbon\Carbon::parse($view->emp_insert_date)->format('D, d F Y') }}</td>
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
