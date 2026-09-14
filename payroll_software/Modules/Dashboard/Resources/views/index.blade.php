@extends('dashboard::layouts.master')

@section('title', 'Dashboard')

@section('header')@endsection

@section('content')
<section 
    class="hero-gallery relative flex items-center justify-center"
    style="
        height: calc(100vh - 5rem); /* যদি header 80px হয় */
        background-image: 
            linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.8)),
            url('{{ asset($getBanner->first()->image ?? 'images/DJI_0204.webp') }}');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        background-attachment: fixed;
    "
>
    <div class="text-center text-white px-4">
        <h1 class="text-3xl md:text-5xl font-bold mb-4">Welcome to Dashboard</h1>
        <p class="text-lg md:text-2xl">ABC Simplified Payroll Management</p>
    </div>
</section>
@endsection
