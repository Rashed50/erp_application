@extends('layouts.admin-master')
@section('title', 'Sale Product Units')
@section('content')
    <script>
        window.units = {!! json_encode($units ?? []) !!};
    </script>
    <div id="app">
        <account-modules-product-units></account-modules-product-units>
    </div>
@endsection
