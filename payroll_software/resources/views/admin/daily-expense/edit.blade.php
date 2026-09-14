@extends('layouts.admin-master')
@section('title') Edit Daily Cost @endsection
@section('content')
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Daily Expense Details</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        </ol>
    </div>
</div>
<!-- Session Flash Message -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        @if(Session::has('success'))
          <div class="alert alert-success alertsuccess" role="alert">
             <strong> {{Session::get('success')}} </strong>
          </div> 
        @endif
        @if(Session::has('error'))
          <div class="alert alert-warning alerterror" role="alert">
            <strong> {{Session::get('error')}} </strong>
          </div>
        @endif
    </div>
</div>

<div class="row">
  
    <div class="col-md-1"></div>
    <div class="col-md-10">
      <form class="form-horizontal" id="registration" action="{{ route('company.daily.new.expesne.update.request') }}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="card">
        <br>
            <div class="card-body card_form" style="padding-top: 0;">
              <input type="hidden" name="id" value="{{ $expensDetails->cost_id }}">
              <input type="hidden" name="old_image" value="{{ $expensDetails->vouchar }}">


              <div class="row form-group custom_form_group{{ $errors->has('sub_comp_name') ? ' has-error' : '' }}">
                <label class="col-sm-3 control-label d-block" style="text-align: left;">Expense By Company:<span class="req_star">*</span></label>
                    <div class="col-sm-7">
                        <select class="form-control" name="sub_comp_name" required>
                            <option value="">Select Here</option>
                            @foreach($subCompanies as $subComp)
                            <option value="{{ $subComp->sb_comp_id }}" {{ $subComp->sb_comp_id == $expensDetails->subCompany->sb_comp_id  ? 'selected' : '' }}>{{ $subComp->sb_comp_name}}</option>
                            {{-- <option value="{{ $subComp->sb_comp_id }}">{{ $subComp->sb_comp_name }}</option> --}}
                            @endforeach
                        </select>
                        @if ($errors->has('sub_comp_name'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('sub_comp_name') }}</strong>
                            </span>
                        @endif
                  </div>
              </div>
              <div class="row form-group custom_form_group{{ $errors->has('cost_type_id') ? ' has-error' : '' }}">
                  <label class="col-sm-3 control-label d-block" style="text-align: left;">Expense Head:<span class="req_star">*</span></label>
                  <div class="col-sm-7">
                    <select class="form-control" name="cost_type_id" required>
                        <option value="">Select Here</option>
                        @foreach($expenseHeads as $cost)
                        <option value="{{ $cost->cost_type_id }}" {{ $cost->cost_type_id == $expensDetails->costType->cost_type_id ? 'selected' : '' }}>{{ $cost->cost_type_name }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('cost_type_id'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('cost_type_id') }}</strong>
                        </span>
                    @endif
                  </div>
              </div>
              <div class="row form-group custom_form_group{{ $errors->has('project_id') ? ' has-error' : '' }}">
                  <label class="col-sm-3 control-label d-block" style="text-align: left;">Expense For Project:<span class="req_star">*</span></label>
                  <div class="col-sm-7">
                    <select class="form-control" name="project_id" required>
                        <option value="">Select Here</option>
                        @foreach($projects as $proj)
                        <option value="{{ $proj->proj_id }}" {{ $proj->proj_id == $expensDetails->project->proj_id ? 'selected' : '' }}>{{ $proj->proj_name }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('project_id'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('project_id') }}</strong>
                        </span>
                    @endif
                  </div>
              </div>

              <div class="row form-group custom_form_group{{ $errors->has('vouchar_no') ? ' has-error' : '' }}">
                  <label class="col-sm-3 control-label d-block" style="text-align: left;">Expense By:<span class="req_star">*</span></label>
                  <div class="col-sm-7">
                    <input type="number" class="form-control" id="employee_id" name="employee_id" value="{{  $expensDetails->employee->employee_id}}" placeholder="Expense By Employee ID" required>
                    @if ($errors->has('employee_id'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('employee_id') }}</strong>
                        </span>
                    @endif
                  </div>
              </div>
              <div class="row form-group custom_form_group{{ $errors->has('vouchar_no') ? ' has-error' : '' }}">
                  <label class="col-sm-3 control-label d-block" style="text-align: left;">Vouchar No:<span class="req_star">*</span></label>
                  <div class="col-sm-7">
                    <input type="text" class="form-control" id="vouchar_no" name="vouchar_no" value="{{ $expensDetails->vouchar_no }}" placeholder="Vouchar No" required>
                    @if ($errors->has('vouchar_no'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('vouchar_no') }}</strong>
                        </span>
                    @endif
                  </div>
              </div>

              <div class="row form-group custom_form_group{{ $errors->has('expire_date') ? ' has-error' : '' }}">
                  <label class="col-sm-3 control-label d-block" style="text-align: left;">Vouchar Date:<span class="req_star">*</span></label>
                  <div class="col-sm-7">
                    <input type="date" class="form-control" id="voucher_date" name="voucher_date" value="{{ $expensDetails->voucher_date }}" required>
                    @if ($errors->has('expire_date'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('voucher_date') }}</strong>
                        </span>
                    @endif
                  </div>
              </div>
              <div class="row form-group custom_form_group{{ $errors->has('description') ? ' has-error' : '' }}">
                  <label class="col-sm-3 control-label d-block" style="text-align: left;">Expense Note:<span class="req_star">*</span></label>
                  <div class="col-sm-7">
                    <textarea name="description" id="description" cols="45" rows="5" placeholder="Daily cost description....">{{ $expensDetails->description }}</textarea>
                    @if ($errors->has('description'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('description') }}</strong>
                        </span>
                    @endif
                  </div>
              </div>
              <div class="row form-group custom_form_group{{ $errors->has('gross_Amount') ? ' has-error' : '' }}">
                  <label class="col-sm-3 control-label d-block" style="text-align: left;">Gross Amount:<span class="req_star">*</span></label>
                  <div class="col-sm-7 ">
                    <input type="text" class="form-control" placeholder="Gross Amount" id="gross_Amount" name="gross_amount" value="{{ $expensDetails->gross_amount }}" required>
                    @if ($errors->has('gross_Amount'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('gross_Amount') }}</strong>
                        </span>
                    @endif
                  </div>
              </div>
              <div class="row form-group custom_form_group{{ $errors->has('vat') ? ' has-error' : '' }}">
                  <label class="col-sm-3 control-label d-block" style="text-align: left;">VAT:<span class="req_star">*</span></label>
                  <div class="col-sm-7">
                    <input type="text" class="form-control" placeholder="Vat" id="vat" name="vat" value="{{ $expensDetails->vat }}" required>
                    @if ($errors->has('vat'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('vat') }}</strong>
                        </span>
                    @endif
                  </div>
              </div>
              <div class="row form-group custom_form_group{{ $errors->has('total_amount') ? ' has-error' : '' }}">
                  <label class="col-sm-3 control-label d-block" style="text-align: left;">Total Amount:<span class="req_star">*</span></label>
                  <div class="col-sm-7">
                    <input type="text" class="form-control" placeholder="Total Amount" id="total_amount" name="total_amount" value="{{ $expensDetails->total_amount }}" required>
                    @if ($errors->has('total_amount'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('total_amount') }}</strong>
                        </span>
                    @endif
                  </div>
              </div>

              <div class="vouchar_image">
               <img src="{{asset('uploads/vouchar/'.$expensDetails->vouchar)}}" alt="">
              </div>

              <div class="row form-group {{ $errors->has('vouchar') ? ' has-error' : '' }}">
                <label class="control-label col-sm-3">Vouchar:</label>
                <div class="input-group col-sm-7">
                    <span class="input-group-btn">
                        <span class="btn btn-default btn-file btnu_browse">
                            Browse… <input type="file" id="vouchar" name="vouchar" id="imgvouchar">
                        </span>
                    </span>
                    <input type="text" class="form-control" readonly>
                </div>
                @if ($errors->has('vouchar'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('vouchar') }}</strong>
                    </span>
                @endif
                <img id='img-vouchar'/>

              </div>

            </div>
            <div class="card-footer card_footer_button text-center">
                <button type="submit" class="btn btn-primary waves-effect">UPDATE</button>
            </div>
        </div>
      </form>
    </div>
    <div class="col-md-1"></div>
</div>




<script type="text/javascript">
$(document).ready(function () {
     
 


    // Total Amount Calculation

    $('#gross_amount').on('focus', function() {
      
      if ($('#gross_amount').val() == "0" || $('#gross_amount').val() == null) {
      $('#gross_amount').val(''); }
    });
    $('#vat').on('focus', function() {
         if ($('#vat').val() == "0" || $('#vat').val() == null) {
         $('#vat').val(''); }
    });

    $('#gross_amount').on('keyup', function() {
      
        if ($('#gross_amount').val() == "" || $('#gross_amount').val() == null) {
        $('#gross_amount').val('0'); }
        var gross_amount = parseFloat($('#gross_amount').val());
        var vat = parseFloat($('#vat').val()); 
        var total_amount = vat+gross_amount;
        $('#total_amount').val(total_amount);
    }); 

    $('#vat').on('keyup', function() {
      
      if ($('#vat').val() == "" || $('#vat').val() == null) {
         $('#vat').val('0');  }
        var gross_amount = parseFloat($('#gross_amount').val());
        var vat = parseFloat($('#vat').val()); 
        var total_amount = vat+gross_amount;
        $('#total_amount').val(total_amount);
    });


});
</script>


@endsection
