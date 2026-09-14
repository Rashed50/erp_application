<!DOCTYPE html>
<html lang="en">
<head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta content="" name="description" />
        <meta content="Uzzal" name="author" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title')</title>
        <link href="{{asset('contents/admin')}}/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="{{asset('contents/admin')}}/assets/css/icons.css" rel="stylesheet" type="text/css" />
        <link href="{{asset('contents/admin')}}/assets/css/moltran.css" rel="stylesheet" type="text/css" />
        <link href="{{asset('contents/admin')}}/assets/css/style.css" rel="stylesheet" type="text/css" />
        <script src="{{asset('contents/admin')}}/assets/js/modernizr.min.js"></script>


    </head>
    <body>
        <div class="wrapper-page">
            <div class="card card-pages">
                @yield('content')
            </div>
        </div>

        <script src="{{asset('contents/admin')}}/assets/js/jquery.min.js"></script>
        <script src="{{asset('contents/admin')}}/assets/js/bootstrap.bundle.min.js"></script>
        <script src="{{asset('contents/admin')}}/assets/js/detect.js"></script>
        <script src="{{asset('contents/admin')}}/assets/js/fastclick.js"></script>
        <script src="{{asset('contents/admin')}}/assets/js/jquery.slimscroll.js"></script>
        <script src="{{asset('contents/admin')}}/assets/js/jquery.blockUI.js"></script>
        <script src="{{asset('contents/admin')}}/assets/js/waves.js"></script>
        <script src="{{asset('contents/admin')}}/assets/js/wow.min.js"></script>
        <script src="{{asset('contents/admin')}}/assets/js/jquery.nicescroll.js"></script>
        <script src="{{asset('contents/admin')}}/assets/js/jquery.scrollTo.min.js"></script>
        <script src="{{asset('contents/admin')}}/assets/js/jquery.app.js"></script>
        <script src="{{asset('contents/admin')}}/assets/js/jquery.app.js"></script>



    <script>
            var resizefunc = [];
            // Login Page Email Input lowercase conversion


                $(document).ready(function() {



                    $('#username').keyup(function() {
                            this.value = this.value.toLocaleLowerCase();
                            const emailInput = document.getElementById('username');
                            const emailValue = emailInput.value;
                            const errorSpan = document.getElementById('email-error');
                            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                            const isValid = re.test(String(emailValue).toLowerCase());

                            if (isValid == false) {
                                // 1. Prevent the default form submission
                                event.preventDefault();
                                // 2. Show the error message
                                emailInput.classList.add('is-invalid'); // Optional: for styling
                                errorSpan.style.display = 'inline';
                                return false;
                            } else {
                                // Validation passed
                                emailInput.classList.remove('is-invalid');
                                errorSpan.style.display = 'none';
                                return true;
                            }
                    });

                    // current pass field
                    $("#show_hide_password1 a").on('click', function(event) {
                        event.preventDefault();
                        if ($('#show_hide_password1 input').attr("type") == "text") {
                            $('#show_hide_password1 input').attr('type', 'password');
                            $('#show_hide_password1 i').addClass("fa-eye-slash");
                            $('#show_hide_password1 i').removeClass("fa-eye");
                        } else if ($('#show_hide_password1 input').attr("type") == "password") {
                            $('#show_hide_password1 input').attr('type', 'text');
                            $('#show_hide_password1 i').removeClass("fa-eye-slash");
                            $('#show_hide_password1 i').addClass("fa-eye");
                        }
                    });

            });



    </script>
	</body>
</html>


