@extends('layouts.admin-master')
@section('title', 'Employee Update')
@section('content')

<div id="app">
    <employee_update_container :data="{{ json_encode($data) }}"></employee_update_container>
</div>

@endsection
