@extends('layouts.admin-master')
@section('title') Inventory Report @endsection
@section('content')
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title"> Inventory Items Report </h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active"> Inventory Report</li>
        </ol>
    </div>
</div> 

<div class="row">
  <div class="col-md-2"></div>
  <div class="col-md-8">
      <form class="form-horizontal" id="item_received_report_form" target="_blank" action="{{route('employee.item.received.report')}}" method="post">
        @csrf
        <div class="card">
             
            <div class="card-body card_form" style="padding-top: 0;">

              <h2 class="card-title card_top_title" style="color: red; text-align:center"> 1. Inventory Item Distribution Report</h2>

              <div class="form-group row custom_form_group{{ $errors->has('icatg_id') ? ' has-error' : '' }}">
                  <label class="control-label col-md-3 custom-control-label">Category Name:<span class="req_star">*</span></label>
                  <div class="col-md-7">
                    <select class="form-control custom-form-control" name="icatg_id" id="icatg_id">
                        <option value="">Select Category Name</option> 
                        @foreach($item_category as $cat)
                                <option value="{{ $cat->icatg_id }}">{{ $cat->icatg_name }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('icatg_id'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('icatg_id') }}</strong>
                        </span>
                    @endif
                  </div>
              </div>

              <div class="form-group row custom_form_group{{ $errors->has('iscatg_id') ? ' has-error' : '' }}">
                  <label class="control-label col-md-3 custom-control-label">Sub Category Name:<span class="req_star">*</span></label>
                  <div class="col-md-7">
                    <select class="form-control custom-form-control" name="iscatg_id" id="iscatg_id">
                        <option value="">Select Sub Category</option>
                    </select>
                    @if ($errors->has('iscatg_id'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('iscatg_id') }}</strong>
                        </span>
                    @endif
                  </div>
              </div>

            <div class="form-group row custom_form_group{{ $errors->has('sub_store_id') ? ' has-error' : '' }}">
                <label class="control-label col-sm-3 custom-control-label">Sub Store:<span class="req_star">*</span></label>
                <div class="col-sm-7">
                  <select class="form-control custom-form-control" name="sub_store_id" id="sub_store_id" >
                      <option value="">Select Sub Store</option>
                      @foreach($sub_store as $st)
                      <option value="{{ $st->sub_store_id  }}">{{ $st->sub_store_name }}</option>
                      @endforeach
                  </select>
                </div>
            </div>


              <div class="row">
                  <div class="col-md-2"></div>
                  <div class="col-md-4">
                    <div class="form-group custom_form_group">
                        <label class="control-label d-block" style="text-align: left;">Start Date:</label>
                        <div>
                            <input type="date" class="form-control" name="start_date" value="{{ old('start_date') }}" required>
                        </div>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group custom_form_group">
                        <label class="control-label d-block" style="text-align: left;">End Date:</label>
                        <div>
                            <input type="date" class="form-control" name="end_date" value="{{ old('end_date') }}" required>
                        </div>
                    </div>
                  </div>
                  <div class="col-md-2"></div>
              </div>
            </div>
            <div class="card-footer card_footer_button text-center">
                <button type="submit" class="btn btn-primary waves-effect">PROCESS</button>
            </div>
        </div>
      </form>
    </div>
  <div class="col-md-2"></div>
</div>

 
{{-- Store wise inventory report --}}
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <form class="form-horizontal" id="item_received_report_form" target="_blank" action="{{route('inventory.item.stock.report')}}" method="post">
          @csrf
          <div class="card">

              <div class="card-body card_form" style="padding-top: 0;">

                <h2 class="card-title card_top_title" style="color: red; text-align:center"> 1. Inventory Item Received Report</h2>

                <div class="form-group row custom_form_group{{ $errors->has('icatg_id') ? ' has-error' : '' }}">
                    <label class="control-label col-md-3 custom-control-label">Category Name:<span class="req_star">*</span></label>
                    <div class="col-md-7">
                      <select class="form-control custom-form-control" name="icatg_id" id="icatg_id">
                          <option value="">Select Category Name</option>
                          @foreach($item_category as $cat)
                                  <option value="{{ $cat->icatg_id }}">{{ $cat->icatg_name }}</option>
                          @endforeach
                      </select>
                      @if ($errors->has('icatg_id'))
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $errors->first('icatg_id') }}</strong>
                          </span>
                      @endif
                    </div>
                </div>

                <div class="form-group row custom_form_group{{ $errors->has('iscatg_id') ? ' has-error' : '' }}">
                    <label class="control-label col-md-3 custom-control-label">Sub Category Name:<span class="req_star">*</span></label>
                    <div class="col-md-7">
                      <select class="form-control custom-form-control" name="iscatg_id" id="iscatg_id">
                          <option value="">Select Sub Category</option>
                      </select>
                      @if ($errors->has('iscatg_id'))
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $errors->first('iscatg_id') }}</strong>
                          </span>
                      @endif
                    </div>
                </div>

              <div class="form-group row custom_form_group{{ $errors->has('sub_store_id') ? ' has-error' : '' }}">
                  <label class="control-label col-sm-3 custom-control-label">Sub Store:<span class="req_star">*</span></label>
                  <div class="col-sm-7">
                    <select class="form-control custom-form-control" name="sub_store_id" id="sub_store_id" >
                        <option value="">Select Sub Store</option>
                        @foreach($sub_store as $st)
                        <option value="{{ $st->sub_store_id  }}">{{ $st->sub_store_name }}</option>
                        @endforeach
                    </select>
                  </div>
              </div>


                <div class="row">
                    <div class="col-md-2"></div>
                    <div class="col-md-4">
                      <div class="form-group custom_form_group">
                          <label class="control-label d-block" style="text-align: left;">Start Date:</label>
                          <div>
                              <input type="date" class="form-control" name="start_date"   >
                          </div>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group custom_form_group">
                          <label class="control-label d-block" style="text-align: left;">End Date:</label>
                          <div>
                              <input type="date" class="form-control" name="end_date"  >
                          </div>
                      </div>
                    </div>
                    <div class="col-md-2"></div>
                </div>
              </div>
              <div class="card-footer card_footer_button text-center">
                  <button type="submit" class="btn btn-primary waves-effect">PROCESS</button>
              </div>
          </div>
        </form>
      </div>
    <div class="col-md-2"></div>
</div>






<script type="text/javascript">
$(document).ready(function() {
  
 
  /* call sub category */

  $('select[name="icatg_id"]').on('change', function(){
      var icatg_id = $(this).val();
      if(icatg_id) {
          $.ajax({
              url: "{{  url('/admin/item/sub-category/ajax') }}/"+icatg_id,
              type:"GET",
              dataType:"json",
              success:function(data) {
                   $('select[name="iscatg_id"]').empty();
                    $.each(data, function(key, value){
                        $('select[name="iscatg_id"]').append('<option value="'+ value.iscatg_id +'">' + value.iscatg_name + '</option>');
                    });

              },

          });
      } else {
          alert('danger');
      }
  });

});
</script>

@endsection
