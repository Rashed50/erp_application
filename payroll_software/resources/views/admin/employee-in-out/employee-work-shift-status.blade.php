@extends('layouts.admin-master')
@section('title') Employee Shift Update @endsection
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
        <h4 class="pull-left page-title bread_title">Employee Working Shift Status Update</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active">Working Shift</li>
        </ol>
    </div>
</div>

<!-- Message Display Section !-->
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




<!-- Employee Search UI !-->

<div class="row">
    <div class="col-md-12">
        <div class="card">
            {{-- Project Wise All Employee Search Form --}}
            <div class="card-body row card_form">

                <div class="col-md-3">
                    <div class="form-group row custom_form_group">
                         <div class="col-sm-6">
                            <button class="btn btn-primary icon" type="button" data-toggle="modal" data-target="#cartModal" id="">Single Employee Update</button>
                        </div>
                    </div>
                </div>

                <div class="col-md-7">
                    <div class="form-group row custom_form_group">
                        <label class="col-sm-2 control-label">Project Name:</label>
                        <div class="col-sm-6">
                            <select class="form-select" name="proj_name"> 
                            <option value="0">Select Project</option>                              
                                @foreach($project as $proj)
                                <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <select class="form-select" name="pagination" id="pagination">
                                <option value="0">First Part</option>
                                <option value="400">Last Part</option>
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <button type="submit" onclick="searchProjectWiseEmployeeList()"
                                class="btn btn-primary waves-effect">Search Employee</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-2"></div>
        </div>
    </div>

</div>

 {{-- Single Employee Update Modal --}}
<div class="row ">
    <div class="modal fade" id="cartModal" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>An Employee Working Shift Update </h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                        id="closeModal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">

                        <!-- <form class="form-horizontal" id="employee-info-form" method="post" action="">
                            @csrf -->
                            <div class="card">
                                <div class="card-body card_form">

                                    <div class="form-group row custom_form_group" id="searchEmployeeId">
                                        <label class="control-label col-md-4">Employee ID:<span
                                                class="req_star">*</span></label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control typeahead"
                                                placeholder="Input Employee ID"
                                                name="empId_for_shift_change" id="emp_id_search"
                                                onkeyup="empSearch()" onfocus="showResult()"
                                                onblur="hideResult()">

                                            <div id="showEmpId"></div>
                                            <span id="error_show" class="d-none"
                                                style="color: red"></span>
                                        </div>
                                        <div class="col-md-3"></div>
                                    </div>
                                    <div class="form-group row custom_form_group">
                                        <label class="col-sm-4 control-label">Available Shift:<span
                                                class="req_star">*</span></label>
                                        <div class="col-sm-8">
                                            <select class="form-control" id="emplDutyshift"
                                                name="shiftingDuty" required>
                                                <option value="">Select Working Shift</option>
                                                <option value="0">Day Shift</option>
                                                <option value="1">Night Shift</option>
                                            </select>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="text-center">
                                        <button type="button" class="btn btn-primary"
                                            onclick="updateAnEmployeeWorkingShift()">Update</button>
                                    </div>
                                </div>

                            </div>
                     </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Employee Entry IN List Table !-->
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <form id="employee-entry-in-form" action="{{ route('employee.shift-status-update') }}" method="post">
                @csrf
                <div class="card-header">
                    <div class="row">

                        <div class="form-group row custom_form_group">
                            <div class="col-sm-3">                                
                                <label id="day_shift_total_emp" >                              
                            </div>
                            <div class="col-sm-3">                                
                                <label id="night_shift_total_emp">                           
                            </div>
                            <div class="col-sm-6">
                                <button type="submit" class="btn btn-primary waves-effect">Update All Selected</button>
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
                                            <th>Name</th>
                                            <th>Iqama No.</th>
                                            <th>Salary</th>
                                            <th>Emp.ID</th>
                                            <th>Night Shift</th>
                                        </tr>
                                    </thead>
                                    <tbody id="employee_entry_in_table_list"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="card-footer card_footer_expode">
                </div>
            </form>
        </div>
    </div>
</div>

<div class="overlay"></div>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script src="/path/to/inputTags.jquery.js"></script>


<!-- script area -->
<script type="text/javascript">
    // loading animation
    $(document).on({
        ajaxStart: function () {
            $("body").addClass("loading");
        },
        ajaxStop: function () {
            $("body").removeClass("loading");
        }
    });

    // Get Employee List by Project Name
    function searchProjectWiseEmployeeList() {
        var project_id =  parseInt($('select[name="proj_name"]').val()); 
        var pagination = parseInt($('select[name="pagination"]').val());
        
        $('#day_shift_total_emp').text('');
        $('#day_day_total_emp').text('');
        $('#employee_entry_in_table_list').html('');
        
        if (project_id > 0) {
            $.ajax({

                type: 'POST',
                dataType: 'json',
                data: {
                    project_id: project_id,  
                    pagination:pagination
                },
                url: "{{ route('employee.list.project.wise.forworking.shift.status') }}",
                success: function (response) {
                    console.log(response)

                    if (response.success == true) {
                        
                         $('#day_shift_total_emp').text('Day Shift: '+response.emp_day_shift);
                         $('#night_shift_total_emp').text('Night Shift: '+response.emp_night_shift);
                     
                        var rows = "";
                        var counter = 1;
                        $.each(response.employee_list, function (key, value) {
                            rows += `
                    <tr>
                        <td>${counter++}</td>
                        <td>${value.employee_name}</td>
                        <td>${value.akama_no} </td>
                       <td>${ value.basic_amount > 0 ? "Basic": "Hourly"}</td>
                        
                        <td> ${value.employee_id} 
                        </td>
                        <td> <input type="hidden" id="emp_auto_id${value.emp_auto_id}" name="emp_auto_id[]" value="${value.emp_auto_id}">
                          ${
                                 (value.isNightShift == 1) ?
                                   ` <input type="checkbox" name="emp_work_night_shift-${value.emp_auto_id}"   checked > Night Shift `
                                    :
                                   `<input type="checkbox" name="emp_work_night_shift-${value.emp_auto_id}" id="emp_work_shift-${value.emp_auto_id}"  > Night Shift`
                                }
                             
                        </td>
                    </tr>
                    `

                    });
                        $('#employee_entry_in_table_list').html(rows);
                    } else {
                        // alert('null');
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000
                        })

                        if ($.isEmptyObject(response.error)) {
                            Toast.fire({
                                type: 'success',
                                title: response.success
                            })
                        } else {
                            Toast.fire({
                                type: 'error',
                                title: response.error
                            })
                        }
                        //  end message
                    }

                }
            });
        } else {
            const toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
            toast.fire({
                type: 'error',
                title: response.error
            });
        }
    }


     // Single Employee Shift Changing
    function updateAnEmployeeWorkingShift() {
        var empID = $('input[name="empId_for_shift_change"]').val();
        var shiftingDuty = $('select[id="emplDutyshift"]').val();

        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: {
                employee_id: empID,
                working_shift: shiftingDuty
            },
            url: "{{ route('update-employee-work-shifting') }}",
            success: function (response) {
                if(response.status != 200){
                    showSweetAlertMessage('error',response.message);
                    return;
                }
                showSweetAlertMessage('success',response.message);
                $('#cartModal').modal('toggle');
             }
        });
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
