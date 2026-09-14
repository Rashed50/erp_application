@extends('layouts.admin-master')
@section('title') New Employee @endsection
@section('content')

<div class="row bread_part">
  <div class="col-sm-12 bread_col">
    <h4 class="pull-left page-title bread_title">Employee Information</h4>
    <ol class="breadcrumb pull-right">
      <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
         <li class="active">Add</li>
    </ol>
  </div>
</div>
<div class="row">
  <div class="col-lg-12">
      <!-- step bar indigator -->
        <div class="row">
            <div class="col-md-12">
                <article class="card">
                    <div class="card-body">
                        <div class="track">
                            <div class="step active">
                                <span class="icon"><i class="fa fa-check"></i> </span>
                                <span class="text">First Step</span>
                            </div>
                            <div class="step">
                                <span class="icon"><i class="fa fa-check"></i> </span>
                                <span class="text">Second Step</span>
                            </div>
                        </div>
                    </div>
                </article>
            </div>
        </div>
        <!-- Session Flash -->
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
        <!-- New Employee Information User Interface -->

    <form class="form-horizontal" id="employee-info-form" method="post" action="{{ route('employee-insert') }}" enctype="multipart/form-data" onsubmit="next_button.disabled=true;" >
      @csrf
      <div class="card">
        <div class="card-body card_form">
              <div class="row custom_form_group">
                        <label class="col-sm-3 control-label">Employee for:<span class="req_star">*</span></label>
                        <div class="col-sm-7">
                        <input type="hidden" class="form-control" id="employee_hidden_Id"
                                value="{{ $empIdGeneret }}" >
                                <select class="form-select" id ="company_id" name="company_id">
                                    <option value="1">Asloob International Contracting Company</option>
                                    <option value="2">Asloob Bedda Contracting Company </option>
                                    <option value="3">Bedaa General Contracting Company</option>
                                    <option value="4">Other Employee</option>
                                </select>

                        </div>
                </div>

             <div class="form-group row custom_form_group">
                        <label class="col-sm-3 control-label">Employee ID:<span class="req_star">*</span></label>
                        <div class="col-sm-7">
                            <input type="text" readonly class="form-control" id="employeeId" name="emp_id"
                                value="{{ $empIdGeneret }}" placeholder="Employee Id " style="font-weight:bold; font-size:16px;"
                                 onfocusout="checkEmployeeId()" required>
                            <span id="checkUniqueId" class="d-none error">This ID Already Exist!</span>
                        </div>


          </div>

          <div class="form-group row custom_form_group{{ $errors->has('emp_name') ? ' has-error' : '' }}">
            <label class="col-sm-3 control-label">Name:<span class="req_star">*</span></label>
            <div class="col-sm-7">
              <input type="text" placeholder="Input Employee Name Here" class="form-control" id="emp_name" name="emp_name" value="{{old('emp_name')}}" autofocus required>
              @if ($errors->has('emp_name'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('emp_name') }}</strong>
              </span>
              @endif
              <div id="showerror1"></div>
            </div>
          </div>

          <div class="form-group row custom_form_group{{ $errors->has('agc_info_auto_id') ? ' has-error' : '' }}">
            <label class="col-sm-3 control-label"> Agency Name:<span class="req_star">*</span></label>
            <div class="col-sm-7">
              <select class="form-select" name="agency" required>
                <option value="">Select Agency Name</option>
                    @foreach($agencies as $agency)
                        @if( old('agency') == $agency->agc_info_auto_id )
                        <option value="{{ $agency->agc_info_auto_id }}" selected>{{ $agency->agc_title }}</option>
                        @else
                        <option value="{{ $agency->agc_info_auto_id }}">{{ $agency->agc_title }}</option>
                        @endif
                    @endforeach
              </select>
              @if ($errors->has('agency'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('agency') }}</strong>
              </span>
              @endif
            </div>
          </div>

          <div class="form-group row custom_form_group{{ $errors->has('sponsor_id') ? ' has-error' : '' }}">
            <label class="col-sm-3 control-label"> Sponsor Name:<span class="req_star">*</span></label>
            <div class="col-sm-7">
              <select class="form-select" name="sponsor_id" required>
                <option value="">Select Sponsor Name</option>
                @foreach($sponsor as $spon)
                @if( old('sponsor_id') == $spon->spons_id )
                <option value="{{ $spon->spons_id }}" selected>{{ $spon->spons_name }}</option>
                @else
                <option value="{{ $spon->spons_id }}">{{ $spon->spons_name }}</option>
                @endif
                @endforeach
              </select>
              @if ($errors->has('sponsor_id'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('sponsor_id') }}</strong>
              </span>
              @endif
            </div>
          </div>


          <div class="form-group row custom_form_group{{ $errors->has('passfort_no') ? ' has-error' : '' }}">
            <label class="col-sm-3 control-label">Passport No:<span class="req_star">*</span></label>
            <div class="col-sm-7">
              <input type="text" placeholder="Input Passport Number Here" class="form-control"  id="passfort_no" name="passfort_no" value="{{old('passfort_no')}}" onfocusout="checkThisEmployeePassportNumber()" required >
                 <span id="uniquePassportErrorMsg" class="d-none error">This Passport Number Already Exist!</span>
              @if ($errors->has('passfort_no'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('passfort_no') }}</strong>
              </span>
              @endif
            </div>
          </div>

          <div class="form-group row custom_form_group{{ $errors->has('passfort_expire_date') ? ' has-error' : '' }}">
            <label class="col-sm-3 control-label">Passport Expire Date:<span class="req_star">*</span></label>
            <div class="col-sm-7">
              <input type="date" class="form-control" name="passfort_expire_date" value="{{ Carbon\Carbon::now()->format('Y-m-d') }}" min="{{ Carbon\Carbon::now()->format('Y-m-d') }}">
              @if ($errors->has('passfort_expire_date'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('passfort_expire_date') }}</strong>
              </span>
              @endif
            </div>
          </div>

          <div class="form-group row custom_form_group{{ $errors->has('akama_no') ? ' has-error' : '' }}">
            <label class="col-sm-3 control-label">Iqama No:<span class="req_star">*</span></label>
            <div class="col-sm-7">
              <input type="number" placeholder="Input Iqama Number Here" class="form-control" id="akama_no" name="akama_no" value="{{old('akama_no')}}" onfocusout="checkThisEmployeeIqamaNumber()" required >
                                <span id="uniqueIqamaErrorMsg" class="d-none error">This Iqama Number Already Exist!</span>
              @if ($errors->has('akama_no'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('akama_no') }}</strong>
              </span>
              @endif
            </div>
          </div>
          <div class="form-group row custom_form_group{{ $errors->has('akama_expire') ? ' has-error' : '' }}">
            <label class="col-sm-3 control-label">Iqama Expire Date:<span class="req_star">*</span></label>
            <div class="col-sm-7">
              <input type="date" class="form-control" name="akama_expire"  value="{{ date('Y-m-d') }}"
                                min="{{ date('Y-m-d') }}" >
              @if ($errors->has('akama_expire'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('akama_expire') }}</strong>
              </span>
              @endif
            </div>
          </div>

           <div class="form-group row custom_form_group{{ $errors->has('accomd_ofb_id') ? ' has-error' : '' }}">
                        <label class="col-sm-3 control-label"> Villa Name</label>
                        <div class="col-sm-7">
                            <select class="form-select" name="accomd_ofb_id">
                                <option value="">Select Villa Name</option>
                                @foreach($accomdOfficeBuilding as $officeBuilding)
                                    @if( old('accomd_ofb_id') == $officeBuilding->ofb_id )
                                    <option value="{{ $officeBuilding->ofb_id }}" selected>{{ $officeBuilding->ofb_name }} - {{ $officeBuilding->ofb_city_name }}</option>
                                    @else
                                    <option value="{{ $officeBuilding->ofb_id }}">{{ $officeBuilding->ofb_name }} - {{ $officeBuilding->ofb_city_name }}</option>
                                    @endif
                                @endforeach
                            </select>
                            @if ($errors->has('accomd_ofb_id'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('accomd_ofb_id') }}</strong>
                            </span>
                            @endif
                        </div>
            </div>

          <div class="form-group row custom_form_group{{ $errors->has('mobile_no') ? ' has-error' : '' }}">
            <label class="col-sm-3 control-label">Abshar Mobile No:<span class="req_star">*</span></label>
            <div class="col-sm-7">
              <input type="number" placeholder="Input Mobile Number" class="form-control"  id="mobile_no" name="mobile_no" value="{{ old('mobile_no') }}" required>
              @if ($errors->has('mobile_no'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('mobile_no') }}</strong>
              </span>
              @endif
            </div>
          </div>

          <div class="form-group row custom_form_group">
            <label class="col-sm-3 control-label">Mobile Number2</label>
            <div class="col-sm-7">
              <input type="number" placeholder="Input Phone Number" class="form-control" name="phone_no" value="{{ old('phone_no') }}">
            </div>
          </div>

            <div class="form-group row custom_form_group">
                    <label class="col-sm-3 control-label">Home Country Contact No:</label>
                        <div class="col-sm-7">
                            <input type="number" placeholder="Home Country Contact Number" class="form-control" name="country_phone_no"
                                value="{{ old('country_phone_no') }}">

                    </div>
            </div>

          <div class="form-group row custom_form_group">
            <label class="col-sm-3 control-label">Email:</label>
            <div class="col-sm-7">
              <input type="email" placeholder="Input Email Address" class="form-control" id="email" name="email" value="{{ old('email') }}" onfocusout="checkThisEmployeeEmail()" >
                  <span id="uniqueEmailErrorMsg" class="d-none error">This Email Already Exist!</span>
            </div>
          </div>

          <div class="row custom_form_group">
            <label class="col-sm-3 control-label">Employee Type:<span class="req_star">*</span></label>
            <div class="col-sm-7">
              <div class="form{{ $errors->has('emp_type_id') ? ' has-error' : '' }}">
                <select class="form-select" name="emp_type_id" name="emp_type_id" required>
                  <option value="">Select Employee Type</option>
                  @foreach($empTypes as $emp)
                  <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>
          </div>

          <div class="row custom_form_group">
            <label class="col-sm-3 control-label">Designation:<span class="req_star">*</span></label>
            <div class="col-sm-7">
              <div class="form{{ $errors->has('designation_id') ? ' has-error' : '' }}">
                <select class="form-select" name="designation_id">
                  <option value="">Select Designation</option>
                  @foreach($empTypes as $emp)
                  <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>
          </div>


          <div id="hourlyEmployee" class=" d-none">
            <div class="row custom_form_group">
              <label class="col-sm-3 control-label">Hourly Basic:<span class="req_star">*</span></label>
              <div class="col-sm-7">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" value="1" name="hourly_employee" id="flexCheckDefault">
                  <label class="form-check-label" for="flexCheckDefault" style="font-size:13px; font-weight:400">
                    Hourly Basic Employee
                  </label>
                </div>
              </div>
            </div>
          </div>



          <div class="form-group row custom_form_group">
            <label class="col-sm-3 control-label">Present Address:</label>
            <div class="col-sm-7">
              <textarea name="present_address" class="form-control" value="{{old('present_address')}}" placeholder="Input Present Address Here">{{old('present_address')}}</textarea>
            </div>
          </div>

          <div class="row custom_form_group">
            <label class="col-sm-3 control-label">Permanent Address:<span class="req_star">*</span></label>
            <div class="col-sm-7">
              <div class="parmanent_address">
                <!-- country -->
                <div class="form-group">
                  <select class="form-select" name="country_id">
                    <option value="">Select Country</option>
                    @foreach($countryList as $country)
                    <option value="{{ $country->id }}"> {{ $country->country_name }}</option>
                    @endforeach
                  </select>
                </div>
                <!-- division -->
                <div class="form-group">
                  <select class="form-select" name="division_id">
                    <option value="">Select Division</option>
                  </select>
                </div>

                <div class="form-group">
                  <select class="form-select" name="district_id">
                    <option value="">Select District</option>
                  </select>
                </div>
                <div class="form-group">
                  <input type="text" class="form-control" value="{{ old('post_code') }}" id="post_code" name="post_code" placeholder="Input Post Code">
                </div>
                <div class="form-group">
                  <textarea class="form-control" id="details" name="details" placeholder="Input Address Details">{{ old('details') }}</textarea>
                </div>

              </div>
            </div>
          </div>

          <div class="form-group row custom_form_group">
            <label class="col-sm-3 control-label">Select Project:</label>
            <div class="col-sm-7">
              <select class="form-select"  id="project_id" name="project_id" >
                <option value="">Select Here</option>
                @foreach($proj as $projInfo)
                @if( old('project_id') == $projInfo->proj_id )
                <option value="{{ $projInfo->proj_id }}" selected>{{ $projInfo->proj_name }}</option>
                @else
                <option value="{{ $projInfo->proj_id }}">{{ $projInfo->proj_name }}</option>
                @endif
                @endforeach
              </select>
            </div>
          </div>



          <div class="form-group row custom_form_group">
            <label class="col-sm-3 control-label">Select Department:</label>
            <div class="col-sm-7">
              <select class="form-select" id="department_id" name="department_id" required>
                @foreach($allDepart as $depart)
                @if (old('department_id') == $depart->dep_id)
                <option value="{{ $depart->dep_id }}" selected>{{ $depart->dep_name }}</option>
                @else
                <option value="{{ $depart->dep_id }}">{{ $depart->dep_name }}</option>
                @endif
                @endforeach
              </select>
            </div>
          </div>

          <div class="form-group row custom_form_group">
            <label class="col-sm-3 control-label">Date Of Birth:</label>
            <div class="col-sm-7">
              <input type="date" class="form-control" name="date_of_birth"  value="{{ date('Y-m-d',strtotime('-6575 days')) }}"
                                max="{{date('Y-m-d',strtotime('-6575 days')) }}" >
            </div>
          </div>

                    <div class="form-group row custom_form_group">
                        <label class="col-sm-3 control-label">Blood Group:</label>
                        <div class="col-sm-7">
                            <select class="form-select" name="blood_group">
                                 <option value="A+">A+</option>
                                <option value="A-">A-</option>
                                <option value="B+">B+</option>
                                <option value="B-">B-</option>
                                <option value="O+">O+</option>
                                <option value="O-">O-</option>
                                <option value="AB+">AB+</option>
                                <option value="AB-">AB-</option>
                            </select>
                        </div>
                    </div>


                    <div class="form-group row custom_form_group">
                        <label class="col-sm-3 control-label">Marital Status:</label>
                        <div class="col-sm-7">
                            <select class="form-select" name="maritus_status">
                                <option value="0">Unmarried</option>
                                <option value="1">Married</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row custom_form_group">
                        <label class="col-sm-3 control-label">Gender:</label>
                        <div class="col-sm-7">
                            <select class="form-select" name="gender" id="gender">
                                <option value="M">Male</option>
                                <option value="F">Female</option>
                            </select>
                        </div>

                    </div>

                    <div class="form-group row custom_form_group">
                        <label class="col-sm-3 control-label">Religion:</label>
                        <div class="col-sm-7">
                            <select class="form-select" name="religion">
                                <option value="1">Muslim</option>
                                <option value="3">Hinduism</option>
                                <option value="2">Christianity</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row custom_form_group">
                        <label class="col-sm-3 control-label">Reference Person</label>
                        <div class="col-sm-7">
                            <input type="number" placeholder="Input Reference Name or Employee ID" class="form-control" name="ref_employee_id"  value="{{ old('ref_employee_id') }}">
                        </div>
                    </div>

                    <div class="form-group row custom_form_group">
                        <label class="col-sm-3 control-label">Remarks</label>
                        <div class="col-sm-7">
                            <input type="text" placeholder="Input Remarks Here" class="form-control" name="remarks" value="{{ old('remarks') }}">
                        </div>
                    </div>

          <div class="form-group row custom_form_group">
            <label class="col-sm-3 control-label">Appointment Date:</label>
            <div class="col-sm-7">

              <input type="date" class="form-control" id ="txtdate" name="appointment_date"  value="{{ date('Y-m-d') }}"
                                max="{{date('Y-m-d') }}" >

            </div>
          </div>

          <div class="form-group row custom_form_group">
            <label class="col-sm-3 control-label">Joining Date:</label>
            <div class="col-sm-7">
              <input type="date" name="joining_date" class="form-control" value="{{ date('Y-m-d') }}" >
            </div>
          </div>

          <div class="form-group row custom_form_group">
            <label class="col-sm-3 control-label">Confirmation Date:</label>
            <div class="col-sm-7">
              <input type="date" name="confirmation_date" value="{{ date('Y-m-d') }}"  max="{{date('Y-m-d') }}" class="form-control" >

            </div>
          </div>


          <div class="form-group row custom_form_group">
            <label class="col-sm-3 control-label">Passport Photo:</label>
            <div class="col-sm-4">
              <div class="input-group passfortFiles">
                <span class="input-group-btn ">
                  <span class="btn btn-default btn-file btnu_browse ">
                    Browse… <input type="file" q name="pasfort_photo" id="imgInp">
                  </span>
                </span>
                <input type="text" class="form-control" readonly>
              </div>
            </div>
            <div class="col-sm-3">
              <img id='img-upload' class="upload_image" />
            </div>
          </div>

          <div class="form-group row custom_form_group">
            <label class="col-sm-3 control-label">Profile Photo:</label>
            <div class="col-sm-4">
              <div class="input-group">
                <span class="input-group-btn">
                  <span class="btn btn-default btn-file btnu_browse">
                    Browse… <input type="file" name="profile_photo" id="imgInp4">
                  </span>
                </span>
                <input type="text" class="form-control" readonly>
              </div>
            </div>
            <div class="col-sm-3">
              <img id='img-upload4' class="upload_image" />
            </div>
          </div>

          <div class="form-group row custom_form_group">
            <label class="col-sm-3 control-label">Iqama Photo:</label>
            <div class="col-sm-4">
              <div class="input-group">
                <span class="input-group-btn">
                  <span class="btn btn-default btn-file btnu_browse">
                    Browse… <input type="file"  name="akama_photo" id="imgInp3">
                  </span>
                </span>
                <input type="text" class="form-control" readonly>
              </div>
            </div>
            <div class="col-sm-3">
              <img id='img-upload3' class="upload_image" />
            </div>
          </div>

          <div class="form-group row custom_form_group">
            <label class="col-sm-3 control-label">Covid Report:</label>
            <div class="col-sm-4">
              <div class="input-group">
                <span class="input-group-btn">
                  <span class="btn btn-default btn-file btnu_browse">
                    Browse… <input type="file" name="covid_certificate" id="imgInp2">
                  </span>
                </span>
                <input type="text" class="form-control" readonly>
              </div>
            </div>
            <div class="col-sm-3">
              <img id='img-upload2' class="upload_image" />
            </div>
          </div>

          <div class="form-group row custom_form_group">
            <label class="col-sm-3 control-label">Appointment Latter:</label>
            <div class="col-sm-4">
              <div class="input-group">
                <span class="input-group-btn">
                  <span class="btn btn-default btn-file btnu_browse">
                    Browse… <input type="file" name="appoint_latter" id="imgInp8">
                  </span>
                </span>
                <input type="text" class="form-control" readonly>
              </div>
            </div>
            <div class="col-sm-3">
              <img id='img-upload8' class="upload_image" />
            </div>
          </div>

            <div class="form-group row custom_form_group">
                        <label class="col-sm-3 control-label">Educational Documents:</label>
                        <div class="col-sm-4">
                            <div class="input-group">
                                <span class="input-group-btn">
                                    <span class="btn btn-default btn-file btnu_browse">
                                        Browse… <input type="file" name="educational_papers" id="imgInp8">
                                    </span>
                                </span>
                                <input type="text" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <img id='img-upload8' class="upload_image" />
                        </div>
                    </div>


          <style>
          /*  #my_camera {*/
          /*    border: 2px solid gray;*/
          /*  }*/
          /*
          </style>
          <!--<div class="row" id="hide">-->

          <!--  <div class="col-md-3">-->
          <!--    <div class="my_camera" id="my_camera"></div>-->
          <!--  </div>-->

          <!--  <div class="col-md-3">-->
          <!--    <input type=button value="Take Snapshot" onClick="take_snapshot()">-->
          <!--    <input type=button value="Off" class="" id="off">-->
          <!--    <input type=button class="d-none" id="on" value="on">-->
          <!--    <input type="hidden" name="image" class="image-tag">-->
          <!--  </div>-->

          <!--  <div class="col-md-1"></div>-->
          <!--  <div class="col-md-3">-->
          <!--    <div id="results">Your captured image</div>-->
          <!--  </div>-->

          <!--  <div class="col-md-2"></div>-->
          <!--</div>-->

        </div>
        <div class="card-footer card_footer_button text-center">
          <button type="submit" id="next_button"  onclick="formValidation();" class="btn btn-primary waves-effect">NEXT</button>
        </div>
      </div>
    </form>
  </div>
</div>


<!-- form validation -->
<script type="text/javascript">
  /*date picker */

//   $(document).ready(function() {

//     $('#txtDate').datepicker({
//       dateFormat: 'mm/dd/yy'
//     });
//     $('#txtDate').datepicker('setDate', 'today');
//   });


  $(document).ready(function() {
    // $("#employee-info-form").validate({
    //   /* form tag off  */
    //   // submitHandler: function(form) { return false; },
    //   /* form tag off  */
    //   rules: {
    //     emp_id: {
    //       required: true,
    //     },
    //     emp_type_id: {
    //       required: true,
    //     },
    //     emp_name: {
    //       required: true,
    //     },
    //     passfort_no: {
    //       required: true,
    //       // number: true,
    //       maxlength: 15,
    //     },
    //     passfort_expire_date: {
    //       required: true,
    //     },
    //     sponsor_id: {
    //       required: true,
    //     },
    //     akama_expire: {
    //       required: true,
    //     },
    //     mobile_no: {
    //       required: true,
    //     },
    //     akama_no: {
    //       required: true,
    //       number: true,

    //       maxlength: 10,
    //       minlength: 10,
    //     },
    //     country_id: {
    //       required: true,
    //     },
    //     division_id: {
    //       required: true,
    //     },
    //     district_id: {
    //       required: true,
    //     },
    //      department_id: {
    //                 required: true,
    //             },
    //   },

    //   messages: {
    //     emp_id: {
    //       required: "You Must Be Input This Field!",
    //     },
    //     emp_name: {
    //       required: "You Must Be Input This Field!",
    //     },
    //     passfort_expire_date: {
    //       required: "You Must Be Select This Field!",
    //     },
    //     emp_type_id: {
    //       required: "You Must Be Select This Field!",
    //     },
    //     sponsor_id: {
    //       required: "You Must Be Select This Field!",
    //     },
    //     akama_expire: {
    //       required: "You Must Be Select This Field!",
    //     },
    //     passfort_no: {
    //       required: "Please Input This Field!",
    //       number: "You Must Be Input Number!",
    //       max: "You Must Be Input Maximum Length 15!",
    //     },
    //     akama_no: {
    //       required: "Please Input This Field!",
    //       number: "You Must Be Input Number Only",
    //       max: "You Must Be Input Maximum Length 10!",
    //     },
    //     department_id: {
    //                 required: "You Must Be Select Any Of This Field!",
    //             },
    //   },
    // });

        $("#employee-info-form").validate({

              rules: {
                emp_id: {
                  required: true,
                },
                emp_name: {
                  required: true,
                },
                passfort_no: {
                  required: true,
                  maxlength: 15,
                },
                passfort_expire_date: {
                  required: true,
                },
                akama_no: {
                  required: true,
                  number: true,
                  maxlength: 10,
                  minlength: 10,
                },
                akama_expire: {
                  required: true,
                },
                sponsor_id: {
                  required: true,
                },
                mobile_no: {
                  required: true,
                },
                country_id: {
                  required: true,
                },
                division_id: {
                  required: true,
                },
                district_id: {
                  required: true,
                },
                emp_type_id: {
                  required: true,
                },
                company_id: {
                  required: true,
                },
                 department_id: {
                            required: true,
                        },
              },

              messages: {
                emp_id: {
                  required: "You Must Be Input This Field!",
                },
                emp_name: {
                  required: "You Must Be Input This Field!",
                },
                passfort_no: {
                  required: "Please Input This Field!",
                  number: "You Must Be Input Number!",
                  max: "You Must Be Input Maximum Length 15!",
                },
                passfort_expire_date: {
                  required: "You Must Be Select This Field!",
                },
                akama_no: {
                  required: "Please Input This Field!",
                  number: "You Must Be Input Number Only",
                  max: "You Must Be Input Maximum Length 10!",
                },
                akama_expire: {
                  required: "You Must Be Select This Field!",
                },

                sponsor_id: {
                  required: "You Must Be Select This Field!",
                },

                mobile_no: {
                  required: "You Must Be Select This Field!",
                },

                country_id: {
                  required: "You Must Be Select This Field!",
                },
                division_id: {
                  required: "You Must Be Select This Field!",
                },
                district_id: {
                  required: "You Must Be Select This Field!",
                },
                emp_type_id: {
                  required: "You Must Be Select This Field!",
                },
                company_id: {
                  required: "You Must Be Select This Field!",
                },
                department_id: {
                            required: "You Must Be Select Any Of This Field!",
                        },
              },


        });

  });



    $("#company_id").change(function(){

             var comId = this.value;
            var emp_type = 1; // company sponsor
            if(comId == 4){
              emp_type = 2 ;// Other Sponsor
            }
             $.ajax({

                type: "GET",
                url: "{{ route('search.new.employee.unique.employee.id') }}",
                data: {
                    new_emp_type: emp_type,
                 },
                dataType: "json",
                success: function (response) {

                  $('#employeeId').val(response.data);
                },
                error:function(error){

                }
            });


        });
        // end dropdown change event

    // Make Employee Name Upper Case
    $('#emp_name').keyup(function() {
                this.value = this.value.toLocaleUpperCase();
        });

    // Make passport_no Upper Case
    $('#passfort_no').keyup(function() {
                this.value = this.value.toLocaleUpperCase();
        });


    // make email is lower case
    $('#email').keyup(function() {
                this.value = this.value.toLocaleLowerCase();
        });

    function checkEmployeeId(){
        var empId = $('#employeeId').val();
        checkingThisEmployeeEmpIdPassportIqamaAndEmail(empId,'employee_id');
    }
    function checkThisEmployeePassportNumber(){
        var passport_no = $('#passfort_no').val();
        checkingThisEmployeeEmpIdPassportIqamaAndEmail(passport_no,'passfort_no');
    }
    function checkThisEmployeeIqamaNumber(){
        var akama_no = $('#akama_no').val();
         checkingThisEmployeeEmpIdPassportIqamaAndEmail(akama_no,'akama_no');
    }
    function checkThisEmployeeEmail(){
        var email = $('#email').val();
        if(email.length > 0){
            checkingThisEmployeeEmpIdPassportIqamaAndEmail(email,'email');
        }
    }

    function checkingThisEmployeeEmpIdPassportIqamaAndEmail(db_value, dbcolum_name) {

        $.ajax({
            type: "POST",
            url: "{{ route('checked-employee.id') }}",
            data: {
                value: db_value,
                dbcolum_name:dbcolum_name
            },
            dataType: "json",
            success: function (response) {
                if ( response.status == 200) {
                    if(dbcolum_name == "employee_id"){
                        $('span[id="checkUniqueId"]').removeClass('d-none').addClass('d-block');
                    }
                    else if(dbcolum_name == "passfort_no"){
                        $('span[id="uniquePassportErrorMsg"]').removeClass('d-none').addClass('d-block');
                    }
                    else if(dbcolum_name == "akama_no"){
                        $('span[id="uniqueIqamaErrorMsg"]').removeClass('d-none').addClass('d-block');
                    }
                    else if(dbcolum_name == "email"){
                        $('span[id="uniqueEmailErrorMsg"]').removeClass('d-none').addClass('d-block');
                    }

                } else {
                    if(dbcolum_name == "employee_id"){
                        $('span[id="checkUniqueId"]').removeClass('d-block').addClass('d-none');
                    }
                    else if(dbcolum_name == "passfort_no"){
                        $('span[id="uniquePassportErrorMsg"]').removeClass('d-block').addClass('d-none');
                    }
                    else if(dbcolum_name == "akama_no"){
                        $('span[id="uniqueIqamaErrorMsg"]').removeClass('d-block').addClass('d-none');
                    }
                    else if(dbcolum_name == "email"){
                        $('span[id="uniqueEmailErrorMsg"]').removeClass('d-block').addClass('d-none');
                    }

                }
            },
            error:function(response){
                alert('Data Checking Operation Failed, Please check Your Internet Connection');
            }
        });
    }




</script>

<!-- end form validation -->







<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.js"></script>-->
<script language="JavaScript">

//   //  Templorary Close Webcamera
//   Webcam.set({
//     width: 200,
//     height: 100,
//     dest_width: 150,
//     dest_height: 100,
//     image_format: 'jpeg',
//     jpeg_quality: 90,
//     force_flash: false
//   });
//   // preload shutter audio clip

//   var shutter = new Audio();
//   shutter.autoplay = true;
//   shutter.src = navigator.userAgent.match(/Firefox/) ? 'shutter.ogg' : 'shutter.mp3';

//   $('#off').click(function() {
//     Webcam.reset();
//     $('#my_camera').removeClass('my_camera');
//     $('#off').addClass('d-none');
//     $('#on').removeClass('d-none');
//   });

//   $('#on').click(function() {
//     Webcam.reset();
//     Webcam.on();
//     $('#my_camera').addClass('my_camera');
//     $('#on').addClass('d-none');
//     $('#off').removeClass('d-none');
//   });


//   Webcam.attach('.my_camera');

//   function take_snapshot() {
//     shutter.play();
//     Webcam.snap(function(data_uri) {
//       $(".image-tag").val(data_uri);
//       document.getElementById('results').innerHTML = '<img src="' + data_uri + '"/>';
//     });
//   }



</script>
@endsection
