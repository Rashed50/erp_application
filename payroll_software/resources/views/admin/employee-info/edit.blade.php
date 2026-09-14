@extends('layouts.admin-master')
@section('title') Update Employee @endsection
@section('content')

<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Employee Information</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>

            <li class="active">Update</li>
        </ol>
    </div>
</div>

<!--  Update Employee Information -->
<div class="row">
    <div class="col-lg-12">
        <form class="form-horizontal" id="employee-info-form" method="post"
            action="{{ route('employee-information-update') }}">
            @csrf
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-8">
                            <h3 class="card-title card_top_title"><i class="fab fa-gg-circle"></i> Update Employee
                                Information</h3>
                        </div>
                        <div class="col-md-4 text-right">
                            <a href="{{ route('employee-list') }}"
                                class="btn btn-md btn-primary waves-effect card_top_button"><i class="fa fa-th"></i> All
                                Employee Information</a>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
                <div class="card-body card_form">
                    <div class="row">
                        <div class="col-md-3"></div>
                        <div class="col-md-7">
                            @if(Session::has('error'))
                            <div class="alert alert-warning alerterror" role="alert" style="margin-left: -20px">
                                <strong>Opps!</strong> please try again.
                            </div>
                            @endif
                        </div>
                        <div class="col-md-2"></div>
                    </div>

                    <input type="hidden" name="id" value="{{ $edit->emp_auto_id }}">

                    <div class="form-group row custom_form_group">
                        <label class="col-sm-3 control-label">Employee ID:<span class="req_star">*</span></label>
                        <div class="col-sm-7">
                            <input type="text" class="form-control" name="emp_id" value="{{ $edit->employee_id }}"
                                disabled placeholder="Employee Id">
                        </div>
                    </div>

                    <div class="form-group row custom_form_group{{ $errors->has('emp_name') ? ' has-error' : '' }}">
                        <label class="col-sm-3 control-label">Name:<span class="req_star">*</span></label>
                        <div class="col-sm-7">
                            <input type="text" placeholder="Input Employee Name Here" class="form-control" id="emp_name"
                                name="emp_name" value="{{ $edit->employee_name }}">
                            @if ($errors->has('emp_name'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('emp_name') }}</strong>
                            </span>
                            @endif
                            <div id="showerror1"></div>
                        </div>
                    </div>


                    <div
                        class="form-group row custom_form_group{{ $errors->has('agc_info_auto_id ') ? ' has-error' : '' }}">
                        <label class="col-sm-3 control-label">Agency Name:<span class="req_star">*</span></label>
                        <div class="col-sm-7">
                            <select class="form-control" name="agency_id">
                                <option value="">Select Agency Name</option>
                                @foreach($agencies as $agency)
                                <option value="{{ $agency->agc_info_auto_id }}" {{ $agency->agc_info_auto_id ==
                                    $edit->agc_info_auto_id ? 'selected' : '' }} >{{ $agency->agc_title }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('agc_info_auto_id '))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('agc_info_auto_id ') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row custom_form_group{{ $errors->has('sponsor_id') ? ' has-error' : '' }}">
                        <label class="col-sm-3 control-label">Sponsor Name:<span class="req_star">*</span></label>
                        <div class="col-sm-7">
                            <select class="form-control" name="sponsor_id">
                                <option value="">Select Sponsor Name</option>
                                @foreach($sponsor as $spon)
                                <option value="{{ $spon->spons_id }}" {{ $spon->spons_id == $edit->sponsor_id ?
                                    'selected' : '' }} >{{ $spon->spons_name }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('sponsor_id'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('sponsor_id') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row custom_form_group{{ $errors->has('passport_no') ? ' has-error' : '' }}">
                        <label class="col-sm-3 control-label">Passport No:<span class="req_star">*</span></label>
                        <div class="col-sm-7">
                            <input type="text" placeholder="Input Passfort Number Here" class="form-control"
                                name="passfort_no" value="{{ $edit->passfort_no }}">
                            @if ($errors->has('passport_no'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('passport_no') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div
                        class="form-group row custom_form_group{{ $errors->has('passport_expire_date') ? ' has-error' : '' }}">
                        <label class="col-sm-3 control-label">Passport Expire:<span class="req_star">*</span></label>
                        <div class="col-sm-7">

                            <input type="date" class="form-control" name="passport_expire_date"
                                value="{{ $edit->passfort_expire_date }}"   >

                            @if ($errors->has('passport_expire_date'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('passport_expire_date') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row custom_form_group{{ $errors->has('akama_no') ? ' has-error' : '' }}">
                        <label class="col-sm-3 control-label">Iqama No:<span class="req_star">*</span></label>
                        <div class="col-sm-7">
                            <input type="text" placeholder="Input Iqama Number Here" class="form-control"
                                name="akama_no" value="{{ $edit->akama_no }}">

                            @if ($errors->has('akama_no'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('akama_no') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row custom_form_group{{ $errors->has('akama_expire') ? ' has-error' : '' }}">
                        <label class="col-sm-3 control-label">Iqama Expire:<span class="req_star">*</span></label>
                        <div class="col-sm-7">
                            <input type="date" class="form-control" name="akama_expire"
                                value="{{ $edit->akama_expire_date }}" min="{{ date('Y-m-d') }}"  >
                            @if ($errors->has('akama_expire'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('akama_expire') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>


                    <div class="form-group row custom_form_group{{ $errors->has('mobile_no') ? ' has-error' : '' }}">
                        <label class="col-sm-3 control-label">Abshar Mobile No:<span class="req_star">*</span></label>
                        <div class="col-sm-7">
                            <input type="text" placeholder="Input Mobile Number" class="form-control" name="mobile_no"
                                value="{{ $edit->mobile_no }}" unique>
                            @if ($errors->has('mobile_no'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('mobile_no') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row custom_form_group{{ $errors->has('phone_no') ? ' has-error' : '' }}">
                        <label class="col-sm-3 control-label">Mobile Number2:</label>
                        <div class="col-sm-7">
                            <input type="text" placeholder="Input Phone Number" class="form-control" name="phone_no"
                                value="{{ $edit->phone_no }}">
                            @if ($errors->has('phone_no'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('phone_no') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row custom_form_group">
                        <label class="col-sm-3 control-label">Home Country Contact No:</label>
                        <div class="col-sm-7">
                          <input type="number" placeholder="Home Country Contact Number" class="form-control"
                            name="country_phone_no" value="{{ $edit->country_phone_no }}">

                        </div>
                      </div>

                    <div class="form-group row custom_form_group">
                        <label class="col-sm-3 control-label">Email:</label>
                        <div class="col-sm-7">
                            <input type="email" placeholder="Input Email Address" class="form-control" name="email"
                                value="{{ $edit->email }}">
                        </div>
                    </div>


                    <div class="form-group row custom_form_group">
                        <label class="col-sm-3 control-label">Present Address:<span class="req_star">*</span></label>
                        <div class="col-sm-7">
                            <textarea name="present_address" class="form-control" value="{{old('present_address')}}"
                                placeholder="Input Present Address Here">{{ $edit->present_address }}</textarea>
                        </div>
                    </div>

                    <div class="row custom_form_group">
                        <label class="col-sm-3 control-label">Permanent Address:</label>
                        <div class="col-sm-7">
                            <div class="parmanent_address">
                                <!-- country -->
                                <div class="form-group{{ $errors->has('country_id') ? ' has-error' : '' }}">
                                    <select class="form-control" name="country_id">
                                        <option value="">Select Country</option>
                                        @foreach($countryList as $country)
                                        <option value="{{ $country->id }}" {{ $country->id == $edit->country_id ?
                                            'selected' : '' }}> {{ $country->country_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- division -->
                                <div class="form-group">
                                    <select class="form-control" name="division_id">
                                        @if($edit->division_id == "")
                                        <option value="">Select Division</option>
                                        @else
                                        <option value="{{ $edit->division->division_id }}">{{
                                            $edit->division->division_name }}</option>
                                        @endif
                                    </select>
                                </div>

                                <div class="form-group">
                                    <select class="form-control" name="district_id">
                                        @if($edit->district_id == "")
                                        <option value="">Select District</option>
                                        @else
                                        <option value="{{ $edit->district->district_id }}">{{
                                            $edit->district->district_name }}</option>
                                        @endif
                                    </select>
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" value="{{ $edit->post_code }}"
                                        id="post_code" name="post_code" placeholder="Input Post Code">
                                </div>
                                <div class="form-group">
                                    <textarea class="form-control" id="details" name="details"
                                        placeholder="Input Address Details">{{ $edit->details }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row custom_form_group">
                        <label class="col-sm-3 control-label">Employee Type:<span class="req_star">*</span></label>
                        <div class="col-sm-7">
                            <div class="form-group{{ $errors->has('emp_type_id') ? ' has-error' : '' }}">
                                <select class="form-control" name="emp_type_id">
                                    <option value="">Select Employee Type</option>
                                    @foreach($empTypes as $emp)
                                    <option value="{{ $emp->id }}" {{ $emp->id == $edit->emp_type_id ? 'selected' : ''
                                        }} >{{ $emp->name }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('emp_type_id'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('emp_type_id') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row custom_form_group">
                        <label class="col-sm-3 control-label">Designation:<span class="req_star">*</span></label>
                        <div class="col-sm-7">
                            <div class="form-group{{ $errors->has('designation_id') ? ' has-error' : '' }}">
                                <select class="form-control" name="designation_id">

                                    @foreach($designationList as $designation)
                                    <option value="{{ $designation->catg_id }}" {{$designation->catg_id ==
                                        $edit->designation_id ? 'selected' : ''}}>{{ $designation->catg_name}}</option>
                                    @endforeach


                                </select>
                                @if ($errors->has('designation_id'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('designation_id') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="form-group row custom_form_group">
                        <label class="col-sm-3 control-label"> Project:</label>
                        <div class="col-sm-7">
                            <select class="form-control" name="project_id">
                                <option value="">Select Here</option>
                                @foreach($proj as $projInfo)
                                <option value="{{ $projInfo->proj_id }}" {{ $projInfo->proj_id == $edit->project_id ?
                                    'selected' : '' }}>{{ $projInfo->proj_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>



                    <div class="form-group row custom_form_group">
                        <label class="col-sm-3 control-label">Department:</label>
                        <div class="col-sm-7">
                            <select class="form-control" name="department_id">
                                <option value="">Select Department</option>
                                @foreach($allDepart as $depart)
                                <option value="{{ $depart->dep_id }}" {{ $depart->dep_id == $edit->department_id ?
                                    'selected' : '' }} >{{ $depart->dep_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row custom_form_group">
                        <label class="col-sm-3 control-label">Date Of Birth:</label>
                        <div class="col-sm-7">
                            <input type="date" class="form-control" name="date_of_birth"
                                value="{{ $edit->date_of_birth }}"
                                max="{{ Carbon\Carbon::now()->subYears(18)->format('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="form-group row custom_form_group">
                        <label class="col-sm-3 control-label">Blood Group:</label>
                        <div class="col-sm-7">
                          <select class="form-select" name="blood_group">
                            <option value="A+" {{ $edit->blood_group == 'A+' ? 'selected' :'' }} >A+</option>
                            <option value="A-" {{ $edit->blood_group == 'A-' ? 'selected' :'' }}>A-</option>
                            <option value="B+" {{ $edit->blood_group == 'B+' ? 'selected' :'' }}>B+</option>
                            <option value="B-" {{ $edit->blood_group == 'B-' ? 'selected' :'' }}>B-</option>
                            <option value="O+" {{ $edit->blood_group == 'O+' ? 'selected' :'' }}>O+</option>
                            <option value="O-" {{ $edit->blood_group == 'O-' ? 'selected' :'' }}>O-</option>
                            <option value="AB+" {{ $edit->blood_group == 'AB+' ? 'selected' :'' }}>AB+</option>
                            <option value="AB-" {{ $edit->blood_group == 'AB-' ? 'selected' :'' }}>AB-</option>
                          </select>
                        </div>
                      </div>

                    <div class="form-group row custom_form_group">
                        <label class="col-sm-3 control-label">Marital Status:</label>
                        <div class="col-sm-7">
                            <select class="form-control" name="maritus_status">
                                <option value="1" {{ $edit->is_married == 1 ? 'selected' :''}} >Married</option>
                                <option value="2" {{ $edit->is_married == 2 ? 'selected' :''}}>Unmarried</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row custom_form_group">
                        <label class="col-sm-3 control-label">Gender:</label>
                        <div class="col-sm-7 gender">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="gender" checked id="gender" value="1"
                                    {{ $edit->gender == 'M' ? 'checked':'' }}>
                                <label class="form-check-label">Male</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="gender" id="gender" value="2" {{
                                    $edit->gender == 'F' ? 'checked':'' }}>
                                <label class="form-check-label">Female</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row custom_form_group">
                        <label class="col-sm-3 control-label">Religion:</label>
                        <div class="col-sm-7">
                            <select class="form-control" name="religion">
                                {{-- <option value="">Select Your Religion</option> --}}
                                @foreach($relig as $reg)
                                <option value="{{ $reg->relig_id }}" {{ $edit->religion_id == $reg->relig_id ? 'selected' :
                                    '' }}>{{ $reg->relig_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row custom_form_group">
                        <label class="col-sm-3 control-label">Reference Employee</label>
                        <div class="col-sm-7">
                          <input type="text" placeholder="Input Reference Name or Employee ID" class="form-control"
                            name="ref_employee_id" value="{{ $edit->ref_employee_id  }}">
                        </div>
                      </div>

                      <div class="form-group row custom_form_group">
                        <label class="col-sm-3 control-label">Remarks</label>
                        <div class="col-sm-7">
                          <input type="text" placeholder="Input Remarks Here" class="form-control" name="remarks"
                            value="{{  $edit->remarks  }}">
                        </div>
                      </div>

                    <div class="form-group row custom_form_group">
                        <label class="col-sm-3 control-label">Appointment:<span class="req_star">*</span></label>
                        <div class="col-sm-7">
                            <input type="date" name="appointment_date" value="{{ $edit->appointment_date }}"
                                class="form-control">
                            @if ($errors->has('appointment_date'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('appointment_date') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row custom_form_group">
                        <label class="col-sm-3 control-label">Joining:</label>
                        <div class="col-sm-7">
                            <input type="date" name="joining_date" class="form-control"
                                max="{{ Carbon\Carbon::now()->format('Y-m-d') }}" value="{{ $edit->joining_date}}">
                        </div>
                    </div>

                    <div class="form-group row custom_form_group">
                        <label class="col-sm-3 control-label">Confirmation:</label>
                        <div class="col-sm-7">
                            <input type="date" name="confirmation_date" value="{{ $edit->confirmation_date }}"
                                class="form-control">
                        </div>
                    </div>
                </div>
                <div class="card-footer card_footer_button text-center">
                    <button type="submit" class="btn btn-primary waves-effect">UPDATE</button>
                </div>
            </div>
        </form>
    </div>
</div>


<!-- Photo Update -->
<div class="row">
    <div class="col-lg-12">
        <form class="form-horizontal" id="employee-info-form" method="post" enctype="multipart/form-data" action="{{ route('employee-image.update') }}">
          @csrf
          <div class="card">
              <div class="card-header">
                  <div class="row">
                      <div class="col-md-8">
                          <h3 class="card-title card_top_title"><i class="fab fa-gg-circle"></i> Update Employee Related File </h3>
                      </div>
                      <div class="clearfix"></div>
                  </div>
              </div>
              <div class="card-body card_form">
                <!-- <input type="hidden" name="id" value="{{ $edit->emp_auto_id }}"> -->
                <input type="hidden" name="emp_auto_id" value="{{ $edit->emp_auto_id }}">

                <input type="hidden" name="old_profile_photo" value="{{ $edit->profile_photo }}">
                <input type="hidden" name="old_pasfort_photo" value="{{ $edit->pasfort_photo }}">
                <input type="hidden" name="old_akama_photo" value="{{ $edit->akama_photo }}">
                <input type="hidden" name="old_medical_report" value="{{ $edit->medical_report }}">
                <input type="hidden" name="old_employee_appoint_latter" value="{{ $edit->employee_appoint_latter }}">
                <input type="hidden" name="old_employee_covid_certificate" value="{{ $edit->covid_certificate }}">


                  <!-- Profile Photo Upload -->
                  <div class="image_custom_row form-group row custom_form_group">
                    <div class="col-md-1"></div>
                    <div class="col-md-4">
                      <label class=" control-label">Profile Photo:</label>
                      <div class="input-group">
                          <span class="input-group-btn">
                              <span class="btn btn-default btn-file btnu_browse">
                                  Browse… <input type="file" accept="image/*" name="profile_photo" id="imgInp4">
                              </span>
                          </span>
                          <input type="text" class="form-control" readonly>
                      </div>
                    </div>
                    <div class="col-sm-3">
                        <img id='img-upload4' class="upload_image"/>
                    </div>
                    <div class="col-md-3">
                        <img src="{{ asset($edit->profile_photo) }}" style="width:50%; max-height: 200px" alt="{{ $edit->profile_photo }}">
                    </div>
                </div>

                <!-- Passport Upload -->
                <div class="image_custom_row form-group row custom_form_group">
                    <div class="col-md-1"></div>
                    <div class="col-md-4">
                      <label class=" control-label">Passport File:</label>
                      <div class="input-group">
                          <span class="input-group-btn">
                              <span class="btn btn-default btn-file btnu_browse">
                                  Browse… <input type="file" accept="image/*,.pdf" name="pasfort_photo" id="imgInp">
                              </span>
                          </span>
                          <input type="text" class="form-control" readonly>
                      </div>
                    </div>
                    <div class="col-sm-3">
                        <img id='img-upload' class="upload_image"/>
                    </div>
                    <div class="col-md-3">
                        <img src="{{ asset($edit->pasfort_photo) }}" style="width:50%; max-height: 200px" alt="{{ $edit->pasfort_photo }}">
                    </div>
                </div>

                   <!-- Iaqama Copy Upload -->
                <div class="image_custom_row form-group row custom_form_group">
                    <div class="col-md-1"></div>
                    <div class="col-md-4">
                    <label class=" control-label">Iqama File:</label>
                      <div class="input-group">
                          <span class="input-group-btn">
                              <span class="btn btn-default btn-file btnu_browse">
                                  Browse… <input type="file" accept="image/*,.pdf" name="akama_photo" id="imgInp3">
                              </span>
                          </span>
                          <input type="text" class="form-control" readonly>
                      </div>
                    </div>
                    <div class="col-sm-3">
                        <img id='img-upload3' class="upload_image"/>
                    </div>
                    <div class="col-md-3">
                        <img src="{{ asset($edit->akama_photo) }}" style="width:50%; max-height: 200px" alt="{{ $edit->akama_photo }}">
                    </div>
                </div>
                <!-- Medical Report Upload -->
                <div class="image_custom_row form-group row custom_form_group">
                    <div class="col-md-1"></div>
                    <div class="col-sm-4">
                      <label class="control-label">Medical Report:</label>
                      <div class="input-group">
                          <span class="input-group-btn">
                              <span class="btn btn-default btn-file btnu_browse">
                                  Browse… <input type="file" accept="image/*,.pdf" name="medical_report" id="imgInp2">
                              </span>
                          </span>
                          <input type="text" class="form-control" readonly>
                      </div>
                    </div>
                    <div class="col-sm-3">
                        <img id='img-upload2' class="upload_image"/>
                    </div>
                    <div class="col-md-3">
                        <img src="{{ asset($edit->medical_report) }}" style="width:50%; max-height: 200px" alt="{{ $edit->medical_report }}">
                    </div>
                </div>

                <!-- Covid Certificate Upload -->
                <div class="form-group row custom_form_group">
                    <div class="col-md-1"></div>
                    <div class="col-sm-4">
                      <label class="control-label">Covid Certificate:</label>
                      <div class="input-group">
                          <span class="input-group-btn">
                              <span class="btn btn-default btn-file btnu_browse">
                                  Browse… <input type="file" accept="image/*,.pdf" name="covid_certificate" id="imgInp5">
                              </span>
                          </span>
                          <input type="text" class="form-control" readonly>
                      </div>
                    </div>
                    <div class="col-sm-3">
                        <img id='img-upload5' class="upload_image"/>
                    </div>
                    <div class="col-md-3">
                        <img src="{{ asset($edit->covid_certificate) }}" style="width:50%; max-height: 200px" alt="{{ $edit->covid_certificate }}">
                    </div>
                </div>

                    <!-- Appointment Letter Upload -->
                <div class="form-group row custom_form_group">
                    <div class="col-md-1"></div>
                    <div class="col-sm-4">
                      <label class="control-label">Appointment Letter:</label>
                      <div class="input-group">
                          <span class="input-group-btn">
                              <span class="btn btn-default btn-file btnu_browse">
                                  Browse… <input type="file" accept="image/*,.pdf" name="appoint_latter" id="imgInp8">
                              </span>
                          </span>
                          <input type="text" class="form-control" readonly>
                      </div>
                    </div>
                    <div class="col-sm-3">
                        <img id='img-upload8' class="upload_image"/>
                    </div>
                    <div class="col-md-3">
                        <img src="{{ asset($edit->employee_appoint_latter) }}" style="width:50%; max-height: 200px" alt="{{ $edit->employee_appoint_latter }}">
                    </div>
                </div>
              </div>
              <div class="card-footer card_footer_button text-center">
                  <button type="submit" class="btn btn-primary waves-effect">UPDATE</button>
              </div>
          </div>
        </form>
    </div>
</div>

<!-- form validation -->
<script type="text/javascript">
  $(document).ready(function(){
    $("#employee-info-form").validate({

      rules: {
        emp_id: {
          required : true,
        },
        emp_type_id: {
          required : true,
        },
        emp_name: {
          required : true,
        },
        passfort_no: {
          required : true,
         // number: true,
          maxlength: 15,
        },
        passfort_expire_date: {
          required : true,
        },
        sponsor_id: {
          required : true,
        },
        akama_expire: {
          required : true,
        },
        mobile_no: {
          required : true,
        },
        akama_no: {
          required : true,
          number: true,
          maxlength: 15,
        },
        country_id: {
          required : true,
        },
        division_id: {
          required : true,
        },
        district_id: {
          required : true,
        },
      },

      messages: {
        emp_id: {
          required : "You Must Be Input This Field!",
        },
        emp_name: {
          required : "You Must Be Input This Field!",
        },
        passfort_expire_date: {
          required : "You Must Be Select This Field!",
        },
        emp_type_id: {
          required : "You Must Be Select This Field!",
        },
        sponsor_id: {
          required : "You Must Be Select This Field!",
        },
        akama_expire: {
          required : "You Must Be Select This Field!",
        },
        passfort_no: {
          required : "Please Input This Field!",
          number : "You Must Be Input Number!",
          max : "You Must Be Input Maximum Length 15!",
        },
        akama_no: {
          required : "Please Input This Field!",
          number : "You Must Be Input Number Only!",
          max : "You Must Be Input Maximum Length 10!",
        },
      },
    });
  });
</script>
@endsection
