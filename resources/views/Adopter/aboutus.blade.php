@extends('layouts.adopter_master')

@section('title', 'About Us')

@section('content')

<!-- Hero Section -->
<div class="position-relative text-white text-center" 
     style="background: url('/images/about.jpg') center/cover no-repeat; height: 350px; display: flex; align-items: center; justify-content: center;">
    <div class="container">
        <h1 class="fw-bold display-4">Petopia</h1>
        <p class="lead">Change the path of lost, abandoned, and unwanted animals' lives</p>
    </div>
</div>

<!-- Problem & Solution Section -->
<div class="container py-5">
    <div class="row">
        <div class="col-md-6">
            <h2 class="fw-bold text-danger">The Problem</h2>
            <p class="text-muted">
                For every licensed dog, there are approximately 4 stray animals out there. Many of them are suffering every day. 
                Animals need help, but they can’t speak for themselves.
            </p>
        </div>
        <div class="col-md-6">
            <h2 class="fw-bold text-success">The Solution</h2>
            <p class="text-muted">
                Petopia is a temporary animal shelter to help the sick, injured, and unwanted. We provide temporary care for 
                healthy, abandoned, and unwanted animals until a home is found.
            </p>
            <p class="text-muted">
                Petopia also plays a role in educating the public about responsibility towards their pets.
            </p>
        </div>
    </div>
</div>

<!-- Mission Section -->
<div class="bg-light py-5">
    <div class="container text-center">
        <h2 class="fw-semibold text-dark">Our Mission</h2>
        <p class="text-muted w-75 mx-auto">
            We aim to streamline the pet adoption process, provide resources for responsible pet care, 
            and support non-profits by reducing manual workload.
        </p>
    </div>
</div>

<!-- Why Choose Us Section -->
<div class="container py-5">
    <div class="text-center">
        <h2 class="fw-semibold text-dark">Why Choose Us?</h2>
    </div>

    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center p-4">
                <h5 class="fw-bold text-primary"><i class="bi bi-hand-thumbs-up"></i> User-Friendly & Efficient</h5>
                <p class="text-muted">A smooth and intuitive adoption experience.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center p-4">
                <h5 class="fw-bold text-primary"><i class="bi bi-file-earmark-text"></i> Comprehensive Pet Profiles</h5>
                <p class="text-muted">Get complete information before adoption.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center p-4">
                <h5 class="fw-bold text-primary"><i class="bi bi-heart"></i> Health & Well-being Focused</h5>
                <p class="text-muted">Ensure pets stay healthy and happy.</p>
            </div>
        </div>
    </div>
</div>

<!-- Get Involved Section -->
<div class="bg-light py-5">
        <!-- Google Map Embed -->
        <div class="mt-4">
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d7966.341805695979!2d101.72216032282088!3d3.211337944612317!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31cc49b569c8a1fd%3A0xa0b42f73eb8db1ef!2sTAR%20UMT%20-%20Bangunan%20Tan%20Sri%20Khaw%20Kai%20Boh!5e0!3m2!1sen!2smy!4v1740709990563!5m2!1sen!2smy" 
                width="100%" 
                height="400" 
                style="border:0; border-radius: 10px; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);" 
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
    </div>
</div>

@endsection
