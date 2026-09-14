@extends('layouts.admin-master')
@section('title')Vehicle Log Book @endsection
@section('content')
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Vehicle Log Book</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active"> Vehicle Log Book</li>
        </ol>
    </div>
</div>
<!-- add division -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        @if(Session::has('success'))
          <div class="alert alert-success alertsuccess" role="alert">
             <strong>Successfully!</strong> Added New Log Book Information.
          </div>
        @endif
        @if(Session::has('success_update'))
          <div class="alert alert-success alertsuccess" role="alert">
             <strong>Successfully!</strong> Update Log Book Information.
          </div>
        @endif
        @if(Session::has('delete'))
          <div class="alert alert-success alertsuccess" role="alert">
             <strong>Successfully!</strong> Delete Log Book Information.
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
    <div class="col-md-3"></div>
    <div class="col-md-6">
      <form class="form-horizontal" enctype="multipart/form-data" id="LogBookForm-validation" action="{{ route('insert-new.LogBook') }}" method="post">
        @csrf
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-8">
                        <h3 class="card-title card_top_title"><i class="fab fa-gg-circle"></i> Add New Vehicle Log Book </h3>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="card-body card_form" style="padding-top: 0;">

              <div class="form-group custom_form_group">
                  <label class="control-label d-block" style="text-align: left;">Vehicle Name:<span class="req_star">*</span></label>
                  <div>
                      <select class="form-control" name="veh_id">
                          <option value="">Select Vehicle Name</option>
                          @foreach($vehicle as $veh)
                          <option value="{{ $veh->veh_id }}">{{ $veh->veh_name }}</option>
                          @endforeach
                      </select>
                  </div>
              </div>

              <div class="form-group custom_form_group">
                  <label class="control-label d-block" style="text-align: left;">Present Miles:<span class="req_star">*</span></label>
                  <div>
                      <input type="text" class="form-control" name="per_milimeter" value="{{ old('per_milimeter') }}" placeholder="Input Present Miles">
                  </div>
              </div>

              <div class="form-group custom_form_group">
                  <label class="control-label d-block" style="text-align: left;"> Fouel Amount (Liter):<span class="req_star">*</span></label>
                  <div>
                      <input type="text" class="form-control" name="fouel_amount" value="{{ old('fouel_amount') }}" placeholder="Input Fouel Amount">
                  </div>
              </div>

              <div class="form-group custom_form_group">
                  <label class="control-label d-block" style="text-align: left;"> Total Cost:<span class="req_star">*</span></label>
                  <div>
                      <input type="text" class="form-control" name="total_cost" value="{{ old('total_cost') }}" placeholder="Input Total Amount">
                  </div>
              </div>

              <div class="form-group custom_form_group">
                  <label class="control-label d-block" style="text-align: left;">Fuel Purchase Date:<span class="req_star">*</span></label>
                  <div>
                      <input type="date" class="form-control" name="date" value="{{ old('date') }}">
                  </div>
              </div>

              <div class="form-group custom_form_group">
                  <label class="control-label d-block" style="text-align: left;">Vouchar Photo:</label>
                  <div class="row">

                    <div class="col-sm-8">
                      <div class="input-group">
                          <span class="input-group-btn">
                              <span class="btn btn-default btn-file btnu_browse">
                                  Browse… <input type="file" name="voucharPhoto" id="imgInp4">
                              </span>
                          </span>
                          <input type="text" class="form-control" readonly>
                      </div>
                      <div class="col-sm-4" style="margin-top: 10px">
                          <img id='img-upload4' class="upload_image"/>
                      </div>
                    </div>

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
    <div class="col-lg-12">
      <div class="card">
          <div class="card-header">
              <div class="row">
                  <div class="col-md-8">
                      <h3 class="card-title card_top_title"><i class="fab fa-gg-circle"></i> Log Book List</h3>
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
                                    <th>Vehicle Name</th>
                                    <th>Kilometer</th>
                                    <th>Fouel</th>
                                    <th>Total Cost</th>
                                    <th>Date</th>
                                    <th>Manage</th>
                                  </tr>
                              </thead>
                              <tbody>
                                @foreach($all as $item)
                                  <tr>
                                    <td> {{ $item->vehicle->veh_name }} </td>
                                    <td> {{ $item->present_miles }} </td>
                                    <td> {{ $item->fouel_amount }} </td>
                                    <td> {{ $item->total_cost }} </td>
                                    <td> {{ $item->date }} </td>
                                    <td>
                                      <a href="{{ route('edit-new.LogBook',$item->lgb_id) }}" title="edit"><i class="fa fa-pencil-square fa-lg edit_icon"></i></a>

                                      <a href="{{ route('delete-new.LogBook',$item->lgb_id) }}" title="delete" title="delete data"><i class="fa fa-trash fa-lg delete_icon"></i></a>
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
<!-- script area -->
<script type="text/javascript">
/* form validation */
$(document).ready(function(){
  $("#LogBookForm-validation").validate({
    /* form tag off  */
    // submitHandler: function(form) { return false; },
    /* form tag off  */
    rules: {
      veh_id: {
        required : true,
      },
      per_milimeter: {
        required : true,
        number: true,
        maxlength: 9,
      },
      fouel_amount: {
        required : true,
        number: true,
        maxlength: 9,
      },
      total_cost: {
        required : true,
        number: true,
        maxlength: 9,
      },
      date: {
        required : true,
      },
    },

    messages: {
      veh_id: {
        required : "You Must Be select This Field!",
      },
      date: {
        required : "You Must Be Select This Field!",
      },
      per_milimeter: {
        required : "Please Input This Field!",
        number : "You Must Be Input Number!",
        max : "You Must Be Input Maximum Length 9!",
      },
      fouel_amount: {
        required : "Please Input This Field!",
        number : "You Must Be Input Number!",
        max : "You Must Be Input Maximum Length 9!",
      },
      total_cost: {
        required : "Please Input This Field!",
        number : "You Must Be Input Number!",
        max : "You Must Be Input Maximum Length 9!",
      },
    },
  });
});
</script>


@endsection
