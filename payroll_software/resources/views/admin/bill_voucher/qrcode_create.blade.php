@extends('layouts.admin-master')
@section('title') QRCode Form @endsection
@section('content')

<div class="row bread_part">
  <div class="col-sm-12 bread_col">

    <ol class="breadcrumb pull-right">
      <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
      <li class="active">Create QR Code </li>
    </ol>
  </div>
</div>
<div class="row">

  <div class="col-lg-12">
    <form class="form-horizontal company-form" id="qrcode_form" method="post" target="_blank" action="{{ route('display-generated-qrcode-only') }}">
      @csrf
      <div class="card">


        <div class="card-body card_form">
          <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-7">
              @if(Session::has('success'))
              <div class="alert alert-success alertsuccess" role="alert" style="margin-left: -20px">
                <strong>Successfully!</strong>Created QR Code
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
          <div class="row">
            
            <div class="col-md-12">

              <div class="form-group row custom_form_group{{ $errors->has('main_contractor_en') ? ' has-error' : '' }}">
                <label class="col-sm-5 control-label">Main Contractor(Eng) :<span class="req_star">*</span></label>
                <div class="col-sm-7">
                  <input type="text" class="form-control" name="main_contractor_en" value="" placeholder="Main Contractor Name (Eng)" >
                  @if ($errors->has('main_contractor_en'))
                  <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('main_contractor_en') }}</strong>
                  </span>
                  @endif
                </div>
              </div>
              <div class="form-group row custom_form_group{{ $errors->has('main_contractor_rb') ? ' has-error' : '' }}">
                <label class="col-sm-5 control-label">Main Contractor (Arabic) :<span class="req_star">*</span></label>
                <div class="col-sm-7">
                  <input type="text" class="form-control" name="main_contractor_rb" value="" placeholder="Enter Main Contractor Name (Arabic)" >
                  @if ($errors->has('main_contractor_rb'))
                  <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('main_contractor_rb') }}</strong>
                  </span>
                  @endif
                </div>
              </div>
              <div class="form-group row custom_form_group{{ $errors->has('main_con_vat_no') ? ' has-error' : '' }}">
                <label class="col-sm-5 control-label"> VAT No. :<span class="req_star">*</span></label>
                <div class="col-sm-7">
                  <input type="text" class="form-control" name="main_con_vat_no" value="" placeholder="Main Contractor Vat No." >
                  @if ($errors->has('main_con_vat_no'))
                  <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('main_con_vat_no') }}</strong>
                  </span>
                  @endif
                </div>
              </div>
              <br />
              <div class="form-group row custom_form_group{{ $errors->has('sub_contractor_en') ? ' has-error' : '' }}">
                <label class="col-sm-5 control-label">Sub-Contractor(Eng) :<span class="req_star">*</span></label>
                <div class="col-sm-7">
                  <input type="text" class="form-control" name="sub_contractor_en" value="" placeholder="Enter Sub-Contractor Name(Eng)" required>
                  @if ($errors->has('sub_contractor_en'))
                  <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('sub_contractor_en') }}</strong>
                  </span>
                  @endif
                </div>
              </div>
              <div class="form-group row custom_form_group{{ $errors->has('sub_contractor_rb') ? ' has-error' : '' }}">
                <label class="col-sm-5 control-label">Sub-Contractor(Arabic):<span class="req_star">*</span></label>
                <div class="col-sm-7">
                  <input type="text" class="form-control" name="sub_contractor_rb" value="" placeholder="Enter Sub-Contractor Name (Arabic)" >
                  @if ($errors->has('sub_contractor_rb'))
                  <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('sub_contractor_rb') }}</strong>
                  </span>
                  @endif
                </div>
              </div>

              <div class="form-group row custom_form_group{{ $errors->has('sub_con_vat_no') ? ' has-error' : '' }}">
                <label class="col-sm-5 control-label"> VAT No. :<span class="req_star">*</span></label>
                <div class="col-sm-7">
                  <input type="text" class="form-control" name="sub_con_vat_no" value="" placeholder="Sub Contractor Vat No." required>
                  @if ($errors->has('sub_con_vat_no'))
                  <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('sub_con_vat_no') }}</strong>
                  </span>
                  @endif
                </div> 
              </div>
              
              
             <div class="form-group row custom_form_group{{ $errors->has('sub_con_vat_no') ? ' has-error' : '' }}">
                <label class="col-sm-5 control-label"> Voucher Date:<span class="req_star">*</span></label>
                <div class="col-sm-7">
                   <input type="date" name="voucher_date" value="<?= date("Y-m-d") ?>" class="form-control">
                </div>
                     
              </div>
               
              <div class="form-group row custom_form_group{{ $errors->has('total') ? ' has-error' : '' }}">
                <label class="col-sm-5 control-label">Total (Excl. VAT):<span class="req_star">*</span></label>
                <div class="col-sm-7">
                  <input type="number" class="custom-form-control form-control" style="font-size:20px;" step="0.01" id="total" name="total" value="" placeholder="Total(Excluding VAT)">
                  @if ($errors->has('total'))
                  <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('total') }}</strong>
                  </span>
                  @endif
                </div>
              </div>
            </div>

          </div>
 
          <div class="row">
          
            <div class="col-md-4">
              <div class="row">
                <div class="col-sm-12">
                  <div class="form-group row custom_form_group{{ $errors->has('vat') ? ' has-error' : '' }}">
                    <label class="col-sm-8 control-label">Total Vat(%):<span class="req_star">*</span></label>
                    <div class="col-sm-4">
                      <input type="number" class="form-control" name="vat" id="vat" value="0">
                      @if ($errors->has('vat'))
                      <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('vat') }}</strong>
                      </span>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="col-sm-12">
                  <div class="form-group row custom_form_group{{ $errors->has('retention') ? ' has-error' : '' }}">
                    <label class="col-sm-8 control-label">Retention(%):<span class="req_star">*</span></label>
                    <div class="col-sm-4">
                      <input type="number" class="form-control" name="retention" id="retention" value="0">
                      @if ($errors->has('retention'))
                      <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('retention') }}</strong>
                      </span>
                      @endif
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-4">
              <div class="col-sm-12">
                <div class="form-group row custom_form_group{{ $errors->has('vat_total') ? ' has-error' : '' }}">
                  <label class="col-sm-5 control-label">VAT Amnt :<span class="req_star">*</span></label>
                  <div class="col-sm-7">
                    <input type="text" class="form-control" name="vat_total" id="vat_total" value="0">
                    @if ($errors->has('vat_total'))
                    <span class="invalid-feedback" role="alert">
                      <strong>{{ $errors->first('vat_total') }}</strong>
                    </span>
                    @endif
                  </div>
                </div>
              </div>
              <div class="col-sm-12">
                <div class="form-group row custom_form_group{{ $errors->has('vat_total') ? ' has-error' : '' }}">
                  <label class="col-sm-5 control-label">Retention Amnt:<span class="req_star">*</span></label>
                  <div class="col-sm-7">
                    <input type="text" class="form-control" name="retention_total" id="retention_total" value="0">
                    @if ($errors->has('retention_total'))
                    <span class="invalid-feedback" role="alert">
                      <strong>{{ $errors->first('retention_total') }}</strong>
                    </span>
                    @endif
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-4">
              <div class="col-sm-12">
                <div class="form-group row custom_form_group{{ $errors->has('total_with_vat') ? ' has-error' : '' }}">
                  <div class="col-sm-12">
                    <label class="control-label">Total Amount Included VAT:<span class="req_star">*</span></label>
                  </div>
                  <div class="col-sm-12">
                    <input type="text" class="form-control" name="total_with_vat" id="total_with_vat" style="font-size:20px;color:green" placeholder="Total Amount Including VAT" value="" required>
                    @if ($errors->has('total_with_vat'))
                    <span class="invalid-feedback" role="alert">
                      <strong>{{ $errors->first('total_with_vat') }}</strong>
                    </span>
                    @endif
                  </div>
                </div>
                 
                
              </div>
              <div class="col-sm-12">
                <div class="form-group row custom_form_group{{ $errors->has('grandTotal') ? ' has-error' : '' }}">
                  <div class="col-sm-12">
                    <label class="control-label">Total Amount(Incl. VAT & Excl. Retension):<span class="req_star">*</span></label>
                  </div>
                  <div class="col-sm-12">
                    <input type="text" class="form-control" name="grandTotal" style="font-size:20px;color:red" id="grandTotal" placeholder="Total Amount Incl. VAT & Excl. Retention" value="" required>
                    @if ($errors->has('grandTotal'))
                    <span class="invalid-feedback" role="alert">
                      <strong>{{ $errors->first('grandTotal') }}</strong>
                    </span>
                    @endif
                  </div>
                </div>
              </div>
            </div>

            <div class="col-sm-12 text-center mt-2">
              <button type="submit" id="onSubmit" onclick="formValidation();" class="btn btn-primary waves-effect">PROCESS</button>
            </div>

          </div>
          <!-- row end -->
        </div>

      </div>
  </div>
  </form>


</div>
</div>

<script>
  $(document).ready(function() {
    

    $("#eVoucherAddToCartForm").validate({
      rules: {
        cartDescription: {
          required: true,
        },
        cartVat: {
          number: true,
        },
        cartQuantity: {
          required: true,
          number: true,
        },
        cartRate: {
          required: true,
          number: true,
        },
      },

      messages: {
        cartDescription: {
          number: "Invalid Number!",
        },
        cartQuantity: {
          number: "Invalid Number!",
          required: "This Field Must be Required!",
        },
        cartVat: {
          required: "This Field Must be Required!",
        },
        cartRate: {
          required: "This Field Must be Required!",
        },
      },


    });

 

    // $('#vat').keyup(function() {
        

    //   if ($('#vat').val() == "" || $('#vat').val() == null) {
    //     $('#vat').val('0');

    //   }
    //   var vat = parseFloat($('#vat').val());
    //   var total = parseFloat($('#total').val()).toFixed(2);
    //   var totalWithVat = (vat * total) / 100;
    //   var vat_total = $('#vat_total').val(totalWithVat);
    //   $('#grandTotal').val(totalWithVat + total);

    //   $('#total_with_vat').val(totalWithVat + total);
    // });

    // $('#retention').keyup(function() {

    //   if ($('#retention').val() == "" || $('#retention').val() == null) {
    //     $('#retention').val('0');

    //   }
    //   var total = parseFloat($('#total').val());
    //   if (total != 0) {
    //     var totalWithVat = parseFloat($('#vat_total').val());
    //     var retention = parseFloat($('#retention').val());
    //     var totalWithRetention = parseFloat((retention * total) / 100);
    //     $('#retention_total').val(totalWithRetention);
    //     var grandTotal = (total + totalWithVat) - totalWithRetention;
    //     $('#grandTotal').val(grandTotal);
    //   } else {
    //     $('#retention_total').val('0');
    //   }
    // });
    
    $('#total').keyup(function() {
        calculate();
      })
    
    $('#vat').keyup(function() {
        calculate();
      })
      
    $('#retention').keyup(function() {
        calculate();
      })
    
    
    function calculate(){

        if ($('#total').val() == "" || $('#total').val() == null) {
            $('#total').val('0');
        }

        if ($('#vat').val() == "" || $('#vat').val() == null) {
            $('#vat').val('0');
        }
        if ($('#retention').val() == "" || $('#retention').val() == null) {
            $('#retention').val('0');
        }

        var vat =document.getElementById("vat").value;  
        var retention = ( document.getElementById("retention").value);  
        var invoice_amount =parseFloat( document.getElementById("total").value); 
       
        var vat_amnt = vat * invoice_amount * 0.01;
        var retention_amnt = retention * invoice_amount * 0.01;
       //  debugger;
        $('#vat_total').val(numberWithCommas(vat_amnt.toFixed(2)));
        $('#retention_total').val(numberWithCommas(retention_amnt.toFixed(2)));
        var totalwith_vat = parseFloat( invoice_amount + vat_amnt).toFixed(2);
        var grand_total = parseFloat( invoice_amount + vat_amnt - retention_amnt).toFixed(2);
        $('#total_with_vat').val(numberWithCommas(totalwith_vat));
        $('#grandTotal').val(numberWithCommas(grand_total));
     

    }
    
       
    function numberWithCommas(x) {
       return x;// return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
    
    
 

  });
</script>
@endsection