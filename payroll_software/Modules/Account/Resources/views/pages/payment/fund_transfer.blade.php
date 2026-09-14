@extends('layouts.admin-master')
@section('title', 'Internal Transfer')
@section('content')

    <div id="app">
        <account-modules-fund-transfer :data="{{ json_encode($data) }}"></account-modules-fund-transfer>
    </div>

@endsection
