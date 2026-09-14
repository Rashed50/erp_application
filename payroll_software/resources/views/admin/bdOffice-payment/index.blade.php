@extends('layouts.admin-master')
@section('title') Office Payment @endsection
@section('content')
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Employee Payment Setup For Other Office </h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        </ol>
    </div>
</div>
<!-- Session Message Section -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        @if(Session::has('success'))
        <div class="alert alert-success alertsuccess" role="alert">
            <strong>{{Session::get('success')}}</strong>
        </div>
        @endif
        
        @if(Session::has('error'))
        <div class="alert alert-warning alerterror" role="alert">
            <strong>{{Session::get('error')}} </strong>
        </div>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-md-1"></div>
    <div class="col-md-10">
        <div class="select_employee">
            <div class="card">
                <div class="card-body card_form">

                    <div class="form-group row custom_form_group">
                        <label class="col-sm-3 control-label">Employee ID:</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control typeahead" placeholder="Input Employee ID"
                                name="emp_id" id="emp_id_search" onkeyup="empSearch()" onfocus="showResult()"
                                onblur="hideResult()">
                            <div id="showEmpId"></div>
                            <span id="error_show" class="d-none" style="color: red"></span>
                        </div>
                        <div class="col-sm-3">
                            <button type="submit" onclick="searchEmployeeDetails()" style="margin-top: 2px"
                                class="btn btn-primary waves-effect">SEARCH</button>
                        </div>
                    </div>

                    {{-- Show Employee Details with form UI --}}
                    <div class="col-md-12">
                        <div id="showEmployeeDetails" class="d-none">

                            <div class="row">
                                <!-- employee Deatils -->
                                <div class="col-md-6">
                                    <table
                                        class="table table-bordered table-striped table-hover custom_view_table show_employee_details_table"
                                        id="showEmployeeDetailsTable">
                                        <tr>
                                            <td> <span class="emp">Project:</span> <span id="show_employee_project_name"
                                                    class="emp2"></span> </td>
                                        </tr>
                                        <tr>
                                            <td> <span class="emp"> Name:</span> <span id="show_employee_name"
                                                    class="emp2"></span> </td>
                                        </tr>
                                        <tr>
                                            <td> <span class="emp">Iqama No:</span> <span id="show_employee_akama_no"
                                                    class="emp2"></span> </td>
                                        </tr>
                                        <tr>
                                            <td> <span class="emp">Sponsor:</span> <span id="show_employee_sponsor_name"
                                                    class="emp2"></span> </td>
                                        </tr>
                                        <tr>
                                            <td> <span class="emp">Type:</span> <span id="show_employee_type"
                                                    class="emp2"></span> </td>
                                        </tr>

                                    </table>
                                </div>
                                <!-- Salary Deatils -->
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <table
                                                class="table table-bordered table-striped table-hover custom_view_table show_employee_details_table"
                                                id="showEmployeeDetailsTable">

                                                <tr>
                                                    <td> <span class="emp">Trade:</span> <span
                                                            id="show_employee_category" class="emp2"></span> </td>
                                                </tr>
                                                <tr>
                                                    <td> <span class="emp">Basic Amount:</span> <span
                                                            id="show_employee_basic" class="emp2"></span> </td>
                                                </tr>
                                                <tr>
                                                    <td> <span class="emp">Hourly Rate:</span> <span
                                                            id="show_employee_hourly_rent" class="emp2"></span> </td>
                                                </tr>
                                                <tr>
                                                    <td> <span class="emp">Food Allowance:</span> <span
                                                            id="show_employee_food_allowance" class="emp2"></span> </td>
                                                </tr>

                                                <tr>
                                                    <td> <span class="emp">Saudi Tax:</span> <span
                                                            id="show_employee_saudi_tax" class="emp2"></span> </td>
                                                </tr>

                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment User Interface -->
                            <div class="row">
                              
                                <div class="col-md-12">
                                    <form style="margin-top:20px" class="form-horizontal" id="registration"
                                        action="{{ route('employee.payment.from-bdoffice.create.insert-request') }}" method="post">
                                        @csrf
                                        <div class="card">
                                            <div class="card-body card_form" style="padding-top: 20;">
                                                <input type="hidden" id="emp_auto_id" name="emp_id" value="">

                                                <div
                                                    class="form-group row custom_form_group{{ $errors->has('approved_amount') ? ' has-error' : '' }}">
                                                    <label class="control-label col-md-3">Approved Amount :<span
                                                            class="req_star">*</span></label>
                                                    <div class="col-md-4">
                                                        <input type="number" class="form-control"
                                                            placeholder="Approved Amount Here" id="approved_amount"
                                                            name="approved_amount" required>
                                                        @if ($errors->has('approved_amount'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('approved_amount') }}</strong>
                                                        </span>
                                                        @endif
                                                    </div>
                                                      <label class="col-sm-3 control-label d-block" style="text-align: left;">Exchange Rate:<span class="req_star">*</span> </label>
                                                        <div class="col-sm-2">
                                                            <input type="number" class="form-control" id="exchange_rate" name="exchange_rate" value="0" placeholder="Exchange Rate" requried >
                                                    
                                                        </div>
                                                </div>

                                               
                                            </div>
                                            <div class="card-footer card_footer_button text-center">
                                                <button type="submit"
                                                    class="btn btn-primary waves-effect">SUBMIT</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                
                            </div>

                         
                        </div>
                    </div>
                    {{-- Show Employee Details --}}

                </div>
            </div>
        </div>
    </div>
    <div class="col-md-1"></div>
</div>

<!-- Payment Employee list -->

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
                                        <th>Employee ID</th>
                                        <th>Emp. Name</th>
                                        <th>Emp. Type</th>
                                        <th>Approved Amount</th>                                        
                                        <th>Payment Status</th>

                                    </tr>
                                </thead>
                                <tbody id="workrecords">
                                    @foreach($all as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->employee->employee_id ?? '' }}</td>
                                        <td>{{ $item->employee->employee_name ?? '' }}</td>
                                        <td>{{ $item->employee->employeeType->name ?? ''  }}</td>
                                        <td>{{ $item->approved_amount }}</td>

                                        <td>
                                             <a href="{{ route('employee.payment.from-bdoffice.edit-from',$item->bdofpay_auto_id ) }}"
                                                title="edit"><i class="fa fa-pencil-square fa-lg edit_icon"></i></a>
                                            <a href="#" onClick="deleteEmpMothlyWor('{{ $item->bdofpay_auto_id }}')"
                                                title="delete"><i id="" class="fa fa-trash fa-lg delete_icon"></i></a>
                                             
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

    
    function deleteEmpMothlyWorkRecord(id) {
        // alert(id);

        swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this imaginary file!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
            .then((willDelete) => {
                if (willDelete) {

                    $.ajax({
                        type: 'GET',
                        url: "{{  url('admin/delete/work') }}/" + id,
                        dataType: 'json',
                        success: function (response) {
                            window.location.reload();
                        }
                    });


                }
            });


    }



</script>
@endsection
