@extends('layouts.website')
@section('title') ABC @endsection
@section('content')


<!--====================  hero slider area ====================-->
{{-- <div class="hero-slider-area space__bottom--r40">
    <div class="hero-slick-slider-wrapper">
      @foreach($getBanner as $banner)
        <div class="single-hero-slider single-hero-slider--background single-hero-slider--overlay position-relative bg-img" data-bg="{{ asset('uploads/banner/'.$banner->ban_image) }}">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <!-- hero slider content -->
                        <div class="hero-slider-content hero-slider-content--extra-space">
                            <h3 class="hero-slider-content__subtitle">{{ $banner->ban_subtitle }}</h3>
                            <h2 class="hero-slider-content__title space__bottom--50">{{ $banner->ban_title }}</h2>
                            <a href="#" class="default-btn default-btn--hero-slider">{{ $banner->ban_caption }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
     </div>
</div> --}}



    {{-- <header>
        <img src="logo.png" alt="Company Logo" class="logo">
        <h1>ABC Simplified Payroll Management</h1>
        <button class="login-button">Login</button>
    </header> --}}
    <div class="gallery">

        <img src="{{ asset('contents/admin') }}/assets/images/home_page/home1.jpg" alt="Payroll Overview">
         <img src="{{ asset('contents/admin') }}/assets/images/home_page/logo_new.png" alt="Payroll Overview">
        <img src="{{ asset('contents/admin') }}/assets/images/home_page/home2.jpg" alt="Payroll Overview">
        <img src="{{ asset('contents/admin') }}/assets/images/home_page/logo_new.png" alt="Payroll Overview">
        <img src="{{ asset('contents/admin') }}/assets/images/home_page/home3.jpg" alt="Payroll Overview">
        <img src="{{ asset('contents/admin') }}/assets/images/home_page/logo_new.png" alt="Payroll Overview">
    </div>
    {{-- <footer>
        &copy; 2025 PayrollPro. All Rights Reserved.
    </footer> --}}


<style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: url('logo.png') no-repeat center center fixed;
            background-size: cover;
            overflow-x: hidden;
            animation: backgroundAnimation 10s infinite alternate;
            height: 100%;
            background-color:#007bff;
        }
        @keyframes backgroundAnimation {
            0% { filter: brightness(1); }
            100% { filter: brightness(0.8); }
        }
        header {
            background-color: #f34a4a;
            color: #333;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 50px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        .logo {
            width: 150px;
        }
        .login-button {
            background: #007bff;
            color: #fff;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
            transition: background 0.3s;
        }
        .login-button:hover {
            background: #0056b3;
        }
        .gallery {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            padding: 50px 0;
        }
        .gallery img {
            width: 30%;
            margin: 10px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            opacity: 0;
            animation: fadeIn 3s infinite alternate;
        }
        @keyframes fadeIn {
            0% { opacity: 0; transform: scale(0.9); }
            100% { opacity: 1; transform: scale(1); }
        }
        footer {
            background: #222;
            color: #fff;
            text-align: center;
            padding: 15px;
            position: relative;
            bottom: 0;
            width: 100%;
        }
</style>

@endsection
