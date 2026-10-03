@extends('layouts.adopter_master')

@section('title', 'Messages')

@push('styles')
<style>
    .message-list-container {
        max-width: 800px;
        margin: 30px auto;
        background: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    }

    .message-header {
        font-size: 24px;
        font-weight: bold;
        text-align: center;
        margin-bottom: 20px;
        color: #1A4E8C;
    }

    .message-item {
        display: flex;
        align-items: center;
        padding: 12px;
        border-bottom: 1px solid #ddd;
        cursor: pointer;
        transition: 0.3s;
        text-decoration: none;
    }

    .message-item:hover {
        background: #f1f1f1;
    }

    /* Profile image styling */
    .message-item img {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 15px;
    }

    /* Circular placeholder styling */
    .avatar-placeholder {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: #1A4E8C; /* Or any color you like */
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        font-size: 14px;
        text-transform: uppercase;
    }

    .message-details {
        flex-grow: 1;
    }

    .message-name {
        font-size: 18px;
        font-weight: bold;
        color: #1A4E8C;
    }

    .message-preview {
        font-size: 14px;
        color: #777;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        max-width: 300px;
    }

    .message-timestamp {
        font-size: 12px;
        color: #999;
    }

    .no-messages {
        text-align: center;
        padding: 20px;
        font-size: 16px;
        color: #777;
    }

    .message-link {
        text-decoration: none;
        color: inherit;
        display: block;
        width: 100%;
    }
</style>
@endpush

@section('content')
<div class="message-list-container">
    <div class="message-header">Your Messages</div>

    @if ($conversations->isNotEmpty())
        @foreach ($conversations as $conversation)
            @php
                $firstMessage = $conversation->first();
                $otherUser = $firstMessage->SenderID == auth()->id() ? $firstMessage->receiver : $firstMessage->sender;
                $lastMessage = $conversation->last();

                // Fetch the other user's profile based on their role
                $otherProfile = null;
                if ($otherUser->role === 'adopter') {
                    $otherProfile = \App\Models\AdopterProfile::where('UserID', $otherUser->UserID)->first();
                } elseif ($otherUser->role === 'shelter_staff') {
                    $otherProfile = \App\Models\ShelterStaffProfile::where('UserID', $otherUser->UserID)->first();
                }

                // Check if the profile picture file actually exists
                $profilePicturePath = $otherProfile && $otherProfile->profile_picture
                    ? public_path($otherProfile->profile_picture)
                    : null;
                
                $hasValidProfilePic = ($profilePicturePath && file_exists($profilePicturePath));

                // Determine the display text if no picture is found
                $nameParts = explode(' ', trim($otherUser->name));
                $firstWord = !empty($nameParts[0]) ? $nameParts[0] : '';
                $placeholderText = strtoupper($firstWord); // e.g. "Ng" => "NG"
            @endphp

            @if(isset($otherUser) && $otherUser->UserID)
                <a href="{{ route('messages.chat', ['receiverId' => $otherUser->UserID]) }}" class="message-link">
                    <div class="message-item">
                        {{-- If profile picture is valid, show image; otherwise show placeholder --}}
                        @if($hasValidProfilePic)
                            <img src="{{ asset($otherProfile->profile_picture) }}" alt="User Avatar">
                        @else
                            <div class="avatar-placeholder">{{ $placeholderText }}</div>
                        @endif

                        <div class="message-details">
                            <div class="message-name">{{ $otherUser->name }}</div>
                            <div class="message-preview">{{ $lastMessage->Content }}</div>
                        </div>
                        <div class="message-timestamp">
                            {{ $lastMessage->created_at->diffForHumans() }}
                        </div>
                    </div>
                </a>
            @endif
        @endforeach
    @else
        <p class="no-messages">You have no messages yet.</p>
    @endif
</div>
@endsection
