@extends('layouts.admin-master')
@section('title', 'Payroll Salary Update')
@section('content')


    <div id="app">
        <salary_update :data="{{ json_encode($data) }}"></salary_update>
    </div>

@endsection
