@extends('layouts.admin-master')
@section('title') Ledger Account Edit @endsection

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
        <chart-of-account-update :data="{{ json_encode($data) }}"></chart-of-account-update>
    </div>

@endsection
