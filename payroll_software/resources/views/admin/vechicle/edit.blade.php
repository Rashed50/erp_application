@extends('layouts.admin-master')
@section('title')Edit Veh. @endsection
@section('content')
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Update Vechicle Information</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active"> Vehicle Information</li>
            <li class="active"> Edit</li>
        </ol>
    </div>
</div>
<!-- add division -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        @if(Session::has('success'))
        <div class="alert alert-success alertsuccess" role="alert">
            <strong> {{ Session::get('success')}} </strong>
        </div>
        @endif
        @if(Session::has('error'))
        <div class="alert alert-warning alerterror" role="alert">
            <strong> {{ Session::get('error') }} </strong>
        </div>
        @endif
    </div>
    <div class="col-md-2"></div>
</div>

<div class="row">
    <div class="col-md-12">
        <form class="form-horizontal" id="vechicleForm-validation" action="{{ route('update-vechicle-related-info') }}"
            method="post">
            @csrf
            <div class="card">

                <div class="card-body card_form" style="padding-top: 0;">
                    <input type="hidden" name="veh_id" value="{{ $edit->veh_id }}">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row custom_form_group">
                                <label class="col-sm-3 control-label">Company Name:<span
                                        class="req_star">*</span></label>
                                <div  class="col-sm-6">
                                    <select class="form-control" name="company_id" required>
                                        <option value="1">Asloob Internation Contracting Comany</option>
                                        <option value="2">Asloob Bedda Contracting Comany </option>
                                        <option value="3">Bedaa General Contracting Comany</option>
                                        <option value="4">Other Employee</option>
                                    </select>
                                </div>
                                <div class="col-md-3"></div>
                            </div>
                            <div class="form-group row custom_form_group">
                                <label class="control-label col-md-3">Name:<span
                                        class="req_star">*</span></label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="veh_name"
                                        value="{{ $edit->veh_name }}" placeholder="Input Vechicle Name">
                                </div>
                                <div class="col-md-3"></div>
                            </div>
                            <div class="form-group row custom_form_group">
                                <label class="control-label col-md-3">Plate Number:</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="veh_plate_number"
                                        value="{{ $edit->veh_plate_number }}" placeholder="Input Vechicle Plate Number">
                                </div>
                                <div class="col-md-3"></div>
                            </div>
                            <div class="form-group row custom_form_group">
                                <label class="control-label col-md-3">Model Number:</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="veh_model_number"
                                        value="{{ $edit->veh_model_number }}" placeholder="Input Vechicle Model Number">
                                </div>
                                <div class="col-md-3"></div>
                            </div>
                            <div class="form-group row custom_form_group">
                                <label class="control-label col-md-3">Brand Name:</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="veh_brand_name"
                                        value="{{ $edit->veh_brand_name }}" placeholder="Input Vechicle Brand Name">
                                </div>
                                <div class="col-md-3"></div>
                            </div>
                            <div class="form-group row custom_form_group">
                                <label class="control-label col-md-3">Licence Number:</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="veh_licence_no"
                                        value="{{ $edit->veh_licence_no }}" placeholder="Input Vechicle Licence Number">
                                </div>
                                <div class="col-md-3"></div>
                            </div>

                            <div class="form-group row custom_form_group">
                                <label class="col-sm-3 control-label">Vechicle Type:<span
                                        class="req_star">*</span></label>
                                <div  class="col-sm-6">
                                    <select class="form-control" name="veh_type_id" required>
                                        <option value="">Select Vechicle Type</option>
                                        <option value="1" {{ $edit->veh_type_id == 1 ? 'selected':'' }} >Jeep</option>
                                        <option value="2" {{ $edit->veh_type_id == 2 ? 'selected':'' }}>Pickup</option>
                                        <option value="3" {{ $edit->veh_type_id == 3 ? 'selected':'' }}>Bus</option>
                                        <option value="4" {{ $edit->veh_type_id == 4 ? 'selected':'' }}>Truck</option>
                                    </select>
                                </div>
                                <div class="col-md-3"></div>
                            </div>
                            <div class="form-group row custom_form_group">
                                <label class="control-label col-md-3">Insurrance Date:</label>
                                <div class="col-md-6">
                                    <input type="date" class="form-control" name="veh_insurrance_date"
                                        value="{{ $edit->veh_insurrance_date }}" placeholder="Input Purchase Date">
                                </div>
                                <div class="col-md-3"></div>
                            </div>
                            <div class="form-group row custom_form_group">
                                <label class="control-label col-md-3">Remarks:<span
                                        class="req_star">*</span></label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="remarks" value="{{ $edit->remarks }}"
                                        placeholder="Remarks for this vehicle">
                                </div>
                                <div class="col-md-3"></div>
                            </div>

                        </div>
                        <!-- 2nd Column -->
                        <div class="col-md-6">
                            <div class="form-group row custom_form_group">
                                <label class="control-label col-md-3"> Price:<span
                                        class="req_star">*</span></label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="veh_price"
                                        value="{{ $edit->veh_price }}" placeholder="Input Vechicle Price">
                                </div>
                                <div class="col-md-3"></div>
                            </div>
                            <div class="form-group row custom_form_group">
                                <label class="control-label col-md-3">Meter Reading:<span
                                        class="req_star">*</span></label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="veh_present_metar"
                                        value="{{ $edit->veh_present_metar }}"
                                        placeholder="Input Vechicle Present Metar">
                                </div>
                                <div class="col-md-3"></div>
                            </div>
                            <div class="form-group row custom_form_group">
                                <label class="control-label col-md-3">Color:<span
                                        class="req_star">*</span></label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="veh_color"
                                        value="{{ $edit->veh_color }}" placeholder="Input Vechicle Color">
                                </div>
                                <div class="col-md-3"></div>
                            </div>
                            <div class="form-group row custom_form_group">
                                <label class="control-label col-md-3">Purchase Date:</label>
                                <div class="col-md-6">
                                    <input type="date" class="form-control" name="veh_purchase_date"
                                        value="{{ $edit->veh_purchase_date }}" placeholder="Input Purchase Date">
                                </div>
                                <div class="col-md-3"></div>
                            </div>
                            <div class="form-group row custom_form_group{{ $errors->has('veh_ins_expire_date') ? ' has-error' : '' }}">
                                <label class="control-label col-md-3">Insurance Exp Date:<span
                                        class="req_star">*</span></label>
                                <div class="col-md-6">
                                    <input type="date" class="form-control" name="veh_ins_expire_date"
                                         value="{{ $edit->veh_ins_expire_date }}">
                                    @if ($errors->has('veh_ins_expire_date'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('veh_ins_expire_date') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="col-md-3"></div>
                            </div>
                            <div class="form-group row custom_form_group">
                                <label class="control-label col-md-3">Fitness Date<span class="req_star">*</span></label>
                                <div class="col-md-6">
                                    <input type="date" class="form-control" name="veh_ins_renew_date"
                                        value="{{ $edit->veh_ins_renew_date }}" placeholder="Input Purchase Date">
                                </div>
                                <div class="col-md-3"></div>
                            </div>
                            <div class="form-group row custom_form_group">
                                <label class="control-label col-md-3">Registration Exp
                                    Date:<span class="req_star">*</span></label>
                                <div class="col-md-6">
                                    <input type="date" class="form-control" name="veh_reg_expire_date"
                                        value="{{ $edit->veh_reg_expire_date }}" placeholder="Input Purchase Date">
                                </div>
                                <div class="col-md-3"></div>
                            </div>
                            <div class="form-group row custom_form_group">
                                <label class="control-label col-md-3">Registration Renewal
                                    Date:<span class="req_star">*</span></label>
                                <div class="col-md-6">
                                    <input type="date" class="form-control" name="veh_reg_renew_date"
                                        value="{{ $edit->veh_reg_renew_date }}" placeholder="Input Purchase Date">
                                </div>
                                <div class="col-md-3"></div>
                            </div>

                            <div class="form-group row custom_form_group">
                                <label class="control-label col-md-3">Acitve Status:</label>
                                <div class="col-md-6">
                                      <select class="form-select"  name="active_status" id="active_status">
                                        <option value="1" {{$edit->status == 1 ? 'selected' : '' }} >Active</option>
                                        <option value="0" {{$edit->status == 0 ? 'selected' : '' }} >Inactive</option>
                                      </select>
                                </div>
                            </div>

                        </div>
                        <div class="card-footer card_footer_button text-center">
                            <button type="submit" class="btn btn-primary waves-effect">UPDATE</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Photo Update -->
<div class="row">
    <div class="col-lg-12">
        <form class="form-horizontal" id="employee-info-form" method="post" enctype="multipart/form-data"
            action="{{ route('update-vechicle-related-image') }}">
            @csrf
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-8">
                            <h3 class="card-title card_top_title"><i class="fab fa-gg-circle"></i> Update Vehicle
                                Related File </h3>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
                <div class="card-body card_form row">
                    <div class="col-md-2"></div>
                    <div class="col-md-8">
                        <input type="hidden" name="veh_id" value="{{ $edit->veh_id }}">
                        <input type="hidden" name="old_insurance_cert" value="{{ $edit->veh_ins_certificate }}">
                        <input type="hidden" name="old_registration_cert" value="{{ $edit->veh_reg_certificate }}">
                        <input type="hidden" name="old_vehicle_photo" value="{{ $edit->veh_photo }}">

                        <!-- Insurance Certificate -->
                        <div class="form-group row custom_form_group">
                            <label class="col-sm-3 control-label">Insurance Certificate:<span
                                            class="req_star">*</span></label>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <span class="input-group-btn">
                                        <span class="btn btn-default btn-file btnu_browse">
                                            Browse… <input type="file" name="veh_ins_certificate" id="imgInp4">
                                        </span>
                                    </span>
                                    <input type="text" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <img id='img-upload4' class="upload_image" />
                            </div>
                        </div>

                        <!-- Registration Certificate -->
                        <div class="form-group row custom_form_group">
                            <label class="col-sm-3 control-label">Registration Certificate:<span
                                            class="req_star">*</span></label>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <span class="input-group-btn">
                                        <span class="btn btn-default btn-file btnu_browse">
                                            Browse… <input type="file" name="veh_reg_certificate" id="imgInp2">
                                        </span>
                                    </span>
                                    <input type="text" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <img id='img-upload2' class="upload_image" />
                            </div>
                        </div>

                        <!-- Vehicle photo -->
                        <div class="form-group row custom_form_group">
                            <label class="col-sm-3 control-label">Vehicle Photo:</label>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <span class="input-group-btn">
                                        <span class="btn btn-default btn-file btnu_browse">
                                            Browse… <input type="file" name="veh_photo" id="imgInp3">
                                        </span>
                                    </span>
                                    <input type="text" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <img id='img-upload3' class="upload_image" />
                            </div>
                        </div>
                        <div class="card-footer card_footer_button text-center">
                            <button type="submit" class="btn btn-primary waves-effect">UPDATE</button>
                        </div>
                    </div>
                    <div class="col-md-2"></div>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- script area -->
<script type="text/javascript">
    /* form validation */
    $(document).ready(function () {
        $("#vechicleForm-validation").validate({
            /* form tag off  */
            // submitHandler: function(form) { return false; },
            /* form tag off  */
            rules: {
                veh_name: {
                    required: true,
                },
                veh_purchase_date: {
                    required: true,
                },
                veh_present_metar: {
                    required: true,
                    number: true,
                    maxlength: 9,
                },
                veh_price: {
                    required: true,
                    number: true,
                    maxlength: 15,
                },
                veh_brand_name: {
                    required: true,
                },
            },

            messages: {
                veh_name: {
                    required: "You Must Be Input This Field!",
                },
                veh_color: {
                    required: "You Must Be Input This Field!",
                },
                veh_purchase_date: {
                    required: "You Must Be Select This Field!",
                },
                veh_price: {
                    required: "Please Input This Field!",
                    number: "You Must Be Input Number!",
                    max: "You Must Be Input Maximum Length 15!",
                },
                veh_present_metar: {
                    required: "Please Input This Field!",
                    number: "You Must Be Input Number!",
                    max: "You Must Be Input Maximum Length 9!",
                },
                veh_brand_name:{
                    required: "Please Input This Field!",
                }
            },
        });
    });
</script>


@endsection
