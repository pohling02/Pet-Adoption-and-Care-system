<div class="profile-container">
    <h3>{{ $adopter->name }}</h3>
    <p><strong>Phone:</strong> {{ $profile->phone_number ?? 'Not provided' }}</p>
    <p><strong>Gender:</strong> {{ $profile->gender ?? 'Not specified' }}</p>
    <p><strong>Occupation:</strong> {{ $profile->occupation ?? 'Not specified' }}</p>
    <p><strong>Pet Preference:</strong> {{ $profile->pet_preference ?? 'No preference' }}</p>
    <p><strong>Activity Level (Pet):</strong> {{ $profile->activity_level ?? 'Not specified' }}</p>
    <p><strong>Other pets:</strong> {{ $profile->other_pets ?? 'Not specified' }}</p>
    <p><strong>Home Type:</strong> {{ $profile->home_type ?? 'Not specified' }}</p>
    <p><strong>Allergies:</strong> {{ $profile->allergies ?? 'Not specified' }}</p>
    <p><strong>Hours per day you can spend with your pet:</strong> {{ $profile->hours_per_day ?? 'Not specified' }}</p>
    <p><strong>Preferred Location:</strong> {{ $profile->preferred_location ?? 'Not specified' }}</p>
    <p><strong>Bio:</strong> {{ $profile->bio ?? 'No bio available' }}</p>
</div>

<style>
    .brief-profile-container {
        text-align: left;
        padding: 20px;
        font-size: 16px;
    }

    .brief-profile-container h2 {
        text-align: center;
        color: #007bff;
        margin-bottom: 15px;
    }

    .brief-profile-container p {
        margin: 8px 0;
        color: #333;
    }

    .brief-profile-container strong {
        color: #000;
    }
</style>
