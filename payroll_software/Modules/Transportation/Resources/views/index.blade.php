@extends('transportation::layouts.master')
@extends('layouts.admin-master')
@section('title', 'Transportation')
@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
{{-- for add to cart icon --}}

<div class="row">
    <div id="app">
        <vehicle_servicing_menu :data_for_servicing_form="{{ json_encode($data_for_servicing_form) }}" ></vehicle_servicing_menu>
    </div>
@endsection


