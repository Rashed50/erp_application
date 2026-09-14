@extends('layouts.admin-master')
@section('title', 'Sale Report')
@section('content')

    <div id="app">
        <account-modules-sales-report 
            :projects="{{ json_encode($projects) }}" 
            :status="{{ json_encode($status) }}">
        </account-modules-sales-report>
    </div>

@endsection
