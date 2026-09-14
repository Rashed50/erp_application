@extends('layouts.admin-master')
@section('title') Salary History  @endsection
@section('content')
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Salary History</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active"> Salary History</li>
        </ol>
    </div>
</div>
<!-- add division -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        @if(Session::has('error'))
          <div class="alert alert-warning alerterror" role="alert">
             <strong>Opps!</strong> Employee Id Dosen,t Not Match!.
          </div>
        @endif
    </div>
</div>

<div class="row">
  <div class="col-md-2"></div>
  <div class="col-md-8">
      <form class="form-horizontal" id="registration" target="_blank" action="{{ route('singleEmpl-iqwama-renewal-process') }}" method="post">
        @csrf
        <div class="card">
            <div class="card-body card_form">

              <div class="form-group row custom_form_group{{ $errors->has('emp_id') ? ' has-error' : '' }}">
                  <label class="col-md-3 control-label d-block">Employee ID:<span class="req_star">*</span></label>
                  <div class="col-md-7">
                    <input type="text" class="form-control typeahead" placeholder="Type Employee ID" name="emp_id" id="emp_id_search" onkeyup="empSearch()" onfocus="showResult()" onblur="hideResult()" value="{{ old('emp_id') }}">
                    @if ($errors->has('emp_id'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('emp_id') }}</strong>
                        </span>
                    @endif
                    <div id="showEmpId"></div>
                  </div>

              </div>

              <div class="form-group row custom_form_group{{ $errors->has('year_id') ? ' has-error' : '' }}">
                  <label class="col-md-3 control-label d-block">Year :<span class="req_star">*</span></label>
                  <div class="col-md-7">
                    <select class="form-control typeahead" name="year_id">
                        @foreach(range(date('Y'), date('Y')-5) as $y)
                            <option value="{{$y}}" {{$y}} >{{$y}}</option>
                        @endforeach
                    </select>
                    @if($errors->has('year_id'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('year_id') }}</strong>
                        </span>
                    @endif
                    <div id="showEmpId"></div>
                  </div>

              </div>


            </div>
            <div class="card-footer card_footer_button text-center">
                <button type="submit" class="btn btn-primary waves-effect">PROCESS</button>
            </div>
        </div>
      </form>
    </div>
  <div class="col-md-2"></div>
</div>
@endsection
