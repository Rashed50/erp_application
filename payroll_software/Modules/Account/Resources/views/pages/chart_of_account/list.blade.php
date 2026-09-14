@extends('layouts.admin-master')
@section('title')
    ChartOfAccount
@endsection

@section('internal-css')

@endsection

@section('content')

<div id="app">
        <chart-of-account-list :data="{{ json_encode($chartOfAccounts) }}">

        </chart-of-account-list>
    </div>

@endsection

