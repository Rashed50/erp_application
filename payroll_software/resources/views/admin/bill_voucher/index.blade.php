@extends('layouts.admin-master')
@section('title') Electric Voucher @endsection
@section('content')

<div class="row bread_part">
    <div class="col-sm-12 bread_col">

        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active">Invoice with QRCode </li>
        </ol>
    </div>
</div>
 <!-- Session alert part start -->
    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-7">
            @if(Session::has('success'))
            <div class="alert alert-success alertsuccess" role="alert" style="margin-left: -20px">
                <strong>Successfully!</strong>Created QR Code Bill Voucher.
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
<!-- Session alert part end -->
<div class="row">
    <div class="card pt-2">
        <div class="card-header">
            <h4 class="card-title">Item Details Information</h4>
        </div>
        <br>
        <form id="eVoucherAddToCartForm">

            <div class="row">
                <div class="col-md-2">
                    <div class="form-group custom_form_group{{ $errors->has('cartAreaNo') ? ' has-error' : '' }}">
                        <label class="col-sm-4 control-label">Area </label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" name="cartAreaNo" id="cartAreaNo"
                                placeholder="Area Details" required>
                            @if ($errors->has('cartAreaNo'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('cartAreaNo') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group custom_form_group{{ $errors->has('itemNo') ? ' has-error' : '' }}">
                        <label class="col-sm-5 control-label">Item No </label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" name="itemNo" id="itemNo"
                                placeholder="Item No" required>
                            @if ($errors->has('itemNo'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('itemNo') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div
                        class="form-group   custom_form_group{{ $errors->has('cartDescription') ? ' has-error' : '' }}">
                        <label class="col-sm-6 control-label">Descrition:<span class="req_star">*</span></label>
                        <div class="col-sm-12">
                            <textarea name="" class="form-control" id="cartDescription" name="cartDescription" cols="30"
                                rows="2" required>{{old('cartDescription')}}</textarea>
                            @if ($errors->has('cartDescription'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('cartDescription') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="form-group custom_form_group{{ $errors->has('cartQuantity') ? ' has-error' : '' }}">
                        <label class="col-sm-8 control-label">Qty:<span class="req_star">*</span></label>
                        <div class="col-sm-12">
                            <input type="number" class="form-control" name="cartQuantity" id="cartQuantity"
                                value="0{{old('cartQuantity')}}" placeholder="Quantity" required min="1">
                            @if ($errors->has('cartQuantity'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('cartQuantity') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="form-group custom_form_group{{ $errors->has('cartRate') ? ' has-error' : '' }}">
                        <label class="col-sm-8 control-label">Rate:<span class="req_star">*</span></label>
                        <div class="col-sm-12">
                            <input type="number" class="form-control" name="cartRate" id="cartRate"
                                value="0{{old('cartRate')}}" placeholder="Rate" required min="1">
                            @if ($errors->has('cartRate'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('cartRate') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                </div>                
                <div class="col-md-1">
                    <div class="form-group custom_form_group">
                        <label class="col-sm-6 control-label">Unit</label>
                        <div class="col-sm-12">
                            <select class="form-control" id="qty_unit" name="qty_unit">
                                <option value="LM">LM</option>
                                <option value="m">m</option>
                                <option value="m2">m2 </option>
                                <option value="m3">m&#x1D32</option>
                                <option value="cm">cm</option>
                                <option value="cm2">cm2</option>
                                <option value="cm3">cm&#x1D32</sup></option>    
                                <option value="Ton">Ton</option>
                                <option value="TM">TM</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group custom_form_group{{ $errors->has('cartTotal') ? ' has-error' : '' }}">
                        <label class="col-sm-6 control-label">Total</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" name="cartTotal" id="cartTotal"
                                value="0{{old('cartTotal')}}" placeholder="cartTotal" required min="1">
                            @if ($errors->has('cartTotal'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('cartTotal') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
                <!--  <div class="col-md-1">
                <label class="control-label custom-control-label">Vat:<span class="req_star">*</span></label>
                <input type="hidden" class="form-control" name="cartVat" id="cartVat" value="0" placeholder="cartVat" required>
                @if ($errors->has('cartVat'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('cartVat') }}</strong>
                </span>
                @endif
            </div> -->
                <!-- <div class="col-md-2">
                <label class="control-label custom-control-label">Sub Total:<span class="req_star">*</span></label>
                <input type="number" class="form-control" name="total" id="" value="{{old('total')}}" placeholder="total">
            </div> -->

            </div>

            <div class="row">
                <div class="col-md-4"></div>
                <div class="col-md-4">
                    <div class="form-group row custom_form_group" style="text-align:right;padding-top: 10px;">
                        <button class="btn btn-primary waves-effect" type="button" id="eVoucherAddToCart"
                            style="margin-left: 160px; font-size: 16px; text-transform: capitalize; padding: 6px 0px; border-radius: 10px; width: 150px;">ADD
                            TO CART</button>
                    </div>
                </div>
                <div class="col-md-4"></div>
            </div>

        </form>
        <!-- cart table section start -->
        <div class="card text-center mt-1">
            <div class="row">
                <div class="col-12">
                    <div class="table-responsive">
                        <table class="table table-bordered custom_table mb-0">
                            <thead>
                                <tr>
                                    <th>Area No</th>
                                    <th>Item No</th>
                                    <th>Description</th>                                   
                                    <th>Rate</th>
                                    <th>Qty</th>
                                    <th>Sub Total</th>
                                    <th>Manage</th>
                                </tr>
                            </thead>
                            <tbody id="eVoucherAddToCartList"></tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3"></td>
                                    <td> <span id="eVouchercartTotQty" style=" font-weight:bold; "> </span> </td>
                                    <td> <span id="eVouchercartSubTotal" style="font-weight:bold; "> </span> </td>
                                </tr>
                            </tfoot>
                        </table>

                    </div>
                </div>
            </div>
        </div>
        
        <!-- Billing informations for bill invoice Start -->
        <form class="form-horizontal company-form" id="registration" method="post" target="_blank"
            action="{{ route('e.voucher.process') }}">
            @csrf
            <div class="card">              
                <!-- Double column Bill voucher form part start -->
                <div class="card-body card_form">
                    <div class="row">
                        <!-- first coll -->
                        <div class="col-md-6">
                            <input type="hidden" name="itemsSubTotalAmount" id="eVouchercartSubTotal">

                            <div class="form-group row custom_form_group{{ $errors->has('main_contractor_id') ? ' has-error' : '' }}">
                                <label class="col-sm-5 control-label">Main Contractor<span class="req_star">*</span></label>

                                <div class="col-sm-7">
                                    <select class="form-control" name="main_contractor_id" required>
                                        <option value=""> Select Main Contractor</option>
                                        @foreach($mainContractors as $contractor)
                                        <option value="{{ $contractor->mc_auto_id }}">{{ $contractor->en_name }}- {{ $contractor->vat_no }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('main_contractor_id'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('main_contractor_id') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group row custom_form_group{{ $errors->has('sub_contractor_id') ? ' has-error' : '' }}">
                                <label class="col-sm-5 control-label">Invoice for Company:<span class="req_star">*</span></label>

                                <div class="col-sm-7">
                                    <select class="form-control" name="sub_contractor_id" required>
                                        <option value=""> Select Company</option>
                                        @foreach($subContractor as $subContractor)
                                        <option value="{{ $subContractor->sb_comp_id }}">{{ $subContractor->sb_comp_name }}- {{ $subContractor->sb_vat_no }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('sub_contractor_id'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('sub_contractor_id') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group row custom_form_group{{ $errors->has('bank_account_id') ? ' has-error' : '' }}">
                                <label class="col-sm-5 control-label">Bank Name<span class="req_star">*</span></label>

                                <div class="col-sm-7">
                                    <select class="form-control" name="bank_account_id" required>
                                        <option value="">Select Bank Name</option>
                                        @foreach($bankInfos as $bankAccnt)
                                        <option value="{{ $bankAccnt->id }}">{{ $bankAccnt->bank_name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('bank_account_id'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('bank_account_id') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>                              

                            <div class="form-group row custom_form_group{{ $errors->has('submitted_date') ? ' has-error' : '' }}">
                                <label class="col-sm-5 control-label">Submit Date<span
                                        class="req_star">*</span></label>
                                <div class="col-sm-7">
                                    <input type="date" class="form-control" id="submitted_date" name="submitted_date"
                                        value="{{Date('Y-m-d')}}" required>
                                </div>
                                @if ($errors->has('submitted_date'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('submitted_date') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="form-group row custom_form_group{{ $errors->has('employee_id') ? ' has-error' : '' }}">
                                <label class="col-sm-5 control-label">Submit By:<span
                                        class="req_star">*</span></label>
                                <div class="col-sm-7">
                                    <select class="form-control" name="employee_id" required>
                                        <option value="">Select Employee</option>
                                        @foreach($employee as $emp)
                                        <option value="{{ $emp->emp_auto_id }}">{{ $emp->name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('employee_id'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('employee_id') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row custom_form_group">
                                <label class="col-sm-5 control-label">Duration From </label>
                                <div class="col-sm-3">
                                    <input type="date" class="form-control" name="invoice_from_date"
                                        value="{{ Date('Y-m-d') }}" required>
                                </div>
                                <label class="col-sm-1 control-label">To </label>
                                         
                                <div class="col-sm-3">
                                    <input type="date" class="form-control" name="invoice_to_date"
                                        value="{{ Date('Y-m-d') }}" required>
                                </div>
                                 
                            </div>


                            <div class="form-group row custom_form_group">
                                <label class="col-sm-5 control-label">Invoice Date<span
                                        class="req_star">*</span></label>
                                <div class="col-sm-7">
                                    <input type="date" class="form-control" name="start_date"
                                        value="{{ Date('Y-m-d') }}" required>
                                </div>
                                @if ($errors->has('start_date'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('start_date') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="form-group row custom_form_group{{ $errors->has('invoice_invoice_typestatus') ? ' has-error' : '' }}">
                                <label class="col-sm-5 control-label">Invoice Type</label>
                                <div class="col-sm-7">
                                    <select class="form-control" id="invoice_type" name="invoice_type" >                                    
                                        <option value="1">Progress</option>
                                        <option value="10">Not Defined</option>
                                    </select>
                                    @if ($errors->has('invoice_type'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('invoice_type') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row custom_form_group{{ $errors->has('invoice_status') ? ' has-error' : '' }}">
                                <label class="col-sm-5 control-label">Invoice Status<span
                                        class="req_star">*</span></label>
                                <div class="col-sm-7">
                                    <select class="form-control" id="invoice_status" name="invoice_status" required>
                                        <!--<option value="">Select Here</option>-->
                                        <option value="0">Pending</option>
                                        <option value="1">Released</option>
                                    </select>
                                    @if ($errors->has('invoice_status'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('invoice_status') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <br>
                        <!-- secound column  -->
                        <div class="col-md-6">

                            <div class="form-group row custom_form_group{{ $errors->has('project_id') ? ' has-error' : '' }}">
                                <label class="col-sm-4 control-label">Project<span class="req_star">*</span></label>

                                <div class="col-sm-6">
                                    <select class="form-control" name="project_id" id="project_id" required>
                                        <option value="">Select Project</option>
                                        @foreach($project as $proj)
                                        <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('project_id'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('project_id') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="form-group row custom_form_group{{ $errors->has('contract_no') ? ' has-error' : '' }}">
                                <label class="col-sm-4 control-label">Contract No<span class="req_star">*</span></label>
                                <div class="col-sm-6"> 
                                    <input type="text" class="form-control" name="contract_no" id="contract_no"
                                        value="{{ old('contract_no') }}" placeholder="Contract No"  required>

                                    @if ($errors->has('contract_no'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('contract_no') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                 
                            </div>

                            <div class="form-group row custom_form_group{{ $errors->has('invoice_no') ? ' has-error' : '' }}">
                                <label class="col-sm-4 control-label">Invoice No<span class="req_star">*</span></label>
                                <div class="col-sm-6">
                                     <input type="hidden" id="hidden_invoice_no"  name="hidden_invoice_no" >
                                    <input type="text" class="form-control" name="invoice_no" id="invoice_no"
                                        value="{{ old('invoice_no') }}" placeholder="Invoice No" onkeyup="invoiceNoOnChange(this.value)" required>

                                    @if ($errors->has('invoice_no'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('invoice_no') }}</strong>
                                    </span>
                                    @endif
                                </div> 
                            </div>

                            <div class="form-group row custom_form_group{{ $errors->has('total') ? ' has-error' : '' }}">
                                <label class="col-sm-4 control-label">Net Amount<span
                                        class="req_star">*</span></label>
                                <div class="col-sm-6">
                                    <input type="text" class="custom-form-control form-control" id="total" name="total"
                                        value="" placeholder="Total(Excluding VAT)">
                                    @if ($errors->has('total'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('total') }}</strong>
                                    </span>
                                    @endif
                                </div> 
                            </div>

                            <div class="form-group row custom_form_group{{ $errors->has('vat') ? ' has-error' : '' }}">
                                <label class="col-sm-4 control-label">VAT<span
                                    class="req_star">*</span></label>
                                <div class="col-sm-2">
                                    <input type="text" class="form-control" name="vat_total" id="vat_total"
                                        value="0">
                                    @if ($errors->has('vat_total'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('vat_total') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <label class="col-sm-2 control-label">% Of</label>
                                   <div class="col-sm-2">
                                    <input type="number" class="form-control" name="vat" id="vat" value="14">
                                    @if ($errors->has('vat'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('vat') }}</strong>
                                    </span>
                                    @endif
                                </div>

                            </div>
                            <div class="form-group row custom_form_group{{ $errors->has('retention') ? ' has-error' : '' }}">

                                <label class="col-sm-4 control-label">Retention<span
                                        class="req_star">*</span></label>

                                <div class="col-sm-2">
                                    <input type="text" class="form-control" name="retention_total"
                                        id="retention_total" value="0"  >
                                    @if ($errors->has('retention_total'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('retention_total') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <label class="col-sm-2 control-label"> % Of:
                                         </label>
                                <div class="col-sm-2">
                                    <input type="number" class="form-control" name="retention" id="retention"
                                        value="9">
                                    @if ($errors->has('retention'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('retention') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                 
                            </div>
 
                            <div class="form-group row custom_form_group{{ $errors->has('total_with_vat') ? ' has-error' : '' }}">
                                
                                <label class="col-sm-4 control-label">Total with VAT<span
                                    class="req_star">*</span></label>
                                    
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="total_with_vat"
                                        id="total_with_vat" placeholder="Total Amount Included VAT" value=""
                                        required>
                                    @if ($errors->has('total_with_vat'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('total_with_vat') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="col-sm-2"></div>
                            </div>
                            <div class="form-group row custom_form_group{{ $errors->has('grandTotal') ? ' has-error' : '' }}">
                                 <label class="col-sm-4 control-label">Total with VAT(Excl. Ret.)<span
                                    class="req_star">*</span></label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="grandTotal" id="grandTotal"
                                        placeholder="Total Amount Included VAT and Exclusive Retention" value=""
                                        required>
                                    @if ($errors->has('grandTotal'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('grandTotal') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row custom_form_group{{ $errors->has('invoice_status') ? ' has-error' : '' }}">
                                <label class="col-sm-4 control-label">Remarks</label>
                                <div class="col-sm-6">
                                    <textarea style="height:50px; resize:none" name="remarks" class="form-control"
                                        placeholder="Remarks">{{ old('remarks') }}</textarea>
                                </div>
                            </div>

                        </div>
                    </div>
                    <!-- Double column Bill voucher form part end -->
                </div>
            </div>

            <div class="col-sm-12 text-center mt-2">
                <button type="submit" id="onSubmit" onclick="formValidation();" class="btn btn-primary waves-effect"  style="border-radius: 15px;
                 width: 150px; height: 40px; letter-spacing: 1px;">PROCESS</button>
            </div>
            <br>
        </form>
        <!-- Billing informations for bill voucher End -->
    </div>


</div>
</div>

<script>
    $(document).ready(function () {

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


        getEVoucherAddToCartData();
       


       function calculateItemTotalAmount(){

            if ($('#cartRate').val() == "" || $('#cartRate').val() == null) {
                $('#cartRate').val('0');
            }
        
            if ($('#cartQuantity').val() == "" || $('#cartQuantity').val() == null) {
                $('#cartQuantity').val('0');
            }

            var cartQuantity = $('#cartQuantity').val();
            var cartRate = $('#cartRate').val();
            var QntyAndRate = cartQuantity * cartRate;
            $('#cartTotal').val(QntyAndRate.toFixed(2));
       }

        // Key press, Paste, Click Arrow to Increment Decrement will fire here
        $('#cartRate').on('input', function() {
            calculateItemTotalAmount(); 
        });
        $('#cartQuantity').on('input', function() {
            calculateItemTotalAmount(); 
        });

    //    function processMultiplicationOfQunatityAndRate(){

    //         if ($('#unitRate').val() == "" || $('#unitRate').val() == null) {
    //             $('#unitRate').val('0');
    //         }
        
    //         if ($('#quantity').val() == "" || $('#quantity').val() == null) {
    //             $('#quantity').val('0');
    //         }
    //         var unitRate = parseFloat($('#unitRate').val());
    //         var quantity = parseFloat($('#quantity').val());
    //         var total = unitRate * quantity ; 
    //         $('#total').val(total.toFixed(2));
    //         var vat = parseFloat($('#vat').val());
       
    //         var totalWithVat = (vat * total) / 100;
    //         var vat_total = $('#vat_total').val(totalWithVat);
    //         var total = parseFloat($('#total').val());
    //         var totalWithVat = parseFloat($('#vat_total').val());
    //         var retention = parseFloat($('#retention').val());
    //         var totalWithRetention = parseFloat((retention * total) / 100);
    //         $('#retention_total').val(totalWithRetention);
    //         var grandTotal = (total + totalWithVat) - totalWithRetention;
    //         $('#grandTotal').val(grandTotal.toFixed(2));
    //     }

        // $("#unitRate").keyup(function () {
        //     alert(250);
        //     processMultiplicationOfQunatityAndRate();
        // });


        // $("#quantity").keyup(function () {
        //     alert(253);
        //     processMultiplicationOfQunatityAndRate();
        // });

        


        $('#vat').on('input', function () {

            if ($('#vat').val() == "" || $('#vat').val() == null) {
                $('#vat').val('0');

            }
            var vat = parseFloat($('#vat').val());
            var total = parseFloat($('#total').val());
            var totalWithVat = (vat * total) / 100;
            var vat_total = $('#vat_total').val(totalWithVat.toFixed(2));
            var calculatedVatAmount = totalWithVat + total;
            $('#grandTotal').val(calculatedVatAmount.toFixed(2));

            $('#total_with_vat').val(calculatedVatAmount.toFixed(2));
        });
 
        $('#retention').on('input', function() {
            calculateRetention(); 
        });

        function calculateRetention(){

            if ($('#retention').val() == "" || $('#retention').val() == null) {
                $('#retention').val('0');

            }
            var total = parseFloat($('#total').val());
            if (total > 0) {
                var totalWithVat = parseFloat($('#vat_total').val());
                var retention = parseFloat($('#retention').val());
                var totalWithRetention = parseFloat((retention * total) / 100);
                $('#retention_total').val(totalWithRetention.toFixed(2));
                var grandTotal = (total + totalWithVat) - totalWithRetention;
                $('#grandTotal').val(grandTotal.toFixed(2));
            } else {
                $('#retention_total').val('0');
            }
        }


        /* ================= add cart type ================= */
        $('#eVoucherAddToCart').on('click', function () {
            $(this).submit('false');
            
            var cartDescription = $('#cartDescription').val();
            var cartVat = 0;// $('#cartVat').val();
            var cartQuantity = $('#cartQuantity').val();
            var cartRate = $('#cartRate').val();
            var cartTotal = $('#cartTotal').val();
            var qty_unit = $('#qty_unit').val();
            var cartAreaNo = $('#cartAreaNo').val() ?? "";
            var itemNo = $('#itemNo').val() ?? "";
            //   alert(cartAreaNo);

            if (cartQuantity != '' && cartRate != '' && cartTotal != '') {

               
                $.ajax({
                    type: "POST",
                    url: "{{ route('evoucher-item-add.tocart')}}",
                    dataType: "json",
                    data: {
                        cartDescription: cartDescription,
                        cartVat: cartVat,
                        cartQuantity: cartQuantity,
                        qty_unit: qty_unit,
                        cartRate: cartRate,
                        cartTotal: cartTotal,
                        cartAreaNo: cartAreaNo,
                        itemNo:itemNo
                    },
                    success: function (data) {

                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: true,
                            timer: 4000
                        })

                        if ($.isEmptyObject(data.error)) {
                            Toast.fire({
                                type: 'success',
                                title: data.success
                            });
                            getEVoucherAddToCartData();
                            $('#cartDescription').val('');
                            $('#cartVat').val('0');
                            $('#cartQuantity').val('0');
                            $('#cartRate').val('0');
                            $('#cartTotal').val('0');
                            $('#cartAreaNo').val('');
                            $('#itemNo').val('');

                        } else {
                            Toast.fire({
                                type: 'error',
                                title: data.error
                            })
                        }
                    }
                });
            } else {
                alert(qty_unit);
            } // end if condition
        });


        /* ================= get cart type ================= */
        function getEVoucherAddToCartData() {
            $.ajax({
                type: "GET",
                url: "{{ route('get.evoucher-add.to.cart.data')}}",
                dataType: "json",
                success: function (data) {

                    $('#eVouchercartTotQty').text(data.cartQty);
                    $('#eVouchercartSubTotal').text(data.cartTotal);
                    $("input[id='eVouchercartSubTotal']").val(data.cartTotal); // form data
                    // grand total
                    $('input[id="total"]').val(data.cartTotal);
                    var html = '';
                    $.each(data.cartContect, function (key, value) {
                         html +=
                            `
                <tr>
                  <td>${value.options.areaNo}</td>
                  <td>${value.options.itemNo}</td>
                  <td>${value.name}</td>                  
                  <td>${value.price} ${value.options.qty_unit}</td>
                  <td>${value.qty}</td>
                  <td>${value.options.cartTotal}</td>
                  <td><a style="cursor:pointer"  type="submit" title="delete" id="${value.rowId}" onclick="removeToCart(this.id)"><i class="fa fa-trash fa-lg delete_icon"></i></a></td>                </tr>
              `
                    });
                    $('#eVoucherAddToCartList').html(html);
                }
            });
        }

        /* ================= remove type ================= */
        function removeToCart(rowId) {
            $.ajax({
                type: 'POST',
                url: "{{ route('evoucher-remove.to.cart')}}",
                data: {
                    rowId: rowId
                },
                dataType: 'json',
                success: function (data) {
                    
                    getEVoucherAddToCartData();
                    //  start message
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    })
                    if ($.isEmptyObject(data.error)) {
                        Toast.fire({
                            type: 'success',
                            title: data.success
                        })
                    } else {
                        Toast.fire({
                            type: 'error',
                            title: data.error
                        })
                    }
                   
                    //  end message
                }
            });
        }


        // Sub company wise bank data load into dropdown list
      //  $(document).ready(function() {
        $('select[name="sub_contractor_id"]').on('change', function() {
            var sub_contractor_id = $(this).val();

            if (sub_contractor_id) {
            $.ajax({
                url: "{{  url('/admin/company/sub-contractor-wise/bank-info/') }}/" + sub_contractor_id,
                type: "GET",
                dataType: "json",
                success: function (data) {
                    // response
                    if (data == "") {
                        $('select[name="bank_account_id"]').empty();
                        $('select[name="bank_account_id"]').append('<option value="">Bank Name Not Found!</option>');
                    } else {
                        $('select[name="bank_account_id"]').empty();
                        $('select[name="bank_account_id"]').append('<option value="">Select Bank Name </option>');
                        // data load
                        $.each(data, function (key, value) {
                            $('select[name="bank_account_id"]').append('<option value="' + value.id + '">' + value.bank_name + '</option>');
                        });
                        // data load
                    }
                    // response
                },

            });
            } else {

            }
        });

   // });
   
       // Project Dropdown change event
        $('select[name="project_id"]').on('change',function(){
           
            //   var project_id = $(this).val();
            //   $('#invoice_no').val(project_id+"-");
           //    $('#hidden_invoice_no').val(project_id+"-");
               
        
        });



    });
    // Invoice on Change Event
    function invoiceNoOnChange(val){
        var value =  $('#invoice_no').val();
        var pro_id = $('#hidden_invoice_no').val();

        // if(value.length < pro_id.length || value == ""  ){//} == "" || value == ''){
        //     $('#invoice_no').val($('#hidden_invoice_no').val());
        // }

    }
    
</script>
@endsection
