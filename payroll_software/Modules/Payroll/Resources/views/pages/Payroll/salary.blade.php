@extends('layouts.admin-master')
@section('title', 'Payroll Salary')
@section('content')


<div id="app">
    <payroll-employee-salary :data="{{ json_encode($data) }}"></payroll-employee-salary>
</div>

@endsection
