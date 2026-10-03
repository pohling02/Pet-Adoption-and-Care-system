@extends('layouts.shelter_master')

@section('title', $pet->PetName . ' - Pet Details')

@section('content')
<div class="container mt-4">
    <div class="card shadow-lg rounded-lg p-4 bg-white border-0">

        <!-- Header Section with Breadcrumbs -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('shelter.home') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('shelter.pets.manage') }}">Manage Pets</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $pet->PetName }}</li>
            </ol>
        </nav>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="fw-bold text-uppercase mb-0" style="color: #003366;">
                <i class="fas fa-paw me-2"></i>{{ $pet->PetName }}
                @if($pet->PetCode)
                    <small class="text-muted fs-6">({{ $pet->PetCode }})</small>
                @endif
            </h2>
            <span class="badge {{ $pet->AdoptionStatus == 'Available' ? 'bg-success' : 'bg-danger' }} fs-5 p-2">
                {{ $pet->AdoptionStatus }}
            </span>
        </div>
        <hr class="border-primary">

        <!-- Pet Images Section with Carousel for Multiple Images -->
        <div class="mb-4">
            @if($pet->images->count() > 0)
            @if($pet->images->count() == 1)
            <div class="text-center">
                <img src="{{ asset($pet->images->first()->ImagePath) }}" 
                     class="img-fluid rounded shadow-sm" 
                     alt="{{ $pet->PetName }}"
                     style="max-height: 400px; object-fit: contain;">
            </div>
            @else
            <div id="petImageCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    @foreach($pet->images as $key => $image)
                    <button type="button" 
                            data-bs-target="#petImageCarousel" 
                            data-bs-slide-to="{{ $key }}" 
                            class="{{ $key == 0 ? 'active' : '' }}"
                            aria-label="Slide {{ $key + 1 }}"></button>
                    @endforeach
                </div>
                <div class="carousel-inner rounded shadow">
                    @foreach($pet->images as $key => $image)
                    <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                        <div class="text-center bg-light" style="height: 400px;">
                            <img src="{{ asset($image->ImagePath) }}" 
                                 class="d-block mx-auto h-100"
                                 style="object-fit: contain;"
                                 alt="{{ $pet->PetName }} image {{ $key + 1 }}">
                        </div>
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

            <!-- Thumbnail Navigation -->
            <div class="d-flex flex-wrap justify-content-center mt-3 thumbnail-container">
                @foreach($pet->images as $key => $image)
                <div class="thumbnail-wrapper m-1" style="width: 80px; height: 80px; cursor: pointer;" 
                     onclick="$('#petImageCarousel').carousel({{ $key }})">
                    <img src="{{ asset($image->ImagePath) }}" 
                         class="img-thumbnail w-100 h-100" 
                         style="object-fit: cover;"
                         alt="Thumbnail {{ $key + 1 }}">
                </div>
                @endforeach
            </div>
            @endif
            @else
            <div class="text-center p-5 bg-light rounded">
                <i class="fas fa-camera fa-3x text-muted mb-3"></i>
                <p class="text-muted fs-5 mb-0">No images uploaded for this pet.</p>
            </div>
            @endif
        </div>

        <!-- Pet Information Cards with Uniform Styling -->
        <div class="row g-4">
            <!-- Basic Information Card -->
            <div class="col-md-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="fw-bold mb-0">
                            <i class="fas fa-info-circle me-2"></i>Basic Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <tbody>
                                    <tr>
                                        <th scope="row" style="width: 40%;">Species</th>
                                        <td>{{ $pet->Species }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Breed</th>
                                        <td>
                                            {{ $pet->Breed }}
                                            @if($pet->ManualBreed)
                                                <small class="text-muted">({{ $pet->ManualBreed }})</small>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Color</th>
                                        <td>{{ $pet->Color }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Gender</th>
                                        <td>{{ $pet->Gender }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Date of Birth</th>
                                        <td>{{ \Carbon\Carbon::parse($pet->DateOfBirth)->format('M d, Y') }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Age</th>
                                        <td>
                                            @php
                                            $years = \Carbon\Carbon::parse($pet->DateOfBirth)->diff(\Carbon\Carbon::now())->y;
                                            $months = \Carbon\Carbon::parse($pet->DateOfBirth)->diff(\Carbon\Carbon::now())->m;
                                            @endphp
                                            @if($years > 0)
                                            {{ $years }} {{ Str::plural('year', $years) }}
                                            @if($months > 0)
                                            , {{ $months }} {{ Str::plural('month', $months) }}
                                            @endif
                                            @else
                                            {{ $months }} {{ Str::plural('month', $months) }}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Current Location</th>
                                        <td>
                                            <span class="badge bg-primary p-2">
                                                <i class="fas fa-map-marker-alt me-1"></i>
                                                {{ $pet->CurrentLocation ?? 'Not specified' }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Current Address</th>
                                        <td>
                                            <i class="fas fa-map-marker-alt me-1"></i>
                                            {{ $pet->CurrentAddress ?? 'Not specified' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Ideal Environment</th>
                                        <td>
                                            <span class="badge bg-info p-2">
                                                <i class="fas fa-home me-1"></i>
                                                @switch($pet->Ideal_Environment)
                                                    @case(1)
                                                        Apartment
                                                        @break
                                                    @case(2)
                                                        Landed Property
                                                        @break
                                                    @case(3)
                                                        Both Apartment & Landed
                                                        @break
                                                    @default
                                                        Not Specified
                                                @endswitch
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Health & Care Card -->
            <div class="col-md-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="fw-bold mb-0">
                            <i class="fas fa-medkit me-2"></i>Health & Care
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <tbody>
                                    <tr>
                                        <th scope="row" style="width: 40%;">Vaccination Status</th>
                                        <td>
                                            <span class="badge {{ $pet->VaccinationStatus == 'Fully Vaccinated' ? 'bg-success' : 'bg-warning text-dark' }} p-2">
                                                {{ $pet->VaccinationStatus }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Health Condition</th>
                                        <td>{{ $pet->HealthCondition ?? 'Healthy' }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Special Needs</th>
                                        <td>{{ $pet->SpecialNeed ?? 'None' }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Allergy</th>
                                        <td>
                                            @if($pet->Allergy && !empty($pet->AllergyDetails))
                                            <span class="badge bg-warning text-dark p-2">
                                                <i class="fas fa-exclamation-triangle me-1"></i>
                                                Yes - {{ $pet->AllergyDetails }}
                                            </span>
                                            @elseif($pet->Allergy)
                                            <span class="badge bg-warning text-dark p-2">
                                                <i class="fas fa-exclamation-triangle me-1"></i>
                                                Yes - Not Specified
                                            </span>
                                            @else
                                            <span class="badge bg-secondary p-2">
                                                <i class="fas fa-check me-1"></i>No
                                            </span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Neutering</th>
                                        <td>
                                            <span class="badge {{ $pet->Neutering ? 'bg-success' : 'bg-secondary' }} p-2">
                                                <i class="{{ $pet->Neutering ? 'fas fa-check' : 'fas fa-times' }} me-1"></i>
                                                {{ $pet->Neutering ? 'Yes' : 'No' }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Personality & Traits Card -->
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="fw-bold mb-0">
                            <i class="fas fa-heart me-2"></i>Personality & Traits
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="fw-bold">Personality:</h6>
                                <p class="fs-6">{{ $pet->Personality ?? 'Not specified' }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-bold">Background:</h6>
                                <p class="fs-6">{{ $pet->Background ?? 'Not provided' }}</p>
                            </div>
                        </div>

                        <!-- Trait Ratings with Visual Indicators -->
                        <div class="row g-3">
                            <div class="col-lg-2 col-md-4 col-6">
                                <div class="card h-100 bg-light border-0">
                                    <div class="card-body text-center p-3">
                                        <h6 class="fw-bold mb-2 small">Energy Level</h6>
                                        <div class="d-flex justify-content-center mb-2">
                                            @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-bolt {{ $i <= $pet->EnergyLevel ? 'text-warning' : 'text-muted' }} mx-1" 
                                               style="font-size: 1.2rem;"></i>
                                            @endfor
                                        </div>
                                        <p class="mb-0 small">{{ $pet->EnergyLevel }}/5</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-4 col-6">
                                <div class="card h-100 bg-light border-0">
                                    <div class="card-body text-center p-3">
                                        <h6 class="fw-bold mb-2 small">Appetite</h6>
                                        <div class="d-flex justify-content-center mb-2">
                                            @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-utensils {{ $i <= $pet->Appetite ? 'text-success' : 'text-muted' }} mx-1" 
                                               style="font-size: 1.2rem;"></i>
                                            @endfor
                                        </div>
                                        <p class="mb-0 small">{{ $pet->Appetite }}/5</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-4 col-6">
                                <div class="card h-100 bg-light border-0">
                                    <div class="card-body text-center p-3">
                                        <h6 class="fw-bold mb-2 small">Friendliness</h6>
                                        <div class="d-flex justify-content-center mb-2">
                                            @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-smile {{ $i <= $pet->Friendliness ? 'text-primary' : 'text-muted' }} mx-1" 
                                               style="font-size: 1.2rem;"></i>
                                            @endfor
                                        </div>
                                        <p class="mb-0 small">{{ $pet->Friendliness }}/5</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-4 col-6">
                                <div class="card h-100 bg-light border-0">
                                    <div class="card-body text-center p-3">
                                        <h6 class="fw-bold mb-2 small">Adaptability</h6>
                                        <div class="d-flex justify-content-center mb-2">
                                            @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-home {{ $i <= $pet->Adaptability ? 'text-info' : 'text-muted' }} mx-1" 
                                               style="font-size: 1.2rem;"></i>
                                            @endfor
                                        </div>
                                        <p class="mb-0 small">{{ $pet->Adaptability }}/5</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-4 col-6">
                                <div class="card h-100 bg-light border-0">
                                    <div class="card-body text-center p-3">
                                        <h6 class="fw-bold mb-2 small">Barking Level</h6>
                                        <div class="d-flex justify-content-center mb-2">
                                            @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-volume-up {{ $i <= $pet->BarkingLevel ? 'text-danger' : 'text-muted' }} mx-1" 
                                               style="font-size: 1.2rem;"></i>
                                            @endfor
                                        </div>
                                        <p class="mb-0 small">{{ $pet->BarkingLevel }}/5</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-4 col-6">
                                <div class="card h-100 bg-light border-0">
                                    <div class="card-body text-center p-3">
                                        <h6 class="fw-bold mb-2 small">Shedding Level</h6>
                                        <div class="d-flex justify-content-center mb-2">
                                            @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-cut {{ $i <= $pet->SheddingLevel ? 'text-secondary' : 'text-muted' }} mx-1" 
                                               style="font-size: 1.2rem;"></i>
                                            @endfor
                                        </div>
                                        <p class="mb-0 small">{{ $pet->SheddingLevel }}/5</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons with Floating Action Button for Adoption -->
        <div class="mt-4 d-flex flex-wrap justify-content-between align-items-center">
            <div>
                <a href="{{ route('shelter.pets.manage') }}" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
                <a href="{{ route('shelter.pets.edit', ['id' => $pet->PetID]) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-1"></i> Edit
                </a>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
            });
    $(document).ready(function() {
    var carousel = new bootstrap.Carousel(document.getElementById('petImageCarousel'), {
    interval: false
    });
    $('.thumbnail-image').on('click', function() {
    var slideIndex = $(this).data('slide-index');
    $('#petImageCarousel').carousel(slideIndex);
    });
    $('#petImageCarousel').on('slide.bs.carousel', function(e) {
    $('.thumbnail-image').removeClass('active');
    $('.thumbnail-image[data-slide-index="' + e.to + '"]').addClass('active');
    });
    });
</script>
@endpush
@endsection