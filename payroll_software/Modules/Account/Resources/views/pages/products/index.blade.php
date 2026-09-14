@extends('layouts.admin-master')
@section('title', 'Sale Products')
@section('content')
    <script>
        window.products = {!! json_encode($products ?? []) !!};
        window.units = {!! json_encode($units ?? []) !!};
    </script>
    <div id="app">
        <account-modules-products></account-modules-products>
    </div>
@endsection
