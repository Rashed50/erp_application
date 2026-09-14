@extends('layouts.admin-master')
@section('title', 'Sale List')
@section('content')

    {{-- breadcumb --}}
    <div id="app">
        <account-modules-sale-index :data="{{ json_encode($data) }}"></account-modules-sale-index>
    </div>

@endsection
