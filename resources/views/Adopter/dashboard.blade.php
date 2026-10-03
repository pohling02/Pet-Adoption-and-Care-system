@extends('layouts.adopter_master')

@section('title', 'Adopter Home')

@section('content')
<head>
    <link href="{{ asset('css/adopterhome.css') }}" rel="stylesheet">
</head>

<!-- HEADER BANNER -->
<div class="header-banner d-flex align-items-center">
    <div class="container text-center">
        <h1>Welcome to Petopia</h1>
        <p>
            Welcome to Petopia! Your one-stop destination for pet adoption and care, connecting loving hearts with loyal companions. Together, let's create a brighter future for every pet in need.
        </p>
    </div>
</div>

@if($recommendedPets->isNotEmpty())
<div class="container mt-5">
    <h2 class="text-center" style="font-family: 'Arial', sans-serif; font-weight: bold; color: #1F4F99; font-size: 2.2rem;">Recommended Pets Based on Your Preferences</h2>
    <div class="row mt-4">
        @foreach ($recommendedPets as $pet)
        <div class="col-md-3 mb-4"> 
            <a href="{{ route('adopter.pet.details', ['id' => $pet->PetID]) }}" class="text-decoration-none text-dark">
                <div class="card h-100 shadow-sm hover-scale">
                    <div id="carousel-{{ $pet->PetID }}" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            @if($pet->images->isNotEmpty())
                            @foreach ($pet->images as $index => $image)
                            <div class="carousel-item @if ($index == 0) active @endif">
                                <img src="{{ asset($image->ImagePath) }}" class="d-block w-100" alt="{{ $pet->PetName }}">
                            </div>
                            @endforeach
                            @else
                            <div class="carousel-item active">
                                <img src="{{ asset('images/placeholder-pet.jpg') }}" class="d-block w-100" alt="{{ $pet->PetName }}">
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="card-body text-center">
                        <h5 class="card-title">{{ $pet->PetName }}</h5>
                        <p class="mb-1"><strong>Species:</strong> {{ $pet->Species }}</p>
                        <p class="mb-1"><strong>Breed:</strong> {{ $pet->Breed }}</p>
                        <p class="mb-1"><strong>Location:</strong> {{ $pet->CurrentLocation }}</p>
                        <p class="mb-1"><strong>Age:</strong> {{ \Carbon\Carbon::parse($pet->DateOfBirth)->age }} Years</p>

                        @if(isset($pet->enhancedPersonality))
                        <div class="mt-3 mb-2">
                            <p class="personality-text fst-italic">{{ Str::limit($pet->enhancedPersonality, 100) }}</p>
                        </div>
                        @else
                        <p class="mb-1"><strong>Personality:</strong> {{ Str::limit($pet->Personality, 50) }}</p>
                        @endif
                    </div>

                    @if(isset($pet->recommendationReason))
                    <div class="card-footer bg-light p-3 border-top">
                        <div class="recommendation-badge">
                            <span class="badge bg-primary mb-2">Perfect Match</span>
                            <small class="d-block text-muted">{{ Str::limit($pet->recommendationReason, 150) }}</small>
                        </div>
                    </div>
                    @endif
                </div>
            </a>
        </div>
        @endforeach
    </div>
</div>
@endif

<!-- SHOW AVAILABLE PETS ONLY IF NO RECOMMENDATIONS -->
@if($recommendedPets->isEmpty())
<div class="container mt-5">
    <h2 class="text-center" style="font-family: 'Arial', sans-serif; font-weight: bold; color: #1F4F99; font-size: 2.2rem;">Available Pets</h2>
    <div class="row mt-4">
        @foreach ($pets as $pet)
        <div class="col-md-3 mb-4"> 
            <a href="{{ route('adopter.pet.details', ['id' => $pet->PetID]) }}" class="text-decoration-none text-dark">
                <div class="card h-100 shadow-sm">
                    <div id="carousel-{{ $pet->PetID }}" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            @if($pet->images->isNotEmpty())
                            @foreach ($pet->images as $index => $image)
                            <div class="carousel-item @if ($index == 0) active @endif">
                                <img src="{{ asset($image->ImagePath) }}" class="d-block w-100" alt="{{ $pet->PetName }}">
                            </div>
                            @endforeach
                            @else
                            <div class="carousel-item active">
                                <img src="{{ asset('images/placeholder-pet.jpg') }}" class="d-block w-100" alt="{{ $pet->PetName }}">
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="card-body text-center">
                        <h5 class="card-title">{{ $pet->PetName }}</h5>
                        <p class="mb-1"><strong>Species:</strong> {{ $pet->Species }}</p>
                        <p class="mb-1"><strong>Breed:</strong> {{ $pet->Breed }}</p>
                        <p class="mb-1"><strong>Color:</strong> {{ $pet->Color }}</p>
                        <p class="mb-1"><strong>Age:</strong> {{ \Carbon\Carbon::parse($pet->DateOfBirth)->age }} Years</p>
                        <p class="mb-1"><strong>Gender:</strong> {{ $pet->Gender }}</p>
                        <p class="mb-1"><strong>Personality:</strong> {{ Str::limit($pet->Personality, 50) }}</p>
                    </div>
                </div>
            </a>
        </div>
        @endforeach
    </div>
</div>
@endif



<!-- MAIN SERVICES -->
<div class="container text-center my-5">
    <h2 class="expert-services-title">Expert Pet Adoption and Care Services</h2>
</div>

<div class="services-section py-5">
    <div class="container">
        <div class="row text-center">
            <!-- Adoption Application -->
            <div class="col-md-4 service-item">
                <div class="service-content">
                    <img src="{{ asset('images/PetAdopAppli.png') }}" alt="Adoption Application" class="service-icon">
                    <div class="service-title">Adoption Application</div>
                </div>
            </div>
            <!-- Comprehensive Pet Health Management -->
            <div class="col-md-4 service-item">
                <div class="service-content">
                    <img src="{{ asset('images/PetHealth.png') }}" alt="Comprehensive Pet Health Management" class="service-icon">
                    <div class="service-title">Comprehensive Pet Health Management</div>
                </div>
            </div>
            <!-- Access to Pet Resources -->
            <div class="col-md-4 service-item">
                <div class="service-content">
                    <img src="{{ asset('images/PetCare.png') }}" alt="Access to Pet Resources" class="service-icon">
                    <div class="service-title">Access to Pet Resources</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- VIDEO SECTION -->
<div class="container my-5">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h2 style="font-family: 'Arial', sans-serif; font-weight: bold; color: #4a90e2; font-size: 2rem;">Learn More About Our Mission</h2>
        </div>
        <div class="col-md-6 text-center">
            <iframe class="video-container" width="100%" height="400" src="https://www.youtube.com/embed/cu765EdZL8o?si=i4R284C6MWGOfpkJ" frameborder="0" allowfullscreen></iframe>
        </div>
    </div>
</div>

@endsection