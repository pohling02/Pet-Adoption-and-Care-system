@extends('layouts.adopter_master')

@section('content')
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="row w-75 shadow-lg rounded bg-white">
            <div class="col-md-6 p-4">
                <h3 class="text-center fw-bold mb-3">Sign Up</h3>
                <p class="text-center">Already a member? <a href="{{ route('adopter.login') }}">Log In</a></p>
                <p class="text-center text-muted small mb-4">Fields marked with * are required</p>

                <form method="POST" action="{{ route('adopter.register') }}" enctype="multipart/form-data">
                    @csrf

                    <!-- Profile Picture -->
                    <div class="text-center mb-4">
                        <img src="{{ asset('images/blankprofile.jpg') }}" id="profilePreview"
                            class="rounded-circle border border-secondary"
                            style="width: 120px; height: 120px; object-fit: cover;">
                    </div>

                    <div class="form-group mb-4">
                        <label for="profile_picture">Profile Picture</label>
                        <input type="file" class="form-control @error('profile_picture') is-invalid @enderror"
                            name="profile_picture" id="profile_picture" accept="image/png, image/jpeg">
                        <small class="text-muted">Allowed formats: PNG, JPG (Max: 2MB)</small>
                        @error('profile_picture')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- ========== Personal Information Section ========== -->
                    <hr class="my-4">
                    <h5 class="fw-semibold mb-3">Personal Information</h5>

                    <div class="form-group mb-4">
                        <label for="name">Full Name *</label>
                        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                            name="name" value="{{ old('name') }}" required placeholder="Enter your full name">
                        @error('name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label for="gender">Gender *</label>
                        <select id="gender" class="form-control @error('gender') is-invalid @enderror" name="gender">
                            <option value="" disabled selected>Select gender</option>
                            <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                            <option value="Prefer not to say" {{ old('gender') == 'Prefer not to say' ? 'selected' : '' }}>Prefer not to say</option>
                        </select>
                        @error('gender')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- ========== Account Security Section ========== -->
                    <hr class="my-4">
                    <h5 class="fw-semibold mb-3">Account Security</h5>

                    <div class="form-group mb-4">
                        <label for="email">Email Address *</label>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                            name="email" value="{{ old('email') }}" required placeholder="your.email@example.com">
                        @error('email')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label for="password">Password *</label>
                        <div class="input-group">
                            <input id="password" type="password"
                                class="form-control @error('password') is-invalid @enderror" name="password" required
                                placeholder="Create a strong password">
                            <button class="btn btn-outline-secondary" type="button"
                                onclick="togglePasswordVisibility('password', 'passwordToggleIcon')">
                                <i id="passwordToggleIcon" class="fas fa-eye"></i>
                            </button>
                        </div>
                        <small class="text-muted">At least 8 characters with uppercase, lowercase, number, and special character.</small>
                        @error('password')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label for="password_confirmation">Confirm Password *</label>
                        <div class="input-group">
                            <input id="password_confirmation" type="password" class="form-control"
                                name="password_confirmation" required placeholder="Confirm your password">
                            <button class="btn btn-outline-secondary" type="button"
                                onclick="togglePasswordVisibility('password_confirmation', 'confirmPasswordToggleIcon')">
                                <i id="confirmPasswordToggleIcon" class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <!-- ========== Contact Details Section ========== -->
                    <hr class="my-4">
                    <h5 class="fw-semibold mb-3">Contact Details</h5>

                    <div class="form-group mb-4">
                        <label for="phone_number">Phone Number</label>
                        <input id="phone_number" type="text" class="form-control" name="phone_number"
                            value="{{ old('phone_number') }}" placeholder="e.g., +6012-4511237">
                    </div>

                    <div class="form-group mb-4">
                        <label for="address">Address</label>
                        <textarea id="address" class="form-control" name="address" placeholder="Enter your complete address">{{ old('address') }}</textarea>
                    </div>

                    <div class="form-group mb-4">
                        <label for="occupation">Occupation</label>
                        <input id="occupation" type="text" class="form-control" name="occupation"
                            value="{{ old('occupation') }}" placeholder="Your Occupation?">
                    </div>

                    <!-- ========== Pet Preferences Section ========== -->
                    <hr class="my-4">
                    <h5 class="fw-semibold mb-3">Pet Preferences</h5>

                    <div class="form-group mb-4">
                        <label for="pet_preference">Preferred Pet Species</label>
                        <select id="pet_preference" class="form-control" name="pet_preference">
                            <option value="" disabled selected>Select your preferred pet species</option>
                            @foreach (['Cat', 'Dog'] as $petSpecies)
                                <option value="{{ $petSpecies }}"
                                    {{ old('pet_preference', $adopterProfile->pet_preference ?? '') == $petSpecies ? 'selected' : '' }}>
                                    {{ $petSpecies }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-4">
                        <label for="preferred_location">Preferred Location</label>
                        <select id="preferred_location" class="form-control" name="preferred_location">
                            <option value="" disabled selected>Select your preferred location</option>
                            @foreach (['Johor', 'Kedah', 'Kelantan', 'Kuala Lumpur', 'Melaka', 'Negeri Sembilan', 'Pahang', 'Penang', 'Perak', 'Perlis', 'Putrajaya', 'Selangor', 'Terengganu', 'Any Location'] as $location)
                                <option value="{{ $location }}"
                                    {{ old('preferred_location', $adopterProfile->preferred_location ?? '') == $location ? 'selected' : '' }}>
                                    {{ $location }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-4">
                        <label for="personality_preference">Preferred Pet Personality</label>
                        <select id="personality_preference" class="form-control" name="personality_preference">
                            @foreach (['Calm', 'Playful', 'Independent', 'Affectionate', 'Energetic'] as $personality)
                                <option value="{{ $personality }}"
                                    {{ old('personality_preference', $adopterProfile->personality_preference ?? '') == $personality ? 'selected' : '' }}>
                                    {{ $personality }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- ========== Lifestyle Section ========== -->
                    <hr class="my-4">
                    <h5 class="fw-semibold mb-3">Lifestyle Information</h5>

                    <div class="form-group mb-4">
                        <label for="activity_level">Activity Level</label>
                        <select id="activity_level" class="form-control" name="activity_level">
                            @foreach (['Low', 'Moderate', 'High'] as $level)
                                <option value="{{ $level }}"
                                    {{ old('activity_level', $adopterProfile->activity_level ?? '') == $level ? 'selected' : '' }}>
                                    {{ $level }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-4">
                        <label for="home_type">Home Type</label>
                        <select id="home_type" class="form-control" name="home_type">
                            @foreach (['Apartment / Condo', 'House with yard', 'Farm / large property'] as $type)
                                <option value="{{ $type }}"
                                    {{ old('home_type', $adopterProfile->home_type ?? '') == $type ? 'selected' : '' }}>
                                    {{ $type }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-4">
                        <label for="other_pets">Do you have other pets?</label>
                        <select id="other_pets" class="form-control" name="other_pets">
                            @foreach (['None', 'Cats', 'Dogs', 'Other'] as $option)
                                <option value="{{ $option }}"
                                    {{ old('other_pets', $adopterProfile->other_pets ?? '') == $option ? 'selected' : '' }}>
                                    {{ $option }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-4">
                        <label for="allergies">Do you have allergies to pets?</label>
                        <select id="allergies" class="form-control" name="allergies">
                            @foreach (['No', 'Yes'] as $option)
                                <option value="{{ $option }}"
                                    {{ old('allergies', $adopterProfile->allergies ?? '') == $option ? 'selected' : '' }}>
                                    {{ $option }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-4">
                        <label for="hours_per_day">Hours per day you can spend with your pet</label>
                        <input id="hours_per_day" type="number" class="form-control" name="hours_per_day"
                            min="0" max="24"
                            value="{{ old('hours_per_day', $adopterProfile->hours_per_day ?? '') }}">
                    </div>

                    <div class="form-group mb-4">
                        <label for="bio">Bio</label>
                        <textarea id="bio" class="form-control" name="bio" rows="3"
                            placeholder="Tell us a little about yourself and why you want to adopt a pet">{{ old('bio', $adopterProfile->bio ?? '') }}</textarea>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-success w-100 py-2">Sign Up</button>
                    </div>
                </form>
            </div>

            <!-- Right Column -->
            <div class="col-md-6 p-4 text-center d-flex flex-column justify-content-center align-items-center bg-light rounded-end">
                <img src="{{ asset('images/an.jpg') }}" class="img-fluid rounded-circle mb-3" alt="Petopia">
                <h3 class="fw-bold">Petopia</h3>
                <p class="text-muted">"Where every paw finds a place to call home."</p>
                <div class="mt-4">
                    <p>Join our community of pet lovers and find your perfect companion!</p>
                    <p>By signing up, you'll be able to:</p>
                    <ul class="text-start">
                        <li>Browse available pets for adoption</li>
                        <li>Save your favorite pets</li>
                        <li>Apply for adoption</li>
                        <li>Connect with other pet owners</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <!-- JS for image preview + password toggle -->
    <script>
        document.getElementById("profile_picture").addEventListener("change", function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById("profilePreview").src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });

        function togglePasswordVisibility(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>
@endsection
