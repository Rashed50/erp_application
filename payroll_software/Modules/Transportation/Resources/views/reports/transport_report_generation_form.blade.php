@extends('transportation::layouts.master')
@extends('layouts.admin-master')
@section('title', 'Transportation')
@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
{{-- for add to cart icon --}}

<div class="row">

    <div id="app">
        <vehicle_maintenance_report  :data_for_form="{{ json_encode($form_data) }}"></vehicle_maintenance_report>
     </div>
@endsection


