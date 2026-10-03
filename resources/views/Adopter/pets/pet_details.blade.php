@extends('layouts.adopter_master')

@section('content')
<div class="container">
    <div class="mt-3">
        <a href="{{ route('adopter.petlist') }}" class="btn return-btn">
            ← Return to Pet List
        </a>
    </div>

    <div class="row mt-3">
        <div class="col-md-12">
            <div class="pet-details-container p-4 rounded shadow">
                <div class="row align-items-start">
                    <!-- Left: Image Slider -->
                    <div class="col-md-5 pet-image-container">
                        @if ($pet->images->isNotEmpty())
                        <div id="petImageCarousel" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                @foreach ($pet->images as $index => $image)
                                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                    <img src="{{ asset('images/' . basename($image->ImagePath)) }}" class="d-block w-100 pet-slider-image rounded"
                                         alt="{{ $pet->PetName }}" loading="lazy">
                                </div>
                                @endforeach
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#petImageCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#petImageCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        </div>
                        @else
                        <img src="{{ asset('images/default-pet.jpg') }}" class="img-fluid rounded pet-slider-image" alt="No Image Available">
                        @endif
                    </div>

                    <!-- Right: Pet Details -->
                    <div class="col-md-7 pet-info-container">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h2 style="color: #1F4EA4;" class="fw-bold mb-0">{{ $pet->PetName }}</h2>
                            <span class="badge {{ $pet->AdoptionStatus == 'Available' ? 'bg-success' : 'bg-secondary' }} fs-6">
                                {{ $pet->AdoptionStatus }}
                            </span>
                        </div>

                        <!-- Basic Information -->
                        <div class="row">
                            <div class="col-md-6">
                                @if($pet->PetCode)
                                <p><strong>Pet Code:</strong> {{ $pet->PetCode }}</p>
                                @endif
                                <p><strong>Species:</strong> {{ $pet->Species }}</p>
                                <p><strong>Breed:</strong> {{ $pet->ManualBreed ?? $pet->Breed }}</p>
                                <p><strong>Color:</strong> {{ $pet->Color }}</p>
                                <p><strong>Age:</strong> {{ \Carbon\Carbon::parse($pet->DateOfBirth)->age }} Years</p>
                                <p><strong>Gender:</strong> {{ $pet->Gender }}</p>
                                <p><strong>Current Location:</strong> {{ $pet->CurrentLocation }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Vaccination:</strong> 
                                    <span class="badge {{ $pet->VaccinationStatus == 'Fully Vaccinated' ? 'bg-success' : 'bg-warning text-dark' }}">
                                        {{ $pet->VaccinationStatus }}
                                    </span>
                                </p>
                                <p><strong>Neutering:</strong> 
                                    <span class="badge {{ $pet->Neutering ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $pet->Neutering ? 'Yes' : 'No' }}
                                    </span>
                                </p>
                                <p><strong>Allergy:</strong> 
                                    <span class="badge {{ $pet->Allergy ? 'bg-warning text-dark' : 'bg-success' }}">
                                        {{ $pet->Allergy ? 'Yes' : 'None' }}
                                    </span>
                                </p>
                                @if($pet->Allergy && $pet->AllergyDetails)
                                <p><strong>Allergy Details:</strong> {{ $pet->AllergyDetails }}</p>
                                @endif
                                <p><strong>Health Conditions:</strong> {{ $pet->HealthCondition ?? 'Healthy' }}</p>
                                <p><strong>Special Needs:</strong> {{ $pet->SpecialNeed ?? 'None' }}</p>
                                <p><strong>Ideal Environment:</strong>
                                    @php
                                        $environmentClass = '';
                                        $environmentText = '';
                                        switch($pet->Ideal_Environment) {
                                            case 1:
                                                $environmentClass = 'bg-info text-dark';
                                                $environmentText = 'Apartment';
                                                break;
                                            case 2:
                                                $environmentClass = 'bg-success';
                                                $environmentText = 'Landed Property';
                                                break;
                                            case 3:
                                                $environmentClass = 'bg-warning text-dark';
                                                $environmentText = 'Apartment & Landed Property';
                                                break;
                                            default:
                                                $environmentClass = 'bg-secondary';
                                                $environmentText = 'Any';
                                        }
                                    @endphp
                                    <span class="badge {{ $environmentClass }}">{{ $environmentText }}</span>
                                </p>
                            </div>
                        </div>

                        <!-- Personality & Background -->
                        <div class="mt-3">
                            <p><strong>Personality:</strong> {{ $pet->Personality ?? 'Not specified' }}</p>
                            <p><strong>Background:</strong> {{ $pet->Background ?? 'No details available' }}</p>
                        </div>

                        <!-- Ratings Section -->
                        <div class="ratings-container mt-4">
                            <h5 class="mb-3">Pet Characteristics</h5>
                            <div class="row">
                                <div class="col-md-6 d-flex align-items-center mb-2">
                                    <strong>Energy Level:</strong> 
                                    <span class="stars ms-2">{!! str_repeat('⭐', $pet->EnergyLevel) !!}</span>
                                    <span class="ms-1 text-muted">({{ $pet->EnergyLevel }}/5)</span>
                                </div>
                                <div class="col-md-6 d-flex align-items-center mb-2">
                                    <strong>Appetite:</strong> 
                                    <span class="stars ms-2">{!! str_repeat('⭐', $pet->Appetite) !!}</span>
                                    <span class="ms-1 text-muted">({{ $pet->Appetite }}/5)</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 d-flex align-items-center mb-2">
                                    <strong>Friendliness:</strong> 
                                    <span class="stars ms-2">{!! str_repeat('⭐', $pet->Friendliness) !!}</span>
                                    <span class="ms-1 text-muted">({{ $pet->Friendliness }}/5)</span>
                                </div>
                                <div class="col-md-6 d-flex align-items-center mb-2">
                                    <strong>Adaptability:</strong> 
                                    <span class="stars ms-2">{!! str_repeat('⭐', $pet->Adaptability) !!}</span>
                                    <span class="ms-1 text-muted">({{ $pet->Adaptability }}/5)</span>
                                </div>
                            </div>
                            @if($pet->Species == 'Dog')
                            <div class="row">
                                <div class="col-md-6 d-flex align-items-center mb-2">
                                    <strong>Barking Level:</strong> 
                                    <span class="stars ms-2">{!! str_repeat('⭐', $pet->BarkingLevel) !!}</span>
                                    <span class="ms-1 text-muted">({{ $pet->BarkingLevel }}/5)</span>
                                </div>
                                <div class="col-md-6 d-flex align-items-center mb-2">
                                    <strong>Shedding Level:</strong> 
                                    <span class="stars ms-2">{!! str_repeat('⭐', $pet->SheddingLevel) !!}</span>
                                    <span class="ms-1 text-muted">({{ $pet->SheddingLevel }}/5)</span>
                                </div>
                            </div>
                            @else
                            <div class="row">
                                <div class="col-md-6 d-flex align-items-center mb-2">
                                    <strong>Shedding Level:</strong> 
                                    <span class="stars ms-2">{!! str_repeat('⭐', $pet->SheddingLevel) !!}</span>
                                    <span class="ms-1 text-muted">({{ $pet->SheddingLevel }}/5)</span>
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- Current Address -->
                        <div class="mt-3">
                            <p><strong>Current Address:</strong></p>
                            <p class="text-muted">{{ $pet->CurrentAddress }}</p>
                        </div>

                        <!-- Adoption Button -->
                        @if($pet->AdoptionStatus == 'Available')
                            @if($pet->adoptionApplications->isEmpty())
                            <a href="{{ route('adoption.create', ['pet' => $pet->PetID]) }}" 
                               class="btn btn-lg mt-3 w-100" 
                               style="background-color: #1F4EA4; border-color: #1F4EA4; color: #fff;">
                                Apply For Adoption
                            </a>
                            @else
                            <button class="btn btn-lg mt-3 w-100" 
                                    style="background-color: #6c757d; border-color: #6c757d; color: #fff;" 
                                    disabled>
                                Application Submitted
                            </button>
                            @endif
                        @else
                        <button class="btn btn-lg mt-3 w-100" 
                                style="background-color: #dc3545; border-color: #dc3545; color: #fff;" 
                                disabled>
                            Not Available for Adoption
                        </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Contact Shelter -->
            <div class="shelter-contact-container mt-4 p-4 rounded shadow">
                <h5 class="text-center mb-4" style="color: #1F4EA4;">Contact Shelter</h5>
                <div class="row text-center">
                    <div class="col contact-item">
                        <div class="contact-icon"><i class="fas fa-user"></i></div>
                        <p class="contact-label">Shelter</p>
                        <p class="contact-text"><strong>{{ $pet->shelter->name ?? 'Unknown Shelter' }}</strong></p>
                    </div>
                    <div class="col contact-item">
                        <div class="contact-icon"><i class="fas fa-envelope"></i></div>
                        <p class="contact-label">Email</p>
                        <p class="contact-text">
                            <a href="mailto:{{ $pet->shelter->email ?? '' }}" class="text-dark">
                                {{ $pet->shelter->email ?? 'Not Available' }}
                            </a>
                        </p>
                    </div>
                    <div class="col contact-item">
                        <div class="contact-icon"><i class="fas fa-phone"></i></div>
                        <p class="contact-label">Phone</p>
                        <p class="contact-text">{{ $pet->shelter->shelterStaffProfile->phone_number ?? 'Not Available' }}</p>
                    </div>
                    <div class="col contact-item">
                        <a href="{{ route('messages.chat', ['receiverId' => $pet->ShelterID, 'pet_id' => $pet->PetID]) }}" class="text-decoration-none text-dark">
                            <div class="contact-icon">
                                <i class="fas fa-comment"></i>
                            </div>
                            <p class="contact-label">Message</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .pet-details-container {
        min-height: auto;
        padding-bottom: 20px;
        background-color: #fff;
        border: 1px solid #e0e0e0;
    }

    .pet-image-container {
        max-height: 450px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .pet-slider-image {
        width: 100%;
        height: auto;
        max-height: 400px;
        object-fit: cover;
        border-radius: 10px;
    }

    .shelter-contact-container {
        background-color: #f5ede1;
        padding: 20px;
        border-radius: 10px;
        width: 100%;
        margin-top: 20px;
    }

    .contact-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        padding: 10px;
    }

    .contact-icon {
        background-color: #8b6d5a;
        color: white;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        margin-bottom: 8px;
        transition: background-color 0.3s;
    }

    .contact-icon:hover {
        background-color: #6d5447;
    }

    .contact-label {
        font-weight: bold;
        font-size: 14px;
        margin-bottom: 5px;
    }

    .contact-text {
        font-size: 14px;
        text-align: center;
        width: 100%;
        margin: 0;
        padding: 3px 0;
    }

    .return-btn {
        background-color: #1F4EA4 !important;
        color: white !important;
        border: none;
        padding: 10px 15px;
        font-size: 14px;
        transition: background-color 0.3s;
        text-decoration: none;
    }

    .return-btn:hover {
        background-color: #333 !important;
        color: white !important;
    }

    .stars {
        font-size: 16px;
    }

    .badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }

    .ratings-container {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        border: 1px solid #e9ecef;
    }

    .pet-info-container p {
        margin-bottom: 8px;
        line-height: 1.4;
    }

    @media (max-width: 768px) {
        .pet-details-container {
            padding: 15px;
        }
        
        .contact-item {
            margin-bottom: 20px;
        }
        
        .ratings-container .col-md-6 {
            margin-bottom: 10px;
        }
    }
</style>
@endsection