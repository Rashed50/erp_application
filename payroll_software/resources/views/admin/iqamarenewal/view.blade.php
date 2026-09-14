@extends('layouts.admin-master')
@section('title') Employee view @endsection
@section('content')
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Iqama Renewal Expense Details</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li><a href="{{ route('show-pending-iqamarenewal-fee') }}">Unapproval Iqama</a></li>
            <li class="active">View</li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">

            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2"></div>
                    <div class="col-md-8">
                        <table class="table table-bordered table-striped table-hover custom_view_table">
                            <tr>
                                <td>Employee Id</td>
                                <td> <b>{{$view->employee_id}} </b></td>
                            </tr>
                            <tr>
                                <td>Name</td>
                                <td><b>{{$view->employee_name}} </b></td>
                            </tr>
                            <tr>
                                <td>Job Status</td>
                                     <td>@if($view->job_status == 1) Active @else Inactive @endif </td>
                            </tr>
                            <tr>
                                <td>Iqama No</td>
                                <td>{{$view->akama_no}} , Expired at : {{ Carbon\Carbon::parse($view->akama_expire_date)->format('D, d F Y') }}</td>
                            </tr>                            
                            <tr>iqama_expire_date
                                <td>Jawazat Fee</td>
                                <td>{{$view->jawazat_fee}}</td>
                            </tr>
                            <tr>
                                <td>Maqtab Alamal Fee</td>
                                <td>{{$view->maktab_alamal_fee}}</td>
                            </tr>
                            <tr>
                                <td>Medical Insurance</td>
                                <td>{{$view->medical_insurance}}</td>
                            </tr>
                            <tr>
                                <td>VISA Amount</td>
                                <td>{{$view->bd_amount}}</td>
                            </tr>
                            <tr>
                                <td>Jawazat Penalty</td>
                                <td>{{$view->jawazat_penalty}}</td>
                            </tr>
                            <tr>
                                <td>Others Fee</td>
                                <td>{{$view->others_fee}}</td>
                            </tr>
                            <tr>
                                <td>Total Amount</td>
                                <td> <b> {{$view->total_amount}} </b></td>
                            </tr>
                            <tr>
                                <td>Remarks</td>
                                <td>{{$view->remarks}}</td>
                            </tr>
                            <tr>
                                <td> Iqama Renewal for </td>
                                <td>{{$view->duration}} Months</td>
                            </tr>
                            <tr>
                                <td>Approval Status</td>
                                <td>{{$view->approved_status == 0 ? 'Waiting for Approval' : 'Approved'}}</td>
                            </tr>
                            <tr>
                                <td>Inserted at</td>
                                <td>{{ Carbon\Carbon::parse($view->emp_insert_date)->format('D, d F Y') }}</td>
                            </tr>
                            <tr>
                                <td>Iqama Valid Upto</td>
                                <td>{{ Carbon\Carbon::parse($view->iqama_expire_date)->format('D, d F Y') }}</td>
                            </tr>
                             <tr>
                                <td>Renewal at</td>
                                <td>{{ Carbon\Carbon::parse($view->renewal_date)->format('D, d F Y') }}</td>
                            </tr>
                            <tr>
                                <td>Expense Paid at</td>
                                <td>{{ Carbon\Carbon::parse($view->payment_date)->format('D, d F Y') }}</td>
                            </tr>
                            
                            <tr>
                                <td> Expensed By </td>
                                <td>{{$view->expense_paid_by == 1 ? 'Self' : 'Company'}}</td>
                            </tr>
                           
                                                      
                        </table>
                    </div>
                    <div class="col-md-2"></div>
                </div>
            </div>
             
        </div>
    </div>
</div>

@endsection
