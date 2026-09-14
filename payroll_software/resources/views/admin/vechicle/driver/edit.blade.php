@extends('layouts.admin-master')
@section('title') Vechicle @endsection
@section('content')
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Driver Informations Edit</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active"> Driver Information Add</li>
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
        <form class="form-horizontal" id="vechicleForm-validation" action="{{ route('update-driver-info') }}"
            method="post">
            @csrf
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-8">
                            <h3 class="card-title card_top_title"><i class="fab fa-gg-circle"></i> Update Driver
                                Information</h3>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
                <div class="card-body card_form" style="padding-top: 0;">

                    <input type="hidden" name="dri_auto_id" value="{{ $edit->dri_auto_id }}">

                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-8">
                            <div class="form-group row custom_form_group" id="searchEmployeeId">
                                <label class="control-label col-md-3">Employee ID:<span
                                        class="req_star">*</span></label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control typeahead"
                                        placeholder="Input Driver Employee ID" name="dri_empId"
                                        value="{{ $edit->dri_emp_id }}" id="emp_id_search" onkeyup="empSearch()"
                                        onfocus="showResult()" onblur="hideResult()">

                                    <div id="showEmpId"></div>
                                    <span id="error_show" class="d-none" style="color: red"></span>
                                </div>
                                <div class="col-md-3"></div>
                            </div>
                            {{-- Search Employee IQama No --}}
                            <div id="searchIqamaNo">
                                <div class="form-group row custom_form_group ">
                                    <label class="control-label col-md-3">IQama No:</label>
                                    <div class="col-md-6">
                                        <input type="text" id="iqamaNoSearch" class="form-control typeahead"
                                            placeholder="Input Driver IQama No" name="dri_iqamaNo"
                                            value="{{ $edit->dri_iqama_no }}">
                                        <span id="error_show" class="d-none" style="color: red"></span>
                                    </div>
                                    <div class="col-md-3"></div>
                                </div>
                            </div>
                            <div class="form-group row custom_form_group">
                                <label class="control-label col-md-3">Name:<span
                                        class="req_star">*</span></label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="dri_name"
                                        value="{{ $edit->dri_name }}" placeholder="Please Enter Driver Name">
                                </div>
                                <div class="col-md-3"></div>
                            </div>
                            <div class="form-group row custom_form_group">
                                <label class="control-label col-md-3">Present Address:</label>
                                <div class="col-md-6">
                                    <textarea name="present_address" class="form-control" value="{{old('dri_address')}}"
                                        placeholder="Please Enter Driver Address Here">{{ $edit->dri_address }}</textarea>
                                </div>
                                <div class="col-md-3"></div>
                            </div>
                            <div class="card-footer card_footer_button text-center">
                                <button type="submit" class="btn btn-primary waves-effect">UPDATE</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2"></div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Photo Update -->
<div class="row">
    <div class="col-lg-12">
        <form class="form-horizontal" id="employee-info-form" method="post" enctype="multipart/form-data"
            action="{{ route('update-driver-images') }}">
            @csrf
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-8">
                            <h3 class="card-title card_top_title"><i class="fab fa-gg-circle"></i> Update Driver Related
                                File </h3>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
                <div class="card-body row card_form">
                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-8">
                            <input type="hidden" name="dri_auto_id" value="{{ $edit->dri_auto_id }}">
                            <input type="hidden" name="old_dri_iqama_certificate"
                                value="{{ $edit->dri_iqama_certificate }}">
                            <input type="hidden" name="old_dri_license_certificate"
                                value="{{ $edit->dri_license_certificate }}">
                            <input type="hidden" name="old_dri_ins_certificate"
                                value="{{ $edit->dri_ins_certificate }}">

                            <!-- Iqama Certificate -->
                            <div class="form-group row custom_form_group">
                                <label class="col-sm-3 control-label">Iqama Certificate:<span
                                        class="req_star">*</span></label>
                                <div class="col-sm-6">
                                    <div class="input-group">
                                        <span class="input-group-btn">
                                            <span class="btn btn-default btn-file btnu_browse">
                                                Browse… <input type="file" name="dri_iqama_certificate" id="imgInp4">
                                            </span>
                                        </span>
                                        <input type="text" class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <img id='img-upload4' class="upload_image" />
                                </div>
                            </div>

                            <!-- Driving License -->
                            <div class="form-group row custom_form_group">
                                <label class="col-sm-3 control-label">Driving License:<span
                                        class="req_star">*</span></label>
                                <div class="col-sm-6">
                                    <div class="input-group">
                                        <span class="input-group-btn">
                                            <span class="btn btn-default btn-file btnu_browse">
                                                Browse… <input type="file" name="dri_license_certificate" id="imgInp2">
                                            </span>
                                        </span>
                                        <input type="text" class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <img id='img-upload2' class="upload_image" />
                                </div>
                            </div>

                            <!-- Insurance Certificate -->
                            <!--<div class="form-group row custom_form_group">-->
                            <!--    <label class="col-sm-3 control-label">Insurance Certificate:</label>-->
                            <!--    <div class="col-sm-6">-->
                            <!--        <div class="input-group">-->
                            <!--            <span class="input-group-btn">-->
                            <!--                <span class="btn btn-default btn-file btnu_browse">-->
                            <!--                    Browse… <input type="file" name="dri_ins_certificate" id="imgInp3">-->
                            <!--                </span>-->
                            <!--            </span>-->
                            <!--            <input type="text" class="form-control" readonly>-->
                            <!--        </div>-->
                            <!--    </div>-->
                            <!--    <div class="col-sm-3">-->
                            <!--        <img id='img-upload3' class="upload_image" />-->
                            <!--    </div>-->
                            <!--</div>-->
                            <div class="card-footer card_footer_button text-center">
                                <button type="submit" class="btn btn-primary waves-effect">UPDATE</button>
                            </div>
                        </div>
                        <div class="col-md-2"></div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- script area -->
<script type="text/javascript">
    /* form validation */
    $(document).ready(function () {
        $("#driverForm-validation").validate({
            /* form tag off  */
            // submitHandler: function(form) { return false; },
            /* form tag off  */
            rules: {
                dri_empId: {
                    required: true,
                },
                dri_name: {
                    required: true,
                },
                dri_license_certificate: {
                    required: true,
                },
                dri_ins_certificate: {
                    required: true,
                },
            },

            messages: {
                dri_empId: {
                    required: "You Must Be Input This Field!",
                },
                dri_name: {
                    required: "You Must Be Input This Field!",
                },
                dri_license_certificate: {
                    required: "You Must Be Provide Your Driving License Here!",
                },
                dri_ins_certificate: {
                    required: "You Must Be Provide Your Insurance Certificate Here!",
                },
            },
        });
    });
</script>


@endsection
