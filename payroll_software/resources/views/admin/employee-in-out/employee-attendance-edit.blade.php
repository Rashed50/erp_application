@extends('layouts.admin-master')
@section('title')Attn. Update @endsection
@section('content')

<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title"> Employee Attendence Update </h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active">Daily Attendence </li>
        </ol>
    </div>
</div>


<!-- Session Message Display Section !-->
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


<!-- Attendance Edit Menu -->
<div class="row mb-1 pb-1">
                        <div class="col-sm-3"></div>
                        <div class="col-sm-2">
                            <button type="button" onclick="openSingleEmployeeAttendanceUpdateForm()" class="btn btn-primary waves-effect">Single Employee</button>
                        </div>
                        <div class="col-sm-2">
                            <button type="button" onclick="openMultipleEmployeeAttendanceUpdateForm()" class="btn btn-primary waves-effect">Multiple Employee</button>
                        </div> 
            <hr>

    </div>



<!--Single Employee Attendance Update !-->
<div class="row d-none" id="single_emp_attendance_update_section">
    <div class="col-md-1"></div>
    <div class="col-md-10">
        <form class="form-horizontal" id="single_emp_attendance_update_form"  >
            @csrf
            <div class="card">
                <div class="card-body card_form">

                    <div class="form-group row custom_form_group">
                        <label class="col-sm-3 control-label">Employee ID</label>
                        <div class="col-sm-4">
                            <input type="number" id="employee_id" class="form-control" value="" name="employee_id"
                                placeholder="Employee ID Type Here" />
                        </div>
                    </div>
                    <div class="form-group row custom_form_group">
                        <label class="col-sm-3 control-label">Project Name</label>
                        <div class="col-sm-4">
                            <select class="form-control" name="proj_name">
                                @foreach($projects as $proj)
                                <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row custom_form_group">
                        <?php $today = date("Y-m-d");
                          $next_date = date('Y-m-d',strtotime((-1*$allow_days_single_emp).' days'));                      
                        ?>
                        <label class="col-sm-3 control-label">Date:</label>
                        <div class="col-sm-4">   
                            <input type="hidden" id="allows_days_single_emp" name="allows_days_single_emp" value="{{$allow_days_single_emp}}" > 
                            <input type="date" name="date" id="date" value="<?= date("Y-m-d") ?>" class="form-control" min="{{ $next_date }}" max="{{date('Y-m-d',strtotime('0 days')) }}" required >
                        </div>

                        <div class="col-sm-3">
                            <button type="button" onclick="employeeAttendanceInOutRecordSearch()"
                                class="btn btn-primary waves-effect">Search</button>
                        </div>
                    </div>


                </div>

                <div class="col-md-12">
                    <div id="employeeAttendanceDetails" class="d-none">
                        <div class="row">
                            <!-- employee Deatils -->
                            <div class="col-md-6">
                                <div class="header_row">
                                    <span class="emp_info">Employee Information Details</span>
                                </div>
                                <table
                                    class="table table-bordered table-striped table-hover custom_view_table show_employee_details_table"
                                    id="showEmployeeDetailsTable">
                                    <tr>
                                        <td> <span class="emp">Employee Id:</span> <span id="show_employee_id"
                                                class="emp2"></span> </td>
                                    </tr>
                                    <tr>
                                        <td> <span class="emp">Employee Name:</span> <span id="show_employee_name"
                                                class="emp2"></span> </td>
                                    </tr>
                                    <tr>
                                        <td> <span class="emp">Iqama No:</span> <span id="show_employee_akama_no"
                                                class="emp2"></span> </td>
                                    </tr>
                                    <tr>
                                        <td> <span class="emp">Project Name:</span> <span
                                                id="show_employee_project_name" class="emp2"></span> </td>
                                    </tr>
                                    <tr>
                                        <td> <span class="emp">Sponsor Name:</span> <span
                                                id="show_employee_sponsor_name" class="emp2"></span> </td>
                                    </tr>
                                    <tr>
                                        <td> <span class="emp">Photo</span>
                                            <img id="show_employee_Profile_photo" class="emp2" />
                                        </td>
                                    </tr>

                                </table>
                            </div>
                            <!-- Attendance Deatils -->
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="header_row">
                                            <span class="emp_info">Employee Attendance Details</span>
                                        </div>
                                        <table
                                            class="table table-bordered table-striped table-hover custom_view_table show_employee_details_table"
                                            id="showEmployeeDetailsTable">

                                            <tr>
                                                <td>
                                                    <div class="form-group row custom_form_group">
                                                        <input id="emp_io_id" name="emp_io_id" type="hidden">
                                                        <label class="col-sm-5 control-label">Date :</label>
                                                        <div class="col-sm-5">
                                                            <input type="date" name="emp_io_date" id="emp_io_date"
                                                                value="<?= date(" Y-m-d") ?>" class="form-control" disabled
                                                            required>

                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>
                                                    <div class="form-group row custom_form_group">
                                                        <label class="col-sm-5 control-label">Attendance IN:</label>
                                                        <div class="col-sm-5">
                                                            <input type="number" class="form-control"
                                                                id="emp_io_entry_time" name="emp_io_entry_time" min="1"
                                                                step="0.5" required>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="form-group row custom_form_group">
                                                        <label class="col-sm-5 control-label">Attendance OUT:</label>
                                                        <div class="col-sm-5">
                                                            <input type="number" class="form-control"
                                                                onkeyup="calculateOutTime()" id="emp_io_out_time"
                                                                name="emp_io_out_time" min="1" step="0.5" required>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>

                                            {{-- <tr>
                                                <td>
                                                    <div class="form-group row custom_form_group">
                                                        <label class="col-sm-5 control-label">Today Hours:</label>
                                                        <div class="col-sm-5">
                                                            <input type="number" class="form-control"
                                                                id="daily_work_hours" name="daily_work_hours" min="1" step ="0.5"
                                                                reqired enabled>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr> --}}
                                            <tr>
 
                                                <td> <span class="emp"> </span> <span style="color: red; font-weight:bold" id="basic_ot_hours"
                                                        class="emp2"></span> </td>
                                            </tr>



                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row custom_form_group">
                                <label class="col-sm-3 control-label"></label>
                                <div class="col-sm-4">
                                    <button type="button" onclick="deleteEmployeeAttendance()"
                                        class="btn btn-primary waves-effect">Delete Record</button>
                                </div>

                                <div class="col-sm-3">
                                    <button type="button" onclick="updatEmployeeAttendanceInOutRecord()"
                                        class="btn btn-primary waves-effect">Update</button>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="col-md-1"></div>
</div>

<!--Mutiple Employee Attendance Update !-->
<div class="row d-none" id ="multi_emp_attendance_update_section">
    
    <!--multiple Employees Searching Section -->
    <div class="row">
        <div class="col-md-12">
            <form class="form-horizontal" id="employeeEntryTimeList" action="" method="">
            <div class="card">
                <div class="card-body card_form">

               
                 <div class="form-group row custom_form_group">

                        <label class="col-md-1 control-label">Date:</label>
                        <div class="col-md-2">
                            <?php $today = date("Y-m-d");
                                $next_date_multi = date('Y-m-d',strtotime((-1*$allow_days_multi_emp).' days'));
                            ?>
                            <input type="hidden" id="allow_days_multi_emp" name="allow_days_multi_emp" value="{{$allow_days_single_emp}}" >
                            <input type="date" name="multiple_attn_date" id="multiple_attn_date" value="<?= date("Y-m-d") ?>" class="form-control"
                                min="{{$next_date_multi}}" max="{{date('Y-m-d',strtotime('0 days')) }}" required >
                        </div>
                        <div class="col-md-2">
                            <input type="checkbox" name="multiple_attn_night_shift" id="multiple_attn_night_shift"  value="0">
                            <label for="saldetlabel">Night Shift</label>
                        </div>

                        <input type="text" placeholder="Enter Multiple Emp. ID (e.g 1020,1030)" name="multi_emp_id"  id="multi_emp_id" class="form-control col-md-3">
                        <label class="col-md-1 control-label">Project </label>
                        <div class="col-md-2">
                            <select class="form-select" name="multiple_attn_proj_name" required >
                            <option value="">Select Project</option>
                                @foreach($projects as $proj)
                                <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                                @endforeach
                            </select>
                        </div>


                        <div class="col-md-3">
                            <button type="submit" id="atten_search_button" onclick="searchMultipleEmployeeAttenRecord()" class="btn btn-primary waves-effect">Search</button>
                            @can('attendence_delete_multiple_employee')
                                   
                            &nbsp;&nbsp;  
                             <button type="button" id ="atten_delete_button" onclick="deleteMultipleEmployeeAttendanceRecords()" name="atten_delete_button" class="btn btn-primary waves-effect">All Delete</button> 
                            @endcan
                        </div>


                    </div>

                </div>

            </div>
            </form>
        </div>  
    </div>
    <!--Attended Employee List -->
    <div class="col-lg-12">
        <div class="card">
        <form id="employee-entry-in-form" action="{{ route('multi.employee.attendance.inout-update-request') }}" onsubmit="atten_update_button.disabled = true;" method="post">
                    @csrf
            <div class="card-header">
                <div class="row">
                    <div class="form-group row custom_form_group" > 
                                <input type="checkbox" hidden name="multiple_attn_night_shift" id="multiple_attn_night_shift"   >
                                <input type="hidden" hidden name="multiple_attn_selected_date" id="multiple_attn_selected_date"   >
                                <label class="col-sm-2 control-label">Attendance IN Time (24 Format):</label>  
                                <div class="col-sm-1">                                    
                                    <input type="number" class="form-control"  id="atten_in_time"  name="atten_in_time" placeholder="IN Time"
                                        onkeyup="calculateAllEmployeeWorkingTime()" step = 0.5 required min = 1>
                                </div>                          
                                <label class="col-sm-1 control-label">Out Time:</label>
                                <div class="col-sm-2">
                                    
                                    <input type="number" class="form-control"  id="atten_out_time"  name="atten_out_time" placeholder="OUT Time(24 Format)"
                                        onkeyup="calculateAllEmployeeWorkingTime()" step = 0.5 required min = 1>
                                        &nbsp &nbsp &nbsp &nbsp &nbsp       
                                        <label  style="background-color: red; font-weight:bold"   id="lbl_all_emp_ot_hours" > 
                                </div>
                                <div class="col-sm-3">
                                    <button type="button" onclick="checkUnCheckAllEmployeeForAttendanceOut()" class="btn btn-primary waves-effect">Check/Uncheck</button>
                                </div>
                                <div class="col-sm-2">
                                    <button type="submit" id ="atten_update_button" name="atten_update_button" class="btn btn-primary waves-effect">Update</button>
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
                                <th>Emp.ID</th>
                                <th>Name</th>
                                <th>Date</th>
                                <th>Shift</th>
                                <th>Basic Salary</th>
                                <th>Basic Hours</th>
                                <th>IN</th>
                                <th>OUT</th>
                                <th>Emp.ID</th>
                                <th colspan="2">Select</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="employee_entry_time_list_view"></tbody>
                    </table>
                    </div>
                </div>
            </div>
            </div>
        </form>
        </div>
    </div>

</div>



 
<script type="text/javascript">
    $(document).ready(function () {

        $("#employeeEntryAttendanceTimeForm").validate({

            submitHandler: function (form) {
                return false;
            },

            rules: {
                emp_io_entry_time: {
                    required: true,
                },
                emp_io_out_time: {
                    required: true,
                },
                emp_io_date: {
                    required: true,
                },
            },

            messages: {
                emp_io_entry_time: {
                    required: "You Must Be Input This Field!",
                },
                emp_io_out_time: {
                    required: "You Must Be Input This Field!",
                },
                emp_io_date: {
                    required: "You Must Be Choose Date Field!",
                },
            },


        });
    });

    function openSingleEmployeeAttendanceUpdateForm(){
        $('#multi_emp_attendance_update_section').removeClass('d-block').addClass('d-none');
        $('#single_emp_attendance_update_section').removeClass('d-none').addClass('d-block');
        
    }

    function openMultipleEmployeeAttendanceUpdateForm(){
        $('#single_emp_attendance_update_section').removeClass('d-block').addClass('d-none');
        $('#multi_emp_attendance_update_section').removeClass('d-none').addClass('d-block');
        
    }
</script>

<!-- Single Employee Attendnace Update Section -->
<script type="text/javascript">

    function calculateOutTime() {

        var outTime = parseFloat($('#emp_io_out_time').val());
        var inTime = parseFloat($('#emp_io_entry_time').val());

        var timeDiff = outTime - inTime;

        if (timeDiff < 0) {
            timeDiff += 24;
        }
 

        if (outTime > 23 || outTime <= 0) {
            $('#basic_ot_hours').text('Invalid Data');
            $('#emp_io_out_time').val('');
         } else {
          
           if(checkThisDateIsFriday()){
            $('#basic_ot_hours').text("Friday Basic : 0, Overtime: "+timeDiff);
          }else {
            if (timeDiff >= 10)
            { 
              $('#basic_ot_hours').text('Basic + OT : 10+' + '' + (timeDiff - 10));
            } else { 
              $('#basic_ot_hours').text('Total Hours :' + timeDiff);
            }  
          }

        }

    }

    function checkThisDateIsFriday(){
        var date = document.getElementById('date').value;  
        date = new Date(date); 
        if (date.getDay() == 5) {
            // Friday
            return true;
        }
        return false;

    }

    function showHideSearchDataContainer(isHide) {
        if (isHide) {
            $('#employeeAttendanceDetails').removeClass('d-block').addClass('d-none');
        }else {
            $('#employeeAttendanceDetails').removeClass('d-none').addClass('d-block');
        }
    }
    
    $('#employee_id').keydown(function (e) {
        if (e.keyCode == 13) {
            employeeAttendanceInOutRecordSearch();
        }
    })

    function employeeAttendanceInOutRecordSearch() {

        
        var employeeId = $('input[name="employee_id"]').val();
        var project_id = $('select[name="proj_name"]').val();
        var date = $('input[name="date"]').val();
        var allows_days_single_emp = $('input[name="allows_days_single_emp"]').val();
        var currentDate = new Date();
        var input_date =new Date(date);
        var day_diff = currentDate.getDate()-input_date.getDate()
    
        // if(day_diff >allows_days_single_emp  || day_diff < -1){           
        //     showSweetAlertMessage('error',"Select Valid Date ");
        //     return;
        // }
         
        if (employeeId == null || employeeId == 0) {
            showSweetAlertMessage('error',"Invalid Employee ID")
            return;
        }else if ($.isEmptyObject(project_id) || $.isEmptyObject(date)) {
            showSweetAlertMessage('error',"Select Project Name and Date");
            return;
        }            
        $.ajax({
            type: 'get',
            dataType: 'json',
            data: {
                date: date,
                employee_id: employeeId,
                project_id: project_id,
            },
            url: "{{ route('employee-attendance-in-out-search-request') }}",
            success: function (response) {
 
                if (response.status == 200) {

                    var attendance_record = response.data;
                    $('#show_employee_id').text(attendance_record.employee_id);
                    $('#show_employee_name').text(attendance_record.employee_name);
                    $('#show_employee_akama_no').text(attendance_record.akama_no);
                    $('#show_employee_project_name').text(attendance_record.proj_name);
                    $('#show_employee_sponsor_name').text(attendance_record.spons_name);

                    $('#emp_io_id').val(attendance_record.emp_io_id);
                    $('#emp_io_date').val(attendance_record.emp_io_entry_date);
                    $('#emp_io_entry_time').val(attendance_record.emp_io_entry_time);
                    $('#emp_io_out_time').val(attendance_record.emp_io_out_time);                
                    $('#basic_ot_hours').text(attendance_record.daily_work_hours + '+' + attendance_record.over_time);
                    showHideSearchDataContainer(false);
                    $('input[name="employee_id"]').val('');

                } else {

                    showHideSearchDataContainer(true);
                    showSweetAlertMessage('error','Record Not Found');                    
                }

            }
        });
            
 
    }
    function resetSinleEmployeeAttendanceUpdateForm()
    {

        $('#show_employee_id').text('');
        $('#show_employee_name').text('');
        $('#show_employee_akama_no').text('');
        $('#show_employee_project_name').text('');
        $('#show_employee_sponsor_name').text('');

        $('#emp_io_id').val('');
        $('#emp_io_entry_time').val('');
        $('#emp_io_out_time').val('');                
        $('#basic_ot_hours').text(''); 
        $('input[name="employee_id"]').val('');

    }

    function deleteEmployeeAttendance() {

        var emp_io_id = $('#emp_io_id').val();
        if (emp_io_id == null || emp_io_id <= 0) {
            showSweetAlertMessage('error',"Employee Not Found to Delete Record");
            return;
        }

            swal({
                title: 'Are you sure?',
                text: 'Once it is deleted, it cannot be recovered',
                icon: "warning",
                buttons: ["Cancel", "OK"],
                dangerMode: true,
            })
            .then((willDelete) => {
                if (willDelete == false) {
                    return;
                }                  
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        emp_io_id: emp_io_id,
                         operation_type:1 , // 2= delete all records of a single date, 1= delete for single employee record
                    },
                    url: "{{ route('employee-attendance-in-out-delete-request') }}",
                    success: function (response) {

                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000
                        })
                        if (response.success) {
                            Toast.fire({
                                type: 'success',
                                title: 'Successfully Delete Record'
                            })
                            $('input[name="employee_id"]').val('');
                            showHideSearchDataContainer(true);
                            employeeAttendanceInOutRecordSearch();
                        } else {
                            Toast.fire({
                                type: 'error',
                                title: response.error
                            })
                        }
                    }

                })               
            });
    }

    function updatEmployeeAttendanceInOutRecord() {


        var emp_io_id = $('#emp_io_id').val();
        var emp_io_date = $('#emp_io_date').val();
        var emp_io_entry_time = $('#emp_io_entry_time').val();
        var emp_io_out_time = $('#emp_io_out_time').val(); 

        var outTime = parseFloat($('#emp_io_out_time').val());
        var inTime = parseFloat($('#emp_io_entry_time').val());

        var timeDiff = outTime - inTime;
         
        if(outTime <= 0 || outTime == '' || inTime <= 0 || inTime =='' ||  emp_io_id <= 0 || emp_io_id == '') {
            showSweetAlertMessage('error','Please Input Valid Data and Try Again');
            return;
        }

        $.ajax({
            type: "POST",
            dataType: 'json',
            url: "{{route('employee-attendance-in-out-update-request')}}",
            data: {
                emp_io_id: emp_io_id,
                emp_io_entry_time: emp_io_entry_time,
                emp_io_out_time: emp_io_out_time,
                emp_io_date: emp_io_date
            },
            success: function (response) {
                
                if (response.status == 200) {
                   showSweetAlertMessage('success','Successfully Updated');
                   showHideSearchDataContainer(true);
                   resetSinleEmployeeAttendanceUpdateForm();
                } else {
                    showSweetAlertMessage('error','Update Operation Failed, Please try Again');
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

<!-- Multi Employee Attendnace Update Section -->
<script type="text/javascript">
   $(document).ready(function()
    {
        $("#employeeEntryTimeList").validate({    
            submitHandler: function(form) {
                return false;
            },            
            rules: {
                proj_name: {
                required: true,
                },
                multiple_attn_date: {
                required: true,
                },
            },
            messages: {
                proj_name: {
                required: "You Must Be Select This Field!",
                },
                multiple_attn_date: {
                required: "You Must Be Chose This Field!",
                },
            },
        });
    });

    function searchMultipleEmployeeAttenRecord() {

        var proj_name = $('select[name="multiple_attn_proj_name"]').val();
        var attendance_date = $('input[name="multiple_attn_date"]').val();
        var working_shift = (document.getElementById("multiple_attn_night_shift").checked) == true ? 1: 0;  
        var allow_days_multi_emp = $('input[name="allow_days_multi_emp"]').val();
     var emp_ids = $('input[name="multi_emp_id"]').val();

            
            var currentDate = new Date();
            var input_date =new Date(attendance_date);
            var day_diff = currentDate.getDate()-input_date.getDate()
        
            // if(day_diff > allow_days_multi_emp || day_diff < -1){           
            //     showSweetAlertMessage('error',"Select Valid Date ");
            //     return;
            // }
            
            $('#employee_entry_time_list_view').html('');
            $('#atten_in_time').val(0);
        
            if (proj_name != "" && attendance_date != "") {
                document.getElementById("atten_search_button").disabled = true;
                $.ajax({

                    type: 'POST',
                    dataType: 'json',
                    data: {
                    proj_id: proj_name,
                    attendance_date: attendance_date,
                    working_shift: working_shift,
                     emp_ids:emp_ids,
                    },
                    url: "{{ route('multi.employee.attendance.record.search.ajaxrequest') }}",
                    success: function(response) {

                        document.getElementById("atten_search_button").disabled = false;
                        if (response.status == 200) {
                        
                            $('#multiple_attn_selected_date').val(attendance_date);
                            var attendance_in_time = 0;
                            var rows = "";
                            var counter = 1;
                            $.each(response.data, function(key, value) {
                            attendance_in_time = value.emp_io_entry_time
                            rows += `
                                    <tr>
                                        <td>${counter++}</td>
                                        <td>${value.employee_id}</td>
                                        <td>${value.employee_name}</td>
                                        <td>${value.emp_io_date}-${value.emp_io_month}-${value.emp_io_year}</td>
                                        <td>${value.emp_io_shift == "1" ? 'Nigth Shift' : 'Day Shift'} </td>                        
                                        <td> ${value.hourly_employee == 1 ? 'Hourly':'Basic'} </td>
                                        <td>${value.basic_hours}</td>
                                        <td>${value.emp_io_entry_time}</td>
                                        <td>${value.emp_io_out_time}</td>
                                        <td>${value.employee_id}</td>
                                        <td style="color:#fff">${value.emp_io_id}</td>
                                        <td> <input type="checkbox" name="entry_out_checkbox-${value.emp_io_id}" id="entry_out_checkbox-${value.emp_io_id}" value="0"></td>

                                        <td style="with:100px">
                                            <div class="row  align-items-center">
                                                <input type="hidden" id="emp_io_id${value.emp_io_id}" name="emp_io_id_list[]" value="${value.emp_io_id}">
                                                <input type="hidden" id="in_time${value.emp_io_id}" value="${value.emp_io_entry_time}">
                                            </div>
                                        </td>
                                        
                                    </tr>
                                    `
                            });
                            $('#employee_entry_time_list_view').html(rows);
                            
                        } else {

                            showSweetAlertMessage('error','Record Not Found');
                            //  end message
                        }
                    }
                });
            }
    } 


    function checkUnCheckAllEmployeeForAttendanceOut(){

        let myTable = document.getElementById('employee_entry_time_list_view');
        for (let row of myTable.rows) {
            allCell = row.cells;          
            var chkboxId = "entry_out_checkbox-" + allCell[10].innerText;
            document.getElementById(chkboxId).checked = !(document.getElementById(chkboxId).checked) 
        }      

    }


     // any changes from keystrokes to arrow clicks and keyboard/mouse paste, not supported in IE <9.
    $('#atten_out_time').on('input', function() {
        calculateAllEmployeeWorkingTime(); 
    });

    $('#atten_in_time').on('input', function() {
        calculateAllEmployeeWorkingTime(); 
    });

  function calculateAllEmployeeWorkingTime() {
 
    var outTime =  parseFloat($('#atten_out_time').val());
    var inTime =   parseFloat($('#atten_in_time').val());
    var timeDiff = outTime - inTime;
    if(inTime == 0  || outTime == 0){
      showSweetAlertMessage('error','Please Input  Attendance IN/Out Time');
      return;
    }

    if (timeDiff < 0) {
      timeDiff += 24;
    }
    if (outTime > 23 || outTime < 0) {
      $('#atten_out_time').val('');
      $('#lbl_all_emp_ot_hours').text('');

    } else
    {      
        var date = new Date((document.getElementById('multiple_attn_date').value));           
        if(date.getDay() == 5){
            // friday 
            $('#lbl_all_emp_ot_hours').text("Friday Basic : 0, Overtime: "+timeDiff);
        }else {
            if (timeDiff >= 10)
            { 
              $('#lbl_all_emp_ot_hours').text('Basic + OT : 10+' + '' + (timeDiff - 10));
            } else { 
              $('#lbl_all_emp_ot_hours').text('Total Hours :' + timeDiff);
            }  
        }

    }

  }
   // delete multiple employee attendance records
   function deleteMultipleEmployeeAttendanceRecords() {
       
       

        var proj_name = $('select[name="multiple_attn_proj_name"]').val();
        var attendance_date = $('input[name="multiple_attn_date"]').val();
        var working_shift = (document.getElementById("multiple_attn_night_shift").checked) == true ? 1: 0;

        if (proj_name == null || proj_name <= 0 || attendance_date == null) {
            showSweetAlertMessage('error',"Select Project, Date and Working shift");
            return;
        }
      

        swal({
            title: 'Are you sure?',
            text: 'Once it is deleted, it cannot be recovered',
            icon: "warning",
            buttons: ["Cancel", "OK"],
            dangerMode: true,
        })
        .then((willDelete) => {
            if (willDelete == false) {
                return;
            }
            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: {
                    project_id: proj_name,
                    attendance_date:attendance_date,
                    working_shift:working_shift,
                    operation_type:2 , // 2= delete all records of a single date, 1= delete for single employee record
                },
                url: "{{ route('employee-attendance-in-out-delete-request') }}",
                success: function (response) {

                    if (response.success) {
                        showSweetAlertMessage('success',"Successfully Deleted All Records");

                    } else {
                        showSweetAlertMessage('error',"Operation Failed");

                    }
                }

            })
        });
    }
    
</script>


@endsection
