@extends('layouts.admin-master')
@section('title') Company Profile @endsection
@section('content')

<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title"> Company Information</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active">Edit Profile</li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <form class="form-horizontal company-form" id="registration" method="post" action="{{ route('update-profile') }}">
          @csrf
          <div class="card">
              <div class="card-header">
                  <div class="row">
                      <div class="col-md-8">
                          <h3 class="card-title card_top_title"><i class="fab fa-gg-circle"></i> Update Company Information</h3>
                      </div>
                      <div class="clearfix"></div>
                  </div>
              </div>
              <div class="card-body card_form">
                <div class="row">
                    <div class="col-md-3"></div>
                    <div class="col-md-7">
                        @if(Session::has('success'))
                          <div class="alert alert-success alertsuccess" role="alert" style="margin-left: -20px">
                             <strong>Successfully!</strong> Updated Profile.
                          </div>
                        @endif
                        @if(Session::has('error'))
                          <div class="alert alert-warning alerterror" role="alert" style="margin-left: -20px">
                             <strong>Opps!</strong> please try again.
                          </div>
                        @endif
                    </div>
                    <div class="col-md-2"></div>
                </div>

                <div class="form-group row custom_form_group{{ $errors->has('comp_name_en') ? ' has-error' : '' }}">
                    <label class="col-sm-3 control-label">English Name:<span class="req_star">*</span></label>
                    <div class="col-sm-7">
                      <input type="text" class="form-control" name="comp_name_en" value="{{ $profile->comp_name_en }}" required>
                      @if ($errors->has('comp_name_en'))
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $errors->first('comp_name_en') }}</strong>
                          </span>
                      @endif
                    </div>
                </div>
                <div class="form-group row custom_form_group{{ $errors->has('comp_name_arb') ? ' has-error' : '' }}">
                    <label class="col-sm-3 control-label">Arabic Name:<span class="req_star">*</span></label>
                    <div class="col-sm-7">
                      <input type="text" class="form-control" name="comp_name_arb" value="{{ $profile->comp_name_arb }}" required>
                      @if ($errors->has('comp_name_arb'))
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $errors->first('comp_name_arb') }}</strong>
                          </span>
                      @endif
                    </div>
                </div>

                <div class="form-group row custom_form_group{{ $errors->has('curc_id') ? ' has-error' : '' }}">
                    <label class="col-sm-3 control-label">Currency:<span class="req_star">*</span></label>
                    <div class="col-sm-7">
                      <select class="form-control" name="curc_id" required>
                          <option value="">Select Currency</option>
                          @foreach($cur as $cu)
                          <option value="{{ $cu->curc_id }}" {{ $profile->curc_id == $cu->curc_id ? 'selected':'' }}>{{ $cu->curc_name }}</option>
                          @endforeach
                      </select>
                      @if ($errors->has('curc_id'))
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $errors->first('curc_id') }}</strong>
                          </span>
                      @endif
                    </div>
                </div>

                <div class="form-group row custom_form_group{{ $errors->has('comp_email1') ? ' has-error' : '' }}">
                    <label class="col-sm-3 control-label">Email 1:<span class="req_star">*</span></label>
                    <div class="col-sm-7">
                      <input type="email" class="form-control" name="comp_email1" value="{{ $profile->comp_email1 }}" required>
                      @if ($errors->has('comp_email1'))
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $errors->first('comp_email1') }}</strong>
                          </span>
                      @endif
                    </div>
                </div>

                <div class="form-group row custom_form_group{{ $errors->has('comp_email2') ? ' has-error' : '' }}">
                    <label class="col-sm-3 control-label">Email 2:<span class="req_star">*</span></label>
                    <div class="col-sm-7">
                      <input type="email" class="form-control" name="comp_email2" value="{{ $profile->comp_email2 }}" required>
                      @if ($errors->has('comp_email2'))
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $errors->first('comp_email2') }}</strong>
                          </span>
                      @endif
                    </div>
                </div>
                <div class="form-group row custom_form_group{{ $errors->has('comp_phone1') ? ' has-error' : '' }}">
                    <label class="col-sm-3 control-label">Phone 1:<span class="req_star">*</span></label>
                    <div class="col-sm-7">
                      <input type="text" class="form-control" name="comp_phone1" value="{{ $profile->comp_phone1 }}" required>
                      @if ($errors->has('comp_phone1'))
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $errors->first('comp_phone1') }}</strong>
                          </span>
                      @endif
                    </div>
                </div>
                <div class="form-group row custom_form_group">
                    <label class="col-sm-3 control-label">Phone 2:</label>
                    <div class="col-sm-7">
                      <input type="text" class="form-control" name="comp_phone2" value="{{ $profile->comp_phone2 }}">

                    </div>
                </div>
                <div class="form-group row custom_form_group{{ $errors->has('comp_mobile1') ? ' has-error' : '' }}">
                    <label class="col-sm-3 control-label">Mobile 1:<span class="req_star">*</span></label>
                    <div class="col-sm-7">
                      <input type="text" class="form-control" name="comp_mobile1" value="{{ $profile->comp_mobile1 }}" required>
                      @if ($errors->has('comp_mobile1'))
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $errors->first('comp_mobile1') }}</strong>
                          </span>
                      @endif
                    </div>
                </div>
                <div class="form-group row custom_form_group{{ $errors->has('comp_mobile2') ? ' has-error' : '' }}">
                    <label class="col-sm-3 control-label">Mobile 2:<span class="req_star">*</span></label>
                    <div class="col-sm-7">
                      <input type="text" class="form-control" name="comp_mobile2" value="{{ $profile->comp_mobile2 }}" required>
                      @if ($errors->has('comp_mobile2'))
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $errors->first('comp_mobile2') }}</strong>
                          </span>
                      @endif
                    </div>
                </div>
                <div class="form-group row custom_form_group{{ $errors->has('comp_support_number') ? ' has-error' : '' }}">
                    <label class="col-sm-3 control-label">Contact Number:<span class="req_star">*</span></label>
                    <div class="col-sm-7">
                      <input type="text" class="form-control" name="comp_support_number" value="{{ $profile->comp_support_number }}" required>
                      @if ($errors->has('comp_support_number'))
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $errors->first('comp_support_number') }}</strong>
                          </span>
                      @endif
                    </div>
                </div>
                <div class="form-group row custom_form_group{{ $errors->has('comp_contact_address') ? ' has-error' : '' }}">
                    <label class="col-sm-3 control-label">Contact Address:<span class="req_star">*</span></label>
                    <div class="col-sm-7">
                      <textarea name="comp_contact_address" class="form-control" required>{{ $profile->comp_contact_address }}</textarea>
                      @if ($errors->has('comp_contact_address'))
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $errors->first('comp_contact_address') }}</strong>
                          </span>
                      @endif
                    </div>
                </div>


                <div class="form-group row custom_form_group{{ $errors->has('comp_hotline_number') ? ' has-error' : '' }}">
                    <label class="col-sm-3 control-label">Hotline Number:<span class="req_star">*</span></label>
                    <div class="col-sm-7">
                      <input type="text" class="form-control" name="comp_hotline_number" value="{{ $profile->comp_hotline_number }}" required>
                      @if ($errors->has('comp_hotline_number'))
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $errors->first('comp_hotline_number') }}</strong>
                          </span>
                      @endif
                    </div>
                </div>
                <div class="form-group row custom_form_group{{ $errors->has('comp_address') ? ' has-error' : '' }}">
                    <label class="col-sm-3 control-label">Address:<span class="req_star">*</span></label>
                    <div class="col-sm-7">
                      <textarea name="comp_address" class="form-control" required>{{ $profile->comp_address }}</textarea>
                      @if ($errors->has('comp_address'))
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $errors->first('comp_address') }}</strong>
                          </span>
                      @endif
                    </div>
                </div>
                <div class="form-group row custom_form_group{{ $errors->has('comp_description') ? ' has-error' : '' }}">
                    <label class="col-sm-3 control-label">Description:<span class="req_star">*</span></label>
                    <div class="col-sm-7">
                      <textarea name="comp_description" class="form-control" required>{{ $profile->comp_description }}</textarea>
                      @if ($errors->has('comp_description'))
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $errors->first('comp_description') }}</strong>
                          </span>
                      @endif
                    </div>
                </div>
                <div class="form-group row custom_form_group{{ $errors->has('comp_mission') ? ' has-error' : '' }}">
                    <label class="col-sm-3 control-label">Mission:<span class="req_star">*</span></label>
                    <div class="col-sm-7">
                      <textarea name="comp_mission" class="form-control" required>{{ $profile->comp_mission }}</textarea>
                      @if ($errors->has('comp_mission'))
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $errors->first('comp_mission') }}</strong>
                          </span>
                      @endif
                    </div>
                </div>
                <div class="form-group row custom_form_group{{ $errors->has('comp_vission') ? ' has-error' : '' }}">
                    <label class="col-sm-3 control-label">Vision:<span class="req_star">*</span></label>
                    <div class="col-sm-7">
                      <textarea name="comp_vission" class="form-control" required>{{ $profile->comp_vission }}</textarea>
                      @if ($errors->has('comp_vission'))
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $errors->first('comp_vission') }}</strong>
                          </span>
                      @endif
                    </div>
                </div>


              </div>
              <div class="card-footer card_footer_button text-center">
                  <button type="submit" id="onSubmit" onclick="formValidation();" class="btn btn-primary waves-effect">UPDATE</button>
              </div>
          </div>
        </form>
    </div>
</div>
@endsection
