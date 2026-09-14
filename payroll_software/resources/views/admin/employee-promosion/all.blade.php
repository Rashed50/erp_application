@extends('layouts.admin-master')
@section('title')Increment @endsection
@section('content')

<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Employee Promotion</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>

        </ol>
    </div>
</div>

<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-7">
        @if(Session::has('success'))
        <div class="alert alert-success alertsuccess" role="alert" style="margin-left: -20px">
            <strong> {{Session::get('success')}} </strong>
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

 <!-- Employee Searching and Insert Form !-->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="row"  style="padding: 0;">
                <div class="card-body card_form" >
                            <div class="row form-group custom_form_group{{ $errors->has('searchBy') ? ' has-error' : '' }}">
                                 <div class="col-md-2">
                                <button type="button" class="btn btn-primary waves-effect" id="bulkImportUI">
                                    Excel Upload </button>
                                </div>
                                <div class="col-md-3">
                                    <input type="text" placeholder="Enter Employee ID or Iqama Number" class="form-control"
                                        id="empl_info" name="empl_info" value="{{ old('empl_info') }}"  required autofocus>
                                    <span id="employee_not_found_error_show" class="d-none"
                                        style="color: red"></span>
                                    @if ($errors->has('empl_info'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('empl_info') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="col-md-1">
                                    <button type="submit" onclick="singleEmoloyeeDetails()" class="btn btn-primary waves-effect">Search</button>
                                </div>
                                <div class="col-md-6">
                                    <div class="btn-toolbar" role="toolbar" aria-label="Toolbar with button groups">

                                        <div class="btn-group" role="group" aria-label="Third group">
                                            <button type="submit" onclick="openPaperUploadSection()" class="btn btn-primary waves-effect">Paper Upload</button>
                                        </div>
                                        <div class="btn-group mr-2" role="group" aria-label="First group">
                                            <?php
                                                $today_date = \Carbon\Carbon::now();

                                                $cu_month = $today_date->format('m');
                                                $cu_year = $today_date->format('Y') ;
                                                $cu_mon_name =  $today_date->format('M');

                                                $pre1_month = $today_date->subMonth()->format('m');
                                                $pre1_year = $today_date->format('Y') ;
                                                $pre1_mon_name =  $today_date->format('M');

                                                $pre2_month = $today_date->subMonth()->format('m');
                                                $pre2_year = $today_date->format('Y') ;
                                                $pre2_mon_name =  $today_date->format('M');

                                                // $pre3_month = $today_date->subMonth()->format('m');
                                                // $pre3_year = $today_date->format('Y');
                                                // $pre3_mon_name =  $today_date->format('M');
                                            ?>
                                            <div class="btn-group" role="group" aria-label="Basic example">
                                                @can('emp_promotion_record_approval')

                                                    <button type="button" class="btn btn-secondary" value="1"  onclick="searchPromotedEmployeesForApproval( {{$cu_month}},{{ $cu_year}} )" > {{ $cu_mon_name }}-{{ $cu_year }} </button>
                                                    <button type="button" class="btn btn-secondary" value="2"  onclick="searchPromotedEmployeesForApproval( {{$pre1_month}},{{ $pre1_year}} )" >  {{ $pre1_mon_name }}-{{ $pre1_year }} </button>
                                                    <button type="button" class="btn btn-secondary" value="3"  onclick="searchPromotedEmployeesForApproval( {{$pre2_month}},{{ $pre2_year}})" >  {{ $pre2_mon_name }}-{{ $pre2_year }} </button>
                                                    {{-- <button type="button" class="btn btn-secondary" value="4"  onclick="searchPromotedEmployeesForApproval( {{$pre3_month}},{{ $pre3_year}} )" > {{ $pre3_mon_name }}-{{ $pre3_year }} </button> --}}
                                                    <button type="button"  class="btn btn-secondary" data-toggle="modal" data-target="#promotion_approval_modal" >Custom</button>

                                                @endcan

                                            </div>
                                        </div>

                                        <div class="btn-group mr-2" role="group" aria-label="Second group">
                                            <button type="button"  data-toggle="modal" data-target="#emp_promotion_report_modal" class="btn btn-primary waves-effect">Report</button>
                                        </div>

                                    </div>
                                </div>
                            </div>


                </div>
            </div>
            <div class="col-md-12 d-none" style="padding-top: 0;" id="showEmployeeAdvanceApplyForm"  >
                        <!--  Promosion form -->
                    <div class="row">
                        <div class="col-md-1"></div>
                        <div class="col-md-10">
                            <form class="form-horizontal" id="emp-promotion-apply"
                                action="{{ route('employee-promosion.submit') }}" method="post"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="card">
                                    <h4 style="text-align: center">Employee Promotion Information</h4>

                                    <div class="card-body row card_form" style="padding-top: 0;">

                                        <input type="hidden" class="inputBasicSalaryAmount" id="input_basic_amount">
                                        <input type="hidden" class="inputMobileAllowanceAmount"
                                            id="input_mobile_allowance">
                                        <input type="hidden" class="inputHourlyRateAmount" id="input_hourly_rate">
                                        <input type="hidden" class="inputHouseRentAmount" id="input_house_rate">
                                        <input type="hidden" class="inputMedicalAllowanceAmount"
                                            id="input_medical_allowance">
                                        <input type="hidden" class="inputLocalTravelAllowanceAmount"
                                            id="input_local_travel_allowance">
                                        <input type="hidden" class="inputConveyanceAllowanceAmount"
                                            id="input_conveyance_allowance">
                                        <input type="hidden" class="inputFoodAllowanceAmount"
                                            id="input_food_allowance">
                                        <input type="hidden" class="inputOthersAmount" id="input_others1">


                                        <input type="hidden" id="input_emp_auto_id" name="emp_id" value=""> {{--
                                        employee table > emp_auto_id --}}
                                        <input type="hidden" id="input_emp_designation" name="designation_id"
                                            value="">
                                        <input type="hidden" class="totalPromotionAmount" name="total_amount">

                                        <div class="row">
                                            <div class="col-md-6">

                                                <div
                                                    class="form-group row custom_form_group{{ $errors->has('promotion_by') ? ' has-error' : '' }}">
                                                    <label class="col-sm-4 control-label d-block" >Reference By:<span
                                                            class="req_star">*</span></label>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control" name="promotion_by"
                                                            value=""
                                                            placeholder="Enter reference name for this promotion">
                                                        @if ($errors->has('promotion_by'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('promotion_by') }}</strong>
                                                        </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="form-group row custom_form_group">
                                                    <label class="col-sm-4 control-label d-block">
                                                        Designation:</label>
                                                    <div class="col-sm-8">
                                                        <select class="form-control" name="emplyoeeDesignation">
                                                            <option value="">Select Status</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div
                                                    class="form-group row custom_form_group{{ $errors->has('basic_amount') ? ' has-error' : '' }}">
                                                    <label class="col-sm-4 control-label d-block">Basic Salary:<span class="req_star">*</span></label>
                                                    <div class="col-sm-8">
                                                        <input type="number" class="form-control"
                                                            id="basic_amount" name="basic_amount" min="0"
                                                            onkeyup="calculateHourlyRate()" required>
                                                        @if ($errors->has('basic_amount'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('basic_amount') }}</strong>
                                                        </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div
                                                    class="form-group row custom_form_group{{ $errors->has('mobile_allowance') ? ' has-error' : '' }}">
                                                    <label class="col-sm-4 control-label d-block">Mobile
                                                        Allow.:<span class="req_star">*</span></label>
                                                    <div class="col-sm-8">
                                                        <input type="number" class="form-control"
                                                            id="input_mobile_allowance" name="mobile_allowance"
                                                            value="" min="0"  required>
                                                        @if ($errors->has('mobile_allowance'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('mobile_allowance')
                                                                }}</strong>
                                                        </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div
                                                    class="form-group row custom_form_group{{ $errors->has('hourly_rent') ? ' has-error' : '' }}">
                                                    <label class="col-sm-4 control-label d-block">Hourly
                                                        Rate:<span class="req_star">*</span></label>
                                                    <div class="col-sm-8">
                                                        <input type="number" class="form-control"
                                                            id="hourly_rate" name="hourly_rent" value="" min="1" required>
                                                        @if ($errors->has('hourly_rent'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('hourly_rent') }}</strong>
                                                        </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div
                                                    class="form-group row custom_form_group{{ $errors->has('house_rent') ? ' has-error' : '' }}">
                                                    <label class="col-sm-4 control-label d-block">House
                                                        Rent:<span class="req_star">*</span></label>
                                                    <div class="col-sm-8">
                                                        <input type="number" class="form-control"
                                                            id="input_house_rate" name="house_rent" value="" min="0" required>
                                                        @if ($errors->has('house_rent'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('house_rent') }}</strong>
                                                        </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="form-group row custom_form_group">
                                                    <label class="col-sm-4 control-label d-block">Approved Papers:</label>
                                                    <div class="col-sm-8">
                                                        <div class="input-group">
                                                            <span class="input-group-btn ">
                                                                <span class="btn btn-default btn-file btnu_browse ">
                                                                    Browse… <input type="file"
                                                                        name="prom_approve_documents" id="imgInp">
                                                                </span>
                                                            </span>
                                                            <input type="text" class="form-control" readonly>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <img id='img-upload' class="upload_image" />
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="col-md-6">

                                                <div
                                                    class="form-group row custom_form_group{{ $errors->has('prom_date') ? ' has-error' : '' }}">
                                                    <label class="col-sm-4 control-label  d-block">Promoted Date:<span
                                                            class="req_star">*</span></label>
                                                    <div class="col-sm-8">
                                                        <input type="date" name="prom_date" class="form-control"  >
                                                        @if ($errors->has('prom_date'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('basic_amount') }}</strong>
                                                        </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div
                                                    class="form-group row custom_form_group{{ $errors->has('medical_allowance') ? ' has-error' : '' }}">
                                                    <label class="col-sm-4 control-label d-block">Medical
                                                        Allow.:<span class="req_star">*</span></label>
                                                    <div class="col-sm-8">
                                                        <input type="number" class="form-control"
                                                            id="input_medical_allowance" name="medical_allowance"
                                                            value="" min="0"  required>
                                                        @if ($errors->has('medical_allowance'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('medical_allowance')
                                                                }}</strong>
                                                        </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div
                                                    class="form-group row custom_form_group{{ $errors->has('local_travel_allowance') ? ' has-error' : '' }}">
                                                    <label class="col-sm-4 control-label d-block">Travel Allow.:<span class="req_star">*</span></label>
                                                    <div class="col-sm-8">
                                                        <input type="number" class="form-control"
                                                            id="input_local_travel_allowance"
                                                            name="local_travel_allowance" value="" min="0"  required>
                                                        @if ($errors->has('local_travel_allowance'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('local_travel_allowance')
                                                                }}</strong>
                                                        </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div
                                                    class="form-group row custom_form_group{{ $errors->has('conveyance_allowance') ? ' has-error' : '' }}">
                                                    <label class="col-sm-4 control-label d-block">Conveyance Allow.:<span
                                                            class="req_star">*</span></label>
                                                    <div class="col-sm-8">
                                                        <input type="number" class="form-control"
                                                            id="input_conveyance_allowance"
                                                            name="conveyance_allowance" value="" min="0" required>
                                                        @if ($errors->has('conveyance_allowance'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('conveyance_allowance')
                                                                }}</strong>
                                                        </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div
                                                    class="form-group row custom_form_group{{ $errors->has('food_allowance') ? ' has-error' : '' }}">
                                                    <label class="col-sm-4 control-label d-block">Food Allow.:<span
                                                            class="req_star">*</span></label>
                                                    <div class="col-sm-8">
                                                        <input type="number" class="form-control"
                                                            id="input_food_allowance" name="food_allowance"
                                                            value="" min="0" required >
                                                        @if ($errors->has('food_allowance'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('food_allowance')
                                                                }}</strong>
                                                        </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div
                                                    class="form-group row custom_form_group{{ $errors->has('others1') ? ' has-error' : '' }}">
                                                    <label class="col-sm-4 control-label d-block">Others:<span
                                                            class="req_star">*</span></label>
                                                    <div class="col-sm-8">
                                                        <input type="number" class="form-control" id="input_others1"
                                                            name="others1" value="" min="0" required>
                                                        @if ($errors->has('others1'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('others1') }}</strong>
                                                        </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div    class="form-group row custom_form_group{{ $errors->has('prom_remarks') ? ' has-error' : '' }}">
                                                    <label class="col-sm-4 control-label d-block">Remarks: </label>
                                                    <div class="col-sm-8">
                                                        <textarea class="form-control" id="prom_remarks"
                                                            name="prom_remarks"
                                                            placeholder="Promotion Remarks"></textarea>
                                                        @if ($errors->has('prom_remarks'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('prom_remarks') }}</strong>
                                                        </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="col-md-3">
                                        <button type="submit" class="btn btn-primary waves-effect"
                                            onclick="totalAmount()">SAVE</button>
                                        </div>

                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </form>
                        </div>
                        <div class="col-md-1"></div>
                    </div>
                    <!-- Employee Information with Salary Details -->
                    <div class="row">
                        <table class="table table-bordered table-striped table-hover show_employee_details_table"
                            id="showEmployeeDetailsTable">
                            <tr>
                                <td>
                                    <span class="emp">Employee Id:</span>
                                    <span id="show_employee_id" class="emp2"></span>
                                </td>
                                <td>
                                    <span class="emp">Employee Name:</span>
                                    <span id="show_employee_name" class="emp2"></span>
                                </td>
                                <td>
                                    <span class="emp">Basic Amount:</span>
                                    <span id="show_employee_basic" class="emp2"></span>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <span class="emp">Iqama No:</span>
                                    <span id="show_employee_akama_no" class="emp2"></span>
                                </td>
                                <td>
                                    <span class="emp">Passport No:</span>
                                    <span id="show_employee_passport_no" class="emp2"></span>
                                </td>
                                <td>
                                    <span class="emp">House Rent:</span>
                                    <span id="show_employee_house_rent" class="emp2"></span>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <span class="emp">Job Status:</span>
                                    <span id="show_employee_job_status" class="emp2"></span>
                                </td>
                                <td>
                                    <span class="emp">Project Name:</span>
                                    <span id="show_employee_project_name" class="emp2"></span>
                                </td>
                                <td>
                                    <span class="emp">Hourly Rate:</span>
                                    <span id="show_employee_hourly_rent" class="emp2"></span>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <span class="emp">Agency Name:</span>
                                    <span id="show_employee_agency_name" class="emp2"></span>
                                </td>
                                <td>
                                    <span class="emp">Sponsor Name:</span>
                                    <span id="show_employee_sponsor_name" class="emp2"></span>
                                </td>
                                <td>
                                    <span class="emp">Mobile Allowance:</span>
                                    <span id="show_employee_mobile_allow" class="emp2"></span>
                                </td>
                            </tr>


                            <tr>
                                <td>
                                    <span class="emp">Employee Type:</span>
                                    <span id="show_employee_type" class="emp2"></span>
                                </td>
                                <td>
                                    <span class="emp">Employee Designation:</span>
                                    <span id="show_employee_category" class="emp2"></span>
                                </td>
                                <td>
                                    <span class="emp">Local Travels Allowance:</span>
                                    <span id="show_employee_local_travel_allow" class="emp2"></span>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <span class="emp">Mobile Number:</span>
                                    <span id="show_employee_mobile_no" class="emp2"></span>
                                </td>
                                <td>
                                    <span class="emp">Joining Date:</span>
                                    <span id="show_employee_joining_date" class="emp2"></span>

                                </td>
                                <td>
                                    <span class="emp">Conveyance Allowance:</span>
                                    <span id="show_employee_conveyance_allow" class="emp2"></span>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <span class="emp">Confirmation Date:</span>
                                    <span id="show_employee_confirmation_date" class="emp2"></span>

                                </td>
                                <td>
                                    <span class="emp">Increment Amount:</span>
                                    <span id="show_employee_increment_amount" class="emp2"></span>
                                </td>
                                <td>
                                    <span class="emp">Food Allowance:</span>
                                    <span id="show_employee_food_allowance" class="emp2"></span>
                                </td>
                            </tr>

                            <tr>
                                <td>

                                </td>
                                <td>
                                    <span class="emp">Medical Allowance:</span>
                                    <span id="show_employee_medical_allow" class="emp2"></span>

                                </td>
                                <td>
                                    <span class="emp">Others:</span>
                                    <span id="show_employee_others" class="emp2"></span>
                                </td>
                            </tr>



                        </table>
                    </div>
            </div>
        </div>
    </div>
</div>

<!--Promoted Employee List Table !-->
<div class="row d-none" id="promoted_employee_list_section">
    <div class="card">
        <form id="employee-entry-in-form" action="{{ route('promoted.employees.approval.request') }}" method="post">
        @csrf
            <div class="card-body">
                <div class="form-group row custom_form_group" >
                    <div class="col-sm-10"></div>
                    <div class="col-sm-2">
                        <button type="submit" class="btn btn-primary waves-effect">Update</button>
                    </div>
                </div>
                <div class="table-responsive">
                    <span id="data_not_found" class="d-none">Data Not Found!</span>
                    <table id="alltableinfo" class="table table-bordered table-hover custom_table mb-0">
                        <thead>
                            <tr>
                                <th>S.N</th>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Iqama</th>
                                <th>Trade</th>
                                <th>Salary</th>
                                <th></th> <!-- auto id column for selection -->
                                <th>Effective</th>
                                <th>Added By</th>
                                <th>Remarks</th>
                                <th colspan="2">Action</th>
                            </tr>
                        </thead>
                        <tbody id="promoted_employee_list_table">
                        </tbody>
                    </table>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="promotion_approval_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="trie">
    <div class="modal-dialog modal-dialog-cenered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4>Searching Promoted Employee List for Approval</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div  class="form-group row custom_form_group{{ $errors->has('modal_prom_date') ? ' has-error' : '' }}">
                    <label class="col-sm-4 control-label  d-block">Promoted Date:<span
                            class="req_star">*</span></label>
                    <div class="col-sm-8">
                        <input type="date" name="modal_prom_date" id="modal_prom_date" class="form-control" value="{{ Carbon\Carbon::now()->format('Y-m-d') }}">
                        @if ($errors->has('modal_prom_date'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('modal_prom_date') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div  class="form-group row custom_form_group{{ $errors->has('modal_to_date') ? ' has-error' : '' }}">
                    <label class="col-sm-4 control-label  d-block">To Date:<span
                            class="req_star">*</span></label>
                    <div class="col-sm-8">
                        <input type="date" name="modal_to_date" id="modal_to_date" class="form-control" value="{{ Carbon\Carbon::now()->format('Y-m-d') }}" max="{{ Carbon\Carbon::now()->format('Y-m-d') }}">
                        @if ($errors->has('modal_to_date'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('modal_to_date') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <button type="submit" id="promted_emp_searching_btn" name="promted_emp_searching_btn" onclick="searchPromotedEmployeesForApproval(null,null)"  class="btn btn-success">Search</button>

            </div>
        </div>
    </div>
</div>


<!-- Paper Upload section !-->
<div class="row d-none" id="promotion_paper_upload_section">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body card_form">
                <div class="form-group row custom_form_group">
                    <label class="col-sm-2 control-label">From</label>
                    <div class="col-sm-2">
                        <input type="date" name="from_date" id="from_date" value="<?= date("Y-m-d") ?>" max="{{date('Y-m-d') }}" class="form-control">
                    </div>
                    <label class="col-sm-2 control-label">To</label>
                    <div class="col-sm-2">
                        <input type="date" name="to_date" id="to_date" value="<?= date("Y-m-d") ?>"   class="form-control">
                    </div>
                    <div class="col-sm-2">
                        <button type="button"  id ="emp_search_button"  onclick="searchPromotedEmployeesForPaperUpload()" class="btn btn-primary waves-effect">Search</button>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <!-- Promotion Approval Paper Upload !-->
    <div class="card">
        <form method="post" action="{{ route('promoted.employees.paper.upload.request') }}" id="promotion_paper_upload_form" enctype="multipart/form-data"
             onsubmit="attendance_submit_button.disabled = true;">
                @csrf
                <div class="card-header">
                    <div class="row">
                        <div class="form-group row custom_form_group">
                            <label class="col-sm-2 control-label">Upload File:</label>
                            <div class="col-sm-2">
                                <div class="input-group">
                                    <span class="input-group-btn">
                                        <span class="btn btn-default btn-file btnu_browse">
                                            Browse… <input type="file" name="upload_paper" id="imgInp4">
                                        </span>
                                    </span>
                                    <input type="text" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <img id='img-upload4' class="upload_image" />
                            </div>
                            <div class="col-sm-2"> <label for="" id="total_selection_label"></label></div>

                            <div class="col-sm-2">
                                <button type="submit" id="paper_upload_submit_button" name="paper_upload_submit_button" class="btn btn-primary waves-effect">Submit </button>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <span id="data_not_found" class="d-none">Data Not Found!</span>
                                <table id="alltableinfo" class="table table-bordered custom_table mb-0">
                                    <thead>
                                        <tr>
                                            <th>S.N</th>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Iqama</th>
                                            <th>Trade</th>
                                            <th>Salary</th>
                                            <th></th> <!-- auto id column for selection -->
                                            <th>Effective</th>
                                            <th>Added By</th>
                                            <th>Remarks</th>
                                            <th colspan="2">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="employee_list_for_upload_paper">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
        </form>
    </div>
</div>

 <!-- Promotion Report Modal -->
 <div class="modal fade" id="emp_promotion_report_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Salary Incresed Employee Report<span class="text-danger" id="errorData"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
                <div class="modal-body">

                        {{-- <div class="form-group row custom_form_group">
                            <label class="col-sm-3 control-label">Status</label>
                            <div class="col-sm-9">
                                <select class="form-select" name="report_search_by" id="report_search_by" required>
                                    <option value="0">Pending </option>
                                    <option value="1">Approved</option>
                                 </select>
                             </div>
                        </div> --}}

                        <div  class="form-group row custom_form_group{{ $errors->has('today') ? ' has-error' : '' }}">
                            <label class="col-sm-3 control-label">From</label>
                            <div class="col-sm-9">
                                <input type="date" class="form-control" id="from_date" name="from_date"
                                    value="<?= date("Y-m-d") ?>" max="{{ Carbon\Carbon::now()->format('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div  class="form-group row custom_form_group{{ $errors->has('today') ? ' has-error' : '' }}">
                            <label class="col-sm-3 control-label">To</label>
                            <div class="col-sm-9">
                                <input type="date" class="form-control" id="to_date" name="today"
                                    value="{{ Carbon\Carbon::now()->format('Y-m-d') }}"
                                max="{{ Carbon\Carbon::now()->format('Y-m-d') }}" required>
                            </div>
                        </div>

                        <h4 style="text-align: center;">OR</h4>
                        <div class="form-group row custom_form_group">
                            <label class="col-sm-3 control-label">Employee ID</label>
                            <div class="col-sm-9">
                                <input type="text"  name="employee_id" class="form-control" placeholder="Employee ID"
                                id="employee_id"   >
                            </div>
                        </div>
                        <button type="submit" id="report_btn" name="report_btn" onclick="searchPromotedEmployeesReport(1)"  class="btn btn-success">Search</button>

                         <hr>

                        <div class="form-group custom_form_group row">
                            <label class="control-label col-md-3">Designation</label>
                            <div class="col-md-9">
                              <select class="form-select" id="designation_heads">
                                <option value="">All</option>
                                @foreach($designation_heads as $arecord)
                                <option value="{{ $arecord->dh_auto_id }}">{{ $arecord->des_head_name }}</option>
                                @endforeach
                              </select>
                            </div>
                        </div>

                        <div class="form-group row custom_form_group">
                            <label class="control-label col-md-3">Project</label>
                            <div class="col-md-9">
                                <select class="form-select" id="project_ids" >
                                    <option value="">All</option>
                                    @foreach($projects as $proj)
                                    <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <h4 style="text-align: center;">OR</h4>
                        <div class="form-group row custom_form_group">
                            <label class="col-sm-3 control-label">Employee ID</label>
                            <div class="col-sm-9">
                                <input type="text"  name="employee_ids" class="form-control" placeholder="Multiple Employee ID e.g. 1020,1030"
                                id="employee_ids"  >
                            </div>
                        </div>


                         <div class="form-group row custom_form_group">
                            <label class="col-sm-3 control-label">Retport Type</label>
                            <div class="col-sm-9">
                                <select class="form-select" name="report_record_type" id="report_record_type" required>
                                    <option value="1">All Records </option>
                                    <option value="2">Only Last Increment</option>
                                 </select>
                             </div>
                        </div>

                    <button type="submit" id="report_btn" name="report_btn" onclick="searchPromotedEmployeesReport(2)"  class="btn btn-success">Search</button>
                </div>
        </div>
    </div>
</div>



    <!--Employee bulk promotion imports -->
    <div class="row d-none" id="employee_bulk_promotion_records_excel_file_import_section">
        <div class="col-md-12">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <form method="POST" enctype="multipart/form-data"
                                    id="upload_emp_promotion_excell_records"
                                    onsubmit="excel_upload_button.disabled = true;">
                                    @csrf

                                        <div class="form-group row custom_form_group mt-3">
                                            <label class="d-block col-md-3 text-right">Excel File:</label>
                                            <div class="col-md-5">
                                                <input type="file" name="file" placeholder="Choose File"
                                                    id="file">
                                                <span class="text-danger">{{ $errors->first('file') }}</span>
                                            </div>

                                            <div class="col-md-4">
                                                <button type="submit" id ="excel_upload_button"
                                                    class="btn btn-primary">UPLOAD</button>
                                            </div>
                                        </div>
                                        <hr>

                                </form>
                            </div>
                        </div>
                        <div class="card-body d-none" id="excell_file_upload_emp_list_table_section">
                            <div class="row">
                                <form method="POST" id="submit_imported_work_record"
                                    onsubmit="excel_submit_button.disabled = true;" action="{{ route('submit.employee.promotions.imported.excel') }}" enctype="multipart/form-data" >
                                    @csrf
                                        <div class="form-group row custom_form_group">
                                            <label class="d-block col-md-3 text-right">Promotion Date</label>
                                            <input type="date" class="col-md-3 form-control" value="{{ Date('Y-m-d') }}" name="promotion_date"
                                                id="promotion_date" required>
                                            <div class="col-md-3 text-right">
                                                <button type="submit" id ="excel_submit_button"
                                                    class="btn btn-primary waves-effect">SUBMIT </button>
                                            </div>
                                        </div>

                                        <div class="form-group row custom_form_group">
                                            <label class="d-block col-md-3 text-right">Promotion Paper</label>
                                            <div class="input-group col-md-4">
                                                <span class="input-group-btn ">
                                                    <span class="btn btn-default btn-file btnu_browse ">
                                                        Browse… <input type="file" name="prom_approve_documents"
                                                            id="imgInp" >
                                                    </span>
                                                </span>
                                                <input type="text" class="form-control" readonly>
                                            </div>
                                            <div class="col-md-5">
                                                <img id='img-upload' class="upload_image" />
                                            </div>
                                        </div>
                                </form>
                                <div class="col-12" id="valid_records_div_section">
                                    <div class="table-responsive">
                                        <table id="excell_file_upload_emp_list_table"
                                            class="table table-bordered custom_table mb-0">
                                            <thead>
                                                <tr>
                                                    <th>S.N</th>
                                                    <th>Emp. ID</th>
                                                    <th>Employee Name</th>
                                                    <th>Iqama No</th>
                                                    <th>Trade</th>
                                                    <th>Sponsor</th>
                                                    <th>Basic</th>
                                                    <th>House Rent</th>
                                                    <th>Hourly Rate</th>
                                                    <th>Food</th>
                                                    <th>Remarks</th>
                                                </tr>
                                            </thead>
                                            <tbody id="excell_file_upload_emp_list_table_body">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="col-12 d-none" id ="upload_error_div_section">
                                    <br />
                                    <h3>Data Error Found</h3>
                                    <div class="table-responsive">
                                        <table id="excell_file_upload_error_emp_list_table"
                                            class="table table-bordered custom_table mb-0">
                                            <thead>
                                                <tr>
                                                    <th>S.N</th>
                                                    <th>Emp. ID</th>
                                                    <th>Remarks</th>
                                                </tr>
                                            </thead>
                                            <tbody id="excell_file_upload_error_emp_list_table_body">

                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="col-12 d-none" id ="validation_error_div_section">
                                    <br />
                                    <h3>Validation Error Found</h3>
                                    <div class="table-responsive">
                                        <table id="excell_file_upload_validation_error_emp_list_table"
                                            class="table table-bordered custom_table mb-0">
                                            <thead>
                                                <tr>
                                                    <th>S.N</th>
                                                    <th>Emp. ID</th>
                                                    <th>Errors</th>
                                                </tr>
                                            </thead>
                                            <tbody id="excell_file_upload_validation_error_emp_list_table_body">

                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>




<!-- added this for Multiple Selection dropdownlist  -->
<link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta2/css/bootstrap-select.min.css"
    integrity="sha512-mR/b5Y7FRsKqrYZou7uysnOdCIJib/7r5QeJMFvLNHNhtye3xJp1TdJVPLtetkukFn227nKpXD9OjUc09lx97Q=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"
    integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4"
    crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta2/js/bootstrap-select.min.js"
    integrity="sha512-FHZVRMUW9FsXobt+ONiix6Z0tIkxvQfxtCSirkKc5Sb4TKHmqq1dZa8DphF0XqKb3ldLu/wgMa8mT6uXiLlRlw=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>




<script type="text/javascript">


  // Excel file upload
        $(document).ready(function(e) {
            // excell data uploaded button event
            $('#upload_emp_promotion_excell_records').submit(function(e) {

                    e.preventDefault();
                    var formData = new FormData(this);


                $.ajax({
                        type: 'POST',
                        url: "{{ route('import.employee.promotions.excel') }}",
                        data: formData,
                        cache: false,
                        contentType: false,
                        processData: false,
                    success: (response) => {

                            if (response.status != 200) {
                                alert(response.error);
                                document.getElementById("excel_upload_button").disabled = false;
                                return;
                            }
                            $('#excell_file_upload_emp_list_table_section').removeClass('d-none')
                                .addClass('d-block');
                            this.reset();
                            var rows = "";
                            var counter = 1;
                            document.getElementById("excel_upload_button").disabled = false;
                            $.each(response.records, function(key, value) {
                                rows += `
                                        <tr>
                                            <td>${counter++}</td>
                                            <td>${value.emp_id}</td>
                                            <td>${value.employee_name}</td>
                                            <td>${value.akama_no}</td>
                                            <td>${value.catg_name}</td>
                                            <td>${value.sponsor_name}</td>
                                            <td>${value.basic_amount}</td>
                                            <td>${value.house_rent}</td>
                                            <td>${value.hourly_rent}</td>
                                            <td>${value.food_allowance}</td>
                                            <td>${value.prom_remarks}</td>
                                        </tr>
                                        `
                            });
                            $('#excell_file_upload_emp_list_table_body').html(rows);

                            var error_rows = "";
                            var error_counter = 1;
                            $.each(response.records_not_found, function(key, value) {
                                error_rows += `
                                    <tr>
                                        <td>${error_counter++}</td>
                                        <td>${value.emp_id}</td>
                                        <td>${value.upload_status}</td>
                                    </tr>
                                    `
                            });

                            $("#upload_error_div_section").removeClass("d-none").addClass(
                                "d-block");
                            $('#excell_file_upload_error_emp_list_table_body').html(error_rows);

                            var validation_error_rows = "";
                            var validation_error_counter = 1;
                            $.each(response.records_with_errors, function(key, value) {
                                validation_error_rows += `
                                    <tr>
                                        <td>${validation_error_counter++}</td>
                                        <td>${value.emp_id}</td>
                                        <td>${value.errors.join(', ')}</td>
                                    </tr>
                                    `
                            });

                            if(response.records_with_errors.length > 0){
                                $("#validation_error_div_section").removeClass("d-none").addClass(
                                "d-block");
                                $('#excell_file_upload_validation_error_emp_list_table_body').html(validation_error_rows);
                            }

                        },
                    error: function(data) {


                            document.getElementById("excel_upload_button").disabled = false;

                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000
                            })
                            Toast.fire({
                                type: 'error',
                                title: data.status
                            })
                    }
                });
            });
        });

    $(document).ready(function () {
        $("#emp-promotion-apply").validate({
            rules: {
                promotion_by: {
                    required: true,
                },
                prom_date: {
                    required: true,
                },
                emplyoeeDesignation: {
                    required: true,
                },
            },

            messages: {
                promotion_by: {
                    required: "You Must Be Input This Field!",
                },
                prom_date: {
                    required: "You Must Be Input This Field!",
                },
                emplyoeeDesignation: {
                    required: "Please Select Any Designation!",
                },
            },
        });


            // bulk import ui
            $("#bulkImportUI").on('click', function() {
                $('#employee_bulk_promotion_records_excel_file_import_section').removeClass('d-none')
                    .addClass('d-block');
                $('#showEmployeeAdvanceApplyForm').addClass('d-none').removeClass('d-block');

            })
    });

    // Method For Reset All Loaded Data
    function resetEmpInfo() {

        $("#show_employee_system_auto_id").html('');
        $("#show_employee_id").html('');
        $("#show_employee_name").html('');
        $("#show_employee_akama_no").html('');
        $("#show_employee_akama_expire_date").html('');
        $("#show_employee_passport_no").html('');
        $("#show_employee_passport_expire_date").html('');
        $("#show_employee_mobile_no").html('');
        $("#show_employee_sponsor_name").html('');
        $("#show_employee_job_status").html('');
        $("#show_employee_project_name").html('');
        $("#show_employee_type").html('');
        $("#show_employee_category").html('');
        $("#show_employee_joining_date").html('');
        $("#show_employee_appointment_date").html('');
        $("#show_employee_email").html('');
        $("#show_employee_basic").html('');
        $("#show_employee_house_rent").html('');
        $("#show_employee_hourly_rent").html('');
        $("#show_employee_mobile_allow").html('');
        $("#show_employee_medical_allow").html('');
        $("#show_employee_local_travel_allow").html('');
        $("#show_employee_conveyance_allow").html('');
        $("#show_employee_increment_amount").html('');
        $("#show_employee_food_allowance").html('');
        $("#show_employee_saudi_tax").html('');
        $("#input_basic_amount").html('');
        $("#basic_amount").html('');
        $("#input_hourly_rate").html('');
        $("#hourly_rate").html('');
        $("#input_house_rate").html('');
        $("#input_mobile_allowance").html('');
        $("#input_medical_allowance").html('');
        $("#input_local_travel_allowance").html('');
        $("#input_conveyance_allowance").html('');
        $("#input_food_allowance").html('');
        $("#input_others1").html('');
        $("#show_employee_agency_name").html('');
    }

    $('#empl_info').keydown(function (e) {
        if (e.keyCode == 13) {
            singleEmoloyeeDetails();
        }
    })

    function totalAmount() {

        var basicSalaryAmount = $(".inputBasicSalaryAmount").val();
        var mobileAllowanceAmount = $(".inputMobileAllowanceAmount").val();
        var hourlyRateAmount = $(".inputHourlyRateAmount").val();
        var houseRentAmount = $(".inputHouseRentAmount").val();
        var medicalAllowanceAmount = $(".inputMedicalAllowanceAmount").val();
        var localTravelAllowanceAmount = $(".inputLocalTravelAllowanceAmount").val();
        var conveyanceAllowanceAmount = $(".inputConveyanceAllowanceAmount").val();
        var foodAllowanceAmount = $(".inputFoodAllowanceAmount").val();
        var othersAmount = $(".inputOthersAmount").val();

        var calculatedAmount = Number(basicSalaryAmount) + Number(mobileAllowanceAmount) + Number(houseRentAmount) + Number(medicalAllowanceAmount) + Number(localTravelAllowanceAmount) + Number(conveyanceAllowanceAmount) + Number(foodAllowanceAmount) + Number(othersAmount);

        $('.totalPromotionAmount').val(calculatedAmount);
    }

    $('#basic_amount').on('input', function() {
        calculateHourlyRate();
    });

    function calculateHourlyRate(){

        var basic = document.getElementById('basic_amount').value != "" ? parseFloat(document.getElementById('basic_amount').value) : 0;
       //  var hourly = basic/300;
        document.getElementById("hourly_rate").value = (basic/300).toFixed(2);

    }

    //   Single Employee Details Info
    function singleEmoloyeeDetails() {

        resetEmpInfo() // reset All Employe Info

        var searchType = "employee_id";
        var searchValue = $("#empl_info").val();
        if (searchValue.length == 10) {
            searchType = "akama_no";
        }
        if ($("#empl_info").val().length === 0) {
           showSweetAlert('Please Input Valid Data','error')
           return;
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

                    if (response.success == false) {
                        $('input[id="emp_auto_id"]').val(null);
                        $("span[id='employee_not_found_error_show']").text('Please Enter An Active Employee Id');
                        $("span[id='employee_not_found_error_show']").addClass('d-block').removeClass('d-none');
                        $("#showEmployeeAdvanceApplyForm").removeClass("d-block").addClass("d-none");
                        return;
                    } else {
                        $("span[id='employee_not_found_error_show']").removeClass('d-block').addClass('d-none');
                    }

                      if (response.findEmployee.length > 1) {
                        showSweetAlert("Critical Error Found, Please Contact with Software Support","error");
                    } else {
                        showSearchingEmployee(response.findEmployee[0], response.designation);
                         openEmployeeSeaching();
                    }
                } // end of success
        }); // end of ajax calling

    }


    function searchPromotedEmployeesReport(report_type){

        var from_date = $('#from_date').val();
        var to_date = $('#to_date').val();
        var employee_ids = report_type == 1 ?  $('#employee_id').val() :  $('#employee_ids').val();
        var project_ids = $('#project_ids').val();
        var designation_heads = $('#designation_heads').val();
    var report_record_type = $('#report_record_type').val();

        // Create the query string with parameters
        const parameterValue = new URLSearchParams({
                from_date:from_date,
                to_date:to_date,
                employee_ids:employee_ids,
                project_ids:project_ids,
                designation_heads:designation_heads,
                report_type:report_type,
                report_record_type:report_record_type,

        }).toString();

        var url = "{{ route('employee.promotion.details.date.to.date.report', ':parameter') }}";
        url = url.replace(':parameter', parameterValue);
        window.open(url, '_blank');

    }


    function searchPromotedEmployeesReport222(){
        var from_date = $('#from_date').val();
        var to_date = $('#to_date').val();
        var search_by = $('#report_search_by').val();
        var search_value = $('#report_search_value').val();
        // Create the query string with parameters
        const parameterValue = new URLSearchParams({
                from_date:from_date,
                to_date:to_date,
                search_by:search_by,
                search_value:search_value,
        }).toString();

        var url = "{{ route('employee.promotion.details.date.to.date.report', ':parameter') }}";
        url = url.replace(':parameter', parameterValue);
        window.open(url, '_blank');

    }




    function daysInMonth(month, year) {
        return new Date(year, month, 0).getDate();
    }
    // searching promotion record
    function searchPromotedEmployeesForApproval(month,year){

        resetEmpInfo();
        var from_date,to_date  ;
        var today = new Date();

        if(month && year){
             var from_date = year+"/"+month+"/"+"1";
             var to_date = year+"/"+month+"/"+daysInMonth(month,year);
        }
        else {
            var from_date = $('#modal_prom_date').val();
            var to_date = $('#modal_to_date').val();
        }

       $('#promoted_employee_list_table').html('');
        $.ajax({
            type:"GET",
            url:"{{ route('promoted.employees.waiting.forapproval.records') }}",
            dataType: 'json',
            data:{
                from_date:from_date,
                to_date:to_date,
                employee_id:0,
            },
            success:function(response){

                    if(response.status != 200 || response.data.length == 0){
                        showSweetAlert('Record Not Found','error');
                        return;
                    }
                    openPromotionApprovalSection();
                    var rows = "";
                    var counter = 1;
                    $.each(response.data, function (key, value) {
                        rows += `
                        <tr>
                            <td>${counter++}</td>
                            <td>${value.employee_id}</td>
                            <td>${value.employee_name}</td>
                            <td>${value.akama_no} </td>
                            <td>${value.catg_name}</td>
                            <td>${value.hourly_employee == 1 ? "Hourly-"+value.hourly_rent : "Basic-"+value.basic_amount}</td>
                            <td style="color:#fff">${value.emp_prom_id}</td>
                            <td><b> ${ (new Date(value.prom_date)).toLocaleString('default', { month: 'long' })} <br>   ${value.prom_date}</b> </td>
                            <td>${value.name}</td>
                            <td>${value.prom_remarks == null ? "" : value.prom_remarks }, ${value.prom_by == null ? "" : value.prom_by}</td>
                            <td id="">
                                <input type="hidden" id="emp_prom_auto_id${value.emp_prom_id}" name="emp_prom_auto_id[]" value="${value.emp_prom_id}">
                                @can('emp_promotion_record_approval')
                                    <input type="checkbox" name="promoted_emp_checkbox-${value.emp_prom_id}" id="promoted_emp_checkbox-${value.emp_prom_id}" value="0">
                                @endcan
                            </td>
                            <td>
                                @can('emp_promotion_record_delete')
                                    <a href="#" onclick="deleteAPromotionRecord(${value.emp_prom_id})" title="Delete"> <i class="fa fa-trash delete_icon"></i></a>
                                @endcan
                                    <a target="_blank" href="` + getAttachmentUrl(value.prom_apprv_documents) + ` "  > <i class="fa fa-eye view_icon"></i>  </a>
                            </td>
                        </tr>
                        `
                    });
                    $('#promoted_employee_list_table').html(rows);

            },
            error:function(response){
                showSweetAlert('error','Network Error, Please Refresh and Try Again');
            }

        })

    }

     function getAttachmentUrl(path)
    {
        return "{{ config('app.aws_s3_bucket_url') }}" + path;
    }


    function showSweetAlert(message,opeation_status){
        const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000
                            })
                            Toast.fire({
                                type: opeation_status,// 'error',
                                title: message
                            })

    }


    // End of Method for Router calling
    function showSearchingEmployee(findEmployee, designation) {
        /* Employement Details  */
        $("span[id='show_employee_id']").text(findEmployee.employee_id);
        $("span[id='show_employee_name']").text(findEmployee.employee_name);
        $("span[id='show_employee_akama_no']").text(findEmployee.akama_no+", "+findEmployee.akama_expire_date);
        $("span[id='show_employee_passport_no']").text(findEmployee.passfort_no+", "+findEmployee.passfort_expire_date);
        $("span[id='show_employee_mobile_no']").text(findEmployee.mobile_no);
        $("span[id='show_employee_job_status']").text(findEmployee.title);
        $("span[id='show_employee_joining_date']").text(findEmployee.joining_date);
        $("span[id='show_employee_appointment_date']").text(findEmployee.appointment_date);
        $("span[id='show_employee_email']").text(findEmployee.email);
        $("span[id='show_employee_confirmation_date']").text(findEmployee.confirmation_date);
        $("span[id='show_employee_agency_name']").text(findEmployee.agc_title);
        $("span[id='show_employee_category']").text(findEmployee.catg_name);


        $("span[id='show_employee_sponsor_name']").text( (findEmployee.sponsor_id == null ? "No Assigned Project": findEmployee.spons_name));
        $("span[id='show_employee_project_name']").text( (findEmployee.project_id == null ? "No Assigned Project": findEmployee.proj_name));
        $("span[id='show_employee_type']").text( (findEmployee.emp_type_id == 1 ? "Direct Manpower": "Indirect Manpower"));


        // Employee Designation Name, Id for dropdown List
        if (designation != '') {
            $('select[name="emplyoeeDesignation"]').empty();
            $('select[name="emplyoeeDesignation"]').append('<option value="">Please Select Designation</option>');
            $.each(designation, function (key, value) {
                $('select[name="emplyoeeDesignation"]').append("<option " + "value=" + value.catg_id + " + " + (value.catg_id == findEmployee.catg_name ? 'selected' : '') + ">" + value.catg_name + "</option>");

            });
        } else {
            $('select[name="emplyoeeDesignation"]').append('<option value="">Data Not Found!</option>');
        }

        /* Show Employee Salary Details */
        $("span[id='show_employee_basic']").text(findEmployee.basic_amount);
        $("span[id='show_employee_house_rent']").text(findEmployee.house_rent);
        $("span[id='show_employee_hourly_rent']").text(findEmployee.hourly_rent);
        $("span[id='show_employee_mobile_allow']").text(findEmployee.mobile_allowance);
        $("span[id='show_employee_medical_allow']").text(findEmployee.medical_allowance);
        $("span[id='show_employee_local_travel_allow']").text(findEmployee.local_travel_allowance);
        $("span[id='show_employee_conveyance_allow']").text(findEmployee.conveyance_allowance);
        $("span[id='show_employee_increment_amount']").text(findEmployee.increment_amount);
        $("span[id='show_employee_food_allowance']").text(findEmployee.food_allowance);
        $("span[id='show_employee_saudi_tax']").text(findEmployee.saudi_tax);

        /* show salary details in input form */
        $('input[id="input_emp_auto_id"]').val(findEmployee.emp_auto_id);
        $('input[id="input_emp_designation"]').val(findEmployee.catg_id);

        $('input[id="input_basic_amount"]').val(findEmployee.basic_amount);
        $('input[id="basic_amount"]').val(findEmployee.basic_amount);
        $('input[id="input_hourly_rate"]').val(findEmployee.hourly_rent);
        $('input[id="hourly_rate"]').val(findEmployee.hourly_rent);
        $('input[id="input_house_rate"]').val(findEmployee.house_rent);
        $('input[id="input_mobile_allowance"]').val(findEmployee.mobile_allowance);
        $('input[id="input_medical_allowance"]').val(findEmployee.medical_allowance);
        $('input[id="input_local_travel_allowance"]').val(findEmployee.local_travel_allowance);
        $('input[id="input_conveyance_allowance"]').val(findEmployee.conveyance_allowance);
        $('input[id="input_food_allowance"]').val(findEmployee.food_allowance);
        $('input[id="input_others1"]').val(findEmployee.others1);
    }

    function openPromotionApprovalSection(){
        $("#promotion_paper_upload_section").removeClass("d-block").addClass("d-none"); // show paper upload section
        $("#showEmployeeAdvanceApplyForm").removeClass("d-block").addClass("d-none"); // show signle employee details
        $('#promoted_employee_list_section').removeClass('d-none').addClass("d-block");
    }

    function openEmployeeSeaching(){
        $('#promoted_employee_list_section').removeClass('d-block').addClass("d-none");
        $("#promotion_paper_upload_section").removeClass("d-block").addClass("d-none"); // show paper upload section
        $("#showEmployeeAdvanceApplyForm").removeClass("d-none").addClass("d-block"); // show signle employee details
    }
    function openPaperUploadSection(){
        $('#promoted_employee_list_section').removeClass('d-block').addClass("d-none");
        $("#showEmployeeAdvanceApplyForm").removeClass("d-block").addClass("d-none"); // show signle employee details
        $("#promotion_paper_upload_section").removeClass("d-none").addClass("d-block"); // show paper upload section

    }
    function searchPromotedEmployeesForPaperUpload(){

            resetEmpInfo();

            var from_date = $('#modal_prom_date').val();
            var to_date = $('#modal_to_date').val();
            $('#promoted_employee_list_table').html('');

            $.ajax({
                type:"GET",
                url:"{{ route('promoted.employees.waiting.forapproval.records') }}",
                dataType: 'json',
                data:{
                    from_date:from_date,
                    to_date:to_date,
                    employee_id:0,
                },
                success:function(response){

                    if(response.status != 200 || response.data.length == 0){
                        showSweetAlert('Record Not Found','error');
                        return;
                    }

                   $('#advance_paper_upload_form').removeClass('d-none').addClass("d-block");
                    var rows = "";
                    var counter = 1;
                    $.each(response.data, function (key, value) {
                         rows += `
                        <tr>
                            <td>${counter++}</td>
                            <td>${value.employee_id}</td>
                            <td>${value.employee_name}</td>
                            <td>${value.akama_no} </td>
                            <td>${value.catg_name}</td>
                            <td>${value.hourly_employee == 1 ? "Hourly-"+value.hourly_rent : "Basic-"+value.basic_amount}</td>
                            <td style="color:#fff">${value.emp_prom_id}</td>
                            <td>${value.prom_date}</td>
                            <td>${value.name}</td>
                            <td>${value.prom_remarks == null ? "" : value.prom_remarks }, ${value.prom_by == null ? "" : value.prom_by}</td>
                            <td>
                                <input type="hidden" id="emp_prom_auto_id${value.emp_prom_id}" name="emp_prom_auto_id[]" value="${value.emp_prom_id}">
                                <input type="checkbox" name="promoted_emp_checkbox-${value.emp_prom_id}" id="promoted_emp_checkbox-${value.emp_prom_id}" value="0">
                            </td>
                            <td>

                            </td>
                        </tr>
                        `
                    });
                    $('#employee_list_for_upload_paper').html(rows);
                    //  <a target="_blank" href="{{ url('${value.prom_apprv_documents}') }}" class="btn btn-success">View </a>

                },
                error:function(response){
                    showSweetAlert('error','Network Error, Please Refresh and Try Again');
                }

            })

    }

    function deleteAPromotionRecord(promotion_auto_id){

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
                    url: "{{  route('promotion.record.delete') }}",
                    data:{
                        promotion_auto_id:promotion_auto_id,
                    },
                    dataType: 'json',
                    success: function (response) {

                        if(response.status == 200){
                            showSweetAlert(response.message,'success');
                            window.location.reload();
                        }else {
                            showSweetAlert(response.message,'error');
                        }
                    },
                    error:function(response){
                        showSweetAlert("Operation Failed, Please Try Again",'error');
                    }
                });


            }
        });

    }

</script>




@endsection
