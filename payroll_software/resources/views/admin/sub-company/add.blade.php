@extends('layouts.admin-master')
@section('title') Create Concern Company Information @endsection
@section('content')
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Concern Company Information</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active"> Concern Company</li>
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
    <div class="col-md-2"></div>
    <div class="col-md-8">
      <form class="form-horizontal" id="registration" action="{{ route('insert-sub-company') }}" method="post">
        @csrf
        <div class="card">
            <br>
            <div class="card-body card_form" style="padding-top: 0;">
              <div class="form-group row custom_form_group{{ $errors->has('company_id') ? ' has-error' : '' }}">
                  <label class="control-label col-md-4">Main Company Name:<span class="req_star">*</span></label>
                  <div class="col-md-6">
                    <select class="form-control" name="company_id" required>
                        @foreach($comp as $com)
                        <option value="{{ $com->comp_id }}">{{ $com->comp_name_en }}</option>
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
                    <input type="text" class="form-control" placeholder="Company Name English" id="sb_comp_name" name="sb_comp_name" value="{{old('sb_comp_name')}}" required>
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
                    <input type="text" class="form-control" placeholder="Company Name Arabic" id="sb_comp_name_arb" name="sb_comp_name_arb" value="{{old('sb_comp_name_arb')}}" required>
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
                    <input type="text" class="form-control" placeholder="Mobile Number" id="sb_comp_mobile1" name="sb_comp_mobile1" value="{{old('sb_comp_mobile1')}}" required>
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
                    <input type="text" class="form-control" placeholder="VAT NO" id="sb_vat_no" name="sb_vat_no" value="{{old('sb_vat_no')}}" required>
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
                    <input type="email" class="form-control" placeholder="Email Address" id="sb_comp_email1" name="sb_comp_email1" value="{{old('sb_comp_email1')}}" required>
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
                    <input type="email" class="form-control" placeholder="Email Address" id="sb_comp_email2" name="sb_comp_email2" value="{{old('sb_comp_email2')}}" >
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
                    <input type="text" class="form-control" placeholder="Phone Number" id="sb_comp_phone1" name="sb_comp_phone1" value="{{old('sb_comp_phone1')}}" required>
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
                    <input type="text" class="form-control" placeholder="Phone Number" id="sb_comp_phone2" name="sb_comp_phone2" value="{{old('sb_comp_phone2')}}">
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
                    <textarea name="sb_comp_address" class="form-control" placeholder="Company Address " required>{{old('sb_comp_address')}}</textarea>
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
                    <textarea name="sb_details" class="form-control" placeholder="Company Contact Person Details Informations">{{old('sb_details')}}</textarea>
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
                <button type="submit" class="btn btn-primary waves-effect">CREATE</button>
            </div>
        </div>
      </form>
    </div>
    <div class="col-md-2"></div>
</div>

<!-- division list -->
<div class="row">
    <div class="col-lg-12">
      <div class="card">
          <div class="card-header">
              <div class="row">
                  <div class="col-md-8">
                      <h3 class="card-title card_top_title"><i class="fab fa-gg-circle"></i> Concern Company List</h3>
                  </div>
                  <div class="clearfix"></div>
              </div>
          </div>
          <div class="card-body">
              <div class="row">
                  <div class="col-12">
                      <div class="table-responsive">
                          <table id="alltableinfo" class="table table-bordered custom_table mb-0">
                              <thead>
                                  <tr>
                                      <th>Company Name</th>
                                      <th>Mobile No</th>
                                      <th>Email Address</th>
                                      <th>Phone No</th>
                                      <th>Manage</th>
                                  </tr>
                              </thead>
                              <tbody>
                                @foreach($all as $item)
                                  <tr>
                                    <td>{{ $item->sb_comp_name }}</td>
                                    <td>{{ $item->sb_comp_mobile1 }}</td>
                                    <td>{{ $item->sb_comp_email1 }}</td>
                                    <td>{{ $item->sb_comp_phone1 }}</td>
                                    <td>
                                      <a href="{{ route('view-info',$item->sb_comp_id) }}" title="view"><i class="fa fa-plus-square fa-lg view_icon"></i></a>
                                      <a href="{{ route('edit-info',$item->sb_comp_id) }}" title="edit"><i class="fa fa-pencil-square fa-lg edit_icon"></i></a>
                                      <a href="{{ route('delete-info',$item->sb_comp_id) }}" title="delete"  id="delete" title="delete data"><i class="fa fa-trash fa-lg delete_icon"></i></a>
                                    </td>
                                  </tr>
                                @endforeach
                              </tbody>
                          </table>
                      </div>
                  </div>
              </div>
          </div>
      </div>
    </div>
</div>
@endsection
