@extends('layouts.admin-master')
@section('title')Emp List Report @endsection
@section('content')
<div class="row bread_part">
  <div class="col-sm-12 bread_col">
    <h4 class="pull-left page-title bread_title"> Employees Report</h4>
    <ol class="breadcrumb pull-right">
      <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
      <li class="active"> Employees Report </li>
    </ol>
  </div>
</div>
<!-- Session Message Flash -->
<div class="row">
  <div class="col-md-2"></div>
  <div class="col-md-8">
    @if(Session::has('error'))
    <div class="alert alert-warning alerterror" role="alert">
      <strong> {{Session::get('error')}} </strong>
    </div>
    @endif
  </div>
</div>


<!-- Employee List Project Wise report section -->
<div class="row">
  <div class="col-md-2"></div>
  <div class="col-md-8">
    <form class="form-horizontal" target="_blank" action="{{ route('project-wise.employee.process') }}" method="post">
      @csrf
      <div class="card">
        <div class="card-header">  <h3 class="card-title card_top_title salary-generat-heading">Employees Information Report </h3> </div>
        <div class="card-body card_form" style="padding-top: 0;">

          <!--Project List Dropdown -->
          <div class="form-group row custom_form_group">
            <label class="control-label col-md-3">Project Name:</label>
            <div class="col-md-6">
              <select class="form-control" name="proj_id">
                <option value="">All Project</option>
                @foreach($projects as $proj)
                <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                @endforeach
              </select>
            </div>
          </div>

        <!--Sponsor List Dropdown -->
          <div class="form-group row custom_form_group">
            <label class="control-label col-md-3">Sponser Name:</label>
            <div class="col-md-6">
              <select class="form-control" name="spons_id">
                <option value="">All Sponsor </option>
                @foreach($sponser as $spons)
                <option value="{{ $spons->spons_id }}">{{ $spons->spons_name }}</option>
                @endforeach
              </select>
            </div>
          </div>

            <!--Trade/Designation List Dropdown -->
          <div class="form-group row custom_form_group">
            <label class="control-label col-md-3">Employee Trade:</label>
            <div class="col-md-6">
              <select class="form-control" name="catg_id">
                <option value="">All Trade</option>
                @foreach($category as $cate)
                <option value="{{ $cate->catg_id }}">{{ $cate->catg_name }}</option>
                @endforeach
              </select>
            </div>
          </div>

              <!--EMployee Status List Dropdown -->
          <div class="form-group row custom_form_group">
            <label class="control-label col-md-3">Employee Status:</label>
            <div class="col-md-6">
              <select class="form-control" name="job_status">
                <!--<option value="">Select Employee Status</option>-->
                @foreach($jobStatus as $status)
                <option value="{{ $status->id }}">{{ $status->title }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <!--Report Format Dropdown -->
          <div class="form-group row custom_form_group">
            <label class="control-label col-md-3"> Report Format:</label>
            <div class="col-md-6">
              <select class="form-control" name="report_format" required>
                <option value="1">Print</option>
                <option value="2">Excell</option>
                <option value="3">CSV</option>
               </select>
            </div>
          </div>


        </div>
        <div class="card-footer card_footer_button text-center">
          <button type="submit" class="btn btn-primary waves-effect">Show Report</button>
        </div>
      </div>
    </form>
  </div>
  <div class="col-md-2"></div>
</div>


  <!-- Employee Type wise report section -->
 <div class="row">
  <div class="col-md-2"></div>
  <div class="col-md-8">
    <form class="form-horizontal" target="_blank" action="{{ route('employe.list.projec.and.type.wise.process') }}" method="post">
      @csrf
      <div class="card">
        <div class="card-header">  <h3 class="card-title card_top_title salary-generat-heading">Project and Employee Type (Basic & Hourly Salary) Report </h3>   </div>
        <div class="card-body card_form" style="padding-top: 0;">

    
    
          <div class="form-group row custom_form_group">
            <label class="control-label col-md-3">Project Name:</label>
            <div class="col-md-6">
              <select class="form-control" name="proj_id">
                <option value="">All Project</option>
                @foreach($projects as $proj)
                <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                @endforeach
              </select>
            </div>
          </div> 
 
          {{-- Employee type List 1 = direct, 2 = indirect --}}
        
          <div class="form-group row custom_form_group">
            <label class="control-label col-md-3"> Employee Type:</label>
            <div class="col-md-6">
              <select class="form-control" name="emp_type_id" required>
                <option value="">Select Employee Type</option>
                <option value="-1">Direct Employee (Basic Salary)</option>
                @foreach($emp_types as $item)
                <option value="{{ $item->id }}">{{ $item->name }}</option>
                @endforeach
                <option value="3">Basic Salary (Direct & Indirect) Employees</option>
              </select>
            </div>
          </div>

          <div class="form-group row custom_form_group">
            <label class="control-label col-md-3"> Report Format:</label>
            <div class="col-md-6">
              <select class="form-control" name="report_format" required>
                <option value="1">Print</option>
                <option value="2">Excell</option>
                <option value="3">CSV</option>
               </select>
            </div>
          </div>


        </div>
        <div class="card-footer card_footer_button text-center">
          <button type="submit" class="btn btn-primary waves-effect">Show Report</button>
        </div>
      </div>
    </form>
  </div>
  <div class="col-md-2"></div>
</div>

 <!-- Employee List From Salary Record report section -->
 <div class="row">
  <div class="col-md-2"></div>
  <div class="col-md-8">
    <form class="form-horizontal" target="_blank" action="{{ route('salary-month.project-wise.employee.process') }}" method="post">
      @csrf
      <div class="card">
        <div class="card-header">  <h3 class="card-title card_top_title salary-generat-heading"> Employee List Report From Salary Month  </h3>   </div>
        <div class="card-body card_form" style="padding-top: 0;">

         <!--Project List Dropdown -->
         <div class="form-group row custom_form_group">
            <label class="control-label col-md-3">Project Name:</label>
            <div class="col-md-6">
              <select class="form-control" name="project_id">
                <option value="">All Project</option>
                @foreach($projects as $proj)
                <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                @endforeach
              </select>
            </div>
          </div>
          
             <!--Sponsor List Dropdown -->
           <div class="form-group row custom_form_group">
            <label class="control-label col-md-3">Sponser Name:</label>
            <div class="col-md-6">
              <select class="form-control" name="sponsor_id">
                <option value="">All Sponsor</option>
                @foreach($sponser as $spons)
                <option value="{{ $spons->spons_id }}">{{ $spons->spons_name }}</option>
                @endforeach
              </select>
            </div>
          </div>

      <!--Salary Month Dropdown -->
          <div class="form-group row custom_form_group">
            <label class="control-label col-md-3">Salary Month</label>
            <div class="col-md-6">
              <select class="form-control" name="month">
                <option value="1">January </option>
                <option value="2">February </option>
                <option value="3">March </option>
                <option value="4">April </option>
                <option value="5">May </option>
                <option value="6">June </option>
                <option value="7">July </option>
                <option value="8">August </option>
                <option value="9">September </option>
                <option value="10">October </option>
                <option value="11">November </option>
                <option value="12">December </option>                           
              </select>
            </div>
          </div>  


        <!-- Year Dropdown -->
          
          <div class="form-group row custom_form_group">
            <label class="control-label col-md-3">Year</label>
            <div class="col-md-6">
              <select class="form-control" name="year" required>
                    @foreach(range(date('Y'), date('Y')-2) as $y)
                      <option value="{{$y}}" {{$y}}>{{$y}}</option>
                    @endforeach
              </select>
            </div>
          </div>

          <div class="form-group row custom_form_group">
            <label class="control-label col-md-3"> Report Format:</label>
            <div class="col-md-6">
              <select class="form-control" name="report_format" required>
                <option value="1">Print</option>
                <option value="2">Excell</option>
                <option value="3">CSV</option>
               </select>
            </div>
          </div>


        </div>
        <div class="card-footer card_footer_button text-center">
          <button type="submit" class="btn btn-primary waves-effect">Show Report</button>
        </div>
      </div>
    </form>
  </div>
  <div class="col-md-2"></div>
</div>

<!-- Employee List By Iqama Expired Date -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <form class="form-horizontal" target="_blank"
            action="{{ route('employee-list-projectwise-by-iqama-expire-date') }}" method="post">
            @csrf
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title card_top_title salary-generat-heading"> Employee Report by Iqama Expiration
                        Date </h3>
                </div>
                <div class="card-body card_form" style="padding-top: 0;">

                    <!--Project List Dropdown -->
                    <div class="form-group row custom_form_group">
                        <label class="control-label col-md-3">Project Name:</label>
                        <div class="col-md-6">
                            <select class="form-control" name="project_id">
                                <option value="">All Project</option>
                                @foreach($projects as $proj)
                                <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!--Time select From Dropdown -->
                    <div class="form-group row custom_form_group">
                        <label class="control-label col-md-3">Expiry Duration:</label>
                        <div class="col-md-6">
                            <select class="form-control" name="expire_durations">
                                <option value="">Select Expiry Time Duration</option>
                                <option value="1">Already Expired</option>
                                <option value="2">This Month</option>
                                <option value="3">Next Month</option>
                                <option value="4">After 3 Months</option>
                                <option value="5">After 6 Months</option>
                                <option value="6">After 12 Months</option>
                                <option value="7">After 2 Years</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row custom_form_group">
                        <label class="col-sm-3 control-label">Iqama Expire Date:<span class="req_star">*</span></label>
                        <div class="col-sm-6">
                            <input type="date" class="form-control" name="iqama_date" value="{{ date('Y-m-d') }}">
                        </div>
                    </div>

                    <!-- <div class="form-group row custom_form_group">
            <label class="control-label col-md-3"> Report Format:</label>
            <div class="col-md-6">
              <select class="form-control" name="report_format" required>
                <option value="1">Print</option>
                <option value="2">Excell</option>
                <option value="3">CSV</option>
               </select>
            </div>
          </div> -->


                </div>
                <div class="card-footer card_footer_button text-center">
                    <button type="submit" class="btn btn-primary waves-effect">Show Report</button>
                </div>
            </div>
        </form>
    </div>
    <div class="col-md-2"></div>
</div>


<!-- Date Wise New Employee Insert Report -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <form class="form-horizontal" target="_blank" action="{{ route('employee.list.new-emp-insert-details.by-date-to-date') }}"
            method="post">
            @csrf
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title card_top_title salary-generat-heading"> Date Wise New Employee Insert Report</h3>
                </div>
                <div class="card-body card_form" style="padding-top: 0;">

                    <div
                    class="form-group row custom_form_group{{ $errors->has('from_date') ? ' has-error' : '' }}">
                    <label class="col-sm-3 control-label">From Date:<span class="req_star">*</span></label>
                    <div class="col-sm-6">
                        <input type="date" class="form-control" name="from_date"
                            value="<?= date(" Y-m-d") ?>" max="{{ Carbon\Carbon::now()->format('Y-m-d') }}" required>
                    </div>
                </div>

                <div
                    class="form-group row custom_form_group{{ $errors->has('today') ? ' has-error' : '' }}">
                    <label class="col-sm-3 control-label">To Date:<span class="req_star">*</span></label>
                    <div class="col-sm-6">
                        <input type="date" class="form-control" name="today" value="{{ Carbon\Carbon::now()->format('Y-m-d') }}"
                        max="{{ Carbon\Carbon::now()->format('Y-m-d') }}" required>
                    </div>
                </div>
                
                 <!-- Employee type List 1 = direct, 2 = indirect -->>

                <div class="form-group row custom_form_group">
                    <label class="control-label col-md-3"> Employee Type:</label>
                    <div class="col-md-6">
                        <select class="form-control" name="emp_type_id" required>
                            <option value="-1">All</option>
                            <option value="1">Direct Employee (Basic Salary)</option> 
                            <option value="2">Indirect Employee</option>                         
                            <option value="3">Basic Salary (Direct & Indirect) Employees</option>
                            <option value="4">Direct Employee (Hourly)</option>   
                        </select>
                    </div>
                </div>

                </div>
                <div class="card-footer card_footer_button text-center">
                    <button type="submit" class="btn btn-primary waves-effect">Show Report</button>
                </div>
            </div>
        </form>
    </div>
    <div class="col-md-2"></div>
</div>

 
 <!-- Multiple Employee ID Report -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <form class="form-horizontal" target="_blank" action="{{ route('employee.details.multiple.employee.id.report') }}"
            method="post">
            @csrf
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title card_top_title salary-generat-heading"> Multiple ID Base Employee Details</h3>
                </div>
                <div class="card-body card_form" style="padding-top: 0;">

                <div  class="form-group row custom_form_group{{ $errors->has('from_date') ? ' has-error' : '' }}">
                    <label class="col-sm-3 control-label">Employees ID:<span class="req_star">*</span></label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" name="multiple_employee_Id"  required>
                    </div>
                </div> 

                </div>
                <div class="card-footer card_footer_button text-center">
                    <button type="submit" class="btn btn-primary waves-effect">Show Report</button>
                </div>
            </div>
        </form>
    </div>
    <div class="col-md-2"></div>
</div>
 

@endsection
