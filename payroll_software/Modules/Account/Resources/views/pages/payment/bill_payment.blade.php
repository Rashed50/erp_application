@extends('layouts.admin-master')
@section('title', 'Purchase Payment')
@section('content')


    <div id="app">
        <account-modules-bill-payment :data="{{ json_encode($data) }}"></account-modules-bill-payment>
    </div>

@endsection
