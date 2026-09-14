@extends('layouts.admin-master')
@section('title') Company Main Contractor Information @endsection
@section('internal-css')
<style media="screen">
    .approve_button {
        background: #2B4049;
        color: #fff;
        font-size: 12px;
        padding: 3px 6px;
        border-radius: 5px;
    }

    .approve_button:hover {
        color: #fff;
    }

    .switch {
        margin-left: 10px;
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 5px;
        right: -5px;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    input:checked+.slider {
        background-color: #2196F3;
    }

    input:focus+.slider {
        box-shadow: 0 0 1px #2196F3;
    }

    input:checked+.slider:before {
        -webkit-transform: translateX(26px);
        -ms-transform: translateX(26px);
        transform: translateX(26px);
    }

    /* Rounded sliders */
    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }
</style>
@endsection
@section('content')
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Company Main Contractor Informations</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active"> Main Contractor </li>
        </ol>
    </div>
</div>
<br>
<!-- add division -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        @if(Session::has('success'))
        <div class="alert alert-success alertsuccess" role="alert">
            <strong>{{Session::get('success')}}</strong>
        </div>
        @endif
        @if(Session::has('error'))
        <div class="alert alert-warning alerterror" role="alert">
            <strong>{{Session::get('error')}}</strong>
        </div>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <form class="form-horizontal" id="mainContractorForm" action="{{ route('insert-main-contractor-info') }}"
            method="post">
            @csrf
            <div class="card">
                <br>
                <div class="card-body card_form" style="padding-top: 0;">

                    <div class="form-group row custom_form_group{{ $errors->has('mc_name_en') ? ' has-error' : '' }}">
                        <label class="control-label col-md-4"> Name (English):<span class="req_star">*</span></label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" placeholder="Main Contractor English Name"
                                id="mc_name_en" name="mc_name_en" value="{{old('mc_name_en')}}" required>
                            @if ($errors->has('mc_name_en'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('mc_name_en') }}</strong>
                            </span>
                            @endif
                        </div>
                        <div class="col-md-2"></div>
                    </div>
                    <div class="form-group row custom_form_group{{ $errors->has('mc_name_arb') ? ' has-error' : '' }}">
                        <label class="control-label col-md-4"> Name (Arabic):<span class="req_star">*</span></label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" placeholder="Main Contractor Arabic Name"
                                id="mc_name_arb" name="mc_name_arb" value="{{old('mc_name_arb')}}" required>
                            @if ($errors->has('mc_name_arb'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('mc_name_arb') }}</strong>
                            </span>
                            @endif
                        </div>
                        <div class="col-md-2"></div>
                    </div>
                    <div class="form-group row custom_form_group{{ $errors->has('mc_phone_no') ? ' has-error' : '' }}">
                        <label class="control-label col-md-4">Phone Number:</label>
                        <div class="col-md-6">
                            <input type="number" class="form-control" placeholder="Phone Number" id="mc_phone_no"
                                name="mc_phone_no" value="{{old('mc_phone_no')}}">
                            @if ($errors->has('mc_phone_no'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('mc_phone_no') }}</strong>
                            </span>
                            @endif
                        </div>
                        <div class="col-md-2"></div>
                    </div>
                    <div class="form-group row custom_form_group{{ $errors->has('mc_vat_no') ? ' has-error' : '' }}">
                        <label class="control-label col-md-4">VAT No:<span class="req_star">*</span></label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" placeholder="VAT NO" id="mc_vat_no" name="mc_vat_no"
                                value="{{old('mc_vat_no')}}" required>
                            @if ($errors->has('mc_vat_no'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('mc_vat_no') }}</strong>
                            </span>
                            @endif
                        </div>
                        <div class="col-md-2"></div>
                    </div>
                    <div class="form-group row custom_form_group{{ $errors->has('mc_email') ? ' has-error' : '' }}">
                        <label class="control-label col-md-4">Email:</label>
                        <div class="col-md-6">
                            <input type="email" class="form-control" placeholder="Email Address" id="mc_email"
                                name="mc_email" value="{{old('mc_email')}}">
                            @if ($errors->has('mc_email'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('mc_email') }}</strong>
                            </span>
                            @endif
                        </div>
                        <div class="col-md-2"></div>
                    </div>
                    <div class="form-group row custom_form_group{{ $errors->has('mc_address') ? ' has-error' : '' }}">
                        <label class="control-label col-md-4">Address:</label>
                        <div class="col-md-6">
                            <textarea name="mc_address" class="form-control"
                                placeholder="Contractor Address Informations">{{old('mc_address')}}</textarea>
                            @if ($errors->has('mc_address'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('mc_address') }}</strong>
                            </span>
                            @endif
                        </div>
                        <div class="col-md-2"></div>
                    </div>
                </div>
                <div class="card-footer card_footer_button text-center">
                    <button type="submit" class="btn btn-primary waves-effect">CREATE</button>
                </div>
            </div>
        </form>
    </div>
    <div class="col-md-2"></div>
</div>

<!-- Main contractor list -->
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-8">
                        <h3 class="card-title card_top_title"><i class="fab fa-gg-circle"></i> Company Main Contractor
                            List</h3>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table id="alltableinfo" class="table table-bordered custom_table mb-0">
                                <thead>
                                    <tr>
                                        <th>SN</th>
                                        <th>Contractor Name</th>
                                        <th>Phone No</th>
                                        <th>Email Address</th>
                                        <th>Vat Amount</th>
                                        <th>Status</th>
                                        <th>Manage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($all as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->en_name }}</td>
                                        <td>{{ $item->phone_no }}</td>
                                        <td>{{ $item->mc_email }}</td>
                                        <td>{{ $item->vat_no }}</td>
                                        <td>
                                            @if ($item->mc_status == 1)
                                            <span class="badge badge-pill badge-success">Active</span>
                                            @else
                                            <span class="badge badge-pill badge-danger">In Active</span>
                                            @endif
                                        </td>
                                        <td>

                                            <a href="#" title="Edit" class="approve_button" data-toggle="modal"
                                                data-target="#demoModalEdit{{ $item->mc_auto_id }}">Edit</a>

                                        </td>


                                        <!-- Edit Form Modal Example Start -->
                                        <div class="modal fade" id="demoModalEdit{{ $item->mc_auto_id }}"
                                            value="{{ $item->mc_auto_id }}" tabindex="-1" role="dialog" aria-
                                            labelledby="demoModalLabel" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="card-title text-info">Main Contractor Informations Update</h5>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close" id="closeModal">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">

                                                            <form class="form-horizontal" id="mainContractorInfoModal"
                                                                method="post" action="{{ route('update-main-contractor-info') }}">
                                                                @csrf
                                                                <div class="card">
                                                                    <div class="card-body card_form">
                                                                        <input type="hidden" name="mainContractorID"
                                                                            value="{{ $item->mc_auto_id }}">

                                                                        <div
                                                                            class="form-group row custom_form_group{{ $errors->has('mc_name_enM') ? ' has-error' : '' }}">
                                                                            <label class="control-label col-md-4"> Name
                                                                                (English):<span
                                                                                    class="req_star">*</span></label>
                                                                            <div class="col-md-8">
                                                                                <input type="text" class="form-control"
                                                                                    placeholder="Main Contractor English Name"
                                                                                    id="mc_name_enModal"
                                                                                    name="mc_name_enM"
                                                                                    value="{{ $item->en_name }}"
                                                                                    required>
                                                                                @if ($errors->has('mc_name_enM'))
                                                                                <span class="invalid-feedback"
                                                                                    role="alert">
                                                                                    <strong>{{
                                                                                        $errors->first('mc_name_enM')
                                                                                        }}</strong>
                                                                                </span>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                        <div
                                                                            class="form-group row custom_form_group{{ $errors->has('mc_name_arbM') ? ' has-error' : '' }}">
                                                                            <label class="control-label col-md-4"> Name
                                                                                (Arabic):<span
                                                                                    class="req_star">*</span></label>
                                                                            <div class="col-md-8">
                                                                                <input type="text" class="form-control"
                                                                                    placeholder="Main Contractor Arabic Name"
                                                                                    id="mc_name_arb" name="mc_name_arbM"
                                                                                    value="{{ $item->ar_name }}"
                                                                                    required>
                                                                                @if ($errors->has('mc_name_arbM'))
                                                                                <span class="invalid-feedback"
                                                                                    role="alert">
                                                                                    <strong>{{
                                                                                        $errors->first('mc_name_arbM')
                                                                                        }}</strong>
                                                                                </span>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                        <div
                                                                            class="form-group row custom_form_group{{ $errors->has('mc_phone_noM') ? ' has-error' : '' }}">
                                                                            <label class="control-label col-md-4">Phone
                                                                                Number:</label>
                                                                            <div class="col-md-8">
                                                                                <input type="number"
                                                                                    class="form-control"
                                                                                    placeholder="Phone Number"
                                                                                    id="mc_phone_noM"
                                                                                    name="mc_phone_noM"
                                                                                    value="{{ $item->phone_no }}">
                                                                                @if ($errors->has('mc_phone_noM'))
                                                                                <span class="invalid-feedback"
                                                                                    role="alert">
                                                                                    <strong>{{
                                                                                        $errors->first('mc_phone_noM')
                                                                                        }}</strong>
                                                                                </span>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                        <div
                                                                            class="form-group row custom_form_group{{ $errors->has('mc_vat_noM') ? ' has-error' : '' }}">
                                                                            <label class="control-label col-md-4">VAT
                                                                                No:<span
                                                                                    class="req_star">*</span></label>
                                                                            <div class="col-md-8">
                                                                                <input type="text" class="form-control"
                                                                                    placeholder="VAT NO" id="mc_vat_noM"
                                                                                    name="mc_vat_noM"
                                                                                    value="{{ $item->vat_no }}"
                                                                                    required>
                                                                                @if ($errors->has('mc_vat_noM'))
                                                                                <span class="invalid-feedback"
                                                                                    role="alert">
                                                                                    <strong>{{
                                                                                        $errors->first('mc_vat_noM')
                                                                                        }}</strong>
                                                                                </span>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                        <div
                                                                            class="form-group row custom_form_group{{ $errors->has('mc_emailM') ? ' has-error' : '' }}">
                                                                            <label
                                                                                class="control-label col-md-4">Email:</label>
                                                                            <div class="col-md-8">
                                                                                <input type="email" class="form-control"
                                                                                    placeholder="Email Address"
                                                                                    id="mc_emailM" name="mc_emailM"
                                                                                    value="{{ $item->mc_email }}">
                                                                                @if ($errors->has('mc_emailM'))
                                                                                <span class="invalid-feedback"
                                                                                    role="alert">
                                                                                    <strong>{{
                                                                                        $errors->first('mc_emailM')
                                                                                        }}</strong>
                                                                                </span>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                        <div
                                                                            class="form-group row custom_form_group{{ $errors->has('mc_addressM') ? ' has-error' : '' }}">
                                                                            <label
                                                                                class="control-label col-md-4">Address:</label>
                                                                            <div class="col-md-8">
                                                                                <textarea name="mc_addressM"
                                                                                    class="form-control"
                                                                                    placeholder="Contractor Address Informations">{{ $item->mc_address }}</textarea>
                                                                                @if ($errors->has('mc_addressM'))
                                                                                <span class="invalid-feedback"
                                                                                    role="alert">
                                                                                    <strong>{{
                                                                                        $errors->first('mc_addressM')
                                                                                        }}</strong>
                                                                                </span>
                                                                                @endif
                                                                            </div>
                                                                        </div>

                                                                        <div class="form-group row custom_form_group">
                                                                            <label class="control-label col-sm-4">Active
                                                                                / InActive: </label>
                                                                            <label class="switch">
                                                                                <input type="checkbox"
                                                                                    name="lock_checkbox"
                                                                                    id="lock_checkbox" {{
                                                                                    $item->mc_status == 1 ?
                                                                                'checked' : '' }}>
                                                                                <span class="slider round"></span>
                                                                            </label>
                                                                        </div>
                                                                        <br><br>

                                                                        <div class="text-center">
                                                                            <button type="submit"
                                                                                class="btn btn-primary">Update
                                                                                Info</button>
                                                                        </div>
                                                                    </div>

                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        <!-- Edit Form Modal Example End-->
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

<script type="text/javascript">
    $(document).ready(function () {
        $("#mainContractorForm").validate({

            rules: {
                mc_name_en: {
                    required: true,
                },
                mc_name_arb: {
                    required: true,
                },
                mc_vat_no: {
                    required: true,
                },
            },

            messages: {
                mc_name_en: {
                    required: "You Must Be Input This Field!",
                },
                mc_name_arb: {
                    required: "You Must Be Input This Field!",
                },
                mc_vat_no: {
                    required: "You Must Be Input This Field!",
                },
            },
        });


        // Make Contractor Name Upper Case
        $('#mc_name_en').keyup(function () {
            this.value = this.value.toLocaleUpperCase();
        });

        // Make Contractor Name Upper Case From modal
        $('#mc_name_enModal').keyup(function () {
            this.value = this.value.toLocaleUpperCase();
        });
    });
</script>
@endsection
