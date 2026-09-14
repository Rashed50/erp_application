@extends('layouts.admin-master')
@section('title', 'Subcon-Report')
@section('content')
    <div id="app">
        <subcontractor_all_reports :projects="{{ json_encode($projects) }}"
            :subcontractors="{{ json_encode($subcontractors) }}" :status="{{ json_encode($status) }}">
        </subcontractor_all_reports>
    </div>

@endsection
