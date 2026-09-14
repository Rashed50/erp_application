@extends('layouts.admin-master')
@section('title', 'Employee Daily Activity')
@section('content')

    {{-- breadcumb --}}
    <div id="app">
        <hr-daily-activity :data="{{ json_encode($data) }}">
        </hr-daily-activity>
    </div>

@endsection
