@extends('layouts.adopter_master')

@section('title', 'Pet Details')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
<style>
    /* Add proper body padding to prevent footer overlap */
    body {
        padding-bottom: 100px; /* Adjust based on your footer height */
    }
    
    .main-content {
        min-height: calc(100vh - 200px); /* Ensure minimum height */
        padding-bottom: 50px; /* Extra spacing before footer */
    }
    
    .profile-box {
        background: linear-gradient(145deg, #f8f4ed, #f5ede1);
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.08);
    }
    .pet-image {
        width: 100%;
        max-width: 220px;
        height: auto;
        aspect-ratio: 1;
        object-fit: cover;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.16);
        transition: transform 0.3s ease;
        cursor: pointer;
    }

    .pet-image:hover {
        transform: scale(1.03);
    }
    .medical-record-table {
        width: 100%;
        border-collapse: collapse;
    }
    .medical-record-table th, .medical-record-table td {
        border: 1px solid #e0e0e0;
        padding: 10px 12px;
        text-align: left;
    }
    .medical-record-table th {
        background: #f1e8dc;
        font-weight: 600;
    }
    .medical-record-table tr:hover {
        background-color: #f9f5f0;
    }
    .pet-stats {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        margin: 15px 0;
        gap: 10px;
    }
    .stat-item {
        text-align: center;
        flex: 1;
        min-width: 100px;
        padding: 15px 10px;
        background: linear-gradient(145deg, #ffffff, #f8f1e7);
        border-radius: 10px;
        box-shadow: 0 3px 6px rgba(0,0,0,0.08);
        transition: all 0.3s;
    }
    .stat-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 12px rgba(0,0,0,0.1);
    }
    .stat-value {
        font-size: 28px;
        font-weight: bold;
        color: #8d6e63;
        margin-bottom: 5px;
    }
    .stat-label {
        font-size: 14px;
        color: #5d4037;
        font-weight: 500;
    }
    .gallery-container {
        position: relative;
        display: flex;
        align-items: center;
        max-width: 280px;
        margin: 0 auto;
    }

    .gallery-thumbnails {
        display: flex;
        overflow-x: auto;
        scroll-behavior: smooth;
        -ms-overflow-style: none;
        scrollbar-width: none;
        gap: 8px;
        padding: 10px 0;
        margin: 0 30px;
    }

    .gallery-thumbnails::-webkit-scrollbar {
        display: none;
    }

    .thumbnail-wrapper {
        flex: 0 0 auto;
        position: relative;
        opacity: 0.7;
        transition: all 0.2s ease;
        border: 2px solid transparent;
        border-radius: 8px;
    }

    .thumbnail-wrapper.active {
        opacity: 1;
        border-color: #8d6e63;
    }

    .pet-thumbnail {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 6px;
        transition: transform 0.2s;
        cursor: pointer;
    }

    .thumbnail-wrapper:hover {
        opacity: 1;
    }

    .pet-thumbnail:hover {
        transform: scale(1.1);
        z-index: 2;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
    .modal-image {
        max-width: 100%;
        max-height: 80vh;
        border-radius: 8px;
    }
    .section-card {
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        border: none;
        transition: box-shadow 0.3s;
        margin-bottom: 30px; /* Add consistent spacing between cards */
    }
    .section-card:hover {
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }
    .card-header {
        border-bottom: none;
    }
    .badge-pet-status {
        font-size: 0.85rem;
        padding: 0.4rem 0.7rem;
        border-radius: 50px;
    }
    .info-group {
        margin-bottom: 12px;
        line-height: 1.5;
    }
    .info-label {
        font-weight: 600;
        color: #5d4037;
        display: inline-block;
        min-width: 100px;
    }
    .info-box {
        background: rgba(255,255,255,0.6);
        padding: 15px;
        border-radius: 10px;
        margin-top: 10px;
        box-shadow: inset 0 1px 3px rgba(0,0,0,0.05);
        line-height: 1.6;
        border: 1px solid rgba(0,0,0,0.05);
    }

    .btn-outline-primary {
        border-color: #8d6e63;
        color: #8d6e63;
    }
    .btn-outline-primary:hover {
        background-color: #8d6e63;
        border-color: #8d6e63;
        color: white;
    }

    .modal-content {
        border-radius: 15px;
        border: none;
    }
    .modal-header {
        border-bottom: 1px solid #f5ede1;
        background-color: #f8f4ed;
    }
    .modal-footer {
        border-top: 1px solid #f5ede1;
        background-color: #f8f4ed;
    }
    .gallery-scroll-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: white;
        border: 1px solid #ddd;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        z-index: 2;
        transition: all 0.2s;
    }

    .gallery-scroll-btn:hover {
        background: #f5ede1;
    }

    .gallery-scroll-btn.left {
        left: -5px;
    }

    .gallery-scroll-btn.right {
        right: -5px;
    }

    /* Modal styles */
    .featured-modal-image {
        max-height: 50vh;
        max-width: 100%;
        object-fit: contain;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    .image-counter {
        font-size: 14px;
        color: #666;
    }

    .modal-gallery-container {
        margin-top: 15px;
        border-top: 1px solid #eee;
        padding-top: 15px;
    }

    .modal-gallery {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        justify-content: center;
    }

    .modal-thumbnail-wrapper {
        position: relative;
        opacity: 0.7;
        transition: all 0.2s ease;
        border: 2px solid transparent;
        border-radius: 6px;
        cursor: pointer;
    }

    .modal-thumbnail-wrapper.active {
        opacity: 1;
        border-color: #8d6e63;
    }

    .modal-thumbnail {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 4px;
    }

    .modal-thumbnail-wrapper:hover {
        opacity: 1;
    }

    /* Environment badge styles */
    .environment-badge {
        font-size: 0.85rem;
        padding: 0.4rem 0.8rem;
        border-radius: 20px;
        font-weight: 500;
    }

    .environment-apartment {
        background-color: #e3f2fd;
        color: #1976d2;
        border: 1px solid #bbdefb;
    }

    .environment-landed {
        background-color: #e8f5e8;
        color: #388e3c;
        border: 1px solid #c8e6c9;
    }

    .environment-both {
        background-color: #fff3e0;
        color: #f57c00;
        border: 1px solid #ffcc02;
    }

    /* Container and spacing fixes */
    .container {
        padding-bottom: 50px; /* Add bottom padding to container */
    }

    .row {
        margin-bottom: 30px; /* Add spacing between rows */
    }

    /* Last card specific spacing */
    .section-card:last-child {
        margin-bottom: 50px; /* Extra margin for the last card */
    }

    @media (max-width: 767px) {
        body {
            padding-bottom: 120px; /* More padding on mobile */
        }
        
        .main-content {
            min-height: calc(100vh - 250px);
            padding-bottom: 70px;
        }
        
        .pet-stats {
            flex-wrap: wrap;
        }
        .stat-item {
            min-width: calc(50% - 10px);
            margin-bottom: 10px;
        }
        .gallery-container {
            max-width: 240px;
        }

        .pet-thumbnail {
            width: 50px;
            height: 50px;
        }

        .modal-thumbnail {
            width: 60px;
            height: 60px;
        }
        
        .container {
            padding-bottom: 70px;
        }
        
        .section-card:last-child {
            margin-bottom: 70px;
        }
    }
</style>
@endpush

@section('content')
<div class="main-content">
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
                        <a href="{{ route('adopter.profile.view') }}" class="btn btn-block">Update User Profile</a>
                        <a href="{{ route('adopter.pets.profile') }}" class="btn btn-block btn-active">Pet Profile</a>
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

            <div class="col-md-9">
                <div class="card section-card">
                    <div class="card-header bg-light py-3">
                        <h4 class="mb-0"><i class="fas fa-paw me-2"></i>Pet Profile</h4>
                    </div>
                    <div class="card-body p-4">
                        <div class="row align-items-start">
                            <div class="col-lg-4 mb-4 mb-lg-0">
                                <div class="text-center">
                                    <img src="{{ asset('images/' . basename($pet->images->first()->ImagePath ?? 'default-pet.jpg')) }}" 
                                         class="pet-image mb-3 img-fluid" alt="{{ $pet->PetName }}" id="featuredPetImage">
                                    <h4 class="mt-2 d-lg-none">{{ $pet->PetName }}</h4>

                                    @if($pet->images && $pet->images->count() > 0)
                                    <div class="gallery-container mt-3">
                                        <button class="gallery-scroll-btn left" id="scrollLeft">
                                            <i class="fas fa-chevron-left"></i>
                                        </button>

                                        <div class="gallery-thumbnails" id="galleryThumbnails">
                                            @foreach($pet->images as $index => $image)
                                            <div class="thumbnail-wrapper {{ $index === 0 ? 'active' : '' }}">
                                                <img src="{{ asset('images/' . basename($image->ImagePath)) }}" 
                                                     class="pet-thumbnail" 
                                                     alt="Pet photo {{ $index + 1 }}"
                                                     data-index="{{ $index }}"
                                                     onclick="updateFeaturedImage(this.src)">
                                            </div>
                                            @endforeach
                                        </div>

                                        <button class="gallery-scroll-btn right" id="scrollRight">
                                            <i class="fas fa-chevron-right"></i>
                                        </button>
                                    </div>

                                    <button type="button" class="btn btn-outline-primary mt-3" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#allPetImagesModal">
                                        <i class="fas fa-images me-1"></i> View All Images ({{ $pet->images->count() }})
                                    </button>
                                    @endif
                                </div>
                            </div>

                            <div class="col-lg-8">
                                <div class="profile-box">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h3 class="mb-0 d-none d-lg-block">{{ $pet->PetName }}</h3>
                                        <span class="badge {{ $pet->AdoptionStatus == 'Available' ? 'bg-success' : 'bg-secondary' }} badge-pet-status">
                                            {{ $pet->AdoptionStatus }}
                                        </span>
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="info-group">
                                                <span class="info-label">Pet ID:</span> {{ $pet->PetID }}
                                            </div>
                                            @if($pet->PetCode)
                                            <div class="info-group">
                                                <span class="info-label">Pet Code:</span> {{ $pet->PetCode }}
                                            </div>
                                            @endif
                                            <div class="info-group">
                                                <span class="info-label">Species:</span> {{ $pet->Species }}
                                            </div>
                                            <div class="info-group">
                                                <span class="info-label">Breed:</span> 
                                                {{ $pet->ManualBreed ?? $pet->Breed }}
                                            </div>
                                            <div class="info-group">
                                                <span class="info-label">Color:</span> {{ $pet->Color }}
                                            </div>
                                            <div class="info-group">
                                                <span class="info-label">Age:</span> {{ \Carbon\Carbon::parse($pet->DateOfBirth)->age }} Years
                                            </div>
                                            <div class="info-group">
                                                <span class="info-label">Gender:</span> {{ $pet->Gender }}
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-group">
                                                <span class="info-label">Location:</span> {{ $pet->CurrentLocation }}
                                            </div>
                                            <div class="info-group">
                                                <span class="info-label">Vaccination:</span> 
                                                <span class="badge {{ $pet->VaccinationStatus == 'Fully Vaccinated' ? 'bg-success' : 'bg-warning text-dark' }}">
                                                    {{ $pet->VaccinationStatus }}
                                                </span>
                                            </div>
                                            <div class="info-group">
                                                <span class="info-label">Neutering:</span> 
                                                <span class="badge {{ $pet->Neutering ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ $pet->Neutering ? 'Yes' : 'No' }}
                                                </span>
                                            </div>
                                            <div class="info-group">
                                                <span class="info-label">Allergy:</span> 
                                                <span class="badge {{ $pet->Allergy ? 'bg-warning text-dark' : 'bg-success' }}">
                                                    {{ $pet->Allergy ? 'Yes' : 'None' }}
                                                </span>
                                            </div>
                                            @if($pet->Allergy && $pet->AllergyDetails)
                                            <div class="info-group">
                                                <span class="info-label">Allergy Details:</span> {{ $pet->AllergyDetails }}
                                            </div>
                                            @endif
                                            <div class="info-group">
                                                <span class="info-label">Ideal Environment:</span>
                                                @php
                                                    $environmentClass = '';
                                                    $environmentText = '';
                                                    switch($pet->Ideal_Environment) {
                                                        case 1:
                                                            $environmentClass = 'environment-apartment';
                                                            $environmentText = 'Apartment';
                                                            break;
                                                        case 2:
                                                            $environmentClass = 'environment-landed';
                                                            $environmentText = 'Landed Property';
                                                            break;
                                                        case 3:
                                                            $environmentClass = 'environment-both';
                                                            $environmentText = 'Apartment & Landed Property';
                                                            break;
                                                        default:
                                                            $environmentClass = 'environment-both';
                                                            $environmentText = 'Any';
                                                    }
                                                @endphp
                                                <span class="environment-badge {{ $environmentClass }}">
                                                    {{ $environmentText }}
                                                </span>
                                            </div>
                                            <div class="info-group">
                                                <span class="info-label">Special Needs:</span> {{ $pet->SpecialNeed ?? 'None' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pet Personality Stats -->
                        <div class="mt-4 pt-3 border-top">
                            <h5 class="mb-3"><i class="fas fa-chart-bar me-2"></i>Pet Characteristics</h5>
                            <div class="pet-stats">
                                <div class="stat-item">
                                    <div class="stat-value">{{ $pet->EnergyLevel }}/5</div>
                                    <div class="stat-label">Energy Level</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-value">{{ $pet->Appetite }}/5</div>
                                    <div class="stat-label">Appetite</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-value">{{ $pet->Friendliness }}/5</div>
                                    <div class="stat-label">Friendliness</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-value">{{ $pet->Adaptability }}/5</div>
                                    <div class="stat-label">Adaptability</div>
                                </div>
                                @if($pet->Species == 'Dog')
                                <div class="stat-item">
                                    <div class="stat-value">{{ $pet->BarkingLevel }}/5</div>
                                    <div class="stat-label">Barking Level</div>
                                </div>
                                @endif
                                <div class="stat-item">
                                    <div class="stat-value">{{ $pet->SheddingLevel }}/5</div>
                                    <div class="stat-label">Shedding Level</div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top">
                            <h5 class="mb-3"><i class="fas fa-heartbeat me-2"></i>Health Condition</h5>
                            <div class="info-box">
                                {{ $pet->HealthCondition ?? 'No health condition information available.' }}
                            </div>
                        </div>

                        <div class="row mt-4 pt-3 border-top">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <h5><i class="fas fa-history me-2"></i>Background</h5>
                                <div class="info-box">
                                    {{ $pet->Background ?? 'No background information available.' }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h5><i class="fas fa-smile me-2"></i>Personality</h5>
                                <div class="info-box">
                                    {{ $pet->Personality ?? 'No personality information available.' }}
                                </div>
                            </div>
                        </div>

                        <!-- Address Information -->
                        <div class="mt-4 pt-3 border-top">
                            <h5 class="mb-3"><i class="fas fa-map-marker-alt me-2"></i>Current Address</h5>
                            <div class="info-box">
                                {{ $pet->CurrentAddress }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card section-card">
                    <div class="card-header bg-light py-3">
                        <h4 class="mb-0"><i class="fas fa-heartbeat me-2"></i>Medical Records</h4>
                    </div>
                    <div class="card-body p-4">
                        @if($pet->healthRecords->isEmpty())
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>No medical records available for this pet.
                        </div>
                        @else
                        <div class="table-responsive">
                            <table class="table table-hover medical-record-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Diagnosis</th>
                                        <th>Health Remarks</th>
                                        <th>Medicine</th>
                                        <th class="text-center">Images</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pet->healthRecords as $record)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($record->LastCheckupDate)->format('M d, Y') }}</td>
                                        <td>{{ $record->Diagnosis ?? '-' }}</td>
                                        <td>{{ $record->HealthRemarks ?? '-' }}</td>
                                        <td>{{ $record->Medicine ?? '-' }}</td>
                                        <td class="text-center">
                                            @if($record->images && $record->images->count() > 0)
                                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#recordImagesModal{{ $record->RecordID }}">
                                                <i class="fas fa-images me-1"></i> {{ $record->images->count() }}
                                            </button>
                                            @else
                                            <span class="text-muted"><i class="fas fa-ban me-1"></i> None</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pet Images Modal -->
<div class="modal fade" id="allPetImagesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-paw me-2"></i>
                    {{ $pet->PetName }}'s Photo Gallery
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <img id="modalFeaturedImage" 
                         src="{{ asset('images/' . basename($pet->images->first()->ImagePath ?? 'default-pet.jpg')) }}" 
                         class="img-fluid featured-modal-image" 
                         alt="{{ $pet->PetName }}">
                    <div class="image-counter mt-2">
                        Image <span id="currentImageIndex">1</span> of {{ $pet->images->count() }}
                    </div>
                </div>

                <div class="d-flex justify-content-between mb-4">
                    <button class="btn btn-outline-secondary" id="prevImage">
                        <i class="fas fa-chevron-left me-1"></i> Previous
                    </button>
                    <button class="btn btn-outline-secondary" id="nextImage">
                        Next <i class="fas fa-chevron-right ms-1"></i>
                    </button>
                </div>

                <div class="modal-gallery-container">
                    <div class="modal-gallery">
                        @foreach($pet->images as $index => $image)
                        <div class="modal-thumbnail-wrapper {{ $index === 0 ? 'active' : '' }}"
                             data-index="{{ $index }}">
                            <img src="{{ asset('images/' . basename($image->ImagePath)) }}" 
                                 class="modal-thumbnail" 
                                 alt="Pet photo {{ $index + 1 }}"
                                 onclick="updateModalFeaturedImage(this, {{ $index }})">
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Health Record Images Modals -->
@foreach($pet->healthRecords as $record)
@if($record->images && $record->images->count() > 0)
<div class="modal fade" id="recordImagesModal{{ $record->RecordID }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-file-medical me-2"></i>
                    Health Record Images ({{ \Carbon\Carbon::parse($record->LastCheckupDate)->format('d/m/Y') }})
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    @foreach($record->images as $image)
                    <div class="col-md-6 text-center mb-4">
                        <img src="{{ asset('storage/' . $image->ImagePath) }}" 
                             alt="Health Record Image"
                             class="img-fluid modal-image"
                             style="max-height: 300px; border: 1px solid #ddd; border-radius: 4px; padding: 5px;">
                        <p class="small text-muted">
                            <i class="far fa-clock me-1"></i>
                            Added {{ \Carbon\Carbon::parse($image->created_at)->format('d/m/Y H:i') }}
                        </p>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                @if($record->Diagnosis || $record->Medicine)
                <div class="me-auto small text-muted">
                    @if($record->Diagnosis)
                    <strong>Diagnosis:</strong> {{ $record->Diagnosis }}
                    @endif
                    @if($record->Medicine)
                    <strong class="ms-2">Medicine:</strong> {{ $record->Medicine }}
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endif
@endforeach
@endsection

@push('scripts')
<script>
    // Enable any tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })

    function updateFeaturedImage(src) {
        document.getElementById('featuredPetImage').src = src;
        // Update active thumbnail
        const thumbnails = document.querySelectorAll('.thumbnail-wrapper');
        thumbnails.forEach(thumb => {
            if (thumb.querySelector('img').src === src) {
                thumb.classList.add('active');
            } else {
                thumb.classList.remove('active');
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Gallery scroll buttons functionality
        const scrollLeftBtn = document.getElementById('scrollLeft');
        const scrollRightBtn = document.getElementById('scrollRight');
        const galleryThumbnails = document.getElementById('galleryThumbnails');
        
        if (scrollLeftBtn && scrollRightBtn && galleryThumbnails) {
            scrollLeftBtn.addEventListener('click', function() {
                galleryThumbnails.scrollBy({
                    left: -180,
                    behavior: 'smooth'
                });
            });
            
            scrollRightBtn.addEventListener('click', function() {
                galleryThumbnails.scrollBy({
                    left: 180,
                    behavior: 'smooth'
                });
            });
        }

        // Modal gallery functionality
        let currentImageIndex = 0;
        const totalImages = {{ $pet->images->count() }};
        const modalFeaturedImage = document.getElementById('modalFeaturedImage');
        const currentIndexElement = document.getElementById('currentImageIndex');
        const prevButton = document.getElementById('prevImage');
        const nextButton = document.getElementById('nextImage');
        const modalThumbnails = document.querySelectorAll('.modal-thumbnail-wrapper');
        
        // Initialize modal when shown
        const petImagesModal = document.getElementById('allPetImagesModal');
        if (petImagesModal) {
            petImagesModal.addEventListener('show.bs.modal', function() {
                currentImageIndex = 0;
                updateModalImage(currentImageIndex);
            });
        }

        // Previous image button functionality
        if (prevButton) {
            prevButton.addEventListener('click', function() {
                currentImageIndex = (currentImageIndex - 1 + totalImages) % totalImages;
                updateModalImage(currentImageIndex);
            });
        }

        // Next image button functionality
        if (nextButton) {
            nextButton.addEventListener('click', function() {
                currentImageIndex = (currentImageIndex + 1) % totalImages;
                updateModalImage(currentImageIndex);
            });
        }

        function updateModalImage(index) {
            if (modalThumbnails.length > 0 && modalThumbnails[index]) {
                const targetImage = modalThumbnails[index].querySelector('img');
                if (modalFeaturedImage && targetImage) {
                    modalFeaturedImage.src = targetImage.src;
                    modalThumbnails.forEach(thumb => {
                        thumb.classList.remove('active');
                    });
                    modalThumbnails[index].classList.add('active');
                    if (currentIndexElement) {
                        currentIndexElement.textContent = (index + 1);
                    }
                }
            }
        }

        // Make updateModalFeaturedImage function accessible globally
        window.updateModalFeaturedImage = function(imgElement, index) {
            if (modalFeaturedImage) {
                modalFeaturedImage.src = imgElement.src;
                currentImageIndex = index;
                modalThumbnails.forEach(thumb => {
                    thumb.classList.remove('active');
                });
                imgElement.parentElement.classList.add('active');
                if (currentIndexElement) {
                    currentIndexElement.textContent = (index + 1);
                }
            }
        };

        // Health record image modals functionality
        document.querySelectorAll('[id^="recordImagesModal"]').forEach(modal => {
            const modalId = modal.id;
            const recordId = modalId.replace('recordImagesModal', '');
            
            // Get elements within this specific modal
            const modalImages = modal.querySelectorAll('.modal-image');
            const modalBody = modal.querySelector('.modal-body');
            
            if (modalImages.length > 1 && modalBody) {
                // Create navigation buttons for modals with multiple images
                modalBody.style.position = 'relative';
                
                const prevBtn = document.createElement('button');
                prevBtn.className = 'btn btn-sm btn-outline-secondary position-absolute start-0 top-50 translate-middle-y ms-2';
                prevBtn.innerHTML = '<i class="fas fa-chevron-left"></i>';
                modalBody.appendChild(prevBtn);
                
                const nextBtn = document.createElement('button');
                nextBtn.className = 'btn btn-sm btn-outline-secondary position-absolute end-0 top-50 translate-middle-y me-2';
                nextBtn.innerHTML = '<i class="fas fa-chevron-right"></i>';
                modalBody.appendChild(nextBtn);
                
                // Set up image navigation
                let currentRecordImageIndex = 0;
                
                // Initially hide all but first image
                modalImages.forEach((img, index) => {
                    const parent = img.closest('.col-md-6');
                    if (parent) {
                        if (index !== 0) {
                            parent.style.display = 'none';
                        }
                    }
                });
                
                // Previous button functionality
                prevBtn.addEventListener('click', () => {
                    if (modalImages[currentRecordImageIndex]) {
                        const currentParent = modalImages[currentRecordImageIndex].closest('.col-md-6');
                        if (currentParent) currentParent.style.display = 'none';
                    }
                    
                    currentRecordImageIndex = (currentRecordImageIndex - 1 + modalImages.length) % modalImages.length;
                    
                    if (modalImages[currentRecordImageIndex]) {
                        const newParent = modalImages[currentRecordImageIndex].closest('.col-md-6');
                        if (newParent) newParent.style.display = 'block';
                    }
                });
                
                // Next button functionality
                nextBtn.addEventListener('click', () => {
                    if (modalImages[currentRecordImageIndex]) {
                        const currentParent = modalImages[currentRecordImageIndex].closest('.col-md-6');
                        if (currentParent) currentParent.style.display = 'none';
                    }
                    
                    currentRecordImageIndex = (currentRecordImageIndex + 1) % modalImages.length;
                    
                    if (modalImages[currentRecordImageIndex]) {
                        const newParent = modalImages[currentRecordImageIndex].closest('.col-md-6');
                        if (newParent) newParent.style.display = 'block';
                    }
                });
                
                // Reset index when modal is shown
                modal.addEventListener('show.bs.modal', function() {
                    currentRecordImageIndex = 0;
                    modalImages.forEach((img, index) => {
                        const parent = img.closest('.col-md-6');
                        if (parent) {
                            parent.style.display = index === 0 ? 'block' : 'none';
                        }
                    });
                });
            }
        });
    });

    // Make updateFeaturedImage accessible globally
    window.updateFeaturedImage = updateFeaturedImage;
</script>
@endpush