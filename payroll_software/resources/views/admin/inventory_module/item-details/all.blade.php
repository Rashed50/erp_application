@extends('layouts.admin-master')
@section('title')
    Item Receive at Store
@endsection
@section('internal-css')
    <style>
        [type=number]::-webkit-inner-spin-button,
        [type=number]::-webkit-outer-spin-button {
            height: auto;
        }

        [type=search] {
            outline-offset: -2px;
            -webkit-appearance: none;
        }

        [type=search]::-webkit-search-decoration {
            -webkit-appearance: none;
        }

        ::-webkit-file-upload-button {
            font: inherit;
            -webkit-appearance: button;
        }

        .custom_form_group label.control-label {
            text-align: right;
            font-weight: bold;
            margin-top: 5px;
            margin-right: 5px;
            font-size: 14px;
        }

        .form-group {
            display: flex;
            align-items: center;
        }

        .form-group label {
            margin-right: 10px;
            min-width: 100px;
            /* Adjust as needed */
        }

        .form-group select {
            flex: 1;
        }

        .search-form-group label {
            min-width: 100px;
            /* Adjust as needed */
            text-align: right;
        }

        .custom_form_group_search label.control-label {
            text-align: right;
            font-weight: bold;
            margin-top: 5px;
            font-size: 14px;
        }
    </style>
@endsection
@section('content')
    <div class="row bread_part">
        <div class="col-sm-12 bread_col">
            <h4 class="pull-left page-title bread_title">Receive New Item </h4>
            <ol class="breadcrumb pull-right">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="active">Inventory</li>
            </ol>
        </div>
    </div>
    <!-- add division -->
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8">
            @if (Session::has('success'))
                <div class="alert alert-success alertsuccess" role="alert">
                    <strong>{{ Session::get('success') }}</strong>
                </div>
            @endif

            @if (Session::has('error'))
                <div class="alert alert-warning alerterror" role="alert">
                    <strong>{{ Session::get('error') }}</strong>
                </div>
            @endif
        </div>
    </div>

     {{-- Item search by item code start --}}
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <!-- Search By Item Code For Item Details Info Start -->
            <div class="card"><br>
                <div class="card-body card_form" style="padding-top: 0;">
                    <div class="row align-items-center">
                        <div class="col-md-10">
                            <div
                                class="search-form-group row custom_form_group_search{{ $errors->has('item_code') ? ' has-error' : '' }}">
                                <label for="itemCodeNumber" class="col-sm-4 col-form-label">Search Item By Code:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="itemCodeNumber" name="item_code"
                                        placeholder="Enter Item Code">
                                    @if ($errors->has('item_code'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('item_code') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 text-right">
                            <button type="button" onclick="searchItemByItmCode()"
                                class="btn btn-primary waves-effect">SEARCH</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Search By Item Code For Item Details Info End -->
        </div>
    </div>


     {{-- item purchase form and item stock add to cart form start --}}
    <div class="row">
        <div class="col-md-6">
            <form id="item_purchase_form" action="/admin/inventory-items/purchase/with-cart-items" method="POST"
                enctype="multipart/form-data">
                @csrf
                <div class="card">

                    <div class="form-group row mt-4 custom_form_group{{ $errors->has('store_id') ? ' has-error' : '' }}">
                        <label class="control-label col-sm-3">Store Name:<span class="req_star">*</span></label>
                        <div class="col-sm-6">
                            <select class="form-select" name="store_id" id="store_id" required>
                                <option value="">Select Store Name</option>
                                @foreach ($storeList as $subStore)
                                    <option value="{{ $subStore->sub_store_id }}">{{ $subStore->sub_store_name }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($errors->has('store_id'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('store_id') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="col-sm-3"></div>
                    </div>
                    <div class="form-group row custom_form_group{{ $errors->has('purchase_by') ? ' has-error' : '' }}">
                        <label class="control-label col-sm-3">Purchase By:<span class="req_star">*</span></label>
                        <div class="col-sm-6">
                            <select class="form-select" name="purchase_by" id="purchase_by">
                                <option value="">Select Purchase By Name</option>
                                @foreach ($purchase_by as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-3"></div>
                    </div>

                    <div class="form-group row custom_form_group{{ $errors->has('purchase_from') ? ' has-error' : '' }}">
                        <label class="control-label col-sm-3">Supplyer Name: </label>
                        <div class="col-sm-6">

                            <select class="form-select" name="purchase_from" id="purchase_from">
                                <option value="">Select Purchase From Name</option>
                                @foreach ($purchase_from as $item)
                                    <option value="{{ $item->isupp_auto_id }}">{{ $item->isupp_name }}
                                    </option>
                                @endforeach
                            </select>

                        </div>
                        <div class="col-sm-3"></div>
                    </div>

                    <div class="form-group row custom_form_group{{ $errors->has('invoice_no') ? ' has-error' : '' }}">
                        <label class="control-label col-sm-3">Invoice No:<span class="req_star">*</span></label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="invoice_no" value="{{ old('invoice_no') }}"
                                placeholder="Item Invoice Number" required>
                            @if ($errors->has('invoice_no'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('invoice_no') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="col-sm-3"></div>
                    </div>

                    <div class="form-group row custom_form_group{{ $errors->has('chalan_no') ? ' has-error' : '' }}">
                        <label class="control-label col-sm-3">Challan Number:</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="chalan_no" value="{{ old('chalan_no') }}"
                                placeholder="Item Chalan Number" required>
                            @if ($errors->has('chalan_no'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('chalan_no') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="col-sm-3"></div>
                    </div>

                    <div class="form-group row custom_form_group{{ $errors->has('invoice_date') ? ' has-error' : '' }}">
                        <label class="control-label col-sm-3">Invoice Date:<span class="req_star">*</span></label>
                        <div class="col-sm-6">
                            <input type="date" class="form-control" name="invoice_date" value="{{ date('Y-m-d') }}"
                                required>
                            @if ($errors->has('invoice_date'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('invoice_date') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="col-sm-3"></div>
                    </div>

                    <div class="form-group row custom_form_group{{ $errors->has('received_date') ? ' has-error' : '' }}">
                        <label class="control-label col-sm-3">Received Date:<span class="req_star">*</span></label>
                        <div class="col-sm-6">
                            <input type="date" class="form-control" name="received_date" value="{{ date('Y-m-d') }}"
                                required>
                            @if ($errors->has('received_date'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('received_date') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="col-sm-3"></div>
                    </div>



                    <div class="row">
                        <div class="card-footer card_footer_button text-center">
                            <button id="purchase_button" onclick="itemPurchaseForm()"
                                class="btn btn-primary waves-effect">SAVE ALL</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>


        <div class="col-md-6">
            <form class="form-horizontal" id="item_stock_cart_form" action="{{ route('item-infos-add-to-cart') }} "
                method="post">
                @csrf
                <div class="card">

                    <div class="card-body card_form" style="padding-top: 0;">

                        <div class="row mt-4">
                            <div class="col-sm-6">
                                <div
                                    class="form-group custom_form_group{{ $errors->has('itype_id') ? ' has-error' : '' }}">
                                    <label class="control-label text-right" for="searchItype_id">Item Type:<span
                                            class="req_star">*</span></label>
                                    <select class="form-select" name="itype_id" id="searchItype_id" required>
                                        <option value="">Select Item Type</option>
                                        @foreach ($allType as $type)
                                            <option value="{{ $type->itype_id }}">{{ $type->itype_name }}</option>
                                        @endforeach
                                    </select>
                                    @error('itype_id')
                                        <span style="color:red">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div
                                    class="form-group custom_form_group{{ $errors->has('icatg_id') ? ' has-error' : '' }}">
                                    <label class="control-label text-right" for="searchIcateg_id">Item Category:<span
                                            class="req_star">*</span></label>
                                    <select class="form-select" name="icatg_id" id="searchIcateg_id" required>
                                        <option value="">Select Category Name</option>
                                    </select>
                                    @error('icatg_id')
                                        <span style="color:red">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6">
                                <div
                                    class="form-group custom_form_group{{ $errors->has('iscatg_id') ? ' has-error' : '' }}">
                                    <label class="control-label">Subcategory:<span class="req_star">*</span></label>
                                    <select class="form-select" name="iscatg_id" id="searchIsubCateg_id" required>
                                        <option value="">Select Subcategory Name</option>
                                    </select>
                                    @error('iscatg_id')
                                        <span style="color:red">{{ $message }}</span>
                                    @enderror
                                </div>

                            </div>
                            <div class="col-sm-6">
                                <div
                                    class="form-group custom_form_group{{ $errors->has('item_deta_code') ? ' has-error' : '' }}">
                                    <label class="control-label">Item Name:<span class="req_star">*</span></label>
                                    <select class="form-select" name="item_deta_code" id="searchItemCode" required>
                                        <option value="">Select Item Name</option>
                                    </select>
                                    @if ($errors->has('item_deta_code'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('item_deta_code') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">


                            <div class="col-sm-6">
                                <div class="form-group custom_form_group{{ $errors->has('item_brand_idFocus') ? ' has-error' : '' }}">
                                    <label class="control-label">Brand<span class="req_star">*</span></label>
                                    <select class="form-select" name="item_brand_id" id="item_brand_idFocus"
                                            required>
                                            <!-- <option value="">Select Brand Name</option> -->
                                            @foreach ($allBrand as $brand)
                                                <option value="{{ $brand->ibrand_id }}">{{ $brand->item_brand_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('item_brand_id'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('item_brand_id') }}</strong>
                                            </span>
                                        @endif
                                        <button type="button" class="btn btn-success" data-toggle="modal"
                                            data-target="#exampleModal">
                                            New
                                        </button>
                                </div>
                            </div>


                            <div class="col-sm-6">
                                <div
                                    class="form-group custom_form_group{{ $errors->has('serial_no') ? ' has-error' : '' }}">
                                    <label class="control-label">Serial/Size:</label>
                                    <input type="text" class="form-control" id="serial_no" name="serial_no"
                                          placeholder="Item Serial No">
                                    @if ($errors->has('serial_no'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('serial_no') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div
                                    class="form-group custom_form_group{{ $errors->has('model_no') ? ' has-error' : '' }}">
                                    <label class="control-label">Model:</label>
                                    <input type="text" class="form-control" id="model_no" name="model_no"
                                          placeholder="Item Model No">
                                    @if ($errors->has('model_no'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('model_no') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div   class="form-group custom_form_group{{ $errors->has('quantity') ? ' has-error' : '' }}">
                                    <label class="control-label">Quantity:<span class="req_star">*</span></label>
                                    <input type="number" class="form-control" name="quantity"  min="1" step="1" placeholder="Item Quantity"
                                        required>
                                    @if ($errors->has('quantity'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('quantity') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                          <div class="row">
                            <div class="col-md-12">
                                <div   class="form-group custom_form_group{{ $errors->has('model_no') ? ' has-error' : '' }}">
                                    <label class="control-label">Remarks:</label>
                                    <textarea class="form-control" id="remarks" name="remarks"   placeholder="Remarks(Optional)" rows="2" cols="50"></textarea>

                                </div>
                            </div>

                        </div>


                        <div class="form-group d-none row mt-2 custom_form_group{{ $errors->has('item_company_id') ? ' has-error' : '' }}">
                            <label class="control-label col-md-3">Owner Type:<span class="req_star">*</span></label>
                            <div class="col-md-6">
                                <select class="form-select" name="item_company_id" id="" required>
                                    <!-- <option value="">Select Company Name</option> -->
                                    @foreach ($allCompany as $company)
                                        <option value="{{ $company->item_comp_id }}">{{ $company->item_comp_name }}  </option>
                                    @endforeach
                                </select>
                                @if ($errors->has('item_company_id'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('item_company_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                             <div class="col-md-2">
                                    <button type="button" class="btn btn-success" data-toggle="modal"  data-target="#exampleModalForCompany">
                                        Add
                                    </button>
                            </div>
                        </div>



                        <div class="row">
                            <div class="col-md-9">

                            </div>

                            <div class="col-md-3">
                                  <button type="submit" id="add_to_card_btn" class="btn btn-primary waves-effect">ADD TO
                                    CART</button>
                            </div>

                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <!-- Modal For New Company Name Insert Start -->
    <div class="modal fade" id="exampleModalForCompany" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                                                id="itemCompanyNameModal" value="{{ old('item_comp_name') }}"
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

    <!-- Modal For New Brand Name Insert Start -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add New Brand Name</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <!-- Serach By Item Code For Item Details Info Start -->
                <div class="card"><br>
                    <div class="card-body card_form" style="padding-top: 0;">
                        <div class="row">
                            <div class="col-md-9">
                                <div class="form-group row custom_form_group{{ $errors->has('item_code') ? ' has-error' : '' }}"
                                    style="margin-right: -10px; margin-left: -10px;">
                                    <label class="control-label col-sm-4">Search Item:</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="itemCodeNumberForBrand"
                                            name="item_code" value="{{ old('item_code') }}"
                                            placeholder="Enter Item Code For Search" key autofocus>
                                        @if ($errors->has('item_code'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('item_code') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" style="margin-top: 2px" onclick="searchItemsByItmCodeForBrand()"
                                    class="btn btn-primary waves-effect">SEARCH</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Serach By Item Code For Item Details Info End -->
                <div class="modal-body">
                    <div class="row">

                        <form class="form-horizontal" id="Inventory-item-brand-form" method="post"
                            action="{{ route('insert.item-type-brand-name') }}">
                            @csrf
                            <div class="card">
                                <div class="card-body card_form">

                                    <div
                                        class="form-group row custom_form_group{{ $errors->has('itype_idB') ? ' has-error' : '' }}">

                                        <label class="control-label col-sm-5">Item Type:<span
                                                class="req_star">*</span></label>
                                        <div class="col-sm-7">
                                            <select class="form-control" name="itype_idB" id="searchItype_idForBrand"
                                                required>
                                                <option value="">Select Item Type</option>
                                                @foreach ($allType as $type)
                                                    <option value="{{ $type->itype_id }}">{{ $type->itype_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('itype_idB'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('itype_idB') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div
                                        class="form-group row custom_form_group{{ $errors->has('icatg_idB') ? ' has-error' : '' }}">
                                        <label class="control-label col-sm-5">Item Category:<span
                                                class="req_star">*</span></label>
                                        <div class="col-sm-7">
                                            <select class="form-control" name="icatg_idB" id="searchIcateg_idForBrand"
                                                required>
                                                <option value="">Select Item Category Name</option>
                                            </select>
                                            @if ($errors->has('icatg_idB'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('icatg_idB') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div
                                        class="form-group row custom_form_group{{ $errors->has('iscatg_idB') ? ' has-error' : '' }}">
                                        <label class="control-label col-sm-5">Item SubCategory:<span
                                                class="req_star">*</span></label>
                                        <div class="col-sm-7">
                                            <select class="form-control" name="iscatg_idB"
                                                id="searchIsubCateg_idForBrand" required>
                                                <option value="">Item Sub Category Name</option>
                                            </select>
                                            @if ($errors->has('iscatg_idB'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('iscatg_idB') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div
                                        class="form-group row custom_form_group{{ $errors->has('item_idB') ? ' has-error' : '' }}">
                                        <label class="control-label col-sm-5">Item Name:<span
                                                class="req_star">*</span></label>
                                        <div class="col-sm-7">
                                            <select class="form-control" name="item_idB" id="searchItemCodeForBrand"
                                                required>
                                                <option value="">Select Item Name</option>
                                            </select>
                                            @if ($errors->has('item_idB'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('item_idB') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div
                                        class="form-group row custom_form_group{{ $errors->has('brand_name') ? ' has-error' : '' }}">
                                        <label class="control-label col-sm-5">Brand Name:</label>
                                        <div class="col-sm-7">
                                            <input type="text" class="form-control" name="brand_name"
                                                id="itemBrandNameModal" value="{{ old('brand_name') }}"
                                                placeholder="Item Brand Name" required autofocus>
                                            @if ($errors->has('brand_name'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('brand_name') }}</strong>
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
                            <!-- </form> -->
                    </div>
                </div>
                <div class="modal-footer">
                    {{-- <button type="button" class="btn btn-primary">Save changes</button> --}}
                </div>
            </div>
        </div>
    </div>
    <!-- Modal For New Brand Name Insert End -->


    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    Item Details Cart List
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table id="alltableinfo" class="table table-bordered custom_table mb-0">
                                    <thead>
                                        <tr>
                                            <th>S.N</th>
                                            <th>Item Code</th>
                                            <th>Item Name</th>
                                            <th>Subcategory</th>
                                            <th>Category</th>
                                            <th>Model/Brand</th>
                                            {{-- <th>Brand</th> --}}
                                            <th>Serial/Size</th>
                                            {{-- <th>Unit</th> --}}
                                            <th>Qty</th>
                                            <th>Manage</th>
                                        </tr>
                                    </thead>
                                    <tbody id="item_details_cart_table_content_view">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <script type="text/javascript">
        $(document).ready(function() {

            // Item Searching by ENter Key press
            $('#itemCodeNumber').keydown(function(e) {
                if (e.keyCode == 13) {
                    searchItemByItmCode();
                }
            })
            // Item Searching by ENter Key press
            $('#itemCodeNumberForBrand').keydown(function(e) {
                if (e.keyCode == 13) {
                    searchItemsByItmCodeForBrand();
                }
            })
            // Make Model No Upper Case
            $('#model_no').keyup(function() {
                this.value = this.value.toLocaleUpperCase();
            });
            // Make Serial No Upper Case
            $('#serial_no').keyup(function() {
                this.value = this.value.toLocaleUpperCase();
            });
            // Make Company Name Upper Case from modal
            $('#itemCompanyNameModal').keyup(function() {
                this.value = this.value.toLocaleUpperCase();
            });
            // Make Brand No Upper Case from modal
            $('#itemBrandNameModal').keyup(function() {
                this.value = this.value.toLocaleUpperCase();
            });

            /* =========================================================================
               ================ This Is For Item Details Form =========================
               ==========================================================================  */
            $('select[name="itype_id"]').on('change', function() {
                var itype_id = $(this).val();
                if (itype_id) {
                    $.ajax({
                        url: "{{ url('/admin/item/category/ajax') }}/" + itype_id,
                        type: "GET",
                        dataType: "json",
                        success: function(data) {
                            var c = $('select[name="icatg_id"]').empty();
                            var s = $('select[name="iscatg_id"]').empty();
                            // var b = $('select[name="item_brand_id"]').empty();
                            var i = $('select[name="item_deta_code"]').empty();
                            $.each(data, function(key, value) {

                                $('select[name="icatg_id"]').append('<option value="' +
                                    value.icatg_id + '">' + value.icatg_name +
                                    '</option>');

                            });
                        },
                    });
                } else {

                }
            });

            $('select[name="icatg_id"]').on('change', function() {
                var icatg_id = $(this).val();
                if (icatg_id) {
                    $.ajax({
                        url: "{{ url('/admin/item/sub-category/ajax/') }}/" + icatg_id,
                        type: "GET",
                        dataType: "json",
                        success: function(data) {
                            var s = $('select[name="iscatg_id"]').empty();
                            // var b = $('select[name="item_brand_id"]').empty();
                            var i = $('select[name="item_deta_code"]').empty();
                            $.each(data, function(key, value) {

                                $('select[name="iscatg_id"]').append('<option value="' +
                                    value.iscatg_id + '">' + value.iscatg_name +
                                    '</option>');

                            });
                        },

                    });
                } else {

                }
            });

            $('select[name="iscatg_id"]').on('change', function() {
                var iscatg_id = $(this).val();
                if (iscatg_id) {
                    $.ajax({
                        url: "{{ url('/admin/item-name/ajax') }}/" + iscatg_id,
                        type: "GET",
                        dataType: "json",
                        success: function(data) {
                            var d = $('select[name="item_deta_code"]').empty();
                            $.each(data, function(key, value) {

                                $('select[name="item_deta_code"]').append(
                                    '<option value="' + value.item_deta_code +
                                    '">' + value.item_deta_name + " " + (value
                                        .item_deta_code) + '</option>');

                            });
                        },

                    });
                } else {

                }
            });


            /* =========================================================================
               ================ This Is For Item Brand Name Form =========================
               ==========================================================================  */
            $('select[name="itype_idB"]').on('change', function() {
                var itype_id = $(this).val();
                if (itype_id) {
                    $.ajax({
                        url: "{{ url('/admin/item/category/ajax') }}/" + itype_id,
                        type: "GET",
                        dataType: "json",
                        success: function(data) {
                            var c = $('select[name="icatg_idB"]').empty();
                            var s = $('select[name="iscatg_idB"]').empty();
                            var i = $('select[name="item_idB"]').empty();
                            $.each(data, function(key, value) {

                                $('select[name="icatg_idB"]').append('<option value="' +
                                    value.icatg_id + '">' + value.icatg_name +
                                    '</option>');

                            });
                        },
                    });
                } else {

                }
            });

            $('select[name="icatg_idB"]').on('change', function() {
                var icatg_id = $(this).val();
                if (icatg_id) {
                    $.ajax({
                        url: "{{ url('/admin/item/sub-category/ajax/') }}/" + icatg_id,
                        type: "GET",
                        dataType: "json",
                        success: function(data) {
                            var d = $('select[name="iscatg_idB"]').empty();
                            var d = $('select[name="item_idB"]').empty();
                            $.each(data, function(key, value) {

                                $('select[name="iscatg_idB"]').append(
                                    '<option value="' + value.iscatg_id + '">' +
                                    value.iscatg_name + '</option>');

                            });
                        },

                    });
                } else {

                }
            });

            $('select[name="iscatg_idB"]').on('change', function() {
                var iscatg_id = $(this).val();
                if (iscatg_id) {
                    $.ajax({
                        url: "{{ url('/admin/item-name/ajax/') }}/" + iscatg_id,
                        type: "GET",
                        dataType: "json",
                        success: function(data) {
                            var d = $('select[name="item_idB"]').empty();
                            $.each(data, function(key, value) {

                                $('select[name="item_idB"]').append('<option value="' +
                                    value.item_id + '">' + value.item_deta_name +
                                    '</option>');

                            });
                        },

                    });
                } else {

                }
            });


            // add to cart button submit
            $("#item_stock_cart_form").submit(function(e) {
                e.preventDefault();
                var form = $("#item_stock_cart_form");
                var data = new FormData($(this)[0]); // if same name two form then o index
                var action = form.attr("action");
                document.getElementById("add_to_card_btn").disabled = true;

            
                $.ajax({
                        url: action,
                        method: form.attr("method"),
                        data: data,
                        processData: false,
                        contentType: false,
                        beforeSend: function() {

                        },
                    })
                    .done(function(res) {
                        document.getElementById("add_to_card_btn").disabled = false;
                        if (res.success) {


                            form[0].reset();
                            showSweetAlertMessage('success', 'Successfully Added');
                            var rows = ""

                            var counter = 0;
                            $.each(res.cart_items, function(key, value) {
                                rows +=
                                    `
                                        <tr>
                                            <td>
                                                ${counter += 1}
                                            </td>
                                            <td>
                                                ${value.options.item_detail_name_code}
                                            </td>

                                             <td>
                                                ${value.options.item_detail_name}
                                            </td>
                                            <td>
                                                ${value.options.item_sub_category}
                                            </td>
                                            <td>
                                                ${value.options.item_category}
                                            </td>
                                            <td>
                                                ${value.options.model_no}
                                            </td>

                                            <td>
                                                ${value.options.serial_no}
                                            </td>

                                            <td>
                                                ${value.qty}
                                            </td>
                                            <td style="text-align: center;">
                                                <button style="border: none;" id="${value.rowId}" onclick="cartItemRemove(this.id)">
                                                    <i class="fa fa-trash fa-lg delete_icon"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    `
                            });
                            $('#item_details_cart_table_content_view').html(rows);
                            $('#itemCodeNumber').focus();
                        } else {
                            showSweetAlertMessage('error', 'Selection Error');
                        }

                    })
                    .fail(function(xhr) {
                        showSweetAlertMessage('error', "Operation Failed, Please Try Aggain");
                        document.getElementById("add_to_card_btn").disabled = false;
                    });
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
                    showSweetAlertMessage('error', 'Please Fill This Field First, For Searching Item Details Info!!!');
                } else {
                    showSweetAlertMessage('success', 'Employee Informations are');
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
                    success: function(response) {

                        if (response.itemInfos) {
                            $("select[id='searchItype_id']").empty();
                            $("select[id='searchIcateg_id']").empty();
                            $("select[id='searchIsubCateg_id']").empty();
                            $("select[id='searchItemCode']").empty();

                            $("select[id='searchItype_id']").append('<option value="' + response.itemInfos[0]
                                .itype_id + '" >' + response.itemInfos[0].itype_name + '</option>');
                            $("select[id='searchIcateg_id']").append('<option value="' + response.itemInfos[0]
                                .icatg_id + '">' + response.itemInfos[0].icatg_name + '</option>');
                            $("select[id='searchIsubCateg_id']").append('<option value="' + response.itemInfos[
                                    0]
                                .iscatg_id + '">' + response.itemInfos[0].iscatg_name + '</option>');
                            $("select[id='searchItemCode']").append('<option value="' + response.itemInfos[0]
                                .item_deta_code + '">' + response.itemInfos[0].item_deta_name + '</option>');


                            if(response.item_brands.length > 0){
                                $("select[id='item_brand_idFocus']").empty();
                                for (var i = 0; i < response.item_brands.length; i++) {
                                    $("select[id='item_brand_idFocus']").append('<option value="' + response.item_brands[i].ibrand_id + '">' + response.item_brands[i].item_brand_name + '</option>');
                                }
                            }  

                            $('#itemCodeNumber').val('');
                            //$('#item_brand_idFocus').focus();
                            $('#serial_no').focus();


                        }

                    } // end of success
                }); // end of ajax calling
            }
        }

        // Search Item Name Details With Item Code For Brand
        function searchItemsByItmCodeForBrand() {
            var itemCodeNo = $("#itemCodeNumberForBrand").val();
            if ($("#itemCodeNumberForBrand").val().length === 0) {
                if ($.isEmptyObject(itemCodeNo)) {
                    showSweetAlertMessage('error', 'Please Fill This Field First, For Searching Item Details Info!!!');
                } else {
                    showSweetAlertMessage('success', 'Items Informations are');
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
                    success: function(response) {
                        $("select[id='searchItype_idForBrand']").empty();
                        $("select[id='searchIcateg_idForBrand']").empty();
                        $("select[id='searchIsubCateg_idForBrand']").empty();
                        $("select[id='searchItemCodeForBrand']").empty();

                        $("select[id='searchItype_idForBrand']").append('<option value="' + response.itemInfos[
                            0].itype_id + '">' + response.itemInfos[0].itype_name + '</option>');
                        $("select[id='searchIcateg_idForBrand']").append('<option value="' + response.itemInfos[
                            0].icatg_id + '">' + response.itemInfos[0].icatg_name + '</option>');
                        $("select[id='searchIsubCateg_idForBrand']").append('<option value="' + response
                            .itemInfos[0].iscatg_id + '">' + response.itemInfos[0].iscatg_name + '</option>'
                        );

                        $('#itemBrandNameModal').focus();

                        if (response.total_item > 0) {
                            var d = $('select[id="searchItemCodeForBrand"]').empty();
                            $.each(response.itemInfos, function(key, value) {

                                $('select[id="searchItemCodeForBrand"]').append('<option value="' +
                                    value.item_deta_code + '">' + value.item_deta_name + (value
                                        .item_deta_code) + '</option>');

                            });

                        } else {
                            $("select[id='searchItemCodeForBrand']").append('<option value="' + response
                                .itemInfos[0].item_deta_code + '">' + response.itemInfos[0].item_deta_name +
                                '</option>');
                        }

                    } // end of success
                }); // end of ajax calling
            }
        }

        function itemPurchaseForm() {
            const purchaseButton = document.getElementById('purchase_button');

            $('#item_purchase_form').submit(function(event) {
                event.preventDefault();
                var formData = new FormData(this);
                // JS validation (uncomment and adjust as needed)
                // if (!formData.get('itype_id')) {
                // showSweetAlertMessage('error', 'Please select any item type name');
                // return;
                // }

                // Disable button to prevent multiple submissions
                purchaseButton.disabled = true;
                purchaseButton.innerText = 'Submitting...';

                // Setup ajax
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                // Call ajax
                $.ajax({
                    type: 'POST',
                    url: '/admin/inventory-items/purchase/with-cart-items',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        if (res.status == 200) {
                            showSweetAlertMessage('success', res.message);
                            window.open('/admin/inventory-items/purchase-invoice/' + res
                                .purchase_record_id, '_blank');
                            $('#item_purchase_form').trigger("reset");
                             //For load rest of the cart items
                            window.location.reload();

                        } else {
                            showSweetAlertMessage('error', res.message);
                        }
                        purchaseButton.disabled = false;
                        purchaseButton.innerText = 'PURCHASE';
                    },
                    error: function(status, error) {
                        showSweetAlertMessage('error', 'An error occurred');
                        purchaseButton.disabled = false;
                        purchaseButton.innerText = 'PURCHASE';
                    }
                });
            });
            $('#item_purchase_form').submit();
        }


        function cartItemRemove(rowId) {
            $.ajax({
                type: 'POST',
                url: '/admin/inventory-item/cart-item/remove',
                dataType: 'json',
                data: {
                    rowId: rowId
                },
                success: function(res) {
                    // window.location.reload();
                    console.log(res);
                    if (res.success) {
                        showSweetAlertMessage('success', res.message);
                        cartItemsLoad(); //For load rest of the cart items
                    } else {
                        showSweetAlertMessage('error', 'Something went wrong!');
                    }
                }
            });
        }


        function cartItemsLoad() {
            $.ajax({
                type: 'GET',
                url: '/admin/inventory/cart-items-load',
                dataType: 'json',
                success: function(res) {
                    if (res.success) {

                        var rows = ""
                        $.each(res.cart_items, function(key, value) {
                            rows +=
                                `
                                    <tr>

                                            <td>
                                                ${value.options.item_category}
                                            </td>
                                            <td>
                                                ${value.options.item_sub_category}
                                            </td>
                                            <td>
                                                ${value.options.item_detail_name}
                                            </td>
                                            <td>
                                                ${value.options.model_no}
                                            </td>

                                            <td>
                                                ${value.options.item_brand}
                                            </td>
                                            <td>
                                                ${value.options.serial_no}
                                            </td>
                                            <td>
                                                ${value.options.unit}
                                            </td>
                                            <td>
                                                ${value.qty}
                                            </td>
                                            <td style="text-align: center;">
                                                <button style="border: none;" id="${value.rowId}" onclick="cartItemRemove(this.id)">
                                                    <i class="fa fa-trash fa-lg delete_icon"></i>
                                                </button>
                                            </td>
                                        </tr>
                                `
                        });
                        $('#item_details_cart_table_content_view').html(rows);
                    }
                }
            });
        }


        function addToCardFormValidation(formData) {
            if (!formData.get('itype_id')) {

                showSweetAlertMessage('error', 'Please select any item type name')
                return;
            }
            if (!formData.get('icatg_id')) {

                showSweetAlertMessage('error', 'Please Select Any Category Name')
                return;
            }
            if (!formData.get('iscatg_id')) {

                showSweetAlertMessage('error', 'Please Enter Any Subcategory Name')
                return;
            }
            if (!formData.get('item_deta_code')) {

                showSweetAlertMessage('error', 'Please Select Any Item Name With Code')
                return;
            }

            if (!formData.get('item_brand_id')) {

                showSweetAlertMessage('error', 'Please Select Brand Name')
                return;
            }


            if (!formData.get('quantity')) {

                showSweetAlertMessage('error', 'Please Input Item Quantity')
                return;
            }

            // if (!formData.get('item_company_id')) {

            //     showSweetAlertMessage('error','Please Select Company Name')
            //     return;
            // }

            // if (!formData.get('store_id')) {
            //     showSweetAlertMessage('error','Please Select Item Receive Store')
            //     return;
            // }
            // if (!formData.get('invoice_no')) {

            //     showSweetAlertMessage('error','Please Input Invoice Number')
            //     return;
            // }
            // if (!formData.get('invoice_date')) {

            //     showSweetAlertMessage('error','Please Select Invoice Date')
            //     return;
            // }
            // if (!formData.get('received_date')) {
            //     Toast.fire({
            //         icon: 'error',
            //         title: 'Enter Items Received Date'
            //     });
            //     showSweetAlertMessage('error','Please Input Item Receive Date')
            //     return;
            // }
            // if (!formData.get('purchase_by')) {

            //     showSweetAlertMessage('error','Select Item Purchaser Name')
            //     return;
            // }
            // if (!formData.get('purchase_from')) {

            //     showSweetAlertMessage('error','Please Select Item Purchase From ')
            //     return;
            // }
            // if (!formData.get('chalan_no')) {

            //     showSweetAlertMessage('error','')
            //     return;
            // }
        }

        function purchaseFormValidation(formData) {



            if (!formData.get('store_id')) {
                showSweetAlertMessage('error','Please Select Item Receive Store')
                return;
            }
            if (!formData.get('invoice_no')) {

                showSweetAlertMessage('error','Please Input Invoice Number')
                return;
            }
            if (!formData.get('invoice_date')) {

                showSweetAlertMessage('error','Please Select Invoice Date')
                return;
            }

            // if (!formData.get('chalan_no')) {

            //     showSweetAlertMessage('error','')
            //     return;
            // }
        }

        function showSweetAlertMessage(type, message) {
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
