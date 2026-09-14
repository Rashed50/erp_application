@extends('layouts.admin-master')
@section('title')
    Journal Informations
@endsection
@section('content')
    <div class="row bread_part">
        <div class="col-sm-12 bread_col">
            <h4 class="pull-left page-title bread_title">Journal Informations </h4>
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

    <form id="account_journal_info_form" action="#" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <div class="card">
                    <div
                        class="form-group row mt-4 custom_form_group{{ $errors->has('jour_type_id') ? ' has-error' : '' }}">
                        <label class="control-label col-sm-4" for="jour_type_id">Journal Type: <span class="req_star">*</span></label>
                        <div class="col-sm-6">
                            <select id="jour_type_id" class="form-select" name="jour_type_id" required>
                                <option value="">Select Journal Type</option>
                                @foreach ($journal_types as $item)
                                    <option value="{{ $item->jour_type_id }}">{{ $item->jour_type_name }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($errors->has('jour_type_id'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('jour_type_id') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="col-md-2"></div>
                    </div>

                    <div class="form-group row custom_form_group{{ $errors->has('chart_of_acct_id') ? ' has-error' : '' }}">
                        <label class="control-label col-sm-4" for="chart_of_acct_id">Chart Of Accounts: </label>
                        <div class="col-sm-6">
                            <select id="chart_of_acct_id" class="form-select" name="chart_of_acct_id">
                                <option value="">Select Any Chart Of Account</option>
                            </select>
                            @if ($errors->has('chart_of_acct_id'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('chart_of_acct_id') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="col-md-2"></div>
                    </div>


                    <div class="form-group row custom_form_group{{ $errors->has('jour_name') ? ' has-error' : '' }}">
                        <label class="control-label col-sm-4">Account Name:<span class="req_star">*</span></label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="jour_name" value="{{ old('jour_name') }}"
                                placeholder="Account Journal Name" required>
                            @if ($errors->has('jour_name'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('jour_name') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="col-md-2"></div>
                    </div>


                    <div class="form-group row custom_form_group{{ $errors->has('jour_code') ? ' has-error' : '' }}">
                        <label class="control-label col-sm-4">Code:<span class="req_star">*</span></label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="jour_code" value="{{ old('jour_code') }}"
                                placeholder="Enter Journal Code" required>
                            @if ($errors->has('jour_code'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('jour_code') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="col-md-2"></div>
                    </div>
                </div>

                <div class="row">
                    <div class="card-footer card_footer_button text-center">
                        <button id="account-journal-info-submit-button" class="btn btn-primary waves-effect">SAVE INFO</button>
                    </div>
                </div>
            </div>
            <div class="col-md-2"></div>
        </div>
    </form>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    Account Journal Informations
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table id="alltableinfo" class="table table-bordered custom_table mb-0">
                                    <thead>
                                        <tr>
                                            <th>Journal Type</th>
                                            <th>Chart Of Account Name</th>
                                            <th>Journal Name</th>
                                            <th>Journal Code</th>
                                            <th>Status</th>
                                            <th>Manage</th>
                                        </tr>
                                    </thead>
                                    <tbody id="account_journal_info_table_content_view">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="journal_info_update_modal" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">Edit Journal Information</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="journalInfoEditForm" action="#" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="edit_jour_id" name="jour_id">

                        <div class="form-group row mt-4 custom_form_group{{ $errors->has('jour_type_id') ? ' has-error' : '' }}">
                            <label class="control-label col-sm-4" for="jour_type_id">Journal Type: <span class="req_star">*</span></label>
                            <div class="col-sm-8">
                                <select id="edit_jour_type_id" class="form-select" name="jour_type_id" required>
                                    <option value="">Select Journal Type</option>
                                    @foreach ($journal_types as $itemJournal)
                                        <option value="{{ $itemJournal->jour_type_id }}">{{ $itemJournal->jour_type_name }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('jour_type_id'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('jour_type_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row custom_form_group{{ $errors->has('chart_of_acct_id') ? ' has-error' : '' }}">
                            <label class="control-label col-sm-4" for="chart_of_acct_id">Chart Of Accounts: </label>
                            <div class="col-sm-8">
                                <select id="edit_chart_of_acct_id" class="form-select" name="chart_of_acct_id">
                                    <option value="">Select Any Chart Of Account</option>
                                </select>
                                @if ($errors->has('chart_of_acct_id'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('chart_of_acct_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row custom_form_group{{ $errors->has('jour_name') ? ' has-error' : '' }}">
                            <label class="control-label col-sm-4">Account Name:<span class="req_star">*</span></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="edit_jour_name" name="jour_name" placeholder="Account Journal Name" required>
                                @if ($errors->has('jour_name'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('jour_name') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="col-md-2"></div>
                        </div>

                        <div class="form-group row custom_form_group{{ $errors->has('jour_code') ? ' has-error' : '' }}">
                            <label class="control-label col-sm-4">Code:<span class="req_star">*</span></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="edit_jour_code" name="jour_code" placeholder="Enter Journal Code" required>
                                @if ($errors->has('jour_code'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('jour_code') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="col-md-2"></div>
                        </div>
                        <div class="row">
                            <div class="card-footer card_footer_button text-center">
                                <button id="account-journal-info-update-button" class="btn btn-primary waves-effect">UPDATE INFO</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


@endsection
