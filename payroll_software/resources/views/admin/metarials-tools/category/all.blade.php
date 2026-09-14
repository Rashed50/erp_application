@extends('layouts.admin-master')
@section('title') Metarial & Tools Category @endsection
@section('content')
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Metarial & Tools Category</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active">Metarial & Tools Category</li>
        </ol>
    </div>
</div>
<!-- add division -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        @if(Session::has('success'))
          <div class="alert alert-success alertsuccess" role="alert">
             <strong>Successfully!</strong> Added New Metarial & Tools Category.
          </div>
        @endif
        @if(Session::has('success_update'))
          <div class="alert alert-success alertsuccess" role="alert">
             <strong>Successfully!</strong> Update Metarial & Tools Category.
          </div>
        @endif
        @if(Session::has('delete'))
          <div class="alert alert-success alertsuccess" role="alert">
             <strong>Successfully!</strong> Delete Metarial & Tools Category.
          </div>
        @endif
        @if(Session::has('error'))
          <div class="alert alert-warning alerterror" role="alert">
             <strong>Opps!</strong> please try again.
          </div>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
      <form class="form-horizontal" id="registration" action="{{ route('insert.item-type-category') }}" method="post">
        @csrf
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-8">
                        <h3 class="card-title card_top_title"><i class="fab fa-gg-circle"></i> Metarial & Tools</h3>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="card-body card_form" style="padding-top: 0;">

              <div class="form-group custom_form_group{{ $errors->has('itype_id') ? ' has-error' : '' }}">
                  <label class="control-label d-block" style="text-align: left;">Item Type:<span class="req_star">*</span></label>
                  <div>
                    <select class="form-control" name="itype_id" required>
                        <option value="">Select Item Type</option>
                        @foreach($allType as $type)
                        <option value="{{ $type->itype_id }}">{{ $type->itype_name }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('itype_id'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('itype_id') }}</strong>
                        </span>
                    @endif
                  </div>
              </div>

              <div class="form-group custom_form_group{{ $errors->has('icatg_name') ? ' has-error' : '' }}">
                  <label class="control-label d-block" style="text-align: left;">Category Name:<span class="req_star">*</span></label>
                  <div>
                    <input type="text" class="form-control" name="icatg_name" value="{{old('icatg_name')}}" placeholder="Category Name" required>
                    @if ($errors->has('icatg_name'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('icatg_name') }}</strong>
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
    <div class="col-md-2"></div>
</div>


<div class="row">
    <div class="col-lg-12">
      <div class="card">
          <div class="card-header">
              <div class="row">
                  <div class="col-md-8">
                      <h3 class="card-title card_top_title"><i class="fab fa-gg-circle"></i> Metarial & Tools </h3>
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
                                      <th>Item Type</th>
                                      <th>Category</th>
                                      <th>Manage</th>
                                  </tr>
                              </thead>
                              <tbody>
                                @foreach($all as $item)
                                  <tr>
                                    <td>{{ $item->itemType->itype_name }}</td>
                                    <td>{{ $item->icatg_name }}</td>
                                    <td>
                                        <a href="{{ route('metarial-tools-category-edit',$item->icatg_id) }}" title="edit"><i class="fa fa-pencil-square fa-lg edit_icon"></i></a>
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
