@extends('layouts.admin-master')
@section('title', 'Purchase Bill')
@section('content')

    <div id="app">
        <account-modules-purchase-edit :data="{{ json_encode($data) }}"></account-modules-purchase-edit>
    </div>

@endsection
