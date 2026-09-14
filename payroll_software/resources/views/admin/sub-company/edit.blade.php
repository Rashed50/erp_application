@extends('layouts.admin-master')
@section('title') Edit Company Information @endsection
@section('content')
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Concern Company Information</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>

        </ol>
    </div>
</div>
<!-- add division -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
         @if(Session::has('success'))
          <div class="alert alert-success alertsuccess" role="alert">
             <strong>{{Session::get('success')}}</strong>
          </div>
        @endif
        @if(Session::has('error'))
          <div class="alert alert-warning alerterror" role="alert">
             <strong>{{Session::get('error')}}</strong>
          </div>
        @endif
    </div>
</div>
<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-6">
      <form class="form-horizontal" id="registration" action="{{ route('update-sub-company') }}" method="post">
        @csrf
        <div class="card">
            <br>
            <div class="card-body card_form" style="padding-top: 0;">
              <input type="hidden" name="id" value="{{ $edit->sb_comp_id }}">

              <div class="form-group row custom_form_group{{ $errors->has('company_id') ? ' has-error' : '' }}">
                  <label class="control-label col-md-4">Main Company Name:<span class="req_star">*</span></label>
                  <div class="col-md-6">
                    <select class="form-control" name="company_id" required>

                        @foreach($comp as $com)
                        <option value="{{ $com->comp_id }}" {{ $com->comp_id == $edit->company_id ? 'selected' : '' }}>{{ $com->comp_name_en }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('company_id'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('company_id') }}</strong>
                        </span>
                    @endif
                  </div>
                  <div class="col-md-2"></div>
              </div>
              <div class="form-group row custom_form_group{{ $errors->has('sb_comp_name') ? ' has-error' : '' }}">
                  <label class="control-label col-md-4">Company Name (English):<span class="req_star">*</span></label>
                  <div class="col-md-6">
                    <input type="text" class="form-control" placeholder="Company Name English" id="sb_comp_name" name="sb_comp_name" value="{{ $edit->sb_comp_name }}" required>
                    @if ($errors->has('sb_comp_name'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('sb_comp_name') }}</strong>
                        </span>
                    @endif
                  </div>
                  <div class="col-md-2"></div>
              </div>
              <div class="form-group row custom_form_group{{ $errors->has('sb_comp_name_arb') ? ' has-error' : '' }}">
                  <label class="control-label col-md-4">Company Name (Arabic):<span class="req_star">*</span></label>
                  <div class="col-md-6">
                    <input type="text" class="form-control" placeholder="Company Name Arabic" id="sb_comp_name_arb" name="sb_comp_name_arb" value="{{ $edit->sb_comp_name_arb }}" required>
                    @if ($errors->has('sb_comp_name_arb'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('sb_comp_name_arb') }}</strong>
                        </span>
                    @endif
                  </div>
                  <div class="col-md-2"></div>
              </div>
              <div class="form-group row custom_form_group{{ $errors->has('sb_comp_mobile1') ? ' has-error' : '' }}">
                  <label class="control-label col-md-4">Mobile Number:<span class="req_star">*</span></label>
                  <div class="col-md-6">
                    <input type="text" class="form-control" placeholder="Mobile Number" id="sb_comp_mobile1" name="sb_comp_mobile1" value="{{ $edit->sb_comp_mobile1 }}" required>
                    @if ($errors->has('sb_comp_mobile1'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('sb_comp_mobile1') }}</strong>
                        </span>
                    @endif
                  </div>
                  <div class="col-md-2"></div>
              </div>
              <div class="form-group row custom_form_group{{ $errors->has('sb_vat_no') ? ' has-error' : '' }}">
                  <label class="control-label col-md-4">VAT No:<span class="req_star">*</span></label>
                  <div class="col-md-6">
                    <input type="text" class="form-control" placeholder="VAT NO" id="sb_vat_no" name="sb_vat_no" value="{{ $edit->sb_vat_no }}" required>
                    @if ($errors->has('sb_vat_no'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('sb_vat_no') }}</strong>
                        </span>
                    @endif
                  </div>
                  <div class="col-md-2"></div>
              </div>
              <div class="form-group row custom_form_group{{ $errors->has('sb_comp_email1') ? ' has-error' : '' }}">
                  <label class="control-label col-md-4">Email Address 1:<span class="req_star">*</span></label>
                  <div class="col-md-6">
                    <input type="email" class="form-control" placeholder="Email Address" id="sb_comp_email1" name="sb_comp_email1" value="{{ $edit->sb_comp_email1 }}" required>
                    @if ($errors->has('sb_comp_email1'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('sb_comp_email1') }}</strong>
                        </span>
                    @endif
                  </div>
                  <div class="col-md-2"></div>
              </div>
              <div class="form-group row custom_form_group{{ $errors->has('sb_comp_email2') ? ' has-error' : '' }}">
                  <label class="control-label col-md-4">Email Address 2:</label>
                  <div class="col-md-6">
                    <input type="email" class="form-control" placeholder="Email Address" id="sb_comp_email2" name="sb_comp_email2" value="{{ $edit->sb_comp_email2 }}" >
                    @if ($errors->has('sb_comp_email2'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('sb_comp_email2') }}</strong>
                        </span>
                    @endif
                  </div>
                  <div class="col-md-2"></div>
              </div>
              <div class="form-group row custom_form_group{{ $errors->has('sb_comp_phone1') ? ' has-error' : '' }}">
                  <label class="control-label col-md-4">Phone Number:<span class="req_star">*</span></label>
                  <div class="col-md-6">
                    <input type="text" class="form-control" placeholder="Phone Number" id="sb_comp_phone1" name="sb_comp_phone1" value="{{ $edit->sb_comp_phone1 }}" required>
                    @if ($errors->has('sb_comp_phone1'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('sb_comp_phone1') }}</strong>
                        </span>
                    @endif
                  </div>
                  <div class="col-md-2"></div>
              </div>
              <div class="form-group row custom_form_group{{ $errors->has('sb_comp_phone2') ? ' has-error' : '' }}">
                  <label class="control-label col-md-4">Phone Number 2:</label>
                  <div class="col-md-6">
                    <input type="text" class="form-control" placeholder="Phone Number" id="sb_comp_phone2" name="sb_comp_phone2" value="{{ $edit->sb_comp_phone2 }}">
                    @if ($errors->has('sb_comp_phone2'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('sb_comp_phone2') }}</strong>
                        </span>
                    @endif
                  </div>
                  <div class="col-md-2"></div>
              </div>
              <div class="form-group row custom_form_group{{ $errors->has('sb_comp_address') ? ' has-error' : '' }}">
                  <label class="control-label col-md-4">Address:<span class="req_star">*</span></label>
                  <div class="col-md-6">
                    <textarea name="sb_comp_address" class="form-control" required>{{ $edit->sb_comp_address }}</textarea>
                    @if ($errors->has('sb_comp_address'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('sb_comp_address') }}</strong>
                        </span>
                    @endif
                  </div>
                  <div class="col-md-2"></div>
              </div>
              <div class="form-group row custom_form_group{{ $errors->has('sb_details') ? ' has-error' : '' }}">
                  <label class="control-label col-md-4">Contact Person Details:</label>
                  <div class="col-md-6">
                    <textarea name="sb_details" class="form-control" required>{{ $edit->sb_comp_contact_parson_details }}</textarea>
                    @if ($errors->has('sb_details'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('sb_details') }}</strong>
                        </span>
                    @endif
                  </div>
                  <div class="col-md-2"></div>
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

@endsection
