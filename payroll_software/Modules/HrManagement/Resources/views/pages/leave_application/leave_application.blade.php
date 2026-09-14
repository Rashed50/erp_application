@extends('layouts.admin-master')
@section('title', 'Leave Appl.')
@section('content')

<div id="app">

    <leave_appliation_manager :form_data="{{ json_encode($data) }}" ></leave_appliation_manager>
    {{-- :data="{{ json_encode($data) }}" --}}
</div>


{{-- <script>
    console.log('Data from PHP:', @json($data));
</script> --}}

@endsection



