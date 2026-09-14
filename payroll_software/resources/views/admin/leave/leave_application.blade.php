@extends('layouts.admin-master')
@section('title') Leave Form @endsection
@section('content')
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Leave Form</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active"> Leave Form</li>
        </ol>
    </div>
</div>
 
<!-- Session Message Flash -->
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
          <strong>{{Session::get('error')}} </strong>
      </div>
      @endif
  </div>
  <div class="col-md-2"></div>
</div>


<!-- employee information searching with (id, passport, iqama) Start -->
<div class="row d-block">
  <div class="col-md-12">
      <div class="card">
          <div class="card-body card_form" style="padding: 0;">
              <div
                  class="form-group row custom_form_group{{ $errors->has('searchBy') ? ' has-error' : '' }}">
                  <label class="col-md-1 control-label d-block" style="text-align: right;">Searching By</label>
                  <div class="col-md-4">
                      <select class="form-select" name="searchBy" id="searchBy" required>
                          <option value="employee_id">Employee ID</option>
                          <option value="akama_no">Iqama </option>
                          <option value="passfort_no">Passport</option>
                      </select>
                  </div>

                  <div class="col-md-3">
                      <input type="text" placeholder="Enter ID/Iqama/Passport No" class="form-control"
                          id="empl_info" name="empl_info" value="{{ old('empl_info') }}"
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
                      <button type="submit" onclick="singleEmoloyeeDetails()" class="btn btn-primary waves-effect">Search</button>
                  </div>
                   <div class="col-md-2">
                    <button type="submit" id="processLeaveAppLicationForm" class="btn btn-primary waves-effect">Print Form</button>
                  </div>
              </div>
          </div>            
      </div>
  </div>
</div>

<!-- Employee Searching Data Deatils Show Start -->
<div class="row d-none" id="employee_searching_result_section">
  {{-- Employee Information --}}
  <div class="col-md-12">
      <table  class="table table-bordered table-striped table-hover custom_view_table show_employee_details_table">
          <tr>
              <td>
                  <span class="emp">Employee Id:</span> <span id="show_employee_id"
                      class="emp2"></span> </td>
                      <td> <span class="emp">Employee Status:</span> <span
                          id="show_employee_job_status" class="emp2"  style="font-weight:bold;color:red;font-size:18px;"></span>
                          <span class="emp">, Trade:</span> <span
                          id="show_employee_category" class="emp2"></span>
                  </td>
              
          </tr>
          <tr>
              <td> <span class="emp">Employee Name:</span> <span id="show_employee_name"
                      class="emp2"></span> </td>                
              <td> <span class="emp">Project Name:</span> <span id="show_employee_project_name" class="emp2"></span>
                  <span class="emp">, Working: </span> <span
                          id="show_employee_working_shift" class="emp2"></span> 
                  
              </td>
          </tr>

          <tr>
              <td> <span class="emp">Passport No:</span> <span id="show_employee_passport_no"
                  class="emp2"></span>
                  <span class="emp">&nbsp;&nbsp; </span> <span id="show_employee_passport_file" class="emp2"></span>
              </td>
              <td> <span class="emp">Iqama No:</span> <span id="show_employee_akama_no"
                          class="emp2"></span>
                   <span class="emp">&nbsp;&nbsp; </span> <span id="show_employee_iqama_file" class="emp2"></span>
              </td>                 
          </tr> 

          <tr>               
              <td> <span class="emp">Employee Type:</span> <span id="show_employee_type"
                      class="emp2"></span> </td>  
              <td> <span class="emp">Agency Name:</span> <span id="show_employee_agency_name"
                          class="emp2"></span> </td>       
           </tr>             

          <tr>
              <td> <span class="emp">Sponsor Name: </span> <span
                      id="show_employee_working_sponsor_name" class="emp2"></span> </td>                  
              <td> 
                      <span class="emp">Mobile Number:</span> <span id="show_employee_mobile_no"
                      class="emp2"></span>
                      <span class="emp">,Home Contact:</span> <span id="show_employee_phone_no"
                      class="emp2"></span>
                  </td>
          </tr>

          <tr> 
              <td> <span class="emp">Accomodation Location:</span> <span
                  id="show_employee_accomodation_ofB_name" class="emp2"></span>                    
              </td>
              <td>
                  <span class="emp">Permanent Address:</span>
                  <span id="show_employee_address_Ds"></span>,
                  <span id="show_employee_address_D"></span> ,
                  <span id="show_employee_address_C" class="emp2"></span>
              </td>
          </tr>

      </table>
  </div>
  
  {{-- Leave Application UI  --}}
  <div id="emp_leave_form">
      <form class="form-horizontal" id="registration" method="post" enctype="multipart/form-data" action="{{ route('leave.application.submit.request') }}">
          @csrf
          <div class="card">
              <div class="card-body card_form">
                  <h4 style="text-align: center; paddding-top:5px;">Leave Application Form </h4>                                
                  <input type="text" class="form-control" id="app_employee_id" name="app_employee_id" value="" hidden required>   
                  
                  <div class="form-group row custom_form_group">
                        <label for="" class="col-sm-2 control-label">Mobile No <span class="req_star">*</span> </label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" value="" id="mobile_no" name="mobile_no" disabled placeholder="Enter Mobile Number">
                        </div>
                        <label for="" class="col-sm-2 control-label">Home Contact No<span class="req_star">*</span>  </label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" value="" id="home_mobile_no" name="home_mobile_no" disabled placeholder="Enter Home Contact Number" >
                        </div>
                    </div>
                  
                <div class="form-group row custom_form_group">
                      <label class="col-sm-2 control-label">Leave Start</label>
                      <div class="col-sm-4">
                          <input type="date" name="start_date"  name="start_date" class="form-control"
                               value="{{ date('Y-m-d') }}" required >
                      </div>
                      <label class="col-sm-2 control-label">Leave For</label>
                      <div class="col-sm-4"> 
                          <select class="form-select" name="leave_reason_id" id="leave_reason_id" required>
                            @foreach($leave_reasons as $reason)
                              <option value="{{ $reason->lev_reas_id}}">{{ $reason->lev_reas_name }}</option>
                            @endforeach
                          </select>                             
                      </div>
                </div>                    
                <div class="form-group row custom_form_group">                    
                      <label class="col-sm-2 control-label">Leave End</label>
                        <div class="col-sm-4">
                          <input type="date" name="end_date"  name="end_date" class="form-control"
                            min="{{date('Y-m-d',strtotime('1 days')) }}"  value="{{date('Y-m-d',strtotime('1 days')) }}" required>
                        </div>

                        <label class="col-sm-2 control-label text-right">Application Date</label>
                        <div class="col-sm-4">
                          <input type="date" name="app_date"  name="app_date" class="form-control"
                              max="{{ date('Y-m-d') }}"    value="{{ date('Y-m-d') }}" required>
                      </div> 
                </div>
                <div class="form-group row custom_form_group">                    
                      <label class="col-sm-2 control-label text-right">Reference By </label>
                      <div class="col-sm-4">
                        <input type="text" class="form-control typeahead" placeholder="Reference By"
                        name="reference_by" id="reference_by" >
                      </div>
                        <label class="col-sm-2 control-label text-right">Leave Contract</label>
                        <div class="col-sm-4">
                                  <select class="form-select" name="leave_contract" id="leave_contract" required >
                                     <option value="">Please Select Leave Contract</option>
                                     <option value="1">1 Year</option>
                                     <option value="2">2 Years</option>
                                     <option value="3">3 Years</option>
                                </select>
                            <select class="form-control" name="application_status" id="application_status" hidden>
                                @foreach($application_status as $ar)
                                    <option value="{{ $ar->leav_sta_auto_id}}">{{ $ar->status_title }}</option>
                                @endforeach
                            </select>
                        </div>
                                         
                </div> 
                <div class="form-group row custom_form_group">                 
                    
                    <label class="col-sm-2 control-label text-right">Remarks</label>
                    <div class="col-sm-10"> 
                       <textarea class="form-control" rows="2" placeholder="Remarks Here"   id="remarks"
                       name="remarks"></textarea>
                    </div>                      
                </div> 
                <div class="form-group row custom_form_group">
                    <label class="col-sm-2 control-label">Application File</label>
                    <div class="col-sm-4">
                        <div class="input-group">
                            <span class="input-group-btn">
                                <span class="btn btn-default btn-file btnu_browse">
                                    Browse… <input type="file" name="leave_paper" id="imgInp4" required>
                                </span>
                            </span>
                            <input type="text" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <img id='img-upload4' class="upload_image" />
                    </div>
                </div>
                  
              </div>
              <div class="card-footer card_footer_button text-center">
                  <button type="submit" id="onSubmit" class="btn btn-primary waves-effect">SAVE</button>
              </div>
          </div>
      </form>
  </div>

</div>




 

<script type="text/javascript">

  // Method For Reset All Loaded Data
  function resetEmpInfo() {
      $("#show_employee_address_C").html('');
      $("#show_employee_address_D").html('');
      $("#show_employee_address_Ds").html('');
      $("#show_employee_address_details").html('');    
      $("#show_employee_agency_name").html('');
      $("#show_employee_passport_no").html('');
      $("#show_employee_mobile_no").html('');
      $("#show_employee_phone_no").html('');        
      $("#show_employee_name").html('');
      $("#show_employee_category").html('');
      $("#show_employee_id").html('');
      $("#show_employee_job_status").html('');
      $("#show_employee_akama_no").html('');
      $("#show_employee_type").html('');
      $("#show_employee_project_name").html('');
      $("#show_employee_working_shift").html('');
      $("#show_employee_accomodation_ofB_name").html('');
      $("#show_employee_working_sponsor_name").html('');
      $("#show_employee_passport_file").html('');
      $("#show_employee_iqama_file").html('');        
  }
  $('#empl_info').keydown(function (e) {
      if (e.keyCode == 13) {
          $("#employee_searching_result_section").removeClass("d-block").addClass("d-none"); 
          $("#showMultiple_EmployeeDetails").removeClass("d-block").addClass("d-none"); // hide multiple employee list
          $("#update_form_section").removeClass("d-block").addClass("d-none");
          singleEmoloyeeDetails();
      }
  })


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

  //   Single Employee Details Info
  function singleEmoloyeeDetails() {

      resetEmpInfo() // reset All Employe Info
      var searchType = $('#searchBy').find(":selected").val();
      var searchValue = $("#empl_info").val();
          if ($("#empl_info").val().length === 0) {
              showSweetAlertMessage('error',"Please Enter Employee ID/Iqama/Passport Number");           
          }  
          $.ajax({
              type: 'POST',
              url: "{{ route('active.employee.searching.searching-with-multitype.parameter') }}",
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
                      $("#employee_searching_result_section").removeClass("d-block").addClass("d-none"); 
                      $("#showMultiple_EmployeeDetails").removeClass("d-block").addClass("d-none"); // hide multiple employee list
                      $("#update_form_section").removeClass("d-block").addClass("d-none");
                      return ;
                  }                  
                  
                  if (response.total_emp > 1) {
                      showSearchingResultAsMultipleRecords(response.findEmployee);
                       alert('Mone Than One Employee Found,Please Inform this issue to Software Engineer');
                  } else {
                      showSearchingEmployee(response.findEmployee[0], response.empOfficeBuilding, response.getAllProject, response.allEmployeeStatus, response.designation, response.agencies, response.sponsors);
                  }
              }, // end of success
              error:function(response){                 
                  showSweetAlertMessage('error',"Operation Failed, Please try Again");  
              }
          }); // end of ajax calling
     
  }

  // End of Method for Router calling
  function showSearchingEmployee(findEmployee, empOfficeBuilding, getAllProject, allEmployeeStatus, designation, agencies, sponsors) {
      /* show employee information in employee table */
      $("#showMultiple_EmployeeDetails").removeClass("d-block").addClass("d-none"); // hide multiple employee list     
      $("span[id='employee_not_found_error_show']").removeClass('d-block').addClass('d-none');
      $("#employee_searching_result_section").removeClass('d-none').addClass("d-block"); // show signle employee details  
      $("#update_form_section").removeClass('d-none').addClass("d-block"); // show update form 

      // Hidden feild employee id, emp auto id, current sponsor id
      $('input[id="input_employee_id"]').val(findEmployee.employee_id);
      $('input[id="input_emp_auto_id"]').val(findEmployee.emp_auto_id);
      $('input[id="input_emp_sponsor_id"]').val(findEmployee.sponsor_id);

      $("span[id='show_employee_address_C']").text(findEmployee.country_name);
      $("span[id='show_employee_address_D']").text(findEmployee.division_name);
      $("span[id='show_employee_address_Ds']").text(findEmployee.district_name);
      $("span[id='show_employee_address_details']").text(findEmployee.details);
      $("span[id='show_employee_agency_name']").text(findEmployee.agc_title);
      $("span[id='show_employee_passport_no']").text(findEmployee.passfort_no);    
      $("span[id='show_employee_mobile_no']").text(findEmployee.mobile_no+", Mobile No: "+ (findEmployee.phone_no != null ? findEmployee.phone_no :""));

      $("span[id='show_employee_phone_no']").text(findEmployee.phone_no);        
      $("span[id='show_employee_akama_no']").text(findEmployee.akama_no);
      $("span[id='show_employee_name']").text(findEmployee.employee_name);
      $("span[id='show_employee_category']").text(findEmployee.catg_name);
      $("span[id='show_employee_id']").text(findEmployee.employee_id); 
      

                  
      var job_status = (findEmployee.job_status > 0 ? findEmployee.title : "Waiting for Approval") + (findEmployee.salary_status == 1 ? ', Salary: Active' : ", Salary: Hold");

      $("span[id='show_employee_job_status']").text(job_status);
      $("span[id='show_employee_accomodation_ofB_name']").text(findEmployee.ofb_name);
      $("span[id='show_employee_working_sponsor_name']").text(findEmployee.spons_name);
      $("span[id='show_employee_project_name']").text(findEmployee.proj_name);  
      $("span[id='show_employee_type']").text( findEmployee.emp_type_id == 1 ? "Direct Manpower" : "Indirect Manpower");
      $("span[id='show_employee_working_shift']").text(findEmployee.isNightShift == 0 ? "Day Shift" : "Night Shift")
      
      // UI form data set
      $("input[id='mobile_no']").val(findEmployee.mobile_no != null ? findEmployee.mobile_no : "");
      $("input[id='home_mobile_no']").val(findEmployee.phone_no != null ? findEmployee.phone_no : "");
      $("input[id='app_employee_id']").val(findEmployee.emp_auto_id); 
      
            // assign leave contract duration in dropdownmenu
     document.getElementById('leave_contract').value = findEmployee.leave_contract;
 
      var passport = `  
                      <span class="emp2" style="text-align: center; text-transform: capitalize;" colspan="3"><a target="_blank" href="{{ url('${findEmployee.pasfort_photo}') }}" class="btn btn-danger">Download</a></span>
                  `       
      var iqama =  `<span class="emp2" style="text-align: center; text-transform: capitalize;" colspan="3"><a target="_blank" href="{{ url('${findEmployee.akama_photo}') }}" class="btn btn-danger">Download</a></span>
                  `
      findEmployee.pasfort_photo == null ?  $("#show_employee_passport_file").append('') :  $("#show_employee_passport_file").append(passport);
      findEmployee.akama_photo == null ?  $("#show_employee_iqama_file").append('') :  $("#show_employee_iqama_file").append(iqama);
       

     
  }

   
   
     $(document).ready(function () {


        // Print Employee Leave Application Form
        document.getElementById('processLeaveAppLicationForm').addEventListener('click', function() {
            var searchType = $('#searchBy').find(":selected").val();
            var searchValue = $("#empl_info").val();

            if(searchValue == ''){
                showSweetAlertMessage('error', "Please Input Valid Data");
                return;
            }

            // Create the query string with parameters
            const queryString = new URLSearchParams({
                searchType: searchType,
                searchValue: searchValue,
            }).toString();
            var parameterValue = queryString; // Set parameter value
            var url = "{{ route('process-print-leave-application', ':parameter') }}";
            url = url.replace(':parameter', parameterValue);
            window.open(url, '_blank');
        });

  });
   
   
</script>

<!-- added this for Multiple Selection dropdownlist  -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta2/css/bootstrap-select.min.css" integrity="sha512-mR/b5Y7FRsKqrYZou7uysnOdCIJib/7r5QeJMFvLNHNhtye3xJp1TdJVPLtetkukFn227nKpXD9OjUc09lx97Q==" crossorigin="anonymous"/>
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta2/js/bootstrap-select.min.js" integrity="sha512-FHZVRMUW9FsXobt+ONiix6Z0tIkxvQfxtCSirkKc5Sb4TKHmqq1dZa8DphF0XqKb3ldLu/wgMa8mT6uXiLlRlw==" crossorigin="anonymous"></script>

@endsection













