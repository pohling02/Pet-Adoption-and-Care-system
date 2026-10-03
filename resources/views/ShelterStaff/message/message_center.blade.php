@extends('layouts.shelter_master')

@section('title', 'Message Center')

@push('styles')
<style>
    .chat-container {
        width: 50%;
        margin: auto;
        padding: 20px;
        background: white;
        border-radius: 10px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    }

    .contact-item {
        display: flex;
        align-items: center;
        padding: 10px;
        border-radius: 8px;
        cursor: pointer;
        transition: 0.3s;
        margin-bottom: 8px;
        background: white;
        border: 1px solid #ddd;
    }

    .contact-item:hover {
        background: #007bff;
        color: white;
    }

    .contact-item img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        margin-right: 10px;
    }

    .avatar-placeholder {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #007bff;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 10px;
        font-size: 14px;
    }

    .contact-email {
        font-size: 12px;
        color: #6c757d;
    }
</style>
@endpush

@section('content')
<div class="container mt-4">
    <div class="chat-container">
        <h3 class="text-center">Your Messages</h3>
        <hr>

        @if(isset($contacts) && $contacts->count() > 0)
            @foreach($contacts as $contact)
                @php
                    // Attempt to retrieve a valid profile picture from Adopter/ShelterStaff profile
                    $profilePicture = asset('images/blank_profile.png');

                    if ($contact->role === 'adopter') {
                        $profile = \App\Models\AdopterProfile::where('UserID', $contact->UserID)->first();
                        if ($profile && $profile->profile_picture && file_exists(public_path($profile->profile_picture))) {
                            $profilePicture = asset($profile->profile_picture);
                        }
                    }
                    elseif ($contact->role === 'shelter_staff') {
                        $profile = \App\Models\ShelterStaffProfile::where('UserID', $contact->UserID)->first();
                        if ($profile && $profile->profile_picture && file_exists(public_path($profile->profile_picture))) {
                            $profilePicture = asset($profile->profile_picture);
                        }
                    }

                    // If no real profile picture found, display the entire first word in uppercase
                    $nameParts = explode(' ', trim($contact->name));
                    $firstWord  = !empty($nameParts) ? $nameParts[0] : '';
                    $initials   = strtoupper($firstWord); // e.g. "Ng" → "NG"
                @endphp

                <div class="contact-item" 
                     onclick="window.location.href='{{ route('messages.chat', ['receiverId' => $contact->UserID]) }}'">
                    @if($profilePicture === asset('images/blank_profile.png'))
                        <!-- Show a circular placeholder with the uppercase first word -->
                        <div class="avatar-placeholder">{{ $initials }}</div>
                    @else
                        <!-- Show the actual profile picture -->
                        <img src="{{ $profilePicture }}" alt="User">
                    @endif

                    <div>
                        <strong>{{ $contact->name }}</strong>
                        <div class="contact-email">{{ $contact->email }}</div>
                    </div>
                </div>
            @endforeach
        @else
            <p class="text-danger text-center">⚠ No conversation available.</p>
        @endif
    </div>
</div>
@endsection
