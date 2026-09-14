@extends('layouts.admin-master')
@section('title')
    Employee Activities Search
@endsection
@section('content')
@section('internal-css')
    <style media="screen">
        a.checkButton {
            background: teal;
            color: #fff !important;
            font-size: 13px;
            padding: 5px 10px;
            cursor: pointer;
        }
    </style>
@endsection

<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Search Employee For New Activity</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active">Employee Status Information Search</li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-7">
        @if (Session::has('success'))
            <div class="alert alert-success alertsuccess" role="alert" style="margin-left: -20px">
                <strong> {{ Session::get('success') }} </strong>
            </div>
        @endif
        @if (Session::has('error'))
            <div class="alert alert-warning alerterror" role="alert" style="margin-left: -20px">
                <strong>{{ Session::get('error') }}</strong>
            </div>
        @endif
    </div>
    <div class="col-md-2"></div>
</div>

<!-- Page Content Start -->


<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body card_form">
                <!-- employee information searching UI -->
                <div class="row">
                    <div class="col-md-2"></div>
                    <div class="col-md-8">
                        <div class="card"><br>
                            <div class="card-body card_form" style="padding-top: 0;">

                                <div class="row form-group custom_form_group{{ $errors->has('searchBy') ? ' has-error' : '' }}">
                                     
                                        <label class="col-md-2 control-label d-block" > Search By</label>                                   
                                        <div class="col-md-3">
                                            
                                            <select class="form-select" name="searchBy" id="searchBy" required>
                                                <option value="employee_id">  Employee ID</option>
                                                <option value="akama_no">  Iqama </option>
                                                <option value="passfort_no">  Passport</option>
                                                
                                            </select>
                                        </div>

                                    <div class="col-md-4">
                                        <input type="text" placeholder="Enter ID/Iqama/Passport No"
                                            class="form-control" id="empl_info" name="empl_info"
                                            value="{{ old('empl_info') }}" onkeyup="typingEmployeeSearchingValue()"
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
                                        <button type="submit" onclick="singleEmoloyeeDetails()" style="margin-top: 2px"
                                        class="btn btn-primary waves-effect">SEARCH</button>
                                    </div>
                                </div>

                            </div> 
                        </div>
                    <div class="col-md-2"></div>
                </div>

                    <!-- show employee information and Employee Activity Form Part -->
                    <div class="col-md-12">
                        <div id="showEmployeeDetails" class="d-none">

                            <div class="row">
                                <div class="header_row">
                                    <span class="emp_info">Add Employee Activity</span>
                                </div>
                                <div class="col-md-6" style="border-color: #f1f1f1; border-style: solid;">
                                    <form method="post" action="{{ route('employee.new.activity.insert.request') }}">
                                        @csrf
                                        <br>
                                       
                                        <input type="hidden" name="emp_auto_id" value="" id="emp_auto_id" />

                                        <div class="form-group row custom_form_group">
                                            <label class="col-sm-3 control-label"> Activity Type </label>
                                            <div class="col-sm-7">
                                                <select class="form-control" name="activity_type"  required>
                                                     <option value="1">Job_Activity</option> 
                                                      <option value="10">Salary_Activity</option> 
                                                       <option value="20">Ilegal_Activity</option>  
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row custom_form_group">
                                            <label class="col-sm-3 control-label">Date<span
                                                    class="req_star">*</span></label>
                                            <div class="col-sm-7">
                                                <input type="date" name="activity_date" value="{{ date('Y-m-d') }}"
                                                    class="form-control">
                                                @if ($errors->has('activity_date'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('activity_date') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group row custom_form_group">
                                            <label class="col-sm-3 control-label">Job Status<span
                                                    class="req_star">*</span></label>
                                            <div class="col-sm-7">
                                                <select class="form-control" name="job_status" required>
                                                    <option value="">Select Job Status</option> 
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
                                            <label class="col-sm-3 control-label">Remarks:</label>
                                            <div class="col-sm-7">
                                                <input type="text" class="form-control" placeholder="Remarks "
                                                    name="activity_remarks">
                                            </div>
                                        </div>

                                        <div class="form-group row custom_form_group">
                                            <label class="col-sm-3 control-label">Description:</label>
                                            <div class="col-sm-7">
                                                <textarea name="activity_description" class="form-control" placeholder="Activity description"> </textarea>
                                            </div>
                                        </div>

                                        <div class="card-footer card_footer_button text-center">
                                            <button type="submit" class="btn btn-primary waves-effect">SAVE</button>
                                        </div>

                                    </form>
                                </div>

                                <!-- Update Employee Job Status -->
                                <div class="col-md-6" >
                               
                                    <!-- <div class="text-center mt-3">
                                        <h5 class="card-title">Update Employee Status</h5>
                                    </div>
                                    <br>
                                    <form method="post" action="{{ route('search.employee.status.update') }}">
                                        @csrf
                                        <input type="hidden" name="emp_auto_id" value=""
                                            id="emp_auto_id" />

                                        <div class="form-group row custom_form_group">
                                            <label class="col-sm-3 control-label">Employee Status : </label>
                                            <div class="col-md-7">
                                                <select class="form-control" name="empStatus" required>
                                                    <option value="">Select Job Status</option>
                                                  
                                                </select>
                                            </div>
                                            <div class="col-sm-2">
                                                <button type="submit" style="margin-top: 2px"
                                                    class="btn btn-primary waves-effect">UPDATE</button>
                                            </div>
                                        </div>
                                    </form> -->

                                    @can('employee_salary_status_update')
                                    <h5>Update Employee Salary Status</h5>
                                    <form method="post" action="{{ route('employee.activity.salary.status.update.request') }}">
                                        @csrf
                                        <br>
                                       
                                        <input type="hidden" name="emp_auto_id" value="" id="emp_auto_id" />

                                        <div class="form-group row custom_form_group">
                                            <label class="col-sm-3 control-label"> Activity Type </label>
                                            <div class="col-sm-7">
                                                <select class="form-control" name="activity_type">
                                                    <option value="10">Salary_Activity</option>                                                     
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row custom_form_group">
                                            <label class="col-sm-3 control-label">Date<span
                                                    class="req_star">*</span></label>
                                            <div class="col-sm-7">
                                                <input type="date" name="activity_date" value="{{ date('Y-m-d') }}"
                                                    class="form-control">
                                                @if ($errors->has('activity_date'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('activity_date') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group row custom_form_group">
                                            <label class="col-sm-3 control-label">Salary Status<span
                                                    class="req_star">*</span></label>
                                            <div class="col-sm-7">
                                                <select class="form-control" name="salary_status" required>
                                                    <option value="">Select Salary Status</option>
                                                     <option value="0"> Undefined </option>
                                                        <option value="1"> Active </option>
                                                      <option value="20"> Salary Hold </option>
                                                      
                                                     
                                                     
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row custom_form_group">
                                            <label class="col-sm-3 control-label">Remarks:</label>
                                            <div class="col-sm-7">
                                                <input type="text" class="form-control" placeholder="Remarks "
                                                    name="activity_remarks">
                                            </div>
                                        </div>

                                        <div class="form-group row custom_form_group">
                                            <label class="col-sm-3 control-label">Description:</label>
                                            <div class="col-sm-7">
                                                <textarea name="activity_description" class="form-control" placeholder="Activity description"> </textarea>
                                            </div>
                                        </div>

                                        <div class="card-footer card_footer_button text-center">
                                            <button type="submit" class="btn btn-primary waves-effect">SAVE</button>
                                        </div>

                                    </form>
                                    @endcan

                                    
                                </div>
                            </div>
                            <br>
                            <!-- Searching Employee Details -->

                            <div class="row">
                                <table
                                    class="table table-bordered table-striped table-hover show_employee_details_table"
                                    id="showEmployeeDetailsTable">
                                    <tr>
                                        <th>
                                            <div class="header_row">
                                                <span class="emp_info">Employee Information Details</span>
                                            </div>
                                        </th>
                                        <th>
                                            
                                                <span class="emp_info">Employee Salary:</span>
                                                <span id="show_employee_salary_type" class="emp2" style="font-size:15px;color:red"></span>
                                        </th>
                                    </tr>

                                    <tr>
                                        <td>
                                            <span class="emp">Employee Id:</span>
                                            <span id="show_employee_id" class="emp2"></span>
                                        </td>
                                        <td>
                                            <span class="emp">Basic Amount:</span>
                                             @can('employee_salary_show_permission')
                                                <span id="show_employee_basic" class="emp2"></span>
                                             @endcan
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <span class="emp">Employee Name:</span>
                                            <span id="show_employee_name" class="emp2"></span>
                                        </td>
                                        <td>
                                            <span class="emp">House Rate:</span>
                                            @can('employee_salary_show_permission')
                                                <span id="show_employee_house_rent" class="emp2"></span>
                                            @endcan
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <span class="emp">Iqama No:</span>
                                            <span id="show_employee_akama_no" class="emp2"></span>
                                        </td>
                                        <td>
                                            <span class="emp">Mobile Allowance:</span>
                                            <span id="show_employee_mobile_allow" class="emp2"></span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <span class="emp">Passport No:</span>
                                            <span id="show_employee_passport_no" class="emp2"></span>
                                        </td>
                                        <td>
                                            <span class="emp">Hourly Rate:</span>
                                            @can('employee_salary_show_permission')
                                                <span id="show_employee_hourly_rent" class="emp2"></span>
                                            @endcan
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <span class="emp">Employee Status:</span>
                                            <span id="show_employee_job_status" class="emp2"></span>
                                        </td>
                                        <td>
                                            <span class="emp">Medical Allowance:</span>
                                            <span id="show_employee_medical_allow" class="emp2"></span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <span class="emp">Project Name:</span>
                                            <span id="show_employee_project_name" class="emp2"></span>
                                        </td>
                                        <td>
                                            <span class="emp">Conveyance Allowance:</span>
                                            <span id="show_employee_conveyance_allow" class="emp2"></span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <span class="emp">Agency Name:</span>
                                            <span id="show_employee_agency_name" class="emp2"></span>
                                        </td>
                                        <td>
                                            <span class="emp">Local Travels Allowance:</span>
                                            <span id="show_employee_local_travel_allow" class="emp2"></span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <span class="emp">Sponsor Name:</span>
                                            <span id="show_employee_sponsor_name" class="emp2"></span>
                                        </td>
                                        <td>
                                            <span class="emp">Increment No:</span>
                                            <span id="show_employee_increment_no" class="emp2"></span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <span class="emp">Employee Designation:</span>
                                            <span id="show_employee_category" class="emp2"></span>
                                        </td>
                                        <td>
                                            <span class="emp">Increment Amount:</span>
                                            <span id="show_employee_increment_amount" class="emp2"></span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <span class="emp">Department:</span>
                                            <span id="show_employee_department" class="emp2"></span>
                                        </td>
                                        <td>
                                            <span class="emp">Others:</span>
                                            <span id="show_employee_others" class="emp2"></span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <span class="emp">Parmanent Address:</span>
                                            <span id="show_employee_address_Ds"></span>,
                                            <span id="show_employee_address_D"></span> ,
                                            <span id="show_employee_address_C" class="emp2"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <span class="emp">Parmanent Address Details:</span>
                                            <span id="show_employee_address_details" class="emp2"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <span class="emp">Mobile Number:</span>
                                            <span id="show_employee_mobile_no" class="emp2"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <span class="emp">Date Of Birth:</span>
                                            <span id="show_employee_date_of_birth" class="emp2"></span>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<!-- Page Content End -->


{{-- iqama no wise Search --}}
<script type="text/javascript">
    // Reset All loaded Data
    function resetUI() {
        $("#show_employee_system_auto_id").html('');
        $("#show_employee_id").html('');
        $("#show_employee_name").html('');
        $("#show_employee_akama_no").html('');
        $("#show_employee_passport_no").html('');
        $("#show_employee_job_status").html('');
        $("#show_employee_agency_name").html('');
        $("#show_employee_mobile_no").html('');
        $("#show_employee_date_of_birth").html('');
        $("#show_employee_category").html('');
        $("#show_employee_address_C").html('');
        $("#show_employee_address_D").html('');
        $("#show_employee_address_Ds").html('');
        $("#show_employee_address_details").html('');
        $("#show_employee_project_name").html('');
        $("#show_employee_department").html('');
        $("#show_employee_sponsor_name").html('');
        $("#show_employee_basic").html('');
        $("#show_employee_house_rent").html('');
        $("#show_employee_mobile_allow").html('');
        $("#show_employee_hourly_rent").html('');
        $("#show_employee_medical_allow").html('');
        $("#show_employee_conveyance_allow").html('');
        $("#show_employee_local_travel_allow").html('');
        $("#show_employee_increment_no").html('');
        $("#show_employee_increment_amount").html('');
        $("#show_employee_others").html('');
    }


    // Enter Key Press Event Fire
    $('#empl_info').keydown(function(e) {
        if (e.keyCode == 13) {
            singleEmoloyeeDetails();
        }
    })


    function singleEmoloyeeDetails() {

        var searchType = $('#searchBy').find(":selected").val();
        var searchValue = $("#empl_info").val();

        $('input[id="emp_auto_id"]').val(null);

        if ($("#empl_info").val().length === 0) {
            //  start message
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            })
            if ($.isEmptyObject(searchValue)) {
                Toast.fire({
                    type: 'error',
                    title: "Please Fill This Input Field First !!!"
                })
            } else {
                Toast.fire({
                    type: 'success',
                    title: "Employee Informations are"
                })
            }
            //  end message

        } else {
            resetUI();
            $.ajax({
                type: 'POST',
                url: "{{ route('employee.searching.searching-with-multitype.parameter') }}", //typeWise.employee-details
                data: {
                    search_by: searchType,
                    employee_searching_value: searchValue
                },
                dataType: 'json',
                success: function(response) {
                    console.log(response)

                    if (response.success == false) {
                        
                        $("span[id='employee_not_found_error_show']").text('Employee Not Found ');
                        $("span[id='employee_not_found_error_show']").addClass('d-block').removeClass(
                            'd-none');
                        $("#showEmployeeDetails").removeClass("d-block").addClass("d-none");

                    } else {
                        $("span[id='employee_not_found_error_show']").removeClass('d-block').addClass('d-none');
                    }

                    if (response.total_emp > 1) {
                       // showSearchingResultAsMultipleRecords(response.findEmployee);
                        
                    } else {
                        showSearchingEmployee(response.findEmployee[0]);
                    }


                } // end of success
            }); // end of ajax calling
        }
        // End of Method for Router calling

    }


    function showSearchingEmployee(findEmployee) {
        /* show employee information in employee table */
        $("#showEmployeeDetails").removeClass("d-none").addClass("d-block"); // show signle employee details

        // Hidden feild employee id, emp auto id
        $('input[id="input_employee_id"]').val(findEmployee.employee_id);
        $('input[id="emp_auto_id"]').val(findEmployee.emp_auto_id);
        

        /* Employement Details  */
        $("span[id='show_employee_system_auto_id']").text(findEmployee.emp_auto_id);
        $("span[id='show_employee_id']").text(findEmployee.employee_id);
        $("span[id='show_employee_name']").text(findEmployee.employee_name);
        $("span[id='show_employee_akama_no']").text(findEmployee.akama_no);
        $("span[id='show_employee_passport_no']").text(findEmployee.passfort_no);
        $("span[id='show_employee_job_status']").text(findEmployee.title);
        $("span[id='show_employee_agency_name']").text(findEmployee.agc_title);
       // $("span[id='show_employee_mobile_no']").text(findEmployee.mobile_no);
         $("span[id='show_employee_mobile_no']").text(findEmployee.mobile_no+", Mobile No: "+ (findEmployee.phone_no != null ? findEmployee.phone_no :""));

        $("span[id='show_employee_date_of_birth']").text(findEmployee.date_of_birth);

        /* Employee Address Details  */
        $("span[id='show_employee_category']").text(findEmployee.catg_name);
        $("span[id='show_employee_address_C']").text(findEmployee.country_name);
        $("span[id='show_employee_address_D']").text(findEmployee.division_name);
        $("span[id='show_employee_address_Ds']").text(findEmployee.district_name);
        $("span[id='show_employee_address_details']").text(findEmployee.details);

        var salary_type = findEmployee.hourly_employee == true ? "Hourly" : "Basic";
        $("span[id='show_employee_salary_type']").text(salary_type);

        /* show project name */
        if (findEmployee.project_id == null) {
            $("span[id='show_employee_project_name']").text("No Assigned Project!");
        } else {
            $("span[id='show_employee_project_name']").text(findEmployee.proj_name);
        }
        /* Department name */
        if (findEmployee.department_id == null) {
            $("span[id='show_employee_department']").text("No Assigned Department");
        } else {
            $("span[id='show_employee_department']").text(findEmployee.dep_name);
        }
        /* Show sponsor name */
        if (findEmployee.sponsor_id == null) {
            $("span[id='show_employee_sponsor_name']").text("No Assigned Sponsor!");
        } else {
            $("span[id='show_employee_sponsor_name']").text(findEmployee.spons_name);
        }


        /* Show Employee Salary Details */
        $("span[id='show_employee_basic']").text(findEmployee.basic_amount);
        $("span[id='show_employee_house_rent']").text(findEmployee.house_rent);
        $("span[id='show_employee_mobile_allow']").text(findEmployee.mobile_allowance);
        $("span[id='show_employee_hourly_rent']").text(findEmployee.hourly_rent);
        $("span[id='show_employee_medical_allow']").text(findEmployee.medical_allowance);
        $("span[id='show_employee_conveyance_allow']").text(findEmployee.conveyance_allowance);
        $("span[id='show_employee_local_travel_allow']").text(findEmployee.local_travel_allowance);
        $("span[id='show_employee_increment_no']").text(findEmployee.increment_no);
        $("span[id='show_employee_increment_amount']").text(findEmployee.increment_amount);
        $("span[id='show_employee_others']").text(findEmployee.others1);

    }
</script>
@endsection
