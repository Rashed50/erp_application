@extends('layouts.admin-master')
@section('title') Inv. Item Details @endsection
@section('content')
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Inventory Item Details Info Update </h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active"> Inventory Items Details Update</li>
        </ol>
    </div>
</div>
<!-- add division -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        @if(Session::has('success'))
        <div class="alert alert-success alertsuccess" role="alert">
            <strong>{{ Session::get('success') }}</strong>
        </div>
        @endif

        @if(Session::has('error'))
        <div class="alert alert-warning alerterror" role="alert">
            <strong>{{ Session::get('error') }}</strong>
        </div>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-6">
        <form class="form-horizontal" id="registration" action="{{ route('update.item-type-item-details') }}"
            method="post">
            @csrf
            <div class="card">
                <div class="card-header"> 

                </div>
                <div class="card-body card_form" style="padding-top: 0;">

                    <input type="hidden" name="item_deta_id" value="{{ $edit->item_deta_id }}">

                    <div class="form-group row custom_form_group{{ $errors->has('itype_id') ? ' has-error' : '' }}">
                        <label class="control-label col-sm-4">Item Type:<span class="req_star">*</span></label>
                        <div class="col-sm-6">
                            <select class="form-control" name="itype_id" id="searchItype_id" required>
                                <option value="">Select Item Type</option>
                                @foreach($allType as $type)
                                <option value="{{ $type->itype_id }}" {{ $type->itype_id == $edit->itype_id ? 'selected' : '' }}>{{ $type->itype_name }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('itype_id'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('itype_id') }}</strong>
                            </span>
                            @endif
                        </div>
                        <div class="col-sm-2"></div>
                    </div>

                    <div class="form-group row custom_form_group{{ $errors->has('icatg_id') ? ' has-error' : '' }}">
                        <label class="control-label col-sm-4">Item Category:<span class="req_star">*</span></label>
                        <div class="col-sm-6">
                            <select class="form-control" name="icatg_id" id="searchIcateg_id" required>
                                <option value="{{ $edit->icatg_id }}"> {{ $edit->itemCatg->icatg_name }}</option>
                            </select>
                            @if ($errors->has('icatg_id'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('icatg_id') }}</strong>
                            </span>
                            @endif
                        </div>
                        <div class="col-sm-2"></div>
                    </div>

                    <div class="form-group row custom_form_group{{ $errors->has('iscatg_id') ? ' has-error' : '' }}">
                        <label class="control-label col-sm-4">Item SubCategory:<span class="req_star">*</span></label>
                        <div class="col-sm-6">
                            <select class="form-control" name="iscatg_id" id="searchIsubCateg_id" required>
                                <option value=" {{ $edit->iscatg_id }} ">{{ $edit->itemSubCatg->iscatg_name }}</option>
                            </select>
                            @if ($errors->has('iscatg_id'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('iscatg_id') }}</strong>
                            </span>
                            @endif
                        </div>
                        <div class="col-sm-2"></div>
                    </div>

                    <div
                        class="form-group row custom_form_group{{ $errors->has('item_deta_code') ? ' has-error' : '' }}">
                        <label class="control-label col-sm-4">Item Name:<span class="req_star">*</span></label>
                        <div class="col-sm-6">
                            <select class="form-control" name="item_deta_code" id="searchItemCode" required>
                                <option value="{{ $edit->item_deta_code }} "> {{ $edit->itemNameCode->item_deta_name }}</option>
                            </select>
                            @if ($errors->has('item_deta_code'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('item_deta_code') }}</strong>
                            </span>
                            @endif
                        </div>
                        <div class="col-sm-2"></div>
                    </div>

                    <div
                        class="form-group row custom_form_group{{ $errors->has('item_brand_id') ? ' has-error' : '' }}">
                        <label class="control-label col-sm-4">Brand Name:<span
                            class="req_star">*</span></label>
                        <div class="col-sm-6">
                            <select class="form-control" name="item_brand_id" id="searchItemCode" required>
                                <option value="{{ $edit->ibrand_id }} "> {{ $edit->itemBrandName->item_brand_name }}</option>
                            </select>
                            @if ($errors->has('item_brand_id'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('item_brand_id') }}</strong>
                            </span>
                            @endif
                        </div>
                        <div class="col-sm-2"></div>
                    </div>

                    <div
                        class="form-group row custom_form_group{{ $errors->has('item_det_unit') ? ' has-error' : '' }}">
                        <label class="control-label col-sm-4">Item Unit:<span class="req_star">*</span></label>
                        <div class="col-sm-6">
                            <select class="form-control" name="item_det_unit" id="" required>
                                <option value="">Select Item Unit</option>
                                @foreach($invItems as $unit)
                                <option value="{{ $unit->value }}" {{ $unit->value == $edit->item_det_unit ? 'selected' : '' }}>{{ $unit->name }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('item_det_unit'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('item_deta_code') }}</strong>
                            </span>
                            @endif
                        </div>
                        <div class="col-sm-2"></div>
                    </div>

                    <div class="form-group row custom_form_group{{ $errors->has('model_no') ? ' has-error' : '' }}">
                        <label class="control-label col-sm-4">Model No:</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="model_no" value=" {{ $edit->model_no }}"
                                placeholder="Item Model No">
                            @if ($errors->has('model_no'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('model_no') }}</strong>
                            </span>
                            @endif
                        </div>
                        <div class="col-sm-2"></div>
                    </div>

                    <div class="form-group row custom_form_group{{ $errors->has('serial_no') ? ' has-error' : '' }}">
                        <label class="control-label col-sm-4">Serial No:</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="serial_no" value="{{ $edit->serial_no }}"
                                placeholder="Item Serial No">
                            @if ($errors->has('serial_no'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('serial_no') }}</strong>
                            </span>
                            @endif
                        </div>
                        <div class="col-sm-2"></div>
                    </div>

                    <div class="form-group row custom_form_group{{ $errors->has('invoice_no') ? ' has-error' : '' }}">
                        <label class="control-label col-sm-4">Invoice No:<span class="req_star">*</span></label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="invoice_no" value="{{ $edit->invoice_no }}"
                                placeholder="Item Invoice Number" required>
                            @if ($errors->has('invoice_no'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('invoice_no') }}</strong>
                            </span>
                            @endif
                        </div>
                        <div class="col-sm-2"></div>
                    </div>

                    <div class="form-group row custom_form_group{{ $errors->has('invoice_date') ? ' has-error' : '' }}">
                        <label class="control-label col-sm-4">Item Invoice Date:<span class="req_star">*</span></label>
                        <div class="col-sm-6">
                            <input type="date" class="form-control" name="invoice_date" value="{{ $edit->invoice_date }}"
                                required>
                            @if ($errors->has('invoice_date'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('invoice_date') }}</strong>
                            </span>
                            @endif
                        </div>
                        <div class="col-sm-2"></div>
                    </div>

                    <div
                        class="form-group row custom_form_group{{ $errors->has('recieved_date') ? ' has-error' : '' }}">
                        <label class="control-label col-sm-4">Item Received Date:<span class="req_star">*</span></label>
                        <div class="col-sm-6">
                            <input type="date" class="form-control" name="recieved_date" value="{{ $edit->received_date }}"
                                required>
                            @if ($errors->has('recieved_date'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('recieved_date') }}</strong>
                            </span>
                            @endif
                        </div>
                        <div class="col-sm-2"></div>
                    </div>

                    <div class="form-group row custom_form_group{{ $errors->has('quantity') ? ' has-error' : '' }}">
                        <label class="control-label col-sm-4">Quantity:<span class="req_star">*</span></label>
                        <div class="col-sm-6">
                            <input type="number" class="form-control" name="quantity" value="{{ $edit->quantity }}"
                                min="0" placeholder="Item Quantity Number" required>
                            @if ($errors->has('quantity'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('quantity') }}</strong>
                            </span>
                            @endif
                        </div>
                        <div class="col-sm-2"></div>
                    </div>

                </div>
                <div class="card-footer card_footer_button text-center">
                    <button type="submit" class="btn btn-primary waves-effect">Update</button>
                </div>
            </div>
        </form>
    </div>
    <div class="col-md-3"></div>
</div>



<script type="text/javascript">
    $(document).ready(function () {
        $('select[name="itype_id"]').on('change', function () {
            var itype_id = $(this).val();
            if (itype_id) {
                $.ajax({
                    url: "{{  url('/admin/item/category/ajax') }}/" + itype_id,
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        var d = $('select[name="icatg_id"]').empty();
                        $.each(data, function (key, value) {

                            $('select[name="icatg_id"]').append('<option value="' + value.icatg_id + '">' + value.icatg_name + '</option>');

                        });
                    },
                });
            } else {

            }
        });

        $('select[name="icatg_id"]').on('change', function () {
            var icatg_id = $(this).val();
            if (icatg_id) {
                $.ajax({
                    url: "{{  url('/admin/item/sub-category/ajax/') }}/" + icatg_id,
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        var d = $('select[name="iscatg_id"]').empty();
                        $.each(data, function (key, value) {

                            $('select[name="iscatg_id"]').append('<option value="' + value.iscatg_id + '">' + value.iscatg_name + '</option>');

                        });
                    },

                });
            } else {

            }
        });

        $('select[name="iscatg_id"]').on('change', function () {
            var iscatg_id = $(this).val();
            if (iscatg_id) {
                $.ajax({
                    url: "{{  url('/admin/item-name/ajax/') }}/" + iscatg_id,
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        var d = $('select[name="item_deta_code"]').empty();
                        $.each(data, function (key, value) {

                            $('select[name="item_deta_code"]').append('<option value="' + value.item_deta_code + '">' + value.item_deta_name + (value.item_deta_code) + '</option>');

                        });
                    },

                });
            } else {

            }
        });

        $('select[name="item_deta_code"]').on('change', function () {
            var item_deta_code = $(this).val();
            alert(item_deta_code)
            if (item_deta_code) {
                $.ajax({
                    url: "{{  url('/admin/item-brand/ajax/') }}/" + item_deta_code,
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        var d = $('select[name="item_brand_id"]').empty();
                        $.each(data, function (key, value) {

                            $('select[name="item_brand_id"]').append('<option value="' + value.ibrand_id + '">' + value.item_brand_name + '</option>');

                        });
                    },

                });
            } else {

            }
        });

    });

</script>






@endsection
