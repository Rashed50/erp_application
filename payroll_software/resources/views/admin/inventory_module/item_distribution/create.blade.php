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
<!-- Session Flash Message-->
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

<!-- Searching MEnu-->
<div class="row">
    <div class="col-md-12">
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
                    <div class="col-md-1">
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
                        <button type="button" style="margin-top: 2px"  onclick="openItemReceivedPaperUploadSection()"
                            class="btn btn-primary waves-effect">Paper Upload</button>
                    </div>

                    <div class="col-md-1">
                        <button type="button" style="margin-top: 2px"  onclick="getItemListWithCode()"
                            class="btn btn-primary waves-effect">Code List</button>
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
                                <option value="1">Box</option>
                                <option value="2">Packet</option>
                                <option value="3">Liter</option>
                                <option value="4">Inch</option>
                                <option value="5">Feet</option>
                                <option value="6">Piece</option>
                                <option value="7">Dozen</option>
                                <option value="8">Pair</option>
                                <option value="9">Meter</option>
                              
    
    
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
     
</div>

 
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
                                        <th>Emp.ID</th>
                                        <th>Name</th>
                                        <th>Item Name</th>
                                        <th>Code</th>
                                        <th>Brand</th>
                                        <th>Store Name</th>                                       
                                        <th>Date</th>
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


<!--  Item Received Paper Upload section !-->
<div class="row d-none" id="item_received_paper_upload_section">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body card_form">
                <div class="form-group row custom_form_group">
                    <label class="col-sm-2 control-label">Items Received From</label>
                    <div class="col-sm-2">
                        <input type="date" name="from_date" id="from_date" value="<?= date("Y-m-d") ?>" max="{{date('Y-m-d') }}" class="form-control">
                    </div>                      
                    <label class="col-sm-2 control-label">To</label>
                    <div class="col-sm-2">
                        <input type="date" name="to_date" id="to_date" value="<?= date("Y-m-d") ?>"   class="form-control">
                    </div>                    
                    <div class="col-sm-2"> 
                        <button type="button"  id ="emp_search_button"  onclick="searchItemReceivedInsertedEmployees()" class="btn btn-primary waves-effect">Search</button>
                    </div>
                </div>

            </div>

        </div>
    </div> 

    <!-- Item Received Paper Upload !-->   
    <div class="card">
        <form method="post" action="{{ route('emp.item.received.paper.upload') }}" id="advance_paper_upload_form" enctype="multipart/form-data" 
             onsubmit="attendance_submit_button.disabled = true;">
                @csrf
                <div class="card-header">
                    <div class="row">
                        <div class="form-group row custom_form_group"> 
                            <label class="col-sm-2 control-label">Upload File:</label>
                            <div class="col-sm-2">
                                <div class="input-group">
                                    <span class="input-group-btn">
                                        <span class="btn btn-default btn-file btnu_browse">
                                            Browse… <input type="file" name="upload_paper" id="imgInp4">
                                        </span>
                                    </span>
                                    <input type="text" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <img id='img-upload4' class="upload_image" />
                            </div>
                            <div class="col-sm-2"> <label for="" id="total_selection_label"></label></div>
                           
                            <div class="col-sm-2">
                                <button type="submit" id="paper_upload_submit_button" name="paper_upload_submit_button" class="btn btn-primary waves-effect">Submit </button>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <span id="data_not_found" class="d-none">Data Not Found!</span>
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
                                    <tbody id="employee_list_for_upload_paper">                                     
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>  
        </form>
    </div>

</div>

<script type="text/javascript">
  
    function openItemReceivedFormSection(){
       $('#item_received_paper_upload_section').removeClass('d-block').addClass('d-none');
       $('#received_item_list_table').removeClass('d-block').addClass('d-none');
       $('#emp_item_recived_form').removeClass('d-none').addClass('d-block');
    }
    function openItemReceivedSearchSection(){
       $('#item_received_paper_upload_section').removeClass('d-block').addClass('d-none');
       $('#emp_item_recived_form').removeClass('d-block').addClass('d-none');
       $('#received_item_list_table').removeClass('d-none').addClass('d-block');
    }
    
    function openItemReceivedPaperUploadSection(){
       $('#emp_item_recived_form').removeClass('d-block').addClass('d-none');
       $('#received_item_list_table').removeClass('d-block').addClass('d-none');
       $('#item_received_paper_upload_section').removeClass('d-none').addClass('d-block');

    }
  
     // upload item received paper
    function searchItemReceivedInsertedEmployees(){
        var from_date = $("#from_date").val();       
        var to_date = $("#to_date").val();      
        

        $.ajax({
            type: 'GET',
            url:"{{route('item.received.emp.search.forpaper.upload')}}",
            data: {
                from_date:from_date,
                to_date:to_date, 
            },
            dataType: 'json',
            success: function (response) {

                   if(response.status != 200){
                         showSweetAlertMessage('error',"Data Not Found ");  
                         return ; 
                   } 

                    openItemReceivedPaperUploadSection();
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
                                <td>${value.item_unit}</td>
                                <td>${value.received_qty}</td>   
                                <td>
                                    <input type="hidden" id="item_rec_auto_id${value.item_received_auto_id}" name="item_rec_auto_id_list[]" value="${value.item_received_auto_id}">                                                             
                                    <input type="checkbox" name="paper_checkbox-${value.item_received_auto_id}" onclick="countTotalSelection()" id="paper_checkbox-${value.item_received_auto_id}" value="0">
                                </td>  
                                <td style="color:#fff">${value.item_received_auto_id}</td>                             
                            </tr>
                            `
                    });

                   $('#employee_list_for_upload_paper').html(rows);  

            } // end of success
        }); // 
    }
 

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
                showSweetAlertMessage('error','Please Input Valid Data'); 
                return; 
            } 

            $.ajax({
                type: 'POST',
                url: "{{ route('item-details-info-by-itemCode') }}",
                data: {
                    itemCode: itemCodeNo,
                },
                dataType: 'json',
                success: function (response) {
                    
                    openItemReceivedFormSection();
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

    // Search Item Name Details With Item Code For Brand
    function searchItemsByItmCodeForBrand(){
        var itemCodeNo = $("#itemCodeNumberForBrand").val();
        if ($("#itemCodeNumberForBrand").val().length === 0) {            
            showSweetAlertMessage('error','Please Input Valid Data'); 
                return; 
        }  
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

    
    $('#searching_emp_id').keydown(function (e) {
        
            if (e.keyCode == 13) {
                searchingEmployeeReceivedItems();
            }
    })

    function searchingEmployeeReceivedItems(){
         
            
            var empl_id_or_iqama = $("#searching_emp_id").val(); 
            if ($("#searching_emp_id").val().length === 0) {            
                showSweetAlertMessage('error','Please Input Valid Data'); 
                return; 
            }           
            $('#item_list_table').html('');      
            $.ajax({
                type: 'POST',
                url:"{{route('emp.received.item.info.search.ajaxrequest')}}",
                data: {
                    emp_id_or_iqama:empl_id_or_iqama,
                },
                dataType: 'json',
                success: function (response) {

                       if(response.status != 200){
                           showSweetAlertMessage('error',"Data Not Found ");  
                           return;                   
                       }                    
                        
                        openItemReceivedSearchSection()
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
                                    <td> ${value.received_qty}</td>   
                                    <td>
                                     @can('emp_received_item_record_delete')
                                        <a href="#" onClick="deleteEmployeeRecievedItemRecord(${value.item_received_auto_id})" title="Delete"><i id="" class="fa fa-trash fa-lg delete_icon"></i></a> 
                                     @endcan 
                                    
                                    </td>                             
                                </tr>
                                `
                        });

                       $('#item_list_table').html(rows);  

                } // end of success
            }); // end of ajax calling
    }
    
     function deleteEmployeeRecievedItemRecord(item_received_auto_id){
        
        swal({
            title: "Are you sure?",
            text: "Once deleted, You will not be able to recover this Record!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
        .then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    type: 'delete',
                    url: "{{url('admin/inventory/item/received-item-delete')}}/" + item_received_auto_id,
                    dataType: 'json',
                    success: function (response) {
                        if(response.status == 200){    
                            showSweetAlertMessage("success",response.message);
                            searchingEmployeeReceivedItems();
                        }else {
                         showSweetAlertMessage("error",response.message);
                        }                     
                    },
                    error:function(response){
                         showSweetAlertMessage("error",response.message);
                    }
                });
            }
        });
        //  window.location.reload();
    }
     
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
