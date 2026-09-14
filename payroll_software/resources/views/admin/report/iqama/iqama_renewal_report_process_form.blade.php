@extends('layouts.admin-master')
@section('title')Iwama Renewal Report @endsection
@section('content')
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Iqama Renewal Report</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active"> Iqama Renewal Report </li>
        </ol>
    </div>
</div>
<!-- Session Message -->
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

 {{-- Iqama Renewal Expense Inserted date to date report --}}
<div class="row mt-2">
  <div class="col-md-2"></div>
  <div class="col-md-8">
    <form class="form-horizontal" id="validate_form" target="_blank" action="{{ route('emp.iqama.renewal.report.date.to.date') }}" method="post">
      @csrf
      <div class="card">
        <div class="card-header">
          <h3 class="card-title card_top_title salary-generat-heading">1. Employee Iqama Renewal Expense Report </h3>
        </div>    

        <div class="card-body card_form" style="padding-top: 0;">
          <div class="form-group row custom_form_group">
            <label class="control-label col-md-3">Sponsor</label>
            <div class="col-md-6">
              <select class="selectpicker" name="sponsor_id[]" multiple >                
                @foreach($sponsors as $sp)
                <option value="{{$sp->spons_id }}">{{$sp->spons_name}}</option>
                @endforeach
              </select>
            </div>
          </div> 

          <div class="form-group row custom_form_group">
            <label class="control-label col-md-3">Purpose Type</label>
            <div class="col-md-6">
              <select class="form-select" name="payment_purpose_id" id="payment_purpose_id">
                <option value="">Select Purpose</option>
                <option value="1"> Iqama Renewal</option>
                <option value="2"> Medical Insurance</option>
                <option value="3"> Exit-Re-Entry</option>
                <option value="4"> Family Iqama</option> 
                <option value="5"> Family Medical Insurance</option>    
                <option value="6">Traffic Violation</option>
            </select>
            </div>
          </div> 

          

          <div class="form-group row custom_form_group">
            <label class="control-label col-md-3">Inserted By</label>
            <div class="col-md-6">
              <select class="form-select" name="inserted_by">
                <option value="">All </option>
                @foreach($inserted_by_users as $u)
                <option value="{{$u->id}}">{{$u->name}}</option>
                @endforeach
              </select>
            </div>
          </div> 

          <div class="form-group row custom_form_group">
                <label class="control-label col-md-3">Approval Status</label>
                <div class="col-md-6">
                  <select class="form-select" name="approval_status">
                    <option value="">All </option>
                    <option value="0">Pending</option>
                    <option value="1">Approved</option>
              
                  </select>
                </div>
          </div> 

            <div class="form-group row custom_form_group">
                <label class="control-label col-md-3">Renewal By</label>
                <div class="col-md-6">
                  <select class="form-select" name="expense_by">
                  <option value="">All </option>
                    <option value="1">Employee Self </option>
                    <option value="2">Company</option>               
                  </select>
                </div>
            </div> 

            <div class="form-group row custom_form_group ">
                <label class="col-sm-3 control-label">From<span class="req_star">*</span></label>
                <div class="col-sm-3">
                  <input type="date" class="form-control" name="start_date" value="<?= date("Y-m-d") ?>">
                </div>
                <label class="col-sm-1 control-label">To<span class="req_star">*</span></label>
                <div class="col-sm-3">
                  <input type="date" class="form-control" name="end_date" value="<?= date("Y-m-d") ?>"
                  max="{{ Carbon\Carbon::now()->format('Y-m-d') }}">
                </div>
            </div>
        </div>

        <div class="card-footer card_footer_button text-center">
          <button type="submit"  class="btn btn-primary waves-effect">Show Report</button>
        </div>

      </div>
    </form>
  </div>
  <div class="col-md-2"></div>
</div>

 {{--Employee base Iqama Renewal Expense & Deduction Summary Report --}}
<div class="row">
  <div class="col-md-2"></div>
  <div class="col-md-8">
      <form class="form-horizontal" id="summary_report_form" target="_blank"
          action="{{ route('project-wise-iqwama-renewal-process') }}" method="post">
          @csrf
          <div class="card">
              <div class="card-header">
                <h3 class="card-title card_top_title salary-generat-heading">2. Iqama Expense & Deduction Summary </h3>
              </div>
              <div class="card-body card_form">
                  <input type="hidden" name ="report_type" value = "1">
                  <div class="form-group row custom_form_group">
                      <label class="control-label col-md-3">Project</label>
                      <div class="col-md-6">
                          <select class="form-control" name="proj_id" required>
                              <option value="0">ALL</option>
                              @foreach($projects as $proj)
                              <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                              @endforeach
                          </select>
                      </div>
                  </div>

                  <div class="form-group row custom_form_group">
                      <label class="control-label col-md-3">Sponsor</label>
                      <div class="col-md-6">
                          <select class="form-control" name="spons_id" required>
                              <option value="0">ALL</option>
                              @foreach($sponsors as $spons)
                              <option value="{{ $spons->spons_id }}">{{ $spons->spons_name }}</option>
                              @endforeach
                          </select>
                      </div>
                  </div>

                  <!-- Month Dropdown Menu -->
                  <div class="form-group row custom_form_group">
                      <label class="control-label col-3">Month</label>
                      <div class="col-sm-7">
                          <select class="form-control" name="month" id="month">
                                <option selected value='1'>January</option>
                                <option value='2'>February</option>
                                <option value='3'>March</option>
                                <option value='4'>April</option>
                                <option value='5'>May</option>
                                <option value='6'>June</option>
                                <option value='7'>July</option>
                                <option value='8'>August</option>
                                <option value='9'>September</option>
                                <option value='10'>October</option>
                                <option value='11'>November</option>
                                <option value='12'>December</option>
                          </select>
                      </div>
                  </div>

                  <div class="form-group row custom_form_group">
                      <label class="col-sm-3 control-label">Year:<span class="req_star">*</span></label>
                      <div class="col-sm-6">
                          <select class="form-control" name="year">
                              @foreach(range(date('Y'), date('Y')-2) as $y)
                              <option value="{{$y}}" {{$y}}>{{$y}}</option>
                              @endforeach
                          </select>
                      </div>
                  </div>


                  <div class="card-footer card_footer_button text-center">
                      <button type="submit" class="btn btn-primary waves-effect">PROCESS</button>
                  </div>
              </div>
          </div>
        </div> 
      </form>
  </div>
  <div class="col-md-2"></div>
</div>

{{-- Sponsor base Iqama Renewal Expense & Deduction Summary Report --}}
 <div class="row">
  <div class="col-md-2"></div>
  <div class="col-md-8">
      <form class="form-horizontal" id="summary_report_form" target="_blank"
          action="{{ route('project-wise-iqwama-renewal-process') }}" method="post">
          @csrf
          <div class="card">

              <div class="card-header">
                <h3 class="card-title card_top_title salary-generat-heading">3. Sponsor Base Iqama Expense & Deduction Summary </h3>
              </div>
              <div class="card-body card_form">

                    <input type="hidden" name ="report_type" value = "2">
                    
                  <!--<div class="form-group row custom_form_group">-->
                  <!--  <label class="control-label col-md-3">Project Name:</label>-->
                  <!--  <div class="col-md-6">-->
                  <!--      <select class="form-control" name="proj_id" > -->
                  <!--          @foreach($projects as $proj)-->
                  <!--          <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>-->
                  <!--          @endforeach-->
                  <!--      </select>-->
                  <!--  </div>-->
                  <!--</div>-->
                  
                  <div class="form-group row custom_form_group">
                    <label class="control-label col-md-3">Sponsor</label>
                    <div class="col-md-6">
                        <select class="selectpicker" name="sponsor_id[]" multiple   >
                          @foreach($sponsors as $spons)
                          <option value="{{ $spons->spons_id }}">{{ $spons->spons_name }}</option>
                          @endforeach
                        </select>
                    </div>
                  </div>
 
                  <!-- Month Dropdown Menu -->
                  <div class="form-group row custom_form_group">
                      <label class="control-label col-3">Salary Month</label>
                      <div class="col-sm-7">
                          <select class="form-control" name="month" id="month">
                                <option selected value='1'>January</option>
                                <option value='2'>February</option>
                                <option value='3'>March</option>
                                <option value='4'>April</option>
                                <option value='5'>May</option>
                                <option value='6'>June</option>
                                <option value='7'>July</option>
                                <option value='8'>August</option>
                                <option value='9'>September</option>
                                <option value='10'>October</option>
                                <option value='11'>November</option>
                                <option value='12'>December</option>
                          </select>
                      </div>
                  </div>
                  <div class="form-group row custom_form_group">
                      <label class="col-sm-3 control-label">Salary Year:<span class="req_star">*</span></label>
                      <div class="col-sm-6">
                          <select class="form-control" name="year">
                              @foreach(range(date('Y'), date('Y')-2) as $y)
                              <option value="{{$y}}" {{$y}}>{{$y}}</option>
                              @endforeach
                          </select>
                      </div>
                  </div>
                  <div class="card-footer card_footer_button text-center">
                    <button type="submit" class="btn btn-primary waves-effect">PROCESS</button>
                </div>
              </div>              
          </div>
      </form>
  </div>
  <div class="col-md-2"></div>
</div>

{{--4 Iqama Expense and Deduction from salary month by month Report --}}
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <form class="form-horizontal" action="{{ route('project-wise-iqwama-renewal-process') }}" method="post"
            target="_blank">
            @csrf
            <div class="card">

                <div class="card-header">
                    <h3 class="card-title card_top_title salary-generat-heading">
                        4. Iqama Expense and Deduction Summary
                    </h3>
                </div>

                <div class="card-body card_form">
                    <input type="hidden" name="report_type" value="4">

                    <div class="form-group row custom_form_group">
                        <label class="control-label col-md-3">From</label>
                        <div class="col-md-6">
                            <input type="date" class="form-control" name="month_from">
                        </div>
                    </div>

                    <div class="form-group row custom_form_group">
                        <label class="control-label col-md-3">To</label>
                        <div class="col-md-6">
                            <input type="date" class="form-control" name="month_to">
                        </div>
                    </div>
                    
                    <div class="card-footer card_footer_button text-center">
                        <button type="submit" class="btn btn-primary waves-effect">PROCESS</button>
                    </div>
                </div>

                {{-- <div class="card-body card_form">
                    <input type="hidden" name="report_type" value="4">
                    <div class="form-group row custom_form_group">
                        <label class="control-label col-3">From</label>
                        <div class="col-sm-3">
                            <select class="selectpicker" name="month_from" id="month" required>
                                <option selected value=''>Selected Month</option>
                                <option value='1'>January</option>
                                <option value='2'>February</option>
                                <option value='3'>March</option>
                                <option value='4'>April</option>
                                <option value='5'>May</option>
                                <option value='6'>June</option>
                                <option value='7'>July</option>
                                <option value='8'>August</option>
                                <option value='9'>September</option>
                                <option value='10'>October</option>
                                <option value='11'>November</option>
                                <option value='12'>December</option>
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <select class="selectpicker" name="year_from" required>
                                @foreach(range(date('Y'), date('Y')-2) as $y)
                                <option value="{{$y}}" {{$y}}>{{$y}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>



                    <div class="form-group row custom_form_group">
                        <label class="control-label col-3">To</label>
                        <div class="col-sm-3">
                            <select class="selectpicker" name="month_to" id="month" required>
                                <option selected value=''>Selected Month</option>
                                <option value='1'>January</option>
                                <option value='2'>February</option>
                                <option value='3'>March</option>
                                <option value='4'>April</option>
                                <option value='5'>May</option>
                                <option value='6'>June</option>
                                <option value='7'>July</option>
                                <option value='8'>August</option>
                                <option value='9'>September</option>
                                <option value='10'>October</option>
                                <option value='11'>November</option>
                                <option value='12'>December</option>
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <select class="selectpicker" name="year_to" required>
                                @foreach(range(date('Y'), date('Y')-2) as $y)
                                <option value="{{$y}}" {{$y}}>{{$y}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="card-footer card_footer_button text-center">
                        <button type="submit" class="btn btn-primary waves-effect">PROCESS</button>
                    </div>
                </div> --}}
            </div>
        </form>
    </div>
    <div class="col-md-2"></div>
</div>
 
 



<!-- added this for Multiple Selection dropdownlist  -->
<link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta2/css/bootstrap-select.min.css"
    integrity="sha512-mR/b5Y7FRsKqrYZou7uysnOdCIJib/7r5QeJMFvLNHNhtye3xJp1TdJVPLtetkukFn227nKpXD9OjUc09lx97Q=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"
    integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4"
    crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta2/js/bootstrap-select.min.js"
    integrity="sha512-FHZVRMUW9FsXobt+ONiix6Z0tIkxvQfxtCSirkKc5Sb4TKHmqq1dZa8DphF0XqKb3ldLu/wgMa8mT6uXiLlRlw=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
 
<script type="text/javascript">
    
</script>
@endsection
   