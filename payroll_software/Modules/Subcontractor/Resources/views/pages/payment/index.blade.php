@extends('layouts.admin-master')
@section('title', 'SC_Payment')
@section('content')
    <div class="row">
        <div id="app">
            <sc_payment_menu :payment_form_data="{{ json_encode($payment_form_data) }}"></sc_payment_menu>
        </div>
    @endsection
