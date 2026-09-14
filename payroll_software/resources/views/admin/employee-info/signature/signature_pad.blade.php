@extends('layouts.admin-master')
@section('title') Signature Pad @endsection
@section('content')

<!--  
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/css/bootstrap.css"> -->
  
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script> 
    <link type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/south-street/jquery-ui.css" rel="stylesheet"> 
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script type="text/javascript" src="http://keith-wood.name/js/jquery.signature.js"></script>
  
    <link rel="stylesheet" type="text/css" href="http://keith-wood.name/css/jquery.signature.css">
  
    <style>
        .kbw-signature { width: 50%; height: 100px;}
        #sig canvas{
            width: 100% !important;
            height: auto;
        }
    </style>

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
  
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Upload Employee Signature</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active">Signature</li>
        </ol>
    </div>
</div>


<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header"></div>
            <div class="card-body card_form">
                 <!-- employee information searching UI -->
                <div class="row">
                   
                    <div class="col-md-12">
                        <div class="card"> 
                                <div class="card-body card_form" style="padding-top: 0;">

                                    <div class="row form-group custom_form_group{{ $errors->has('searchBy') ? ' has-error' : '' }}">
                                    <div class="col-md-2"> </div>
                                        <!-- <label class="col-md-1 control-label d-block" style="text-align: left;">Employee Search By  </label> -->
                                        <div class="col-md-4">
                                         
                                            <select class="form-control" name="searchBy" id="searchBy" required>
                                                <option value="employee_id">Searching By Employee ID</option>
                                                <option value="akama_no">Searching By Iqama </option>
                                                <option value="passfort_no">Searching By Passport</option>
                                            </select>
                                        </div>

                                        <div class="col-md-2">
                                            <input type="text" placeholder="Enter ID/Iqama/Passport No" class="form-control"
                                                id="empl_info" name="empl_info" value="{{ old('empl_info') }}" onkeyup="typingEmployeeSearchingValue()" required>
                                                <span id="employee_not_found_error_show" class="d-none" style="color: red"></span>
                                            @if ($errors->has('empl_info'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('empl_info') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                        <div class="col-md-2"> <button type="submit" onclick="singleEmoloyeeDetails()" style="margin-top: 2px"
                                                         class="btn btn-primary waves-effect">SEARCH</button> </div>

                                             <div class="col-md-2"> </div>
                                    </div>

                                </div>
                                
                        </div>
                    
                </div>

                <!-- show employee information -->
                <div class="row">
                    <div class="col-md-12">
                        <div id="showEmployeeDetails" class="d-none">
                            <div class="row">
                                <!-- employee Deatils -->
                                <div class="col-md-12">
                                    <div class="header_row">
                                        <span class="emp_info">Employee Information Details</span>
                                    </div>
                                    <table
                                        class="table table-bordered table-striped table-hover custom_view_table show_employee_details_table"
                                        id="showEmployeeDetailsTable">

                                        <tr>
                                            <td> <span class="emp">Employee Id:</span> <span id="show_employee_id"
                                                    class="emp2"></span> </td>
                                            <td> <span class="emp">Basic Amount:</span> <span
                                                            id="show_employee_basic" class="emp2"></span> </td>

                                        </tr>
                                        <tr>
                                            <td> <span class="emp">Employee Name:</span> <span id="show_employee_name"
                                                    class="emp2"></span> </td>
                                        <td> <span class="emp">Hourly Rate:</span> <span
                                                            id="show_employee_hourly_rent" class="emp2"></span> </td>
                                            
                                        </tr>
                                        <tr>
                                            <td> <span class="emp">Job Status:</span> <span id="show_employee_job_status"
                                                    class="emp2"></span> </td>
                                            <td> <span class="emp">Mobile Allowance:</span> <span
                                                            id="show_employee_mobile_allow" class="emp2"></span> </td>
                                        </tr>
                                        <tr>
                                            <td> <span class="emp">Iqama No:</span> <span id="show_employee_akama_no"
                                                    class="emp2"></span> </td>

                                        <td> <span class="emp">Food Allowance:</span> <span
                                                            id="show_employee_food_allow" class="emp2"></span> </td>
                                        </tr>
                                        <tr>
                                            <td> <span class="emp">Iqama Expire Date:</span> <span
                                                    id="show_employee_akama_expire_date" class="emp2"></span> </td>
                                                    <td> <span class="emp">Sponsor Name:</span> <span
                                                    id="show_employee_sponsor_name" class="emp2"></span> </td>
                                        </tr>   
                                        <tr>
                                            <td> <span class="emp">Passport No:</span> <span id="show_employee_passport_no"
                                                    class="emp2"></span> </td>
                                        <td> <span class="emp">Passport Expire Date:</span> <span
                                                    id="show_employee_passport_expire_date" class="emp2"></span> </td>
                                        </tr>
                                    
                                        <tr>
                                            <td> <span class="emp">Mobile Number:</span> <span id="show_employee_mobile_no"
                                                    class="emp2"></span> </td>
                                        <td> <span class="emp">Email Address:</span> <span id="show_employee_email"
                                                    class="emp2"></span> </td>
                                        </tr>  
                                        <tr>
                                            <td> <span class="emp">Joining Date:</span> <span
                                                    id="show_employee_joining_date" class="emp2"></span> </td>
                                        </tr>
                                    
                                    </table>
                                </div>
                                
                            </div> 
                        </div> 
                    </div>
                </div>
                <!-- show Multiple Emmployee information -->
                <div class="col-md-12">
                    <div id="showMultiple_EmployeeDetails" class="d-none">                          
                        <div class="row" style="margin-top: 50px">
                            <div class="col-lg-12">
                                <div class="card">                                   
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="table-responsive">
                                                    <table id="alltableinfo"
                                                        class="table table-bordered custom_table mb-0">
                                                        <thead>
                                                            <tr>
                                                                <th>Emp. ID</th>
                                                                <th>Name</th>
                                                                <th>Iqama No</th>
                                                                <th>Passport No</th>
                                                                <th>Job Status </th>
                                                                <th>Working Project </th>
                                                                <th>Sponsor </th>
                                                                <th>Basic Salary </th>  
                                                                <th>Hourly Rate </th>                                                               
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

                 <!-- show Signature Pad -->
                 <div class="row">
                        <div class="col-md-6 offset-md-3 mt-5">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Signature Pad </h5>
                                </div>
                                <div class="card-body">
                                        <!-- @if ($message = Session::get('success'))
                                            <div class="alert alert-success  alert-dismissible">
                                                <button type="button" class="close" data-dismiss="alert">×</button>  
                                                <strong>{{ $message }}</strong>
                                            </div>
                                        @endif -->
                                        <form method="POST" action="{{ route('employee.signature.upload.request') }}">
                                            @csrf
                                            <div class="col-md-12"> 
                                                
                                                <div id="sig" ></div>
                                                <br/>
                                                <button id="clear" class="btn btn-danger">Clear Signature</button>
                                                <textarea id="signature64" name="signed" style="display: none"> </textarea>

                                                <button class="btn btn-success">Save</button>
                                            </div> 
                                       
                                        </form>
                                </div>
                            </div>
                        </div>
                </div>

            </div>
          
        </div>
    </div>
</div>
<script type="text/javascript">

    function resetUI(){
  
    }

    function typingEmployeeSearchingValue(){
        $("span[id='employee_not_found_error_show']").removeClass('d-block').addClass('d-none');
    }

    function showSearchingResultAsMultipleRecords(empList){
        $("#showEmployeeDetails").removeClass("d-block").addClass("d-none"); // Hide Single EMployee Details
        $("#showMultiple_EmployeeDetails").removeClass("d-none").addClass("d-block"); // Show Multiple Employee list
        $("#multiple_employee_details_tbl_list").html('');        
                   var emp_records = "";
                                 $.each(empList, function(key, value) {                                    

                                    emp_records += `
                                                <tr>
                                                <td>${value.employee_id}</td>
                                                <td>${value.employee_name}</td>
                                                <td>${value.akama_no}, ${value.akama_expire_date}</td>
                                                <td>${value.passfort_no}, ${value.passfort_expire_date}</td>
                                                <td>${value.status.title}</td>
                                                <td>${value.project.proj_name}</td>
                                                <td>${value.sponsor.spons_name}</td>
                                                <td>${value.basic_amount}</td>
                                                <td>${value.hourly_rent}</td>
                                                </tr>
                                                `
                                });
                                 $("#multiple_employee_details_tbl_list").html(emp_records);
    }
 

    function singleEmoloyeeDetails(){

                var searchType = $('#searchBy').find(":selected").val();
                var searchValue = $("#empl_info").val();
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
                            
                            if (response.success == false) {
                                $('input[id="emp_auto_id"]').val(null);
                                $("span[id='employee_not_found_error_show']").text('Employee Not Found ');
                                $("span[id='employee_not_found_error_show']").addClass('d-block').removeClass('d-none');
                                $("#showEmployeeDetails").removeClass("d-block").addClass("d-none");
                                $("#showMultiple_EmployeeDetails").removeClass("d-block").addClass("d-none");
                              
                            } else {                               
                                $("span[id='employee_not_found_error_show']").removeClass('d-block').addClass('d-none');                               
                            } 

                             if(response.total_emp > 1){                             
                                 showSearchingResultAsMultipleRecords(response.findEmployee);
                                // alert(response.total_emp); 
                             }else { 
                                 showSearchingEmployee(response.findEmployee[0]);
                            }
                             
                                
                } // end of success
            }); // end of ajax calling
    }
    // End of Method for Router calling
    
    function showSearchingEmployee(findEmployee){
        
                        /* show employee information in employee table */

                                $("#showMultiple_EmployeeDetails").removeClass("d-block").addClass("d-none"); // hide multiple employee list
                                $("#showEmployeeDetails").removeClass("d-none").addClass("d-block"); // show signle employee details
                                $("span[id='show_employee_id']").text(findEmployee.employee_id);
                                $("span[id='show_employee_name']").text(findEmployee.employee_name);
                                $("span[id='show_employee_akama_no']").text(findEmployee.akama_no);
                                $("span[id='show_employee_akama_expire_date']").text(findEmployee.akama_expire_date);

                                $("span[id='show_employee_passport_no']").text(findEmployee.passfort_no);
                                $("span[id='show_employee_passport_expire_date']").text(findEmployee.passfort_expire_date);

                                $("span[id='show_employee_job_status']").text(findEmployee.status.title);
                                
                                /* show project name */
                                if (findEmployee.project_id == null) {
                                    $("span[id='show_employee_project_name']").text("No Assigned Project!");
                                } else {
                                    $("span[id='show_employee_project_name']").text(findEmployee.project.proj_name);
                                }

                                // 
                                
                                $("span[id='show_employee_agency_name']").text(findEmployee.agency.title);

                                /* Show sponsor name */
                                if (findEmployee.sponsor_id == null) {
                                    $("span[id='show_employee_sponsor_name']").text("No Assigned Sponsor!");
                                } else {
                                    $("span[id='show_employee_sponsor_name']").text(findEmployee.sponsor.spons_name);
                                }


                                /* Direct And Indirect Status */
                                if (findEmployee.emp_type_id == 2) {
                                    $("#work_hours_field").addClass('d-none').removeClass('d-block');
                                    $("#work_hours_field_custom").val(0);
                                } else {
                                    $("#work_hours_field").addClass('d-block').removeClass('d-none');
                                }
                                
                        
                                /* Department name */
                                if (findEmployee.department_id == null) {
                                    $("span[id='show_employee_department']").text("No Assigned Department");
                                } else {
                                    $("span[id='show_employee_department']").text(findEmployee.department.dep_name);
                                }
                            
                                /* Employee Type  */
                                if (findEmployee.emp_type_id == 1) {
                                    $("span[id='show_employee_type']").text("Direct Manpower");
                                } else {
                                    $("span[id='show_employee_type']").text("Indirect Manpower");
                                }

                                /* Employee Address Details  */
                                $("span[id='show_employee_category']").text(findEmployee.category.catg_name);
                                $("span[id='show_employee_address_C']").text(findEmployee.country.country_name);
                                $("span[id='show_employee_address_D']").text(findEmployee.division.division_name);
                                $("span[id='show_employee_address_Ds']").text(findEmployee.district.district_name);
                                $("span[id='show_employee_address_details']").text(findEmployee.details);
                                $("span[id='show_employee_present_address']").text(findEmployee.present_address);

                                /* Employement Details  */
                                $("span[id='show_employee_confirmation_date']").text(findEmployee.confirmation_date);
                                $("span[id='show_employee_appointment_date']").text(findEmployee.appointment_date);
                                $("span[id='show_employee_date_of_birth']").text(findEmployee.date_of_birth);
                                $("span[id='show_employee_mobile_no']").text(findEmployee.mobile_no);
                                $("span[id='show_employee_email']").text(findEmployee.email);
                                $("span[id='show_employee_joining_date']").text(findEmployee.joining_date);
                                
                               
                          
                            
    }






</script>


<script type="text/javascript">
    var sig = $('#sig').signature({syncField: '#signature64', syncFormat: 'PNG'});
    $('#clear').click(function(e) {
        e.preventDefault();
        sig.signature('clear');
        $("#signature64").val('');
    });
</script>



@endsection
