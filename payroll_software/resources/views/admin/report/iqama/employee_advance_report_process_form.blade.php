@extends('layouts.admin-master')
@section('title') Advance Report @endsection
@section('content')
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Advance Report</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active"> Advance History</li>
        </ol>
    </div>
</div>
<br>
<!-- Alert Part Start -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        @if(Session::has('no_record'))
        <div class="alert alert-warning alerterror" role="alert">
            <strong>{{ Session::get('no_record') }}</strong>
        </div>
        @endif
    </div>
</div>


<!-- Employees Advance Paper Create Section -->
<div class="row" id="employees_advance_paper_create_section">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <div class="card">
            <form class="form-horizontal" id="employees_advance_paper_form" target="_blank" action="{{ route('emp.advance.papers.create.request')}}" method="POST">
                @csrf
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-9">
                            <h3 class="card-title card_top_title salary-generat-heading">#1 Creating Employees Advance Papers</h3>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ url('uploads/all_form/advance_blank_paper.pdf') }}" target="_blank">Advance Paper (Blank)</a>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
                <div class="card-body card_form" style="padding-top: 0;">
                    
                            <div class="form-group row custom_form_group">
                                <label class="col-sm-4 control-label">Amount</label>
                                <div class="col-sm-2">
                                    <input type="number" class="form-control" id="adv_amount" placeholder="Enter Amount Here" name="adv_amount"  autofocus >
                                </div>
                                <label class="col-sm-2 control-label">Date<span class="req_star">*</span></label>
                                <div class="col-sm-4">
                                    <input type="date" class="form-control" name="adv_date" value="<?= date("Y-m-d") ?>">
                                </div>
                            </div>  
                            
                            <div class="form-group row custom_form_group">
                                <label class="col-sm-4 control-label">Emp ID</label>
                                <div class="col-sm-8">
                                <input type="text" class="form-control" id="emp_id"
                                placeholder="Multiple Emp ID Type Here (Separeate by comma ,)" name="adv_emp_ids"
                                autofocus="autofocus">
                                </div>
                            </div>
                            <!--<h4 style="text-align:center"> OR </h4>-->
                       
                            <!--<div class="form-group row custom_form_group">-->
                            <!--    <label class="col-sm-4 control-label">Project:</label>-->
                            <!--    <div class="col-sm-8">-->
                            <!--        <select class="form-control" id="proj_name" name="proj_name">-->
                            <!--            <option value="">Select Project Name</option>-->
                            <!--            @foreach($projects as $proj)-->
                            <!--            <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>-->
                            <!--            @endforeach-->
                            <!--        </select>-->
                            <!--    </div>-->
                            <!--</div>-->
                    

                       
                            <div class="form-group row custom_form_group">
                                <!--<label class="col-sm-4 control-label">Villa Name:</label>-->
                                <!--<div class="col-sm-6">-->
                                <!--    <select class="form-control" name="acomdOfbId">-->
                                <!--        <option value="">Select Villa Name</option>-->
                                <!--        @foreach($accomdOfficeBuilding as $ofb)-->
                                <!--        <option value="{{ $ofb->ofb_id }}">{{ $ofb->ofb_name }}</option>-->
                                <!--        @endforeach-->
                                <!--    </select>-->
                                <!--</div>-->
                                <div class="col-sm-8"> </div>  
                                <div class="col-sm-2"><button type="submit" class="btn btn-primary waves-effect">Create Paper</button></div>                                
                            </div>  
                            
                    <!-- </div> -->
                </div>
            </form>
        </div>
    </div>
    <div class="col-md-2"></div>
</div>

<!--  Single Employee Advance Report -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <form class="form-horizontal" id="iqama-report-ui" target="_blank"
            action="{{ route('emp.addvance.report.process') }}" method="post">
            @csrf
            
            
             <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-12">
                            <h3 class="card-title card_top_title salary-generat-heading">#2 An Employee All or Multiple Employees Last Advance Report</h3>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
                <div class="card-body card_form" style="padding-top: 0;">

                    <div class="row form-group custom_form_group{{ $errors->has('empID') ? ' has-error' : '' }}">

                        <label class="col-md-4 control-label d-block">Employee ID </label>
                        <div class="col-md-6">
                            <input type="text" placeholder="Enter Multiple Employee ID Here" class="form-control"
                                id="empl_id" name="empl_id"  
                                onkeyup="typingEmployeeSearchingValue()" autofocus="autofocus" required>
                            <span id="employee_not_found_error_show" class="d-none" style="color: red"></span>
                            @if ($errors->has('empl_id'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('empl_id') }}</strong>
                            </span>
                            @endif
                        </div>                                             
                    </div>
                    <div class="form-group row custom_form_group">
                        <label class="control-label col-sm-4">Report Type:</label>
                        <div class="col-sm-4">
                            <select class="form-select" id="report_type">                               
                                <option value="1"> An Employee All Records</option>
                                <option value="2">Employee Last Advance</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="button" id="empAdvaceRecordReport" onclick="showSingleEmployeeAdvanceReport()" class="btn btn-primary waves-effect">Show Report</button>
                        </div> 
                    </div>

                </div>
            </div>
            
        </form>
    </div>
    <div class="col-md-2"></div>
</div>

<!-- Search For Employee Advance Report Start -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <form class="form-horizontal" id="iqama-report-ui" target="_blank"
            action="{{ route('emp.addvance.report.process') }}" method="post">
            @csrf
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-12">
                            <h3 class="card-title card_top_title salary-generat-heading">#3 Employee Advance Report</h3>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
                <div class="card-body card_form" style="padding-top: 0;">

                    {{-- Project List --}}
                    <div class="form-group row custom_form_group">
                        <label class="control-label col-sm-4">Project:</label>
                        <div class="col-sm-6">
                            <select class="form-control" name="proj_id">
                                <option value=""> All Project</option>
                                @foreach($projects as $proj)
                                <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row custom_form_group{{ $errors->has('start_date') ? ' has-error' : '' }}">
                        <label class="col-sm-4 control-label">Start Date:<span class="req_star">*</span></label>
                        <div class="col-sm-6">
                            <input type="date" class="form-control" name="start_date" value="<?= date("Y-m-d") ?>">
                        </div>
                    </div>

                    <div class="form-group row custom_form_group{{ $errors->has('end_date') ? ' has-error' : '' }}">
                        <label class="col-sm-4 control-label">End Date:<span class="req_star">*</span></label>
                        <div class="col-sm-6">
                            <input type="date" class="form-control" name="end_date" value="<?= date("Y-m-d") ?>">
                        </div>
                    </div>

                    <div class="form-group row custom_form_group">
                        <label class="col-sm-4 control-label">Report Type</label>
                        <div class="col-sm-6">
                            <select class="form-control" name="report_type">
                                <option value="1">Advance payment datewise </option>
                                <option value="2">Month by Month Advance & Deduction Summary</option>
                                <option value="3">Employees Advance & Deduction Summary</option>
                                <option value="4">Year to Year Advance Summary</option>
                                <option value="5">Unpaid Employee List</option>
                                <option value="6">Advance Insert datewise </option>
                                <option value="7">Employee Other Advance Summary </option>
                            </select>
                        </div>
                        <div class="col-md-2"> 
                             @can('all_emp_advance_summary_report')
                                <button type="submit" class="btn btn-primary waves-effect">Show Report</button>
                            @endcan </div>
                    </div>
                </div>                 
            </div>
        </form>
    </div>
    <div class="col-md-2"></div>
</div>

<!-- Search Advance By Entry User -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <form class="form-horizontal" id="iqama-report-ui" target="_blank"
            action="{{ route('employee.advance.inserted.byuser.report') }}" method="post">
            @csrf
            <div class="card">                
                    <h3 class="card-title card_top_title salary-generat-heading">#4 Advance Inserted By</h3> 
                    {{-- User List --}}
                    <div class="form-group row custom_form_group">
                        <label class="control-label col-sm-2">Inserted By:</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="user_id"> 
                             <option value="">Select One </option>
                                @foreach($users as $auser)
                                <option value="{{ $auser->id }}"> {{ $auser->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row custom_form_group{{ $errors->has('start_date') ? ' has-error' : '' }}">
                        <label class="col-sm-2 control-label">From Date:<span class="req_star">*</span></label>
                        <div class="col-sm-4">
                            <input type="date" class="form-control" name="start_date" value="<?= date("Y-m-d") ?>">
                        </div>
                        <label class="col-sm-2 control-label">End Date:<span class="req_star">*</span></label>
                        <div class="col-sm-4">
                            <input type="date" class="form-control" name="end_date" value="<?= date("Y-m-d") ?>">
                        </div>
                    </div>

                    <div class="form-group row custom_form_group">                         
                        <div class="col-sm-10">                          
                        </div>
                        <div class="col-md-2"> 
                             @can('all_emp_advance_summary_report')
                                <button type="submit" class="btn btn-primary waves-effect">Show Report</button>
                            @endcan </div>
                    </div>
                </div>                 
            </div>
        </form>
    </div>
    <div class="col-md-2"></div>
</div>






<script type="text/javascript">

    // document.getElementById('empAdvaceRecordReport').addEventListener('click', function() {       
    //     showSingleEmployeeAdvanceReport();
    // });

    $('#empl_id').keydown(function (e) {
        if (e.keyCode == 13) { // Enter Key press
            showSingleEmployeeAdvanceReport();
        }
    })

    function showSingleEmployeeAdvanceReport(){

        var empID = $("#empl_id").val();
        var report_type = $("#report_type").val();
        const queryString = new URLSearchParams({
            empID: empID,
            report_type:report_type,
        }).toString();

        var parameterValue = queryString; // Set parameter value
        var url = "{{ route('single.employee.advance.records.report', ':parameter') }}";
        url = url.replace(':parameter', parameterValue);
        window.open(url, '_blank');
    }



</script>
@endsection
