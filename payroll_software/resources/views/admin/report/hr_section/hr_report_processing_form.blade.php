@extends('layouts.admin-master')
@section('title')HR Report @endsection
@section('content')
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title"> Human Resource Report</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active"> HR Report </li>
        </ol>
    </div>
</div>
<!-- Session Message Flash -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        @if(Session::has('error'))
        <div class="alert alert-warning alerterror" role="alert">
            <strong> {{Session::get('error')}} </strong>
        </div>
        @endif
    </div>
</div>

<!-- 1 Multiple Employee ID base details Report -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <form class="form-horizontal" target="_blank" action="{{ route('employee.details.multiple.employee.id.report') }}"
            method="post">
            @csrf
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title card_top_title salary-generat-heading">1. Multiple ID Base Employee Details</h3>
                </div>
                <div class="card-body card_form" style="padding-top: 0;">

                <div  class="form-group row custom_form_group{{ $errors->has('from_date') ? ' has-error' : '' }}">
                    <label class="col-sm-3 control-label">Employees ID:<span class="req_star">*</span></label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" name="multiple_employee_Id" autofocus  required>
                    </div>
                     <div class="col-sm-3">
                    </div>
                </div>
                <div class="form-group row custom_form_group">
                        <label class="control-label col-md-3">Report Type:</label>
                        <div class="col-md-6">
                            <select class="form-select" name="report_type" id="report_type" required>
                                <option value="2">Employee Activities</option>
                                <option value="3">Employee Working Project</option>
                                <option value="1">Employee Details</option>
                                <option value="4">Prevacation Statement</option>
                            </select>
                        </div>
                </div>
                <div class="form-group row custom_form_group">
                        <label class="control-label col-md-3">Report Type:</label>
                        <div class="col-md-6">
                            <select class="form-select" name="report_format" id="report_format" required>
                                <option value="1">PDF</option>
                                <option value="2">Excel</option>
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <button type="submit" class="btn btn-primary waves-effect">Show Report</button>
                         </div>
                </div>
                
            </div>
        </form>
    </div>
    <div class="col-md-2"></div>
</div>


<!-- 2 Employee Summary Pivot Table Report -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <form class="form-horizontal" target="_blank" action="{{ route('hr.employee.summary.report') }}"
            method="post">
            @csrf
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title card_top_title salary-generat-heading">2. Employees Summary Report </h3>
                </div>
                <div class="card-body card_form" style="padding-top: 0;">

                    <div class="form-group row custom_form_group">
                        <label class="control-label col-md-3">Project Name:</label>
                        <div class="col-md-6">
                            <select class="selectpicker" name="project_id_list[]" multiple >
                                @foreach($projects as $proj)
                                <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row custom_form_group">
                        <!-- <label class="control-label col-md-3">Employee Status:</label> -->
                        <div class="col-md-6">
                            <select class="form-control" name="job_status" hidden>
                             <option value="1">Active</option>
                              <option value="2">InActive</option>
                               <option value="3">Final_Exit</option>
                                <option value="4">Release</option>
                                 <option value="5">Vacation</option>
                                 <option value="6">Run_Away</option>
                                  <option value="7">VISA_Cancel</option>
                                   <option value="8">Final_Exit_and_Return_Back</option>
                            </select>


                        </div>
                    </div>

                    <div class="form-group row custom_form_group">
                        <label class="control-label col-md-3">Summary Report:</label>
                        <div class="col-md-6">
                            <select class="form-control" name="report_format"  id="summary_report" required>
                                <option value="1">Employee Summary (Trade)</option>
                                <option value="2">Employee Summary (Project)</option>
                                <option value="4">Employee Summary (Sponsor)</option>
                                <option value="3">Total Manpower Pivot Chart</option>
                             </select>
                        </div>
                        <div class="col-sm-3">
                            <button type="submit" class="btn btn-primary waves-effect">Show Summary</button>
                        </div>
                    </div>
                     <!--Sponsor List Dropdown -->
                    <!--<div id="sponsor_summary_section" class="d-none" >-->
                    <!--     <div class="col-md-6" style="padding-left: 200px;">-->
                    <!--        <b> Sponsor :</b>-->
                    <!--        <select class="selectpicker" name="spons_id[]" multiple>-->
                    <!--            @foreach($sponser as $spons)-->
                    <!--            <option value="{{ $spons->spons_id }}">{{ $spons->spons_name }}</option>-->
                    <!--            @endforeach-->
                    <!--        </select>-->
                    <!--    </div>-->
                    <!--</div>-->

            </div>
        </form>
    </div>
    <div class="col-md-2"></div>
</div>

 <!-- 3 Designation Head Basis Employees Report  -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <form class="form-horizontal" target="_blank" action="{{ route('hr.employee.designation.head.wise.report') }}"
            method="post">
            @csrf
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title card_top_title salary-generat-heading">3. Designation Head Basis Employees Report </h3>
                </div>
                <div class="card-body card_form" style="padding-top: 0;">
                    <div class="form-group row custom_form_group">
                        <label class="control-label col-md-3">Project Name:</label>
                        <div class="col-md-6">
                            <select class="selectpicker" name="project_id_list[]" multiple >
                                @foreach($projects as $proj)
                                <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row custom_form_group">
                        <label class="control-label col-md-3">Designation Head</label>
                        <div class="col-md-6">
                                <select class="selectpicker" name="designation_head[]" multiple>
                                    @foreach($designation_heads as $arecord)
                                    <option value="{{$arecord->dh_auto_id}}">{{$arecord->des_head_name}}</option>
                                    @endforeach
                                </select>
                        </div>
                    </div>

                    <div class="form-group row custom_form_group">
                        <label class="control-label col-md-3">Employee Status</label>
                        <div class="col-md-6">
                                <select class="selectpicker" name="job_status[]" multiple >
                                   <option value="1">Active</option>
                                  <option value="2">InActive</option>
                                   <option value="3">Final_Exit</option>
                                    <option value="4">Release</option>
                                     <option value="5">Vacation</option>
                                     <option value="6">Run_Away</option>
                                      <option value="7">VISA_Cancel</option>
                                       <option value="8">Final_Exit_and_Return_Back</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row custom_form_group">
                        <label class="control-label col-md-3">Summary Report:</label>
                        <div class="col-md-6">
                            <select class="form-select" name="report_format" required>
                                <option value="1">Summary Report</option>
                                <option value="2">Employee List</option>
                            </select>
                        </div>
                    <div class="col-sm-3">
                        <button type="submit" class="btn btn-primary waves-effect">Show Report</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="col-md-2"></div>
</div>


<!-- 3.1 Sponsor Base Employee Summary -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <form class="form-horizontal" target="_blank" action="{{ route('hr.employee.sponsor.base.emp.summary.report') }}" method="post">
            @csrf
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title card_top_title salary-generat-heading">3.1 Sponsor Base Employee (Active,Vacation,Runaway) Summary</h3>
                </div>
                <div class="card-body card_form" style="padding-top: 0;">

                    <!--Project List Dropdown -->
                    <!-- <div class="form-group row custom_form_group">-->
                    <!--    <label class="control-label col-md-3">Project:</label>-->
                    <!--    <div class="col-md-6">-->
                    <!--        <select class="selectpicker" name="project_id_list[]" multiple>-->
                    <!--            @foreach($projects as $proj)-->
                    <!--            <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>-->
                    <!--            @endforeach-->
                    <!--        </select>-->
                    <!--    </div>-->
                    <!--</div>-->

                    <!--Sponsor List Dropdown -->
                    <div class="form-group row custom_form_group">
                        <label class="control-label col-md-3">Sponsor:</label>
                        <div class="col-md-4">
                            <select class="selectpicker" name="spons_id[]" multiple>
                                <!--<option value="">All Sponsor</option>-->
                                @foreach($sponser as $spons)
                                <option value="{{ $spons->spons_id }}">{{ $spons->spons_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <button type="submit" class="btn btn-primary waves-effect">Show Report</button>
                        </div>
                    </div>
            </div>
        </form>
    </div>
    <div class="col-md-2"></div>
</div>



<!-- 3.2 Employee Last Activity Details -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <form class="form-horizontal" target="_blank" action="{{ route('hr.employee.last.activity.report') }}" method="post">
            @csrf
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title card_top_title salary-generat-heading">3.2 Employee Last Activity Details</h3>
                </div>
                <div class="card-body card_form" style="padding-top: 0;">

                    <!--Project List Dropdown -->
                    <div class="form-group row custom_form_group">
                        <label class="control-label col-md-3">Project:</label>
                        <div class="col-md-6">
                            <select class="selectpicker" name="project_id_list[]" multiple>
                                @foreach($projects as $proj)
                                <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!--Sponsor List Dropdown -->
                    <div class="form-group row custom_form_group">
                        <label class="control-label col-md-3">Sponsor:</label>
                        <div class="col-md-4">
                            <select class="selectpicker" name="spons_id[]" multiple>
                                @foreach($sponser as $spons)
                                <option value="{{ $spons->spons_id }}">{{ $spons->spons_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                     <!--Activity Duration -->
                     <div class="form-group row custom_form_group">
                        <label class="control-label col-md-3">Period:</label>
                        <div class="col-md-4">
                            <select class="form-select" name="activity_period">
                                <option value="3">More Than 3 Months</option>
                                <option value="6">More Than 6 Months</option>
                                <option value="9">More Than 9 Months</option>
                                <option value="12">More Than 1 Year</option>

                            </select>
                        </div>

                        <div class="col-sm-2">
                            <button type="submit" class="btn btn-primary waves-effect">Show Report</button>
                        </div>
                    </div>
            </div>
        </form>
    </div>
    <div class="col-md-2"></div>
</div>


<!-- 4 Employee List Project Wise report section -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <form class="form-horizontal" target="_blank" action="{{ route('hr.employee.report.process') }}" method="post">
            @csrf
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title card_top_title salary-generat-heading"> 4. Employee List With File Download </h3>
                </div>
                <div class="card-body card_form" style="padding-top: 0;">

                    <!--Project List Dropdown -->
                    <div class="form-group row custom_form_group">
                        <label class="control-label col-md-3">Project:</label>
                        <div class="col-md-6">

                            <select class="selectpicker" name="project_id_list[]" multiple >
                                @foreach($projects as $proj)
                                <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!--Sponsor List Dropdown -->
                    <div class="form-group row custom_form_group">
                        <label class="control-label col-md-3">Sponsor:</label>
                        <div class="col-md-6">
                            <select class="selectpicker" name="spons_id[]" multiple>
                                @foreach($sponser as $spons)
                                <option value="{{ $spons->spons_id }}">{{ $spons->spons_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!--Trade/Designation List Dropdown -->
                    <div class="form-group row custom_form_group">
                        <label class="control-label col-md-3">Trade:</label>
                        <div class="col-md-6">
                            <select class="selectpicker" name="catg_id[]" multiple>
                                 @foreach($category as $cate)
                                <option value="{{ $cate->catg_id }}">{{ $cate->catg_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!--EMployee Status List Dropdown -->
                    <div class="form-group row custom_form_group">
                        <label class="control-label col-md-3">Employee Status:</label>
                        <div class="col-md-6">
                            <select class="selectpicker"  name="job_status[]" multiple>
                                 <option value="1">Active</option>
                                  <option value="2">InActive</option>
                                   <option value="3">Final_Exit</option>
                                    <option value="4">Release</option>
                                     <option value="5">Vacation</option>
                                     <option value="6">Run_Away</option>
                                      <option value="7">VISA_Cancel</option>
                                       <option value="8">Final_Exit_and_Return_Back</option>

                            </select>
                        </div>
                    </div>

                    <!--Report Format Dropdown -->
                    <div class="form-group row custom_form_group">
                        <label class="control-label col-md-3"> Report Format:</label>
                        <div class="col-md-6">
                            <select class="form-control" name="report_format" required>
                                <option value="1">Print</option>
                                <option value="2">Excell</option>
                                <option value="3">CSV</option>
                            </select>
                      </div>
                        <div class="col-sm-3">
                            <button type="submit" class="btn btn-primary waves-effect">Show Report</button>
                        </div>
                    </div>
            </div>
        </form>
    </div>
    <div class="col-md-2"></div>
</div>



 <!-- 5 Employee List By Iqama Expired Date -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <form class="form-horizontal" target="_blank"
            action="{{ route('employee-list-projectwise-by-iqama-expire-date') }}" method="post">
            @csrf
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title card_top_title salary-generat-heading">5. Employee Report by Iqama Expiration
                        Date </h3>
                </div>
                <div class="card-body card_form" style="padding-top: 0;">

                    <!--Project List Dropdown -->
                     <div class="form-group row custom_form_group">
                        <label class="control-label col-md-3">Project Name:</label>
                        <div class="col-md-6">
                            <select class="selectpicker" name="project_id_list[]" multiple>
                                @foreach($projects as $proj)
                                <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row custom_form_group">
                        <label class="control-label col-md-3">Sponsor Name:</label>
                        <div class="col-md-4">
                            <select class="selectpicker" name="sponsor_id_list[]" multiple>
                                @foreach($sponser as $spons)
                                <option value="{{ $spons->spons_id }}">{{ $spons->spons_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!--Time select From Dropdown -->
                    <div class="form-group row custom_form_group">
                        <label class="control-label col-md-3">Expiry Duration:</label>
                        <div class="col-md-6">
                            <select class="form-control" name="expire_durations" required>
                                <option value="">Select Expiry Time Duration</option>
                                <option value="10">Valid Iqama</option>
                                <option value="1">Already Expired</option>
                                <option value="2">This Month</option>
                                <option value="3">Next Month</option>
                                <option value="4">After 3 Months</option>
                                <option value="5">After 6 Months</option>
                                <option value="6">After 12 Months</option>
                                <option value="7">After 2 Years</option>
                                <option value="8">Iqama Not Updated Emp List</option>
                               <option value="9">Iqama File Not Uploaded</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row custom_form_group">
                        <label class="col-sm-3 control-label">Starting Date:<span class="req_star">*</span></label>
                        <div class="col-sm-6">
                            <input type="date" class="form-control" name="iqama_date" value="{{ date('Y-m-d') }}">
                        </div>
                    </div>

                    <div class="form-group row custom_form_group">
                        <label class="control-label col-md-3"> Report Format:</label>
                        <div class="col-md-6">
                          <select class="form-control" name="report_format" required>
                            <option value="1">Print</option>
                            <option value="2">Excell</option>
                            <option value="3">CSV</option>
                           </select>
                      </div>
                        <div class="col-sm-3">
                            <button type="submit" class="btn btn-primary waves-effect">Show Report</button>
                        </div>
                    </div>
            </div>
        </form>
    </div>
    <div class="col-md-2"></div>
</div>


<!-- 6 Company Villa Wise All Employees Report -->

<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <form class="form-horizontal" target="_blank" id="villa_wise_employee" action="{{ route('hr.villa_wise_employees.report') }}" method="post">
            @csrf
            <div class="card">
                <div class="card-header">
                    <!--<h3 class="card-title card_top_title salary-generat-heading">6. Accommodation Villawise Employees List-->
                    <!--    Report</h3>-->
                </div>
                <div class="card-body card_form" style="padding-top: 0;">


                    <!--<div class="form-group row custom_form_group{{ $errors->has('accomd_ofb_id') ? ' has-error' : '' }}">-->
                    <!--    <label class="control-label col-md-3">Villa Name:</label>-->
                    <!--    <div class="col-md-6">-->
                    <!--        <select class="selectpicker" name="accomd_ofb_id[]" multiple required>-->
                    <!--            <option value="">Select Villa Name</option>-->
                    <!--            @foreach($accomdOfficeBuilding as $officeBuilding)-->
                    <!--            <option value="{{ $officeBuilding->ofb_id }}">{{ $officeBuilding->ofb_name }} - {{ $officeBuilding->ofb_city_name }}</option>-->
                    <!--            @endforeach-->
                    <!--        </select>-->

                    <!--    </div>-->
                    <!--</div>-->

                    <!--<div class="form-group row custom_form_group">-->
                    <!--    <label class="control-label col-md-3">Project Name:</label>-->
                    <!--    <div class="col-md-6">-->

                    <!--        <select class="selectpicker" name="project_name_id[]" multiple>-->
                    <!--            @foreach($projects as $proj)-->
                    <!--            <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>-->
                    <!--            @endforeach-->
                    <!--        </select>-->
                    <!--    </div>-->
                    <!--</div>-->


                   <!-- <div class="form-group row custom_form_group">-->
                   <!--     <label class="control-label col-md-3">Employee Trade:</label>-->
                   <!--     <div class="col-md-6">-->
                   <!--         <select class="selectpicker" name="catg_id[]" multiple>-->
                   <!--             <option value="">All Trade</option>-->
                   <!--             @foreach($category as $cate)-->
                   <!--             <option value="{{ $cate->catg_id }}">{{ $cate->catg_name }}</option>-->
                   <!--             @endforeach-->
                   <!--         </select>-->
                   <!--     </div>-->
                   <!--     <div class="col-sm-3">-->
                   <!--         <button type="submit" class="btn btn-primary waves-effect">Show Report</button>-->
                   <!--     </div>-->
                   <!--</div>-->
            </div>
        </form>
    </div>
    <div class="col-md-2"></div>
</div>


<!-- 7 Date Wise New Employee Insert Report -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <form class="form-horizontal" target="_blank" action="{{ route('employee.list.new-emp-insert-details.by-date-to-date') }}"
            method="post">
            @csrf
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title card_top_title salary-generat-heading">7. Inserted New Employees</h3>
                </div>
                <div class="card-body card_form" style="padding-top: 0;">

                <div
                    class="form-group row custom_form_group{{ $errors->has('from_date') ? ' has-error' : '' }}">
                    <label class="col-sm-3 control-label">From Date:<span class="req_star">*</span></label>
                    <div class="col-sm-6">
                        <input type="date" class="form-control" name="from_date"
                            value="<?= date("Y-m-d") ?>" max="{{ Carbon\Carbon::now()->format('Y-m-d') }}" required>
                    </div>
                </div>

                <div  class="form-group row custom_form_group{{ $errors->has('todate') ? ' has-error' : '' }}">
                    <label class="col-sm-3 control-label">To Date:<span class="req_star">*</span></label>
                    <div class="col-sm-6">
                        <input type="date" class="form-control" name="todate" value="{{ Carbon\Carbon::now()->format('Y-m-d') }}"
                          required>
                    </div>
                </div>

                <!--Sponsor List Dropdown -->
                <div class="form-group row custom_form_group">
                        <label class="control-label col-md-3">Sponsor:</label>
                        <div class="col-md-6">
                            <select class="selectpicker" name="spons_id[]" multiple>
                                @foreach($sponser as $spons)
                                <option value="{{ $spons->spons_id }}">{{ $spons->spons_name }}</option>
                                @endforeach
                            </select>
                        </div>
                </div>

                <!-- Employee type List 1 = direct, 2 = indirect -->

                <div class="form-group row custom_form_group">
                    <label class="control-label col-md-3"> Employee Type:</label>
                    <div class="col-md-6">
                        <select class="form-control" name="emp_type_id" required>
                            <option value="-1">All</option>
                            <option value="1">Direct Employee (Basic Salary)</option>
                            <option value="2">Indirect Employee</option>
                            <option value="3">Basic Salary (Direct & Indirect) Employees</option>
                            <option value="4">Direct Employee (Hourly)</option>
                        </select>
                    </div>
                </div>

                <div class="form-group row custom_form_group">
                    <label class="control-label col-md-3">Report Type:</label>
                    <div class="col-md-6">
                        <select class="form-control" name="report_type" required>
                            <option value="1">Employee List(Print)</option>
                            <option value="2">Employee List(Excell)</option>
                            <option value="3">Summary By Sponsor</option>
                        </select>
                    </div>
                    <div class="col-sm-3">
                        @can('new_employee_insertion_report')
                            <button type="submit" class="btn btn-primary waves-effect">Show Report</button>
                        @endcan
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="col-md-2"></div>
</div>


<!-- 8 Date to Date Vacation Emp Report -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <form class="form-horizontal" target="_blank" action="{{ route('employee.activity.report') }}"
            method="post">
            @csrf
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title card_top_title salary-generat-heading">8. Vacation/Final Exit Employees Report</h3>
                </div>
                <div class="card-body card_form" style="padding-top: 0;">

                <div
                    class="form-group row custom_form_group{{ $errors->has('from_date') ? ' has-error' : '' }}">
                    <label class="col-sm-3 control-label">From Date:<span class="req_star">*</span></label>
                    <div class="col-sm-6">
                        <input type="date" class="form-control" name="from_date"
                            value="<?= date("Y-m-d") ?>" max="{{ Carbon\Carbon::now()->format('Y-m-d') }}" required>
                    </div>
                </div>

                <div  class="form-group row custom_form_group{{ $errors->has('today') ? ' has-error' : '' }}">
                    <label class="col-sm-3 control-label">To Date:<span class="req_star">*</span></label>
                    <div class="col-sm-6">
                        <input type="date" class="form-control" name="to_date" value="{{ Carbon\Carbon::now()->format('Y-m-d') }}"
                        max="{{ Carbon\Carbon::now()->format('Y-m-d') }}" required>
                    </div>
                </div>
                    <div class="form-group row custom_form_group">
                        <label class="control-label col-md-3">Job Status:</label>
                        <div class="col-md-6">
                            <select class="form-control" name="job_status">
                                  <option value="1">Active</option>
                                  <option value="2">InActive</option>
                                   <option value="3">Final_Exit</option>
                                    <option value="4">Release</option>
                                     <option value="5">Vacation</option>
                                     <option value="6">Run_Away</option>
                                      <option value="7">VISA_Cancel</option>
                                       <option value="8">Final_Exit_and_Return_Back</option>

                            </select>
                        </div>
                        <!-- <div class="col-sm-3">-->
                        <!--    <button type="submit" class="btn btn-primary waves-effect">Show Report</button>-->
                        <!--</div>-->
                    </div>

                     <div class="form-group row custom_form_group">
                    <label class="control-label col-md-3">Report Type</label>
                    <div class="col-md-6">
                        <select class="form-control" name="report_type" required>
                              <option value="1">Employees View </option>
                            <option value="2">Employees Excel Download</option>
                            <option value="3">Monthly Summary</option>
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <button type="submit" class="btn btn-primary waves-effect">Show Report</button>
                    </div>
                </div>


            </div>
        </form>
    </div>
    <div class="col-md-2"></div>
</div>

<!-- 9  Hourly & Basic Salary Employee List by Type & project wise Report -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <form class="form-horizontal" target="_blank" action="{{ route('hr.employee.typewise.report.process') }}" method="post">
            @csrf
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title card_top_title salary-generat-heading">9. Hourly & Basic Salary Employee List</h3>
                </div>
                <div class="card-body card_form" style="padding-top: 0;">

                    <!--Project List Dropdown -->
                    <div class="form-group row custom_form_group">
                        <label class="control-label col-md-3">Project Name:</label>
                        <div class="col-md-6">
                            <select class="selectpicker" name="project_id_list[]" multiple>
                                @foreach($projects as $proj)
                                <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!--Sponsor List Dropdown -->
                    <div class="form-group row custom_form_group">
                        <label class="control-label col-md-3">Sponsor Name:</label>
                        <div class="col-md-6">
                            <select class="selectpicker" name="spons_id[]" multiple>
                                @foreach($sponser as $spons)
                                <option value="{{ $spons->spons_id }}">{{ $spons->spons_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <!-- Employee Salary Type -->
                    <div class="form-group row custom_form_group">
                        <label class="control-label col-md-3"> Employee Type:</label>
                        <div class="col-md-6">
                            <select class="form-control" name="emp_type_id" required>
                                <option value="-1">All</option>
                                <option value="1">Direct Employee (Basic Salary)</option>
                                <option value="2">Direct Employee (Hourly)</option>
                                <option value="3">Indirect Employee</option>
                                <option value="4">Basic Salary (Direct & Indirect) Employees</option>

                            </select>
                        </div>
                    </div>
                    <!--Report Format Dropdown -->
                    <div class="form-group row custom_form_group">
                        <label class="control-label col-md-3"> Report Format:</label>
                        <div class="col-md-6">
                            <select class="form-control" name="report_format" required>
                                <option value="1">Print</option>
                                <option value="2">Excell</option>
                                <option value="3">CSV</option>
                            </select>
                        </div>
                        <div class="col-sm-3">

                        </div>
                    </div>
                    <div class="form-group row custom_form_group">
                        <label class="control-label col-md-3"> Report Type:</label>
                        <div class="col-md-6">
                            <select class="form-control" name="report_format" required>
                                <option value="1">List</option>
                                <option value="2">Summary</option>
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <button type="submit" class="btn btn-primary waves-effect">Show Report</button>
                        </div>
                    </div>
            </div>
        </form>
    </div>
    <div class="col-md-2"></div>
</div>



 <!-- 10 Employee TUV Informations Report -->
 <div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <form class="form-horizontal" id="registration" target="_blank" action="{{ route('hr.report.employee_tuv_infos') }}" method="post">
          @csrf
          <div class="card">
            <div class="card-header">
                <h3 class="card-title card_top_title salary-generat-heading">9. TUV Information </h3>
            </div>
              <div class="card-body card_form" style="padding-top: 10;">
                <div class="row">
                    <div class="col-md-2"></div>
                    <div class="col-md-8">
                      <div class="form-group row custom_form_group">
                          <label class="control-label col-md-6" style="text-align: left;">TUV Card Holder Employees</label>
                          <div class="col-md-4">
                                  <button type="submit" class="btn btn-primary waves-effect">Show Report </button>
                          </div>
                      </div>
                    </div>
                    <div class="col-md-2"></div>
                </div>
              </div>
          </div>
        </form>
      </div>
    <div class="col-md-2"></div>
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

<!-- form validation -->
<script type="text/javascript">

    $(document).ready(function () {

        $('#increment_form_modal').on('shown.bs.modal', function (e) {
             $('#employee_id').focus();
        })

        $('#increment_form_modal').on('hidden.bs.modal', function (e) {
            $(this)
            .find("input,textarea,select").val('').end()
            .find("input[type=checkbox], input[type=radio]").prop("checked", "").end();
        })

    });

</script>
@endsection
