@extends('layouts.admin-master')
@section('title')Catering Service @endsection
@section('content')

<style>
    body {
        font-family: "Open Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", Helvetica, Arial, sans-serif;
    }
</style>
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Employee Catering Service</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active">Catering</li>
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


<!-- Catering Menu Section Start -->
<div class="row" id="">
    <div class="col-md-1"></div>
    <div class="col-md-10">
        <form class="form-horizontal">
            <div class="card">
                <div class="card-body card_form">
                    <div class="row">                       
                        <div class="col-sm-3">
                            @can('catering_monthly_record_insert')
                            <button type="button" class="btn btn-primary" data-toggle="modal" id="new_record_button" data-target="#catering_insert_modal">New Record
                            </button> 
                            @endcan
                            </div>
                        <div class="col-sm-3">
                            <button type="button" onclick="openMultipleEmployeeSection()"
                                class="btn btn-primary waves-effect">Upload Excel File</button>
                            <br> <br>
                        </div>
                        <div class="col-sm-3">
                            @can('catering_monthly_record_search')
                            <button type="button" onclick="openCateringSearchingSection()"
                              class="btn btn-primary waves-effect">Search</button> 
                            @endcan
                          <br> <br>
                        </div>
                        <div class="col-sm-3"> 
                            @can('catering_record_report')
                            <button type="button" class="btn btn-primary" data-toggle="modal" id="new_record_button" data-target="#catering_report_modal">Report
                            </button> 
                            @endcan
                        </div>
                       
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="col-md-1"></div>
</div>

<!--  Employee Monthly Catering Record Insert Modal -->
<div class="modal fade" id="catering_insert_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Employee Monthly Catering Record Insert Form<span class="text-danger" id="errorData"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                
                
                <input type="hidden" id="modal_emcr_auto_id" name="modal_emcr_auto_id" value="">
                <div class="form-group row custom_form_group{{ $errors->has('new_record_employee_id') ? ' has-error' : '' }}">
                    <label class="control-label col-md-3">Emp. ID<span
                            class="req_star">*</span></label>
                    <div class="col-md-9">
                        <input type="number" class="form-control"  placeholder="Enter an Employee ID"
                            id="modal_employee_id" name="modal_employee_id"  
                            value="{{old('modal_employee_id')}}" autofocus required >                           
                            @if ($errors->has('modal_employee_id'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('modal_employee_id') }}</strong>
                            </span>
                            @endif
                    </div>
                </div>
                
                  <div class="form-group row custom_form_group">
                    <label class="col-md-3 control-label" for="project_name">Project</label>
                    <div class="col-md-9">
                    <select class="form-select" name="modal_project" id="modal_project" required>
                        <option value="">Select Project Name</option>
                        @foreach ($projects as $item)
                            <option value="{{ $item->proj_id }}">{{ $item->proj_name }}</option>
                        @endforeach
                    </select>
                    </div>
                </div>
                              
                  <div class="form-group row custom_form_group">                       
                    <label class="control-label col-md-3">Month</label>
                    <div class="col-md-4">
                        <select class="form-select" name="modal_month" id="modal_month" required>                             
                            <option value="1" {{ 1 == date('m') ? 'selected' :'' }}> January</option>
                            <option value="2" {{ 2 == date('m') ? 'selected' :'' }}> February</option>
                            <option value="3" {{ 3 == date('m') ? 'selected' :'' }}> March</option>
                            <option value="4" {{ 4 == date('m') ? 'selected' :'' }}> April</option>
                            <option value="5" {{ 5 == date('m') ? 'selected' :'' }}> May</option>
                            <option value="6" {{ 6 == date('m') ? 'selected' :'' }}> June</option>
                            <option value="7" {{ 7 == date('m') ? 'selected' :'' }}> July</option>
                            <option value="8" {{ 8 == date('m') ? 'selected' :'' }}> Auguest</option>
                            <option value="9" {{ 9 == date('m') ? 'selected' :'' }}> September</option>
                            <option value="10" {{ 10 == date('m') ? 'selected' :'' }}> October</option>
                            <option value="11" {{ 11 == date('m') ? 'selected' :'' }}> November</option>
                            <option value="12" {{ 12 == date('m') ? 'selected' :'' }}> December</option>                      
                        </select>
                     </div>
                    <label class="col-sm-2 control-label"> Year </label>
                    <div class="col-sm-3">
                        <select class="form-select" name="modal_year"  id="modal_year">
                            @foreach(range(date('Y'), date('Y')-1) as $y)
                            <option value="{{$y}}" {{$y}}>{{$y}}</option>
                            @endforeach
                        </select>
                    </div>
                </div> 
                <div class="form-group row custom_form_group{{ $errors->has('total_day') ? ' has-error' : '' }}">
                    <label class="control-label col-md-3">Meal Days:<span
                            class="req_star">*</span></label>
                    <div class="col-md-9">
                        <input type="number" class="form-control" step="1" placeholder="Meal Days"
                            id="total_day" name="total_day" value="{{old('total_day')}}"  required min="1" max="31">                           
                            @if ($errors->has('total_day'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('total_day') }}</strong>
                            </span>
                            @endif
                    </div>
                </div>
                
                <div class="form-group row custom_form_group{{ $errors->has('amount') ? ' has-error' : '' }}">
                    <label class="control-label col-md-3">Amount:<span
                            class="req_star">*</span></label>
                    <div class="col-md-9">
                        <input type="number" class="form-control" step="1" placeholder="Meal Amount"
                            id="amount" name="amount" value="{{old('amount')}}"  required min="1" max="1000">                           
                            @if ($errors->has('amount'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('amount') }}</strong>
                            </span>
                            @endif
                    </div>
                </div>
                
                <div class="form-group row custom_form_group">
                    <label class="col-sm-3 control-label">Remarks:</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control"  id="remarks"  name="remarks" placeholder="Type Here..."
                        >
                    </div>
                </div>             
               
            </div>

            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" id="new_record_save_button" class="btn btn-primary waves-effect"  
                onclick="saveEmployeeMonthlyCateringServiceRecord()" >SAVE</button>
            </div>
             
        </div>
    </div>
</div>
  
<!-- Searching Employee Monthly Catering Record -->
<div class="row d-none" id="searching_section">
    <div class="col-md-12">       
        <div class="card">
                <div class="card-body card_form">                    
                    <div class="form-group row custom_form_group">
                        
                        <!-- Month Dropdown Menu -->
                        <!--<label class="control-label col-sm-1">Month</label>-->
                        <div class="col-sm-2">                             
                            <select class="form-select" id="month" name="month" hidden required>   
                                <!--<option value="">All Record</option>  -->
                                <option value="1" {{ 1 == date('m') ? 'selected' :'' }}> January</option>
                                <option value="2" {{ 2 == date('m') ? 'selected' :'' }}> February</option>
                                <option value="3" {{ 3 == date('m') ? 'selected' :'' }}> March</option>
                                <option value="4" {{ 4 == date('m') ? 'selected' :'' }}> April</option>
                                <option value="5" {{ 5 == date('m') ? 'selected' :'' }}> May</option>
                                <option value="6" {{ 6 == date('m') ? 'selected' :'' }}> June</option>
                                <option value="7" {{ 7 == date('m') ? 'selected' :'' }}> July</option>
                                <option value="8" {{ 8 == date('m') ? 'selected' :'' }}> Auguest</option>
                                <option value="9" {{ 9 == date('m') ? 'selected' :'' }}> September</option>
                                <option value="10" {{ 10 == date('m') ? 'selected' :'' }}> October</option>
                                <option value="11" {{ 11 == date('m') ? 'selected' :'' }}> November</option>
                                <option value="12" {{ 12 == date('m') ? 'selected' :'' }}> December</option>
                            </select>
                        </div>
                        
                        
                        <!-- YEar Dropdown Menu -->
                        <label class="control-label col-sm-2">Year</label>
                        <div class="col-sm-2">
                            <select class="form-control" name="year" id="year">
                                @foreach(range(date('Y'), date('Y')-1) as $y)
                                <option value="{{$y}}" {{$y}}>{{$y}}</option>
                                @endforeach
                            </select>
                        </div>
                        

                        <!-- Employee ID -->  
                        <label class="control-label col-md-2">Employee ID:</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control typeahead" placeholder="Input Employee ID" id="emp_id" autofocus >
                            <span class="error d-none" id="error_massage"></span>
                        </div>
                         <!-- Searching Button--> 
                        <div class="col-md-2">
                            <button type="button" id="multi_record_search_btn" class="btn btn-primary waves-effect" onclick="searchAnEmployeeCateringRecord()">Search</button>
                            {{-- <button type="button" class="btn btn-primary" data-toggle="modal" id="new_record_button" data-target="#catering_insert_modal">New Record
                            </button> --}}
                        </div> 
                    </div>
                </div>    
        </div>
        <!-- monthly catering record list -->
        <div class="row">
            <div class="col-12">
                <div class="table-responsive">
                    <table id="alltableinfo" class="table table-bordered custom_table mb-0">
                        <thead>
                            <tr>
                                <th>S.N</th>
                                <th>Emp. Id</th>
                                <th>Name</th>
                                <th>Iqama</th>
                                <th>Salary</th>
                                <th>Sponsor</th>
                                 <th>Project</th>
                                <th>Days</th>
                                <th>Amount</th>
                                <th>Month,Year</th>
                                <th>Inserted By</th>
                                <th>Remarks</th>
                                <th>Manage</th>
                            </tr>
                        </thead>
                        <tbody id="employee_catering_records_table_body"></tbody>
                    </table>
                </div>
            </div>
        </div>
               
    </div>
</div>
 

<!-- Catering Service Excell File Upload Start-->
<div class="row d-none" id="catering_records_excel_import_section">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <div class="row">   
                  <form method="POST" enctype="multipart/form-data" id="upload_catering_records"  onsubmit="excel_upload_button.disabled = true;" >                 
                         <div class="row"> 
                              <div class="form-group row custom_form_group"> 
                                                          
                                  <label class="col-sm-1 control-label"> Month</label>                           
                                  <div class="col-sm-2">
                                      <select class="form-control" name="month" required>
                                        <option value="">Select Month</option>
                                        <option value="1" {{ 1 == date('m') ? 'selected' :'' }}> January</option>
                                        <option value="2" {{ 2 == date('m') ? 'selected' :'' }}> February</option>
                                        <option value="3" {{ 3 == date('m') ? 'selected' :'' }}> March</option>
                                        <option value="4" {{ 4 == date('m') ? 'selected' :'' }}> April</option>
                                        <option value="5" {{ 5 == date('m') ? 'selected' :'' }}> May</option>
                                        <option value="6" {{ 6 == date('m') ? 'selected' :'' }}> June</option>
                                        <option value="7" {{ 7 == date('m') ? 'selected' :'' }}> July</option>
                                        <option value="8" {{ 8 == date('m') ? 'selected' :'' }}> Auguest</option>
                                        <option value="9" {{ 9 == date('m') ? 'selected' :'' }}> September</option>
                                        <option value="10" {{ 10 == date('m') ? 'selected' :'' }}> October</option>
                                        <option value="11" {{ 11 == date('m') ? 'selected' :'' }}> November</option>
                                        <option value="12" {{ 12 == date('m') ? 'selected' :'' }}> December</option>
                                      </select>
                                  </div>
                                  <label class="col-sm-1 control-label">Year</label>
                                  <div class="col-sm-2">
                                      <select class="form-control" name="year" required>
                                        @foreach(range(date('Y'), date('Y')-1) as $y)
                                          <option value="{{$y}}" {{$y}}>{{$y}}</option>
                                        @endforeach
                                      </select>
                                  </div>
                              </div>

                            <div class="form-group row custom_form_group"> 
                            <div class="col-sm-3">  </div>
                            <div class="col-sm-3">
                                <input type="file" name="file" placeholder="Choose File" id="file">
                                          <span class="text-danger">{{ $errors->first('file') }}</span>
                                </div>
                              <div class="col-sm-2">
                                    <button type="submit" id ="excel_upload_button" class="btn btn-primary">UPLOAD</button>
                              </div>
                            <div class="col-sm-2">  </div>
                          </div> 
                        </div>
                  </form> 
                </div>
            </div>
            <div class="card-body" id="excell_file_upload_emp_list_table_section" >
                <div class="row">
                    <form method="POST"  id="submit_imported_catering_record"  onsubmit="excel_submit_button.disabled = true;" >
                        <div class="row">
                                <div class="form-group row custom_form_group">                                  

                                  <label class="col-md-2 control-label" for="project_name">Project</label>
                                  <div class="col-md-6">
                                    <select class="form-select" name="project" id="project"  >
                                            <option value="">Select Project Name</option>  
                                        @foreach ($projects as $item)
                                            <option value="{{ $item->proj_id }}">{{ $item->proj_name }}</option>
                                        @endforeach
                                    </select>
                                  </div>
                                  <div class="col-sm-3">
                                      <button type="submit" id ="excel_submit_button"  class="btn btn-primary waves-effect">SUBMIT </button>
                                  </div>
                                </div>
                        </div>
                        <div class="col-12" id="valid_records_div_section" > 

                          <div class="table-responsive">
                              <table id="excell_file_upload_emp_list_table" class="table table-bordered custom_table mb-0">
                                  <thead>
                                      <tr>
                                            <th>S.N</th>
                                            <th>Emp Id</th>
                                            <th>Name</th>
                                            <th>Emp.Type</th>
                                            <th>Month,Year</th>
                                            <th>Days</th>
                                            <th>Amount</th> 
                                            <th>Remarks</th>                                      
                                      </tr>
                                  </thead>
                                  <tbody id="excell_file_upload_emp_list_table_body">                                    
                                  </tbody>
                              </table>
                          </div>
                        </div>                        
                    </form>
                    <div class="col-12 d-none" id ="upload_error_div_section">
                        <br/>
                        <h3>Data Error Found</h3>
                          <div class="table-responsive">
                              <table id="excell_file_upload_error_emp_list_table" class="table table-bordered custom_table mb-0">
                                  <thead>
                                      <tr>
                                          <th>S.N</th>
                                          <th>Emp Id</th>
                                          <th>Name</th>
                                          <th>Emp.Type</th>
                                          <th>Month,Year</th>
                                          <th>Days</th>
                                          <th>Amount</th> 
                                          <th>Remarks</th>
                                      </tr>
                                  </thead>
                                  <tbody id="excell_file_upload_error_emp_list_table_body">
                                    
                                  </tbody>
                              </table>
                          </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Excell File Upload End -->


<!--  Employee Catering Vervice Report Modal -->
<div class="modal fade" id="catering_report_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="catering_report_form" action="{{route('catering.service.report')}}" method="GET" target="_blank">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Employee Catering Service Report<span class="text-danger" id="errorData"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group row custom_form_group{{ $errors->has('new_record_employee_id') ? ' has-error' : '' }}">
                        <label class="control-label col-md-3">Emp. ID </label>
                        <div class="col-md-9">
                            <input type="number" class="form-control"  placeholder="Enter an Employee ID"
                                id="report_employee_id" name="report_employee_id"  
                                value="{{old('report_employee_id')}}"     >                           
                                @if ($errors->has('report_employee_id'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('report_employee_id') }}</strong>
                                </span>
                                @endif
                        </div>
                    </div> 
                    <h5 style="text-align: center"> OR  </h5>
                    <div class="form-group row custom_form_group">
                        <label class="col-md-3 control-label" for="project_name">Project</label>
                        <div class="col-md-9">
                        <select class="form-select" name="report_project" id="report_project">
                            <option value="">Select Project Name</option>
                            @foreach ($projects as $item)
                                <option value="{{ $item->proj_id }}">{{ $item->proj_name }}</option>
                            @endforeach
                        </select>
                        </div>
                    </div>
                    <div class="form-group row custom_form_group">                       
                        <label class="control-label col-md-3">Month</label>
                        <div class="col-md-4">
                            <select class="form-select" name="report_month" id="report_month" required>                             
                                <option value="1" {{ 1 == date('m') ? 'selected' :'' }}> January</option>
                                <option value="2" {{ 2 == date('m') ? 'selected' :'' }}> February</option>
                                <option value="3" {{ 3 == date('m') ? 'selected' :'' }}> March</option>
                                <option value="4" {{ 4 == date('m') ? 'selected' :'' }}> April</option>
                                <option value="5" {{ 5 == date('m') ? 'selected' :'' }}> May</option>
                                <option value="6" {{ 6 == date('m') ? 'selected' :'' }}> June</option>
                                <option value="7" {{ 7 == date('m') ? 'selected' :'' }}> July</option>
                                <option value="8" {{ 8 == date('m') ? 'selected' :'' }}> Auguest</option>
                                <option value="9" {{ 9 == date('m') ? 'selected' :'' }}> September</option>
                                <option value="10" {{ 10 == date('m') ? 'selected' :'' }}> October</option>
                                <option value="11" {{ 11 == date('m') ? 'selected' :'' }}> November</option>
                                <option value="12" {{ 12 == date('m') ? 'selected' :'' }}> December</option>                      
                            </select>
                        </div>
                        <label class="col-sm-2 control-label"> Year </label>
                        <div class="col-sm-3">
                            <select class="form-select" name="report_year"  id="report_year">
                                @foreach(range(date('Y'), date('Y')-1) as $y)
                                <option value="{{$y}}" {{$y}}>{{$y}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div> 
                </div>

                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" id="new_record_save_button" class="btn btn-primary waves-effect"  
                    onclick="processEmployeeMonthlyCateringServiceReport()" >Report</button>
                </div>
            </form>
             
        </div>
    </div>
</div>



 

<!-- script area -->
<script type="text/javascript">


    function openCateringSearchingSection() {
               
        $('#catering_records_excel_import_section').removeClass('d-block').addClass('d-none');
        $('#searching_section').removeClass('d-none').addClass('d-block'); 
     //   document.getElementById("emp_id_search").focus();
    }

    function openMultipleEmployeeSection() {
        $('#searching_section').removeClass('d-block').addClass('d-none'); 
        $('#catering_records_excel_import_section').removeClass('d-none').addClass('d-block');
    }


    $(document).ready(function (e) {
        // excell data uploaded button event               
        $('#upload_catering_records').submit(function(e) { 
            // reset previous records
            $('#excell_file_upload_emp_list_table_body').html('');
            $('#excell_file_upload_error_emp_list_table_body').html('');
            e.preventDefault();
            var formData = new FormData(this);  
            $.ajax({
                    type:'POST',
                    url: "{{ route('catering.monthly.record.import.preview.request')}}",
                    data: formData,
                    cache:false,
                    contentType: false,
                    processData: false,
                    success: (response) => {                              
                        this.reset();                         
                        var rows = "";
                        var counter = 1;                            
                        document.getElementById("excel_upload_button").disabled = false;
                         
                        $.each(response.records, function (key, value) {

                            rows += `
                                <tr>
                                    <td>${counter++}</td>
                                    <td>${value.emp_id}</td>
                                    <td>${value.employee_name} </td>
                                    <td>${value.hourly_employee == 1 ? 'Hourly' : 'Basic Salary'} </td>
                                    <td>${value.month_name}, ${value.year}</td>                                    
                                    <td>${value.total_days}</td> 
                                    <td> ${value.amount}</td>       
                                    <td> ${value.upload_status}</td>   
                                </tr>
                                `
                        }); 
                        $('#excell_file_upload_emp_list_table_body').html(rows); 
                    
                    
                    var error_rows = "";
                    var error_counter = 1;
                    $.each(response.records_not_found, function (key, value) {
                            error_rows += `
                            <tr>
                                <td>${error_counter++}</td>
                                <td>${value.emp_id}</td>
                                <td>${value.employee_name} </td>
                                <td>${value.hourly_employee == 1 ? 'Hourly' : 'Basic Salary'} </td>
                                <td>${value.month_name}, ${value.year}</td>                                    
                                <td>${value.total_days}</td> 
                                <td> ${value.amount}</td>       
                                <td> ${value.upload_status}</td>    
                            </tr>
                            `
                    }); 
                    
                    $("#upload_error_div_section").removeClass("d-none").addClass("d-block");
                    $('#excell_file_upload_error_emp_list_table_body').html(error_rows);                                                     
                    },
                    error: function(data){ 
                        document.getElementById("excel_upload_button").disabled = false; 
                    }
            });
        });

        // Submit Imported Excell Data
        $('#submit_imported_catering_record').submit(function(e) {                   
            e.preventDefault();
            var formData = new FormData(this);  
            $.ajax({
                type:'POST',
                url: "{{ route('catering.monthly.record.import.submit.request')}}",
                data: formData,
                cache:false,
                contentType: false,
                processData: false,
                success: (response) => {
                    if(response.status == 200){                         
                        showSweetAlert("success", response.message)                      
                    }else {
                        showSweetAlert('error', response.message)
                    }
                    this.reset();
                    location.reload();
                                        
                },
                error: function(data){
                    
                }
            });

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

    function searchAnEmployeeCateringRecord() {

        var empId = $('#emp_id').val();
        var month = $('#month').val();
        var year = $('#year').val();   

            $('#employee_catering_records_table_body').html('');           
            $.ajax({
                type: "GET",
                url: "{{ route('anemployee.catering.record.search') }}",
                data: {
                    emp_id: empId,
                    month: month,
                    year: year
                },
                dataType: "json",
                success: function(response) {
                    var rows = "";
                    var counter = 0;
                                
                    if (response.error != null) {                       
                        showSweetAlert('error','Records Not Found');        
                        return;
                    }                   
                    $('#emp_id').val("");
                    $.each(response.data, function(key, value) {
                        counter++;
                        rows += `
                                <tr>
                                    <td>${counter}</td>
                                    <td>${value.employee_id}</td>
                                    <td>${value.employee_name}</td>
                                    <td>${value.akama_no}</td>
                                    <td>${value.hourly_employee == true? 'Hourly' : 'Basic'}</td>
                                    <td>${value.spons_name}</td>
                                    <td>${ value.proj_name != null ? value.proj_name : '-'}</td>
                                    <td>${value.total_days}</td>
                                    <td>${value.amount}</td>
                                    <td>${value.month_name}<br> ${value.year}</td>
                                    <td>${value.inserted_by }</td>
                                    <td>${value.remarks != null ? value.remarks:"-" }</td>
                                    <td>
                                    @can('catering_monthly_record_delete')
                                        <a href="#" onClick="deleteAnEmployeeCateringRecord(${value.emcr_auto_id})" vallue="${value.emcr_auto_id}" title="Delete"><i class="fa fa-trash fa-sm delete_icon"></i></a>
                                    @endcan
                                        &nbsp;&nbsp;&nbsp;
                                    @can('catering_monthly_record_update')
                                        <a href="" id="edit_catering_record" data-toggle="modal" data-target="#catering_insert_modal" data-id="${value.emcr_auto_id}"> <i class="fa fa-edit fa-sm edit_icon"></i> </a>
                                    @endcan
                                    </td>
                                </tr>
                            `                        
                    });
                    $('#employee_catering_records_table_body').html(rows);
                }

            });
    }

   
     // new record Form Open
    // $(document).on('click',"#new_record_button",function(){
    // }); 

    $('#catering_insert_modal').on('shown.bs.modal', function (e) {
        $('#modal_employee_id').val($('#emp_id').val());
        $('#modal_employee_id').focus();
    })
    
    // Reset Modal Previous Data    
     $('#catering_insert_modal').on('hidden.bs.modal', function (e) {
       $(this)
      .find("input,textarea,select,hidden").val('').end()
      .find("input[type=checkbox], input[type=radio]").prop("checked", "").end();
    })

    function saveEmployeeMonthlyCateringServiceRecord() {

      
        var emp_id = $('#modal_employee_id').val();
        var emcr_auto_id = $('#modal_emcr_auto_id').val();

        
        var month = $('#modal_month').val();
        var year = $('#modal_year').val(); 
        var total_days =   $('input[name="total_day"]').val();
        var amount =   $('input[name="amount"]').val();
        var remarks = $('#remarks').val(); 
        var project = $('#modal_project').val();

        
        if(emp_id == ""){
            showSweetAlert('error',"Please Input Employee ID");
            return;
        }else if(month == "" || year == ""){
            showSweetAlert('error',"Please Select Month & Year");
            return;
        }
        else if (  total_days <= 0 || total_days >31 ) {
            showSweetAlert('error',"Please Input Valid Days");
            return;
        }
        else if (amount < 0 || amount > 2000 ) {
            showSweetAlert('error',"Please Input Valid Amount");
            return;
        }
        else if (project == "" ) {
            showSweetAlert('error',"Please Select Project Name");
            return;
        }
        
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: {
            emcr_auto_id:emcr_auto_id,
            emp_id: emp_id,
            month:month,
            year:year,
            total_days:total_days,
            amount:amount,
            remarks:remarks,
            project:project,
            
            },
            url: "{{ route('catering.monthly.record.store.request') }}",
            beforeSend:()=>{
                $("body").addClass("loading");
            },
            complete:()=>{
                $("body").removeClass("loading");
            },
            success: function(response) {
                
                if(response.status == 200){                    
                   showSweetAlert("success",response.message); 
                   $('#catering_insert_modal').modal('hide'); 
                   $('#modal_employee_id').val(''); 
                   $('#modal_emcr_auto_id').val('');
                   searchAnEmployeeCateringRecord();
                    
                }else {
                    showSweetAlert("error",response.message); 
                }                      
            },
            error:function(response){
                showSweetAlert('error','Operation Failed!, Pleage Try Again ');
            }
        });
    }

    // Open Modal For Update Multi Project Work Record
    $(document).on("click", "#edit_catering_record", function(){
       
    
        var emcr_auto_id = $(this).data('id');
        $.ajax({
            type: "GET",
            url: "{{ route('anemployee.catering.record.search') }}",
            data: {
                emcr_auto_id: emcr_auto_id,                
            },
            datatype:"json",
            success: function(response){
                if(response.data != null){
                     $('#modal_employee_id').val(response.data.employee_id);
                    $('#modal_month').val(response.data.month);
                    $('#modal_year').val(response.data.year);                  
                    $('#total_day').val(response.data.total_days);  
                    $('#amount').val(response.data.amount);
                    $('#remarks').val(response.data.remarks);                           
                    $('#modal_emcr_auto_id').val(response.data.emcr_auto_id);
                    $('#modal_project').val(response.data.proj_id);
                     
                }else{
                    $('#errorData').text(response.error);
                }
                    
            }
        })

    });

     function deleteAnEmployeeCateringRecord(id) {

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
                        url:"{{ route('anemployee.catering.record.delete') }}",
                        data:{
                            emcr_auto_id:id,
                        },
                        dataType: 'json',
                        success: function(response) {
                            if(response.status == 200){
                                showSweetAlert('success', response.message);
                                searchAnEmployeeCateringRecord();                                
                            }else {
                                showSweetAlert('error', response.message);
                            }                          
                        },
                        error:function(response){
                           showSweetAlert('error', "System Error, Reload and Try again");
                        }
                    });
                }
            });
    }
    
    
  

</script>
@endsection
 