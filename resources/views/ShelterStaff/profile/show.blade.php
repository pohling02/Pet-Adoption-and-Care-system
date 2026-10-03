@extends('layouts.shelter_master')

@section('title', 'Shelter Staff Profile')

@section('content')
<div class="container mt-4">
    <h2 class="text-center text-white bg-primary p-3 rounded">Shelter Staff Profile</h2>

    <div class="card shadow-sm p-4">
        <div class="row">
            <div class="col-md-3 text-center">
                @php
                $profilePicturePath = ($profile && $profile->profile_picture) 
                ? asset($profile->profile_picture) 
                : asset('images/blankprofile.jpg');
                @endphp


                <img src="{{ $profilePicturePath }}" 
                     class="rounded-circle border border-secondary" 
                     style="width: 120px; height: 120px; object-fit: cover;">
            </div>
            <div class="col-md-9">
                <h4>{{ $user->name }}</h4>
                <p><strong>Email:</strong> {{ $user->email }}</p>
                <p><strong>Phone Number:</strong> {{ $profile->phone_number ?? 'Not provided' }}</p>
                <p><strong>Gender:</strong> {{ $profile->gender ?? 'Not specified' }}</p>
                <p><strong>Shelter Name:</strong> {{ $profile->shelter_name ?? 'Not provided' }}</p>
                <p><strong>Position:</strong> {{ $profile->position ?? 'Not provided' }}</p>
                <p><strong>Address:</strong> {{ $profile->shelter_address ?? 'Not provided' }}</p>
                <p><strong>Bio:</strong> {{ $profile->bio ?? 'No bio available' }}</p>

                <a href="{{ route('shelter.profile.edit') }}" class="btn btn-warning">Edit Profile</a>
                <a href="{{ route('shelter.password.form') }}" class="btn btn-warning">Change Password</a>
            </div>
        </div>
    </div>
</div>
@endsection
