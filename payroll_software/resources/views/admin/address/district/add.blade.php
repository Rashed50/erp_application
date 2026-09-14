@extends('layouts.admin-master')
@section('title') Add District @endsection
@section('content')
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">District</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active">Add District</li>
        </ol>
    </div>
</div>
<!-- add division -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        @if(Session::has('success_add'))
          <div class="alert alert-success alertsuccess" role="alert">
             <strong>Successfully!</strong> Added District.
          </div>
        @endif
        @if(Session::has('success_update'))
          <div class="alert alert-success alertsuccess" role="alert">
             <strong>Successfully!</strong> Update District.
          </div>
        @endif
        @if(Session::has('delete_update'))
          <div class="alert alert-success alertsuccess" role="alert">
             <strong>Successfully!</strong> Update District.
          </div>
        @endif
        @if(Session::has('success_error'))
          <div class="alert alert-warning alerterror" role="alert">
             <strong>Opps!</strong> please try again.
          </div>
        @endif
        @if(Session::has('already_exit'))
          <div class="alert alert-warning alerterror" role="alert">
             <strong>Opps!</strong> This District Already Exit!.
          </div>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-6">
      <form class="form-horizontal" id="registration" action="{{ route('insert-district') }}" method="post">
        @csrf
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-8">
                        <h3 class="card-title card_top_title"><i class="fab fa-gg-circle"></i> New District Information</h3>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="card-body card_form" style="padding-top: 0;">
              <div class="form-group custom_form_group row{{ $errors->has('country_id') ? ' has-error' : '' }}">
                  <label class="control-label col-sm-3">Country Name:</label>
                  <div class="col-sm-9">
                    <select class="form-control" name="country_id" required>
                        <option value="">Select Country</option>
                        @foreach($allCountry as $country)
                        <option value="{{ $country->id }}">{{ $country->country_name }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('country_id'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('country_id') }}</strong>
                        </span>
                    @endif
                  </div>
              </div>

              <div class="form-group row custom_form_group{{ $errors->has('division_id') ? ' has-error' : '' }}">
                  <label class="control-label col-md-3">Division Name:</label>
                  <div class="col-md-9">
                    <select class="form-control" name="division_id" required>
                        <option value="">Select Division</option>
                    </select>
                    @if ($errors->has('division_id'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('division_id') }}</strong>
                        </span>
                    @endif
                  </div>
              </div>

              <div class="form-group custom_form_group row{{ $errors->has('district_name') ? ' has-error' : '' }}">
                  <label class="control-label col-md-3">District Name:<span class="req_star">*</span></label>
                  <div class="col-md-9">
                    <input type="text" placeholder="District Name Type Here" class="form-control" id="district_name" name="district_name" value="{{old('district_name')}}" required>
                    @if ($errors->has('district_name'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('district_name') }}</strong>
                        </span>
                    @endif
                  </div>
              </div>
            </div>
            <div class="card-footer card_footer_button text-center">
                <button type="submit" class="btn btn-primary waves-effect">SAVE</button>
            </div>
        </div>
      </form>
    </div>
    <div class="col-md-3"></div>
</div>

<!-- division list -->
<div class="row">
    <div class="col-md-1"></div>
    <div class="col-lg-10">
      <div class="card">
          <div class="card-header">
              <div class="row">
                  <div class="col-md-8">
                      <h3 class="card-title card_top_title"><i class="fab fa-gg-circle"></i> District List</h3>
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
                                      <th>Country Name</th>
                                      <th>Division Name</th>
                                      <th>District Name</th>
                                      <th>Manage</th>
                                  </tr>
                              </thead>
                              <tbody>
                                @foreach($allDistrict as $item)
                                  <tr>
                                    <td>{{ $item->country->country_name }}</td>
                                    <td>{{ $item->division->division_name }}</td>
                                    <td>{{ $item->district_name }}</td>
                                    <td>
                                      <a href="{{ route('edit-district',[$item->district_id]) }}" title="edit"><i class="fa fa-pencil-square fa-lg edit_icon"></i></a>
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
    <div class="col-md-1"></div>
</div>

@endsection
