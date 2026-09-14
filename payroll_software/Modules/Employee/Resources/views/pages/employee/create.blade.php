@extends('layouts.admin-master')
@section('title', 'Employee')
@section('content')


{{-- breadcumb --}}
<div id="app">
    <a href="{{ route('admin.employee.employee.payslip') }}" class="btn btn-primary mb-3">Generate Payslip (Test)</a>
    <employee-create-component :data="{{ json_encode($data) }}">
    </employee-create-component>
</div>

@endsection
