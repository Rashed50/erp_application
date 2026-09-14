@extends('layouts.admin-master')
@section('title', 'Sale Create')
@section('content')

    <div id="app">
        <account-modules-sale-add :data="{{ json_encode($data) }}"></account-modules-sale-add>
    </div>

@endsection
