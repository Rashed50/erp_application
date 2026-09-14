@extends('layouts.admin-master')
@section('title', 'Sale Create')
@section('content')

    <div id="app">
        <account-modules-sales-payment :data="{{ json_encode($data) }}"></account-modules-sales-payment>
    </div>

@endsection
