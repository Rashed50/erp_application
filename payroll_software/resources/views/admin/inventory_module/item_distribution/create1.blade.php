@extends('layouts.admin-master')
@section('title')Item Received @endsection
@section('content')
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Items Distribution</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active"> Items Distribution</li>
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
    <div class="col-md-1"></div>
    <div class="col-md-10">
        <!-- Serach By Item Code For Item Details Info Start -->
         
        <div class="card"> 
            <div class="card-body card_form" style="padding-top: 0;">
                <div class="row">
                    <div class="col-md-3">
                        <div  class="form-group row custom_form_group{{ $errors->has('item_code') ? ' has-error' : '' }}">
                            <label class="control-label col-sm-4">Search Item By Code:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="itemCodeNumber" name="item_code"
                                    value="{{old('item_code')}}" placeholder="Enter Item Code" key>
                                @if ($errors->has('item_code'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('item_code') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" style="margin-top: 2px" onclick="searchItemByItmCode()"
                            class="btn btn-primary waves-effect">SEARCH</button>
                    </div>

                    <div class="col-md-3">
                        <div  class="form-group row custom_form_group{{ $errors->has('searching_emp_id') ? ' has-error' : '' }}">
                            <label class="control-label col-sm-4">Search Received Items:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="searching_emp_id" name="searching_emp_id"
                                     placeholder="Enter Emp ID or Iqama No">
                                @if ($errors->has('emp_id'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('searching_emp_id') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" style="margin-top: 2px"  onclick="searchingEmployeeReceivedItems()"
                            class="btn btn-primary waves-effect">SEARCH DETAILS</button>
                    </div>

                    <div class="col-md-2">
                        <button type="button" style="margin-top: 2px"  onclick="getItemListWithCode()"
                            class="btn btn-primary waves-effect">Item Code List</button>
                    </div>

                </div>
            </div>
        </div>

        <!-- Serach By Item Code For Item Details Info End -->
        <form class="form-horizontal d-none"  id="emp_item_recived_form" action="{{ route('emp.received.item.info.insert.request') }}"
            method="post">
            @csrf
            <div class="card">
                <div class="card-header"> 
                </div>
                <div class="card-body card_form" style="padding-top: 0;">

                    <div class="form-group row custom_form_group{{ $errors->has('itype_id') ? ' has-error' : '' }}">
                        <label class="control-label col-sm-4">Item Type:<span class="req_star">*</span></label>
                        <div class="col-sm-6">
                            <select class="form-control" name="itype_id" id="searchItype_id" required>
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
                        <div class="col-sm-2"></div>
                    </div>

                    <div class="form-group row custom_form_group{{ $errors->has('icatg_id') ? ' has-error' : '' }}">
                        <label class="control-label col-sm-4">Item Category:<span class="req_star">*</span></label>
                        <div class="col-sm-6">
                            <select class="form-control" name="icatg_id" id="searchIcateg_id" required>
                                <option value="">Select Category Name</option>
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
                        <label class="control-label col-sm-4">Subcategory:<span class="req_star">*</span></label>
                        <div class="col-sm-6">
                            <select class="form-control" name="iscatg_id" id="searchIsubCateg_id" required>
                                <option value="">Select Subcategory Name</option>
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
                        <input type="hidden" class="form-control" id="item_code_auto_id" name="item_code_auto_id" required >
                        <div class="col-sm-6">
                            <select class="form-control" name="item_deta_code" id="searchItemCode" required>
                                <option value="">Select Item Name</option>
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
                            <select class="form-control" name="item_brand_id" id="item_brand_idFocus" required>
                                <option value="">Select Brand Name</option>
                                @foreach($allBrand as $brand)
                                <option value="{{ $brand->ibrand_id }}">{{ $brand->item_brand_name }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('item_brand_id'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('item_brand_id') }}</strong>
                            </span>
                            @endif
                        </div>                        
                    </div>

                    <div
                        class="form-group row custom_form_group{{ $errors->has('store_id') ? ' has-error' : '' }}">
                        <label class="control-label col-sm-4">Received From:<span
                            class="req_star">*</span></label>
                        <div class="col-sm-6">
                            <select class="form-control" name="store_id" id="store_id" required>
                                <option value="">Select Store Name</option>
                                @foreach($storeList as $subStore)
                                <option value="{{ $subStore->sub_store_id }}">{{ $subStore->sub_store_name }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('store_id'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('store_id') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div
                        class="form-group row custom_form_group{{ $errors->has('item_det_unit') ? ' has-error' : '' }}">
                        <label class="control-label col-sm-4">Item Unit:<span class="req_star">*</span></label>
                        <div class="col-sm-6">
                            <select class="form-control" name="item_det_unit" id="item_det_unit" required>
                                <option value="">Select Item Unit</option>
                                @foreach($itemUnitList as $unit)
                                <option value="{{ $unit->value }}">{{ $unit->name }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('item_det_unit'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('item_det_unit') }}</strong>
                            </span>
                            @endif
                        </div>
                        <div class="col-sm-2"></div>
                    </div>

                    <div class="form-group row custom_form_group{{ $errors->has('model_no') ? ' has-error' : '' }}">
                        <label class="control-label col-sm-4">Model No:</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="model_no" name="model_no"
                                value="{{old('model_no')}}" placeholder="Item Model No">
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
                            <input type="text" class="form-control" id="serial_no" name="serial_no"
                                value="{{old('serial_no')}}" placeholder="Item Serial No">
                            @if ($errors->has('serial_no'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('serial_no') }}</strong>
                            </span>
                            @endif
                        </div>
                        <div class="col-sm-2"></div>
                    </div>

                    <div class="form-group row custom_form_group{{ $errors->has('employee_id') ? ' has-error' : '' }}">
                        <label class="control-label col-sm-4">Employee ID:<span class="req_star">*</span></label>
                        <div class="col-sm-6">
                        <input type="text" class="form-control typeahead" placeholder="Type Employee ID" name="emp_id" id="emp_id_search" onkeyup="empSearch()" onfocus="showResult()" onblur="hideResult()" value="{{ old('emp_id') }}">
                        @if ($errors->has('emp_id'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('emp_id') }}</strong>
                        </span>
                        @endif
                            <div id="showEmpId"></div>
                        </div>
                        
                    </div> 
                    <div
                        class="form-group row custom_form_group{{ $errors->has('recieved_date') ? ' has-error' : '' }}">
                        <label class="control-label col-sm-4">Item Received Date:<span class="req_star">*</span></label>
                        <div class="col-sm-6">
                            <input type="date" class="form-control" name="recieved_date" value="{{date('Y-m-d')}}"
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
                            <input type="number" class="form-control" name="quantity" value="{{old('quantity')}}"
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
                    <button type="submit" class="btn btn-primary waves-effect">SAVE</button>
                </div>
            </div>
        </form>
    </div>
    <div class="col-md-1"></div>
</div>


<!-- Modal For New Company Name Insert Start -->
<div class="modal fade" id="exampleModalForCompany" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add New Company Name</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">

                    <form class="form-horizontal" id="Inventory-item-brand-form" method="post"
                        action="{{ route('insert.item-type.item-company-name') }}">
                        @csrf
                        <div class="card">
                            <div class="card-body card_form">

                                <div
                                    class="form-group row custom_form_group{{ $errors->has('item_comp_name') ? ' has-error' : '' }}">
                                    <label class="control-label col-sm-5">Company Name:</label>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" name="item_comp_name"
                                            id="itemCompanyNameModal" value="{{old('item_comp_name')}}"
                                            placeholder="Item Compnay Name Here" required autofocus>
                                        @if ($errors->has('item_comp_name'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('item_comp_name') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>
                                <br>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary">Add Info</button>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                {{-- <button type="button" class="btn btn-primary">Save changes</button> --}}
            </div>
        </div>
    </div>
</div>
<!-- Modal For New Company Name Insert End -->

<div class="row d-none" id="received_item_list_table">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table id="alltableinfo" class="table table-bordered custom_table mb-0">
                                <thead>
                                    <tr>
                                        <th>S.N</th>
                                        <th>Emp. ID</th>
                                        <th>Emplpoyee</th>
                                        <th>Item Name</th>
                                        <th>Code</th>
                                        <th>Brand</th>
                                        <th>Store Name</th>                                       
                                        <th>Date</th>
                                        <th>Unit</th>
                                        <th>Qty</th>
                                        <th>Manage</th>
                                    </tr>
                                </thead>
                                <tbody id="item_list_table">
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script type="text/javascript">


 // emp_item_recived_form

    $(document).ready(function () {

        // Item Searching by ENter Key press
        $('#itemCodeNumber').keydown(function (e) {
            if (e.keyCode == 13) {
                searchItemByItmCode();
            }
        })
         // Item Searching by ENter Key press
        $('#itemCodeNumberForBrand').keydown(function (e) {
            if (e.keyCode == 13) {
                searchItemsByItmCodeForBrand();
            }
        })
        // Make Model No Upper Case
        $('#model_no').keyup(function () {
            this.value = this.value.toLocaleUpperCase();
        });
        // Make Serial No Upper Case
        $('#serial_no').keyup(function () {
            this.value = this.value.toLocaleUpperCase();
        });
        // Make Company Name Upper Case from modal
        $('#itemCompanyNameModal').keyup(function () {
            this.value = this.value.toLocaleUpperCase();
        });
        // Make Brand No Upper Case from modal
        $('#itemBrandNameModal').keyup(function () {
            this.value = this.value.toLocaleUpperCase();
        });

        /* =========================================================================
           ================ This Is For Item Details Form =========================
           ==========================================================================  */
        $('select[name="itype_id"]').on('change', function () {
            var itype_id = $(this).val();
            if (itype_id) {
                $.ajax({
                    url: "{{  url('/admin/item/category/ajax') }}/" + itype_id,
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        var c = $('select[name="icatg_id"]').empty();
                        var s = $('select[name="iscatg_id"]').empty();
                        // var b = $('select[name="item_brand_id"]').empty();
                        var i = $('select[name="item_deta_code"]').empty();
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
                        var s = $('select[name="iscatg_id"]').empty();
                        // var b = $('select[name="item_brand_id"]').empty();
                        var i = $('select[name="item_deta_code"]').empty();
                        $.each(data, function (key, value) {

                            $('select[name="iscatg_id"]').append('<option value="' + value.iscatg_id + '">' + value.iscatg_name + '</option>');

                        });
                    },

                });
            } else {

            }
        });

        // $('select[name="iscatg_id"]').on('change', function () {
        //     var iscatg_id = $(this).val();
        //     if (iscatg_id) {
        //         $.ajax({
        //             url: "{{  url('/admin/item-brand/ajax/') }}/" + iscatg_id,
        //             type: "GET",
        //             dataType: "json",
        //             success: function (data) {
        //                 var b = $('select[name="item_brand_id"]').empty();
        //                 var i = $('select[name="item_deta_code"]').empty();
        //                 $.each(data, function (key, value) {

        //                     $('select[name="item_brand_id"]').append('<option value="' + value.ibrand_id + '">' + value.item_brand_name + '</option>');

        //                 });
        //             },

        //         });
        //     } else {

        //     }
        // });

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


        /* =========================================================================
           ================ This Is For Item Brand Name Form =========================
           ==========================================================================  */
        $('select[name="itype_idB"]').on('change', function () {
            var itype_id = $(this).val();
            if (itype_id) {
                $.ajax({
                    url: "{{  url('/admin/item/category/ajax') }}/" + itype_id,
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        var c = $('select[name="icatg_idB"]').empty();
                        var s = $('select[name="iscatg_idB"]').empty();
                        var i = $('select[name="item_idB"]').empty();
                        $.each(data, function (key, value) {

                            $('select[name="icatg_idB"]').append('<option value="' + value.icatg_id + '">' + value.icatg_name + '</option>');

                        });
                    },
                });
            } else {

            }
        });

        $('select[name="icatg_idB"]').on('change', function () {
            var icatg_id = $(this).val();
            if (icatg_id) {
                $.ajax({
                    url: "{{  url('/admin/item/sub-category/ajax/') }}/" + icatg_id,
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        var d = $('select[name="iscatg_idB"]').empty();
                        var d = $('select[name="item_idB"]').empty();
                        $.each(data, function (key, value) {

                            $('select[name="iscatg_idB"]').append('<option value="' + value.iscatg_id + '">' + value.iscatg_name + '</option>');

                        });
                    },

                });
            } else {

            }
        });

        $('select[name="iscatg_idB"]').on('change', function () {
            var iscatg_id = $(this).val();
            if (iscatg_id) {
                $.ajax({
                    url: "{{  url('/admin/item-name/ajax/') }}/" + iscatg_id,
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        var d = $('select[name="item_idB"]').empty();
                        $.each(data, function (key, value) {

                            $('select[name="item_idB"]').append('<option value="' + value.item_id + '">' + value.item_deta_name + '</option>');

                        });
                    },

                });
            } else {

            }
        });
    });


    // Serch Item Name Details for form input Data
    function searchItemByItmCode() {
        var itemCodeNo = $("#itemCodeNumber").val();
        if ($("#itemCodeNumber").val().length === 0) {
            //  start message
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            })
            if ($.isEmptyObject(itemCodeNo)) {
                Toast.fire({
                    type: 'error',
                    title: "Please Fill This Field First, For Searching Item Details Info!!!"
                })
            }  
            //  end message

        } else {

            $.ajax({
                type: 'POST',
                url: "{{ route('item-details-info-by-itemCode') }}",
                data: {
                    itemCode: itemCodeNo,
                },
                dataType: 'json',
                success: function (response) {

                    console.log(response)
                    
                    $("#emp_item_recived_form").removeClass("d-none").addClass("d-block");
                    $("#received_item_list_table").removeClass("d-block").addClass("d-none");

                    $("select[id='searchItype_id']").empty();
                    $("select[id='searchIcateg_id']").empty();
                    $("select[id='searchIsubCateg_id']").empty();
                    $("select[id='searchItemCode']").empty();

                    $("select[id='searchItype_id']").append('<option value="' + response.itemInfos[0].itype_id + '" >' + response.itemInfos[0].itype_name + '</option>');
                    $("select[id='searchIcateg_id']").append('<option value="' + response.itemInfos[0].icatg_id + '">' + response.itemInfos[0].icatg_name + '</option>');
                    $("select[id='searchIsubCateg_id']").append('<option value="' + response.itemInfos[0].iscatg_id + '">' + response.itemInfos[0].iscatg_name + '</option>');
                    $("select[id='searchItemCode']").append('<option value="' + response.itemInfos[0].item_deta_code + '">' + response.itemInfos[0].item_deta_name + '</option>');
                    $('#item_code_auto_id').val(response.itemInfos[0].item_id);
                    $('#item_brand_idFocus').focus();

                } // end of success
            }); // end of ajax calling
        }
    }

    // Search Item Name Details With Item Code For Brand
    function searchItemsByItmCodeForBrand(){
        var itemCodeNo = $("#itemCodeNumberForBrand").val();
        if ($("#itemCodeNumberForBrand").val().length === 0) {
            //  start message
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            })
            if ($.isEmptyObject(itemCodeNo)) {
                Toast.fire({
                    type: 'error',
                    title: "Please Fill This Field First, For Searching Item Details Info!!!"
                })
            } else {
                Toast.fire({
                    type: 'success',
                    title: "Items Informations are"
                })
            }
            //  end message

        } else {
            $.ajax({
                type: 'POST',
                url: "{{ route('item-details-info-by-itemCode') }}",
                data: {
                    itemCode: itemCodeNo,
                },
                dataType: 'json',
                success: function (response) {
                    console.log(response)
                    $("select[id='searchItype_idForBrand']").empty();
                    $("select[id='searchIcateg_idForBrand']").empty();
                    $("select[id='searchIsubCateg_idForBrand']").empty();
                    $("select[id='searchItemCodeForBrand']").empty();

                    $("select[id='searchItype_idForBrand']").append('<option value="' + response.itemInfos[0].itype_id + '">' + response.itemInfos[0].itype_name + '</option>');
                    $("select[id='searchIcateg_idForBrand']").append('<option value="' + response.itemInfos[0].icatg_id + '">' + response.itemInfos[0].icatg_name + '</option>');
                    $("select[id='searchIsubCateg_idForBrand']").append('<option value="' + response.itemInfos[0].iscatg_id + '">' + response.itemInfos[0].iscatg_name + '</option>');

                    $('#itemBrandNameModal').focus();

                    if (response.total_item > 0) {
                        var d = $('select[id="searchItemCodeForBrand"]').empty();
                        $.each(response.itemInfos, function (key, value) {

                            $('select[id="searchItemCodeForBrand"]').append('<option value="' + value.item_deta_code + '">' + value.item_deta_name + (value.item_deta_code) + '</option>');

                        });

                    } else {
                        $("select[id='searchItemCodeForBrand']").append('<option value="' + response.itemInfos[0].item_deta_code + '">' + response.itemInfos[0].item_deta_name + '</option>');
                    }

                } // end of success
            }); // end of ajax calling
        }
    }

    
    $('#searching_emp_id').keydown(function (e) {
        
            if (e.keyCode == 13) {
                searchingEmployeeReceivedItems();
            }
    })

    function searchingEmployeeReceivedItems(){
       
         var empl_id_or_iqama = $("#searching_emp_id").val();       
        
            $.ajax({
                type: 'POST',
                url:"{{route('emp.received.item.info.search.ajaxrequest')}}",
                data: {
                    emp_id_or_iqama:empl_id_or_iqama,
                },
                dataType: 'json',
                success: function (response) {
                    
                    
                       if(response.status != 200){

                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000
                                })                             
                                Toast.fire({
                                    type: 'error',
                                    title: response.error
                                })
                                return ;                            
                       }
                      
                        $('#item_list_table').html('');
                        $("#emp_item_recived_form").removeClass("d-block").addClass("d-none");
                        $("#received_item_list_table").removeClass("d-none").addClass("d-block");

                        var rows = "";
                        var counter = 1;
                        $.each(response.data, function (key, value) {

                            rows += `
                                <tr>
                                     <td>${counter++}</td>
                                    <td>${value.employee_id}</td>
                                    <td>${value.employee_name}</td>
                                    <td>${value.item_deta_name}</td>
                                    <td>${value.item_code} </td>
                                    <td>${value.item_brand_name}</td>
                                    <td>${value.sub_store_name}</td>                                   
                                    <td>${value.received_date}</td>                                   
                                    <td> ${value.item_unit}</td>
                                    <td> ${value.received_qty}</td>   
                                    <td>
                                        </td>                                  
                                </tr>
                                `
                        });
  
                       $('#item_list_table').html(rows);  

                } // end of success
            }); // end of ajax calling
    }
 
 
   // show list of item name with code 
  function getItemListWithCode(){

        const queryString = new URLSearchParams({
            item_type_id: 0,
            category_id: 0,
            subcategory_id:3,           
        }).toString();
       

        var parameterValue = queryString; // Set parameter value
        var url = "{{ route('search.itemcode.list.report', ':parameter') }}";
        url = url.replace(':parameter', parameterValue);
        window.open(url, '_blank');
    }
 
 

</script>






@endsection
