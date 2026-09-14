@extends('layouts.admin-master')
@section('title') Suppliers List @endsection

@section('internal-css')
    <style>
        /* Chrome, Safari, Edge, Opera */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Firefox */
        input[type="number"] {
            -moz-appearance: textfield;
        }
    </style>
@endsection


@section('content')
    <div id="app">
        <inventory-suppliers-list
            :data="{{ json_encode($data) }}"
        ></inventory-suppliers-list>
    </div>
@endsection
