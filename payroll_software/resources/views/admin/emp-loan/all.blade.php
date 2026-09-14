@extends('layouts.admin-master')
@section('title') Emp. Advance @endsection
@section('content')
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Employee Advance</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active">Employee Advance</li>
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
        <div class="alert alert-success alertsuccess" role="alert" style="margin-left: -20px">
            <strong>{{Session::get('error')}}</strong>
        </div>
        @endif
    </div>
</div>

<!-- Advance Menu Section -->
<div class="row" id="">
    <div class="col-md-12">
        <form class="form-horizontal" id="employeeListForm" action="" method="">
            <div class="card">
                <div class="card-body card_form">
                    <div class="row">
                                                
                        <div class="col-sm-1" style="overflow:hidden">
                            @can('employee_advance_insert')
                                <button type="submit" onclick="openSingleEmployeeAdvanceSection()"
                                    class="btn btn-primary waves-effect">Single</button>
                            @endcan
                           &nbsp; &nbsp;
                        </div>
                        <div class="col-sm-1"></div>
                        <div class="col-sm-1" style="overflow:hidden">
                            @can('mult_employee_advance_insert')
                                <!--<button type="submit" onclick="openMultipleEmployeeAdvanceSection()"-->
                                <!--class="btn btn-primary waves-effect">Multiple</button>-->
                            @endcan
                            &nbsp; &nbsp;
                        </div>
                        <div class="col-sm-1"></div>
                        <div class="col-sm-1" style="overflow:hidden">
                            <button type="submit" onclick="openAdvanceSearchingSection()"
                                class="btn btn-primary waves-effect">Search</button>
                                &nbsp; &nbsp;
                        </div>
                        <div class="col-sm-1"> </div>
                        <div class="col-sm-1"> </div>
                        <div class="col-sm-2" style="overflow:hidden">
                             @can('advance_paper_upload_insert')
                                <button type="submit" onclick="openEmployeeAdvancePaperUploadSection()"
                                class="btn btn-primary waves-effect">Paper Upload</button>
                                &nbsp; &nbsp;
                            @endcan
                        </div>
                        
                        <div class="col-sm-1"> </div>
                        <div class="col-sm-2" style="overflow:hidden">                             
                            <!--<button type="button" id="invoice_report" data-toggle="modal" data-target="#cash_received_form_modal" class="btn btn-primary waves-effect">Create Form</button> -->
                        </div>
                        
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>


<!-- Single Employee Advance Insert Section -->
<div class="row d-none" id="single_emp_advance_section">
    <div class="col-md-12">
             <h5 class="card-title">Single Employee Advance </h5>
            <div class="row form-group custom_form_group{{ $errors->has('searchBy') ? ' has-error' : '' }}">
                <div class="col-md-2">
                 
                </div>
                <label class="col-md-2 control-label d-block">Employee Searching by </label>
                <div class="col-md-3">
                    <select class="form-select" name="searchBy" id="searchBy" required>
                        <option value="employee_id">Searching By Employee ID</option>
                        <option value="akama_no">Searching By Iqama </option>
                        <option value="passfort_no">Searching By Passport</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <input type="text" placeholder="Enter ID/Iqama/Passport No" class="form-control" id="empl_info"
                        name="empl_info" value="{{ old('empl_info') }}" 
                        autofocus="autofocus" required>
                    <span id="employee_not_found_error_show" class="d-none" style="color: red"></span>
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
                <div class="col-md-1"> </div>
            </div>
        <div class="card d-none" id="advance_insertion_section">
            <div class="row">
                 <!-- Emplopyee Information --> 
                <div class="col-md-4">                 
                    <div class="emp_id_show text-center">
                            <span class="req_star" style="font-size: 20px;">Employee ID <span id="show_employee_id" class="req_star">Required</span>
                            </span>
                    </div> 
                    <table class="table table-bordered table-striped table-hover custom_view_table show_employee_details_table"
                                    id="showEmployeeDetailsTable">
                                    <tr>
                                        <td> <span class="emp">Iqama:</span> <span id="show_employee_akama_no"
                                                    class="emp2"></span> </td>
                                        <td> <span class="emp"> Name:</span> <span id="show_employee_name"
                                                class="emp2"></span> </td>
                                                                              
                                    </tr>
                                    <tr>
                                        <td> <span class="emp">Status:</span> <span id="show_employee_job_status"
                                            class="emp2"></span> </td>  
                                            <td> <span class="emp"> Trade:</span> <span id="show_employee_category"
                                                class="emp2"></span> </td>
                                    </tr>
                                    <tr>
                                        <td> <span class="emp">Passport:</span> <span id="show_employee_passport_no"
                                                class="emp2"></span> </td>
                                        <td> <span class="emp">Mobile:</span> <span id="show_employee_mobile_no"
                                                    class="emp2"></span> </td>
                                        
                                                                                              
                                    </tr>
                                     
                                    
                                    <tr> <td> <span class="emp">Agency:</span> <span id="show_employee_agency_name"
                                        class="emp2"></span> </td>
                                         
                                        <td> <span class="emp">Sponsor:</span> <span
                                            id="show_employee_sponsor_name" class="emp2"></span> </td> 
                                        
                                    </tr> 
                                    <tr>
                                         <td>
                                            <span class="emp">Salary:</span> 
                                            <span id="show_salary_type" class="emp2" style="font-weight:bold;color:red;font-size:18px;"></span>   
                                            @can('employee_salary_show_permission') 
                                                &nbsp;&nbsp;
                                                <span id="show_salary_amount"  class="emp2" style="font-weight:bold;color:red;font-size:18px;"></span>
                                            @endcan                         
                                       </td>
                                       
                                        <td> <span class="emp">Working at:</span>
                                        <span id="show_emp_project_name"
                                        class="emp2"></span> </td>
                                    </tr>
                                     
                    </table>                            
                </div>
                
                <!-- Advance Insert User Interface --> 
                <div class="col-md-8" style="border-left: 2px solid rgb(75, 64, 64);">
                   
                       <form class="form-horizontal" id="advance_insert_form" action="{{ route('insert-advance.pay') }} " enctype="multipart/form-data"
                            method="post" > 
                            @csrf                     
                            
                            <div class="card-body card_form row">

                                <input type="hidden" style="font-weight: bold; font-size:20px; color:red; width:100px;" class="form-control" name="emp_id" id="form_emp_id" 
                                readonly required>     
                                
                                    <div  class="form-group row custom_form_group{{ $errors->has('project_id') ? ' has-error' : '' }}">
                                        <label class="col-sm-3 control-label"> Working Project:</label>
                                        <div class="col-sm-7">
                                            <select class="form-select" id="project_id" name="project_id" autofocus required>
                                                <option value="">Select Working Project</option>
                                                @foreach($projects as $ap)
                                                <option value="{{ $ap->proj_id }}">{{ $ap->proj_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @if ($errors->has('project_id'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('project_id') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                
                                    <div  class="form-group row custom_form_group{{ $errors->has('adv_purpose_id') ? ' has-error' : '' }}">
                                        <label class="col-sm-3 control-label"> Purpose:</label>
                                        <div class="col-sm-7">
                                            <select class="form-select" id="adv_purpose_id" name="adv_purpose_id" autofocus required>
                                                <option value="">Select Here</option>
                                                @foreach($purpose as $pur)
                                                <option value="{{ $pur->id }}">{{ $pur->purpose }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @if ($errors->has('adv_purpose_id'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('adv_purpose_id') }}</strong>
                                        </span>
                                        @endif
                                    </div>

                                    <div  class="form-group row custom_form_group{{ $errors->has('adv_amount') ? ' has-error' : '' }}">
                                        <label class="col-sm-3 control-label">Amount:<span
                                                class="req_star">*</span></label>
                                        <div class="col-sm-4">
                                            <input type="number" class="form-control" placeholder="Input Amount"
                                                name="adv_amount" id="adv_amount" value="{{old('adv_amount')}}" step="1" min="1" required>
                                            @if ($errors->has('adv_amount'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('adv_amount') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                        <label class="col-sm-2 control-label">Installation<span
                                            class="req_star">*</span></label>
                                        <div class="col-sm-2">
                                            <input type="number" class="form-control" placeholder="Input Number of Installation"
                                            id="installes_month"  name="installes_month" value="1" min="1"  required>
                                            @if ($errors->has('installes_month'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('installes_month') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>                            
                                    <div class="form-group row custom_form_group">
                                        <label class="col-sm-3 control-label">Advance Date:</label>
                                        <div class="col-sm-7">
                                            <input type="date" class="form-control" name="adv_date" id="adv_date"
                                                value="{{ Carbon\Carbon::now()->format('Y-m-d') }}">
                                        </div>
                                    </div>                                    
                                
                                <div class="form-group row custom_form_group">
                                    <label class="col-sm-3 control-label">Remarks:</label>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" placeholder="Remarks "
                                        id="adv_remarks"  name="adv_remarks" value="{{old('adv_remarks')}}">
                                    </div>
                                </div>

                                <div class="form-group row custom_form_group">
                                    <label class="col-sm-3 control-label">Upload:</label>
                                    <div class="col-sm-4">
                                        <div class="input-group">
                                            <span class="input-group-btn">
                                                <span class="btn btn-default btn-file btnu_browse">
                                                    Browse… <input type="file" name="advance_paper" id="imgInp4">
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
                            <div style="text-align:center; padding-bottom:10px">
                                <button type="submit" id="adv_save_btn"   class="btn btn-primary waves-effect">SAVE</button>
                             </div>
                    </form>  
                        
                </div>
            </div>
        </div>
    </div>
</div>
 

<!-- Multiple Employees Advance Section -->
<div class="row d-none" id="multiple_employee_advance_section">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                 <div class="row">
                    <h5 class="card-title">Multiple Employees Advance Form
                    </h5><br><br>
                    <div class="col-sm-3">
                        <input type="text" name="multi_emp_id" id="multi_emp_id" class="form-control" placeholder="Input Employee ID ">
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group row custom_form_group">
                            <label class="col-sm-4 control-label">Project:</label>
                            <div class="col-sm-8">
                                <select class="form-control" id="proj_name" name="proj_name">
                                    <!--<option value="">Select Project Name</option>-->
                                    @foreach($projects as $proj)
                                    <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <div class="form-group row custom_form_group">
                            <label class="col-sm-4 control-label">Villa Name:</label>
                            <div class="col-sm-8">
                                <select class="form-control" name="acomdOfbId" required>
                                    <option value="">Select Villa Name</option>
                                    @foreach($accomdOfficeBuilding as $ofb)
                                    <option value="{{ $ofb->ofb_id }}">{{ $ofb->ofb_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-2">
                        <button type="submit" onclick="searchProjectWiseEmployeeList()"
                            class="btn btn-primary waves-effect">Search</button>
                    </div>
                </div>
                <div class="col-sm-1"></div>
            </div>
        </div>
        <form class="form-horizontal" id="employeeListForm" action="{{ route('multiple.emp.advance.insert.request')}}"
            method="POST" onsubmit="multi_adv_save_btn.disabled = true;" enctype="multipart/form-data" >
            @csrf
            <div class="card-body">
                <div class="row d-none" id="multiple_employee_adv_form">
                    <div class="col-12">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-3">
                                     
                                    <div  class="form-group row custom_form_group{{ $errors->has('adv_purpose_id') ? ' has-error' : '' }}">
                                        <label class="col-sm-5 control-label"> Advance Purpose:</label>
                                        <div class="col-sm-7">
                                            <select class="form-control" name="adv_purpose_id" autofocus required>
                                                <option value="">Select Here</option>
                                                @foreach($purpose as $pur)
                                                <option value="{{ $pur->id }}">{{ $pur->purpose }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @if ($errors->has('adv_purpose_id'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('adv_purpose_id') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row custom_form_group">
                                        <label class="col-sm-5 control-label">Advance Date:</label>
                                        <div class="col-sm-7">
                                            <input type="date" class="form-control" name="adv_date"
                                                value="{{ Carbon\Carbon::now()->format('Y-m-d') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group row custom_form_group">
                                        <label class="col-sm-5 control-label">Remarks:</label>
                                        <div class="col-sm-7">
                                            <input type="text" class="form-control" placeholder="Remarks "
                                                name="adv_remarks" value="{{old('adv_remarks')}}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div
                                        style="display: flex; justify-content: right; height: 40px; margin-bottom: 30px;">
                                        <button type="submit" id="multi_adv_save_btn" class="btn btn-primary waves-effect">Save Advance
                                             </button>
                                    </div>
                                </div>
                            </div>
                            
                             <div class="form-group row custom_form_group">
                                <label class="col-sm-2 control-label">Choose Advance Paper</label>
                                <div class="col-sm-4">
                                    <div class="input-group">
                                        <span class="input-group-btn">
                                            <span class="btn btn-default btn-file btnu_browse">
                                                Browse… <input type="file" name="advance_paper" id="imgInp4">
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
                    </div>

                    <div class="table-responsive">
                        <span id="data_not_found" class="d-none">Data Not Found!</span>
                        <table id="alltableinfo" class="table table-bordered custom_table mb-0">
                            <thead>
                                <tr>
                                    <th>S.N</th>
                                    <th>Emp.ID</th>
                                    <th>Name</th>
                                    <th>Iqama No</th>
                                    <th>Basic / H Rate</th>
                                    <th>Advance Amount</th>
                                    <th>Select</th>
                                </tr>
                            </thead>
                            <tbody id="employee_list_table"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

 <!-- Employee Advance Edit Modal -->
<div class="modal fade" id="emp_adv_edit_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLongTitle">Employee Advance Update</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
             
                <input type="hidden" id ="modal_adv_audo_id" name ="modal_adv_audo_id">
                <input type="hidden" id ="modal_emp_audo_id" name ="modal_emp_audo_id">
                <div  class="form-group row custom_form_group{{ $errors->has('adv_purpose_id') ? ' has-error' : '' }}">
                    <label class="col-sm-2 control-label"> Purpose:</label>
                    <div class="col-sm-10">
                        <select class="form-select" id="modal_adv_purpose_id" name="modal_adv_purpose_id" autofocus required>
                            <option value="">Select Here</option>
                            @foreach($purpose as $pur)
                            <option value="{{ $pur->id }}">{{ $pur->purpose }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if ($errors->has('adv_purpose_id'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('adv_purpose_id') }}</strong>
                    </span>
                    @endif
                </div>

                <div  class="form-group row custom_form_group{{ $errors->has('modal_adv_amount') ? ' has-error' : '' }}">
                    <label class="col-sm-2 control-label">Amount:<span
                            class="req_star">*</span></label>
                    <div class="col-sm-4">
                        <input type="number" class="form-control" placeholder="Input Amount"
                            name="modal_adv_amount" id="modal_adv_amount" value="{{old('modal_adv_amount')}}" step="1" min="1" required>
                        @if ($errors->has('modal_adv_amount'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('modal_adv_amount') }}</strong>
                        </span>
                        @endif
                    </div>
                    <label class="col-sm-3 control-label">No. of Install.<span
                        class="req_star">*</span></label>
                    <div class="col-sm-3">
                        <input type="number" class="form-control" placeholder="Input Number of Installation"
                        id="modal_installes_month"  name="modal_installes_month" value="1" min="1"  required>
                        @if ($errors->has('modal_installes_month'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('modal_installes_month') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>                            
                <div class="form-group row custom_form_group">
                    <label class="col-sm-2 control-label">Advance Date:</label>
                    <div class="col-sm-10">
                        <input type="date" class="form-control" name="modal_adv_date" id="modal_adv_date"
                            value="{{ Carbon\Carbon::now()->format('Y-m-d') }}">
                    </div>
                </div>                                    
            
                <div class="form-group row custom_form_group">
                    <label class="col-sm-2 control-label">Remarks:</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" placeholder="Remarks "
                        id="modal_adv_remarks"  name="modal_adv_remarks" value="{{old('modal_adv_remarks')}}">
                    </div>
                </div>
                <br> 
                <button type="submit" id="updatebtn" name="updatebtn" onclick="updateEmployeeAdvance()"  class="btn btn-success"  >Update</button>
         </div>
        <div class="modal-footer">          
          
        </div>
      </div>
    </div>
</div>


 <!-- Employee Advance Search Section-->
<div class="row d-none" id="emp_advance_search_section">
    <div class="col-md-12">
        <div class="card">
            <h5 class="card-title"> Searching Employee Advance Records</h5>
            <div class="form-group row custom_form_group{{ $errors->has('emp_id') ? ' has-error' : '' }}">
                <label class="col-md-3 control-label d-block">Employee Searching by </label>
                <div class="col-md-3">
                    <select class="form-select" name="adv_searchBy" id="adv_searchBy" required>
                        <option value="employee_id">Employee ID</option>
                        <option value="akama_no">Iqama </option>
                        <option value="passfort_no">Passport</option>
                    </select>
                </div>
                <div class="col-sm-3">
                    <input type="text" class="form-control" id="emp_id" placeholder="Type Employee ID Here..."
                        name="emp_id" value="{{old('emp_id')}}" autofocus="autofocus" required>
                    @if ($errors->has('emp_id'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('emp_id') }}</strong>
                    </span>
                    @endif
                </div>
                <div class="col-md-2">
                    <button type="submit" onclick="EmployeeAdvanceList()"
                        class="btn btn-primary waves-effect">SEARCH</button>
                </div>
                 
            </div>
        </div>
        <div class="card-body d-none" id="advance_searching_result_section">
            <div class="row">
                <div class="col-12">
                    <div class="table-responsive">
                        <table id="alltableinfo" class="table table-bordered custom_table mb-0">
                            <thead>
                                <tr>                                       
                                    <th>S.N</th>
                                    <th>Emp.ID</th>
                                    <th>Name</th>
                                    <th>Iqama/Passport</th>
                                    <th>Salary</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                    <th>Purpose</th>
                                    <th>Remarks</th>
                                    <th>Inserted By</th>
                                    <th>Inserted At</th>
                                    <th>Paper</th>
                                    <th>Manage</th>
                                </tr>
                            </thead>
                            <tbody id="addvancerecordlist">                                    
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>    
</div>

<!--  Employee Advance Paper Upload section !-->
<div class="row d-none" id="advance_paper_upload_section">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body card_form">
                <div class="form-group row custom_form_group">
                    <label class="col-sm-2 control-label"> Advance Records From</label>
                    <div class="col-sm-2">
                        <input type="date" name="from_date" value="<?= date("Y-m-d") ?>" max="{{date('Y-m-d') }}" class="form-control">
                    </div>                      
                    <label class="col-sm-2 control-label">To</label>
                    <div class="col-sm-2">
                        <input type="date" name="to_date" value="<?= date("Y-m-d") ?>" max="{{date('Y-m-d') }}" class="form-control">
                    </div>                    
                    <div class="col-sm-2"> 
                        <button type="button"  id ="emp_search_button"  onclick="searchAdvanceInsertedEmployees()" class="btn btn-primary waves-effect">Search</button>
                    </div>
                </div>

            </div>

        </div>
    </div> 

    <!-- Employee Advance Paper Upload !-->   
    <div class="card">
        <form method="post" action="{{ route('advance.paper.upload.request') }}" id="advance_paper_upload_form" enctype="multipart/form-data" 
             onsubmit="attendance_submit_button.disabled = true;">
                @csrf
                @method('put')
                <div class="card-header">
                    <div class="row">
                        <div class="form-group row custom_form_group"> 
                            <label class="col-sm-2 control-label">Upload File:</label>
                            <div class="col-sm-2">
                                <div class="input-group">
                                    <span class="input-group-btn">
                                        <span class="btn btn-default btn-file btnu_browse">
                                            Browse… <input type="file" name="advance_paper" id="imgInp4">
                                        </span>
                                    </span>
                                    <input type="text" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <img id='img-upload4' class="upload_image" />
                            </div>
                            <div class="col-sm-2"> <label for="" id="total_selection_label"></label></div>
                            <div class="col-sm-2">
                                <button type="submit" id="attendance_submit_button" name="attendance_submit_button" class="btn btn-primary waves-effect">Submit </button>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <span id="data_not_found" class="d-none">Data Not Found!</span>
                                <table id="alltableinfo" class="table table-bordered table-hover custom_table mb-0">
                                    <thead>
                                        <tr>
                                            <th>S.N</th>
                                            <th>Emp.ID</th>
                                            <th>Name</th>
                                            <th>Iqama/Passport</th>
                                            <th>Salary Type</th>
                                            <th>Amount</th>
                                            <th>Purpose</th>
                                            <th>Inserted</th>
                                            <th>Inserted By</th>
                                            <th>Remarks</th>
                                            <th>Selection</th> 
                                            <th>Action</th>  
                                        </tr>
                                    </thead>
                                    <tbody id="advance_paper_list_table"></tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>  
        </form>
    </div>
</div>

   <!-- Advance Received FORM Modal !--> 
<div class="modal fade" id="cash_received_form_modal" tabindex="-1" role="dialog" aria-labelledby="demoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
         <div class="modal-content">
             <div class="modal-body">
                <h5 style="color: green; text-align:center"> ADVANCE RECEIVE FORM </h5>
                <hr>
                <form class="form-horizontal" id="cash_received_form" method="post" target="_blank" action="{{ route('emp.advance.papers.create.request') }}">
                 @csrf


                        <div  class="form-group row custom_form_group">
                            <label class="col-sm-4 control-label"> Project</label>
                            <div class="col-sm-6">
                                <select class="form-select" id="project_id" name="project_id" autofocus >
                                    <option value="">Select Working Project</option>
                                    @foreach($projects as $ap)
                                    <option value="{{ $ap->proj_id }}">{{ $ap->proj_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row custom_form_group">
                            <label class="col-sm-4 control-label">Form Type <span class="req_star">*</span> </label>
                            <div class="col-sm-6">
                                <select class="form-control" name="form_type" id="form_type" required>  
                                    <option value="1">Advance Received</option>
                                    <option value="2">Cash Receipt </option>                                    
                                </select>                                 
                            </div>
                        </div>

                        <div class="form-group row custom_form_group">
                            <input type="hidden" class="form-control" name="request_type" value="2">                       

                            <label class="col-sm-4 control-label">Employee ID<span class="req_star">*</span></label>
                            <div class="col-sm-6"> 
                                <input type="text" class="form-control" name="employee_id" id="employee_id" placeholder="Enter Employee ID Here..." required>                         
                            </div>
                        </div>                       

                        <div class="form-group row custom_form_group">
                            <label class="col-sm-4 control-label">Payment By </label>
                            <div class="col-sm-6">
                                <select class="form-control" name="payment_method" id="payment_method"  required>                             
                                       <option value="CASH">CASH</option>
                                       <option value="BANK">BANK</option>                                    
                                </select>
                                 
                            </div>
                        </div>  
                        <div class="form-group row custom_form_group">
                            <label class="col-sm-4 control-label">Amount<span class="req_star">*</span></label>
                            <div class="col-sm-6"> 
                                <input type="number" class="form-control" name="amount" id="amount" placeholder="Enter Amount Here..." value="">                         
                            </div>
                        </div> 

                        <div class="form-group row custom_form_group">
                            <label class="col-sm-4 control-label">Date<span class="req_star">*</span> </label>
                            <div class="col-sm-6"> 
                                <input type="date" name="received_date" value="<?= date("Y-m-d") ?>"   class="form-control">
                             </div>
                        </div> 
                        
                        
                        <div class="form-group row custom_form_group">
                            <label class="col-sm-4 control-label">Receiver Type<span class="req_star">*</span></label>
                            <div class="col-sm-6">
                                <select class="form-control" name="receiver_type" id="receiver_type" required>
                                    <option value="">Please Select One </option>                                       
                                    <option value="Employee"> Employee</option>
                                    <option value="Subcontract"> Subcontract</option>   
                                    <option value="Other-Bus"> Other-Bus</option> 
                                    <option value="Office"> Office</option> 
                                    <option value="Site Expense"> Site Expense</option>
                                    <option value="Catering Expense"> Catering Expense</option>
                                    <option value="Travel Expense"> Travel Expense</option> 
                                    <option value="Sweet Water"> Sweet Water</option> 
                                    <option value="Waste Water"> Waste Water</option> 
                                    <option value="Advance Payment"> Advance Payment</option> 
                                    <option value="Medical Treatmen">Medical Treatment</option>
                                </select>                                 
                            </div>
                        </div>

                        <div class="form-group row custom_form_group">
                            <label class="col-sm-4 control-label">Remarks</label>
                            <div class="col-sm-6"> 
                                <textarea class="form-control" rows="3"  name="remarks" id="remarks" ></textarea>
                            </div>
                        </div>
                        
                        <button type="submit" id="invoice_report_button"  class="btn btn-primary waves-effect"  style="border-radius: 15px; width: 150px; height: 40px; letter-spacing: 1px;">Create Form</button>
                </form>                  
              </div>
          </div>
    </div>
</div>

<script>


    function openSingleEmployeeAdvanceSection(){
        
         $('#emp_advance_search_section').removeClass('d-block').addClass('d-none');
         $('#multiple_employee_advance_section').removeClass('d-block').addClass('d-none');
         $('#advance_paper_upload_section').removeClass('d-block').addClass('d-none');
         $('#single_emp_advance_section').removeClass('d-none').addClass('d-block');
         document.getElementById("empl_info").focus();
                       
    }
    
    function openMultipleEmployeeAdvanceSection(){
        
        $('#single_emp_advance_section').removeClass('d-block').addClass('d-none');
        $('#emp_advance_search_section').removeClass('d-block').addClass('d-none');
        $('#advance_paper_upload_section').removeClass('d-block').addClass('d-none');
        $('#multiple_employee_advance_section').removeClass('d-none').addClass('d-block'); 
         document.getElementById("multi_emp_id").focus();              
    }
    
    function openAdvanceSearchingSection(){
        
        $('#single_emp_advance_section').removeClass('d-block').addClass('d-none');
        $('#multiple_employee_advance_section').removeClass('d-block').addClass('d-none');   
        $('#emp_advance_search_section').removeClass('d-none').addClass('d-block');
        $('#advance_paper_upload_section').removeClass('d-block').addClass('d-none');  
        document.getElementById("emp_id").focus();              
    }
    
    function openEmployeeAdvancePaperUploadSection() {
    
            $('#single_emp_advance_section').removeClass('d-block').addClass('d-none');
            $('#multiple_employee_advance_section').removeClass('d-block').addClass('d-none');
            $('#emp_advance_search_section').removeClass('d-block').addClass('d-none');
            $('#advance_paper_upload_section').removeClass('d-none').addClass('d-block');
            
        }
    

    // Method For Reset All Loaded Data
    function resetEmpInfo() {
        
        $("#show_employee_id").html('');
        $("#show_employee_name").html('');
        $("#show_employee_akama_no").html('');
        $("#show_employee_passport_no").html('');
        $("#show_employee_job_status").html('');
        $("#show_salary_amount").html('');
        $("#show_salary_type").html('');
        $("#show_employee_mobile_no").html('');
        $("#show_employee_agency_name").html('');
        $("#show_employee_category").html('');
        $("#show_employee_sponsor_name").html('');
        $("#show_emp_project_name").html('');
        
    }

    // Enter Key Press Event Fire For Emloyee Advance Searching
    $('#emp_id').keydown(function (e) {
        if (e.keyCode == 13) {
            EmployeeAdvanceList();
        }
    })

    // Enter Key Press Event Fire For Emloyee Searching
    $('#empl_info').keydown(function (e) {
        if (e.keyCode == 13) {
            singleEmoloyeeDetails();
        }
    })
    
    $('#multi_emp_id').keydown(function (e){
        if(e.keyCode == 13){
          searchProjectWiseEmployeeList();
        }
    })
 
 
    // searching employee advance
  
     function EmployeeAdvanceList() {

        var searchValue = $("#emp_id").val();
        var searchType = $('#adv_searchBy').find(":selected").val();    

        $('#addvancerecordlist').html('');
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: {
                search_by: searchType,
                employee_searching_value: searchValue,
            },
            url: "{{ route('employee.advance.list.search') }}",
            success: function (response) {
                
                if (response.status == 200) {
                  
                   
                    var rows = "";
                    var counter = 1;
                    $.each(response.data, function (key, value) {
                        var editurl = "{{ url('admin/employee/advance/payment/edit')}}" + "/" + value.id;
                        var deleteurl = "{{ url('admin/employee/advance/payment/delete')}}" + "/" + value.id;

                        rows += `
                        <tr>

                            <td>${counter++}</td>
                            <td>${value.employee_id}</td>
                            <td>${value.employee_name}</td>
                            <td>${value.akama_no}, ${value.passfort_no}</td>
                            <td>${value.hourly_employee == 1 ? 'Hourly' : 'Basic Salary' }</td>
                            <td> ${value.adv_amount}</td>  
                            <td> ${value.date}</td>  
                            <td> ${value.purpose}</td>   
                            <td> ${value.adv_remarks == null ? '-' : value.adv_remarks  }</td>                              
                            <td> ${value.inserted_by}</td>                                                                                  
                            <td> ${value.created_at}</td>  
                            <td><a target="_blank" href="{{ url('${value.advance_paper}') }}"> <i class="fa fa-eye"></i>  </a> </td>
                            <td id="">
                                @can('employee_advance_edit')
                                    
                                   <a href="" id="adv_edit_button" data-toggle="modal" data-target="#emp_adv_edit_modal" data-id="${value.id}"> <i class="fa fa-pencil-square fa-lg edit_icon"></i> | </a>

                                @endcan
                            
                                @can('employee_advance_delete')
                                    <a href="${deleteurl}" title="delete" id="delete"><i class="fa fa-trash fa-lg delete_icon"></i></a>
                                @endcan
                            </td>
                            
                        </tr>
                        `
                    });
                    if(counter == 0){
                         showMessage('Advance Record Not Found','error');
                         return ;
                    }  
                    $('#advance_searching_result_section').removeClass('d-none').addClass('d-block');                
                    $('#addvancerecordlist').html(rows);
                } else {
                    showMessage(response.message,'error');
                }
            },
            error:function(response){
                showMessage('Operation Failed','error');
            }
        });
    }

    //   Searching Employee Details  by Employee ID
    function singleEmoloyeeDetails() {

        resetEmpInfo(); // reset All Employe Info
        var searchType = $('#searchBy').find(":selected").val();
        var searchValue = $("#empl_info").val();

        if ($("#empl_info").val().length === 0) {
            showMessage('Please Input Valid Data','error');
            return;
        }
        $.ajax({
                type: 'POST',
                 url: "{{ route('employee.searching.searching-with-multitype.parameter') }}", // all job status employee
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
                        $("#advance_insertion_section").removeClass("d-block").addClass("d-none");
                        return;
                    } else {
                        $("span[id='employee_not_found_error_show']").removeClass('d-block').addClass('d-none');
                    }                  

                    if (response.total_emp > 1) {
                        alert('Multiple Employee Found, Please Contact with Software Engineer');
                    } else {                       
                         showSearchingEmployee(response.findEmployee[0]);
                    }
                    
                } // end of success
            }); 
        
    }
     
   
    
      function showSearchingEmployee(findEmployee) {
          
        
        $("#advance_insertion_section").removeClass("d-none").addClass("d-block");
        $("input[id='form_emp_id']").val(findEmployee.employee_id);

        $("span[id='show_employee_id']").text(findEmployee.employee_id);
        $("span[id='show_employee_name']").text(findEmployee.employee_name);
        $("span[id='show_employee_akama_no']").text(findEmployee.akama_no);
        $("span[id='show_employee_passport_no']").text(findEmployee.passfort_no);
        $("span[id='show_employee_job_status']").text(findEmployee.title);
        $("span[id='show_employee_mobile_no']").text(findEmployee.mobile_no);
        $("span[id='show_employee_agency_name']").text(findEmployee.agc_title);
        $("span[id='show_employee_category']").text(findEmployee.catg_name);
        
        $("span[id='show_salary_type']").text((findEmployee.hourly_employee == 1 ? "Hourly":"Basic"));
        $("span[id='show_salary_amount']").text((findEmployee.hourly_employee == 1 ? findEmployee.hourly_rent:findEmployee.basic_amount));
        $("span[id='show_emp_project_name']").text(findEmployee.proj_name);        



        if (findEmployee.job_status == 1) {
            $("span[id='show_employee_job_status']").text("Active");
        } else if (findEmployee.job_status == 2) {
            $("span[id='show_employee_job_status']").text("Inactive");
        } else if (findEmployee.job_status == 3) {
            $("span[id='show_employee_job_status']").text("Prerelease");
        } else if (findEmployee.job_status == 4) {
            $("span[id='show_employee_job_status']").text("Release");
        } else if (findEmployee.job_status == 5) {
            $("span[id='show_employee_job_status']").text("Vacation");
        }

        /* Show sponsor name */
        if (findEmployee.sponsor_id == null) {
            $("span[id='show_employee_sponsor_name']").text("No Assigned Sponsor!");
        } else {
            $("span[id='show_employee_sponsor_name']").text(findEmployee.spons_name);
        }

       
 
    }

    // project wise multiple employee form validation
    $(document).ready(function () {
        
        $("#employeeListForm").validate({
            submitHandler: function (form) {
                return false;
            },
            rules: {
                proj_name: {
                    required: true,
                },
                acomdOfbId: {
                    required: true,
                },
            },
            messages: {
                proj_name: {
                    required: "You Must Be Select This Field!",
                },
                acomdOfbId: {
                    required: "You Must Be Select This Field!",
                },
            },
        });
        
        // new advance insertion form submit
        $("#advance_insert_form").submit(function (e) {
        
                e.preventDefault();
                var form = $("#advance_insert_form");
                var data =  new FormData($(this)[0]);  // if same name two form then o index
                var action = form.attr("action");
                document.getElementById("adv_save_btn").disabled = true;

                $.ajax({
                        url: action,
                        method: form.attr("method"),
                        data: data,
                        processData: false,
                        contentType: false,
                        beforeSend:function(){
                                
                        },
                })
                .done(function(response) {        
                        document.getElementById("adv_save_btn").disabled = false;
                        if(response.status == 200){                                    
                            resetAdvanceInsertForm(); 
                            $("#advance_insertion_section").removeClass("d-block").addClass("d-none");                                    
                            showMessage(response.message,'success');
                        }else {
                            showMessage(response.message,'error');
                        }                                             
                })
                .fail(function(xhr) {
                    showMessage("Operation Failed, Please Try Aggain",'error');
                    document.getElementById("adv_save_btn").disabled = false;
                });
        });
        
    });


    // Searching Employees For Multiple Employee Advance Payment
    function searchProjectWiseEmployeeList() {

        var project_id = $('select[name="proj_name"]').val();
        var building_id = $('select[name="acomdOfbId"]').val();
        var multi_emp_id = $('#multi_emp_id').val();

        if (!project_id && multi_emp_id == null) {
            showMessage('Please Input Multiple Employee ID Or Select Project Name','error');
            return;
        }

        $('#employee_list_table').html('');
       
        
        $.ajax({
                type: 'POST',
                dataType: 'json',
                data: {
                    project_id: project_id,
                    building_id: building_id,
                    multi_emp_id:multi_emp_id,
                },
                url: "{{ route('employee.advance.employee.list.foradvance.ajax.request') }}",
                success: function (response) {
 
                    if (response.status == 200) { 
                         
                       $("#multiple_employee_adv_form").removeClass("d-none").addClass("d-block");                                    
                        
                        var rows = "";
                        var counter = 1;
                        $.each(response.empList, function (key, value) {
                            rows += `
                                    <tr>
                                        <td>${counter++}</td>
                                        <td> ${value.employee_id}</td>
                                        <td>${value.employee_name}</td>
                                        <td>${value.akama_no}</td>
                                        <td>${value.basic_amount} / ${value.hourly_rent}</td>
                                        <td>
                                        <input type="hidden" id="emp_auto_id${value.emp_auto_id}" name="emp_auto_id[]" value="${value.emp_auto_id}">
                                          <input type="hidden" name="project_id_${value.emp_auto_id}" value = "${value.project_id}" required >
                                         <input type="number" name="adv_amount-${value.emp_auto_id}" id="adv_amount-${value.emp_auto_id}" placeholder="Type Here.." style="width:110px;" min="0">
                                        </td>
                                        <td><input type="checkbox" name="adv_checkbox-${value.emp_auto_id}" id="adv_checkbox-${value.emp_auto_id}" value="0"></td>

                                    </tr>
                                    `
                        });
                        $('#employee_list_table').html(rows);
                    } else {
                        $("span[id='employee_not_found_error_show']").removeClass('d-block').addClass('d-none');
                        showMessage('Employee Not Found','error');
                    }

                } ,
                error:function(response){
                    showMessage('Operation Failed Try Again','error');
                }
        });
         
    }
    
        // Employee Searching for Advance Paper Upload 
    function searchAdvanceInsertedEmployees(){

        var from_date = $('input[name="from_date"]').val();
        var to_date = $('input[name="to_date"]').val(); 
        
          $('#advance_paper_list_table').html('');

        $.ajax({
            type:"POST",
            url: "{{  route('advance.paper.upload.formult.employee') }}",
            data:{
                from_date:from_date,
                to_date:to_date,
            },
            success:function(response){   
                if(response.status == 200){          

                    $("#advance_paper_list_section").removeClass("d-none").addClass("d-block");  
                   
                    
                    var rows = "";
                    var counter = 1;
                    $.each(response.data, function (key, value) { 
                        var viewurl =  value.advance_paper == null ? '':'<a target="_blank" href="{{ url('${ value.advance_paper}') }}" class="btn btn-success">View </a>' ;                    
                        rows += `
                                    <tr>
                                        <td>${counter++}</td>
                                        <td>${value.employee_id}</td>
                                        <td>${value.employee_name}</td>
                                        <td>${value.akama_no}, ${value.passfort_no}</td>
                                        <td>${value.hourly_employee == 1 ? 'Hourly' : 'Basic Salary' }</td>
                                        <td> ${value.adv_amount}</td> 
                                        <td>${value.purpose}</td>
                                        <td> ${value.created_at}</td>  
                                        <td> ${value.inserted_by}</td> 
                                        <td> ${value.adv_remarks == null ? '-' : value.adv_remarks  }</td>    
                                        <td>
                                            <input type="hidden" id="adv_auto_id${value.adv_auto_id}" name="adv_auto_id_list[]" value="${value.adv_auto_id}">                                                             
                                           <input type="checkbox" name="adv_paper_checkbox-${value.adv_auto_id}" onclick="countTotalSelection()" id="adv_paper_checkbox-${value.adv_auto_id}" value="0">
                                        </td>
                                        <td> ${viewurl} </td>   
                                        <td style="color:#fff">${value.adv_auto_id}</td>  
                                    </tr>
                                    `
                                    // <td style="color:white"> ${value.adv_auto_id}</td>
                                    
                                  
                                            
                                            
                    });
                    $('#advance_paper_list_table').html(rows);
                    if(counter == 1){
                         showMessage('Advance Record Not Found','error');
                    }
                }else {
                    showMessage('Data Not Found','error');
                }
            },
            error:function(response){
                showMessage('Operation Failed','error');
            }
        });

    }
    
    // Checkbox Selected Counter 
    function countTotalSelection(){
        let myTable = document.getElementById('advance_paper_list_table');
            var counter = 0;
            
            for (let row of myTable.rows) {
                allCell = row.cells;          
                var chkboxId = "adv_paper_checkbox-" + allCell[12].innerText;                   
                (document.getElementById(chkboxId).checked) ? counter++ : 0;  
            }
            (document.getElementById('total_selection_label')).innerText = "Total Selected: "+counter;
    }

    function showMessage(message,operationType){
         //  start message
         const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            })
           
                Toast.fire({
                    type: operationType,
                    title: message
                })
            
    }
 
   
    // Open Modal For Update Employee Salary Information
    $(document).on("click", "#adv_edit_button", function(){

        var adv_auto_id = $(this).data('id');       
        var editurl = "{{ url('admin/employee/advance/payment/edit')}}" + "/" + adv_auto_id;
     
        $.ajax({
        type: "GET",
        url: editurl, 
        success: function(response){
         
            if(response.status == 200){                 
                 var arecord = response.data;
                 $('#modal_adv_audo_id').val(arecord.id);
                 $('#modal_emp_audo_id').val(arecord.emp_id);
                 $('#modal_adv_purpose_id').val(arecord.adv_purpose_id);
                 $('#modal_adv_amount').val(arecord.adv_amount);
                 $('#modal_installes_month').val(arecord.installes_month);
                 $('#modal_adv_date').val(arecord.date);                 
                 $('#modal_adv_remarks').val(arecord.adv_remarks);
            }else{              
               showSweetAlert('Update Operation Failed, Please try Again ','error');  
            }                   
        },
        error:function(response){
            showSweetAlert('Operation Failed ','error');  
        }
        })

    });

    function updateEmployeeAdvance(){
     
         var modal_adv_audo_id = $('#modal_adv_audo_id').val();
         var modal_emp_audo_id = $('#modal_emp_audo_id').val();
         var modal_adv_purpose_id = $('#modal_adv_purpose_id').val();
         var modal_adv_amount = $('#modal_adv_amount').val();
         var modal_installes_month = $('#modal_installes_month').val();
         var modal_adv_date = $('#modal_adv_date').val();              
         var modal_adv_remarks = $('#modal_adv_remarks').val();

         if(modal_adv_audo_id <=0 || modal_adv_amount <=0 || modal_installes_month <= 0 ){
            showMessage("Please Input Valid Data",'error');
            return;
         }
         $("#emp_adv_edit_modal").modal('hide'); // hide modal

        $.ajax({
            type:"POST",
            url:"{{route('update-advance.pay')}}",
            data:{
                modal_adv_audo_id:modal_adv_audo_id,
                modal_emp_audo_id:modal_emp_audo_id,
                modal_adv_purpose_id:modal_adv_purpose_id,
                modal_adv_amount:modal_adv_amount,
                modal_installes_month:modal_installes_month,
                modal_adv_date:modal_adv_date,
                modal_adv_remarks:modal_adv_remarks,                
            },
            datatype:"json",
            success:function(response){
               if(response.status == 200){
                showMessage('Update Operation Successfully Completed','success');  
                EmployeeAdvanceList();               
               }else {
                showMessage('Update Operation Failed','error');  
               }
              
            },
            error:function(response){
                showMessage('Network Error','error');  
            }
            
        })

    }
    
    // reset advance update model UI
    $('#emp_adv_edit_modal').on('hidden.bs.modal', function (e) {
      $(this)
      .find("input,textarea,select").val('').end()
      .find("input[type=checkbox], input[type=radio]").prop("checked", "").end();
    })


    function resetAdvanceInsertForm() 
    {
        $('#advance_insert_form')[0].reset();
    }



</script>

@endsection
