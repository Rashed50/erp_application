@extends('layouts.admin-master')
@section('title') Bank Informations @endsection
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
        <h4 class="pull-left page-title bread_title">Company Bank Informations</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active"> Bank Informations Add</li>
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
    <div class="col-md-2"></div>
    <div class="col-md-8">

        <form class="form-horizontal" id="employee-info-form" method="post" action="{{ route('bank-infos.update') }}">
            @csrf
            <div class="card">
                <div class="card-body card_form">
                    <input type="hidden" name="bank_accont_id" value="{{ $edit->id }}">

                    <div
                        class="form-group row custom_form_group{{ $errors->has('sub_company_id') ? ' has-error' : '' }}">
                        <label class="col-sm-4 control-label">Sub
                            Company:<span class="req_star">*</span></label>


                        <div class="col-sm-8">
                            <select class="form-control" name="sub_company_id" required>
                                <option value=""> Select Sub Company
                                </option>
                                @foreach($subCompany as $subCompany)
                                <option value="{{ $subCompany->sb_comp_id }}" {{ $subCompany->sb_comp_id == $edit->sub_company_id ? 'selected' : '' }}>
                                    {{ $subCompany->sb_comp_name }}
                                </option>
                                @endforeach
                            </select>
                            @if ($errors->has('sub_company_id'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{
                                    $errors->first('sub_company_id')
                                    }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row custom_form_group{{ $errors->has('bank_name') ? ' has-error' : '' }}">
                        <label class="control-label col-sm-4">Bank
                            Name:</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="bank_name" id="updateBankNameUpper"
                                value="{{ $edit->bank_name }}" placeholder="Bank Name Here" required autofocus>
                            @if ($errors->has('bank_name'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{
                                    $errors->first('bank_name')
                                    }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div
                        class="form-group row custom_form_group{{ $errors->has('bank_accnt_type') ? ' has-error' : '' }}">
                        <label class="control-label col-sm-4">Account
                            Type:</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="bank_accnt_type" id="updateBankAccntType"
                                value="{{ $edit->account_type }}" placeholder="Bank Account Type Here" required>
                            @if ($errors->has('bank_accnt_type'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{
                                    $errors->first('bank_accnt_type')
                                    }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div
                        class="form-group row custom_form_group{{ $errors->has('bank_accnt_no') ? ' has-error' : '' }}">
                        <label class="control-label col-sm-4">Account
                            No:</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="bank_accnt_no" id=""
                                value="{{ $edit->account_no }}" placeholder="Bank Account Number Here" required>
                            @if ($errors->has('bank_accnt_no'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{
                                    $errors->first('bank_accnt_no')
                                    }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    
                        <div   class="form-group row custom_form_group{{ $errors->has('bank_accnt_no') ? ' has-error' : '' }}">
                                <label class="control-label col-sm-4">IBAN No:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name="ibank_no" id=""
                                    value="{{ $edit->ibank_no }}" placeholder="IBAN Number"
                                        required>
                                    @if ($errors->has('ibank_no'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('ibank_no') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                    
                        <div class="form-group row custom_form_group{{ $errors->has('bank_name') ? ' has-error' : '' }}">
                                <label class="control-label col-sm-4">Beneficiary Name:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name="beneficiary_name" id="beneficiary_name"
                                        value="{{ $edit->beneficiary_name }}" placeholder="Enter Beneficiary Name Here" required
                                        autofocus>
                                    @if ($errors->has('beneficiary_name'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('beneficiary_name') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                    <div class="form-group row custom_form_group">
                        <label class="control-label col-sm-4">Active
                            / InActive: </label>
                        <label class="switch">
                            <input type="checkbox" name="lock_checkbox" id="lock_checkbox" {{ $edit->account_status == 1
                            ?
                            'checked' : '' }}>
                            <span class="slider round"></span>
                        </label>
                    </div>

                    <br><br>

                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">Update
                            Info</button>
                    </div>
                </div>

            </div>
        </form>
    </div>
    <div class="col-md-2"></div>
</div>

<script type="text/javascript">

    $(document).ready(function () {

        // Make Company Bank Name Upper Case from modal
        $('#updateBankNameUpper').keyup(function () {
            this.value = this.value.toLocaleUpperCase();
        });
        $('#updateBankAccntType').keyup(function () {
            this.value = this.value.toLocaleUpperCase();
        });
        
          $('#beneficiary_name').keyup(function () {
            this.value = this.value.toLocaleUpperCase();
        });
    });

</script>
@endsection
