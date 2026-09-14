@extends('layouts.admin-master')
@section('title')
    Chart Of Account
@endsection
@section('internal-css')
    <style>
        /* Chrome, Safari, Edge, Opera */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Firefox */
        input[type="number"] {
            -moz-appearance: textfield;
        }
    </style>
@endsection
@section('content')
    <div class="row bread_part">
        <div class="col-sm-12 bread_col">
            <h4 class="pull-left page-title bread_title">Company Chart Of Account </h4>
            <ol class="breadcrumb pull-right">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="active"></li>
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

    <form id="company_chart_of_account" action="#" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div
                        class="form-group row mt-4 custom_form_group{{ $errors->has('acct_type_id') ? ' has-error' : '' }}">
                        <label class="control-label col-sm-4" for="acct_type_id">Account Type: <span
                                class="req_star">*</span></label>
                        <div class="col-sm-6">
                            <select id="acct_type_id" class="form-select" name="acct_type_id" id="acct_type_id" required>
                                <option value="">Select Account Type</option>
                                @foreach ($account_types as $item)
                                    <option value="{{ $item->acct_type_id }}">{{ $item->acct_type_name }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($errors->has('acct_type_id'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('acct_type_id') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="col-md-2"></div>
                    </div>

                    <div
                        class="form-group row custom_form_group{{ $errors->has('chart_of_acct_name') ? ' has-error' : '' }}">
                        <label class="control-label col-sm-4">Account Name:<span class="req_star">*</span></label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="chart_of_acct_name"
                                value="{{ old('chart_of_acct_name') }}" placeholder="Account Holder Name" required>
                            @if ($errors->has('chart_of_acct_name'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('chart_of_acct_name') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="col-md-2"></div>
                    </div>

                    <div
                        class="form-group row custom_form_group{{ $errors->has('chart_of_acct_number') ? ' has-error' : '' }}">
                        <label class="control-label col-sm-4">Account Number:<span class="req_star">*</span></label>
                        <div class="col-sm-6">
                            <input type="number" class="form-control" name="chart_of_acct_number"
                                value="{{ old('chart_of_acct_number') }}" placeholder="Chart Of Account Number" required>
                            @if ($errors->has('chart_of_acct_number'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('chart_of_acct_number') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="col-md-2"></div>
                    </div>


                    <div class="form-group row custom_form_group{{ $errors->has('account_id') ? ' has-error' : '' }}">
                        <label class="control-label col-sm-4" for="account_id">Sub Account Of: </label>
                        <div class="col-sm-6">
                            <select id="account_id" class="form-select" name="account_id" id="account_id" >
                                <option selected value="0">Select Mother Account</option>
                                @foreach ($mother_accounts as $ma)
                                <option value="{{$ma->chart_of_acct_id}}"> {{$ma->chart_of_acct_name}}-{{$ma->chart_of_acct_number}}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('account_id'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('account_id') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="col-md-2"></div>
                    </div>

                    <div class="form-group row custom_form_group{{ $errors->has('acct_balance') ? ' has-error' : '' }}">
                        <label class="control-label col-sm-4">Opening Balance:</label>
                        <div class="col-sm-6">
                            <input type="number" class="form-control" name="acct_balance"
                                value="{{ old('acct_balance') }}" placeholder="Account Balance">
                            @if ($errors->has('acct_balance'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('acct_balance') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="col-md-2"></div>
                    </div>

                    <div class="form-group row custom_form_group{{ $errors->has('opening_date') ? ' has-error' : '' }}">
                        <label class="control-label col-sm-4">Opening Date: <span class="req_star">*</span></label>
                        <div class="col-sm-6">
                            <input type="date" class="form-control" name="opening_date" value="{{ date('Y-m-d') }}"
                                required>
                            @if ($errors->has('opening_date'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('opening_date') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="col-md-2"></div>
                    </div>


                    <div class="form-group row custom_form_group">
                        <label class="col-sm-4 control-label"></label>
                        <div class="col-sm-6">
                            <input type="checkbox" id="active_status" name="active_status" value="1">
                            <label class="col-sm-6 control-label" style="text-align:left;">Active</label><br>
                        </div>
                        <div class="col-md-2"></div>
                    </div>

                </div>
            </div>
            <div class="row">
                <div class="card-footer card_footer_button text-center">
                    <button id="account-info-submit-button" class="btn btn-primary waves-effect">SAVE</button>
                </div>
            </div>


        </div>
    </form>


    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    Chart Of Account Informations
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table id="alltableinfo" class="table table-bordered custom_table mb-0">
                                    <thead>
                                        <tr>
                                            <th>Type</th>
                                            <th>Account Name</th>
                                            <th>Number</th>
                                            <th>Balance</th>
                                            <th>Opening Date</th>
                                            <th>Predefined</th>
                                            <th>Transaction Status</th>
                                            <th>IsClosed</th>
                                            <th>Created By</th>
                                            <th>Manage</th>
                                        </tr>
                                    </thead>
                                    <tbody id="chart_of_account_table_content_view">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>



    <!-- Modal For Edit Account Information Start -->
    <div class="modal fade" id="chartOfAccountEditModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Chart Of Account Information Edit</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <form class="form-horizontal" id="chartOfAccountEditForm" method="post" action="#">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card">

                                        <input type="hidden" name="chart_of_acct_id" id="chart_of_acct_id">
                                        <div
                                            class="form-group row mt-4 custom_form_group{{ $errors->has('acct_type_id') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-4" for="acct_type_id">Account Type: <span
                                                    class="req_star">*</span></label>
                                            <div class="col-sm-6">
                                                <select id="acct_type_id" class="form-select" name="acct_type_id" required>
                                                    <option value="">Select Account Type</option>
                                                    @foreach ($account_types as $item)
                                                        <option value="{{ $item->acct_type_id }}">{{ $item->acct_type_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @if ($errors->has('acct_type_id'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('acct_type_id') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="col-md-2"></div>
                                        </div>

                                        <div
                                            class="form-group row custom_form_group{{ $errors->has('chart_of_acct_name') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-4">Account Name:<span class="req_star">*</span></label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control" name="chart_of_acct_name" id="chart_of_acct_name"  value="{{ old('chart_of_acct_name') }}" placeholder="Account Holder Name" required>
                                                @if ($errors->has('chart_of_acct_name'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('chart_of_acct_name') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="col-md-2"></div>
                                        </div>

                                        <div
                                            class="form-group row custom_form_group{{ $errors->has('chart_of_acct_number') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-4">Account Number:<span class="req_star">*</span></label>
                                            <div class="col-sm-6">
                                                <input type="number" class="form-control" name="chart_of_acct_number" id="chart_of_acct_number" value="{{ old('chart_of_acct_number') }}" placeholder="Chart Of Account Number" required>
                                                @if ($errors->has('chart_of_acct_number'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('chart_of_acct_number') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="col-md-2"></div>
                                        </div>


                                        <div class="form-group row custom_form_group{{ $errors->has('account_id') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-4" for="account_id">Sub Account Of: </label>
                                            <div class="col-sm-6">
                                                <select id="account_id" class="form-select" name="account_id">
                                                    <option selected value="1">Customer</option>
                                                    <option value="2">Employee</option>
                                                    <option value="3">Owner</option>
                                                </select>
                                                @if ($errors->has('account_id'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('account_id') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="col-md-2"></div>
                                        </div>

                                        <div class="form-group row custom_form_group{{ $errors->has('acct_balance') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-4">Opening Balance:</label>
                                            <div class="col-sm-6">
                                                <input type="number" class="form-control" name="acct_balance" id="acct_balance" value="{{ old('acct_balance') }}" placeholder="Account Balance">
                                                @if ($errors->has('acct_balance'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('acct_balance') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="col-md-2"></div>
                                        </div>

                                        <div class="form-group row custom_form_group{{ $errors->has('opening_date') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-4">Opening Date: <span class="req_star">*</span></label>
                                            <div class="col-sm-6">
                                                <input type="date" class="form-control" name="opening_date" id="opening_date" value="{{ date('Y-m-d') }}"
                                                    required>
                                                @if ($errors->has('opening_date'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('opening_date') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="col-md-2"></div>
                                        </div>


                                        <div class="form-group row custom_form_group">
                                            <label class="col-sm-4 control-label"></label>
                                            <div class="col-sm-6">
                                                <input type="checkbox" id="active_status" name="active_status" value="1">
                                                <label class="col-sm-6 control-label" style="text-align:left;">Active</label><br>
                                            </div>
                                            <div class="col-md-2"></div>
                                        </div>

                                    </div>
                                </div>
                                <div class="row">
                                    <div class="card-footer card_footer_button text-center">
                                        <button id="account-info-update-button" class="btn btn-primary waves-effect">UPDATE</button>
                                    </div>
                                </div>


                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal For Edit Account Information End -->
@endsection
