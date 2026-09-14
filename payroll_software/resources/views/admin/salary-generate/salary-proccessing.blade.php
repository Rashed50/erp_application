@extends('layouts.admin-master')
@section('title')
    Salary Processing
@endsection
@section('content')


    <style>
        .overlay {
            display: none;
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: 999;
            background: rgba(255, 255, 255, 0.8) url('{{ asset('Loading.gif') }}') center no-repeat;
        }

        /* Turn off scrollbar when body element has the loading class */
        body.loading {
            overflow: hidden;
        }

        /* Make spinner image visible when body element has the loading class */
        body.loading .overlay {
            display: block;
        }
    </style>
    <div class="row bread_part">
        <div class="col-sm-12 bread_col">
            <h4 class="pull-left page-title bread_title"> Salary Processing </h4>
            <ol class="breadcrumb pull-right">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="active"> Salary Processing</li>
            </ol>
        </div>
    </div>



    <!-- response Message -->
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8">

            @if (Session::has('success'))
                <div class="alert alert-success alertsuccess" role="alert">
                    <strong> {{ Session::get('success') }}</strong>
                </div>
            @endif
            @if (Session::has('error'))
                <div class="alert alert-warning alerterror" role="alert">
                    <strong> {{ Session::get('error') }}</strong>
                </div>
            @endif



        </div>
        <div class="col-md-2"></div>
    </div>


    <?php $current_month = date('m');
    $months = [12];
    for ($m = 1; $m <= 12; $m++) {
        $months[$m - 1] = date('F', mktime(0, 0, 0, $m, 1, date('Y')));
    }
    ?>

    <!-- Salary Record Processing -->
    <div class="row">
        <div class="col-md-1"></div>
        <div class="col-lg-10">
            <div class="card">
                <div class="card-header">
                    <div class="col-md-12">
                        <h3 class="card-title card_top_title salary-generat-heading">Employees Salary Processing</h3>
                    </div>
                </div>
                <div class="card-body card_form" style="padding-top: 0;">
                    {{-- Project List --}}
                    <div class="form-group row custom_form_group">
                        <label class="col-sm-3 control-label">Project:<span class="req_star">*</span></label>
                        <div class="col-sm-9">
                            <select class="selectpicker" name="proj_ids" id="proj_ids" multiple required>
                                @foreach ($projects as $proj)
                                    <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row custom_form_group">
                        <label class="col-sm-3 control-label">Sponsor:</label>
                        <div class="col-sm-6">
                            <select class="selectpicker" id="sponsor_ids" multiple="multiple">
                                <option value="1">Asloob Sponsor</option>
                                {{-- <option value="2">Others Sponsor</option> --}}

                                @foreach ($sponsor_for_salary_process as $spons)
                                    <option value="{{ $spons->spons_id }}">{{ $spons->spons_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3  d-none">
                            <input type="checkbox" id="wps_employees_checkbox" name="wps_employees_checkbox" value="1">
                            <label for="projec_cost_check">WPS Employees</label><br><br>
                        </div>
                    </div>
                    {{-- Month List --}}
                    <div class="form-group row custom_form_group">
                        <label class="col-sm-3 control-label">Month:<span class="req_star">*</span></label>
                        <div class="col-sm-4">
                            <select class="form-select" name="month" id="month">
                                @foreach ($month as $data)
                                    <option value="{{ $data->month_id }}"
                                        {{ $data->month_id == $currentMonth ? 'selected' : '' }}>{{ $data->month_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        {{-- Year List --}}
                        <label class="col-sm-2 control-label"> Year:<span class="req_star">*</span></label>
                        <div class="col-sm-3">
                            <select class="form-select" name="year" id="year">
                                @foreach (range(date('Y'), date('Y') - 1) as $y)
                                    <option value="{{ $y }}" {{ $y }}>{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                </div>
                <div class="card-footer card_footer_button text-center">
                    @can('salary_processing_permission')
                        <button onclick="EmployeeSalaryProcessing()" class="btn btn-primary btn-sm emp-sarch">Process</button>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    @endcan
                    @can('salary_processing_history_report')
                        <button onclick="showSalaryProcessHistory()" class="btn btn-primary btn-sm emp-sarch">Processing
                            History</button> <br><br>
                    @endcan
                </div>
            </div>

        </div>
        <div class="col-md-1"></div>
    </div>

    <!--1 Employee Id base Salary Report -->
    <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-10">
            <form class="form-horizontal" id="registration" target="_blank"
                action="{{ route('single-employee-salary-generat') }}" method="post">
                @csrf
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-12">
                                <h3 class="card-title card_top_title salary-generat-heading">1. Processing Multiple Employee
                                    Salary</h3>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    <div class="card-body card_form" style="padding-top: 0;">

                        <div class="form-group row custom_form_group{{ $errors->has('emp_id') ? ' has-error' : '' }}">
                            <label class="control-label col-sm-2">Employee ID:</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control  typeahead"
                                    placeholder="Type Multiple Employee ID" name="emp_id" id="emp_id_search"
                                    value="{{ old('emp_id') }}">
                                @if ($errors->has('emp_id'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('emp_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div id="showEmpId"></div>
                        </div>

                        {{-- Month Year List --}}
                        <div class="form-group row custom_form_group{{ $errors->has('month') ? ' has-error' : '' }}">
                            <label class="control-label col-sm-2">Salary Month:</label>
                            <div class="col-sm-3">
                                <select class="form-select" name="month">
                                    @foreach ($month as $item)
                                        <option value="{{ $item->month_id }}"
                                            {{ $item->month_id == $current_month ? 'selected' : '' }}>
                                            {{ $item->month_name }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('month'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('month') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <label class="control-label col-sm-2">Year:</label>
                            <div class="col-sm-2">
                                <select class="form-select" name="year">
                                    @foreach (range(date('Y'), date('Y') - 3) as $y)
                                        <option value="{{ $y }}" {{ $y }}>{{ $y }}
                                        </option>
                                    @endforeach
                                </select>

                                @if ($errors->has('year'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('year') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>



                    </div>
                    <div class="card-footer card_footer_button text-center">
                        @can('salary-processing')
                            <button type="submit" class="btn btn-primary waves-effect">Process & Print</button>
                        @endcan
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-1"></div>
    </div>

    <!--2 Project & Employee Status Wise Salary Report -->
    <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-10">
            <form class="form-horizontal" id="registration" target="_blank"
                action="{{ route('project-month-empStatus.wise-salary') }}" method="post">
                @csrf
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-12">
                                <h3 class="card-title card_top_title salary-generat-heading">2. Projet & Employee Status
                                    base Salary Report</h3>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    <div class="card-body card_form" style="padding-top: 0;">

                        {{-- Project List --}}
                        <div class="form-group row custom_form_group">
                            <label class="control-label col-sm-2">Project Name:</label>
                            <div class="col-sm-8">
                                <select class="form-select" name="proj_id" required>
                                    @foreach ($projects as $proj)
                                        <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Employee Status --}}
                        <div class="form-group custom_form_group">
                            <div>
                                <select class="form-control" name="emp_status_id" hidden required>
                                    <option value="1">Active</option>
                                </select>
                            </div>
                        </div>

                        {{-- Month Year List --}}
                        <div class="form-group row custom_form_group{{ $errors->has('month') ? ' has-error' : '' }}">
                            <label class="control-label col-sm-2">Salary Month1:</label>
                            <div class="col-sm-2">
                                <select class="form-select" name="month">
                                    @foreach ($month as $item)
                                        <!--<option value="{{ $item->month_id }}" {{ $item->month_id == $current_month ? 'selected' : '' }}>{{ $item->month_name }}</option>-->
                                        <option value="{{ $item->month_id }}"
                                            {{ $item->month_id == $current_month ? 'selected' : '' }}>
                                            {{ $item->month_name }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('month'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('month') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <label class="control-label col-sm-2">Year:</label>
                            <div class="col-sm-4">
                                <select class="form-select" name="year">
                                    @foreach (range(date('Y'), date('Y') - 4) as $y)
                                        <option value="{{ $y }}" {{ $y }}>{{ $y }}
                                        </option>
                                    @endforeach
                                </select>

                                @if ($errors->has('year'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('year') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row custom_form_group">
                            <label class="control-label col-sm-2">Salary Status</label>
                            <div class="col-sm-2">
                                <select class="form-select" name="salary_status">
                                    <option value="">All</option>
                                    <option value="0">Unpaid</option>
                                    <option value="1">Paid</option>

                                </select>
                            </div>
                            <label class="control-label col-sm-2">Report Type</label>
                            <div class="col-sm-4">
                                <select class="form-select" name="salary_report_type" required>
                                    <option value="3">Projectwise Paid/Unpaid</option>
                                    <option value="1">Employee now working</option>
                                    <option value="6">Basic Emp. now working</option>
                                    <option value="7">Hourly Emp. now working</option>
                                    <option value="8">Inactive but have salary All Projects</option>
                                    <option value="2">Employee Last Salary</option>
                                    <option value="4">1 or More Salary Unpaid Employees</option>
                                    <option value="5">2 or More Salary Unpaid Employees</option>
                                </select>
                            </div>

                        </div>

                    </div>
                    <div class="card-footer card_footer_button text-center">
                        <button type="submit" class="btn btn-primary waves-effect">Report Show </button>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-1"></div>
    </div>



    <!--3 Project & Employee Type (Basic Salary, Hourly,Direct) salary Report -->
    <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-10">
            <form class="form-horizontal" id="registration" target="_blank"
                action="{{ route('project-month-empType.wise-salary') }}" method="post">
                @csrf
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-12">
                                <h3 class="card-title card_top_title salary-generat-heading">3. Projet & Employee type base
                                    Salary Report</h3>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    <div class="card-body card_form" style="padding-top: 0;">


                        <div class="card-body card_form" style="padding-top: 0;">

                            {{-- Project List and emp type --}}
                            <div class="form-group row custom_form_group">

                                <label class="control-label col-sm-2"> Project:</label>
                                <div class="col-sm-4">
                                    <select class="form-select" name="proj_id">
                                        <option value="">All Project</option>
                                        @foreach ($projects as $proj)
                                            <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <label class="control-label col-sm-2"> Employee Type:</label>
                                <div class="col-sm-4">
                                    {{-- Employee type List 1 = direct, 2 = indirect --}}
                                    <select class="form-select" name="emp_type_id" required>
                                        <option value="0">All Type</option>
                                        <option value="-2">Direct Employee(Basic & Hourly)</option>
                                        <option value="-1">Direct Employee (Basic Salary)</option>
                                        <option value="1">Hourly Employee</option>
                                        <option value="2">Indirect Employee</option>
                                        <option value="3">Basic Salary (Direct & Indirect) Employees</option>

                                    </select>
                                </div>
                            </div>

                            {{-- Month and year List --}}
                            <div class="form-group row custom_form_group">
                                <label class="control-label col-sm-2">Month:</label>
                                <div class="col-sm-4"> <select class="form-select" name="month" required>
                                        @foreach ($month as $item)
                                            <option value="{{ $item->month_id }}"
                                                {{ $item->month_id == Carbon\Carbon::now()->format('m') ? 'selected' : '' }}>
                                                {{ $item->month_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <label class="control-label col-sm-2">Year:</label>
                                <div class="col-sm-4">
                                    <select class="form-select" name="year">
                                        @foreach (range(date('Y'), date('Y') - 4) as $y)
                                            <option value="{{ $y }}" {{ $y }}>{{ $y }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row custom_form_group">
                                <label class="control-label col-md-2">Status</label>
                                <div class="col-md-4">
                                    <select class="selectpicker" name="salary_status[]" multiple required>
                                        <option value="0">Salary Unpaid</option>
                                        <option value="1">Salary Paid</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <input type="checkbox" id="projec_cost_check" name="projec_cost_check"
                                        value="1">
                                    <label for="projec_cost_check">Only Project Work Salary</label><br><br>
                                    <input type="checkbox" id="paid_unpaid_show" name="paid_unpaid_show" value="1">
                                    <label for="paid_unpaid_show">Salary Status Show in Salary Sheet</label><br>
                                </div>
                            </div>
                        </div>


                    </div>
                    <div class="card-footer card_footer_button text-center">
                        <button type="submit" class="btn btn-primary waves-effect">Report Show </button>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-1"></div>
    </div>


    <!-- 3.1 Trade Base Salary Report -->
    <div class="row">
        <div class="col-md-1"></div>
        <div class="col-lg-10">
            <form class="form-horizontal" id="trade_base_salary_report_from" target="_blank"
                action="{{ route('salary.report.project.and.trade') }}" method="post">
                @csrf
                <div class="card">
                    <div class="card-header">
                        <div class="col-md-12">
                            <h3 class="card-title card_top_title salary-generat-heading">3.1 Trade Base Salary Report</h3>
                        </div>
                    </div>
                    <div class="card-body card_form">

                        {{-- Project List --}}
                        <div class="form-group row custom_form_group">
                            <label class="col-sm-3 control-label">Project:<span class="req_star">*</span></label>
                            <div class="col-sm-6">
                                <select class="selectpicker" name="proj_idss[]" id="proj_idss[]" multiple>
                                    @foreach ($projects as $proj)
                                        <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row custom_form_group">
                            <label class="col-sm-3 control-label">Trade:</label>
                            <div class="col-sm-6">
                                <select class="selectpicker" id="trade_idss[]" name="trade_idss[]" multiple="multiple">
                                    @foreach ($trades as $tn)
                                        <option value="{{ $tn->catg_id }}">{{ $tn->catg_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        {{-- Month List --}}
                        <div class="form-group row custom_form_group">
                            <label class="col-sm-3 control-label">Month:<span class="req_star">*</span></label>
                            <div class="col-sm-3">
                                <select class="form-select" name="month" id="month">
                                    @foreach ($month as $data)
                                        <option value="{{ $data->month_id }}"
                                            {{ $data->month_id == $currentMonth ? 'selected' : '' }}>
                                            {{ $data->month_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- Year List --}}
                            <label class="col-sm-2 control-label"> Year:<span class="req_star">*</span></label>
                            <div class="col-sm-2">
                                <select class="form-select" name="year" id="year">
                                    @foreach (range(date('Y'), date('Y') - 4) as $y)
                                        <option value="{{ $y }}" {{ $y }}>{{ $y }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row custom_form_group">
                            <label class="control-label col-sm-3">Status</label>
                            <div class="col-sm-3">
                                <select class="selectpicker" name="salary_statuss[]" multiple>
                                    <option value="0">Unpaid</option>
                                    <option value="1">Paid</option>
                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="card-footer card_footer_button text-center">
                        <button type="submit" class="btn btn-primary waves-effect">Report Show </button>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-1"></div>
    </div>



    <!--4 Sponser Wise Salary Generate -->
    @can('sponsor_salary_report_menu')
        <div class="row">
            <div class="col-md-1"></div>
            <div class="col-md-10">
                <form class="form-horizontal" id="registration" target="_blank"
                    action="{{ route('sponser-month.wise-salary-report') }}" method="post">
                    @csrf
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-12">
                                    <h3 class="card-title card_top_title salary-generat-heading">4. Sponsor base Salary Report
                                    </h3>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <div class="card-body card_form" style="padding-top: 0;">


                            {{-- Sponser & Project List --}}
                            <div class="form-group row custom_form_group">
                                <label class="control-label col-sm-2">Sponsor Name:</label>
                                <div class="col-sm-3">
                                    <select class="selectpicker" name="SponsId[]" multiple>
                                        @foreach ($sponser as $spons)
                                            <option value="{{ $spons->spons_id }}">{{ $spons->spons_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <label class="control-label col-sm-2">Project Name:</label>
                                <div class="col-sm-3">
                                    <select class="selectpicker" name="proj_id[]" multiple>
                                        @foreach ($projects as $proj)
                                            <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row custom_form_group{{ $errors->has('month') ? ' has-error' : '' }}">
                                <label class="control-label col-sm-2">Salary Month:</label>
                                <div class="col-sm-3">
                                    <select class="form-select" name="month">
                                        @foreach ($month as $item)
                                            <option value="{{ $item->month_id }}"
                                                {{ $item->month_id == $current_month ? 'selected' : '' }}>
                                                {{ $item->month_name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('month'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('month') }}</strong>
                                        </span>
                                    @endif
                                </div>

                                <label class="control-label col-sm-2">Year:</label>
                                <div class="col-sm-3">
                                    <select class="form-select" name="year">
                                        @foreach (range(date('Y'), date('Y') - 4) as $y)
                                            <option value="{{ $y }}" {{ $y }}>{{ $y }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @if ($errors->has('year'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('year') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row custom_form_group">
                                <label class="control-label col-sm-2">Salary Status</label>
                                <div class="col-sm-3">
                                    <select class="selectpicker" name="salary_status[]" multiple required>
                                        <option value="0">Unpaid</option>
                                        <option value="1">Paid</option>
                                    </select>
                                </div>
                            </div>


                        </div>
                        <div class="card-footer card_footer_button text-center">
                            <button type="submit" class="btn btn-primary waves-effect">Report Show </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-1"></div>
        </div>
    @endcan


    <!--5  Report For Saudi By Sponsor  -->
    <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-10">
            <form class="form-horizontal" id="registration" target="_blank"
                action="{{ route('sponser.salary.month.saudi.salary.report') }}" method="post">
                @csrf
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-12">
                                <h3 class="card-title card_top_title salary-generat-heading">5. Salary Report For Saudi By
                                    Sponsor </h3>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    <div class="card-body card_form" style="padding-top: 0;">

                        {{-- Sponser List --}}
                        <div class="form-group row custom_form_group">
                            <label class="col-sm-2 control-label">Sponser Name:</label>
                            <div class="col-sm-6">
                                <select class="selectpicker" name="sponsor_id_list[]" multiple>
                                    @foreach ($sponser as $spons)
                                        <option value="{{ $spons->spons_id }}">{{ $spons->spons_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Month & Year --}}

                        <div class="form-group row custom_form_group{{ $errors->has('month') ? ' has-error' : '' }}">
                            <label class="control-label col-sm-2">Salary Month:</label>
                            <div class="col-sm-3">
                                <select class="form-select" name="month">
                                    @foreach ($month as $item)
                                        <option value="{{ $item->month_id }}"
                                            {{ $item->month_id == $current_month ? 'selected' : '' }}>
                                            {{ $item->month_name }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('month'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('month') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <label class="control-label col-sm-2">Year:</label>
                            <div class="col-sm-3">
                                <select class="form-select" name="year">
                                    @foreach (range(date('Y'), date('Y') - 4) as $y)
                                        <option value="{{ $y }}" {{ $y }}>{{ $y }}
                                        </option>
                                    @endforeach
                                </select>

                                @if ($errors->has('year'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('year') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                    </div>
                    <div class="card-footer card_footer_button text-center">
                        <button type="submit" class="btn btn-primary waves-effect">Report Show </button>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-1"></div>
    </div>

    <!--6 Month wise All Employee Salary with PAID/Unpaid -->
    <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-10">
            <form class="form-horizontal" id="registration" target="_blank"
                action="{{ route('all-emp.salary-without-project-emp-type') }}" method="post">
                @csrf
                <div class="card">

                    <h3 class="card-title card_top_title salary-generat-heading">6. All Employees Salary Report</h3>

                    <div class="card-body card_form" style="padding-top: 0;">

                        {{-- Month List --}}
                        <div class="form-group row custom_form_group">
                            <label class="col-sm-2 control-label d-block">Salary Month:</label>
                            <div class="col-sm-6">
                                <select class="form-select" name="month" required>
                                    @foreach ($month as $item)
                                        <option value="{{ $item->month_id }}"
                                            {{ $item->month_id == Carbon\Carbon::now()->format('m') ? 'selected' : '' }}>
                                            {{ $item->month_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>


                        {{-- Year List --}}
                        <div class="form-group row custom_form_group{{ $errors->has('year') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label d-block">Salary Year:</label>
                            <div class="col-sm-6">
                                <select class="form-select" name="year">
                                    @foreach (range(date('Y'), date('Y') - 4) as $y)
                                        <option value="{{ $y }}" {{ $y }}>{{ $y }}
                                        </option>
                                    @endforeach
                                </select>

                                @if ($errors->has('year'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('year') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row custom_form_group">
                            <label class="col-sm-2 control-label d-block">Salary Status</label>
                            <div class="col-sm-6">
                                <select class="form-select" name="salary_status" required>
                                    <option value="0">Unpaid</option>
                                    <option value="1">Paid</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row custom_form_group">
                            <label class="col-sm-2 control-label d-block">Report Type:</label>
                            <div class="col-sm-6">
                                <select class="form-select" name="report_type" id="report_type" required>
                                    <option value="1">Employee List</option>
                                    <option value="2">Year by Year Summary</option>
                                    <option value="3">Project base Summary</option>

                                </select>
                            </div>
                        </div>

                    </div>

                    {{-- Month List --}}
                    <div class="card-footer card_footer_button text-center">
                        <button type="submit" class="btn btn-primary waves-effect">Report Show </button>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-1"></div>
    </div>


    <!--7 Monthly Paid/Unpaid Salary Summary -->
    <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-10">
            <form class="form-horizontal" id="registration" target="_blank"
                action="{{ route('year-month.wise-salary-summary') }}" method="post">
                @csrf
                <div class="card">
                    <h3 class="card-title card_top_title salary-generat-heading">7. Monthly Paid & Unpaid Salary Summary
                    </h3>
                    <div class="card-body card_form" style="padding-top: 0;">

                        {{-- Project List --}}
                        <div class="form-group row custom_form_group">
                            <label class="col-sm-2 control-label d-block"> Project:</label>
                            <div class="col-sm-6">
                                <select class="selectpicker" id="proj_id[]" name="proj_id[]" multiple>
                                    <option value=""> Select Project</option>
                                    @foreach ($all_projects as $proj)
                                        <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Month List --}}
                        <div class="form-group row custom_form_group{{ $errors->has('month') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label d-block"> Month:</label>
                            <div class="col-sm-6">
                                <select class="form-select" name="month">
                                    @foreach ($month as $item)
                                        <option value="{{ $item->month_id }}"
                                            {{ $item->month_id == Carbon\Carbon::now()->format('m') ? 'selected' : '' }}>
                                            {{ $item->month_name }}</option>
                                    @endforeach
                                </select>

                                @if ($errors->has('month'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('month') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Year List --}}
                        <div class="form-group row custom_form_group{{ $errors->has('year') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label d-block"> Year:</label>
                            <div class="col-sm-3">
                                <select class="form-select" name="year">
                                    @foreach (range(date('Y'), date('Y') - 4) as $y)
                                        <option value="{{ $y }}" {{ $y }}>{{ $y }}
                                        </option>
                                    @endforeach
                                </select>

                                @if ($errors->has('year'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('year') }}</strong>
                                    </span>
                                @endif
                            </div>

                        </div>
                        <div class="form-group row custom_form_group">
                            <label class="col-sm-2 control-label d-block">Salary Status</label>
                            <div class="col-sm-6">
                                <select class="selectpicker" name="salary_status[]" multiple>

                                    <option value="0">Unpaid</option>
                                    <option value="1">Paid</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row custom_form_group">
                            <label class="col-sm-2 control-label d-block">Report Type:</label>
                            <div class="col-sm-6">
                                <select class="form-select" name="report_type" id="report_type" required>
                                    <option value="1">Monthly Summary</option>
                                    <option value="2">Only Project Salary Expense</option>
                                    <option value="3">Project Base Now Working Employees </option>
                                    <option value="4">Currently Not Working Employees Summary </option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer card_footer_button text-center">
                        <button type="submit" class="btn btn-primary waves-effect">Report Show</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-1"></div>
    </div>


    <!--8 Project wise Total Working Hours and Total Salary -->
    <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-10">
            <form class="form-horizontal" id="registration" target="_blank"
                action="{{ route('project-wise-Total.Working.Hours-And-Total.Salary') }}" method="post">
                @csrf
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title card_top_title salary-generat-heading">8. Salarysheet Base Salary & Deduction
                            Summary Report</h3>
                    </div>
                    <div class="card-body card_form" style="padding-top: 0;">

                        {{-- Project List --}}
                        <div class="form-group row custom_form_group">
                            <label class="col-sm-3 control-label d-block">Project:</label>
                            <div class="col-sm-9">
                                <select class="selectpicker" name="project_id_list[]" multiple>
                                    <!-- <option value="0"> Select Project</option> -->

                                    @foreach ($all_projects as $proj)
                                        <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- From Date --}}
                        <div class="form-group row custom_form_group">
                            <label class=" col-sm-3 control-label d-block">From Date:</label>
                            <div class="col-sm-9">
                                <input type="date" name="from_date" value="<?= date('Y-m-d') ?>"
                                    class="form-control">
                            </div>
                        </div>

                        {{-- To Date --}}
                        <div class="form-group row custom_form_group">
                            <label class="col-sm-3 control-label d-block">To Date:</label>
                            <div class="col-sm-9">
                                <input type="date" name="to_date" value="<?= date('Y-m-d') ?>" class="form-control">
                            </div>
                        </div>

                        <div class="form-group row custom_form_group">
                            <label class="col-sm-3 control-label d-block">Report Type</label>
                            <div class="col-sm-9">
                                <select class="form-select" name="report_type">
                                    <option value="1">Salary Summary</option>
                                    <option value="3">Only Project Salary Summary</option>
                                    <option value="2">Manpower Summary</option>
                                    <option value="4">Single Month Summary</option>
                                    <option value="5">Yearly Salary Statement</option>
                                    <option value="6">Yearly Statement Customize Month</option>

                                </select>
                            </div>
                        </div>

                        <div class="form-group row custom_form_group">
                            <label class="control-label col-md-3">Report Format</label>
                            <div class="col-sm-6">
                                <select class="form-select" name="report_format">
                                    <option value="1">Pdf</option>
                                    <option value="2">Excell</option>
                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="card-footer card_footer_button text-center">
                        <button type="submit" class="btn btn-primary waves-effect">Report Show</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-1"></div>
    </div>


    <!--9 Project wise Basic Empl and Hourly Emp Salary Summary -->
    <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-10">
            <form class="form-horizontal" id="registration" target="_blank"
                action="{{ route('project-wise.basic.hourly.emp.salary.sumamry') }}" method="post">
                @csrf
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-12">
                                <h3 class="card-title card_top_title salary-generat-heading">9 Basic & Hourly Employees
                                    Salary Summary</h3>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    <div class="card-body card_form" style="padding-top: 0;">

                        {{-- Project List --}}
                        <div class="form-group row custom_form_group">
                            <label class="control-label col-md-3">Project</label>
                            <div class="col-md-6">
                                <select class="selectpicker" name="proj_id[]" multiple>
                                    <option value="">All Project</option>
                                    @foreach ($projects as $proj)
                                        <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        {{-- From Date --}}
                        <div class="form-group row custom_form_group">
                            <label class="control-label col-md-3">From Date:</label>
                            <div class="col-sm-6">
                                <input type="date" name="from_date" value="<?= date('Y-m-d') ?>"
                                    class="form-control">
                            </div>
                        </div>

                        {{-- To Date --}}
                        <div class="form-group row custom_form_group">
                            <label class="control-label col-md-3">To Date:</label>
                            <div class="col-sm-6">
                                <input type="date" name="to_date" value="<?= date('Y-m-d') ?>" class="form-control">
                            </div>
                        </div>

                        <div class="form-group row custom_form_group">
                            <label class="control-label col-md-3">Report Type</label>
                            <div class="col-sm-6">
                                <select class="form-control" name="report_type">
                                    <option value="1">Monthly Salary Statement</option>
                                    <option value="2">Basic & Hourly Salary Summary</option>
                                    <option value="3">Multiple Month Salary</option>
                                    <option value="4">Staff & Direct Emp. Salary Summary with Catering</option>
                                    <option value="5">Staff & Direct Emp. Salary Summary without Catering</option>
                                    <option value="6">Only Project Salary Expence</option>
                                    <option value="7">Monthly Unpaid Salary Summary</option>

                                </select>
                            </div>
                        </div>

                        <div class="form-group row custom_form_group">
                            <label class="control-label col-md-3">Report Format</label>
                            <div class="col-sm-6">
                                <select class="form-select" name="report_format">
                                    <option value="1">Pdf</option>
                                    <option value="2">Excell</option>
                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="card-footer card_footer_button text-center">
                        <button type="submit" class="btn btn-primary waves-effect">Report Show</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-1"></div>
    </div>
    <!-- 9.1 Sponsor Salary Summary -->
    <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-10">
            <form class="form-horizontal" id="sponsor_salary_summary_report_form" target="_blank"
                action="{{ route('project-wise.basic.hourly.emp.salary.sumamry') }}" method="post">
                @csrf
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title card_top_title salary-generat-heading">9.1 Asloob and SubCon Salary Summary
                            Report</h3>
                    </div>
                    <div class="card-body card_form" style="padding-top: 0;">

                        {{--   Date --}}
                        <div class="form-group row custom_form_group">
                            <label class="control-label col-md-3">Asloob From </label>
                            <input type="date" name="asloob_from_date" value="<?= date('Y-m-d') ?>"
                                class="col-sm-3 form-control">
                            <label class="control-label col-md-1"> To </label>
                            <input type="date" name="asloob_to_date" value="<?= date('Y-m-d') ?>"
                                class="col-sm-3 form-control">
                        </div>

                        {{-- Project List --}}
                        <div class="form-group row custom_form_group">
                            <label class="col-sm-3 control-label">Project:<span class="req_star">*</span></label>
                            <div class="col-sm-9">
                                <select class="selectpicker" name="proj_ids" id="proj_ids" multiple required>
                                    @foreach ($projects as $proj)
                                        <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row custom_form_group">
                            <label class="col-sm-3 control-label">Sponsor:</label>
                            <div class="col-sm-6">
                                <select class="selectpicker" id="sponsor_ids" multiple="multiple">

                                    @foreach ($sponsor_for_salary_process as $spons)
                                        <option value="{{ $spons->spons_id }}">{{ $spons->spons_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3  d-none">
                                <input type="checkbox" id="wps_employees_checkbox" name="wps_employees_checkbox"
                                    value="1">
                                <label for="projec_cost_check">WPS Employees</label><br><br>
                            </div>
                        </div>
                        {{--   Date --}}
                        <div class="form-group row custom_form_group">
                            <label class="control-label col-md-3">SubCon Form </label>
                            <input type="date" name="subcon_from_date" value="<?= date('Y-m-d') ?>"
                                class="col-sm-3 form-control">
                            <label class="control-label col-md-1">To </label>
                            <input type="date" name="subcon_to_date" value="<?= date('Y-m-d') ?>"
                                class="col-sm-3 form-control">
                        </div>

                        <div class="form-group row custom_form_group ">
                            <label class="control-label col-md-3">Report Type</label>
                            <div class="col-sm-4">
                                <select class="form-select" name="report_type">
                                    <option value="8">Summary Month By Month</option>

                                </select>
                            </div>
                            <div class="col-sm-2">
                                <button type="submit" class="btn btn-primary waves-effect">Report Show</button>
                            </div>
                        </div>

                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-1"></div>
    </div>

    <!-- 10 Sponsor Salary Summary -->
    <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-10">
            <form class="form-horizontal" id="sponsor_salary_summary_report_form" target="_blank"
                action="{{ route('sponsor.wise.salary.sumamry.report') }}" method="post">
                @csrf
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title card_top_title salary-generat-heading">10 Salary Summary Report Sponsor-base
                        </h3>
                    </div>
                    <div class="card-body card_form" style="padding-top: 0;">

                        <div class="form-group row custom_form_group">
                            <label class="col-sm-3 control-label d-block">Sponsor:</label>
                            <div class="col-sm-6">
                                <select class="form-select" name="sponsor_id" required>
                                    @foreach ($sponser as $spons)
                                        <option value="{{ $spons->spons_id }}">{{ $spons->spons_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row custom_form_group">
                            <label class="control-label col-md-3">Project</label>
                            <div class="col-md-6">
                                <select class="selectpicker" name="proj_id[]" multiple>
                                    @foreach ($projects as $proj)
                                        <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        {{--   Date --}}
                        <div class="form-group row custom_form_group">
                            <label class="control-label col-md-3">Date From </label>
                            <input type="date" name="from_date" value="<?= date('Y-m-d') ?>"
                                class="col-sm-3 form-control">
                            <label class="control-label col-md-1">To </label>
                            <input type="date" name="to_date" value="<?= date('Y-m-d') ?>"
                                class="col-sm-3 form-control">
                        </div>

                        <div class="form-group row custom_form_group">
                            <label class="control-label col-md-3">Report Type</label>
                            <div class="col-sm-4">
                                <select class="form-select" name="report_type">
                                    <option value="1">Summary Month By Month</option>
                                    <option value="2">Single Month As Per Multiproject Work</option>
                                    <option value="3">Single Month As Per Salary Sheet</option>
                                </select>
                            </div>
                            <div class="col-sm-2">
                                <button type="submit" class="btn btn-primary waves-effect">Report Show</button>
                            </div>
                        </div>

                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-1"></div>
    </div>


    <!-- 11 Multiple Employee ID Base Salary Summary -->
    <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-10">
            <form class="form-horizontal" id="registration" target="_blank"
                action="{{ route('multiple-empidbase-salary-process') }}" method="post">
                @csrf
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title card_top_title salary-generat-heading">11. Multiple Emplyees ID Salary</h3>
                    </div>
                    <div class="card-body card_form" style="padding-top: 0;">

                        <div class="form-group row custom_form_group{{ $errors->has('emp_id') ? ' has-error' : '' }}">
                            <label class="col-sm-3 control-label d-block">Employee ID:</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control typeahead" placeholder="Type Employee ID"
                                    name="multiple_emp_Id" id="multiple_empId" value="{{ old('multiple_emp_Id') }}">
                                @if ($errors->has('emp_id'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('emp_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div id="showEmpId"></div>
                        </div>

                        {{-- Month List --}}
                        <div class="form-group row custom_form_group{{ $errors->has('month') ? ' has-error' : '' }}">
                            <label class="col-sm-3 control-label d-block">Salary Month:</label>
                            <div class="col-sm-6">
                                <select class="form-select" name="month">
                                    @foreach ($month as $item)
                                        <option value="{{ $item->month_id }}"
                                            {{ $item->month_id == Carbon\Carbon::now()->format('m') ? 'selected' : '' }}>
                                            {{ $item->month_name }}</option>
                                    @endforeach
                                </select>

                                @if ($errors->has('month'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('month') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Year List --}}
                        <div class="form-group row custom_form_group{{ $errors->has('year') ? ' has-error' : '' }}">
                            <label class="col-sm-3 control-label d-block">Salary Year:</label>
                            <div class="col-sm-6">
                                <select class="form-select" name="year">
                                    @foreach (range(date('Y'), date('Y') - 4) as $y)
                                        <option value="{{ $y }}" {{ $y }}>{{ $y }}
                                        </option>
                                    @endforeach
                                </select>

                                @if ($errors->has('year'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('year') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        {{-- Salary Status --}}
                        <div class="form-group row custom_form_group">
                            <label class="col-sm-3 control-label d-block">Salary Status</label>
                            <div class="col-sm-4">
                                <select class="form-select" name="salary_status" required>
                                    <option value="0">Unpaid</option>
                                    <option value="1">Paid</option>
                                    <option value="">All</option>
                                </select>
                            </div>
                            <div class="col-sm-2">
                                <button type="submit" class="btn btn-primary waves-effect">Report Show</button>
                            </div>
                        </div>
                    </div>

                </div>
            </form>
        </div>
        <div class="col-md-1"></div>
    </div>


    <!-- 12 Salary Paid By Bank Report -->
    <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-10">
            <form class="form-horizontal" id="registration" target="_blank"
                action="{{ route('salary.report.paid.by.bank') }}" method="post">
                @csrf
                <div class="card">
                    <h3 class="card-title card_top_title salary-generat-heading">12 Salary Paid to Bank Employees</h3>
                    <div class="card-body card_form" style="padding-top: 0;">

                        {{-- Project List --}}
                        <div class="form-group row custom_form_group">
                            <label class="control-label col-sm-2">Project:</label>
                            <div class="col-sm-7">
                                <select class="selectpicker" name="project_id_list[]" multiple>
                                    @foreach ($projects as $proj)
                                        <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Month Year List --}}
                        <div class="form-group row custom_form_group{{ $errors->has('month') ? ' has-error' : '' }}">
                            <label class="control-label col-sm-2">Month:</label>
                            <div class="col-sm-3">
                                <select class="form-select" name="month">
                                    @foreach ($month as $item)
                                        <option value="{{ $item->month_id }}"
                                            {{ $item->month_id == Carbon\Carbon::now()->format('m') ? 'selected' : '' }}>
                                            {{ $item->month_name }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('month'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('month') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <label class="control-label col-sm-2">Year:</label>
                            <div class="col-sm-3">
                                <select class="form-select" name="year">
                                    @foreach (range(date('Y'), date('Y') - 3) as $y)
                                        <option value="{{ $y }}" {{ $y }}>{{ $y }}
                                        </option>
                                    @endforeach
                                </select>

                                @if ($errors->has('year'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('year') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row custom_form_group">
                            <label class="control-label col-md-2">Report Type</label>
                            <div class="col-sm-8">
                                <select class="form-select" name="report_format">
                                    <option value="1">Preview in PDF</option>
                                    <option value="2">Excel Download</option>
                                </select>
                            </div>

                        </div>


                        <div class="card-footer card_footer_button text-center">
                            <button type="submit" class="btn btn-primary waves-effect">Report Show </button>
                        </div>
                    </div>
            </form>
        </div>
        <div class="col-md-1"></div>
    </div>
    <!-- end section -->


    {{-- 13 Salary Status(Paid by date) Update Report --}}
    <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-10">
            <form class="form-horizontal" id="registration" target="_blank"
                action="{{ route('salary.report.paid.by.date.who.did.it') }}" method="post">
                @csrf
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-12">
                                <h3 class="card-title card_top_title salary-generat-heading">13. Salay Paid Report</h3>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    <div class="card-body card_form" style="padding-top: 0;">

                        {{-- Project List --}}
                        <div class="form-group row custom_form_group">
                            <label class="control-label col-sm-2">Project:</label>
                            <div class="col-sm-7">
                                <select class="selectpicker" name="project_id_list[]" multiple>
                                    @foreach ($projects as $proj)
                                        <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{--   emp type --}}
                        <div class="form-group row custom_form_group">
                            <label class="control-label col-sm-2"> Emp. Type:</label>
                            <div class="col-sm-4">
                                {{-- Employee type List 1 = direct, 2 = indirect --}}
                                <select class="form-select" name="emp_type_id" required>
                                    <option value="0">All Type</option>
                                    <option value="-1">Direct Employee (Basic Salary)</option>
                                    <option value="1">Direct Employee (Hourly)</option>
                                    <option value="2">Indirect Employee</option>
                                    <option value="3">Basic Salary (Direct & Indirect) Employees</option>
                                </select>
                            </div>
                            <label class="control-label col-sm-2">Paid Date:</label>
                            <div class="col-sm-4">
                                <input type="date" name="updated_date" value="<?= date('Y-m-d') ?>"
                                    class="form-control">
                            </div>
                        </div>

                        {{-- Month and year List --}}
                        <div class="form-group row custom_form_group">
                            <label class="control-label col-sm-2">Salary Month:</label>
                            <div class="col-sm-4"> <select class="form-select" name="month" required>
                                    @foreach ($month as $item)
                                        <option value="{{ $item->month_id }}"
                                            {{ $item->month_id == Carbon\Carbon::now()->format('m') ? 'selected' : '' }}>
                                            {{ $item->month_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <label class="control-label col-sm-2">Year:</label>
                            <div class="col-sm-4">
                                <select class="form-select" name="year">
                                    @foreach (range(date('Y'), date('Y') - 1) as $y)
                                        <option value="{{ $y }}" {{ $y }}>{{ $y }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row custom_form_group">
                            <label class="control-label col-sm-2">Update By:</label>
                            <div class="col-sm-4">
                                <select class="selectpicker" name="update_by_ids[]" multiple>
                                    <option value="2">Ashraf Alam Ansari</option>
                                    <option value="3">Rashedul Islam</option>
                                    <option value="11">Sabbir Ahmed</option>
                                    <option value="12">Jahidul Islam Jony</option>
                                    <option value="74">Uttam Das</option>
                                </select>
                            </div>

                            <label class="control-label col-sm-2">Report Type:</label>
                            <div class="col-sm-4">
                                <select class="form-select" name="report_type" required>
                                    <option value="1">List </option>
                                    <option value="2">Summary</option>
                                </select>
                            </div>

                        </div>
                    </div>
                    <div class="card-footer card_footer_button text-center">
                        <button type="submit" class="btn btn-primary waves-effect">Report Show </button>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-1"></div>
    </div>


    <div class="overlay"></div>


    <!-- added this for Multiple Selection dropdownlist  -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta2/css/bootstrap-select.min.css"
        integrity="sha512-mR/b5Y7FRsKqrYZou7uysnOdCIJib/7r5QeJMFvLNHNhtye3xJp1TdJVPLtetkukFn227nKpXD9OjUc09lx97Q=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta2/js/bootstrap-select.min.js"
        integrity="sha512-FHZVRMUW9FsXobt+ONiix6Z0tIkxvQfxtCSirkKc5Sb4TKHmqq1dZa8DphF0XqKb3ldLu/wgMa8mT6uXiLlRlw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>


    <script>
        function EmployeeSalaryProcessing() {


            var proj_ids = $("#proj_ids").val();
            var month = $("#month").val();
            var year = $("#year").val();

            var selectedSponsor_ids = $('#sponsor_ids').val();
            //  const wps_selected = (document.getElementById('wps_employees_checkbox')).checked ? 1 : 0;

            // if(selectedSponsor_ids ==  '' || selectedSponsor_ids == null){
            //     showSweetAlert('error',"Please Select Sponsor Type");
            //     return ;
            // }
            // else if(selectedSponsor_ids.length == 2){
            //      showSweetAlert('error',"Please Select Only Single Sponsor Type");
            //      return;
            // }

            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: {
                    proj_ids: proj_ids,
                    sponsor_ids: selectedSponsor_ids,
                    month: month,
                    year: year,
                    //  is_wps_employees:wps_selected
                    operation_type: 1, //1= salary process , 2 = process history report
                },
                url: "{{ route('employee-salary-process-request') }}",
                beforeSend: () => {
                    $("body").addClass("loading");
                },
                complete: () => {
                    $("body").removeClass("loading");
                },
                success: function(response) {
                    if (response.status == 200) {
                        // alert(response.message);
                        showSweetAlert('success', response.message);
                    } else {
                        //alert(response.message);
                        showSweetAlert('error', response.message);
                    }

                },
                error: function(response) {
                    //alert(response.message);
                    showSweetAlert('error', response.message);
                }
            });
        }

        function showSalaryProcessHistory() {

            // 1. Collect your data
            var month = $("#month").val();
            var year = $("#year").val();
            var url = "{{ route('employee-salary-process-request') }}";
            var token = "{{ csrf_token() }}"; // Laravel requires this for POST
            // alert(token);

            // 2. Create a hidden form dynamically
            var form = $('<form>', {
                action: url,
                method: 'POST',
                target: '_blank' // This is the magic line for the new tab
            });

            // 3. Add parameters as hidden inputs
            var inputs = {
                _token: token,
                month: month,
                year: year,
                operation_type: 2 // Your process history report type
            };

            $.each(inputs, function(name, value) {
                form.append($('<input>', {
                    type: 'hidden',
                    name: name,
                    value: value
                }));
            });

            // 4. Append to body, submit, and cleanup
            $('body').append(form);
            form.submit();
            form.remove();
            return;



            var month = $("#month").val();
            var year = $("#year").val();

            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: {
                    month: month,
                    year: year,
                    //  is_wps_employees:wps_selected
                    operation_type: 2, // 1 salary process , 2 = process history report
                },
                url: "{{ route('employee-salary-process-request') }}",
                success: function(response) {
                    if (response.status == 200) {
                        // alert(response.message);
                        showSweetAlert('success', response.message);
                    } else {
                        //alert(response.message);
                        showSweetAlert('error', response.message);
                    }

                },
                error: function(response) {
                    //alert(response.message);
                    showSweetAlert('error', response.message);
                }
            });
        }


        function showSweetAlert(type, message) {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            })

            Toast.fire({
                type: type,
                title: message
            })

        }

        //  comment by rashed  $('#tags').inputTags();
    </script>

@endsection
