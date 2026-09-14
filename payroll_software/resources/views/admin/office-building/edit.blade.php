@extends('layouts.admin-master')
@section('title') Office Building @endsection
@section('content')
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Update Accommodation Information</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active"> Villa Information</li>
        </ol>
    </div>
</div>
<!-- add division -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        @if(Session::has('success'))
          <div class="alert alert-success alertsuccess" role="alert">
             <strong>{{ Session::get('success') }}</strong>
          </div>
        @endif
        @if(Session::has('error'))
          <div class="alert alert-warning alerterror" role="alert">
             <strong>{{ Session::get('error') }}</strong>
          </div>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-6">
      <form class="form-horizontal" id="office_buildings-validation" action="{{ route('rent.new-building.update') }}" enctype="multipart/form-data" method="post">
        @csrf
        <div class="card">
            <div class="card-header">
            </div>

            <div class="card-body" style="padding-top: 0;">
                <input type="hidden" name="id" value="{{ $edit->ofb_id }}">

                <div class="form-group row custom_form_group" id="searchEmployeeId">
                    <label class="control-label col-md-4">Employee ID:<span
                            class="req_star">*</span></label>
                    <div class="col-md-6">
                        <input type="text" class="form-control typeahead"
                            placeholder="Input Employee ID" name="empId" id="emp_id_search"
                            onkeyup="empSearch()" onfocus="showResult()" onblur="hideResult()" value="{{ $edit->employee_id }}">

                        <div id="showEmpId"></div>
                        <span id="error_show" class="d-none" style="color: red"></span>
                    </div>
                    <div class="col-md-2"></div>
                </div>

                <div class="form-group row custom_form_group">
                    <label class="control-label col-sm-4"> Building Name:<span class="req_star">*</span></label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" name="ofb_name" value="{{ $edit->ofb_name }}" placeholder="Input Building Name">
                    </div>
                    <div class="col-sm-2"></div>
                </div>

              <div class="form-group row custom_form_group">
                  <label class="control-label col-sm-4">Rent Date:<span class="req_star">*</span></label>
                  <div class="col-sm-6">
                      <input type="date" class="form-control" name="ofb_rent_date" value="{{ $edit->ofb_rent_date }}" >
                  </div>
                  <div class="col-sm-2"></div>
              </div>

              <div class="form-group custom_form_group">
                  <!-- <label class="control-label d-block" style="text-align: left;">Rent Form:<span class="req_star">*</span></label> -->
                  <div>
                      <input type="hidden" class="form-control" name="ofb_rent_form" value="{{ old('ofb_rent_form') }}" placeholder="Input Rent Form">
                  </div>
              </div>

              <div class="form-group row custom_form_group">
                  <label class="control-label col-sm-4"> Owner Mobile:<span class="req_star">*</span></label>
                  <div class="col-sm-6">
                      <input type="text" class="form-control" name="ofb_owner_mobile" value="{{ $edit->ofb_owner_mobile }}" placeholder="Input Owner Mobile">
                  </div>
                  <div class="col-sm-2"></div>
              </div>

              <div class="form-group row custom_form_group">
                  <label class="control-label col-sm-4">Accommodation Capacity:<span class="req_star">*</span></label>
                  <div class="col-sm-6">
                      <input type="number" class="form-control" name="ofb_accomodation_capacity" value="{{ $edit->ofb_accomod_capacity }}" placeholder="Enter Building Accomodation Capacity">
                  </div>
                  <div class="col-sm-2"></div>
              </div>

              <div class="form-group row custom_form_group">
                  <label class="control-label col-sm-4"> Amount Per Month:<span class="req_star">*</span></label>
                  <div class="col-sm-6">
                      <input type="text" class="form-control" name="ofb_rent_amount" value="{{ $edit->ofb_rent_amount }}" placeholder="Input Amount Per Month">
                  </div>
                  <div class="col-sm-2"></div>
              </div>

              <div class="form-group row custom_form_group">
                  <label class="control-label col-sm-4"> Advance Payment:<span class="req_star">*</span></label>
                  <div class="col-sm-6">
                      <input type="number" class="form-control" name="ofb_advance_amount" value="{{ $edit->ofb_advance_amount }}" placeholder="Input Advance Payment">
                  </div>
                  <div class="col-sm-2"></div>
              </div>

              <div class="form-group row custom_form_group">
                  <label class="control-label col-sm-4"> Agrement Date:<span class="req_star">*</span></label>
                  <div class="col-sm-6">
                      <input type="date" class="form-control" name="ofb_agrement_date" value="{{ $edit->ofb_agrement_date }}" >
                  </div>
                  <div class="col-sm-2"></div>
              </div>

              <div class="form-group row custom_form_group">
                  <label class="control-label col-sm-4"> Expiration date:<span class="req_star">*</span></label>
                  <div class="col-sm-6">
                      <input type="date" class="form-control" name="ofb_expiration_date"value="{{ $edit->ofb_expiration_date }}" min="{{ date('Y-m-d') }}">
                  </div>
                  <div class="col-sm-2"></div>
              </div>

              <div class="form-group row custom_form_group">
                  <label class="control-label col-sm-4"> City Name:<span class="req_star">*</span></label>
                  <div class="col-sm-6">
                      <input type="text" class="form-control" name="ofb_city_name" value="{{ $edit->ofb_city_name }}" placeholder="Input City Name">
                  </div>
                  <div class="col-sm-2"></div>
              </div>

              <div class="form-group row custom_form_group">
                  <label class="control-label col-sm-4"> Location Details<span class="req_star">*</span></label>
                  <div class="col-sm-6">
                    <textarea class="form-control" name="ofb_location_details" placeholder="Input Building Location Details">{{ $edit->ofb_loct_details }}</textarea>
                  </div>
                  <div class="col-sm-2"></div>
              </div>

              <div class="form-group row custom_form_group">
                <label class="col-sm-4 control-label">Agreement Papers:</label>
                <div class="col-sm-5">
                    <div class="input-group">
                        <span class="input-group-btn">
                            <span class="btn btn-default btn-file btnu_browse">
                                Browse… <input type="file" name="ofb_dead_papers" id="imgInp3">
                            </span>
                        </span>
                        <input type="text" class="form-control" readonly>
                    </div>
                </div>
                <div class="col-sm-3">
                    <img id='img-upload3' class="upload_image" />
                </div>
              </div>
              <br><br>

              <div class="row">
                <div class="col-sm-6" style="text-align: right;">
                    <label for="">Old Deed Photo: </label>
                    <input type="hidden" name="old_image" value="{{ $edit->ofb_dead_papers }}">
                </div>
                <div class="col-sm-6">
                    <img src="{{ asset($edit->ofb_dead_papers) }}" alt="" style="width:100px">
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
  $("#office_buildings-validation").validate({
    /* form tag off  */
    // submitHandler: function(form) { return false; },
    /* form tag off  */
    rules: {
      ofb_rent_date: {
        required : true,
      },
      ofb_agrement_date: {
        required : true,
      },
      ofb_expiration_date: {
        required : true,
      },


      ofb_owner_mobile: {
        required : true,
        number: true,
        maxlength: 15,
      },

      ofb_rent_amount: {
        required : true,
        number: true,
        maxlength: 15,
      },
      ofb_advance_amount: {
        required : true,
        number: true,
        maxlength: 15,
      },

    },

    messages: {
      ofb_rent_date: {
        required : "You Must Be Select This Field!",
      },
      ofb_agrement_date: {
        required : "You Must Be Select This Field!",
      },
      ofb_expiration_date: {
        required : "You Must Be Select This Field!",
      },
      ofb_owner_mobile: {
        required : "Please Input This Field!",
        number : "You Must Be Input Number!",
        max : "You Must Be Input Maximum Length 15!",
      },
      ofb_rent_amount: {
        required : "Please Input This Field!",
        number : "You Must Be Input Number!",
        max : "You Must Be Input Maximum Length 15!",
      },
      ofb_advance_amount: {
        required : "Please Input This Field!",
        number : "You Must Be Input Number!",
        max : "You Must Be Input Maximum Length 15!",
      },
    },

    errorPlacement: function(error, element)
    {
      if (element.is(":radio"))
      {
          error.appendTo(element.parents('.gender'));
      }
      else if(element.is(":file")){
          error.appendTo(element.parents('.passfortFiles'));
      }
      else
      {
          error.insertAfter( element );
      }

     }




  });
});
</script>


@endsection
