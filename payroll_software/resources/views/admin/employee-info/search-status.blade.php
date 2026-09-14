@extends('layouts.admin-master')
@section('title') Emp Update @endsection
@section('content')

<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Employee Information Update</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active">Employee Update</li>
        </ol>
    </div>
</div>

<!-- Session Message Flash -->
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
            <strong>{{Session::get('error')}} </strong>
        </div>
        @endif
    </div>
    <div class="col-md-2"></div>
</div>


<!-- employee information searching with (id, passport, iqama) Start -->
<div class="row d-block">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body card_form" style="padding: 0;">
                <div
                    class="form-group row custom_form_group{{ $errors->has('searchBy') ? ' has-error' : '' }}">
                    <label class="col-md-2 control-label d-block" style="text-align: right; margin-right:5px;">Employee
                    </label>
                    <div class="col-md-4">
                        <select class="form-select" name="searchBy" id="searchBy" required>
                            <option value="employee_id">Searching By Employee ID</option>
                            <option value="akama_no">Searching By Iqama </option>
                            <option value="passfort_no">Searching By Passport</option>
                            <option value="employee_infos.email">Searching By Email</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <input type="text" placeholder="Enter ID/Iqama/Passport No" class="form-control"
                            id="empl_info" name="empl_info" value="{{ old('empl_info') }}"
                             required autofocus>
                        <span id="employee_not_found_error_show" class="d-none"
                            style="color: red"></span>
                        @if ($errors->has('empl_info'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('empl_info') }}</strong>
                        </span>
                        @endif
                    </div>
                    <div class="col-md-2">
                        <button type="submit" onclick="singleEmoloyeeDetails()" class="btn btn-primary waves-effect">SEARCH</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Employee Searching Data Deatils Show Start -->
<div class="row d-none" id="employee_searching_result_section">
    <!-- Employee Information update related modal buttons area Start -->
    <div class="row mt-0 mb-0" id="information_update_modal_button_area">
        <div class="col-md-12">
            <div class="btn-toolbar" role="toolbar" aria-label="Toolbar with button groups">

                <div class="btn-group" role="group" aria-label="Third group">
                    @can('add_remarks_on_employee_action')
                        <button type="button" data-toggle="modal" data-target="#employee_activity_remarks_modal" class="btn btn-primary waves-effect">Add Remarks</button>
                    @endcan
                    @can('add_remarks_on_employee_action')
                       &nbsp;&nbsp;&nbsp;&nbsp; <button type="button" data-toggle="modal" data-target="#employee_ajeer_file_modal" class="btn btn-primary waves-effect">AJEER Paper Upload</button>
                    @endcan


                </div>

            </div>
        </div>
    </div>
 <!-- Employee Information display section -->
    <div class="col-md-12">
        <table  class="table table-bordered table-striped table-hover custom_view_table show_employee_details_table">
            <tr>
                <td> <span class="emp">Employee ID:</span> <span id="show_employee_id"
                        class="emp2"></span> </td>
                        <td> <span class="emp">Status:</span> <span
                            id="show_employee_job_status" class="emp2" style="font-weight:bold;color:red;font-size:18px;"></span>
                            <span class="emp">, Trade:</span> <span
                            id="show_employee_category" class="emp2"></span>
                        </td>

            </tr>
            <tr>
                <td> <span class="emp">Name:</span> <span id="show_employee_name"
                        class="emp2"></span> </td>

                        <td> <span class="emp">Project:</span> <span
                            id="show_employee_project_name" class="emp2"></span>
                             <span class="emp">, Working: </span> <span
                            id="show_employee_working_shift" class="emp2"></span>

                            </td>
            </tr>


             <tr>
                <td>
                    <span id="show_employee_passport_file"></span>
                    <span class="emp">Passport No:</span> <span id="show_employee_passport_no" class="emp2"> </span>

                </td>
                <td>

                    <span id="show_employee_iqama_file" class="emp2"></span>
                    <span class="emp">Iqama No:</span> <span id="show_employee_akama_no" class="emp2"> </span>
                </td>
            </tr>
            <tr>
                <td> <span class="emp">Employee Type:</span> <span id="show_employee_type"
                        class="emp2"></span> </td>
                <td> <span class="emp">Agency:</span> <span id="show_employee_agency_name"  class="emp2"></span>
                    , &nbsp;  &nbsp;  &nbsp; <span class="emp">Ajeer File:</span>
                        <span id="show_employee_ajeer_file" class="emp2"></span>
                </td>
             </tr>

            <tr>
                <td> <span class="emp">Sponsor: </span> <span
                        id="show_employee_working_sponsor_name" class="emp2"></span> </td>

                <td>
                        <span class="emp">Abshar Mobile No:</span> <span id="show_employee_mobile_no"
                        class="emp2"></span>
                        <span class="emp">, &nbsp;Home Contact:</span> <span id="show_employee_phone_no"
                        class="emp2"></span>
                    </td>
            </tr>

            <tr>
                <td> <span class="emp">Accomodation:</span> <span
                    id="show_employee_accomodation_ofB_name" class="emp2"></span>
                </td>
                <td>
                    <span class="emp">Permanent Address:</span>
                    <span id="show_employee_address_Ds"></span>,
                    <span id="show_employee_address_D"></span> ,
                    <span id="show_employee_address_C" class="emp2"></span>
                </td>
            </tr>

        </table>
    </div>
</div>

<!--######################################################################
         Employee Information update related modal area Start
    ######################################################################
!-->

<div class="modal fade" id="employee_activity_remarks_modal" tabindex="-1" role="dialog" aria-labelledby="demoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Employee GOSI Number and Administration Activity Status Update </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="#" id="employee_activity_remarks_add_form" method="POST">
                    @csrf

                    <input type="hidden" name="emp_auto_id" value="" id="input_emp_auto_id" />

                    <div class="form-group custom_form_group{{ $errors->has('emp_act_remarks') ? ' has-error' : '' }}">
                        <label for="emp_act_remarks" class="control-label d-block text-left">Remarks:
                            {{-- <span class="req_star">*</span> --}}
                        </label>
                        <div>
                            <textarea class="form-control" id="emp_act_remarks" name="emp_act_remarks" placeholder="Add Employee Activity Remarks" >{{ old('emp_act_remarks') }}</textarea>
                            @if ($errors->has('emp_act_remarks'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('emp_act_remarks') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group custom_form_group{{ $errors->has('emp_act_remarks') ? ' has-error' : '' }}">
                        <label class="control-label d-block text-left">GOSI Number:
                            {{-- <span class="req_star">*</span> --}}
                        </label>
                        <div>
                            <input class="form-control" name='gosi_number' placeholder='Add Gosi Number' value="{{ old('gosi_number') }}"></input>
                        </div>
                    </div>
                    <div class="text-center" style="margin-top: 40px;">
                        <button id="emp_act_remarks_add_btn" class="btn btn-primary waves-effect" style="border-radius: 15px; width: 150px; height: 40px; letter-spacing: 1px;">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Upload employee ajeer file !-->
<div class="modal fade" id="employee_ajeer_file_modal" tabindex="-1" role="dialog" aria-labelledby="demoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                 <h5 class="card-title">Employee AJEER Document Update </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {{-- this is iqama upload routing , operation 1 = iqama update , 2 = blood group update , 3 = ajeer file update --}}
                <form method="post" id="emp_photo_bg_info_form" action="{{ route('search.employee.iqama.update') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="input_emp_auto_id" value="" id="input_emp_auto_id" />
                        <input type="hidden" name="operation_type" value="3" id="operation_type" />

                        <!-- Employee AJEER File -->
                        <div class="form-group row custom_form_group">
                            <label class="col-md-3 control-label text-right">AJEER File:</label>
                            <div class="col-md-9">
                                <div class="input-group">
                                    <span class="input-group-btn">
                                        <span class="btn btn-default btn-file btnu_browse">
                                            Browse… <input type="file" name="ajeer_file" id="imgInp2"  >
                                        </span>
                                    </span>
                                    <input type="text" class="form-control" readonly>
                                </div>
                            </div>
                        </div>
                    <div class="text-center" style="margin-top: 40px;">
                        @can('employee_iqama_no_update')
                            <button type="submit" style="margin: 2px"
                            class="btn btn-primary waves-effect">Update</button>
                        @endcan
                    </div>
                </form>
                   <div class="form-group row custom_form_group">
                                        <div class="col-md-10">
                                            <img id='img-upload2' width="200" />
                                        </div>
                    </div>
            </div>
        </div>
    </div>
</div>

<!-- employee information update form-->
<div class="row d-none" id="update_form_section">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body card_form">
                <!-- Employee Information Update Section And Employee Info Show Start -->
                <div class="col-md-12">
                        <!-- Employee Project And Iqama Info Update Start -->
                        <div class="row">
                            <!-- Employee Project Update -->
                            <div class="col-md-6" style="border-color: #B2BEB5; border-style: solid;">
                                <div class="text-center mt-3">
                                    <h5 class="card-title">Update Employee Working Project</h5>
                                </div>
                                <br>
                                <form method="post" action="{{ route('search.employee.project.update') }}">
                                    @csrf
                                    <div class="form-group row custom_form_group">
                                        <label class="col-sm-3 control-label">Project Assign Date :</label>
                                        <div class="col-sm-5">
                                            <input type="date" class="form-control" name="date"
                                                value="{{date('Y-m-d')}}" />
                                        </div>

                                    </div>

                                    <div class="form-group row custom_form_group">
                                        <label class="col-sm-3 control-label">Project Name :</label>
                                        <div class="col-sm-5">
                                            <input type="hidden" name="emp_auto_id" value="" id="input_emp_auto_id" />
                                            <select class="form-select" name="projectStatus" required>
                                                <option value="">Select Project Name</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-4">
                                            @can('single_employee_transfer')
                                                <button type="submit" style="margin-top: 2px" class="btn btn-primary waves-effect"> Update</button>
                                            @endcan
                                        </div>
                                    </div>

                                </form>
                            </div>

                            <!-- Update Iqama and passport Information -->
                            <div class="col-md-6" style="border-color: #B2BEB5; border-style: solid;">
                                <div class="text-center mt-3">
                                    <h5 class="card-title">Passport & Iqama Information Update </h5>
                                </div>
                                <br>
                                <form method="post" id="employee-info-form-for-update"
                                    action="{{ route('search.employee.iqama.update') }}" enctype="multipart/form-data">
                                    @csrf

                                    <input type="hidden" name="emp_auto_id" value="" id="input_emp_auto_id" />

                                    <div   class="form-group row custom_form_group{{ $errors->has('akama_no_up') ? ' has-error' : '' }}">
                                        <label class="col-sm-3 control-label">Iqama No:<span
                                                class="req_star">*</span></label>
                                        <div class="col-sm-7">
                                            <input type="number" placeholder="Input Iqama Number Here"
                                                class="form-control" name="akama_no_up" value=""  >
                                            @if ($errors->has('akama_no_up'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('akama_no_up') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div
                                        class="form-group row custom_form_group{{ $errors->has('akama_expire') ? ' has-error' : '' }}">
                                        <label class="col-sm-3 control-label">Iqama Expire Date:<span
                                                class="req_star">*</span></label>
                                        <div class="col-sm-7">
                                            <input type="date" class="form-control" name="akama_expire"
                                                value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}">
                                            @if ($errors->has('akama_expire'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('akama_expire') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group row custom_form_group">
                                        <label class="col-sm-3 control-label">Upload Iqama File:</label>
                                        <div class="col-sm-5">
                                            <div class="input-group">
                                                <span class="input-group-btn">
                                                    <span class="btn btn-default btn-file btnu_browse">
                                                        Browse… <input type="file" name="akama_photo" id="imgInp3">
                                                    </span>
                                                </span>
                                                <input type="text" class="form-control" readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <div   class="form-group row custom_form_group">
                                        <label class="col-sm-3 control-label">Passport No: </label>
                                        <div class="col-sm-7">
                                            <input type="text" placeholder="Input Passport Number"
                                                class="form-control" name="passport_no_up"   >
                                        </div>
                                    </div>

                                    <div class="form-group row custom_form_group">
                                        <label class="col-sm-3 control-label">Upload Passport File:</label>
                                        <div class="col-sm-5">
                                            <div class="input-group">
                                                <span class="input-group-btn">
                                                    <span class="btn btn-default btn-file btnu_browse">
                                                        Browse… <input type="file" name="passport_file" id="imgInp4">
                                                    </span>
                                                </span>
                                                <input type="text" class="form-control" readonly>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            @can('employee_iqama_no_update')
                                                <button type="submit" style="margin-top: 2px"
                                                class="btn btn-primary waves-effect">Update</button>
                                            @endcan
                                        </div>

                                    </div>

                                    <div class="col-sm-4">
                                        <img id='img-upload3' class="upload_image" />
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- Employee Trade/Designation And Sponsor Info Update Start -->
                        <div class="row">
                            <!--  Employee Trade/Designation Update -->
                            <div class="col-md-6" style="border-color: #B2BEB5; border-style: solid;">
                                <div class="text-center mt-3">
                                    <h5 class="card-title">Update Employee Trade/Designation </h5>
                                </div>

                                <form method="post" action="{{ route('search.employee.disignation.update') }}">
                                    @csrf
                                    <div class="form-group row custom_form_group">

                                        <label class="col-sm-4 control-label">Assign Trade:</label>
                                        <div class="col-sm-8">
                                            <input type="hidden" name="emp_auto_id" value="" id="input_emp_auto_id" />
                                            <select class="form-select" name="emplyoeeDesignation" >
                                                <option value="">Select Trade</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row custom_form_group">
                                        <label class="col-sm-4 control-label">Work Performance:</label>
                                        <div class="col-sm-8">
                                            <select class="form-select" name="empWorkActivityRating" required>
                                                <option value="">Select Rating</option>
                                                <!--@foreach ($empWorkRating as $rating)-->
                                                <!--    <option value="{{ $rating->value }}">{{ $rating->name }}</option>-->
                                                <!--@endforeach-->
                                                <option value="0">Undefine</option>
                                                  <option value="5">Low</option>
                                                    <option value="10">Medium</option>
                                                      <option value="15">Good</option>
                                                        <option value="20">Best</option>
                                            </select>
                                        </div>

                                    </div>


                                    <div class="form-group row custom_form_group">
                                        <label class="col-sm-4 control-label">Multi Expert Trade:</label>
                                        <div class="col-sm-8">
                                            <select class="selectpicker" name="empMultiExptDesignation[]" multiple>
                                                @foreach($designationList as $designation)
                                                    <option value="{{ $designation->catg_id }}">{{ $designation->catg_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>


                                    <div class="card-footer card_footer_button text-center">
                                        <button type="submit" class="btn btn-primary waves-effect">Update Trade</button>
                                    </div>

                                </form>
                            </div>

                            <!-- Employee Sponsor Update -->
                            <div class="col-md-6" style="border-color: #B2BEB5; border-style: solid;">
                                <div class="text-center mt-3">
                                    <h5 class="card-title">Update Employee Sponsor Informations</h5>
                                </div>
                                <br>
                                <form method="post" action="{{ route('employee.sponsor.info.update') }}">
                                    @csrf

                                    <div class="form-group row custom_form_group">
                                        <label class="col-sm-4 control-label">Sponsor Name :</label>
                                        <div class="col-sm-8">
                                            <input type="hidden" name="emp_auto_id" value="" id="input_emp_auto_id" />
                                            <input type="hidden" name="emp_prev_sponsor_id" value="" id="input_emp_sponsor_id" />
                                            <select class="form-select" name="emp_current_sponsor" required>
                                                <option value="">Sponsor</option>
                                            </select>
                                        </div>
                                    </div>
                                    <br><br>
                                    <div class="card-footer card_footer_button text-center">
                                        @can('employee_sponsor_update')
                                        <button type="submit" class="btn btn-primary waves-effect">Update Sponsor</button>
                                        @endcan
                                    </div>

                                </form>
                            </div>
                        </div>
                        <!-- Employee Accommodation Update Start -->
                        <div class="row">
                            <!--  Employee Accommodation Update -->
                            <div class="col-md-6" style="border-color: #B2BEB5; border-style: solid;">
                                <div class="text-center mt-3">
                                    <h5 class="card-title">Update Employee Accommodation Info </h5>
                                </div>
                                <br>
                                <form method="post" action="{{ route('search.employee.accomodation-info.update') }}">
                                    @csrf

                                    <input type="hidden" name="emp_auto_id" value="" id="input_emp_auto_id" />

                                     <div class="form-group row custom_form_group">
                                        <label class="col-sm-4 control-label">Email:</label>
                                        <div class="col-sm-7">
                                            <input type="email" placeholder="Input Email Address" class="form-control" name="email"
                                                >
                                        </div>
                                    </div>

                                    <div  class="form-group row custom_form_group{{ $errors->has('mobile_no_up') ? ' has-error' : '' }}">
                                        <label class="col-sm-4 control-label">Abshar Mobile No:<span
                                                class="req_star">*</span></label>
                                        <div class="col-sm-7">
                                            <input type="number" placeholder="Input Phone Number Here"
                                                class="form-control" name="mobile_no_up" value="" >
                                            @if ($errors->has('mobile_no_up'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('mobile_no_up') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>

                                      <div
                                        class="form-group row custom_form_group{{ $errors->has('phone_no_up') ? ' has-error' : '' }}">
                                        <label class="col-sm-4 control-label">Mobile Number2:</label>
                                        <div class="col-sm-7">
                                            <input type="number" placeholder=""
                                                class="form-control" name="phone_no_up" value="Input Another Mobile Number" >
                                            @if ($errors->has('phone_no_up'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('phone_no_up') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group row custom_form_group">
                                        <label class="col-sm-4 control-label">Contact No(Home):</label>
                                        <div class="col-sm-7">
                                            <input type="number" placeholder="Input Home Country Mobile Number" class="form-control" name="country_phone_no"
                                                value="{{ old('country_phone_no') }}">
                                        </div>
                                    </div>

                                    <div class="form-group row custom_form_group">

                                        <label class="col-sm-3 control-label">Villa Name:</label>
                                        <div class="col-sm-5">
                                            <input type="hidden" name="emp_auto_id" value="" id="input_emp_auto_id" />
                                            <select class="form-select" name="emplyoeeAccommodationBuiling" >
                                                <option value="">Select Villa Name</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-4">
                                            @can('employee_accommodation_update')
                                            <button type="submit" style="margin-top: 2px"    class="btn btn-primary waves-effect">Update</button>
                                             @endcan
                                        </div>
                                    </div>

                                </form>
                            </div>

                            <!--  Employee Agency Information Update -->
                            <div class="col-md-6" style="border-color: #B2BEB5; border-style: solid;">

                                 <div class="text-center mt-3">
                                    <h5 class="card-title">Update Employee Reference Information</h5>
                                </div>

                                <form method="post" action="{{ route('search.employee.agency.emp-agency-update') }}">
                                    @csrf
                                    <div class="form-group row custom_form_group">
                                        <label class="col-sm-3 control-label">Agency Name:</label>
                                        <div class="col-sm-5">
                                            <input type="hidden" name="emp_auto_id" value="" id="input_emp_auto_id" />
                                            <select class="form-select" name="emplyoeeAgency" >
                                                <option value="">Select Agency</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row custom_form_group">
                                        <label class="col-sm-3 control-label">Reference Person Name</label>
                                        <div class="col-sm-5">
                                            <input type="text" placeholder="Input Reference Name or Employee ID" class="form-control" name="ref_employee_id"  value="{{ old('ref_employee_id') }}">
                                        </div>
                                    </div>
                                    <div class="form-group row custom_form_group">
                                        <label class="col-sm-3 control-label">Ref. Contact</label>
                                        <div class="col-sm-5">
                                            <input type="text" placeholder="Input Reference Contact Number Here" class="form-control" name="ref_contact_no" value="{{ old('ref_contact_no') }}">
                                        </div>
                                    </div>

                                    <div class="form-group row custom_form_group">
                                        <label class="col-sm-3 control-label">Remarks</label>
                                        <div class="col-sm-5">
                                            <input type="text" placeholder="Input Remarks Here" class="form-control" name="remarks" value="{{ old('remarks') }}">
                                        </div>

                                        <div class="col-sm-4">
                                            @can('employee_agency_update')
                                               <button type="submit" style="margin-top: 2px"
                                               class="btn btn-primary waves-effect">Update</button>
                                             @endcan
                                       </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="row">

                            <!-- Update Photo and Blood Group Information -->
                            <div class="col-md-6" style="border-color: #B2BEB5; border-style: solid;">
                                <div class="text-center mt-3">
                                    <h5 class="card-title">Update Photo & Blood Group </h5>
                                </div>
                                <br>
                                <form method="post" id="emp_photo_bg_info_form"
                                    action="{{ route('search.employee.iqama.update') }}" enctype="multipart/form-data">
                                    @csrf

                                    <input type="hidden" name="input_emp_auto_id" value="" id="input_emp_auto_id" />
                                    <input type="hidden" name="operation_type" value="2" id="operation_type" />

                                    <!-- Blood Group -->
                                    <div class="form-group row custom_form_group">
                                        <label class="col-sm-3 control-label">Blood Group:</label>
                                        <div class="col-sm-4">
                                            <select class="form-select" name="blood_group">
                                                <option value="A+">A+</option>
                                                <option value="A-">A-</option>
                                                <option value="B+">B+</option>
                                                <option value="B-">B-</option>
                                                <option value="O+">O+</option>
                                                <option value="O-">O-</option>
                                                <option value="AB+">AB+</option>
                                                <option value="AB-">AB-</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row custom_form_group">
                                        <label class="col-sm-3 control-label">Blood Group Paper:</label>
                                        <div class="col-sm-4">
                                            <div class="input-group">
                                                <span class="input-group-btn">
                                                    <span class="btn btn-default btn-file btnu_browse">
                                                        Browse… <input type="file" name="blood_group_paper" id="imgInp7">
                                                    </span>
                                                </span>
                                                <input type="text" class="form-control" readonly>
                                            </div>
                                        </div>
                                        <div class="col-sm-5">
                                            <img id='img-upload7' class="upload_image" />
                                        </div>
                                    </div>

                                    <!-- Employee photo -->
                                    <div class="form-group row custom_form_group">
                                        <label class="col-sm-3 control-label">Employee photo:</label>
                                        <div class="col-sm-4">
                                            <div class="input-group">
                                                <span class="input-group-btn">
                                                    <span class="btn btn-default btn-file btnu_browse">
                                                        Browse… <input type="file" name="profile_photo" id="imgInp5" accept="image/x-png,image/jpg,image/jpeg">
                                                    </span>
                                                </span>
                                                <input type="text" class="form-control" readonly>
                                            </div>

                                        </div>
                                        <div class="col-md-5">
                                            <img id='img-upload5' width="200" />
                                        </div>
                                    </div>
                                   <div class="form-group row custom_form_group">
                                        <div class="col-md-4">
                                        </div>
                                        <div class="col-sm-4">
                                                @can('employee_iqama_no_update')
                                                    <button type="submit" style="margin-top: 2px"
                                                    class="btn btn-primary waves-effect">Update</button>
                                                @endcan
                                        </div>
                                   </div>
                                </form>
                            </div>

                            <div class="col-md-6" style="border-color: #B2BEB5; border-style: solid;">
                                <div class="text-center mt-3">
                                    <h5 class="card-title">Employee AJEER Document Update </h5>
                                </div>
                                <br>
                                <form method="post" id="emp_photo_bg_info_form"
                                    action="{{ route('search.employee.iqama.update') }}" enctype="multipart/form-data">
                                    @csrf

                                    <input type="hidden" name="input_emp_auto_id" value="" id="input_emp_auto_id" />
                                    <input type="hidden" name="operation_type" value="3" id="operation_type" />

                                    <!-- Employee photo -->
                                    <div class="form-group row custom_form_group">
                                        <label class="col-sm-4 control-label">AJEER File:</label>
                                        <div class="col-sm-4">
                                            <div class="input-group">
                                                <span class="input-group-btn">
                                                    <span class="btn btn-default btn-file btnu_browse">
                                                        Browse… <input type="file" name="profile_photo" id="imgInp2" accept="image/x-png,image/jpg,image/jpeg">
                                                    </span>
                                                </span>
                                                <input type="text" class="form-control" readonly>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <img id='img-upload2' width="200" />
                                        </div>
                                    </div>
                                   <div class="form-group row custom_form_group">
                                        <div class="col-md-4">
                                        </div>
                                        <div class="col-sm-4">
                                                @can('employee_iqama_no_update')
                                                    <button type="submit" style="margin-top: 2px"
                                                    class="btn btn-primary waves-effect">Update</button>
                                                @endcan
                                        </div>
                                   </div>
                                    <div class="form-group row custom_form_group">
                                        <div class="col-sm-10">
                                            <img id='img-upload2' width="200" />
                                        </div>
                                   </div>
                                </form>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    // Method For Reset All Loaded Data
    function resetEmpInfo() {
        $("#show_employee_address_C").html('');
        $("#show_employee_address_D").html('');
        $("#show_employee_address_Ds").html('');
        $("#show_employee_address_details").html('');
        $("#show_employee_agency_name").html('');
        $("#show_employee_passport_no").html('');
        $("#show_employee_passport_file").html('');
        $("#show_employee_mobile_no").html('');
        $("#show_employee_phone_no").html('');
        $("#show_employee_name").html('');
        $("#show_employee_category").html('');
        $("#show_employee_id").html('');
        $("#show_employee_job_status").html('');
        $("#show_employee_akama_no").html('');
        $("#show_employee_iqama_file").html('');
        $("#show_employee_type").html('');
        $("#show_employee_project_name").html('');
        $("#show_employee_working_shift").html('');
        $("#show_employee_accomodation_ofB_name").html('');
        $("#show_employee_working_sponsor_name").html('');
        $("#show_employee_ajeer_file").html('');
    }
    $('#empl_info').keydown(function (e) {
        if (e.keyCode == 13) {

            $("#employee_searching_result_section").removeClass("d-block").addClass("d-none");
            $("#showMultiple_EmployeeDetails").removeClass("d-block").addClass("d-none"); // hide multiple employee list
            $("#update_form_section").removeClass("d-block").addClass("d-none");
            singleEmoloyeeDetails();
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

    //   Single Employee Details Info
    function singleEmoloyeeDetails() {

        resetEmpInfo() // reset All Employe Info
        var searchType = $('#searchBy').find(":selected").val();
        var searchValue = $("#empl_info").val();
            if ($("#empl_info").val().length === 0) {
                showSweetAlertMessage('error',"Please Enter Employee ID/Iqama/Passport Number");
                return
            }
            $.ajax({
                type: 'POST',
                url: "{{ route('active.employee.searching.searching-with-multitype.parameter') }}",
                data: {
                    search_by: searchType,
                    employee_searching_value: searchValue
                },
                dataType: 'json',
                success: function (response) {

                     if (response.status != 200) {
                        $('input[id="emp_auto_id"]').val(null);
                        $("span[id='employee_not_found_error_show']").text('Please Enter An Active Employee Id');
                        $("span[id='employee_not_found_error_show']").addClass('d-block').removeClass('d-none');
                        $("#employee_searching_result_section").removeClass("d-block").addClass("d-none");
                        $("#showMultiple_EmployeeDetails").removeClass("d-block").addClass("d-none"); // hide multiple employee list
                        $("#update_form_section").removeClass("d-block").addClass("d-none");
                        return ;
                    }

                    if (response.findEmployee.length > 1) {
                        // showSearchingResultAsMultipleRecords(response.findEmployee);
                        showSweetAlertMessage("error","Critical Error Found, Please Contact with Software Support");
                    } else {
                        showSearchingEmployee(response.findEmployee[0], response.empOfficeBuilding, response.getAllProject, response.allEmployeeStatus, response.designation, response.agencies, response.sponsors);
                    }
                }, // end of success
                error:function(response){
                    showSweetAlertMessage('error',"Operation Failed, Please try Again");
                }
            }); // end of ajax calling

    }

    // End of Method for Router calling
    function showSearchingEmployee(findEmployee, empOfficeBuilding, getAllProject, allEmployeeStatus, designation, agencies, sponsors) {
        /* show employee information in employee table */
        $("#showMultiple_EmployeeDetails").removeClass("d-block").addClass("d-none"); // hide multiple employee list
        $("span[id='employee_not_found_error_show']").removeClass('d-block').addClass('d-none');
        $("#employee_searching_result_section").removeClass('d-none').addClass("d-block"); // show signle employee details
        $("#update_form_section").removeClass('d-none').addClass("d-block"); // show update form

        // Hidden feild employee id, emp auto id, current sponsor id
        $('input[id="input_employee_id"]').val(findEmployee.employee_id);
        $('input[id="input_emp_auto_id"]').val(findEmployee.emp_auto_id);
        $('input[id="input_emp_sponsor_id"]').val(findEmployee.sponsor_id);

         $("span[id='show_employee_address_C']").text((findEmployee.country_name +', Email: '+findEmployee.email));
        $("span[id='show_employee_address_D']").text(findEmployee.division_name);
        $("span[id='show_employee_address_Ds']").text(findEmployee.district_name);
        $("span[id='show_employee_address_details']").text(findEmployee.details);
        $("span[id='show_employee_agency_name']").text(findEmployee.agc_title+", GOSI: "+ (findEmployee.gosi_number == null ? "Not Assigned" : findEmployee.gosi_number));

        $("span[id='show_employee_passport_no']").text(findEmployee.passfort_no);
        $("span[id='show_employee_mobile_no']").text(findEmployee.mobile_no+", Mobile No: "+ (findEmployee.phone_no != null ? findEmployee.phone_no :""));
        $("span[id='show_employee_phone_no']").text(findEmployee.country_phone_no);
        $("span[id='show_employee_akama_no']").text(findEmployee.akama_no);
        $("span[id='show_employee_name']").text(findEmployee.employee_name);
        $("span[id='show_employee_category']").text(findEmployee.catg_name);
        $("span[id='show_employee_id']").text(findEmployee.employee_id);


        var job_status = (findEmployee.job_status > 0 ? findEmployee.title : "Waiting for Approval") + (findEmployee.salary_status == 1 ? ', Salary: Active' : ", Salary: Hold");
        job_status += ", "+ (findEmployee.emp_act_remarks != null ? findEmployee.emp_act_remarks : "");

        $("span[id='show_employee_job_status']").text(job_status);


        $("span[id='show_employee_accomodation_ofB_name']").text(findEmployee.ofb_name);
        $("span[id='show_employee_working_sponsor_name']").text(findEmployee.spons_name);

         var passport = `
                        <span class="emp2" style="text-align: center; text-transform: capitalize;" colspan="3"><a target="_blank" href=  "`+ getAttachmentUrl(findEmployee.pasfort_photo) + ` "><i class="fas fa-eye fa-lg view_icon"></i></a></span>
                    `
        var iqama =  `<span class="emp2" style="text-align: center; text-transform: capitalize;" colspan="3"><a target="_blank" href="`+ getAttachmentUrl(findEmployee.akama_photo) + ` " ><i class="fas fa-eye fa-lg view_icon"></i></a></span>
                    `
        findEmployee.pasfort_photo == null ?  $("#show_employee_passport_file").append('') :  $("#show_employee_passport_file").append(passport);
        findEmployee.akama_photo == null ?  $("#show_employee_iqama_file").append('') :  $("#show_employee_iqama_file").append(iqama);



       var ajeer =  `<span class="emp2" style="text-align: center; text-transform: capitalize;" colspan="3"><a target="_blank" href="` + getAttachmentUrl(findEmployee.ajeer_file) + ` " ><i class="fas fa-eye fa-lg view_icon"></i></a></span>`
       findEmployee.ajeer_file == null ?  $("#show_employee_ajeer_file").append('-') :  $("#show_employee_ajeer_file").append(ajeer);

        /* Employee Type  */

        // if (findEmployee.emp_type_id == 1) {
        //     $("span[id='show_employee_type']").text(findEmployee.emp_type_id+"-Direct Manpower");
        // } else {
        //     $("span[id='show_employee_type']").text(findEmployee.emp_type_id+"-Indirect Manpower");
        // }
         $("span[id='show_employee_type']").text(" ");

        if (findEmployee.emp_type_id == null) {
            $("span[id='show_employee_type']").text("-Not Set");
        }
        else if (findEmployee.emp_type_id == 1) {
            $("span[id='show_employee_type']").text(findEmployee.emp_type_id+"- Direct Manpower");
        } else {
            $("span[id='show_employee_type']").text(findEmployee.emp_type_id+"- Indirect Manpower");
        }



        /* show project name */
        if (findEmployee.project_id == null) {
            $("span[id='show_employee_project_name']").text("No Assigned Project!");
        } else {
            $("span[id='show_employee_project_name']").text(findEmployee.proj_name);
        }

        /* Employee Work Shift Status  */
         findEmployee.isNightShift == 0 ? $("span[id='show_employee_working_shift']").text("Day Shift"): $("span[id='show_employee_working_shift']").text("Night Shift")

        // Employee Accomodation Building Name, Id for dropdown List
        if (empOfficeBuilding != '') {
            $('select[name="emplyoeeAccommodationBuiling"]').empty();
            $('select[name="emplyoeeAccommodationBuiling"]').append('<option value="">Please Select Building Name</option>');
            $.each(empOfficeBuilding, function (key, value) {
                $('select[name="emplyoeeAccommodationBuiling"]').append('<option value="' + value.ofb_id + '">' + value.ofb_name + ', ' + value.ofb_city_name + '</option>');
            });

        } else {
            $('select[name="emplyoeeAccommodationBuiling"]').append('<option value="">Data Not Found!</option>');
        }

        // Employee Project Name, Id for dropdown List
        if (getAllProject != '') {
            $('select[name="projectStatus"]').empty();
            $('select[name="projectStatus"]').append('<option value="">Please Select Project</option>');
            $.each(getAllProject, function (key, value) {
                $('select[name="projectStatus"]').append('<option value="' + value.proj_id + '">' + value.proj_name + '</option>');
            });
        } else {
            $('select[[name="projectStatus"]').append('<option>Data Not Found</option>');
        }

        // Employee Status Name, Id for dropdown List
        if (allEmployeeStatus != '') {
            $('select[name="empStatus"]').empty();
            $('select[name="empStatus"]').append('<option value="">Please Select Emp Status</option>');
            $.each(allEmployeeStatus, function (key, value) {
                $('select[name="empStatus"]').append('<option value="' + value.id + '">' + value.title + '</option>');
            });

        } else {
            $('select[name="empStatus"]').append('<option value="">Data Not Found!</option>');
        }

        // Employee Designation Name, Id for dropdown List
        if (designation != '') {
            $('select[name="emplyoeeDesignation"]').empty();
            $('select[name="emplyoeeDesignation"]').append('<option value="">Please Select Designation</option>');
            $.each(designation, function (key, value) {
                $('select[name="emplyoeeDesignation"]').append('<option value="' + value.catg_id + '">' + value.catg_name + '</option>');

            });
        } else {
            $('select[name="emplyoeeDesignation"]').append('<option value="">Data Not Found!</option>');
        }

        // Employee Sponsor Name, Id for dropdown List
        if ( sponsors != '') {
            $('select[name="emp_current_sponsor"]').empty();
            $('select[name="emp_current_sponsor"]').append('<option value="">Please Select Sponsor</option>');
            $.each(sponsors, function (key, value) {
                $('select[name="emp_current_sponsor"]').append('<option value="' + value.spons_id + '">' + value.spons_name + '</option>');

            });
        } else {
            $('select[name="emp_current_sponsor"]').append('<option value="">Data Not Found!</option>');
        }


        // Employee Agencies Name, Id for dropdown List
        if (agencies != '') {
            $('select[name="emplyoeeAgency"]').empty();
            $('select[name="emplyoeeAgency"]').append('<option value="">Please Select Agency</option>');
            $.each(agencies, function (key, value) {
                $('select[name="emplyoeeAgency"]').append('<option value="' + value.agc_info_auto_id + '">' + value.agc_title + '</option>');
            });

        } else {
            $('select[name="emplyoeeAgency"]').append('<option value="">Data Not Found!</option>');
        }

    }


    function getAttachmentUrl(path)
    {
        return "{{ config('app.aws_s3_bucket_url') }}" + path;;
    }

    // Form Validation For Iqama Info Update
    $(document).ready(function () {


    });
</script>

<!-- added this for Multiple Selection dropdownlist  -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta2/css/bootstrap-select.min.css" integrity="sha512-mR/b5Y7FRsKqrYZou7uysnOdCIJib/7r5QeJMFvLNHNhtye3xJp1TdJVPLtetkukFn227nKpXD9OjUc09lx97Q==" crossorigin="anonymous"/>
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta2/js/bootstrap-select.min.js" integrity="sha512-FHZVRMUW9FsXobt+ONiix6Z0tIkxvQfxtCSirkKc5Sb4TKHmqq1dZa8DphF0XqKb3ldLu/wgMa8mT6uXiLlRlw==" crossorigin="anonymous"></script>

@endsection
