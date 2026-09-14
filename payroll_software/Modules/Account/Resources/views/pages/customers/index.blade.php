@extends('layouts.admin-master')
@section('title', 'Sale Customers')
@section('content')
    <div id="app">
        <account-modules-customers
            :customers="{{ json_encode($customers) }}"
            :save = "'{{ route('admin.accounting.customer.save') }}'"
            :update = "'{{ route('admin.accounting.customer.update') }}'"
            :delete = "'{{ route('admin.accounting.customer.delete') }}'"
        ></account-modules-customers>
    </div>
@endsection
