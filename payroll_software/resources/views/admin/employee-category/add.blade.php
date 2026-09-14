@extends('layouts.admin-master')
@section('title') Employee Designation @endsection
@section('content')
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Employee Designation</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active">Employee Designation</li>
        </ol>
    </div>
</div>
<!-- add division -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        @if(Session::has('success_add'))
          <div class="alert alert-success alertsuccess" role="alert">
             <strong>Successfully!</strong> Added Employee Designation.
          </div>
        @endif
        @if(Session::has('success_update'))
          <div class="alert alert-success alertsuccess" role="alert">
             <strong>Successfully!</strong> Update Employee Designation.
          </div>
        @endif
        @if(Session::has('success_delete'))
          <div class="alert alert-success alertsuccess" role="alert">
             <strong>Successfully!</strong> Delete Employee Designation.
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
    <div class="col-md-3"></div>
    <div class="col-md-6">
      <form class="form-horizontal" id="designation-form" action="{{ route('insert-category') }}" method="post">
        @csrf
        <div class="card">
            <div class="card-header">
                
            </div>
            <div class="card-body card_form" style="padding-top: 0;">

              <div class="form-group custom_form_group row{{ $errors->has('emp_type_id') ? ' has-error' : '' }}">
                  <label class="control-label col-md-4" style="text-align: left;">Employee Type:<span class="req_star">*</span></label>
                  <div class="col-md-8">
                    <select class="form-select" name="emp_type_id" required>
                      <option value="">Select Employee Type</option>
                      @foreach($getType as $type)
                      <option value="{{ $type->id }}">{{ $type->name }}</option>
                      @endforeach
                    </select>
                    @if ($errors->has('catg_name'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('catg_name') }}</strong>
                        </span>
                    @endif
                  </div>
              </div>
              
               <div class="form-group custom_form_group row{{ $errors->has('emp_trade_head') ? ' has-error' : '' }}">
                <label class="control-label col-md-4" style="text-align: left;">Designation Head:<span class="req_star">*</span></label>
                <div class="col-md-8">
                  <select class="form-select" name="emp_trade_head" required>
                    <option value="">Select Designation Head</option>
                    @foreach($trade_heads as $arecord)
                    <option value="{{ $arecord->dh_auto_id }}">{{ $arecord->des_head_name }}</option>
                    @endforeach
                  </select>

                  @if ($errors->has('emp_trade_head'))
                      <span class="invalid-feedback" role="alert">
                          <strong>{{ $errors->first('emp_trade_head') }}</strong>
                      </span>
                  @endif
                </div>
            </div>

              <div class="form-group custom_form_group row{{ $errors->has('catg_name') ? ' has-error' : '' }}">
                  <label class="control-label col-md-4" style="text-align: left;">Trade Name:<span class="req_star">*</span></label>
                  <div class="col-md-8">
                    <input type="text" placeholder="Input Trade Name" class="form-control" id="catg_name" name="catg_name" value="{{old('catg_name')}}">
                    @if ($errors->has('catg_name'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('catg_name') }}</strong>
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

<!-- trade list -->
<div class="row">
    <div class="col-md-1"></div>
    <div class="col-lg-10">
      <div class="card">
          <div class="card-body">
                <div class="table-responsive">
                <table id="dt-vertical-scroll" class="table" cellspacing="0" width="100%">
                          <thead>
                            <tr>
                                <th>SN No</th>
                                <th>Employee Type</th>
                                <th>Desig. Head</th>
                                <th>Trade</th>
                                <th>Manage</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($all as $item)
                            <tr>
                            <td> {{ $loop->iteration }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->des_head_name }}</td>
                            <td>{{ $item->catg_name }}</td>
                            <td>
                                @can('designation-edit')
                                    <a href="{{ route('edit-employee-category',[$item->catg_id]) }}" title="edit"><i class="fa fa-pencil-square fa-lg edit_icon"></i></a>
                                @endcan
                            </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
          </div>
      </div>
    </div>
    <div class="col-md-1"></div>
</div>
@endsection
