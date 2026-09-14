@extends('layouts.admin-master')
@section('title')Attn Approval Iqama list @endsection
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
        <h4 class="pull-left page-title bread_title">Employee Attendance Approval  </h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active"> Approval </li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-7">
        @if(Session::has('success'))
        <div class="alert alert-success alertsuccess" role="alert">
            <strong>{{Session::get('success')}}</strong>
        </div>
        @endif
        @if(Session::has('error'))
        <div class="alert alert-warning alerterror" role="alert">
            <strong>{{Session::get('error')}}</strong>
        </div>
        @endif
    </div>
    <div class="col-md-2"></div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table id="alltableinfo" class="table table-bordered custom_table mb-0">
                                <thead>
                                    <tr>
                                        <th>S.N</th>
                                        <th>Project Name</th>
                                        <th>Attendance Month</th>
                                        <th>Year</th>
                                        <th>Approval Status</th>
                                        <th>Action</th>
                                      
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($approval_pending_record as $item)
                                    <tr>  
                                        <td>{{$loop->iteration}}</td>
                                        <td>{{ $item->proj_name }}</td>
                                        <td>{{ $item->month->name }}</td>
                                        <td>{{ $item->year }}</td>
                                        <td>{{ $item->approval_status->name }}</td>
                                        <td> <a href="{{ route('employee.attendance.month-record.approval-request',$item->atten_appro_auto_id) }}" title="Approve" id="approved" class="approve_button"><i class="fa fa-thumbs-up"></i></a>
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

 
@endsection

@section('script')
<script type="text/javascript">
    // function iqamaDetailsView(){
    //   var emplId = $('input[name=emp_id]').val();
    //         alert(emplId);

    // }
</script>
@endsection
