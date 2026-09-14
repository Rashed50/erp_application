@extends('layouts.admin-master')
@section('title') Sponser Wise Employees  @endsection
@section('content')
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Sponser Wise Employees </h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active"> Sponser Wise Employees </li>
        </ol>
    </div>
</div>
<!-- add division -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        @if(Session::has('error'))
          <div class="alert alert-warning alerterror" role="alert">
             <strong>Opps!</strong> This Sponser Employee Not Assigned!.
          </div>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
      <form class="form-horizontal" target="_blank" action="{{ route('sponser-wise.employee.process') }}" method="post">
        @csrf
        <div class="card">
            <div class="card-header"></div>
            <div class="card-body card_form" style="padding-top: 0;">

              <div class="form-group custom_form_group">
                  <label class="control-label d-block" style="text-align: left;"> Sponser Name:</label>
                  <div>
                    <select class="form-control" name="spons_id">
                        @foreach($sponser as $spos)
                        <option value="{{ $spos->spons_id }}" >{{ $spos->spons_name }}</option>
                        @endforeach
                    </select>
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
