@extends('layouts.adopter_master')

@section('title', 'Edit Profile')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
@endpush

@section('content')
<div class="container">
    <div class="row">
        <!-- Left Sidebar -->
        <div class="col-md-3">
            <div class="sidebar shadow p-3 rounded text-center">
                <!-- ✅ Display Profile Picture -->
                <div class="profile-image mx-auto">
                    @if($profile->profile_picture)
                    <img src="{{ asset($profile->profile_picture) }}" 
                         class="rounded-circle border border-secondary"
                         style="width: 100px; height: 100px; object-fit: cover;">
                    @else
                    <img src="{{ asset('images/blankprofile.jpg') }}" 
                         class="rounded-circle border border-secondary"
                         style="width: 100px; height: 100px; object-fit: cover;">
                    @endif
                </div>

                <h5 class="text-center mt-2"><strong>{{ Auth::user()->name }}</strong></h5>
            </div>
        </div>

        <!-- Right Profile Content -->
        <div class="col-md-9">
            <div class="card shadow-sm p-4">
                <h4 class="section-title">Edit User Information</h4>
                <form action="{{ route('adopter.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', Auth::user()->name) }}" placeholder="Enter your full name" required>
                    </div>

                    <div class="form-group mt-2">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" value="{{ Auth::user()->email }}" placeholder="Your email address" disabled>
                    </div>

                    <div class="form-group mt-2">
                        <label for="gender">Gender</label>
                        <select class="form-control" id="gender" name="gender">
                            <option value="">Select Gender</option>
                            <option value="Male" {{ old('gender', $profile->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('gender', $profile->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                            <option value="Prefer not to say" {{ old('gender', $profile->gender) == 'Prefer not to say' ? 'selected' : '' }}>Prefer not to say</option>
                        </select>
                    </div>

                    <div class="form-group mt-2">
                        <label for="phone_number">Phone Number</label>
                        <input type="text" class="form-control" id="phone_number" name="phone_number" value="{{ old('phone_number', $profile->phone_number) }}" placeholder="e.g., +1 (555) 123-4567">
                    </div>

                    <div class="form-group mt-2">
                        <label for="address">Address</label>
                        <input type="text" class="form-control" id="address" name="address" value="{{ old('address', $profile->address) }}" placeholder="Your current address">
                    </div>

                    <div class="form-group mt-2">
                        <label for="occupation">Occupation</label>
                        <input type="text" class="form-control" id="occupation" name="occupation" value="{{ old('occupation', $profile->occupation) }}" placeholder="Your current job or profession">
                    </div>

                    <div class="form-group mb-3">
                        <label for="pet_preference">Pet Preference</label>

                        <select id="pet_preference" class="form-control" name="pet_preference">
                            <option value="" disabled selected>Select your preferred pet species</option>
                            @foreach (['Cat', 'Dog'] as $petSpecies)
                                <option value="{{ $petSpecies }}"
                                    {{ old('pet_preference', $profile->pet_preference ?? '') == $petSpecies ? 'selected' : '' }}>
                                    {{ $petSpecies }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mt-2">
                        <label for="preferred_location">Preferred Location</label>
                        <select class="form-control" id="preferred_location" name="preferred_location">
                            <option value="" disabled {{ old('preferred_location', $profile->preferred_location) == null ? 'selected' : '' }}>Select your preferred location</option>
                            <option value="Johor" {{ old('preferred_location', $profile->preferred_location) == 'Johor' ? 'selected' : '' }}>Johor</option>
                            <option value="Kedah" {{ old('preferred_location', $profile->preferred_location) == 'Kedah' ? 'selected' : '' }}>Kedah</option>
                            <option value="Kelantan" {{ old('preferred_location', $profile->preferred_location) == 'Kelantan' ? 'selected' : '' }}>Kelantan</option>
                            <option value="Kuala Lumpur" {{ old('preferred_location', $profile->preferred_location) == 'Kuala Lumpur' ? 'selected' : '' }}>Kuala Lumpur</option>
                            <option value="Melaka" {{ old('preferred_location', $profile->preferred_location) == 'Melaka' ? 'selected' : '' }}>Melaka</option>
                            <option value="Negeri Sembilan" {{ old('preferred_location', $profile->preferred_location) == 'Negeri Sembilan' ? 'selected' : '' }}>Negeri Sembilan</option>
                            <option value="Pahang" {{ old('preferred_location', $profile->preferred_location) == 'Pahang' ? 'selected' : '' }}>Pahang</option>
                            <option value="Penang" {{ old('preferred_location', $profile->preferred_location) == 'Penang' ? 'selected' : '' }}>Penang</option>
                            <option value="Perak" {{ old('preferred_location', $profile->preferred_location) == 'Perak' ? 'selected' : '' }}>Perak</option>
                            <option value="Perlis" {{ old('preferred_location', $profile->preferred_location) == 'Perlis' ? 'selected' : '' }}>Perlis</option>
                            <option value="Putrajaya" {{ old('preferred_location', $profile->preferred_location) == 'Putrajaya' ? 'selected' : '' }}>Putrajaya</option>
                            <option value="Selangor" {{ old('preferred_location', $profile->preferred_location) == 'Selangor' ? 'selected' : '' }}>Selangor</option>
                            <option value="Terengganu" {{ old('preferred_location', $profile->preferred_location) == 'Terengganu' ? 'selected' : '' }}>Terengganu</option>
                            <option value="Any Location" {{ old('preferred_location', $profile->preferred_location) == 'Any Location' ? 'selected' : '' }}>Any Location</option>
                        </select>
                    </div>

                    <div class="form-group mt-2">
                        <label for="bio">Bio</label>
                        <textarea class="form-control" id="bio" name="bio" rows="3" placeholder="Tell us a bit about yourself and why you want to adopt a pet">{{ old('bio', $profile->bio) }}</textarea>
                    </div>

                    <div class="form-group mb-3">
                        <label for="activity_level">Activity Level</label>
                        <select id="activity_level" class="form-control" name="activity_level">
                            @foreach (['Low', 'Moderate', 'High'] as $level)
                                <option value="{{ $level }}"
                                    {{ old('activity_level', $profile->activity_level ?? '') == $level ? 'selected' : '' }}>
                                    {{ $level }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="home_type">Home Type</label>
                        <select id="home_type" class="form-control" name="home_type">
                            @foreach (['Apartment / Condo', 'House with yard', 'Farm / large property'] as $type)
                                <option value="{{ $type }}"
                                    {{ old('home_type', $profile->home_type ?? '') == $type ? 'selected' : '' }}>
                                    {{ $type }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="other_pets">Do you have other pets?</label>
                        <select id="other_pets" class="form-control" name="other_pets">
                            @foreach (['None', 'Cats', 'Dogs', 'Other'] as $option)
                                <option value="{{ $option }}"
                                    {{ old('other_pets', $profile->other_pets ?? '') == $option ? 'selected' : '' }}>
                                    {{ $option }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="allergies">Do you have allergies to pets?</label>
                        <select id="allergies" class="form-control" name="allergies">
                            @foreach (['No', 'Yes'] as $option)
                                <option value="{{ $option }}"
                                    {{ old('allergies', $profile->allergies ?? '') == $option ? 'selected' : '' }}>
                                    {{ $option }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="hours_spend">Hours Spend per Day</label>
                        <input type="number" 
                            class="form-control" 
                            id="hours_spend" 
                            name="hours_per_day" 
                            min="1" 
                            max="24" 
                            value="{{ old('hours_per_day') }}" >
                    </div>


                    <div class="form-group mb-3">
                        <label for="personality_preference">Preferred Pet Personality</label>
                        <select id="personality_preference" class="form-control" name="personality_preference">
                            @foreach (['Calm', 'Playful', 'Independent', 'Affectionate', 'Energetic'] as $personality)
                                <option value="{{ $personality }}"
                                    {{ old('personality_preference', $profile->personality_preference ?? '') == $personality ? 'selected' : '' }}>
                                    {{ $personality }}</option>
                            @endforeach
                        </select>
                    </div>


                    <!-- ✅ Profile Picture Upload -->
                    <div class="form-group mt-2">
                        <label for="profile_picture">Profile Picture</label>
                        <input type="file" class="form-control" id="profile_picture" name="profile_picture" accept="image/png, image/jpeg">
                        <small class="text-muted">Accepted formats: PNG, JPG (Max: 2MB)</small>
                    </div>

                    <div class="mt-2" id="image-preview-container" style="display: none;">
                        <img id="image-preview" src="#" alt="Preview" style="max-width: 200px; max-height: 200px;" class="img-thumbnail">
                    </div>

                    <button type="submit" class="btn btn-primary mt-3">Save Changes</button>
                    <a href="{{ route('adopter.profile.view') }}" class="btn btn-secondary mt-3">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Add this script at the end of your page or in a separate JS file
    document.addEventListener('DOMContentLoaded', function () {
        const profilePictureInput = document.getElementById('profile_picture');
        const imagePreviewContainer = document.getElementById('image-preview-container');
        const imagePreview = document.getElementById('image-preview');
        const removeCheckbox = document.getElementById('remove_profile_picture');

        profilePictureInput.addEventListener('change', function () {
            if (this.files && this.files[0]) {
                const reader = new FileReader();

                reader.onload = function (e) {
                    imagePreview.src = e.target.result;
                    imagePreviewContainer.style.display = 'block';
                    // Uncheck remove checkbox if user selects a new image
                    if (removeCheckbox && removeCheckbox.checked) {
                        removeCheckbox.checked = false;
                    }
                }

                reader.readAsDataURL(this.files[0]);
            }
        });

        // Hide preview if user checks "remove picture" box
        if (removeCheckbox) {
            removeCheckbox.addEventListener('change', function () {
                if (this.checked) {
                    imagePreviewContainer.style.display = 'none';
                    profilePictureInput.value = '';
                }
            });
        }
    });
</script>
@endsection