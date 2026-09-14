@extends('layouts.admin-master')
@section('title', 'Attendance')
@section('content')
<div class="row">
    <div id="app">
        <attendance_in_out :form_data="{{ json_encode($data) }}"  >  </attendance_in_out>
    </div>
@endsection
