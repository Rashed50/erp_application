<!DOCTYPE html>
<html class="no-js" lang="zxx">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title')</title>
    <link rel="shortcut icon" href="{{asset('contents/admin')}}/assets/images/favicon_asloob.ico">

    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('contents/website') }}/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('contents/website') }}/assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="{{ asset('contents/website') }}/assets/css/flaticon.min.css">
    <link rel="stylesheet" href="{{ asset('contents/website') }}/assets/css/plugins/slick.min.css">
    <link rel="stylesheet" href="{{ asset('contents/website') }}/assets/css/plugins/cssanimation.min.css">
    <link rel="stylesheet" href="{{ asset('contents/website') }}/assets/css/plugins/justifiedGallery.min.css">
    <link rel="stylesheet" href="{{ asset('contents/website') }}/assets/css/plugins/light-gallery.min.css">
    <link rel="stylesheet" href="{{ asset('contents/website') }}/assets/css/style.css">
    <link rel="stylesheet" href="{{ asset('contents/website') }}/assets/css/custom.css">
    <style>
        .brand-logo a img{
          height: 60px;
          background-color:#fff;
        }
    </style>
</head>
<body>
    <div class="header-area header-sticky bg-img space__inner--y30 background-repeat--x background-color--white d-none d-lg-block" data-bg="{{ asset('contents/website') }}/assets/img/icons/ruler.png">
        <!-- header top -->
        <div class="header-top" >
            <div class="container">
                <div class="row">
                    <div class="col-lg-10">
                        {{-- <div class="brand-logo">
                            <a href="{{ url('/') }}">
                                <img src="{{ asset('contents/admin') }}/assets/images/logo_icon.png" class="img-fluid" alt="">
                            </a>
                        </div> --}}
                        <h3>   ABC Simplified Payroll Management</h3>
                    </div>
                    {{-- <div class="col-lg-4">

                    </div> --}}
                    <div class="col-lg-2 text-right">
                      @if (Route::has('login'))
                        <div class="header-top-info text-right">
                          @auth
                            <a class="header-top-info__link" href="{{ route('admin.dashboard') }}"><span><b style="color: black">Dashboard</b></span></a>
                          @else
                            <a class="header-top-info__link" href="{{ route('login') }}"><span><b style="color: black;">Login</b></span></a>
                          @endauth
                        </div>
                      @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--====================  End of header area  ====================-->

    <!--====================  mobile header ====================-->
    <div class="mobile-header header-sticky bg-img space__inner--y30 background-repeat--x background-color--white d-block d-lg-none" data-bg="{{ asset('contents/website') }}/assets/img/icons/ruler.png">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-9">
                    {{-- <div class="brand-logo">
                        <a href="{{ url('/') }}">
                            <img src="{{ asset('contents/admin') }}/assets/images/logo_icon.png" class="img-fluid" alt="">
                        </a>
                    </div> --}}
                    <h3>   ABC Simplified Payroll Management</h3>
                </div>
                <div class="col-3 text-right">

                    <div class="text-right">
                      @if (Route::has('login'))
                          @auth
                            <a class="header-top-info__link" href="{{ route('admin.dashboard') }}"> <span>Dashboard</span> </a>
                          @else
                            <a class="header-top-info__link" href="{{ route('login') }}"> <span>Login</span> </a>
                          @endauth
                      @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--====================  End of mobile header  ====================-->
    @yield('content');
    <!--====================  footer area ====================-->
    <!-- copyright text -->
    <div class="copyright-area background-color--deep-dark space__inner--y15">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <?php
                        $date =\Carbon\Carbon::now()->format('Y');
                    ?>
                    <p class="copyright-text">Copyright &copy; <a href="#">ASLOOB BEDAA CO.</a>, All Rights Reserved - {{ $date }}</p>
                </div>
            </div>
        </div>
    </div>
    <!--====================  End of footer area  ====================-->

    <!-- JS
    ============================================ -->
    <!-- Modernizer JS -->
    <script src="{{ asset('contents/website') }}/assets/js/modernizr-2.8.3.min.js"></script>
    <!-- jQuery JS -->
    <script src="{{ asset('contents/website') }}/assets/js/jquery-3.4.1.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="{{ asset('contents/website') }}/assets/js/bootstrap.min.js"></script>

    <!-- Counterup JS -->
    <script src="{{ asset('contents/website') }}/assets/js/plugins/counterup.min.js"></script>
    <!-- Waypoint JS -->
    <script src="{{ asset('contents/website') }}/assets/js/plugins/waypoint.min.js"></script>
    <!-- Justified Gallery JS -->
    <script src="{{ asset('contents/website') }}/assets/js/plugins/justifiedGallery.min.js"></script>
    <!-- Image Loaded JS -->
    <script src="{{ asset('contents/website') }}/assets/js/plugins/imageloaded.min.js"></script>

    <!-- Mailchimp JS -->
    {{-- <script src="{{ asset('contents/website') }}/assets/js/plugins/mailchimp-ajax-submit.min.js"></script> --}}

    <!-- Main JS -->
    <script src="{{ asset('contents/website') }}/assets/js/main.js"></script>
</body>
<!-- Mirrored from demo.hasthemes.com/brenda-preview/brenda/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Sun, 15 Mar 2020 20:52:41 GMT -->
</html>
