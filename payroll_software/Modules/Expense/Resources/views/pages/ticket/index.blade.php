@extends('layouts.admin-master')
@section('title', 'Ticket')
@section('content')

    <div id="app">
        <ticket-index :data="{{ json_encode($data) }}"></ticket-index>
    </div>


@endsection