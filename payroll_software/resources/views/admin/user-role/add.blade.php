@extends('layouts.admin-master')
@section('title') Add Division @endsection
@section('content')
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Add Division</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="active">Add Division</li>
        </ol>
    </div>
</div>
<!-- add division -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        @if(Session::has('success_add'))
          <div class="alert alert-success alertsuccess" role="alert">
             <strong>Successfully!</strong> Added Division.
          </div>
        @endif
        @if(Session::has('success_update'))
          <div class="alert alert-success alertsuccess" role="alert">
             <strong>Successfully!</strong> Update Division.
          </div>
        @endif
        @if(Session::has('delete_update'))
          <div class="alert alert-success alertsuccess" role="alert">
             <strong>Successfully!</strong> `Update` Division.
          </div>
        @endif
        @if(Session::has('success_error'))
          <div class="alert alert-warning alerterror" role="alert">
             <strong>Opps!</strong> please try again.
          </div>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-md-4"></div>
    <div class="col-md-4">
      <form class="form-horizontal" id="registration" action="{{ route('insert-division') }}" method="post">
        @csrf
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-8">
                        <h3 class="card-title card_top_title"><i class="fab fa-gg-circle"></i> Division Information</h3>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="card-body card_form" style="padding-top: 0;">
              <div class="form-group custom_form_group{{ $errors->has('country_id') ? ' has-error' : '' }}">
                  <label class="control-label d-block" style="text-align: left;">Country Name:<span class="req_star">*</span></label>
                  <div>
                    <select class="form-control" name="country_id" required>
                        <option value="">Select Country</option>
                        @foreach($countries as $country)
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
              <div class="form-group custom_form_group{{ $errors->has('division_name') ? ' has-error' : '' }}">
                  <label class="control-label d-block" style="text-align: left;">Division Name:<span class="req_star">*</span></label>
                  <div>
                    <input type="text" placeholder="please enter division name" class="form-control" id="division_name" name="division_name" value="{{old('division_name')}}" required>
                    @if ($errors->has('division_name'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('division_name') }}</strong>
                        </span>
                    @endif
                  </div>
              </div>
            </div>
            <div class="card-footer card_footer_button text-center">
                <button type="submit" class="btn btn-primary waves-effect">INSERT</button>
            </div>
        </div>
      </form>
    </div>
    <div class="col-md-4"></div>
</div>

<!-- division list -->
<div class="row">
    <div class="col-md-1"></div>
    <div class="col-lg-10">
      <div class="card">
          <div class="card-header">
              <div class="row">
                  <div class="col-md-8">
                      <h3 class="card-title card_top_title"><i class="fab fa-gg-circle"></i> All Designation List</h3>
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
                                      <th>Manage</th>
                                  </tr>
                              </thead>
                              <tbody>
                                @foreach($getallDiv as $div)
                                  <tr>
                                    <td>{{ $div->country->country_name }}</td>
                                    <td>{{ $div->division_name }}</td>
                                    <td>
                                      <a href="{{ route('edit-division',[$div->division_id]) }}" title="edit"><i class="fa fa-pencil-square fa-lg edit_icon"></i></a>
                                      <a href="{{ route('delete-division',[$div->division_id]) }}" title="delete"  id="delete" title="delete data"><i class="fa fa-trash fa-lg delete_icon"></i></a>
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
