@extends('layouts.admin-master')
@section('title', 'SubContractor')
@section('content')
<div class="row">
    <div id="app">
        <subcontractor-menu :data_for_subcontractor_form="{{ json_encode($data_for_subcontractor_form) }}" ></subcontractor-menu>
    </div>
@endsection

