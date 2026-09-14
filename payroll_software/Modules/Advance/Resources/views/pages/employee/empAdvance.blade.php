@extends('layouts.admin-master')
@section('title', 'Employee Advance')
@section('content')

    {{-- breadcumb --}}
    <div id="app">
        <advance-modules-employee-advance :data="{{ json_encode($data) }}">
        </advance-modules-employee-advance>
    </div>

@endsection
