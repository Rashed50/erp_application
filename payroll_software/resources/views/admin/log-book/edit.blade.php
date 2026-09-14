@extends('layouts.admin-master')
@section('title')Vehicle  Log Book @endsection
@section('content')
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Vehicle Log Book</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active"> Vehicle Log Book</li>
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
    <div class="col-md-3"></div>
    <div class="col-md-6">
      <form class="form-horizontal" enctype="multipart/form-data" id="LogBookForm-validation" action="{{ route('update-new.LogBook') }}" method="post">
        @csrf
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-8">
                        <h3 class="card-title card_top_title"><i class="fab fa-gg-circle"></i> Update Vehicle Log Book</h3>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="card-body card_form" style="padding-top: 0;">

              <div class="form-group custom_form_group">
                  <input type="hidden" name="id" value="{{ $edit->lgb_id }}">
                  <label class="control-label d-block" style="text-align: left;">Vehicle Name:<span class="req_star">*</span></label>
                  <div>
                      <select class="form-control" name="veh_id">
                          <option value="">Select Vehicle Name</option>
                          @foreach($vehicle as $veh)
                          <option value="{{ $veh->veh_id }}" {{ $veh->veh_id == $edit->veh_id ? 'selected':'' }}>{{ $veh->veh_name }}</option>
                          @endforeach
                      </select>
                  </div>
              </div>

              <div class="form-group custom_form_group">
                  <label class="control-label d-block" style="text-align: left;">Present Miles:<span class="req_star">*</span></label>
                  <div>
                      <input type="text" class="form-control" name="per_milimeter" value="{{ $edit->per_milimeter }}" placeholder="Input Present Miles">
                  </div>
              </div>

              <div class="form-group custom_form_group">
                  <label class="control-label d-block" style="text-align: left;"> Fouel Amount (Liter):<span class="req_star">*</span></label>
                  <div>
                      <input type="text" class="form-control" name="fouel_amount" value="{{ $edit->fouel_amount }}" placeholder="Input Fouel Amount">
                  </div>
              </div>

              <div class="form-group custom_form_group">
                  <label class="control-label d-block" style="text-align: left;"> Total Cost:<span class="req_star">*</span></label>
                  <div>
                      <input type="text" class="form-control" name="total_cost" value="{{ $edit->total_cost }}" placeholder="Input Total Amount">
                  </div>
              </div>

              <div class="form-group custom_form_group">
                  <label class="control-label d-block" style="text-align: left;">Fuel Purchase Date:<span class="req_star">*</span></label>
                  <div>
                      <input type="date" class="form-control" name="date" value="{{ $edit->date }}">
                  </div>
              </div>

              <div class="form-group custom_form_group">
                  <label class="control-label d-block" style="text-align: left;">Vouchar Photo:</label>
                  <div class="row">

                    <div class="col-sm-8">
                      <div class="input-group">
                          <span class="input-group-btn">
                              <span class="btn btn-default btn-file btnu_browse">
                                  Browse… <input type="file" name="voucharPhoto" id="imgInp4">
                              </span>
                          </span>
                          <input type="text" class="form-control" readonly>
                      </div>
                      <div class="col-sm-4" style="margin-top: 10px">
                          <img id='img-upload4' class="upload_image"/>
                      </div>
                    </div>
                    <input type="hidden" name="old_image" value="{{ $edit->vouchar_photo }}">
                    <div class="col-sm-4">
                        <img src="{{ asset($edit->vouchar_photo) }}" alt="" style="width:100px">
                    </div>

                  </div>
              </div>

            </div>
            <div class="card-footer card_footer_button text-center">
                <button type="submit" class="btn btn-primary waves-effect">UPDATE</button>
            </div>
        </div>
      </form>
    </div>
    <div class="col-md-3"></div>
</div>
<!-- script area -->
<script type="text/javascript">
/* form validation */
$(document).ready(function(){
  $("#LogBookForm-validation").validate({
    /* form tag off  */
    // submitHandler: function(form) { return false; },
    /* form tag off  */
    rules: {
      veh_id: {
        required : true,
      },
      per_milimeter: {
        required : true,
        number: true,
        maxlength: 9,
      },
      fouel_amount: {
        required : true,
        number: true,
        maxlength: 9,
      },
      total_cost: {
        required : true,
        number: true,
        maxlength: 9,
      },
      date: {
        required : true,
      },
    },

    messages: {
      veh_id: {
        required : "You Must Be select This Field!",
      },
      date: {
        required : "You Must Be Select This Field!",
      },
      per_milimeter: {
        required : "Please Input This Field!",
        number : "You Must Be Input Number!",
        max : "You Must Be Input Maximum Length 9!",
      },
      fouel_amount: {
        required : "Please Input This Field!",
        number : "You Must Be Input Number!",
        max : "You Must Be Input Maximum Length 9!",
      },
      total_cost: {
        required : "Please Input This Field!",
        number : "You Must Be Input Number!",
        max : "You Must Be Input Maximum Length 9!",
      },
    },
  });
});
</script>


@endsection
