@extends('layouts.admin-master')
@section('title')
    Emp. Search
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
        <h4 class="pull-left page-title bread_title">Employee Information Search</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active">Employee Information Search</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
              <!-- employee information searching UI -->
            <div class="card-header">
                <div  class="row form-group custom_form_group{{ $errors->has('searchBy') ? ' has-error' : '' }}">
                        <label class="col-md-2 control-label d-block" style="text-align: left;">Employee Searching By
                        </label>
                        <div class="col-md-3">
                            <select class="form-select" name="searchBy" id="searchBy" required>
                                <option value="employee_id"> Employee ID</option>
                                <option value="akama_no">Iqama </option>
                                <option value="passfort_no">Passport</option>
                                <option value="1">Master Searching</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <input type="text" placeholder="Enter ID/Iqama/Passport No"
                                class="form-control" id="empl_info" name="empl_info"
                                value="{{ old('empl_info') }}"
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
                        <div class="col-md-2">
                             @can('employee_salary_show_permission')
                                <button type="button" id="empInfoPrintPreview" class="btn btn-primary waves-effect">Print</button>
                            @endcan
                        </div>
                </div>
            </div>
            <div class="card-body card_form">

                <div class="row">


                    <!-- show employee information -->
                    <div class="col-md-12">
                        <div id="showEmployeeDetails" class="d-none">
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
                                            <div class="header_row">
                                                <span class="emp_info">Employee Information Details</span>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="header_row">
                                                <span class="emp_info">Employee Salary Details</span>
                                            </div>
                                        </th>
                                    </tr>

                                    <tr>
                                        <td>
                                            <span class="emp">Employee Id:</span>
                                            <span id="show_employee_id" class="emp2"></span>
                                        </td>
                                        <td>
                                            <span class="emp">Employee Name:</span>
                                            <span id="show_employee_name" class="emp2"></span>
                                        </td>
                                        <td>
                                            <span class="emp">Basic Amount:</span>
                                            <span id="show_employee_basic" class="emp2"></span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <span class="emp">Iqama No:</span>
                                            <span id="show_employee_akama_no" class="emp2"></span>
                                        </td>
                                        <td>
                                            <span class="emp">Iqama Expire Date:</span>
                                            <span id="show_employee_akama_expire_date" class="emp2"></span>
                                        </td>
                                        <td>
                                            <span class="emp">House Rent:</span>
                                            <span id="show_employee_house_rent" class="emp2"></span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <span class="emp">Passport No:</span>
                                            <span id="show_employee_passport_no" class="emp2"></span>
                                        </td>
                                        <td>
                                            <span class="emp">Salary Type:</span>

                                            <span id="show_employee_salary_type" class="emp2"  style="font-weight:bold;color:red;font-size:18px;"></span>
                                        </td>
                                        <td>
                                            <span class="emp">Mobile Allowance:</span>
                                            <span id="show_employee_mobile_allow" class="emp2"></span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <span class="emp">Agency Name:</span>
                                            <span id="show_employee_agency_name" class="emp2"></span>
                                        </td>
                                        <td>
                                            <span class="emp">Sponsor Name:</span>
                                            <span id="show_employee_sponsor_name" class="emp2"></span>
                                        </td>
                                        <td>
                                            <span class="emp">Hourly Rate:</span>
                                            <span id="show_employee_hourly_rent" class="emp2"></span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td><b>
                                            <span class="emp">Employee Status:</span>
                                            <span id="show_employee_job_status"  class="emp2" style="font-weight:bold;color:red;font-size:18px;" ></span>
                                            </b>
                                        </td>
                                        <td>
                                            <span class="emp">Project Name:</span>
                                            <span id="show_employee_project_name" class="emp2"></span>
                                        </td>
                                        <td>
                                            <span class="emp">Food Allowance:</span>
                                            <span id="show_employee_food_allow" class="emp2"></span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <span class="emp">Employee Type:</span>
                                            <span id="show_employee_type" class="emp2"></span>
                                        </td>
                                        <td>
                                            <span class="emp">Employee Designation:</span>
                                            <span id="show_employee_category" class="emp2"></span>
                                        </td>
                                        <td>
                                            <span class="emp">Medical Allowance:</span>
                                            <span id="show_employee_medical_allow" class="emp2"></span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <span class="emp">Department:</span>
                                            <span id="show_employee_department" class="emp2"></span>
                                        </td>
                                        <td>
                                            <span class="emp">Joining Date:</span>
                                            <span id="show_employee_joining_date" class="emp2"></span>
                                        </td>
                                        <td>
                                            <span class="emp">Local Travels Allowance:</span>
                                            <span id="show_employee_local_travel_allow" class="emp2"></span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <span class="emp">Present Address:</span>
                                            <span id="show_employee_present_address" class="emp2"></span>
                                        </td>
                                        <td>
                                            <span class="emp">Permanent Address:</span>
                                            <span id="show_employee_address_Ds"></span>,
                                            <span id="show_employee_address_D"></span> ,
                                            <span id="show_employee_address_C" class="emp2"></span>
                                        </td>
                                        <td>
                                            <span class="emp">Conveyance Allowance:</span>
                                            <span id="show_employee_conveyance_allow" class="emp2"></span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <span class="emp">Permanent Address:</span>
                                            <span id="show_employee_address_details" class="emp2"></span>
                                        </td>
                                        <td>
                                            <span class="emp">Date Of Birth:</span>
                                            <span id="show_employee_date_of_birth" class="emp2"></span>
                                        </td>
                                        <td>
                                            <span class="emp">Increment No:</span>
                                            <span id="show_employee_increment_no" class="emp2"></span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <span class="emp">Abshar Mob. No:</span>
                                            <span id="show_employee_mobile_no" class="emp2"></span>
                                        </td>
                                        <td>
                                            <span class="emp">  Home Contact :</span>
                                            <span id="show_employee_own_mobile_no" class="emp2"></span>
                                        </td>
                                        <td>
                                            <span class="emp">Increment Amount:</span>
                                            <span id="show_employee_increment_amount" class="emp2"></span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <span class="emp">Email Address:</span>
                                            <span id="show_employee_email" class="emp2"></span>
                                        </td>
                                        <td>
                                            <span class="emp">Marital Status:</span>
                                            <span id="show_employee_metarials" class="emp2"></span>
                                        </td>
                                        <td>
                                            <span class="emp">Others:</span>
                                            <span id="show_employee_others" class="emp2"></span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <span class="emp">Confirmation Date:</span>
                                            <span id="show_employee_confirmation_date" class="emp2"></span>
                                        </td>

                                        <td>
                                            <span class="emp">Accomodation Location:</span>
                                            <span id="show_employee_accomodation_ofB_name" class="emp2"></span>
                                        </td>
                                        <td>
                                            <span class="emp">Insert By</span>
                                            <span id="show_employee_insert_by" class="emp2"></span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <span class="emp">Working Shift</span>
                                            <span id="show_employee_working_shift" class="emp2"></span>
                                        </td>

                                        <td>
                                            <span class="emp">Appointment Date:</span>
                                            <span id="show_employee_appointment_date" class="emp2"></span>
                                        </td>

                                        <td>
                                            <span class="emp">--</span>
                                            <span id="show_employee_system_auto_id" class="emp2"></span>
                                        </td>

                                    </tr>

                                    <tr>
                                        <td>
                                            <span class="emp">Reference Person</span>
                                            <span id="show_employee_ref_person" class="emp2"></span>
                                        </td>

                                        <td>
                                            <span class="emp">Ref. Contact No:</span>
                                            <span id="show_employee_ref_contact_no" class="emp2"></span>
                                        </td>

                                        <td>
                                            <span class="emp">Remarks</span>
                                            <span id="show_employee_remarks" class="emp2"></span>
                                        </td>

                                    </tr>

                                    <tr>
                                        <td id="show_employee_Medical_photo"></td>
                                        <td id="show_employee_Profile_photo"></td>
                                        <td id="show_employee_Appointment_photo"></td>
                                    </tr>

                                    <tr>
                                        <td id="show_employee_covid_certificate"></td>
                                        <td id="show_employee_Pasport_photo"></td>
                                        <td id="show_employee_Iqama_photo"></td>
                                    </tr>
                                </table>
                            </div>
                            <!-- job experience list -->
                            <!--<div class="row" style="margin-top: 50px">-->
                            <!--    <div class="col-lg-12">-->
                            <!--        <div class="card">-->
                            <!--            <div class="card-header">-->
                            <!--                <div class="row">-->
                            <!--                    <div class="col-md-8">-->
                            <!--                        <h3 class="card-title card_top_title"><i-->
                            <!--                                class="fab fa-gg-circle"></i>-->
                            <!--                            Job Experience </h3>-->
                            <!--                    </div>-->
                            <!--                    <div class="clearfix"></div>-->
                            <!--                </div>-->
                            <!--            </div>-->
                            <!--            <div class="card-body">-->
                            <!--                <div class="row">-->
                            <!--                    <div class="col-12">-->
                            <!--                        <div class="table-responsive">-->
                            <!--                            <table id="alltableinfo"-->
                            <!--                                class="table table-bordered custom_table mb-0">-->
                            <!--                                <thead>-->
                            <!--                                    <tr>-->
                            <!--                                        <th>Company Name</th>-->
                            <!--                                        <th>Title</th>-->
                            <!--                                        <th>Total Days</th>-->
                            <!--                                        <th>Designation</th>-->
                            <!--                                        <th>Responsibilty</th>-->
                                                                    <!-- <th>Manage</th> -->
                            <!--                                    </tr>-->
                            <!--                                </thead>-->
                            <!--                                <tbody id="show_employee_job_experience_list">-->



                            <!--                                </tbody>-->
                            <!--                            </table>-->
                            <!--                        </div>-->
                            <!--                    </div>-->
                            <!--                </div>-->
                            <!--            </div>-->
                            <!--        </div>-->
                            <!--    </div>-->
                            <!--</div>-->
                            <!-- Employee Contact person -->
                            <!--<div class="row" style="margin-top: 50px">-->
                            <!--    <div class="col-lg-12">-->
                            <!--        <div class="card">-->
                            <!--            <div class="card-header">-->
                            <!--                <div class="row">-->
                            <!--                    <div class="col-md-8">-->
                            <!--                        <h3 class="card-title card_top_title"><i-->
                            <!--                                class="fab fa-gg-circle"></i>-->
                            <!--                            Contact Person Information </h3>-->
                            <!--                    </div>-->
                            <!--                    <div class="clearfix"></div>-->
                            <!--                </div>-->
                            <!--            </div>-->
                            <!--            <div class="card-body">-->
                            <!--                <div class="row">-->
                            <!--                    <div class="col-12">-->
                            <!--                        <div class="table-responsive">-->
                            <!--                            <table id="alltableinfo"-->
                            <!--                                class="table table-bordered custom_table mb-0">-->
                            <!--                                <thead>-->
                            <!--                                    <tr>-->
                            <!--                                        <th>Person Name</th>-->
                            <!--                                        <th>Mobile No</th>-->
                            <!--                                        <th>Relationship</th>-->

                                                                    <!-- <th>Manage</th> -->
                            <!--                                    </tr>-->
                            <!--                                </thead>-->
                            <!--                                <tbody id="show_employee_contact_person_list"></tbody>-->
                            <!--                            </table>-->
                            <!--                        </div>-->
                            <!--                    </div>-->
                            <!--                </div>-->
                            <!--            </div>-->
                            <!--        </div>-->
                            <!--    </div>-->
                            <!--</div>-->
                        </div>

                    </div>

                    <!-- show Multiple Emmployee information -->
                    <!--<div class="col-md-12">-->
                    <!--    <div id="showMultiple_EmployeeDetails" class="d-none">-->
                    <!--        <div class="row" style="margin-top: 50px">-->
                    <!--            <div class="col-lg-12">-->
                    <!--                <div class="card">-->
                    <!--                    <div class="card-body">-->
                    <!--                        <div class="row">-->
                    <!--                            <div class="col-12">-->
                    <!--                                <div class="table-responsive">-->
                    <!--                                    <table id="alltableinfo"-->
                    <!--                                        class="table table-bordered custom_table mb-0">-->
                    <!--                                        <thead>-->
                    <!--                                            <tr>-->
                    <!--                                                <th>Emp. ID</th>-->
                    <!--                                                <th>Name</th>-->
                    <!--                                                <th>Iqama No</th>-->
                    <!--                                                <th>Passport No</th>-->
                    <!--                                                <th>Job Status </th>-->
                    <!--                                                <th>Working Project </th>-->
                    <!--                                                <th>Sponsor </th>-->
                    <!--                                                <th>Basic Salary </th>-->
                    <!--                                                <th>Hourly Rate </th>-->
                    <!--                                            </tr>-->
                    <!--                                        </thead>-->
                    <!--                                        <tbody id="multiple_employee_details_tbl_list">-->
                    <!--                                        </tbody>-->
                    <!--                                    </table>-->
                    <!--                                </div>-->
                    <!--                            </div>-->
                    <!--                        </div>-->
                    <!--                    </div>-->
                    <!--                </div>-->
                    <!--            </div>-->
                    <!--        </div>-->

                    <!--    </div>-->


                    <!--</div>-->

                     <div class="card-body d-none" id="showMultiple_EmployeeDetails">
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table id="dt-vertical-scroll" class="table table-bordered table-hover" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th>S.N</th>
                                                <th>Emp. ID</th>
                                                <th>Name</th>
                                                <th>Iqama</th>
                                                <th>Passport</th>
                                                <th>Project </th>
                                                <th>Sponsor </th>
                                                <th>Salary </th>
                                                <th>Job Status </th>
                                            </tr>
                                        </thead>
                                        <tbody id="multiple_employee_details_tbl_list">
                                        </tbody>

                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function resetUI() {
        $("#show_employee_Profile_photo").html('');
        $("#show_employee_Pasport_photo").html('');
        $("#show_employee_job_experience_list").html('');
        $("#show_employee_Medical_photo").html('');
        $("#show_employee_Iqama_photo").html('');
        $("#show_employee_Appointment_photo").html('');
        $("#show_employee_covid_certificate").html('');
        $("#multiple_employee_details_tbl_list").html('');

         $("#showEmployeeDetails").removeClass("d-block").addClass("d-none");
         $("#showMultiple_EmployeeDetails").removeClass("d-block").addClass("d-none");
    }

    function typingEmployeeSearchingValue() {
        $("span[id='employee_not_found_error_show']").removeClass('d-block').addClass('d-none');
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

    function showSearchingResultAsMultipleRecords(empList) {
        $("#showEmployeeDetails").removeClass("d-block").addClass("d-none"); // Hide Single EMployee Details
        $("#showMultiple_EmployeeDetails").removeClass("d-none").addClass("d-block"); // Show Multiple Employee list
        $("#multiple_employee_details_tbl_list").html('');
        var emp_records = "";
        var counter = 1;
        $.each(empList, function(key, value) {
            emp_records += `
                                <tr>
                                <td>${counter++}</td>
                                <td>${value.employee_id}</td>
                                <td>${value.employee_name}</td>
                                <td>${value.akama_no},<br> ${value.akama_expire_date}</td>
                                <td>${value.passfort_no},<br> ${value.passfort_expire_date}</td>
                                <td>${value.proj_name}</td>
                                <td>${value.spons_name}</td>
                                <td>${value.hourly_rent == true ? 'Hourly': 'Basic Salary' }</td>
                                <td>${value.title}</td>
                                </tr>
                                `
        });
        $("#multiple_employee_details_tbl_list").html(emp_records);
    }


    // Enter Key Press Event Fire
    $('#empl_info').keydown(function(e) {
        if (e.keyCode == 13) {
            singleEmoloyeeDetails();
        }
    })

     document.getElementById('empInfoPrintPreview').addEventListener('click', function() {
        var searchType = $('#searchBy').find(":selected").val();
        var searchValue = $("#empl_info").val();

         if(searchType == 1){
            showSweetAlertMessage('error', "Print Preview not possible in Master Searching");
            return;
        }

        // Create the query string with parameters
        const queryString = new URLSearchParams({
            searchType: searchType,
            searchValue: searchValue,
        }).toString();

        var parameterValue = queryString; // Set parameter value
        var url = "{{ route('anemployee.details_info.print.privew', ':parameter') }}";
        url = url.replace(':parameter', parameterValue);
        window.open(url, '_blank');

    });


    function singleEmoloyeeDetails() {

        var searchType = $('#searchBy').find(":selected").val();
        var searchValue = $("#empl_info").val();

        if ($("#empl_info").val().length === 0) {
            showSweetAlertMessage("error","Please Input Valid Data");
            return;
        }
        resetUI();
        $.ajax({
                type: 'POST',
                url: "{{ route('employee.searching.searching-with-multitype.parameter') }}",
                data: {
                    search_by: searchType,
                    employee_searching_value: searchValue
                },
                dataType: 'json',
                success: function(response) {

                    if (response.status != 200) {
                        $('input[id="emp_auto_id"]').val(null);
                        $("span[id='employee_not_found_error_show']").text('Employee Not Found ');
                        $("span[id='employee_not_found_error_show']").addClass('d-block').removeClass(
                            'd-none');
                        $("#showEmployeeDetails").removeClass("d-block").addClass("d-none");
                        $("#showMultiple_EmployeeDetails").removeClass("d-block").addClass("d-none");
                        return;

                    } else {
                        $("span[id='employee_not_found_error_show']").removeClass('d-block').addClass(
                            'd-none');
                    }

                    if (response.findEmployee.length > 1) {
                        // multiple employee display
                        showSweetAlertMessage("error","Critical Error Found, Please Contact with Software Support");
                        showSearchingResultAsMultipleRecords(response.findEmployee);
                    } else {
                        showSearchingEmployee(response.findEmployee[0]);
                    }


                } // end of success
            }); // end of ajax calling


    }

    function showSearchingEmployee(findEmployee) {

        /* show employee information in employee table */
        $("#showMultiple_EmployeeDetails").removeClass("d-block").addClass("d-none"); // hide multiple employee list
        $("#showEmployeeDetails").removeClass("d-none").addClass("d-block"); // show signle employee details
        $("span[id='show_employee_id']").text(findEmployee.employee_id);
        $("span[id='show_employee_system_auto_id']").text(findEmployee.emp_auto_id);

        $("span[id='show_employee_name']").text(findEmployee.employee_name);
        $("span[id='show_employee_akama_no']").text(findEmployee.akama_no);
       // $("span[id='show_employee_akama_expire_date']").text(findEmployee.akama_expire_date);
        $("span[id='show_employee_akama_no']").text(findEmployee.akama_no+", "+findEmployee.akama_expire_date);

       // $("span[id='show_employee_passport_no']").text(findEmployee.passfort_no);
          $("span[id='show_employee_passport_no']").text(findEmployee.passfort_no+", "+findEmployee.passfort_expire_date);

        $("span[id='show_employee_salary_type']").text((findEmployee.hourly_employee == 1 ? "Hourly":"Basic"));

        var job_status = (findEmployee.job_status > 0 ? findEmployee.title : "Waiting for Approval") + (findEmployee.salary_status == 1 ? ', Salary: Active' : ", Salary: Hold");

        $("span[id='show_employee_job_status']").text(job_status);

        /* show project name */
        if (findEmployee.project_id == null) {
            $("span[id='show_employee_project_name']").text("No Assigned Project!");
        } else {
            $("span[id='show_employee_project_name']").text(findEmployee.proj_name);
        }

        $("span[id='show_employee_agency_name']").text(findEmployee.agc_title);

        /* Show sponsor name */
        if (findEmployee.sponsor_id == null) {
            $("span[id='show_employee_sponsor_name']").text("No Assigned Sponsor!");
        } else {
            $("span[id='show_employee_sponsor_name']").text(findEmployee.spons_name);
        }


        /* Direct And Indirect Status */
        if (findEmployee.emp_type_id == 2) {
            $("#work_hours_field").addClass('d-none').removeClass('d-block');
            $("#work_hours_field_custom").val(0);
        } else {
            $("#work_hours_field").addClass('d-block').removeClass('d-none');
        }


        /* Employee Type  */
        if (findEmployee.emp_type_id == 1) {
            $("span[id='show_employee_type']").text("Direct Manpower");
        } else {
            $("span[id='show_employee_type']").text("Indirect Manpower");
        }

        /* Employee Work Shift Status  */
        if (findEmployee.isNightShift == 0) {
            $("span[id='show_employee_working_shift']").text("Day Shift");
        } else {
            $("span[id='show_employee_working_shift']").text("Night Shift");
        }

        /* Employee Address Details  */
        $("span[id='show_employee_category']").text(findEmployee.catg_name);
        $("span[id='show_employee_address_C']").text(findEmployee.country_name);
        $("span[id='show_employee_address_D']").text(findEmployee.division_name);
        $("span[id='show_employee_address_Ds']").text(findEmployee.district_name);
        $("span[id='show_employee_address_details']").text(findEmployee.details);
        $("span[id='show_employee_present_address']").text(findEmployee.present_address);

        /* Employement Details  */
        $("span[id='show_employee_confirmation_date']").text(findEmployee.confirmation_date);
        $("span[id='show_employee_appointment_date']").text(findEmployee.appointment_date);
        $("span[id='show_employee_date_of_birth']").text(findEmployee.date_of_birth);

        $("span[id='show_employee_mobile_no']").text(findEmployee.mobile_no+", Mobile No: "+ (findEmployee.phone_no != null ? findEmployee.phone_no :""));

        $("span[id='show_employee_own_mobile_no']").text(findEmployee.phone_no);
        $("span[id='show_employee_email']").text(findEmployee.email);
        $("span[id='show_employee_joining_date']").text(findEmployee.joining_date);
        $("span[id='show_employee_accomodation_ofB_name']").text(findEmployee.ofb_name);
        $("span[id='show_employee_insert_by']").text(findEmployee.entered_id+", Updated by "+findEmployee.update_by);

        $("span[id='show_employee_ref_person']").text(findEmployee.ref_employee_id);
        $("span[id='show_employee_ref_contact_no']").text(findEmployee.ref_contact_no);
        $("span[id='show_employee_remarks']").text(findEmployee.remarks);


        /* Maritial Status  */
        if (findEmployee.maritus_status == 0) {
            $("span[id='show_employee_metarials']").text('Unmarried');
        } else {
            $("span[id='show_employee_metarials']").text('Married');
        }

        /* Show Employee Salary Details */

        /*
        $("span[id='show_employee_basic']").text(findEmployee.basic_amount);
        $("span[id='show_employee_house_rent']").text(findEmployee.house_rent);
        $("span[id='show_employee_hourly_rent']").text(findEmployee.hourly_rent);
        $("span[id='show_employee_mobile_allow']").text(findEmployee.mobile_allowance);
        $("span[id='show_employee_food_allow']").text(findEmployee.food_allowance);
        $("span[id='show_employee_medical_allow']").text(findEmployee.medical_allowance);
        $("span[id='show_employee_local_travel_allow']").text(findEmployee.local_travel_allowance);
        $("span[id='show_employee_conveyance_allow']").text(findEmployee.conveyance_allowance);
        $("span[id='show_employee_increment_no']").text(findEmployee.increment_no);
        $("span[id='show_employee_increment_amount']").text(findEmployee.increment_amount);
        $("span[id='show_employee_food_allowance']").text(findEmployee.food_allowance);
        $("span[id='show_employee_saudi_tax']").text(findEmployee.saudi_tax);
        $("span[id='show_employee_others']").text(findEmployee.others1);
        */

        /* Department name */
        if (findEmployee.dept_id == null) {
            $("span[id='show_employee_department']").text("No Assigned Department");
        } else {
            $("span[id='show_employee_department']").text(findEmployee.dep_name);
        }


        /* Employee Uploaded File Details With Download System */
        var passport = `
                            <td class="emp" style="text-align: center" colspan="3">Passport:&nbsp;&nbsp;
                                <img height="50" width="50" src="{{ asset('uploads/zip.jpg') }}" alt="">

                            <span class="emp2" style="text-align: center; text-transform: capitalize;" colspan="3"><a target="_blank" href= "`+ getAttachmentUrl(findEmployee.pasfort_photo) + ` "   class="btn btn-danger">Download</a></span></td>
                        `
        $("#show_employee_Pasport_photo").append(passport);
        var Medical = `
                            <td class="emp" style="text-align: center" colspan="3"> Medical Report :
                                <img height="50" width="50" src="{{ asset('uploads/zip.jpg') }}" alt="">

                            <span class="emp2" style="text-align: center; text-transform: capitalize;" colspan="3"><a target="_blank" href= "`+ getAttachmentUrl(findEmployee.medical_report) + ` "    class="btn btn-danger">Download</a></span></td>
                        `
        $("#show_employee_Medical_photo").append(Medical);

        var Iqama = `
                        <td class="emp" style="text-align: center" colspan="3"> Iqama :
                            <img height="50" width="50" src="{{ asset('uploads/zip.jpg') }}" alt="">

                        <span class="emp2" style="text-align: center; text-transform: capitalize;" colspan="3"><a target="_blank" href= "`+ getAttachmentUrl(findEmployee.akama_photo) + ` " class="btn btn-danger">Download</a></span></td>
                    `
        $("#show_employee_Iqama_photo").append(Iqama);

        var Appointment = `
                            <td class="emp" style="text-align: center" colspan="3"> Appointment :
                                <img height="50" width="50" src="{{ asset('uploads/zip.jpg') }}" alt="">

                            <span class="emp2" style="text-align: center; text-transform: capitalize;" colspan="3"><a target="_blank" href="`+ getAttachmentUrl(findEmployee.employee_appoint_latter) + ` "  class="btn btn-danger">Download</a></span></td>
                        `
        $("#show_employee_Appointment_photo").append(Appointment);

        var Profile = `
                        <td class="emp" style="text-align: center" colspan="3"> Photo :
                            <img height="50" width="50" src="{{ asset('uploads/zip.jpg') }}" alt="">

                        <span class="emp2" style="text-align: center; text-transform: capitalize;" colspan="3"><a target="_blank" href= "`+ getAttachmentUrl(findEmployee.profile_photo) + `" class="btn btn-danger">Download</a></span></td>
                    `
        $("#show_employee_Profile_photo").append(Profile);

        var Covid_Certificate = `
                                    <td class="emp" style="text-align: center" colspan="3"> COVID Report
                                        <img height="50" width="50" src="{{ asset('uploads/zip.jpg') }}" alt="">

                                    <span class="emp2" style="text-align: center; text-transform: capitalize;" colspan="3"><a target="_blank" href="`+ getAttachmentUrl(findEmployee.covid_certificate) + ` "   class="btn btn-danger">Download</a></span></td>
                                `
        $("#show_employee_covid_certificate").append(Covid_Certificate);

    }


    function getAttachmentUrl(path)
    {
        return "{{ config('app.aws_s3_bucket_url') }}" + path;
    }
</script>
@endsection
