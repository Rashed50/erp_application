@extends('layouts.admin-master')
@section('title')
Invoice Update
@endsection
@section('content')
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Invoice Update</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active">Invoice Update </li>
        </ol>
    </div>
</div>
<!-- Session alert part start -->
<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-7">
        @if(Session::has('success'))
        <div class="alert alert-success alertsuccess" role="alert" style="margin-left: -20px">
            <strong>{{Session::get('success')}}</strong> 
        </div>
        @endif
        @if(Session::has('error'))
        <div class="alert alert-warning alerterror" role="alert" style="margin-left: -20px">
             <strong>{{Session::get('error')}}</strong> 
        </div>
        @endif
    </div>
    <div class="col-md-2"></div>
</div>
<!-- Session alert part end -->

<div class="row">
    <!-- Bill voucher searching part start -->
    <div class="col-md-1"></div>
    <div class="col-md-10">
        <div class="card"><br>
            <div class="card-body card_form" style="padding-top: 15px;">

                <div class="row form-group custom_form_group{{ $errors->has('invoiceNo') ? ' has-error' : '' }}">
                    <label class="col-md-2 control-label d-block" style="text-align: right;">Invoice No </label>
                    <div class="col-md-4">
                        <input type="text" placeholder="Enter Bill Invoice Number Here" class="form-control"
                            id="invoiceNo" name="invoiceNo" value="{{ old('invoiceNo') }}" required autofocus>
                        <span id="informations_not_found_error_show" class="d-none" style="color: red"></span>
                        @if ($errors->has('invoiceNo'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('invoiceNo') }}</strong>
                        </span>
                        @endif
                    </div>
                    <div class="col-md-5">                     
                       <button type="submit" onclick="voucherInformationDetails()"
                                class="btn btn-primary waves-effect">SEARCH</button>
                       <button type="button" id="invoicePrintPreview" class="btn btn-primary waves-effect">PRINT VIEW</button>   

                        @can('invoice_with_qrcode_record_delete')
                            <button type="button" id="invoice_delete_button" onclick="deleteAInvoiceRecord()" class="btn btn-primary waves-effect">Delete</button> 
                        @endcan 
                       
                        @can('invoice_with_qrcode_report')
                         <button type="button" id="invoice_report" data-toggle="modal" data-target="#invoice_report_modal" class="btn btn-primary waves-effect">Report</button> 
                        @endcan  
                                                
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-1"></div>
    <!-- Bill voucher searching part end -->

    <div class="card pt-2 d-none" id="addTocart_bill_voucher_form">
        <!-- Bill voucher Add To Cart Informations part start -->
        <div class="card-header">
            <h4 class="card-title">Item Details Information</h4>
        </div>
        <br>
        <form id="eVoucherAddToCartForm">


            <div class="row">
                <input type="hidden" name="invoiceRecordAutoIDForCart" id="invoiceRecordAutoIDForCart">
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
                            <input type="text" class="form-control" name="itemNo" id="itemNo" placeholder="Item No"
                                required>
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
                        <label class="col-sm-5 control-label">Description</label>
                        <div class="col-sm-12">
                            <textarea name="" class="form-control" id="cartDescription" name="cartDescription"
                                placeholder="Item Descriptions" cols="30" rows="2"
                                required>{{old('cartDescription')}}</textarea>
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
                                <option value="m2">m<sup>2</sup></option>
                                <option value="m3">m<sup>3</sup></option>
                                <option value="cm2">cm<sup>2</sup></option>
                                <option value="cm3">cm<sup>3</sup></option>
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
                                    <td colspan="4"></td>
                                    <td> <span id="eVouchercartTotQty" style=" font-weight:bold; "> </span> </td>
                                    <td> <span id="eVouchercartSubTotal" style="font-weight:bold; " id="itemSubTotal">
                                        </span> </td>
                                </tr>
                            </tfoot>
                        </table>

                    </div>
                </div>
            </div>
        </div>
        <!-- Bill voucher Add To Cart Informations part end -->

        <form class="form-horizontal company-form" id="registration" method="post" target="_blank"
            action="{{ route('updated.e.voucher.process') }}">
            @csrf
            <div class="card">                  
                <div class="card-body card_form">
                     
                    <!-- Double column Bill voucher form part start -->
                    <div class="row">
                        <!-- first coll -->
                        <div class="col-md-6">
                            <input type="hidden" name="invoiceRecordAutoID" id="invoiceRecordAutoID"
                                value="invoice_record_auto_id">

                            <div
                                class="form-group row custom_form_group{{ $errors->has('main_contractor_id') ? ' has-error' : '' }}">
                                <label class="col-sm-5 control-label">Main Contractor:<span
                                        class="req_star">*</span></label>

                                <div class="col-sm-7">
                                    <select class="form-control" name="main_contractor_id" id="main_contractor_id" required>
                                        <option value=""> Select Main Contractor</option>
                                        @foreach($main_contractors as $sc)
                                            <option value="{{$sc->mc_auto_id}}">{{ $sc->en_name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('main_contractor_id'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('main_contractor_id') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div
                                class="form-group row custom_form_group{{ $errors->has('sub_contractor_id') ? ' has-error' : '' }}">
                                <label class="col-sm-5 control-label">Our Company:<span
                                        class="req_star">*</span></label>

                                <div class="col-sm-7">
                                    <select class="form-control" name="sub_contractor_id" id="sub_contractor_id" required>
                                        <option value=""> Select Invoice For</option>
                                        @foreach($company_list as $com)
                                            <option value="{{$com->sb_comp_id}}">{{ $com->sb_comp_name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('sub_contractor_id'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('sub_contractor_id') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div
                                class="form-group row custom_form_group{{ $errors->has('bank_account_id') ? ' has-error' : '' }}">
                                <label class="col-sm-5 control-label">Bank Name:<span class="req_star">*</span></label>

                                <div class="col-sm-7">
                                    <select class="form-control" name="bank_account_id" id="bank_account_id" required>
                                        <option value="">Select Bank Name</option>                                         
                                    </select>
                                    @if ($errors->has('bank_account_id'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('bank_account_id') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>  
                            <div  class="form-group row custom_form_group{{ $errors->has('project_id') ? ' has-error' : '' }}">
                                <label class="col-sm-5 control-label">Invoice For Project:<span
                                        class="req_star">*</span></label>

                                <div class="col-sm-7">
                                    <select class="form-control" name="project_id" id="project_id" required>
                                        <option value="">Select Project</option>
                                        @foreach($projects as $proj)
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
                            <div
                                class="form-group row custom_form_group{{ $errors->has('submitted_date') ? ' has-error' : '' }}">
                                <label class="col-sm-5 control-label">Submitted Date:<span
                                        class="req_star">*</span></label>
                                <div class="col-sm-7">
                                    <input type="date" class="form-control" id="submitted_date" name="submitted_date"
                                        value="{{ Date('Y-m-d') }}" required>
                                </div>
                                @if ($errors->has('submitted_date'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('submitted_date') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="form-group row custom_form_group{{ $errors->has('employee_id') ? ' has-error' : '' }}">
                                <label class="col-sm-5 control-label">Submitted By:<span
                                        class="req_star">*</span></label>
                                <div class="col-sm-7">
                                    <select class="form-control" name="employee_id" required>
                                         @foreach($users as $us)
                                            <option value="{{$us->id}}">{{$us->name}}</option>
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
                                <label class="col-sm-5 control-label">Invoice Date:<span
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
                            <div
                                class="form-group row custom_form_group{{ $errors->has('invoice_status') ? ' has-error' : '' }}">
                                <label class="col-sm-5 control-label">Invoice Status:<span
                                        class="req_star">*</span></label>
                                <div class="col-sm-7">
                                    <select class="form-control" id="invoice_status" name="invoice_status" required>
                                        <option value="">Select Here</option>
                                        <option value="0">Pending</option>
                                        <option value="1">Payment Cleared</option>
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

                            <div   class="form-group row custom_form_group{{ $errors->has('invoice_no') ? ' has-error' : '' }}">
                                <label class="col-sm-4 control-label">Invoice No:<span class="req_star">*</span></label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="invoice_no" id="invoice_no"
                                        value="{{ old('invoice_no') }}" placeholder="Invoice No" required>

                                    @if ($errors->has('invoice_no'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('invoice_no') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="col-sm-2"></div>
                            </div>

                            <div
                                class="form-group row custom_form_group{{ $errors->has('total') ? ' has-error' : '' }}">
                                <label class="col-sm-4 control-label">Total (Excl. VAT):<span
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
                                <div class="col-sm-2"></div>
                            </div>

                            <div class="form-group row custom_form_group{{ $errors->has('vat') ? ' has-error' : '' }}">
                                <label class="col-sm-4 control-label">Total Vat:<span class="req_star">*</span></label>
                                <div class="col-sm-2">
                                    <input type="text" class="form-control" name="vat_total" id="vat_total" value="0">
                                    @if ($errors->has('vat_total'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('vat_total') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <label class="col-sm-2 control-label">% Of</label>
                                <div class="col-sm-2">
                                    <input type="number" class="form-control" name="vat" id="vat" value="0">
                                    @if ($errors->has('vat'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('vat') }}</strong>
                                    </span>
                                    @endif
                                </div>

                            </div>
                            <div
                                class="form-group row custom_form_group{{ $errors->has('retention_total') ? ' has-error' : '' }}">

                                <label class="col-sm-4 control-label">Total Retention:<span
                                        class="req_star">*</span></label>

                                <div class="col-sm-2">
                                    <input type="text" class="form-control" name="retention_total" id="retention_total"
                                        value="0">
                                    @if ($errors->has('retention_total'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('retention_total') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <label class="col-sm-2 control-label"> % Of:
                                </label>
                                <div class="col-sm-2">
                                    <input type="number" class="form-control" name="retention" id="retention" value="0">
                                    @if ($errors->has('retention'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('retention') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="col-sm-2"></div>
                            </div>
                            <!-- <div class="form-group row custom_form_group{{ $errors->has('retention_total') ? ' has-error' : '' }}">
                                    <label class="col-sm-4 control-label">Total Retention:<span
                                            class="req_star">*</span></label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" name="retention_total"
                                            id="retention_total" value="0">
                                        @if ($errors->has('retention_total'))
    <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('retention_total') }}</strong>
                                        </span>
    @endif
                                    </div>
                                    <div class="col-sm-2"></div>
                                </div> -->
                            <!-- <div class="form-group row custom_form_group{{ $errors->has('vat_total') ? ' has-error' : '' }}">
                                    <label class="col-sm-4 control-label">Total VAT :<span
                                            class="req_star">*</span></label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" name="vat_total" id="vat_total"
                                            value="0">
                                        @if ($errors->has('vat_total'))
    <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('vat_total') }}</strong>
                                        </span>
    @endif
                                    </div>
                                    <div class="col-sm-2"></div>
                                </div> -->
                            <div   class="form-group row custom_form_group{{ $errors->has('total_with_vat') ? ' has-error' : '' }}">
                                <div class="col-sm-4" style="text-align: right;">
                                    <label class="control-label">Total Amount with VAT:<span
                                            class="req_star">*</span></label>
                                </div>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="total_with_vat" id="total_with_vat"
                                        placeholder="Total Amount Included VAT" value="" required>
                                    @if ($errors->has('total_with_vat'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('total_with_vat') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="col-sm-2"></div>
                            </div>
                            <div  class="form-group row custom_form_group{{ $errors->has('grandTotal') ? ' has-error' : '' }}">
                                <div class="col-sm-4" style="text-align: right;">
                                    <label class="control-label">Total Amount with VAT(Excl. Retension):<span
                                            class="req_star">*</span></label>
                                </div>
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
                                <div class="col-sm-2"></div>
                            </div>
                            <div   class="form-group row custom_form_group{{ $errors->has('remarks') ? ' has-error' : '' }}">
                                <label class="col-sm-4 control-label">Remarks:</label>
                                <div class="col-sm-8">
                                    <textarea style="height:100px; resize:none" name="remarks" id="remarks"
                                        class="form-control" placeholder="Remarks">{{ old('remarks') }}</textarea>
                                </div>
                                @if ($errors->has('remarks'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('remarks') }}</strong>
                                </span>
                                @endif
                            </div>

                            <input type="hidden" name="billVoucherInfos" id="billVoucherInfos" val>
                            <input type="hidden" name="carts" id="cartsInfo" val>

                        </div>
                    </div>
                    <!-- Double column Bill voucher form part end -->
                </div>
            </div>

            <div class="col-sm-12 text-center mt-2">
                <button type="submit" id="onSubmit" onclick="formValidation();" class="btn btn-primary waves-effect"
                    style="border-radius: 15px;
                width: 150px; height: 40px; letter-spacing: 1px;">PROCESS</button>
            </div>
            <br>
        </form>
    </div>
</div>

<!-- invoice record list start -->
<div class="row d-block" id="invoiceRecordList">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <div class="row">

                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table id="dt-vertical-scroll" class="table" cellspacing="0" width="100%">
                                <thead>
                                    <tr> 
                                        <th>S.N</th>
                                        <th>Invoice No</th>
                                        <th>Main Contractor</th>
                                        <th>Sub Contractor</th>
                                         <th>Project</th>
                                        <th>Date</th>
                                        <th>Total Amount</th>
                                        <th>VAT</th>
                                        <th>Total(VAT Inc.)</th>
                                        <th>Retention</th>
                                        <th>Bill Amount</th>
                                        <th>Remarks</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody> 
                                    @foreach($invoice_records as $item)
                                    <tr>
                                        <td> {{ $loop->iteration }} </td>
                                        <td> {{ $item->invoice_no }} </td>
                                        <td> {{ $item->mainContractor->en_name }} </td>
                                        <td> {{ $item->subContractor->sb_comp_name }} </td>
                                        <td> {{ $item->proj_name }} </td>
                                        <td> {{ Carbon\Carbon::parse($item->submitted_date)->format('D, d F Y') }} </td>
                                        <td> {{ $item->items_grand_total_amount }} </td>
                                        <td> {{ $item->total_vat }} </td>
                                        <td> {{ $item->total_amount }} </td>
                                        <td> {{ $item->total_retention }} </td>
                                        <td> {{ $item->total_amount -  $item->total_retention }} </td>
                                        <td> {{ $item->remarks }} </td>                                   
                                        <td>
                                            
                                            <span class="badge badge-pill badge-danger"></span>
                                            <a href="#" title="Edit" class="approve_button" data-toggle="modal"
                                                data-target="#invoiceStatsuModal-{{ $item->invoice_record_auto_id}}">
                                                @if ($item->invoice_status_id == 1)
                                                 Pending @else  Released @endif
                                            </a>
                                            
                                        </td>
                                           <!-- Invoice Update Modal -->
                                           <div class="modal fade" id="invoiceStatsuModal-{{ $item->invoice_record_auto_id }}"
                                            value="{{ $item->invoice_record_auto_id }}" tabindex="-1" role="dialog" aria-
                                            labelledby="demoModalLabel" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                     
                                                    <div class="modal-body">
                                                        <div class="row">
                                                        <form class="form-horizontal" id="employee-info-form"
                                                                method="post"
                                                                action="{{ route('updated.e.voucher.invoice.status') }}">
                                                                @csrf
                                                            <div class="col-sm-12">
                                                                <div class="card">    
                                                                    <input type="hidden" name = "invoice_record_auto_id" value = "{{ $item->invoice_record_auto_id }}" />                                                           
                                                                    <div class="form-group row custom_form_group{{ $errors->has('invoice_status') ? ' has-error' : '' }}">
                                                                        <label class="col-sm-5 control-label">Invoice Status:<span
                                                                                class="req_star">*</span></label>
                                                                        <div class="col-sm-7">
                                                                            <select class="form-control" id="invoice_status" name="invoice_status" required>
                                                                                <option value="">Select Invoice Status</option>
                                                                                <option value="1">Pending</option>
                                                                                <option value="5">Released</option>
                                                                            </select>
                                                                            
                                                                        </div>
                                                                    </div>                                                                  
                                                                    <button type="submit" id="onSubmit" onclick="formValidation();" class="btn btn-primary waves-effect"  style="border-radius: 15px;width: 100px; height: 40px; letter-spacing: 1px;">Update</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            </div>
                                           <!-- Invoice Update Modal End -->
                                      
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


<!-- Invoice Report Modal -->
<div class="row">
   
    <div class="modal fade" id="invoice_report_modal" tabindex="-1" role="dialog" aria-labelledby="demoModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
             <div class="modal-content">
                 <div class="modal-body">
                    <form class="form-horizontal" id="invoice_report_form" method="post" target="_blank" action="{{ route('qr.invoice.report') }}">
                     @csrf
 

                            <div class="form-group row custom_form_group">
                                <label class="col-sm-4 control-label">Company:<span class="req_star">*</span></label>

                                <div class="col-sm-6">
                                    <select class="selectpicker" name="ccompany_id_list[]"  multiple required>
                                        @foreach($company_list as $com)
                                        <option value="{{$com->sb_comp_id}}">{{ $com->sb_comp_name }}</option>
                                        @endforeach
                                    </select>
                                     
                                </div>
                            </div> 

                            <div class="form-group row custom_form_group{{ $errors->has('project_id') ? ' has-error' : '' }}">
                                <label class="col-sm-4 control-label">Invoice For Project:<span class="req_star">*</span></label>

                                <div class="col-sm-6">
                                    <select class="selectpicker" name="project_id_list[]" id="project_id_list[]" multiple required>                                
                                        @foreach($projects as $proj)
                                        <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                                        @endforeach
                                    </select>
                                     
                                </div>
                            </div> 
                            
                            <div class="form-group row custom_form_group{{ $errors->has('project_id') ? ' has-error' : '' }}">
                                <label class="col-sm-4 control-label">Invoice Status:<span class="req_star">*</span></label>

                                <div class="col-sm-6">
                                    <select class="form-control" name="invoice_status" id="invoice_status">
                                        <option value="">All </option>                                       
                                        <option value="1"> Pending Invoice</option>
                                        <option value="5"> Paid Invoice</option>
                                        
                                    </select>
                                     
                                </div>
                            </div>
                            
                            <button type="submit" id="invoice_report_button"  class="btn btn-primary waves-effect"  style="border-radius: 15px; width: 150px; height: 40px; letter-spacing: 1px;">Show Report</button>
                    </form>                  
                  </div>
              </div>
        </div>
    </div>
     <!-- Invoice Report Modal End -->
</div>


<link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta2/css/bootstrap-select.min.css"
    integrity="sha512-mR/b5Y7FRsKqrYZou7uysnOdCIJib/7r5QeJMFvLNHNhtye3xJp1TdJVPLtetkukFn227nKpXD9OjUc09lx97Q=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"
    integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4"
    crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta2/js/bootstrap-select.min.js"
    integrity="sha512-FHZVRMUW9FsXobt+ONiix6Z0tIkxvQfxtCSirkKc5Sb4TKHmqq1dZa8DphF0XqKb3ldLu/wgMa8mT6uXiLlRlw=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<!-- invoice record list end -->
<script type="text/javascript">
    function resetUI() {
        $("#show_employee_Profile_photo").html('');
        $("#show_employee_Pasport_photo").html('');
        $("#show_employee_job_experience_list").html('');
        $("#show_employee_Medical_photo").html('');
        $("#show_employee_Iqama_photo").html('');
        $("#show_employee_Appointment_photo").html('');
        $("#show_employee_covid_certificate").html('');
    }

    // Enter Key Press Event Fire
    $('#invoiceNo').keydown(function (e) {
        if (e.keyCode == 13) {
            voucherInformationDetails();
        }
    })

    // onclick function start
    function voucherInformationDetails() {
        var invoiceNo = $("#invoiceNo").val();

        if ($("#invoiceNo").val().length === 0) {
            //  start message
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            })
            if ($.isEmptyObject(invoiceNo)) {
                Toast.fire({
                    type: 'error',
                    title: "Please Fill This Input Field First !!!"
                })
            } else {
                Toast.fire({
                    type: 'success',
                    title: "Bill Voucher Informations are"
                })
            }
            //  end message

        } else {
            resetUI();
            $.ajax({
                type: 'POST',
                url: "{{ route('bill_voucher.information_details.by_invoice_no') }}", //invoice no wise Invoice Record Search
                data: {
                    invoiceNo: invoiceNo
                },
                dataType: 'json',
                success: function (response) {                   

                    if (response.success == false) {
                        $("span[id='informations_not_found_error_show']").text(
                            'Bill Voucher Informations Not Found ');
                        $("span[id='informations_not_found_error_show']").addClass('d-block').removeClass(
                            'd-none');
                        $("#addTocart_bill_voucher_form").removeClass("d-block").addClass("d-none");
                        $("#printPreviewBtn").removeClass("d-block").addClass("d-none");
                        return;

                    } else {
                        $("span[id='informations_not_found_error_show']").removeClass("d-block").addClass("d-none");
                        $("#invoiceRecordList").removeClass("d-block").addClass("d-none");
                        $("#printPreviewBtn").removeClass("d-none").addClass("d-block");

                        showSearchingEmployee(response.voucherInfo, response.project, response
                            .mainContractor, response.subContractor, response.bankInfos, response.employee);
                        showSearchingResultAsMultipleRecords(response.cartInfo);
                    } 
                    

                } // end of success
            }); // end of ajax calling
        }
        // End of Method for Router calling

    }

    function showSearchingEmployee(voucherInfo, project, mainContractor, subContractor, bankInfos, employee) {

        /* show employee information in employee table */
        $("#addTocart_bill_voucher_form").removeClass("d-none").addClass("d-block"); // show signle employee details

        $("input[id='invoiceRecordAutoID']").val(voucherInfo.invoice_record_auto_id);
        $("input[id='submitted_date']").val(voucherInfo.submitted_date);
        $("textarea[id='remarks']").val(voucherInfo.remarks);
        $("input[id='invoice_no']").val(voucherInfo.invoice_no); 
        $("input[id='contract_no']").val(voucherInfo.contract_no);
        $("input[id='total']").val(voucherInfo.items_grand_total_amount);
        $("input[id='vat']").val(voucherInfo.percent_of_vat);
        $("input[id='vat_total']").val(voucherInfo.total_vat);
        $("input[id='retention_total']").val(voucherInfo.total_retention);
        $("input[id='retention']").val(voucherInfo.percent_of_retention);
        $("input[id='total_with_vat']").val(voucherInfo.total_amount);

        var totalRetention = parseFloat($('#retention_total').val());
        var totalAmount = parseFloat($('#total_with_vat').val());
        var grandTotal = totalAmount - totalRetention;
        $('#grandTotal').val(grandTotal.toFixed(2));

        $("#main_contractor_id").val(parseInt(voucherInfo.main_contractor_id));
        $("#sub_contractor_id").val(parseInt(voucherInfo.sub_contractor_id));
        $("#project_id").val(parseInt(voucherInfo.project_id));
        $("#invoice_status").val(parseInt(voucherInfo.invoice_status_id));
        
        // Company Bank Name, Id for dropdown List
         if (bankInfos != '') {
            $('select[name="bank_account_id"]').empty();
            $('select[name="bank_account_id"]').append('<option value="">Please Select Bank Name </option>');
            $.each(bankInfos, function (key, value) {
                $('select[name="bank_account_id"]').append('<option value="' + value.id + '">' + value
                    .bank_name + '</option>');
            });
        } else {
            $('select[[name="bank_account_id"]').append('<option>Data Not Found</option>');
        }
        $("#bank_account_id").val(parseInt(voucherInfo.bank_details_id));
 
    }

    function showSearchingResultAsMultipleRecords(cartInfo) {
        $("#eVoucherAddToCartList").html('');
        var cartTable = "";
        $.each(cartInfo, function (key, value) {
            cartTable += `
                                <tr>
                                    <td>${value.options.areaNo}</td>
                                    <td>${value.options.itemNo}</td>
                                    <td>${value.name}</td>
                                    <td>${value.price} ${value.options.qty_unit}</td>
                                    <td>${value.qty}</td>
                                    <td>${value.options.cartTotal}</td>
                                    <td><a style="cursor:pointer"  type="submit" title="delete" id="${value.rowId}" onclick="removeToCart(this.id)"><i class="fa fa-trash fa-lg delete_icon"></i></a></td>
                                </tr>
                                `
        });
        $("#eVoucherAddToCartList").html(cartTable);
    }

    /* ================= Delete Item From Invoice Record Details type ================= */
        // function deleteCartItem(invRecordAutoID) {
        //     alert(invRecordAutoID);
        //     $.ajax({
        //         type: 'POST',
        //         url: "{{ route('evoucher-remove.to.cart') }}",
        //         data: {
        //             invRecordAutoID: invRecordAutoID
        //         },
        //         dataType: 'json',
        //         success: function(data) {

        //             getEVoucherAddToCartData();
        //             //  start message
        //             const Toast = Swal.mixin({
        //                 toast: true,
        //                 position: 'top-end',
        //                 showConfirmButton: false,
        //                 timer: 3000
        //             })
        //             if ($.isEmptyObject(data.error)) {
        //                 Toast.fire({
        //                     type: 'success',
        //                     title: data.success
        //                 })
        //             } else {
        //                 Toast.fire({
        //                     type: 'error',
        //                     title: data.error
        //                 })
        //             }

        //             //  end message
        //         }
        //     });
        // }
</script>
<script>
    $(document).ready(function () {
        getEVoucherAddToCartData();

        $('#cartQuantity').on('input', function () {
            calculateCartSubtotal();
        });
        $('#cartRate').on('input', function () {
            calculateCartSubtotal();
        });

        function calculateCartSubtotal() {
            var cartQuantity = $('#cartQuantity').val();
            var cartRate = $('#cartRate').val();
            var QntyAndRate = cartQuantity * cartRate;
            $('#cartTotal').val(QntyAndRate);
        }


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
                cartAreaNo: {
                    required: true,
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
                cartAreaNo: {
                    required: "This Field Must be Required!",
                },
            },


        });

        $("#unitRate").keyup(function () {

            if ($('#unitRate').val() == "" || $('#unitRate').val() == null) {
                $('#unitRate').val('0');
            }
            var unitRate = parseFloat($('#unitRate').val());
            var quantity = parseFloat($('#quantity').val());
            var total = $('#total').val(unitRate * quantity);
            var vat = parseFloat($('#vat').val());
            var total = parseFloat($('#total').val());
            var totalWithVat = (vat * total) / 100;
            var vat_total = $('#vat_total').val(totalWithVat);
            var total = parseFloat($('#total').val());
            var totalWithVat = parseFloat($('#vat_total').val());
            var retention = parseFloat($('#retention').val());
            var totalWithRetention = parseFloat((retention * total) / 100);
            $('#retention_total').val(totalWithRetention);
            var grandTotal = (total + totalWithVat) - totalWithRetention;
            $('#grandTotal').val(grandTotal.toFixed(2));
        });

        $("#quantity").keyup(function () {

            if ($('#quantity').val() == "" || $('#quantity').val() == null) {
                $('#quantity').val('0');
            }
            var unitRate = parseFloat($('#unitRate').val());
            var quantity = parseFloat($('#quantity').val());
            var total = $('#total').val(unitRate * quantity);
            var vat = parseFloat($('#vat').val());
            var total = parseFloat($('#total').val());
            var totalWithVat = (vat * total) / 100;
            var vat_total = $('#vat_total').val(totalWithVat);
            var total = parseFloat($('#total').val());
            var totalWithVat = parseFloat($('#vat_total').val());
            var retention = parseFloat($('#retention').val());
            var totalWithRetention = parseFloat((retention * total) / 100);
            $('#retention_total').val(totalWithRetention);
            var grandTotal = (total + totalWithVat) - totalWithRetention;
            $('#grandTotal').val(grandTotal.toFixed(2));
        });

      

        $('#vat').on('input', function () {
            calculateTotalVAT();
        });

        function calculateTotalVAT() {
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
        }

        $('#retention').on('input', function () {
            calculateTotalRetention();
        });

        function calculateTotalRetention() {

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
            
           // $(this).submit('false');
           
            var cartDescription = $('#cartDescription').val();
            var cartVat = 0;// $('#cartVat').val();
            var cartQuantity = $('#cartQuantity').val();
            var cartRate = $('#cartRate').val();
            var cartTotal = $('#cartTotal').val();
            var qty_unit = $('#qty_unit').val();
            var cartAreaNo = $('#cartAreaNo').val() ?? "";
            var itemNo = $('#itemNo').val() ?? "";

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
                        itemNo: itemNo,
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
                url: "{{ route('get.evoucher-add.to.cart.data') }}",
                dataType: "json",
                success: function (data) {

                    $('#eVouchercartTotQty').text(data.cartQty);
                    $('#eVouchercartSubTotal').text(data.cartTotal);
                    $("input[id='eVouchercartSubTotal']").val(data.cartTotal); // form data
                    // grand total
                    $('input[id="total"]').val(data.cartTotal);

                    var html = '';

                    $.each(data.cartContect, function (key, value) {
                        // console.log(data.cartContect);
                        html +=
                            `
                                    <tr>
                                        <td>${value.options.areaNo}</td>
                                        <td>${value.options.itemNo}</td>
                                        <td>${value.name}</td>
                                        <td>${value.price} ${value.options.qty_unit}</td>
                                        <td>${value.qty}</td>
                                        <td>${value.options.cartTotal}</td>
                                        <td><a style="cursor:pointer"  type="submit" title="delete" id="${value.rowId}" onclick="removeToCart(this.id)"><i class="fa fa-trash fa-lg delete_icon"></i></a></td>
                                    </tr>
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
                url: "{{ route('evoucher-remove.to.cart') }}",
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
        $('select[name="sub_contractor_id"]').on('change', function () {
            var sub_contractor_id = $(this).val();

            if (sub_contractor_id) {
                $.ajax({
                    url: "{{ url('/admin/company/sub-contractor-wise/bank-info/') }}/" +
                        sub_contractor_id,
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        // response
                        if (data == "") {
                            $('select[name="bank_account_id"]').empty();
                            $('select[name="bank_account_id"]').append(
                                '<option value="">Bank Name Not Found!</option>');
                        } else {
                            $('select[name="bank_account_id"]').empty();
                            $('select[name="bank_account_id"]').append(
                                '<option value="">Select Bank Name </option>');
                            // data load
                            $.each(data, function (key, value) {
                                $('select[name="bank_account_id"]').append(
                                    '<option value="' + value.id + '">' + value
                                        .bank_name + '</option>');
                            });
                            // data load
                        }
                        // response
                    },

                });
            } else {

            }
        });


    });


    document.getElementById('invoicePrintPreview').addEventListener('click', function() {

        var invoiceNo = $('#invoiceNo').val();        
        const queryString = new URLSearchParams({
            invoice_no: invoiceNo,
        }).toString();
        var parameterValue = queryString; // Set parameter value
        var url = "{{ route('e.voucher.process-print.preview', ':parameter') }}";
        url = url.replace(':parameter', parameterValue);
        window.open(url, '_blank');

    });

    // delete invoice 
    function deleteAInvoiceRecord(){
                var invoiceNo = $('#invoiceNo').val(); 
                $.ajax({
                    type: 'POST',
                    url: "{{ route('qrcode.invoice.record.delete') }}",
                    data: {
                        invoice_number: invoiceNo
                    },
                    dataType: 'json',
                    success: function (response) {
                        
                        if(response.status == 200){
                            showMessage(response.message,'success');
                        }else {
                            showMessage(response.message,'error');
                        }                      
                    },
                    error:function(response){
                        showMessage("Network Error, Pleasge reload",'error');
                    }
            });
    }

    function showMessage(message,operationType){
      
         const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            })
           
                Toast.fire({
                    type: operationType,
                    title: message
                })
            
    }

    function showSweetAlertMessage(type,message){
        const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            })            
                Toast.fire({
                    type: type,
                    title: message,
                })
    }

</script>
@endsection
