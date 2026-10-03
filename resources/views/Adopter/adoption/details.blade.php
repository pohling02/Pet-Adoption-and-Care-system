@extends('layouts.adopter_master')

@section('title', 'Adoption Application Details')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
<style>
    .info-box {
        border: 1px solid #ddd;
        border-radius: 10px;
        padding: 20px;
        background: #fff;
        margin-bottom: 20px;
        box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
    }
    .info-title {
        font-size: 18px;
        font-weight: bold;
        background: #d6ccc2;
        padding: 8px 15px;
        border-radius: 5px;
        display: inline-block;
        margin-bottom: 15px;
    }
    .info-item {
        font-size: 16px;
        margin-bottom: 8px;
    }
    .info-item strong {
        color: #5a5a5a;
    }
    .badge {
        padding: 8px 12px;
        font-size: 14px;
        font-weight: bold;
        border-radius: 5px;
    }
    .btn-back {
        background: #b38b7c;
        color: white;
        padding: 10px 15px;
        border-radius: 5px;
        text-decoration: none;
    }
    .btn-back:hover {
        background: #9a6f5e;
        color: white;
    }
    .pet-image-container {
        text-align: center;
        margin: 20px auto;
        max-width: 500px;
    }
    .pet-image {
        width: 40%;
        max-height: 220px; /* Limit max height */
        object-fit: cover; /* Prevents image distortion */
        border-radius: 10px;
        box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="row">
        <!-- Left Sidebar -->
        <div class="col-md-3">
            <div class="sidebar shadow p-3 rounded">
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
                    <a href="{{ route('adopter.profile.view') }}" class="btn btn-block">My Profile</a>
                    <a href="{{ route('adopter.pets.profile') }}" class="btn btn-block 
                       {{ request()->is('adopter/pets/pet_profile') ? 'btn-active' : '' }}">
                        Pet Profile
                    </a>
                    <a href="{{ route('adopter.appointments') }}" class="btn btn-block">My Appointment</a>
                    <a href="{{ route('adopter.adoption') }}" class="btn btn-block btn-active">My Adoption</a>
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

        <!-- Right Content Area -->
        <div class="col-md-9">
            <div class="info-box">
                <span class="info-title">Adoption Application Details</span>
                <div class="row">
                    <div class="col-md-6 info-item">
                        <strong>Application ID:</strong> {{ sprintf('%04d', $adoption->ApplicationID) }}
                    </div>
                    <div class="col-md-6 info-item">
                        <strong>Application Date:</strong> {{ $adoption->ApplicationDate }}
                    </div>
                    <div class="col-md-6 info-item">
                        <strong>Adoption Status:</strong>
                        <span class="badge 
                              @if($adoption->AdoptionStatus == 'Approved') bg-success 
                              @elseif($adoption->AdoptionStatus == 'Rejected') bg-danger 
                              @elseif($adoption->AdoptionStatus == 'Under Review') bg-warning 
                              @else bg-secondary @endif">
                            {{ ucfirst($adoption->AdoptionStatus) }}
                        </span>
                        @if($adoption->AdoptionStatus == 'Rejected' && $adoption->StaffNotes)
                        <br>
                        <small class="text-muted">Reason: {{ $adoption->StaffNotes }}</small>
                        @endif
                    </div>

                </div>
            </div>

            <!-- Pet Image Section -->
            <div class="info-box text-center">
                @if ($adoption->pet->images->isNotEmpty())
                <img src="{{ asset('images/' . basename($adoption->pet->images->first()->ImagePath)) }}" 
                     alt="Pet Image" class="pet-image">
                @else
                <img src="{{ asset('images/default-pet.jpg') }}" alt="Default Pet Image" class="pet-image">
                @endif
            </div>


            <div class="info-box">
                <span class="info-title">Pet Details</span>
                <div class="row">
                    <div class="col-md-6 info-item"><strong>Pet Name:</strong> {{ $adoption->pet->PetName ?? 'Unknown' }}</div>
                    <div class="col-md-6 info-item"><strong>Pet Type:</strong> {{ $adoption->pet->Species ?? 'Unknown' }}</div>
                    <div class="col-md-6 info-item"><strong>Pet Age:</strong> {{ $adoption->pet->Age ?? 'Not Specified' }}</div>
                    <div class="col-md-6 info-item"><strong>Pet Breed:</strong> {{ $adoption->pet->Breed ?? 'Not Specified' }}</div>
                </div>
            </div>

            <div class="info-box">
                <span class="info-title">Adopter Details</span>
                <div class="row">
                    <div class="col-md-6 info-item"><strong>Full Name:</strong> {{ $adoption->FullName }}</div>
                    <div class="col-md-6 info-item"><strong>Gender:</strong> {{ $adoption->Gender }}</div>
                    <div class="col-md-6 info-item"><strong>Age:</strong> {{ $adoption->Age }}</div>
                    <div class="col-md-6 info-item"><strong>Occupation:</strong> {{ $adoption->Occupation }}</div>
                    <div class="col-md-6 info-item"><strong>Address:</strong> {{ $adoption->Address }}</div>
                    <div class="col-md-6 info-item"><strong>State:</strong> {{ $adoption->State }}</div>
                    <div class="col-md-6 info-item"><strong>Phone:</strong> {{ $adoption->ContactNumber }}</div>
                    <div class="col-md-6 info-item"><strong>Email:</strong> {{ $adoption->Email }}</div>
                </div>
            </div>

            <div class="info-box">
                <span class="info-title">Household & Pet Care Details</span>
                <div class="row">
                    <div class="col-md-6 info-item"><strong>Household Details:</strong> {{ $adoption->HouseholdDetails }}</div>
                    <div class="col-md-6 info-item"><strong>Other Pets:</strong> {{ $adoption->OtherPetsInfo }}</div>
                    <div class="col-md-6 info-item"><strong>Pet Care Plan:</strong> {{ $adoption->PetCarePlan }}</div>
                    <div class="col-md-6 info-item"><strong>Emergency Plan:</strong> {{ $adoption->EmergencyPlan }}</div>
                </div>
            </div>

            <div class="info-box">
                <span class="info-title">Reference Information</span>
                <div class="info-item"><strong>Reference Name:</strong> {{ $adoption->ReferenceName }}</div>
                <div class="info-item"><strong>Contact:</strong> {{ $adoption->ReferenceContact }}</div>
                <div class="info-item"><strong>Relationship:</strong> {{ $adoption->ReferenceRelationship }}</div>
            </div>

            <div class="info-box">
                <span class="info-title">Living Environment Photos</span>
                <div class="row">
                    @if($adoption->photos->isNotEmpty())
                    @foreach($adoption->photos as $photo)
                    <div class="col-md-4">
                        <img src="{{ asset('storage/' . $photo->PhotoPath) }}" class="img-fluid rounded mb-3">
                    </div>
                    @endforeach
                    @else
                    <div class="col-md-12 text-center">
                        <img src="{{ asset('images/no-photos-uploaded.png') }}" alt="No photos uploaded" class="img-fluid" style="max-width: 200px;">
                    </div>
                    @endif
                </div>
            </div>
            <a href="{{ route('adopter.adoption') }}" class="btn-back">Back to List</a>
        </div>
    </div>
</div>
@endsection
