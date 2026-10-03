@extends('layouts.shelter_master')

@section('content')
<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="row w-75 shadow-lg rounded bg-white">
        <div class="col-md-6 p-4">
            <h3 class="text-center fw-bold">Shelter Staff Sign Up</h3>
            <p class="text-center">Already a member? <a href="{{ route('shelter.login') }}">Log In</a></p>

            <!-- Added Registration Instructions -->
            <div class="alert alert-info mb-4">
                <h5 class="alert-heading"><i class="fas fa-info-circle"></i> Registration Instructions</h5>
                <hr>
                <ol class="mb-0">
                    <li>Fill in all required fields marked with <span class="text-danger">*</span></li>
                    <li>Upload your business license (PDF, PNG, or JPG, max 2MB)</li>
                    <li>Add a profile picture (optional)</li>
                    <li>Create a secure password (at least 8 characters)</li>
                    <li>Complete the reCAPTCHA verification</li>
                    <li>Click "Sign Up" to submit your application</li>
                </ol>
                <p class="mt-2 mb-0"><small>Note: Your registration will be reviewed by our admin team before approval.</small></p>
            </div>

            <!-- Show validation errors -->
            @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                    <li><i class="fas fa-exclamation-circle"></i> {{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            <form method="POST" action="{{ route('shelter.register') }}" enctype="multipart/form-data">
                @csrf
                <!-- Full Name -->
                <div class="form-group mb-3">
                    <label for="name">Full Name <span class="text-danger">*</span></label>
                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required>
                    @error('name')
                    <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Email -->
                <div class="form-group mb-3">
                    <label for="email">Email <span class="text-danger">*</span></label>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required>
                    <small class="text-muted">This will be used for login and communications</small>
                    @error('email')
                    <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Phone Number -->
                <div class="form-group mb-3">
                    <label for="phone_number">Phone Number <span class="text-danger">*</span></label>
                    <input id="phone_number" type="text" class="form-control @error('phone_number') is-invalid @enderror" name="phone_number" value="{{ old('phone_number') }}" required placeholder="e.g., 0184567890">
                    @error('phone_number')
                    <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Shelter Name -->
                <div class="form-group mb-3">
                    <label for="shelter_name">Shelter Name <span class="text-danger">*</span></label>
                    <input id="shelter_name" type="text" class="form-control @error('shelter_name') is-invalid @enderror" name="shelter_name" value="{{ old('shelter_name') }}" required placeholder="e.g., JB HOPE">
                    @error('shelter_name')
                    <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Shelter Address -->
                <div class="form-group mb-3">
                    <label for="shelter_address">Shelter Address <span class="text-danger">*</span></label>
                    <textarea id="shelter_address" class="form-control @error('shelter_address') is-invalid @enderror" name="shelter_address" required placeholder="Street address, City, State, ZIP">{{ old('shelter_address') }}</textarea>
                    @error('shelter_address')
                    <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Position (Optional) -->
                <div class="form-group mb-3">
                    <label for="position">Position (Optional)</label>
                    <input id="position" type="text" class="form-control @error('position') is-invalid @enderror" name="position" value="{{ old('position') }}" placeholder="e.g., Shelter Manager, Volunteer Coordinator">
                    @error('position')
                    <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Bio (Optional) -->
                <div class="form-group mb-3">
                    <label for="bio">Bio (Optional)</label>
                    <textarea id="bio" class="form-control @error('bio') is-invalid @enderror" name="bio" placeholder="Brief description about yourself or your shelter">{{ old('bio') }}</textarea>
                    @error('bio')
                    <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Business License Upload -->
                <div class="form-group mb-3">
                    <label for="business_license">Business License <span class="text-danger">*</span> (PDF, PNG, JPG)</label>
                    <input type="file" id="business_license" class="form-control @error('business_license') is-invalid @enderror" name="business_license" accept=".pdf,.png,.jpg,.jpeg" required>
                    <small class="text-muted">Upload your shelter's business license or certification document (Max file size: 2MB)</small>
                    @error('business_license')
                    <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Profile Picture Upload with Preview -->
                <div class="form-group mb-3">
                    <label for="profile_picture" class="form-label d-block">Profile Picture (Optional)</label>
                    <div class="row align-items-center">
                        <!-- Circle Preview -->
                        <div class="col-auto">
                            <div id="profilePreview"
                                 class="rounded-circle me-3"
                                 style="width: 80px; height: 80px;
                                 background-color: #f0f0f0;
                                 background-size: cover;
                                 background-position: center;
                                 background-repeat: no-repeat;
                                 border: 2px solid #ddd;">
                            </div>
                        </div>
                        <!-- File Input -->
                        <div class="col">
                            <input type="file"
                                   id="profile_picture"
                                   class="form-control @error('profile_picture') is-invalid @enderror"
                                   name="profile_picture"
                                   accept="image/png, image/jpeg">
                            <small class="text-muted">Click "Choose File" to upload a profile photo (Accepted formats: PNG, JPG, max 2MB)</small>
                            @error('profile_picture')
                            <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Gender Selection -->
                <div class="form-group mb-3">
                    <label for="gender">Gender <span class="text-danger">*</span></label>
                    <select id="gender" class="form-select @error('gender') is-invalid @enderror" name="gender" required>
                        <option value="" selected disabled>Select your gender</option>
                        <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                        <option value="Prefer not to say" {{ old('gender') == 'Prefer not to say' ? 'selected' : '' }}>Prefer not to say</option>
                    </select>
                    @error('gender')
                    <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Password -->
                <div class="form-group mb-3">
                    <label for="password">Password <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input id="password"
                               type="password"
                               class="form-control @error('password') is-invalid @enderror"
                               name="password"
                               required>
                        <button class="btn btn-outline-secondary" type="button"
                                onclick="togglePasswordVisibility('password', 'passwordToggleIcon')">
                            <i id="passwordToggleIcon" class="fas fa-eye"></i>
                        </button>
                    </div>
                    <small class="text-muted">Must be at least 8 characters with a mix of letters, numbers, and symbols</small>
                    @error('password')
                    <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div class="form-group mb-3">
                    <label for="password_confirmation">Confirm Password <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input id="password_confirmation"
                               type="password"
                               class="form-control @error('password_confirmation') is-invalid @enderror"
                               name="password_confirmation"
                               required>
                        <button class="btn btn-outline-secondary" type="button"
                                onclick="togglePasswordVisibility('password_confirmation', 'confirmPasswordToggleIcon')">
                            <i id="confirmPasswordToggleIcon" class="fas fa-eye"></i>
                        </button>
                    </div>
                    <small class="text-muted">Re-enter your password to confirm</small>
                    @error('password_confirmation')
                    <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Visible reCAPTCHA v2 widget -->
                <div class="form-group mb-3">
                    <div class="g-recaptcha" data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"></div>
                    <small class="text-muted">Please complete the CAPTCHA verification</small>
                    @error('g-recaptcha-response')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                    @error('captcha')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="text-center">
                    <button type="submit" class="btn btn-success w-100">Sign Up</button>
                    <small class="d-block mt-2 text-muted">By signing up, you agree to our Terms of Service and Privacy Policy</small>
                </div>
            </form>
        </div>

        <!-- Image & Branding -->
        <div class="col-md-6 p-4 text-center d-flex flex-column justify-content-center align-items-center">
            <img src="{{ asset('images/huan.jpg') }}" class="img-fluid rounded-circle mb-3" alt="Petopia">
            <h3 class="fw-bold">Petopia</h3>
            <p class="text-muted">"Where every paw finds a place to call home."</p>
        </div>
    </div>
</div>

<!-- reCAPTCHA v2 Script -->
<script src="https://www.google.com/recaptcha/api.js" async defer></script>

<script>
                                    document.getElementById('profile_picture').addEventListener('change', function (event) {
                                        var preview = document.getElementById('profilePreview');
                                        var file = event.target.files[0];
                                        if (file) {
                                            var reader = new FileReader();
                                            reader.onload = function (e) {
                                                preview.style.backgroundImage = 'url(' + e.target.result + ')';
                                            }
                                            reader.readAsDataURL(file);
                                        } else {
                                            // If no file is chosen, reset to default
                                            preview.style.backgroundImage = '';
                                        }
                                    });

                                    function togglePasswordVisibility(inputId, iconId) {
                                        var input = document.getElementById(inputId);
                                        var icon = document.getElementById(iconId);

                                        if (input.type === "password") {
                                            input.type = "text";
                                            icon.classList.remove("fa-eye");
                                            icon.classList.add("fa-eye-slash");
                                        } else {
                                            input.type = "password";
                                            icon.classList.remove("fa-eye-slash");
                                            icon.classList.add("fa-eye");
                                        }
                                    }
</script>
@endsection