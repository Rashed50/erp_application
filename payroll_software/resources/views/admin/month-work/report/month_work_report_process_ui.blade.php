@extends('layouts.admin-master')
@section('title') Work Records @endsection
@section('content')
<div class="row bread_part">
  <div class="col-sm-12 bread_col">
    <h4 class="pull-left page-title bread_title">Employee Monthly Work Records</h4>
    <ol class="breadcrumb pull-right">
      <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
      <li class="active">Work Records</li>
    </ol>
  </div>
</div>
<!-- alert message -->
<div class="row">
  <div class="col-md-2"></div>
  <div class="col-md-8">
    @if(Session::has('success'))
    <div class="alert alert-success alertsuccess" role="alert">
    <strong> {{Session::get('success')}}</strong>  
    </div>
    @endif
    @if(Session::has('error'))
    <div class="alert alert-success alertsuccess" role="alert">
      <strong> {{Session::get('error')}}</strong>  
    </div>
    @endif
   
  </div>
</div>

<!-- Procet, Sponser base All Employee Monthly work Record -->

<div class="row">
  <div class="col-md-2"></div>
  <div class="col-md-8">
    <form class="form-horizontal" id="registration" target="_blank" action="{{ route('project-wise-employe.month-work.record.report') }}" method="post">
      @csrf
      <div class="card">
        <div class="card-header">
          <div class="row">
            <div class="col-md-12">
              <h3 class="card-title card_top_title salary-generat-heading"> Employees Work Records </h3>
            </div>
            <div class="clearfix"></div>
          </div>
        </div>
        <div class="card-body card_form" style="padding-top: 0;">

            <div class="form-group row custom_form_group">
                <label class="col-sm-3 control-label">Emp. IDs:</label>
                <div class="col-sm-5">
                    <input type="text" class="form-control" id="employee_ids" name="employee_ids" />
                 
                </div>
            </div>

          <div class="form-group row custom_form_group">
                <label class="col-sm-3 control-label">Working Project:</label>
                <div class="col-sm-5">
                <select class="selectpicker" name="proj_id[]" multiple>
                  {{-- <option value="0">All</option> --}}
                  @foreach($projects as $proj)
                  <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                  @endforeach
                </select>
              </div>
          </div>

          {{-- Sponser List --}}
          <div class="form-group row custom_form_group">
            <label class="col-sm-3 control-label" >Sponsor:</label>
            <div class="col-sm-5">
              <select class="selectpicker" name="SponsId[]" multiple>
                {{-- <option value="0">All</option> --}}
                @foreach($sponser as $spons)
                <option value="{{ $spons->spons_id }}">{{ $spons->spons_name }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="form-group row custom_form_group">
            <label class="col-sm-3 control-label"  >Month:</label>
            <div class="col-sm-5">
               <select class="form-select" name="month" required>
                @foreach($month as $item)
                <option value="{{ $item->month_id }}" {{ $item->month_id == Carbon\Carbon::now()->format('m') ? 'selected' :'' }}>{{ $item->month_name }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-group row custom_form_group">
            <label class="col-sm-3 control-label"  >Year:</label>
            <div class="col-sm-5">
                <select class="form-select" name="year" required>
                                @foreach(range(date('Y'), date('Y')-1) as $y)
                                    <option value="{{$y}}" {{$y}}>{{$y}}</option>
                                @endforeach
                </select>
            </div>
          </div>
          <div class="form-group row custom_form_group">
            <label class="col-sm-3 control-label" >Data Source:</label>
            <div class="col-sm-5">
                <select class="form-select" name="data_source" required>
                  <option value="1">Monthly Work Records</option>
                  <option value="2">Multi-Project Records</option>
                  <option value="3">Attendance IN/OUT</option>
                   <option value="4">Only WPS Employees</option>
                    {{-- 5 are used by others page  --}}
                <option value="7">Word Record Update History</option>
                </select>
            </div>
          </div>
        </div>
        <div class="card-footer card_footer_button text-center">
          <button type="submit" class="btn btn-primary waves-effect">Show</button>
        </div>
      </div>
    </form>
  </div>
  <div class="col-md-2"></div>
</div>


<!-- Active Employee But Not In Work History by Procet, Sponser  -->

<div class="row">
  <div class="col-md-2"></div>
  <div class="col-md-8">
    <form class="form-horizontal" id="registration" target="_blank" action="{{ route('work-history-employee-notin-work-record') }}" method="post">
      @csrf
      <div class="card">
        <div class="card-header">
          <div class="row">
            <div class="col-md-12">
              <h3 class="card-title card_top_title salary-generat-heading"> Employee Those are not in Work Records </h3>
            </div>
            <div class="clearfix"></div>
          </div>
        </div>
        <div class="card-body card_form" style="padding-top: 0;">


          


          <div class="form-group row custom_form_group">
                <label class="col-sm-3 control-label">Working Project:</label>
                <div class="col-sm-5">
                <select class="form-select" name="proj_id" required>
                  {{-- <option value="0">All</option> --}}
                  @foreach($projects as $proj)
                  <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                  @endforeach
                </select>
              </div>
          </div>

          {{-- Sponser List --}}
          <div class="form-group row custom_form_group">
            <label class="col-sm-3 control-label" >Sponsor:</label>
            <div class="col-sm-5">
              <select class="form-select" name="SponsId" required>
                <option value="0">All</option>
                @foreach($sponser as $spons)
                <option value="{{ $spons->spons_id }}">{{ $spons->spons_name }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="form-group row custom_form_group">
            <label class="col-sm-3 control-label"  >Month:</label>
            <div class="col-sm-5">
               <select class="form-select" name="month" required>
                @foreach($month as $item)
                <option value="{{ $item->month_id }}" {{ $item->month_id == Carbon\Carbon::now()->format('m') ? 'selected' :'' }}>{{ $item->month_name }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="form-group row custom_form_group">
            <label class="col-sm-3 control-label"  >Year:</label>
            <div class="col-sm-5">
                        <select class="form-select" name="year" required>
                                        @foreach(range(date('Y'), date('Y')-1) as $y)
                                          <option value="{{$y}}" {{$y}}>{{$y}}</option>
                                        @endforeach
                        </select>
            </div>
          </div>

          <div class="form-group row custom_form_group">
            <label class="control-label col-sm-3"> Employee Status:</label>
            <div class="col-sm-5">
              <select class="form-select" name="emp_status_id">
                
                <option value="1"> Active</option>
                 <option value="2">Inactive </option>
                  <option value="3">Release </option>
                   <option value="3">Final_Exit  </option>
                    <option value="4"> Release </option>
                     <option value="5">Vacation  </option>
                      <option value="6">Run_Away  </option>
                       <option value="7">VISA Cancel  </option>
                             <option value="8"> Final Exi & Return Back </option>
                       
              </select>
            </div>
          </div>

        </div>
        <div class="card-footer card_footer_button text-center">
          <button type="submit" class="btn btn-primary waves-effect">Show</button>
        </div>
      </div>
    </form>
  </div>
  <div class="col-md-2"></div>
</div>




<!-- All Employee Work Status Summary Report -->
<div class="row">
  <div class="col-md-2"></div>
  <div class="col-md-8">
    <form class="form-horizontal" id="registration" target="_blank" action="{{ route('all-employee-work-status-summary') }}" method="post">
      @csrf
      <div class="card">
        <div class="card-header">
          <div class="row">
            <div class="col-md-12">
              <h3 class="card-title card_top_title salary-generat-heading">Total Employees Summary Report </h3>
            </div>
            <div class="clearfix"></div>
          </div>
        </div>
        <div class="card-body card_form" style="padding-top: 0;">

          <div class="form-group row custom_form_group">
            <label class="control-label col-sm-3"  >Working Month:</label>
            <div class="col-sm-5">
              <select class="form-select" name="month" required>
                @foreach($month as $item)
                <option value="{{ $item->month_id }}" {{ $item->month_id == Carbon\Carbon::now()->format('m') ? 'selected' :'' }}>{{ $item->month_name }}</option>
                @endforeach
              </select>
            </div>
          </div>


        

        </div>
        <div class="card-footer card_footer_button text-center">
          <button type="submit" class="btn btn-primary waves-effect">Show</button>
        </div>
      </div>
    </form>
  </div>
  <div class="col-md-2"></div>
</div>


{{-- working hours summary report --}}
<div class="row">
  <div class="col-md-2"></div>
  <div class="col-md-8">
    <form class="form-horizontal" id="registration" target="_blank" action="{{ route('project-wise-employe.month-work.record.report') }}" method="post">
      @csrf
      <div class="card">
        <div class="card-header">
          <div class="row">
            <div class="col-md-12">
              <h3 class="card-title card_top_title salary-generat-heading"> Working Hours Summary </h3>
            </div>
            <div class="clearfix"></div>
          </div>
        </div>
        <div class="card-body card_form" style="padding-top: 0;">
          <div class="form-group row custom_form_group">
                <label class="col-sm-3 control-label">  Project:</label>
                <div class="col-sm-5">
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
            <label class="col-sm-3 control-label"  >Year:</label>
            <div class="col-sm-5">
                <input type="date" class="form-control" name='to_date' id="from_date" value="{{ date("Y-m-d") }}">
            </div>
          </div>
          <div class="form-group row custom_form_group">
            <label class="col-sm-3 control-label" >Data Source:</label>
            <div class="col-sm-5">
                <select class="form-select" name="data_source" required>
                  <option value="5">Work Hours Summary</option>
                </select>
            </div>
          </div>
        </div>
        <div class="card-footer card_footer_button text-center">
          <button type="submit" class="btn btn-primary waves-effect">Show</button>
        </div>
      </div>
    </form>
  </div>
  <div class="col-md-2"></div>
</div>


<!-- added this for Multiple Selection dropdownlist  -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta2/css/bootstrap-select.min.css" integrity="sha512-mR/b5Y7FRsKqrYZou7uysnOdCIJib/7r5QeJMFvLNHNhtye3xJp1TdJVPLtetkukFn227nKpXD9OjUc09lx97Q==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta2/js/bootstrap-select.min.js" integrity="sha512-FHZVRMUW9FsXobt+ONiix6Z0tIkxvQfxtCSirkKc5Sb4TKHmqq1dZa8DphF0XqKb3ldLu/wgMa8mT6uXiLlRlw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>



@endsection