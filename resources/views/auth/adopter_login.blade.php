@extends('layouts.adopter_master')

@section('content')
<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="row w-75 shadow-lg rounded bg-white">
        <!-- Left Side (Image & Branding) -->
        <div class="col-md-6 p-4 text-center d-flex flex-column justify-content-center align-items-center">
            <img src="{{ asset('images/an.jpg') }}" class="img-fluid rounded-circle mb-3" alt="Petopia">
            <h3 class="fw-bold">Petopia</h3>
            <p class="text-muted">"Where every paw finds a place to call home."</p>
        </div>

        <!-- Right Side (Login Form) -->
        <div class="col-md-6 p-4">
            <h3 class="text-center fw-bold">Log In</h3>
            <p class="text-center">
                New to this site? <a href="{{ route('adopter.register.form') }}">Sign Up</a>
            </p>

            <!-- Display login error message -->
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <!-- Display validation errors -->
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                          <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('adopter.login') }}">
                @csrf

                <div class="form-group mb-3">
                    <label for="email">Email</label>
                    <input 
                        id="email" 
                        type="email" 
                        class="form-control @error('email') is-invalid @enderror"
                        name="email" 
                        value="{{ old('email') }}" 
                        required 
                        autofocus
                    >
                    @error('email')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="password">Password</label>
                    <div class="input-group">
                        <input 
                            id="password" 
                            type="password" 
                            class="form-control @error('password') is-invalid @enderror"
                            name="password" 
                            required
                        >
                        <!-- Make it look clickable with cursor: pointer -->
                        <span 
                            class="input-group-text bg-light border-start-0" 
                            style="cursor: pointer;" 
                            id="togglePasswordIcon"
                        >
                            <i class="bi bi-eye-slash"></i>
                        </span>
                    </div>
                    @error('password')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="text-end mb-3">
                    <a href="{{ route('adopter.password.request') }}">Forgot password?</a>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary w-100">Log In</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Inline script that attaches the event listener after the DOM is rendered -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const passwordField = document.getElementById("password");
    const toggleSpan    = document.getElementById("togglePasswordIcon");
    const toggleIcon    = toggleSpan.querySelector("i");

    toggleSpan.addEventListener("click", function() {
        if (passwordField.type === "password") {
            passwordField.type = "text";  // Show password
            toggleIcon.classList.remove("bi-eye-slash");
            toggleIcon.classList.add("bi-eye");
        } else {
            passwordField.type = "password";  // Hide password
            toggleIcon.classList.remove("bi-eye");
            toggleIcon.classList.add("bi-eye-slash");
        }
    });
});
</script>
@endsection
