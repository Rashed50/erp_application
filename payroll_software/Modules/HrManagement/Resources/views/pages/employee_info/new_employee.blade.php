@extends('layouts.admin-master')
@section('title', 'New Employee')
@section('content')

    <div id="app">
        {{-- <AddEmployeeComponent /> --}}
        {{-- <add-empolyee-/>></add-empolyee-component> --}}

        <new_employee_container :data="{{ json_encode($data) }}"></new_employee_container>
    </div>

@endsection
