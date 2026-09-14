@extends('layouts.admin-master')
@section('title') Edit Month Work History @endsection
@section('content')
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Month Work History</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active">Edit Month Work History</li>
        </ol>
    </div>
</div>
<!-- add division -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        @if(Session::has('error'))
        <div class="alert alert-warning alerterror" role="alert">
            <strong>Opps!</strong> please try again.
        </div>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <form class="form-horizontal" id="registration" action="{{ route('update-month-work') }}" onsubmit="update_button.disabled = true;"  method="post">
            @csrf
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-8">
                            <h3 class="card-title card_top_title"><i class="fab fa-gg-circle"></i> Edit Daily Work History</h3>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
                <div class="card-body card_form" style="padding-top: 0;">
                    <input type="hidden" name="id" value="{{ $edit->month_work_id }}">
                    <div class="form-group custom_form_group{{ $errors->has('emp_id') ? ' has-error' : '' }}">
                        <label class="control-label d-block" style="text-align: left;">Employee ID : {{ $edit->employee->employee_id }} == Name : {{ $edit->employee->employee_name ?? "" }} </label>
                        <div>
                            <input type="hidden" class="form-control typeahead" placeholder="Type Employee ID" name="emp_id" id="emp_id_search" onkeyup="empSearch()" onfocus="showResult()" onblur="hideResult()" value="{{ $edit->employee->employee_id }}">
                            @if ($errors->has('emp_id'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('emp_id') }}</strong>
                            </span>
                            @endif
                        </div>
                        <div id="showEmpId"></div>
                    </div>

                    <div class="form-group custom_form_group{{ $errors->has('year') ? ' has-error' : '' }}">
                        <label class="control-label d-block" style="text-align: left;">Year:<span class="req_star">*</span></label>
                        <div>

                            <select class="form-control" name="year">
                                @foreach(range(date('Y'), date('Y')-1) as $y)
                                <option value="{{$y}}" {{$y}}>{{$y}}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('year'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('year') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group custom_form_group{{ $errors->has('month') ? ' has-error' : '' }}">
                        <label class="control-label d-block" style="text-align: left;">Month:<span class="req_star">*</span></label>
                        <div>

                            <select class="form-control" name="month">
                                @foreach($month as $data)
                                <option value="{{ $data->month_id }}" {{ $data->month_id == $data->month_id ? 'selected':'' }}>{{ $data->month_name }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('month'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('month') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group custom_form_group{{ $errors->has('work_hours') ? ' has-error' : '' }}">
                        <label class="control-label d-block" style="text-align: left;">Total Work Hours:<span class="req_star">*</span></label>
                        <div>
                            <input type="text" class="form-control" placeholder="Work Hours" id="work_hours" name="work_hours" value="{{ $edit->total_hours }}" required max="450" min="1">
                            @if ($errors->has('work_hours'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('work_hours') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>


                    <div class="form-group custom_form_group{{ $errors->has('overtime') ? ' has-error' : '' }}">
                        <label class="control-label d-block" style="text-align: left;">Over Time:<span class="req_star">*</span></label>
                        <div>
                            <input type="text" class="form-control" placeholder="Overtime" id="overtime" name="overtime" value="{{ $edit->overtime }}" required max="50">
                            @if ($errors->has('overtime'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('overtime') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group custom_form_group{{ $errors->has('total_work_day') ? ' has-error' : '' }}">
                        <label class="control-label d-block" style="text-align: left;">Total Work Days:<span class="req_star">*</span></label>
                        <div>
                            <input type="text" class="form-control" placeholder="Work Days" id="total_work_day" name="total_work_day" value="{{ $edit->total_work_day }}" required max="30">
                            @if ($errors->has('total_work_day'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('total_work_day') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                </div>
                <div class="card-footer card_footer_button text-center">
                    <button type="submit" id="update_button" class="btn btn-primary waves-effect">UPDATE</button>
                </div>
            </div>
        </form>
    </div>
    <div class="col-md-2"></div>
</div>
@endsection