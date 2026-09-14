@extends('layouts.admin-master')
@section('title') Update Cost Type @endsection
@section('content')

<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Update Expense Head</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active"> Expense Head</li>
        </ol>
    </div>
</div>

<!-- Session Flass Message -->

<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
    @if(Session::has('success'))
          <div class="alert alert-warning alerterror" role="alert">
             <strong> {{Session::get('success')}}</strong>  
          </div>
     @endif
        @if(Session::has('error'))
          <div class="alert alert-warning alerterror" role="alert">
             <strong> {{Session::get('error')}}</strong>  
          </div>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-6">
      <form class="form-horizontal" id="registration" action="{{ route('update-cost-type') }}" method="post">
        @csrf
        <div class="card">             
            <div class="card-body card_form" >
                <div class="form-group row custom_form_group">
                    <label class="control-label col-sm-3"  >Expense Head Name:<span class="req_star">*</span></label>
                    <div class="col-sm-6">
                    <input type="text" class="form-control" name="cost_type_name" value="{{ $edit->cost_type_name }}" autofocus placeholder="Expense Head Name" required>
                    </div>
                    <div class="col-sm-3">
                    <button type="submit" class="btn btn-primary waves-effect">SAVE</button>
                    </div>
                </div>
            </div>           
        </div>
      </form>
    </div>
    <div class="col-md-3"></div>
</div>
@endsection
