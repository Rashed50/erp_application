@extends('layouts.admin-master')
@section('title') Employee Salary List @endsection
@section('content')

<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Employee Salary Details </h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active">Employee Salary Details</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        @if(Session::has('success_soft'))
        <div class="alert alert-success alertsuccess" role="alert">
            <strong>Successfully!</strong> delete salary details.
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
    <div class="col-12">
        <div class="card">

            <div class="card-body">

                <div class="row">
                    <form method="post" action="{{route('employee-salary-details-list-search')}}">
                        @csrf
                        <div class="row">
                            <div class="col-md-3"></div>
                            <div class="col-md-3">
                                <div class="form-group row custom_form_group">
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" placeholder="Search By Employee ID" name="employee_id" id="employee_id">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" style="margin-top: 2px" class="btn btn-primary waves-effect">SEARCH</button>
                            </div>
                            <div class="clearfix"></div>
                        </div>

                    </form>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table id="employeeTable" class="table table-bordered table-hover custom_table mb-0">
                                <thead>
                                    <th>Emp Id</th>
                                    <th>Emp Name</th>
                                    <th>Emp Type</th>
                                    <th>Basic Salary</th>
                                    <th>Basic Hours</th>
                                    <th>Hourly Rate</th>
                                    <th>Food</th>
                                    <th>S.T</th>
                                    <th>Manage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($all as $key=>$item)
                                    <tr>
                                        <td>{{ $item->employee_id }}</td>
                                        <td>{{ $item->employee_name }}</td>

                                        @if($item->hourly_employee == 1)
                                        <td>Direct(Hourly)</td>
                                        @else
                                        <td>{{ $item->emp_type_id == 1 ? 'Direct' : 'Indirect' }}</td>
                                        @endif

                                        <td>{{ $item->basic_amount }}</td>
                                        <td>{{ $item->basic_hours }}</td>
                                        <td>{{ $item->hourly_rent }}</td>
                                        <td>{{ $item->food_allowance }}</td>
                                        <td>{{ $item->saudi_tax }}</td>
                                        
                                        <td>
                                            <!--<a href="{{ route('salary-single-details',[$item->sdetails_id]) }}" title="view"><i class="fas fa-eye fa-lg view_icon"></i></a>-->
                                            <a href="{{ route('salary-single-edit',[$item->sdetails_id]) }}" title="edit"><i class="fa fa-pencil-square fa-lg edit_icon"></i></a>
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


<script>
    $(document).ready(function() {

    });
</script>

@endsection