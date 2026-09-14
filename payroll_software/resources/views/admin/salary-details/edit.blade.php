@extends('layouts.admin-master')
@section('title') Update Employee Salary Information @endsection
@section('content')

  @section('internal-css')
      {{-- image resize --}}
      <style media="screen">
          img.image-resize{
            width: 100px;
            border: 1px solid #ddd;
            margin-left: 5px;
          }
          div.employee-info{
            display: flex;
          }
          p.employee-content{

          }
          .card_form {
          	padding-top: 10px;
          }
      </style>
  @endsection

<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Employee Salary Information</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active">Update Salary Info</li>
        </ol>
    </div>
</div>


<div class="row">
    <div class="col-lg-12">
        <form class="form-horizontal" id="salary-details-form" method="post" action="{{ route('salary-detalis-update') }}">
          @csrf
          <div class="card">
              <div class="card-header">
                  <div class="row">
                      <div class="col-md-3">
                          <a href="{{ route('salary-details') }}" class="btn btn-md btn-primary waves-effect card_top_button"><i class="fa fa-th"></i> Salary List</a>
                      </div>
                      <div class="col-md-5">
                          <div class="employee-info">
                            <p class="employee-content">
                                <ul>
                                  <li> <strong>Project: </strong> <span>{{ $data->employee->project_id == NULL ? 'Not Assigned' : $data->employee->project->proj_name }}</span> </li>
                                  <li> <strong>ID: </strong> <span>{{ $data->employee->employee_id }}</span> </li>
                                  <li> <strong>Name: </strong> <span>{{ $data->employee->employee_name }}</span> </li>

                                  @if($data->employee->hourly_employee == 1)
                                    <li> <strong>Type: </strong> <span>{{ $data->employee->employeeType->name }} (Hourly Basis)</span> </li>
                                  @else
                                    <li> <strong>Type: </strong> <span>{{ $data->employee->employeeType->name }}</span> </li>
                                  @endif


                                  <li> <strong>Desig: </strong> <span>{{ $data->employee->category->catg_name }}</span> </li>
                                  <li> <strong>Total Salary Amount: </strong> <span id="TotalSalaryAmount"></span> </li>
                                </ul>
                            </p>
                           
                            
                          </div>
                      </div>
                       <div class="col-md-4">
                            @if ($data->employee->profile_photo != NULL)
                                <img src="{{ asset($data->employee->profile_photo) }}" alt="" class="image-resize">
                            @else
                                <img src="{{ asset('contents/admin') }}/assets/images/logo_icon.png" alt="" class="image-resize"  width="300" height="80">

                            @endif
                       </div>
                   </div>
              </div>
              <div class="card-body card_form">
                <input type="hidden" name="id" value="{{ $data->sdetails_id }}">
                {{-- Conditoinaly Show Input Field --}}
                @if ($data->employee->hourly_employee == 1)
                  <div style="text-align:center">
                      <a class="employee-status-button" href="{{ route('directman-status-update',$data->employee->emp_auto_id) }}">Change to Basic Salary</a>
                  </div>

                  <div class="form-group row custom_form_group{{ $errors->has('hourly_rate') ? ' has-error' : '' }}" >
                      <label class="col-sm-3 control-label" for="basic_hours">Hourly Rate:<span class="req_star">*</span></label>
                      <div class="col-sm-7">
                        <input type="text" class="form-control" name="hourly_rate" value="{{ $data->hourly_rent}}" min="0">
                        @if ($errors->has('hourly_rate'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('hourly_rate') }}</strong>
                            </span>
                        @endif
                      </div>
                  </div>
                  <div class="form-group row custom_form_group{{ $errors->has('food_allowance') ? ' has-error' : '' }}" >
                      <label class="col-sm-3 control-label" for="basic_hours">Food Allowance:<span class="req_star">*</span></label>
                      <div class="col-sm-7">
                        <input type="text" class="form-control" name="food_allowance" value="{{ $data->food_allowance }}" min="0">
                        @if ($errors->has('food_allowance'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('food_allowance') }}</strong>
                            </span>
                        @endif
                      </div>
                  </div>
                  <div class="form-group row custom_form_group{{ $errors->has('others1') ? ' has-error' : '' }}" >
                      <label class="col-sm-3 control-label" for="basic_hours">Others:<span class="req_star">*</span></label>
                      <div class="col-sm-7">
                        <input type="text" class="form-control" name="others1" value="{{ $data->others1 }}" min="0">
                        @if ($errors->has('others1'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('others1') }}</strong>
                            </span>
                        @endif
                      </div>
                  </div>
                  {{-- others field values 0 --}}
                  <input type="hidden" name="basic_amount" value="0">
                  <input type="hidden" name="basic_hours" value="0">
                  <input type="hidden" name="house_rent" value="0">
                  <input type="hidden" name="mobile_allowance" value="0">
                  <input type="hidden" name="medical_allowance" value="0">
                  <input type="hidden" name="local_travel_allowance" value="0">
                  <input type="hidden" name="conveyance_allowance" value="0">

                @else
                  {{-- without hourly basic --}}
                  {{-- Direct Man Power --}}
                  @if($data->employee->emp_type_id == 1)
                    <div style="text-align: center;">
                      <a class="employee-status-button"  href="{{ route('directman-status-update',$data->employee->emp_auto_id) }}">Change To Hourly Salary</a>
                    </div>


                    <div class="form-group row custom_form_group{{ $errors->has('basic_amount') ? ' has-error' : '' }}">
                        <label class="col-sm-3 control-label" for="basic_amount">Basic Salary:<span class="req_star">*</span></label>
                        <div class="col-sm-7">
                          <input type="text" placeholder="Input Amount" class="form-control" id="basic_amount" name="basic_amount" value="{{ $data->basic_amount }}" min="0" onkeyup="hourlyRate()">
                          @if ($errors->has('basic_amount'))
                              <span class="invalid-feedback" role="alert">
                                  <strong>{{ $errors->first('basic_amount') }}</strong>
                              </span>
                          @endif
                        </div>
                    </div>

                    <div class="form-group row custom_form_group{{ $errors->has('basic_hours') ? ' has-error' : '' }}" >
                        <label class="col-sm-3 control-label" for="basic_hours">Basic Hours:<span class="req_star">*</span></label>
                        <div class="col-sm-7">
                          <input type="text" placeholder="Input Basic Hours" class="form-control" id="basic_hours" name="basic_hours" value="{{ $data->basic_hours }}" min="1">
                          @if ($errors->has('basic_hours'))
                              <span class="invalid-feedback" role="alert">
                                  <strong>{{ $errors->first('basic_hours') }}</strong>
                              </span>
                          @endif
                        </div>
                    </div>

                    <div class="form-group row custom_form_group{{ $errors->has('hourly_rate') ? ' has-error' : '' }}" >
                        <label class="col-sm-3 control-label" for="basic_hours">Hourly Rate:<span class="req_star">*</span></label>
                        <div class="col-sm-7">
                          <input type="text" placeholder="Input Hours Rate" class="form-control" id="hourly_rate" name="hourly_rate" value="{{ $data->hourly_rent }}" min="0">
                          {{-- <input type="hidden" name="hourly_rate" id="hourly_rate2" value=""> --}}
                          @if ($errors->has('hourly_rate'))
                              <span class="invalid-feedback" role="alert">
                                  <strong>{{ $errors->first('hourly_rate') }}</strong>
                              </span>
                          @endif
                        </div>
                    </div>

                    <div class="form-group row custom_form_group{{ $errors->has('house_rent') ? ' has-error' : '' }}">
                        <label class="col-sm-3 control-label" for="basic_amount">House Rent:<span class="req_star">*</span></label>
                        <div class="col-sm-7">
                          <input type="text" placeholder="Input Amount" class="form-control" id="house_rent" name="house_rent" value="{{ $data->house_rent }}">
                          @if ($errors->has('house_rent'))
                              <span class="invalid-feedback" role="alert">
                                  <strong>{{ $errors->first('house_rent') }}</strong>
                              </span>
                          @endif
                        </div>
                    </div>

                    <div class="form-group row custom_form_group{{ $errors->has('mobile_allowance') ? ' has-error' : '' }}">
                        <label class="col-sm-3 control-label">Moblie Allowance:</label>
                        <div class="col-sm-7">
                          <input type="text" placeholder="Input Amount" class="form-control" id="mobile_allowance" name="mobile_allowance" value="{{ $data->mobile_allowance }}">
                          @if ($errors->has('mobile_allowance'))
                              <span class="invalid-feedback" role="alert">
                                  <strong>{{ $errors->first('mobile_allowance') }}</strong>
                              </span>
                          @endif
                        </div>
                    </div>

                    <div class="form-group row custom_form_group{{ $errors->has('food_allowance') ? ' has-error' : '' }}" >
                        <label class="col-sm-3 control-label">Food Allowance:<span class="req_star">*</span></label>
                        <div class="col-sm-7">
                          <input type="text" class="form-control" name="food_allowance" value="{{ $data->food_allowance }}" min="0">
                          @if ($errors->has('food_allowance'))
                              <span class="invalid-feedback" role="alert">
                                  <strong>{{ $errors->first('food_allowance') }}</strong>
                              </span>
                          @endif
                        </div>
                    </div>

                    <div class="form-group row custom_form_group{{ $errors->has('others1') ? ' has-error' : '' }}" >
                        <label class="col-sm-3 control-label">Others:<span class="req_star">*</span></label>
                        <div class="col-sm-7">
                          <input type="text" class="form-control" name="others1" value="{{ $data->others1 }}" min="0">
                          @if ($errors->has('others1'))
                              <span class="invalid-feedback" role="alert">
                                  <strong>{{ $errors->first('others1') }}</strong>
                              </span>
                          @endif
                        </div>
                    </div>

                    {{-- others input field valu 0 --}}
                    <input type="hidden" name="medical_allowance" value="0">
                    <input type="hidden" name="conveyance_allowance" value="0">
                    <input type="hidden" name="local_travel_allowance" value="0">
                  @else
                    {{-- Indirect man Power --}}
                    <div class="form-group row custom_form_group{{ $errors->has('basic_amount') ? ' has-error' : '' }}">
                        <label class="col-sm-3 control-label" for="basic_amount">Basic Salary:<span class="req_star">*</span></label>
                        <div class="col-sm-7">
                          <input type="text" placeholder="Input Amount" class="form-control" id="basic_amount" name="basic_amount" value="{{ $data->basic_amount }}" min="0" onkeyup="basicAmount()">
                          @if ($errors->has('basic_amount'))
                              <span class="invalid-feedback" role="alert">
                                  <strong>{{ $errors->first('basic_amount') }}</strong>
                              </span>
                          @endif
                        </div>
                    </div>

                    <div class="form-group row custom_form_group{{ $errors->has('hourly_rate') ? ' has-error' : '' }}" >
                        <label class="col-sm-3 control-label" for="basic_hours">Hourly Rate:<span class="req_star">*</span></label>
                        <div class="col-sm-7">
                          <input type="text" placeholder="Input Hours Rate" class="form-control" id="hourly_rate" name="hourly_rate" value="{{ $data->hourly_rent }}" min="0">
                          {{-- <input type="hidden" name="hourly_rate" id="hourly_rate2" value=""> --}}
                          @if ($errors->has('hourly_rate'))
                              <span class="invalid-feedback" role="alert">
                                  <strong>{{ $errors->first('hourly_rate') }}</strong>
                              </span>
                          @endif
                        </div>
                    </div>

                    {{-- hidden field --}}
                    <input type="hidden" id="tempbasicamount" value="">
                    <input type="hidden" id="temphouseamount" value="">
                    <input type="hidden" id="tempmobileallowance" value="">
                    <input type="hidden" id="templocalallowance" value="">
                    <input type="hidden" id="tempfoodAllowance" value="">
                    {{-- hidden field --}}

                    <div class="form-group row custom_form_group{{ $errors->has('house_rent') ? ' has-error' : '' }}" >
                        <label class="col-sm-3 control-label" for="basic_hours">House Rent:<span class="req_star">*</span></label>
                        <div class="col-sm-7">
                          <input type="text" class="form-control" id="house_rent" name="house_rent" value="{{ $data->house_rent }}" min="0" onkeyup="houseAmount()">
                          @if ($errors->has('house_rent'))
                              <span class="invalid-feedback" role="alert">
                                  <strong>{{ $errors->first('hourly_rate') }}</strong>
                              </span>
                          @endif
                        </div>
                    </div>

                    <div class="form-group row custom_form_group{{ $errors->has('mobile_allowance') ? ' has-error' : '' }}" >
                        <label class="col-sm-3 control-label" for="basic_hours">Mobile Allowance:<span class="req_star">*</span></label>
                        <div class="col-sm-7">
                          <input type="text" class="form-control" name="mobile_allowance" id="mobile_allowance" value="{{ $data->mobile_allowance }}" min="0" onkeyup="mobileAllowance()">
                          @if ($errors->has('mobile_allowance'))
                              <span class="invalid-feedback" role="alert">
                                  <strong>{{ $errors->first('mobile_allowance') }}</strong>
                              </span>
                          @endif
                        </div>
                    </div>

                    <div class="form-group row custom_form_group{{ $errors->has('local_travel_allowance') ? ' has-error' : '' }}" >
                        <label class="col-sm-3 control-label" for="basic_hours">Local Travels Allowance:<span class="req_star">*</span></label>
                        <div class="col-sm-7">
                          <input type="text" class="form-control" name="local_travel_allowance" id="local_travel_allowance" value="{{ $data->local_travel_allowance }}" min="0" onkeyup="localTravel()">
                          @if ($errors->has('local_travel_allowance'))
                              <span class="invalid-feedback" role="alert">
                                  <strong>{{ $errors->first('local_travel_allowance') }}</strong>
                              </span>
                          @endif
                        </div>
                    </div>

                    <div class="form-group row custom_form_group{{ $errors->has('food_allowance') ? ' has-error' : '' }}" >
                        <label class="col-sm-3 control-label">Food Allowance:<span class="req_star">*</span></label>
                        <div class="col-sm-7">
                          <input type="text" class="form-control" name="food_allowance" id="food_allowance" value="{{ $data->food_allowance }}" min="0" onkeyup="foodAllowance()">
                          @if ($errors->has('food_allowance'))
                              <span class="invalid-feedback" role="alert">
                                  <strong>{{ $errors->first('food_allowance') }}</strong>
                              </span>
                          @endif
                        </div>
                    </div>

                    <div class="form-group row custom_form_group{{ $errors->has('others1') ? ' has-error' : '' }}" >
                        <label class="col-sm-3 control-label">Others:<span class="req_star">*</span></label>
                        <div class="col-sm-7">
                          <input type="text" class="form-control" name="others1" id="others1" value="{{ $data->others1 }}" min="0" onkeyup="othersAmount()">
                          @if ($errors->has('others1'))
                              <span class="invalid-feedback" role="alert">
                                  <strong>{{ $errors->first('others1') }}</strong>
                              </span>
                          @endif
                        </div>
                    </div>
                    {{-- others input field valu 0 --}}
                    <input type="hidden" name="basic_hours" value="0">
                    <input type="hidden" name="medical_allowance" value="0">
                    <input type="hidden" name="conveyance_allowance" value="0">
                  @endif
                {{-- main endif --}}
                @endif
                {{-- end main endif --}}
              </div>
              {{-- Conditoinaly Show Input Field --}}
              @if($data->employee->emp_type_id == 1)
                <input type="hidden" id="employeeType" value="1">
              @else
                <input type="hidden" id="employeeType" value="2">
              @endif
              {{-- Conditoinaly Show Input Field --}}
              <div class="card-footer card_footer_button text-center">
                  <button type="submit" id="onSubmit" onclick="formValidation();" class="btn btn-primary waves-effect">UPDATE</button>
              </div>
          </div>
        </form>
    </div>
</div>
<!-- script area -->
<script type="text/javascript">
    function basicAmount(){
      let basic_amount = $("#basic_amount").val();
      /* -- store -- */
      $('span[id="TotalSalaryAmount"]').text(basic_amount);
      $('input[id="tempbasicamount"]').val(basic_amount);
    }
    /* ==================  ================== */
    function houseAmount(){
      let basic_amount =  parseFloat( $("#tempbasicamount").val() );
      let house_rent = parseFloat( $("#house_rent").val() );

      if(house_rent >= 0){
          var total_amount = (basic_amount + house_rent);
          $('span[id="TotalSalaryAmount"]').text('');
          $('span[id="TotalSalaryAmount"]').text(total_amount);
          $("#temphouseamount").val(total_amount);

      }else{
        $('span[id="TotalSalaryAmount"]').text(basic_amount);
        $("#temphouseamount").val(house_rent);
      }

    }
    /* ==================  ================== */
    function mobileAllowance(){
      let temphouseamount =  parseFloat( $("#temphouseamount").val() );
      let mobile_allowance = parseFloat( $("#mobile_allowance").val() );

      if(mobile_allowance >= 0){
          var total_amount = (temphouseamount + mobile_allowance);
          $('span[id="TotalSalaryAmount"]').text('');
          $('span[id="TotalSalaryAmount"]').text(total_amount);
          $("#tempmobileallowance").val(total_amount);

      }else{
        $('span[id="TotalSalaryAmount"]').text(temphouseamount);
        $("#tempmobileallowance").val(mobile_allowance);
      }

    }
    /* ==================  ================== */
    function localTravel(){
      let tempmobileallowance =  parseFloat( $("#tempmobileallowance").val() );
      let local_travel_allowance = parseFloat( $("#local_travel_allowance").val() );

      if(local_travel_allowance >= 0){
          var total_amount = (tempmobileallowance + local_travel_allowance);
          $('span[id="TotalSalaryAmount"]').text('');
          $('span[id="TotalSalaryAmount"]').text(total_amount);
          $("#templocalallowance").val(total_amount);

      }else{
        $('span[id="TotalSalaryAmount"]').text(tempmobileallowance);
        $("#templocalallowance").val(local_travel_allowance);
      }

    }
    /* ==================  ================== */
    function foodAllowance(){
      let templocalallowance =  parseFloat( $("#templocalallowance").val() );
      let food_allowance = parseFloat( $("#food_allowance").val() );

      if(food_allowance >= 0){
          var total_amount = (templocalallowance + food_allowance);
          $('span[id="TotalSalaryAmount"]').text('');
          $('span[id="TotalSalaryAmount"]').text(total_amount);
          $("#tempfoodAllowance").val(total_amount);

      }else{
        $('span[id="TotalSalaryAmount"]').text(templocalallowance);
        $("#tempfoodAllowance").val(food_allowance);
      }

    }
    /* ==================  ================== */
    function othersAmount(){
      let tempfoodAllowance =  parseFloat( $("#tempfoodAllowance").val() );
      let others1 = parseFloat( $("#others1").val() );

      if(others1 >= 0){
          var total_amount = (tempfoodAllowance + others1);
          $('span[id="TotalSalaryAmount"]').text('');
          $('span[id="TotalSalaryAmount"]').text(total_amount);

      }else{
        $('span[id="TotalSalaryAmount"]').text(tempfoodAllowance);
      }

    }


    function Calculation(){
      var basic_amount = parseInt( $("#basic_amount").val() );
      var basic_hours = parseInt( $("#basic_hours").val() );
      if(basic_amount !=0 && basic_hours !=0){
        var result = Math.round(basic_amount / basic_hours);
        $('#hourly_rate').val(result);
      }else{
        $('#hourly_rate').val(0);
      }
    }

    /* ===== Hourly Rate ===== */
    // function hourlyRate(){
    //   var basic_amount = parseInt( $("#basic_amount").val() );
    //
    //   var employeeType = parseInt( $("#employeeType").val() );
    //   if(employeeType == 1){
    //     var hourlyRate =  Math.round( ( (basic_amount / 30) / 10 ) );
    //     $("#hourly_rate").val(hourlyRate);
    //     $("#hourly_rate2").val(hourlyRate);
    //   }else{
    //     var hourlyRate = Math.round( ( (basic_amount / 30) / 10));
    //     $("#hourly_rate").val(hourlyRate);
    //     $("#hourly_rate2").val(hourlyRate);
    //   }
    // }

</script>


<!-- form validation -->
<script type="text/javascript">
  $(document).ready(function() {
    $("#salary-details-form").validate({
      rules: {
        basic_amount : {
          required: true,
          // number: true,
        },

        basic_hours : {
          required: true,
          // number: true,
        },

      },
      messages : {
        basic_amount: {
          required : "You Must Be Input This Field!",
          // number : "Invalid Amount!",
        },

        basic_hours: {
          required : "You Must Be Input This Field!",
          // number : "Invalid Amount!",
        }


      }
    });
  });
</script>
@endsection
