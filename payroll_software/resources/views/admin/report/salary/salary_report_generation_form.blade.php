@extends('layouts.admin-master')
@section('title') Salary Report @endsection
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
    background: rgba(255, 255, 255, 0.8) url('{{ asset("Loading.gif")}}') center no-repeat;
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
    <h4 class="pull-left page-title bread_title"> Salary Report </h4>
    <ol class="breadcrumb pull-right">
      <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
      <li class="active"> Report</li>
    </ol>
  </div>
</div>

<div class="row">
    
    @can('prevacation_salary_summary_statement')
        <div class="col-md-4 col-xl-3">
            <a href="" id="leave_application_edit_button" data-toggle="modal" data-target="#prevacation_salary_report_modal">
              <div class="mini-stat clearfix bx-shadow bg-white">
                  <span class="mini-stat-icon bg-primary"><i class="md md-person"></i></span>
                  <div class="mini-stat-info text-center text-dark mini_stat_info">
                      Prevacation Salary Report
                  </div>        
              </div>
            </a>
        </div>
    @endcan  
    
    @can('salary_closing_employee_report')
        <div class="col-md-4 col-xl-3">
          <a href="" id="leave_application_edit_button" data-toggle="modal" data-target="#salary_closing_report_modal">
            <div class="mini-stat clearfix bx-shadow bg-white">
                <span class="mini-stat-icon bg-primary"><i class="md md-person"></i></span>
                <div class="mini-stat-info text-center text-dark mini_stat_info">
                  Salary Closing Report
                </div>        
            </div>
          </a>
        </div>
    @endcan 
    
    @can('salary_hold_employees_salary_report')
        <div class="col-md-4 col-xl-3">
          <a href="" id="leave_application_edit_button" data-toggle="modal" data-target="#salary_hold_report_modal">
            <div class="mini-stat clearfix bx-shadow bg-white">
                <span class="mini-stat-icon bg-primary"><i class="md md-person"></i></span>
                <div class="mini-stat-info text-center text-dark mini_stat_info">
                  Salary Hold Employees
                </div>        
            </div>
          </a>
        </div>
    @endcan
    
      @can('office_staff_employee_salary_sheet_print')
    <div class="col-md-4 col-xl-3">
      <a href="" id="leave_application_edit_button" data-toggle="modal" data-target="#office_staff_salary_report_modal">
        <div class="mini-stat clearfix bx-shadow bg-white">
            <span class="mini-stat-icon bg-primary"><i class="md md-person"></i></span>
            <div class="mini-stat-info text-center text-dark mini_stat_info">
              Staff Salary
            </div>        
        </div>
      </a>
    </div>
  @endcan
  
  @can('debit_invoice_daily_report')
    <div class="col-md-4 col-xl-3">
      <a href="" id="expense_report_button" data-toggle="modal" data-target="#expense_by_empid_report_modal">
        <div class="mini-stat clearfix bx-shadow bg-white">
            <span class="mini-stat-icon bg-primary"><i class="md md-person"></i></span>
            <div class="mini-stat-info text-center text-dark mini_stat_info">
             Cash Received Report
            </div>        
        </div>
      </a>
    </div>
  @endcan
  
  
    @can('salary_bonus_report')
      <div class="col-md-4 col-xl-3">
        <a href="" id="bonus_report_button" data-toggle="modal" data-target="#emp_bonus_report_modal">
          <div class="mini-stat clearfix bx-shadow bg-white">
              <span class="mini-stat-icon bg-primary"><i class="md md-person"></i></span>
              <div class="mini-stat-info text-center text-dark mini_stat_info">
               Bonus Report
              </div>
          </div>
        </a>
      </div>
    @endcan
  
  
    @can('report_salary_summary_designation_head')
        <div class="col-md-4 col-xl-3">
            <a href="" id="bonus_report_button" data-toggle="modal" data-target="#sponsor_type_salary_report__cost_controll_modal">
            <div class="mini-stat clearfix bx-shadow bg-white">
                <span class="mini-stat-icon bg-primary"><i class="md md-person"></i></span>
                <div class="mini-stat-info text-center text-dark mini_stat_info">
                Project Cost Allocation
                </div>
            </div>
            </a>
        </div>
    @endcan
    
    @can('sponsor_salary_report_menu')
    <div class="col-md-4 col-xl-3">
        <a href="" id="bonus_report_button" data-toggle="modal" data-target="#aproject_current_working_emp_salary_report">
        <div class="mini-stat clearfix bx-shadow bg-white">
            <span class="mini-stat-icon bg-primary"><i class="md md-person"></i></span>
            <div class="mini-stat-info text-center text-dark mini_stat_info">
               Sub-contractor & Asloob Sponsor
            </div>
        </div>
        </a>
    </div>
   @endcan
   
   
    @can('salary_sheet_preview_base_on_emp_type')
        <div class="col-md-4 col-xl-3">
            <a href="" id="bonus_report_button" data-toggle="modal" data-target="#aproject_salary_sheet_preview">
            <div class="mini-stat clearfix bx-shadow bg-white">
                <span class="mini-stat-icon bg-primary"><i class="md md-person"></i></span>
                <div class="mini-stat-info text-center text-dark mini_stat_info">
                Salary Sheet Preview
                </div>
            </div>
            </a>
        </div>
    @endcan
    
    @can('salary_summary_related_all_report')
        <div class="col-md-4 col-xl-3">
            <a href="" id="bonus_report_button" data-toggle="modal" data-target="#salary_summary_related_report_modal">
            <div class="mini-stat clearfix bx-shadow bg-white">
                <span class="mini-stat-icon bg-primary"><i class="md md-person"></i></span>
                <div class="mini-stat-info text-center text-dark mini_stat_info">
                 Project  Salary Summary
                </div>
            </div>
            </a>
        </div>
    @endcan
    

</div> {{-- End of Row --}}


<?php  $current_month = date('m');
    $months = array(12);
    for ($m=1; $m<=12; $m++) {
    $months[$m-1] = date('F', mktime(0,0,0,$m, 1, date('Y')));
    }
?>

<!-- 1 Prevacation Salary Report Modal -->
<div class="modal fade" id="prevacation_salary_report_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="exampleModalLabel">Prevacation Salary Report </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form class="form-horizontal" id="employee_salary_report_form" target="_blank" action="{{ route('anemployee.salary.report') }}" method="post">
        @csrf
          <div class="modal-body">
            <input type="text" id="report_type" hidden name="report_type" value="4">
            <input type="text" class="form-control col-sm-12 typeahead" placeholder="Enter Multiple Employee ID" name="multiple_employee_Id" id="multiple_employee_Id" autofocus>
          </div>
          <div class="modal-footer">
              <div class="row">
                <div class="col-sm-6 text-left">
                <button type="submit" class="btn btn-primary">Report</button>
                </div>
                <div class="col-sm-6 text-right">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
              </div>
          </div>
        </form> 
    </div>
  </div>
</div>

<!-- 2 Salary Closing Report Modal -->
<div class="modal fade" id="salary_closing_report_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="exampleModalLabel">Salary Closing Report </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form class="form-horizontal" id="salary_closing_report_form" target="_blank" action="{{ route('salary.closing.report') }}" method="post">
        @csrf
          <div class="modal-body">
               <div class="form-group row custom_form_group">                       
                <label class="control-label col-md-2">From</label>
                <div class="col-md-4">
                  <input type="date" class="form-control" name="from_date" value="{{date("Y-m-d")}}">
                    {{-- <select class="form-select" name="month" id="month" required>                             
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
                    </select> --}}
                </div>
                <label class="col-sm-2 control-label"> To </label>
                <div class="col-sm-3">
                    <input type="date" class="form-control" name="to_date" value="{{date("Y-m-d")}}">
                    {{-- <select class="form-select" name="year"  id="year">
                        @foreach(range(date('Y'), date('Y')-1) as $y)
                        <option value="{{$y}}" {{$y}}>{{$y}}</option>
                        @endforeach
                    </select> --}}
                </div>
              </div> 
              <div class="form-group row custom_form_group">
                <label class="control-label col-md-2">Report</label>
                  <select name="report_type" class="form-control col-md-10" id="report_type">
                    <option value="1">Employee List</option>
                    <option value="2">Summary Month by Month</option>
                  </select>
              </div> 
            
          </div>
          <div class="modal-footer">
              <div class="row">
                <div class="col-sm-6 text-left">
                <button type="submit" class="btn btn-primary">Report</button>
                </div>
                <div class="col-sm-6 text-right">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
              </div>
          </div>
        </form> 
    </div>
  </div>
</div>


<!-- 3 Salary Hold Employee Salary Report Modal -->
<div class="modal fade" id="salary_hold_report_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center red" id="exampleModalLabel">Salary hold Employees Salary Report </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form class="form-horizontal" id="salary_hold_report_form" target="_blank" action="{{ route('employee.salary.sheet.print_preview.bysalary_type') }}" method="post">
        @csrf
          <div class="modal-body">
              <input type="hidden" name="salary_report_type" name="salary_report_type"  value="1">
              <div class="form-group row custom_form_group">
                  <label class="control-label col-md-2">Project</label>
                  <div class="col-md-10">
                    <select name="project_ids[]" class="selectpicker" id="project_ids[]" multiple>
                      @foreach($projects as $p)
                          <option value="{{$p->proj_id}}">{{$p->proj_name}}</option>
                      @endforeach
                    </select>
                  </div>                  
              </div> 

            <div class="form-group row custom_form_group">                       
                <label class="control-label col-md-2">Month</label>
                <div class="col-md-4">
                    <select class="form-select" name="month" id="month" required>                             
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
                <div class="col-sm-4">
                    <select class="form-select" name="year"  id="year">
                        @foreach(range(date('Y'), date('Y')-2) as $y)
                        <option value="{{$y}}" {{$y}}>{{$y}}</option>
                        @endforeach
                    </select>
                </div>
            </div> 
            <div class="form-group row custom_form_group">
                <label class="control-label col-md-2">Emp. Status</label>
                <div class="col-md-4">
                    <select class="selectpicker" name="job_status[]" id="job_status[]" multiple>
                        <option value="1"> Active </option>
                        <option value="2"> Inactive </option>
                        <option value="3"> Final Exit </option>
                        <option value="4"> Release </option>
                        <option value="5"> Vacation </option>
                        <option value="6"> Runaway </option>
                        <option value="7"> VISA Cancel </option>
                        <option value="8"> Final Exit and Return Back</option>
                    </select>
                </div>
            </div>
            <div class="form-group row custom_form_group">
                <label class="control-label col-md-2">Salary Status</label>
                <div class="col-md-4">
                    <select class="selectpicker" name="salary_status[]" id="salary_status[]" multiple>
                        <option value="1"> Paid</option>
                        <option value="0"> UnPaid</option>
                    </select>
                </div>
            </div>
            
          </div>
          <div class="modal-footer">
              <div class="row">
                <div class="col-sm-6 text-left">
                <button type="submit" class="btn btn-primary">Process</button>
                </div>
                <div class="col-sm-6 text-right">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
              </div>
          </div>
        </form> 
    </div>
  </div>
</div>

<!-- 4 Office Staff Salary Report Modal -->
<div class="modal fade" id="office_staff_salary_report_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center red" id="exampleModalLabel"> Office Staff Employees Salary Report </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form class="form-horizontal" id="office_staff_salary_report_form" target="_blank" action="{{ route('employee.salary.sheet.print_preview.bysalary_type') }}" method="post">
        @csrf
          <div class="modal-body">

               <input type="hidden" name="salary_report_type" name="salary_report_type"  value="2">
              <div class="form-group row custom_form_group">
                  <label class="control-label col-md-2">Project</label>
                  <div class="col-md-10">
                    <select name="project_ids[]" class="selectpicker" id="project_ids[]" multiple>
                      @foreach($projects as $p)
                          <option value="{{$p->proj_id}}">{{$p->proj_name}}</option>
                      @endforeach
                    </select>
                  </div>                  
              </div> 

               <div class="form-group row custom_form_group">                       
                <label class="control-label col-md-2">Month</label>
                <div class="col-md-4">
                    <select class="form-select" name="month" id="month" required>                             
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
                <div class="col-sm-4">
                    <select class="form-select" name="year"  id="year">
                        @foreach(range(date('Y'), date('Y')-2) as $y)
                        <option value="{{$y}}" {{$y}}>{{$y}}</option>
                        @endforeach
                    </select>
                </div>
              </div> 
              
            
          </div>
          <div class="modal-footer">
              <div class="row">
                <div class="col-sm-6 text-left">
                <button type="submit" class="btn btn-primary">Process</button>
                </div>
                <div class="col-sm-6 text-right">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
              </div>
          </div>
        </form> 
    </div>
  </div>
</div>


<!-- 5 Cash Receive From Rashed Vai as Expense by Emp ID Report !--> 
<div class="modal fade" id="expense_by_empid_report_modal" tabindex="-1" role="dialog" aria-labelledby="demoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
         <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Daily Expense By Employee Report </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>
             <div class="modal-body">
                   
                   <form   action="{{ route('company.datebydate.trans.summary.report') }}" target="_blank" onsubmit="" method="POST">
                    @csrf  
                    <input type="hidden" class="form-control" name = "report_type"  value="3" >

                    <div class="form-group row custom_form_group">
                        <label class="col-sm-4 control-label">Employee ID<span class="req_star">*</span> </label>
                        <div class="col-sm-6"> 
                            <input type="number" id="employee_id" name="employee_id" class="form-control" required>
                        </div>
                    </div> 
                    
                    <div class="form-group row custom_form_group">
                        <label class="col-sm-4 control-label">From Date<span class="req_star">*</span> </label>
                        <div class="col-sm-6"> 
                            <input type="date" id="from_date" name="from_date" value="<?= date("Y-m-d") ?>" class="form-control" required>
                        </div>
                    </div> 
                    
                    <div class="form-group row custom_form_group">
                        <label class="col-sm-4 control-label">To Date<span class="req_star">*</span> </label>
                        <div class="col-sm-6"> 
                            <input type="date" id="to_date" name="to_date" value="<?= date("Y-m-d") ?>" class="form-control" required>
                        </div>
                    </div>

                    <button type="submit" id="report_button"   class="btn btn-primary waves-effect"  style="border-radius: 15px; width: 150px; height: 40px; letter-spacing: 1px;">Report</button>
                 </form>    
              </div>
          </div>
    </div>
</div>


<!-- 6 Employee Bonus Report By Emp Id Or Date To Date !-->
<div class="modal fade" id="emp_bonus_report_modal" tabindex="-1" role="dialog" aria-labelledby="demoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
         <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Employee Bonus Report </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>
             <div class="modal-body">


                   <form class="form-horizontal" id="employee_bonus_report_process_form" target="_blank" action="{{ route('employee.bonus.details.report') }}" method="post">
                    @csrf

                    <select name="bonus_report_type" id="bonus_report_type" hidden>
                        <option value="1">Date to Date Report</option>
                    </select>

                    <select name="bonus_type" id="bonus_type" hidden>
                        <option value="1">Bonus Type</option>
                    </select>

                    <div class="form-group row custom_form_group">
                        <label class="col-sm-3 control-label">Employee ID </label>
                        <div class="col-sm-8">
                            <input type="number" id="employee_id" placeholder="Enter Employee ID Number" name="employee_id" class="form-control">
                        </div>
                        <div class="col-sm-1"></div>
                    </div>

                    <div class="form-group row custom_form_group">
                        <label class="col-sm-3 control-label">From Date<span class="req_star">*</span> </label>
                        <div class="col-sm-8">
                            <input type="date" id="from_date" name="from_date" value="<?= date("Y-m-d") ?>" class="form-control">
                        </div>
                        <div class="col-sm-1"></div>
                    </div>

                    <div class="form-group row custom_form_group">
                        <label class="col-sm-3 control-label">To Date<span class="req_star">*</span> </label>
                        <div class="col-sm-8">
                            <input type="date" id="to_date" name="to_date" value="<?= date("Y-m-d") ?>" class="form-control">
                        </div>
                        <div class="col-sm-1"></div>
                    </div>

                    <div class="text-center mt-3">
                        <button id="emp_bonus_process_submit_button" class="btn btn-primary waves-effect"  style="border-radius: 15px; width: 150px; height: 40px; letter-spacing: 1px;">Report</button>
                    </div>
                 </form>
              </div>
          </div>
    </div>
</div>


<!--7 Project-Wise Salary Allocation Report Designation Head Salary Report (Cost Control) -->
<div class="modal fade" id="sponsor_type_salary_report__cost_controll_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            {{-- Project-Wise Salary Allocation Report  --}}
            <div class="row">           
          
                <div class="modal-header" style="background-color: #182F49;">
                    <h5 class="modal-title text-center" style="color: white; text-align:center; padding-left:20px;" > Project-Wise Salary Allocation Report </h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <form class="form-horizontal" id="office_staff_salary_report_form" target="_blank" action="{{ route('salary.report.project.cost.controll') }}" method="post">
                  @csrf
                    <div class="modal-body">
        
                        <input type="hidden" name="salary_report_type" name="salary_report_type"  value="2">
                        <div class="form-group row custom_form_group">
                            <label class="control-label col-md-2">Project</label>
                            <div class="col-md-10">
                                <select class="form-select" class="selectpicker" id="project_ids" name="project_ids" required>
                                @foreach($projects as $p)
                                    <option value="{{$p->proj_id}}">{{$p->proj_name}}</option>
                                @endforeach
                              </select>
                            </div>
                        </div>
        
                        <div class="form-group row custom_form_group">
                            <label class="control-label col-md-2">Sponsor</label>
                            <div class="col-md-10">
                                <select class="selectpicker" name="sponsor_ids[]" multiple>
                                    <option value="Asloob">Asloob</option>
                                    <option value="Subcon">Others</option>
                                 </select>
                            </div>
                        </div>
        
                        {{-- <div class="form-group row custom_form_group">
                            <label class="control-label col-md-2">Des Head</label>
                            <div class="col-md-10">
                                <select class="selectpicker" name="designation_head_ids[]" multiple required>
                                    @foreach($designation_heads as $dh)
                                   <option value="{{ $dh->dh_auto_id }}">{{ $dh->des_head_name }}</option>
                                   @endforeach
                                 </select>
                            </div>
                        </div> --}}
        
                          <div class="form-group row custom_form_group">
                            <label class="control-label col-md-2">Emp. Type</label>
                            <div class="col-md-10">
                                <select class="form-select" name="emp_type" id="emp_type" required>
                                    <option value="">Select Employee Type</option>
                                    @foreach($emp_types as $emp)
                                    <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
        
        
        
                        <div class="form-group row custom_form_group">
                            <label class="control-label col-md-2">Trade</label>
                            <div class="col-md-10">
                                <select class="selectpicker" name="designation_ids[]" id="designation_ids" multiple>
                                    {{-- <option value="">Select Designation</option> --}}
                                    {{-- @foreach($trades as $td)
                                    <option value="{{ $td->catg_id }}">{{ $td->catg_name }}</option>
                                    @endforeach --}}
                                </select>
        
        
                            </div>
                        </div>
        
        
        
                         <div class="form-group row custom_form_group">
                            <label class="control-label col-md-2">Month</label>
                            <div class="col-md-4">
                                <select class="form-select" name="month" id="month" required>
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
                            <div class="col-sm-4">
                                <select class="form-select" name="year"  id="year">
                                    @foreach(range(date('Y'), date('Y')-4) as $y)
                                    <option value="{{$y}}" {{$y}}>{{$y}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
        
                        <div class="form-group row custom_form_group">
                            <label class="control-label col-md-2">Report Type</label>
                            <div class="col-md-10">
                                <select class="form-select" name="report_type" required>
        
                                    @can('report_salary_emp_details_designation_head')
                                    <option value="0">Salary Details</option>
                                    @endcan
                                    @can('report_salary_summary_designation_head')
                                    <option value="1">Salary Summary</option>
                                    @endcan
        
                                </select>
                            </div>
                        </div>
                        <div class="row">
                              <div class="col-sm-6 text-left">
                              <button type="submit" class="btn btn-primary">Process</button>
                              </div>
                              <div class="col-sm-6 text-right">
                              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                              </div>
                        </div>
        
                    </div>
                  
                </form>
            </div>

            {{-- Designation Head Salary Allocation report  --}}
            <div class="row">
                <div class="modal-header" style="background-color: #182F49;">
                    <h5 class="modal-title text-center" style="color: white; text-align:center; padding-left:20px;" > Designation Head Salary Allocation </h5>
                </div>
                <form class="form-horizontal" id="office_staff_salary_report_form" target="_blank" action="{{ route('salary.report.project.cost.controll') }}" method="post">
                @csrf
                    <div class="modal-body">
                        {{-- <input type="hidden" name="salary_report_type" name="salary_report_type"  value="3"> --}}
                        <div class="form-group row custom_form_group">
                            <label class="control-label col-md-2">Project</label>
                            <div class="col-md-10">
                                <select class="form-select" class="selectpicker" id="project_ids" name="project_ids" required>
                                @foreach($projects as $p)
                                    <option value="{{$p->proj_id}}">{{$p->proj_name}}</option>
                                @endforeach
                            </select>
                            </div>
                        </div>

                        <div class="form-group row custom_form_group">
                            <label class="control-label col-md-2">Sponsor</label>
                            <div class="col-md-10">
                                <select class="selectpicker" name="sponsor_ids[]" multiple>
                                    @foreach($sponsors as $spons)
                                <option value="{{ $spons->spons_id }}">{{ $spons->spons_name }}</option>
                                @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row custom_form_group">
                            <label class="control-label col-md-2">Designation</label>
                            <div class="col-md-10">
                                <select class="selectpicker" name="designation_head_ids[]" multiple required>
                                    @foreach($designation_heads as $dh)
                                <option value="{{ $dh->dh_auto_id }}">{{ $dh->des_head_name }}</option>
                                @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row custom_form_group">
                            <label class="control-label col-md-2">Month</label>
                            <div class="col-md-4">
                                <select class="form-select" name="month" id="month" required>
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
                            <div class="col-sm-4">
                                <select class="form-select" name="year"  id="year">
                                    @foreach(range(date('Y'), date('Y')-2) as $y)
                                    <option value="{{$y}}" {{$y}}>{{$y}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row custom_form_group">
                            <label class="control-label col-md-2">Report Type</label>
                            <div class="col-md-10">
                                <select class="form-select" name="report_type" required>

                                    {{-- @can('report_salary_emp_details_designation_head')
                                        <option value="2">Salary Details</option>
                                    @endcan --}}
                                    @can('report_salary_summary_designation_head')
                                        <option value="3">Salary Summary</option>
                                    @endcan

                                </select>
                            </div>
                        </div>
                        <div class="row">
                              <div class="col-sm-6 text-left">
                              <button type="submit" class="btn btn-primary">Process</button>
                              </div>
                              <div class="col-sm-6 text-right">
                              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                              </div>
                        </div>
                    </div>
                </form>
            </div>
            
              <br><br>
                {{-- Only Project Base Salary Summary for cost control --}}
            <div class="row">
                <div class="modal-header" style="background-color: #182F49;">
                    <h5 class="modal-title text-center" style="color: white; text-align:center; padding-left:20px;" > Only Project Base Salary Summary </h5>
                </div>
                <form class="form-horizontal" id="office_staff_salary_report_form" target="_blank" action="{{ route('salary.report.project.cost.controll') }}" method="post">
                @csrf
                    <div class="modal-body">
                        {{-- <input type="hidden" name="salary_report_type" name="salary_report_type"  value="3"> --}}
                        <div class="form-group row custom_form_group">
                            <label class="control-label col-md-2">Project</label>
                            <div class="col-md-10">
                                <select   class="selectpicker" id="project_ids[]" name="project_ids[]" multiple>
                                @foreach($projects as $p)
                                    <option value="{{$p->proj_id}}">{{$p->proj_name}}</option>
                                @endforeach
                            </select>
                            </div>
                        </div>

                        <div class="form-group row custom_form_group">
                            <label class="control-label col-md-2">Month</label>
                            <div class="col-md-4">
                                <select class="form-select" name="month" id="month" required>
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
                            <div class="col-sm-4">
                                <select class="form-select" name="year"  id="year">
                                    @foreach(range(date('Y'), date('Y')-2) as $y)
                                    <option value="{{$y}}" {{$y}}>{{$y}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row custom_form_group">
                            <label class="control-label col-md-2">Report Type</label>
                            <div class="col-md-10">
                                <select class="form-select" name="report_type" required>
                                    @can('report_salary_summary_designation_head')
                                        <option value="4">Salary Summary</option>
                                    @endcan
                                    {{-- <option value="5">Salary Details</option> --}}
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6 text-left">
                        <button type="submit" class="btn btn-primary">Process</button>
                        </div>
                        <div class="col-sm-6 text-right">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </form>
            </div>
            <hr>
            <br><br>
            {{-- WORKING HOURS SUMMARY REPORT work record section a report --}}
             <div class="row">
                <div class="modal-header" style="background-color: #182F49;">
                    <h5 class="modal-title text-center" style="color: white; text-align:center; padding-left:20px;" > Man-Hours Month by Month Report </h5>
                </div>
                <form class="form-horizontal" id="hours_statment_report_form" target="_blank" action="{{ route('project-wise-employe.month-work.record.report') }}" method="post">
                @csrf
                    <div class="modal-body">
                        <div class="card-body card_form" style="padding-top: 0;">
                            <div class="form-group row custom_form_group">
                                    <label class="col-sm-3 control-label">  Project:</label>
                                    <div class="col-sm-9">
                                    <select class="selectpicker" name="project_ids[]" multiple>
                                    {{-- <option value="0">All</option> --}}
                                    @foreach($projects as $proj)
                                    <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                                    @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row custom_form_group">
                                <label class="col-sm-3 control-label"  >From:</label>
                                <div class="col-sm-5">
                                    <input type="date" class="form-control" name='from_date' id="from_date" value="{{ date("Y-m-d") }}">
                                </div>
                            </div>
                            <div class="form-group row custom_form_group">
                                <label class="col-sm-3 control-label"  >To:</label>
                                <div class="col-sm-5">
                                    <input type="date" class="form-control" name='to_date' id="to_date" value="{{ date("Y-m-d") }}">
                                </div>
                            </div>
                            <div class="form-group row custom_form_group">
                                <label class="col-sm-3 control-label" >Report Type:</label>
                                <div class="col-sm-9">
                                    <select class="form-select" name="data_source" required>
                                    <option value="5">Work Hours Summary</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6 text-left">
                        <button type="submit" class="btn btn-primary">Process</button>
                        </div>
                        <div class="col-sm-6 text-right">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>

                </form>
            </div>
       </div> {{-- end modal-content --}}
    </div>
</div>


<!--8 Single Project a Sponsor Multiple MOnth Salary Summary Report -->
<div class="modal fade" id="aproject_current_working_emp_salary_report" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
             <h4 class="modal-title text-center" id="sponsorReportModal" > <span style="color:red"> Sub-contractor & Asloob Sponsor Salary Reports</span>  </h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form class="form-horizontal" id="current_working_emp_salary_report_form" target="_blank" action="{{ route('salary.report.aproject.sponsor.multi.month') }}" method="post">
          @csrf
            <div class="modal-body">

                <input type="hidden" name="salary_report_type" name="salary_report_type"  value="2">
                <div class="form-group row custom_form_group">
                    <label class="control-label col-md-2">Project</label>
                    <div class="col-md-10">
                        <select class="selectpicker" id="project_ids[]" name="project_ids[]" multiple>
                        @foreach($projects as $p)
                            <option value="{{$p->proj_id}}">{{$p->proj_name}}</option>
                        @endforeach
                      </select>
                    </div>
                </div>

                <div class="form-group row custom_form_group">
                    <label class="control-label col-md-2">Sponsor</label>
                    <div class="col-md-10">
                        <select class="selectpicker" name="sponsor_ids[]" multiple>
                            @foreach($sponsors as $spons)
                           <option value="{{ $spons->spons_id }}">{{ $spons->spons_name }}</option>
                           @endforeach
                         </select>
                    </div>
                </div>

                 <div class="form-group row custom_form_group">
                    <label class="control-label col-md-2">Month</label>
                    <div class="col-md-4">
                        <select class="selectpicker" name="month[]"  multiple required>
                            <option value="1"  > January</option>
                            <option value="2" > February</option>
                            <option value="3" > March</option>
                            <option value="4"  > April</option>
                            <option value="5"  > May</option>
                            <option value="6"  > June</option>
                            <option value="7"  > July</option>
                            <option value="8"  > Auguest</option>
                            <option value="9"  > September</option>
                            <option value="10"  > October</option>
                            <option value="11"  > November</option>
                            <option value="12"  > December</option>
                        </select>
                    </div>
                    <label class="col-sm-3 control-label"> Year </label>
                    <div class="col-sm-3">
                        <select class="form-select" name="year"  id="year">
                            @foreach(range(date('Y'), date('Y')-2) as $y)
                            <option value="{{$y}}" {{$y}}>{{$y}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group row custom_form_group">
                    <label class="control-label col-md-2">Report Type</label>
                    <div class="col-md-10">
                        <select class="form-select" name="report_type" required>
                            {{-- <option value="1">Salary Details</option> --}}
                            <option value="2">Salary Summary</option>
                            <option value="1">Singe Month with Multi-Sponsors</option>
                            @can('report_salary_emp_details_designation_head')

                            @endcan
                            @can('report_salary_summary_designation_head')
                            @endcan
                            
                            @can('report-subcon-asloob-unpaid-salary-summary')
                                <option value="3">Subcont & Asloob Sponsor Unpaid Salary</option>
                                <option value="4">Subcont & Asloob Paid/Unpaid Salary</option>
                            @endcan
                             <option value="5">A Project Subcontractor Actual  Salary</option>

                        </select>
                    </div>
                </div>



            </div>
            <div class="modal-footer">
                <div class="row">
                  <div class="col-sm-6 text-left">
                  <button type="submit" class="btn btn-primary">Process</button>
                  </div>
                  <div class="col-sm-6 text-right">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  </div>
                </div>
            </div>
        </form>
      </div>
    </div>
</div>




<!--9 salary_sheet_preview_base_on_emp_type Reports -->
<div class="modal fade" id="aproject_salary_sheet_preview" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title text-center" > <span style="color:red"> Salary Sheet Preview</span>  </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">

                <!-- 2 Project & Employee Status Wise Salary Report -->
                <div class="row">
                    <div class="col-md-12">
                        <form class="form-horizontal" id="registration" target="_blank" action="{{ route('project-month-empStatus.wise-salary') }}" method="post">
                            @csrf

                            <h5 style="color: red;text-align:center;">2 >> At Present Working Employees Salary Report</h5>

                                {{-- Project List --}}
                                <div class="form-group row custom_form_group">
                                    <label class="control-label col-md-2"  >Project</label>
                                    <div class="col-md-10">
                                        <select class="form-select" name="proj_id" required>
                                        @foreach($projects as $proj)
                                        <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                                        @endforeach
                                        </select>
                                    </div>
                                </div>

                                {{-- Month Year List --}}
                                <div class="form-group row custom_form_group{{ $errors->has('month') ? ' has-error' : '' }}">
                                    <label class="control-label col-md-2" >Month</label>
                                    <div class="col-md-4">
                                        <select class="form-select" name="month">
                                        {{-- @foreach($months as $mn)
                                        <option value="5" {{ $mn == $current_month ? 'selected' : "" }}>{{ $mn }}</option>
                                        @endforeach --}}
                                        <option value="1"> January</option>
                                        <option value="2" > February</option>
                                        <option value="3" > March</option>
                                        <option value="4"  > April</option>
                                        <option value="5"  > May</option>
                                        <option value="6"  > June</option>
                                        <option value="7"  > July</option>
                                        <option value="8"  > Auguest</option>
                                        <option value="9"  > September</option>
                                        <option value="10"  > October</option>
                                        <option value="11"  > November</option>
                                        <option value="12"  > December</option>
                                        </select>

                                    </div>

                                    <label class="control-label col-md-2"  >Year</label>
                                    <div class="col-md-4">
                                        <select class="form-select" name="year">
                                        @foreach(range(date('Y'), date('Y')-2) as $y)
                                        <option value="{{$y}}" {{$y}}>{{$y}}</option>
                                        @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row custom_form_group">
                                    <label class="control-label col-md-3" >Salary Status</label>
                                    <div class="col-md-9">
                                        <select class="form-select" name="salary_status">
                                            <option value="">All</option>
                                            <option value="0">Unpaid</option>
                                            <option value="1">Paid</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row custom_form_group">
                                    <label class="control-label col-md-3">Report Type</label>
                                    <div class="col-md-9">
                                        <select class="form-select" name="salary_report_type" required>
                                            <option value="3">Projectwise Paid/Unpaid</option>
                                            <option value="1">Employees now working</option>
                                            <option value="6">Basic Employees now working</option>
                                            <option value="7">Hourly Employees now working</option>
                                            <option value="2">Employee Last Salary</option>
                                            <option value="4">1 or More Salary Unpaid Employees</option>
                                            <option value="5">2 or More Salary Unpaid Employees</option>
                                        </select>
                                    </div>
                                </div>
                            <button type="submit" class="btn btn-primary waves-effect">Process </button>
                        </form>
                    </div>
                </div>
                <hr>
                <!-- 3 Project & Employee Status Wise Salary Report -->
                <div class="row">
                    <div class="col-md-12">
                        <form class="form-horizontal" id="salary_report" target="_blank" action="{{ route('project-month-empType.wise-salary') }}" method="post">
                            @csrf
                            <h5 style="color: red;text-align:center;">3 >> Salary Report by Employee Type</h5>

                            <div class="form-group row custom_form_group">
                                <label class="control-label col-md-2">Project</label>
                                <div class="col-md-10">
                                    <select class="form-select" id="proj_id" name="proj_id" >
                                        <option value="">All Projects</option>
                                    @foreach($projects as $p)
                                        <option value="{{$p->proj_id}}">{{$p->proj_name}}</option>
                                    @endforeach
                                </select>
                                </div>
                            </div>

                            <div class="form-group row custom_form_group">
                                <label class="control-label col-sm-2" > Emp. Type:</label>
                                <div  class="col-sm-10">
                                    {{-- Employee type List 1 = direct, 2 = indirect --}}
                                    <select class="form-select" name="emp_type_id" required>
                                        <option value="0">All Employee</option>
                                        <option value="-1">Direct Employee (Basic Salary)</option>
                                        <option value="1">Direct Employee (Hourly)</option>
                                        <option value="2">Indirect Employee</option>
                                        <option value="3">Basic Salary (Direct & Indirect) Employees</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row custom_form_group">
                                <label class="control-label col-md-2">Month</label>
                                <div class="col-md-4">
                                    <select class="form-select" name="month"  required>
                                        <option value="1"> January</option>
                                        <option value="2" > February</option>
                                        <option value="3" > March</option>
                                        <option value="4"  > April</option>
                                        <option value="5"  > May</option>
                                        <option value="6"  > June</option>
                                        <option value="7"  > July</option>
                                        <option value="8"  > Auguest</option>
                                        <option value="9"  > September</option>
                                        <option value="10"  > October</option>
                                        <option value="11"  > November</option>
                                        <option value="12"  > December</option>
                                    </select>
                                </div>
                                <label class="col-sm-3 control-label"> Year </label>
                                <div class="col-sm-3">
                                    <select class="form-select" name="year"  id="year">
                                        @foreach(range(date('Y'), date('Y')-2) as $y)
                                        <option value="{{$y}}" {{$y}}>{{$y}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row custom_form_group">
                                <label class="control-label col-sm-2"  >Status</label>
                                <div class="col-sm-4">
                                <select class="selectpicker" name="salary_status[]" multiple required>
                                    <option value="0">Salary Unpaid</option>
                                    <option value="1">Salary Paid</option>
                                </select>
                                </div>
                            </div>
                            <br>
                            <div class="form-group row custom_form_group">
                                <div class="col-md-6">
                                    <input type="checkbox" id="projec_cost_check" name="projec_cost_check" value="1">
                                    <label for="projec_cost_check">Only Selected Project</label><br>

                                </div>
                                <div class="col-md-6">
                                    <input type="checkbox" id="paid_unpaid_show" name="paid_unpaid_show" value="1">
                                    <label for="paid_unpaid_show">Salary Status show in Payslip</label><br>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6 text-left">
                                <button type="submit" class="btn btn-primary">Process</button>
                                </div>
                                <div class="col-sm-6 text-right">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <hr>
                <!-- 12 Salary Paid By Bank Report -->
                <div class="row">
                    <div class="col-md-12">
                        <form class="form-horizontal" id="bank_paid_salary_report_form" target="_blank" action="{{ route('salary.report.paid.by.bank') }}" method="post">
                            @csrf
                            <h5 style="color: red;text-align:center;">Salary Paid by Bank Report</h5>
                            {{-- Project List --}}
                            <div class="form-group row custom_form_group">
                                <label class="control-label col-md-2"  >Project:</label>
                                <div class="col-md-10">
                                    <select class="selectpicker" name="project_id_list[]" multiple>
                                    @foreach($projects as $proj)
                                    <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                                    @endforeach
                                    </select>
                                </div>
                            </div>
                            {{-- Month Year List --}}
                            <div class="form-group row custom_form_group{{ $errors->has('month') ? ' has-error' : '' }}">
                                <label class="control-label col-md-2" >Salary Month:</label>
                                <div class="col-md-10">
                                    <select class="form-select" name="month">
                                        <option value="1"> January</option>
                                        <option value="2" > February</option>
                                        <option value="3" > March</option>
                                        <option value="4"  > April</option>
                                        <option value="5"  > May</option>
                                        <option value="6"  > June</option>
                                        <option value="7"  > July</option>
                                        <option value="8"  > Auguest</option>
                                        <option value="9"  > September</option>
                                        <option value="10"  > October</option>
                                        <option value="11"  > November</option>
                                        <option value="12"  > December</option>
                                    </select>
                                </div>

                                <label class="control-label col-md-2"  >Year:</label>
                                <div class="col-md-10">
                                    <select class="form-select" name="year">
                                    @foreach(range(date('Y'), date('Y')-2) as $y)
                                        <option value="{{$y}}" {{$y}}>{{$y}}</option>
                                    @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6 text-left">
                                    <button type="submit" class="btn btn-primary">Process</button>
                                </div>
                                <div class="col-sm-6 text-right">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                
            </div> {{-- modal body end --}}
        </div> {{-- modal content end --}}
    </div> {{-- modal document end --}}
</div> {{-- modal end --}}


<!-- 10 Project wise Basic Empl and Hourly Emp Salary Summary -->
<div class="modal fade" id="salary_summary_related_report_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title text-center" > <span style="color:red"> Basic & Hourly Employees Salary Summary</span>  </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <!-- 2 Project & Employee Status Wise Salary Report -->
                <div class="row">
                    <div class="col-md-12">
                        <form class="form-horizontal" id="registration" target="_blank" action="{{ route('project-wise.basic.hourly.emp.salary.sumamry') }}" method="post">
                              @csrf
                            {{-- Project List --}}
                            <div class="form-group row custom_form_group">
                                            <label class="control-label col-md-3">Project</label>
                                            <div class="col-md-6">
                                            <select class="selectpicker" name="proj_id[]" multiple>
                                                    <option value="" >All Project</option>
                                                    @foreach($projects as $proj)
                                                    <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                            </div>
                            {{-- From Date --}}
                            <div class="form-group row custom_form_group">
                                <label class="control-label col-md-3">From</label>
                                <div class="col-sm-6">
                                <input type="date" name="from_date" value="<?= date("Y-m-d") ?>" class="form-control">
                                </div>
                            </div>

                            {{-- To Date --}}
                            <div class="form-group row custom_form_group">
                                <label class="control-label col-md-3"  >To</label>
                                <div class="col-sm-6">
                                <input type="date" name="to_date" value="<?= date("Y-m-d") ?>" class="form-control">
                                </div>
                            </div>

                            <div class="form-group row custom_form_group">
                                <label class="control-label col-md-3"  >Report Type</label>
                                <div class="col-sm-6">
                                <select class="form-select" name="report_type" >
                                    <option value="1">Monthly Salary Statement</option>
                                    <option value="2">Basic & Hourly Salary Summary</option>
                                    <!--<option value="3">Multi-Project Month by Month</option> in processing page it transfered in report no 8 -->
                                    <option value="4">Staff & Direct Emp. Salary Summary with Catering</option>
                                    <option value="5">Staff & Direct Emp. Salary Summary without Catering</option>
                                    <option value="6">Only Project Salary Expence</option>
                                    <option value="7">Monthly Unpaid Salary Summary</option>
                                </select>
                                </div>
                            </div>
                            <div class="form-group row custom_form_group">
                                <label class="control-label col-md-3" hidden >Report Format</label>
                                <div class="col-sm-6">
                                <select class="form-select" hidden name="report_format" >
                                    <option selected value="1">Pdf</option>
                                    <option value="2">Excell</option>
                                </select>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary waves-effect">Process</button>
                        </form>
                    </div>
                </div>
                <hr>
            </div> {{-- modal body end --}}
        </div> {{-- modal content end --}}
    </div> {{-- modal document end --}}
</div> {{-- modal end --}}



<!--11 Designation Head Salary Report, combine with report 7  -->
<div class="modal fade" id="designation_head_salary_report__cost_controll_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title text-center red" id="exampleModalLabel"> Designation Head Salary Report (Cost Control) </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form class="form-horizontal" id="office_staff_salary_report_form" target="_blank" action="{{ route('salary.report.project.cost.controll') }}" method="post">
          @csrf
            <div class="modal-body">

                {{-- <input type="hidden" name="salary_report_type" name="salary_report_type"  value="3"> --}}
                <div class="form-group row custom_form_group">
                    <label class="control-label col-md-2">Project</label>
                    <div class="col-md-10">
                        <select class="form-select" class="selectpicker" id="project_ids" name="project_ids" required>
                        @foreach($projects as $p)
                            <option value="{{$p->proj_id}}">{{$p->proj_name}}</option>
                        @endforeach
                      </select>
                    </div>
                </div>

                <div class="form-group row custom_form_group">
                    <label class="control-label col-md-2">Sponsor</label>
                    <div class="col-md-10">
                        <select class="selectpicker" name="sponsor_ids[]" multiple>
                            @foreach($sponsors as $spons)
                           <option value="{{ $spons->spons_id }}">{{ $spons->spons_name }}</option>
                           @endforeach
                         </select>
                    </div>
                </div>

                <div class="form-group row custom_form_group">
                    <label class="control-label col-md-2">Designation</label>
                    <div class="col-md-10">
                        <select class="selectpicker" name="designation_head_ids[]" multiple required>
                            @foreach($designation_heads as $dh)
                           <option value="{{ $dh->dh_auto_id }}">{{ $dh->des_head_name }}</option>
                           @endforeach
                         </select>
                    </div>
                </div>

                 <div class="form-group row custom_form_group">
                    <label class="control-label col-md-2">Month</label>
                    <div class="col-md-4">
                        <select class="form-select" name="month" id="month" required>
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
                    <div class="col-sm-4">
                        <select class="form-select" name="year"  id="year">
                            @foreach(range(date('Y'), date('Y')-2) as $y)
                            <option value="{{$y}}" {{$y}}>{{$y}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group row custom_form_group">
                    <label class="control-label col-md-2">Report Type</label>
                    <div class="col-md-10">
                        <select class="form-select" name="report_type" required>

                            @can('report_salary_emp_details_designation_head')
                            <option value="2">Salary Details</option>
                            @endcan
                            @can('report_salary_summary_designation_head')
                            <option value="3">Salary Summary</option>
                            @endcan

                        </select>
                    </div>
                </div>



            </div>
            <div class="modal-footer">
                <div class="row">
                  <div class="col-sm-6 text-left">
                  <button type="submit" class="btn btn-primary">Process</button>
                  </div>
                  <div class="col-sm-6 text-right">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  </div>
                </div>
            </div>
          </form>
      </div>
    </div>
</div>



<div class="overlay"></div>
<!-- added thes for Multiple Selection dropdownlist  -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta2/css/bootstrap-select.min.css" integrity="sha512-mR/b5Y7FRsKqrYZou7uysnOdCIJib/7r5QeJMFvLNHNhtye3xJp1TdJVPLtetkukFn227nKpXD9OjUc09lx97Q==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta2/js/bootstrap-select.min.js" integrity="sha512-FHZVRMUW9FsXobt+ONiix6Z0tIkxvQfxtCSirkKc5Sb4TKHmqq1dZa8DphF0XqKb3ldLu/wgMa8mT6uXiLlRlw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
 
 
 
<script>

      /* call employee category */
      $('select[name="emp_type"]').on('change', function() {
        var emp_type_id = $(this).val();


        if (emp_type_id) {
          $.ajax({
            url: "{{  url('/admin/employee/category/ajax') }}/" + emp_type_id,
            type: "GET",
            dataType: "json",
            success: function(data) {
                    $('#designation_ids').empty(); // Clear existing options
                
               $('#designation_ids').selectpicker('refresh'); // Refresh the selectpicker to show the new
               
                const select = document.getElementById('designation_ids');
              $.each(data, function(key, value) {
                  const option = document.createElement('option');
                    option.value = value.catg_id;
                    option.textContent = value.catg_name;
                    select.appendChild(option);
               });
               document.getElementById('designation_ids').value = ''; // Clear the selection
               $('#designation_ids').selectpicker('refresh'); // Refresh the selectpicker to show the new
                // options

            },

          });
        } else {
          alert('Please select a valid employee type');
        }


      });


</script>

@endsection
