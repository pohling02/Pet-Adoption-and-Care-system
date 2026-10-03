@extends('layouts.shelter_master')

@section('title', 'Edit Shelter Staff Profile')

@section('content')
<div class="container mt-4">
    <h2 class="text-center text-white bg-primary p-3 rounded">Edit Profile</h2>

    <div class="card shadow-sm p-4">
        <form method="POST" action="{{ route('shelter.profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="text-center mb-4">
                @php
                $profilePicturePath = ($profile && $profile->profile_picture) 
                ? asset($profile->profile_picture) 
                : asset('images/blankprofile.jpg');
                @endphp

                <!-- Added ID for preview -->
                <img id="profile-picture-preview" src="{{ $profilePicturePath }}" 
                     class="rounded-circle border border-secondary"
                     style="width: 120px; height: 120px; object-fit: cover;">
            </div>

            <div class="mb-3">
                <label class="form-label">Upload New Profile Picture</label>
                <!-- Added id attribute for JavaScript targeting -->
                <input type="file" class="form-control" id="profile_picture" name="profile_picture" accept="image/png, image/jpeg">
                <small class="text-muted">Allowed formats: PNG, JPG (Max: 2MB)</small>
            </div>

            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" class="form-control" name="name" value="{{ old('name', $user->name) }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Phone Number</label>
                @php
                $phoneNumber = optional($profile)->phone_number;
                @endphp
                <input type="text" class="form-control" name="phone_number" value="{{ old('phone_number', $phoneNumber) }}">
            </div>

            <div class="mb-3">
                <label class="form-label">Gender</label>
                <select class="form-select" name="gender">
                    <option value="" {{ !optional($profile)->gender ? 'selected' : '' }}>Select your gender</option>
                    <option value="Male" {{ optional($profile)->gender == 'Male' ? 'selected' : '' }}>Male</option>
                    <option value="Female" {{ optional($profile)->gender == 'Female' ? 'selected' : '' }}>Female</option>
                    <option value="Prefer not to say" {{ optional($profile)->gender == 'Prefer not to say' ? 'selected' : '' }}>Prefer not to say</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Shelter Name</label>
                @php
                $shelterName = optional($profile)->shelter_name;
                @endphp
                <input type="text" class="form-control" name="shelter_name"
                       value="{{ old('shelter_name', $shelterName) }}">
            </div>

            <div class="mb-3">
                <label class="form-label">Position</label>
                <input type="text" class="form-control" name="position" 
                       value="{{ old('position', optional($profile)->position) }}">
            </div>

            <div class="mb-3">
                <label class="form-label">Shelter Address</label>
                <input type="text" class="form-control" name="shelter_address" 
                       value="{{ old('shelter_address', optional($profile)->shelter_address) }}">
            </div>

            <div class="mb-3">
                <label class="form-label">Bio</label>
                <textarea name="bio" id="bio" class="form-control" rows="5" 
                          placeholder="Tell us about yourself and your role at the shelter..."
                          maxlength="500">{{ old('bio', optional($profile)->bio) }}</textarea>
                <small class="text-muted" id="bio-counter">0/500 characters</small>
            </div>

            <button type="submit" class="btn btn-success">Save Changes</button>
            <a href="{{ route('shelter.profile.view') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const fileInput = document.getElementById("profile_picture");
        const previewImg = document.getElementById("profile-picture-preview");

        fileInput.addEventListener("change", function () {
            const file = this.files[0];
            if (file) {
                let fileSizeMB = file.size / (1024 * 1024); // Convert size to MB
                if (fileSizeMB > 2) { // 2MB limit check
                    alert("Error: The profile picture must not exceed 2MB.");
                    this.value = ""; // Clear file input
                    return;
                }
                const reader = new FileReader();
                reader.onload = function (e) {
                    // Update the preview image with the selected file data
                    previewImg.src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });

        // Bio character counter functionality
        const bioTextarea = document.getElementById("bio");
        const bioCounter = document.getElementById("bio-counter");

        // Update character count on load
        if (bioTextarea) {
            bioCounter.textContent = `${bioTextarea.value.length}/500 characters`;

            // Update character count when typing
            bioTextarea.addEventListener("input", function () {
                bioCounter.textContent = `${this.value.length}/500 characters`;
            });
        }
    });
</script>

@endsection