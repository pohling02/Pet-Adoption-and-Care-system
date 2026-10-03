@extends('layouts.shelter_master')
@section('title', 'Shelter Staff Login')

@push('styles')
<style>
    .login-container {
        min-height: calc(100vh - 180px);
        padding: 40px 0;
    }

    .login-card {
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        border: none;
        overflow: hidden;
        max-width: 450px;
        margin: 0 auto;
    }

    .card-header-custom {
        background-color: var(--petopia-blue);
        color: white;
        text-align: center;
        padding: 2rem;
    }

    .shield-icon {
        font-size: 2rem;
        background: rgba(255, 255, 255, 0.2);
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        margin: 0 auto 1rem;
    }

    .form-floating > label {
        padding-left: 1rem;
    }

    .form-control {
        padding: 1rem;
        height: auto;
        border-radius: 8px;
        background-color: #f0f4f8;
        border: 1px solid #e0e6ed;
    }

    .form-control:focus {
        background-color: #fff;
    }

    .btn-login {
        background-color: var(--petopia-blue);
        color: white;
        border: none;
        border-radius: 8px;
        padding: 0.75rem;
        font-weight: 600;
        transition: all 0.3s;
    }

    .btn-login:hover {
        background-color: var(--petopia-blue-dark);
        transform: translateY(-2px);
    }

    .form-check-input:checked {
        background-color: var(--petopia-blue);
        border-color: var(--petopia-blue);
    }

    .forgot-password {
        color: var(--petopia-blue);
    }

    .signup-link {
        color: var(--petopia-blue);
        font-weight: 600;
    }
</style>
@endpush

@section('content')
<div class="login-container d-flex align-items-center justify-content-center">
    <div class="container">
        <div class="login-card">
            <!-- Card Header with Icon -->
            <div class="card-header-custom">
                <div class="shield-icon">
                    <i class="bi bi-shield-lock"></i>
                </div>
                <h2 class="fs-4 mb-1">Staff Portal</h2>
                <p class="mb-0 opacity-75">Access your shelter management dashboard</p>
            </div>

            <!-- Login Form -->
            <div class="card-body p-4">
                @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                <form method="POST" action="{{ route('shelter.login') }}">
                    @csrf
                    <!-- Email Field -->
                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-envelope text-muted"></i>
                            </span>
                            <input type="email" class="form-control border-start-0" 
                                   name="email" id="email" placeholder="Email Address" 
                                   value="{{ old('email') }}" required>
                        </div>
                    </div>

                    <!-- Password Field -->
                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-lock text-muted"></i>
                            </span>
                            <input type="password" class="form-control border-start-0" 
                                   name="password" id="password" placeholder="Password" required>
                            <span class="input-group-text bg-light border-start-0" onclick="togglePassword()">
                                <i class="bi bi-eye-slash" id="togglePasswordIcon"></i>
                            </span>
                        </div>
                    </div>

                    <!-- Remember Me and Forgot Password -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="remember" name="remember">
                            <label class="form-check-label text-muted" for="remember">
                                Remember me
                            </label>
                        </div>
                        <a href="{{ route('shelter.password.request') }}" class="forgot-password">
                            Forgot password?
                        </a>
                    </div>

                    <!-- Sign In Button -->
                    <div class="mb-4">
                        <button type="submit" class="btn btn-login w-100 d-flex align-items-center justify-content-center">
                            <i class="bi bi-box-arrow-in-right me-2"></i> Sign In
                        </button>
                    </div>

                    <!-- Sign Up Link -->
                    <div class="text-center">
                        <p class="mb-0 text-muted">Don't have an account? <a href="{{ route('shelter.register') }}" class="signup-link">Sign Up</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function togglePassword() {
        const passwordField = document.getElementById("password");
        const toggleIcon = document.getElementById("togglePasswordIcon");

        if (passwordField.type === "password") {
            passwordField.type = "text";
            toggleIcon.classList.remove("bi-eye-slash");
            toggleIcon.classList.add("bi-eye");
        } else {
            passwordField.type = "password";
            toggleIcon.classList.remove("bi-eye");
            toggleIcon.classList.add("bi-eye-slash");
        }
    }
</script>
@endpush
@endsection