@extends('layouts.admin-master')
@section('title', 'Report')
@section('content')
<div class="row">
    <div id="app">
        <report-menu :data_for_report_form="{{ json_encode($data_for_report_form) }}"></report-menu>
    </div>
</div>
@endsection

