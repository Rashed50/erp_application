@extends('layouts.app')
@section('title') Login | Asloob Bedda @endsection

@section('content')
<style>


    /* Custom Styling for the Redesign */
    .login-card {
        border: none;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    .bg-img-header {
        background: linear-gradient(rgba(0, 51, 102, 0.85), rgba(0, 51, 102, 0.85)),
                    url('https://images.unsplash.com/photo-1504307651254-35680f356dfd?auto=format&fit=crop&w=800&q=80'); /* Modern construction bg */
        background-size: cover;
        background-position: center;
        padding: 40px 20px;
    }
    .math-captcha-box {
        background: #f8f9fa;
        border: 2px dashed #dee2e6;
        border-radius: 10px;
        padding: 5px;
        margin-bottom: 5px;
    }
    .input-lg {
        padding: 12px 15px;
        font-size: 1rem;
    }
    .btn-login {
        padding: 12px;
        font-weight: 600;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        border-radius: 8px;
    }
</style>

<div class="card login-card">
    {{-- <div class="bg-img-header text-center">
        <h3 class="text-white m-0 fw-bold">ASLOOB BEDDA</h3>
        <p class="text-white-50 m-0">Payroll Management System</p>
    </div> --}}
    <img src="{{ asset('images/logo_new.png') }}" alt="ASLOOB BEDAA" >

    {{-- <h3 class="text-white m-0" style="font-weight: 600; letter-spacing: 1px;">
        PAYROLL LOGIN
    </h3> --}}

    <div class="card-body p-4 p-md-5">
        <form action="{{ route('login') }}" onsubmit="return validateForm()" method="post">
            @csrf

            {{-- Error Handling --}}
            @error('banned')
                <div class="alert alert-danger py-2 small">{{ $message }}</div>
            @enderror

            {{-- Username Field --}}
            <div class="mb-1">
                <label class="form-label fw-bold small text-muted">USER ID</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="fa fa-user text-primary"></i></span>
                    <input id="username" type="text" placeholder="Username or Email"
                           class="form-control input-lg border-start-0 @error('username') is-invalid @enderror"
                           name="username" required autofocus>
                </div>
                @error('username')
                    <span class="text-danger small"><strong>{{ $message }}</strong></span>
                @enderror
            </div>

            {{-- Password Field --}}
            <div class="mb-2">
                <label class="form-label fw-bold small text-muted">PASSWORD</label>
                <div class="input-group" id="show_hide_password">
                    <span class="input-group-text bg-white border-end-0"><i class="fa fa-lock text-primary"></i></span>
                    <input id="password" type="password" placeholder="********"
                           class="form-control input-lg border-start-0 border-end-0 @error('password') is-invalid @enderror"
                           name="password" required>
                    <span class="input-group-text bg-white border-start-0">
                        <a href="javascript:void(0)" class="text-muted" id="togglePassword"><i class="fa fa-eye-slash"></i></a>
                    </span>
                </div>
            </div>

            {{-- PHP Math Logic (Keep your existing session logic) --}}
            @php
                $first_value = rand(1,9);
                $second_value = rand(1,9);
                $operator = rand(1,3);
                $operator_symbol = '+';
                if($operator == 1) { $total = $first_value + $second_value; }
                elseif($operator == 2) { $operator_symbol = '-'; $total = $first_value - $second_value; }
                else { $operator_symbol = '×'; $total = $first_value * $second_value; }
                session(['total' => $total]);
            @endphp

            {{-- Math Captcha Section --}}
            <div class="math-captcha-box text-center">
                {{-- <label class="form-label d-block text-muted small fw-bold mb-2">SECURITY VERIFICATION</label> --}}
                <div class="d-flex align-items-center justify-content-center">
                    <span class="h4 mb-0 fw-bold text-dark">{{ $first_value }}</span>
                    <span class="mx-3 h5 mb-0 text-primary">{{ $operator_symbol }}</span>
                    <span class="h4 mb-0 fw-bold text-dark">{{ $second_value }}</span>
                    <span class="mx-3 h5 mb-0">=</span>
                    <input type="number" name="result" class="form-control text-center fw-bold"
                           style="width: 100px; border-color: #0d6efd" placeholder="Result ?" required>
                </div>
            </div>

            <div class="d-flex justify-content-between  align-items-center mb-2">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label small text-muted" for="remember">Keep me logged in</label>
                </div>
                </div>

            <div class="text-center">
                <button class="btn btn-primary btn-login btn-lg shadow-sm px-5" type="submit" style="min-width: 150px;">
                    LOGIN <i class="fa fa-sign-in-alt ms-2"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Toggle Password Visibility
document.getElementById('togglePassword').addEventListener('click', function (e) {
    const password = document.getElementById('password');
    const icon = this.querySelector('i');
    if (password.type === "password") {
        password.type = "text";
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    } else {
        password.type = "password";
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    }
});

function validateForm() {
    let user_value = document.querySelector('input[name="result"]').value;
    if (user_value != @json(session('total', 0))) {
        alert("Incorrect Security Answer. Please try again.");
        return false;
    }
    return true;
}
</script>
@endsection
