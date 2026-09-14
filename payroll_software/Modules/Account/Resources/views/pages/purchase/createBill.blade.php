@extends('layouts.admin-master')
@section('title', 'Purchase Bill')
@section('content')

    <div id="app">
        <account-modules-purchase-bill :data="{{ json_encode($data) }}"></account-modules-purchase-bill>
    </div>

@endsection
