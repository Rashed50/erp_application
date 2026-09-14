@extends('layouts.admin-master')
@section('title') Suppliers Update @endsection

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
        <inventory-suppliers-update :data="{{ json_encode($data) }}"></inventory-suppliers-update>
    </div>

@endsection
