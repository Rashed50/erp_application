@extends('layouts.admin-master')
@section('title') Work Record @endsection
@section('content')

<style>
    body {
        font-family: "Open Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", Helvetica, Arial, sans-serif;
    }
</style>
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Searching Employee Working (Multi Project) Records</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active">Working Record</li>
        </ol>
    </div>
</div>
<!-- Session Flash Message -->

<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
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

<!-- Searching Employee Monthly Multiple Projce Record -->
<div class="row">
    <div class="col-md-12">
            <div class="card">
                <div class="card-body card_form">
                    <div class="form-group row custom_form_group">
                        <!-- YEar Dropdown Menu -->
                        <label class="control-label col-sm-1">Year</label>
                        <div class="col-sm-1">
                            <select class="form-select" name="year" id="year">
                                @foreach(range(date('Y'), date('Y')-2) as $y)
                                <option value="{{$y}}" {{$y}}>{{$y}}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- Month Dropdown Menu -->
                        <label class="control-label col-sm-1">Month</label>
                        <div class="col-sm-2">
                            <select class="form-select" name="month" id="month">
                                @foreach($months as $month)
                                <option value="{{$month->month_id}}" {{$month->month_id == $currentMonth ? 'selected' : "" }}>{{$month->month_name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Employee ID -->
                        <label class="control-label col-md-2">Employee ID:</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control typeahead" placeholder="Input Employee ID" id="emp_id" autofocus  value="{{ old('emp_id') }}">
                            <span class="error d-none" id="error_massage"></span>
                        </div>
                         <!-- Searching Button-->
                        <div class="col-md-3">
                            @can('employee_working_record_searching')
                                <button type="button" id="multi_record_search_btn" class="btn btn-primary waves-effect" onclick="employeeMultipleWordRecordSearchBtn()">Search</button>
                            @endcan
                            @can('employee_working_record_add')
                                <button type="button" class="btn btn-primary" data-toggle="modal" id="new_record_button" data-target="#emp_multi_project_work_insert_modal">New Record </button>
                            @endcan
                        </div>
                    </div>
            </div>
    </div>
</div>

<!-- update monthly record -->
{{-- <div class="row">
    <div class="col-md-12">
        <div id="showEmployeeDetails" class="d-none">
            <div class="row">
                <div class="col-md-1">
                </div>
                <div class="col-md-10">
                        <form style="margin-top:20px" class="form-horizontal" id="workrecordupdate"  onsubmit="update_button.disabled = true;" action="{{ route('update-employee-work-record-submit') }}" method="post">
                            @csrf
                            <div class="card">
                                <div class="card-body card_form" style="padding-top: 20;">
                                    <input type="hidden" id="emp_auto_id" name="emp_auto_id" value="">
                                    <input type="hidden" id="month_work_id" name="month_work_id" value="">
                                    <input type="hidden" id="month_id" name="month_id" value="">
                                    <input type="hidden" id="year_id" name="year_id" value="">

                                    <div class="form-group row custom_form_group">
                                    <label class="col-sm-3 control-label"> Project Name:<span class="req_star">*</span></label>
                                    <div class="col-sm-7">
                                        <select class="form-control" id="project_id" name="project_id">
                                        @foreach($projects as $proj)
                                        <option value="{{ $proj->proj_id }}"> {{ $proj->proj_name }} </option>
                                        @endforeach
                                        </select>
                                    </div>
                                    </div>

                                    <div class="form-group row custom_form_group">
                                    <label class="col-sm-3 control-label"> Year:<span class="req_star">*</span></label>
                                    <div class="col-sm-4">
                                        <select class="form-control" name="year">
                                        @foreach(range(date('Y'), date('Y')-1) as $y)
                                        <option value="{{$y}}" {{$y}}>{{$y}}</option>
                                        @endforeach
                                        </select>
                                    </div>
                                    </div>


                                    <div class="form-group row custom_form_group">
                                    <label class="control-label col-md-3">Month:</label>
                                    <div class="col-md-7">
                                        <select class="form-control" name="month">
                                        @foreach($months as $data)
                                        <option value="{{ $data->month_id }}" {{ $data->month_id == $currentMonth ? 'selected':'' }}>{{ $data->month_name }}</option>
                                        @endforeach
                                        </select>
                                    </div>
                                    </div>

                                    <div id="work_hours_field" class="">
                                    <div class="form-group row custom_form_group{{ $errors->has('work_hours') ? ' has-error' : '' }}">
                                        <label class="control-label col-md-3">Total Hours:<span class="req_star">*</span></label>
                                        <div class="col-md-7">
                                        <input type="text" class="form-control" placeholder="Work Hours" id="work_hours" name="work_hours" value="{{old('work_hours')}}" required max="450" min="1">
                                        @if ($errors->has('work_hours'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('work_hours') }}</strong>
                                        </span>
                                        @endif
                                        </div>
                                    </div>
                                    </div>

                                    <div class="form-group row custom_form_group{{ $errors->has('overtime') ? ' has-error' : '' }}">
                                    <label class="control-label col-md-3">Overtime Hours:<span class="req_star">*</span></label>
                                    <div class="col-md-7">
                                        <input type="text" class="form-control" placeholder="Overtime Hours" id="overtime" name="overtime" value="{{old('overtime')}} 0" required max="150">
                                        @if ($errors->has('overtime'))
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('overtime') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    </div>

                                    <div class="form-group row custom_form_group{{ $errors->has('total_work_day') ? ' has-error' : '' }}">
                                    <label class="control-label col-md-3">Total Days:<span class="req_star">*</span></label>
                                    <div class="col-md-7">

                                        <input type="text" class="form-control" placeholder="Work Days" id="total_work_day" name="total_work_day" value="{{old('total_work_day')}}" required max="30">
                                        @if ($errors->has('total_work_day'))
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('total_work_day') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    </div>


                                </div>
                                <div class="card-footer card_footer_button text-center">
                                    <button type="submit" id="update_button" class="btn btn-primary waves-effect">SUBMIT</button>
                                </div>
                            </div>
                        </form>
                </div>
                <div class="col-md-1"></div>
            </div>
        </div>
    </div>
</div> --}}

<!-- monthly multiproject record list -->
<div class="row">
    <div class="col-12">
        <div class="table-responsive">
            <table id="alltableinfo" class="table table-bordered custom_table mb-0">
                <thead>
                    <tr>
                        <th>S.N</th>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Iqama</th>
                        <th>Salary</th>
                        <th>Project</th>
                        <th>Sponsor</th>
                        <th>Hours</th>
                        <th>OT</th>
                        <th>Days</th>
                        <th>Month</th>
                        <th>Inserted</th>
                        <th>Manage</th>
                    </tr>
                </thead>
                <tbody id="empMultiProjectWorkRecords"></tbody>
            </table>
        </div>
    </div>
</div>


<!--  Multiple Project Work Record Update Modal -->
<div class="modal fade" id="emp_multi_project_word_update_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Update An Employee Multiple Project Work Record <span class="text-danger" id="errorData"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

                <div class="modal-body">
                <form id="updaterecord"  method="post" action="{{route('update-employee-work-record-submit')}}"   onsubmit="updatebtn.disabled = true;" >
                       @csrf

                        <input type="hidden" id="modal_emp_auto_id" name="modal_emp_auto_id" value="">
                        <input type="hidden" id="modal_empwh_auto_id" name="modal_empwh_auto_id" value="">


                        <div class="form-group row custom_form_group">
                            <label class="col-sm-3 control-label">Month:</label>
                            <div class="col-sm-2">
                                <input type="text" id="modal_month" class="form-control " name="modal_month"   readonly>
                            </div>

                            <label class="col-sm-3 control-label">Year:</label>
                            <div class="col-sm-2">
                                <input type="text" id="modal_year" class="form-control " name="modal_year"   readonly>
                            </div>
                        </div>

                        <div class="form-group row custom_form_group">
                            <label class="control-label col-sm-3">Project:</label>
                            <div class="col-sm-7">
                            <select class="form-select" name="modal_project_name" id="modal_project_name" required >
                                <option value="">Select Project</option>
                            </select>
                            <span class="error d-none" id="error_massage"></span>
                            </div>
                        </div>

                        <div class="form-group row custom_form_group{{ $errors->has('modal_working_day') ? ' has-error' : '' }}">
                            <label class="control-label col-md-3">Working Days:<span
                                    class="req_star">*</span></label>
                            <div class="col-md-3">
                                <input type="number"  class="form-control" step="1" placeholder="Working Day"
                                    id="modal_working_day" name="modal_working_day"  value="{{old('modal_working_day')}}" required min="1" max="31">
                                    @if ($errors->has('modal_working_day'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('modal_working_day') }}</strong>
                                    </span>
                                    @endif
                            </div>
                            <label class="control-label col-md-2">P.Leave:<span
                                    class="req_star">*</span></label>
                            <div class="col-md-2">
                                <input type="number" class="form-control" step="1" placeholder="Paid Leave" name="modal_paid_leave" required min="0"  id="modal_paid_leave" >
                            </div>
                        </div>
                        {{-- Total working days --}}
                        <div class="form-group row custom_form_group">
                            <label class="col-sm-3 control-label">Total Days:</label>
                            <div class="col-sm-7">
                                <input type="number" id="modal_total_day"  class="form-control " name="modal_total_day" value="" min="1" max="30" step="1" required>
                            </div>
                        </div>

                        <div class="form-group row custom_form_group">
                            <label class="col-sm-3 control-label">Basic Hours:</label>
                            <div class="col-sm-7">
                                <input type="number" id="modal_total_hour" class="form-control " name="modal_total_hour" value="" min="1" max="350" step="0.5" required>
                                <input type="hidden" id="empwh_auto_id"  name="empwh_auto_id" value="">
                                <input type="hidden" id="modal_emp_auto_id" name="emp_id" value="">
                            </div>
                        </div>


                        <div class="form-group row custom_form_group">
                            <label class="col-sm-3 control-label">Overtime:</label>
                            <div class="col-sm-7">
                                <input type="number" id="modal_total_overtime" class="form-control " name="modal_total_overtime" value="0" min="0" step="0.5" max="200" required>
                            </div>
                        </div>



                        <div class="form-group row custom_form_group">
                            <label class="col-sm-3 control-label">Working Start:</label>
                            <div class="col-sm-7">
                                <input type="date" class="form-control datepicker start_date" id="modal_start_date" name="modal_start_date" value="" required>
                            </div>
                        </div>

                        <div class="form-group row custom_form_group">
                            <label class="col-sm-3 control-label">Working End:</label>
                            <div class="col-sm-7">
                                <input type="date" class="form-control datepicker endDate" id="modal_end_date" name="modal_end_date" value="" required>
                            </div>
                        </div>
                                <!-- <div class="modal-footer"> -->
                    <button type="submit" id="updatebtn" name="updatebtn"  class="btn btn-success"  >Update</button>

                </div>
            </form>
        </div>
    </div>
</div>


<!--  Multiple Project Work Record Insert Modal -->
<div class="modal fade" id="emp_multi_project_work_insert_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Adding An Employee Work Record<span class="text-danger" id="errorData"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <div class="form-group row custom_form_group{{ $errors->has('new_record_employee_id') ? ' has-error' : '' }}">
                    <label class="control-label col-md-3">Emp. ID<span
                            class="req_star">*</span></label>
                    <div class="col-md-9">
                        <input type="number" class="form-control"  placeholder="Enter an Employee ID"
                            id="new_record_employee_id" name="new_record_employee_id"
                            value="{{old('new_record_employee_id')}}" required >
                            @if ($errors->has('new_record_employee_id'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('new_record_employee_id') }}</strong>
                            </span>
                            @endif
                    </div>
                </div>

                <div class="form-group row custom_form_group ">
                    <label class="control-label col-md-3">Project</label>
                    <div class="col-md-9">
                        <select class="form-select" name="new_record_proj_name" required>
                            <option value="">Select Project</option>
                            @foreach($projects as $proj)
                            <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                  <div class="form-group row custom_form_group">
                    <label class="control-label col-md-3">Month</label>
                    <div class="col-md-5">
                        <select class="form-select" name="new_record_month" id="new_record_month" required>
                            <option value="">Select Month</option>
                            <option value="1">January</option>
                            <option value="2">February</option>
                            <option value="3">March</option>
                            <option value="4">April</option>
                            <option value="5">May</option>
                            <option value="6">June</option>
                            <option value="7">July</option>
                            <option value="8">Auguest</option>
                            <option value="9">September</option>
                            <option value="10">October</option>
                            <option value="11">November</option>
                            <option value="12">December</option>
                        </select>
                     </div>
                    <label class="col-sm-1 control-label"> Year </label>
                    <div class="col-sm-3">
                        <select class="form-select" name="new_record_year"  id="new_record_year">
                            @foreach(range(date('Y'), date('Y')-1) as $y)
                            <option value="{{$y}}" {{$y}}>{{$y}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row custom_form_group{{ $errors->has('working_day') ? ' has-error' : '' }}">
                    <label class="control-label col-md-3">Working Days:<span
                            class="req_star">*</span></label>
                    <div class="col-md-4">
                         <input type="number"  class="form-control" step="1" placeholder="Working Day"  id="working_day" name="working_day"  value="{{old('working_day')}}" required min="0" max="30">
                            @if ($errors->has('working_day'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('working_day') }}</strong>
                            </span>
                            @endif
                    </div>
                    <label class="control-label col-md-2">P.Leave:<span
                            class="req_star">*</span></label>
                    <div class="col-md-3">
                        <input type="number" class="form-control" step="1" placeholder="Paid Leave" name="paid_leave" required min="0"  id="paid_leave" >
                    </div>
                </div>
                {{-- Total working days --}}
                <div class="form-group row custom_form_group{{ $errors->has('total_work_day') ? ' has-error' : '' }}">
                    <label class="control-label col-md-3">Total Days:<span
                            class="req_star">*</span></label>
                    <div class="col-md-9">
                        <input type="number" disabled class="form-control" step="1" placeholder="Total Working Days"  name="new_record_total_work_day" id="new_record_total_work_day"    required min="1" max="30">
                            @if ($errors->has('total_work_day'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('total_work_day') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group row custom_form_group">
                    <label class="col-sm-3 control-label">Basic Hours:</label>
                    <div class="col-sm-9">
                        <input type="number" class="form-control"  id="new_record_total_basic_hours"  name="new_record_total_basic_hours" placeholder="Total Basic Hours"
                         min="0" max="350" step="0.5" required>
                    </div>
                </div>
                <div class="form-group row custom_form_group">
                    <label class="col-sm-3 control-label">Overtime:</label>
                    <div class="col-sm-9">
                        <input type="number" class="form-control" name="new_record_total_overtime" id="new_record_total_overtime" min="0" value="0"  step="0.5" required>
                    </div>
                </div>
                <div class="form-group row custom_form_group">
                    <label class="col-sm-2 control-label">Work From:</label>
                    <div class="col-sm-4">
                        <input type="date" class="form-control datepicker" id="datepicker" name="new_record_startDate"
                            value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}" required>
                     </div>
                     <label class="col-sm-1 control-label">To:</label>
                     <div class="col-sm-5">
                         <input type="date" class="form-control datepicker" id="datepicker" max="{{ date('Y-m-d') }}" name="new_record_endDate"
                             value="{{ date('Y-m-d') }}" required>
                      </div>
                </div>
            </div>
            <div class="card-footer card_footer_button text-center">
                <button type="submit" id="new_record_save_button" class="btn btn-primary waves-effect"
                    onclick="saveAnEmployeeMultipleProjectWorkingInformation()" >SAVE</button>
            </div>
        </div>
    </div>
</div>


<!-- script area -->
<script type="text/javascript">
    /* form validation */
    $(document).ready(function() {
        $("#employeeMultipleWordRecordSearchBtn").validate({
            /* form tag off  */
            submitHandler: function(form) {
                return false;
            },
            /* form tag off  */
            rules: {
                emp_id: {
                    required: true,
                },

                month: {
                    required: true,
                },
                year: {
                    required: true,
                },

            },

            messages: {
                emp_id: {
                    required: "You Must Be Input This Field!",
                },
                month: {
                    required: "Please Select Month Name",
                },
                year: {
                    required: "Please Select Year",
                },

            },


        });


        $("#emp_id").on('keyup', function (e) {
            if (e.key === 'Enter' || e.keyCode === 13) {
                employeeMultipleWordRecordSearchBtn();
            }
        });
    });

    function showSweetAlert(type,message){
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

    function employeeMultipleWordRecordSearchBtn() {

        var empId = $('#emp_id').val();
        var month = $('#month').val();
        var year = $('#year').val();

            $('#empMultiProjectWorkRecords').html('');
            $.ajax({
                type: "POST",
                url: "{{ route('employee.month.work.record.search') }}",
                data: {
                    emp_id: empId,
                    month: month,
                    year: year
                },
                dataType: "json",
                success: function(response) {
                    var rows = "";
                    var counter = 0;

                    if (response.status != 200) {
                        showSweetAlert('error',response.message);
                        return;
                    }else if (response.data.length == 0) {
                        showSweetAlert('error','Work Records Not Found');
                        return;
                    }
                    $.each(response.data, function(key, value) {
                        counter++;
                        rows += `
                                <tr>
                                    <td>${counter}</td>
                                    <td>${value.employee_id}</td>
                                    <td>${value.employee_name}</td>
                                    <td>${value.akama_no}</td>
                                    <td>${value.hourly_employee == 1 ? 'Hourly':'Basic' } </td>
                                    <td>${value.proj_name}</td>
                                    <td>${value.spons_name}</td>
                                    <td>${value.total_hour}</td>
                                    <td>${value.total_overtime }</td>
                                    <td>${value.total_day - value.paid_leave}+${value.paid_leave}</td>
                                    <td>${ getMonthNameByNumber(value.month) } </td>
                                    <td>${value.created_by == null ? '-' : value.created_by}</td>
                                    <td>
                                        @can('employee_working_record_delete')
                                                <a href="#" onClick="deleteMultiProjectBtnClick(${value.empwh_auto_id})" vallue="${value.empwh_auto_id}" title="Delete"><i class="fa fa-trash delete_icon"></i></a>&nbsp;&nbsp;&nbsp;
                                        @endcan
                                        @can('employee_working_record_edit')
                                            <a href="" id="editRecord" data-toggle="modal" data-target="#emp_multi_project_word_update_modal" data-id="${value.empwh_auto_id}"><i class="fa fa-edit edit_icon"></i></a>
                                        @endcan

                                    </td>
                                </tr>
                               `


                    });
                    $('#empMultiProjectWorkRecords').html(rows);
                 }

            });

    }

    function deleteMultiProjectBtnClick(id) {
        swal({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover the record",
                icon: "warning",
                buttons: true,
                dangerMode: true,
                focusConfirm: false,
            })

            .then((willDelete) => {
                if (willDelete) {

                    $.ajax({
                        type: 'GET',
                        url: "{{  url('admin/delete/employee/multi-project/work/record') }}/" + id,
                        dataType: 'json',
                        success: function(response) {

                            if(response.status == 200){
                                showSweetAlert('success', response.message);
                                employeeMultipleWordRecordSearchBtn();

                            }else {
                                showSweetAlert('error', response.message);
                            }

                        }
                    });
                }
            });
    }

    // Open Modal For Update Multi Project Work Record
    $(document).on("click", "#editRecord", function(){
        var id = $(this).data('id');

        $.ajax({
            type: "post",
            url: "{{ route('edit-multiproject-work-record') }}",
                data: {id: id},
            datatype:"json",
            success: function(response){

                if(response.workRecord != null){

                    $('#modal_emp_auto_id').val(response.workRecord.emp_id);
                    $('#modal_month').val(response.workRecord.month);
                    $('#modal_year').val(response.workRecord.year);
                    $('#modal_working_day').val(parseInt(response.workRecord.total_day)-parseInt(response.workRecord.paid_leave));
                    $('#modal_paid_leave').val(response.workRecord.paid_leave);
                    $('#modal_total_day').val(response.workRecord.total_day);
                    $('#modal_total_hour').val(response.workRecord.total_hour);
                    $('#modal_total_overtime').val(response.workRecord.total_overtime);
                    $('#modal_start_date').val(response.workRecord.start_date);
                    $('#modal_end_date').val(response.workRecord.end_date);
                    $('#modal_empwh_auto_id').val(response.workRecord.empwh_auto_id);

                    if(response.project !=''){
                        $('select[id="modal_project_name"]').empty();
                       // $('select[id="modal_project_name"]').append('<option>Please Select Project</option>');
                        $.each(response.project, function(key, value) {
                        $('select[id="modal_project_name"]').append('<option value="' + value.proj_id + '">' + value.proj_name + '</option>');
                        });
                        document.getElementById("modal_project_name").value = response.workRecord.project_id;

                    }
                }else{
                    $('#errorData').text(response.error);
                }

            }
        })

    });
       // Update Module
    $("#modal_working_day").on('change keydown paste input', function(){
      calculateTotalWorkingDaysInModal();
    });
    $("#modal_paid_leave").on('change keydown paste input', function(){
      calculateTotalWorkingDaysInModal();
    });

    function calculateTotalWorkingDaysInModal(){

        var working_day = $('#modal_working_day').val();
        var paid_leave = $('#modal_paid_leave').val();
        if(working_day == '')
           working_day = 0;
        if(paid_leave == ''){
            $('#modal_paid_leave').val(0);
            paid_leave = 0;
        }


         total = parseInt(working_day)+parseInt(paid_leave)
         if(total > 30){
             $('#modal_working_day').val(0);
             $('#modal_paid_leave').val(0);
             $('#modal_total_day').val(0);
            alert('Total Working Days more than 30');
           //  showSweetAlert('error', response.message);
            return;
         }
        $('#modal_total_day').val(total);

    }


   // Reset Edit Modal Previous Data
    $('#emp_multi_project_word_update_modal').on('hidden.bs.modal', function (e) {
      $(this)
      .find("input,textarea,select").val('').end()
      .find("input[type=checkbox], input[type=radio]").prop("checked", "").end();
    })


    $("#working_day").on('change keydown paste input', function(){
      calculateTotalWorkingDays();
    });
    $("#paid_leave").on('change keydown paste input', function(){
      calculateTotalWorkingDays();
    });

    function calculateTotalWorkingDays(){
        var working_day = $('#working_day').val();
        var paid_leave = $('#paid_leave').val();
        if(working_day == '')
           working_day = 0;
        if(paid_leave == ''){
            $('#paid_leave').val(0);
            paid_leave = 0;
        }

         total = parseInt(working_day)+parseInt(paid_leave)
          if(total > 30){
             $('#paid_leave').val(0);
             $('#working_day').val(0);
             $('#new_record_total_work_day').val(0);
             alert('Total Working Days more than 30');
             return;
         }
        $('#new_record_total_work_day').val(total);

    }


  // store new record
    $(document).on('click',"#new_record_button",function(){

       var emp_id = $('#emp_id').val();
       $('#new_record_employee_id').val(emp_id);
       var new_record_employee_id = document.getElementById('new_record_employee_id');
       new_record_employee_id.focus();
    });

    function saveAnEmployeeMultipleProjectWorkingInformation() {


        var emp_id = $('#new_record_employee_id').val();
        var month = $('#new_record_month').val();
        var year = $('#new_record_year').val();
        var proj_name = $('select[name="new_record_proj_name"]').val();
        var totalHourTime = $('input[name="new_record_total_basic_hours"]').val();
        var totalOverTime = $('input[name="new_record_total_overtime"]').val();
        var total_days =   $('input[name="new_record_total_work_day"]').val();
        var paid_leave =   $('input[name="paid_leave"]').val();
        var startDate = $('input[name="new_record_startDate"]').val();
        var endDate = $('input[name="new_record_endDate"]').val();
        if(emp_id == ""){
            showSweetAlert('error',"Please Input Employee ID");
            return;
        }else if(month == ""){
            showSweetAlert('error',"Please Select Working Month");
            return;
        }
        else if ( proj_name == "" || total_days <= 0 || totalOverTime < 0 || totalHourTime <= 0) {
            showSweetAlert('error',"Please Input All Require Data");
            return;
        }


        document.getElementById("new_record_save_button").disabled = true;
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: {
            emp_id: emp_id,
            proj_name: proj_name,
            month:month,
            year:year,
            startDate: startDate,
            endDate: endDate,
            total_days:total_days,
            paid_leave:paid_leave,
            totalHourTime: totalHourTime,
            totalOverTime: totalOverTime
            },
            url: "{{ route('employee-multiple-time-insert') }}",
            beforeSend:()=>{
                $("body").addClass("loading");
            },
            complete:()=>{
                $("body").removeClass("loading");
            },
            success: function(response) {
                document.getElementById("new_record_save_button").disabled = false;
                if(response.status == 200){
                   showSweetAlert("success",response.message);
                   $('#emp_multi_project_work_insert_modal').modal('hide');
                   $('#new_record_employee_id').val('');
                   $('input[name="new_record_total_basic_hours"]').val(0);
                   $('input[name="new_record_total_overtime"]').val(0);
                   $('input[name="new_record_total_work_day"]').val(0);
                   $('input[name="working_day"]').val(0);
                   $('input[name="paid_leave"]').val(0);
                    var emp_id = $('#emp_id').val();
                    if(emp_id != ""){
                        employeeMultipleWordRecordSearchBtn();
                    }
                }else {
                    showSweetAlert("error",response.message);
                }

            },
            error:function(response){
                document.getElementById("new_record_save_button").disabled = false;
                showSweetAlert('error','Operation Failed!, Pleage Try Again ');
            }
        });
    }


  function getMonthNameByNumber(mn){
        const months = ["",
                "January",
                "February",
                "March",
                "April",
                "May",
                "June",
                "July",
                "August",
                "September",
                "October",
                "November",
                "December",
                ];
        return months[mn];
  }

</script>
@endsection
