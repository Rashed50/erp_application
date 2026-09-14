@extends('layouts.admin-master')
@section('title') Emp IN&Out Report @endsection
@section('content')

<div class="row bread_part">
  <div class="col-sm-12 bread_col">
    <h4 class="pull-left page-title bread_title">Employee Attendance Report</h4>
    <ol class="breadcrumb pull-right">
      <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
      <li class="active">Attendance Report</li>
    </ol>
  </div>
</div>



<!-- 1 WPS EMPLOYEE WORK RECORD REPORT, same route for below emplyee id base report !-->
<div class="row">
    <div class="col-md-1"></div>
    <div class="col-md-10" >
      <form class="form-horizontal" id="wps_attence_reoprt_section" target="_blank" action="{{ route('employee-attendance-single-empl-attendance-report') }}" method="post">
        @csrf
        <div class="card">
            <h3 class="card-title card_top_title salary-generat-heading col-sm-8" >1 WPS Employees Attentance Report<h3>
          <div class="card-body card_form">

                <div class="form-group row custom_form_group">
                      <label class="control-label col-md-3">Employee IDs:</label>
                      <div class="col-md-9">
                        <input type="text" class="form-control typeahead" placeholder="Input Multiple Employee ID" name="employee_id" value="{{ old('employee_id') }}">
                        <span class="error d-none" id="error_massage"></span>
                      </div>
                </div>

                <div class="form-group row custom_form_group">
                    <label class="col-md-3 control-label">Project<span class="req_star">*</span></label>
                    <div class="col-md-9">
                        <select class="selectpicker" name="project_ids[]" multiple required>
                            <option value="">Select Project</option>
                            @foreach($project as $item)
                            <option value="{{ $item->proj_id }}">{{ $item->proj_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group row custom_form_group">
                  <label class="col-sm-3 control-label">Month:<span class="req_star">*</span></label>
                  <div class="col-sm-4">
                    <select class="form-select" name="month_id">
                       @foreach($month as $item)
                      <option value="{{ $item->month_id }}" {{ $item->month_id == Carbon\Carbon::now()->format('m') ? 'selected' :'' }}>{{ $item->month_name }}</option>
                      @endforeach
                    </select>
                  </div>
                  <label class="col-md-2 control-label">Year:<span class="req_star">*</span></label>
                  <div class="col-md-3">
                    <select class="form-select" name="year_id" required>
                      @foreach(range(date('Y'), date('Y')-2) as $y)
                      <option value="{{$y}}" {{$y}}>{{$y}}</option>
                      @endforeach
                    </select>
                  </div>

                </div>
                <div class="form-group row custom_form_group">
                    <label class="col-md-3 control-label">Report Type:<span class="req_star">*</span></label>
                    <div class="col-md-4">
                        <select class="form-select"   name="report_type" id="report_type">
                            <option value="5">Today Present Employee List</option>
                            <option value="4">Overtime Sheet Print</option> <!-- 1,2,3 are in below section report no 1.1  -->
                            <option value="6">Monthly Summary</option>

                        </select>
                    </div>
                    <label class="col-md-2 control-label"> Format:<span class="req_star">*</span></label>
                    <div class="col-md-3">
                        <select class="form-select"  name="report_format" id="report_format">
                            <option value="1">PDF Preview</option>
                            <option value="2">Excel Download</option>
                        </select>
                    </div>
                </div>

                <div class="form-group row custom_form_group">
                    <label class="col-md-3 control-label text-right">Working Date:<span class="req_star">*</span></label>
                    <div id="div_working_date" class="col-md-4">
                            <input type="date" class="form-control" id="working_date" name="working_date" value="{{date('Y-m-d')}}" />
                    </div>
                    <label class="col-md-2 control-label text-right">Shift:<span class="req_star">*</span></label>
                    <div class="col-md-3">
                         <select  class="form-select"  name="working_shift" id="working_shift">
                            <option value="0">Day Shift</option>
                            <option value="1">Night Shift</option>
                            {{-- <option value="2">Both Shift</option> --}}
                         </select>
                    </div>
                </div>

                 <div class="form-group row custom_form_group">
                     <div id="div_working_date" class="col-md-3">
                     </div>

                    <div class="col-md-6">
                        <button type="submit" class="btn btn-primary waves-effect">PROCESS</button>
                    </div>
                </div>

          </div>
        </div>
      </form>
    </div>
    <div class="col-md-1"> </div>
</div>


<!-- 1.1 Single Employee Attendence Report   projects/information/report !-->
<div class="row">
  <div class="col-md-1"></div>
  <div class="col-md-10">
    <form class="form-horizontal" id="employeeInOutReport" target="_blank" action="{{ route('employee-attendance-single-empl-attendance-report') }}" method="post">
      @csrf
      <div class="card">
         <h3 class="card-title card_top_title salary-generat-heading col-sm-8" >1.1-Multiple Employee Monthly Attendance Details <h3>

        <div class="card-body card_form">

                <div class="form-group row custom_form_group">
                    <label class="control-label col-md-2">Employee ID:</label>
                    <div class="col-md-10">
                      <input type="text" class="form-control typeahead" placeholder="Input Emp.ID Separate by Comma(,)" name="employee_id" value="{{ old('employee_id') }}" autofocus>
                      <span class="error d-none" id="error_massage"></span>
                    </div>
                </div>
              <div class="form-group row custom_form_group">
                  <label class="col-sm-2 control-label">Month:<span class="req_star">*</span></label>
                  <div class="col-sm-4">
                    <select class="form-select" name="month_id">
                      {{-- <option value="">All</option> --}}
                      @foreach($month as $item)
                      <option value="{{ $item->month_id }}" {{ $item->month_id == Carbon\Carbon::now()->format('m') ? 'selected' :'' }}>{{ $item->month_name }}</option>
                      @endforeach
                    </select>
                  </div>
                  <label class="col-sm-2 control-label">Year:<span class="req_star">*</span></label>
                  <div class="col-sm-4">
                    <select class="form-select" name="year_id" required>
                      @foreach(range(date('Y'), date('Y')-1) as $y)
                      <option value="{{$y}}" {{$y}}>{{$y}}</option>
                      @endforeach
                    </select>
                  </div>

                </div>
                <div class="form-group row custom_form_group">
                  <label class="col-sm-2 control-label">Report Type:<span class="req_star">*</span></label>
                  <div class="col-sm-4">
                      <select class="form-select"  name="report_type" id="report_type">
                        <!--<option value="4">Overtime Sheet Print</option>-->
                        <option value="1">Single Month Report</option>
                        <option value="2">An Employee All Records</option>
                        <option value="3">An Employee Attn Details</option>
                        <!--<option value="7">Today Present Employee List</option>-->
                        <!--5, 6 are in above report no 1  -->

                      </select>
                  </div>
                  <label class="col-sm-2 control-label">Working Date:<span class="req_star">*</span></label>
                  <div id="div_working_date" class="col-sm-3">
                        <input type="date" class="form-control" id="working_date" name="working_date" value="{{date('Y-m-d')}}" />
                  </div>
                </div>
                <div class="form-group row custom_form_group">
                     <div class="col-sm-4">
                   </div>
                   <div class="col-sm-6">
                        <button type="submit" class="btn btn-primary waves-effect">PROCESS</button>
                  </div>
                </div>
         </div>
      </div>
    </form>
  </div>
  <div class="col-md-1"></div>
</div>



<!-- 2 Employee Monthly details Attendence Report !-->
<div class="row">
  <div class="col-md-1"></div>
  <div class="col-md-10">
    <form class="form-horizontal" id="employeeInOutReport" target="_blank" action="{{ route('employee-entry-out-report-process') }}" method="post">
      @csrf
      <div class="card">
        <h3 class="card-title card_top_title salary-generat-heading">2-Monthly(Date by Date) Attendance Details  <h3>
        <div class="card-body card_form">
              <input type="number" id="page_offset" name="page_offset" hidden value="0">
              <div class="form-group row custom_form_group">
                <label class="col-sm-3 control-label">Project Name<span class="req_star">*</span></label>
                <div class="col-sm-5">
                  <select class="form-select" name="project_id" required>
                    <option value="">Select Project</option>
                    @foreach($project as $item)
                    <option value="{{ $item->proj_id }}">{{ $item->proj_name }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-3"><a href="#" onclick="showProjectListReport()"> Projects Color Code</a> </div>
              </div>
              <div class="form-group row custom_form_group">
                <label class="col-sm-3 control-label"> Sponsor:<span class="req_star">*</span></label>
                <div class="col-sm-5">
                  <select class="form-select" name="sponserId">
                    <option value="">All Sponsor</option>
                    @foreach($sponser as $item)
                    <option value="{{ $item->spons_id }}">{{ $item->spons_name }}</option>
                    @endforeach
                  </select>
                </div>

              </div>

              <div class="form-group row custom_form_group">
                        <label class="col-sm-3 control-label">Day 1 To Selected Date::</label>
                        <div class="col-sm-5">
                            <input type="date" class="form-control" name="date" value="{{date('Y-m-d')}}" />
                        </div>
              </div>

              <div class="form-group row custom_form_group">
                        <label class="col-sm-3 control-label">Working Shift</label>
                        <div class="col-sm-5">
                                <select class="form-select"  name="working_shift" >
                                  <option value="0">Day Shift</option>
                                  <option value="1">Night Shift</option>
                                  <option value="">Both Shift</option>
                                </select>
                        </div>
              </div>
              <div class="form-group row custom_form_group">
                <label class="col-sm-3 control-label">Data Range</label>
                <div class="col-sm-3">
                        <select class="form-select"  name="page_offset" >
                          <option value="0">1 - 500</option>
                          <option value="500">501 - 1000</option>
                        </select>
                </div>
                <div class="col-sm-4">
                  <input type="checkbox" id="monthly_summary" name="monthly_summary" value="1">
                  <label class="control-label"> Monthly Summary</label><br>
                </div>

              </div>
              <div class="form-group row custom_form_group">
                <div  class="col-sm-4">
                 </div>
                <div  class="col-sm-4">
                  <button type="submit" class="btn btn-primary waves-effect">PROCESS</button>
                </div>
              </div>

         </div>
      </div>
    </form>
  </div>
  <div class="col-md-1"></div>
</div>
<!--<div class="row">-->
<!--  <div class="col-md-1"></div>-->
<!--  <div class="col-md-10">-->
<!--    <form class="form-horizontal" id="employeeInOutReport" target="_blank" action="{{ route('employee-entry-out-report-process') }}" method="post">-->
<!--      @csrf-->
<!--      <div class="card">-->
<!--         <h3 class="card-title card_top_title salary-generat-heading">2-Monthly(Date by Date) Attendance Details <h3>-->
<!--        <div class="card-body card_form">-->

<!--              <div class="form-group row custom_form_group">-->
<!--                <label class="col-sm-2 control-label">Project Name<span class="req_star">*</span></label>-->
<!--                <div class="col-sm-6">-->
<!--                  <select class="form-select" name="project_id" required>-->
<!--                    <option value="">Select Project</option>-->
<!--                    @foreach($project as $item)-->
<!--                    <option value="{{ $item->proj_id }}">{{ $item->proj_name }}</option>-->
<!--                    @endforeach-->
<!--                  </select>-->
<!--                 </div>-->
<!--                 <div class="col-md-4"><a href="#"   onclick="showProjectListReport()">Projects Color Code</a> </div>-->
<!--              </div>-->
<!--              <div class="form-group row custom_form_group">-->
<!--                <label class="col-sm-2 control-label"> Sponsor</label>-->
<!--                <div class="col-sm-4">-->
<!--                  <select class="form-select" name="sponserId">-->
<!--                    <option value="">All Sponsor</option>-->
<!--                    @foreach($sponser as $item)-->
<!--                    <option value="{{ $item->spons_id }}">{{ $item->spons_name }}</option>-->
<!--                    @endforeach-->
<!--                  </select>-->
<!--                </div>-->
<!--              </div>-->

<!--              <div class="form-group row custom_form_group">-->
<!--                        <label class="col-sm-2 control-label">Attendance Last Date :</label>-->
<!--                        <div class="col-sm-4">-->
<!--                            <input type="date" class="form-control" name="date" value="{{date('Y-m-d')}}" /> -->
<!--                        </div> -->
<!--              </div>-->


<!--              <div class="form-group row custom_form_group">-->
<!--                        <label class="col-sm-2 control-label">Working Shift</label>-->
<!--                        <div class="col-sm-4">-->
<!--                                <select class="form-select"  name="working_shift" >-->
<!--                                  <option value="0">Day Shift</option>-->
<!--                                  <option value="1">Night Shift</option>-->
<!--                                  <option value="">Both Shift</option>-->
<!--                                </select>-->
<!--                        </div> -->
<!--              </div>-->

<!--              <div class="form-group row custom_form_group">-->
<!--                <label class="col-sm-2 control-label">Data Range</label>-->
<!--                <div class="col-sm-2">-->
<!--                        <select class="form-select"  name="page_offset" >-->
<!--                          <option value="0">1 - 500</option>-->
<!--                          <option value="500">501 - 1000</option>                        -->-->
<!--                        </select>-->
<!--                </div>-->
<!--                <div class="col-sm-2">-->
<!--                  <input type="checkbox" id="monthly_summary" name="monthly_summary" value="1">-->
<!--                  <label class="control-label"> Monthly Summary</label><br>-->
<!--                </div>-->
<!--                <div  class="col-sm-2">-->
<!--                  <button type="submit" class="btn btn-primary waves-effect">PROCESS</button>-->
<!--                </div>-->
<!--              </div>-->


<!--         </div>-->

<!--      </div>-->
<!--    </form>-->
<!--  </div>-->
<!--  <div class="col-md-1"></div>-->
<!--</div>-->




<!-- 3.1 Employee Attendence Sumamry Report Project WIse !-->
<div class="row">
    <div class="col-md-1"></div>
    <div class="col-md-10">
        <div class="card">
             <h3 class="card-title card_top_title salary-generat-heading">3-1 Daily Attendance Working Hours Summary <h3>
            <div class="card-body card_form">

                <form class="form-horizontal" id="registration" target="_blank"
                    action="{{ route('employee.daily.attendance.summary.process.request') }}" method="post">
                    @csrf
                   <!-- Project List  -->
                    <div class="form-group row custom_form_group">
                        <label class="col-sm-2 control-label">Project Name:</label>
                        <div class="col-sm-6">
                                <select class="selectpicker" name="project_name_id[]" multiple >
                                  @foreach($project as $proj)
                                  <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                                  @endforeach
                                </select>
                        </div>
                    </div>

                    <div class="form-group row custom_form_group">
                        <label class="col-sm-2 control-label"> Date :</label>
                        <div class="col-sm-6">
                            <input type="date" class="form-control" name="date" value="{{date('Y-m-d')}}" />
                        </div>
                    </div>

                    <div class="form-group row custom_form_group">
                        <label class="col-sm-2 control-label">Working Shift</label>
                        <div class="col-sm-4">
                                <select class="form-select" name="working_shift"  >
                                   <option value="3">Yesterday Nightshift & Today Dayshift</option>
                                  <option value="0">Day Shift</option>
                                  <option value="1">Night Shift</option>
                                  <option value="2">Both Shift</option>

                                </select>
                        </div>
                         <div  class="col-sm-2">
                          <button type="submit" class="btn btn-primary waves-effect">PROCESS</button>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
    <div class="col-md-1"></div>
</div>


<!-- 3.2 Attendance Summary Report !-->
<div class="row">
  <div class="col-md-1"></div>
  <div class="col-md-10">
      <div class="card">
          <h3 class="card-title card_top_title salary-generat-heading"> 3-2 Attendance Summary Report <h3>
          <div class="card-body card_form">
              <form class="form-horizontal" id="registration" target="_blank"
                  action="{{ route('employee.monthly.attendance.summary.process.request') }}" method="post">
                  @csrf
                 <!-- Project List  -->
                  <div class="form-group row custom_form_group">
                      <label class="col-sm-2 control-label">Project:</label>
                      <div class="col-sm-4">
                              <select class="selectpicker" name="project_name_id[]" multiple >
                                @foreach($project as $proj)
                                <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                                @endforeach
                              </select>
                      </div>
                  </div>

                  <div class="form-group row custom_form_group">
                    <label class="col-sm-2 control-label">Date<span class="req_star">*</span></label>
                    <div class="col-sm-4">
                      <input type="date" class="form-control" name="date" value="{{date('Y-m-d')}}" />

                      {{-- <select class="form-control" name="month_id">
                        @foreach($month as $item)
                        <option value="{{ $item->month_id }}" {{ $item->month_id == Carbon\Carbon::now()->format('m') ? 'selected' :'' }}>{{ $item->month_name }}</option>
                        @endforeach
                      </select> --}}
                    </div>
                  </div>

                  {{-- <div class="form-group row custom_form_group">
                    <label class="col-sm-2 control-label">Year:<span class="req_star">*</span></label>
                    <div class="col-sm-4">
                      <select class="form-control" name="year_id" required>
                        @foreach(range(date('Y'), date('Y')-1) as $y)
                        <option value="{{$y}}" {{$y}}>{{$y}}</option>
                        @endforeach
                      </select>
                    </div>
                    <div  class="col-sm-2">
                      <button type="submit" class="btn btn-primary waves-effect">PROCESS</button>
                    </div>
                  </div>  --}}

                  <div class="form-group row custom_form_group">
                    <label class="col-sm-2 control-label">Report Type </label>
                    <div class="col-sm-4">
                      <select class="form-control" name="report_type" required>
                         <option value="1">Single Month Working Hours</option>
                         <option value="2">Today Present Employee</option>
                      </select>
                    </div>
                    <div  class="col-sm-2">
                      <button type="submit" class="btn btn-primary waves-effect">PROCESS</button>
                    </div>
                  </div>


              </form>
          </div>

      </div>
  </div>
  <div class="col-md-1"></div>
</div>

<!-- 4 Employee Attendance Excel Download !-->
<div class="row">
  <div class="col-md-1"></div>
  <div class="col-md-10">
    <form class="form-horizontal" id="attendance_excel_download_form" target="_blank" action="{{ route('employee.in.out.record.excel.download') }}" method="post">
      @csrf
      <div class="card">
        <h3 class="card-title card_top_title salary-generat-heading">4-Employee Attendance Download As Excel<h3>
        <div class="card-body card_form">
              <div class="form-group row custom_form_group">
                <label class="col-sm-2 control-label">Project<span class="req_star">*</span></label>
                <div class="col-sm-4">
                  <select class="form-select" name="project_id" required>
                    @foreach($project as $item)
                    <option value="{{ $item->proj_id }}">{{ $item->proj_name }}</option>
                    @endforeach
                  </select>
                 </div>
              </div>
              {{-- <div class="form-group row custom_form_group">
                <label class="col-sm-2 control-label"> Sponsor:<span class="req_star">*</span></label>
                <div class="col-sm-4">
                  <select class="form-select" name="sponserId">
                    <option value="">All Sponsor</option>
                    @foreach($sponser as $item)
                    <option value="{{ $item->spons_id }}">{{ $item->spons_name }}</option>
                    @endforeach
                  </select>
                </div>
              </div> --}}

              <div class="form-group row custom_form_group">
                        <label class="col-sm-2 control-label">Day 1 To Selected Date:</label>
                        <div class="col-sm-4">
                            <input type="date" class="form-control" name="date" value="{{date('Y-m-d')}}" />
                        </div>
              </div>
              <div class="form-group row custom_form_group">
                <label class="col-sm-2 control-label">Month:<span class="req_star">*</span></label>
                <div class="col-sm-4">
                  <select class="form-select" name="month_id">
                    @foreach($month as $item)
                      <option value="{{ $item->month_id }}" {{ $item->month_id == Carbon\Carbon::now()->format('m') ? 'selected' :'' }}>{{ $item->month_name }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="form-group row custom_form_group">
                <label class="col-sm-2 control-label">Year:<span class="req_star">*</span></label>
                <div class="col-sm-4">
                  <select class="form-select" name="year_id" required>
                    @foreach(range(date('Y'), date('Y')-2) as $y)
                    <option value="{{$y}}" {{$y}}>{{$y}}</option>
                    @endforeach
                  </select>
                </div>
              </div>

              <div class="form-group row custom_form_group">
                        <label class="col-sm-2 control-label">Working Shift</label>
                        <div class="col-sm-4">
                                <select class="form-select"  name="working_shift" >
                                  <option value="0">Day Shift</option>
                                  <option value="1">Night Shift</option>
                                  <option value="">Both Shift</option>
                                </select>
                        </div>

              </div>
              <div class="form-group row custom_form_group">
                <label class="col-sm-2 control-label">Report Type</label>
                <div class="col-sm-4">
                        <select class="form-select"  name="report_type" >
                          <option value="1">Project Base </option>
                          <option value="2">Employee Base</option>
                        </select>
                </div>
                <div  class="col-sm-2">
                  <button type="submit" class="btn btn-primary waves-effect">PROCESS</button>
                </div>
      </div>
         </div>
      </div>
    </form>
  </div>
  <div class="col-md-1"></div>
</div>


<!-- 5 Projectwise Month to Month Work Hours Summary !-->
<div class="row">
  <div class="col-md-1"></div>
  <div class="col-md-10">
    <form class="form-horizontal" id="employeeInOutReport" target="_blank" action="{{ route('emp.attendance.total.work.hours.summary.report') }}" method="post">
      @csrf
      <div class="card">
         <h3 class="card-title card_top_title salary-generat-heading">5-Projectwise Month by Month Work Hours Summary<h3>
        <div class="card-body card_form">
                <!-- Project List  -->
               <div class="form-group row custom_form_group">
                        <label class="col-sm-2 control-label">Project:</label>
                        <div class="col-sm-4">
                                <select class="selectpicker" name="project_id[]" multiple >
                                  @foreach($project as $proj)
                                  <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                                  @endforeach
                                </select>
                        </div>
                    </div>

              <div class="form-group row custom_form_group">
                        <label class="col-sm-2 control-label">From:</label>
                        <div class="col-sm-4">
                            <input type="date" class="form-control" name="fromdate" value="{{date('Y-m-d')}}" />
                        </div>
              </div>

              <div class="form-group row custom_form_group">
                        <label class="col-sm-2 control-label">To:</label>
                        <div class="col-sm-4">
                            <input type="date" class="form-control" name="todate" value="{{date('Y-m-d')}}" />
                        </div>
                          <div  class="col-sm-2">
                          <button type="submit" class="btn btn-primary waves-effect">PROCESS</button>
                        </div>
              </div>
         </div>


      </div>
    </form>
  </div>
  <div class="col-md-1"></div>
</div>





<!-- 6 Employee Attendence Manpower Sumamry Report Project WIse !-->
<div class="row">
    <div class="col-md-1"></div>
    <div class="col-md-10">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="card-title card_top_title salary-generat-heading">6-Daily Attendance Manpower Summary<h3>
                    </div>

                </div>
            </div>
            <div class="card-body card_form">

                <form class="form-horizontal" id="registration" target="_blank"
                    action="{{ route('employee.daily.attendance.manpower.summary.request') }}" method="post">
                    @csrf
                   <!-- Project List  -->
                   <div class="form-group row custom_form_group">
                    <label class="col-sm-2 control-label">Project Name<span class="req_star">*</span></label>
                    <div class="col-sm-4">
                      <select class="form-control" name="project_id" required>
                        @foreach($project as $item)
                        <option value="{{ $item->proj_id }}">{{ $item->proj_name }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                    <div class="form-group row custom_form_group">
                        <label class="col-sm-2 control-label"> Date :</label>
                        <div class="col-sm-4">
                            <input type="date" class="form-control" name="date" value="{{date('Y-m-d')}}" />
                        </div>
                    </div>
                    <div class="form-group row custom_form_group">
                        <label class="col-sm-2 control-label"></label>
                        <div class="col-sm-4">
                          <input type="checkbox" id="check_day_night" name="check_day_night" value="1">
                            <label class="col-sm-6 control-label"> Day & Night Details</label><br>
                        </div>
                        <div  class="col-sm-2">
                          <button type="submit" class="btn btn-primary waves-effect">PROCESS</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
    <div class="col-md-1"></div>
</div>


<div class="row">
    <div class="col-md-1"></div>
    <div class="col-md-10">
        <div class="card">
            <h3 class="card-title card_top_title salary-generat-heading">7-Absence Employees Attendance Details <h3>
            <div class="card-body card_form">
                <form class="form-horizontal" id="registration" target="_blank"
                    action="{{ route('employee.daily.absent.manpower.report.request') }}" method="post">
                    @csrf
                   <!-- Project List  -->
                   <div class="form-group row custom_form_group">
                    <label class="col-sm-2 control-label">Project Name  </label>
                    <div class="col-sm-4">
                      <select class="selectpicker" name="project_ids[]" multiple>
                        @foreach($project as $item)
                        <option value="{{ $item->proj_id }}">{{ $item->proj_name }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>

                  <div class="form-group row custom_form_group">
                  <label class="col-sm-2 control-label"> Sponsor:<span class="req_star">*</span></label>
                  <div class="col-sm-4">
                    <select class="selectpicker" name="sponsor_ids[]" multiple>
                      @foreach($sponser as $item)
                      <option value="{{ $item->spons_id }}">{{ $item->spons_name }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
                    <div class="form-group row custom_form_group">
                        <label class="col-sm-2 control-label"> Date :</label>
                        <div class="col-sm-4">
                            <input type="date" class="form-control" name="date" value="{{date('Y-m-d')}}" />
                        </div>
                    </div>
                    <div class="form-group row custom_form_group">
                        <label class="col-sm-2 control-label">Working Shift</label>
                        <div class="col-sm-4">
                                <select class="selectpicker" name="day_night_shift[]" multiple >
                                  <option value="0">Day Shift</option>
                                  <option value="1">Night Shift</option>
                                </select>
                        </div>

                    </div>

                    <div class="form-group row custom_form_group">
                      <label class="col-sm-2 control-label">Report Type</label>
                      <div class="col-sm-4">
                              <select class="form-select" name="report_type" >
                                <option value="1">Attendance Details</option>
                                <option value="2">Employee Details List</option>
                              </select>
                      </div>
                      <div class="col-sm-2">
                        <button type="submit" class="btn btn-primary waves-effect">PROCESS</button>
                    </div>
                  </div>


                </form>
            </div>

        </div>
    </div>
    <div class="col-md-1"></div>
</div>


<!-- 8 Employee Monthly Absent  Report !-->
<div class="row">
  <div class="col-md-1"></div>
  <div class="col-md-10">
    <form class="form-horizontal" id="employeeInOutReport" target="_blank" action="{{ route('employee.monthly.absent.report.request') }}" method="post">
      @csrf
      <div class="card">
        <div class="card-body card_form">
              <h3 class="card-title card_top_title salary-generat-heading">8. Employee Absent Monthly Report <h3>
                <div class="form-group row custom_form_group">
                <label class="col-sm-2 control-label">Project Name<span class="req_star">*</span></label>
                <div class="col-sm-4">
                  <select class="form-control" name="project_id">
                    <option value="">Select Project</option>
                    @foreach($project as $item)
                    <option value="{{ $item->proj_id }}">{{ $item->proj_name }}</option>
                    @endforeach
                  </select>
                 </div>
              </div>

              <div class="form-group row custom_form_group">
                <label class="col-sm-2 control-label"> Total Absent Days </label>
                <div class="col-sm-4">
                   <input type="number" class="form-control typeahead" placeholder="Input Working Day" name="working_day"  required>
                </div>
              </div>


             <!--<div class="form-group row custom_form_group">-->
             <!--           <label class="col-sm-2 control-label">Join Before:</label>-->
             <!--           <div class="col-sm-4">-->
             <!--               <input type="date" class="form-control" name="join_date" value="{{date('Y-m-d')}}" />-->
             <!--           </div>-->
             <!-- </div>-->


              <div class="form-group row custom_form_group">
                <label class="col-sm-2 control-label">Attendance Month:<span class="req_star">*</span></label>
                <div class="col-sm-4">
                  <select class="form-select" name="month_id">
                    @foreach($month as $item)
                      <option value="{{ $item->month_id }}" {{ $item->month_id == Carbon\Carbon::now()->format('m') ? 'selected' :'' }}>{{ $item->month_name }}</option>
                    @endforeach
                  </select>
                </div>
              </div>

              <div class="form-group row custom_form_group">
                  <label class="col-sm-2 control-label">Year:<span class="req_star">*</span></label>
                  <div class="col-sm-4">
                    <select class="form-select" name="year_id" required>
                      @foreach(range(date('Y'), date('Y')-1) as $y)
                      <option value="{{$y}}" {{$y}}>{{$y}}</option>
                      @endforeach
                    </select>
                  </div>

              </div>
               <div class="form-group row custom_form_group">
                <label class="col-sm-2 control-label">Report Type:<span class="req_star">*</span></label>
                <div class="col-sm-4">
                  <select class="form-select" name="report_type">
                     <option value="1">Attendance Report</option>
                     <option value="2">Contact Details</option>
                  </select>
                </div>
                 <div class="col-sm-2">
                      <button type="submit" class="btn btn-primary waves-effect">PROCESS</button>
                  </div>
              </div>

         </div>
      </div>
    </form>
  </div>
  <div class="col-md-1"></div>
</div>



<!-- script area -->
<script type="text/javascript">
  /* form validation */
  $(document).ready(function() {
    $("#employeeInOutReport").validate({
      rules: {
        month_id: {
          required: true,
        },
        year_id: {
          required: true,
        },
      },

      messages: {
        month_id: {
          required: "You Must Be Select This Field!",
        },
        year_id: {
          required: "Please Select year!",
        },
      },


    });
  });

 function showProjectListReport(){
        var searchType = $('#searchBy').find(":selected").val();
              var searchValue = $("#empl_info").val();

              if(searchType == 1){
                  alert("Print Preview not possible in Master Searching");
                  return;
              }

              // Create the query string with parameters
              const queryString = new URLSearchParams({
                  project_working_status: 1,
                  project_status: 1,
              }).toString();

              var parameterValue = queryString; // Set parameter value
              var url = "{{ route('project.information.report', ':parameter') }}";
              url = url.replace(':parameter', parameterValue);
              window.open(url, '_blank');
  }


</script>

<!-- added this for Multiple Selection dropdownlist  -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta2/css/bootstrap-select.min.css" integrity="sha512-mR/b5Y7FRsKqrYZou7uysnOdCIJib/7r5QeJMFvLNHNhtye3xJp1TdJVPLtetkukFn227nKpXD9OjUc09lx97Q==" crossorigin="anonymous"
  referrerpolicy="no-referrer" />

<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta2/js/bootstrap-select.min.js" integrity="sha512-FHZVRMUW9FsXobt+ONiix6Z0tIkxvQfxtCSirkKc5Sb4TKHmqq1dZa8DphF0XqKb3ldLu/wgMa8mT6uXiLlRlw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>


@endsection
