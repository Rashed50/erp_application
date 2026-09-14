@extends('layouts.admin-master')
@section('title', 'Ticket Buy')
@section('content')

    <div id="app">
        <ticket-create :data="{{ json_encode($data) }}"></ticket-create>
    </div>


@endsection
