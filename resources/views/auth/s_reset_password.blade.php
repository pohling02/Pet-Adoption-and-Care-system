@extends('layouts.shelter_master')

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
                        <p class="text-center text-secondary mb-4">
                            Create a new password for your account
                        </p>

                        <!-- Password Reset Instructions -->
                        <div class="alert alert-info mb-4">
                            <h5 class="alert-heading fs-6"><i class="bi bi-info-circle"></i> Instructions</h5>
                            <hr>
                            <ol class="mb-0 ps-3">
                                <li>Your email is pre-filled and cannot be changed</li>
                                <li>Create a strong password (at least 8 characters)</li>
                                <li>Include a mix of letters, numbers, and symbols</li>
                                <li>Confirm your password by typing it again</li>
                                <li>Click "Reset Password" to save changes</li>
                            </ol>
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
                                    <div class="form-floating">
                                        <input type="email" class="form-control" name="email" id="email" value="{{ old('email', $email) }}" required readonly>
                                        <label for="email" class="form-label">Email</label>
                                    </div>
                                    <small class="text-muted">This is the email associated with your account</small>
                                </div>

                                <!-- Password Requirements -->
                                <div class="col-12">
                                    <div class="password-requirements p-2 bg-light border rounded">
                                        <p class="mb-1 fw-medium"><i class="bi bi-shield-lock"></i> Password Requirements:</p>
                                        <ul class="ps-3 mb-0 small">
                                            <li>At least 8 characters long</li>
                                            <li>Include uppercase and lowercase letters</li>
                                            <li>Include at least one number</li>
                                            <li>Include at least one special character (!@#$%^&*)</li>
                                        </ul>
                                    </div>
                                </div>
                                
                                <!-- New Password Field with Toggle -->
                                <div class="col-12">
                                    <label for="password" class="form-label">New Password <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" id="password" placeholder="Enter your new password" required>
                                        <span class="input-group-text" id="togglePassword" style="cursor: pointer;" title="Show/Hide Password">
                                            <i class="bi bi-eye"></i>
                                        </span>
                                    </div>
                                    <small class="text-muted">Create a new password for your account</small>
                                    @error('password')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                
                                <!-- Confirm Password Field with Toggle -->
                                <div class="col-12">
                                    <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" placeholder="Re-enter your new password" required>
                                        <span class="input-group-text" id="togglePasswordConfirmation" style="cursor: pointer;" title="Show/Hide Password">
                                            <i class="bi bi-eye"></i>
                                        </span>
                                    </div>
                                    <small class="text-muted">Type your new password again to confirm</small>
                                </div>
                                
                                <!-- Submit Button -->
                                <div class="col-12">
                                    <div class="d-grid my-3">
                                        <button class="btn btn-lg" style="background-color: #34495E; color: white;" type="submit">Reset Password</button>
                                    </div>
                                </div>
                                
                                <!-- Back to Login Link -->
                                <div class="col-12">
                                    <div class="d-grid">
                                        <a href="{{ route('shelter.login') }}" class="btn btn-outline-dark">Back to Login</a>
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
@endsection

@section('scripts')
<!-- Include Bootstrap Icons if not already loaded -->
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