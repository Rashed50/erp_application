@extends('layouts.admin-master')
@section('title', 'Employee Transfer')
@section('content')

    <div id="app">
        <employee_transfer_container :data="{{ json_encode($projects) }}">
        </employee_transfer_container>


    </div>

@endsection
