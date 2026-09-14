@extends('layouts.admin-master')
@section('title') Emp. Update @endsection
@section('content')

@section('internal-css')

<style>
    tr {
        height: 20px;
        padding: 0;
        margin: 0;
    }

    td {
        padding-bottom: 0;
        margin: 0;
    }
</style>
@endsection

<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Employee All Information Update</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active">Employee Update</li>
        </ol>
    </div>
</div>
{{-- Session Message --}}
<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-7">
        @if(Session::has('success'))
        <div class="alert alert-success alertsuccess" role="alert" style="margin-left: -20px">
            <strong>{{Session::get('success')}}</strong>
        </div>
        @endif
        @if(Session::has('error'))
        <div class="alert alert-warning alerterror" role="alert" style="margin-left: -20px">
        <strong>{{Session::get('error')}}</strong>
        </div>
        @endif
    </div>
    <div class="col-md-2"></div>
</div>

<!--  Employee All Information Update Modal-->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <!-- employee information searching UI -->
            <div class="card-header">
                <div  class="row form-group custom_form_group{{ $errors->has('searchBy') ? ' has-error' : '' }}">
                   <label class="col-md-2 control-label d-block" style="text-align: left;">Employee Searching By
                   </label>
                   <div class="col-md-3">
                       <select class="form-select" name="searchBy" id="searchBy" required>
                           <option value="employee_id"> Employee ID</option>
                           <option value="akama_no">Iqama </option>
                       </select>
                   </div>

                   <div class="col-md-3">
                       <input type="text" placeholder="Enter ID/Iqama/Passport No"
                           class="form-control" id="empl_info" name="empl_info"
                           value="{{ old('empl_info') }}" required autofocus>
                       @if ($errors->has('empl_info'))
                           <span class="invalid-feedback" role="alert">
                               <strong>{{ $errors->first('empl_info') }}</strong>
                           </span>
                       @endif
                   </div>
                   <div class="col-md-2">
                       <button type="submit" onclick="searchEmployeeForUpdateAllInformation()" style="margin-top: 2px"
                       class="btn btn-primary waves-effect">SEARCH</button>
                   </div>
                   <div class="col-md-2">
                        <!--<button type="button" class="btn btn-primary waves-effect" data-toggle="modal" data-target="#employee_salary_payment_method_modal">Payment Method Update</button>-->
                   </div>

               </div>
            </div>
            <!-- show employee information -->
            <div class="card-body card_form" style="padding-top: 0px;">
                    <div id="showEmployeeDetails" class="d-none" style="padding-top: 0px;">
                        <form action="{{ route('employee-all-information-update') }}" method="POST" >
                            @csrf
                            <div class="row">
                                <!-- employee Deatils -->
                                <div class="col-md-6">
                                    <div class="header_row">
                                        <span class="emp_info">Employee Information</span>
                                    </div>
                                    <table class="table table-bordered table-striped table-hover custom_view_table show_employee_details_table" id="showEmployeeDetailsTable">

                                        <tr>
                                            <td>
                                                <div class="form-group row custom_form_group">
                                                    <label class="col-sm-8 control-label">Employee ID : <strong id="show_employee_id" style="font-size: 20px; color:red;font-weight:bold"></strong> </label>
                                                    <input id="employee_auto_id" name="id" type="hidden" required>
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <div class="form-group row custom_form_group">
                                                    <label class="col-sm-3 control-label">Emp. Name :</label>
                                                    <div class="col-sm-7">
                                                        <input class="form-control" id="show_employee_name" name="emp_name" type="text" style='text-transform:uppercase' required>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                {{-- <div class="form-group row custom_form_group">
                                                    <label class="col-sm-2 control-label">Iqama No:</label>
                                                    <div class="col-sm-5">
                                                        <input class="form-control" id="show_employee_akama_no" name="akama_no" type="number" onkeyup="checkThisEmployeeIqamaNumber()"  required>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <input class="form-control" id="show_employee_akama_expire_date" name="akama_expire" type="date">
                                                    </div>
                                                </div> --}}

                                                <div class="form-group row custom_form_group{{ $errors->has('show_employee_akama_no') ? ' has-error' : '' }}">
                                                    <label class="col-sm-2 control-label">Iqama No:<span class="req_star">*</span></label>
                                                    <div class="col-sm-4">
                                                        <input type="number" placeholder="Input Iqama Number Here" class="form-control"
                                                        id="show_employee_akama_no" name="akama_no"   onfocusout="checkThisEmployeeIqamaNumber()" required>
                                                            <span id="uniqueIqamaErrorMsg" class="d-none error">This Iqama Number Already Exist!</span>
                                                        @if ($errors->has('show_employee_akama_no'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('show_employee_akama_no') }}</strong>
                                                        </span>
                                                        @endif
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <input class="form-control" id="show_employee_akama_expire_date" name="akama_expire" type="date">
                                                    </div>
                                                </div>

                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                {{-- <div class="form-group row custom_form_group">
                                                    <label class="col-sm-2 control-label">Passport No:</label>
                                                    <div class="col-sm-5">
                                                        <input class="form-control" id="show_employee_passport_no"  name="passfort_no" type="text" onkeyup="checkThisEmployeePassportNumber()"  required>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <input class="form-control" id="show_employee_passport_expire_date" name="passfort_expire_date" type="date">
                                                    </div>
                                                </div> --}}

                                                <div class="form-group row custom_form_group{{ $errors->has('show_employee_passport_no') ? ' has-error' : '' }}">
                                                    <label class="col-sm-2 control-label">Passport No:<span class="req_star">*</span></label>
                                                    <div class="col-sm-5">
                                                        <input type="text" placeholder="Input Passport Number Here" class="form-control"
                                                        id="show_employee_passport_no" name="passfort_no"  onfocusout="checkThisEmployeePassportNumber()" >
                                                            <span id="uniquePassportErrorMsg" class="d-none error">This Passport Number Already Exist!</span>
                                                        @if ($errors->has('show_employee_passport_no'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('show_employee_passport_no') }}</strong>
                                                        </span>
                                                        @endif
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <input class="form-control" id="show_employee_passport_expire_date" name="passfort_expire_date" type="date">
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <div class="form-group row custom_form_group{{ $errors->has('sponsor_id') ? ' has-error' : '' }}">
                                                    <label class="col-sm-3 control-label"> Sponsor:<span class="req_star">*</span></label>
                                                    <div class="col-sm-4">
                                                        <select class="form-select" name="sponsor_id" required>
                                                            <option value="">Select Sponsor</option>
                                                        </select>
                                                    </div>
                                                    <label class="col-sm-2 control-label">Status:<span class="req_star">*</span></label>
                                                    <div class="col-sm-3">
                                                            <select class="form-select" name="EmpStatus_id" style="font-size: 20px; color:red;font-weight:bold" required>
                                                                <option value="">Select Status</option>
                                                            </select>

                                                    </div>

                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="form-group row custom_form_group">
                                                    <label class="col-sm-2 control-label">Mobile No. :</label>
                                                    <div class="col-sm-3">
                                                        <input class="form-control" id="show_employee_mobile_no" name="mobile_no" type="text">
                                                    </div>
                                                    <label class="col-sm-2 control-label">Email:</label>
                                                    <div class="col-sm-4">
                                                        <input class="form-control" id="show_employee_email" name="email" type="email">
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <div class="form-group row custom_form_group">
                                                    <label class="col-sm-3 control-label">Project:</label>
                                                    <div class="col-sm-7">
                                                        <select class="form-select" name="projectStatus" required>
                                                            <option value="">Select Here</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group row custom_form_group">
                                                    <label class="col-sm-3 control-label">Assign Date:</label>
                                                    <div class="col-sm-7">
                                                        <input class="form-control" id="" name="asign_date" type="date" value="{{date('Y-m-d')}}">
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <div class="row custom_form_group">
                                                    <label class="col-sm-3 control-label">Address:<span class="req_star">*</span></label>
                                                    <div class="col-sm-7">
                                                        <div class="parmanent_address">
                                                            <!-- country -->
                                                            <div class="form-group">
                                                                <select class="form-select" name="country_id">
                                                                    <option value="">Select Country</option>
                                                                    <option value="id "> </option>
                                                                </select>
                                                            </div>
                                                            <!-- division -->
                                                            <div class="form-group">
                                                                <select class="form-select" name="division_id">
                                                                    <option value="">Select Division</option>
                                                                </select>
                                                            </div>

                                                            <div class="form-group">
                                                                <select class="form-select" name="district_id">
                                                                    <option value="">Select District</option>
                                                                </select>
                                                            </div>
                                                            <div class="form-group">
                                                                <input type="text" class="form-control" value="{{ old('post_code') }}" id="post_code" name="post_code" placeholder="Input Post Code">
                                                            </div>
                                                            <div class="form-group">
                                                                <textarea class="form-control detailsAdd" id="details" name="details" placeholder="Input Address Details">{{ old('details') }}</textarea>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <div class="form-group row custom_form_group">
                                                    <label class="col-sm-3 control-label">Joining:</label>
                                                    <div class="col-sm-5">
                                                        <input class="form-control" id="show_employee_joining_date" name="joining_date" type="date">
                                                    </div>
                                                    <label class="col-sm-1 control-label">DoB :</label>
                                                    <div class="col-sm-3">
                                                        <input class="form-control" id="show_employee_date_of_birth" name="date_of_birth" type="date">
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="form-group row custom_form_group">
                                                    <label class="col-sm-3 control-label">Confirmation:</label>
                                                    <div class="col-sm-3">
                                                        <input class="form-control" id="show_employee_confirmation_date" name="confirmation_date" type="date">
                                                    </div>

                                                    <label class="col-sm-3 control-label">Appointed:</label>
                                                    <div class="col-sm-3">
                                                        <input class="form-control" id="show_employee_confirmation_date" name="appointment_date" type="date">
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="form-group row custom_form_group">
                                                    <label class="col-sm-3 control-label">Department:</label>
                                                    <div class="col-sm-5">
                                                        <select class="form-select" name="department_id">
                                                            <option value="">Select Department</option>

                                                        </select>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <div class="form-group row custom_form_group">
                                                    <label class="col-sm-3 control-label">Marital Status:<span class="req_star">*</span></label>
                                                    <div class="col-sm-3">
                                                        <select class="form-select" name="maritus_status">
                                                            <option value="1">Unmarried</option>
                                                            <option value="2">Married</option>
                                                        </select>
                                                    </div>
                                                    <label class="col-sm-2 control-label">Gender:<span class="req_star">*</span></label>
                                                    <div class="col-sm-4 gender">
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="gender" checked id="gender" value="1">
                                                            <label class="form-check-label">Male</label>
                                                        </div>

                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="gender" id="gender" value="2">
                                                            <label class="form-check-label">Female</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>



                                    </table>
                                </div>
                                <!-- Salary Deatils -->
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="header_row">
                                                <span class="emp_info">Employee Salary Details</span>
                                            </div>
                                            <table class="table table-bordered table-striped table-hover custom_view_table show_employee_details_table" id="showEmployeeDetailsTable">

                                                <tr>
                                                    <td>
                                                        <div class="row custom_form_group">
                                                            <label class="col-sm-3 control-label">Emp. Type:<span class="req_star">*</span></label>
                                                            <div class="col-sm-7">
                                                                <select class="form-select" name="emp_type_id" required>
                                                                    <option value="">Select Employee Type</option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="row custom_form_group d-none" id="emp_type_wise_show">
                                                            <label class="col-sm-3 control-label"></label>
                                                            <div class="col-sm-5">
                                                            Hourly Employee: <input class="" type="checkbox" id="hourly_type_emp" name ="hourly_type_emp"  value="1" >
                                                            </div>
                                                        </div>

                                                          <div class="row custom_form_group">
                                                                <label class="col-sm-3 control-label"></label>
                                                            <div class="col-sm-5 ">
                                                             Staff Employee: <input class="" type="checkbox" id="staff_employee" name ="staff_employee"  value="1" >
                                                            </div>
                                                        </div>

                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>
                                                        <div class="row custom_form_group">
                                                            <label class="col-sm-3 control-label">Designation:<span class="req_star">*</span></label>
                                                            <div class="col-sm-7">
                                                                <div class="">
                                                                    <select class="form-select" name="designation_id" required>
                                                                        <option value="">Select Designation</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="form-group row custom_form_group">
                                                            <label class="col-sm-3 control-label">Basic Hours:</label>
                                                            <div class="col-sm-7">
                                                                <input class="form-control" id="show_employee_basic_ours"  min="0" name="basic_hours" type="number" required>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>
                                                        <div class="form-group row custom_form_group">
                                                            <label class="col-sm-3 control-label">Basic Amount:</label>
                                                            <div class="col-sm-7">
                                                                <input class="form-control" id="show_employee_basic" onkeyup="calculateHourlyRate()" min="0" name="basic_amount" type="number" required>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>
                                                        <div class="form-group row custom_form_group">
                                                            <label class="col-sm-3 control-label">Hourly Rate:</label>
                                                            <div class="col-sm-7">
                                                                <input class="form-control" id="show_employee_hourly_rent" name="hourly_rent" step="0.01" min="0" type="number">
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>
                                                        <div class="form-group row custom_form_group">
                                                            <label class="col-sm-3 control-label">House Rent:</label>
                                                            <div class="col-sm-7">
                                                                <input class="form-control" id="show_employee_house_rent" name="house_rent" min="0" required type="number">
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>
                                                        <div class="form-group row custom_form_group">
                                                            <label class="col-sm-3 control-label">Mobile:</label>
                                                            <div class="col-sm-7">
                                                                <input class="form-control" id="show_employee_mobile_allow" name="mobile_allowance" min="0" required type="number">
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>
                                                        <div class="form-group row custom_form_group">
                                                            <label class="col-sm-3 control-label">Food:</label>
                                                            <div class="col-sm-7">
                                                                <input class="form-control" id="show_employee_food_allow" name="food_allowance" min="0" value="0" type="number" required>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="form-group row custom_form_group">
                                                            <label class="col-sm-3 control-label">Contribution Amount:</label>
                                                            <div class="col-sm-7">
                                                                <input class="form-control" id="show_employee_contribution_amoun" name="contribution_amoun" min="0" value="0" type="number" required>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="form-group row custom_form_group">
                                                            <label class="col-sm-3 control-label">Saudi Tax:</label>
                                                            <div class="col-sm-7">
                                                                <input class="form-control" id="show_employee_saudi_tax" name="saudi_tax" min="0" value="0" type="number" required>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>
                                                        <div class="form-group row custom_form_group">
                                                            <label class="col-sm-3 control-label">Medical:</label>
                                                            <div class="col-sm-7">
                                                                <input class="form-control" id="show_employee_medical_allow" name="medical_allowance" min="0" value="0" type="number" required>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="form-group row custom_form_group">
                                                            <label class="col-sm-3 control-label"> Travels:</label>
                                                            <div class="col-sm-7">
                                                                <input class="form-control" id="show_employee_local_travel_allow" name="local_travel_allowance" min="0" value="0" type="number" required>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="form-group row custom_form_group">
                                                            <label class="col-sm-3 control-label">Conveyance:</label>
                                                            <div class="col-sm-7">
                                                                <input class="form-control" id="show_employee_conveyance_allow" name="conveyance_allowance" min="0" value="0" type="number" required>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="form-group row custom_form_group">
                                                            <label class="col-sm-3 control-label">Increment Amount:</label>
                                                            <div class="col-sm-7">
                                                                <input class="form-control" id="show_employee_increment_amount" name="increment_amount" type="number">
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="form-group row custom_form_group">
                                                            <label class="col-sm-3 control-label">Others:</label>
                                                            <div class="col-sm-7">
                                                                <input class="form-control" id="show_employee_others" name="employee_others" type="number" value="0">
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 text-right">
                                <button type="submit" style="margin-top: 2px" class="btn btn-primary waves-effect">Update</button>
                            </div>
                        </form>
                    </div>
            </div>
        </div>
    </div>
</div>


<!--  Employee Salary Payment Method Update Modal-->
<div class="modal fade" id="employee_salary_payment_method_modal" tabindex="-1" role="dialog"   aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Salary Payment Method Update <span class="text-danger" id="errorData"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" id="salary_payment_method_form" method="post" action="{{ route('employee-all-information-update') }}">
                @csrf
                <div class="modal-body">

                        <input type="hidden" id="operation_type" name="operation_type" value="2" required>

                        <div class="form-group row custom_form_group">
                            <label class="col-md-4 control-label">Employee ID</label>

                            <input type="text" class="form-control col-md-8" name="employee_id", id="employee_id" autofocus required>
                                @if ($errors->has('employee_id'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('employee_id') }}</strong>
                                </span>
                                @endif

                        </div>

                        <div class="form-group row custom_form_group">
                            <label class="col-md-4 control-label">Payment Method</label>
                            <div class="col-md-8">
                                <select class="form-select" name="payment_method" id="payment_method" required>
                                    <option value="">Select One</option>
                                    <option value="Bank">Bank</option>
                                    <option value="Cash">Cash</option>
                                </select>
                                @if ($errors->has('payment_method'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('payment_method') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        </div>
                        <button type="submit" id="submit_button" name="submit_button"    class="btn btn-success">UPDATE</button>
                </div>
            </form>
        </div>
    </div>
</div>



<script type="text/javascript">


    function calculateHourlyRate(){

       var basic = document.getElementById('show_employee_basic').value != "" ? parseFloat(document.getElementById('show_employee_basic').value) : 0;
       document.getElementById("show_employee_hourly_rent").value = (basic/300).toFixed(2);

   }

    $('#empl_info').keydown(function(event){
        if(event.keyCode == 13){
            searchEmployeeForUpdateAllInformation();
        }
    })

    function showSweetAlertMessage(type,message){
        const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            })
                Toast.fire({
                    type: type,
                    title: message,
                })
    }

    function searchEmployeeForUpdateAllInformation() {

      var searchType = $('#searchBy').find(":selected").val();
        var searchValue = $("#empl_info").val();

          if($("#empl_info").val().length === 0) {
                showSweetAlertMessage('error',"Please Enter Employee ID/Iqama/Passport Number");
                return
        }

      $.ajax({
        type: 'POST',
        url: "{{ route('search.employee-for-update') }}",
        data: {
            searchType: searchType,
            searchValue: searchValue
        },
        dataType: 'json',
        success: function(response) {


          if (response.status != 200) {

            $("input[id='emp_id_search']").val('');
            $("#showEmployeeDetails").addClass("d-none").removeClass("d-block");
            showSweetAlertMessage(response.error,response.message);
            return ;
          }else if(response.findEmployee.job_status == 0){
            showSweetAlertMessage("error","This Employee is not Approved");
            return;
          }

          // show form
          $("#showEmployeeDetails").removeClass("d-none").addClass("d-block");

          $('input[id="employee_auto_id"]').val(response.findEmployee.emp_auto_id);
          $("#show_employee_id").text(response.findEmployee.employee_id);
          $("input[id='show_employee_name']").val(response.findEmployee.employee_name);
          $("input[id='show_employee_akama_no']").val(response.findEmployee.akama_no);
          $("input[id='show_employee_akama_expire_date']").val(response.findEmployee.akama_expire_date);
          $("input[id='show_employee_passport_no']").val(response.findEmployee.passfort_no);
          $("input[id='show_employee_passport_expire_date']").val(response.findEmployee.passfort_expire_date);
          $("input[id='show_employee_confirmation_date']").val(response.findEmployee.confirmation_date);
          $("input[id='show_employee_appointment_date']").val(response.findEmployee.appointment_date);
          $("input[id='show_employee_date_of_birth']").val(response.findEmployee.date_of_birth);
          $("input[id='show_employee_mobile_no']").val(response.findEmployee.mobile_no);
          $("input[id='show_employee_email']").val(response.findEmployee.email);
          $("input[id='show_employee_joining_date']").val(response.findEmployee.joining_date);

          /* show employee Salary */
          $("input[id='show_employee_basic']").val(response.findEmployee.basic_amount);
          $('input[id="show_employee_basic_ours"]').val(response.findEmployee.basic_hours);
          $("input[id='show_employee_house_rent']").val(response.findEmployee.house_rent);
          $("input[id='show_employee_hourly_rent']").val(response.findEmployee.hourly_rent);
          $("input[id='show_employee_mobile_allow']").val(response.findEmployee.mobile_allowance);
          $("input[id='show_employee_food_allow']").val(response.findEmployee.food_allowance);
          $("input[id='show_employee_contribution_amoun']").val(response.findEmployee.cpf_contribution);
          $("input[id='show_employee_saudi_tax']").val(response.findEmployee.saudi_tax);
          $("input[id='show_employee_medical_allow']").val(response.findEmployee.medical_allowance);
          $("input[id='show_employee_local_travel_allow']").val(response.findEmployee.local_travel_allowance);
          $("input[id='show_employee_conveyance_allow']").val(response.findEmployee.conveyance_allowance);
          $("input[id='show_employee_increment_no']").val(response.findEmployee.increment_no);
          $("input[id='show_employee_increment_amount']").val(response.findEmployee.increment_amount);
          $("input[id='show_employee_food_allowance']").val(response.findEmployee.food_allowance);
          $("input[id='show_employee_saudi_tax']").val(response.findEmployee.saudi_tax);
          $("input[id='show_employee_others']").val(response.findEmployee.others1);

          $("input[id='show_employee_address_details']").val(response.findEmployee.details);
          $("input[id='show_employee_present_address']").val(response.findEmployee.present_address);

          if (response.findEmployee.maritus_status == 1) {
            $("input[id='show_employee_metarials']").val('Unmarried');
          } else {
            $("input[id='show_employee_metarials']").val('Married');
          }

          // update as office staff
          // $('#staff_employee').prop('checked', parseInt(response.findEmployee.staff_employee));

            if (parseInt(response.findEmployee.staff_employee) == 1) {
                $('#staff_employee').attr('checked', true);
              } else {
                $('#staff_employee').attr('checked', false);
            }

          // department
          if (response.allDepartment != '') {
            $('select[name="department_id"]').empty();
            if (response.findEmployee.department_id == null) {
              $('select[name="department_id"]').append('<option value="">No Assigned Department</option>');

            } else {

              $('select[name="department_id"]').append('<option value="' + response.findEmployee.dep_id + '">' + response.findEmployee.dep_name + '</option>');
            }
            $.each(response.allDepartment, function(key, value) {
              $('select[name="department_id"]').append('<option value="' + value.dep_id + '">' + value.dep_name + '</option>');
            });
          } else {
            $('select[[name="department_id"]').append('<option>Data Not Found</option>');
          }

          // manpower
          if (response.allEmpType != '') {
            $('select[name="emp_type_id"]').empty();
            if (response.findEmployee.emp_type_id == 1) {
              $('select[name="emp_type_id"]').append('<option value="1">Direct Manpower</option>');
              $("#emp_type_wise_show").addClass('d-block').removeClass('d-none');
              if (response.findEmployee.hourly_employee == 1) {
                $('#hourly_type_emp').attr('checked', true);
              } else {
                $('#hourly_type_emp').attr('checked', false);
              }

            } else {
              $('select[name="emp_type_id"]').append('<option value="2">Indirect Manpower</option>');
              $("#emp_type_wise_show").addClass('d-none').removeClass('d-block');
            }

            $.each(response.allEmpType, function(key, value) {
              $('select[name="emp_type_id"]').append('<option value="' + value.id + '">' + value.name + '</option>');
            });
          } else {
            $('select[[name="emp_type_id"]').append('<option>Data Not Found</option>');
          }


          // country
          if (response.allCountry != '') {
            $('select[name="country_id"]').empty();
            $('select[name="division_id"]').empty();
            $('select[name="district_id"]').empty();
            $("input[id='post_code']").val('');
            $("input[id='details']").val('');
            $("input[id='post_code']").val(response.findEmployee.post_code);
            $("input[id='details']").html(response.findEmployee.details);
            $('select[name="country_id"]').append('<option value="' + response.findEmployee.country_id + '">' + response.findEmployee.country_name + '</option>');
            $('select[name="division_id"]').append('<option value="' + response.findEmployee.division_id + '">' + response.findEmployee.division_name + '</option>');
            $('select[name="district_id"]').append('<option value="' + response.findEmployee.district_id + '">' + response.findEmployee.district_name + '</option>');
            $.each(response.allCountry, function(key, value) {
              $('select[name="country_id"]').append('<option value="' + value.id + '">' + value.country_name + '</option>');
            });
          } else {
            $('select[[name="country_id"]').append('<option>Data Not Found</option>');
          }
          // sponsor
          if (response.allSponsor != '') {
            $('select[name="sponsor_id"]').empty();
            $('select[name="sponsor_id"]').append('<option value="' + response.findEmployee.spons_id + '">' + response.findEmployee.spons_name + '</option>');
            $.each(response.allSponsor, function(key, value) {
              $('select[name="sponsor_id"]').append('<option value="' + value.spons_id + '">' + value.spons_name + '</option>');
            });
          } else {
            $('select[[name="sponsor_id"]').append('<option>Data Not Found</option>');
          }

          // project getAllProject
          if (response.getAllProject != '') {

            $('select[name="projectStatus"]').empty();
            $('select[name="projectStatus"]').append('<option value="' + response.findEmployee.proj_id + '">' + response.findEmployee.proj_name + '</option>');
            $.each(response.getAllProject, function(key, value) {
              $('select[name="projectStatus"]').append('<option value="' + value.proj_id + '">' + value.proj_name + '</option>');
            });
          } else {

            $('select[[name="projectStatus"]').append('<option>Data Not Found</option>');
          }
          // designation
          if (response.designation != '') {
            $('select[name="designation_id"]').empty();
            $('select[name="designation_id"]').append('<option value="' + response.findEmployee.designation_id + '">' + response.findEmployee.catg_name + '</option>');
            $.each(response.designation, function(key, value) {
              $('select[name="designation_id"]').append('<option value="' + value.catg_id + '">' + value.catg_name + '</option>');
            });
          } else {
            $('select[[name="designation_id"]').append('<option>Data Not Found</option>');
          }

          // employee status
          if (response.allEmployeeStatus != '') {

            $('select[name="EmpStatus_id"]').empty();
            $('select[name="EmpStatus_id"]').append('<option value="' + response.findEmployee.id + '">' + response.findEmployee.title + '</option>');
           // $.each(response.allEmployeeStatus, function(key, value) {
              $('select[name="EmpStatus_id"]').append('<option value="' + "1" + '">' + "Active" + '</option>');
                $('select[name="EmpStatus_id"]').append('<option value="' + "2" + '">' + "InActive" + '</option>');
                  $('select[name="EmpStatus_id"]').append('<option value="' + "3" + '">' + "Final_Exit" + '</option>');
                    $('select[name="EmpStatus_id"]').append('<option value="' + "4" + '">' + "Release" + '</option>');
                      $('select[name="EmpStatus_id"]').append('<option value="' + "5" + '">' + "Vacation" + '</option>');
                        $('select[name="EmpStatus_id"]').append('<option value="' + "6" + '">' + "Run_Away" + '</option>');
                         $('select[name="EmpStatus_id"]').append('<option value="' + "7" + '">' + "VISA_Cancel" + '</option>');
                          $('select[name="EmpStatus_id"]').append('<option value="' + "8" + '">' + "Final_Exit_and_Return_Back" + '</option>');


           // });

          } else {
            $('select[name="EmpStatus_id"]').append('<option value="">Data Not Found!</option>');
          }

            // show employee information in employee table
            if (response.findEmployee.profile_photo != null) {
            var img = response.findEmployee.profile_photo;

            var html = ` <img height="80" src="{{asset('${img}')}}" alt=""> `;

            $('#profile_img').html(html);

            var imgPath = '{{ asset("' + img + '") }}';
            $('#asloob_img').addClass('d-none').removeClass('d-block');
            $('#profile_img').addClass('d-block').removeClass('d-none');

          } else {
            $('#profile_img').addClass('d-none').removeClass('d-block');
            $('#asloob_img').addClass('d-block').removeClass('d-none');
          }

         // ====================================================================
        }

      });
    }


    // Make Employee Name Upper Case
    $('#show_employee_name').keyup(function() {
                this.value = this.value.toLocaleUpperCase();
    });

    $('#show_employee_passport_no').keyup(function() {
                this.value = this.value.toLocaleUpperCase();
    });
    // make email is lower case
    $('#show_employee_email').keyup(function() {
                this.value = this.value.toLocaleLowerCase();
    });


    function checkThisEmployeePassportNumber(){
        var passport_no = $('#show_employee_passport_no').val();
        checkingThisEmployeeEmpIdPassportIqamaAndEmail(passport_no,'passfort_no');
    }
    function checkThisEmployeeIqamaNumber(){
        var akama_no = $('#show_employee_akama_no').val();
        if(akama_no.length > 10 || akama_no.length < 10){
            showSweetAlertMessage('error','Invalid Iqama Number');
            return;
        }
         checkingThisEmployeeEmpIdPassportIqamaAndEmail(akama_no,'akama_no');
    }
    function checkThisEmployeeEmail(){
        var email = $('#email').val();
        if(email.length > 0){
            checkingThisEmployeeEmpIdPassportIqamaAndEmail(email,'email');
        }
    }

    function checkingThisEmployeeEmpIdPassportIqamaAndEmail(db_value, dbcolum_name) {
        $.ajax({
            type: "POST",
            url: "{{ route('checked-employee.id') }}",
            data: {
                value: db_value,
                dbcolum_name:dbcolum_name
            },
            dataType: "json",
            success: function (response) {
                if ( response.status == 200) {
                    if(dbcolum_name == "employee_id"){
                        $('span[id="checkUniqueId"]').removeClass('d-none').addClass('d-block');
                    }
                    else if(dbcolum_name == "passfort_no"){
                        $('span[id="uniquePassportErrorMsg"]').removeClass('d-none').addClass('d-block');
                    }
                    else if(dbcolum_name == "akama_no"){
                        $('span[id="uniqueIqamaErrorMsg"]').removeClass('d-none').addClass('d-block');
                    }
                    else if(dbcolum_name == "email"){
                        $('span[id="uniqueEmailErrorMsg"]').removeClass('d-none').addClass('d-block');
                    }

                } else {
                    if(dbcolum_name == "employee_id"){
                        $('span[id="checkUniqueId"]').removeClass('d-block').addClass('d-none');
                    }
                    else if(dbcolum_name == "passfort_no"){
                        $('span[id="uniquePassportErrorMsg"]').removeClass('d-block').addClass('d-none');
                    }
                    else if(dbcolum_name == "akama_no"){
                        $('span[id="uniqueIqamaErrorMsg"]').removeClass('d-block').addClass('d-none');
                    }
                    else if(dbcolum_name == "email"){
                        $('span[id="uniqueEmailErrorMsg"]').removeClass('d-block').addClass('d-none');
                    }

                }


            },
            error:function(response){
                alert('Data Checking Operation Failed, Please check Your Internet Connection');
            }
        });
    }

    // update employee salary payment method
    $(document).ready(function () {
        //   form submit
        $("#salary_payment_method_form").submit(function (e) {

                e.preventDefault();
                var form = $("#salary_payment_method_form");
                var data =  new FormData($(this)[0]);  // if same name two form then o index
                var action = form.attr("action");
                if(form.valid() == false){
                    return;
                }
                document.getElementById("submit_button").disabled = true;
                $.ajax({
                        url: action,
                        method: form.attr("method"),
                        data: data,
                        processData: false,
                        contentType: false,
                        beforeSend:function(){

                        },
                })
                .done(function(response) {
                        document.getElementById("submit_button").disabled = false;

                        if(response.status == 200){
                            $('#salary_payment_method_form')[0].reset();
                            $('#employee_salary_payment_method_modal').modal('hide');
                            showSweetAlertMessage('success',response.message);
                        }else {
                            showSweetAlertMessage('error', response.message);
                        }
                })
                .fail(function(xhr) {
                    showSweetAlertMessage('error',"Operation Failed, Please Try Aggain");
                    document.getElementById("submit_button").disabled = false;
                });
        });
    });

    $('#employee_salary_payment_method_modal').on('shown.bs.modal', function () {
        $('#employee_id').focus();
    })

</script>
@endsection
