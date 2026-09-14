@extends('layouts.admin-master')
@section('title', 'Sale Edit')
@section('content')

    <div id="app">
        <account-modules-sale-edit :data="{{ json_encode($data) }}"></account-modules-sale-edit>
    </div>

@endsection
