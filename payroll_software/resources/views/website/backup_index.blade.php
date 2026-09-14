<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Asloob Beda - Construction Excellence</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@500;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">

    <!-- Lightbox CSS & JS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js"></script>

    <style>
        :root {
            --primary: #E67E22;    /* Orange - Construction Energy */
            --dark: #2D4762;       /* Deep Blue - Header/Footer */
            --light: #f8f9fa;
            --gray: #34495e;
        }

        * { box-sizing: border-box; }
        body {
            font-family: 'Roboto', sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
            background: #f4f6f9;
        }

        h1, h2, h3, h4, h5 { font-family: 'Oswald', sans-serif; font-weight: 700; }

        /* Header */
        .navbar {
            background: var(--dark) !important;
            padding: 1rem 0;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            position: fixed;
            width: 100%;
            z-index: 1000;
        }
        .navbar-brand {
            font-size: 1.8rem;
            font-weight: 700;
            color: white !important;
        }
        .navbar-brand span { color: var(--primary); }
        .navbar-brand img {
            transition: transform 0.3s;
        }
        .navbar-brand:hover img {
            transform: scale(1.05);
        }
        .nav-link {
            color: white !important;
            font-weight: 500;
            margin: 0 10px;
            transition: 0.3s;
        }
        .nav-link:hover {
            color: var(--primary) !important;
            transform: translateY(-2px);
        }

        /* Hero Carousel */
        .carousel-item {
            height: 100vh;
            position: relative;
            background: no-repeat center center/cover;
        }
        .carousel-overlay {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(135deg, rgba(45,71,98,0.8), rgba(230,126,34,0.6));
        }
        .carousel-caption {
            z-index: 2;
            bottom: 30%;
            text-align: left;
            max-width: 600px;
            left: 10% !important;
            right: auto !important;
        }
        .carousel-caption h1 {
            font-size: 3.5rem;
            font-weight: 700;
            text-shadow: 2px 2px 10px rgba(0,0,0,0.7);
        }
        .carousel-caption p {
            font-size: 1.3rem;
            margin: 1rem 0;
            text-shadow: 1px 1px 5px rgba(0,0,0,0.6);
        }
        .btn-construction {
            background: var(--primary);
            color: white;
            padding: 12px 30px;
            font-weight: 600;
            border-radius: 0;
            border: 3px solid var(--primary);
            transition: 0.3s;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .btn-construction:hover {
            background: transparent;
            color: white;
            border-color: white;
        }

        /* Section Title */
        .section-title {
            font-size: 2.8rem;
            color: var(--dark);
            position: relative;
            display: inline-block;
            padding-bottom: 15px;
        }
        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 5px;
            background: var(--primary);
        }

        /* Services */
        .service-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            transition: 0.4s;
            border: 1px solid #ddd;
        }
        .service-card:hover {
            transform: translateY(-15px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }
        .service-icon {
            font-size: 3.5rem;
            color: var(--primary);
            margin-bottom: 1rem;
        }

        /* Projects */
        .project-card {
            position: relative;
            overflow: hidden;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .project-card img {
            transition: 0.5s;
            height: 250px;
            object-fit: cover;
        }
        .project-card:hover img {
            transform: scale(1.1);
        }
        .project-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(transparent, rgba(45,71,98,0.9));
            color: white;
            padding: 2rem 1rem 1rem;
            transform: translateY(50px);
            transition: 0.4s;
        }
        .project-card:hover .project-overlay {
            transform: translateY(0);
        }

        /* Stats */
        .stats {
            background: var(--dark);
            color: white;
            padding: 4rem 0;
        }
        .stat-number {
            font-size: 3rem;
            font-weight: 700;
            color: var(--primary);
        }

        /* Footer */
        footer {
            background: var(--dark);
            color: white;
            padding: 3rem 0 1.5rem;
        }
        footer a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }
        footer a:hover { color: white; }
        .footer-logo {
            font-size: 1.8rem;
            font-weight: 700;
        }
        .footer-logo span { color: var(--primary); }
    </style>
</head>
<body>

<!-- Header -->
<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="/">
            @php $firstBanner = $getBanner->first(); @endphp
            @if(isset($firstBanner->company_logo))
                <img src="{{ asset($firstBanner->company_logo) }}" alt="Asloob Beda" class="img-fluid" style="height: 55px;">
            @endif
            <div class="d-flex flex-column">
                {{-- <span class="fw-bold text-white" style="font-size: 1.1rem; line-height: 1;">ASLOOB</span> --}}
                <span class="fw-bold" style="font-size: 1.1rem; line-height: 1;color:#E67E22 ">ASLOOB</span>
                <span class="text-primary fw-bold" style="font-size: 1.3rem; line-height: 1; letter-spacing: 1px;">BEDA</span>
                <small class="text-white opacity-75" style="font-size: 0.65rem; letter-spacing: 2px;">CONTRACTING COMPANY</small>
            </div>
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
            <span class="navbar-toggler-icon" style="filter: brightness(0) invert(1);"></span>
        </button>
        <div class="collapse navbar-collapse" id="nav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="#home">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="#services">Services</a></li>
                <li class="nav-item"><a class="nav-link" href="#projects">Projects</a></li>
                <li class="nav-item"><a class="nav-link" href="#team">Team</a></li>
                <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Hero Carousel -->
<div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">
        @foreach($getBanner as $index => $banner)
        <div class="carousel-item {{ $index == 0 ? 'active' : '' }}" style="background-image: url('{{ asset($banner->image) }}')">
            <div class="carousel-overlay"></div>
            <div class="carousel-caption">
                <h1>{{ $banner->title }}</h1>
                <p>{{ $banner->subtitle }}</p>
                <a href="{{ $banner->button_url }}" class="btn-construction">{{ $banner->button_text }}</a>
            </div>
        </div>
        @endforeach
    </div>
    <button class="carousel-control-prev" data-bs-target="#heroCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
    </button>
    <button class="carousel-control-next" data-bs-target="#heroCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
    </button>
</div>

<!-- Services -->
<section id="services" class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Our Expertise</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="service-card text-center p-4">
                    <div class="service-icon"><i class="fas fa-hard-hat"></i></div>
                    <h5>General Contracting</h5>
                    <p>Full-scale construction from foundation to finish.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="service-card text-center p-4">
                    <div class="service-icon"><i class="fas fa-building"></i></div>
                    <h5>Commercial Projects</h5>
                    <p>Office towers, malls, and industrial complexes.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="service-card text-center p-4">
                    <div class="service-icon"><i class="fas fa-home"></i></div>
                    <h5>Residential Development</h5>
                    <p>Luxury homes and apartment complexes.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats -->
<div class="stats text-center">
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <div class="stat-number">150+</div>
                <p>Projects Completed</p>
            </div>
            <div class="col-md-3">
                <div class="stat-number">50+</div>
                <p>Expert Engineers</p>
            </div>
            <div class="col-md-3">
                <div class="stat-number">15</div>
                <p>Years Experience</p>
            </div>
            <div class="col-md-3">
                <div class="stat-number">98%</div>
                <p>Client Satisfaction</p>
            </div>
        </div>
    </div>
</div>

<!-- Projects -->
<section id="projects" class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Featured Projects</h2>
        </div>
        <div class="row g-4">
            @foreach([1,2,3] as $i)
            <div class="col-md-4">
                <div class="project-card">
                    <img src="https://via.placeholder.com/400x250/2D4762/white?text=Project+{{ $i }}" class="w-100" alt="Project">
                    <div class="project-overlay">
                        <h5>Al Maktoum Tower {{ $i }}</h5>
                        <p>120-Story Mixed-Use Skyscraper</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Team & Labor Gallery -->
<section id="team" class="py-5 bg-white">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Our Team & Work in Action</h2>
            <p class="lead text-muted">Dedicated professionals building your future</p>
        </div>

        <!-- Lightbox Gallery -->
        <div class="row g-3">
            @foreach($laborImages as $img)
            <div class="col-6 col-md-4 col-lg-3">
                <a href="{{ asset($img) }}" data-lightbox="labor" data-title="Asloob Beda Team">
                    <img src="{{ asset($img) }}" class="img-fluid rounded shadow-sm w-100" 
                         style="height: 200px; object-fit: cover; transition: 0.3s;"
                         alt="Team Work">
                </a>
            </div>
            @endforeach
        </div>

        <div class="text-center mt-4">
            <small class="text-muted">Click any image to enlarge</small>
        </div>
    </div>
</section>

<!-- Contact Form -->
<section id="contact" class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Get In Touch</h2>
            <p class="lead text-muted">We'd love to hear from you</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <form action="" method="POST" class="p-4 bg-white rounded shadow-sm">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" class="form-control" placeholder="John Doe" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="john@example.com" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control" placeholder="+880 1xxx xxxxxx">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Subject</label>
                            <select name="subject" class="form-select">
                                <option>General Inquiry</option>
                                <option>Project Consultation</option>
                                <option>Career Opportunity</option>
                                <option>Partnership</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Message</label>
                            <textarea name="message" rows="5" class="form-control" placeholder="Tell us about your project..." required></textarea>
                        </div>
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-construction px-5">
                                Send Message
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer>
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-5">
                <div class="d-flex align-items-center gap-3 mb-3">
                    @if(isset($firstBanner->company_logo))
                        <img src="{{ asset($firstBanner->company_logo) }}" alt="Logo" style="height: 65px;">
                    @endif
                    <div>
                        <div class="text-white fw-bold" style="font-size: 1.1rem;">ASLOOB</div>
                        <div class="text-primary fw-bold" style="font-size: 1.4rem; letter-spacing: 1px;">BEDA</div>
                        <small class="text-white opacity-75">CONTRACTING COMPANY</small>
                    </div>
                </div>
                <p class="mb-0">Delivering world-class infrastructure with passion and precision.</p>
            </div>
            <div class="col-md-3 text-center">
                <p class="mb-1"><strong>Contact</strong></p>
                <p class="mb-0"><i class="fas fa-phone"></i> +880 123 456 789</p>
                <p><i class="fas fa-envelope"></i> info@asloobbeda.com</p>
            </div>
            <div class="col-md-4 text-end">
                <p class="mb-1"><a href="#">Privacy</a> • <a href="#">Terms</a></p>
                <p class="mb-0">&copy; {{ date('Y') }} Asloob Beda. All Rights Reserved.</p>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>