@extends('layouts.admin-master')
@section('title')
    Chart Of Account List
@endsection

@section('internal-css')

@endsection

@section('content')
    <div class="row bread_part">
        <div class="col-sm-12 bread_col d-flex" style="justify-content: space-between;">
            <h4 class="pull-left page-title bread_title">Company Chart Of Account </h4>
            <div>
                <a class="btn btn-success btn-sm" href="{{route('admin.accounting.company.chart.of.account')}}">
                    Add New
                </a>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                {{-- <div class="card-header d-flex" style="justify-content: space-between;">
                    <div class="fw-bold">Chart Of Account Informations</div>
                    <div>
                        <a class="btn btn-success btn-sm" href="{{route('admin.accounting.company.chart.of.account')}}">
                            Add New
                        </a>
                    </div>
                </div> --}}
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
                                    <tbody>
                                        @foreach($chartOfAccounts as $account)
                                            <tr>
                                                <td>{{ $account->type }}</td>
                                                <td>{{ $account->chart_of_acct_name }}</td>
                                                <td>{{ $account->chart_of_acct_number }}</td>
                                                <td>{{ number_format($account->acct_balance, 2) }}</td>
                                                <td>{{ \Carbon\Carbon::parse($account->opening_date)->format('Y-m-d') }}</td>
                                                <td>{{ $account->predefined ? 'Yes' : 'No' }}</td>
                                                <td>{{ $account->transaction_status ? 'Active' : 'Inactive' }}</td>
                                                <td>{{ $account->is_closed ? 'Yes' : 'No' }}</td>
                                                <td>{{ $account->createdBy->name ?? 'N/A' }}</td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#example2Modal">
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                    &nbsp;
                                                    <button type="button" class="btn btn-sm btn-danger">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </td>
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

@endsection

