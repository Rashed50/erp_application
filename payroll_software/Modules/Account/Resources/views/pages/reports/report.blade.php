@extends('layouts.admin-master')
@section('title', ' Report Container')
@section('content')

    <div id="app">
        <account-modules-report-container :data_for_report_form="{{ json_encode($data_for_report_form) }}">
        </account-modules-report-container>
    </div>

@endsection
