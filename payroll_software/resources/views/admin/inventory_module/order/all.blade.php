@extends('layouts.admin-master')
@section('title') Metarial & Tools Sub Category @endsection
@section('content')
<div class="row bread_part">
  <div class="col-sm-12 bread_col">
    <h4 class="pull-left page-title bread_title">Metarial & Tools Purchase</h4>
    <ol class="breadcrumb pull-right">
      <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
      <li class="active">Metarial & Tools Purchase</li>
    </ol>
  </div>
</div>
<!-- session message section -->
<div class="row">
  <div class="col-md-2"></div>
  <div class="col-md-8">
    @if(Session::has('success'))
    <div class="alert alert-success alertsuccess" role="alert">
      <strong>Successfully!</strong> Added New Metarial & Tools Sub Category.
    </div>
    @endif
    @if(Session::has('success_update'))
    <div class="alert alert-success alertsuccess" role="alert">
      <strong>Successfully!</strong> Update Metarial & Tools Sub Category.
    </div>
    @endif
    @if(Session::has('delete'))
    <div class="alert alert-success alertsuccess" role="alert">
      <strong>Successfully!</strong> Delete Metarial & Tools Sub Category.
    </div>
    @endif
    @if(Session::has('error'))
    <div class="alert alert-warning alerterror" role="alert">
      <strong>Opps!</strong> please try again.
    </div>
    @endif
    @if(Session::has('not_match_employee'))
    <div class="alert alert-warning alerterror" role="alert">
      <strong>Opps!</strong> Employee Id Dosn,t Match!.
    </div>
    @endif
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-body card_form" style="padding-top: 10px;">

        <form id="addToCartForm">

          <div class="form-group row custom_form_group{{ $errors->has('itype_id') ? ' has-error' : '' }}">
            <label class="control-label col-sm-3 custom-control-label">Item Type:<span class="req_star">*</span></label>
            <div class="col-sm-7">
              <select class="form-control custom-form-control" name="itype_id" required id="itype_id">
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

          <div class="form-group row custom_form_group{{ $errors->has('icatg_id') ? ' has-error' : '' }}">
            <label class="control-label col-md-3 custom-control-label">Category Name:<span class="req_star">*</span></label>
            <div class="col-md-7">
              <select class="form-control custom-form-control" name="icatg_id" id="icatg_id">
                <option value="">Select Category Name</option>
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
              <select class="form-control custom-form-control" name="iscatg_id" id="iscatg_id" required>
                <option value="">Select Sub Category</option>
              </select>
              @if ($errors->has('iscatg_id'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('iscatg_id') }}</strong>
              </span>
              @endif
            </div>
          </div>

          <div class="form-group row custom_form_group{{ $errors->has('quantity') ? ' has-error' : '' }}">
            <label class="control-label col-md-3 custom-control-label">Quantity:<span class="req_star">*</span></label>
            <div class="col-md-7">
              <input type="number" class="form-control custom-form-control" name="quantity" id="quantity" value="{{old('stock_amount')}}" placeholder="Quantity" required min="1">

              @if ($errors->has('quantity'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('quantity') }}</strong>
              </span>
              @endif
            </div>
          </div>

          <div class="form-group row custom_form_group{{ $errors->has('stock_amount') ? ' has-error' : '' }}">
            <label class="control-label col-md-3 ">Amount:<span class="req_star">*</span></label>
            <div class="col-md-7">
              <input type="text" class="form-control custom-form-control" id="stock_amount" name="stock_amount" value="{{old('stock_amount')}}" placeholder="Unit Price" required>
              @if ($errors->has('stock_amount'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('stock_amount') }}</strong>
              </span>
              @endif
            </div>
          </div>

          <div class="form-group row custom_form_group" style="text-align:right;">
            <label class="control-label col-md-3"></label>
            <div class="col-md-7">
              <button class="btn btn-primary waves-effect" style="font-size: 12px; text-transform: capitalize; padding: 1px 5px;" onclick="addToCart()">ADD TO CART</button>
            </div>
          </div>
        </form>


        <!-- form submit -->
        <form id="metarialTools" action="{{ route('metarial.tools-order-confirm') }}" method="post">
          @csrf

          <div class="form-group row custom_form_group{{ $errors->has('emp_id') ? ' has-error' : '' }}">
            <label class="control-label col-md-3 custom-control-label">Purchase ID:</label>
            <div class="col-md-7">
              <input type="text" class="custom-form-control form-control typeahead" placeholder="Input Employee ID" name="emp_id" id="emp_id_search" onkeyup="empSearch()" onfocus="showResult()" onblur="hideResult()" value="{{ old('emp_id') }}">
              @if ($errors->has('emp_id'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('emp_id') }}</strong>
              </span>
              @endif
              <div id="showEmpId"></div>
            </div>
          </div>

          <div class="form-group row custom_form_group">
            <label class="control-label col-md-3 custom-control-label">Net Amount:</label>
            <div class="col-md-7">
              <input type="text" class="custom-form-control form-control" id="net_amount" value="" disabled>
              <input type="hidden" id="net_amount_hidden" value="">

            </div>
          </div>

          <div class="form-group row custom_form_group{{ $errors->has('other_amount') ? ' has-error' : '' }}">
            <label class="control-label col-md-3 custom-control-label">Other Amount:<span class="req_star">*</span></label>
            <div class="col-md-7">
              <input type="text" min="0" class="custom-form-control form-control" id="other_amount_id" name="other_amount" value="{{old('other_amount')}}" placeholder="Other Amount" onkeyup="netOtherSum()" min="0">
              @if ($errors->has('other_amount'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('other_amount') }}</strong>
              </span>
              @endif
            </div>
          </div>

          <div class="form-group row custom_form_group{{ $errors->has('total_amount') ? ' has-error' : '' }}">
            <label class="control-label col-md-3 custom-control-label">Total Amount:<span class="req_star">*</span></label>
            <div class="col-md-7">
              <input type="text" class="form-control custom-form-control" id="total_amount" value="" disabled>

              <input type="hidden" id="hidden_total_amount" name="total_amount" value="">
              <input type="hidden" id="net_amount_hidden" name="net_amount" value="">

              @if ($errors->has('total_amount'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('total_amount') }}</strong>
              </span>
              @endif
            </div>
          </div>

          <div class="form-group row custom_form_group{{ $errors->has('paid_amount') ? ' has-error' : '' }}">
            <label class="control-label col-md-3 custom-control-label">Paid Amount:<span class="req_star">*</span></label>
            <div class="col-md-7">
              <input type="text" class="form-control custom-form-control" id="paid_amount" name="paid_amount" value="{{old('paid_amount')}}" placeholder="Paid Amount">
              @if ($errors->has('paid_amount'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('paid_amount') }}</strong>
              </span>
              @endif
            </div>
          </div>

          <div class="form-group row custom_form_group" style="text-align:right;">
            <label class="control-label col-md-3"></label>
            <div class="col-md-7">
              <button type="submit" class="btn btn-primary waves-effect" style="font-size: 12px; text-transform: capitalize; padding: 1px 5px;">SAVE</button>
            </div>
          </div>
        </form>


      </div>
      <!-- Metarial List -->
      <div class="card-footer card_footer_button text-center">
        <div class="row">
          <div class="col-12">
            <div class="table-responsive">
              <table class="table table-bordered custom_table mb-0">
                <thead>
                  <tr>
                    <th>Item Type</th>
                    <th>Category</th>
                    <th>Sub Category</th>
                    <th>Quantity & Price</th>
                    <th>Manage</th>
                    <th>Sub Total</th>
                  </tr>
                </thead>
                <tbody id="metarial_tools_list_view"></tbody>
                <tfoot>
                  <tr>
                    <td colspan="5"></td>
                    <td> <span id="cartSubTotal" style="padding: 10px; display: block;border: 1px solid #ddd;"> </span> </td>
                  </tr>
                </tfoot>
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
  $(document).ready(function() {
    $("#metarialTools").validate({
      rules: {
        emp_id: {
          required: true,
        },
        other_amount: {
          number: true,
        },
        paid_amount: {
          required: true,
          number: true,
        },
      },

      messages: {
        other_amount: {
          number: "Invalid Number!",
        },
        paid_amount: {
          number: "Invalid Number!",
          required: "This Field Must be Required!",
        },
        emp_id: {
          required: "This Field Must be Required!",
        },
      },


    });
  });
  /* form validation add to cart */
  $(document).ready(function() {
    $("#addToCartForm").validate({
      /* form tag off  */
      submitHandler: function(form) {
        return false;
      },
      /* form tag off  */
      rules: {
        itype_id: {
          required: true,
        },
        icatg_id: {
          required: true,
        },
        iscatg_id: {
          required: true,
        },
        quantity: {
          required: true,
          number: true,
          min: 1,
        },
        stock_amount: {
          required: true,
          number: true,
        },
      },

      messages: {
        itype_id: {
          required: "You Must Be Select This Field!",
        },
        icatg_id: {
          required: "You Must Be Select This Field!",
        },
        iscatg_id: {
          required: "You Must Be Select This Field!",
        },
        quantity: {
          required: "Please Input This Field!",
          number: "You Must Be Input Number!",
          min: "You Must Be Input Minimum 1!",
        },
        stock_amount: {
          required: "Please Input This Field!",
          number: "You Must Be Input Number!",
        },
      },


    });
  });


  /* Calculation */
  function netOtherSum() {
    var net_amount = parseFloat($("#net_amount").val());
    var other_amount = parseFloat($("#other_amount_id").val());

    if (other_amount >= 0) {
      var total_amount = (net_amount + other_amount);
      $("#total_amount").val(total_amount);
      $("#hidden_total_amount").val(total_amount);
    } else {
      $("#total_amount").val(net_amount);
      $("#hidden_total_amount").val(total_amount);
    }
  }




  $(document).ready(function() {
    $('select[name="itype_id"]').on('change', function() {
      var itype_id = $(this).val();
      if (itype_id) {
        $.ajax({
          url: "{{  url('/admin/item/category/ajax') }}/" + itype_id,
          type: "GET",
          dataType: "json",
          success: function(data) {
            var d = $('select[name="icatg_id"]').empty();
            $.each(data, function(key, value) {

              $('select[name="icatg_id"]').append('<option value="' + value.icatg_id + '">' + value.icatg_name + '</option>');

            });

          },

        });
      } else {

      }

    });
    /* call sub category */
    /* call district */
    $('select[name="icatg_id"]').on('change', function() {
      var icatg_id = $(this).val();
      if (icatg_id) {
        $.ajax({
          url: "{{  url('/admin/item/sub-category/ajax') }}/" + icatg_id,
          type: "GET",
          dataType: "json",
          success: function(data) {
            $('select[name="iscatg_id"]').empty();
            $.each(data, function(key, value) {
              $('select[name="iscatg_id"]').append('<option value="' + value.iscatg_id + '">' + value.iscatg_name + '</option>');
            });

          },

        });
      } else {
        alert('danger');
      }
    });

  });
</script>
<!-- Add to Cart -->
<script type="text/javascript">


</script>

@endsection
