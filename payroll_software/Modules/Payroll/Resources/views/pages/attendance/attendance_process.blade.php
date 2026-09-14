@extends('layouts.admin-master')
@section('title', 'Attendance')
@section('content')
<div class="row">
    <div id="app">
        <attendance_process :form_data="{{ json_encode($data) }}"  >  </attendance_process>
    </div>
@endsection
