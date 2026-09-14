@extends('layouts.admin-master')
@section('title') Add TUV Information @endsection
@section('content')

<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title"> Employee TUV Information</h4>
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

<style media="screen">
    a.checkButton {
        background: teal;
        color: #fff !important;
        font-size: 13px;
        padding: 5px 10px;
        cursor: pointer;
    }
</style>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body card_form">

                <!-- employee information searching with (id, passport, iqama) Start -->
                <div class="row">
                    <div class="col-md-2"></div>
                    <div class="col-md-8">
                        <div class="card"><br>
                            <div class="card-body card_form" style="padding: 0;">

                                <div
                                    class="row form-group custom_form_group{{ $errors->has('searchBy') ? ' has-error' : '' }}">
                                    <label class="col-md-2 control-label d-block" style="text-align: left;">Employee
                                    </label>
                                    <div class="col-md-5">
                                        <select class="form-select" name="searchBy" id="searchBy" required>
                                            <option value="employee_id">Searching By Employee ID</option>
                                            <option value="akama_no">Searching By Iqama </option>
                                            <option value="passfort_no">Searching By Passport</option>
                                        </select>
                                    </div>

                                    <div class="col-md-5">
                                        <input type="text" placeholder="Enter ID/Iqama/Passport No" class="form-control"
                                            id="empl_info" name="empl_info" value="{{ old('empl_info') }}"
                                            onkeyup="typingEmployeeSearchingValue()" required autofocus>
                                        <span id="employee_not_found_error_show" class="d-none"
                                            style="color: red"></span>
                                        @if ($errors->has('empl_info'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('empl_info') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>

                            </div>
                            <div class="card-footer card_footer_button text-center">
                                <button type="submit" onclick="singleEmoloyeeDetails()" style="margin-top: 2px"
                                    class="btn btn-primary waves-effect">SEARCH</button>
                            </div>
                        </div>
                        <div class="col-md-2"></div>
                    </div>
                </div>
                <!-- employee information searching with (id, passport, iqama) End -->

                <div class="row">
                    
                     <!-- Emp TUV Insert Information Start -->
                    <div class="d-none" id="showEmployeeTUVApplyForm">
                        <form class="form-horizontal" method="post"
                            action="{{ route('employee.tuv.information.insert.request') }}">
                            @csrf
                            <div class="card">
                                <div class="card-body card_form row">
                                    <div class="col-md-2"></div>
                                    <div class="col-md-8" style="border-color: #B2BEB5; border-style: solid;">
                                        <div
                                            class="form-group row custom_form_group{{ $errors->has('emp_auto_id') ? ' has-error' : '' }}">
                                            <div class="col-sm-5"></div>
                                            <div class="col-sm-5">
                                                <span class="req_star">Employee ID <span id="show_employee_id"
                                                        class="req_star">Required</span>
                                                </span>
                                                <input type="hidden" class="form-control" id="input_emp_auto_id"
                                                    name="emp_auto_id" value="" required>
                                                @if ($errors->has('emp_auto_id'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('emp_auto_id') }}</strong>
                                                </span>
                                                @endif
                                            </div>
                                            <div class="col-sm-2"></div>
                                        </div>
                                        <br>
                                        <div
                                            class="form-group row custom_form_group{{ $errors->has('company_id') ? ' has-error' : '' }}">
                                            <label class="col-sm-4 control-label">Company Name:<span
                                                    class="req_star">*</span></label>
                                            <div class="col-sm-6">
                                                <select class="form-control" name="company_id" required>
                                                    <option value="">Select Company Name</option>
                                                    <option value="1">Asloob Internation Contracting Comany
                                                    </option>
                                                    <option value="2">Asloob Bedda Contracting Comany </option>
                                                    <option value="3">Bedaa General Contracting Comany</option>
                                                    <option value="4">Other Employee</option>
                                                </select>
                                                @if ($errors->has('company_id'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('company_id') }}</strong>
                                                </span>
                                                @endif
                                            </div>
                                            <div class="col-md-2"></div>
                                        </div>
                                        <!--Trade/Designation List Dropdown -->
                                        <div
                                            class="form-group row custom_form_group{{ $errors->has('catg_id') ? ' has-error' : '' }}">
                                            <label class="col-sm-4 control-label">Trade Name:<span
                                                    class="req_star">*</span></label>
                                            <div class="col-sm-6">
                                                <select class="form-control" name="catg_id" required>
                                                      @foreach ($designations as $item)
                                                    <option value="{{$item->catg_id}}">{{$item->catg_name}}</option>

                                                    @endforeach
                                                </select>
                                                @if ($errors->has('catg_id'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('catg_id') }}</strong>
                                                </span>
                                                @endif
                                            </div>
                                            <div class="col-md-2"></div>
                                        </div>
                                        <div
                                            class="form-group row custom_form_group{{ $errors->has('tvu_card_no') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-4">Card No:</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control" name="tvu_card_no"
                                                    value="{{old('tvu_card_no')}}" placeholder="Employee Card No"
                                                    required>
                                                @if ($errors->has('tvu_card_no'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('tvu_card_no') }}</strong>
                                                </span>
                                                @endif
                                            </div>
                                            <div class="col-sm-2"></div>
                                        </div>

                                        <div
                                            class="form-group row custom_form_group{{ $errors->has('tvu_issue_date') ? ' has-error' : '' }}">
                                            <label class="col-sm-4 control-label">Issue Date:<span
                                                    class="req_star">*</span></label>
                                            <div class="col-sm-6">
                                                <input type="date" name="tvu_issue_date" class="form-control"
                                                    max="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}" required>
                                            </div>
                                            @if ($errors->has('tvu_issue_date'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('tvu_issue_date') }}</strong>
                                            </span>
                                            @endif
                                            <div class="col-md-2"></div>
                                        </div>

                                        <div
                                            class="form-group row custom_form_group{{ $errors->has('tvu_expire_date') ? ' has-error' : '' }}">
                                            <label class="col-sm-4 control-label">Expire Date:<span
                                                    class="req_star">*</span></label>
                                            <div class="col-sm-6">
                                                <input type="date" name="tvu_expire_date" class="form-control"
                                                    min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}" required>
                                            </div>
                                            @if ($errors->has('tvu_expire_date'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('tvu_expire_date') }}</strong>
                                            </span>
                                            @endif
                                            <div class="col-md-2"></div>
                                        </div>
                                        
                                        <div class="form-group row custom_form_group{{ $errors->has('tuv_photo') ? ' has-error' : '' }}">
                                            <label class="col-sm-4 control-label">TUV Photo:</label>
                                            <div class="col-sm-5">
                                                <div class="input-group">
                                                    <span class="input-group-btn ">
                                                        <span class="btn btn-default btn-file btnu_browse ">
                                                            Browse… <input type="file" name="tuv_photo" id="imgInp">
                                                        </span>
                                                    </span>
                                                    <input type="text" class="form-control" readonly>
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <img id='img-upload' class="upload_image" />
                                            </div>
                                        </div>

                                    </div>


                                </div>

                                <div class="card-footer card_footer_button text-center">
                                    <button type="submit" id="onSubmit"
                                        class="btn btn-primary waves-effect">SAVE INFO</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- Employee Searching Data Deatils Shaw Start -->
                    <div id="showEmployeeDetails" class="d-none">
                        <div class="row">
                            <div class="col-md-2"></div>
                            <div class="col-md-8">
                                <div class="header_row">
                                    <span class="emp_info">Employee Information Details</span>
                                </div>
                                <table
                                    class="table table-bordered table-striped table-hover custom_view_table show_employee_details_table"
                                    id="showEmployeeDetailsTable">
                                    <tr>
                                        <td> <span class="emp">Project Name:</span> <span id="show_employee_project_name"
                                                class="emp2"></span> </td>
                                        <td> <span class="emp">Department:</span> <span id="show_employee_department"
                                                class="emp2"></span> </td>
                                    </tr>
                                    <tr>
                                        <td> <span class="emp">Employee Status:</span> <span id="show_employee_job_status"
                                                class="emp2"></span> </td>
                                        <td> <span class="emp">Employee Type:</span> <span id="show_employee_type"
                                                class="emp2"></span> </td>

                                    </tr>
                                    <tr>
                                        <td> <span class="emp">Employee Id:</span> <span id="show_employee_id"
                                                class="emp2"></span> </td>
                                        <td> <span class="emp">Employee Designation:</span> <span
                                                id="show_employee_category" class="emp2"></span> </td>
                                    </tr>
                                    <tr>
                                        <td> <span class="emp">Employee Name:</span> <span id="show_employee_name"
                                                class="emp2"></span> </td>
                                        <td> <span class="emp">Date Of Birth:</span> <span id="show_employee_date_of_birth"
                                                class="emp2"></span> </td>
                                    </tr>

                                    <tr>
                                        <td> <span class="emp">Iqama No:</span> <span id="show_employee_akama_no"
                                                class="emp2"></span> </td>
                                        <td> <span class="emp">Mobile Number:</span> <span id="show_employee_mobile_no"
                                                class="emp2"></span> </td>
                                    </tr>

                                    <tr>
                                        <td> <span class="emp">Passport No:</span> <span id="show_employee_passport_no"
                                                class="emp2"></span> </td>
                                        <td> <span class="emp">Agency Name:</span> <span id="show_employee_agency_name"
                                                class="emp2"></span> </td>
                                    </tr>


                                    <tr>
                                        <td> <span class="emp">Working Shift: </span> <span id="show_employee_working_shift"
                                                class="emp2"></span> </td>
                                        <td> <span class="emp">Accomodation Villa Name:</span> <span
                                                id="show_employee_accomodation_ofB_name" class="emp2"></span> </td>
                                    </tr>

                                    <tr>
                                        {{-- <td> <span class="emp">Parmanent Address Details:</span> <span
                                                id="show_employee_address_details" class="emp2"></span> </td> --}}

                                        <td>
                                            <span class="emp">Present Address:</span>
                                            <span id="show_employee_present_address" class="emp2"></span>
                                        </td>
                                        <td>
                                            <span class="emp">Parmanent Address:</span>
                                            <span id="show_employee_address_Ds"></span>,
                                            <span id="show_employee_address_D"></span> ,
                                            <span id="show_employee_address_C" class="emp2"></span>
                                        </td>
                                    </tr>

                                </table>
                            </div>
                            <div class="col-md-2"></div>
                        </div>
                        <!-- Employee Searching Data Deatils Shaw End -->
                    </div>
                    
                 </div>

            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    // Method For Reset All Loaded Data
    function resetEmpInfo() {
        $("#show_employee_address_C").html('');
        $("#show_employee_address_D").html('');
        $("#show_employee_address_Ds").html('');
        $("#show_employee_address_details").html('');
        $("#show_employee_present_address").html('');
        $("#show_employee_agency_name").html('');
        $("#show_employee_passport_no").html('');
        $("#show_employee_mobile_no").html('');
        $("#show_employee_date_of_birth").html('');
        $("#show_employee_name").html('');
        $("#show_employee_category").html('');
        $("#show_employee_id").html('');
        $("#show_employee_job_status").html('');
        $("#show_employee_department").html('');
        $("#show_employee_akama_no").html('');
        $("#show_employee_type").html('');
        $("#show_employee_project_name").html('');
        $("#show_employee_working_shift").html('');
        $("#show_employee_accomodation_ofB_name").html('');
    }

    $('#empl_info').keydown(function (e) {
        if (e.keyCode == 13) {
            singleEmoloyeeDetails();
        }
    })

    //   Single Employee Details Info
    function singleEmoloyeeDetails() {

        resetEmpInfo() // reset All Employe Info
        var searchType = $('#searchBy').find(":selected").val();
        var searchValue = $("#empl_info").val();
         if ($("#empl_info").val().length === 0) {
            showSweetAlertMessage('error','Please Input Valid Data');
            return;
        } 
         $.ajax({
                type: 'POST',
                   url: "{{ route('employee.searching.searching-with-multitype.parameter') }}",
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
                        $("#showEmployeeDetails").removeClass("d-block").addClass("d-none");
                        $("#showEmployeeTUVApplyForm").removeClass("d-block").addClass("d-none");
                    } else {
                        $("span[id='employee_not_found_error_show']").removeClass('d-block').addClass('d-none');
                    }

                    if (response.findEmployee.length > 1) {
                        //showSearchingResultAsMultipleRecords(response.findEmployee);
                        showSweetAlertMessage('error','Multiple Employee Found, Please Contact with Software Support Team');
                    } else {
                        showSearchingEmployee(response.findEmployee[0], response.designation);
                    }

                } // end of success
            }); // end of ajax calling
        
    }

    // End of Method for Router calling
    function showSearchingEmployee(findEmployee, designation) {
        
        /* show employee information in employee table */
        $("#showMultiple_EmployeeDetails").removeClass("d-block").addClass("d-none"); // hide multiple employee list
        $("#showEmployeeDetails").removeClass("d-none").addClass("d-block"); // show signle employee details
        $("#showEmployeeTUVApplyForm").removeClass("d-none").addClass("d-block");

        // Hidden feild employee id, emp auto id
        $('input[id="input_employee_id"]').val(findEmployee.employee_id);
        $('input[id="input_emp_auto_id"]').val(findEmployee.emp_auto_id);

        $("span[id='show_employee_address_C']").text(findEmployee.country_name);
        $("span[id='show_employee_address_D']").text(findEmployee.division_name);
        $("span[id='show_employee_address_Ds']").text(findEmployee.district_name);
        $("span[id='show_employee_address_details']").text(findEmployee.details);
        $("span[id='show_employee_present_address']").text(findEmployee.present_address);
        $("span[id='show_employee_agency_name']").text(findEmployee.agc_title);
        $("span[id='show_employee_passport_no']").text(findEmployee.passfort_no);
        $("span[id='show_employee_mobile_no']").text(findEmployee.mobile_no);
        $("span[id='show_employee_akama_no']").text(findEmployee.akama_no);
        $("span[id='show_employee_date_of_birth']").text(findEmployee.date_of_birth);
        $("span[id='show_employee_name']").text(findEmployee.employee_name);
        $("span[id='show_employee_category']").text(findEmployee.catg_name);
        $("span[id='show_employee_id']").text(findEmployee.employee_id);
        $("span[id='show_employee_job_status']").text(findEmployee.title);
        $("span[id='show_employee_accomodation_ofB_name']").text(findEmployee.ofb_name);



        /* Department name */
        if (findEmployee.department_id == null) {
            $("span[id='show_employee_department']").text("No Assigned Department");
        } else {
            $("span[id='show_employee_department']").text(findEmployee.dep_name);
        }
        /* Employee Type  */
        if (findEmployee.emp_type_id == 1) {
            $("span[id='show_employee_type']").text("Direct Manpower");
        } else {
            $("span[id='show_employee_type']").text("Indirect Manpower");
        }
        /* show project name */
        if (findEmployee.project_id == null) {
            $("span[id='show_employee_project_name']").text("No Assigned Project!");
        } else {
            $("span[id='show_employee_project_name']").text(findEmployee.proj_name);
        }

        /* Employee Work Shift Status  */
        if (findEmployee.isNightShift == 0) {
            $("span[id='show_employee_working_shift']").text("Day Shift");
        } else {
            $("span[id='show_employee_working_shift']").text("Night Shift");
        }

        $('select[name="catg_id"]').val(findEmployee.catg_id);

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
