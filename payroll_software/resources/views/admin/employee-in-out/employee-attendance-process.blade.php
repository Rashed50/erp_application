@extends('layouts.admin-master')
@section('title')Atten. Process @endsection
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
        background: rgba(255, 255, 255, 0.8) url('{{ asset("animation/Loading.gif")}}') center no-repeat;
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
        <h4 class="pull-left page-title bread_title">Employee Attendance Processing</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active">Employee Attendance</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        @if(Session::has('success'))
        <div class="alert alert-success alertsuccess" role="alert">
            <strong>{{ Session::get('success') }}</strong>
        </div>
        @endif

        @if(Session::has('error'))
        <div class="alert alert-warning alerterror" role="alert">
            <strong>{{ Session::get('error') }}</strong>
        </div>
        @endif
    </div>
    <div class="col-md-2"></div>
</div>


<!-- Employee ID Base Attendance Process !-->
<div class="row mt-2">
    <div class="col-md-1"></div>
    <div class="col-md-10">
       <div class="card">
            <h5 class="card-title card_top_title salary-generat-heading">Attendance Process By Employee ID</h5>
            <div class="card-body">
             
                <div class="form-group row custom_form_group">
                    <label class="col-md-2 control-label">Employee ID</label>
                    <input type="text" class="col-md-8 form-control ml-2" id="employee_id"  name="employee_id" placeholder="Type Here Multiple Employee ID (e.g 1000,1001)">
                </div>
                  {{-- Month Year List --}}
                <div class="form-group row custom_form_group">
                    <label class="col-sm-2 control-label"> Month:</label>
                    <div class="col-sm-3">
                        <select class="form-select" name="single_emp_month">
                            @foreach($month as $item)
                            <option value="{{ $item->month_id }}" {{ $item->month_id ==
                                Carbon\Carbon::now()->format('m') ? 'selected' :'' }}>{{ $item->month_name }}</option>
                            @endforeach
                        </select>

                        @if ($errors->has('month'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('single_emp_month') }}</strong>
                        </span>
                        @endif
                    </div>
                    {{-- Year --}}
                    <label class="col-sm-1 control-label"> Year:</label>
                    <div class="col-sm-2">
                        <select class="form-select" name="single_emp_year">
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
                    <div class="col-md-1">
                        <button type="button" class="btn btn-primary waves-effect" onclick="processEmployeeAttendance(1)" >Process</button>
                    </div>
                </div> 
            </div>
       </div>
    </div>
    <div class="col-md-1"></div>

</div>


<!-- Project base Attendance Process !-->
<div class="row justify-content-center mt-1">
    <div class="col-md-1"></div>
    <div class="col-md-10">
        <div class="card">
            <h5 class="card-title card_top_title salary-generat-heading">Attendance Process By Working Project</h5>
             <div class="card-body card_form">

                {{-- Project List --}}
                <div class="form-group row custom_form_group">
                    <label class="col-sm-2 control-label">Project Name:</label>
                    <div class="col-sm-6">
                        <select class="form-select" name="proj_name">
                            <option value="">Select Project</option>
                            @foreach($project as $proj)
                            <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Month Year List --}}
                <div class="form-group row custom_form_group">
                    <label class="col-sm-2 control-label"> Month:</label>
                    <div class="col-sm-3">
                        <select class="form-select" name="month">
                            @foreach($month as $item)
                            <option value="{{ $item->month_id }}" {{ $item->month_id ==
                                Carbon\Carbon::now()->format('m') ? 'selected' :'' }}>{{ $item->month_name }}</option>
                            @endforeach
                        </select>

                        @if ($errors->has('month'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('month') }}</strong>
                        </span>
                        @endif
                    </div>
                    {{-- Year --}}
                    <label class="col-sm-1 control-label"> Year:</label>
                    <div class="col-sm-2">
                        <select class="form-select" name="year">
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

                {{-- Working Shift --}}
                <div class="form-group row custom_form_group">
                    
                    <label class="col-sm-2 control-label"> Working Shift:</label>
                    <div class="col-sm-3">
                        <select class="form-select" name="working_shift"> 
                            <option value="0">Day Shift</option>
                            <option value="1">Nisht Shift</option>
                            <option value="">Both Shift</option>                           
                        </select>

                        @if ($errors->has('year'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('year') }}</strong>
                        </span>
                        @endif
                    </div>
                    <div class="col-sm-1"></div>
                    <div class="col-sm-2">
                        <button type="submit" id="attn_process_btn" onclick="processEmployeeAttendance(2)"
                            class="btn btn-primary waves-effect">Process</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="col-md-1"></div>
</div>
<div class="overlay"></div>
 

<script type="text/javascript">
    function processEmployeeAttendance(process_type) {

        var project_id = $('select[name="proj_name"]').val();
        var month_id = $('select[name="month"]').val();
        var year = $('select[name="year"]').val();
        var working_shift = $('select[name="working_shift"]').val();
        var employee_id = $('#employee_id').val();

        if(process_type == 1){
              month_id = $('select[name="single_emp_month"]').val();
              year = $('select[name="single_emp_year"]').val();
              employee_id = $('#employee_id').val();
        } 

        if(process_type == 1 && employee_id == ""){
            showSweetAlertMessage('error', 'Please input Employee ID');
            return;
        }
        else if(process_type == 2 && project_id == ""){
            showSweetAlertMessage('error','Please Select Project Name')
            return;
        }
        document.getElementById('attn_process_btn').disabled  = true;
         
        $.ajax({
            dataType: "json",
            url: "{{ route('employee-attendance-process-request') }}",
            data: {
                project_id: project_id,
                month_id: month_id,
                year: year,
                working_shift:working_shift,
                process_type:process_type,
                employee_id:employee_id
            },
            success: function (response) {
                document.getElementById('attn_process_btn').disabled  = false;
                if (response.success) {
                    showSweetAlertMessage('success',response.message);

                } else {
                    showSweetAlertMessage('error',response.message);
                }
            }
        })
    }

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

</script>

@endsection
