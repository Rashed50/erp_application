@extends('layouts.admin-master')
@section('title', 'Payslip Upload')
@section('content')

<div id="app">
    <payslip-upload-manager  ></payslip-upload-manager>
</div>

@endsection

@push('scripts')
<script>
    // Pass any additional data if needed

</script>
@endpush
