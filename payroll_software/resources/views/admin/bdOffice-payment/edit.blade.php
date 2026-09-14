@extends('layouts.admin-master')
@section('title') Office Payment @endsection
@section('content')
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Other Office Employee Payment Details Update</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        </ol>
    </div>
</div>
<!-- add division -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        @if(Session::has('error'))
        <div class="alert alert-warning alerterror" role="alert">
            <strong>Opps!</strong> please try again.
        </div>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <form class="form-horizontal" id="registration" action="{{ route('employee.payment.info.update.request.for-bdoffice') }}" method="post">
            @csrf
            <div class="card">
                
                <div class="card-body card_form" style="padding-top: 0;">
                    
                       <br><br>
                
                        <input type="hidden" id="emp_auto_id" name="emp_id" value="">
                        <input type="hidden" id="bdofpay_auto_id" name="bdofpay_auto_id" value="{{$SinglePaymentInfo->bdofpay_auto_id}}">
                        <div> Employee ID: 
                            <input type="number" class="form-control typeahead" placeholder="Type Employee ID"
                            name="emp_id" id="emp_id_search" onkeyup="empSearch()" onfocus="showResult()"
                            onblur="hideResult()" value="{{ $SinglePaymentInfo->employee->employee_id }}">
                            @if ($errors->has('emp_id'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('emp_id') }}</strong>
                            </span>
                            @endif
                        </div>

 


                <div class="form-group custom_form_group{{ $errors->has('vouchar_no') ? ' has-error' : '' }}">
                  <label class="control-label d-block" style="text-align: left;">Employee Name 
                  <div>
                    <input type="text" class="form-control" id="emp_name" name="emp_name" value="{{$SinglePaymentInfo->employee->employee_name}}" placeholder="Vouchar No" readonly>
                    
                  </div>
              </div>



                <div class="form-group row custom_form_group{{ $errors->has('approved_amount') ? ' has-error' : '' }}">
                    <label class="control-label col-md-3">Approved Amount :<span
                            class="req_star">*</span></label>
                    <div class="col-md-7">
                        <input type="text" class="form-control"
                            placeholder="Approved Amount Here" id="approved_amount"
                            name="approved_amount" value="{{ $SinglePaymentInfo->approved_amount  }}"
                            required>
                        @if ($errors->has('approved_amount'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('approved_amount') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>

                </div>
                <div class="card-footer card_footer_button text-center">
                    <button type="submit" class="btn btn-primary waves-effect">UPDATE</button>
                </div>
            </div>
        </form>
    </div>
    <div class="col-md-2"></div>
</div>
@endsection
