@extends('layouts.admin-master')
@section('title', 'SC_Service')
@section('content')
<div class="row">
    <div id="app">
        <sc_service_menu :service_form_data="{{ json_encode($service_form_data) }}"  ></sc_service_menu>
    </div>
@endsection
