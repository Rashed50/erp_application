@extends('layouts.admin-master')
@section('title') Salary-Report-Form @endsection
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
    <h4 class="pull-left page-title bread_title">Employee Summary Search</h4>
    <ol class="breadcrumb pull-right">
      <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
      <li class="active">Employee Summary Search</li>
    </ol>
  </div>
</div>
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header"></div>
      <div class="card-body card_form">
        <div class="row">
          <div class="col-md-10">
            {{-- checkbox for iqama No wise search employee --}}
            <div class="row">
              <div class="col-md-5"></div>
              <div class="col-md-3">
                <div class="form-check" style="margin-bottom:15px;">
                </div>
              </div>
            </div>

            {{-- Search Employee Id --}}
            <div id="searchEmployeeId" class=" d-block">
              <form action="{{ route('employee-salary.summary-process') }}" target="_blank" method="post">
                @csrf
                <div class="form-group row custom_form_group ">

                  <label class="col-sm-2 control-label">Report Type<span class="req_star">*</span></label>
                  <div class="col-sm-4">
                    <select class="form-select" id= "emp_report_type" name="emp_report_type">
                        @can('salary_summary_related_all_report')
                          <option value="1">Upaid Salary</option>
                          <option value="2">Current Fiscal Year Salary</option>
                          <option value="3">Summary Emlpoyee Copy</option>
                          <option value="5">Advance History</option>
                          <option value="6">BD Office Payment</option>
                          <option value="7">Salary All Records</option>


                        @endcan
                        <option value="9">Closing Agreement Paper</option>
                        <option value="4">Working Project History</option>
                        <option value="8">Paid Salary Summary</option>
                    </select>
                  </div>

                  <label class="col-sm-2 control-label">Employee ID<span class="req_star">*</span></label>
                  <div class="col-sm-2">
                    <input type="text" class="form-control typeahead" placeholder="Employee ID" name="emp_id" id="emp_id_search" autofocus >
                    <div id="showEmpId"></div>
                    <span id="error_show" class="d-none" style="color: red"></span>
                  </div>


                  <div class="col-sm-1">
                    <button type="submit" style="margin-top: 2px" class="btn btn-primary waves-effect">SEARCH</button>

                  </div>
                </div>
              </form>
            </div>


            {{-- Search Employee IQama No --}}
            <div id="searchIqamaNo" class=" d-none">
              <form action="{{ route('employee-salary.summary-process') }}" target="_blank" method="post">
                @csrf
                <div class="form-group row custom_form_group">
                  <label class="col-sm-2 control-label">Iqama No:</label>
                  <div class="col-sm-3">
                    <input type="text" id="iqamaNoSearch" class="form-control typeahead" placeholder="Input IQama No" name="iqamaNo">
                    <span id="error_show" class="d-none" style="color: red"></span>
                  </div>


                  <label class="col-sm-2 control-label">Salary Year:<span class="req_star">*</span></label>
                  <div class="col-sm-3">
                    <select class="form-control" name="year">
                      @foreach(range(date('Y'), date('Y')-5) as $y)
                      <option value="{{$y}}" {{$y}}>{{$y}}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="col-sm-2">
                    <button type="submit" style="margin-top: 2px" class="btn btn-primary waves-effect">SEARCH</button>
                  </div>
                  <div class="col-md-1"></div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
{{-- iqama no wise Search --}}
<script type="text/javascript">
  // iqama Wise search
  function iqamaWiseSearch() {
    $('#iqamaWiseSearch').removeClass('d-block').addClass('d-none');
    $('#idWiseSearch').removeClass('d-none').addClass('d-block');
    // input field
    $('#searchEmployeeId').removeClass('d-block').addClass('d-none');
    $('#searchIqamaNo').removeClass('d-none').addClass('d-block');

  }
  // id Wise search
  function idWiseSearch() {
    $('#idWiseSearch').removeClass('d-block').addClass('d-none');
    $('#iqamaWiseSearch').addClass('d-block').removeClass('d-none');
    // input field
    $('#searchIqamaNo').removeClass('d-block').addClass('d-none');
    $('#searchEmployeeId').removeClass('d-none').addClass('d-block');
  }
</script>
@endsection
