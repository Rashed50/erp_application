<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>..::ASLOOB::..</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Lightbox -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css" rel="stylesheet">

    <style>
        :root {
            --primary: #E67E22;
            --dark: #2D4762;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body, html {
            height: 100%;
            font-family: 'Oswald', sans-serif;
            background: #000;
            color: white;
            overflow-x: hidden;
        }

        /* Header - Responsive Height */
        .navbar {
            background: var(--dark) !important;
            padding: 0.8rem 0 !important;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            box-shadow: 0 4px 20px rgba(0,0,0,0.5);
        }
        .navbar-brand img {
            height: 48px;
        }
        .navbar-brand .logo-text {
            font-size: 1.1rem;
            line-height: 1.1;
        }
        .navbar-brand .logo-text span:first-child { color: var(--primary); }
        .navbar-brand .logo-text span:last-child { color: white; font-size: 1.4rem; font-weight: 700; }

        /* Hero + Gallery */
        .hero-gallery {
            min-height: 100vh;
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.8)),
                        url('{{ asset($getBanner->first()->image ?? "images/DJI_0204.webp") }}') center/cover no-repeat fixed;
            padding: 90px 15px 120px; /* header + footer space */
            display: flex;
            align-items: center;
        }

        /* Responsive Grid - Mobile এ ১টা, Tablet এ ২টা, Desktop এ ৪+ */
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(1, 1fr); /* Mobile: 1 column */
            gap: 16px;
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 10px;
        }

        /* Tablet (≥576px) → 2 columns */
        @media (min-width: 576px) {
            .gallery-grid { grid-template-columns: repeat(2, 1fr); }
        }

        /* Desktop (≥992px) → 4 columns */
        @media (min-width: 992px) {
            .gallery-grid { grid-template-columns: repeat(4, 1fr); gap: 20px; }
        }

        /* Extra Large (≥1400px) → 5 columns */
        @media (min-width: 1400px) {
            .gallery-grid { grid-template-columns: repeat(5, 1fr); }
        }

        .gallery-item {
            aspect-ratio: 1 / 1;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.6);
            transition: all 0.4s ease;
        }
        .gallery-item:hover {
            transform: translateY(-12px) scale(1.02);
            box-shadow: 0 25px 50px rgba(230, 126, 34, 0.4);
        }
        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s ease;
        }
        .gallery-item:hover img {
            transform: scale(1.2);
        }

        /* Fixed Footer */
        .fixed-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background: rgba(45, 71, 98, 0.97);
            backdrop-filter: blur(12px);
            padding: 14px 0;
            text-align: center;
            font-size: 0.95rem;
            z-index: 999;
            border-top: 1px solid rgba(255,255,255,0.15);
        }

        /* Mobile Header Text Smaller */
        @media (max-width: 576px) {
            .navbar-brand img { height: 40px; }
            .navbar-brand .logo-text { font-size: 0.95rem; }
            .navbar-brand .logo-text span:last-child { font-size: 1.2rem; }
        }
    </style>
</head>
<body>

<!-- Header -->
<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-3" href="/">
            @if(isset($getBanner->first()->company_logo))
                <img src="{{ asset($getBanner->first()->company_logo) }}" alt="Asloob Bedaa">
            @endif
            {{-- <div class="logo-text">
                <span>ASLOOB</span><br>
                <span>BEDAA</span>
            </div> --}}
            <h2 style="color: white">   ABC Simplified Payroll Management</h2>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
            <span class="navbar-toggler-icon" style="filter: brightness(0) invert(1);"></span>
        </button>

        <div class="collapse navbar-collapse" id="nav">
            <ul class="navbar-nav ms-auto gap-4">
                <li><a class="nav-link text-white" href="#home">Home</a></li>
                <li><a class="nav-link text-white" href="#team">Team</a></li>
                <li><a class="nav-link text-white" href="#contact">Contact</a></li>
                 @if (Route::has('login'))

                    @auth
                    <li> <a class="nav-link text-white" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    @else
                    <li>   <a class="nav-link text-white" href="{{ route('login') }}">Login</a></li>
                    @endauth

                @endif
            </ul>
        </div>
    </div>
</nav>

<!-- Full Screen Gallery -->
<section class="hero-gallery">
    {{-- <div class="gallery-grid">
        @foreach($laborImages as $img)
        <div class="gallery-item">
            <a href="{{ asset($img) }}" data-lightbox="asloob">
                <img src="{{ asset($img) }}" alt="Asloob Beda Team">
            </a>
        </div>
        @endforeach
    </div> --}}
</section>

<!-- Fixed Footer -->
<div class="fixed-footer">
    © {{ date('Y') }} Asloob Bedaa Co. All Rights Reserved.
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js"></script>

<script>
    lightbox.option({
        resizeDuration: 300,
        wrapAround: true,
        disableScrolling: true,
        showImageNumberLabel: false,
        positionFromTop: 100
    });
</script>
</body>
</html>
