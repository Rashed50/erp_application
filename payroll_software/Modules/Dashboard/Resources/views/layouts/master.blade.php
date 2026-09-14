<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard')</title>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    @stack('styles')
</head>
<body class="bg-white text-gray-900 min-h-screen">

    {{-- Header --}}
    @include('dashboard::layouts.header')

    {{-- Page Content --}}
    <main class="pt-24 pb-20">
        @yield('content')
    </main>

    {{-- Footer --}}
    @include('dashboard::layouts.footer')

    <!-- =========== Vue [ vite bundler ] ============ -->
    @vite('resources/js/app.js')
    <!-- ============================================= -->


    @stack('scripts')
</body>
</html>
