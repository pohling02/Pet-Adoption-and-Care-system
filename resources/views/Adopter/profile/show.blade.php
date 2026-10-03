@extends('layouts.adopter_master')

@section('title', 'Adopter Profile')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">

@endpush

@section('content')
<div class="container">
    <div class="row">
        <!-- Left Sidebar -->
        <div class="col-md-3">
            <div class="sidebar shadow p-3 rounded text-center">
                <div class="profile-image mx-auto">
                    @if($profile->profile_picture)
                    <img src="{{ asset($profile->profile_picture) }}" 
                         class="rounded-circle border border-secondary" 
                         style="width: 100px; height: 100px; object-fit: cover;">
                    @else
                    <div class="profile-initials mx-auto">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                    @endif
                    <h5 class="text-center mt-2"><strong>{{ Auth::user()->name }}</strong></h5>
                </div>

                <div class="mt-3">
                    <a href="{{ route('adopter.profile.view') }}" class="btn btn-block btn-active">My Profile</a>
                    <a href="{{ route('adopter.pets.profile') }}" class="btn btn-block 
                       {{ request()->is('adopter/pets/pet_profile') ? 'btn-active' : '' }}">
                        Pet Profile
                    </a>
                    <a href="{{ route('adopter.appointments') }}" class="btn btn-block">My Appointment</a>
                    <a href="{{ route('adopter.adoption') }}" class="btn btn-block">My Adoption</a>
                    <a href="{{ route('adopter.logout') }}" class="btn btn-block btn-danger"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        Log Out
                    </a>
                    <form id="logout-form" action="{{ route('adopter.logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </div>
        </div>

        <!-- Right Profile Content -->
        <div class="col-md-9">
            <div class="card shadow-sm p-4">
                <h4 class="section-title">User Information</h4>
                <div class="row mt-2">
                    <div class="col-md-6">
                        <p><strong>Name</strong> : {{ Auth::user()->name }}</p>
                        <p><strong>Email</strong> : {{ Auth::user()->email }}</p>
                        <p><strong>Gender</strong> : {{ $profile->gender ?? 'Not provided' }}</p>
                        <p><strong>Phone No</strong> : {{ $profile->phone_number ?? 'Not provided' }}</p>
                        <p><strong>Address</strong> : {{ $profile->address ?? 'Not provided' }}</p>
                        <p><strong>Occupation</strong> : {{ $profile->occupation ?? 'Not specified' }}</p>
                        <p><strong>Preferred Location</strong> : {{ $profile->preferred_location ?? 'Not specified' }}</p>
                        <p><strong>Interested In</strong> : {{ $profile->pet_preference ?? 'Not specified' }}</p>
                        <p><strong>Activity Level</strong> : {{ $profile->activity_level ?? 'Not specified' }}</p>
                        <p><strong>Home Type</strong> : {{ $profile->home_type ?? 'Not specified' }}</p>
                        <p><strong>Do you have other pets?</strong> : {{ $profile->other_pets ?? 'Not specified' }}</p>
                        <p><strong>Do you have allergies to pets?</strong> : {{ $profile->allergies ?? 'Not specified' }}</p>
                        <p><strong>Hours per day you can spend with your pet</strong> : {{ $profile->hours_per_day ?? 'Not specified' }}</p>
                        <p><strong>Bio</strong> : {{ $profile->bio ?? 'Not specified' }}</p>
                    </div>
                </div>
                <div class="profile-btn-container mt-3">
                    <a href="{{ route('adopter.profile.edit') }}" class="btn me-2" style="background-color: #c39081; color: white;">
                        <i class="fas fa-user-edit"></i> Edit Profile
                    </a>
                    <a href="{{ route('adopter.password.form') }}" class="btn" style="background-color: #51565c; color: white;">
                        <i class="fas fa-key"></i> Change Password
                    </a>
                </div>
            </div>

            <!-- Adoption Activity Section -->
            <div class="card shadow-sm p-4 mt-3">
                <h4 class="section-title">Adoption Activity</h4>
                <div class="row mt-2">
                    <div class="col-md-6">
                        <p><strong>Pet Adopted</strong> : {{ $adopted_pets_count }}</p>
                        <p><strong>Pending Application</strong> : {{ $pending_applications_count }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
