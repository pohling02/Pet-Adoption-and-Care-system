@extends('layouts.adopter_master')

@section('title', 'Reset Password')

@section('content')
<section class="bg-light min-vh-100 d-flex align-items-center justify-content-center py-3 py-md-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5 col-xxl-4">
                <div class="card border border-light-subtle rounded-3 shadow-sm">
                    <div class="card-body p-3 p-md-4 p-xl-5">
                        <div class="text-center mb-3">
                            <a href="{{ route('adopter.home') }}">
                                <img src="{{ asset('images/logo.png') }}" alt="Petopia Logo" class="img-fluid" style="max-width: 150px;">
                            </a>
                        </div>

                        <h2 class="fs-5 fw-bold text-center mb-2">Reset Your Password</h2>
                        <p class="text-center text-secondary mb-3">
                            Create a new secure password for your Petopia account
                        </p>

                        <!-- Password Reset Instructions -->
                        <div class="alert alert-info mb-4">
                            <h5 class="alert-heading fs-6"><i class="bi bi-info-circle-fill me-1"></i> How to Reset Your Password</h5>
                            <hr>
                            <ol class="mb-0 ps-3">
                                <li>Your email address is already filled in</li>
                                <li>Create a new strong password (8+ characters)</li>
                                <li>Type the same password again to confirm</li>
                                <li>Click the "Reset Password" button</li>
                            </ol>
                            <div class="mt-2 small">
                                <i class="bi bi-lightbulb-fill me-1 text-warning"></i> 
                                You can click the eye icon <i class="bi bi-eye"></i> to show or hide your password
                            </div>
                        </div>

                        @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <form method="POST" action="{{ route('adopter.password.update') }}">
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}">

                            <div class="row gy-3 overflow-hidden">
                                <!-- Email (readonly) -->
                                <div class="col-12">
                                    <label for="email" class="form-label fw-medium">Your Email</label>
                                    <div class="form-floating">
                                        <input type="email" class="form-control" name="email" id="email" value="{{ old('email', $email) }}" required readonly>
                                        <label for="email" class="form-label">Email Address</label>
                                    </div>
                                    <small class="text-muted">This is the email linked to your account</small>
                                </div>

                                <!-- Password Requirements -->
                                <div class="col-12">
                                    <div class="password-requirements p-3 bg-light border rounded">
                                        <p class="mb-1 fw-medium"><i class="bi bi-shield-lock me-1"></i> Strong Password Tips:</p>
                                        <ul class="ps-3 mb-0 small">
                                            <li>Use at least 8 characters</li>
                                            <li>Include uppercase letters (A-Z)</li>
                                            <li>Include lowercase letters (a-z)</li>
                                            <li>Include numbers (0-9)</li>
                                            <li>Include special characters (!@#$%^&*)</li>
                                            <li>Avoid using personal information</li>
                                        </ul>
                                    </div>
                                </div>
                                
                                <!-- New Password Field with Toggle -->
                                <div class="col-12">
                                    <label for="password" class="form-label fw-medium">New Password <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="form-floating flex-grow-1">
                                            <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" id="password" placeholder="New Password" required>
                                            <label for="password" class="form-label">New Password</label>
                                        </div>
                                        <span class="input-group-text" id="togglePassword" style="cursor: pointer;" title="Show/Hide Password">
                                            <i class="bi bi-eye"></i>
                                        </span>
                                    </div>
                                    <small class="text-muted">Enter your new password</small>
                                    @error('password')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                
                                <!-- Confirm Password Field with Toggle -->
                                <div class="col-12">
                                    <label for="password_confirmation" class="form-label fw-medium">Confirm Password <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="form-floating flex-grow-1">
                                            <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" placeholder="Confirm Password" required>
                                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                                        </div>
                                        <span class="input-group-text" id="togglePasswordConfirmation" style="cursor: pointer;" title="Show/Hide Password">
                                            <i class="bi bi-eye"></i>
                                        </span>
                                    </div>
                                    <small class="text-muted">Type your new password again to confirm</small>
                                </div>
                                
                                <div class="col-12">
                                    <div class="d-grid my-3">
                                        <button class="btn btn-dark btn-lg" type="submit">
                                            <i class="bi bi-check-circle me-1"></i> Reset Password
                                        </button>
                                        <small class="text-center mt-2 text-muted">You'll be redirected to login after resetting</small>
                                    </div>
                                </div>
                                
                                <div class="col-12">
                                    <div class="d-grid">
                                        <a href="{{ route('adopter.login') }}" class="btn btn-outline-dark">
                                            <i class="bi bi-arrow-left me-1"></i> Back to Login
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Include Bootstrap Icons if not already included -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<script>
    // Toggle for New Password field
    const togglePassword = document.querySelector('#togglePassword');
    const passwordInput = document.querySelector('#password');

    togglePassword.addEventListener('click', function () {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.querySelector('i').classList.toggle('bi-eye');
        this.querySelector('i').classList.toggle('bi-eye-slash');
    });

    // Toggle for Confirm Password field
    const togglePasswordConfirmation = document.querySelector('#togglePasswordConfirmation');
    const passwordConfirmationInput = document.querySelector('#password_confirmation');

    togglePasswordConfirmation.addEventListener('click', function () {
        const type = passwordConfirmationInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordConfirmationInput.setAttribute('type', type);
        this.querySelector('i').classList.toggle('bi-eye');
        this.querySelector('i').classList.toggle('bi-eye-slash');
    });
</script>
@endsection